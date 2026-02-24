<?php
require 'vendor/autoload.php';
(Dotenv\Dotenv::createImmutable('.'))->safeLoad();

$db = new PDO(
    'pgsql:host=localhost;port=5433;dbname=db_dmc_dataload',
    'postgres',
    'dmc2023@'
);

// Reativar rotina 25 com data_fim +2 horas
$db->exec("UPDATE tb_rotinas 
           SET ativa = true, 
               data_fim = NOW() + INTERVAL '2 hours',
               proxima_execucao = NOW() + INTERVAL '1 minute'
           WHERE id = 25");

echo "Rotina 25 reativada!\n";
echo "Hora atual: " . date('Y-m-d H:i:s') . "\n";

// Verificar rotina
$r = $db->query("SELECT id, nome, ativa, agendamento_cron, proxima_execucao, data_fim FROM tb_rotinas WHERE id = 25")->fetch(PDO::FETCH_ASSOC);
echo "\nRotina:\n";
echo "  Nome: " . $r['nome'] . "\n";
echo "  Ativa: " . ($r['ativa'] ? 'SIM' : 'NAO') . "\n";
echo "  CRON: " . $r['agendamento_cron'] . "\n";
echo "  Proxima: " . $r['proxima_execucao'] . "\n";
echo "  Data Fim: " . $r['data_fim'] . "\n";

// Verificar se tem blocos
$blocos = $db->query("SELECT COUNT(*) FROM tb_blocos WHERE id_rotina = 25")->fetchColumn();
echo "\nBlocos SQL: " . $blocos . "\n";
