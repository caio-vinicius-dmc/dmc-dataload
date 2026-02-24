<?php
// Carregar .env
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

try {
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $port = $_ENV['DB_PORT'] ?? '5433';
    $dbname = $_ENV['DB_DATABASE'] ?? 'db_dmc_dataload';
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $db = new PDO($dsn, $_ENV['DB_USERNAME'] ?? 'postgres', $_ENV['DB_PASSWORD'] ?? 'dmc2023@');
    
    // Forçar próxima execução para agora
    $stmt = $db->prepare("
        UPDATE tb_rotinas
        SET proxima_execucao = NOW() - INTERVAL '1 second'
        WHERE id = 25
    ");
    $stmt->execute();
    
    echo "✓ Próxima execução forçada para agora\n";
    echo "Aguarde 10 segundos para o worker detectar...\n";
    
    sleep(10);
    
    // Verificar última execução
    $stmt = $db->query("
        SELECT 
            id,
            status,
            blocos_executados,
            blocos_sucesso,
            blocos_falha,
            registros_processados,
            meta::text as meta_text
        FROM tb_logs_execucao
        ORDER BY id DESC
        LIMIT 1
    ");
    
    $exec = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "\n=== ÚLTIMA EXECUÇÃO ===\n";
    echo "ID: " . $exec['id'] . "\n";
    echo "Status: " . $exec['status'] . "\n";
    echo "Blocos: " . $exec['blocos_executados'] . " (sucesso: " . $exec['blocos_sucesso'] . ", falha: " . $exec['blocos_falha'] . ")\n";
    echo "Registros: " . $exec['registros_processados'] . "\n";
    
    if ($exec['meta_text']) {
        echo "\nMETA salvo: SIM ✓\n";
        $meta = json_decode($exec['meta_text'], true);
        if (isset($meta[0])) {
            $bloco = $meta[0];
            echo "  - Bloco: " . $bloco['bloco'] . "\n";
            echo "  - Status: " . $bloco['status'] . "\n";
            echo "  - Registros: " . $bloco['registros'] . "\n";
        }
    } else {
        echo "\nMETA salvo: NÃO ✗\n";
    }
    
} catch (Exception $e) {
    echo "✗ Erro: " . $e->getMessage() . "\n";
}
