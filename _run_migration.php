<?php
require 'vendor/autoload.php';
$db = \App\Core\Database::getConexao();
$sql = file_get_contents('migrations/008_auditoria_configuracoes_webhooks.sql');
$db->exec($sql);
echo 'Migration OK' . PHP_EOL;

foreach (['tb_auditoria', 'tb_configuracoes', 'tb_webhooks'] as $t) {
    $r = $db->query("SELECT count(*) FROM $t")->fetchColumn();
    echo $t . ': ' . $r . ' rows' . PHP_EOL;
}
