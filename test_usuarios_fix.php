<?php
/**
 * Teste: Fix do loop de associação de usuários a empresas/projetos
 * Verifica que CSRF token funciona corretamente na tela de usuarios
 */

$baseUrl = 'http://localhost/DMC-DATALOAD/public';
$ok = 0; $fail = 0; $total = 0;

function test($nome, $condicao) {
    global $ok, $fail, $total;
    $total++;
    if ($condicao) { $ok++; echo "  ✓ $nome\n"; }
    else { $fail++; echo "  ✗ FALHOU: $nome\n"; }
}

// Login
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "$baseUrl/login",
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query(['usuario' => 'admin', 'senha' => 'Admin@2026']),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_COOKIEJAR => __DIR__ . '/test_cookies_usr.txt',
    CURLOPT_COOKIEFILE => __DIR__ . '/test_cookies_usr.txt'
]);
$loginResp = curl_exec($ch);
$loginCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "\n=== Login ===\n";
test("Login retorna redirect (302)", $loginCode === 302);

// Extrair CSRF token da página de usuarios
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "$baseUrl/admin/usuarios",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEFILE => __DIR__ . '/test_cookies_usr.txt',
    CURLOPT_COOKIEJAR => __DIR__ . '/test_cookies_usr.txt'
]);
$pagina = curl_exec($ch);
$pageCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "\n=== Página de Usuários ===\n";
test("Página carrega (200)", $pageCode === 200);
test("Página contém título Usuários", strpos($pagina, 'Usu') !== false);

// Extrair csrfToken do JS
$csrfToken = null;
if (preg_match("/const csrfToken = ['\"]([^'\"]+)['\"]/", $pagina, $m)) {
    $csrfToken = $m[1];
}
// Tentar outro formato
if (!$csrfToken && preg_match('/csrfToken\s*=\s*"([^"]+)"/', $pagina, $m)) {
    $csrfToken = $m[1];
}
test("CSRF token encontrado na página", !empty($csrfToken));
if ($csrfToken) {
    echo "    Token: " . substr($csrfToken, 0, 20) . "...\n";
}

// Testar API de listagem
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "$baseUrl/admin/usuarios/list",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEFILE => __DIR__ . '/test_cookies_usr.txt',
    CURLOPT_COOKIEJAR => __DIR__ . '/test_cookies_usr.txt'
]);
$listResp = curl_exec($ch);
$listCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
$listData = json_decode($listResp, true);

echo "\n=== API Listagem Usuários ===\n";
test("Listagem retorna 200", $listCode === 200);
test("Listagem tem sucesso=true", ($listData['sucesso'] ?? false) === true);
test("Listagem tem dados", is_array($listData['dados'] ?? null));
$qtdUsers = count($listData['dados'] ?? []);
echo "    Usuários encontrados: $qtdUsers\n";

// Testar API de empresas disponíveis
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "$baseUrl/api/permissoes/empresas-usuario",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEFILE => __DIR__ . '/test_cookies_usr.txt',
    CURLOPT_COOKIEJAR => __DIR__ . '/test_cookies_usr.txt'
]);
$empResp = curl_exec($ch);
$empCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
$empData = json_decode($empResp, true);

echo "\n=== API Empresas Disponíveis ===\n";
test("Empresas retorna 200", $empCode === 200);
test("Empresas tem sucesso=true", ($empData['sucesso'] ?? false) === true);
$empresas = $empData['dados'] ?? [];
echo "    Empresas: " . count($empresas) . "\n";

// Testar API de projetos
if (!empty($empresas)) {
    $empIds = implode(',', array_column($empresas, 'id'));
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "$baseUrl/api/permissoes/projetos-usuario?empresas=$empIds",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => __DIR__ . '/test_cookies_usr.txt',
        CURLOPT_COOKIEJAR => __DIR__ . '/test_cookies_usr.txt'
    ]);
    $projResp = curl_exec($ch);
    $projCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $projData = json_decode($projResp, true);
    
    echo "\n=== API Projetos Disponíveis ===\n";
    test("Projetos retorna 200", $projCode === 200);
    test("Projetos tem sucesso=true", ($projData['sucesso'] ?? false) === true);
    $projetos = $projData['dados'] ?? [];
    echo "    Projetos: " . count($projetos) . "\n";
}

