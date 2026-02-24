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
    
    // Cancelar execução 29
    $stmt = $db->prepare("
        UPDATE tb_logs_execucao 
        SET 
            status = 'erro',
            data_fim = NOW(),
            erro = 'Execução cancelada - travamento detectado'
        WHERE id = 29 AND status = 'executando'
    ");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "✓ Execução 29 cancelada\n";
    } else {
        echo "✗ Nenhuma execução cancelada (já concluída ou não encontrada)\n";
    }
    
} catch (Exception $e) {
    echo "✗ Erro: " . $e->getMessage() . "\n";
}
