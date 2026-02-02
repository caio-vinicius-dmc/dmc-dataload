<?php
/**
 * DMC DataLoad - Scheduler Controller
 * Gerencia o agendamento e execução de rotinas
 */

namespace App\Controllers;

class SchedulerController
{
    private $db;
    
    public function __construct()
    {
        $this->db = \App\Core\Database::getConexao();
    }
    
    /**
     * Retorna rotinas com agendamento
     */
    public function getRotinasAgendadas(): void
    {
        header('Content-Type: application/json');
        
        try {
            $sql = "SELECT r.id, r.nome, r.agendamento_cron, r.ativa, 
                           r.ultima_execucao, r.proxima_execucao,
                           c.nome_conexao as conexao
                    FROM tb_rotinas r
                    LEFT JOIN tb_perfis_conexao c ON r.id_conexao = c.id
                    WHERE r.agendamento_cron IS NOT NULL 
                      AND r.agendamento_cron != ''
                    ORDER BY r.nome";
            
            $stmt = $this->db->query($sql);
            $rotinas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Calcular próxima execução para cada rotina
            foreach ($rotinas as &$rotina) {
                if ($rotina['agendamento_cron']) {
                    $rotina['proxima_execucao'] = $this->calcularProximaExecucao($rotina['agendamento_cron']);
                }
            }
            
            echo json_encode(['sucesso' => true, 'dados' => $rotinas]);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
    
    /**
     * Status do worker/scheduler
     */
    public function getStatus(): void
    {
        header('Content-Type: application/json');
        
        try {
            // Verificar se o worker está rodando
            $pidFile = __DIR__ . '/../../storage/scheduler.pid';
            $running = false;
            
            if (file_exists($pidFile)) {
                $pid = trim(file_get_contents($pidFile));
                // Verificar se o processo existe
                if (PHP_OS_FAMILY === 'Windows') {
                    exec("tasklist /FI \"PID eq $pid\" 2>NUL", $output);
                    $running = count($output) > 1;
                } else {
                    $running = file_exists("/proc/$pid");
                }
            }
            
            // Contar execuções em andamento
            $sql = "SELECT COUNT(*) FROM tb_logs_execucao WHERE status = 'executando'";
            $runningCount = $this->db->query($sql)->fetchColumn();
            
            // Última verificação do scheduler
            $logFile = __DIR__ . '/../../storage/logs/scheduler.log';
            $lastRun = null;
            if (file_exists($logFile)) {
                $lastRun = date('Y-m-d H:i:s', filemtime($logFile));
            }
            
            // Próxima execução agendada
            $sql = "SELECT MIN(proxima_execucao) FROM tb_rotinas WHERE ativa = true AND agendamento_cron IS NOT NULL";
            $nextRun = $this->db->query($sql)->fetchColumn();
            
            echo json_encode([
                'sucesso' => true,
                'running' => $running,
                'running_count' => (int)$runningCount,
                'last_run' => $lastRun,
                'next_run' => $nextRun
            ]);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
    
    /**
     * Iniciar o worker
     */
    public function start(): void
    {
        header('Content-Type: application/json');
        
        try {
            $storageDir = __DIR__ . '/../../storage';
            if (!is_dir($storageDir)) {
                mkdir($storageDir, 0755, true);
            }
            
            $pidFile = $storageDir . '/scheduler.pid';
            
            // Verificar se já está rodando
            if (file_exists($pidFile)) {
                $pid = trim(file_get_contents($pidFile));
                if (PHP_OS_FAMILY === 'Windows') {
                    exec("tasklist /FI \"PID eq $pid\" 2>NUL", $output);
                    if (count($output) > 1) {
                        echo json_encode(['sucesso' => false, 'erro' => 'Worker já está em execução']);
                        return;
                    }
                }
            }
            
            // Iniciar o worker em background
            $workerScript = __DIR__ . '/../../bin/scheduler-worker.php';
            
            if (PHP_OS_FAMILY === 'Windows') {
                $cmd = "start /B php \"$workerScript\" > NUL 2>&1";
                pclose(popen($cmd, 'r'));
            } else {
                $cmd = "php $workerScript > /dev/null 2>&1 &";
                exec($cmd);
            }
            
            // Aguardar um pouco e verificar
            usleep(500000); // 0.5 segundos
            
            // Registrar log
            $this->registrarLog('Scheduler iniciado', 'info');
            
            echo json_encode(['sucesso' => true, 'mensagem' => 'Worker iniciado']);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
    
    /**
     * Parar o worker
     */
    public function stop(): void
    {
        header('Content-Type: application/json');
        
        try {
            $pidFile = __DIR__ . '/../../storage/scheduler.pid';
            
            if (file_exists($pidFile)) {
                $pid = trim(file_get_contents($pidFile));
                
                if (PHP_OS_FAMILY === 'Windows') {
                    exec("taskkill /PID $pid /F 2>NUL");
                } else {
                    exec("kill -9 $pid 2>/dev/null");
                }
                
                unlink($pidFile);
            }
            
            $this->registrarLog('Scheduler parado', 'warning');
            
            echo json_encode(['sucesso' => true, 'mensagem' => 'Worker parado']);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
    
    /**
     * Ativar/desativar rotina
     */
    public function toggle(): void
    {
        header('Content-Type: application/json');
        
        try {
            $id = $_POST['id'] ?? null;
            $ativa = $_POST['ativa'] ?? 0;
            
            if (!$id) {
                throw new \Exception('ID da rotina não informado');
            }
            
            $sql = "UPDATE tb_rotinas SET ativa = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$ativa ? 't' : 'f', $id]);
            
            $this->registrarLog("Rotina $id " . ($ativa ? 'ativada' : 'desativada'), 'info');
            
            echo json_encode(['sucesso' => true]);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
    
    /**
     * Atualizar agendamento CRON
     */
    public function atualizar(): void
    {
        header('Content-Type: application/json');
        
        try {
            $id = $_POST['id'] ?? null;
            $cron = $_POST['cron'] ?? '';
            
            if (!$id) {
                throw new \Exception('ID da rotina não informado');
            }
            
            // Validar expressão CRON
            if ($cron && !$this->validarCron($cron)) {
                throw new \Exception('Expressão CRON inválida');
            }
            
            $proximaExecucao = $cron ? $this->calcularProximaExecucao($cron) : null;
            
            $sql = "UPDATE tb_rotinas SET 
                    agendamento_cron = ?, 
                    proxima_execucao = ?
                    WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $cron ?: null,
                $proximaExecucao,
                $id
            ]);
            
            echo json_encode(['sucesso' => true]);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
    
    /**
     * Obter logs do scheduler
     */
    public function getLogs(): void
    {
        header('Content-Type: application/json');
        
        try {
            $sql = "SELECT * FROM tb_logs_sistema 
                    WHERE canal = 'scheduler' 
                    ORDER BY criado_em DESC 
                    LIMIT 50";
            $logs = $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            
            echo json_encode(['sucesso' => true, 'logs' => $logs]);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
    
    /**
     * Calcular próxima execução baseado no CRON
     */
    private function calcularProximaExecucao(string $cron): ?string
    {
        try {
            $parts = explode(' ', trim($cron));
            if (count($parts) !== 5) {
                return null;
            }
            
            // Implementação simplificada
            $now = new \DateTime();
            $now->modify('+1 minute');
            $now->setTime($now->format('H'), $now->format('i'), 0);
            
            // Para uma implementação completa, usar uma biblioteca como dragonmantank/cron-expression
            return $now->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Validar expressão CRON
     */
    /**
     * Obter detalhes de um agendamento específico
     */
    public function detalhes(): void
    {
        header('Content-Type: application/json');
        
        try {
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                echo json_encode(['sucesso' => false, 'erro' => 'ID não informado']);
                return;
            }
            
            $sql = "SELECT id, nome, agendamento_cron, data_inicio, data_fim, 
                           datas_ignorar_json, ignorar_feriados, max_tentativas, 
                           timeout, notificar_falha, ativa
                    FROM tb_rotinas 
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $rotina = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$rotina) {
                echo json_encode(['sucesso' => false, 'erro' => 'Rotina não encontrada']);
                return;
            }
            
            echo json_encode($rotina);
            
        } catch (\Exception $e) {
            error_log("Erro ao buscar detalhes: " . $e->getMessage());
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
    
    /**
     * Excluir agendamento (remove apenas o agendamento, não a rotina)
     */
    public function excluir(): void
    {
        header('Content-Type: application/json');
        
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            
            if (empty($data['id_rotina'])) {
                echo json_encode(['sucesso' => false, 'erro' => 'Rotina não informada']);
                return;
            }
            
            // Remove o agendamento (mantém a rotina)
            $sql = "UPDATE tb_rotinas 
                    SET agendamento_cron = NULL, 
                        ativa = false,
                        data_inicio = NULL,
                        data_fim = NULL,
                        datas_ignorar_json = NULL,
                        ignorar_feriados = false
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$data['id_rotina']]);
            
            $this->registrarLog("Agendamento removido da rotina ID {$data['id_rotina']}", 'info');
            
            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Agendamento removido com sucesso'
            ]);
            
        } catch (\Exception $e) {
            error_log("Erro ao excluir agendamento: " . $e->getMessage());
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
    
    /**
     * Salvar/atualizar configuração de agendamento
     */
    public function salvar(): void
    {
        header('Content-Type: application/json');
        
        try {
            $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            
            // Validações
            if (empty($data['id_rotina'])) {
                echo json_encode(['sucesso' => false, 'erro' => 'Rotina não informada']);
                return;
            }
            
            if (empty($data['agendamento_cron'])) {
                echo json_encode(['sucesso' => false, 'erro' => 'Expressão CRON não informada']);
                return;
            }
            
            if (!$this->validarCron($data['agendamento_cron'])) {
                echo json_encode(['sucesso' => false, 'erro' => 'Expressão CRON inválida']);
                return;
            }
            
            // Verificar se a rotina existe
            $stmt = $this->db->prepare("SELECT id FROM tb_rotinas WHERE id = ?");
            $stmt->execute([$data['id_rotina']]);
            if (!$stmt->fetch()) {
                echo json_encode(['sucesso' => false, 'erro' => 'Rotina não encontrada']);
                return;
            }
            
            // Processar datas ignorar (converter de texto para JSON array)
            $datasIgnorar = null;
            if (!empty($data['datas_ignorar'])) {
                $linhas = explode("\n", $data['datas_ignorar']);
                $datas = [];
                foreach ($linhas as $linha) {
                    $linha = trim($linha);
                    if (!empty($linha)) {
                        // Validar formato DD/MM/YYYY
                        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $linha)) {
                            $datas[] = $linha;
                        }
                    }
                }
                if (!empty($datas)) {
                    $datasIgnorar = json_encode($datas);
                }
            }
            
            // Preparar SQL de atualização
            $sql = "UPDATE tb_rotinas SET 
                    agendamento_cron = :cron,
                    ativa = :ativa,
                    max_tentativas = :max_tentativas";
            
            $params = [
                ':cron' => $data['agendamento_cron'],
                ':ativa' => !empty($data['ativa']) ? 1 : 0,
                ':max_tentativas' => $data['max_tentativas'] ?? 3
            ];
            
            // Adicionar campos opcionais (verificar se as colunas existem)
            $colunas = $this->verificarColunasExistentes();
            
            if ($colunas['data_inicio']) {
                $sql .= ", data_inicio = :data_inicio";
                $params[':data_inicio'] = !empty($data['data_inicio']) ? $data['data_inicio'] : null;
            }
            
            if ($colunas['data_fim']) {
                $sql .= ", data_fim = :data_fim";
                $params[':data_fim'] = !empty($data['data_fim']) ? $data['data_fim'] : null;
            }
            
            if ($colunas['datas_ignorar_json']) {
                $sql .= ", datas_ignorar_json = :datas_ignorar";
                $params[':datas_ignorar'] = $datasIgnorar;
            }
            
            if ($colunas['ignorar_feriados']) {
                $sql .= ", ignorar_feriados = :ignorar_feriados";
                $params[':ignorar_feriados'] = !empty($data['ignorar_feriados']) ? 1 : 0;
            }
            
            if ($colunas['timeout']) {
                $sql .= ", timeout = :timeout";
                $params[':timeout'] = $data['timeout'] ?? 300;
            }
            
            if ($colunas['notificar_falha']) {
                $sql .= ", notificar_falha = :notificar_falha";
                $params[':notificar_falha'] = !empty($data['notificar_falha']) ? 1 : 0;
            }
            
            $sql .= " WHERE id = :id_rotina";
            $params[':id_rotina'] = $data['id_rotina'];
            
            // Executar atualização
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            // Log da operação
            $this->registrarLog("Agendamento configurado para rotina ID {$data['id_rotina']}: {$data['agendamento_cron']}", 'info');
            
            echo json_encode([
                'sucesso' => true,
                'mensagem' => 'Agendamento configurado com sucesso!',
                'proxima_execucao' => $this->calcularProximaExecucao($data['agendamento_cron'])
            ]);
            
        } catch (\Exception $e) {
            error_log("Erro ao salvar agendamento: " . $e->getMessage());
            echo json_encode([
                'sucesso' => false,
                'erro' => 'Erro ao salvar: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Verificar quais colunas existem na tb_rotinas
     */
    private function verificarColunasExistentes(): array
    {
        try {
            $sql = "SELECT column_name 
                    FROM information_schema.columns 
                    WHERE table_name = 'tb_rotinas' 
                      AND table_schema = 'public'";
            $stmt = $this->db->query($sql);
            $colunas = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            
            return [
                'data_inicio' => in_array('data_inicio', $colunas),
                'data_fim' => in_array('data_fim', $colunas),
                'datas_ignorar_json' => in_array('datas_ignorar_json', $colunas),
                'ignorar_feriados' => in_array('ignorar_feriados', $colunas),
                'timeout' => in_array('timeout', $colunas),
                'notificar_falha' => in_array('notificar_falha', $colunas)
            ];
        } catch (\Exception $e) {
            // Retornar false para todas se der erro
            return [
                'data_inicio' => false,
                'data_fim' => false,
                'datas_ignorar_json' => false,
                'ignorar_feriados' => false,
                'timeout' => false,
                'notificar_falha' => false
            ];
        }
    }

    private function validarCron(string $cron): bool
    {
        $parts = explode(' ', trim($cron));
        return count($parts) === 5;
    }
    
    /**
     * Registrar log
     */
    private function registrarLog(string $mensagem, string $nivel = 'info'): void
    {
        try {
            $sql = "INSERT INTO logs_sistema (nivel, categoria, mensagem, usuario_id, created_at)
                    VALUES (:nivel, 'scheduler', :mensagem, :usuario_id, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nivel' => $nivel,
                ':mensagem' => $mensagem,
                ':usuario_id' => $_SESSION['usuario_id'] ?? null
            ]);
        } catch (\Exception $e) {
            // Silenciar erro de log
        }
    }
}
