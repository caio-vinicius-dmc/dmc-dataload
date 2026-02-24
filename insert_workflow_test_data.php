<?php
/**
 * Script para inserir dados de teste para o sistema de Workflows
 */
require 'vendor/autoload.php';
use App\Core\Database;

Database::loadEnv('./');
$db = Database::getConexao();

echo "=== Inserindo dados de teste para Workflows ===\n\n";

// 1. Inserir API de teste (JSONPlaceholder)
echo "1. Criando API de teste (JSONPlaceholder)...\n";
$sql = "INSERT INTO tb_api_externas 
    (nome, descricao, url, metodo, headers, auth_tipo, credenciais, tipo_resposta, intervalo_polling, timeout, ativo)
    VALUES 
    ('JSONPlaceholder - Posts', 'API pública para testes', 'https://jsonplaceholder.typicode.com/posts', 'GET', '{}', 'none', '{}', 'json', 60, 30, true)
    ON CONFLICT DO NOTHING
    RETURNING id";

try {
    $stmt = $db->query($sql);
    $apiId = $stmt->fetchColumn();
    if (!$apiId) {
        // Buscar ID existente
        $stmt = $db->query("SELECT id FROM tb_api_externas WHERE nome = 'JSONPlaceholder - Posts' LIMIT 1");
        $apiId = $stmt->fetchColumn();
    }
    echo "   API ID: {$apiId}\n";
} catch (Exception $e) {
    echo "   Erro: " . $e->getMessage() . "\n";
    $stmt = $db->query("SELECT id FROM tb_api_externas WHERE nome LIKE 'JSONPlaceholder%' LIMIT 1");
    $apiId = $stmt->fetchColumn();
    echo "   Usando API existente ID: {$apiId}\n";
}

// 2. Inserir API de usuários
echo "\n2. Criando API de usuários...\n";
$sql = "INSERT INTO tb_api_externas 
    (nome, descricao, url, metodo, headers, auth_tipo, credenciais, tipo_resposta, intervalo_polling, timeout, ativo)
    VALUES 
    ('JSONPlaceholder - Users', 'API de usuários para testes', 'https://jsonplaceholder.typicode.com/users', 'GET', '{}', 'none', '{}', 'json', 120, 30, true)
    ON CONFLICT DO NOTHING
    RETURNING id";

try {
    $stmt = $db->query($sql);
    $apiUsersId = $stmt->fetchColumn();
    if (!$apiUsersId) {
        $stmt = $db->query("SELECT id FROM tb_api_externas WHERE nome = 'JSONPlaceholder - Users' LIMIT 1");
        $apiUsersId = $stmt->fetchColumn();
    }
    echo "   API Users ID: {$apiUsersId}\n";
} catch (Exception $e) {
    echo "   Erro: " . $e->getMessage() . "\n";
}

// 3. Criar workflow de teste
echo "\n3. Criando workflow de teste...\n";
$dadosJson = json_encode([
    'nodes' => [
        ['id' => 'node_1', 'tipo' => 'trigger', 'label' => 'Início', 'posicao' => ['x' => 100, 'y' => 50], 'configuracao' => []],
        ['id' => 'node_2', 'tipo' => 'notification', 'label' => 'Notificar', 'posicao' => ['x' => 100, 'y' => 200], 'configuracao' => ['tipo' => 'log', 'mensagem' => 'Workflow executado!']],
        ['id' => 'node_3', 'tipo' => 'end', 'label' => 'Fim', 'posicao' => ['x' => 100, 'y' => 350], 'configuracao' => ['status' => 'success']]
    ],
    'edges' => [
        ['id' => 'edge_1', 'source' => 'node_1', 'target' => 'node_2', 'condicao' => 'always'],
        ['id' => 'edge_2', 'source' => 'node_2', 'target' => 'node_3', 'condicao' => 'always']
    ]
]);

$sql = "INSERT INTO tb_workflows 
    (nome, descricao, trigger_tipo, trigger_config, dados_json, ativo, versao)
    VALUES 
    ('Workflow de Teste', 'Workflow criado automaticamente para testes', 'api_event', '{}', :dados, true, 1)
    ON CONFLICT DO NOTHING
    RETURNING id";

try {
    $stmt = $db->prepare($sql);
    $stmt->execute([':dados' => $dadosJson]);
    $workflowId = $stmt->fetchColumn();
    if (!$workflowId) {
        $stmt = $db->query("SELECT id FROM tb_workflows WHERE nome = 'Workflow de Teste' LIMIT 1");
        $workflowId = $stmt->fetchColumn();
    }
    echo "   Workflow ID: {$workflowId}\n";
} catch (Exception $e) {
    echo "   Erro: " . $e->getMessage() . "\n";
    $workflowId = null;
}

// 4. Criar evento de API
if ($apiId && $workflowId) {
    echo "\n4. Criando evento de API...\n";
    $sql = "INSERT INTO tb_eventos_api 
        (id_api, nome, descricao, jsonpath, tipo_valor, operador, valor_esperado, acao, id_workflow, ativo)
        VALUES 
        (:id_api, 'Post ID Maior que 50', 'Dispara quando encontra post com ID > 50', '$[0].id', 'number', 'greater_than', '50', 'trigger_workflow', :id_workflow, true)
        ON CONFLICT DO NOTHING
        RETURNING id";
    
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute([':id_api' => $apiId, ':id_workflow' => $workflowId]);
        $eventoId = $stmt->fetchColumn();
        if (!$eventoId) {
            $stmt = $db->query("SELECT id FROM tb_eventos_api WHERE nome = 'Post ID Maior que 50' LIMIT 1");
            $eventoId = $stmt->fetchColumn();
        }
        echo "   Evento ID: {$eventoId}\n";
    } catch (Exception $e) {
        echo "   Erro: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Verificando dados inseridos ===\n";

echo "\nAPIs Externas:\n";
$stmt = $db->query("SELECT id, nome, url, ativo FROM tb_api_externas ORDER BY id");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo "  [{$row['id']}] {$row['nome']} - " . ($row['ativo'] ? 'ATIVO' : 'INATIVO') . "\n";
}

echo "\nWorkflows:\n";
$stmt = $db->query("SELECT id, nome, trigger_tipo, ativo FROM tb_workflows ORDER BY id");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo "  [{$row['id']}] {$row['nome']} ({$row['trigger_tipo']}) - " . ($row['ativo'] ? 'ATIVO' : 'INATIVO') . "\n";
}

echo "\nEventos de API:\n";
$stmt = $db->query("
    SELECT e.id, e.nome, a.nome as api_nome, e.ativo 
    FROM tb_eventos_api e 
    JOIN tb_api_externas a ON a.id = e.id_api 
    ORDER BY e.id
");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo "  [{$row['id']}] {$row['nome']} (API: {$row['api_nome']}) - " . ($row['ativo'] ? 'ATIVO' : 'INATIVO') . "\n";
}

echo "\n=== Dados de teste inseridos com sucesso! ===\n";
