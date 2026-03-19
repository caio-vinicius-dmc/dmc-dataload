<?php
/**
 * Motor de Execução de Workflows
 * Interpreta o grafo do workflow e executa os nós em ordem
 */

namespace App\Lib;

use PDO;
use App\Core\Database;
use App\Controllers\RotinasController2 as RotinasController;

class WorkflowEngine
{
    private PDO $db;
    private array $contexto = [];
    private int $execucaoId;
    private array $resultadosNodes = [];
    private int $nodesExecutados = 0;
    private int $nodesSucesso = 0;
    private int $nodesFalha = 0;

    public function __construct(PDO $db = null)
    {
        $this->db = $db ?? Database::getConexao();
    }

    /**
     * Executar workflow
     */
    public function executar(array $workflow, int $execucaoId, array $contextoInicial = []): array
    {
        $this->execucaoId = $execucaoId;
        $this->contexto = $contextoInicial;
        $this->resultadosNodes = [];
        $this->nodesExecutados = 0;
        $this->nodesSucesso = 0;
        $this->nodesFalha = 0;
        
        try {
            $dadosJson = $workflow['dados_json'] ?? [];
            $nodes = $dadosJson['nodes'] ?? [];
            $edges = $dadosJson['edges'] ?? [];
            
            if (empty($nodes)) {
                return [
                    'sucesso' => true,
                    'mensagem' => 'Workflow vazio',
                    'nodes_executados' => 0,
                    'nodes_sucesso' => 0,
                    'nodes_falha' => 0,
                    'detalhes' => []
                ];
            }
            
            // Construir grafo de adjacência
            $grafo = $this->construirGrafo($nodes, $edges);
            
            // Encontrar nó inicial (trigger ou primeiro nó)
            $nodeInicial = $this->encontrarNodeInicial($nodes);
            
            if (!$nodeInicial) {
                return [
                    'sucesso' => false,
                    'erro' => 'Nenhum nó inicial encontrado',
                    'nodes_executados' => 0,
                    'nodes_sucesso' => 0,
                    'nodes_falha' => 0,
                    'detalhes' => []
                ];
            }
            
            // Executar em ordem topológica
            $this->executarNode($nodeInicial, $nodes, $edges, $grafo);
            
            return [
                'sucesso' => $this->nodesFalha === 0,
                'nodes_executados' => $this->nodesExecutados,
                'nodes_sucesso' => $this->nodesSucesso,
                'nodes_falha' => $this->nodesFalha,
                'detalhes' => $this->resultadosNodes,
                'contexto_final' => $this->contexto
            ];
            
        } catch (\Exception $e) {
            return [
                'sucesso' => false,
                'erro' => $e->getMessage(),
                'nodes_executados' => $this->nodesExecutados,
                'nodes_sucesso' => $this->nodesSucesso,
                'nodes_falha' => $this->nodesFalha,
                'detalhes' => $this->resultadosNodes
            ];
        }
    }

    /**
     * Construir grafo de adjacência
     */
    private function construirGrafo(array $nodes, array $edges): array
    {
        $grafo = [];
        
        // Inicializar com todos os nodes
        foreach ($nodes as $node) {
            $nodeId = $node['id'];
            $grafo[$nodeId] = [
                'node' => $node,
                'saidas' => [],
                'entradas' => []
            ];
        }
        
        // Adicionar edges
        foreach ($edges as $edge) {
            $origem = $edge['source'];
            $destino = $edge['target'];
            
            if (isset($grafo[$origem]) && isset($grafo[$destino])) {
                $grafo[$origem]['saidas'][] = [
                    'destino' => $destino,
                    'condicao' => $edge['data']['condition'] ?? 'always',
                    'expressao' => $edge['data']['expression'] ?? null
                ];
                $grafo[$destino]['entradas'][] = $origem;
            }
        }
        
        return $grafo;
    }

    /**
     * Encontrar nó inicial
     */
    private function encontrarNodeInicial(array $nodes): ?array
    {
        // Prioridade 1: Nó do tipo trigger
        foreach ($nodes as $node) {
            if (($node['type'] ?? '') === 'trigger') {
                return $node;
            }
        }
        
        // Prioridade 2: Primeiro nó
        return $nodes[0] ?? null;
    }

