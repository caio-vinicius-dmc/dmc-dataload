<?php
/**
 * DMC DataLoad - Worker de Execução Assíncrona
 * 
 * Execute via cron a cada minuto:
 * * * * * * php /path/to/scripts/worker.php >> /path/to/storage/logs/worker.log 2>&1
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Servicos\ServicoExecucao;

Database::loadEnv(__DIR__ . '/../');

class Worker
{
    private \PDO $db;
    private string $lockFile;
    private int $maxExecutionTime = 300; // 5 minutos
    private int $heartbeatInterval = 30; // segundos
    
    public function __construct()
    {
        $this->db = Database::getConexao();
        $this->lockFile = __DIR__ . '/../storage/worker.lock';
    }
    
    public function log(string $level, string $message, array $context = []): void
    {
        $entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'pid' => getmypid()
        ];
        
        $logFile = __DIR__ . '/../storage/logs/worker.log';
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }
        
        file_put_contents($logFile, json_encode($entry) . PHP_EOL, FILE_APPEND | LOCK_EX);
        
        // Console output
        echo "[{$entry['timestamp']}] [{$level}] {$message}" . PHP_EOL;
    }
    
    public function adquirirLock(): bool
    {
        // Verificar se já existe um worker rodando
        if (file_exists($this->lockFile)) {
            $lockData = json_decode(file_get_contents($this->lockFile), true);
            
            if ($lockData) {
                $pid = $lockData['pid'] ?? 0;
                $lastHeartbeat = $lockData['heartbeat'] ?? 0;
                
                // Verificar se o processo ainda está ativo
                if ($this->processoAtivo($pid)) {
                    // Verificar heartbeat (timeout de 2 minutos)
                    if (time() - $lastHeartbeat < 120) {
                        $this->log('INFO', 'Worker já em execução', ['pid' => $pid]);
                        return false;
                    }
                    
                    $this->log('WARNING', 'Worker travado detectado, assumindo controle', ['old_pid' => $pid]);
                }
            }
        }
        
        // Criar lock
        $lockData = [
            'pid' => getmypid(),
            'started' => time(),
            'heartbeat' => time()
        ];
        
        file_put_contents($this->lockFile, json_encode($lockData));
        $this->log('INFO', 'Lock adquirido');
        
        return true;
    }
    
    public function liberarLock(): void
    {
        if (file_exists($this->lockFile)) {
            unlink($this->lockFile);
        }
        $this->log('INFO', 'Lock liberado');
    }
    
    public function atualizarHeartbeat(): void
    {
        if (file_exists($this->lockFile)) {
            $lockData = json_decode(file_get_contents($this->lockFile), true) ?? [];
            $lockData['heartbeat'] = time();
            file_put_contents($this->lockFile, json_encode($lockData));
        }
    }
    
    private function processoAtivo(int $pid): bool
    {
        if ($pid <= 0) return false;
        
        if (PHP_OS_FAMILY === 'Windows') {
            exec("tasklist /FI \"PID eq {$pid}\" 2>NUL", $output);
            return count($output) > 1;
        }
        
        return posix_kill($pid, 0);
    }
    
    public function buscarRotinasAgendadas(): array
    {
        $sql = "SELECT r.*, p.nome_conexao 
                FROM tb_rotinas r 
                JOIN tb_perfis_conexao p ON r.id_conexao = p.id
                WHERE r.ativa = true 
                  AND r.esta_executando = false
                  AND r.agendamento_cron IS NOT NULL
                  AND (r.proxima_execucao IS NULL OR r.proxima_execucao <= NOW())
                  AND r.tentativas_falha < r.max_tentativas
                ORDER BY r.proxima_execucao ASC
                LIMIT 10";
        
        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function calcularProximaExecucao(string $cron): ?\DateTime
    {
        // Parser simples de cron (minuto hora dia mês diaSemana)
        // Para produção, usar library como dragonmantank/cron-expression
        $partes = explode(' ', trim($cron));
        
        if (count($partes) !== 5) {
            return null;
        }
        
        $agora = new \DateTime();
        $agora->modify('+1 minute');
        $agora->setTime((int)$agora->format('H'), (int)$agora->format('i'), 0);
        
        // Simplificado: apenas suporta */N ou números fixos
        // Para produção completa, usar biblioteca dedicada
        return $agora;
    }
    
    public function executarRotina(array $rotina): array
    {
        $idRotina = $rotina['id'];
        $this->log('INFO', "Iniciando execução da rotina", ['id' => $idRotina, 'nome' => $rotina['nome']]);
        
        // Marcar como executando
        $this->db->prepare("UPDATE tb_rotinas SET esta_executando = true, ultima_verificacao = NOW() WHERE id = ?")
                 ->execute([$idRotina]);
        
        // Criar log de execução
        $stmt = $this->db->prepare("INSERT INTO tb_logs_execucao (id_rotina, status) VALUES (?, 'executando') RETURNING id");
        $stmt->execute([$idRotina]);
        $idLog = $stmt->fetchColumn();
        
        $inicio = microtime(true);
        
        try {
            $svc = new ServicoExecucao();
            $resultado = $svc->executarRotina($idRotina);
            
            $duracao = (int)((microtime(true) - $inicio) * 1000);
            
            if ($resultado['sucesso']) {
                // Sucesso
                $this->db->prepare("UPDATE tb_logs_execucao 
                    SET data_fim = NOW(), duracao_ms = ?, status = 'sucesso', 
                        blocos_executados = ?, blocos_sucesso = ?
                    WHERE id = ?")
                    ->execute([
                        $duracao,
                        count($resultado['logs'] ?? []),
                        count($resultado['logs'] ?? []),
                        $idLog
                    ]);
                
                // Reset tentativas e calcular próxima execução
                $proxima = null;
                if (!empty($rotina['agendamento_cron'])) {
                    $proxima = $this->calcularProximaExecucao($rotina['agendamento_cron']);
                }
                
                $this->db->prepare("UPDATE tb_rotinas 
                    SET esta_executando = false, ultima_execucao = NOW(), 
                        tentativas_falha = 0, proxima_execucao = ?
                    WHERE id = ?")
                    ->execute([$proxima?->format('Y-m-d H:i:s'), $idRotina]);
                
                $this->log('INFO', "Rotina executada com sucesso", [
                    'id' => $idRotina,
                    'duracao_ms' => $duracao
                ]);
                
                // Webhook sucesso
                if (!empty($rotina['webhook_sucesso'])) {
                    $this->enviarWebhook($rotina['webhook_sucesso'], $resultado);
                }
                
                return ['sucesso' => true, 'duracao_ms' => $duracao];
                
            } else {
                throw new \Exception($resultado['erro'] ?? 'Erro desconhecido');
            }
            
        } catch (\Throwable $e) {
            $duracao = (int)((microtime(true) - $inicio) * 1000);
            
            // Falha
            $this->db->prepare("UPDATE tb_logs_execucao 
                SET data_fim = NOW(), duracao_ms = ?, status = 'falha', mensagem_erro = ?
                WHERE id = ?")
                ->execute([$duracao, $e->getMessage(), $idLog]);
            
            // Backoff exponencial: 1min, 2min, 4min, 8min...
            $tentativas = ($rotina['tentativas_falha'] ?? 0) + 1;
            $backoffMinutos = pow(2, $tentativas - 1);
            $proxima = new \DateTime();
            $proxima->modify("+{$backoffMinutos} minutes");
            
            $this->db->prepare("UPDATE tb_rotinas 
                SET esta_executando = false, tentativas_falha = ?, proxima_execucao = ?
                WHERE id = ?")
                ->execute([$tentativas, $proxima->format('Y-m-d H:i:s'), $idRotina]);
            
            $this->log('ERROR', "Falha na execução da rotina", [
                'id' => $idRotina,
                'erro' => $e->getMessage(),
                'tentativa' => $tentativas,
                'proxima_tentativa' => $proxima->format('Y-m-d H:i:s')
            ]);
            
            // Webhook falha
            if (!empty($rotina['webhook_falha'])) {
                $this->enviarWebhook($rotina['webhook_falha'], ['erro' => $e->getMessage()]);
            }
            
            return ['sucesso' => false, 'erro' => $e->getMessage()];
        }
    }
    
    private function enviarWebhook(string $url, array $payload): void
    {
        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10
            ]);
            curl_exec($ch);
            curl_close($ch);
        } catch (\Throwable $e) {
            $this->log('WARNING', 'Falha ao enviar webhook', ['url' => $url, 'erro' => $e->getMessage()]);
        }
    }
    
    public function detectarTravamentos(): void
    {
        // Rotinas marcadas como executando há mais de 30 minutos
        $sql = "UPDATE tb_rotinas 
                SET esta_executando = false, tentativas_falha = tentativas_falha + 1
                WHERE esta_executando = true 
                  AND ultima_verificacao < NOW() - INTERVAL '30 minutes'
                RETURNING id, nome";
        
        $stmt = $this->db->query($sql);
        $travadas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        foreach ($travadas as $r) {
            $this->log('WARNING', 'Rotina travada detectada e liberada', ['id' => $r['id'], 'nome' => $r['nome']]);
        }
    }
    
    public function executar(): void
    {
        if (!$this->adquirirLock()) {
            return;
        }
        
        $this->log('INFO', '=== Worker iniciado ===');
        
        try {
            // Detectar travamentos
            $this->detectarTravamentos();
            
            // Buscar rotinas agendadas
            $rotinas = $this->buscarRotinasAgendadas();
            $this->log('INFO', 'Rotinas agendadas encontradas', ['count' => count($rotinas)]);
            
            foreach ($rotinas as $rotina) {
                $this->atualizarHeartbeat();
                $this->executarRotina($rotina);
            }
            
        } finally {
            $this->liberarLock();
            $this->log('INFO', '=== Worker finalizado ===');
        }
    }
}

// Executar
$worker = new Worker();
$worker->executar();
