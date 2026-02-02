<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Core\AuthMiddleware;
use App\Core\ErrorHandler;
use App\Core\Logger;
use App\Controladores\ConexoesController;
use App\Controladores\RotinasController2 as RotinasController;
use App\Controladores\ApiController;
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
$rotasPublicas = ['/', '/login', '/api/health', '/api/versao', '/api/metrics'];
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
    try {
        $usuario = $_POST['usuario'] ?? '';
        $senha = $_POST['senha'] ?? '';
        
        $svc = new ServicoAutenticacao();
        $resultado = $svc->autenticar($usuario, $senha);
        
        if ($resultado['sucesso']) {
            AuthMiddleware::definirUsuario($resultado['usuario']);
            header('Content-Type: application/json');
            echo json_encode(['sucesso' => true]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['sucesso' => false, 'erro' => $resultado['mensagem']]);
        }
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(ErrorHandler::tratarErro($e, 'Erro ao autenticar'));
    }
    exit;
}

// Logout
if ($path === '/logout' && $method === 'POST') {
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
    if (strpos($path, '/api/') === 0) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Não autenticado']);
    } else {
        header('Location: ' . BASE_URL . '/login');
    }
    exit;
}

// Dashboard
if ($path === '/' || $path === '/dashboard') {
    include __DIR__ . '/../views/dashboard_new.php';
    exit;
}

