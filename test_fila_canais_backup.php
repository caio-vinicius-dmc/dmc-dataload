<?php
/**
 * DMC DataLoad - Teste Completo: Fila, Canais e Backups
 * Simula operações reais de browser para Background Queue, Slack/Teams, Backup/Restore
 */

$baseUrl = 'http://localhost/DMC-DATALOAD/public';
$cookieFile = __DIR__ . '/test_cookies_marathon.txt';
$ok = 0; $fail = 0; $total = 0;

function test($nome, $condicao) {
    global $ok, $fail, $total;
    $total++;
    if ($condicao) { $ok++; echo "  [OK] $nome\n"; }
    else { $fail++; echo "  [FAIL] $nome\n"; }
}

function http($method, $url, $data = null, $isJson = false) {
    global $cookieFile;
    $ch = curl_init();
    $opts = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_FOLLOWLOCATION => true
    ];
    if ($method === 'POST') {
        $opts[CURLOPT_POST] = true;
        $opts[CURLOPT_POSTFIELDS] = $isJson ? json_encode($data) : http_build_query($data ?? []);
        if ($isJson) $opts[CURLOPT_HTTPHEADER] = ['Content-Type: application/json'];
    }
    curl_setopt_array($ch, $opts);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    // Strip HTML prefix if present (root index.php output)
    $jsonBody = $resp;
    if (preg_match('/\{[\s]*"/', $resp, $bm, PREG_OFFSET_CAPTURE)) $jsonBody = substr($resp, $bm[0][1]);
    elseif (($pos = strpos($resp, '[{')) !== false) $jsonBody = substr($resp, $pos);
    return ['code' => $code, 'body' => $resp, 'json' => json_decode($jsonBody, true)];
}

// Login
echo "\n=== LOGIN ===\n";
http('POST', "$baseUrl/login", ['usuario' => 'admin', 'senha' => 'Admin@2026']);

// Obter CSRF token
$page = http('GET', "$baseUrl/admin/fila");
preg_match('/csrfToken\s*=\s*["\']([^"\']+)["\']/', $page['body'], $m);
$csrf = $m[1] ?? '';
test("CSRF token obtido", !empty($csrf));

// ========================================
echo "\n=== FILA DE EXECUÇÃO ===\n";

// Testar estatísticas
$r = http('GET', "$baseUrl/api/fila/stats");
test("Stats da fila retorna 200", $r['code'] === 200);
test("Stats tem campos esperados", isset($r['json']['dados']['pendentes']));

// Testar listagem da fila (vazia)
$r = http('GET', "$baseUrl/api/fila/listar");
test("Listagem da fila retorna 200", $r['code'] === 200);
test("Listagem tem dados array", is_array($r['json']['dados'] ?? null));

// Enfileirar sem CSRF (deve falhar)
$r = http('POST', "$baseUrl/api/fila/enfileirar", [
    'tipo' => 'rotina', 'id_recurso' => 1, 'nome_recurso' => 'Teste'
]);
test("Enfileirar sem CSRF retorna 403", $r['code'] === 403);

// Enfileirar com dados inválidos
$r = http('POST', "$baseUrl/api/fila/enfileirar", [
    'tipo' => 'invalido', 'id_recurso' => 0, '_csrf_token' => $csrf
]);
test("Enfileirar com tipo inválido retorna 400", $r['code'] === 400);

// Enfileirar rotina válida
$r = http('POST', "$baseUrl/api/fila/enfileirar", [
    'tipo' => 'rotina', 'id_recurso' => 999, 'nome_recurso' => 'Rotina Teste Fila', 'prioridade' => 3, '_csrf_token' => $csrf
]);
test("Enfileirar rotina retorna sucesso", ($r['json']['sucesso'] ?? false) === true);
$filaId = $r['json']['fila_id'] ?? null;
test("Retorna fila_id", !empty($filaId));

// Enfileirar duplicado (deve falhar)
if ($filaId) {
    $r = http('POST', "$baseUrl/api/fila/enfileirar", [
        'tipo' => 'rotina', 'id_recurso' => 999, 'nome_recurso' => 'Rotina Teste Fila', '_csrf_token' => $csrf
    ]);
    test("Enfileirar duplicado retorna erro", ($r['json']['sucesso'] ?? true) === false);
}

// Verificar status do item
if ($filaId) {
    $r = http('GET', "$baseUrl/api/fila/status/$filaId");
    test("Status do item retorna 200", $r['code'] === 200);
    test("Status é pendente", ($r['json']['dados']['status'] ?? '') === 'pendente');
    test("Prioridade é 3", (int)($r['json']['dados']['prioridade'] ?? 0) === 3);
}

// Cancelar item
if ($filaId) {
    $r = http('POST', "$baseUrl/api/fila/cancelar/$filaId", ['_csrf_token' => $csrf]);
    test("Cancelar item retorna sucesso", ($r['json']['sucesso'] ?? false) === true);
    
    // Verificar que ficou cancelado
    $r = http('GET', "$baseUrl/api/fila/status/$filaId");
    test("Status pós-cancelamento é cancelado", ($r['json']['dados']['status'] ?? '') === 'cancelado');
}

