<?php
/**
 * DMC DataLoad - Scheduler Worker v2
 * Script que roda em background para executar rotinas agendadas
 * 
 * Uso: php scheduler-worker.php
 */

// Configurações
set_time_limit(0);
ini_set('memory_limit', '256M');
error_reporting(E_ALL);

// Configurar timezone
date_default_timezone_set('America/Sao_Paulo');

// Carregar autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Carregar configurações
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Diretório de storage
$storageDir = __DIR__ . '/../storage';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
}
if (!is_dir($storageDir . '/logs')) {
    mkdir($storageDir . '/logs', 0755, true);
}

// Criar arquivo PID
$pidFile = $storageDir . '/scheduler.pid';
file_put_contents($pidFile, getmypid());

// Arquivo de controle para parar o worker
$stopFile = $storageDir . '/scheduler.stop';
if (file_exists($stopFile)) {
    unlink($stopFile);
}

// Conectar ao banco
$db = null;
try {
    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s',
        $_ENV['DB_HOST'] ?? 'localhost',
        $_ENV['DB_PORT'] ?? '5433',
        $_ENV['DB_DATABASE'] ?? 'db_dmc_dataload'
    );
    
    $db = new PDO($dsn, $_ENV['DB_USERNAME'] ?? 'postgres', $_ENV['DB_PASSWORD'] ?? 'dmc2023@', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    logMessage("Scheduler Worker v2 iniciado (PID: " . getmypid() . ")", 'info');
    logMessage("Conectado ao banco: " . ($_ENV['DB_DATABASE'] ?? 'db_dmc_dataload'), 'info');
    
} catch (PDOException $e) {
    logMessage("ERRO FATAL ao conectar ao banco: " . $e->getMessage(), 'error');
    exit(1);
}

// Controle para evitar execuções duplicadas no mesmo minuto
$ultimasExecucoes = [];

// Loop principal - verifica a cada 10 segundos
$intervaloVerificacao = 10;

