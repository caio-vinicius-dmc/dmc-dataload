<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>TESTE - APIs Externas - SEM AUTH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="alert alert-info">
            <h4><i class="bi bi-info-circle"></i> ARQUIVO DE TESTE - SEM AUTENTICAÇÃO</h4>
            <p>Este arquivo renderiza DIRETAMENTE a view sem passar pelo sistema de autenticação.</p>
            <p><strong>Se este arquivo funcionar sem erros, o problema é cache do navegador.</strong></p>
        </div>
    </div>

<?php
// Renderizar view diretamente
$pageTitle = 'APIs Externas - TESTE';
$currentPage = 'apis-externas';
$usuario = ['nome' => 'Usuario Teste', 'id' => 999];
$base = '/DMC-DATALOAD/public';
$_SESSION['usuario'] = $usuario;
define('BASE_URL', $base);

// Incluir view
include __DIR__ . '/views/apis-externas.php';
?>

</body>
</html>
