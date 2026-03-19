<?php
/**
 * Teste E2E dos novos recursos: Auditoria, Configurações, Webhooks
 */

$baseUrl = 'http://localhost/DMC-DATALOAD/public';
$totalOk = 0;
$totalFail = 0;

function ok($msg) { global $totalOk; $totalOk++; echo "  [OK] $msg\n"; }
function fail($msg) { global $totalFail; $totalFail++; echo "  [FAIL] $msg\n"; }
function info($msg) { echo "  [INFO] $msg\n"; }

function loginAdmin(): string {
    global $baseUrl;
    $ch = curl_init("$baseUrl/login");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query(['usuario' => 'admin', 'senha' => 'Admin@2026']),
        CURLOPT_HEADER => true,
        CURLOPT_FOLLOWLOCATION => false,
    ]);
    $resp = curl_exec($ch);
    preg_match_all('/Set-Cookie:\s*([^;]+)/i', $resp, $m);
    curl_close($ch);
    return implode('; ', $m[1] ?? []);
}

function get(string $path, string $cookie): array {
    global $baseUrl;
    $ch = curl_init("$baseUrl$path");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["Cookie: $cookie"],
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => $body, 'json' => json_decode($body, true)];
}

function post(string $path, array $data, string $cookie, string $csrf = ''): array {
    global $baseUrl;
    if ($csrf) $data['_csrf_token'] = $csrf;
    $ch = curl_init("$baseUrl$path");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_HTTPHEADER => ["Cookie: $cookie"],
    ]);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => $body, 'json' => json_decode($body, true)];
}

function getCSRF(string $cookie): string {
    // Token is in auditoria or webhooks page as: const csrfToken = "xxx";
    $r = get('/admin/webhooks', $cookie);
    if (preg_match('/const\s+csrfToken\s*=\s*["\']([a-f0-9]+)["\']/i', $r['body'], $m)) {
        return $m[1];
    }
    // Fallback: conexoes page has hidden input
    $r = get('/conexoes', $cookie);
    if (preg_match('/name="_csrf_token"[^>]*value="([a-f0-9]+)"/i', $r['body'], $m)) {
        return $m[1];
    }
    return '';
}

echo "========================================\n";
echo "  TESTE E2E - Novos Recursos\n";
echo "========================================\n\n";

// 1. Login
echo "--- 1. Login ---\n";
$cookie = loginAdmin();
$cookie ? ok("Login admin OK") : fail("Login admin falhou");

$csrf = getCSRF($cookie);
$csrf ? ok("CSRF token obtido") : fail("CSRF token não obtido");

// 2. Páginas carregam
echo "\n--- 2. Páginas HTML ---\n";
$pages = [
    '/admin/auditoria' => 'Auditoria',
    '/admin/webhooks' => 'Webhooks',  
    '/configuracoes' => 'Configurações',
];
foreach ($pages as $path => $nome) {
    $r = get($path, $cookie);
    $r['code'] === 200 ? ok("$nome: HTTP 200") : fail("$nome: HTTP {$r['code']}");
}

// 3. API Auditoria
echo "\n--- 3. API Auditoria ---\n";
$r = get('/api/auditoria', $cookie);
if ($r['code'] === 200) {
    ok("GET /api/auditoria = 200");
    $data = $r['json'];
    if (isset($data['registros'])) {
        ok("Retorna registros de auditoria");
        info("Total: " . ($data['total'] ?? count($data['registros'])) . " registros");
    } else {
        fail("Formato inesperado");
    }
    if (isset($data['estatisticas'])) {
        ok("Retorna estatísticas");
        info("Stats: " . json_encode($data['estatisticas']));
    }
} else {
    fail("GET /api/auditoria = {$r['code']}");
}

// 4. API Auditoria Exportar
echo "\n--- 4. Auditoria Exportar CSV ---\n";
$r = get('/api/auditoria/exportar', $cookie);
$r['code'] === 200 ? ok("Export CSV = 200") : fail("Export CSV = {$r['code']}");

// 5. API Configurações
echo "\n--- 5. API Configurações ---\n";
$r = get('/api/configuracoes', $cookie);
if ($r['code'] === 200) {
    ok("GET /api/configuracoes = 200");
    $data = $r['json'];
    if (isset($data['configs'])) {
        ok("Retorna configs");
        info("Total configs: " . count($data['configs']));
    }
} else {
    fail("GET /api/configuracoes = {$r['code']}");
}

// 6. Salvar configuração
echo "\n--- 6. Salvar Config ---\n";
$r = post('/api/configuracoes/geral', ['app_nome' => 'DMC DataLoad'], $cookie, $csrf);
if ($r['code'] === 200 && ($r['json']['sucesso'] ?? false)) {
    ok("Salvar config geral");
} else {
    fail("Salvar config geral: " . ($r['json']['erro'] ?? $r['body']));
}

