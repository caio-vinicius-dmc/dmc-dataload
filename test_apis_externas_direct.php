<?php
// Script de teste com sessão simulada
session_start();
$_SESSION['usuario'] = ['nome' => 'Teste', 'id' => 1];

define('BASE_URL', '/DMC-DATALOAD/public');

// Renderizar a view
include __DIR__ . '/views/apis-externas.php';
?>
