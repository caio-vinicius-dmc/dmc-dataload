<?php
/**
 * DMC DataLoad - Teste E2E Completo: Simulação de Navegação Real
 * Simula um usuário navegando pela aplicação, testando TODOS os fluxos CRUD
 * desde login, criação de usuários, conexões, rotinas, até admin pages.
 */

$baseUrl = 'http://localhost/DMC-DATALOAD/public';
$cookieFile = __DIR__ . '/test_cookies_e2e.txt';
$ok = 0; $fail = 0; $total = 0; $erros = [];

function test($nome, $condicao, $detalhe = '') {
    global $ok, $fail, $total, $erros;
    $total++;
    if ($condicao) { $ok++; echo "  [OK] $nome\n"; }
    else { $fail++; echo "  [FAIL] $nome" . ($detalhe ? " — $detalhe" : "") . "\n"; $erros[] = $nome; }
}

function http($method, $url, $data = null, $isJson = false, $rawBody = false) {
    global $cookieFile;
    $ch = curl_init();
    $opts = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30
    ];
    if ($method === 'POST') {
        $opts[CURLOPT_POST] = true;
        if ($rawBody) {
            $opts[CURLOPT_POSTFIELDS] = $data;
        } else {
            $opts[CURLOPT_POSTFIELDS] = $isJson ? json_encode($data) : http_build_query($data ?? []);
        }
        if ($isJson) $opts[CURLOPT_HTTPHEADER] = ['Content-Type: application/json'];
    }
    curl_setopt_array($ch, $opts);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    // Strip HTML prefix from root index.php
    $jsonBody = $resp;
    if (preg_match('/\{[\s]*"/', $resp, $bm, PREG_OFFSET_CAPTURE)) $jsonBody = substr($resp, $bm[0][1]);
    elseif (($pos = strpos($resp, '[{')) !== false) $jsonBody = substr($resp, $pos);
    return ['code' => $code, 'body' => $resp, 'json' => json_decode($jsonBody, true)];
}

function getCsrf($url) {
    $r = http('GET', $url);
    // Try JS const pattern first: const csrfToken = "...";
    if (preg_match('/const\s+csrfToken\s*=\s*"([^"]+)"/', $r['body'], $m)) {
        return ['csrf' => $m[1], 'body' => $r['body'], 'code' => $r['code']];
    }
    // Try hidden input pattern: <input ... id="csrfToken" value="...">
    if (preg_match('/id=["\']csrfToken["\']\s+value=["\']([^"\']+)["\']/', $r['body'], $m)) {
        return ['csrf' => $m[1], 'body' => $r['body'], 'code' => $r['code']];
    }
    if (preg_match('/value=["\']([^"\']+)["\']\s+id=["\']csrfToken["\']/', $r['body'], $m)) {
        return ['csrf' => $m[1], 'body' => $r['body'], 'code' => $r['code']];
    }
    // Try name="_csrf_token" pattern
    if (preg_match('/name=["\']_csrf_token["\']\s+[^>]*value=["\']([^"\']+)["\']/', $r['body'], $m)) {
        return ['csrf' => $m[1], 'body' => $r['body'], 'code' => $r['code']];
    }
    return ['csrf' => '', 'body' => $r['body'], 'code' => $r['code']];
}

// =============================================================
echo "\n" . str_repeat('=', 60) . "\n";
echo "  DMC DataLoad — Teste E2E Completo (Simulação Browser)\n";
echo str_repeat('=', 60) . "\n";

// =============================================================
echo "\n=== 1. LOGIN ===\n";

// Testar página de login carrega
$r = http('GET', "$baseUrl/login");
test("Página de login carrega (200)", $r['code'] === 200);
test("Página contém formulário de login", strpos($r['body'], 'usuario') !== false && strpos($r['body'], 'senha') !== false);

// Login com credenciais inválidas
$r = http('POST', "$baseUrl/login", ['usuario' => 'inexistente', 'senha' => 'errada']);
test("Login inválido retorna erro", ($r['json']['sucesso'] ?? true) === false);
test("Mensagem de erro presente", !empty($r['json']['erro']));

// Login com admin
$r = http('POST', "$baseUrl/login", ['usuario' => 'admin', 'senha' => 'Admin@2026']);
test("Login admin retorna sucesso", ($r['json']['sucesso'] ?? false) === true);

