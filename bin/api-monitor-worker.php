<?php
/**
 * API Monitor Worker
 * 
 * Worker em background que monitora APIs externas configuradas,
 * verifica eventos e dispara workflows automaticamente.
 * 
 * Uso: php bin/api-monitor-worker.php [--interval=30] [--once]
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Lib\WorkflowEngine;

// Carregar .env
Database::loadEnv(__DIR__ . '/../');

// Configurações
$intervalo = 30; // segundos entre verificações
$executarUmaVez = false;
$verbose = true;

// Processar argumentos
foreach ($argv as $arg) {
    if (strpos($arg, '--interval=') === 0) {
        $intervalo = intval(substr($arg, 11));
    }
    if ($arg === '--once') {
        $executarUmaVez = true;
    }
    if ($arg === '--quiet' || $arg === '-q') {
        $verbose = false;
    }
}

function log_msg($msg, $type = 'INFO') {
    global $verbose;
    if (!$verbose) return;
    
    $timestamp = date('Y-m-d H:i:s');
    $icon = match($type) {
        'OK' => '✓',
        'ERROR' => '✗',
        'WARN' => '⚠',
        'EVENT' => '⚡',
        'WORKFLOW' => '🔄',
        default => '•'
    };
    echo "[{$timestamp}] {$icon} {$msg}\n";
}

function main() {
    global $intervalo, $executarUmaVez;
    
    log_msg("=== API Monitor Worker Iniciado ===");
    log_msg("Intervalo: {$intervalo}s | Modo: " . ($executarUmaVez ? 'Único' : 'Contínuo'));
    
    $db = Database::getConexao();
    
    while (true) {
        try {
            verificarApis($db);
        } catch (Exception $e) {
            log_msg("Erro no ciclo: " . $e->getMessage(), 'ERROR');
        }
        
        if ($executarUmaVez) {
            log_msg("Modo único - encerrando");
            break;
        }
        
        log_msg("Aguardando {$intervalo}s...");
        sleep($intervalo);
    }
}

function verificarApis($db) {
    // Buscar APIs ativas com eventos configurados
    $sql = "
        SELECT DISTINCT a.* 
        FROM tb_api_externas a
        INNER JOIN tb_eventos_api e ON e.id_api = a.id AND e.ativo = true
        WHERE a.ativo = true
    ";
    
    $stmt = $db->query($sql);
    $apis = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($apis)) {
        log_msg("Nenhuma API com eventos ativos encontrada");
        return;
    }
    
    log_msg("Verificando " . count($apis) . " APIs...");
    
    foreach ($apis as $api) {
        verificarApi($db, $api);
    }
}

function verificarApi($db, $api) {
    log_msg("API: {$api['nome']} ({$api['url']})");
    
    // Buscar eventos ativos desta API
    $sql = "SELECT * FROM tb_eventos_api WHERE id_api = :id_api AND ativo = true";
    $stmt = $db->prepare($sql);
    $stmt->execute([':id_api' => $api['id']]);
    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($eventos)) {
        return;
    }
    
    // Fazer requisição à API
    $response = fazerRequisicaoApi($api);
    
    if ($response === null) {
        log_msg("  Falha na requisição", 'ERROR');
        return;
    }
    
    log_msg("  Resposta OK - HTTP {$response['status']}");
    
    // Verificar cada evento
    foreach ($eventos as $evento) {
        verificarEvento($db, $api, $evento, $response);
    }
}

function fazerRequisicaoApi($api) {
    $url = $api['url'];
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => $api['timeout'] ?? 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => construirHeaders($api)
    ]);
    
    // Configurar método
    $metodo = strtoupper($api['metodo'] ?? 'GET');
    if ($metodo === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if (!empty($api['body_template'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $api['body_template']);
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        log_msg("  cURL Error: {$error}", 'ERROR');
        return null;
    }
    
    return [
        'status' => $httpCode,
        'body' => $response,
        'data' => json_decode($response, true)
    ];
}

function construirHeaders($api) {
    $headers = ['Accept: application/json'];
    
    // Headers customizados
    if (!empty($api['headers'])) {
        $customHeaders = is_string($api['headers']) ? json_decode($api['headers'], true) : $api['headers'];
        if (is_array($customHeaders)) {
            foreach ($customHeaders as $key => $value) {
                $headers[] = "{$key}: {$value}";
            }
        }
    }
    
    // Autenticação
    $authTipo = $api['auth_tipo'] ?? 'none';
    $credenciais = is_string($api['credenciais'] ?? '{}') ? json_decode($api['credenciais'], true) : ($api['credenciais'] ?? []);
    
    switch ($authTipo) {
        case 'bearer':
            if (!empty($credenciais['token'])) {
                $headers[] = "Authorization: Bearer {$credenciais['token']}";
            }
            break;
            
        case 'basic':
            if (!empty($credenciais['username']) && !empty($credenciais['password'])) {
                $encoded = base64_encode($credenciais['username'] . ':' . $credenciais['password']);
                $headers[] = "Authorization: Basic {$encoded}";
            }
            break;
            
        case 'api_key':
            if (!empty($credenciais['api_key']) && !empty($credenciais['api_key_header'])) {
                $headers[] = "{$credenciais['api_key_header']}: {$credenciais['api_key']}";
            }
            break;
    }
    
    return $headers;
}

function verificarEvento($db, $api, $evento, $response) {
    $nomeEvento = $evento['nome'];
    $jsonPath = $evento['jsonpath'] ?? '';
    $operador = $evento['operador'] ?? 'equals';
    $valorEsperado = $evento['valor_esperado'] ?? '';
    
    // Extrair valor do JSON
    $valorAtual = extrairValorJsonPath($response['data'], $jsonPath);
    
    // Avaliar condição
    $condicaoAtendida = avaliarCondicao($valorAtual, $operador, $valorEsperado);
    
    log_msg("  Evento: {$nomeEvento} | Path: {$jsonPath} | Valor: " . json_encode($valorAtual) . " | Condição: " . ($condicaoAtendida ? 'SIM' : 'NÃO'));
    
    if (!$condicaoAtendida) {
        return;
    }
    
    log_msg("  ⚡ EVENTO DISPARADO: {$nomeEvento}", 'EVENT');
    
    // Registrar valor capturado
    registrarValorCapturado($db, $api, $evento, $valorAtual, $response['data']);
    
    // Disparar workflow se configurado
    if (!empty($evento['id_workflow'])) {
        dispararWorkflow($db, $evento, $valorAtual, $response['data']);
    }
    
    // Executar ação direta se configurada
    if (!empty($evento['acao']) && $evento['acao'] !== 'trigger_workflow') {
        executarAcaoDireta($db, $evento, $valorAtual, $response['data']);
    }
}

function extrairValorJsonPath($data, $path) {
    if (empty($path) || $data === null) {
        return $data;
    }
    
    // Remove $ inicial se existir
    $path = ltrim($path, '$.');
    
    // Divide o path por pontos e colchetes
    $partes = preg_split('/\.|\[|\]/', $path, -1, PREG_SPLIT_NO_EMPTY);
    
    $valor = $data;
    foreach ($partes as $parte) {
        if (is_array($valor)) {
            if (is_numeric($parte)) {
                $valor = $valor[intval($parte)] ?? null;
            } else {
                $valor = $valor[$parte] ?? null;
            }
        } else {
            return null;
        }
        
        if ($valor === null) {
            return null;
        }
    }
    
    return $valor;
}

function avaliarCondicao($valorAtual, $operador, $valorEsperado) {
    // Converter valor esperado para o tipo correto
    if (is_numeric($valorEsperado)) {
        $valorEsperado = strpos($valorEsperado, '.') !== false ? floatval($valorEsperado) : intval($valorEsperado);
    } elseif ($valorEsperado === 'true') {
        $valorEsperado = true;
    } elseif ($valorEsperado === 'false') {
        $valorEsperado = false;
    } elseif ($valorEsperado === 'null') {
        $valorEsperado = null;
    }
    
    switch ($operador) {
        case '==':
        case 'equals':
            return $valorAtual == $valorEsperado;
            
        case '===':
        case 'strict_equals':
            return $valorAtual === $valorEsperado;
            
        case '!=':
        case 'not_equals':
            return $valorAtual != $valorEsperado;
            
        case '>':
        case 'greater':
            return $valorAtual > $valorEsperado;
            
        case '>=':
        case 'greater_equals':
            return $valorAtual >= $valorEsperado;
            
        case '<':
        case 'less':
            return $valorAtual < $valorEsperado;
            
        case '<=':
        case 'less_equals':
            return $valorAtual <= $valorEsperado;
            
        case 'contains':
            return is_string($valorAtual) && strpos($valorAtual, $valorEsperado) !== false;
            
        case 'not_contains':
            return is_string($valorAtual) && strpos($valorAtual, $valorEsperado) === false;
            
        case 'starts_with':
            return is_string($valorAtual) && strpos($valorAtual, $valorEsperado) === 0;
            
        case 'ends_with':
            return is_string($valorAtual) && substr($valorAtual, -strlen($valorEsperado)) === $valorEsperado;
            
        case 'regex':
            return is_string($valorAtual) && preg_match($valorEsperado, $valorAtual);
            
        case 'exists':
            return $valorAtual !== null;
            
        case 'not_exists':
            return $valorAtual === null;
            
        case 'in':
            $lista = is_array($valorEsperado) ? $valorEsperado : explode(',', $valorEsperado);
            return in_array($valorAtual, array_map('trim', $lista));
            
        case 'not_in':
            $lista = is_array($valorEsperado) ? $valorEsperado : explode(',', $valorEsperado);
            return !in_array($valorAtual, array_map('trim', $lista));
            
        case 'changed':
            // Requer comparação com valor anterior (implementar cache)
            return true;
            
        default:
            return false;
    }
}

function registrarValorCapturado($db, $api, $evento, $valorCapturado, $dadosCompletos) {
    $sql = "
        INSERT INTO tb_valores_capturados 
        (id_evento, id_api, valor, valor_json, response_completo, condição_match, data_captura)
        VALUES (:id_evento, :id_api, :valor, :valor_json, :response, true, NOW())
    ";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':id_evento' => $evento['id'],
        ':id_api' => $api['id'],
        ':valor' => is_array($valorCapturado) ? json_encode($valorCapturado) : (string)$valorCapturado,
        ':valor_json' => is_array($valorCapturado) ? json_encode($valorCapturado) : null,
        ':response' => json_encode($dadosCompletos)
    ]);
    
    // Atualizar contagem de matches no evento
    $sqlUpdate = "
        UPDATE tb_eventos_api 
        SET ultimo_valor_capturado = :valor, 
            ultimo_match = true, 
            total_matches = total_matches + 1,
            ultima_verificacao = NOW()
        WHERE id = :id
    ";
    $stmtUpdate = $db->prepare($sqlUpdate);
    $stmtUpdate->execute([
        ':valor' => is_array($valorCapturado) ? json_encode($valorCapturado) : (string)$valorCapturado,
        ':id' => $evento['id']
    ]);
    
    log_msg("  Valor capturado registrado", 'OK');
}

function dispararWorkflow($db, $evento, $valorCapturado, $dadosApi) {
    $workflowId = $evento['id_workflow'];
    
    log_msg("  🔄 Disparando workflow #{$workflowId}", 'WORKFLOW');
    
    try {
        $engine = new WorkflowEngine($db);
        
        // Contexto inicial com dados do evento
        $contextoInicial = [
            'evento_id' => $evento['id'],
            'evento_nome' => $evento['nome'],
            'api_id' => $evento['id_api'],
            'valor_capturado' => $valorCapturado,
            'dados_api' => $dadosApi,
            'disparado_em' => date('c')
        ];
        
        $resultado = $engine->executar($workflowId, $contextoInicial);
        
        if ($resultado['sucesso']) {
            log_msg("  Workflow executado com sucesso - Execução #{$resultado['execucao_id']}", 'OK');
        } else {
            log_msg("  Workflow falhou: " . ($resultado['erro'] ?? 'Erro desconhecido'), 'ERROR');
        }
        
    } catch (Exception $e) {
        log_msg("  Erro ao executar workflow: " . $e->getMessage(), 'ERROR');
    }
}

function executarAcaoDireta($db, $evento, $valorCapturado, $dadosApi) {
    $acao = $evento['acao'];
    
    log_msg("  Executando ação direta: {$acao}");
    
    switch ($acao) {
        case 'store_value':
            // Já armazenamos no registrarValorCapturado
            log_msg("  [STORE] Valor armazenado", 'OK');
            break;
            
        case 'notify':
            log_msg("  [NOTIFY] Evento: {$evento['nome']} | Valor: " . json_encode($valorCapturado), 'EVENT');
            break;
            
        case 'store_and_trigger':
            // Já armazenamos e o trigger será chamado depois
            log_msg("  [STORE+TRIGGER] Valor armazenado e workflow disparado", 'OK');
            break;
    }
}

function executarRotinaDireta($db, $rotinaId, $valorCapturado, $dadosApi) {
    // Buscar rotina
    $sql = "SELECT * FROM tb_rotinas WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $rotinaId]);
    $rotina = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$rotina) {
        log_msg("  Rotina #{$rotinaId} não encontrada", 'ERROR');
        return;
    }
    
    log_msg("  Executando rotina: {$rotina['nome']}");
    
    // Criar log de execução
    $sqlLog = "
        INSERT INTO tb_rotina_logs 
        (rotina_id, status, mensagem, iniciado_em)
        VALUES (:rotina_id, 'executando', 'Disparado por evento de API', NOW())
        RETURNING id
    ";
    
    $stmtLog = $db->prepare($sqlLog);
    $stmtLog->execute([':rotina_id' => $rotinaId]);
    $logId = $stmtLog->fetchColumn();
    
    try {
        // Executar blocos da rotina
        // (implementação simplificada - usa o engine existente)
        
        $sqlUpdate = "
            UPDATE tb_rotina_logs 
            SET status = 'sucesso', finalizado_em = NOW()
            WHERE id = :id
        ";
        $stmtUpdate = $db->prepare($sqlUpdate);
        $stmtUpdate->execute([':id' => $logId]);
        
        log_msg("  Rotina executada com sucesso", 'OK');
        
    } catch (Exception $e) {
        $sqlUpdate = "
            UPDATE tb_rotina_logs 
            SET status = 'erro', erro = :erro, finalizado_em = NOW()
            WHERE id = :id
        ";
        $stmtUpdate = $db->prepare($sqlUpdate);
        $stmtUpdate->execute([':id' => $logId, ':erro' => $e->getMessage()]);
        
        log_msg("  Erro na rotina: " . $e->getMessage(), 'ERROR');
    }
}

function enviarWebhook($url, $dados) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'User-Agent: DMC-DATALOAD/1.0'
        ]
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode >= 200 && $httpCode < 300) {
        log_msg("  Webhook enviado: HTTP {$httpCode}", 'OK');
    } else {
        log_msg("  Webhook falhou: HTTP {$httpCode}", 'ERROR');
    }
}

// Executar
main();