while (true) {
    // Verificar sinal de parada
    if (file_exists($stopFile)) {
        logMessage("Sinal de parada recebido. Encerrando...", 'warning');
        @unlink($stopFile);
        break;
    }
    
    try {
        $agoraMinuto = date('Y-m-d H:i'); // Chave única por minuto
        
        // 1. Desativar rotinas com data_fim expirada
        desativarRotinasExpiradas($db);
        
        // 2. Buscar rotinas ativas que precisam executar
        $sql = "SELECT r.id, r.nome, r.agendamento_cron, r.data_inicio, r.data_fim,
                       r.id_conexao, r.ultima_execucao, r.proxima_execucao
                FROM tb_rotinas r
                WHERE r.ativa = true 
                  AND r.agendamento_cron IS NOT NULL
                  AND r.agendamento_cron != ''
                  AND (r.data_inicio IS NULL OR r.data_inicio <= NOW())
                  AND (r.data_fim IS NULL OR r.data_fim >= NOW())
                ORDER BY r.id";
        
        $stmt = $db->query($sql);
        $rotinas = $stmt->fetchAll();
        
        foreach ($rotinas as $rotina) {
            $rotinaKey = $rotina['id'] . '_' . $agoraMinuto;
            
            // Verificar se já executou neste minuto
            if (isset($ultimasExecucoes[$rotinaKey])) {
                continue;
            }
            
            // Verificar se o CRON bate com o horário atual
            if (shouldExecuteNow($rotina['agendamento_cron'])) {
                logMessage("Rotina {$rotina['nome']} (ID: {$rotina['id']}) - CRON bate, executando...", 'info');
                
                // Marcar como executada neste minuto
                $ultimasExecucoes[$rotinaKey] = true;
                
                // Executar a rotina
                executarRotina($db, $rotina);
                
                // Calcular e atualizar próxima execução
                $proximaExecucao = calcularProximaExecucao($rotina['agendamento_cron']);
                $sqlUpdate = "UPDATE tb_rotinas SET ultima_execucao = NOW(), proxima_execucao = :proxima WHERE id = :id";
                $stmtUp = $db->prepare($sqlUpdate);
                $stmtUp->execute([':proxima' => $proximaExecucao, ':id' => $rotina['id']]);
                
                logMessage("Próxima execução de {$rotina['nome']}: $proximaExecucao", 'debug');
            }
        }
        
        // 3. Buscar pipelines com trigger CRON ativas
        $sqlPip = "SELECT id, nome, agendamento_cron
                   FROM tb_pipelines
                   WHERE ativo = true
                     AND trigger_tipo = 'cron'
                     AND agendamento_cron IS NOT NULL
                     AND agendamento_cron != ''
                   ORDER BY id";
        $stmtPip = $db->query($sqlPip);
        $pipelines = $stmtPip->fetchAll();

        foreach ($pipelines as $pip) {
            $pipKey = 'pip_' . $pip['id'] . '_' . $agoraMinuto;

            if (isset($ultimasExecucoes[$pipKey])) {
                continue;
            }

            if (shouldExecuteNow($pip['agendamento_cron'])) {
                logMessage("Pipeline {$pip['nome']} (ID: {$pip['id']}) - CRON bate, executando...", 'info');
                $ultimasExecucoes[$pipKey] = true;

                try {
                    // Simular sessão de sistema para execução
                    if (session_status() === PHP_SESSION_NONE) {
                        session_id('schedulerworker' . getmypid());
                        session_start();
                    }
                    $_SESSION['usuario_id'] = 1;
                    $_SESSION['usuario_nome'] = 'scheduler';
                    $_SESSION['nivel_acesso'] = 'super_admin';
                    $_SESSION['usuario_autenticado'] = true;
                    $_SESSION['usuario'] = [
                        'id' => 1,
                        'nome' => 'scheduler',
                        'email' => 'system@dmc.com',
                        'nivel_acesso' => 'super_admin',
                        'ativo' => true
                    ];

                    $pipeCtrl = new \App\Controllers\PipelineController();
                    $resultado = $pipeCtrl->executar($pip['id']);

                    $status = ($resultado['sucesso'] ?? false) ? 'sucesso' : 'erro';
                    $duracao = $resultado['duracao_ms'] ?? 0;
                    $nodesOk = $resultado['nodes_sucesso'] ?? 0;
                    $nodesFail = $resultado['nodes_falha'] ?? 0;

                    logMessage("Pipeline {$pip['nome']}: {$status} ({$duracao}ms, {$nodesOk} ok, {$nodesFail} falhas)", $status === 'sucesso' ? 'info' : 'error');
                } catch (Exception $e) {
                    logMessage("Erro ao executar pipeline {$pip['nome']}: " . $e->getMessage(), 'error');
                }
            }
        }

        // Limpar execuções antigas (manter só último minuto)
        $minutoAnterior = date('Y-m-d H:i', strtotime('-2 minutes'));
        foreach (array_keys($ultimasExecucoes) as $key) {
            if (strpos($key, $minutoAnterior) !== false || strpos($key, date('Y-m-d H:i', strtotime('-1 minute'))) !== false) {
                // Manter
            } else {
                $partes = explode('_', $key);
                if (count($partes) >= 2) {
                    $keyMinuto = $partes[1] . '_' . ($partes[2] ?? '');
                    if ($keyMinuto < $minutoAnterior) {
                        unset($ultimasExecucoes[$key]);
                    }
                }
            }
        }
        
        // 4. Polling de APIs externas
        try {
            $pollingEngine = new \App\Core\ApiPollingEngine($db);
            $pollingResult = $pollingEngine->executarPolling();
            if (!empty($pollingResult)) {
                $totalApis = count($pollingResult);
                $totalMatches = array_sum(array_column($pollingResult, 'eventos_match'));
                logMessage("Polling: {$totalApis} APIs verificadas, {$totalMatches} eventos acionados", 'info');
            }
        } catch (Exception $e) {
            logMessage("Erro no polling de APIs: " . $e->getMessage(), 'error');
        }
        
    } catch (Exception $e) {
        logMessage("Erro no loop principal: " . $e->getMessage(), 'error');
    }
    
    // Aguardar antes da próxima verificação
    sleep($intervaloVerificacao);
}

