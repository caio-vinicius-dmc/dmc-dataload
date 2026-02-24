<?php
/**
 * Controlador para APIs Externas e Eventos
 */

namespace App\Controladores;

use App\Core\Database;
use App\Core\ErrorHandler;

class ApiExternaController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConexao();
    }

    // =====================
    // APIS EXTERNAS
    // =====================

    /**
     * Listar todas as APIs externas
     */
    public function listarApis(): array
    {
        try {
            $stmt = $this->db->query("
                SELECT 
                    a.*,
                    (SELECT COUNT(*) FROM tb_eventos_api e WHERE e.id_api = a.id) as total_eventos
                FROM tb_api_externas a
                ORDER BY a.nome ASC
            ");
            return [
                'sucesso' => true,
                'dados' => $stmt->fetchAll(\PDO::FETCH_ASSOC)
            ];
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao listar APIs');
        }
    }

    /**
     * Buscar API por ID
     */
    public function buscarApi(int $id): array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tb_api_externas WHERE id = ?");
            $stmt->execute([$id]);
            $api = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$api) {
                return ['sucesso' => false, 'erro' => 'API não encontrada'];
            }
            
            // Decodificar JSONs
            $api['headers'] = json_decode($api['headers'] ?? '{}', true);
            $api['credenciais'] = json_decode($api['credenciais'] ?? '{}', true);
            
            return ['sucesso' => true, 'dados' => $api];
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao buscar API');
        }
    }

    /**
     * Salvar API (criar ou atualizar)
     */
    public function salvarApi(array $data): array
    {
        try {
            $id = !empty($data['id']) ? (int)$data['id'] : null;
            
            // Preparar headers e credenciais
            $headers = [];
            if (!empty($data['header_keys']) && is_array($data['header_keys'])) {
                foreach ($data['header_keys'] as $i => $key) {
                    if (!empty($key) && isset($data['header_values'][$i])) {
                        $headers[$key] = $data['header_values'][$i];
                    }
                }
            }
            
            $credenciais = [];
            $authTipo = $data['auth_tipo'] ?? 'none';
            
            switch ($authTipo) {
                case 'bearer':
                    $credenciais['token'] = $data['bearer_token'] ?? '';
                    break;
                case 'basic':
                    $credenciais['username'] = $data['basic_username'] ?? '';
                    $credenciais['password'] = $data['basic_password'] ?? '';
                    break;
                case 'api_key':
                    $credenciais['api_key'] = $data['api_key'] ?? '';
                    $credenciais['api_key_header'] = $data['api_key_header'] ?? 'X-API-Key';
                    break;
            }
            
            $params = [
                $data['nome'] ?? 'Nova API',
                $data['descricao'] ?? null,
                $data['url'] ?? '',
                $data['metodo'] ?? 'GET',
                json_encode($headers),
                $authTipo,
                json_encode($credenciais),
                $data['body_template'] ?? null,
                $data['tipo_resposta'] ?? 'json',
                (int)($data['intervalo_polling'] ?? 60),
                (int)($data['timeout'] ?? 30),
                isset($data['ativo']) ? ($data['ativo'] === 'on' || $data['ativo'] === '1' || $data['ativo'] === true) : true
            ];
            
            if ($id) {
                // Atualizar
                $sql = "UPDATE tb_api_externas SET 
                    nome = ?, descricao = ?, url = ?, metodo = ?, headers = ?, 
                    auth_tipo = ?, credenciais = ?, body_template = ?, tipo_resposta = ?, 
                    intervalo_polling = ?, timeout = ?, ativo = ?, data_atualizacao = NOW()
                    WHERE id = ?";
                $params[] = $id;
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                
                return ['sucesso' => true, 'mensagem' => 'API atualizada com sucesso', 'id' => $id];
            } else {
                // Criar
                $sql = "INSERT INTO tb_api_externas 
                    (nome, descricao, url, metodo, headers, auth_tipo, credenciais, body_template, tipo_resposta, intervalo_polling, timeout, ativo)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    RETURNING id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                $novoId = $stmt->fetchColumn();
                
                return ['sucesso' => true, 'mensagem' => 'API criada com sucesso', 'id' => $novoId];
            }
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao salvar API');
        }
    }

    /**
     * Deletar API
     */
    public function deletarApi(int $id): array
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM tb_api_externas WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                return ['sucesso' => false, 'erro' => 'API não encontrada'];
            }
            
            return ['sucesso' => true, 'mensagem' => 'API excluída com sucesso'];
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao excluir API');
        }
    }

    /**
     * Testar conexão com API externa
     */
    public function testarApi(array $data): array
    {
        try {
            $url = $data['url'] ?? '';
            $metodo = strtoupper($data['metodo'] ?? 'GET');
            $authTipo = $data['auth_tipo'] ?? 'none';
            $timeout = (int)($data['timeout'] ?? 30);
            $bodyTemplate = $data['body_template'] ?? null;
            
            // Headers
            $headers = [];
            if (!empty($data['header_keys']) && is_array($data['header_keys'])) {
                foreach ($data['header_keys'] as $i => $key) {
                    if (!empty($key) && isset($data['header_values'][$i])) {
                        $headers[] = "$key: " . $data['header_values'][$i];
                    }
                }
            }
            
            // Autenticação
            switch ($authTipo) {
                case 'bearer':
                    $headers[] = 'Authorization: Bearer ' . ($data['bearer_token'] ?? '');
                    break;
                case 'basic':
                    $auth = base64_encode(($data['basic_username'] ?? '') . ':' . ($data['basic_password'] ?? ''));
                    $headers[] = 'Authorization: Basic ' . $auth;
                    break;
                case 'api_key':
                    $headerName = $data['api_key_header'] ?? 'X-API-Key';
                    $headers[] = "$headerName: " . ($data['api_key'] ?? '');
                    break;
            }
            
            // Requisição cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Para desenvolvimento
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            if ($metodo === 'POST' || $metodo === 'PUT' || $metodo === 'PATCH') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);
                if ($bodyTemplate) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyTemplate);
                    $headers[] = 'Content-Type: application/json';
                }
            }
            
            $inicio = microtime(true);
            $response = curl_exec($ch);
            $fim = microtime(true);
            
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $erro = curl_error($ch);
            $tempoMs = (int)(($fim - $inicio) * 1000);
            
            curl_close($ch);
            
            if ($erro) {
                return [
                    'sucesso' => false,
                    'erro' => 'Erro de conexão: ' . $erro,
                    'http_code' => 0,
                    'tempo_ms' => $tempoMs
                ];
            }
            
            // Tentar parsear resposta
            $dadosResposta = null;
            $tipoResposta = $data['tipo_resposta'] ?? 'json';
            
            if ($tipoResposta === 'json') {
                $dadosResposta = json_decode($response, true);
            }
            
            return [
                'sucesso' => $httpCode >= 200 && $httpCode < 300,
                'http_code' => $httpCode,
                'tempo_ms' => $tempoMs,
                'response' => $dadosResposta ?? $response,
                'response_raw' => substr($response, 0, 5000) // Limitar tamanho
            ];
            
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao testar API');
        }
    }

    // =====================
    // EVENTOS DE API
    // =====================

    /**
     * Listar eventos de uma API
     */
    public function listarEventos(int $idApi = null): array
    {
        try {
            $sql = "
                SELECT 
                    e.*,
                    a.nome as api_nome,
                    w.nome as workflow_nome
                FROM tb_eventos_api e
                JOIN tb_api_externas a ON a.id = e.id_api
                LEFT JOIN tb_workflows w ON w.id = e.id_workflow
            ";
            
            $params = [];
            if ($idApi) {
                $sql .= " WHERE e.id_api = ?";
                $params[] = $idApi;
            }
            
            $sql .= " ORDER BY a.nome, e.nome";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return [
                'sucesso' => true,
                'dados' => $stmt->fetchAll(\PDO::FETCH_ASSOC)
            ];
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao listar eventos');
        }
    }

    /**
     * Buscar evento por ID
     */
    public function buscarEvento(int $id): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT e.*, a.nome as api_nome
                FROM tb_eventos_api e
                JOIN tb_api_externas a ON a.id = e.id_api
                WHERE e.id = ?
            ");
            $stmt->execute([$id]);
            $evento = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$evento) {
                return ['sucesso' => false, 'erro' => 'Evento não encontrado'];
            }
            
            return ['sucesso' => true, 'dados' => $evento];
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao buscar evento');
        }
    }

    /**
     * Salvar evento
     */
    public function salvarEvento(array $data): array
    {
        try {
            $id = !empty($data['id']) ? (int)$data['id'] : null;
            
            $params = [
                (int)($data['id_api'] ?? 0),
                $data['nome'] ?? 'Novo Evento',
                $data['descricao'] ?? null,
                $data['jsonpath'] ?? null,
                $data['xpath'] ?? null,
                $data['tipo_valor'] ?? 'string',
                $data['operador'] ?? 'equals',
                $data['valor_esperado'] ?? null,
                $data['acao'] ?? 'store_value',
                !empty($data['id_workflow']) ? (int)$data['id_workflow'] : null,
                isset($data['armazenar_valor']) ? ($data['armazenar_valor'] === 'on' || $data['armazenar_valor'] === '1') : true,
                isset($data['ativo']) ? ($data['ativo'] === 'on' || $data['ativo'] === '1') : true
            ];
            
            if ($id) {
                $sql = "UPDATE tb_eventos_api SET 
                    id_api = ?, nome = ?, descricao = ?, jsonpath = ?, xpath = ?, 
                    tipo_valor = ?, operador = ?, valor_esperado = ?, acao = ?,
                    id_workflow = ?, armazenar_valor = ?, ativo = ?, data_atualizacao = NOW()
                    WHERE id = ?";
                $params[] = $id;
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                
                return ['sucesso' => true, 'mensagem' => 'Evento atualizado', 'id' => $id];
            } else {
                $sql = "INSERT INTO tb_eventos_api 
                    (id_api, nome, descricao, jsonpath, xpath, tipo_valor, operador, valor_esperado, acao, id_workflow, armazenar_valor, ativo)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    RETURNING id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                $novoId = $stmt->fetchColumn();
                
                return ['sucesso' => true, 'mensagem' => 'Evento criado', 'id' => $novoId];
            }
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao salvar evento');
        }
    }

    /**
     * Deletar evento
     */
    public function deletarEvento(int $id): array
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM tb_eventos_api WHERE id = ?");
            $stmt->execute([$id]);
            
            return ['sucesso' => true, 'mensagem' => 'Evento excluído'];
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao excluir evento');
        }
    }

    /**
     * Testar extração de valor com JSONPath
     */
    public function testarJsonPath(array $data): array
    {
        try {
            $json = $data['json'] ?? '';
            $jsonpath = $data['jsonpath'] ?? '';
            $operador = $data['operador'] ?? 'equals';
            $valorEsperado = $data['valor_esperado'] ?? '';
            
            // Decodificar JSON
            $dados = json_decode($json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['sucesso' => false, 'erro' => 'JSON inválido: ' . json_last_error_msg()];
            }
            
            // Extrair valor usando JSONPath simples
            $valorExtraido = $this->extrairValorJsonPath($dados, $jsonpath);
            
            // Avaliar condição
            $match = $this->avaliarCondicao($valorExtraido, $operador, $valorEsperado);
            
            return [
                'sucesso' => true,
                'valor_extraido' => $valorExtraido,
                'tipo_valor' => gettype($valorExtraido),
                'match' => $match,
                'jsonpath' => $jsonpath
            ];
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao testar JSONPath');
        }
    }

    /**
     * Extrair valor usando JSONPath simples (sem biblioteca externa)
     */
    private function extrairValorJsonPath($dados, string $jsonpath)
    {
        // Remover $ inicial se houver
        $path = ltrim($jsonpath, '$.');
        
        if (empty($path)) {
            return $dados;
        }
        
        // Dividir por . e processar cada parte
        $partes = preg_split('/\.(?![^\[]*\])/', $path);
        $valor = $dados;
        
        foreach ($partes as $parte) {
            if ($valor === null) {
                return null;
            }
            
            // Verificar se tem índice de array [n]
            if (preg_match('/^(\w+)\[(\d+)\]$/', $parte, $m)) {
                $chave = $m[1];
                $indice = (int)$m[2];
                
                if (isset($valor[$chave]) && isset($valor[$chave][$indice])) {
                    $valor = $valor[$chave][$indice];
                } else {
                    return null;
                }
            } elseif (preg_match('/^\[(\d+)\]$/', $parte, $m)) {
                // Apenas índice [n]
                $indice = (int)$m[1];
                if (isset($valor[$indice])) {
                    $valor = $valor[$indice];
                } else {
                    return null;
                }
            } else {
                // Chave simples
                if (isset($valor[$parte])) {
                    $valor = $valor[$parte];
                } else {
                    return null;
                }
            }
        }
        
        return $valor;
    }

    /**
     * Avaliar condição
     */
    private function avaliarCondicao($valor, string $operador, $valorEsperado): bool
    {
        // Converter valor esperado para o tipo apropriado
        if (is_numeric($valor) && is_numeric($valorEsperado)) {
            $valorEsperado = floatval($valorEsperado);
            $valor = floatval($valor);
        }
        
        switch ($operador) {
            case 'equals':
                return $valor == $valorEsperado;
            case 'not_equals':
                return $valor != $valorEsperado;
            case 'contains':
                return is_string($valor) && strpos($valor, $valorEsperado) !== false;
            case 'not_contains':
                return is_string($valor) && strpos($valor, $valorEsperado) === false;
            case 'greater_than':
                return $valor > $valorEsperado;
            case 'less_than':
                return $valor < $valorEsperado;
            case 'greater_or_equal':
                return $valor >= $valorEsperado;
            case 'less_or_equal':
                return $valor <= $valorEsperado;
            case 'is_null':
                return $valor === null;
            case 'is_not_null':
                return $valor !== null;
            case 'is_true':
                return $valor === true || $valor === 'true' || $valor === 1 || $valor === '1';
            case 'is_false':
                return $valor === false || $valor === 'false' || $valor === 0 || $valor === '0';
            case 'regex':
                return is_string($valor) && preg_match($valorEsperado, $valor);
            default:
                return false;
        }
    }

    /**
     * Listar valores capturados
     */
    public function listarValoresCapturados(int $idEvento = null, int $limite = 50): array
    {
        try {
            $sql = "
                SELECT 
                    v.*,
                    e.nome as evento_nome,
                    a.nome as api_nome
                FROM tb_valores_capturados v
                JOIN tb_eventos_api e ON e.id = v.id_evento
                JOIN tb_api_externas a ON a.id = v.id_api
            ";
            
            $params = [];
            if ($idEvento) {
                $sql .= " WHERE v.id_evento = ?";
                $params[] = $idEvento;
            }
            
            $sql .= " ORDER BY v.data_captura DESC LIMIT ?";
            $params[] = $limite;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return [
                'sucesso' => true,
                'dados' => $stmt->fetchAll(\PDO::FETCH_ASSOC)
            ];
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao listar valores');
        }
    }
}