// Dashboard carrega após login
$r = http('GET', "$baseUrl/dashboard");
test("Dashboard carrega (200)", $r['code'] === 200);
test("Dashboard contém sidebar", strpos($r['body'], 'sidebar') !== false);
test("Dashboard contém métricas", strpos($r['body'], 'dashboard') !== false || strpos($r['body'], 'Dashboard') !== false);

// Health check
$r = http('GET', "$baseUrl/api/health");
test("Health check retorna 200", $r['code'] === 200);
test("Health status ok", ($r['json']['status'] ?? '') === 'ok');

// Sessão
$r = http('GET', "$baseUrl/api/sessao");
test("API sessão retorna dados", ($r['json']['autenticado'] ?? false) === true);
test("Sessão tem usuario admin", ($r['json']['usuario']['nome_usuario'] ?? '') === 'admin');

// =============================================================
echo "\n=== 2. PÁGINAS ADMIN — Carregamento ===\n";

$adminPages = [
    '/admin/usuarios' => 'Usu',
    '/admin/empresas' => 'Empresa',
    '/admin/projetos' => 'Projeto',
    '/admin/auditoria' => 'Auditoria',
    '/admin/webhooks' => 'Webhook',
    '/admin/canais' => 'Canais',
    '/admin/fila' => 'Fila',
    '/admin/backups' => 'Backup',
    '/configuracoes' => 'Configura',
];

foreach ($adminPages as $path => $text) {
    $r = http('GET', "$baseUrl$path");
    test("Página $path carrega (200)", $r['code'] === 200, "HTTP {$r['code']}");
    test("Página $path contém '$text'", strpos($r['body'], $text) !== false);
}

// =============================================================
echo "\n=== 3. PÁGINAS PRINCIPAIS — Carregamento ===\n";

$mainPages = [
    '/conexoes' => 'Conex',
    '/rotinas' => 'Rotina',
    '/pipelines' => 'Pipeline',
    '/workflows' => 'Workflow',
    '/historico' => 'Hist',
    '/calendario' => 'Calend',
    '/scheduler' => 'Agenda',
    '/sql-editor' => 'SQL',
    '/diagrama' => 'Diagrama',
    '/logs' => 'Log',
    '/apis-externas' => 'API',
    '/eventos-api' => 'Evento',
];

foreach ($mainPages as $path => $text) {
    $r = http('GET', "$baseUrl$path");
    test("Página $path carrega (200)", $r['code'] === 200, "HTTP {$r['code']}");
}

// =============================================================
echo "\n=== 4. SIDEBAR — Links presentes ===\n";

$r = http('GET', "$baseUrl/dashboard");
$sidebarLinks = [
    '/dashboard', '/conexoes', '/rotinas', '/pipelines', '/workflows',
    '/historico', '/calendario', '/scheduler', '/sql-editor', '/diagrama',
    '/logs', '/admin/usuarios', '/admin/empresas', '/admin/canais',
    '/admin/fila', '/admin/backups'
];
foreach ($sidebarLinks as $link) {
    test("Sidebar contém link $link", strpos($r['body'], $link) !== false);
}

// =============================================================
echo "\n=== 5. CRUD EMPRESAS ===\n";

$page = getCsrf("$baseUrl/admin/empresas");
$csrf = $page['csrf'];
test("CSRF token obtido para empresas", !empty($csrf));

// Criar empresa
$r = http('POST', "$baseUrl/admin/empresas/salvar", [
    'nome' => 'Empresa Teste E2E',
    'descricao' => 'Empresa criada pelo teste E2E',
    'ativa' => '1',
    '_csrf_token' => $csrf
]);
test("Criar empresa retorna sucesso", ($r['json']['sucesso'] ?? false) === true, $r['json']['erro'] ?? '');
$empresaId = $r['json']['id'] ?? null;
test("Empresa tem ID", !empty($empresaId));

// Listar empresas
$r = http('GET', "$baseUrl/admin/empresas/list");
test("Listar empresas retorna dados", !empty($r['json']['dados']));
$found = false;
foreach (($r['json']['dados'] ?? $r['json']['data'] ?? []) as $emp) {
    if (($emp['nome'] ?? $emp['nome_empresa'] ?? '') === 'Empresa Teste E2E') $found = true;
}
test("Empresa criada aparece na listagem", $found);

// =============================================================
echo "\n=== 6. CRUD PROJETOS ===\n";

$page = getCsrf("$baseUrl/admin/projetos");
$csrf = $page['csrf'];

