<?php
/**
 * DMC DataLoad - API Polling Engine
 * 
 * Responsável por:
 * - Polling automático de APIs externas
 * - Avaliação de eventos/condições (JSONPath)
 * - Armazenamento de valores capturados
 * - Disparo de workflows
 * - Envio de notificações
 * - Logging de auditoria
 */

namespace App\Core;

use PDO;
use App\Controllers\ApiExternaController;
use App\Controllers\WorkflowController;

class ApiPollingEngine
{
    private PDO $db;
    private ApiExternaController $apiController;

    public function __construct(PDO $db = null)
    {
        $this->db = $db ?? Database::getConexao();
        $this->apiController = new ApiExternaController();
    }

    /**
     * Executar polling de todas as APIs ativas que precisam de verificação
     */
    public function executarPolling(): array
    {
        $resultados = [];
        
        try {
            $apis = $this->buscarApisParaPolling();
            
            foreach ($apis as $api) {
                try {
                    $resultado = $this->verificarApi($api);
                    $resultados[] = $resultado;
                } catch (\Exception $e) {
                    Logger::error("Erro polling API {$api['nome']} (ID:{$api['id']})", [
                        'erro' => $e->getMessage(),
                        'componente' => 'polling_engine'
                    ]);
                    $resultados[] = [
                        'api_id' => $api['id'],
                        'api_nome' => $api['nome'],
                        'sucesso' => false,
                        'erro' => $e->getMessage()
                    ];
                }
            }
        } catch (\Exception $e) {
            Logger::critical("Erro fatal no polling engine", [
                'erro' => $e->getMessage(),
                'componente' => 'polling_engine'
            ]);
        }
        
        return $resultados;
    }

