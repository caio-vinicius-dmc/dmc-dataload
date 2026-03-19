<?php
/**
 * Teste E2E do sistema RBAC
 */
require_once __DIR__ . '/vendor/autoload.php';

\App\Core\Database::loadEnv(__DIR__ . '/');
$db = \App\Core\Database::getConexao();
$erros = 0;
$total = 0;

function teste($nome, $resultado) {
    global $erros, $total;
    $total++;
    $status = $resultado ? '[OK]' : '[FALHOU]';
    if (!$resultado) $erros++;
    echo "  $status $nome\n";
    return $resultado;
}

echo "========================================\n";
echo "  TESTE E2E - RBAC DMC DataLoad\n";
echo "========================================\n\n";

// ==============================
// 1. TABELAS
// ==============================
echo "--- 1. Tabelas RBAC ---\n";
$tabelas = ['tb_empresas', 'tb_projetos', 'tb_usuario_empresas', 'tb_usuario_projetos', 'tb_recurso_empresas', 'tb_recurso_projetos', 'tb_compartilhamentos'];
foreach ($tabelas as $t) {
    $existe = $db->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_name = '$t'")->fetchColumn();
    teste("Tabela $t existe", $existe > 0);
}

// ==============================
// 2. USUÁRIOS E NÍVEIS
// ==============================
echo "\n--- 2. Usuários e Níveis ---\n";
$usuarios = $db->query("SELECT id, nome_usuario, nivel_acesso FROM tb_usuarios ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
foreach ($usuarios as $u) {
    echo "  [INFO] ID {$u['id']}: {$u['nome_usuario']} => {$u['nivel_acesso']}\n";
}
teste("Admin (id=1) é super_admin", $usuarios[0]['nivel_acesso'] === 'super_admin');

// ==============================
// 3. COLUNAS criado_por
// ==============================
echo "\n--- 3. Colunas criado_por ---\n";
$checks = [
    ['tb_perfis_conexao', 'criado_por'],
    ['tb_eventos_api', 'criado_por'],
    ['tb_rotinas', 'id_usuario_criador'],
    ['tb_pipelines', 'criado_por'],
    ['tb_workflows', 'criado_por'],
    ['tb_api_externas', 'criado_por'],
];
foreach ($checks as [$tabela, $coluna]) {
    $existe = $db->query("SELECT COUNT(*) FROM information_schema.columns WHERE table_name = '$tabela' AND column_name = '$coluna'")->fetchColumn();
    teste("$tabela.$coluna", $existe > 0);
}

// ==============================
// 4. SERVIÇO DE PERMISSÃO
// ==============================
echo "\n--- 4. ServicoPermissao ---\n";
try {
    // Sem sessão ativa, testar métodos estáticos
    $niveis = ['operador' => 1, 'desenvolvedor' => 2, 'admin' => 3, 'super_admin' => 4];
    foreach ($niveis as $papel => $esperado) {
        teste("obterNivel('$papel') = $esperado", \App\Servicos\ServicoPermissao::obterNivel($papel) === $esperado);
    }
    
    // Testar operadorPodeAcessarPagina
    teste("Operador pode: /dashboard", \App\Servicos\ServicoPermissao::operadorPodeAcessarPagina('/dashboard'));
    teste("Operador pode: /historico", \App\Servicos\ServicoPermissao::operadorPodeAcessarPagina('/historico'));
    teste("Operador pode: /diagrama", \App\Servicos\ServicoPermissao::operadorPodeAcessarPagina('/diagrama'));
    teste("Operador pode: /scheduler", \App\Servicos\ServicoPermissao::operadorPodeAcessarPagina('/scheduler'));
    teste("Operador pode: /calendario", \App\Servicos\ServicoPermissao::operadorPodeAcessarPagina('/calendario'));
    teste("Operador NÃO pode: /conexoes", !\App\Servicos\ServicoPermissao::operadorPodeAcessarPagina('/conexoes'));
    teste("Operador NÃO pode: /rotinas", !\App\Servicos\ServicoPermissao::operadorPodeAcessarPagina('/rotinas'));
    teste("Operador NÃO pode: /workflows", !\App\Servicos\ServicoPermissao::operadorPodeAcessarPagina('/workflows'));
    teste("Operador NÃO pode: /pipelines", !\App\Servicos\ServicoPermissao::operadorPodeAcessarPagina('/pipelines'));
    teste("Operador NÃO pode: /admin/usuarios", !\App\Servicos\ServicoPermissao::operadorPodeAcessarPagina('/admin/usuarios'));
    
    // Testar operadorPodeAcessarApi
    teste("Oper API GET /api/dashboard/metricas", \App\Servicos\ServicoPermissao::operadorPodeAcessarApi('/api/dashboard/metricas', 'GET'));
    teste("Oper API POST bloqueado", !\App\Servicos\ServicoPermissao::operadorPodeAcessarApi('/api/dashboard/metricas', 'POST'));
    teste("Oper API GET /conexoes/list bloqueado", !\App\Servicos\ServicoPermissao::operadorPodeAcessarApi('/conexoes/list', 'GET'));
    
} catch (Exception $e) {
    echo "  [ERRO] " . $e->getMessage() . "\n";
    $erros++;
}

// ==============================
// 5. TESTE HTTP COM CURL
// ==============================
echo "\n--- 5. Testes HTTP (Login + RBAC) ---\n";

$baseUrl = 'http://localhost/DMC-DATALOAD/public';
$cookieFile = tempnam(sys_get_temp_dir(), 'rbac_test_');

// Login como admin
$ch = curl_init("$baseUrl/login");
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query(['usuario' => 'admin', 'senha' => 'Admin@2026']),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cookieFile,
    CURLOPT_COOKIEFILE => $cookieFile,
    CURLOPT_FOLLOWLOCATION => true,
]);
$resp = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$loginRes = json_decode($resp, true);
teste("Login como admin: status 200", $httpCode === 200);
teste("Login como admin: sucesso", ($loginRes['sucesso'] ?? false) === true);

