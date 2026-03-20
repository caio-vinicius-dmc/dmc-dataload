<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\AuthMiddleware;
use PDO;
use Exception;

/**
 * Controlador de Logs do Sistema
 */
class LogsController
{
    /**
     * Listar logs com filtros e paginação (com RBAC)
     */
    public function listar()
    {
        try {
            $db = Database::getConexao();
            
            $pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
            $porPagina = isset($_GET['por_pagina']) ? min(100, max(10, (int)$_GET['por_pagina'])) : 50;
            $offset = ($pagina - 1) * $porPagina;
            
            $filtros = [];
            $params = [];
            
            // RBAC: não-admins só veem seus próprios logs
            $usuario = AuthMiddleware::obterUsuario();
            $idUsuario = AuthMiddleware::obterUsuarioId();
            $nivel = $usuario['nivel_acesso'] ?? 'operador';
            if (!in_array($nivel, ['super_admin', 'admin'])) {
                $filtros[] = "id_usuario = ?";
                $params[] = $idUsuario;
            }
            
            // Filtros
            if (!empty($_GET['nivel'])) {
                $filtros[] = "nivel = ?";
                $params[] = strtoupper($_GET['nivel']);
            }
            
            if (!empty($_GET['canal'])) {
                $filtros[] = "canal = ?";
                $params[] = $_GET['canal'];
            }
            
            if (!empty($_GET['data_de'])) {
                $filtros[] = "criado_em >= ?";
                $params[] = $_GET['data_de'] . ' 00:00:00';
            }
            
            if (!empty($_GET['data_ate'])) {
                $filtros[] = "criado_em <= ?";
                $params[] = $_GET['data_ate'] . ' 23:59:59';
            }
            
            if (!empty($_GET['busca'])) {
                $filtros[] = "mensagem ILIKE ?";
                $params[] = '%' . $_GET['busca'] . '%';
            }
            
            $where = count($filtros) > 0 ? 'WHERE ' . implode(' AND ', $filtros) : '';
            
            // Buscar logs
            $sql = "SELECT * FROM tb_logs_sistema 
                    {$where}
                    ORDER BY criado_em DESC 
                    LIMIT {$porPagina} OFFSET {$offset}";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Renomear campo created_at para compatibilidade com frontend
            foreach ($logs as &$log) {
                $log['created_at'] = $log['criado_em'];
                $log['usuario'] = $log['id_usuario'] ? 'User ' . $log['id_usuario'] : null;
                $log['ip'] = $log['ip_address'];
                $log['categoria'] = $log['canal'];
                $log['nivel'] = strtolower($log['nivel']);
            }
            
            // Contar total
            $sqlCount = "SELECT COUNT(*) FROM tb_logs_sistema {$where}";
            $stmtCount = $db->prepare($sqlCount);
            $stmtCount->execute($params);
            $total = $stmtCount->fetchColumn();
            
            // Estatísticas (com mesmo filtro RBAC)
            $statsWhere = '';
            $statsParams = [];
            if (!in_array($nivel, ['super_admin', 'admin'])) {
                $statsWhere = 'WHERE id_usuario = ?';
                $statsParams[] = $idUsuario;
            }
            $stmtStats = $db->prepare("SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN nivel = 'DEBUG' THEN 1 ELSE 0 END) as debug,
                SUM(CASE WHEN nivel = 'INFO' THEN 1 ELSE 0 END) as info,
                SUM(CASE WHEN nivel = 'WARNING' THEN 1 ELSE 0 END) as warning,
                SUM(CASE WHEN nivel = 'ERROR' THEN 1 ELSE 0 END) as error,
                SUM(CASE WHEN nivel = 'CRITICAL' THEN 1 ELSE 0 END) as critical
                FROM tb_logs_sistema {$statsWhere}");
            $stmtStats->execute($statsParams);
            $stats = $stmtStats->fetch(PDO::FETCH_ASSOC);
            
            header('Content-Type: application/json');
            echo json_encode([
                'sucesso' => true,
                'logs' => $logs,
                'total' => $total,
                'pagina' => $pagina,
                'por_pagina' => $porPagina,
                'estatisticas' => $stats
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'sucesso' => false,
                'erro' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Limpar logs antigos
     */
    public function limpar()
    {
        try {
            $db = Database::getConexao();
            
            $dias = isset($_POST['dias']) ? max(1, (int)$_POST['dias']) : 30;
            
            $stmt = $db->prepare("DELETE FROM tb_logs_sistema WHERE criado_em < NOW() - INTERVAL '{$dias} days'");
            $stmt->execute();
            
            $deleted = $stmt->rowCount();
            
            header('Content-Type: application/json');
            echo json_encode([
                'sucesso' => true,
                'mensagem' => "Logs removidos: {$deleted}"
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'sucesso' => false,
                'erro' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Exportar logs (com RBAC)
     */
    public function exportar()
    {
        try {
            $db = Database::getConexao();
            
            $filtros = [];
            $params = [];
            
            // RBAC: não-admins só veem seus próprios logs
            $usuario = AuthMiddleware::obterUsuario();
            $idUsuario = AuthMiddleware::obterUsuarioId();
            $nivel = $usuario['nivel_acesso'] ?? 'operador';
            if (!in_array($nivel, ['super_admin', 'admin'])) {
                $filtros[] = "id_usuario = ?";
                $params[] = $idUsuario;
            }
            
            if (!empty($_GET['nivel'])) {
                $filtros[] = "nivel = ?";
                $params[] = $_GET['nivel'];
            }
            
            if (!empty($_GET['canal'])) {
                $filtros[] = "canal = ?";
                $params[] = $_GET['canal'];
            }
            
            $where = count($filtros) > 0 ? 'WHERE ' . implode(' AND ', $filtros) : '';
            
            $sql = "SELECT * FROM tb_logs_sistema {$where} ORDER BY criado_em DESC LIMIT 10000";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="logs_' . date('Y-m-d') . '.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['ID', 'Data/Hora', 'Nível', 'Categoria', 'Mensagem', 'Usuário', 'IP']);
            
            foreach ($logs as $log) {
                fputcsv($output, [
                    $log['id'],
                    $log['criado_em'],
                    $log['nivel'],
                    $log['canal'],
                    $log['mensagem'],
                    $log['id_usuario'] ? 'User ' . $log['id_usuario'] : '',
                    $log['ip_address'] ?? ''
                ]);
            }
            
            fclose($output);
            
        } catch (Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'sucesso' => false,
                'erro' => $e->getMessage()
            ]);
        }
    }
}
