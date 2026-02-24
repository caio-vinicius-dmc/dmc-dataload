<?php
// MODO TESTE
if (isset($_GET['api_mock'])) {
    header('Content-Type: application/json');
    if ($_GET['api_mock'] === 'list') {
        echo json_encode([
            'sucesso' => true,
            'dados' => [
                ['id_execucao' => 1, 'id_workflow' => 1, 'workflow_nome' => 'Workflow 1', 'status' => 'success', 'iniciado_em' => date('Y-m-d H:i:s', strtotime('-1 hour')), 'duracao_segundos' => 5],
                ['id_execucao' => 2, 'id_workflow' => 2, 'workflow_nome' => 'Workflow 2', 'status' => 'running', 'iniciado_em' => date('Y-m-d H:i:s', strtotime('-10 minutes')), 'duracao_segundos' => 0],
                ['id_execucao' => 3, 'id_workflow' => 1, 'workflow_nome' => 'Workflow 1', 'status' => 'error', 'iniciado_em' => date('Y-m-d H:i:s', strtotime('-3 hours')), 'duracao_segundos' => 2]
            ]
        ]);
    }
    exit;
}

session_start();
$_SESSION['usuario'] = ['nome' => 'Teste'];
define('BASE_URL', '/DMC-DATALOAD/public');
$pageTitle = 'TESTE - Execuções';
$currentPage = 'workflow-execucoes';

ob_start();
include __DIR__ . '/views/workflow-execucoes.php';
$content = ob_get_clean();

$mockScript = <<<'MOCK'
<script>
const originalGetJSON = $.getJSON;
$.getJSON = function(url, success) {
    if (url.includes('/api/workflows/execucoes/list')) return originalGetJSON('/DMC-DATALOAD/test_workflow_execucoes.php?api_mock=list', success);
    return originalGetJSON.apply(this, arguments);
};
console.log('%c[MODO TESTE]', 'background: #10b981; color: white; padding: 4px;');
</script>
MOCK;

echo str_replace('</body>', $mockScript . '</body>', $content);
?>