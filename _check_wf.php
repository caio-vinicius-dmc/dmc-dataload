<?php
require 'vendor/autoload.php';
$db = new PDO('pgsql:host=localhost;port=5433;dbname=db_dmc_dataload', 'postgres', 'dmc2023@');
$r = $db->query("SELECT column_name, is_nullable, column_default FROM information_schema.columns WHERE table_name = 'tb_workflows' ORDER BY ordinal_position");
foreach ($r as $c) {
    echo $c['column_name'] . ' | nullable=' . $c['is_nullable'] . ' | default=' . ($c['column_default'] ?: 'none') . "\n";
}
