<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Core\AuthMiddleware;
use App\Core\ErrorHandler;
use App\Core\Logger;
use App\Controllers\ConexoesController;
use App\Controllers\RotinasController2 as RotinasController;
use App\Controllers\ApiController;
use App\Controllers\ApiExternaController;
use App\Controllers\WorkflowController;
use App\Controllers\PipelineController;
use App\Servicos\ServicoAutenticacao;

// Carregar .env
Database::loadEnv(__DIR__ . '/../');

// Inicializar error handler
ErrorHandler::inicializar();

// Iniciar sessão
AuthMiddleware::iniciarSessao();

// Detectar base path dinamicamente
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = dirname($scriptName);
if ($basePath === '\\' || $basePath === '/') {
    $basePath = '';
}

// Obter o path da requisição removendo o base path
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = $requestUri;

// Remover o base path se existir
if ($basePath && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// Garantir que começa com /
if (empty($path) || $path[0] !== '/') {
    $path = '/' . $path;
}

// Remover trailing slash (exceto para /)
if ($path !== '/' && substr($path, -1) === '/') {
    $path = rtrim($path, '/');
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Definir constante para uso nas views
define('BASE_URL', $basePath);

// Rotas públicas (sem autenticação)
$rotasPublicas = ['/', '/login', '/esqueci-senha', '/redefinir-senha', '/api/health', '/api/versao', '/api/metrics'];
$requerAutenticacao = !in_array($path, $rotasPublicas);

// Health check
if ($path === '/api/health') {
    ApiController::health();
    exit;
}

// Versão da API
if ($path === '/api/versao') {
    ApiController::versao();
    exit;
}

// Métricas Prometheus
if ($path === '/api/metrics') {
    ApiController::metrics();
    exit;
}

// Login
if ($path === '/login' && $method === 'GET') {
    include __DIR__ . '/../views/login.php';
    exit;
}

if ($path === '/login' && $method === 'POST') {
    header('Content-Type: application/json');
    try {
        $usuario = trim($_POST['usuario'] ?? '');
        $senha = $_POST['senha'] ?? '';
        
        $svc = new ServicoAutenticacao();
        $resultado = $svc->autenticar($usuario, $senha);
        
        if ($resultado['sucesso']) {
            AuthMiddleware::definirUsuario($resultado['usuario']);
            \App\Servicos\ServicoAuditoria::registrar('login', 'sessao', $resultado['usuario']['id'] ?? null, $usuario);
            echo json_encode(['sucesso' => true]);
        } else {
            echo json_encode(['sucesso' => false, 'erro' => $resultado['mensagem']]);
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(ErrorHandler::tratarErro($e, 'Erro ao autenticar'));
    }
    exit;
}

// Esqueci minha senha
if ($path === '/esqueci-senha' && $method === 'GET') {
    include __DIR__ . '/../views/esqueci-senha.php';
    exit;
}

if ($path === '/esqueci-senha' && $method === 'POST') {
    header('Content-Type: application/json');
    try {
        $identificador = trim($_POST['identificador'] ?? '');
        if (empty($identificador)) {
            echo json_encode(['sucesso' => false, 'erro' => 'Informe usuário ou e-mail']);
            exit;
        }
        $svc = new \App\Servicos\ServicoRecuperacaoSenha();
        $resultado = $svc->solicitar($identificador);
        echo json_encode($resultado);
    } catch (\Exception $e) {
        echo json_encode(['sucesso' => false, 'erro' => 'Erro ao processar solicitação']);
    }
    exit;
}

// Redefinir senha
if ($path === '/redefinir-senha' && $method === 'GET') {
    include __DIR__ . '/../views/redefinir-senha.php';
    exit;
}

if ($path === '/redefinir-senha' && $method === 'POST') {
    header('Content-Type: application/json');
    try {
        $token = $_POST['token'] ?? '';
        $chaveHex = $_POST['chave_hex'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        if (empty($token) || empty($chaveHex) || empty($novaSenha)) {
            echo json_encode(['sucesso' => false, 'erro' => 'Todos os campos são obrigatórios']);
            exit;
        }
        $svc = new \App\Servicos\ServicoRecuperacaoSenha();
        $resultado = $svc->redefinirSenha($token, $chaveHex, $novaSenha);
        echo json_encode($resultado);
    } catch (\Exception $e) {
        echo json_encode(['sucesso' => false, 'erro' => 'Erro ao redefinir senha']);
    }
    exit;
}

// Logout
if ($path === '/logout' && $method === 'POST') {
    \App\Servicos\ServicoAuditoria::registrar('logout', 'sessao');
    AuthMiddleware::destruirSessao();
    header('Content-Type: application/json');
    echo json_encode(['sucesso' => true]);
    exit;
}

// API de sessão
if ($path === '/api/sessao' && $method === 'GET') {
    header('Content-Type: application/json');
    if (AuthMiddleware::verificarAutenticacao()) {
        echo json_encode([
            'autenticado' => true,
            'usuario' => AuthMiddleware::obterUsuario()
        ]);
    } else {
        echo json_encode(['autenticado' => false]);
    }
    exit;
}

// Middleware de autenticação para rotas protegidas
if ($requerAutenticacao && !AuthMiddleware::verificarAutenticacao()) {
    // Rotas que respondem JSON (APIs e endpoints AJAX)
    $isApiRoute = strpos($path, '/api/') === 0 
        || strpos($path, '/conexoes/') === 0
        || strpos($path, '/rotinas/') === 0
        || strpos($path, '/workflows/') === 0
        || strpos($path, '/pipelines/') === 0
        || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
    
    if ($isApiRoute) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Não autenticado']);
    } else {
        header('Location: ' . BASE_URL . '/login');
    }
    exit;
}

// Restrição do Operador: bloquear páginas não permitidas
if ($requerAutenticacao && AuthMiddleware::verificarAutenticacao()) {
    $usuario = AuthMiddleware::obterUsuario();
    if (($usuario['nivel_acesso'] ?? '') === 'operador') {
        $isApiRoute = (strpos($path, '/api/') === 0 || strpos($path, '/conexoes/') === 0 
            || strpos($path, '/rotinas/') === 0 || strpos($path, '/pipelines/') === 0
            || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'));
        
        if ($isApiRoute) {
            // Para APIs: operador só pode GET em rotas permitidas
            if (!\App\Servicos\ServicoPermissao::operadorPodeAcessarApi($path, $method)) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['erro' => 'Acesso negado. Operadores têm acesso somente leitura.', 'sucesso' => false]);
                exit;
            }
        } else {
            // Para páginas: operador só pode acessar as permitidas
            if (!\App\Servicos\ServicoPermissao::operadorPodeAcessarPagina($path)) {
                http_response_code(403);
                echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Acesso Negado</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                </head><body class="bg-light d-flex align-items-center justify-content-center vh-100">
                <div class="text-center"><h1 class="text-danger">403</h1><h3>Acesso Negado</h3>
                <p class="text-muted">Operadores só podem acessar: Dashboard, Histórico, Diagrama, Scheduler e Calendário.</p>
                <a href="' . BASE_URL . '/dashboard" class="btn btn-primary mt-3">Ir para Dashboard</a></div></body></html>';
                exit;
            }
        }
    }
}

// Dashboard
if ($path === '/' || $path === '/dashboard') {
    include __DIR__ . '/../views/dashboard.php';
    exit;
}

// API Dashboard métricas
if ($path === '/api/dashboard/metricas' && $method === 'GET') {
    try {
        $db = Database::getConexao();
        
        // ========= RBAC: pré-computar IDs visíveis por tipo de recurso =========
        $fRot = \App\Servicos\ServicoPermissao::filtroVisibilidadePosicional('rotina', 'r', 'id_usuario_criador');
        $stmt = $db->prepare("SELECT r.id FROM tb_rotinas r WHERE ({$fRot['where']})");
        $stmt->execute($fRot['params']);
        $rotIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $fPip = \App\Servicos\ServicoPermissao::filtroVisibilidadePosicional('pipeline', 'p', 'criado_por');
        $stmt = $db->prepare("SELECT p.id FROM tb_pipelines p WHERE ({$fPip['where']})");
        $stmt->execute($fPip['params']);
        $pipIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $fWf = \App\Servicos\ServicoPermissao::filtroVisibilidadePosicional('workflow', 'w', 'criado_por');
        $stmt = $db->prepare("SELECT w.id FROM tb_workflows w WHERE ({$fWf['where']})");
        $stmt->execute($fWf['params']);
        $wfIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Helper: cria cláusula IN segura (intval previne injection)
        $inSql = function(array $ids): string {
            if (empty($ids)) return '(0)';
            return '(' . implode(',', array_map('intval', $ids)) . ')';
        };
        $rotIn = $inSql($rotIds);
        $pipIn = $inSql($pipIds);
        $wfIn  = $inSql($wfIds);
        
        // Total de rotinas visíveis
        $total = $db->query("SELECT COUNT(*) FROM tb_rotinas WHERE id IN {$rotIn}")->fetchColumn();
        
        // Em execução (filtrado)
        $emExec = $db->query("
            SELECT (SELECT COUNT(*) FROM tb_rotinas WHERE esta_executando = true AND id IN {$rotIn})
                 + (SELECT COUNT(*) FROM tb_pipeline_execucoes WHERE status = 'running' AND id_pipeline IN {$pipIn})
                 + (SELECT COUNT(*) FROM tb_workflow_execucoes WHERE status = 'running' AND id_workflow IN {$wfIn})
        ")->fetchColumn();
        
        // Execuções hoje (filtrado)
        $execHoje = $db->query("
            SELECT (SELECT COUNT(*) FROM tb_logs_execucao WHERE data_inicio >= CURRENT_DATE AND id_rotina IN {$rotIn})
                 + (SELECT COUNT(*) FROM tb_pipeline_execucoes WHERE data_inicio >= CURRENT_DATE AND id_pipeline IN {$pipIn})
                 + (SELECT COUNT(*) FROM tb_workflow_execucoes WHERE data_inicio >= CURRENT_DATE AND id_workflow IN {$wfIn})
        ")->fetchColumn();
        
        // Falhas hoje (filtrado)
        $falhasHoje = $db->query("
            SELECT (SELECT COUNT(*) FROM tb_logs_execucao WHERE status IN ('falha','erro') AND data_inicio >= CURRENT_DATE AND id_rotina IN {$rotIn})
                 + (SELECT COUNT(*) FROM tb_pipeline_execucoes WHERE status = 'error' AND data_inicio >= CURRENT_DATE AND id_pipeline IN {$pipIn})
                 + (SELECT COUNT(*) FROM tb_workflow_execucoes WHERE status = 'failed' AND data_inicio >= CURRENT_DATE AND id_workflow IN {$wfIn})
        ")->fetchColumn();
        
        // Rotinas ativas (agendadas, filtrado)
        $ativas = $db->query("SELECT COUNT(*) FROM tb_rotinas WHERE ativa = true AND agendamento_cron IS NOT NULL AND id IN {$rotIn}")->fetchColumn();
        
        // Próximas execuções (5, filtrado)
        $proximas = $db->query("SELECT r.id, r.nome, r.proxima_execucao, r.agendamento_cron, p.nome_conexao as conexao, 'rotina' as tipo
            FROM tb_rotinas r 
            LEFT JOIN tb_perfis_conexao p ON r.id_conexao = p.id
            WHERE r.ativa = true AND r.proxima_execucao IS NOT NULL AND r.id IN {$rotIn}
            ORDER BY r.proxima_execucao ASC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        
        // Incluir pipelines com cron (filtrado)
        $proximasPip = $db->query("SELECT id, nome, agendamento_cron, 'pipeline' as tipo
            FROM tb_pipelines
            WHERE ativo = true AND trigger_tipo = 'cron' AND agendamento_cron IS NOT NULL AND agendamento_cron != '' AND id IN {$pipIn}
            ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
        
        // Calcular próxima execução para pipelines
        $schedulerCtrl = new \App\Controllers\SchedulerController();
        foreach ($proximasPip as &$pip) {
            $pip['proxima_execucao'] = $schedulerCtrl->calcularProximaExecucao($pip['agendamento_cron']);
            $pip['conexao'] = null;
        }
        unset($pip);
        $proximasPip = array_filter($proximasPip, fn($p) => $p['proxima_execucao'] !== null);
        
        // Mesclar e ordenar por próxima execução
        $todasProximas = array_merge($proximas, $proximasPip);
        usort($todasProximas, fn($a, $b) => strcmp($a['proxima_execucao'] ?? '', $b['proxima_execucao'] ?? ''));
        $todasProximas = array_slice($todasProximas, 0, 5);
        
        // Total de pipelines e workflows visíveis
        $totalPipelines = $db->query("SELECT COUNT(*) FROM tb_pipelines WHERE id IN {$pipIn}")->fetchColumn();
        $pipelinesAtivos = $db->query("SELECT COUNT(*) FROM tb_pipelines WHERE ativo = true AND trigger_tipo = 'cron' AND id IN {$pipIn}")->fetchColumn();
        $totalWorkflows = $db->query("SELECT COUNT(*) FROM tb_workflows WHERE id IN {$wfIn}")->fetchColumn();
        $workflowsAtivos = $db->query("SELECT COUNT(*) FROM tb_workflows WHERE ativo = true AND id IN {$wfIn}")->fetchColumn();
        
        // Últimas execuções (10, filtrado)
        $ultimas = $db->query("
            (SELECT l.id, l.status, l.data_inicio, l.duracao_ms, r.nome as rotina, 'rotina' as tipo_execucao
            FROM tb_logs_execucao l LEFT JOIN tb_rotinas r ON l.id_rotina = r.id
            WHERE l.id_rotina IN {$rotIn})
            UNION ALL
            (SELECT pe.id, 
                CASE pe.status WHEN 'success' THEN 'sucesso' WHEN 'error' THEN 'falha' WHEN 'running' THEN 'executando' WHEN 'cancelled' THEN 'cancelado' ELSE pe.status END,
                pe.data_inicio, pe.duracao_ms, p.nome, 'pipeline'
            FROM tb_pipeline_execucoes pe LEFT JOIN tb_pipelines p ON pe.id_pipeline = p.id
            WHERE pe.id_pipeline IN {$pipIn})
            UNION ALL
            (SELECT we.id, 
                CASE we.status WHEN 'completed' THEN 'sucesso' WHEN 'failed' THEN 'falha' WHEN 'running' THEN 'executando' WHEN 'cancelled' THEN 'cancelado' WHEN 'paused' THEN 'pausado' ELSE we.status END,
                we.data_inicio, we.duracao_ms, w.nome, 'workflow'
            FROM tb_workflow_execucoes we LEFT JOIN tb_workflows w ON we.id_workflow = w.id
            WHERE we.id_workflow IN {$wfIn})
            ORDER BY data_inicio DESC LIMIT 10
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        // Dados para gráfico (últimos 7 dias, filtrado)
        $grafico = $db->query("SELECT data,
            SUM(sucesso) as sucesso, SUM(falha) as falha
            FROM (
                SELECT DATE(data_inicio) as data,
                    COUNT(*) FILTER (WHERE status = 'sucesso') as sucesso,
                    COUNT(*) FILTER (WHERE status IN ('falha','erro')) as falha
                FROM tb_logs_execucao WHERE data_inicio >= CURRENT_DATE - INTERVAL '7 days' AND id_rotina IN {$rotIn}
                GROUP BY DATE(data_inicio)
                UNION ALL
                SELECT DATE(data_inicio),
                    COUNT(*) FILTER (WHERE status = 'success'),
                    COUNT(*) FILTER (WHERE status = 'error')
                FROM tb_pipeline_execucoes WHERE data_inicio >= CURRENT_DATE - INTERVAL '7 days' AND id_pipeline IN {$pipIn}
                GROUP BY DATE(data_inicio)
                UNION ALL
                SELECT DATE(data_inicio),
                    COUNT(*) FILTER (WHERE status = 'completed'),
                    COUNT(*) FILTER (WHERE status = 'failed')
                FROM tb_workflow_execucoes WHERE data_inicio >= CURRENT_DATE - INTERVAL '7 days' AND id_workflow IN {$wfIn}
                GROUP BY DATE(data_inicio)
            ) combined
            GROUP BY data ORDER BY data ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode([
            'sucesso' => true,
            'total_rotinas' => (int)$total,
            'execucoes_hoje' => (int)$execHoje,
            'falhas_hoje' => (int)$falhasHoje,
            'em_execucao' => (int)$emExec,
            'rotinas_ativas' => (int)$ativas,
            'total_pipelines' => (int)$totalPipelines,
            'pipelines_agendados' => (int)$pipelinesAtivos,
            'total_workflows' => (int)$totalWorkflows,
            'workflows_ativos' => (int)$workflowsAtivos,
            'proximas_execucoes' => $todasProximas,
            'ultimas_execucoes' => $ultimas,
            'grafico_7dias' => $grafico
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(ErrorHandler::tratarErro($e));
    }
    exit;
}

// Rotas simples
if ($path === '/conexoes/test' && $method === 'POST') {
    // Validar CSRF token
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $data = $_POST;
    unset($data['_csrf_token']);
    $c = new ConexoesController();
    header('Content-Type: application/json');
    echo json_encode($c->testarConexao($data));
    exit;
}

// Testar conexão existente por ID (sem expor senha)
if (preg_match('#^/conexoes/test/(\d+)$#', $path, $m) && $method === 'POST') {
    // Validar CSRF token
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $id = intval($m[1]);
    $c = new ConexoesController();
    header('Content-Type: application/json');
    echo json_encode($c->testarConexaoPorId($id));
    exit;
}

if ($path === '/conexoes/salvar' && $method === 'POST') {
    // Validar CSRF token
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $data = $_POST;
    unset($data['_csrf_token']);
    $c = new ConexoesController();
    header('Content-Type: application/json');
    $resultado = $c->salvar($data);
    \App\Servicos\ServicoAuditoria::registrar(
        isset($data['id']) && $data['id'] ? 'editar' : 'criar',
        'conexao',
        (int)($resultado['id'] ?? $data['id'] ?? 0),
        $data['nome'] ?? ''
    );
    echo json_encode($resultado);
    exit;
}

if ($path === '/conexoes/list' && $method === 'GET') {
    $c = new ConexoesController();
    header('Content-Type: application/json');
    echo json_encode($c->listar());
    exit;
}

if (preg_match('#^/conexoes/get/(\d+)$#', $path, $m) && $method === 'GET') {
    $id = intval($m[1]);
    $c = new ConexoesController();
    header('Content-Type: application/json');
    $resultado = $c->buscar($id);
    $resultado['empresas'] = \App\Servicos\ServicoPermissao::obterEmpresasDoRecurso('conexao', $id);
    $resultado['projetos'] = \App\Servicos\ServicoPermissao::obterProjetosDoRecurso('conexao', $id);
    echo json_encode($resultado);
    exit;
}

if (preg_match('#^/conexoes/delete/(\d+)$#', $path, $m) && $method === 'POST') {
    // Validar CSRF token
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $id = intval($m[1]);
    header('Content-Type: application/json');
    if (!\App\Servicos\ServicoPermissao::podeModificarRecurso('conexao', $id)) {
        http_response_code(403);
        echo json_encode(['erro' => 'Sem permissão para excluir esta conexão', 'sucesso' => false]);
        exit;
    }
    $c = new ConexoesController();
    \App\Servicos\ServicoAuditoria::registrar('excluir', 'conexao', $id);
    echo json_encode($c->deletar($id));
    exit;
}

if ($path === '/conexoes/drivers-status' && $method === 'GET') {
    $c = new ConexoesController();
    header('Content-Type: application/json');
    echo json_encode($c->driversStatus());
    exit;
}

if (preg_match('#^/conexoes/driver-install-info/(\w+)$#', $path, $m) && $method === 'GET') {
    $tipoBanco = $m[1];
    $c = new ConexoesController();
    header('Content-Type: application/json');
    echo json_encode($c->driverInstallInfo($tipoBanco));
    exit;
}

if (preg_match('#^/conexoes/install-driver/(\w+)$#', $path, $m) && $method === 'POST') {
    // Validar CSRF token
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $tipoBanco = $m[1];
    $c = new ConexoesController();
    header('Content-Type: application/json');
    echo json_encode($c->installDriver($tipoBanco));
    exit;
}

if (preg_match('#^/rotinas/run/(\d+)$#', $path, $m) && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    // Rate limiting: max 10 execuções por minuto por sessão
    $rateLimiter = new \App\Core\RateLimiter();
    $sessionId = session_id() ?: 'anonymous';
    if (!$rateLimiter->permitir("exec_rotina:{$sessionId}", 10, 60)) {
        http_response_code(429);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Limite de execuções excedido. Aguarde 1 minuto.', 'sucesso' => false]);
        exit;
    }
    $id = intval($m[1]);
    $r = new RotinasController();
    header('Content-Type: application/json');
    echo json_encode($r->executar($id));
    exit;
}

if ($path === '/rotinas/list' && $method === 'GET') {
    $c = new RotinasController();
    header('Content-Type: application/json');
    echo json_encode($c->listar());
    exit;
}

if (preg_match('#^/rotinas/get/(\d+)$#', $path, $m) && $method === 'GET') {
    $id = intval($m[1]);
    $c = new RotinasController();
    header('Content-Type: application/json');
    $resultado = $c->buscar($id);
    $resultado['empresas'] = \App\Servicos\ServicoPermissao::obterEmpresasDoRecurso('rotina', $id);
    $resultado['projetos'] = \App\Servicos\ServicoPermissao::obterProjetosDoRecurso('rotina', $id);
    echo json_encode($resultado);
    exit;
}

if ($path === '/rotinas/salvar' && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $data = $_POST;
    unset($data['_csrf_token']);
    $c = new RotinasController();
    header('Content-Type: application/json');
    $resultado = $c->salvar($data);
    \App\Servicos\ServicoAuditoria::registrar(
        isset($data['id']) && $data['id'] ? 'editar' : 'criar',
        'rotina', (int)($resultado['id'] ?? $data['id'] ?? 0), $data['nome'] ?? ''
    );
    echo json_encode($resultado);
    exit;
}

if (preg_match('#^/rotinas/delete/(\d+)$#', $path, $m) && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $id = intval($m[1]);
    header('Content-Type: application/json');
    if (!\App\Servicos\ServicoPermissao::podeModificarRecurso('rotina', $id)) {
        http_response_code(403);
        echo json_encode(['erro' => 'Sem permissão para excluir esta rotina', 'sucesso' => false]);
        exit;
    }
    \App\Servicos\ServicoAuditoria::registrar('excluir', 'rotina', $id);
    $c = new RotinasController();
    echo json_encode($c->deletar($id));
    exit;
}

// Toggle ativa rotina
if (preg_match('#^/rotinas/toggle/(\d+)$#', $path, $m) && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $id = intval($m[1]);
    $c = new RotinasController();
    header('Content-Type: application/json');
    echo json_encode($c->toggleAtiva($id));
    exit;
}

// Estatísticas rotina
if (preg_match('#^/rotinas/stats/(\d+)$#', $path, $m) && $method === 'GET') {
    $id = intval($m[1]);
    $c = new RotinasController();
    header('Content-Type: application/json');
    echo json_encode($c->estatisticas($id));
    exit;
}

// fallback - homepage mínima
if ($path === '/conexoes') {
    include __DIR__ . '/../views/conexoes.php';
    exit;
}

if ($path === '/drivers-status') {
    include __DIR__ . '/../views/drivers_status.php';
    exit;
}

if ($path === '/rotinas') {
    include __DIR__ . '/../views/rotinas.php';
    exit;
}

if ($path === '/rotinas/editor') {
    include __DIR__ . '/../views/rotinas/editor.php';
    exit;
}

if ($path === '/rotinas/cadastro') {
    include __DIR__ . '/../views/rotinas/cadastro.php';
    exit;
}

// Histórico de execuções
if ($path === '/historico') {
    include __DIR__ . '/../views/historico.php';
    exit;
}

// ========== NOVAS VIEWS ==========

// Calendário
if ($path === '/calendario') {
    include __DIR__ . '/../views/calendario.php';
    exit;
}

// Scheduler / Agendamentos
if ($path === '/scheduler' || $path === '/agendamentos') {
    include __DIR__ . '/../views/scheduler.php';
    exit;
}

// Meu Perfil
if ($path === '/meu-perfil') {
    include __DIR__ . '/../views/meu-perfil.php';
    exit;
}

// Logs do Sistema
if ($path === '/logs') {
    include __DIR__ . '/../views/logs.php';
    exit;
}

// Configurações
if ($path === '/configuracoes') {
    \App\Servicos\ServicoPermissao::exigirNivel('super_admin');
    include __DIR__ . '/../views/configuracoes.php';
    exit;
}

// Admin - Usuários
if ($path === '/admin/usuarios') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    include __DIR__ . '/../views/admin/usuarios.php';
    exit;
}

// Admin - Empresas
if ($path === '/admin/empresas') {
    \App\Servicos\ServicoPermissao::exigirNivel('super_admin');
    include __DIR__ . '/../views/admin/empresas.php';
    exit;
}

// Admin - Projetos
if ($path === '/admin/projetos') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    include __DIR__ . '/../views/admin/projetos.php';
    exit;
}

// Admin - Auditoria
if ($path === '/admin/auditoria') {
    \App\Servicos\ServicoPermissao::exigirNivel('super_admin');
    include __DIR__ . '/../views/admin/auditoria.php';
    exit;
}

// Admin - Webhooks
if ($path === '/admin/webhooks') {
    \App\Servicos\ServicoPermissao::exigirNivel('super_admin');
    include __DIR__ . '/../views/admin/webhooks.php';
    exit;
}

// SQL Editor
if ($path === '/sql-editor') {
    include __DIR__ . '/../views/sql_editor.php';
    exit;
}

// Diagrama de Banco de Dados
if ($path === '/diagrama') {
    include __DIR__ . '/../views/diagrama.php';
    exit;
}

// ========== NOVAS ROTAS API ==========

// API Cron - Presets
if ($path === '/api/cron/presets' && $method === 'GET') {
    ApiController::cronPresets();
    exit;
}

// API Cron - Validar
if ($path === '/api/cron/validar' && ($method === 'GET' || $method === 'POST')) {
    ApiController::validarCron();
    exit;
}

// API Logs do sistema
if ($path === '/api/logs' && $method === 'GET') {
    ApiController::logs();
    exit;
}

// API Estatísticas de logs
if ($path === '/api/logs/estatisticas' && $method === 'GET') {
    ApiController::estatisticasLogs();
    exit;
}

// API Status dos workers
if ($path === '/api/workers/status' && $method === 'GET') {
    ApiController::workersStatus();
    exit;
}

// ========== API SCHEDULER ==========

if ($path === '/api/scheduler/rotinas' && $method === 'GET') {
    $c = new \App\Controllers\SchedulerController();
    $c->getRotinasAgendadas();
    exit;
}

if ($path === '/api/scheduler/status' && $method === 'GET') {
    $c = new \App\Controllers\SchedulerController();
    $c->getStatus();
    exit;
}

if ($path === '/api/scheduler/start' && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new \App\Controllers\SchedulerController();
    $c->start();
    exit;
}

if ($path === '/api/scheduler/stop' && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new \App\Controllers\SchedulerController();
    $c->stop();
    exit;
}

if ($path === '/api/scheduler/toggle' && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new \App\Controllers\SchedulerController();
    $c->toggle();
    exit;
}

if ($path === '/api/scheduler/atualizar' && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new \App\Controllers\SchedulerController();
    $c->atualizar();
    exit;
}

if ($path === '/api/scheduler/logs' && $method === 'GET') {
    $c = new \App\Controllers\SchedulerController();
    $c->getLogs();
    exit;
}

if ($path === '/api/scheduler/salvar' && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new \App\Controllers\SchedulerController();
    $c->salvar();
    exit;
}

// ========== API SQL EDITOR ==========

if (preg_match('#^/sql-editor/connect/(\d+)$#', $path, $m) && $method === 'GET') {
    $id = intval($m[1]);
    $c = new \App\Controllers\SqlEditorController();
    header('Content-Type: application/json');
    echo json_encode($c->connect($id));
    exit;
}

if (preg_match('#^/sql-editor/objects/(\d+)$#', $path, $m) && $method === 'GET') {
    $id = intval($m[1]);
    $c = new \App\Controllers\SqlEditorController();
    header('Content-Type: application/json');
    echo json_encode($c->getObjects($id));
    exit;
}

if (preg_match('#^/sql-editor/metadata/(\d+)$#', $path, $m) && $method === 'GET') {
    $id = intval($m[1]);
    $c = new \App\Controllers\SqlEditorController();
    header('Content-Type: application/json');
    echo json_encode($c->getMetadata($id));
    exit;
}

// SQL Editor - Lazy Loading APIs
if (preg_match('#^/sql-editor/tables/(\d+)/(.+)$#', $path, $m) && $method === 'GET') {
    $id = intval($m[1]);
    $schema = urldecode($m[2]);
    $c = new \App\Controllers\SqlEditorController();
    header('Content-Type: application/json');
    echo json_encode($c->getTables($id, $schema));
    exit;
}

if (preg_match('#^/sql-editor/views/(\d+)/(.+)$#', $path, $m) && $method === 'GET') {
    $id = intval($m[1]);
    $schema = urldecode($m[2]);
    $c = new \App\Controllers\SqlEditorController();
    header('Content-Type: application/json');
    echo json_encode($c->getViews($id, $schema));
    exit;
}

if (preg_match('#^/sql-editor/functions/(\d+)/(.+)$#', $path, $m) && $method === 'GET') {
    $id = intval($m[1]);
    $schema = urldecode($m[2]);
    $c = new \App\Controllers\SqlEditorController();
    header('Content-Type: application/json');
    echo json_encode($c->getFunctions($id, $schema));
    exit;
}

if (preg_match('#^/sql-editor/procedures/(\d+)/(.+)$#', $path, $m) && $method === 'GET') {
    $id = intval($m[1]);
    $schema = urldecode($m[2]);
    $c = new \App\Controllers\SqlEditorController();
    header('Content-Type: application/json');
    echo json_encode($c->getProcedures($id, $schema));
    exit;
}

if (preg_match('#^/sql-editor/packages/(\d+)/(.+)$#', $path, $m) && $method === 'GET') {
    $id = intval($m[1]);
    $schema = urldecode($m[2]);
    $c = new \App\Controllers\SqlEditorController();
    header('Content-Type: application/json');
    echo json_encode($c->getPackages($id, $schema));
    exit;
}

if ($path === '/sql-editor/execute' && $method === 'POST') {
    $data = $_POST;
    $c = new \App\Controllers\SqlEditorController();
    header('Content-Type: application/json');
    echo json_encode($c->execute($data));
    exit;
}

// ========== API DIAGRAMA ==========

if (preg_match('#^/diagrama/estrutura/(\d+)$#', $path, $m) && $method === 'GET') {
    $id = intval($m[1]);
    $c = new \App\Controllers\DiagramaController();
    header('Content-Type: application/json');
    echo json_encode($c->getEstrutura($id));
    exit;
}

if (preg_match('#^/diagrama/estrutura-tabela/(\d+)/([^/]+)/([^/]+)$#', $path, $m) && $method === 'GET') {
    $id = intval($m[1]);
    $schema = urldecode($m[2]);
    $tabela = urldecode($m[3]);
    $c = new \App\Controllers\DiagramaController();
    header('Content-Type: application/json');
    echo json_encode($c->getEstruturaTabela($id, $schema, $tabela));
    exit;
}

if (preg_match('#^/diagrama/tabelas/(\d+)$#', $path, $m) && $method === 'GET') {
    $id = intval($m[1]);
    $c = new \App\Controllers\DiagramaController();
    header('Content-Type: application/json');
    echo json_encode($c->listarTabelas($id));
    exit;
}

if (preg_match('#^/diagrama/posicoes/(\d+)$#', $path, $m) && $method === 'GET') {
    $id = intval($m[1]);
    $c = new \App\Controllers\DiagramaController();
    header('Content-Type: application/json');
    echo json_encode($c->carregarPosicoes($id));
    exit;
}

if (preg_match('#^/diagrama/posicoes/(\d+)$#', $path, $m) && $method === 'POST') {
    $id = intval($m[1]);
    $posicoes = json_decode(file_get_contents('php://input'), true) ?? [];
    $c = new \App\Controllers\DiagramaController();
    header('Content-Type: application/json');
    echo json_encode($c->salvarPosicoes($id, $posicoes));
    exit;
}

if (preg_match('#^/api/scheduler/detalhes/(\d+)$#', $path, $matches) && $method === 'GET') {
    $_GET['id'] = $matches[1];
    $c = new \App\Controllers\SchedulerController();
    $c->detalhes();
    exit;
}

if ($path === '/api/scheduler/excluir' && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new \App\Controllers\SchedulerController();
    $c->excluir();
    exit;
}

// ========== API CALENDÁRIO ==========

if ($path === '/api/calendario/eventos' && $method === 'GET') {
    $c = new \App\Controllers\CalendarioController();
    $c->getEventos();
    exit;
}

// ========== API USUÁRIOS (ADMIN) ==========

if ($path === '/admin/usuarios/list' && $method === 'GET') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $c = new \App\Controllers\UsersController();
    $c->listar();
    exit;
}

if ($path === '/admin/usuarios/salvar' && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new \App\Controllers\UsersController();
    $c->salvar();
    exit;
}

if (preg_match('#^/admin/usuarios/get/(\d+)$#', $path, $m) && $method === 'GET') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $c = new \App\Controllers\UsersController();
    $c->get((int)$m[1]);
    exit;
}

if (preg_match('#^/admin/usuarios/delete/(\d+)$#', $path, $m) && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new \App\Controllers\UsersController();
    $c->delete((int)$m[1]);
    exit;
}

if ($path === '/admin/usuarios/reset-senha' && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new \App\Controllers\UsersController();
    $c->resetSenha();
    exit;
}

if ($path === '/admin/usuarios/reset-senha-email' && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new \App\Controllers\UsersController();
    $c->resetSenhaEmail();
    exit;
}

if ($path === '/admin/usuarios/desbloquear' && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new \App\Controllers\UsersController();
    $c->desbloquearUsuario();
    exit;
}

// ========== API PERFIL (PRÓPRIO USUÁRIO) ==========

if ($path === '/api/perfil' && $method === 'GET') {
    $c = new \App\Controllers\UsersController();
    $c->meuPerfil();
    exit;
}

if ($path === '/api/perfil/atualizar' && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new \App\Controllers\UsersController();
    $c->atualizarPerfil();
    exit;
}

if ($path === '/api/perfil/alterar-senha' && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new \App\Controllers\UsersController();
    $c->alterarMinhaSenha();
    exit;
}

if ($path === '/api/perfil/solicitar-reset-email' && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    header('Content-Type: application/json');
    try {
        $usuario = AuthMiddleware::obterUsuario();
        $svc = new \App\Servicos\ServicoRecuperacaoSenha();
        $resultado = $svc->solicitarPorAdmin((int)$usuario['id']);
        echo json_encode($resultado);
    } catch (\Exception $e) {
        echo json_encode(['sucesso' => false, 'erro' => 'Erro ao processar solicitação']);
    }
    exit;
}

// ========== API EMPRESAS (ADMIN) ==========

if ($path === '/admin/empresas/list' && $method === 'GET') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $c = new \App\Controllers\EmpresasController();
    $c->listar();
    exit;
}

if ($path === '/admin/empresas/salvar' && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('super_admin');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new \App\Controllers\EmpresasController();
    $c->salvar();
    exit;
}

if (preg_match('#^/admin/empresas/get/(\d+)$#', $path, $m) && $method === 'GET') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $c = new \App\Controllers\EmpresasController();
    $c->get((int)$m[1]);
    exit;
}

if (preg_match('#^/admin/empresas/delete/(\d+)$#', $path, $m) && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('super_admin');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new \App\Controllers\EmpresasController();
    $c->delete((int)$m[1]);
    exit;
}

// ========== API PROJETOS (ADMIN) ==========

if ($path === '/admin/projetos/list' && $method === 'GET') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $c = new \App\Controllers\ProjetosController();
    $c->listar();
    exit;
}

if ($path === '/admin/projetos/salvar' && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new \App\Controllers\ProjetosController();
    $c->salvar();
    exit;
}

if (preg_match('#^/admin/projetos/get/(\d+)$#', $path, $m) && $method === 'GET') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $c = new \App\Controllers\ProjetosController();
    $c->get((int)$m[1]);
    exit;
}

if (preg_match('#^/admin/projetos/delete/(\d+)$#', $path, $m) && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new \App\Controllers\ProjetosController();
    $c->delete((int)$m[1]);
    exit;
}

// ========== API PERMISSÕES/COMPARTILHAMENTOS ==========

if ($path === '/api/permissoes/empresas-usuario' && $method === 'GET') {
    header('Content-Type: application/json');
    echo json_encode(['sucesso' => true, 'dados' => \App\Servicos\ServicoPermissao::empresasDisponiveisParaAdmin()]);
    exit;
}

if ($path === '/api/permissoes/projetos-usuario' && $method === 'GET') {
    header('Content-Type: application/json');
    $idsEmpresas = isset($_GET['empresas']) ? array_map('intval', explode(',', $_GET['empresas'])) : null;
    echo json_encode(['sucesso' => true, 'dados' => \App\Servicos\ServicoPermissao::projetosDisponiveisParaAdmin($idsEmpresas)]);
    exit;
}

if ($path === '/api/permissoes/papeis-disponiveis' && $method === 'GET') {
    header('Content-Type: application/json');
    echo json_encode(['sucesso' => true, 'dados' => \App\Servicos\ServicoPermissao::papeisDisponiveis()]);
    exit;
}

if ($path === '/api/compartilhamentos/salvar' && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('desenvolvedor');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    header('Content-Type: application/json');
    $tipo = $_POST['tipo_recurso'] ?? '';
    $idRecurso = !empty($_POST['id_recurso']) ? (int)$_POST['id_recurso'] : null;
    $idDestino = (int)($_POST['id_usuario_destino'] ?? 0);
    $permissao = $_POST['permissao'] ?? 'ver';
    if (!$tipo || !$idDestino) {
        echo json_encode(['sucesso' => false, 'erro' => 'Dados incompletos']);
        exit;
    }
    $ok = \App\Servicos\ServicoPermissao::compartilharRecurso($tipo, $idRecurso, $idDestino, $permissao);
    echo json_encode(['sucesso' => $ok, 'mensagem' => $ok ? 'Compartilhado com sucesso' : 'Erro ao compartilhar']);
    exit;
}

if ($path === '/api/compartilhamentos/remover' && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('desenvolvedor');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    header('Content-Type: application/json');
    $tipo = $_POST['tipo_recurso'] ?? '';
    $idRecurso = !empty($_POST['id_recurso']) ? (int)$_POST['id_recurso'] : null;
    $idDestino = (int)($_POST['id_usuario_destino'] ?? 0);
    $ok = \App\Servicos\ServicoPermissao::removerCompartilhamento($tipo, $idRecurso, $idDestino);
    echo json_encode(['sucesso' => $ok]);
    exit;
}

if ($path === '/api/compartilhamentos/listar' && $method === 'GET') {
    header('Content-Type: application/json');
    $tipo = $_GET['tipo_recurso'] ?? '';
    $idRecurso = !empty($_GET['id_recurso']) ? (int)$_GET['id_recurso'] : null;
    echo json_encode(['sucesso' => true, 'dados' => \App\Servicos\ServicoPermissao::listarCompartilhamentos($tipo, $idRecurso)]);
    exit;
}

// ========== API LOGS DO SISTEMA ==========

if ($path === '/api/logs/listar' && $method === 'GET') {
    $c = new \App\Controllers\LogsController();
    $c->listar();
    exit;
}

if ($path === '/api/logs/limpar' && $method === 'POST') {
    $c = new \App\Controllers\LogsController();
    $c->limpar();
    exit;
}

if ($path === '/api/logs/exportar' && $method === 'GET') {
    $c = new \App\Controllers\LogsController();
    $c->exportar();
    exit;
}

// ========== FIM NOVAS ROTAS ==========

// API Histórico - Listar (Unificado: rotinas + pipelines + workflows)
if ($path === '/api/historico' && $method === 'GET') {
    try {
        $db = Database::getConexao();
        $tipo = $_GET['tipo'] ?? '';
        
        $partsRotina = [];
        $partsPipeline = [];
        $partsWorkflow = [];
        $paramsRotina = [];
        $paramsPipeline = [];
        $paramsWorkflow = [];
        
        // Filtros comuns
        if (!empty($_GET['status'])) {
            $statusVal = $_GET['status'];
            // Mapear status entre sistemas
            $partsRotina[] = "l.status = ?";
            $paramsRotina[] = $statusVal;
            // Pipeline usa 'success'/'error' em vez de 'sucesso'/'falha'
            $statusPipeline = str_replace(['sucesso','falha','executando'], ['success','error','running'], $statusVal);
            $partsPipeline[] = "pe.status = ?";
            $paramsPipeline[] = $statusPipeline;
            // Workflow usa 'completed'/'failed' em vez de 'sucesso'/'falha'
            $statusWorkflow = str_replace(['sucesso','falha','executando'], ['completed','failed','running'], $statusVal);
            $partsWorkflow[] = "we.status = ?";
            $paramsWorkflow[] = $statusWorkflow;
        }
        if (!empty($_GET['data_inicio'])) {
            $di = $_GET['data_inicio'] . ' 00:00:00';
            $partsRotina[] = "l.data_inicio >= ?"; $paramsRotina[] = $di;
            $partsPipeline[] = "pe.data_inicio >= ?"; $paramsPipeline[] = $di;
            $partsWorkflow[] = "we.data_inicio >= ?"; $paramsWorkflow[] = $di;
        }
        if (!empty($_GET['data_fim'])) {
            $df = $_GET['data_fim'] . ' 23:59:59';
            $partsRotina[] = "l.data_inicio <= ?"; $paramsRotina[] = $df;
            $partsPipeline[] = "pe.data_inicio <= ?"; $paramsPipeline[] = $df;
            $partsWorkflow[] = "we.data_inicio <= ?"; $paramsWorkflow[] = $df;
        }
        if (!empty($_GET['rotina'])) {
            $partsRotina[] = "l.id_rotina = ?"; $paramsRotina[] = (int)$_GET['rotina'];
        }
        
        $whereRotina = count($partsRotina) > 0 ? ' AND ' . implode(' AND ', $partsRotina) : '';
        $wherePipeline = count($partsPipeline) > 0 ? ' AND ' . implode(' AND ', $partsPipeline) : '';
        $whereWorkflow = count($partsWorkflow) > 0 ? ' AND ' . implode(' AND ', $partsWorkflow) : '';
        
        $unions = [];
        $allParams = [];
        
        // Filtros de visibilidade RBAC
        $filtroRotVis = \App\Servicos\ServicoPermissao::filtroVisibilidadePosicional('rotina', 'r', 'id_usuario_criador');
        $filtroPipVis = \App\Servicos\ServicoPermissao::filtroVisibilidadePosicional('pipeline', 'p', 'criado_por');
        $filtroWfVis = \App\Servicos\ServicoPermissao::filtroVisibilidadePosicional('workflow', 'w', 'criado_por');
        
        // Rotinas
        if (!$tipo || $tipo === 'rotina') {
            $unions[] = "SELECT l.id, 'rotina' as tipo_execucao, r.nome as nome_origem,
                CASE l.status WHEN 'sucesso' THEN 'sucesso' WHEN 'falha' THEN 'falha' WHEN 'erro' THEN 'erro' WHEN 'executando' THEN 'executando' ELSE l.status END as status,
                l.data_inicio, l.data_fim, l.duracao_ms, l.registros_processados,
                l.blocos_executados as nodes_total, l.blocos_sucesso as nodes_sucesso, l.blocos_falha as nodes_falha,
                l.mensagem_erro as erro
                FROM tb_logs_execucao l LEFT JOIN tb_rotinas r ON l.id_rotina = r.id
                WHERE 1=1 {$whereRotina} AND ({$filtroRotVis['where']})";
            $allParams = array_merge($allParams, $paramsRotina, $filtroRotVis['params']);
        }
        
        // Pipelines
        if (!$tipo || $tipo === 'pipeline') {
            $unions[] = "SELECT pe.id, 'pipeline' as tipo_execucao, p.nome as nome_origem,
                CASE pe.status WHEN 'success' THEN 'sucesso' WHEN 'error' THEN 'falha' WHEN 'running' THEN 'executando' WHEN 'cancelled' THEN 'cancelado' ELSE pe.status END as status,
                pe.data_inicio, pe.data_fim, pe.duracao_ms, NULL::bigint as registros_processados,
                pe.nodes_total, pe.nodes_sucesso, pe.nodes_falha,
                pe.erro
                FROM tb_pipeline_execucoes pe LEFT JOIN tb_pipelines p ON pe.id_pipeline = p.id
                WHERE 1=1 {$wherePipeline} AND ({$filtroPipVis['where']})";
            $allParams = array_merge($allParams, $paramsPipeline, $filtroPipVis['params']);
        }
        
        // Workflows
        if (!$tipo || $tipo === 'workflow') {
            $unions[] = "SELECT we.id, 'workflow' as tipo_execucao, w.nome as nome_origem,
                CASE we.status WHEN 'completed' THEN 'sucesso' WHEN 'failed' THEN 'falha' WHEN 'running' THEN 'executando' WHEN 'cancelled' THEN 'cancelado' WHEN 'paused' THEN 'pausado' ELSE we.status END as status,
                we.data_inicio, we.data_fim, we.duracao_ms, NULL::bigint as registros_processados,
                we.nodes_total, we.nodes_sucesso, we.nodes_falha,
                we.erro
                FROM tb_workflow_execucoes we LEFT JOIN tb_workflows w ON we.id_workflow = w.id
                WHERE 1=1 {$whereWorkflow} AND ({$filtroWfVis['where']})";
            $allParams = array_merge($allParams, $paramsWorkflow, $filtroWfVis['params']);
        }
        
        if (empty($unions)) {
            $unions[] = "SELECT 0 as id, '' as tipo_execucao, '' as nome_origem, '' as status, NULL::timestamptz as data_inicio, NULL::timestamptz as data_fim, 0 as duracao_ms, NULL::bigint as registros_processados, 0 as nodes_total, 0 as nodes_sucesso, 0 as nodes_falha, '' as erro WHERE false";
        }
        
        $sql = "(" . implode(") UNION ALL (", $unions) . ") ORDER BY data_inicio DESC LIMIT 500";
        $stmt = $db->prepare($sql);
        $stmt->execute($allParams);
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // RBAC: pré-computar IDs visíveis por tipo de recurso para stats
        $fRotSt = \App\Servicos\ServicoPermissao::filtroVisibilidadePosicional('rotina', 'r', 'id_usuario_criador');
        $stR = $db->prepare("SELECT r.id FROM tb_rotinas r WHERE ({$fRotSt['where']})");
        $stR->execute($fRotSt['params']);
        $rotIdsSt = $stR->fetchAll(PDO::FETCH_COLUMN);

        $fPipSt = \App\Servicos\ServicoPermissao::filtroVisibilidadePosicional('pipeline', 'p', 'criado_por');
        $stP = $db->prepare("SELECT p.id FROM tb_pipelines p WHERE ({$fPipSt['where']})");
        $stP->execute($fPipSt['params']);
        $pipIdsSt = $stP->fetchAll(PDO::FETCH_COLUMN);

        $fWfSt = \App\Servicos\ServicoPermissao::filtroVisibilidadePosicional('workflow', 'w', 'criado_por');
        $stW = $db->prepare("SELECT w.id FROM tb_workflows w WHERE ({$fWfSt['where']})");
        $stW->execute($fWfSt['params']);
        $wfIdsSt = $stW->fetchAll(PDO::FETCH_COLUMN);

        $inSqlSt = function(array $ids): string {
            if (empty($ids)) return '(0)';
            return '(' . implode(',', array_map('intval', $ids)) . ')';
        };
        $rotInSt = $inSqlSt($rotIdsSt);
        $pipInSt = $inSqlSt($pipIdsSt);
        $wfInSt  = $inSqlSt($wfIdsSt);

        // Estatísticas unificadas (com RBAC)
        $stats = $db->query("
            SELECT 
                COALESCE(r.sucesso_24h,0) + COALESCE(p.sucesso_24h,0) + COALESCE(w.sucesso_24h,0) as sucesso_24h,
                COALESCE(r.falhas_24h,0) + COALESCE(p.falhas_24h,0) + COALESCE(w.falhas_24h,0) as falhas_24h,
                COALESCE(r.executando,0) + COALESCE(p.executando,0) + COALESCE(w.executando,0) as executando,
                (COALESCE(r.tempo_medio_ms,0) + COALESCE(p.tempo_medio_ms,0) + COALESCE(w.tempo_medio_ms,0)) / 
                    NULLIF((CASE WHEN r.tempo_medio_ms IS NOT NULL THEN 1 ELSE 0 END + CASE WHEN p.tempo_medio_ms IS NOT NULL THEN 1 ELSE 0 END + CASE WHEN w.tempo_medio_ms IS NOT NULL THEN 1 ELSE 0 END), 0) as tempo_medio_ms
            FROM
            (SELECT 
                SUM(CASE WHEN status = 'sucesso' AND data_inicio >= NOW() - INTERVAL '24 hours' THEN 1 ELSE 0 END) as sucesso_24h,
                SUM(CASE WHEN status IN ('falha','erro') AND data_inicio >= NOW() - INTERVAL '24 hours' THEN 1 ELSE 0 END) as falhas_24h,
                SUM(CASE WHEN status = 'executando' THEN 1 ELSE 0 END) as executando,
                AVG(duracao_ms) FILTER (WHERE duracao_ms IS NOT NULL) as tempo_medio_ms
            FROM tb_logs_execucao WHERE id_rotina IN {$rotInSt}) r,
            (SELECT 
                SUM(CASE WHEN status = 'success' AND data_inicio >= NOW() - INTERVAL '24 hours' THEN 1 ELSE 0 END) as sucesso_24h,
                SUM(CASE WHEN status = 'error' AND data_inicio >= NOW() - INTERVAL '24 hours' THEN 1 ELSE 0 END) as falhas_24h,
                SUM(CASE WHEN status = 'running' THEN 1 ELSE 0 END) as executando,
                AVG(duracao_ms) FILTER (WHERE duracao_ms IS NOT NULL) as tempo_medio_ms
            FROM tb_pipeline_execucoes WHERE id_pipeline IN {$pipInSt}) p,
            (SELECT 
                SUM(CASE WHEN status = 'completed' AND data_inicio >= NOW() - INTERVAL '24 hours' THEN 1 ELSE 0 END) as sucesso_24h,
                SUM(CASE WHEN status = 'failed' AND data_inicio >= NOW() - INTERVAL '24 hours' THEN 1 ELSE 0 END) as falhas_24h,
                SUM(CASE WHEN status = 'running' THEN 1 ELSE 0 END) as executando,
                AVG(duracao_ms) FILTER (WHERE duracao_ms IS NOT NULL) as tempo_medio_ms
            FROM tb_workflow_execucoes WHERE id_workflow IN {$wfInSt}) w
        ")->fetch(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode([
            'sucesso' => true,
            'dados' => $dados,
            'estatisticas' => $stats
        ]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(ErrorHandler::tratarErro($e));
    }
    exit;
}

// API Histórico - Detalhes (por tipo)
if (preg_match('#^/api/historico/(rotina|pipeline|workflow)/(\d+)$#', $path, $m) && $method === 'GET') {
    try {
        $tipoExec = $m[1];
        $id = (int)$m[2];
        $db = Database::getConexao();
        
        if ($tipoExec === 'rotina') {
            $stmt = $db->prepare("SELECT l.*, r.nome as nome_rotina, r.id_usuario_criador as criador_recurso
                FROM tb_logs_execucao l LEFT JOIN tb_rotinas r ON l.id_rotina = r.id WHERE l.id = ?");
            $stmt->execute([$id]);
            $log = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$log) { http_response_code(404); header('Content-Type: application/json'); echo json_encode(['sucesso' => false, 'erro' => 'Não encontrado']); exit; }
            if (!empty($log['id_rotina']) && !\App\Servicos\ServicoPermissao::podeVerRecurso('rotina', (int)$log['id_rotina'], isset($log['criador_recurso']) ? (int)$log['criador_recurso'] : null)) {
                http_response_code(403); header('Content-Type: application/json'); echo json_encode(['sucesso' => false, 'erro' => 'Acesso negado']); exit;
            }
            
            $log['tipo_execucao'] = 'rotina';
            $log['nome_origem'] = $log['nome_rotina'] ?? 'Rotina #' . ($log['id_rotina'] ?? '?');
            
            // Processar logs detalhados
            $log['logs'] = [];
            $metaData = null;
            if (!empty($log['meta'])) {
                $metaData = is_string($log['meta']) ? json_decode($log['meta'], true) : $log['meta'];
            } elseif (!empty($log['detalhes_json'])) {
                $metaData = is_string($log['detalhes_json']) ? json_decode($log['detalhes_json'], true) : $log['detalhes_json'];
            }
            if (!empty($metaData) && is_array($metaData)) {
                foreach ($metaData as $item) {
                    $logItem = [
                        'bloco' => $item['bloco'] ?? 'Bloco',
                        'tipo' => $item['tipo'] ?? 'SQL',
                        'ordem' => $item['ordem'] ?? null,
                        'status' => 'sucesso',
                        'duracao_ms' => $item['duracao_ms'] ?? null,
                        'registros' => null, 'resultado' => null, 'erro' => null,
                        'sql' => $item['sql'] ?? null, 'arquivo_csv' => null
                    ];
                    if (isset($item['res'])) {
                        $res = $item['res'];
                        $logItem['status'] = ($res['sucesso'] ?? true) ? 'sucesso' : 'erro';
                        $logItem['resultado'] = $res['resultado'] ?? null;
                        $logItem['erro'] = $res['erro'] ?? null;
                        $logItem['registros'] = $res['linhas'] ?? $res['registros'] ?? null;
                        $logItem['arquivo_csv'] = $res['arquivo'] ?? null;
                    }
                    if (isset($item['status'])) $logItem['status'] = $item['status'];
                    if (isset($item['resultado'])) $logItem['resultado'] = $item['resultado'];
                    if (isset($item['erro'])) { $logItem['erro'] = $item['erro']; $logItem['status'] = 'erro'; }
                    if (isset($item['registros'])) $logItem['registros'] = $item['registros'];
                    $log['logs'][] = $logItem;
                }
            }
            
        } elseif ($tipoExec === 'pipeline') {
            $stmt = $db->prepare("SELECT pe.*, p.nome as nome_pipeline, p.modo, p.criado_por as criador_recurso
                FROM tb_pipeline_execucoes pe LEFT JOIN tb_pipelines p ON pe.id_pipeline = p.id WHERE pe.id = ?");
            $stmt->execute([$id]);
            $log = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$log) { http_response_code(404); header('Content-Type: application/json'); echo json_encode(['sucesso' => false, 'erro' => 'Não encontrado']); exit; }
            if (!empty($log['id_pipeline']) && !\App\Servicos\ServicoPermissao::podeVerRecurso('pipeline', (int)$log['id_pipeline'], isset($log['criador_recurso']) ? (int)$log['criador_recurso'] : null)) {
                http_response_code(403); header('Content-Type: application/json'); echo json_encode(['sucesso' => false, 'erro' => 'Acesso negado']); exit;
            }
            
            $log['tipo_execucao'] = 'pipeline';
            $log['nome_origem'] = $log['nome_pipeline'] ?? 'Pipeline #' . ($log['id_pipeline'] ?? '?');
            // Normalizar status
            $statusMap = ['success'=>'sucesso','error'=>'falha','running'=>'executando','cancelled'=>'cancelado','pending'=>'pendente'];
            $log['status'] = $statusMap[$log['status']] ?? $log['status'];
            
            // Processar log_execucao JSONB em blocos
            $log['logs'] = [];
            $logExec = null;
            if (!empty($log['log_execucao'])) {
                $logExec = is_string($log['log_execucao']) ? json_decode($log['log_execucao'], true) : $log['log_execucao'];
            }
            if (!empty($logExec) && is_array($logExec)) {
                foreach ($logExec as $i => $item) {
                    $log['logs'][] = [
                        'bloco' => $item['node_label'] ?? $item['node_id'] ?? ('Nó ' . ($i + 1)),
                        'tipo' => strtoupper($item['node_type'] ?? $item['tipo'] ?? 'NODE'),
                        'ordem' => $item['ordem'] ?? ($i + 1),
                        'status' => ($item['status'] ?? 'success') === 'success' ? 'sucesso' : 'erro',
                        'duracao_ms' => $item['duracao_ms'] ?? null,
                        'registros' => $item['registros'] ?? $item['rows_affected'] ?? null,
                        'resultado' => isset($item['output']) ? (is_array($item['output']) ? json_encode($item['output']) : $item['output']) : null,
                        'erro' => $item['erro'] ?? $item['error'] ?? null,
                        'sql' => $item['sql'] ?? $item['query'] ?? null,
                        'arquivo_csv' => null
                    ];
                }
            }
            // Resultado global
            if (!empty($log['resultado'])) {
                $log['resultado_decoded'] = is_string($log['resultado']) ? json_decode($log['resultado'], true) : $log['resultado'];
            }
            
        } elseif ($tipoExec === 'workflow') {
            $stmt = $db->prepare("SELECT we.*, w.nome as nome_workflow, w.criado_por as criador_recurso
                FROM tb_workflow_execucoes we LEFT JOIN tb_workflows w ON we.id_workflow = w.id WHERE we.id = ?");
            $stmt->execute([$id]);
            $log = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$log) { http_response_code(404); header('Content-Type: application/json'); echo json_encode(['sucesso' => false, 'erro' => 'Não encontrado']); exit; }
            if (!empty($log['id_workflow']) && !\App\Servicos\ServicoPermissao::podeVerRecurso('workflow', (int)$log['id_workflow'], isset($log['criador_recurso']) ? (int)$log['criador_recurso'] : null)) {
                http_response_code(403); header('Content-Type: application/json'); echo json_encode(['sucesso' => false, 'erro' => 'Acesso negado']); exit;
            }
            
            $log['tipo_execucao'] = 'workflow';
            $log['nome_origem'] = $log['nome_workflow'] ?? 'Workflow #' . ($log['id_workflow'] ?? '?');
            $statusMap = ['completed'=>'sucesso','failed'=>'falha','running'=>'executando','cancelled'=>'cancelado','paused'=>'pausado','pending'=>'pendente'];
            $log['status'] = $statusMap[$log['status']] ?? $log['status'];
            
            // Buscar execuções de nós
            $stmtNodes = $db->prepare("SELECT * FROM tb_workflow_node_execucoes WHERE id_workflow_execucao = ? ORDER BY ordem, data_inicio");
            $stmtNodes->execute([$id]);
            $nodes = $stmtNodes->fetchAll(PDO::FETCH_ASSOC);
            
            $log['logs'] = [];
            foreach ($nodes as $node) {
                $nodeStatusMap = ['completed'=>'sucesso','failed'=>'erro','skipped'=>'pulado','running'=>'executando','waiting'=>'aguardando','pending'=>'pendente'];
                $log['logs'][] = [
                    'bloco' => $node['label'] ?? $node['node_id'] ?? 'Nó',
                    'tipo' => strtoupper($node['tipo_node'] ?? 'NODE'),
                    'ordem' => $node['ordem'] ?? null,
                    'status' => $nodeStatusMap[$node['status']] ?? $node['status'],
                    'duracao_ms' => $node['duracao_ms'] ?? null,
                    'registros' => null,
                    'resultado' => !empty($node['output_data']) ? (is_string($node['output_data']) ? $node['output_data'] : json_encode($node['output_data'])) : null,
                    'erro' => $node['erro'] ?? null,
                    'sql' => null,
                    'arquivo_csv' => null
                ];
            }
            
            // Decodificar JSONs
            if (!empty($log['trigger_data']) && is_string($log['trigger_data'])) $log['trigger_data'] = json_decode($log['trigger_data'], true);
            if (!empty($log['contexto']) && is_string($log['contexto'])) $log['contexto'] = json_decode($log['contexto'], true);
            if (!empty($log['resultado_json']) && is_string($log['resultado_json'])) $log['resultado_json'] = json_decode($log['resultado_json'], true);
        }
        
        header('Content-Type: application/json');
        echo json_encode(['sucesso' => true, 'dados' => $log]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(ErrorHandler::tratarErro($e));
    }
    exit;
}

// API Histórico - Detalhes (legado - rotina por ID)
if (preg_match('#^/api/historico/(\d+)$#', $path, $m) && $method === 'GET') {
    try {
        $id = (int)$m[1];
        $db = Database::getConexao();
        
        $stmt = $db->prepare("SELECT l.*, r.nome as nome_rotina, r.id_usuario_criador as criador_recurso
            FROM tb_logs_execucao l 
            LEFT JOIN tb_rotinas r ON l.id_rotina = r.id 
            WHERE l.id = ?");
        $stmt->execute([$id]);
        $log = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$log) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['sucesso' => false, 'erro' => 'Log não encontrado']);
            exit;
        }
        
        // Verificação RBAC
        if (!empty($log['id_rotina']) && !\App\Servicos\ServicoPermissao::podeVerRecurso('rotina', (int)$log['id_rotina'], isset($log['criador_recurso']) ? (int)$log['criador_recurso'] : null)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['sucesso' => false, 'erro' => 'Acesso negado']);
            exit;
        }
        
        // Carregar logs detalhados do campo meta (JSONB)
        $log['logs'] = [];
        $metaData = null;
        
        // Tentar carregar de meta primeiro
        if (!empty($log['meta'])) {
            if (is_string($log['meta'])) {
                $metaData = json_decode($log['meta'], true);
            } else {
                $metaData = $log['meta'];
            }
        }
        // Fallback para detalhes_json
        elseif (!empty($log['detalhes_json'])) {
            if (is_string($log['detalhes_json'])) {
                $metaData = json_decode($log['detalhes_json'], true);
            } else {
                $metaData = $log['detalhes_json'];
            }
        }
        
        // Normalizar formato dos logs para o frontend
        if (!empty($metaData) && is_array($metaData)) {
            foreach ($metaData as $item) {
                $logItem = [
                    'bloco' => $item['bloco'] ?? 'Bloco',
                    'tipo' => $item['tipo'] ?? 'SQL',
                    'ordem' => $item['ordem'] ?? null,
                    'status' => 'sucesso',
                    'duracao_ms' => $item['duracao_ms'] ?? null,
                    'registros' => null,
                    'resultado' => null,
                    'erro' => null,
                    'sql' => $item['sql'] ?? null,
                    'arquivo_csv' => null
                ];
                
                // Processar res (formato antigo)
                if (isset($item['res'])) {
                    $res = $item['res'];
                    $logItem['status'] = ($res['sucesso'] ?? true) ? 'sucesso' : 'erro';
                    $logItem['resultado'] = $res['resultado'] ?? null;
                    $logItem['erro'] = $res['erro'] ?? null;
                    $logItem['registros'] = $res['linhas'] ?? $res['registros'] ?? null;
                    $logItem['arquivo_csv'] = $res['arquivo'] ?? null;
                }
                
                // Processar formato novo
                if (isset($item['status'])) {
                    $logItem['status'] = $item['status'];
                }
                if (isset($item['resultado'])) {
                    $logItem['resultado'] = $item['resultado'];
                }
                if (isset($item['erro'])) {
                    $logItem['erro'] = $item['erro'];
                    $logItem['status'] = 'erro';
                }
                if (isset($item['registros'])) {
                    $logItem['registros'] = $item['registros'];
                }
                
                $log['logs'][] = $logItem;
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode(['sucesso' => true, 'dados' => $log]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(ErrorHandler::tratarErro($e));
    }
    exit;
}

// API Histórico - Exportar CSV (unificado: rotinas + pipelines + workflows)
if ($path === '/api/historico/exportar' && $method === 'GET') {
    try {
        $db = Database::getConexao();
        
        $tipo = $_GET['tipo'] ?? '';
        $partsRotina = [];
        $partsPipeline = [];
        $partsWorkflow = [];
        $paramsRotina = [];
        $paramsPipeline = [];
        $paramsWorkflow = [];
        
        if (!empty($_GET['status'])) {
            $statusVal = $_GET['status'];
            $partsRotina[] = "l.status = ?";
            $paramsRotina[] = $statusVal;
            $statusPipeline = str_replace(['sucesso','falha','executando'], ['success','error','running'], $statusVal);
            $partsPipeline[] = "pe.status = ?";
            $paramsPipeline[] = $statusPipeline;
            $statusWorkflow = str_replace(['sucesso','falha','executando'], ['completed','failed','running'], $statusVal);
            $partsWorkflow[] = "we.status = ?";
            $paramsWorkflow[] = $statusWorkflow;
        }
        if (!empty($_GET['rotina'])) {
            $partsRotina[] = "l.id_rotina = ?";
            $paramsRotina[] = (int)$_GET['rotina'];
        }
        
        $whereRotina = count($partsRotina) > 0 ? ' AND ' . implode(' AND ', $partsRotina) : '';
        $wherePipeline = count($partsPipeline) > 0 ? ' AND ' . implode(' AND ', $partsPipeline) : '';
        $whereWorkflow = count($partsWorkflow) > 0 ? ' AND ' . implode(' AND ', $partsWorkflow) : '';
        
        // Filtros de visibilidade RBAC
        $filtroRotVis = \App\Servicos\ServicoPermissao::filtroVisibilidadePosicional('rotina', 'r', 'id_usuario_criador');
        $filtroPipVis = \App\Servicos\ServicoPermissao::filtroVisibilidadePosicional('pipeline', 'p', 'criado_por');
        $filtroWfVis = \App\Servicos\ServicoPermissao::filtroVisibilidadePosicional('workflow', 'w', 'criado_por');
        
        $unions = [];
        $allParams = [];
        
        if (!$tipo || $tipo === 'rotina') {
            $unions[] = "SELECT l.id, 'rotina' as tipo_execucao, r.nome as nome_origem,
                CASE l.status WHEN 'sucesso' THEN 'sucesso' WHEN 'falha' THEN 'falha' WHEN 'erro' THEN 'erro' ELSE l.status END as status,
                l.data_inicio, l.data_fim, l.duracao_ms, l.registros_processados,
                l.mensagem_erro as erro
                FROM tb_logs_execucao l LEFT JOIN tb_rotinas r ON l.id_rotina = r.id
                WHERE 1=1 {$whereRotina} AND ({$filtroRotVis['where']})";
            $allParams = array_merge($allParams, $paramsRotina, $filtroRotVis['params']);
        }
        if (!$tipo || $tipo === 'pipeline') {
            $unions[] = "SELECT pe.id, 'pipeline' as tipo_execucao, p.nome as nome_origem,
                CASE pe.status WHEN 'success' THEN 'sucesso' WHEN 'error' THEN 'falha' ELSE pe.status END as status,
                pe.data_inicio, pe.data_fim, pe.duracao_ms, NULL::bigint as registros_processados,
                pe.erro
                FROM tb_pipeline_execucoes pe LEFT JOIN tb_pipelines p ON pe.id_pipeline = p.id
                WHERE 1=1 {$wherePipeline} AND ({$filtroPipVis['where']})";
            $allParams = array_merge($allParams, $paramsPipeline, $filtroPipVis['params']);
        }
        if (!$tipo || $tipo === 'workflow') {
            $unions[] = "SELECT we.id, 'workflow' as tipo_execucao, w.nome as nome_origem,
                CASE we.status WHEN 'completed' THEN 'sucesso' WHEN 'failed' THEN 'falha' ELSE we.status END as status,
                we.data_inicio, we.data_fim, we.duracao_ms, NULL::bigint as registros_processados,
                we.erro
                FROM tb_workflow_execucoes we LEFT JOIN tb_workflows w ON we.id_workflow = w.id
                WHERE 1=1 {$whereWorkflow} AND ({$filtroWfVis['where']})";
            $allParams = array_merge($allParams, $paramsWorkflow, $filtroWfVis['params']);
        }
        
        if (empty($unions)) {
            $unions[] = "SELECT 0 as id, '' as tipo_execucao, '' as nome_origem, '' as status, NULL::timestamptz as data_inicio, NULL::timestamptz as data_fim, 0 as duracao_ms, NULL::bigint as registros_processados, '' as erro WHERE false";
        }
        
        $sql = "(" . implode(") UNION ALL (", $unions) . ") ORDER BY data_inicio DESC LIMIT 5000";
        $stmt = $db->prepare($sql);
        $stmt->execute($allParams);
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="historico_execucoes_' . date('Y-m-d_His') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
        
        // Header com tipo_execucao
        fputcsv($output, ['ID', 'Tipo', 'Nome', 'Status', 'Início', 'Fim', 'Duração (ms)', 'Registros', 'Erro'], ';');
        
        foreach ($dados as $row) {
            fputcsv($output, [
                $row['id'],
                $row['tipo_execucao'],
                $row['nome_origem'],
                $row['status'],
                $row['data_inicio'],
                $row['data_fim'],
                $row['duracao_ms'],
                $row['registros_processados'],
                $row['erro']
            ], ';');
        }
        
        fclose($output);
    } catch (Exception $e) {
        echo 'Erro: ' . $e->getMessage();
    }
    exit;
}

// API Download CSV de execução específica
if (preg_match('#^/api/download-csv/(\d+)$#', $path, $m) && $method === 'GET') {
    try {
        $id = (int)$m[1];
        $db = Database::getConexao();
        
        $stmt = $db->prepare("SELECT caminho_csv FROM tb_logs_execucao WHERE id = ?");
        $stmt->execute([$id]);
        $caminho = $stmt->fetchColumn();
        
        if (!$caminho || !file_exists($caminho)) {
            http_response_code(404);
            echo 'Arquivo não encontrado';
            exit;
        }
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . basename($caminho) . '"');
        readfile($caminho);
    } catch (Exception $e) {
        http_response_code(500);
        echo 'Erro: ' . $e->getMessage();
    }
    exit;
}

// API Executar rotina (via histórico ou dashboard)
if ($path === '/api/executar-rotina' && $method === 'POST') {
    // Validar CSRF
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    // Rate limiting: max 10 execuções por minuto por sessão
    $rateLimiter = new \App\Core\RateLimiter();
    $sessionId = session_id() ?: 'anonymous';
    if (!$rateLimiter->permitir("exec_rotina:{$sessionId}", 10, 60)) {
        http_response_code(429);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Limite de execuções excedido. Aguarde 1 minuto.', 'sucesso' => false]);
        exit;
    }
    try {
        $db = Database::getConexao();
        
        $idRotina = null;
        
        if (!empty($_POST['rotina_id'])) {
            $idRotina = (int)$_POST['rotina_id'];
        } elseif (!empty($_POST['log_id'])) {
            $stmt = $db->prepare("SELECT id_rotina FROM tb_logs_execucao WHERE id = ?");
            $stmt->execute([(int)$_POST['log_id']]);
            $idRotina = $stmt->fetchColumn();
        }
        
        if (!$idRotina) {
            header('Content-Type: application/json');
            echo json_encode(['sucesso' => false, 'erro' => 'ID da rotina não informado']);
            exit;
        }
        
        $r = new RotinasController();
        header('Content-Type: application/json');
        echo json_encode($r->executar($idRotina));
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(ErrorHandler::tratarErro($e));
    }
    exit;
}

// rota de teste: cria uma rotina de exemplo via GET (apenas para testes locais)
if ($path === '/rotinas/test_save' && $method === 'GET') {
    $c = new RotinasController();
    header('Content-Type: application/json');
    $payload = [
        'nome' => 'Teste_Auto',
        'descricao' => 'Criado via rota de teste',
        'id_conexao' => '1',
        'bloco_codigo' => ['B1'],
        'tipo_bloco' => ['SELECT'],
        'script_sql' => ['SELECT 1 as x']
    ];
    echo json_encode($c->salvar($payload));
    exit;
}

// Test insert directly (min columns) to isolate controller issues
if ($path === '/rotinas/test_save2' && $method === 'GET') {
    $db = Database::getConexao();
    $sql = 'INSERT INTO tb_rotinas (nome, descricao, id_conexao) VALUES (?, ?, ?) RETURNING id';
    $ins = $db->prepare($sql);
    try {
        $ins->execute(['TS2', 'Teste direta', 1]);
        $id = $ins->fetchColumn();
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'id' => $id]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// rota de teste 3: criar rotina via controlador com id_usuario_criador explicitamente nulo
if ($path === '/rotinas/test_save3' && $method === 'GET') {
    $c = new RotinasController();
    header('Content-Type: application/json');
    $payload = [
        'nome' => 'Teste_Auto_3',
        'descricao' => 'Criado via rota de teste 3',
        'id_conexao' => '1',
        'id_usuario_criador' => null,
        'bloco_codigo' => ['B1','B2'],
        'tipo_bloco' => ['SELECT','SELECT'],
        'script_sql' => ['SELECT 1 as x','SELECT 2 as y']
    ];
    echo json_encode($c->salvar($payload));
    exit;
}

// rota de teste: executar rotina por id via GET (apenas para testes locais)
if (preg_match('#^/rotinas/test_run/(\d+)$#', $path, $m) && $method === 'GET') {
    $id = intval($m[1]);
    $r = new RotinasController();
    header('Content-Type: application/json');
    echo json_encode($r->executar($id));
    exit;
}

// =====================================================
// ROTAS: APIs EXTERNAS
// =====================================================

// View APIs Externas
if ($path === '/apis-externas') {
    include __DIR__ . '/../views/apis-externas.php';
    exit;
}

// Listar APIs
if ($path === '/api/apis-externas/list' && $method === 'GET') {
    $c = new ApiExternaController();
    header('Content-Type: application/json');
    echo json_encode($c->listarApis());
    exit;
}

// Buscar API por ID
if (preg_match('#^/api/apis-externas/get/(\d+)$#', $path, $m) && $method === 'GET') {
    $c = new ApiExternaController();
    $id = (int)$m[1];
    header('Content-Type: application/json');
    $resultado = $c->buscarApi($id);
    $resultado['empresas'] = \App\Servicos\ServicoPermissao::obterEmpresasDoRecurso('api', $id);
    $resultado['projetos'] = \App\Servicos\ServicoPermissao::obterProjetosDoRecurso('api', $id);
    echo json_encode($resultado);
    exit;
}

// Salvar API
if ($path === '/api/apis-externas/salvar' && $method === 'POST') {
    $c = new ApiExternaController();
    $data = $_POST;
    $input = file_get_contents('php://input');
    if ($input && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
        $data = json_decode($input, true);
    }
    // Validar CSRF token
    $csrfToken = $data['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    unset($data['_csrf_token']);
    header('Content-Type: application/json');
    echo json_encode($c->salvarApi($data));
    exit;
}

// Deletar API
if (preg_match('#^/api/apis-externas/delete/(\d+)$#', $path, $m) && $method === 'POST') {
    // Validar CSRF token
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $id = (int)$m[1];
    header('Content-Type: application/json');
    if (!\App\Servicos\ServicoPermissao::podeModificarRecurso('api_externa', $id)) {
        http_response_code(403);
        echo json_encode(['erro' => 'Sem permissão para excluir esta API', 'sucesso' => false]);
        exit;
    }
    $c = new ApiExternaController();
    \App\Servicos\ServicoAuditoria::registrar('excluir', 'api_externa', $id);
    echo json_encode($c->deletarApi($id));
    exit;
}

// Testar API
if ($path === '/api/apis-externas/testar' && $method === 'POST') {
    $c = new ApiExternaController();
    $data = $_POST;
    $input = file_get_contents('php://input');
    if ($input && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
        $data = json_decode($input, true);
    }
    // Validar CSRF token
    $csrfToken = $data['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    unset($data['_csrf_token']);
    // Rate limiting: max 20 testes por minuto por sessão
    $rateLimiter = new \App\Core\RateLimiter();
    $sessionId = session_id() ?: 'anonymous';
    if (!$rateLimiter->permitir("api_test:{$sessionId}", 20, 60)) {
        http_response_code(429);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Limite de testes excedido. Aguarde 1 minuto.', 'sucesso' => false]);
        exit;
    }
    header('Content-Type: application/json');
    echo json_encode($c->testarApi($data));
    exit;
}

// =====================================================
// ROTAS: EVENTOS DE API
// =====================================================

// View Eventos
if ($path === '/eventos-api') {
    include __DIR__ . '/../views/eventos-api.php';
    exit;
}

// Listar Eventos
if ($path === '/api/eventos-api/list' && $method === 'GET') {
    $idApi = isset($_GET['id_api']) ? (int)$_GET['id_api'] : (isset($_GET['api']) ? (int)$_GET['api'] : null);
    $c = new ApiExternaController();
    header('Content-Type: application/json');
    echo json_encode($c->listarEventos($idApi));
    exit;
}

// Buscar Evento por ID
if (preg_match('#^/api/eventos-api/get/(\d+)$#', $path, $m) && $method === 'GET') {
    $c = new ApiExternaController();
    header('Content-Type: application/json');
    echo json_encode($c->buscarEvento((int)$m[1]));
    exit;
}

// Salvar Evento
if ($path === '/api/eventos-api/salvar' && $method === 'POST') {
    $c = new ApiExternaController();
    $data = $_POST;
    $input = file_get_contents('php://input');
    if ($input && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
        $data = json_decode($input, true);
    }
    // Validar CSRF token
    $csrfToken = $data['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    unset($data['_csrf_token']);
    header('Content-Type: application/json');
    echo json_encode($c->salvarEvento($data));
    exit;
}

// Deletar Evento
if (preg_match('#^/api/eventos-api/delete/(\d+)$#', $path, $m) && $method === 'POST') {
    // Validar CSRF token
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new ApiExternaController();
    header('Content-Type: application/json');
    echo json_encode($c->deletarEvento((int)$m[1]));
    exit;
}

// Testar JSONPath
if ($path === '/api/eventos-api/testar-jsonpath' && $method === 'POST') {
    $c = new ApiExternaController();
    $data = $_POST;
    $input = file_get_contents('php://input');
    if ($input && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
        $data = json_decode($input, true);
    }
    // Validar CSRF token
    $csrfToken = $data['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    unset($data['_csrf_token']);
    header('Content-Type: application/json');
    echo json_encode($c->testarJsonPath($data));
    exit;
}

// Listar Valores Capturados
if ($path === '/api/valores-capturados/list' && $method === 'GET') {
    $idEvento = isset($_GET['id_evento']) ? (int)$_GET['id_evento'] : null;
    $c = new ApiExternaController();
    header('Content-Type: application/json');
    echo json_encode($c->listarValoresCapturados($idEvento));
    exit;
}

// Executar Polling Manual de uma API
if (preg_match('#^/api/apis-externas/polling/(\d+)$#', $path, $m) && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $engine = new \App\Core\ApiPollingEngine();
    header('Content-Type: application/json');
    echo json_encode($engine->executarPollingApi((int)$m[1]));
    exit;
}

// Executar Polling de todas as APIs
if ($path === '/api/apis-externas/polling' && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $engine = new \App\Core\ApiPollingEngine();
    header('Content-Type: application/json');
    echo json_encode(['sucesso' => true, 'resultados' => $engine->executarPolling()]);
    exit;
}

// =====================================================
// ROTAS: NOTIFICAÇÕES
// =====================================================

// Listar Notificações
if ($path === '/api/notificacoes/list' && $method === 'GET') {
    $limite = isset($_GET['limite']) ? min((int)$_GET['limite'], 100) : 20;
    $db = \App\Core\Database::getConexao();
    $stmt = $db->prepare("SELECT * FROM tb_notificacoes ORDER BY created_at DESC LIMIT ?");
    $stmt->execute([$limite]);
    header('Content-Type: application/json');
    echo json_encode(['sucesso' => true, 'dados' => $stmt->fetchAll(\PDO::FETCH_ASSOC)]);
    exit;
}

// Contar Notificações não lidas
if ($path === '/api/notificacoes/count' && $method === 'GET') {
    $db = \App\Core\Database::getConexao();
    $stmt = $db->query("SELECT COUNT(*) FROM tb_notificacoes WHERE lida = false");
    header('Content-Type: application/json');
    echo json_encode(['sucesso' => true, 'count' => (int)$stmt->fetchColumn()]);
    exit;
}

// Marcar notificação como lida
if (preg_match('#^/api/notificacoes/lida/(\d+)$#', $path, $m) && $method === 'POST') {
    $db = \App\Core\Database::getConexao();
    $stmt = $db->prepare("UPDATE tb_notificacoes SET lida = true WHERE id = ?");
    $stmt->execute([(int)$m[1]]);
    header('Content-Type: application/json');
    echo json_encode(['sucesso' => true]);
    exit;
}

// Marcar todas como lidas
if ($path === '/api/notificacoes/lida-todas' && $method === 'POST') {
    $db = \App\Core\Database::getConexao();
    $db->exec("UPDATE tb_notificacoes SET lida = true WHERE lida = false");
    header('Content-Type: application/json');
    echo json_encode(['sucesso' => true]);
    exit;
}

// =====================================================
// ROTAS: CONFIGURAÇÕES
// =====================================================

// API: Carregar configurações
if ($path === '/api/configuracoes' && $method === 'GET') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $c = new \App\Controllers\ConfiguracoesController();
    $c->carregar();
    exit;
}

// API: Salvar configurações por grupo
if (preg_match('#^/api/configuracoes/(geral|email|ldap|scheduler|seguranca|notificacoes)$#', $path, $m) && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $c = new \App\Controllers\ConfiguracoesController();
    $c->salvarGrupo($m[1]);
    exit;
}

// API: Testar e-mail
if ($path === '/api/configuracoes/testar-email' && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $c = new \App\Controllers\ConfiguracoesController();
    $c->testarEmail();
    exit;
}

// API: Testar conexão LDAP
if ($path === '/api/configuracoes/testar-ldap' && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $c = new \App\Controllers\ConfiguracoesController();
    $c->testarLdap();
    exit;
}

// API: Backup do banco de dados
if ($path === '/api/configuracoes/backup-bd' && $method === 'GET') {
    \App\Servicos\ServicoPermissao::exigirNivel('super_admin');
    $c = new \App\Controllers\ConfiguracoesController();
    $c->backupBD();
    exit;
}

// API: Exportar configurações
if ($path === '/api/configuracoes/exportar' && $method === 'GET') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $c = new \App\Controllers\ConfiguracoesController();
    $c->exportar();
    exit;
}

// API: Importar configurações
if ($path === '/api/configuracoes/importar' && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('super_admin');
    $c = new \App\Controllers\ConfiguracoesController();
    $c->importar();
    exit;
}

// API: Limpar dados antigos
if ($path === '/api/configuracoes/limpar-dados' && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('super_admin');
    $c = new \App\Controllers\ConfiguracoesController();
    $c->limparDados();
    exit;
}

// =====================================================
// ROTAS: AUDITORIA
// =====================================================

// API: Listar auditoria
if ($path === '/api/auditoria' && $method === 'GET') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    header('Content-Type: application/json');
    echo json_encode(\App\Servicos\ServicoAuditoria::listar($_GET));
    exit;
}

// API: Exportar auditoria CSV
if ($path === '/api/auditoria/exportar' && $method === 'GET') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    \App\Servicos\ServicoAuditoria::exportar($_GET);
    exit;
}

// =====================================================
// ROTAS: WEBHOOKS
// =====================================================

// API: Listar webhooks
if ($path === '/api/webhooks/list' && $method === 'GET') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    header('Content-Type: application/json');
    echo json_encode(\App\Servicos\ServicoWebhook::listar());
    exit;
}

// API: Buscar webhook
if (preg_match('#^/api/webhooks/get/(\d+)$#', $path, $m) && $method === 'GET') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    header('Content-Type: application/json');
    echo json_encode(\App\Servicos\ServicoWebhook::buscar((int)$m[1]));
    exit;
}

// API: Salvar webhook
if ($path === '/api/webhooks/salvar' && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    header('Content-Type: application/json');
    echo json_encode(\App\Servicos\ServicoWebhook::salvar($_POST));
    exit;
}

// API: Excluir webhook
if (preg_match('#^/api/webhooks/delete/(\d+)$#', $path, $m) && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    header('Content-Type: application/json');
    echo json_encode(\App\Servicos\ServicoWebhook::excluir((int)$m[1]));
    exit;
}

// API: Testar webhook
if (preg_match('#^/api/webhooks/testar/(\d+)$#', $path, $m) && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    header('Content-Type: application/json');
    echo json_encode(\App\Servicos\ServicoWebhook::testar((int)$m[1]));
    exit;
}

// =====================================================
// ROTAS: WORKFLOWS
// =====================================================

// View Lista de Workflows
if ($path === '/workflows') {
    include __DIR__ . '/../views/workflows.php';
    exit;
}

// View Workflow Builder
if ($path === '/workflow-builder' || preg_match('#^/workflow-builder/(\d+)$#', $path, $matches)) {
    $params = ['id' => $matches[1] ?? null];
    include __DIR__ . '/../views/workflow-builder.php';
    exit;
}

// View Execuções de Workflow
if ($path === '/workflow-execucoes') {
    include __DIR__ . '/../views/workflow-execucoes.php';
    exit;
}

// Listar Workflows
if ($path === '/api/workflows/list' && $method === 'GET') {
    $c = new WorkflowController();
    header('Content-Type: application/json');
    echo json_encode($c->listar());
    exit;
}

// Buscar Workflow por ID
if (preg_match('#^/api/workflows/get/(\d+)$#', $path, $m) && $method === 'GET') {
    $c = new WorkflowController();
    $id = (int)$m[1];
    header('Content-Type: application/json');
    $resultado = $c->buscar($id);
    $resultado['empresas'] = \App\Servicos\ServicoPermissao::obterEmpresasDoRecurso('workflow', $id);
    $resultado['projetos'] = \App\Servicos\ServicoPermissao::obterProjetosDoRecurso('workflow', $id);
    echo json_encode($resultado);
    exit;
}

// Salvar Workflow
if ($path === '/api/workflows/salvar' && $method === 'POST') {
    $c = new WorkflowController();
    $data = $_POST;
    
    // Se veio JSON no body
    $input = file_get_contents('php://input');
    if ($input && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
        $data = json_decode($input, true);
    }
    
    // Validar CSRF
    $csrfToken = $data['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    unset($data['_csrf_token']);
    
    header('Content-Type: application/json');
    $resultado = $c->salvar($data);
    \App\Servicos\ServicoAuditoria::registrar(
        isset($data['id']) && $data['id'] ? 'editar' : 'criar',
        'workflow', (int)($resultado['id'] ?? $data['id'] ?? 0), $data['nome'] ?? ''
    );
    echo json_encode($resultado);
    exit;
}

// Deletar Workflow
if (preg_match('#^/api/workflows/delete/(\d+)$#', $path, $m) && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $id = (int)$m[1];
    header('Content-Type: application/json');
    if (!\App\Servicos\ServicoPermissao::podeModificarRecurso('workflow', $id)) {
        http_response_code(403);
        echo json_encode(['erro' => 'Sem permissão para excluir este workflow', 'sucesso' => false]);
        exit;
    }
    \App\Servicos\ServicoAuditoria::registrar('excluir', 'workflow', $id);
    $c = new WorkflowController();
    echo json_encode($c->deletar($id));
    exit;
}

// Alternar ativo
if (preg_match('#^/api/workflows/toggle/(\d+)$#', $path, $m) && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new WorkflowController();
    header('Content-Type: application/json');
    echo json_encode($c->alternarAtivo((int)$m[1]));
    exit;
}

// Duplicar Workflow
if (preg_match('#^/api/workflows/duplicar/(\d+)$#', $path, $m) && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new WorkflowController();
    header('Content-Type: application/json');
    echo json_encode($c->duplicar((int)$m[1]));
    exit;
}

// Executar Workflow
if (preg_match('#^/api/workflows/executar/(\d+)$#', $path, $m) && $method === 'POST') {
    // Validar CSRF (aceita via JSON body, POST ou header)
    $contexto = [];
    $input = file_get_contents('php://input');
    if ($input) {
        $contexto = json_decode($input, true) ?? [];
    }
    $csrfToken = $contexto['_csrf_token'] ?? $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    unset($contexto['_csrf_token']);
    // Rate limiting: max 10 execuções de workflow por minuto por sessão
    $rateLimiter = new \App\Core\RateLimiter();
    $sessionId = session_id() ?: 'anonymous';
    if (!$rateLimiter->permitir("exec_workflow:{$sessionId}", 10, 60)) {
        http_response_code(429);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Limite de execuções excedido. Aguarde 1 minuto.', 'sucesso' => false]);
        exit;
    }
    $c = new WorkflowController();
    header('Content-Type: application/json');
    echo json_encode($c->executar((int)$m[1], $contexto));
    exit;
}

// Listar Execuções
if ($path === '/api/workflow-execucoes/list' && $method === 'GET') {
    $idWorkflow = isset($_GET['id_workflow']) ? (int)$_GET['id_workflow'] : null;
    $c = new WorkflowController();
    header('Content-Type: application/json');
    echo json_encode($c->listarExecucoes($idWorkflow));
    exit;
}

// Buscar Execução
if (preg_match('#^/api/workflow-execucoes/get/(\d+)$#', $path, $m) && $method === 'GET') {
    $c = new WorkflowController();
    header('Content-Type: application/json');
    echo json_encode($c->buscarExecucao((int)$m[1]));
    exit;
}

// Listar Rotinas Disponíveis (para uso no builder)
if ($path === '/api/workflows/rotinas-disponiveis' && $method === 'GET') {
    $c = new WorkflowController();
    header('Content-Type: application/json');
    echo json_encode($c->listarRotinasDisponiveis());
    exit;
}

// Obter estatísticas dos workflows
if ($path === '/api/workflows/stats' && $method === 'GET') {
    $c = new WorkflowController();
    header('Content-Type: application/json');
    echo json_encode($c->obterEstatisticas());
    exit;
}

// Cancelar execução
if (preg_match('#^/api/workflow-execucoes/cancelar/(\d+)$#', $path, $m) && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new WorkflowController();
    header('Content-Type: application/json');
    echo json_encode($c->cancelarExecucao((int)$m[1]));
    exit;
}

// =====================================================
// ROTAS: PIPELINES
// =====================================================

// View Lista de Pipelines
if ($path === '/pipelines') {
    include __DIR__ . '/../views/pipelines/index.php';
    exit;
}

// View Pipeline Builder (novo ou editar)
if ($path === '/pipelines/builder' || preg_match('#^/pipelines/builder/(\d+)$#', $path, $matches)) {
    $pipelineId = $matches[1] ?? null;
    include __DIR__ . '/../views/pipelines/builder.php';
    exit;
}

// API: Listar Pipelines
if ($path === '/pipelines/list' && $method === 'GET') {
    $c = new PipelineController();
    header('Content-Type: application/json');
    echo json_encode($c->listar());
    exit;
}

// API: Buscar Pipeline por ID
if (preg_match('#^/pipelines/get/(\d+)$#', $path, $m) && $method === 'GET') {
    $c = new PipelineController();
    $id = (int)$m[1];
    header('Content-Type: application/json');
    $resultado = $c->buscar($id);
    $resultado['empresas'] = \App\Servicos\ServicoPermissao::obterEmpresasDoRecurso('pipeline', $id);
    $resultado['projetos'] = \App\Servicos\ServicoPermissao::obterProjetosDoRecurso('pipeline', $id);
    echo json_encode($resultado);
    exit;
}

// API: Salvar Pipeline
if ($path === '/pipelines/salvar' && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new PipelineController();
    $data = $_POST;
    $input = file_get_contents('php://input');
    if ($input && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
        $data = json_decode($input, true);
    }
    header('Content-Type: application/json');
    $resultado = $c->salvar($data);
    \App\Servicos\ServicoAuditoria::registrar(
        isset($data['id']) && $data['id'] ? 'editar' : 'criar',
        'pipeline', (int)($resultado['id'] ?? $data['id'] ?? 0), $data['nome'] ?? ''
    );
    echo json_encode($resultado);
    exit;
}

// API: Deletar Pipeline
if (preg_match('#^/pipelines/delete/(\d+)$#', $path, $m) && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $id = (int)$m[1];
    header('Content-Type: application/json');
    if (!\App\Servicos\ServicoPermissao::podeModificarRecurso('pipeline', $id)) {
        http_response_code(403);
        echo json_encode(['erro' => 'Sem permissão para excluir este pipeline', 'sucesso' => false]);
        exit;
    }
    \App\Servicos\ServicoAuditoria::registrar('excluir', 'pipeline', $id);
    $c = new PipelineController();
    echo json_encode($c->deletar($id));
    exit;
}

// API: Executar Pipeline
if (preg_match('#^/pipelines/executar/(\d+)$#', $path, $m) && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    // Rate limiting: max 10 execuções de pipeline por minuto por sessão
    $rateLimiter = new \App\Core\RateLimiter();
    $sessionId = session_id() ?: 'anonymous';
    if (!$rateLimiter->permitir("exec_pipeline:{$sessionId}", 10, 60)) {
        http_response_code(429);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Limite de execuções excedido. Aguarde 1 minuto.', 'sucesso' => false]);
        exit;
    }
    $c = new PipelineController();
    header('Content-Type: application/json');
    echo json_encode($c->executar((int)$m[1]));
    exit;
}

// API: Duplicar Pipeline
if (preg_match('#^/pipelines/duplicar/(\d+)$#', $path, $m) && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new PipelineController();
    header('Content-Type: application/json');
    echo json_encode($c->duplicar((int)$m[1]));
    exit;
}

// API: Toggle Ativo Pipeline
if (preg_match('#^/pipelines/toggle/(\d+)$#', $path, $m) && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new PipelineController();
    header('Content-Type: application/json');
    echo json_encode($c->toggleAtivo((int)$m[1]));
    exit;
}

// API: Exportar Pipeline
if (preg_match('#^/pipelines/exportar/(\d+)$#', $path, $m) && $method === 'GET') {
    $c = new PipelineController();
    header('Content-Type: application/json');
    echo json_encode($c->exportar((int)$m[1]));
    exit;
}

// API: Importar Pipeline
if ($path === '/pipelines/importar' && $method === 'POST') {
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $c = new PipelineController();
    $data = json_decode(file_get_contents('php://input'), true);
    header('Content-Type: application/json');
    echo json_encode($c->importar($data));
    exit;
}

// API: Listar Conexões disponíveis (para nodes SQL)
if ($path === '/pipelines/conexoes' && $method === 'GET') {
    $c = new PipelineController();
    header('Content-Type: application/json');
    echo json_encode($c->listarConexoes());
    exit;
}

// API: Estatísticas de Pipelines
if ($path === '/pipelines/stats' && $method === 'GET') {
    $c = new PipelineController();
    header('Content-Type: application/json');
    echo json_encode($c->estatisticas());
    exit;
}

// API: Histórico de Execuções de um Pipeline
if (preg_match('#^/pipelines/historico/(\d+)$#', $path, $m) && $method === 'GET') {
    $c = new PipelineController();
    header('Content-Type: application/json');
    echo json_encode($c->historicoExecucoes((int)$m[1]));
    exit;
}

// API: Detalhe de uma Execução
if (preg_match('#^/pipelines/execucao/(\d+)$#', $path, $m) && $method === 'GET') {
    $c = new PipelineController();
    header('Content-Type: application/json');
    echo json_encode($c->detalheExecucao((int)$m[1]));
    exit;
}

// API: Listar APIs Externas disponíveis para nodes HTTP
if ($path === '/pipelines/apis-externas' && $method === 'GET') {
    $c = new PipelineController();
    header('Content-Type: application/json');
    echo json_encode($c->listarApisExternas());
    exit;
}

// API: Listar Eventos de API disponíveis para triggers
if ($path === '/pipelines/eventos-api' && $method === 'GET') {
    $c = new PipelineController();
    header('Content-Type: application/json');
    echo json_encode($c->listarEventosApi());
    exit;
}

// API: Listar Rotinas disponíveis para uso em pipelines
if ($path === '/pipelines/rotinas' && $method === 'GET') {
    $c = new PipelineController();
    header('Content-Type: application/json');
    echo json_encode($c->listarRotinas());
    exit;
}

// API: Listar Tabelas de uma conexão
if (preg_match('#^/pipelines/tabelas/(\d+)$#', $path, $m) && $method === 'GET') {
    $c = new PipelineController();
    header('Content-Type: application/json');
    echo json_encode($c->listarTabelas((int)$m[1]));
    exit;
}

// API: Listar Colunas de uma tabela
if (preg_match('#^/pipelines/colunas/(\d+)/([^/]+)$#', $path, $m) && $method === 'GET') {
    $c = new PipelineController();
    $schema = $_GET['schema'] ?? '';
    header('Content-Type: application/json');
    echo json_encode($c->listarColunas((int)$m[1], urldecode($m[2]), $schema));
    exit;
}

// Debug: criar usuário de teste no DB interno
if ($path === '/debug/create_user' && $method === 'GET') {
    $db = Database::getConexao();
    $sql = "INSERT INTO tb_usuarios (nome_usuario, senha_hash, eh_ldap, nivel_acesso) VALUES (" . $db->quote('debug_user') . ", NULL, false, " . $db->quote('admin') . ") RETURNING id";
    $res = $db->query($sql);
    $id = $res ? $res->fetchColumn() : null;
    header('Content-Type: application/json');
    echo json_encode(['id' => $id]);
    exit;
}

echo "<h1>DMC DataLoad</h1><p>Front controller mínimo. Configure o server document root em public/</p>";

// ========== API FILA DE EXECUÇÃO (Background Queue) ==========

// Página de fila
if ($path === '/admin/fila') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    include __DIR__ . '/../views/admin/fila.php';
    exit;
}

// Enfileirar execução
if ($path === '/api/fila/enfileirar' && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('desenvolvedor');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $tipo = $_POST['tipo'] ?? '';
    $idRecurso = (int)($_POST['id_recurso'] ?? 0);
    $nomeRecurso = $_POST['nome_recurso'] ?? '';
    $prioridade = (int)($_POST['prioridade'] ?? 5);
    if (!in_array($tipo, ['rotina', 'pipeline', 'workflow']) || $idRecurso <= 0) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['sucesso' => false, 'erro' => 'Tipo e id_recurso são obrigatórios']);
        exit;
    }
    header('Content-Type: application/json');
    echo json_encode(\App\Servicos\ServicoFila::enfileirar($tipo, $idRecurso, $nomeRecurso, $prioridade));
    exit;
}