    /**
     * Executar nó e seus sucessores
     */
    private function executarNode(array $node, array $allNodes, array $allEdges, array $grafo): void
    {
        $nodeId = $node['id'];
        
        // Evitar execução duplicada
        if (isset($this->resultadosNodes[$nodeId])) {
            return;
        }
        
        $tipo = $node['type'] ?? 'custom';
        $label = $node['data']['label'] ?? 'Nó';
        $config = $node['data'] ?? [];
        
        // Criar registro de execução do nó
        $stmtInsert = $this->db->prepare("
            INSERT INTO tb_workflow_node_execucoes 
            (id_workflow_execucao, node_id, tipo_node, label, status, data_inicio, input_data, ordem)
            VALUES (?, ?, ?, ?, 'running', NOW(), ?, ?)
            RETURNING id
        ");
        $stmtInsert->execute([
            $this->execucaoId,
            $nodeId,
            $tipo,
            $label,
            json_encode(['contexto' => $this->contexto]),
            $this->nodesExecutados
        ]);
        $nodeExecId = $stmtInsert->fetchColumn();
        
        $inicio = microtime(true);
        $resultado = null;
        $erro = null;
        $status = 'completed';
        
        try {
            // Executar baseado no tipo
            switch ($tipo) {
                case 'trigger':
                    $resultado = $this->executarTrigger($config);
                    break;
                    
                case 'rotina':
                    $resultado = $this->executarRotina($config);
                    break;
                    
                case 'condition':
                    $resultado = $this->executarCondition($config);
                    break;
                    
                case 'delay':
                    $resultado = $this->executarDelay($config);
                    break;
                    
                case 'notification':
                    $resultado = $this->executarNotification($config);
                    break;
                    
                case 'set_variable':
                    $resultado = $this->executarSetVariable($config);
                    break;
                    
                case 'end':
                    $resultado = ['status' => 'completed', 'mensagem' => 'Workflow finalizado'];
                    break;
                    
                default:
                    $resultado = ['status' => 'unknown', 'tipo' => $tipo];
            }
            
            $this->nodesSucesso++;
            
        } catch (\Exception $e) {
            $status = 'failed';
            $erro = $e->getMessage();
            $this->nodesFalha++;
        }
        
        $fim = microtime(true);
        $duracaoMs = (int)(($fim - $inicio) * 1000);
        
        // Atualizar registro de execução do nó
        $stmtUpdate = $this->db->prepare("
            UPDATE tb_workflow_node_execucoes SET 
                status = ?,
                data_fim = NOW(),
                duracao_ms = ?,
                output_data = ?,
                erro = ?
            WHERE id = ?
        ");
        $stmtUpdate->execute([
            $status,
            $duracaoMs,
            json_encode($resultado ?? []),
            $erro,
            $nodeExecId
        ]);
        
        $this->nodesExecutados++;
        $this->resultadosNodes[$nodeId] = [
            'status' => $status,
            'resultado' => $resultado,
            'erro' => $erro,
            'duracao_ms' => $duracaoMs
        ];
        
        // Armazenar resultado no contexto
        $this->contexto['nodes'][$nodeId] = $resultado;
        
        // Encontrar e executar próximos nós
        if (isset($grafo[$nodeId])) {
            foreach ($grafo[$nodeId]['saidas'] as $saida) {
                $destino = $saida['destino'];
                $condicao = $saida['condicao'];
                
                // Verificar condição
                $deveExecutar = $this->avaliarCondicaoEdge($condicao, $saida['expressao'], $resultado, $status);
                
                if ($deveExecutar && isset($grafo[$destino])) {
                    $this->executarNode($grafo[$destino]['node'], $allNodes, $allEdges, $grafo);
                }
            }
        }
    }

    /**
     * Avaliar condição de edge
     */
    private function avaliarCondicaoEdge(string $condicao, ?string $expressao, $resultado, string $statusNode): bool
    {
        switch ($condicao) {
            case 'always':
                return true;
                
            case 'when_success':
                return $statusNode === 'completed';
                
            case 'when_error':
                return $statusNode === 'failed';
                
            case 'when_true':
                return $resultado === true || ($resultado['value'] ?? null) === true;
                
            case 'when_false':
                return $resultado === false || ($resultado['value'] ?? null) === false;
                
            case 'custom':
                return $this->avaliarExpressao($expressao);
                
            default:
                return true;
        }
    }

    /**
     * Avaliar expressão customizada
     */
    private function avaliarExpressao(?string $expressao): bool
    {
        if (empty($expressao)) {
            return true;
        }
        
        // Substituir variáveis de contexto
        $expressaoProcessada = preg_replace_callback(
            '/\$\{(\w+(?:\.\w+)*)\}/',
            function ($matches) {
                return $this->obterValorContexto($matches[1]);
            },
            $expressao
        );
        
        // Avaliar expressão simples (cuidado com eval!)
        // Por segurança, vamos implementar um parser simples
        if (preg_match('/^(\d+(?:\.\d+)?)\s*(==|!=|>|<|>=|<=)\s*(\d+(?:\.\d+)?)$/', $expressaoProcessada, $m)) {
            $v1 = floatval($m[1]);
            $op = $m[2];
            $v2 = floatval($m[3]);
            
            switch ($op) {
                case '==': return $v1 == $v2;
                case '!=': return $v1 != $v2;
                case '>':  return $v1 > $v2;
                case '<':  return $v1 < $v2;
                case '>=': return $v1 >= $v2;
                case '<=': return $v1 <= $v2;
            }
        }
        
        return true;
    }

    /**
     * Obter valor do contexto
     */
    private function obterValorContexto(string $path)
    {
        $partes = explode('.', $path);
        $valor = $this->contexto;
        
        foreach ($partes as $parte) {
            if (is_array($valor) && isset($valor[$parte])) {
                $valor = $valor[$parte];
            } else {
                return null;
            }
        }
        
        return $valor;
    }

    // =====================
    // EXECUTORES DE TIPOS
    // =====================

    private function executarTrigger(array $config): array
    {
        return [
            'status' => 'triggered',
            'tipo' => $config['trigger_type'] ?? 'manual',
            'timestamp' => date('c')
        ];
    }

    private function executarRotina(array $config): array
    {
        $idRotina = $config['id_referencia'] ?? $config['rotina_id'] ?? null;
        
        if (!$idRotina) {
            throw new \Exception('ID da rotina não especificado');
        }
        
        $controller = new RotinasController();
        $resultado = $controller->executar((int)$idRotina);
        
        if (!$resultado['sucesso']) {
            throw new \Exception($resultado['erro'] ?? 'Erro ao executar rotina');
        }
        
        return [
            'status' => 'completed',
            'rotina_id' => $idRotina,
            'registros' => $resultado['registros'] ?? 0,
            'duracao_ms' => $resultado['duracao_ms'] ?? 0
        ];
    }

    private function executarCondition(array $config): array
    {
        $expressao = $config['expression'] ?? '';
        $resultado = $this->avaliarExpressao($expressao);
        
        return [
            'status' => 'evaluated',
            'expression' => $expressao,
            'value' => $resultado
        ];
    }

    private function executarDelay(array $config): array
    {
        $segundos = (int)($config['seconds'] ?? 1);
        $segundos = min($segundos, 60); // Máximo 60 segundos para evitar timeout
        
        sleep($segundos);
        
        return [
            'status' => 'completed',
            'delay_seconds' => $segundos
        ];
    }

    private function executarNotification(array $config): array
    {
        $tipo = $config['notification_type'] ?? 'log';
        $mensagem = $config['message'] ?? 'Notificação do workflow';
        
        // Substituir variáveis na mensagem
        $mensagem = preg_replace_callback(
            '/\{\{(\w+(?:\.\w+)*)\}\}/',
            function ($matches) {
                $valor = $this->obterValorContexto($matches[1]);
                return is_scalar($valor) ? $valor : json_encode($valor);
            },
            $mensagem
        );
        
        switch ($tipo) {
            case 'log':
                error_log("[Workflow Notification] $mensagem");
                break;
                
            case 'database':
                $stmt = $this->db->prepare("
                    INSERT INTO tb_logs_sistema (nivel, mensagem, contexto, data_criacao)
                    VALUES ('info', ?, ?, NOW())
                ");
                $stmt->execute([$mensagem, json_encode(['workflow_execucao' => $this->execucaoId])]);
                break;
                
            // Outros tipos podem ser implementados (email, webhook, etc)
        }
        
        return [
            'status' => 'sent',
            'type' => $tipo,
            'message' => $mensagem
        ];
    }

    private function executarSetVariable(array $config): array
    {
        $nome = $config['variable_name'] ?? 'var';
        $valor = $config['variable_value'] ?? null;
        
        // Substituir variáveis no valor
        if (is_string($valor)) {
            $valor = preg_replace_callback(
                '/\{\{(\w+(?:\.\w+)*)\}\}/',
                function ($matches) {
                    return $this->obterValorContexto($matches[1]);
                },
                $valor
            );
        }
        
        $this->contexto['variables'][$nome] = $valor;
        
        return [
            'status' => 'set',
            'variable' => $nome,
            'value' => $valor
        ];
    }
}