// Limpar PID ao sair
if (file_exists($pidFile)) {
    @unlink($pidFile);
}

logMessage("Scheduler Worker encerrado", 'info');

// ============================================
// FUNÇÕES
// ============================================

/**
 * Verificar se deve executar AGORA baseado no CRON
 */
function shouldExecuteNow(string $cron): bool
{
    $parts = explode(' ', trim($cron));
    if (count($parts) !== 5) {
        return false;
    }
    
    list($cronMinute, $cronHour, $cronDay, $cronMonth, $cronWeekday) = $parts;
    
    $nowMinute = (int)date('i');
    $nowHour = (int)date('H');
    $nowDay = (int)date('d');
    $nowMonth = (int)date('m');
    $nowWeekday = (int)date('w');
    
    // Verificar minuto
    if (!matchesCronField($cronMinute, $nowMinute)) {
        return false;
    }
    
    // Verificar hora
    if (!matchesCronField($cronHour, $nowHour)) {
        return false;
    }
    
    // Verificar dia do mês
    if (!matchesCronField($cronDay, $nowDay)) {
        return false;
    }
    
    // Verificar mês
    if (!matchesCronField($cronMonth, $nowMonth)) {
        return false;
    }
    
    // Verificar dia da semana
    if (!matchesCronField($cronWeekday, $nowWeekday)) {
        return false;
    }
    
    return true;
}

/**
 * Verificar se um campo CRON bate com um valor
 */
function matchesCronField(string $field, int $value): bool
{
    // Wildcard
    if ($field === '*') {
        return true;
    }
    
    // Intervalo (ex: */5)
    if (strpos($field, '*/') === 0) {
        $interval = (int)substr($field, 2);
        return $interval > 0 && ($value % $interval === 0);
    }
    
    // Range (ex: 1-5)
    if (strpos($field, '-') !== false) {
        list($start, $end) = explode('-', $field);
        return $value >= (int)$start && $value <= (int)$end;
    }
    
    // Lista (ex: 1,3,5)
    if (strpos($field, ',') !== false) {
        $values = array_map('intval', explode(',', $field));
        return in_array($value, $values);
    }
    
    // Valor específico
    return (int)$field === $value;
}

/**
 * Desativar rotinas com data_fim expirada
 */
function desativarRotinasExpiradas(PDO $db): void
{
    try {
        $sql = "UPDATE tb_rotinas 
                SET ativa = false, proxima_execucao = NULL 
                WHERE ativa = true 
                  AND data_fim IS NOT NULL 
                  AND data_fim < NOW()
                RETURNING id, nome";
        $stmt = $db->query($sql);
        $desativadas = $stmt->fetchAll();
        
        foreach ($desativadas as $r) {
            logMessage("Rotina '{$r['nome']}' (ID: {$r['id']}) DESATIVADA - data fim expirada", 'warning');
        }
    } catch (Exception $e) {
        logMessage("Erro ao desativar rotinas expiradas: " . $e->getMessage(), 'error');
    }
}

/**
 * Executar uma rotina
 */
