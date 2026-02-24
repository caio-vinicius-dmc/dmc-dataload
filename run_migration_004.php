<?php
/**
 * Executar migração 004 - Sistema de Workflows
 */

// Carregar .env
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
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
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== MIGRAÇÃO 004: Sistema de Workflows ===\n\n";
    
    // Ler e executar SQL
    $sql = file_get_contents(__DIR__ . '/migrations/004_create_workflow_tables.sql');
    
    // Separar por comandos (considerando comentários)
    $statements = [];
    $current = '';
    $lines = explode("\n", $sql);
    
    foreach ($lines as $line) {
        $trimmed = trim($line);
        
        // Ignorar linhas vazias e comentários
        if (empty($trimmed) || strpos($trimmed, '--') === 0) {
            continue;
        }
        
        $current .= ' ' . $line;
        
        // Se termina com ;, é um comando completo
        if (substr($trimmed, -1) === ';') {
            $statements[] = trim($current);
            $current = '';
        }
    }
    
    // Executar cada statement
    $success = 0;
    $errors = [];
    
    foreach ($statements as $stmt) {
        if (empty(trim($stmt))) continue;
        
        try {
            $db->exec($stmt);
            $success++;
            
            // Mostrar o que foi criado
            if (preg_match('/CREATE TABLE IF NOT EXISTS (\w+)/i', $stmt, $matches)) {
                echo "✓ Tabela criada: {$matches[1]}\n";
            } elseif (preg_match('/CREATE INDEX IF NOT EXISTS (\w+)/i', $stmt, $matches)) {
                echo "  + Índice: {$matches[1]}\n";
            } elseif (preg_match('/INSERT INTO (\w+)/i', $stmt, $matches)) {
                echo "✓ Dados inseridos em: {$matches[1]}\n";
            } elseif (preg_match('/COMMENT ON TABLE (\w+)/i', $stmt, $matches)) {
                echo "  + Comentário: {$matches[1]}\n";
            }
            
        } catch (PDOException $e) {
            // Ignorar erros de "já existe"
            if (strpos($e->getMessage(), 'already exists') === false &&
                strpos($e->getMessage(), 'duplicate key') === false) {
                $errors[] = $e->getMessage();
            }
        }
    }
    
    echo "\n=== RESULTADO ===\n";
    echo "Comandos executados: $success\n";
    
    if (!empty($errors)) {
        echo "\nErros encontrados:\n";
        foreach ($errors as $err) {
            echo "  ✗ $err\n";
        }
    }
    
    // Verificar tabelas criadas
    echo "\n=== TABELAS CRIADAS ===\n";
    $tables = $db->query("
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_name LIKE 'tb_%'
        ORDER BY table_name
    ")->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        echo "  • $table\n";
    }
    
    echo "\n✓ Migração concluída!\n";
    
} catch (Exception $e) {
    echo "✗ Erro fatal: " . $e->getMessage() . "\n";
    exit(1);
}
