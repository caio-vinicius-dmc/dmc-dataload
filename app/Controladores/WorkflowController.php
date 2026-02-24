<?php
/**
 * Controlador para Workflows
 */

namespace App\Controladores;

use App\Core\Database;
use App\Core\ErrorHandler;

class WorkflowController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConexao();
    }

    // =====================
    // WORKFLOWS
    // =====================

    /**
     * Listar todos os workflows
     */
    public function listar(): array
    {
        try {
            $stmt = $this->db->query("
                SELECT 
                    w.*,
                    (SELECT COUNT(*) FROM tb_workflow_nodes n WHERE n.id_workflow = w.id) as total_nodes,
                    (SELECT COUNT(*) FROM tb_workflow_execucoes e WHERE e.id_workflow = w.id) as total_execucoes,
                    (SELECT COUNT(*) FROM tb_workflow_execucoes e WHERE e.id_workflow = w.id AND e.status = 'completed') as execucoes_sucesso
                FROM tb_workflows w
                ORDER BY w.nome ASC
            ");
            return [
                'sucesso' => true,
                'dados' => $stmt->fetchAll(\PDO::FETCH_ASSOC)
            ];
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao listar workflows');
        }
    }

    /**
     * Buscar workflow por ID
     */
    public function buscar(int $id): array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tb_workflows WHERE id = ?");
            $stmt->execute([$id]);
            $workflow = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$workflow) {
                return ['sucesso' => false, 'erro' => 'Workflow não encontrado'];
            }
            
            // Decodificar dados_json
            $workflow['dados_json'] = json_decode($workflow['dados_json'] ?? '{"nodes":[],"edges":[]}', true);
            $workflow['trigger_config'] = json_decode($workflow['trigger_config'] ?? '{}', true);
            
            // Buscar nodes e edges
            $stmtNodes = $this->db->prepare("SELECT * FROM tb_workflow_nodes WHERE id_workflow = ? ORDER BY ordem_execucao");
            $stmtNodes->execute([$id]);
            $workflow['nodes'] = $stmtNodes->fetchAll(\PDO::FETCH_ASSOC);
            
            $stmtEdges = $this->db->prepare("SELECT * FROM tb_workflow_edges WHERE id_workflow = ? ORDER BY id");
            $stmtEdges->execute([$id]);
            $workflow['edges'] = $stmtEdges->fetchAll(\PDO::FETCH_ASSOC);
            
            return ['sucesso' => true, 'dados' => $workflow];
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao buscar workflow');
        }
    }

    /**
     * Salvar workflow (criar ou atualizar)
     */
    public function salvar(array $data): array
    {
        try {
            $id = !empty($data['id']) ? (int)$data['id'] : null;
            
            // Preparar dados_json
            $dadosJson = [];
            if (isset($data['dados_json'])) {
                if (is_string($data['dados_json'])) {
                    $dadosJson = json_decode($data['dados_json'], true);
                } else {
                    $dadosJson = $data['dados_json'];
                }
            }
            
            // Preparar trigger_config
            $triggerConfig = [];
            if (isset($data['trigger_config'])) {
                if (is_string($data['trigger_config'])) {
                    $triggerConfig = json_decode($data['trigger_config'], true);
                } else {
                    $triggerConfig = $data['trigger_config'];
                }
            }
            
            $this->db->beginTransaction();
            
            $params = [
                $data['nome'] ?? 'Novo Workflow',
                $data['descricao'] ?? null,
                isset($data['ativo']) ? ($data['ativo'] === 'on' || $data['ativo'] === '1' || $data['ativo'] === true) : false,
                json_encode($dadosJson ?: ['nodes' => [], 'edges' => []]),
                $data['trigger_tipo'] ?? 'manual',
                json_encode($triggerConfig ?: [])
            ];
            
            if ($id) {
                // Atualizar
                $versao = (int)($data['versao'] ?? 1);
                $sql = "UPDATE tb_workflows SET 
                    nome = ?, descricao = ?, ativo = ?, dados_json = ?, 
                    trigger_tipo = ?, trigger_config = ?, versao = versao + 1, data_atualizacao = NOW()
                    WHERE id = ?";
                $params[] = $id;
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
            } else {
                // Criar
                $sql = "INSERT INTO tb_workflows 
                    (nome, descricao, ativo, dados_json, trigger_tipo, trigger_config)
                    VALUES (?, ?, ?, ?, ?, ?)
                    RETURNING id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                $id = $stmt->fetchColumn();
            }
            
            // Sincronizar nodes e edges se fornecidos
            if (!empty($dadosJson['nodes'])) {
                $this->sincronizarNodes($id, $dadosJson['nodes']);
            }
            
            if (!empty($dadosJson['edges'])) {
                $this->sincronizarEdges($id, $dadosJson['edges']);
            }
            
            $this->db->commit();
            
            return ['sucesso' => true, 'mensagem' => 'Workflow salvo', 'id' => $id];
        } catch (\Exception $e) {
            $this->db->rollBack();
            return ErrorHandler::tratarErro($e, 'Erro ao salvar workflow');
        }
    }

    /**
     * Sincronizar nodes do workflow
     */
    private function sincronizarNodes(int $idWorkflow, array $nodes): void
    {
        // Remover nodes existentes
        $stmt = $this->db->prepare("DELETE FROM tb_workflow_nodes WHERE id_workflow = ?");
        $stmt->execute([$idWorkflow]);
        
        // Inserir novos nodes
        $sql = "INSERT INTO tb_workflow_nodes 
            (id_workflow, node_id, tipo_node, label, id_referencia, posicao_x, posicao_y, config_json, ordem_execucao)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        foreach ($nodes as $ordem => $node) {
            $config = $node['data'] ?? [];
            
            $stmt->execute([
                $idWorkflow,
                $node['id'] ?? 'node_' . $ordem,
                $node['type'] ?? 'custom',
                $node['data']['label'] ?? 'Nó ' . ($ordem + 1),
                $node['data']['id_referencia'] ?? null,
                (int)($node['position']['x'] ?? 0),
                (int)($node['position']['y'] ?? 0),
                json_encode($config),
                $ordem
            ]);
        }
    }

    /**
     * Sincronizar edges do workflow
     */
    private function sincronizarEdges(int $idWorkflow, array $edges): void
    {
        // Remover edges existentes
        $stmt = $this->db->prepare("DELETE FROM tb_workflow_edges WHERE id_workflow = ?");
        $stmt->execute([$idWorkflow]);
        
        // Inserir novos edges
        $sql = "INSERT INTO tb_workflow_edges 
            (id_workflow, edge_id, node_origem, node_destino, condicao, expressao_condicional, label, estilo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        foreach ($edges as $edge) {
            $stmt->execute([
                $idWorkflow,
                $edge['id'] ?? 'edge_' . uniqid(),
                $edge['source'] ?? '',
                $edge['target'] ?? '',
                $edge['data']['condition'] ?? 'always',
                $edge['data']['expression'] ?? null,
                $edge['label'] ?? null,
                json_encode($edge['style'] ?? [])
            ]);
        }
    }

    /**
     * Deletar workflow
     */
    public function deletar(int $id): array
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM tb_workflows WHERE id = ?");
            $stmt->execute([$id]);
            
            return ['sucesso' => true, 'mensagem' => 'Workflow excluído'];
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao excluir workflow');
        }
    }

    /**
     * Ativar/Desativar workflow
     */
    public function alternarAtivo(int $id): array
    {
        try {
            $stmt = $this->db->prepare("UPDATE tb_workflows SET ativo = NOT ativo, data_atualizacao = NOW() WHERE id = ? RETURNING ativo");
            $stmt->execute([$id]);
            $novoStatus = $stmt->fetchColumn();
            
            return ['sucesso' => true, 'ativo' => $novoStatus];
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao alternar status');
        }
    }

    /**
     * Duplicar workflow
     */
    public function duplicar(int $id): array
    {
        try {
            $original = $this->buscar($id);
            if (!$original['sucesso']) {
                return $original;
            }
            
            $dados = $original['dados'];
            $dados['nome'] = $dados['nome'] . ' (Cópia)';
            $dados['ativo'] = false;
            unset($dados['id']);
            
            return $this->salvar($dados);
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao duplicar workflow');
        }
    }

    // =====================
    // EXECUÇÃO
    // =====================

    /**
     * Executar workflow manualmente
     */
    public function executar(int $id, array $contexto = []): array
    {
        try {
            // Carregar workflow
            $workflowResult = $this->buscar($id);
            if (!$workflowResult['sucesso']) {
                return $workflowResult;
            }
            
            $workflow = $workflowResult['dados'];
            
            // Criar registro de execução
            $stmt = $this->db->prepare("
                INSERT INTO tb_workflow_execucoes 
                (id_workflow, versao_workflow, status, triggered_by, trigger_data, contexto, nodes_total)
                VALUES (?, ?, 'running', 'manual', ?, ?, ?)
                RETURNING id
            ");
            $stmt->execute([
                $id,
                $workflow['versao'],
                json_encode(['manual' => true, 'timestamp' => date('c')]),
                json_encode($contexto),
                count($workflow['dados_json']['nodes'] ?? [])
            ]);
            $execucaoId = $stmt->fetchColumn();
            
            // Executar workflow (em background seria ideal, mas vamos fazer síncrono por enquanto)
            $engine = new \App\Lib\WorkflowEngine($this->db);
            $resultado = $engine->executar($workflow, $execucaoId, $contexto);
            
            // Atualizar status final
            $statusFinal = $resultado['sucesso'] ? 'completed' : 'failed';
            $stmt = $this->db->prepare("
                UPDATE tb_workflow_execucoes SET 
                    status = ?,
                    data_fim = NOW(),
                    duracao_ms = EXTRACT(EPOCH FROM (NOW() - data_inicio)) * 1000,
                    nodes_executados = ?,
                    nodes_sucesso = ?,
                    nodes_falha = ?,
                    resultado_json = ?,
                    erro = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $statusFinal,
                $resultado['nodes_executados'] ?? 0,
                $resultado['nodes_sucesso'] ?? 0,
                $resultado['nodes_falha'] ?? 0,
                json_encode($resultado['detalhes'] ?? []),
                $resultado['erro'] ?? null,
                $execucaoId
            ]);
            
            return [
                'sucesso' => $resultado['sucesso'],
                'execucao_id' => $execucaoId,
                'status' => $statusFinal,
                'resultado' => $resultado
            ];
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao executar workflow');
        }
    }

    /**
     * Listar execuções de workflow
     */
    public function listarExecucoes(int $idWorkflow = null, int $limite = 50): array
    {
        try {
            $sql = "
                SELECT 
                    e.*,
                    w.nome as workflow_nome
                FROM tb_workflow_execucoes e
                JOIN tb_workflows w ON w.id = e.id_workflow
            ";
            
            $params = [];
            if ($idWorkflow) {
                $sql .= " WHERE e.id_workflow = ?";
                $params[] = $idWorkflow;
            }
            
            $sql .= " ORDER BY e.data_inicio DESC LIMIT ?";
            $params[] = $limite;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return [
                'sucesso' => true,
                'dados' => $stmt->fetchAll(\PDO::FETCH_ASSOC)
            ];
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao listar execuções');
        }
    }

    /**
     * Buscar detalhes de uma execução
     */
    public function buscarExecucao(int $id): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    e.*,
                    w.nome as workflow_nome,
                    w.dados_json as workflow_dados
                FROM tb_workflow_execucoes e
                JOIN tb_workflows w ON w.id = e.id_workflow
                WHERE e.id = ?
            ");
            $stmt->execute([$id]);
            $execucao = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$execucao) {
                return ['sucesso' => false, 'erro' => 'Execução não encontrada'];
            }
            
            // Buscar execuções de nodes
            $stmtNodes = $this->db->prepare("
                SELECT * FROM tb_workflow_node_execucoes 
                WHERE id_workflow_execucao = ? 
                ORDER BY ordem
            ");
            $stmtNodes->execute([$id]);
            $execucao['nodes_execucoes'] = $stmtNodes->fetchAll(\PDO::FETCH_ASSOC);
            
            // Decodificar JSONs
            $execucao['trigger_data'] = json_decode($execucao['trigger_data'] ?? '{}', true);
            $execucao['contexto'] = json_decode($execucao['contexto'] ?? '{}', true);
            $execucao['resultado_json'] = json_decode($execucao['resultado_json'] ?? '{}', true);
            $execucao['workflow_dados'] = json_decode($execucao['workflow_dados'] ?? '{}', true);
            
            return ['sucesso' => true, 'dados' => $execucao];
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao buscar execução');
        }
    }

    /**
     * Listar rotinas disponíveis para uso no workflow
     */
    public function listarRotinasDisponiveis(): array
    {
        try {
            $stmt = $this->db->query("
                SELECT id, nome, descricao 
                FROM tb_rotinas 
                WHERE ativo = true
                ORDER BY nome
            ");
            
            return [
                'sucesso' => true,
                'dados' => $stmt->fetchAll(\PDO::FETCH_ASSOC)
            ];
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao listar rotinas');
        }
    }

    /**
     * Obter estatísticas gerais dos workflows
     */
    public function obterEstatisticas(): array
    {
        try {
            // Estatísticas gerais
            $stmtGeral = $this->db->query("
                SELECT 
                    COUNT(*) as total_workflows,
                    SUM(CASE WHEN ativo THEN 1 ELSE 0 END) as workflows_ativos,
                    SUM(CASE WHEN NOT ativo THEN 1 ELSE 0 END) as workflows_inativos
                FROM tb_workflows
            ");
            $geral = $stmtGeral->fetch(\PDO::FETCH_ASSOC);
            
            // Estatísticas de execuções
            $stmtExec = $this->db->query("
                SELECT 
                    COUNT(*) as total_execucoes,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as execucoes_sucesso,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as execucoes_falha,
                    SUM(CASE WHEN status = 'running' THEN 1 ELSE 0 END) as execucoes_em_andamento,
                    AVG(CASE WHEN duracao_ms IS NOT NULL THEN duracao_ms ELSE 0 END) as tempo_medio_ms
                FROM tb_workflow_execucoes
            ");
            $execucoes = $stmtExec->fetch(\PDO::FETCH_ASSOC);
            
            // Últimas execuções (24h)
            $stmtRecentes = $this->db->query("
                SELECT 
                    COUNT(*) as execucoes_24h,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as sucesso_24h,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as falha_24h
                FROM tb_workflow_execucoes
                WHERE data_inicio >= NOW() - INTERVAL '24 hours'
            ");
            $recentes = $stmtRecentes->fetch(\PDO::FETCH_ASSOC);
            
            // Top 5 workflows mais executados
            $stmtTop = $this->db->query("
                SELECT 
                    w.id,
                    w.nome,
                    COUNT(e.id) as total_execucoes,
                    SUM(CASE WHEN e.status = 'completed' THEN 1 ELSE 0 END) as sucesso,
                    SUM(CASE WHEN e.status = 'failed' THEN 1 ELSE 0 END) as falha
                FROM tb_workflows w
                LEFT JOIN tb_workflow_execucoes e ON e.id_workflow = w.id
                GROUP BY w.id, w.nome
                HAVING COUNT(e.id) > 0
                ORDER BY total_execucoes DESC
                LIMIT 5
            ");
            $topWorkflows = $stmtTop->fetchAll(\PDO::FETCH_ASSOC);
            
            // Taxa de sucesso por tipo de trigger
            $stmtTrigger = $this->db->query("
                SELECT 
                    triggered_by,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as sucesso
                FROM tb_workflow_execucoes
                WHERE triggered_by IS NOT NULL
                GROUP BY triggered_by
            ");
            $statsTrigger = $stmtTrigger->fetchAll(\PDO::FETCH_ASSOC);
            
            return [
                'sucesso' => true,
                'dados' => [
                    'geral' => $geral,
                    'execucoes' => $execucoes,
                    'recentes' => $recentes,
                    'top_workflows' => $topWorkflows,
                    'por_trigger' => $statsTrigger
                ]
            ];
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao obter estatísticas');
        }
    }

    /**
     * Cancelar execução em andamento
     */
    public function cancelarExecucao(int $id): array
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE tb_workflow_execucoes 
                SET status = 'cancelled', 
                    data_fim = NOW(),
                    duracao_ms = EXTRACT(EPOCH FROM (NOW() - data_inicio)) * 1000
                WHERE id = ? AND status IN ('pending', 'running')
            ");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                return ['sucesso' => false, 'erro' => 'Execução não encontrada ou já finalizada'];
            }
            
            return ['sucesso' => true, 'mensagem' => 'Execução cancelada'];
        } catch (\Exception $e) {
            return ErrorHandler::tratarErro($e, 'Erro ao cancelar execução');
        }
    }
}
