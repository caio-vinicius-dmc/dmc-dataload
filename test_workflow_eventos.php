<?php
// MODO TESTE
if (isset($_GET['api_mock'])) {
    header('Content-Type: application/json');
    if ($_GET['api_mock'] === 'eventos') {
        echo json_encode([
            'sucesso' => true,
            'dados' => [
                ['id_evento' => 1, 'nome' => 'Evento 1', 'id_api' => 1, 'api_nome' => 'API Teste', 'jsonpath' => '$.status', 'operador' => '==', 'valor_comparacao' => 'success', 'acao' => 'workflow', 'ativo' => true, 'total_matches' => 15],
                ['id_evento' => 2, 'nome' => 'Evento 2', 'id_api' => 2, 'api_nome' => 'API Teste 2', 'jsonpath' => '$.error', 'operador' => '!=', 'valor_comparacao' => 'null', 'acao' => 'notificacao', 'ativo' => false, 'total_matches' => 3]
            ]
        ]);
    } elseif ($_GET['api_mock'] === 'apis') {
        echo json_encode(['sucesso' => true, 'dados' => [['id_api' => 1, 'nome' => 'API 1'], ['id_api' => 2, 'nome' => 'API 2']]]);
    }
    exit;
}

session_start();
$_SESSION['usuario'] = ['nome' => 'Teste'];
define('BASE_URL', '/DMC-DATALOAD/public');
$pageTitle = 'TESTE - Eventos';
$currentPage = 'eventos-api';

ob_start();
include __DIR__ . '/views/eventos-api.php';
$content = ob_get_clean();

$mockScript = <<<'MOCK'
<script>
const originalGetJSON = $.getJSON;
$.getJSON = function(url, success) {
    if (url.includes('/api/eventos-api/list')) return originalGetJSON('/DMC-DATALOAD/test_workflow_eventos.php?api_mock=eventos', success);
    if (url.includes('/api/apis-externas/list')) return originalGetJSON('/DMC-DATALOAD/test_workflow_eventos.php?api_mock=apis', success);
    return originalGetJSON.apply(this, arguments);
};
console.log('%c[MODO TESTE]', 'background: #10b981; color: white; padding: 4px;');
</script>
MOCK;

echo str_replace('</body>', $mockScript . '</body>', $content);
?>