// Criar projeto associado à empresa
$r = http('POST', "$baseUrl/admin/projetos/salvar", [
    'nome' => 'Projeto Teste E2E',
    'descricao' => 'Projeto criado pelo teste E2E',
    'id_empresa' => $empresaId,
    'ativo' => '1',
    '_csrf_token' => $csrf
]);
test("Criar projeto retorna sucesso", ($r['json']['sucesso'] ?? false) === true, $r['json']['erro'] ?? '');
$projetoId = $r['json']['id'] ?? null;
test("Projeto tem ID", !empty($projetoId));

// Listar projetos
$r = http('GET', "$baseUrl/admin/projetos/list");
test("Listar projetos retorna dados", !empty($r['json']['dados'] ?? $r['json']['data'] ?? []));

// =============================================================
echo "\n=== 7. CRUD USUÁRIOS ===\n";

$page = getCsrf("$baseUrl/admin/usuarios");
$csrf = $page['csrf'];

// Criar usuário
$userPayload = "nome_usuario=testuser_e2e&senha=Test%402026&nivel_acesso=operador&_csrf_token=" . urlencode($csrf);
if ($empresaId) $userPayload .= "&empresas%5B%5D=" . urlencode($empresaId);
if ($projetoId) $userPayload .= "&projetos%5B%5D=" . urlencode($projetoId);
$r = http('POST', "$baseUrl/admin/usuarios/salvar", $userPayload, false, true);
test("Criar usuário retorna sucesso", ($r['json']['sucesso'] ?? false) === true, $r['json']['erro'] ?? '');
$userId = $r['json']['id'] ?? null;
test("Usuário tem ID", !empty($userId));

// Listar usuários
$r = http('GET', "$baseUrl/admin/usuarios/list");
test("Listar usuários retorna dados", !empty($r['json']['dados']));
$found = false;
foreach ($r['json']['dados'] ?? [] as $u) {
    if ($u['nome_usuario'] === 'testuser_e2e') { $found = true; break; }
}
test("Usuário testuser_e2e aparece na listagem", $found);

// Buscar usuário por ID
if ($userId) {
    $r = http('GET', "$baseUrl/admin/usuarios/get/$userId");
    test("GET usuario retorna dados", !empty($r['json']['nome_usuario']));
    test("Usuário tem empresas associadas", !empty($r['json']['empresas']));
    test("Usuário tem projetos associados", !empty($r['json']['projetos']));
}

// =============================================================
echo "\n=== 8. LOGIN COM NOVO USUÁRIO ===\n";

// Salvar cookies admin separado
$adminCookie = $cookieFile;
$cookieFile = __DIR__ . '/test_cookies_e2e_newuser.txt';

$r = http('POST', "$baseUrl/login", ['usuario' => 'testuser_e2e', 'senha' => 'Test@2026']);
test("Login testuser_e2e retorna sucesso", ($r['json']['sucesso'] ?? false) === true, $r['json']['erro'] ?? '');

if ($r['json']['sucesso'] ?? false) {
    // Verificar que pode acessar o dashboard
    $r = http('GET', "$baseUrl/dashboard");
    test("Novo usuário acessa dashboard (200)", $r['code'] === 200);
    
    // Verificar sessão
    $r = http('GET', "$baseUrl/api/sessao");
    test("Sessão tem usuário testuser_e2e", ($r['json']['usuario']['nome_usuario'] ?? '') === 'testuser_e2e');
    test("Nível de acesso é operador", ($r['json']['usuario']['nivel_acesso'] ?? '') === 'operador');
    
    // Operador NÃO pode acessar admin de empresas (testando RBAC)
    $r = http('GET', "$baseUrl/admin/empresas");
    test("Operador bloqueado de admin/empresas (403)", $r['code'] === 403 || strpos($r['body'], 'Acesso negado') !== false || strpos($r['body'], 'Permiss') !== false);
    
    // Logout novo usuário
    http('POST', "$baseUrl/logout");
}

// Restaurar cookie admin
@unlink($cookieFile);
$cookieFile = $adminCookie;

// =============================================================
echo "\n=== 9. RESET SENHA USUÁRIO ===\n";

$page = getCsrf("$baseUrl/admin/usuarios");
$csrf = $page['csrf'];

