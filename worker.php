<?php
/**
 * DMC DataLoad - Background Queue Worker
 * 
 * Processa itens da fila de execução em background.
 * Uso: php worker.php [--once] [--sleep=5]
 * 
 * Opções:
 *   --once   Processa um item e sai
 *   --sleep  Segundos entre verificações (padrão: 5)
 */

// Impedir execução via web
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('Este script só pode ser executado via CLI.');
}

require __DIR__ . '/vendor/autoload.php';

use App\Servicos\ServicoFila;
use App\Servicos\ServicoExecucao;
use App\Servicos\ServicoNotificacao;
use App\Controllers\PipelineController;
use App\Controllers\WorkflowController;

$once = in_array('--once', $argv ?? []);
$sleep = 5;
foreach ($argv ?? [] as $arg) {
    if (strpos($arg, '--sleep=') === 0) {
        $sleep = max(1, (int)substr($arg, 8));
    }
}

$workerId = 'worker-' . gethostname() . '-' . getmypid();
echo "[{$workerId}] Worker iniciado" . ($once ? ' (modo once)' : '') . "\n";

// Registrar handler de shutdown para limpar items travados
register_shutdown_function(function () use ($workerId) {
    echo "[{$workerId}] Worker encerrando...\n";
});

$running = true;
if (function_exists('pcntl_async_signals')) {
    pcntl_async_signals(true);
}
if (function_exists('pcntl_signal')) {
    pcntl_signal(SIGTERM, function () use (&$running, $workerId) {
        echo "[{$workerId}] Recebido SIGTERM, encerrando gracefully...\n";
        $running = false;
    });
    pcntl_signal(SIGINT, function () use (&$running, $workerId) {
        echo "[{$workerId}] Recebido SIGINT, encerrando gracefully...\n";
        $running = false;
    });
}

while ($running) {
    try {
        // Recuperar itens travados
        $recuperados = ServicoFila::recuperarTravados();
        if ($recuperados > 0) {
            echo "[{$workerId}] Recuperados {$recuperados} itens travados\n";
        }
        
        // Obter próximo item
        $item = ServicoFila::obterProximo($workerId);
        
        if (!$item) {
            if ($once) break;
            sleep($sleep);
            continue;
        }
        
        $tipo = $item['tipo'];
        $idRecurso = (int)$item['id_recurso'];
        $filaId = (int)$item['id'];
        
        echo "[{$workerId}] Processando #{$filaId}: {$tipo} #{$idRecurso} ({$item['nome_recurso']})\n";
        
        $resultado = processarItem($tipo, $idRecurso);
        
        if ($resultado['sucesso']) {
            ServicoFila::concluir($filaId, $resultado);
            echo "[{$workerId}] #{$filaId} concluído com sucesso\n";
        } else {
            ServicoFila::falhar($filaId, $resultado['erro'] ?? 'Erro desconhecido', $resultado);
            echo "[{$workerId}] #{$filaId} falhou: " . ($resultado['erro'] ?? 'Erro desconhecido') . "\n";
        }
        
    } catch (\Throwable $e) {
        echo "[{$workerId}] Erro: {$e->getMessage()}\n";
        if (isset($filaId)) {
            ServicoFila::falhar($filaId, $e->getMessage());
        }
        sleep(1);
    }
    
    if ($once) break;
}

echo "[{$workerId}] Worker encerrado\n";

/**
 * Processar um item da fila de acordo com o tipo
 */
function processarItem(string $tipo, int $idRecurso): array
{
    switch ($tipo) {
        case 'rotina':
            $svc = new ServicoExecucao();
            return $svc->executarRotina($idRecurso);
            
        case 'pipeline':
            $ctrl = new PipelineController();
            return $ctrl->executarPipeline($idRecurso);
            
        case 'workflow':
            $ctrl = new WorkflowController();
            return $ctrl->executarWorkflow($idRecurso);
            
        default:
            return ['sucesso' => false, 'erro' => "Tipo desconhecido: {$tipo}"];
    }
}