// Status de um item da fila
if (preg_match('#^/api/fila/status/(\d+)$#', $path, $m) && $method === 'GET') {
    header('Content-Type: application/json');
    $item = \App\Servicos\ServicoFila::status((int)$m[1]);
    echo json_encode($item ? ['sucesso' => true, 'dados' => $item] : ['sucesso' => false, 'erro' => 'Item não encontrado']);
    exit;
}

// Listar fila
if ($path === '/api/fila/listar' && $method === 'GET') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $status = $_GET['status'] ?? null;
    $tipo = $_GET['tipo'] ?? null;
    $limite = min(100, max(1, (int)($_GET['limite'] ?? 50)));
    $offset = max(0, (int)($_GET['offset'] ?? 0));
    header('Content-Type: application/json');
    echo json_encode(array_merge(['sucesso' => true], \App\Servicos\ServicoFila::listar($status, $tipo, $limite, $offset)));
    exit;
}

// Estatísticas da fila
if ($path === '/api/fila/stats' && $method === 'GET') {
    header('Content-Type: application/json');
    echo json_encode(['sucesso' => true, 'dados' => \App\Servicos\ServicoFila::estatisticas()]);
    exit;
}

// Cancelar item da fila
if (preg_match('#^/api/fila/cancelar/(\d+)$#', $path, $m) && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $ok = \App\Servicos\ServicoFila::cancelar((int)$m[1]);
    header('Content-Type: application/json');
    echo json_encode(['sucesso' => $ok, 'mensagem' => $ok ? 'Cancelado' : 'Item não encontrado ou não pode ser cancelado']);
    exit;
}