// Testar criação de usuário com empresas e projetos (SEM CSRF - deve falhar)
echo "\n=== Teste CSRF: Salvar SEM token (deve falhar 403) ===\n";
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "$baseUrl/admin/usuarios/salvar",
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'nome_usuario' => 'test_csrf_fail',
        'senha' => 'Test@123',
        'nivel_acesso' => 'operador'
    ]),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEFILE => __DIR__ . '/test_cookies_usr.txt',
    CURLOPT_COOKIEJAR => __DIR__ . '/test_cookies_usr.txt'
]);
$csrfFailResp = curl_exec($ch);
$csrfFailCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
test("Salvar sem CSRF retorna 403", $csrfFailCode === 403);

// Testar criação de usuário com empresas e projetos (COM CSRF)
echo "\n=== Criar Usuário com Empresas/Projetos ===\n";
$nomeTest = 'test_assoc_' . time();
$postData = [
    'nome_usuario' => $nomeTest,
    'senha' => 'Test@123456',
    'nivel_acesso' => 'operador',
    '_csrf_token' => $csrfToken
];
// Associar primeira empresa se existir
if (!empty($empresas)) {
    $postData['empresas'] = [$empresas[0]['id']];
    echo "    Associando empresa: {$empresas[0]['nome']} (ID: {$empresas[0]['id']})\n";
}
if (!empty($projetos)) {
    $postData['projetos'] = [$projetos[0]['id']];
    echo "    Associando projeto: {$projetos[0]['nome']} (ID: {$projetos[0]['id']})\n";
}

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "$baseUrl/admin/usuarios/salvar",
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($postData),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEFILE => __DIR__ . '/test_cookies_usr.txt',
    CURLOPT_COOKIEJAR => __DIR__ . '/test_cookies_usr.txt'
]);
$saveResp = curl_exec($ch);
$saveCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
$saveData = json_decode($saveResp, true);

test("Salvar retorna 200", $saveCode === 200);
test("Salvar tem sucesso=true", ($saveData['sucesso'] ?? false) === true);
$novoUserId = $saveData['id'] ?? null;
test("Retorna ID do novo usuário", !empty($novoUserId));
echo "    Novo usuário ID: $novoUserId\n";