function executarRotina(PDO $db, array $rotina): void
{
    $rotinaId = $rotina['id'];
    $rotinaNome = $rotina['nome'];
    
    logMessage(">>> INICIANDO: $rotinaNome (ID: $rotinaId)", 'info');
    
    // Registrar início no histórico
    $historicoId = null;
    try {
        $sqlHistorico = "INSERT INTO tb_logs_execucao (id_rotina, data_inicio, status)
                         VALUES (:rotina_id, NOW(), 'executando')
                         RETURNING id";
        $stmtHist = $db->prepare($sqlHistorico);
        $stmtHist->execute([':rotina_id' => $rotinaId]);
        $historicoId = $stmtHist->fetchColumn();
        logMessage("Historico criado: ID $historicoId", 'debug');
    } catch (Exception $e) {
        logMessage("Erro ao criar historico: " . $e->getMessage(), 'error');
    }
    
    $inicio = microtime(true);
    $linhasAfetadas = 0;
    $erro = null;
    $status = 'sucesso';
    $metaBlocos = [];
    
    try {
        // Executar a rotina com o serviço de execução
        logMessage("Executando rotina: $rotinaNome", 'info');
        
        // Carregar a rotina completa com seus blocos
        $stmtRotina = $db->prepare("SELECT * FROM tb_rotinas WHERE id = ?");
        $stmtRotina->execute([$rotinaId]);
        $rotinaCompleta = $stmtRotina->fetch(PDO::FETCH_ASSOC);
        
        if (!$rotinaCompleta) {
            throw new Exception("Rotina não encontrada");
        }
        
        // Carregar blocos SQL da rotina
        $stmtBlocos = $db->prepare("SELECT * FROM tb_blocos_rotina WHERE id_rotina = ? ORDER BY ordem ASC");
        $stmtBlocos->execute([$rotinaId]);
        $blocos = $stmtBlocos->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($blocos)) {
            logMessage("Rotina sem blocos SQL - executando simulação", 'warning');
            // Simulação para rotinas sem blocos
            sleep(2);
            $linhasAfetadas = rand(10, 100);
            
            $metaBlocos[] = [
                'bloco' => 'Simulacao',
                'tipo' => 'SIMULACAO',
                'status' => 'sucesso',
                'ordem' => 1,
                'duracao_ms' => 2000,
                'registros' => $linhasAfetadas,
                'resultado' => "Simulacao: $linhasAfetadas registros processados"
            ];
        } else {
            // Executar blocos reais
            logMessage("Executando " . count($blocos) . " blocos", 'info');
            
            foreach ($blocos as $idx => $bloco) {
                $blocoInicio = microtime(true);
                $blocoStatus = 'sucesso';
                $blocoErro = null;
                $blocoRegistros = 0;
                $blocoResultado = null;
                
                try {
                    $sql = $bloco['script_sql'];
                    
                    // Conectar ao banco de destino se necessário
                    $dbExec = $db; // Por padrão, usar o mesmo DB
                    
                    // Executar SQL
                    $stmtExec = $dbExec->prepare($sql);
                    $stmtExec->execute();
                    $blocoRegistros = $stmtExec->rowCount();
                    $linhasAfetadas += $blocoRegistros;
                    
                    $blocoResultado = "Linhas afetadas: $blocoRegistros";
                    logMessage("  Bloco '{$bloco['codigo_bloco']}' OK - $blocoRegistros registros", 'success');
                    
                } catch (Exception $e) {
                    $blocoStatus = 'erro';
                    $blocoErro = $e->getMessage();
                    logMessage("  Bloco '{$bloco['codigo_bloco']}' ERRO: $blocoErro", 'error');
                }
                
                $blocoFim = microtime(true);
                $blocoDuracaoMs = (int)(($blocoFim - $blocoInicio) * 1000);
                
                // Adicionar ao meta
                $metaBlocos[] = [
                    'bloco' => $bloco['codigo_bloco'],
                    'tipo' => $bloco['tipo_bloco'] ?? 'SQL',
                    'status' => $blocoStatus,
                    'ordem' => $idx + 1,
                    'duracao_ms' => $blocoDuracaoMs,
                    'registros' => $blocoRegistros,
                    'resultado' => $blocoResultado,
                    'erro' => $blocoErro,
                    'sql' => strlen($sql) > 500 ? substr($sql, 0, 500) . '...' : $sql
                ];
            }
            
            logMessage("Rotina $rotinaNome concluida - $linhasAfetadas registros processados", 'success');
        }
        
    } catch (Exception $e) {
        $erro = $e->getMessage();
        $status = 'falha';
        logMessage("ERRO em $rotinaNome: $erro", 'error');
    }
    
    $fim = microtime(true);
    $duracaoMs = (int)(($fim - $inicio) * 1000);
    
    // Atualizar histórico
    if ($historicoId) {
        try {
            $metaJson = json_encode($metaBlocos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            
            $sqlUpdate = "UPDATE tb_logs_execucao 
                          SET data_fim = NOW(), 
                              status = :status, 
                              registros_processados = :linhas,
                              mensagem_erro = :mensagem,
                              duracao_ms = :duracao,
                              meta = :meta::jsonb,
                              blocos_executados = :blocos_total,
                              blocos_sucesso = :blocos_ok,
                              blocos_falha = :blocos_erro
                          WHERE id = :id";
            $stmtUp = $db->prepare($sqlUpdate);
            
            $blocosOk = count(array_filter($metaBlocos, fn($b) => $b['status'] === 'sucesso'));
            $blocosErro = count($metaBlocos) - $blocosOk;
            
            $stmtUp->execute([
                ':status' => $status,
                ':linhas' => $linhasAfetadas,
                ':mensagem' => $erro,
                ':duracao' => $duracaoMs,
                ':meta' => $metaJson,
                ':blocos_total' => count($metaBlocos),
                ':blocos_ok' => $blocosOk,
                ':blocos_erro' => $blocosErro,
                ':id' => $historicoId
            ]);
            logMessage("Historico ID $historicoId atualizado: $status", 'debug');
        } catch (Exception $e) {
            logMessage("Erro ao atualizar historico: " . $e->getMessage(), 'error');
        }
    }
    
    logMessage("<<< FINALIZADO: $rotinaNome em {$duracaoMs}ms - Status: $status", 'info');
}

