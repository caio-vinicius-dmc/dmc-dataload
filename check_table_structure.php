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
    
    echo "=== COLUNAS TB_LOGS_EXECUCAO ===\n\n";
    $stmt = $db->query("
        SELECT column_name, data_type 
        FROM information_schema.columns 
        WHERE table_name = 'tb_logs_execucao'
        ORDER BY ordinal_position
    ");
    
    while ($col = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $col['column_name'] . " (" . $col['data_type'] . ")\n";
    }
    
} catch (Exception $e) {
    echo "✗ Erro: " . $e->getMessage() . "\n";
}