// Verificar que o usuário foi criado com as associações corretas
if ($novoUserId) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "$baseUrl/admin/usuarios/get/$novoUserId",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => __DIR__ . '/test_cookies_usr.txt',
        CURLOPT_COOKIEJAR => __DIR__ . '/test_cookies_usr.txt'
    ]);
    $getResp = curl_exec($ch);
    $getCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $userData = json_decode($getResp, true);
    
    echo "\n=== Verificar Associações ===\n";
    test("GET usuario retorna 200", $getCode === 200);
    test("Nome correto", ($userData['nome_usuario'] ?? '') === $nomeTest);
    test("Nível correto (operador)", ($userData['nivel_acesso'] ?? '') === 'operador');
    
    $userEmpresas = $userData['empresas'] ?? [];
    $userProjetos = $userData['projetos'] ?? [];
    
    if (!empty($empresas)) {
        test("Tem empresas associadas", count($userEmpresas) > 0);
        test("Empresa correta associada", in_array($empresas[0]['id'], array_column($userEmpresas, 'id')));
    }
    if (!empty($projetos)) {
        test("Tem projetos associados", count($userProjetos) > 0);
        test("Projeto correto associado", in_array($projetos[0]['id'], array_column($userProjetos, 'id')));
    }
    
    // Editar: trocar empresas e projetos
    echo "\n=== Editar Associações ===\n";
    $editData = [
        'id' => $novoUserId,
        'nome_usuario' => $nomeTest,
        'nivel_acesso' => 'operador',
        '_csrf_token' => $csrfToken
    ];
    // Se tiver mais de uma empresa, trocar para a segunda
    if (count($empresas) > 1) {
        $editData['empresas'] = [$empresas[1]['id']];
        echo "    Trocando para empresa: {$empresas[1]['nome']} (ID: {$empresas[1]['id']})\n";
    }
    // Se tiver mais de um projeto, trocar para o segundo
    if (count($projetos) > 1) {
        $editData['projetos'] = [$projetos[1]['id']];
        echo "    Trocando para projeto: {$projetos[1]['nome']} (ID: {$projetos[1]['id']})\n";
    }
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "$baseUrl/admin/usuarios/salvar",
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($editData),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => __DIR__ . '/test_cookies_usr.txt',
        CURLOPT_COOKIEJAR => __DIR__ . '/test_cookies_usr.txt'
    ]);
    $editResp = curl_exec($ch);
    $editCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $editResult = json_decode($editResp, true);
    
    test("Edição retorna 200", $editCode === 200);
    test("Edição tem sucesso=true", ($editResult['sucesso'] ?? false) === true);
    
    // Verificar associações após edição
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "$baseUrl/admin/usuarios/get/$novoUserId",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => __DIR__ . '/test_cookies_usr.txt',
        CURLOPT_COOKIEJAR => __DIR__ . '/test_cookies_usr.txt'
    ]);
    $getResp2 = curl_exec($ch);
    curl_close($ch);
    $userData2 = json_decode($getResp2, true);
    
    if (count($empresas) > 1) {
        $emps2 = array_column($userData2['empresas'] ?? [], 'id');
        test("Nova empresa associada após edição", in_array($empresas[1]['id'], $emps2));
        test("Empresa anterior removida", !in_array($empresas[0]['id'], $emps2));
    }

    // Testar reset de senha com CSRF
    echo "\n=== Reset Senha com CSRF ===\n";
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "$baseUrl/admin/usuarios/reset-senha",
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'id' => $novoUserId,
            'senha' => 'NovaSenha@2026',
            '_csrf_token' => $csrfToken
        ]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => __DIR__ . '/test_cookies_usr.txt',
        CURLOPT_COOKIEJAR => __DIR__ . '/test_cookies_usr.txt'
    ]);
    $resetResp = curl_exec($ch);
    $resetCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $resetData = json_decode($resetResp, true);
    test("Reset senha retorna 200", $resetCode === 200);
    test("Reset senha sucesso", ($resetData['sucesso'] ?? false) === true);
    
    // Reset sem CSRF deve falhar
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "$baseUrl/admin/usuarios/reset-senha",
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'id' => $novoUserId,
            'senha' => 'Fail@2026'
        ]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => __DIR__ . '/test_cookies_usr.txt',
        CURLOPT_COOKIEJAR => __DIR__ . '/test_cookies_usr.txt'
    ]);
    $resetFailResp = curl_exec($ch);
    $resetFailCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    test("Reset sem CSRF retorna 403", $resetFailCode === 403);

    // Excluir o usuário de teste (limpar)
    echo "\n=== Excluir Usuário de Teste ===\n";
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "$baseUrl/admin/usuarios/delete/$novoUserId",
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query(['_csrf_token' => $csrfToken]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => __DIR__ . '/test_cookies_usr.txt',
        CURLOPT_COOKIEJAR => __DIR__ . '/test_cookies_usr.txt'
    ]);
    $delResp = curl_exec($ch);
    $delCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $delData = json_decode($delResp, true);
    test("Exclusão retorna 200", $delCode === 200);
    test("Exclusão sucesso", ($delData['sucesso'] ?? false) === true);
    
    // Delete sem CSRF deve falhar
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "$baseUrl/admin/usuarios/delete/99999",
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => '',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => __DIR__ . '/test_cookies_usr.txt',
        CURLOPT_COOKIEJAR => __DIR__ . '/test_cookies_usr.txt'
    ]);
    $delFailResp = curl_exec($ch);
    $delFailCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    test("Delete sem CSRF retorna 403", $delFailCode === 403);
}

// Limpar cookies de teste
@unlink(__DIR__ . '/test_cookies_usr.txt');

echo "\n=== RESULTADO ===\n";
echo "Total: $total | OK: $ok | Falhas: $fail\n";
exit($fail > 0 ? 1 : 0);
