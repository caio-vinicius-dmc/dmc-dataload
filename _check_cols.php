<?php
require_once __DIR__ . '/vendor/autoload.php';
$db = \App\Core\Database::getConexao();
$r = $db->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'tb_usuarios' ORDER BY ordinal_position");
foreach($r as $row) echo $row['column_name'] . "\n";