// API Dashboard métricas
if ($path === '/api/dashboard/metricas' && $method === 'GET') {
    try {
        $db = Database::getConexao();
        
        // Total de rotinas
        $total = $db->query("SELECT COUNT(*) FROM tb_rotinas")->fetchColumn();
        
        // Rotinas em execução
        $emExec = $db->query("SELECT COUNT(*) FROM tb_rotinas WHERE esta_executando = true")->fetchColumn();
        
        // Execuções hoje
        $execHoje = $db->query("SELECT COUNT(*) FROM tb_logs_execucao WHERE data_inicio >= CURRENT_DATE")->fetchColumn();
        
        // Falhas hoje
        $falhasHoje = $db->query("SELECT COUNT(*) FROM tb_logs_execucao WHERE status = 'falha' AND data_inicio >= CURRENT_DATE")->fetchColumn();
        
        // Rotinas ativas (agendadas)
        $ativas = $db->query("SELECT COUNT(*) FROM tb_rotinas WHERE ativa = true AND agendamento_cron IS NOT NULL")->fetchColumn();
        
        // Próximas execuções (5)
        $proximas = $db->query("SELECT r.id, r.nome, r.proxima_execucao, p.nome_conexao as conexao
            FROM tb_rotinas r 
            LEFT JOIN tb_perfis_conexao p ON r.id_conexao = p.id
            WHERE r.ativa = true AND r.proxima_execucao IS NOT NULL 
            ORDER BY r.proxima_execucao ASC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        
        // Últimas execuções (10)
        $ultimas = $db->query("SELECT l.id, l.status, l.data_inicio, l.duracao_ms, r.nome as rotina
            FROM tb_logs_execucao l 
            LEFT JOIN tb_rotinas r ON l.id_rotina = r.id
            ORDER BY l.data_inicio DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
        
        // Dados para gráfico (últimos 7 dias)
        $grafico = $db->query("SELECT 
            DATE(data_inicio) as data,
            COUNT(*) FILTER (WHERE status = 'sucesso') as sucesso,
            COUNT(*) FILTER (WHERE status = 'falha') as falha
            FROM tb_logs_execucao 
            WHERE data_inicio >= CURRENT_DATE - INTERVAL '7 days'
            GROUP BY DATE(data_inicio)
            ORDER BY data ASC")->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode([
            'sucesso' => true,
            'total_rotinas' => (int)$total,
            'execucoes_hoje' => (int)$execHoje,
            'falhas_hoje' => (int)$falhasHoje,
            'em_execucao' => (int)$emExec,
            'rotinas_ativas' => (int)$ativas,
            'proximas_execucoes' => $proximas,
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
    $data = $_POST;
    $c = new ConexoesController();
    header('Content-Type: application/json');
    echo json_encode($c->testarConexao($data));
    exit;
}

if ($path === '/conexoes/salvar' && $method === 'POST') {
    $data = $_POST;
    $c = new ConexoesController();
    header('Content-Type: application/json');
    echo json_encode($c->salvar($data));
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
    echo json_encode($c->buscar($id));
    exit;
}

if (preg_match('#^/conexoes/delete/(\d+)$#', $path, $m) && $method === 'POST') {
    $id = intval($m[1]);
    $c = new ConexoesController();
    header('Content-Type: application/json');
    echo json_encode($c->deletar($id));
    exit;
}

if (preg_match('#^/rotinas/run/(\d+)$#', $path, $m) && $method === 'POST') {
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
    echo json_encode($c->buscar($id));
    exit;
}

if ($path === '/rotinas/salvar' && $method === 'POST') {
    $data = $_POST;
    $c = new RotinasController();
    header('Content-Type: application/json');
    echo json_encode($c->salvar($data));
    exit;
}

if (preg_match('#^/rotinas/delete/(\d+)$#', $path, $m) && $method === 'POST') {
    $id = intval($m[1]);
    $c = new RotinasController();
    header('Content-Type: application/json');
    echo json_encode($c->deletar($id));
    exit;
}

// Toggle ativa rotina
if (preg_match('#^/rotinas/toggle/(\d+)$#', $path, $m) && $method === 'POST') {
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
    include __DIR__ . '/../views/conexoes_new.php';
    exit;
}

if ($path === '/rotinas') {
    include __DIR__ . '/../views/rotinas_new.php';
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
    include __DIR__ . '/../views/historico_new.php';
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

// Logs do Sistema
if ($path === '/logs') {
    include __DIR__ . '/../views/logs.php';
    exit;
}

// Configurações
if ($path === '/configuracoes') {
    include __DIR__ . '/../views/configuracoes.php';
    exit;
}

// Admin - Usuários
if ($path === '/admin/usuarios') {
    include __DIR__ . '/../views/admin/usuarios.php';
    exit;
}

// SQL Editor
if ($path === '/sql-editor') {
    include __DIR__ . '/../views/sql_editor.php';
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
    $c = new \App\Controllers\SchedulerController();
    $c->start();
    exit;
}

if ($path === '/api/scheduler/stop' && $method === 'POST') {
    $c = new \App\Controllers\SchedulerController();
    $c->stop();
    exit;
}

if ($path === '/api/scheduler/toggle' && $method === 'POST') {
    $c = new \App\Controllers\SchedulerController();
    $c->toggle();
    exit;
}

if ($path === '/api/scheduler/atualizar' && $method === 'POST') {
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
    $c = new \App\Controllers\SchedulerController();
    $c->salvar();
    exit;
}

// ========== API SQL EDITOR ==========

if (preg_match('#^/sql-editor/connect/(\d+)$#', $path, $m) && $method === 'GET') {
    $id = intval($m[1]);
    $c = new \App\Controladores\SqlEditorController();
    header('Content-Type: application/json');
    echo json_encode($c->connect($id));
    exit;
}

if (preg_match('#^/sql-editor/objects/(\d+)$#', $path, $m) && $method === 'GET') {
    $id = intval($m[1]);
    $c = new \App\Controladores\SqlEditorController();
    header('Content-Type: application/json');
    echo json_encode($c->getObjects($id));
    exit;
}

if ($path === '/sql-editor/execute' && $method === 'POST') {
    $data = $_POST;
    $c = new \App\Controladores\SqlEditorController();
    header('Content-Type: application/json');
    echo json_encode($c->execute($data));
    exit;
}

if (preg_match('#^/api/scheduler/detalhes/(\d+)$#', $path, $matches) && $method === 'GET') {
    $_GET['id'] = $matches[1];
    $c = new \App\Controllers\SchedulerController();
    $c->detalhes();
    exit;
}

if ($path === '/api/scheduler/excluir' && $method === 'POST') {
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
    $c = new \App\Controllers\UsersController();
    $c->listar();
    exit;
}

if ($path === '/admin/usuarios/salvar' && $method === 'POST') {
    $c = new \App\Controllers\UsersController();
    $c->salvar();
    exit;
}

if (preg_match('#^/admin/usuarios/get/(\d+)$#', $path, $m) && $method === 'GET') {
    $c = new \App\Controllers\UsersController();
    $c->get((int)$m[1]);
    exit;
}

if (preg_match('#^/admin/usuarios/delete/(\d+)$#', $path, $m) && $method === 'POST') {
    $c = new \App\Controllers\UsersController();
    $c->delete((int)$m[1]);
    exit;
}

if ($path === '/admin/usuarios/reset-senha' && $method === 'POST') {
    $c = new \App\Controllers\UsersController();
    $c->resetSenha();
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

// API Histórico - Listar
if ($path === '/api/historico' && $method === 'GET') {
    try {
        $db = Database::getConexao();
        
        $filtros = [];
        $params = [];
        
        if (!empty($_GET['rotina'])) {
            $filtros[] = "l.id_rotina = ?";
            $params[] = (int)$_GET['rotina'];
        }
        if (!empty($_GET['status'])) {
            $filtros[] = "l.status = ?";
            $params[] = $_GET['status'];
        }
        if (!empty($_GET['data_inicio'])) {
            $filtros[] = "l.data_inicio >= ?";
            $params[] = $_GET['data_inicio'] . ' 00:00:00';
        }
        if (!empty($_GET['data_fim'])) {
            $filtros[] = "l.data_inicio <= ?";
            $params[] = $_GET['data_fim'] . ' 23:59:59';
        }
        
        $where = count($filtros) > 0 ? ' AND ' . implode(' AND ', $filtros) : '';
        
        // Buscar execuções
        $sql = "SELECT l.*, r.nome as nome_rotina 
                FROM tb_logs_execucao l 
                LEFT JOIN tb_rotinas r ON l.id_rotina = r.id 
                WHERE 1=1 {$where}
                ORDER BY l.data_inicio DESC 
                LIMIT 500";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Estatísticas
        $stats = $db->query("SELECT 
            SUM(CASE WHEN status = 'sucesso' AND data_inicio >= NOW() - INTERVAL '24 hours' THEN 1 ELSE 0 END) as sucesso_24h,
            SUM(CASE WHEN status = 'falha' AND data_inicio >= NOW() - INTERVAL '24 hours' THEN 1 ELSE 0 END) as falhas_24h,
            SUM(CASE WHEN status = 'executando' THEN 1 ELSE 0 END) as executando,
            AVG(duracao_ms) FILTER (WHERE duracao_ms IS NOT NULL) as tempo_medio_ms
            FROM tb_logs_execucao")->fetch(PDO::FETCH_ASSOC);
        
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

// API Histórico - Detalhes
if (preg_match('#^/api/historico/(\d+)$#', $path, $m) && $method === 'GET') {
    try {
        $id = (int)$m[1];
        $db = Database::getConexao();
        
        $stmt = $db->prepare("SELECT l.*, r.nome as nome_rotina 
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
        
        // Carregar logs detalhados do campo meta (JSONB)
        $log['logs'] = [];
        if (!empty($log['meta'])) {
            // Se meta vier como string JSON, decodificar
            if (is_string($log['meta'])) {
                $log['logs'] = json_decode($log['meta'], true) ?? [];
            } else {
                // Se meta já for array (PDO pode retornar JSONB como array)
                $log['logs'] = $log['meta'];
            }
        }
        // Fallback para detalhes_json (compatibilidade)
        elseif (!empty($log['detalhes_json'])) {
            $log['logs'] = json_decode($log['detalhes_json'], true) ?? [];
        }
        
        header('Content-Type: application/json');
        echo json_encode(['sucesso' => true, 'dados' => $log]);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(ErrorHandler::tratarErro($e));
    }
    exit;
}

// API Histórico - Exportar CSV
if ($path === '/api/historico/exportar' && $method === 'GET') {
    try {
        $db = Database::getConexao();
        
        $filtros = [];
        $params = [];
        
        if (!empty($_GET['rotina'])) {
            $filtros[] = "l.id_rotina = ?";
            $params[] = (int)$_GET['rotina'];
        }
        if (!empty($_GET['status'])) {
            $filtros[] = "l.status = ?";
            $params[] = $_GET['status'];
        }
        
        $where = count($filtros) > 0 ? ' AND ' . implode(' AND ', $filtros) : '';
        
        $sql = "SELECT l.id, r.nome as rotina, l.status, l.data_inicio, l.data_fim, 
                       l.duracao_ms, l.registros_processados, l.mensagem_erro
                FROM tb_logs_execucao l 
                LEFT JOIN tb_rotinas r ON l.id_rotina = r.id 
                WHERE 1=1 {$where}
                ORDER BY l.data_inicio DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="historico_execucoes_' . date('Y-m-d_His') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
        
        // Header
        fputcsv($output, ['ID', 'Rotina', 'Status', 'Início', 'Fim', 'Duração (ms)', 'Registros', 'Erro'], ';');
        
        foreach ($dados as $row) {
            fputcsv($output, [
                $row['id'],
                $row['rotina'],
                $row['status'],
                $row['data_inicio'],
                $row['data_fim'],
                $row['duracao_ms'],
                $row['registros_processados'],
                $row['mensagem_erro']
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
