<?php
/**
 * DMC DataLoad - Scheduler Worker
 * Script que roda em background para executar rotinas agendadas
 * 
 * Uso: php scheduler-worker.php
 */

// Configurações
set_time_limit(0);
ini_set('memory_limit', '256M');

// Carregar autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Carregar configurações
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Conectar ao banco
try {
    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s',
        $_ENV['DB_HOST'] ?? 'localhost',
        $_ENV['DB_PORT'] ?? '5433',
        $_ENV['DB_NAME'] ?? 'db_dmc_dataload'
    );
    
    $db = new PDO($dsn, $_ENV['DB_USER'] ?? 'postgres', $_ENV['DB_PASS'] ?? 'dmc2023@', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    logMessage("Erro ao conectar ao banco: " . $e->getMessage(), 'error');
    exit(1);
}

// Diretório de storage
$storageDir = __DIR__ . '/../storage';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
}

// Criar arquivo PID
$pidFile = $storageDir . '/scheduler.pid';
file_put_contents($pidFile, getmypid());

// Arquivo de controle para parar o worker
$stopFile = $storageDir . '/scheduler.stop';
if (file_exists($stopFile)) {
    unlink($stopFile);
}

logMessage("Scheduler Worker iniciado (PID: " . getmypid() . ")", 'info');

// Loop principal
$intervalo = 60; // segundos entre verificações
$ultimaVerificacao = 0;

while (true) {
    // Verificar sinal de parada
    if (file_exists($stopFile)) {
        logMessage("Sinal de parada recebido. Encerrando...", 'warning');
        unlink($stopFile);
        break;
    }
    
    // Verificar se é hora de executar
    $agora = time();
    if (($agora - $ultimaVerificacao) >= $intervalo) {
        $ultimaVerificacao = $agora;
        
        logMessage("Verificando rotinas agendadas...", 'debug');
        
        try {
            // Buscar rotinas que precisam executar
            $sql = "SELECT r.*, 
                           c_origem.tipo as origem_tipo, c_origem.host as origem_host, 
                           c_origem.porta as origem_porta, c_origem.banco as origem_banco,
                           c_origem.usuario as origem_usuario, c_origem.senha as origem_senha,
                           c_destino.tipo as destino_tipo, c_destino.host as destino_host, 
                           c_destino.porta as destino_porta, c_destino.banco as destino_banco,
                           c_destino.usuario as destino_usuario, c_destino.senha as destino_senha
                    FROM rotinas r
                    LEFT JOIN conexoes c_origem ON r.conexao_origem_id = c_origem.id
                    LEFT JOIN conexoes c_destino ON r.conexao_destino_id = c_destino.id
                    WHERE r.ativa = true 
                      AND r.agendamento_cron IS NOT NULL
                      AND (r.proxima_execucao IS NULL OR r.proxima_execucao <= NOW())";
            
            $stmt = $db->query($sql);
            $rotinas = $stmt->fetchAll();
            
            if (count($rotinas) > 0) {
                logMessage(count($rotinas) . " rotina(s) para executar", 'info');
                
                foreach ($rotinas as $rotina) {
                    executarRotina($db, $rotina);
                }
            }
            
        } catch (Exception $e) {
            logMessage("Erro na verificação: " . $e->getMessage(), 'error');
        }
    }
    
    // Aguardar antes da próxima verificação
    sleep(5);
}

// Limpar PID ao sair
if (file_exists($pidFile)) {
    unlink($pidFile);
}

logMessage("Scheduler Worker encerrado", 'info');

/**
 * Executar uma rotina
 */
