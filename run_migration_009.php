<?php
require 'vendor/autoload.php';
$db = \App\Core\Database::getConexao();
$sql = file_get_contents('migrations/009_fila_canais_backups.sql');
$db->exec($sql);
echo "Migration 009 OK\n";
foreach (['tb_fila_execucao','tb_canais_notificacao','tb_backups'] as $t) {
    $r = $db->query("SELECT count(*) FROM $t")->fetchColumn();
    echo "$t: $r rows\n";
}