// Verificar estatísticas atualizadas
$r = http('GET', "$baseUrl/api/fila/stats");
test("Stats mostra cancelados > 0", ($r['json']['dados']['cancelados'] ?? 0) > 0);

// Testar página da fila carrega
$r = http('GET', "$baseUrl/admin/fila");
test("Página da fila carrega", $r['code'] === 200);
test("Página contém Fila de Execução", strpos($r['body'], 'Fila') !== false);

// ========================================
echo "\n=== CANAIS DE NOTIFICAÇÃO (Slack/Teams) ===\n";

// Testar página de canais
$r = http('GET', "$baseUrl/admin/canais");
test("Página de canais carrega", $r['code'] === 200);
test("Página contém Canais", strpos($r['body'], 'Canais') !== false);

// Obter CSRF da página de canais
preg_match('/csrfToken\s*=\s*["\']([^"\']+)["\']/', $r['body'], $m);
$csrfCanais = $m[1] ?? $csrf;

// Listar canais (vazio)
$r = http('GET', "$baseUrl/api/canais/listar");
test("Listar canais retorna 200", $r['code'] === 200);
test("Dados é array", is_array($r['json']['dados'] ?? null));

// Criar canal sem CSRF
$r = http('POST', "$baseUrl/api/canais/salvar", [
    'nome' => 'Teste', 'tipo' => 'slack', 'webhook_url' => 'https://hooks.slack.com/test'
]);
test("Criar canal sem CSRF retorna 403", $r['code'] === 403);

// Criar canal inválido (sem nome)
$r = http('POST', "$baseUrl/api/canais/salvar", [
    'tipo' => 'slack', 'webhook_url' => 'https://hooks.slack.com/test', '_csrf_token' => $csrfCanais
]);
test("Criar canal sem nome retorna erro", ($r['json']['sucesso'] ?? true) === false);

// Criar canal válido (Slack)
$r = http('POST', "$baseUrl/api/canais/salvar", [
    'nome' => 'Alertas Teste', 'tipo' => 'slack', 'webhook_url' => 'https://hooks.slack.com/services/T00000/B00000/xxxx',
    'canal' => '#alertas-teste', 'ativo' => '1', 'notificar_falha' => '1', '_csrf_token' => $csrfCanais
]);
test("Criar canal Slack retorna sucesso", ($r['json']['sucesso'] ?? false) === true);
$canalId = $r['json']['id'] ?? null;
test("Retorna ID do canal", !empty($canalId));

// Criar canal Teams
$r = http('POST', "$baseUrl/api/canais/salvar", [
    'nome' => 'Teams Teste', 'tipo' => 'teams', 'webhook_url' => 'https://outlook.office.com/webhook/test',
    'ativo' => '1', 'notificar_sucesso' => '1', 'notificar_falha' => '1', '_csrf_token' => $csrfCanais
]);
test("Criar canal Teams retorna sucesso", ($r['json']['sucesso'] ?? false) === true);
$canalTeamsId = $r['json']['id'] ?? null;

// Criar canal Discord  
$r = http('POST', "$baseUrl/api/canais/salvar", [
    'nome' => 'Discord Teste', 'tipo' => 'discord', 'webhook_url' => 'https://discord.com/api/webhooks/test/token',
    'ativo' => '1', 'notificar_inicio' => '1', '_csrf_token' => $csrfCanais
]);
test("Criar canal Discord retorna sucesso", ($r['json']['sucesso'] ?? false) === true);

// Listar canais (agora com 3)
$r = http('GET', "$baseUrl/api/canais/listar");
test("Listar mostra 3 canais", count($r['json']['dados'] ?? []) >= 3);

// Editar canal
if ($canalId) {
    $r = http('POST', "$baseUrl/api/canais/salvar", [
        'id' => $canalId, 'nome' => 'Alertas Teste Editado', 'tipo' => 'slack',
        'webhook_url' => 'https://hooks.slack.com/services/T00000/B00000/yyyy',
        'canal' => '#alertas-prod', 'ativo' => '1', 'notificar_falha' => '1', 'notificar_sucesso' => '1',
        '_csrf_token' => $csrfCanais
    ]);
    test("Editar canal retorna sucesso", ($r['json']['sucesso'] ?? false) === true);
}

// Deletar canais de teste
foreach ([$canalId, $canalTeamsId] as $id) {
    if ($id) {
        $r = http('POST', "$baseUrl/api/canais/delete/$id", ['_csrf_token' => $csrfCanais]);
        test("Deletar canal $id retorna sucesso", ($r['json']['sucesso'] ?? false) === true);
    }
}

// Limpar canal Discord também
$r = http('GET', "$baseUrl/api/canais/listar");
foreach ($r['json']['dados'] ?? [] as $c) {
    if (strpos($c['nome'], 'Teste') !== false) {
        http('POST', "$baseUrl/api/canais/delete/{$c['id']}", ['_csrf_token' => $csrfCanais]);
    }
}