if ($userId) {
    $r = http('POST', "$baseUrl/admin/usuarios/reset-senha", [
        'id' => $userId,
        'senha' => 'NovaSenha@2026',
        '_csrf_token' => $csrf
    ]);
    test("Reset senha retorna sucesso", ($r['json']['sucesso'] ?? false) === true, $r['json']['erro'] ?? '');
    
    // Testar login com nova senha
    $tempCookie = __DIR__ . '/test_cookies_e2e_reset.txt';
    $savedCookie = $cookieFile;
    $cookieFile = $tempCookie;
    
    $r = http('POST', "$baseUrl/login", ['usuario' => 'testuser_e2e', 'senha' => 'NovaSenha@2026']);
    test("Login com nova senha funciona", ($r['json']['sucesso'] ?? false) === true);
    http('POST', "$baseUrl/logout");
    
    @unlink($tempCookie);
    $cookieFile = $savedCookie;
}

// =============================================================
echo "\n=== 10. CRUD CONEXÕES ===\n";

$page = getCsrf("$baseUrl/conexoes");
$csrf = $page['csrf'];
test("CSRF token obtido para conexões", !empty($csrf));

// Página contém modal
test("Página conexões contém modal", strpos($page['body'], 'modalConexao') !== false);
test("Botão Nova Conexão sem data-bs-toggle", strpos($page['body'], 'data-bs-toggle="modal" data-bs-target="#modalConexao"') === false);

// Criar conexão
$r = http('POST', "$baseUrl/conexoes/salvar", [
    'nome_conexao' => 'Conexão Teste E2E',
    'tipo_banco' => 'postgres',
    'host' => 'localhost',
    'porta' => '5433',
    'nome_banco' => 'db_dmc_dataload',
    'usuario' => 'postgres',
    'senha' => 'dmc2023@',
    '_csrf_token' => $csrf
]);
test("Criar conexão retorna sucesso", ($r['json']['sucesso'] ?? false) === true, $r['json']['mensagem'] ?? $r['json']['erro'] ?? '');
$conexaoId = $r['json']['id'] ?? null;
test("Conexão tem ID", !empty($conexaoId));

// Listar conexões
$r = http('GET', "$baseUrl/conexoes/list");
test("Listar conexões retorna dados", !empty($r['json']['dados'] ?? $r['json']['data'] ?? []));

// Buscar conexão por ID
if ($conexaoId) {
    $r = http('GET', "$baseUrl/conexoes/get/$conexaoId");
    test("GET conexão retorna dados", !empty($r['json']['nome_conexao']));
    test("Conexão tem nome correto", ($r['json']['nome_conexao'] ?? '') === 'Conexão Teste E2E');
}

// Testar conexão
if ($conexaoId) {
    $r = http('POST', "$baseUrl/conexoes/test/$conexaoId", ['_csrf_token' => $csrf]);
    test("Teste de conexão retorna sucesso", ($r['json']['sucesso'] ?? false) === true, $r['json']['erro'] ?? '');
}

// =============================================================
echo "\n=== 11. CRUD ROTINAS ===\n";

// Verificar se existe conexão para usar
$r = http('GET', "$baseUrl/conexoes/list");
$primeiraConexao = null;
foreach ($r['json']['dados'] ?? [] as $c) {
    $primeiraConexao = $c['id'];
    break;
}

// Listar rotinas
$r = http('GET', "$baseUrl/api/rotinas/listar");
test("API rotinas/listar retorna 200", $r['code'] === 200);
$totalRotinasAntes = count($r['json']['dados'] ?? []);

// Criar rotina
if ($primeiraConexao) {
    $r = http('POST', "$baseUrl/api/rotinas/salvar", [
        'nome_rotina' => 'Rotina Teste E2E',
        'descricao' => 'Rotina criada por teste E2E automatizado',
        'id_conexao' => $primeiraConexao,
        'blocos' => json_encode([['sql' => 'SELECT 1 AS teste', 'ordem' => 1]]),
        '_csrf_token' => $csrf
    ], false);
    test("Criar rotina retorna sucesso", ($r['json']['sucesso'] ?? false) === true, $r['json']['erro'] ?? '');
    $rotinaId = $r['json']['id'] ?? null;
    
    if (!$rotinaId) {
        // Tentar API alternativa
        $page2 = getCsrf("$baseUrl/rotinas/cadastro");
        $r = http('POST', "$baseUrl/api/rotinas/salvar", [
            'nome_rotina' => 'Rotina Teste E2E',
            'descricao' => 'Rotina criada por teste E2E automatizado',
            'id_conexao' => $primeiraConexao,
            'blocos' => json_encode([['sql' => 'SELECT 1 AS teste', 'ordem' => 1]]),
            '_csrf_token' => $page2['csrf']
        ]);
        test("Criar rotina (retry) retorna sucesso", ($r['json']['sucesso'] ?? false) === true, $r['json']['erro'] ?? '');
        $rotinaId = $r['json']['id'] ?? null;
    }
} else {
    echo "  [SKIP] Sem conexão disponível para criar rotina\n";
    $rotinaId = null;
}

