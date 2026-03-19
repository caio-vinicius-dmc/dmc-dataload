<?php
$dsn = 'pgsql:host=localhost;port=5433;dbname=db_dmc_dataload';
$pdo = new PDO($dsn, 'postgres', 'dmc2023@');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tables = ['tb_auditoria', 'tb_configuracoes', 'tb_webhooks'];
foreach ($tables as $t) {
    $stmt = $pdo->prepare("SELECT to_regclass('public.' || :t)");
    $stmt->execute([':t' => $t]);
    $r = $stmt->fetchColumn();
    echo $t . ': ' . ($r ? 'EXISTS' : 'NOT EXISTS') . PHP_EOL;
}

$stmt = $pdo->query("SELECT tablename FROM pg_tables WHERE schemaname='public' AND tablename LIKE 'tb_%' ORDER BY tablename");
foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $t) echo 'table: ' . $t . PHP_EOL;
