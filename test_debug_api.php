<?php
$cookieFile = __DIR__ . '/test_dbg.txt';

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost/DMC-DATALOAD/public/login',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => 'usuario=admin&senha=Admin@2026',
    CURLOPT_COOKIEJAR => $cookieFile,
    CURLOPT_FOLLOWLOCATION => true
]);
curl_exec($ch);
curl_close($ch);

$urls = [
    'STATS' => '/api/fila/stats',
    'FILA_LISTAR' => '/api/fila/listar',
    'CANAIS_LISTAR' => '/api/canais/listar',
    'BACKUPS_LISTAR' => '/api/backups/listar',
];

foreach ($urls as $label => $path) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "http://localhost/DMC-DATALOAD/public$path",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookieFile
    ]);
    $r = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "[$label] HTTP $code: " . substr($r, 0, 500) . "\n\n";
}

// Test enfileirar with CSRF
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost/DMC-DATALOAD/public/admin/fila',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEFILE => $cookieFile
]);
$page = curl_exec($ch);
curl_close($ch);
preg_match('/csrfToken\s*=\s*["\']([^"\']+)["\']/', $page, $m);
$csrf = $m[1] ?? 'NONE';
echo "CSRF: $csrf\n\n";

// Try enfileirar
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost/DMC-DATALOAD/public/api/fila/enfileirar',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'tipo' => 'rotina', 'id_recurso' => 999, 'nome_recurso' => 'Teste Debug', '_csrf_token' => $csrf
    ]),
    CURLOPT_COOKIEFILE => $cookieFile,
    CURLOPT_COOKIEJAR => $cookieFile
]);
$r = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "[ENFILEIRAR] HTTP $code: " . substr($r, 0, 500) . "\n\n";

// Try criar backup
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost/DMC-DATALOAD/public/api/backups/criar',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query(['tipo' => 'completo', '_csrf_token' => $csrf]),
    CURLOPT_COOKIEFILE => $cookieFile,
    CURLOPT_COOKIEJAR => $cookieFile
]);
$r = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "[BACKUP_CRIAR] HTTP $code: " . substr($r, 0, 1000) . "\n\n";

@unlink($cookieFile);
