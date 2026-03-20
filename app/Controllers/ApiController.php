<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\CronParser;
use App\Core\Logger;

/**
 * Controlador de API - Endpoints auxiliares
 */
class ApiController
{
    /**
     * Health check do sistema
     */
    public static function health(): void
    {
        header('Content-Type: application/json');
        
        $status = [
            'status' => 'ok',
            'timestamp' => time(),
            'versao' => '2.0.0'
        ];
        
        // Verificar conexão com banco
        try {
            $db = Database::getConexao();
            $db->query('SELECT 1');
            $status['database'] = 'ok';
        } catch (\Exception $e) {
            $status['database'] = 'error';
            $status['status'] = 'degraded';
        }
        
        echo json_encode($status);
    }
    
    /**
     * Retorna presets de expressões cron
     */
    public static function cronPresets(): void
    {
        header('Content-Type: application/json');
        
        $presets = CronParser::presets();
        
        // Formatar para o frontend
        $resultado = [];
        foreach ($presets as $label => $valor) {
            $resultado[] = [
                'valor' => $valor,
                'label' => $label,
                'descricao' => CronParser::descrever($valor)
            ];
        }
        
        echo json_encode([
            'sucesso' => true,
            'presets' => $resultado
        ]);
    }
    
    /**
     * Valida uma expressão cron
     */
    public static function validarCron(): void
    {
        header('Content-Type: application/json');
        
        $expressao = $_POST['expressao'] ?? $_GET['expressao'] ?? '';
        
        $resultado = CronParser::validar($expressao);
        
        if ($resultado['valida']) {
            $resultado['descricao'] = CronParser::descrever($expressao);
            $resultado['proximas'] = CronParser::proximasExecucoes($expressao, 5);
        }
        
        echo json_encode($resultado);
    }
    
