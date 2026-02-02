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
            $rotinasIds = !empty($_GET['rotinas']) ? explode(',', $_GET['rotinas']) : [];
            
            // Buscar rotinas ativas com agendamento
            $sql = "SELECT id, nome, agendamento_cron, descricao, data_inicio, data_fim
                    FROM tb_rotinas 
                    WHERE ativa = true 
                      AND agendamento_cron IS NOT NULL 
                      AND agendamento_cron != ''";
            
            if (!empty($rotinasIds)) {
                $placeholders = implode(',', array_fill(0, count($rotinasIds), '?'));
                $sql .= " AND id IN ($placeholders)";
            }
            
            $sql .= " ORDER BY nome";
            
            $stmt = $this->db->prepare($sql);
            if (!empty($rotinasIds)) {
                $stmt->execute($rotinasIds);
            } else {
                $stmt->execute();
            }
            
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