// ========== API CANAIS DE NOTIFICAÇÃO (Slack/Teams) ==========

// Página de canais
if ($path === '/admin/canais') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    include __DIR__ . '/../views/admin/canais.php';
    exit;
}

// Listar canais
if ($path === '/api/canais/listar' && $method === 'GET') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    header('Content-Type: application/json');
    echo json_encode(['sucesso' => true, 'dados' => \App\Servicos\ServicoCanalNotificacao::listar()]);
    exit;
}

// Salvar canal
if ($path === '/api/canais/salvar' && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    header('Content-Type: application/json');
    echo json_encode(\App\Servicos\ServicoCanalNotificacao::salvar($_POST));
    exit;
}

// Deletar canal
if (preg_match('#^/api/canais/delete/(\d+)$#', $path, $m) && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    header('Content-Type: application/json');
    echo json_encode(\App\Servicos\ServicoCanalNotificacao::deletar((int)$m[1]));
    exit;
}

// Testar canal
if (preg_match('#^/api/canais/testar/(\d+)$#', $path, $m) && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('admin');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    header('Content-Type: application/json');
    echo json_encode(\App\Servicos\ServicoCanalNotificacao::testar((int)$m[1]));
    exit;
}

// ========== API BACKUP/RESTORE ==========