    /**
     * Retorna logs do sistema (com RBAC)
     */
    public static function logs(): void
    {
        header('Content-Type: application/json');
        
        $nivel = $_GET['nivel'] ?? null;
        $canal = $_GET['canal'] ?? null;
        $limite = min(intval($_GET['limite'] ?? 100), 1000);
        
        try {
            $db = Database::getConexao();
            
            $sql = "SELECT id, nivel, mensagem, contexto, canal, ip_address, criado_em 
                    FROM tb_logs_sistema 
                    WHERE 1=1";
            $params = [];
            
            // RBAC: não-admins só veem seus próprios logs
            $usuario = \App\Core\AuthMiddleware::obterUsuario();
            $idUsuario = \App\Core\AuthMiddleware::obterUsuarioId();
            $nivelAcesso = $usuario['nivel_acesso'] ?? 'operador';
            if (!in_array($nivelAcesso, ['super_admin', 'admin'])) {
                $sql .= " AND id_usuario = :rbac_uid";
                $params[':rbac_uid'] = $idUsuario;
            }
            
            if ($nivel) {
                $sql .= " AND nivel = :nivel";
                $params[':nivel'] = strtoupper($nivel);
            }
            
            if ($canal) {
                $sql .= " AND canal = :canal";
                $params[':canal'] = $canal;
            }
            
            $sql .= " ORDER BY criado_em DESC LIMIT :limite";
            $params[':limite'] = $limite;
            
            $stmt = $db->prepare($sql);
            foreach ($params as $key => $value) {
                if ($key === ':limite') {
                    $stmt->bindValue($key, $value, \PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $value);
                }
            }
            $stmt->execute();
            $logs = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Decodificar contexto JSON
            foreach ($logs as &$log) {
                $log['contexto'] = json_decode($log['contexto'], true) ?: [];
            }
            
            echo json_encode([
                'sucesso' => true,
                'dados' => $logs
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'sucesso' => false,
                'erro' => 'Erro ao buscar logs: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Estatísticas de logs (com RBAC)
     */
    public static function estatisticasLogs(): void
    {
        header('Content-Type: application/json');
        
        try {
            $db = Database::getConexao();
            
            // RBAC: não-admins só veem seus próprios logs
            $usuario = \App\Core\AuthMiddleware::obterUsuario();
            $idUsuario = \App\Core\AuthMiddleware::obterUsuarioId();
            $nivelAcesso = $usuario['nivel_acesso'] ?? 'operador';
            $rbacWhere = '';
            $rbacParams = [];
            if (!in_array($nivelAcesso, ['super_admin', 'admin'])) {
                $rbacWhere = ' AND id_usuario = ?';
                $rbacParams = [$idUsuario];
            }
            
            // Contagem por nível nas últimas 24h
            $stmt = $db->prepare("
                SELECT nivel, COUNT(*) as total
                FROM tb_logs_sistema
                WHERE criado_em > CURRENT_TIMESTAMP - INTERVAL '24 hours'{$rbacWhere}
                GROUP BY nivel
            ");
            $stmt->execute($rbacParams);
            $porNivel = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Contagem por canal nas últimas 24h
            $stmt = $db->prepare("
                SELECT canal, COUNT(*) as total
                FROM tb_logs_sistema
                WHERE criado_em > CURRENT_TIMESTAMP - INTERVAL '24 hours'{$rbacWhere}
                GROUP BY canal
                ORDER BY total DESC
                LIMIT 10
            ");
            $stmt->execute($rbacParams);
            $porCanal = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Total geral
            $stmt = $db->prepare("SELECT COUNT(*) FROM tb_logs_sistema WHERE 1=1{$rbacWhere}");
            $stmt->execute($rbacParams);
            $total = $stmt->fetchColumn();
            
            echo json_encode([
                'sucesso' => true,
                'estatisticas' => [
                    'total' => (int)$total,
                    'por_nivel' => $porNivel,
                    'por_canal' => $porCanal
                ]
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'sucesso' => false,
                'erro' => 'Erro ao buscar estatísticas: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Status dos workers
     */
    public static function workersStatus(): void
    {
        header('Content-Type: application/json');
        
        try {
            $db = Database::getConexao();
            
            $stmt = $db->query("SELECT * FROM vw_status_workers");
            $workers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $ativos = count(array_filter($workers, fn($w) => $w['status'] === 'ativo'));
            
            echo json_encode([
                'sucesso' => true,
                'workers' => $workers,
                'resumo' => [
                    'total' => count($workers),
                    'ativos' => $ativos,
                    'inativos' => count($workers) - $ativos
                ]
            ]);
            
        } catch (\Exception $e) {
            // Se a view não existir, retornar array vazio
            echo json_encode([
                'sucesso' => true,
                'workers' => [],
                'resumo' => ['total' => 0, 'ativos' => 0, 'inativos' => 0]
            ]);
        }
    }
    
    /**
     * Versão da API
     */
    public static function versao(): void
    {
        header('Content-Type: application/json');
        
        echo json_encode([
            'versao' => '2.0.0',
            'php' => PHP_VERSION,
            'data_build' => date('Y-m-d H:i:s'),
            'features' => [
                'cron_parser' => true,
                'retry_handler' => true,
                'structured_logging' => true,
                'timeout_manager' => true
            ]
        ]);
    }
    
    /**
     * Métricas para Prometheus
     */
    public static function metrics(): void
    {
        header('Content-Type: text/plain');
        
        try {
            $db = Database::getConexao();
            
            // Total de rotinas
            $stmt = $db->query("SELECT COUNT(*) FROM tb_rotinas");
            $totalRotinas = $stmt->fetchColumn();
            
            // Rotinas ativas
            $stmt = $db->query("SELECT COUNT(*) FROM tb_rotinas WHERE ativa = true");
            $rotinasAtivas = $stmt->fetchColumn();
            
            // Execuções hoje
            $stmt = $db->query("
                SELECT COUNT(*) FROM tb_logs_execucao 
                WHERE DATE(data_inicio) = CURRENT_DATE
            ");
            $execucoesHoje = $stmt->fetchColumn();
            
            // Falhas hoje
            $stmt = $db->query("
                SELECT COUNT(*) FROM tb_logs_execucao 
                WHERE DATE(data_inicio) = CURRENT_DATE AND status = 'falha'
            ");
            $falhasHoje = $stmt->fetchColumn();
            
            // Tempo médio de execução
            $stmt = $db->query("
                SELECT COALESCE(AVG(duracao_ms), 0) FROM tb_logs_execucao 
                WHERE duracao_ms IS NOT NULL AND data_inicio > CURRENT_TIMESTAMP - INTERVAL '24 hours'
            ");
            $tempoMedio = $stmt->fetchColumn();
            
            // Formato Prometheus
            $output = "# HELP dmc_dataload_rotinas_total Total de rotinas cadastradas\n";
            $output .= "# TYPE dmc_dataload_rotinas_total gauge\n";
            $output .= "dmc_dataload_rotinas_total {$totalRotinas}\n\n";
            
            $output .= "# HELP dmc_dataload_rotinas_ativas Rotinas ativas\n";
            $output .= "# TYPE dmc_dataload_rotinas_ativas gauge\n";
            $output .= "dmc_dataload_rotinas_ativas {$rotinasAtivas}\n\n";
            
            $output .= "# HELP dmc_dataload_execucoes_hoje Execuções hoje\n";
            $output .= "# TYPE dmc_dataload_execucoes_hoje counter\n";
            $output .= "dmc_dataload_execucoes_hoje {$execucoesHoje}\n\n";
            
            $output .= "# HELP dmc_dataload_falhas_hoje Falhas hoje\n";
            $output .= "# TYPE dmc_dataload_falhas_hoje counter\n";
            $output .= "dmc_dataload_falhas_hoje {$falhasHoje}\n\n";
            
            $output .= "# HELP dmc_dataload_tempo_medio_ms Tempo médio de execução (ms)\n";
            $output .= "# TYPE dmc_dataload_tempo_medio_ms gauge\n";
            $output .= "dmc_dataload_tempo_medio_ms " . round($tempoMedio, 2) . "\n";
            
            echo $output;
            
        } catch (\Exception $e) {
            echo "# Error: " . $e->getMessage() . "\n";
        }
    }
}
