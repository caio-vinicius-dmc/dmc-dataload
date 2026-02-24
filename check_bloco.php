<?php
require 'vendor/autoload.php';
(Dotenv\Dotenv::createImmutable('.'))->safeLoad();

$db = new PDO(
    'pgsql:host=localhost;port=5433;dbname=db_dmc_dataload',
    'postgres',
    'dmc2023@'
);

echo "=== ESTRUTURA TB_BLOCOS_ROTINA ===\n\n";
$cols = $db->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'tb_blocos_rotina' ORDER BY ordinal_position")->fetchAll(PDO::FETCH_COLUMN);
echo "Colunas: " . implode(', ', $cols) . "\n\n";

$bloco = $db->query("SELECT * FROM tb_blocos_rotina WHERE id_rotina = 25 ORDER BY ordem LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if ($bloco) {
    echo "=== BLOCO DA ROTINA 25 ===\n\n";
    print_r($bloco);
} else {
    echo "Nenhum bloco encontrado para a rotina 25\n";
}