// Página de backups
if ($path === '/admin/backups') {
    \App\Servicos\ServicoPermissao::exigirNivel('super_admin');
    include __DIR__ . '/../views/admin/backups.php';
    exit;
}

// Listar backups
if ($path === '/api/backups/listar' && $method === 'GET') {
    \App\Servicos\ServicoPermissao::exigirNivel('super_admin');
    header('Content-Type: application/json');
    echo json_encode(['sucesso' => true, 'dados' => \App\Servicos\ServicoBackup::listar()]);
    exit;
}

// Criar backup
if ($path === '/api/backups/criar' && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('super_admin');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    $tipo = $_POST['tipo'] ?? 'completo';
    header('Content-Type: application/json');
    echo json_encode(\App\Servicos\ServicoBackup::criar($tipo));
    exit;
}

// Download backup
if (preg_match('#^/api/backups/download/(\d+)$#', $path, $m) && $method === 'GET') {
    \App\Servicos\ServicoPermissao::exigirNivel('super_admin');
    \App\Servicos\ServicoBackup::download((int)$m[1]);
    exit;
}

// Restaurar backup
if ($path === '/api/backups/restaurar' && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('super_admin');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    if (!isset($_FILES['arquivo'])) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['sucesso' => false, 'erro' => 'Nenhum arquivo enviado']);
        exit;
    }
    header('Content-Type: application/json');
    echo json_encode(\App\Servicos\ServicoBackup::restaurar($_FILES['arquivo']));
    exit;
}

// Deletar backup
if (preg_match('#^/api/backups/delete/(\d+)$#', $path, $m) && $method === 'POST') {
    \App\Servicos\ServicoPermissao::exigirNivel('super_admin');
    $csrfToken = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!AuthMiddleware::validarTokenCSRF($csrfToken)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Token CSRF inválido', 'sucesso' => false]);
        exit;
    }
    header('Content-Type: application/json');
    echo json_encode(\App\Servicos\ServicoBackup::deletar((int)$m[1]));
    exit;
}