    /**
     * Buscar APIs ativas que precisam de polling
     */
    private function buscarApisParaPolling(): array
    {
        $stmt = $this->db->query("
            SELECT a.*
            FROM tb_api_externas a
            WHERE a.ativo = true
              AND a.intervalo_polling > 0
              AND (
                a.ultima_verificacao IS NULL 
                OR a.ultima_verificacao + (a.intervalo_polling || ' seconds')::interval <= NOW()
              )
            ORDER BY a.ultima_verificacao ASC NULLS FIRST
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verificar uma API específica: fazer request, avaliar eventos, executar ações
     */
    private function verificarApi(array $api): array
    {
        $apiId = (int)$api['id'];
        $resultado = [
            'api_id' => $apiId,
            'api_nome' => $api['nome'],
            'sucesso' => false,
            'http_code' => 0,
            'tempo_ms' => 0,
            'eventos_avaliados' => 0,
            'eventos_match' => 0,
            'acoes_executadas' => []
        ];
        
        // Preparar dados para o cURL
        $headers = json_decode($api['headers'] ?? '{}', true) ?: [];
        $credenciais = json_decode($api['credenciais'] ?? '{}', true) ?: [];
        
        $testData = [
            'url' => $api['url'],
            'metodo' => $api['metodo'] ?? 'GET',
            'auth_tipo' => $api['auth_tipo'] ?? 'none',
            'timeout' => (int)($api['timeout'] ?? 30),
            'tipo_resposta' => $api['tipo_resposta'] ?? 'json',
            'body_template' => $api['body_template'] ?? null,
            'header_keys' => array_keys($headers),
            'header_values' => array_values($headers),
            'bearer_token' => $credenciais['token'] ?? '',
            'basic_username' => $credenciais['username'] ?? '',
            'basic_password' => $credenciais['password'] ?? '',
            'api_key' => $credenciais['api_key'] ?? '',
            'api_key_header' => $credenciais['api_key_header'] ?? 'X-API-Key'
        ];
        
        // Executar requisição
        $response = $this->apiController->testarApi($testData);
        $resultado['http_code'] = $response['http_code'] ?? 0;
        $resultado['tempo_ms'] = $response['tempo_ms'] ?? 0;
        
        // Atualizar status da API
        if ($response['sucesso'] ?? false) {
            $resultado['sucesso'] = true;
            $this->atualizarStatusApi($apiId, $resultado['http_code'], null);
            
            // Avaliar eventos se a resposta for JSON
            $dadosResposta = $response['response'] ?? null;
            if (is_array($dadosResposta) || is_string($dadosResposta)) {
                if (is_string($dadosResposta)) {
                    $dadosResposta = json_decode($dadosResposta, true);
                }
                if ($dadosResposta !== null) {
                    $eventosResult = $this->avaliarEventos($apiId, $dadosResposta);
                    $resultado['eventos_avaliados'] = $eventosResult['avaliados'];
                    $resultado['eventos_match'] = $eventosResult['matches'];
                    $resultado['acoes_executadas'] = $eventosResult['acoes'];
                }
            }
        } else {
            $erro = $response['erro'] ?? ('HTTP ' . ($response['http_code'] ?? 0));
            $this->atualizarStatusApi($apiId, $resultado['http_code'], $erro);
        }
        
        Logger::info("Polling API: {$api['nome']}", [
            'api_id' => $apiId,
            'sucesso' => $resultado['sucesso'],
            'http_code' => $resultado['http_code'],
            'tempo_ms' => $resultado['tempo_ms'],
            'eventos_match' => $resultado['eventos_match'],
            'componente' => 'polling_engine'
        ]);
        
        return $resultado;
    }

    /**
     * Atualizar status da API após verificação
     */
    private function atualizarStatusApi(int $apiId, int $httpCode, ?string $erro): void
    {
        $stmt = $this->db->prepare("
            UPDATE tb_api_externas SET 
                ultima_verificacao = NOW(),
                ultimo_status = ?,
                ultimo_erro = ?
            WHERE id = ?
        ");
        $stmt->execute([$httpCode, $erro, $apiId]);
    }

    /**
     * Avaliar todos os eventos ativos de uma API
     */
    private function avaliarEventos(int $apiId, $dadosResposta): array
    {
        $result = ['avaliados' => 0, 'matches' => 0, 'acoes' => []];
        
        $stmt = $this->db->prepare("
            SELECT e.* FROM tb_eventos_api e
            WHERE e.id_api = ? AND e.ativo = true
        ");
        $stmt->execute([$apiId]);
        $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($eventos as $evento) {
            $result['avaliados']++;
            
            $jsonpath = $evento['jsonpath'] ?? '';
            if (empty($jsonpath)) continue;
            
            // Extrair valor
            $valorExtraido = $this->apiController->extrairValorJsonPath($dadosResposta, $jsonpath);
            
            // Avaliar condição
            $operador = $evento['operador'] ?? 'equals';
            $valorEsperado = $evento['valor_esperado'] ?? '';
            $match = $this->apiController->avaliarCondicao($valorExtraido, $operador, $valorEsperado);
            
            // Armazenar valor capturado se configurado
            if ($evento['armazenar_valor'] ?? true) {
                $this->armazenarValorCapturado($evento, $apiId, $valorExtraido, $match);
            }
            
            // Atualizar evento
            $this->atualizarEvento($evento, $valorExtraido, $match);
            
            if ($match) {
                $result['matches']++;
                
                // Executar ação baseada na configuração do evento
                $acao = $evento['acao'] ?? 'store_value';
                $acaoResult = $this->executarAcao($acao, $evento, $valorExtraido);
                $result['acoes'][] = $acaoResult;
            }
        }
        
        return $result;
    }

    /**
     * Armazenar valor capturado no histórico
     */
    private function armazenarValorCapturado(array $evento, int $apiId, $valor, bool $match): void
    {
        try {
            $valorStr = is_array($valor) || is_object($valor) ? json_encode($valor) : (string)$valor;
            $valorJson = json_encode($valor);
            
            $stmt = $this->db->prepare("
                INSERT INTO tb_valores_capturados (id_evento, id_api, valor, valor_json, \"condição_match\", data_captura)
                VALUES (?, ?, ?, ?::jsonb, ?, NOW())
            ");
            $stmt->execute([
                (int)$evento['id'],
                $apiId,
                $valorStr,
                $valorJson,
                $match ? 't' : 'f'
            ]);
        } catch (\Exception $e) {
            Logger::warning("Erro ao armazenar valor capturado", [
                'evento_id' => $evento['id'],
                'erro' => $e->getMessage(),
                'componente' => 'polling_engine'
            ]);
        }
    }

    /**
     * Atualizar estatísticas do evento
     */
    private function atualizarEvento(array $evento, $valorExtraido, bool $match): void
    {
        $valorStr = is_array($valorExtraido) || is_object($valorExtraido) 
            ? json_encode($valorExtraido) 
            : (string)$valorExtraido;
        
        $sql = "UPDATE tb_eventos_api SET 
            ultimo_valor_capturado = ?,
            data_atualizacao = NOW()";
        $params = [$valorStr];
        
        if ($match) {
            $sql .= ", ultimo_match = true, total_matches = COALESCE(total_matches, 0) + 1";
        } else {
            $sql .= ", ultimo_match = false";
        }
        
        $sql .= " WHERE id = ?";
        $params[] = (int)$evento['id'];
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    /**
     * Executar ação quando condição é atendida
     */
    private function executarAcao(string $acao, array $evento, $valorExtraido): array
    {
        $result = [
            'evento_id' => $evento['id'],
            'evento_nome' => $evento['nome'],
            'acao' => $acao,
            'sucesso' => false
        ];
        
        switch ($acao) {
            case 'store_value':
                // Já armazenado acima
                $result['sucesso'] = true;
                break;
                
            case 'trigger_workflow':
                $result = array_merge($result, $this->dispararWorkflow($evento, $valorExtraido));
                break;
                
            case 'store_and_trigger':
                // Valor já armazenado, disparar workflow
                $result = array_merge($result, $this->dispararWorkflow($evento, $valorExtraido));
                break;
                
            case 'notify':
                $result = array_merge($result, $this->enviarNotificacao($evento, $valorExtraido));
                break;
                
            default:
                $result['sucesso'] = true;
                break;
        }
        
        Logger::info("Ação executada: {$acao}", [
            'evento_id' => $evento['id'],
            'evento_nome' => $evento['nome'],
            'sucesso' => $result['sucesso'],
            'componente' => 'polling_engine'
        ]);
        
        return $result;
    }

    /**
     * Disparar um workflow quando evento é acionado
     */
    private function dispararWorkflow(array $evento, $valorExtraido): array
    {
        $workflowId = (int)($evento['id_workflow'] ?? 0);
        if ($workflowId <= 0) {
            return [
                'sucesso' => false,
                'erro' => 'Nenhum workflow configurado para este evento'
            ];
        }
        
        try {
            $workflowCtrl = new WorkflowController();
            
            // Contexto enviado ao workflow
            $contexto = [
                'trigger' => 'api_event',
                'evento_id' => $evento['id'],
                'evento_nome' => $evento['nome'],
                'api_id' => $evento['id_api'],
                'valor_capturado' => $valorExtraido,
                'operador' => $evento['operador'],
                'valor_esperado' => $evento['valor_esperado'],
                'timestamp' => date('c')
            ];
            
            $resultado = $workflowCtrl->executar($workflowId, $contexto);
            
            Logger::info("Workflow disparado por evento de API", [
                'workflow_id' => $workflowId,
                'evento_id' => $evento['id'],
                'resultado' => $resultado['sucesso'] ?? false,
                'componente' => 'polling_engine'
            ]);
            
            return [
                'sucesso' => $resultado['sucesso'] ?? false,
                'workflow_id' => $workflowId,
                'execucao_id' => $resultado['execucao_id'] ?? null,
                'status' => $resultado['status'] ?? 'unknown'
            ];
        } catch (\Exception $e) {
            Logger::error("Erro ao disparar workflow", [
                'workflow_id' => $workflowId,
                'evento_id' => $evento['id'],
                'erro' => $e->getMessage(),
                'componente' => 'polling_engine'
            ]);
            return [
                'sucesso' => false,
                'erro' => 'Erro ao executar workflow: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Enviar notificação quando evento é acionado
     */
    private function enviarNotificacao(array $evento, $valorExtraido): array
    {
        try {
            $valorStr = is_array($valorExtraido) || is_object($valorExtraido) 
                ? json_encode($valorExtraido) 
                : (string)$valorExtraido;
            
            $mensagem = sprintf(
                'Evento "%s" acionado: condição %s %s atendida. Valor capturado: %s',
                $evento['nome'],
                $evento['operador'],
                $evento['valor_esperado'] ?? '',
                $valorStr
            );
            
            // Inserir notificação no banco
            $stmt = $this->db->prepare("
                INSERT INTO tb_notificacoes (tipo, titulo, mensagem, dados, created_at)
                VALUES ('api_event', ?, ?, ?, NOW())
            ");
            $stmt->execute([
                'Evento: ' . $evento['nome'],
                $mensagem,
                json_encode([
                    'evento_id' => $evento['id'],
                    'api_id' => $evento['id_api'],
                    'valor' => $valorExtraido,
                    'operador' => $evento['operador'],
                    'valor_esperado' => $evento['valor_esperado']
                ])
            ]);
            
            // Log de auditoria
            Logger::info("Notificação criada", [
                'evento_id' => $evento['id'],
                'titulo' => 'Evento: ' . $evento['nome'],
                'componente' => 'polling_engine'
            ]);
            
            return ['sucesso' => true];
        } catch (\Exception $e) {
            Logger::error("Erro ao criar notificação", [
                'evento_id' => $evento['id'],
                'erro' => $e->getMessage(),
                'componente' => 'polling_engine'
            ]);
            return [
                'sucesso' => false,
                'erro' => 'Erro ao criar notificação: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Executar polling de uma API específica (para testes ou execução manual)
     */
    public function executarPollingApi(int $apiId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM tb_api_externas WHERE id = ?");
        $stmt->execute([$apiId]);
        $api = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$api) {
            return ['sucesso' => false, 'erro' => 'API não encontrada'];
        }
        
        return $this->verificarApi($api);
    }
}
