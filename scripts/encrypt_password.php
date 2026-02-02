<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Utils\Crypto;

App\Core\Database::loadEnv(__DIR__ . '/..');

$argv = $_SERVER['argv'] ?? [];
if (!isset($argv[1])) {
    echo "Uso: php scripts/encrypt_password.php 'senha_plain'\n";
    exit(1);
}

$senha = $argv[1];
$key = getenv('ENCRYPTION_KEY') ?: getenv('APP_KEY');
if (!$key) {
    echo "Defina ENCRYPTION_KEY no .env (32 bytes base64)\n";
    exit(2);
}

try {
    $enc = Crypto::encrypt($senha, $key);
    echo $enc . PHP_EOL;
} catch (\Throwable $e) {
    echo 'Erro: ' . $e->getMessage() . PHP_EOL;
    exit(3);
}
