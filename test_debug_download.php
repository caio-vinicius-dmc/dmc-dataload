<?php
$cookieFile = __DIR__ . '/test_dbg2.txt';
$base = 'http://localhost/DMC-DATALOAD/public';

// Login
$ch = curl_init("$base/login");
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => 'usuario=admin&senha=Admin@2026', CURLOPT_COOKIEJAR => $cookieFile, CURLOPT_FOLLOWLOCATION => true]);
curl_exec($ch); curl_close($ch);

// Get CSRF
$ch = curl_init("$base/admin/backups");
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEFILE => $cookieFile]);
$page = curl_exec($ch); curl_close($ch);
preg_match('/csrfToken\s*=\s*["\']([^"\']+)["\']/', $page, $m);
$csrf = $m[1] ?? '';
echo "CSRF: " . substr($csrf, 0, 16) . "...\n";

// Create backup
$ch = curl_init("$base/api/backups/criar");
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query(['tipo' => 'rotinas', '_csrf_token' => $csrf]), CURLOPT_COOKIEFILE => $cookieFile, CURLOPT_COOKIEJAR => $cookieFile]);
$r = curl_exec($ch); curl_close($ch);
if (($p = strpos($r, '{"')) !== false) $r = substr($r, $p);
$data = json_decode($r, true);
$id = $data['id'] ?? 'NONE';
echo "Created backup ID: $id\n";

// Download
$ch = curl_init("$base/api/backups/download/$id");
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEFILE => $cookieFile]);
$body = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP: $code\n";
echo "Body length: " . strlen($body) . "\n";
echo "First 300 chars:\n" . substr($body, 0, 300) . "\n\n";

// Try stripping
$pos = strpos($body, '{"');
echo "First open brace at pos: $pos\n";
if ($pos !== false) {
    $json = substr($body, $pos);
    $decoded = json_decode($json, true);
    echo "JSON decode: " . ($decoded === null ? "NULL - " . json_last_error_msg() : "OK, keys=" . implode(',', array_keys($decoded))) . "\n";
}

// Cleanup
$ch = curl_init("$base/api/backups/delete/$id");
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query(['_csrf_token' => $csrf]), CURLOPT_COOKIEFILE => $cookieFile, CURLOPT_COOKIEJAR => $cookieFile]);
curl_exec($ch); curl_close($ch);
@unlink($cookieFile);
