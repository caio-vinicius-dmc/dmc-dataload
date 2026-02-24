<?php
// MODO TESTE - Intercepta requisições API e retorna dados mockados
if (isset($_GET['api_mock'])) {
    header('Content-Type: application/json');
    
    if ($_GET['api_mock'] === 'list') {
        echo json_encode([
            'sucesso' => true,
            'dados' => [
                [
                    'id_api' => 1,
                    'nome' => 'API Exemplo 1',
                    'url' => 'https://api.exemplo.com/data',
                    'metodo' => 'GET',
                    'tipo_autenticacao' => 'bearer',
                    'ativo' => true,
                    'total_requisicoes' => 150,
                    'ultima_requisicao_em' => date('Y-m-d H:i:s')
                ],
                [
                    'id_api' => 2,
                    'nome' => 'API Exemplo 2',
                    'url' => 'https://api.exemplo.com/users',
                    'metodo' => 'POST',
                    'tipo_autenticacao' => 'basic',
                    'ativo' => false,
                    'total_requisicoes' => 85,
                    'ultima_requisicao_em' => date('Y-m-d H:i:s', strtotime('-2 hours'))
                ],
                [
                    'id_api' => 3,
                    'nome' => 'API Exemplo 3',
                    'url' => 'https://webhook.site/abc123',
                    'metodo' => 'PUT',
                    'tipo_autenticacao' => 'none',
                    'ativo' => true,
                    'total_requisicoes' => 42,
                    'ultima_requisicao_em' => date('Y-m-d H:i:s', strtotime('-5 minutes'))
                ]
            ]
        ]);
    }
    exit;
}

session_start();
$_SESSION['usuario'] = ['nome' => 'Teste'];
define('BASE_URL', '/DMC-DATALOAD/public');
$pageTitle = 'TESTE - APIs Externas';
$currentPage = 'apis-externas';

// Capturar saída da view
ob_start();
include __DIR__ . '/views/apis-externas.php';
$content = ob_get_clean();

// Injetar script para redirecionar chamadas API para modo mock
$mockScript = <<<'MOCK'
<script>
// MODO TESTE - Redirecionar chamadas API para dados mockados
(function() {
    const originalBaseUrl = baseUrl;
    
    // Override do jQuery getJSON para interceptar chamadas da API
    const originalGetJSON = $.getJSON;
    $.getJSON = function(url, success) {
        console.log('[MODO TESTE] Interceptando:', url);
        
        if (url.includes('/api/apis-externas/list')) {
            const mockUrl = '/DMC-DATALOAD/test_workflow_apis.php?api_mock=list';
            return originalGetJSON.call(this, mockUrl, success);
        }
        
        // Se não for uma rota mockada, usar comportamento original
        return originalGetJSON.apply(this, arguments);
    };
    
    console.log('%c[MODO TESTE ATIVO]', 'background: #10b981; color: white; padding: 4px 8px; border-radius: 4px;');
    console.log('APIs mockadas disponíveis. Dados de exemplo serão exibidos.');
})();
</script>
MOCK;

// Injetar antes do </body>
$content = str_replace('</body>', $mockScript . '</body>', $content);

echo $content;
?>