function executarRotina(PDO $db, array $rotina): void
{
    global $storageDir;
    
    $rotinaId = $rotina['id'];
    $rotinaNome = $rotina['nome'];
    
    logMessage("Iniciando execução: $rotinaNome (ID: $rotinaId)", 'info');
    
    // Registrar início no histórico
    $sqlHistorico = "INSERT INTO historico_execucoes (rotina_id, inicio, status)
                     VALUES (:rotina_id, NOW(), 'executando')
                     RETURNING id";
    $stmtHist = $db->prepare($sqlHistorico);
    $stmtHist->execute([':rotina_id' => $rotinaId]);
    $historicoId = $stmtHist->fetchColumn();
    
    $inicio = microtime(true);
    $linhasAfetadas = 0;
    $erro = null;
    
    try {
        // Conectar à origem
        $origemDsn = buildDsn($rotina['origem_tipo'], $rotina['origem_host'], 
                              $rotina['origem_porta'], $rotina['origem_banco']);
        $origemPdo = new PDO($origemDsn, $rotina['origem_usuario'], $rotina['origem_senha'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        // Conectar ao destino
        $destinoDsn = buildDsn($rotina['destino_tipo'], $rotina['destino_host'], 
                               $rotina['destino_porta'], $rotina['destino_banco']);
        $destinoPdo = new PDO($destinoDsn, $rotina['destino_usuario'], $rotina['destino_senha'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        // Executar query de origem
        $queryOrigem = $rotina['query_origem'];
        $stmtOrigem = $origemPdo->query($queryOrigem);
        $dados = $stmtOrigem->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($dados) > 0) {
            // Iniciar transação no destino
            $destinoPdo->beginTransaction();
            
            try {
                // Limpar tabela destino se configurado
                if ($rotina['limpar_destino']) {
                    $destinoPdo->exec("TRUNCATE TABLE {$rotina['tabela_destino']}");
                }
                
                // Inserir dados
                $colunas = array_keys($dados[0]);
                $placeholders = ':' . implode(', :', $colunas);
                $colunasStr = implode(', ', $colunas);
                
                $sqlInsert = "INSERT INTO {$rotina['tabela_destino']} ($colunasStr) VALUES ($placeholders)";
                $stmtInsert = $destinoPdo->prepare($sqlInsert);
                
                foreach ($dados as $linha) {
                    $params = [];
                    foreach ($linha as $col => $val) {
                        $params[":$col"] = $val;
                    }
                    $stmtInsert->execute($params);
                    $linhasAfetadas++;
                }
                
                $destinoPdo->commit();
                logMessage("$rotinaNome: $linhasAfetadas linhas transferidas", 'success');
                
            } catch (Exception $e) {
                $destinoPdo->rollBack();
                throw $e;
            }
        } else {
            logMessage("$rotinaNome: Nenhum dado encontrado na origem", 'warning');
        }
        
        $status = 'sucesso';
        
    } catch (Exception $e) {
        $erro = $e->getMessage();
        $status = 'falha';
        logMessage("ERRO em $rotinaNome: $erro", 'error');
    }
    
    $fim = microtime(true);
    $duracao = round($fim - $inicio, 2);
    
    // Atualizar histórico
    $sqlUpdate = "UPDATE historico_execucoes 
                  SET fim = NOW(), 
                      status = :status, 
                      linhas_afetadas = :linhas,
                      mensagem = :mensagem,
                      duracao_segundos = :duracao
                  WHERE id = :id";
    $stmtUp = $db->prepare($sqlUpdate);
    $stmtUp->execute([
        ':status' => $status,
        ':linhas' => $linhasAfetadas,
        ':mensagem' => $erro,
        ':duracao' => $duracao,
        ':id' => $historicoId
    ]);
    
    // Atualizar próxima execução da rotina
    $proximaExecucao = calcularProximaExecucao($rotina['agendamento_cron']);
    $sqlRotina = "UPDATE rotinas 
                  SET ultima_execucao = NOW(), 
                      proxima_execucao = :proxima
                  WHERE id = :id";
    $stmtRot = $db->prepare($sqlRotina);
    $stmtRot->execute([':proxima' => $proximaExecucao, ':id' => $rotinaId]);
    
    logMessage("$rotinaNome concluído em {$duracao}s - Status: $status", 'info');
}

/**
 * Construir DSN para conexão
 */
function buildDsn(string $tipo, string $host, string $porta, string $banco): string
{
    switch (strtolower($tipo)) {
        case 'postgresql':
        case 'pgsql':
            return "pgsql:host=$host;port=$porta;dbname=$banco";
        case 'mysql':
        case 'mariadb':
            return "mysql:host=$host;port=$porta;dbname=$banco;charset=utf8mb4";
        case 'sqlserver':
        case 'mssql':
            return "sqlsrv:Server=$host,$porta;Database=$banco";
        case 'oracle':
            return "oci:dbname=//$host:$porta/$banco";
        default:
            throw new Exception("Tipo de banco não suportado: $tipo");
    }
}

/**
 * Calcular próxima execução baseado no CRON
 */
function calcularProximaExecucao(string $cron): string
{
    // Implementação simplificada - para produção, usar biblioteca cron-expression
    $now = new DateTime();
    $now->modify('+1 minute');
    
    // Parse básico do cron
    $parts = explode(' ', trim($cron));
    if (count($parts) === 5) {
        $minuto = $parts[0];
        
        // Se é um intervalo específico
        if (strpos($minuto, '*/') === 0) {
            $intervalo = (int)substr($minuto, 2);
            $currentMinute = (int)$now->format('i');
            $nextMinute = ceil($currentMinute / $intervalo) * $intervalo;
            if ($nextMinute >= 60) {
                $now->modify('+1 hour');
                $nextMinute = 0;
            }
            $now->setTime($now->format('H'), $nextMinute, 0);
        }
    }
    
    return $now->format('Y-m-d H:i:s');
}

/**
 * Registrar log
 */
function logMessage(string $message, string $level = 'info'): void
{
    global $storageDir, $db;
    
    $timestamp = date('Y-m-d H:i:s');
    $logLine = "[$timestamp] [$level] $message\n";
    
    // Arquivo de log
    $logDir = $storageDir . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logDir . '/scheduler.log', $logLine, FILE_APPEND);
    
    // Console
    echo $logLine;
    
    // Banco de dados
    try {
        $sql = "INSERT INTO logs_sistema (nivel, categoria, mensagem, created_at)
                VALUES (:nivel, 'scheduler', :mensagem, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->execute([':nivel' => $level, ':mensagem' => $message]);
    } catch (Exception $e) {
        // Silenciar erro de log
    }
}
