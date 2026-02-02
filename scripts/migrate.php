<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;

App\Core\Database::loadEnv(__DIR__ . '/..');

echo "Executando migração SQL...\n";

$sqlFile = __DIR__ . '/../sql/schema_postgres.sql';
if (!file_exists($sqlFile)) {
    echo "Arquivo SQL não encontrado: $sqlFile\n";
    exit(1);
}

$sql = file_get_contents($sqlFile);
try {
    $pdo = Database::getConexao();
    $pdo->exec($sql);
    echo "Migração executada com sucesso.\n";
} catch (\Throwable $e) {
    echo "Erro na migração: " . $e->getMessage() . "\n";
    exit(2);
}
