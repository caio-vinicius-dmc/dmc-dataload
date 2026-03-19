<?php
$baseUrl = 'http://localhost/DMC-DATALOAD/public';

// Login
$ch = curl_init("$baseUrl/login");
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query(['usuario' => 'admin', 'senha' => 'Admin@2026']), CURLOPT_HEADER => true, CURLOPT_FOLLOWLOCATION => false]);
$resp = curl_exec($ch);
preg_match_all('/Set-Cookie:\s*([^;]+)/i', $resp, $m);
$cookie = implode('; ', $m[1]);
curl_close($ch);
echo "Cookie: $cookie\n";

// Get CSRF from webhooks page
$ch = curl_init("$baseUrl/admin/webhooks");
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => ["Cookie: $cookie"], CURLOPT_HEADER => true]);
$body = curl_exec($ch);
curl_close($ch);

// Show all csrfToken matches
if (preg_match_all('/csrfToken/i', $body, $mmm)) {
    echo "csrfToken found " . count($mmm[0]) . " times\n";
}
if (preg_match('/const\s+csrfToken\s*=\s*["\']([a-f0-9]+)["\']/i', $body, $mm)) {
    $csrf = $mm[1];
    echo "CSRF extracted: $csrf\n";
} else {
    echo "CSRF NOT found in webhooks page\n";
    // Try conexoes page
    $ch = curl_init("$baseUrl/conexoes");
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => ["Cookie: $cookie"]]);
    $body2 = curl_exec($ch);
    curl_close($ch);
    if (preg_match('/name="_csrf_token"[^>]*value="([a-f0-9]+)"/i', $body2, $mm2)) {
        $csrf = $mm2[1];
        echo "CSRF from conexoes: $csrf\n";
    } else {
        echo "CSRF NOT found anywhere!\n";
        // Try to find what's in the page
        if (preg_match('/csrfToken\s*=\s*(.{0,80})/i', $body, $mm3)) {
            echo "Pattern found: " . $mm3[0] . "\n";
        }
        $csrf = '';
    }
}

// Send workflow JSON with CSRF in JSON body
$payload = [
    'nome' => 'WF Test Debug',
    'trigger_tipo' => 'manual',
    'dados_json' => ['nodes' => [], 'edges' => []],
    '_csrf_token' => $csrf,
];
$jsonPayload = json_encode($payload);
echo "Payload: $jsonPayload\n";

$ch = curl_init("$baseUrl/api/workflows/salvar");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $jsonPayload,
    CURLOPT_HTTPHEADER => ["Cookie: $cookie", "Content-Type: application/json"],
]);
$result = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP $code\n";
echo "Response: $result\n";
