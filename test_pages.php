<?php
/**
 * ARQUIVO TEMPORÁRIO APENAS PARA TESTES
 * Renderiza as views sem autenticação para validação de JavaScript
 */

define('BASE_URL', '/DMC-DATALOAD/public');

$page = $_GET['page'] ?? 'workflows';

header('Content-Type: text/html; charset=utf-8');

// Renderizar a view solicitada
switch($page) {
    case 'workflows':
        include __DIR__ . '/views/workflows.php';
        break;
    case 'workflow-execucoes':
        include __DIR__ . '/views/workflow-execucoes.php';
        break;
    case 'apis-externas':
        include __DIR__ . '/views/apis-externas.php';
        break;
    case 'eventos-api':
        include __DIR__ . '/views/eventos-api.php';
        break;
    case 'workflow-builder':
        include __DIR__ . '/views/workflow-builder.php';
        break;
    default:
        echo "Página não encontrada. Use: ?page=workflows, ?page=workflow-execucoes, etc.";
}
?>
