<?php
namespace App\Controllers;

use App\Core\Database;
use App\Core\AuthMiddleware;
use App\Utils\Crypto;
use PDO;

class PipelineController
{
    /**
     * Lista todos os pipelines com estatísticas
     */
    public function listar(): array
    {
        $db = Database::getConexao();
        $filtro = \App\Servicos\ServicoPermissao::filtroVisibilidade('pipeline', 'p', 'criado_por');
        $rows = $db->prepare("
            SELECT p.id, p.nome, p.descricao, p.modo, p.ativo, p.versao,
                   p.trigger_tipo, p.agendamento_cron, p.tags,
                   p.criado_por, p.data_criacao, p.data_atualizacao,
                   (SELECT COUNT(*) FROM tb_pipeline_execucoes WHERE id_pipeline = p.id) as total_execucoes,
                   (SELECT COUNT(*) FROM tb_pipeline_execucoes WHERE id_pipeline = p.id AND status = 'success') as execucoes_sucesso,
                   (SELECT COUNT(*) FROM tb_pipeline_execucoes WHERE id_pipeline = p.id AND status = 'error') as execucoes_falha,
                   (SELECT data_inicio FROM tb_pipeline_execucoes WHERE id_pipeline = p.id ORDER BY id DESC LIMIT 1) as ultima_execucao,
                   (SELECT COUNT(*) FROM jsonb_object_keys(
                       COALESCE(
                           p.dados_flow->'drawflow'->'Home'->'data',
                           '{}'::jsonb)
                   )) as total_nodes
            FROM tb_pipelines p
            WHERE ({$filtro['where']})
            ORDER BY p.data_atualizacao DESC
        ");
        $rows->execute($filtro['params']);
        $rows = $rows->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$r) {
            $r['tags'] = json_decode($r['tags'] ?? '[]', true);
        }

        return ['sucesso' => true, 'data' => $rows];
    }

    /**
     * Busca pipeline por ID
     */
    public function buscar(int $id): array
    {
        $db = Database::getConexao();
        $s = $db->prepare("SELECT * FROM tb_pipelines WHERE id = ?");
        $s->execute([$id]);
        $r = $s->fetch(PDO::FETCH_ASSOC);

        if (!$r) {
            return ['sucesso' => false, 'mensagem' => 'Pipeline não encontrado'];
        }

        $r['dados_flow'] = json_decode($r['dados_flow'] ?? '{}', true);
        $r['variaveis'] = json_decode($r['variaveis'] ?? '{}', true);
        $r['trigger_config'] = json_decode($r['trigger_config'] ?? '{}', true);
        $r['tags'] = json_decode($r['tags'] ?? '[]', true);

        return ['sucesso' => true, 'data' => $r];
    }

