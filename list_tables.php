<?php
require 'vendor/autoload.php';
(Dotenv\Dotenv::createImmutable('.'))->safeLoad();

$db = new PDO(
    'pgsql:host=localhost;port=5433;dbname=db_dmc_dataload',
    'postgres',
    'dmc2023@'
);

echo "=== TABELAS RELACIONADAS ===\n\n";
$tables = $db->query("SELECT tablename FROM pg_tables WHERE schemaname = 'public' AND (tablename LIKE '%bloco%' OR tablename LIKE '%rotina%') ORDER BY tablename")->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $t) {
    echo $t . "\n";
}