if ($loginRes['sucesso'] ?? false) {
    // Testar dashboard (deve funcionar)
    $ch = curl_init("$baseUrl/api/dashboard/metricas");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookieFile,
    ]);
    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    teste("Super admin: GET /api/dashboard/metricas = 200", $httpCode === 200);

    // Testar listar conexões
    $ch = curl_init("$baseUrl/conexoes/list");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookieFile,
    ]);
    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $conRes = json_decode($resp, true);
    curl_close($ch);
    teste("Super admin: GET /conexoes/list = 200", $httpCode === 200);
    teste("Super admin: conexões retornadas", isset($conRes['data']));

    // Testar listar rotinas
    $ch = curl_init("$baseUrl/rotinas/list");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookieFile,
    ]);
    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $rotRes = json_decode($resp, true);
    curl_close($ch);
    teste("Super admin: GET /rotinas/list = 200", $httpCode === 200);
    teste("Super admin: rotinas retornadas", isset($rotRes['data']));

    // Testar listar pipelines
    $ch = curl_init("$baseUrl/pipelines/list");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookieFile,
    ]);
    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $pipRes = json_decode($resp, true);
    curl_close($ch);
    teste("Super admin: GET /pipelines/list = 200", $httpCode === 200);
    teste("Super admin: pipelines retornadas", isset($pipRes['data']) || isset($pipRes['sucesso']));

    // Testar listar workflows
    $ch = curl_init("$baseUrl/api/workflows/list");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookieFile,
    ]);
    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $wfRes = json_decode($resp, true);
    curl_close($ch);
    teste("Super admin: GET /api/workflows/list = 200", $httpCode === 200);
    teste("Super admin: workflows retornados", isset($wfRes['dados']) || isset($wfRes['sucesso']));

    // Testar listar APIs externas
    $ch = curl_init("$baseUrl/api/apis-externas/list");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookieFile,
    ]);
    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $apiRes = json_decode($resp, true);
    curl_close($ch);
    teste("Super admin: GET /api/apis-externas/list = 200", $httpCode === 200);
    teste("Super admin: APIs retornadas", isset($apiRes['dados']) || isset($apiRes['sucesso']));

    // Testar listar usuarios
    $ch = curl_init("$baseUrl/admin/usuarios/list");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookieFile,
    ]);
    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $usrRes = json_decode($resp, true);
    curl_close($ch);
    teste("Super admin: GET /admin/usuarios/list = 200", $httpCode === 200);
    teste("Super admin: usuarios retornados com empresas", isset($usrRes['dados']));

    // Testar empresas
    $ch = curl_init("$baseUrl/admin/empresas/list");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookieFile,
    ]);
    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $empRes = json_decode($resp, true);
    curl_close($ch);
    teste("Super admin: GET /admin/empresas/list = 200", $httpCode === 200);
    teste("Super admin: empresas retornadas", isset($empRes['dados']) || isset($empRes['sucesso']));
    if (!isset($empRes['dados']) && !isset($empRes['sucesso'])) {
        echo "  [DEBUG] empresas resp: " . substr($resp, 0, 200) . "\n";
    }

    // Testar projetos
    $ch = curl_init("$baseUrl/admin/projetos/list");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookieFile,
    ]);
    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $projRes = json_decode($resp, true);
    curl_close($ch);
    teste("Super admin: GET /admin/projetos/list = 200", $httpCode === 200);
    teste("Super admin: projetos retornados", isset($projRes['dados']) || isset($projRes['sucesso']));
    if (!isset($projRes['dados']) && !isset($projRes['sucesso'])) {
        echo "  [DEBUG] projetos resp: " . substr($resp, 0, 200) . "\n";
    }

    // Testar historico
    $ch = curl_init("$baseUrl/api/historico");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookieFile,
    ]);
    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $histRes = json_decode($resp, true);
    curl_close($ch);
    teste("Super admin: GET /api/historico = 200", $httpCode === 200);
    teste("Super admin: historico retornado", isset($histRes['dados']));

    // Testar scheduler
    $ch = curl_init("$baseUrl/api/scheduler/rotinas");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookieFile,
    ]);
    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $schedRes = json_decode($resp, true);
    curl_close($ch);
    teste("Super admin: GET /api/scheduler/rotinas = 200", $httpCode === 200);
    teste("Super admin: scheduler retornado", isset($schedRes['dados']) || isset($schedRes['sucesso']));
    if (!isset($schedRes['dados']) && !isset($schedRes['sucesso'])) {
        echo "  [DEBUG] scheduler resp: " . substr($resp, 0, 200) . "\n";
    }

    // Testar calendario
    $ch = curl_init("$baseUrl/api/calendario/eventos?inicio=2025-01-01&fim=2025-12-31");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookieFile,
    ]);
    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $calRes = json_decode($resp, true);
    curl_close($ch);
    teste("Super admin: GET /api/calendario/eventos = 200", $httpCode === 200);
    teste("Super admin: calendario retornado", isset($calRes['sucesso']));
    if (!isset($calRes['sucesso'])) {
        echo "  [DEBUG] calendario resp: " . substr($resp, 0, 200) . "\n";
    }

    // Testar papeis disponiveis
    $ch = curl_init("$baseUrl/api/permissoes/papeis-disponiveis");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookieFile,
    ]);
    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $papeis = json_decode($resp, true);
    curl_close($ch);
    teste("Super admin: papeis disponiveis", $httpCode === 200 && !empty($papeis['dados']));
    echo "  [INFO] Papeis: " . implode(', ', $papeis['dados'] ?? []) . "\n";
}

@unlink($cookieFile);

// ==============================
// Resultado Final
// ==============================
echo "\n========================================\n";
echo "  RESULTADO: $total testes, " . ($total - $erros) . " OK, $erros falhas\n";
echo "========================================\n";
exit($erros > 0 ? 1 : 0);
