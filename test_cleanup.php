<?php
// Cleanup leftover test data
$dsn = 'pgsql:host=localhost;port=5433;dbname=db_dmc_dataload';
$pdo = new PDO($dsn, 'postgres', 'dmc2023@');
$pdo->exec("DELETE FROM tb_fila_execucao WHERE nome_recurso LIKE '%Teste%'");
$pdo->exec("DELETE FROM tb_canais_notificacao WHERE nome LIKE '%Teste%'");
$pdo->exec("DELETE FROM tb_backups WHERE nome LIKE 'backup_%'");
// Also clean backup files
foreach (glob(__DIR__ . '/backups/backup_*.json') as $f) unlink($f);
echo "Cleaned up.\n";
