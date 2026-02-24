<?php
// TESTE DIRETO - SEM AUTENTICAÇÃO
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pageTitle = 'APIs Externas';
$currentPage = 'apis-externas';
$usuario = ['nome' => 'Teste'];
$base = '/DMC-DATALOAD/public';

// Simular variáveis globais
$_SESSION['usuario'] = $usuario;
define('BASE_URL', $base);

// Incluir a view diretamente
include __DIR__ . '/views/apis-externas.php';
?>
