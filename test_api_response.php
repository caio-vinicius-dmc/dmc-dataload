<?php
// Teste simples sem incluir index.php

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
    
    $stmt = $db->prepare("
        SELECT 
            e.id,
            e.id_rotina,
            e.data_inicio,
            e.data_fim,
            e.status,
            e.mensagem_erro,
            e.duracao_ms,
            e.blocos_executados,
            e.blocos_sucesso,
            e.blocos_falha,
            e.registros_processados,
            e.meta,
            e.detalhes_json,
            r.nome as rotina_nome
        FROM tb_logs_execucao e
        JOIN tb_rotinas r ON r.id = e.id_rotina
        WHERE e.id = 33
    ");
    $stmt->execute();
    $exec = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$exec) {
        echo "Execução não encontrada\n";
        exit;
    }
    
    // Processar meta/detalhes
    $meta = json_decode($exec['meta'] ?? '[]', true);
    $detalhes = json_decode($exec['detalhes_json'] ?? '[]', true);
    
    // Normalizar formato
    $logs = [];
    
    if (!empty($meta)) {
        $logs = $meta;
    } elseif (!empty($detalhes)) {
        foreach ($detalhes as $det) {
            $logs[] = [
                'bloco' => $det['bloco'] ?? 'Unknown',
                'tipo' => $det['tipo'] ?? 'SQL',
                'status' => isset($det['sucesso']) ? ($det['sucesso'] ? 'sucesso' : 'erro') : 'sucesso',
                'ordem' => $det['ordem'] ?? 0,
                'duracao_ms' => $det['duracao_ms'] ?? 0,
                'registros' => $det['registros'] ?? 0,
                'resultado' => $det['resultado'] ?? null,
                'erro' => $det['erro'] ?? null,
                'sql' => $det['sql'] ?? null
            ];
        }
    }
    
    $response = [
        'id' => $exec['id'],
        'rotina_nome' => $exec['rotina_nome'],
        'data_inicio' => $exec['data_inicio'],
        'data_fim' => $exec['data_fim'],
        'status' => $exec['status'],
        'duracao_ms' => $exec['duracao_ms'],
        'blocos_executados' => $exec['blocos_executados'],
        'blocos_sucesso' => $exec['blocos_sucesso'],
        'blocos_falha' => $exec['blocos_falha'],
        'registros_processados' => $exec['registros_processados'],
        'mensagem_erro' => $exec['mensagem_erro'],
        'logs' => $logs
    ];
    
    echo "=== RESPOSTA DA API ===\n\n";
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
