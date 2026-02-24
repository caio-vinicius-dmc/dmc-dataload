<?php
// MODO TESTE
if (isset($_GET['api_mock'])) {
    header('Content-Type: application/json');
    if ($_GET['api_mock'] === 'list') {
        echo json_encode([
            'sucesso' => true,
            'dados' => [
                ['id_workflow' => 1, 'nome' => 'Workflow Exemplo 1', 'trigger_tipo' => 'manual', 'ativo' => true, 'total_execucoes' => 25, 'ultima_execucao_em' => date('Y-m-d H:i:s')],
                ['id_workflow' => 2, 'nome' => 'Workflow Exemplo 2', 'trigger_tipo' => 'cron', 'ativo' => false, 'total_execucoes' => 10, 'ultima_execucao_em' => date('Y-m-d H:i:s', strtotime('-1 day'))],
                ['id_workflow' => 3, 'nome' => 'Workflow Exemplo 3', 'trigger_tipo' => 'api_event', 'ativo' => true, 'total_execucoes' => 50, 'ultima_execucao_em' => date('Y-m-d H:i:s', strtotime('-2 hours'))]
            ]
        ]);
    }
    exit;
}

session_start();
$_SESSION['usuario'] = ['nome' => 'Teste'];
define('BASE_URL', '/DMC-DATALOAD/public');
$pageTitle = 'TESTE - Workflows';
$currentPage = 'workflows';

ob_start();
include __DIR__ . '/views/workflows.php';
$content = ob_get_clean();

$mockScript = <<<'MOCK'
<script>
const originalGetJSON = $.getJSON;
$.getJSON = function(url, success) {
    if (url.includes('/api/workflows/list')) return originalGetJSON('/DMC-DATALOAD/test_workflow_workflows.php?api_mock=list', success);
    return originalGetJSON.apply(this, arguments);
};
console.log('%c[MODO TESTE]', 'background: #10b981; color: white; padding: 4px;');
</script>
MOCK;

echo str_replace('</body>', $mockScript . '</body>', $content);
?>