    /**
     * Salva um pipeline (INSERT ou UPDATE)
     */
    public function salvar(array $data): array
    {
        $db = Database::getConexao();
        $userId = AuthMiddleware::obterUsuarioId();

        $nome = trim($data['nome'] ?? '');
        if (empty($nome)) {
            return ['sucesso' => false, 'mensagem' => 'Nome é obrigatório'];
        }

        $descricao = $data['descricao'] ?? '';
        $modo = $data['modo'] ?? 'nocode';
        $ativo = isset($data['ativo']) ? (bool) $data['ativo'] : false;
        $dadosFlow = $data['dados_flow'] ?? '{"drawflow":{"Home":{"data":{}}}}';
        $dadosCode = $data['dados_code'] ?? '';
        $variaveis = $data['variaveis'] ?? '{}';
        $agendamentoCron = $data['agendamento_cron'] ?? null;
        $triggerTipo = $data['trigger_tipo'] ?? 'manual';
        $triggerConfig = $data['trigger_config'] ?? '{}';
        $tags = $data['tags'] ?? '[]';

        if (is_array($dadosFlow)) $dadosFlow = json_encode($dadosFlow);
        if (is_array($variaveis)) $variaveis = json_encode($variaveis);
        if (is_array($triggerConfig)) $triggerConfig = json_encode($triggerConfig);
        if (is_array($tags)) $tags = json_encode($tags);

        $id = $data['id'] ?? null;

        if ($id) {
            // Update
            $s = $db->prepare("
                UPDATE tb_pipelines 
                SET nome = ?, descricao = ?, modo = ?, ativo = ?,
                    dados_flow = ?::jsonb, dados_code = ?, variaveis = ?::jsonb,
                    agendamento_cron = ?, trigger_tipo = ?, trigger_config = ?::jsonb,
                    tags = ?::jsonb, versao = versao + 1, data_atualizacao = NOW()
                WHERE id = ?
            ");
            $s->execute([$nome, $descricao, $modo, $ativo ? 'true' : 'false',
                         $dadosFlow, $dadosCode, $variaveis,
                         $agendamentoCron, $triggerTipo, $triggerConfig,
                         $tags, $id]);

            // Associar empresas/projetos
            if (isset($data['empresas']) && is_array($data['empresas'])) {
                \App\Servicos\ServicoPermissao::associarRecursoEmpresas('pipeline', (int)$id, array_map('intval', $data['empresas']));
            }
            if (isset($data['projetos']) && is_array($data['projetos'])) {
                \App\Servicos\ServicoPermissao::associarRecursoProjetos('pipeline', (int)$id, array_map('intval', $data['projetos']));
            }

            return ['sucesso' => true, 'mensagem' => 'Pipeline atualizado', 'id' => (int)$id];
        }

        // Insert
        $s = $db->prepare("
            INSERT INTO tb_pipelines 
            (nome, descricao, modo, ativo, dados_flow, dados_code, variaveis,
             agendamento_cron, trigger_tipo, trigger_config, tags, criado_por)
            VALUES (?, ?, ?, ?, ?::jsonb, ?, ?::jsonb, ?, ?, ?::jsonb, ?::jsonb, ?)
            RETURNING id
        ");
        $s->execute([$nome, $descricao, $modo, $ativo ? 'true' : 'false',
                     $dadosFlow, $dadosCode, $variaveis,
                     $agendamentoCron, $triggerTipo, $triggerConfig,
                     $tags, $userId]);

        $newId = (int)$s->fetchColumn();

        // Associar empresas/projetos
        if (isset($data['empresas']) && is_array($data['empresas'])) {
            \App\Servicos\ServicoPermissao::associarRecursoEmpresas('pipeline', $newId, array_map('intval', $data['empresas']));
        }
        if (isset($data['projetos']) && is_array($data['projetos'])) {
            \App\Servicos\ServicoPermissao::associarRecursoProjetos('pipeline', $newId, array_map('intval', $data['projetos']));
        }

        return ['sucesso' => true, 'mensagem' => 'Pipeline criado', 'id' => $newId];
    }

    /**
     * Deleta um pipeline
     */
    public function deletar(int $id): array
    {
        $db = Database::getConexao();
        $s = $db->prepare("DELETE FROM tb_pipelines WHERE id = ?");
        $s->execute([$id]);
        return ['sucesso' => true, 'mensagem' => 'Pipeline removido'];
    }

    /**
     * Duplica um pipeline
     */
    public function duplicar(int $id): array
    {
        $db = Database::getConexao();
        $userId = AuthMiddleware::obterUsuarioId();

        $s = $db->prepare("SELECT * FROM tb_pipelines WHERE id = ?");
        $s->execute([$id]);
        $r = $s->fetch(PDO::FETCH_ASSOC);

        if (!$r) {
            return ['sucesso' => false, 'mensagem' => 'Pipeline não encontrado'];
        }

        $ins = $db->prepare("
            INSERT INTO tb_pipelines 
            (nome, descricao, modo, dados_flow, dados_code, variaveis,
             trigger_tipo, trigger_config, tags, criado_por)
            VALUES (?, ?, ?, ?::jsonb, ?, ?::jsonb, ?, ?::jsonb, ?::jsonb, ?)
            RETURNING id
        ");
        $ins->execute([
            $r['nome'] . ' (cópia)',
            $r['descricao'],
            $r['modo'],
            $r['dados_flow'],
            $r['dados_code'],
            $r['variaveis'],
            $r['trigger_tipo'],
            $r['trigger_config'],
            $r['tags'],
            $userId
        ]);

        $newId = $ins->fetchColumn();
        return ['sucesso' => true, 'mensagem' => 'Pipeline duplicado', 'id' => (int)$newId];
    }

    /**
     * Toggle ativo/inativo
     */
    public function toggleAtivo(int $id): array
    {
        $db = Database::getConexao();
        $s = $db->prepare("UPDATE tb_pipelines SET ativo = NOT ativo, data_atualizacao = NOW() WHERE id = ? RETURNING ativo");
        $s->execute([$id]);
        $ativo = $s->fetchColumn();
        return ['sucesso' => true, 'ativo' => (bool) $ativo];
    }

    /**
     * Exporta pipeline como JSON
     */
    public function exportar(int $id): array
    {
        $result = $this->buscar($id);
        if (!$result['sucesso']) return $result;

        $p = $result['data'];
        unset($p['criado_por']);
        return ['sucesso' => true, 'data' => $p, 'filename' => 'pipeline_' . $p['id'] . '_' . date('Ymd') . '.json'];
    }

    /**
     * Importa pipeline de JSON
     */
    public function importar(array $data): array
    {
        if (empty($data['pipeline'])) {
            return ['sucesso' => false, 'mensagem' => 'Dados do pipeline não fornecidos'];
        }

        $p = $data['pipeline'];
        if (is_string($p)) {
            $p = json_decode($p, true);
            if (!$p) return ['sucesso' => false, 'mensagem' => 'JSON inválido'];
        }

        $p['nome'] = ($p['nome'] ?? 'Pipeline Importado') . ' (importado)';
        unset($p['id']);

        return $this->salvar($p);
    }

    /**
     * Lista conexões disponíveis 
     */
    public function listarConexoes(): array
    {
        $db = Database::getConexao();
        $rows = $db->query("
            SELECT id, nome_conexao, tipo_banco, host, porta, nome_banco
            FROM tb_perfis_conexao 
            ORDER BY nome_conexao
        ")->fetchAll(PDO::FETCH_ASSOC);

        return ['sucesso' => true, 'data' => $rows];
    }

    /**
     * Histórico de execuções de um pipeline
     */
    public function historicoExecucoes(int $id): array
    {
        $db = Database::getConexao();
        $s = $db->prepare("
            SELECT id, status, data_inicio, data_fim, duracao_ms,
                   nodes_total, nodes_executados, nodes_sucesso, nodes_falha,
                   erro, executado_por
            FROM tb_pipeline_execucoes
            WHERE id_pipeline = ?
            ORDER BY id DESC
            LIMIT 50
        ");
        $s->execute([$id]);
        return ['sucesso' => true, 'data' => $s->fetchAll(PDO::FETCH_ASSOC)];
    }

    /**
     * Detalhes de uma execução
     */
    public function detalheExecucao(int $execId): array
    {
        $db = Database::getConexao();
        $s = $db->prepare("SELECT * FROM tb_pipeline_execucoes WHERE id = ?");
        $s->execute([$execId]);
        $r = $s->fetch(PDO::FETCH_ASSOC);

        if (!$r) return ['sucesso' => false, 'mensagem' => 'Execução não encontrada'];

        $r['resultado'] = json_decode($r['resultado'] ?? '{}', true);
        $r['log_execucao'] = json_decode($r['log_execucao'] ?? '[]', true);

        return ['sucesso' => true, 'data' => $r];
    }

    /**
     * Executa um pipeline
     */
    public function executar(int $id): array
    {
        $db = Database::getConexao();
        $userId = AuthMiddleware::obterUsuarioId();

        // Carregar pipeline
        $s = $db->prepare("SELECT * FROM tb_pipelines WHERE id = ?");
        $s->execute([$id]);
        $pipeline = $s->fetch(PDO::FETCH_ASSOC);

        if (!$pipeline) {
            return ['sucesso' => false, 'mensagem' => 'Pipeline não encontrado'];
        }

        $flowData = json_decode($pipeline['dados_flow'], true);
        $nodes = $flowData['drawflow']['Home']['data'] ?? [];

        if (empty($nodes)) {
            return ['sucesso' => false, 'mensagem' => 'Pipeline sem nós para executar'];
        }

        // Criar registro de execução
        $ins = $db->prepare("
            INSERT INTO tb_pipeline_execucoes 
            (id_pipeline, status, nodes_total, executado_por)
            VALUES (?, 'running', ?, ?)
            RETURNING id
        ");
        $ins->execute([$id, count($nodes), $userId]);
        $execId = $ins->fetchColumn();

        // Executar pipeline
        $logs = [];
        $context = [
            'variables' => json_decode($pipeline['variaveis'] ?? '{}', true),
            'results' => [],
            'errors' => []
        ];

        $nodesSuccess = 0;
        $nodesFail = 0;
        $nodesExecuted = 0;
        $startTime = microtime(true);
        $hasError = false;
        $errorMsg = '';

        try {
            // Ordenar nós por topologia (baseado em conexões)
            $sortedNodes = $this->topologicalSort($nodes);

            foreach ($sortedNodes as $nodeId) {
                $node = $nodes[$nodeId] ?? null;
                if (!$node) continue;

                $nodeType = $node['data']['type'] ?? $node['name'] ?? 'unknown';
                $nodeLabel = $node['data']['label'] ?? $nodeType;
                $nodeConfig = $node['data'] ?? [];
                $nodesExecuted++;

                // Verificar se deve pular (condição saiu por branch diferente)
                if (isset($context['skip_nodes'][$nodeId])) {
                    $logs[] = [
                        'node_id' => $nodeId,
                        'label' => $nodeLabel,
                        'type' => $nodeType,
                        'status' => 'skipped',
                        'message' => 'Pulado por condição',
                        'timestamp' => date('c')
                    ];
                    continue;
                }

                $nodeStart = microtime(true);
                try {
                    $result = $this->executarNode($nodeType, $nodeConfig, $context);
                    $nodeTime = round((microtime(true) - $nodeStart) * 1000);

                    $context['results'][$nodeId] = $result['data'] ?? null;
                    if (isset($result['variables'])) {
                        $context['variables'] = array_merge($context['variables'], $result['variables']);
                    }

                    // Tratar condições (qual branch seguir)
                    if ($nodeType === 'condition' && isset($result['branch'])) {
                        $this->handleConditionBranch($node, $result['branch'], $nodes, $context);
                    }

                    $nodesSuccess++;
                    $logs[] = [
                        'node_id' => $nodeId,
                        'label' => $nodeLabel,
                        'type' => $nodeType,
                        'status' => 'success',
                        'duration_ms' => $nodeTime,
                        'result_preview' => $this->previewResult($result['data'] ?? null),
                        'timestamp' => date('c')
                    ];
                } catch (\Throwable $e) {
                    $nodeTime = round((microtime(true) - $nodeStart) * 1000);
                    $nodesFail++;
                    $hasError = true;
                    $errorMsg = "Nó '{$nodeLabel}': " . $e->getMessage();

                    $logs[] = [
                        'node_id' => $nodeId,
                        'label' => $nodeLabel,
                        'type' => $nodeType,
                        'status' => 'error',
                        'duration_ms' => $nodeTime,
                        'error' => $e->getMessage(),
                        'timestamp' => date('c')
                    ];

                    // Verificar se deve parar na falha
                    $stopOnError = $nodeConfig['stop_on_error'] ?? true;
                    if ($stopOnError) break;
                }
            }
        } catch (\Throwable $e) {
            $hasError = true;
            $errorMsg = $e->getMessage();
        }

        $totalMs = round((microtime(true) - $startTime) * 1000);

        // Atualizar execução
        $upd = $db->prepare("
            UPDATE tb_pipeline_execucoes 
            SET status = ?, data_fim = NOW(), duracao_ms = ?,
                nodes_executados = ?, nodes_sucesso = ?, nodes_falha = ?,
                resultado = ?::jsonb, log_execucao = ?::jsonb, erro = ?
            WHERE id = ?
        ");
        $upd->execute([
            $hasError ? 'error' : 'success',
            $totalMs,
            $nodesExecuted, $nodesSuccess, $nodesFail,
            json_encode($context['results']),
            json_encode($logs),
            $hasError ? $errorMsg : null,
            $execId
        ]);

        // Notificar falha
        if ($hasError) {
            \App\Servicos\ServicoNotificacao::notificarFalhaPipeline(
                $id,
                $pipeline['nome'] ?? "Pipeline #{$id}",
                $errorMsg,
                $execId
            );
        }

        return [
            'sucesso' => !$hasError,
            'mensagem' => $hasError ? $errorMsg : 'Pipeline executado com sucesso',
            'execucao_id' => $execId,
            'duracao_ms' => $totalMs,
            'nodes_executados' => $nodesExecuted,
            'nodes_sucesso' => $nodesSuccess,
            'nodes_falha' => $nodesFail,
            'logs' => $logs
        ];
    }

    /**
     * Ordenação topológica dos nós
     */
    private function topologicalSort(array $nodes): array
    {
        $graph = [];
        $inDegree = [];

        foreach ($nodes as $id => $node) {
            $graph[$id] = [];
            if (!isset($inDegree[$id])) $inDegree[$id] = 0;
        }

        // Construir grafo de adjacência
        foreach ($nodes as $id => $node) {
            foreach ($node['outputs'] ?? [] as $output) {
                foreach ($output['connections'] ?? [] as $conn) {
                    $targetId = (string) $conn['node'];
                    $graph[$id][] = $targetId;
                    $inDegree[$targetId] = ($inDegree[$targetId] ?? 0) + 1;
                }
            }
        }

        // BFS (Kahn's algorithm)
        $queue = [];
        foreach ($inDegree as $id => $deg) {
            if ($deg === 0) $queue[] = $id;
        }

        $sorted = [];
        while (!empty($queue)) {
            $current = array_shift($queue);
            $sorted[] = $current;

            foreach ($graph[$current] ?? [] as $neighbor) {
                $inDegree[$neighbor]--;
                if ($inDegree[$neighbor] === 0) {
                    $queue[] = $neighbor;
                }
            }
        }

        return $sorted;
    }

    /**
     * Executa um nó individual
     */
    private function executarNode(string $type, array $config, array &$context): array
    {
        switch ($type) {
            case 'trigger':
                return ['data' => ['started' => true, 'timestamp' => date('c')]];

            case 'sql_query':
                return $this->execSqlQuery($config, $context);

            case 'http_request':
                return $this->execHttpRequest($config, $context);

            case 'transform':
                return $this->execTransform($config, $context);

            case 'condition':
                return $this->execCondition($config, $context);

            case 'loop':
                return $this->execLoop($config, $context);

            case 'set_variable':
                return $this->execSetVariable($config, $context);

            case 'log_node':
                return $this->execLog($config, $context);

            case 'script':
                return $this->execScript($config, $context);

            case 'delay':
                $seconds = min((int)($config['delay_seconds'] ?? 1), 30);
                sleep($seconds);
                return ['data' => ['delayed' => $seconds]];

            case 'email':
                return $this->execEmail($config, $context);

            case 'api_call':
                return $this->execApiCall($config, $context);

            case 'end':
                return ['data' => ['finished' => true]];

            default:
                return ['data' => null];
        }
    }

    /**
     * Executa nó SQL Query
     */
    private function execSqlQuery(array $config, array &$context): array
    {
        $connId = (int)($config['connection_id'] ?? 0);
        $sql = $config['sql_query'] ?? '';

        if (!$connId || !$sql) {
            throw new \RuntimeException('Conexão e SQL são obrigatórios');
        }

        // Substituir variáveis no SQL
        $sql = $this->replaceVariables($sql, $context['variables']);

        // Buscar dados da conexão
        $db = Database::getConexao();
        $s = $db->prepare("SELECT tipo_banco, host, porta, nome_banco, usuario, senha_encriptada, parametros_extras FROM tb_perfis_conexao WHERE id = ?");
        $s->execute([$connId]);
        $conn = $s->fetch(PDO::FETCH_ASSOC);

        if (!$conn) throw new \RuntimeException("Conexão ID {$connId} não encontrada");

        // Descriptografar senha
        $key = $_ENV['ENCRYPTION_KEY'] ?? $_SERVER['ENCRYPTION_KEY'] ?? getenv('ENCRYPTION_KEY') ?: '';
        $senha = '';
        if (!empty($conn['senha_encriptada']) && $key) {
            $senha = Crypto::decrypt($conn['senha_encriptada'], $key);
        }

        // Conectar
        $pdo = $this->createConnection($conn['tipo_banco'], $conn['host'], $conn['porta'], $conn['nome_banco'], $conn['usuario'], $senha, $conn['parametros_extras']);

        // Executar SQL
        $maxRows = min((int)($config['max_rows'] ?? 1000), 10000);
        $timeout = min((int)($config['timeout'] ?? 30), 120);

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $sqlType = strtoupper(strtok(trim($sql), " \t\n\r"));
        if (in_array($sqlType, ['SELECT', 'WITH', 'SHOW', 'DESCRIBE', 'EXPLAIN'])) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $total = count($rows);
            if ($total > $maxRows) $rows = array_slice($rows, 0, $maxRows);

            // Salvar resultado como variável
            if (!empty($config['output_variable'])) {
                $context['variables'][$config['output_variable']] = $rows;
            }

            return ['data' => ['rows' => $rows, 'total' => $total, 'truncated' => $total > $maxRows]];
        }

        $affected = $stmt->rowCount();
        return ['data' => ['affected_rows' => $affected, 'type' => $sqlType]];
    }

    /**
     * Cria conexão PDO com banco externo
     */
    private function createConnection(string $tipo, string $host, ?int $porta, ?string $dbName, ?string $user, string $pass, ?string $extrasJson): PDO
    {
        $extras = json_decode($extrasJson ?? '{}', true) ?: [];

        switch ($tipo) {
            case 'postgres':
                $port = $porta ?: 5432;
                $dsn = "pgsql:host={$host};port={$port};dbname={$dbName}";
                break;
            case 'mysql':
            case 'mariadb':
                $port = $porta ?: 3306;
                $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
                break;
            case 'sqlserver':
                $port = $porta ?: 1433;
                $instance = $extras['instance_name'] ?? '';
                $dsn = !empty($instance) 
                    ? "sqlsrv:Server={$host}\\{$instance},{$port};Database={$dbName}"
                    : "sqlsrv:Server={$host},{$port};Database={$dbName}";
                break;
            case 'oracle':
                $port = $porta ?: 1521;
                $sid = $extras['sid'] ?? $dbName;
                $dsn = "oci:dbname=//{$host}:{$port}/{$sid}";
                break;
            default:
                throw new \RuntimeException("Tipo de banco '{$tipo}' não suportado");
        }

        return new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    /**
     * Executa nó HTTP Request
     */
    private function execHttpRequest(array $config, array &$context): array
    {
        $url = $config['url'] ?? '';
        if (!$url) throw new \RuntimeException('URL é obrigatória');

        $url = $this->replaceVariables($url, $context['variables']);

        // Validar URL (não permitir file://, gopher://, etc.)
        $parsed = parse_url($url);
        $scheme = strtolower($parsed['scheme'] ?? '');
        if (!in_array($scheme, ['http', 'https'])) {
            throw new \RuntimeException('Apenas HTTP/HTTPS são permitidos');
        }

        // Bloquear IPs internos (SSRF protection)
        $host = $parsed['host'] ?? '';
        $ip = gethostbyname($host);
        if ($this->isPrivateIP($ip)) {
            throw new \RuntimeException('Acesso a IPs internos não é permitido');
        }

        $method = strtoupper($config['method'] ?? 'GET');
        $headers = $config['headers'] ?? [];
        $body = $config['body'] ?? '';
        $timeout = min((int)($config['timeout'] ?? 30), 60);

        if (is_string($headers)) $headers = json_decode($headers, true) ?: [];

        $body = $this->replaceVariables($body, $context['variables']);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3,
            CURLOPT_CUSTOMREQUEST => $method,
        ]);

        // Auth
        $authType = $config['auth_type'] ?? 'none';
        if ($authType === 'bearer' && !empty($config['auth_token'])) {
            $headers['Authorization'] = 'Bearer ' . $config['auth_token'];
        } elseif ($authType === 'basic' && !empty($config['auth_user'])) {
            curl_setopt($ch, CURLOPT_USERPWD, $config['auth_user'] . ':' . ($config['auth_pass'] ?? ''));
        }

        if (!empty($headers)) {
            $curlHeaders = [];
            foreach ($headers as $k => $v) {
                $curlHeaders[] = "{$k}: {$v}";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
        }

        if (in_array($method, ['POST', 'PUT', 'PATCH']) && $body) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) throw new \RuntimeException("cURL: {$error}");

        $decoded = json_decode($response, true);

        if (!empty($config['output_variable'])) {
            $context['variables'][$config['output_variable']] = $decoded ?? $response;
        }

        return ['data' => [
            'status_code' => $httpCode,
            'body' => $decoded ?? $response,
            'is_json' => $decoded !== null
        ]];
    }

    /**
     * Verifica se IP é privado (SSRF protection)
     */
    private function isPrivateIP(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, 
            FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }

    /**
     * Executa nó API Call - chama API externa cadastrada e avalia condição
     */
    private function execApiCall(array $config, array &$context): array
    {
        $apiId = (int)($config['api_id'] ?? 0);
        if (!$apiId) throw new \RuntimeException('API não selecionada');

        $apiCtrl = new ApiExternaController();
        $apiRes = $apiCtrl->buscarApi($apiId);
        if (!$apiRes['sucesso'] || empty($apiRes['dados'])) {
            throw new \RuntimeException('API não encontrada (ID: ' . $apiId . ')');
        }

        $api = $apiRes['dados'];

        // Testar a API (faz a chamada real)
        $testData = [
            'url' => $api['url'],
            'metodo' => $api['metodo'] ?? 'GET',
            'auth_tipo' => $api['auth_tipo'] ?? 'none',
            'tipo_resposta' => $api['tipo_resposta'] ?? 'json',
            'timeout' => $api['timeout'] ?? 30,
        ];

        // Extrair headers e credenciais
        $headers = $api['headers'];
        if (is_string($headers)) $headers = json_decode($headers, true) ?: [];
        if (!empty($headers)) {
            $testData['header_keys'] = array_keys($headers);
            $testData['header_values'] = array_values($headers);
        }

        $creds = $api['credenciais'];
        if (is_string($creds)) $creds = json_decode($creds, true) ?: [];
        if (!empty($creds)) {
            foreach ($creds as $k => $v) {
                $testData[$k] = $v;
            }
        }

        $testResult = $apiCtrl->testarApi($testData);
        if (!$testResult['sucesso']) {
            throw new \RuntimeException('Chamada API falhou: ' . ($testResult['erro'] ?? 'Erro desconhecido'));
        }

        $response = $testResult['response'] ?? null;
        $jsonpath = $config['jsonpath'] ?? '';
        $conditionOp = $config['condition_op'] ?? 'is_not_null';
        $conditionValue = $config['condition_value'] ?? '';
        $outputVar = $config['output_variable'] ?? 'api_result';

        // Extrair valor via JSONPath se configurado
        $extractedValue = $response;
        $match = true;

        if ($jsonpath && is_array($response)) {
            $extractedValue = $apiCtrl->extrairValorJsonPath($response, $jsonpath);
            $match = $apiCtrl->avaliarCondicao($extractedValue, $conditionOp, $conditionValue);
        }

        // Salvar resultado nas variáveis do contexto
        $context['variables'][$outputVar] = $extractedValue;
        $context['variables'][$outputVar . '_match'] = $match;
        $context['variables'][$outputVar . '_raw'] = $response;

        return [
            'data' => [
                'api_nome' => $api['nome'],
                'status_code' => $testResult['status_code'] ?? null,
                'extracted_value' => $extractedValue,
                'match' => $match,
                'jsonpath' => $jsonpath,
            ],
            'output' => $match ? 0 : 1 // output_1 = match, output_2 = no match
        ];
    }

    /**
     * Executa nó Transform
     */
    private function execTransform(array $config, array &$context): array
    {
        $inputVar = $config['input_variable'] ?? '';
        $data = $context['variables'][$inputVar] ?? $context['results'] ?? [];
        $transformType = $config['transform_type'] ?? 'map';

        switch ($transformType) {
            case 'map':
                // Mapear campos
                $mapping = $config['field_mapping'] ?? [];
                if (is_string($mapping)) $mapping = json_decode($mapping, true) ?: [];
                if (!empty($mapping) && is_array($data)) {
                    $result = array_map(function($row) use ($mapping) {
                        $newRow = [];
                        foreach ($mapping as $from => $to) {
                            $newRow[$to] = $row[$from] ?? null;
                        }
                        return $newRow;
                    }, $data);
                } else {
                    $result = $data;
                }
                break;

            case 'filter':
                $field = $config['filter_field'] ?? '';
                $operator = $config['filter_operator'] ?? 'equals';
                $value = $config['filter_value'] ?? '';
                $result = is_array($data) ? array_values(array_filter($data, function($row) use ($field, $operator, $value) {
                    $fieldVal = $row[$field] ?? null;
                    switch ($operator) {
                        case 'equals': return $fieldVal == $value;
                        case 'not_equals': return $fieldVal != $value;
                        case 'contains': return stripos((string)$fieldVal, $value) !== false;
                        case 'greater': return $fieldVal > $value;
                        case 'less': return $fieldVal < $value;
                        case 'is_null': return $fieldVal === null;
                        case 'not_null': return $fieldVal !== null;
                        default: return true;
                    }
                })) : $data;
                break;

            case 'sort':
                $field = $config['sort_field'] ?? '';
                $dir = $config['sort_direction'] ?? 'asc';
                $result = $data;
                if (is_array($result) && $field) {
                    usort($result, function($a, $b) use ($field, $dir) {
                        $va = $a[$field] ?? '';
                        $vb = $b[$field] ?? '';
                        $cmp = is_numeric($va) && is_numeric($vb) ? $va - $vb : strcmp((string)$va, (string)$vb);
                        return $dir === 'desc' ? -$cmp : $cmp;
                    });
                }
                break;

            case 'limit':
                $limit = max(1, (int)($config['limit_count'] ?? 10));
                $offset = max(0, (int)($config['limit_offset'] ?? 0));
                $result = is_array($data) ? array_slice($data, $offset, $limit) : $data;
                break;

            case 'aggregate':
                $field = $config['agg_field'] ?? '';
                $op = $config['agg_operation'] ?? 'count';
                if (!is_array($data)) {
                    $result = ['value' => 0];
                } else {
                    $values = array_column($data, $field);
                    switch ($op) {
                        case 'count': $result = ['value' => count($data)]; break;
                        case 'sum': $result = ['value' => array_sum($values)]; break;
                        case 'avg': $result = ['value' => count($values) ? array_sum($values) / count($values) : 0]; break;
                        case 'min': $result = ['value' => !empty($values) ? min($values) : null]; break;
                        case 'max': $result = ['value' => !empty($values) ? max($values) : null]; break;
                        default: $result = ['value' => count($data)];
                    }
                }
                break;

            default:
                $result = $data;
        }

        if (!empty($config['output_variable'])) {
            $context['variables'][$config['output_variable']] = $result;
        }

        return ['data' => $result];
    }

    /**
     * Executa nó Condition
     */
    private function execCondition(array $config, array &$context): array
    {
        $leftRaw = $config['left_operand'] ?? '';
        $operator = $config['operator'] ?? '==';
        $rightRaw = $config['right_operand'] ?? '';

        $left = $this->resolveValue($leftRaw, $context['variables']);
        $right = $this->resolveValue($rightRaw, $context['variables']);

        $result = false;
        switch ($operator) {
            case '==': $result = $left == $right; break;
            case '!=': $result = $left != $right; break;
            case '>': $result = $left > $right; break;
            case '<': $result = $left < $right; break;
            case '>=': $result = $left >= $right; break;
            case '<=': $result = $left <= $right; break;
            case 'contains': $result = stripos((string)$left, (string)$right) !== false; break;
            case 'starts_with': $result = str_starts_with((string)$left, (string)$right); break;
            case 'ends_with': $result = str_ends_with((string)$left, (string)$right); break;
            case 'is_empty': $result = empty($left); break;
            case 'not_empty': $result = !empty($left); break;
            case 'matches': $result = @preg_match('/' . $right . '/', (string)$left) === 1; break;
        }

        return [
            'data' => ['condition' => $result, 'left' => $left, 'right' => $right],
            'branch' => $result ? 'true' : 'false'
        ];
    }

    /**
     * Trata branching condicional
     */
    private function handleConditionBranch(array $node, string $branch, array $allNodes, array &$context): void
    {
        // output_1 = true, output_2 = false
        $skipOutput = $branch === 'true' ? 'output_2' : 'output_1';

        if (isset($node['outputs'][$skipOutput])) {
            foreach ($node['outputs'][$skipOutput]['connections'] ?? [] as $conn) {
                $context['skip_nodes'][(string)$conn['node']] = true;
                // Propagar skip para nós filhos
                $this->propagateSkip((string)$conn['node'], $allNodes, $context);
            }
        }
    }

    private function propagateSkip(string $nodeId, array $allNodes, array &$context): void
    {
        $node = $allNodes[$nodeId] ?? null;
        if (!$node) return;

        foreach ($node['outputs'] ?? [] as $output) {
            foreach ($output['connections'] ?? [] as $conn) {
                $targetId = (string) $conn['node'];
                // Só propagar se TODOS os inputs estão skipados
                $target = $allNodes[$targetId] ?? null;
                if (!$target) continue;

                $allInputsSkipped = true;
                foreach ($target['inputs'] ?? [] as $input) {
                    foreach ($input['connections'] ?? [] as $inConn) {
                        if (!isset($context['skip_nodes'][(string)$inConn['node']])) {
                            $allInputsSkipped = false;
                            break 2;
                        }
                    }
                }

                if ($allInputsSkipped) {
                    $context['skip_nodes'][$targetId] = true;
                    $this->propagateSkip($targetId, $allNodes, $context);
                }
            }
        }
    }

    /**
     * Executa nó Loop
     */
    private function execLoop(array $config, array &$context): array
    {
        $inputVar = $config['input_variable'] ?? '';
        $data = $context['variables'][$inputVar] ?? [];
        $maxIter = min((int)($config['max_iterations'] ?? 100), 1000);
        $iterVar = $config['iterator_variable'] ?? 'item';

        if (!is_array($data)) {
            return ['data' => ['iterations' => 0]];
        }

        $results = [];
        $i = 0;
        foreach ($data as $item) {
            if ($i >= $maxIter) break;
            $context['variables'][$iterVar] = $item;
            $context['variables']['_index'] = $i;
            $results[] = $item;
            $i++;
        }

        if (!empty($config['output_variable'])) {
            $context['variables'][$config['output_variable']] = $results;
        }

        return ['data' => ['iterations' => $i, 'items' => $results]];
    }

    /**
     * Executa nó Set Variable
     */
    private function execSetVariable(array $config, array &$context): array
    {
        $name = $config['variable_name'] ?? '';
        $value = $config['variable_value'] ?? '';

        if (empty($name)) throw new \RuntimeException('Nome da variável é obrigatório');

        $value = $this->resolveValue($value, $context['variables']);
        $context['variables'][$name] = $value;

        return ['data' => [$name => $value], 'variables' => [$name => $value]];
    }

    /**
     * Executa nó Log
     */
    private function execLog(array $config, array &$context): array
    {
        $message = $config['message'] ?? '';
        $message = $this->replaceVariables($message, $context['variables']);
        $level = $config['level'] ?? 'info';

        return ['data' => ['message' => $message, 'level' => $level]];
    }

    /**
     * Executa nó Script (expressões seguras)
     */
    private function execScript(array $config, array &$context): array
    {
        $code = $config['script_code'] ?? '';
        $language = $config['script_language'] ?? 'expression';

        if ($language === 'sql') {
            // Redirecionar para execução SQL
            return $this->execSqlQuery($config, $context);
        }

        // Para expressões simples — substituir variáveis e avaliar
        $code = $this->replaceVariables($code, $context['variables']);

        // Verificar segurança - bloquear funções perigosas
        $blocked = ['exec', 'system', 'passthru', 'shell_exec', 'popen', 'proc_open',
                     'eval', 'assert', 'file_get_contents', 'file_put_contents', 'fopen',
                     'fwrite', 'unlink', 'rmdir', 'mkdir', 'rename', 'copy', 'move_uploaded_file',
                     'curl_init', 'fsockopen', 'stream_socket_client', 'mail', 'header',
                     'setcookie', 'session_start', 'phpinfo', 'getenv', 'putenv',
                     'include', 'require', 'include_once', 'require_once'];
        
        $codeLower = strtolower($code);
        foreach ($blocked as $fn) {
            if (strpos($codeLower, $fn) !== false) {
                throw new \RuntimeException("Função '{$fn}' não é permitida por segurança");
            }
        }

        // Executar como expressão PHP simples
        $vars = $context['variables'];
        $result = null;
        try {
            // Criar escopo isolado com variáveis 
            $fn = function() use ($code, $vars) {
                extract($vars);
                return eval('return ' . $code . ';');
            };
            $result = $fn();
        } catch (\Throwable $e) {
            throw new \RuntimeException('Erro no script: ' . $e->getMessage());
        }

        if (!empty($config['output_variable'])) {
            $context['variables'][$config['output_variable']] = $result;
        }

        return ['data' => $result];
    }

    /**
     * Executa nó Email
     */
    private function execEmail(array $config, array &$context): array
    {
        $to = $config['email_to'] ?? '';
        $subject = $config['email_subject'] ?? '';
        $body = $config['email_body'] ?? '';

        if (!$to || !$subject) {
            throw new \RuntimeException('Destinatário e assunto são obrigatórios');
        }

        $to = $this->replaceVariables($to, $context['variables']);
        $subject = $this->replaceVariables($subject, $context['variables']);
        $body = $this->replaceVariables($body, $context['variables']);

        $headers = "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";

        $sent = @mail($to, $subject, $body, $headers);

        return ['data' => ['sent' => $sent, 'to' => $to]];
    }

    /**
     * Substitui {{variavel}} por valores
     */
    private function replaceVariables(string $text, array $vars): string
    {
        return preg_replace_callback('/\{\{(\w+)\}\}/', function($m) use ($vars) {
            $val = $vars[$m[1]] ?? $m[0];
            return is_array($val) ? json_encode($val) : (string) $val;
        }, $text);
    }

    /**
     * Resolve um valor (pode ser referência a variável)
     */
    private function resolveValue($value, array $vars)
    {
        if (is_string($value) && preg_match('/^\{\{(\w+)\}\}$/', $value, $m)) {
            return $vars[$m[1]] ?? $value;
        }
        if (is_string($value) && strpos($value, '{{') !== false) {
            return $this->replaceVariables($value, $vars);
        }
        if (is_numeric($value)) return $value + 0;
        if ($value === 'true') return true;
        if ($value === 'false') return false;
        if ($value === 'null') return null;
        return $value;
    }

    /**
     * Preview de resultado para logs
     */
    private function previewResult($data): string
    {
        if ($data === null) return 'null';
        if (is_bool($data)) return $data ? 'true' : 'false';
        if (is_string($data)) return mb_substr($data, 0, 100);
        if (is_numeric($data)) return (string) $data;
        if (is_array($data)) {
            if (isset($data['rows'])) return count($data['rows']) . ' registros';
            if (isset($data['affected_rows'])) return $data['affected_rows'] . ' linhas afetadas';
            return count($data) . ' itens';
        }
        return '(objeto)';
    }

    /**
     * Estatísticas gerais dos pipelines
     */
    public function estatisticas(): array
    {
        $db = Database::getConexao();

        $total = $db->query("SELECT COUNT(*) FROM tb_pipelines")->fetchColumn();
        $ativos = $db->query("SELECT COUNT(*) FROM tb_pipelines WHERE ativo = true")->fetchColumn();
        $execHoje = $db->query("SELECT COUNT(*) FROM tb_pipeline_execucoes WHERE data_inicio >= CURRENT_DATE")->fetchColumn();
        $successHoje = $db->query("SELECT COUNT(*) FROM tb_pipeline_execucoes WHERE data_inicio >= CURRENT_DATE AND status = 'success'")->fetchColumn();
        $errorHoje = $db->query("SELECT COUNT(*) FROM tb_pipeline_execucoes WHERE data_inicio >= CURRENT_DATE AND status = 'error'")->fetchColumn();

        return [
            'sucesso' => true,
            'total' => (int)$total,
            'ativos' => (int)$ativos,
            'execucoes_hoje' => (int)$execHoje,
            'sucesso_hoje' => (int)$successHoje,
            'erro_hoje' => (int)$errorHoje
        ];
    }

    /**
     * Lista APIs externas disponíveis para uso em nodes HTTP
     */
    public function listarApisExternas(): array
    {
        $db = Database::getConexao();
        $rows = $db->query("
            SELECT id, nome, descricao, url, metodo, headers, auth_tipo,
                   body_template, tipo_resposta, timeout, ativo,
                   ultimo_status, ultima_verificacao
            FROM tb_api_externas
            ORDER BY nome
        ")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$r) {
            $r['headers'] = json_decode($r['headers'] ?? '{}', true);
        }

        return ['sucesso' => true, 'data' => $rows];
    }

    /**
     * Lista eventos de API disponíveis para triggers
     */
    public function listarEventosApi(): array
    {
        $db = Database::getConexao();
        $rows = $db->query("
            SELECT e.id, e.nome, e.descricao, e.jsonpath, e.operador, 
                   e.valor_esperado, e.acao, e.ativo, e.total_matches,
                   a.nome as api_nome, a.url as api_url
            FROM tb_eventos_api e
            JOIN tb_api_externas a ON a.id = e.id_api
            WHERE e.ativo = true
            ORDER BY e.nome
        ")->fetchAll(PDO::FETCH_ASSOC);

        return ['sucesso' => true, 'data' => $rows];
    }

    /**
     * Lista rotinas disponíveis para uso em pipelines
     */
    public function listarRotinas(): array
    {
        $db = Database::getConexao();
        $rows = $db->query("
            SELECT r.id, r.nome, r.descricao, r.ativa, r.agendamento_cron,
                   c.nome_conexao, c.tipo_banco
            FROM tb_rotinas r
            LEFT JOIN tb_perfis_conexao c ON c.id = r.id_conexao
            ORDER BY r.nome
        ")->fetchAll(PDO::FETCH_ASSOC);

        return ['sucesso' => true, 'data' => $rows];
    }

    /**
     * Lista tabelas/schemas de uma conexão
     */
    public function listarTabelas(int $connId): array
    {
        $db = Database::getConexao();
        $s = $db->prepare("SELECT tipo_banco, host, porta, nome_banco, usuario, senha_encriptada, parametros_extras FROM tb_perfis_conexao WHERE id = ?");
        $s->execute([$connId]);
        $conn = $s->fetch(PDO::FETCH_ASSOC);

        if (!$conn) {
            return ['sucesso' => false, 'mensagem' => 'Conexão não encontrada'];
        }

        $key = $_ENV['ENCRYPTION_KEY'] ?? $_SERVER['ENCRYPTION_KEY'] ?? getenv('ENCRYPTION_KEY') ?: '';
        $senha = '';
        if (!empty($conn['senha_encriptada']) && $key) {
            $senha = Crypto::decrypt($conn['senha_encriptada'], $key);
        }

        try {
            $pdo = $this->createConnection($conn['tipo_banco'], $conn['host'], $conn['porta'], $conn['nome_banco'], $conn['usuario'], $senha, $conn['parametros_extras']);

            $tables = [];
            switch ($conn['tipo_banco']) {
                case 'postgres':
                    $stmt = $pdo->query("
                        SELECT table_schema, table_name, table_type 
                        FROM information_schema.tables 
                        WHERE table_schema NOT IN ('pg_catalog','information_schema')
                        ORDER BY table_schema, table_name
                    ");
                    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case 'mysql':
                case 'mariadb':
                    $stmt = $pdo->query("
                        SELECT table_schema, table_name, table_type
                        FROM information_schema.tables
                        WHERE table_schema = DATABASE()
                        ORDER BY table_name
                    ");
                    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case 'sqlserver':
                    $stmt = $pdo->query("
                        SELECT TABLE_SCHEMA as table_schema, TABLE_NAME as table_name, TABLE_TYPE as table_type
                        FROM INFORMATION_SCHEMA.TABLES
                        ORDER BY TABLE_SCHEMA, TABLE_NAME
                    ");
                    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case 'oracle':
                    $stmt = $pdo->query("SELECT owner as table_schema, table_name, 'TABLE' as table_type FROM all_tables WHERE owner = USER ORDER BY table_name");
                    $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
            }

            return ['sucesso' => true, 'data' => $tables, 'tipo_banco' => $conn['tipo_banco']];
        } catch (\Throwable $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao listar tabelas: ' . $e->getMessage()];
        }
    }

    /**
     * Lista colunas de uma tabela em uma conexão
     */
    public function listarColunas(int $connId, string $tabela, string $schema = ''): array
    {
        $db = Database::getConexao();
        $s = $db->prepare("SELECT tipo_banco, host, porta, nome_banco, usuario, senha_encriptada, parametros_extras FROM tb_perfis_conexao WHERE id = ?");
        $s->execute([$connId]);
        $conn = $s->fetch(PDO::FETCH_ASSOC);

        if (!$conn) {
            return ['sucesso' => false, 'mensagem' => 'Conexão não encontrada'];
        }

        $key = $_ENV['ENCRYPTION_KEY'] ?? $_SERVER['ENCRYPTION_KEY'] ?? getenv('ENCRYPTION_KEY') ?: '';
        $senha = '';
        if (!empty($conn['senha_encriptada']) && $key) {
            $senha = Crypto::decrypt($conn['senha_encriptada'], $key);
        }

        try {
            $pdo = $this->createConnection($conn['tipo_banco'], $conn['host'], $conn['porta'], $conn['nome_banco'], $conn['usuario'], $senha, $conn['parametros_extras']);

            $columns = [];
            switch ($conn['tipo_banco']) {
                case 'postgres':
                    $schemaFilter = $schema ?: 'public';
                    $stmt = $pdo->prepare("
                        SELECT column_name, data_type, is_nullable, column_default
                        FROM information_schema.columns
                        WHERE table_schema = ? AND table_name = ?
                        ORDER BY ordinal_position
                    ");
                    $stmt->execute([$schemaFilter, $tabela]);
                    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case 'mysql':
                case 'mariadb':
                    $stmt = $pdo->prepare("
                        SELECT column_name, data_type, is_nullable, column_default
                        FROM information_schema.columns
                        WHERE table_schema = DATABASE() AND table_name = ?
                        ORDER BY ordinal_position
                    ");
                    $stmt->execute([$tabela]);
                    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case 'sqlserver':
                    $schemaFilter = $schema ?: 'dbo';
                    $stmt = $pdo->prepare("
                        SELECT COLUMN_NAME as column_name, DATA_TYPE as data_type, IS_NULLABLE as is_nullable, COLUMN_DEFAULT as column_default
                        FROM INFORMATION_SCHEMA.COLUMNS
                        WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
                        ORDER BY ORDINAL_POSITION
                    ");
                    $stmt->execute([$schemaFilter, $tabela]);
                    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case 'oracle':
                    $stmt = $pdo->prepare("
                        SELECT column_name, data_type, nullable as is_nullable, data_default as column_default
                        FROM all_tab_columns
                        WHERE owner = USER AND table_name = UPPER(?)
                        ORDER BY column_id
                    ");
                    $stmt->execute([$tabela]);
                    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;
            }

            return ['sucesso' => true, 'data' => $columns];
        } catch (\Throwable $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao listar colunas: ' . $e->getMessage()];
        }
    }
}
