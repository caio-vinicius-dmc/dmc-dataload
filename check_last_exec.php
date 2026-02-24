<?php
require 'vendor/autoload.php';
(Dotenv\Dotenv::createImmutable('.'))->safeLoad();

$db = new PDO(
    'pgsql:host=localhost;port=5433;dbname=db_dmc_dataload',
    'postgres',
    'dmc2023@'
);

echo "=== ÚLTIMA EXECUÇÃO ===\n\n";

$r = $db->query("SELECT id, status, blocos_executados, registros_processados, meta, data_inicio 
                 FROM tb_logs_execucao 
                 ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

echo "ID: " . $r['id'] . "\n";
echo "Data: " . $r['data_inicio'] . "\n";
echo "Status: " . $r['status'] . "\n";
echo "Blocos Executados: " . ($r['blocos_executados'] ?? 0) . "\n";
echo "Registros Processados: " . ($r['registros_processados'] ?? 0) . "\n";

if ($r['meta']) {
    $meta = json_decode($r['meta'], true);
    echo "Meta: " . count($meta) . " blocos detalhados\n\n";
    
    foreach ($meta as $idx => $bloco) {
        echo "  Bloco " . ($idx + 1) . ": " . $bloco['bloco'] . " | ";
        echo "Status: " . $bloco['status'] . " | ";
        echo "Registros: " . ($bloco['registros'] ?? 0) . "\n";
    }
} else {
    echo "Meta: NULL (sem detalhes de blocos)\n";
}
