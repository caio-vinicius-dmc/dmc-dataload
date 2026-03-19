<?php
/**
 * Teste E2E - Pipeline Builder, Workflow Builder, SQL Editor, Diagrama, API Events
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

function post(string $path, $data, string $cookie, string $csrf = '', bool $json = false): array {
    global $baseUrl;
    $ch = curl_init("$baseUrl$path");
    $headers = ["Cookie: $cookie"];
    if ($json) {
        if ($csrf) $data['_csrf_token'] = $csrf;
        $body = json_encode($data);
        $headers[] = 'Content-Type: application/json';
    } else {
        if ($csrf) $data['_csrf_token'] = $csrf;
        $body = http_build_query($data);
    }
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => $resp, 'json' => json_decode($resp, true)];
}

function getCSRF(string $cookie): string {
    $r = get('/admin/webhooks', $cookie);
    if (preg_match('/const\s+csrfToken\s*=\s*["\']([a-f0-9]+)["\']/i', $r['body'], $m)) {
        return $m[1];
    }
    $r = get('/conexoes', $cookie);
    if (preg_match('/name="_csrf_token"[^>]*value="([a-f0-9]+)"/i', $r['body'], $m)) {
        return $m[1];
    }
    return '';
}

echo "========================================\n";
echo "  TESTE E2E - Builders & Editors\n";
echo "========================================\n\n";

$cookie = loginAdmin();
$cookie ? ok("Login admin OK") : fail("Login admin falhou");
$csrf = getCSRF($cookie);
$csrf ? ok("CSRF token obtido") : fail("CSRF token não obtido");

// ===== PIPELINE BUILDER =====
echo "\n--- 1. Pipeline Builder ---\n";

// Listar paginas
$r = get('/pipelines', $cookie);
$r['code'] === 200 ? ok("GET /pipelines = 200") : fail("GET /pipelines = {$r['code']}");

$r = get('/pipelines/list', $cookie);
if ($r['code'] === 200) {
    ok("GET /pipelines/list = 200");
    $pipelines = $r['json']['pipelines'] ?? $r['json'] ?? [];
    info("Pipelines existentes: " . (is_array($pipelines) ? count($pipelines) : 0));
} else {
    fail("GET /pipelines/list = {$r['code']}");
}

// Criar pipeline de teste
$csrf = getCSRF($cookie);
$r = post('/pipelines/salvar', [
    'nome' => 'Pipeline Teste E2E',
    'descricao' => 'Criado por teste automatizado',
    'etapas' => json_encode([]),
], $cookie, $csrf);
$pipelineId = null;
if ($r['code'] === 200 && ($r['json']['sucesso'] ?? false)) {
    ok("Criar pipeline");
    $pipelineId = $r['json']['id'] ?? null;
    info("Pipeline ID: $pipelineId");
} else {
    // Sometimes returns data directly
    $pipelineId = $r['json']['id'] ?? null;
    if ($pipelineId) {
        ok("Criar pipeline (formato alternativo)");
    } else {
        fail("Criar pipeline: " . ($r['json']['erro'] ?? substr($r['body'], 0, 200)));
    }
}

if ($pipelineId) {
    // Buscar pipeline
    $r = get("/pipelines/get/$pipelineId", $cookie);
    if ($r['code'] === 200) {
        ok("Buscar pipeline por ID");
    } else {
        fail("Buscar pipeline = {$r['code']}");
    }

    // Deletar pipeline
    $csrf = getCSRF($cookie);
    $r = post("/pipelines/delete/$pipelineId", [], $cookie, $csrf);
    if ($r['code'] === 200 && ($r['json']['sucesso'] ?? false)) {
        ok("Deletar pipeline");
    } else {
        fail("Deletar pipeline: " . ($r['json']['erro'] ?? substr($r['body'], 0, 200)));
    }
}

// ===== WORKFLOW BUILDER =====
echo "\n--- 2. Workflow Builder ---\n";

$r = get('/workflows', $cookie);
$r['code'] === 200 ? ok("GET /workflows = 200") : fail("GET /workflows = {$r['code']}");

$r = get('/api/workflows/list', $cookie);
if ($r['code'] === 200) {
    ok("GET /api/workflows/list = 200");
    $workflows = $r['json']['workflows'] ?? $r['json'] ?? [];
    info("Workflows existentes: " . (is_array($workflows) ? count($workflows) : 0));
} else {
    fail("GET /api/workflows/list = {$r['code']}");
}

// Criar workflow de teste
$csrf = getCSRF($cookie);
$workflowData = [
    'nome' => 'Workflow Teste E2E',
    'descricao' => 'Criado por teste automatizado',
    'trigger_tipo' => 'manual',
    'dados_json' => json_encode(['nodes' => [], 'edges' => []]),
];
$r = post('/api/workflows/salvar', $workflowData, $cookie, $csrf, true);
$workflowId = null;
if ($r['code'] === 200) {
    $workflowId = $r['json']['id'] ?? $r['json']['dados']['id'] ?? null;
    if ($workflowId || ($r['json']['sucesso'] ?? false)) {
        ok("Criar workflow");
        info("Workflow ID: $workflowId");
    } else {
        fail("Criar workflow: " . ($r['json']['erro'] ?? substr($r['body'], 0, 200)));
    }
} else {
    fail("Criar workflow = {$r['code']}: " . substr($r['body'], 0, 200));
}

if ($workflowId) {
    // Buscar workflow
    $r = get("/api/workflows/get/$workflowId", $cookie);
    if ($r['code'] === 200) {
        ok("Buscar workflow por ID");
    } else {
        fail("Buscar workflow = {$r['code']}");
    }

    // Toggle workflow
    $csrf = getCSRF($cookie);
    $r = post("/api/workflows/toggle/$workflowId", [], $cookie, $csrf);
    if ($r['code'] === 200) {
        ok("Toggle workflow ativo/inativo");
    } else {
        fail("Toggle workflow = {$r['code']}");
    }

    // Deletar workflow
    $csrf = getCSRF($cookie);
    $r = post("/api/workflows/delete/$workflowId", [], $cookie, $csrf);
    if ($r['code'] === 200 && ($r['json']['sucesso'] ?? false)) {
        ok("Deletar workflow");
    } else {
        fail("Deletar workflow: " . ($r['json']['erro'] ?? substr($r['body'], 0, 200)));
    }
}

// ===== SQL EDITOR =====
echo "\n--- 3. SQL Editor ---\n";

$r = get('/rotinas', $cookie);
$r['code'] === 200 ? ok("GET /rotinas = 200") : fail("GET /rotinas = {$r['code']}");

$r = get('/rotinas/list', $cookie);
if ($r['code'] === 200) {
    ok("GET /rotinas/list = 200");
    $rotinas = $r['json']['rotinas'] ?? $r['json'] ?? [];
    info("Rotinas existentes: " . (is_array($rotinas) ? count($rotinas) : 0));
} else {
    fail("GET /rotinas/list = {$r['code']}");
}

// ===== DIAGRAMA =====
echo "\n--- 4. Diagrama ---\n";

$r = get('/diagrama', $cookie);
$r['code'] === 200 ? ok("GET /diagrama = 200") : fail("GET /diagrama = {$r['code']}");

// ===== API EVENTS =====
echo "\n--- 5. API Events ---\n";

$r = get('/apis-externas', $cookie);
$r['code'] === 200 ? ok("GET /apis-externas = 200") : fail("GET /apis-externas = {$r['code']}");

$r = get('/api/apis-externas/list', $cookie);
if ($r['code'] === 200) {
    ok("GET /api/apis-externas/list = 200");
    $apis = $r['json']['apis'] ?? $r['json'] ?? [];
    info("APIs externas existentes: " . (is_array($apis) ? count($apis) : 0));
} else {
    fail("GET /api/apis-externas/list = {$r['code']}");
}

// ===== SCHEDULER =====
echo "\n--- 6. Scheduler ---\n";

$r = get('/scheduler', $cookie);
$r['code'] === 200 ? ok("GET /scheduler = 200") : fail("GET /scheduler = {$r['code']}");

$r = get('/api/scheduler/rotinas', $cookie);
$r['code'] === 200 ? ok("GET /api/scheduler/rotinas = 200") : fail("GET /api/scheduler/rotinas = {$r['code']}");

// ===== CALENDÁRIO =====
echo "\n--- 7. Calendário ---\n";

$r = get('/calendario', $cookie);
$r['code'] === 200 ? ok("GET /calendario = 200") : fail("GET /calendario = {$r['code']}");

$r = get('/api/calendario/eventos', $cookie);
$r['code'] === 200 ? ok("GET /api/calendario/eventos = 200") : fail("GET /api/calendario/eventos = {$r['code']}");

// ===== DASHBOARD =====
echo "\n--- 8. Dashboard ---\n";

$r = get('/dashboard', $cookie);
$r['code'] === 200 ? ok("GET /dashboard = 200") : fail("GET /dashboard = {$r['code']}");

$r = get('/api/dashboard/metricas', $cookie);
if ($r['code'] === 200) {
    ok("GET /api/dashboard/metricas = 200");
    info("Métricas retornadas: " . count($r['json'] ?? []));
} else {
    fail("GET /api/dashboard/metricas = {$r['code']}");
}

// ===== HISTÓRICO =====
echo "\n--- 9. Histórico ---\n";

$r = get('/historico', $cookie);
$r['code'] === 200 ? ok("GET /historico = 200") : fail("GET /historico = {$r['code']}");

$r = get('/api/historico', $cookie);
if ($r['code'] === 200) {
    ok("GET /api/historico = 200");
} else {
    fail("GET /api/historico = {$r['code']}");
}

// ===== CONEXÕES =====
echo "\n--- 10. Conexões ---\n";

$r = get('/conexoes', $cookie);
$r['code'] === 200 ? ok("GET /conexoes = 200") : fail("GET /conexoes = {$r['code']}");

$r = get('/conexoes/list', $cookie);
$r['code'] === 200 ? ok("GET /conexoes/list = 200") : fail("GET /conexoes/list = {$r['code']}");

// ===== ADMIN =====
echo "\n--- 11. Admin ---\n";

$r = get('/admin/usuarios', $cookie);
$r['code'] === 200 ? ok("GET /admin/usuarios = 200") : fail("GET /admin/usuarios = {$r['code']}");

$r = get('/admin/empresas', $cookie);
$r['code'] === 200 ? ok("GET /admin/empresas = 200") : fail("GET /admin/empresas = {$r['code']}");

$r = get('/admin/projetos', $cookie);
$r['code'] === 200 ? ok("GET /admin/projetos = 200") : fail("GET /admin/projetos = {$r['code']}");

// ===== VERIFICAÇÃO AUDITORIA PIPELINE/WORKFLOW =====
echo "\n--- 12. Auditoria Pipeline/Workflow ---\n";
$r = get('/api/auditoria?entidade=pipeline', $cookie);
if ($r['code'] === 200) {
    $total = $r['json']['total'] ?? 0;
    $total > 0 ? ok("Pipelines registrados na auditoria ($total)") : info("Nenhum pipeline na auditoria (pode ser que não criou)");
}

$r = get('/api/auditoria?entidade=workflow', $cookie);
if ($r['code'] === 200) {
    $total = $r['json']['total'] ?? 0;
    $total > 0 ? ok("Workflows registrados na auditoria ($total)") : info("Nenhum workflow na auditoria (pode ser que não criou)");
}

echo "\n========================================\n";
echo "  RESULTADO: $totalOk OK, $totalFail falhas\n";
echo "========================================\n";