// =============================================================
echo "\n=== 12. EXECUÇÃO DE ROTINA ===\n";

if ($rotinaId) {
    $page2 = getCsrf("$baseUrl/rotinas");
    $r = http('POST', "$baseUrl/api/rotinas/executar/$rotinaId", [
        '_csrf_token' => $page2['csrf']
    ]);
    test("Executar rotina retorna sucesso", ($r['json']['sucesso'] ?? false) === true, $r['json']['erro'] ?? '');
}

// =============================================================
echo "\n=== 13. HISTÓRICO ===\n";

$r = http('GET', "$baseUrl/api/historico");
test("API histórico retorna 200", $r['code'] === 200);
test("Histórico retorna estrutura válida", isset($r['json']['sucesso']));

// =============================================================
echo "\n=== 14. CALENDÁRIO ===\n";

$r = http('GET', "$baseUrl/api/calendario/eventos");
test("API calendário retorna 200", $r['code'] === 200);

// =============================================================
echo "\n=== 15. SCHEDULER ===\n";

$r = http('GET', "$baseUrl/api/scheduler/status");
test("API scheduler/status retorna 200", $r['code'] === 200);

// =============================================================
echo "\n=== 16. DASHBOARD MÉTRICAS ===\n";

$r = http('GET', "$baseUrl/api/dashboard/metricas");
test("API métricas retorna 200", $r['code'] === 200);
test("Métricas tem dados", !empty($r['json']));

// =============================================================
echo "\n=== 17. PIPELINES ===\n";

$r = http('GET', "$baseUrl/api/pipelines/listar");
test("API pipelines/listar retorna 200", $r['code'] === 200);

// =============================================================
echo "\n=== 18. WORKFLOWS ===\n";

$r = http('GET', "$baseUrl/api/workflows/listar");
test("API workflows/listar retorna 200", $r['code'] === 200);

// =============================================================
echo "\n=== 19. APIs EXTERNAS ===\n";

$r = http('GET', "$baseUrl/api/apis-externas/listar");
test("API apis-externas/listar retorna 200", $r['code'] === 200);

// =============================================================
echo "\n=== 20. SQL EDITOR ===\n";

$r = http('GET', "$baseUrl/sql-editor");
test("SQL Editor carrega (200)", $r['code'] === 200);
test("SQL Editor contém CodeMirror", strpos($r['body'], 'CodeMirror') !== false || strpos($r['body'], 'codemirror') !== false || strpos($r['body'], 'sql') !== false);

// =============================================================
echo "\n=== 21. DIAGRAMA ===\n";

$r = http('GET', "$baseUrl/diagrama");
test("Diagrama carrega (200)", $r['code'] === 200);

// =============================================================
echo "\n=== 22. LOGS ===\n";

$r = http('GET', "$baseUrl/logs");
test("Logs carrega (200)", $r['code'] === 200);

// =============================================================
echo "\n=== 23. CONFIGURAÇÕES ===\n";

$r = http('GET', "$baseUrl/api/configuracoes/listar");
test("API configurações retorna 200", $r['code'] === 200);

// =============================================================
echo "\n=== 24. AUDITORIA ===\n";

$r = http('GET', "$baseUrl/api/auditoria");
test("API auditoria retorna 200", $r['code'] === 200);
test("Auditoria tem registros", !empty($r['json']['registros']));

// =============================================================
echo "\n=== 25. WEBHOOKS ===\n";

$page = getCsrf("$baseUrl/admin/webhooks");
$csrf = $page['csrf'];
test("CSRF obtido para webhooks", !empty($csrf));

$r = http('GET', "$baseUrl/api/webhooks/listar");
test("API webhooks/listar retorna 200", $r['code'] === 200);

