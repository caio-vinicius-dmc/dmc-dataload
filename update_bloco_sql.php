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
    
    // Atualizar script SQL do bloco 41 para algo que existe
    $stmt = $db->prepare("
        UPDATE tb_blocos_rotina
        SET script_sql = 'SELECT id, codigo_bloco, ordem FROM tb_blocos_rotina LIMIT 5'
        WHERE id = 41
    ");
    $stmt->execute();
    
    echo "✓ Bloco 41 atualizado com SQL que funciona\n";
    
} catch (Exception $e) {
    echo "✗ Erro: " . $e->getMessage() . "\n";
}
