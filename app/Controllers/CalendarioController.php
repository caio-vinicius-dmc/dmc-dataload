<?php

namespace App\Controllers;

use Cron\CronExpression;

class CalendarioController
{
    private $db;
    
    public function __construct()
    {
        $this->db = \App\Core\Database::getConexao();
    }
    
    /**
     * Retorna eventos do calendário baseados nos agendamentos
     */
    public function getEventos(): void
    {
        header('Content-Type: application/json');
        
        try {
            $inicio = $_GET['inicio'] ?? date('Y-m-01');
            $fim = $_GET['fim'] ?? date('Y-m-t');
            $rotinasIdsRaw = !empty($_GET['rotinas']) ? explode(',', $_GET['rotinas']) : [];
            
            // Separar IDs numéricos (rotinas) de prefixados (pip_, wf_, exec_)
            $rotinasIds = array_values(array_filter($rotinasIdsRaw, function($id) {
                return ctype_digit((string)$id);
            }));
            $pipelineIds = [];
            foreach ($rotinasIdsRaw as $id) {
                if (str_starts_with($id, 'pip_')) {
                    $numId = substr($id, 4);
                    if (ctype_digit($numId)) $pipelineIds[] = $numId;
                }
            }
            
            // Buscar rotinas ativas com agendamento
            $filtroRot = \App\Servicos\ServicoPermissao::filtroVisibilidade('rotina', 'r', 'id_usuario_criador');
            $sql = "SELECT r.id, r.nome, r.agendamento_cron, r.descricao, r.data_inicio, r.data_fim
                    FROM tb_rotinas r
                    WHERE r.ativa = true 
                      AND r.agendamento_cron IS NOT NULL 
                      AND r.agendamento_cron != ''
                      AND ({$filtroRot['where']})";
            
            $params = $filtroRot['params'];
            if (!empty($rotinasIds)) {
                $placeholders = implode(',', array_fill(0, count($rotinasIds), '?'));
                $sql .= " AND r.id IN ($placeholders)";
            }
            
            $sql .= " ORDER BY r.nome";
            
            $stmt = $this->db->prepare($sql);
            // Bind named params from filter + positional from rotinasIds
            $paramIndex = 1;
            foreach ($filtroRot['params'] as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            if (!empty($rotinasIds)) {
                foreach ($rotinasIds as $rid) {
                    $stmt->bindValue($paramIndex++, $rid);
                }
            }
            $stmt->execute();
            
            $rotinas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Cores para as rotinas
            $cores = [
                '#3788d8', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6',
                '#1abc9c', '#34495e', '#e67e22', '#16a085', '#c0392b'
            ];
            
            $eventos = [];
            $dataInicio = new \DateTime($inicio);
            $dataFim = new \DateTime($fim);
            
            foreach ($rotinas as $index => $rotina) {
                $cor = $cores[$index % count($cores)];
                
                try {
                    $cron = new CronExpression($rotina['agendamento_cron']);
                    
                    // Determinar data de início real (a maior entre início do período e data_inicio da rotina)
                    $dataInicioRotina = $rotina['data_inicio'] ? new \DateTime($rotina['data_inicio']) : $dataInicio;
                    $dataFimRotina = $rotina['data_fim'] ? new \DateTime($rotina['data_fim']) : $dataFim;
                    
                    // Se a rotina ainda não começou no período, usar a data de início dela
                    $dataAtual = ($dataInicioRotina > $dataInicio) ? clone $dataInicioRotina : clone $dataInicio;
                    
                    // Se a rotina termina antes do fim do período, usar a data fim dela
                    $dataLimite = ($dataFimRotina < $dataFim) ? $dataFimRotina : $dataFim;
                    
                    $execucoes = 0;
                    $maxExecucoes = 100; // Limite para não sobrecarregar
                    
                    while ($dataAtual <= $dataLimite && $execucoes < $maxExecucoes) {
                        $proximaExecucao = $cron->getNextRunDate($dataAtual);
                        
                        if ($proximaExecucao > $dataLimite) {
                            break;
                        }
                        
                        // Verificar se está dentro do período válido da rotina
                        if ($proximaExecucao < $dataInicioRotina) {
                            $dataAtual = $proximaExecucao;
                            continue;
                        }
                        
                        $eventos[] = [
                            'id' => $rotina['id'] . '_' . $proximaExecucao->getTimestamp(),
                            'rotina_id' => $rotina['id'],
                            'rotina_nome' => $rotina['nome'],
                            'titulo' => $rotina['nome'],
                            'descricao' => $rotina['descricao'] ?: 'Execução agendada',
                            'data' => $proximaExecucao->format('Y-m-d\TH:i:s'),
                            'cron' => $rotina['agendamento_cron'],
                            'cor' => $cor
                        ];
                        
                        $dataAtual = $proximaExecucao;
                        $execucoes++;
                    }
                    
                } catch (\Exception $e) {
                    error_log("Erro ao processar CRON da rotina {$rotina['id']}: " . $e->getMessage());
                    continue;
                }
            }

            // Buscar pipelines com trigger cron
            $filtroPip = \App\Servicos\ServicoPermissao::filtroVisibilidade('pipeline', 'p', 'criado_por');
            $sqlPip = "SELECT p.id, p.nome, p.agendamento_cron, p.descricao
                       FROM tb_pipelines p
                       WHERE p.ativo = true
                         AND p.trigger_tipo = 'cron'
                         AND p.agendamento_cron IS NOT NULL
                         AND p.agendamento_cron != ''
                         AND ({$filtroPip['where']})";
            
            // Se há filtro ativo e tem pipeline IDs, filtrar
            $pipParams = [];
            if (!empty($rotinasIdsRaw) && !empty($pipelineIds)) {
                $placeholdersPip = implode(',', array_fill(0, count($pipelineIds), '?'));
                $sqlPip .= " AND p.id IN ($placeholdersPip)";
                $pipParams = $pipelineIds;
            } elseif (!empty($rotinasIdsRaw) && empty($pipelineIds)) {
                $sqlPip .= " AND 1=0";
            }
            
            $sqlPip .= " ORDER BY p.nome";
            $stmtPip = $this->db->prepare($sqlPip);
            $pipParamIndex = 1;
            foreach ($filtroPip['params'] as $key => $val) {
                $stmtPip->bindValue($key, $val);
            }
            foreach ($pipParams as $pid) {
                $stmtPip->bindValue($pipParamIndex++, $pid);
            }
            $stmtPip->execute();
            $pipelines = $stmtPip->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($pipelines as $pip) {
                $cor = '#17a2b8';
                try {
                    $cron = new CronExpression($pip['agendamento_cron']);
                    $dataAtualPip = clone $dataInicio;
                    $execucoesPip = 0;

                    while ($dataAtualPip <= $dataFim && $execucoesPip < 100) {
                        $proximaExecucao = $cron->getNextRunDate($dataAtualPip);
                        if ($proximaExecucao > $dataFim) break;

                        $eventos[] = [
                            'id' => 'pip_' . $pip['id'] . '_' . $proximaExecucao->getTimestamp(),
                            'rotina_id' => 'pip_' . $pip['id'],
                            'rotina_nome' => $pip['nome'],
                            'titulo' => '[Pipeline] ' . $pip['nome'],
                            'descricao' => $pip['descricao'] ?: 'Pipeline agendado',
                            'data' => $proximaExecucao->format('Y-m-d\TH:i:s'),
                            'cron' => $pip['agendamento_cron'],
                            'cor' => $cor,
                            'tipo' => 'pipeline'
                        ];

                        $dataAtualPip = $proximaExecucao;
                        $execucoesPip++;
                    }
                } catch (\Exception $e) {
                    error_log("Erro ao processar CRON do pipeline {$pip['id']}: " . $e->getMessage());
                    continue;
                }
            }
            
            // Buscar execuções passadas (rotinas)
            try {
            $filtroExecRot = \App\Servicos\ServicoPermissao::filtroVisibilidadePosicional('rotina', 'r', 'id_usuario_criador');
            $sqlExecRotinas = "SELECT l.id, l.id_rotina, r.nome, l.status, l.data_inicio, l.data_fim, l.duracao_ms
                               FROM tb_logs_execucao l
                               LEFT JOIN tb_rotinas r ON l.id_rotina = r.id
                               WHERE l.data_inicio >= ? AND l.data_inicio <= ? AND ({$filtroExecRot['where']})
                               ORDER BY l.data_inicio DESC
                               LIMIT 200";
            $stmtExecR = $this->db->prepare($sqlExecRotinas);
            $stmtExecR->execute(array_merge([$inicio, $fim], $filtroExecRot['params']));
            $execRotinas = $stmtExecR->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($execRotinas as $exec) {
                if (empty($exec['data_inicio'])) continue;
                $corExec = $exec['status'] === 'sucesso' ? '#28a745' : ($exec['status'] === 'falha' || $exec['status'] === 'erro' ? '#dc3545' : '#ffc107');
                $statusLabel = ucfirst($exec['status']);
                $duracao = $exec['duracao_ms'] ? round($exec['duracao_ms'] / 1000, 1) . 's' : '';
                $eventos[] = [
                    'id' => 'exec_r_' . $exec['id'],
                    'rotina_id' => $exec['id_rotina'],
                    'rotina_nome' => $exec['nome'] ?? 'Rotina #' . $exec['id_rotina'],
                    'titulo' => '[Executado] ' . ($exec['nome'] ?? 'Rotina #' . $exec['id_rotina']),
                    'descricao' => "Status: {$statusLabel}" . ($duracao ? " | Duração: {$duracao}" : ''),
                    'data' => $exec['data_inicio'],
                    'cron' => null,
                    'cor' => $corExec,
                    'tipo' => 'execucao_rotina',
                    'status' => $exec['status']
                ];
            }
            } catch (\Exception $e) {
                error_log("Erro ao buscar execuções de rotinas: " . $e->getMessage());
            }

            // Buscar execuções passadas (pipelines)
            try {
            $filtroExecPip = \App\Servicos\ServicoPermissao::filtroVisibilidadePosicional('pipeline', 'p', 'criado_por');
            $sqlExecPip = "SELECT pe.id, pe.id_pipeline, p.nome, pe.status, pe.data_inicio, pe.data_fim, pe.duracao_ms
                           FROM tb_pipeline_execucoes pe
                           LEFT JOIN tb_pipelines p ON pe.id_pipeline = p.id
                           WHERE pe.data_inicio >= ? AND pe.data_inicio <= ? AND ({$filtroExecPip['where']})
                           ORDER BY pe.data_inicio DESC
                           LIMIT 200";
            $stmtExecP = $this->db->prepare($sqlExecPip);
            $stmtExecP->execute(array_merge([$inicio, $fim], $filtroExecPip['params']));
            $execPipelines = $stmtExecP->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($execPipelines as $exec) {
                if (empty($exec['data_inicio'])) continue;
                $statusNorm = str_replace(['success','error','running'], ['sucesso','falha','executando'], $exec['status']);
                $corExec = $statusNorm === 'sucesso' ? '#17a2b8' : ($statusNorm === 'falha' ? '#dc3545' : '#ffc107');
                $duracao = $exec['duracao_ms'] ? round($exec['duracao_ms'] / 1000, 1) . 's' : '';
                $eventos[] = [
                    'id' => 'exec_p_' . $exec['id'],
                    'rotina_id' => 'pip_' . $exec['id_pipeline'],
                    'rotina_nome' => $exec['nome'] ?? 'Pipeline #' . $exec['id_pipeline'],
                    'titulo' => '[Exec Pipeline] ' . ($exec['nome'] ?? 'Pipeline #' . $exec['id_pipeline']),
                    'descricao' => "Status: " . ucfirst($statusNorm) . ($duracao ? " | Duração: {$duracao}" : ''),
                    'data' => $exec['data_inicio'],
                    'cron' => null,
                    'cor' => $corExec,
                    'tipo' => 'execucao_pipeline',
                    'status' => $exec['status']
                ];
            }
            } catch (\Exception $e) {
                error_log("Erro ao buscar execuções de pipelines: " . $e->getMessage());
            }

            // Buscar execuções passadas (workflows)
            try {
            $filtroExecWf = \App\Servicos\ServicoPermissao::filtroVisibilidadePosicional('workflow', 'w', 'criado_por');
            $sqlExecWf = "SELECT we.id, we.id_workflow, w.nome, we.status, we.data_inicio, we.data_fim, we.duracao_ms
                          FROM tb_workflow_execucoes we
                          LEFT JOIN tb_workflows w ON we.id_workflow = w.id
                          WHERE we.data_inicio >= ? AND we.data_inicio <= ? AND ({$filtroExecWf['where']})
                          ORDER BY we.data_inicio DESC
                          LIMIT 200";
            $stmtExecW = $this->db->prepare($sqlExecWf);
            $stmtExecW->execute(array_merge([$inicio, $fim], $filtroExecWf['params']));
            $execWorkflows = $stmtExecW->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($execWorkflows as $exec) {
                if (empty($exec['data_inicio'])) continue;
                $statusNorm = str_replace(['completed','failed','running'], ['sucesso','falha','executando'], $exec['status']);
                $corExec = $statusNorm === 'sucesso' ? '#6f42c1' : ($statusNorm === 'falha' ? '#dc3545' : '#ffc107');
                $duracao = $exec['duracao_ms'] ? round($exec['duracao_ms'] / 1000, 1) . 's' : '';
                $eventos[] = [
                    'id' => 'exec_w_' . $exec['id'],
                    'rotina_id' => 'wf_' . $exec['id_workflow'],
                    'rotina_nome' => $exec['nome'] ?? 'Workflow #' . $exec['id_workflow'],
                    'titulo' => '[Exec Workflow] ' . ($exec['nome'] ?? 'Workflow #' . $exec['id_workflow']),
                    'descricao' => "Status: " . ucfirst($statusNorm) . ($duracao ? " | Duração: {$duracao}" : ''),
                    'data' => $exec['data_inicio'],
                    'cron' => null,
                    'cor' => $corExec,
                    'tipo' => 'execucao_workflow',
                    'status' => $exec['status']
                ];
            }
            } catch (\Exception $e) {
                error_log("Erro ao buscar execuções de workflows: " . $e->getMessage());
            }

            echo json_encode([
                'sucesso' => true,
                'eventos' => $eventos,
                'total' => count($eventos),
                'periodo' => [
                    'inicio' => $inicio,
                    'fim' => $fim
                ]
            ]);
            
        } catch (\Exception $e) {
            error_log("Erro ao buscar eventos do calendário: " . $e->getMessage());
            echo json_encode([
                'sucesso' => false,
                'erro' => $e->getMessage()
            ]);
        }
    }
}