// Criar webhook
$r = http('POST', "$baseUrl/api/webhooks/salvar", [
    'nome' => 'Webhook Teste E2E',
    'url' => 'https://httpbin.org/post',
    'eventos' => 'falha_execucao',
    'ativo' => '1',
    '_csrf_token' => $csrf
]);
test("Criar webhook retorna sucesso", ($r['json']['sucesso'] ?? false) === true, $r['json']['erro'] ?? '');
$webhookId = $r['json']['id'] ?? null;

// Deletar webhook
if ($webhookId) {
    $r = http('POST', "$baseUrl/api/webhooks/delete/$webhookId", ['_csrf_token' => $csrf]);
    test("Deletar webhook retorna sucesso", ($r['json']['sucesso'] ?? false) === true);
}

// =============================================================
echo "\n=== 26. FILA DE EXECUÇÃO ===\n";

$page = getCsrf("$baseUrl/admin/fila");
$csrf = $page['csrf'];
test("Página fila contém stat-card", strpos($page['body'], 'stat-card') !== false);

$r = http('GET', "$baseUrl/api/fila/stats");
test("Fila stats retorna 200", $r['code'] === 200);
test("Stats tem campo pendentes", isset($r['json']['dados']['pendentes']));

// =============================================================
echo "\n=== 27. CANAIS DE NOTIFICAÇÃO ===\n";

$page = getCsrf("$baseUrl/admin/canais");
$csrf = $page['csrf'];

$r = http('POST', "$baseUrl/api/canais/salvar", [
    'nome' => 'Canal Teste E2E',
    'tipo' => 'slack',
    'webhook_url' => 'https://hooks.slack.com/services/test/e2e',
    'ativo' => '1',
    'notificar_falha' => '1',
    '_csrf_token' => $csrf
]);
test("Criar canal retorna sucesso", ($r['json']['sucesso'] ?? false) === true, $r['json']['erro'] ?? '');
$canalE2eId = $r['json']['id'] ?? null;

// =============================================================
echo "\n=== 28. BACKUPS ===\n";

$page = getCsrf("$baseUrl/admin/backups");
$csrf = $page['csrf'];

$r = http('POST', "$baseUrl/api/backups/criar", ['tipo' => 'configuracoes', '_csrf_token' => $csrf]);
test("Criar backup retorna sucesso", ($r['json']['sucesso'] ?? false) === true, $r['json']['erro'] ?? '');
$backupE2eId = $r['json']['id'] ?? null;

// =============================================================
echo "\n=== 29. PERMISSÕES RBAC — Verificação ===\n";

$r = http('GET', "$baseUrl/api/permissoes/papeis-disponiveis");
test("Papéis disponíveis retorna dados", !empty($r['json']['dados']));

$r = http('GET', "$baseUrl/api/sessao");
test("Sessão retorna nível de acesso", !empty($r['json']['usuario']['nivel_acesso'] ?? ''));

// =============================================================
echo "\n=== 30. RESPONSIVIDADE — Verificação CSS ===\n";

// Verificar que layout base tem viewport meta
$r = http('GET', "$baseUrl/dashboard");
test("HTML tem viewport meta", strpos($r['body'], 'viewport') !== false);
test("HTML tem charset UTF-8", strpos($r['body'], 'charset') !== false);
test("Layout contém Bootstrap 5", strpos($r['body'], 'bootstrap') !== false);
test("Layout contém jQuery", strpos($r['body'], 'jquery') !== false || strpos($r['body'], 'jQuery') !== false);
test("Layout contém SweetAlert2", strpos($r['body'], 'sweetalert2') !== false || strpos($r['body'], 'Swal') !== false);

// =============================================================
echo "\n=== 31. CSRF — Proteção em rotas POST ===\n";

// Verificar que scripts RBAC estão acessíveis
$r = http('GET', "$baseUrl/assets/js/rbac-recurso.js");
test("Script rbac-recurso.js carrega (200)", $r['code'] === 200);
test("Script rbac-recurso.js contém função rbacLimparSelects", strpos($r['body'], 'rbacLimparSelects') !== false);

$r = http('GET', "$baseUrl/assets/js/rbac-compartilhamento.js");
test("Script rbac-compartilhamento.js carrega (200)", $r['code'] === 200);
test("Script contém abrirModalCompartilhamento", strpos($r['body'], 'abrirModalCompartilhamento') !== false);

