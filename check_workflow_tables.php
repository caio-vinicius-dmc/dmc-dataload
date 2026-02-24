<?php
require 'vendor/autoload.php';
use App\Core\Database;
Database::loadEnv('./');
$db = Database::getConexao();

echo "=== Verificando tabela tb_eventos_api ===\n";
$r = $db->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'tb_eventos_api'");
print_r($r->fetchAll(PDO::FETCH_COLUMN));

echo "\n=== Verificando tabela tb_api_externas ===\n";
$r = $db->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'tb_api_externas'");
print_r($r->fetchAll(PDO::FETCH_COLUMN));
