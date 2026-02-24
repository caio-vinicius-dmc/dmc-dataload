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
    
    // Buscar última execução
    $stmt = $db->query("
        SELECT 
            id,
            id_rotina,
            status,
            data_inicio,
            data_fim,
            blocos_executados,
            blocos_sucesso,
            blocos_falha,
            registros_processados,
            meta,
            mensagem_erro
        FROM tb_logs_execucao
        ORDER BY id DESC
        LIMIT 1
    ");
    
    $exec = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($exec) {
        echo "=== ÚLTIMA EXECUÇÃO ===\n";
        echo "ID: " . $exec['id'] . "\n";
        echo "Rotina: " . $exec['id_rotina'] . "\n";
        echo "Status: " . $exec['status'] . "\n";
        echo "Início: " . $exec['data_inicio'] . "\n";
        echo "Fim: " . $exec['data_fim'] . "\n";
        echo "Blocos executados: " . $exec['blocos_executados'] . "\n";
        echo "Blocos sucesso: " . $exec['blocos_sucesso'] . "\n";
        echo "Blocos falha: " . $exec['blocos_falha'] . "\n";
        echo "Registros processados: " . $exec['registros_processados'] . "\n";
        
        if ($exec['mensagem_erro']) {
            echo "Erro: " . $exec['mensagem_erro'] . "\n";
        }
        
        echo "\n=== META ===\n";
        
        if ($exec['meta']) {
            $meta = json_decode($exec['meta'], true);
            echo json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        } else {
            echo "NULL\n";
        }
    } else {
        echo "Nenhuma execução encontrada\n";
    }
    
} catch (Exception $e) {
    echo "✗ Erro: " . $e->getMessage() . "\n";
}
