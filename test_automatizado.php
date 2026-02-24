<?php
/**
 * Script de Testes Automatizados - Módulo Workflow
 * Testa todas as operações CRUD e funcionalidades
 */

$baseUrl = 'http://localhost:8042';
$testResults = [];
$totalTests = 0;
$passedTests = 0;
$cookieFile = sys_get_temp_dir() . '/test_cookies.txt';

// Limpar arquivo de cookies
if (file_exists($cookieFile)) {
    unlink($cookieFile);
}

// Função auxiliar para fazer requisições
function fazerRequisicao($url, $metodo = 'GET', $dados = null, $contentType = 'application/json') {
    global $cookieFile;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    
    if ($metodo !== 'GET') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);
        if ($dados) {
            if ($contentType === 'application/json') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($dados));
            }
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $json = json_decode($response, true);
    return ['code' => $httpCode, 'data' => $json, 'raw' => $response];
}

// Função para registrar teste
function registrarTeste($nome, $passou, $detalhes = '') {
    global $testResults, $totalTests, $passedTests;
    $totalTests++;
    if ($passou) $passedTests++;
    
    $status = $passou ? '✅' : '❌';
    $testResults[] = [
        'nome' => $nome,
        'passou' => $passou,
        'detalhes' => $detalhes
    ];
    
    echo "$status $nome\n";
    if (!$passou && $detalhes) {
        echo "   └─ $detalhes\n";
    }
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║     TESTES AUTOMATIZADOS - MÓDULO WORKFLOW DMC-DATALOAD        ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// =====================================================
// AUTENTICAÇÃO (TESTE #0)
// =====================================================
echo "🔐 TESTE #0: Autenticação\n";
echo str_repeat("─", 64) . "\n";

// Fazer login
$resLogin = fazerRequisicao("$baseUrl/login", 'POST', [
    'usuario' => 'admin',
    'senha' => 'admin123'
], 'application/x-www-form-urlencoded');

$autenticado = $resLogin['code'] === 200 && isset($resLogin['data']['sucesso']) && $resLogin['data']['sucesso'];
registrarTeste("0.1 Login", $autenticado, $autenticado ? "Sessão criada" : "Falha no login");

if (!$autenticado) {
    echo "\n❌ ERRO CRÍTICO: Não foi possível autenticar!\n";
    echo "   Verifique se o usuário 'admin' existe no banco de dados.\n\n";
    exit(1);
}

echo "\n";

// =====================================================
// TESTE 1: APIs EXTERNAS
// =====================================================
echo "📡 TESTE #1: APIs Externas\n";
echo str_repeat("─", 64) . "\n";

// 1.1 Listar APIs
$res = fazerRequisicao("$baseUrl/api/apis-externas/list");
$listarOk = $res['code'] === 200 && isset($res['data']['sucesso']) && $res['data']['sucesso'];
registrarTeste("1.1 Listar APIs", $listarOk, $listarOk ? count($res['data']['dados']) . " APIs encontradas" : "Código HTTP: {$res['code']}");

$apisIniciais = $listarOk ? count($res['data']['dados']) : 0;

// 1.2 Criar nova API
$novaApi = [
    'nome' => 'API Teste Automatizado',
    'descricao' => 'Criada pelo teste automatizado em ' . date('Y-m-d H:i:s'),
    'url' => 'https://jsonplaceholder.typicode.com/todos/1',
    'metodo' => 'GET',
    'auth_tipo' => 'none',
    'tipo_resposta' => 'json',
    'intervalo_polling' => 60,
    'timeout' => 30,
    'ativo' => true
];

$res = fazerRequisicao("$baseUrl/api/apis-externas/salvar", 'POST', $novaApi);
$criarApiOk = $res['code'] === 200 && isset($res['data']['sucesso']) && $res['data']['sucesso'];
$idApiCriada = $criarApiOk ? $res['data']['id'] : null;
registrarTeste("1.2 Criar API", $criarApiOk, $criarApiOk ? "ID: $idApiCriada" : "Erro: " . ($res['data']['erro'] ?? 'Desconhecido'));

// 1.3 Buscar API criada
if ($idApiCriada) {
    $res = fazerRequisicao("$baseUrl/api/apis-externas/get/$idApiCriada");
    $buscarOk = $res['code'] === 200 && isset($res['data']['sucesso']) && $res['data']['sucesso'];
    registrarTeste("1.3 Buscar API por ID", $buscarOk, $buscarOk ? "Nome: " . $res['data']['dados']['nome'] : "Erro ao buscar");
    
    // 1.4 Editar API
    $apiEditada = $novaApi;
    $apiEditada['id'] = $idApiCriada;
    $apiEditada['nome'] = 'API Teste EDITADA';
    
    $res = fazerRequisicao("$baseUrl/api/apis-externas/salvar", 'POST', $apiEditada);
    $editarOk = $res['code'] === 200 && isset($res['data']['sucesso']) && $res['data']['sucesso'];
    registrarTeste("1.4 Editar API", $editarOk);
    
    // 1.5 Testar API (fazer requisição real)
    $dadosTeste = $novaApi;
    $dadosTeste['id_api'] = $idApiCriada;
    
    $res = fazerRequisicao("$baseUrl/api/apis-externas/testar", 'POST', $dadosTeste);
    $testarOk = $res['code'] === 200 && isset($res['data']['sucesso']);
    registrarTeste("1.5 Testar API", $testarOk, $testarOk ? "HTTP {$res['data']['http_code']}, {$res['data']['tempo_ms']}ms" : "Erro de teste");
}

// =====================================================
// TESTE 2: EVENTOS API
// =====================================================
echo "\n";
echo "🎯 TESTE #2: Eventos de API\n";
echo str_repeat("─", 64) . "\n";

// 2.1 Listar eventos
$res = fazerRequisicao("$baseUrl/api/eventos-api/list");
$listarEventosOk = $res['code'] === 200 && isset($res['data']['sucesso']) && $res['data']['sucesso'];
registrarTeste("2.1 Listar Eventos", $listarEventosOk, $listarEventosOk ? count($res['data']['dados']) . " eventos" : "Erro");

// 2.2 Criar evento
if ($idApiCriada) {
    $novoEvento = [
        'id_api' => $idApiCriada,
        'nome' => 'Evento Teste',
        'descricao' => 'Evento de teste automatizado',
        'jsonpath' => '$.completed',
        'tipo_valor' => 'boolean',
        'operador' => 'equals',
        'valor_esperado' => 'true',
        'acao' => 'store_value',
        'armazenar_valor' => true,
        'ativo' => true
    ];
    
    $res = fazerRequisicao("$baseUrl/api/eventos-api/salvar", 'POST', $novoEvento);
    $criarEventoOk = $res['code'] === 200 && isset($res['data']['sucesso']) && $res['data']['sucesso'];
    $idEventoCriado = $criarEventoOk ? $res['data']['id'] : null;
    registrarTeste("2.2 Criar Evento", $criarEventoOk, $criarEventoOk ? "ID: $idEventoCriado" : "Erro: " . ($res['data']['erro'] ?? ''));
    
    // 2.3 Editar evento
    if ($idEventoCriado) {
        $eventoEditado = $novoEvento;
        $eventoEditado['id'] = $idEventoCriado;
        $eventoEditado['nome'] = 'Evento EDITADO';
        
        $res = fazerRequisicao("$baseUrl/api/eventos-api/salvar", 'POST', $eventoEditado);
        $editarEventoOk = $res['code'] === 200 && isset($res['data']['sucesso']) && $res['data']['sucesso'];
        registrarTeste("2.3 Editar Evento", editarEventoOk);
    }
}

// =====================================================
// TESTE 3: WORKFLOWS
// =====================================================
echo "\n";
echo "⚙️  TESTE #3: Workflows\n";
echo str_repeat("─", 64) . "\n";

// 3.1 Listar workflows
$res = fazerRequisicao("$baseUrl/api/workflows/list");
$listarWorkflowsOk = $res['code'] === 200 && isset($res['data']['sucesso']) && $res['data']['sucesso'];
registrarTeste("3.1 Listar Workflows", $listarWorkflowsOk, $listarWorkflowsOk ? count($res['data']['dados']) . " workflows" : "Erro");

// 3.2 Criar workflow
$novoWorkflow = [
    'nome' => 'Workflow Teste',
    'descricao' => 'Workflow de teste automatizado',
    'ativo' => false,
    'trigger_tipo' => 'manual',
    'dados_json' => [
        'nodes' => [
            [
                'id' => 'node_start',
                'type' => 'trigger',
                'data' => ['label' => 'Início'],
                'position' => ['x' => 100, 'y' => 100]
            ],
            [
                'id' => 'node_end',
                'type' => 'end',
                'data' => ['label' => 'Fim'],
                'position' => ['x' => 300, 'y' => 100]
            ]
        ],
        'edges' => [
            [
                'id' => 'edge_1',
                'source' => 'node_start',
                'target' => 'node_end'
            ]
        ]
    ],
    'trigger_config' => []
];

$res = fazerRequisicao("$baseUrl/api/workflows/salvar", 'POST', $novoWorkflow);
$criarWorkflowOk = $res['code'] === 200 && isset($res['data']['sucesso']) && $res['data']['sucesso'];
$idWorkflowCriado = $criarWorkflowOk ? $res['data']['id'] : null;
registrarTeste("3.2 Criar Workflow", $criarWorkflowOk, $criarWorkflowOk ? "ID: $idWorkflowCriado" : "Erro: " . ($res['data']['erro'] ?? ''));

// 3.3 Buscar workflow
if ($idWorkflowCriado) {
    $res = fazerRequisicao("$baseUrl/api/workflows/get/$idWorkflowCriado");
    $buscarWorkflowOk = $res['code'] === 200 && isset($res['data']['sucesso']) && $res['data']['sucesso'];
    registrarTeste("3.3 Buscar Workflow", $buscarWorkflowOk);
    
    // 3.4 Alternar status ativo
    $res = fazerRequisicao("$baseUrl/api/workflows/toggle/$idWorkflowCriado", 'POST');
    $toggleOk = $res['code'] === 200 && isset($res['data']['sucesso']) && $res['data']['sucesso'];
    registrarTeste("3.4 Alternar Status Ativo", $toggleOk);
    
    // 3.5 Duplicar workflow
    $res = fazerRequisicao("$baseUrl/api/workflows/duplicar/$idWorkflowCriado", 'POST');
    $duplicarOk = $res['code'] === 200 && isset($res['data']['sucesso']) && $res['data']['sucesso'];
    $idWorkflowDuplicado = $duplicarOk ? $res['data']['id'] : null;
    registrarTeste("3.5 Duplicar Workflow", $duplicarOk, $duplicarOk ? "Novo ID: $idWorkflowDuplicado" : "");
}

// =====================================================
// TESTE 4: EXECUÇÕES E ESTATÍSTICAS
// =====================================================
echo "\n";
echo "📊 TESTE #4: Execuções e Estatísticas\n";
echo str_repeat("─", 64) . "\n";

// 4.1 Listar execuções
$res = fazerRequisicao("$baseUrl/api/workflow-execucoes/list");
$listarExecOk = $res['code'] === 200 && isset($res['data']['sucesso']) && $res['data']['sucesso'];
registrarTeste("4.1 Listar Execuções", $listarExecOk, $listarExecOk ? count($res['data']['dados']) . " execuções" : "Erro");

// 4.2 Obter estatísticas
$res = fazerRequisicao("$baseUrl/api/workflows/stats");
$statsOk = $res['code'] === 200 && isset($res['data']['sucesso']) && $res['data']['sucesso'];
registrarTeste("4.2 Obter Estatísticas", $statsOk, $statsOk ? "Total workflows: " . $res['data']['dados']['geral']['total_workflows'] : "Erro");

// 4.3 Listar rotinas disponíveis
$res = fazerRequisicao("$baseUrl/api/workflows/rotinas-disponiveis");
$rotinasOk = $res['code'] === 200 && isset($res['data']['sucesso']) && $res['data']['sucesso'];
registrarTeste("4.3 Listar Rotinas Disponíveis", $rotinasOk, $rotinasOk ? count($res['data']['dados']) . " rotinas" : "Erro");

// =====================================================
// TESTE 5: LIMPEZA (DELETAR DADOS DE TESTE)
// =====================================================
echo "\n";
echo "🧹 TESTE #5: Limpeza de Dados de Teste\n";
echo str_repeat("─", 64) . "\n";

// Deletar workflow duplicado
if (isset($idWorkflowDuplicado)) {
    $res = fazerRequisicao("$baseUrl/api/workflows/delete/$idWorkflowDuplicado", 'POST');
    registrarTeste("5.1 Deletar Workflow Duplicado", $res['code'] === 200);
}

// Deletar workflow criado
if (isset($idWorkflowCriado)) {
    $res = fazerRequisicao("$baseUrl/api/workflows/delete/$idWorkflowCriado", 'POST');
    registrarTeste("5.2 Deletar Workflow", $res['code'] === 200);
}

// Deletar evento criado
if (isset($idEventoCriado)) {
    $res = fazerRequisicao("$baseUrl/api/eventos-api/delete/$idEventoCriado", 'POST');
    registrarTeste("5.3 Deletar Evento", $res['code'] === 200);
}

// Deletar API criada
if (isset($idApiCriada)) {
    $res = fazerRequisicao("$baseUrl/api/apis-externas/delete/$idApiCriada", 'POST');
    registrarTeste("5.4 Deletar API", $res['code'] === 200);
}

// =====================================================
// RESUMO FINAL
// =====================================================
echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                       RESUMO DOS TESTES                        ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$failedTests = $totalTests - $passedTests;
$percentual = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 1) : 0;

echo "✅ Testes aprovados: $passedTests / $totalTests ($percentual%)\n";
if ($failedTests > 0) {
    echo "❌ Testes reprovados: $failedTests\n\n";
    echo "Testes que falharam:\n";
    foreach ($testResults as $test) {
        if (!$test['passou']) {
            echo "  • {$test['nome']}\n";
            if ($test['detalhes']) {
                echo "    └─ {$test['detalhes']}\n";
            }
        }
    }
}

echo "\n";
if ($percentual >= 90) {
    echo "🎉 EXCELENTE! Sistema funcionando corretamente!\n";
} elseif ($percentual >= 70) {
    echo "⚠️  BOM, mas há problemas a corrigir.\n";
} else {
    echo "❌ CRÍTICO! Muitos testes falhando.\n";
}

echo "\n";