// Refresh CSRF after POST
$csrf = getCSRF($cookie);

// 7. Testar Email (expected to fail without real SMTP, but endpoint should work)
echo "\n--- 7. Testar Email ---\n";
$r = post('/api/configuracoes/testar-email', ['destinatario' => 'test@example.com'], $cookie, $csrf);
if ($r['code'] === 200) {
    ok("Endpoint testar-email retorna 200");
    $success = $r['json']['sucesso'] ?? false;
    info("Resultado: " . ($success ? "Email enviado" : ($r['json']['erro'] ?? 'falhou (esperado sem SMTP real)')));
} else {
    fail("Endpoint testar-email = {$r['code']}");
}

$csrf = getCSRF($cookie);

// 8. Webhooks CRUD
echo "\n--- 8. Webhooks CRUD ---\n";
$r = get('/api/webhooks/list', $cookie);
if ($r['code'] === 200) {
    ok("GET /api/webhooks/list = 200");
    info("Webhooks existentes: " . count($r['json']['webhooks'] ?? []));
} else {
    fail("GET /api/webhooks/list = {$r['code']}");
}

// Criar webhook de teste
$r = post('/api/webhooks/salvar', [
    'nome' => 'Teste Automatizado',
    'url' => 'https://httpbin.org/post',
    'eventos' => ['falha_execucao', 'sucesso_execucao'],
    'ativo' => '1',
], $cookie, $csrf);

$webhookId = null;
if ($r['code'] === 200 && ($r['json']['sucesso'] ?? false)) {
    ok("Criar webhook");
    $webhookId = $r['json']['id'] ?? null;
    info("Webhook ID: $webhookId");
} else {
    fail("Criar webhook: " . ($r['json']['erro'] ?? $r['body']));
}

$csrf = getCSRF($cookie);

if ($webhookId) {
    // Buscar webhook
    $r = get("/api/webhooks/get/$webhookId", $cookie);
    if ($r['code'] === 200 && ($r['json']['webhook'] ?? $r['json']['dados'] ?? false)) {
        ok("Buscar webhook por ID");
    } else {
        fail("Buscar webhook: " . ($r['json']['erro'] ?? $r['body']));
    }

    // Testar webhook
    $r = post("/api/webhooks/testar/$webhookId", [], $cookie, $csrf);
    if ($r['code'] === 200) {
        ok("Testar webhook endpoint funciona");
        info("Resultado: " . ($r['json']['sucesso'] ? 'OK' : ($r['json']['erro'] ?? 'falhou')));
    } else {
        fail("Testar webhook = {$r['code']}");
    }

    $csrf = getCSRF($cookie);

    // Deletar webhook
    $r = post("/api/webhooks/delete/$webhookId", [], $cookie, $csrf);
    if ($r['code'] === 200 && ($r['json']['sucesso'] ?? false)) {
        ok("Deletar webhook");
    } else {
        fail("Deletar webhook: " . ($r['json']['erro'] ?? $r['body']));
    }
}

// 9. Exportar/Importar configs
echo "\n--- 9. Exportar Configs ---\n";
$r = get('/api/configuracoes/exportar', $cookie);
if ($r['code'] === 200) {
    ok("Exportar configs = 200");
    $data = json_decode($r['body'], true);
    if (is_array($data) && count($data) > 0) {
        ok("Exporta dados JSON válidos");
        info("Configs exportadas: " . count($data));
    }
} else {
    fail("Exportar configs = {$r['code']}");
}

// 10. Verificar auditoria registrou ações
echo "\n--- 10. Verificação Auditoria ---\n";
$r = get('/api/auditoria?acao=login', $cookie);
if ($r['code'] === 200) {
    $total = $r['json']['total'] ?? 0;
    $total > 0 ? ok("Logins registrados na auditoria ($total)") : info("Nenhum login registrado ainda");
}

$r = get('/api/auditoria?entidade=configuracao', $cookie);
if ($r['code'] === 200) {
    $total = $r['json']['total'] ?? 0;
    $total > 0 ? ok("Mudanças de config registradas ($total)") : info("Nenhuma mudança de config registrada");
}

// 11. Sidebar links
echo "\n--- 11. Sidebar ---\n";
$r = get('/dashboard', $cookie);
if (strpos($r['body'], '/admin/auditoria') !== false) {
    ok("Link Auditoria no sidebar");
} else {
    fail("Link Auditoria não encontrado no sidebar");
}
if (strpos($r['body'], '/admin/webhooks') !== false) {
    ok("Link Webhooks no sidebar");
} else {
    fail("Link Webhooks não encontrado no sidebar");
}

echo "\n========================================\n";
echo "  RESULTADO: $totalOk OK, $totalFail falhas\n";
echo "========================================\n";