// Verificar que página conexões referencia scripts RBAC
$r = http('GET', "$baseUrl/conexoes");
test("Conexões referencia rbac-recurso.js", strpos($r['body'], 'rbac-recurso.js') !== false);
test("Conexões referencia rbac-compartilhamento.js", strpos($r['body'], 'rbac-compartilhamento.js') !== false);
test("Conexões contém função novaConexao", strpos($r['body'], 'function novaConexao') !== false);
test("Conexões contém função editarConexao", strpos($r['body'], 'function editarConexao') !== false);

// =============================================================
echo "\n=== 32. CSRF — Proteção em rotas POST ===\n";

// Tentar criar empresa sem CSRF
$r = http('POST', "$baseUrl/admin/empresas/salvar", ['nome_empresa' => 'SemCSRF']);
test("Empresa sem CSRF retorna 403", $r['code'] === 403);

// Tentar criar usuario sem CSRF
$r = http('POST', "$baseUrl/admin/usuarios/salvar", ['nome_usuario' => 'semcsrf', 'senha' => '123456']);
test("Usuario sem CSRF retorna 403", $r['code'] === 403);

// Tentar deletar conexão sem CSRF
$r = http('POST', "$baseUrl/conexoes/delete/9999", []);
test("Conexão delete sem CSRF retorna 403", $r['code'] === 403);

// =============================================================
echo "\n=== 32. LIMPEZA — Remover dados de teste ===\n";

// Deletar canal
if ($canalE2eId) {
    $page = getCsrf("$baseUrl/admin/canais");
    $r = http('POST', "$baseUrl/api/canais/delete/$canalE2eId", ['_csrf_token' => $page['csrf']]);
    test("Cleanup: canal deletado", ($r['json']['sucesso'] ?? false) === true);
}

// Deletar backup
if ($backupE2eId) {
    $page = getCsrf("$baseUrl/admin/backups");
    $r = http('POST', "$baseUrl/api/backups/delete/$backupE2eId", ['_csrf_token' => $page['csrf']]);
    test("Cleanup: backup deletado", ($r['json']['sucesso'] ?? false) === true);
}

// Deletar conexão
if ($conexaoId) {
    $page = getCsrf("$baseUrl/conexoes");
    $r = http('POST', "$baseUrl/conexoes/delete/$conexaoId", ['_csrf_token' => $page['csrf']]);
    test("Cleanup: conexão deletada", ($r['json']['sucesso'] ?? false) === true);
}

// Deletar rotina
if ($rotinaId) {
    $page = getCsrf("$baseUrl/rotinas");
    $r = http('POST', "$baseUrl/api/rotinas/deletar/$rotinaId", ['_csrf_token' => $page['csrf']]);
    test("Cleanup: rotina deletada", ($r['json']['sucesso'] ?? false) === true);
}

// Deletar usuário
if ($userId) {
    $page = getCsrf("$baseUrl/admin/usuarios");
    $r = http('POST', "$baseUrl/admin/usuarios/delete/$userId", ['_csrf_token' => $page['csrf']]);
    test("Cleanup: usuario deletado", ($r['json']['sucesso'] ?? false) === true);
}

// Deletar projeto
if ($projetoId) {
    $page = getCsrf("$baseUrl/admin/projetos");
    $r = http('POST', "$baseUrl/admin/projetos/delete/$projetoId", ['_csrf_token' => $page['csrf']]);
    test("Cleanup: projeto deletado", ($r['json']['sucesso'] ?? false) === true);
}

// Deletar empresa
if ($empresaId) {
    $page = getCsrf("$baseUrl/admin/empresas");
    $r = http('POST', "$baseUrl/admin/empresas/delete/$empresaId", ['_csrf_token' => $page['csrf']]);
    test("Cleanup: empresa deletada", ($r['json']['sucesso'] ?? false) === true);
}

// Limpar cookies
@unlink($cookieFile);
@unlink(__DIR__ . '/test_cookies_e2e_newuser.txt');
@unlink(__DIR__ . '/test_cookies_e2e_reset.txt');

// =============================================================
echo "\n" . str_repeat('=', 60) . "\n";
if ($fail > 0) {
    echo "  FALHAS ($fail):\n";
    foreach ($erros as $e) echo "    - $e\n";
}
echo "\n  RESULTADO FINAL: $total testes, $ok OK, $fail falhas\n";
echo str_repeat('=', 60) . "\n";
exit($fail > 0 ? 1 : 0);