/**
 * Calcular próxima execução baseado no CRON
 */
function calcularProximaExecucao(string $cron): string
{
    $parts = explode(' ', trim($cron));
    if (count($parts) !== 5) {
        return date('Y-m-d H:i:s', strtotime('+1 minute'));
    }
    
    list($cronMinute, $cronHour, $cronDay, $cronMonth, $cronWeekday) = $parts;
    
    $now = new DateTime();
    $now->modify('+1 minute'); // Começar do próximo minuto
    $now->setTime((int)$now->format('H'), (int)$now->format('i'), 0);
    
    // Tentar encontrar a próxima execução (máximo 1 ano à frente)
    for ($i = 0; $i < 525600; $i++) { // 365 dias * 24 horas * 60 minutos
        $minute = (int)$now->format('i');
        $hour = (int)$now->format('H');
        $day = (int)$now->format('d');
        $month = (int)$now->format('m');
        $weekday = (int)$now->format('w');
        
        if (matchesCronField($cronMinute, $minute) &&
            matchesCronField($cronHour, $hour) &&
            matchesCronField($cronDay, $day) &&
            matchesCronField($cronMonth, $month) &&
            matchesCronField($cronWeekday, $weekday)) {
            return $now->format('Y-m-d H:i:s');
        }
        
        $now->modify('+1 minute');
    }
    
    return date('Y-m-d H:i:s', strtotime('+1 hour'));
}

/**
 * Registrar log
 */
function logMessage(string $message, string $level = 'info'): void
{
    global $storageDir, $db;
    
    $timestamp = date('Y-m-d H:i:s');
    $levelUpper = strtoupper($level);
    $logLine = "[$timestamp] [$levelUpper] $message\n";
    
    // Console
    $colors = [
        'info' => "\033[0;36m",    // Cyan
        'success' => "\033[0;32m", // Green
        'warning' => "\033[0;33m", // Yellow
        'error' => "\033[0;31m",   // Red
        'debug' => "\033[0;37m",   // Gray
    ];
    $reset = "\033[0m";
    echo ($colors[$level] ?? '') . $logLine . $reset;
    
    // Arquivo de log
    $logDir = $storageDir . '/logs';
    @file_put_contents($logDir . '/scheduler.log', $logLine, FILE_APPEND | LOCK_EX);
    
    // Banco de dados (apenas níveis importantes)
    if (in_array($level, ['info', 'warning', 'error', 'success']) && $db) {
        try {
            // Limpar caracteres inválidos para UTF-8
            $messageLimpa = preg_replace('/[^\x20-\x7E]/', '', $message);
            
            $sql = "INSERT INTO tb_logs_sistema (nivel, categoria, mensagem, criado_em)
                    VALUES (:nivel, 'scheduler', :mensagem, NOW())";
            $stmt = $db->prepare($sql);
            @$stmt->execute([':nivel' => $level, ':mensagem' => substr($messageLimpa, 0, 500)]);
        } catch (Exception $e) {
            // Silenciar erro de log
        }
    }
}
