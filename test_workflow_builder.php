<?php
// MODO TESTE
if (isset($_GET['api_mock'])) {
    header('Content-Type: application/json');
    if ($_GET['api_mock'] === 'rotinas') {
        echo json_encode(['sucesso' => true, 'dados' => [['id_rotina' => 1, 'nome' => 'Rotina Exemplo 1'], ['id_rotina' => 2, 'nome' => 'Rotina Exemplo 2']]]);
    }
    exit;
}

session_start();
$_SESSION['usuario'] = ['nome' => 'Teste'];
define('BASE_URL', '/DMC-DATALOAD/public');
$pageTitle = 'TESTE - Builder';
$currentPage = 'workflow-builder';

ob_start();
include __DIR__ . '/views/workflow-builder.php';
$content = ob_get_clean();

$mockScript = <<<'MOCK'
<script>
const originalGetJSON = $.getJSON;
$.getJSON = function(url, success) {
    if (url.includes('/api/rotinas/list')) return originalGetJSON('/DMC-DATALOAD/test_workflow_builder.php?api_mock=rotinas', success);
    return originalGetJSON.apply(this, arguments);
};
console.log('%c[MODO TESTE]', 'background: #10b981; color: white; padding: 4px;');
</script>
MOCK;

echo str_replace('</body>', $mockScript . '</body>', $content);
?>
