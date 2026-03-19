<?php
$base = 'http://localhost/DMC-DATALOAD/public';
$cookie = tempnam(sys_get_temp_dir(), 'rc');

$ch = curl_init("$base/login");
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => 'usuario=admin&senha=teste123',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIEJAR => $cookie,
    CURLOPT_COOKIEFILE => $cookie,
    CURLOPT_FOLLOWLOCATION => true,
]);
$loginResp = curl_exec($ch);
$loginCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "LOGIN => HTTP $loginCode => $loginResp\n\n";

$urls = [
    '/api/empresas/list',
    '/api/projetos/list',
    '/scheduler/agendadas',
    '/calendario/eventos?inicio=2025-01-01&fim=2025-12-31',
];

foreach ($urls as $u) {
    $ch = curl_init("$base$u");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIEFILE => $cookie,
    ]);
    $r = curl_exec($ch);
    $c = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "$u => HTTP $c => " . substr($r, 0, 500) . "\n\n";
}
unlink($cookie);