// ========================================
echo "\n=== BACKUP & RESTORE ===\n";

// Testar página de backups
$r = http('GET', "$baseUrl/admin/backups");
test("Página de backups carrega", $r['code'] === 200);
test("Página contém Backup", strpos($r['body'], 'Backup') !== false);

// Obter CSRF da página de backups
preg_match('/csrfToken\s*=\s*["\']([^"\']+)["\']/', $r['body'], $m);
$csrfBackup = $m[1] ?? $csrf;

// Listar backups (vazio)
$r = http('GET', "$baseUrl/api/backups/listar");
test("Listar backups retorna 200", $r['code'] === 200);
test("Dados é array", is_array($r['json']['dados'] ?? null));

// Criar backup sem CSRF
$r = http('POST', "$baseUrl/api/backups/criar", ['tipo' => 'completo']);
test("Criar backup sem CSRF retorna 403", $r['code'] === 403);

// Criar backup completo
$r = http('POST', "$baseUrl/api/backups/criar", ['tipo' => 'completo', '_csrf_token' => $csrfBackup]);
test("Criar backup completo retorna sucesso", ($r['json']['sucesso'] ?? false) === true);
$backupId = $r['json']['id'] ?? null;
test("Retorna ID do backup", !empty($backupId));
test("Retorna tamanho > 0", ($r['json']['tamanho'] ?? 0) > 0);
echo "    Tamanho: " . number_format($r['json']['tamanho'] ?? 0) . " bytes\n";

// Criar backup de rotinas
$r = http('POST', "$baseUrl/api/backups/criar", ['tipo' => 'rotinas', '_csrf_token' => $csrfBackup]);
test("Criar backup rotinas retorna sucesso", ($r['json']['sucesso'] ?? false) === true);
$backupRotinasId = $r['json']['id'] ?? null;

// Criar backup de configurações
$r = http('POST', "$baseUrl/api/backups/criar", ['tipo' => 'configuracoes', '_csrf_token' => $csrfBackup]);
test("Criar backup configs retorna sucesso", ($r['json']['sucesso'] ?? false) === true);

// Listar backups (agora com 3)
$r = http('GET', "$baseUrl/api/backups/listar");
$backups = $r['json']['dados'] ?? [];
test("Listar mostra 3 backups", count($backups) >= 3);

// Download backup
if ($backupId) {
    $r = http('GET', "$baseUrl/api/backups/download/$backupId");
    test("Download backup retorna 200", $r['code'] === 200);
    $backupJson = $r['json'];
    test("Download é JSON válido", $backupJson !== null);
    test("Backup tem _meta", isset($backupJson['_meta']));
    test("Meta tem versão", ($backupJson['_meta']['versao'] ?? '') === '1.0');
    test("Meta tem tipo completo", ($backupJson['_meta']['tipo'] ?? '') === 'completo');
    test("Backup tem tb_rotinas", array_key_exists('tb_rotinas', $backupJson ?? []));
    test("Backup tem tb_perfis_conexao", array_key_exists('tb_perfis_conexao', $backupJson ?? []));
    test("Backup tem tb_configuracoes", array_key_exists('tb_configuracoes', $backupJson ?? []));
}

// Deletar backup
if ($backupRotinasId) {
    $r = http('POST', "$baseUrl/api/backups/delete/$backupRotinasId", ['_csrf_token' => $csrfBackup]);
    test("Deletar backup retorna sucesso", ($r['json']['sucesso'] ?? false) === true);
}

// Limpar restantes
$r = http('GET', "$baseUrl/api/backups/listar");
foreach ($r['json']['dados'] ?? [] as $b) {
    http('POST', "$baseUrl/api/backups/delete/{$b['id']}", ['_csrf_token' => $csrfBackup]);
}

// ========================================
echo "\n=== PÁGINAS DO SIDEBAR ===\n";

$pages = [
    '/admin/fila' => 'Fila',
    '/admin/canais' => 'Canais',
    '/admin/backups' => 'Backup'
];
foreach ($pages as $path => $text) {
    $r = http('GET', "$baseUrl$path");
    test("Página $path carrega (200)", $r['code'] === 200);
}

// Verificar sidebar tem os links
$r = http('GET', "$baseUrl/dashboard");
test("Sidebar tem link Slack/Teams", strpos($r['body'], '/admin/canais') !== false);
test("Sidebar tem link Fila", strpos($r['body'], '/admin/fila') !== false);
test("Sidebar tem link Backups", strpos($r['body'], '/admin/backups') !== false);

// ========================================
echo "\n=== WORKER CLI ===\n";
// Verificar que worker.php tem sintaxe válida
$output = [];
exec('php -l ' . escapeshellarg(__DIR__ . '/worker.php') . ' 2>&1', $output, $exitCode);
test("worker.php sintaxe OK", $exitCode === 0);

// Limpar
@unlink($cookieFile);

echo "\n========================================\n";
echo "  RESULTADO: $total testes, $ok OK, $fail falhas\n";
echo "========================================\n";
exit($fail > 0 ? 1 : 0);
