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
            if (!empty($data['_rbac_presente'])) {
                $idsEmpresas = isset($data['empresas']) && is_array($data['empresas']) ? array_map('intval', $data['empresas']) : [];
                $idsProjetos = isset($data['projetos']) && is_array($data['projetos']) ? array_map('intval', $data['projetos']) : [];
                \App\Servicos\ServicoPermissao::associarRecursoEmpresas('pipeline', (int)$id, $idsEmpresas);
                \App\Servicos\ServicoPermissao::associarRecursoProjetos('pipeline', (int)$id, $idsProjetos);
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
        if (!empty($data['_rbac_presente'])) {
            $idsEmpresas = isset($data['empresas']) && is_array($data['empresas']) ? array_map('intval', $data['empresas']) : [];
            $idsProjetos = isset($data['projetos']) && is_array($data['projetos']) ? array_map('intval', $data['projetos']) : [];
            \App\Servicos\ServicoPermissao::associarRecursoEmpresas('pipeline', $newId, $idsEmpresas);
            \App\Servicos\ServicoPermissao::associarRecursoProjetos('pipeline', $newId, $idsProjetos);
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
     * Lista conexões disponíveis (com RBAC)
     */
    public function listarConexoes(): array
    {
        $db = Database::getConexao();
        $filtro = \App\Servicos\ServicoPermissao::filtroVisibilidade('conexao', 'c', 'criado_por');
        $sql = "
            SELECT c.id, c.nome_conexao, c.tipo_banco, c.host, c.porta, c.nome_banco
            FROM tb_perfis_conexao c
            WHERE ({$filtro['where']})
            ORDER BY c.nome_conexao
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute($filtro['params']);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                    if (in_array($nodeType, ['condition', 'try_catch', 'api_call', 'switch_case']) && isset($result['branch'])) {
                        $this->handleConditionBranch($node, $result['branch'], $nodes, $context);
                    }

                    $nodesSuccess++;
                    $logEntry = [
                        'node_id' => $nodeId,
                        'label' => $nodeLabel,
                        'type' => $nodeType,
                        'status' => 'success',
                        'duration_ms' => $nodeTime,
                        'result_preview' => $this->previewResult($result['data'] ?? null),
                        'timestamp' => date('c')
                    ];
                    // Incluir file_path quando o nó gera arquivo
                    $rd = $result['data'] ?? null;
                    if (is_array($rd) && isset($rd['file_path'])) {
                        $logEntry['file_path'] = $rd['file_path'];
                        if (isset($rd['records'])) $logEntry['records'] = $rd['records'];
                        if (isset($rd['filename'])) $logEntry['filename'] = $rd['filename'];
                    }
                    $logs[] = $logEntry;
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
                    $stopRaw = $nodeConfig['stop_on_error'] ?? true;
                    $stopOnError = filter_var($stopRaw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;
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

            case 'try_catch':
                return $this->execTryCatch($config, $context);

            case 'data_merge':
                return $this->execDataMerge($config, $context);

            case 'sql_upsert':
                return $this->execSqlUpsert($config, $context);

            case 'switch_case':
                return $this->execSwitchCase($config, $context);

            case 'format_template':
                return $this->execFormatTemplate($config, $context);

            case 'regex':
                return $this->execRegex($config, $context);

            case 'csv_parse':
                return $this->execCsvParse($config, $context);

            case 'counter':
                return $this->execCounter($config, $context);

            case 'file_export':
                return $this->execFileExport($config, $context);

            case 'data_format':
                return $this->execDataFormat($config, $context);

            case 'rotina':
                return $this->execRotina($config, $context);

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
            case 'sqlite':
                $sqlitePath = $extras['sqlite_path'] ?? $dbName ?? '';
                if (empty($sqlitePath) || !file_exists($sqlitePath)) {
                    throw new \RuntimeException("Arquivo SQLite não encontrado: {$sqlitePath}");
                }
                $dsn = "sqlite:{$sqlitePath}";
                $user = null;
                $pass = null;
                break;
            case 'odbc':
                $odbcDsn = $extras['odbc_dsn'] ?? $dbName ?? '';
                $dsn = "odbc:{$odbcDsn}";
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
        // Determinar quais outputs pular (todos exceto o branch ativo)
        $allOutputs = array_keys($node['outputs'] ?? []);
        $activeOutput = $branch;

        // Compatibilidade: branch 'true'/'false' → output_1/output_2
        if ($branch === 'true') $activeOutput = 'output_1';
        elseif ($branch === 'false') $activeOutput = 'output_2';

        foreach ($allOutputs as $outputKey) {
            if ($outputKey === $activeOutput) continue;
            foreach ($node['outputs'][$outputKey]['connections'] ?? [] as $conn) {
                $context['skip_nodes'][(string)$conn['node']] = true;
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
     * Executa nó Email (com suporte a anexos)
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

        // Coletar anexos — variável com caminhos de arquivos
        $anexos = [];
        $attachVar = trim($config['email_attachments'] ?? '');
        if ($attachVar) {
            $attachData = $context['variables'][$attachVar] ?? null;
            if ($attachData) {
                if (is_string($attachData) && file_exists($attachData)) {
                    $anexos[] = $attachData;
                } elseif (is_array($attachData)) {
                    if (isset($attachData['file_path'])) {
                        $anexos[] = $attachData['file_path'];
                    } elseif (isset($attachData['files'])) {
                        foreach ($attachData['files'] as $f) {
                            if (is_string($f) && file_exists($f)) $anexos[] = $f;
                            elseif (is_array($f) && !empty($f['file_path'])) $anexos[] = $f['file_path'];
                        }
                    } else {
                        foreach ($attachData as $f) {
                            if (is_string($f) && file_exists($f)) $anexos[] = $f;
                            elseif (is_array($f) && !empty($f['file_path'])) $anexos[] = $f['file_path'];
                        }
                    }
                }
            }
        }

        $resultado = \App\Servicos\ServicoEmail::enviar($to, $subject, $body, true, $anexos);

        if (!($resultado['sucesso'] ?? false)) {
            throw new \RuntimeException('Falha ao enviar e-mail: ' . ($resultado['erro'] ?? 'Erro desconhecido'));
        }

        return ['data' => ['sent' => true, 'to' => $to, 'subject' => $subject, 'attachments' => count($anexos)]];
    }

    /**
     * Executa nó Try/Catch - tenta executar uma ação com retry e branch de erro
     */
    private function execTryCatch(array $config, array &$context): array
    {
        $actionType = $config['action_type'] ?? '';
        $maxRetries = min((int)($config['max_retries'] ?? 0), 3);
        $retryDelay = min((int)($config['retry_delay_seconds'] ?? 1), 10);
        $errorVariable = $config['error_variable'] ?? 'last_error';

        if (empty($actionType)) {
            throw new \RuntimeException('Tipo de ação do try/catch é obrigatório');
        }

        $lastError = null;
        for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
            try {
                $result = $this->executarNode($actionType, $config, $context);
                // Sucesso: seguir output_1 (true branch)
                $context['variables'][$errorVariable] = null;
                return [
                    'data' => $result['data'] ?? null,
                    'branch' => 'output_1',
                    'attempts' => $attempt + 1
                ];
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                if ($attempt < $maxRetries) {
                    sleep($retryDelay);
                }
            }
        }

        // Falhou após todas as tentativas: seguir output_2 (catch branch)
        $context['variables'][$errorVariable] = $lastError;
        return [
            'data' => ['error' => $lastError, 'attempts' => $maxRetries + 1],
            'branch' => 'output_2'
        ];
    }

    /**
     * Executa nó Data Merge - combina dois datasets (join, union, diff)
     */
    private function execDataMerge(array $config, array &$context): array
    {
        $leftVar = $config['left_variable'] ?? '';
        $rightVar = $config['right_variable'] ?? '';
        $mergeType = $config['merge_type'] ?? 'inner_join';
        $leftKey = $config['left_key'] ?? 'id';
        $rightKey = $config['right_key'] ?? 'id';

        if (empty($leftVar) || empty($rightVar)) {
            throw new \RuntimeException('Variáveis de entrada são obrigatórias');
        }

        $left = $context['variables'][$leftVar] ?? [];
        $right = $context['variables'][$rightVar] ?? [];

        if (!is_array($left)) $left = json_decode((string)$left, true) ?: [];
        if (!is_array($right)) $right = json_decode((string)$right, true) ?: [];

        // Indexar dataset direito por chave
        $rightIndex = [];
        foreach ($right as $row) {
            $key = $row[$rightKey] ?? null;
            if ($key !== null) {
                $rightIndex[$key][] = $row;
            }
        }

        $result = [];

        switch ($mergeType) {
            case 'inner_join':
                foreach ($left as $lRow) {
                    $lKey = $lRow[$leftKey] ?? null;
                    if ($lKey !== null && isset($rightIndex[$lKey])) {
                        foreach ($rightIndex[$lKey] as $rRow) {
                            $result[] = array_merge($lRow, $rRow);
                        }
                    }
                }
                break;

            case 'left_join':
                foreach ($left as $lRow) {
                    $lKey = $lRow[$leftKey] ?? null;
                    if ($lKey !== null && isset($rightIndex[$lKey])) {
                        foreach ($rightIndex[$lKey] as $rRow) {
                            $result[] = array_merge($lRow, $rRow);
                        }
                    } else {
                        $result[] = $lRow;
                    }
                }
                break;

            case 'full_join':
                $matchedRight = [];
                foreach ($left as $lRow) {
                    $lKey = $lRow[$leftKey] ?? null;
                    if ($lKey !== null && isset($rightIndex[$lKey])) {
                        foreach ($rightIndex[$lKey] as $rRow) {
                            $result[] = array_merge($lRow, $rRow);
                            $matchedRight[$lKey] = true;
                        }
                    } else {
                        $result[] = $lRow;
                    }
                }
                foreach ($right as $rRow) {
                    $rKey = $rRow[$rightKey] ?? null;
                    if ($rKey !== null && !isset($matchedRight[$rKey])) {
                        $result[] = $rRow;
                    }
                }
                break;

            case 'union':
                $result = array_merge($left, $right);
                break;

            case 'diff':
                $rightKeys = [];
                foreach ($right as $rRow) {
                    $rKey = $rRow[$rightKey] ?? null;
                    if ($rKey !== null) $rightKeys[$rKey] = true;
                }
                foreach ($left as $lRow) {
                    $lKey = $lRow[$leftKey] ?? null;
                    if ($lKey === null || !isset($rightKeys[$lKey])) {
                        $result[] = $lRow;
                    }
                }
                break;

            default:
                throw new \RuntimeException("Tipo de merge '{$mergeType}' não suportado");
        }

        return ['data' => $result, 'count' => count($result), 'merge_type' => $mergeType];
    }

    /**
     * Executa nó SQL Upsert - inserção/atualização em lote
     */
    private function execSqlUpsert(array $config, array &$context): array
    {
        $connId = (int)($config['connection_id'] ?? 0);
        $targetTable = trim($config['target_table'] ?? '');
        $inputVar = $config['input_variable'] ?? '';
        $operation = $config['operation'] ?? 'insert';
        $conflictKeys = $config['conflict_keys'] ?? '';
        $fieldMapping = $config['field_mapping'] ?? '';
        $batchSize = min(max((int)($config['batch_size'] ?? 100), 1), 1000);
        $onError = $config['on_error'] ?? 'abort';

        if (!$connId || !$targetTable || !$inputVar) {
            throw new \RuntimeException('Conexão, tabela e variável de entrada são obrigatórios');
        }

        // Validar nome da tabela (prevenir SQL injection)
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_.]*$/', $targetTable)) {
            throw new \RuntimeException('Nome de tabela inválido');
        }

        $data = $context['variables'][$inputVar] ?? [];
        if (!is_array($data)) $data = json_decode((string)$data, true) ?: [];
        if (empty($data)) return ['data' => ['inserted' => 0, 'updated' => 0, 'failed' => 0, 'total' => 0]];

        // Normalizar mapeamento de campos
        $mapping = [];
        if (!empty($fieldMapping)) {
            $mapping = is_string($fieldMapping) ? (json_decode($fieldMapping, true) ?: []) : $fieldMapping;
        }

        // Normalizar chaves de conflito
        $cKeys = [];
        if (!empty($conflictKeys)) {
            $cKeys = is_string($conflictKeys) ? array_map('trim', explode(',', $conflictKeys)) : $conflictKeys;
        }

        // Validar nomes de colunas
        foreach ($cKeys as $ck) {
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $ck)) {
                throw new \RuntimeException("Nome de coluna de conflito inválido: {$ck}");
            }
        }

        // Buscar dados da conexão
        $db = Database::getConexao();
        $s = $db->prepare("SELECT tipo_banco, host, porta, nome_banco, usuario, senha_encriptada, parametros_extras FROM tb_perfis_conexao WHERE id = ?");
        $s->execute([$connId]);
        $conn = $s->fetch(PDO::FETCH_ASSOC);
        if (!$conn) throw new \RuntimeException("Conexão ID {$connId} não encontrada");

        $key = $_ENV['ENCRYPTION_KEY'] ?? $_SERVER['ENCRYPTION_KEY'] ?? getenv('ENCRYPTION_KEY') ?: '';
        $senha = '';
        if (!empty($conn['senha_encriptada']) && $key) {
            $senha = Crypto::decrypt($conn['senha_encriptada'], $key);
        }

        $pdo = $this->createConnection($conn['tipo_banco'], $conn['host'], $conn['porta'], $conn['nome_banco'], $conn['usuario'], $senha, $conn['parametros_extras']);

        $inserted = 0;
        $updated = 0;
        $failed = 0;
        $errors = [];

        // Processar em lotes
        $batches = array_chunk($data, $batchSize);

        foreach ($batches as $batch) {
            foreach ($batch as $row) {
                // Aplicar mapeamento
                $mapped = [];
                if (!empty($mapping)) {
                    foreach ($mapping as $src => $dst) {
                        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $dst)) continue;
                        $mapped[$dst] = $row[$src] ?? null;
                    }
                } else {
                    foreach ($row as $col => $val) {
                        if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $col)) {
                            $mapped[$col] = $val;
                        }
                    }
                }

                if (empty($mapped)) continue;

                $columns = array_keys($mapped);
                $placeholders = array_map(fn($c) => ':' . $c, $columns);

                try {
                    if ($operation === 'upsert' && !empty($cKeys) && $conn['tipo_banco'] === 'postgres') {
                        $colList = implode(', ', $columns);
                        $valList = implode(', ', $placeholders);
                        $conflictList = implode(', ', $cKeys);
                        $updateParts = [];
                        foreach ($columns as $col) {
                            if (!in_array($col, $cKeys)) {
                                $updateParts[] = "{$col} = EXCLUDED.{$col}";
                            }
                        }
                        $updateClause = !empty($updateParts) ? 'DO UPDATE SET ' . implode(', ', $updateParts) : 'DO NOTHING';
                        $sql = "INSERT INTO {$targetTable} ({$colList}) VALUES ({$valList}) ON CONFLICT ({$conflictList}) {$updateClause}";
                    } elseif ($operation === 'upsert' && !empty($cKeys) && in_array($conn['tipo_banco'], ['mysql', 'mariadb'])) {
                        $colList = implode(', ', $columns);
                        $valList = implode(', ', $placeholders);
                        $updateParts = [];
                        foreach ($columns as $col) {
                            if (!in_array($col, $cKeys)) {
                                $updateParts[] = "{$col} = VALUES({$col})";
                            }
                        }
                        $updateClause = !empty($updateParts) ? implode(', ', $updateParts) : $columns[0] . ' = VALUES(' . $columns[0] . ')';
                        $sql = "INSERT INTO {$targetTable} ({$colList}) VALUES ({$valList}) ON DUPLICATE KEY UPDATE {$updateClause}";
                    } else {
                        // Simple INSERT
                        $colList = implode(', ', $columns);
                        $valList = implode(', ', $placeholders);
                        $sql = "INSERT INTO {$targetTable} ({$colList}) VALUES ({$valList})";
                    }

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($mapped);

                    $rowCount = $stmt->rowCount();
                    if ($operation === 'upsert' && $rowCount === 2) {
                        $updated++;
                    } else {
                        $inserted++;
                    }
                } catch (\Throwable $e) {
                    $failed++;
                    $errors[] = $e->getMessage();
                    if ($onError === 'abort') {
                        throw new \RuntimeException("Erro no upsert (linha {$failed}): " . $e->getMessage());
                    }
                }
            }
        }

        return [
            'data' => [
                'inserted' => $inserted,
                'updated' => $updated,
                'failed' => $failed,
                'total' => $inserted + $updated + $failed,
                'errors' => array_slice($errors, 0, 10)
            ]
        ];
    }

    /**
     * Substitui {{variavel}} por valores
     */
    private function replaceVariables(string $text, array $vars): string
    {
        return preg_replace_callback('/\{\{([\w.]+)\}\}/', function($m) use ($vars) {
            $path = explode('.', $m[1]);
            $val = $vars[$path[0]] ?? null;
            for ($i = 1; $i < count($path); $i++) {
                if (is_array($val) && array_key_exists($path[$i], $val)) {
                    $val = $val[$path[$i]];
                } else {
                    return $m[0];
                }
            }
            if ($val === null) return $m[0];
            return is_array($val) ? json_encode($val) : (is_bool($val) ? ($val ? 'true' : 'false') : (string) $val);
        }, $text);
    }

    /**
     * Resolve um valor (pode ser referência a variável)
     */
    private function resolveValue($value, array $vars)
    {
        // Suporte a acesso simples {{var}} e aninhado {{var.key}}
        if (is_string($value) && preg_match('/^\{\{([\w.]+)\}\}$/', $value, $m)) {
            $path = explode('.', $m[1]);
            $resolved = $vars[$path[0]] ?? null;
            for ($i = 1; $i < count($path); $i++) {
                if (is_array($resolved) && array_key_exists($path[$i], $resolved)) {
                    $resolved = $resolved[$path[$i]];
                } else {
                    return $value; // caminho inválido, retorna string original
                }
            }
            return $resolved ?? $value;
        }
        if (is_string($value) && strpos($value, '{{') !== false) {
            return $this->replaceVariables($value, $vars);
        }

        // Fallback: nome de variável sem {{}} (ex: "resultado" ou "resultado.sucesso")
        if (is_string($value) && preg_match('/^[\w.]+$/', $value) && !is_numeric($value) && !in_array($value, ['true', 'false', 'null'], true)) {
            $path = explode('.', $value);
            if (array_key_exists($path[0], $vars)) {
                $resolved = $vars[$path[0]];
                for ($i = 1; $i < count($path); $i++) {
                    if (is_array($resolved) && array_key_exists($path[$i], $resolved)) {
                        $resolved = $resolved[$path[$i]];
                    } else {
                        return $value;
                    }
                }
                return $resolved;
            }
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
     * Estatísticas gerais dos pipelines (com RBAC)
     */
    public function estatisticas(): array
    {
        $db = Database::getConexao();

        // RBAC: pré-computar IDs visíveis
        $f = \App\Servicos\ServicoPermissao::filtroVisibilidadePosicional('pipeline', 'p', 'criado_por');
        $st = $db->prepare("SELECT p.id FROM tb_pipelines p WHERE ({$f['where']})");
        $st->execute($f['params']);
        $ids = $st->fetchAll(PDO::FETCH_COLUMN);
        $inSql = empty($ids) ? '(0)' : '(' . implode(',', array_map('intval', $ids)) . ')';

        $total = $db->query("SELECT COUNT(*) FROM tb_pipelines WHERE id IN {$inSql}")->fetchColumn();
        $ativos = $db->query("SELECT COUNT(*) FROM tb_pipelines WHERE ativo = true AND id IN {$inSql}")->fetchColumn();
        $execHoje = $db->query("SELECT COUNT(*) FROM tb_pipeline_execucoes WHERE data_inicio >= CURRENT_DATE AND id_pipeline IN {$inSql}")->fetchColumn();
        $successHoje = $db->query("SELECT COUNT(*) FROM tb_pipeline_execucoes WHERE data_inicio >= CURRENT_DATE AND status = 'success' AND id_pipeline IN {$inSql}")->fetchColumn();
        $errorHoje = $db->query("SELECT COUNT(*) FROM tb_pipeline_execucoes WHERE data_inicio >= CURRENT_DATE AND status = 'error' AND id_pipeline IN {$inSql}")->fetchColumn();

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
     * Lista APIs externas disponíveis para uso em nodes HTTP (com RBAC)
     */
    public function listarApisExternas(): array
    {
        $db = Database::getConexao();
        $filtro = \App\Servicos\ServicoPermissao::filtroVisibilidade('api_externa', 'a', 'criado_por');
        $sql = "
            SELECT a.id, a.nome, a.descricao, a.url, a.metodo, a.headers, a.auth_tipo,
                   a.body_template, a.tipo_resposta, a.timeout, a.ativo,
                   a.ultimo_status, a.ultima_verificacao
            FROM tb_api_externas a
            WHERE ({$filtro['where']})
            ORDER BY a.nome
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute($filtro['params']);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$r) {
            $r['headers'] = json_decode($r['headers'] ?? '{}', true);
        }

        return ['sucesso' => true, 'data' => $rows];
    }

    /**
     * Lista eventos de API disponíveis para triggers (com RBAC via API pai)
     */
    public function listarEventosApi(): array
    {
        $db = Database::getConexao();
        $filtro = \App\Servicos\ServicoPermissao::filtroVisibilidade('api_externa', 'a', 'criado_por');
        $sql = "
            SELECT e.id, e.nome, e.descricao, e.jsonpath, e.operador, 
                   e.valor_esperado, e.acao, e.ativo, e.total_matches,
                   a.nome as api_nome, a.url as api_url
            FROM tb_eventos_api e
            JOIN tb_api_externas a ON a.id = e.id_api
            WHERE e.ativo = true AND ({$filtro['where']})
            ORDER BY e.nome
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute($filtro['params']);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['sucesso' => true, 'data' => $rows];
    }

    /**
     * Lista rotinas disponíveis para uso em pipelines (com RBAC)
     */
    public function listarRotinas(): array
    {
        $db = Database::getConexao();
        $filtro = \App\Servicos\ServicoPermissao::filtroVisibilidade('rotina', 'r', 'id_usuario_criador');
        $sql = "
            SELECT r.id, r.nome, r.descricao, r.ativa, r.agendamento_cron,
                   c.nome_conexao, c.tipo_banco
            FROM tb_rotinas r
            LEFT JOIN tb_perfis_conexao c ON c.id = r.id_conexao
            WHERE ({$filtro['where']})
            ORDER BY r.nome
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute($filtro['params']);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                case 'sqlite':
                    $stmt = $pdo->query("SELECT 'main' as table_schema, name as table_name, type as table_type FROM sqlite_master WHERE type IN ('table','view') AND name NOT LIKE 'sqlite_%' ORDER BY name");
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
                case 'sqlite':
                    $cols = $pdo->query("PRAGMA table_info(" . $pdo->quote($tabela) . ")")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($cols as $col) {
                        $columns[] = [
                            'column_name' => $col['name'],
                            'data_type' => $col['type'] ?: 'TEXT',
                            'is_nullable' => $col['notnull'] ? 'NO' : 'YES',
                            'column_default' => $col['dflt_value']
                        ];
                    }
                    break;
            }

            return ['sucesso' => true, 'data' => $columns];
        } catch (\Throwable $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao listar colunas: ' . $e->getMessage()];
        }
    }

    /**
     * Switch/Case — roteamento multi-branch (3 outputs)
     * Output 1 = case_1 match, Output 2 = case_2 match, Output 3 = default
     */
    private function execSwitchCase(array $config, array &$context): array
    {
        $varName = $config['switch_variable'] ?? '';
        // Buscar o valor: se é referência a variável (com ou sem {{}}), resolver
        $wrappedVar = (strpos($varName, '{{') === false) ? '{{' . $varName . '}}' : $varName;
        $value = $this->resolveValue($wrappedVar, $context['variables']);
        $case1 = $config['case_1'] ?? '';
        $case2 = $config['case_2'] ?? '';

        if ((string)$value === (string)$case1) {
            $branch = 'output_1';
        } elseif ((string)$value === (string)$case2) {
            $branch = 'output_2';
        } else {
            $branch = 'output_3';
        }

        return [
            'data' => ['value' => $value, 'matched_branch' => $branch],
            'branch' => $branch
        ];
    }

    /**
     * Format Template — interpola variáveis em um template de texto
     */
    private function execFormatTemplate(array $config, array &$context): array
    {
        $template = $config['template'] ?? '';
        $result = $this->replaceVariables($template, $context['variables']);
        return ['data' => $result];
    }

    /**
     * Regex — match, match_all, replace ou test sobre uma variável
     */
    private function execRegex(array $config, array &$context): array
    {
        $mode = $config['regex_mode'] ?? 'match';
        $inputVar = $config['input_variable'] ?? '';
        $input = $context['variables'][$inputVar] ?? $this->resolveValue($inputVar, $context['variables']);
        if (!is_string($input)) {
            $input = json_encode($input);
        }
        $pattern = $config['pattern'] ?? '';

        // Validar que o pattern é um regex PCRE válido
        if (@preg_match($pattern, '') === false) {
            throw new \RuntimeException("Padrão regex inválido: {$pattern}");
        }

        switch ($mode) {
            case 'match':
                preg_match($pattern, $input, $matches);
                return ['data' => $matches ?: []];

            case 'match_all':
                preg_match_all($pattern, $input, $matches);
                return ['data' => $matches[0] ?? []];

            case 'replace':
                $replacement = $config['replacement'] ?? '';
                $result = preg_replace($pattern, $replacement, $input);
                return ['data' => $result];

            case 'test':
                $result = (bool)preg_match($pattern, $input);
                return ['data' => $result];

            default:
                return ['data' => null];
        }
    }

    /**
     * CSV Parse — converte texto CSV em array de objetos.
     * Se a entrada já for um array (ex: resultado de SQL Query), converte para CSV e re-parseia,
     * ou simplesmente repassa os dados.
     */
    private function execCsvParse(array $config, array &$context): array
    {
        $inputVar = $config['input_variable'] ?? '';
        $input = $context['variables'][$inputVar] ?? $this->resolveValue($inputVar, $context['variables']);

        // Se já é um array de objetos (ex: resultado de SQL), tratar diretamente
        if (is_array($input)) {
            $outputVar = $config['output_variable'] ?? '';
            if ($outputVar) {
                $context['variables'][$outputVar] = $input;
            }
            return ['data' => $input, 'variables' => $outputVar ? [$outputVar => $input] : []];
        }

        if (!is_string($input)) {
            throw new \RuntimeException("Variável '{$inputVar}' não contém texto CSV");
        }

        $delimiter = $config['delimiter'] ?? ',';
        $enclosure = $config['enclosure'] ?? '"';
        $hasHeader = ($config['has_header'] ?? 'true') === 'true';

        $lines = [];
        $rows = explode("\n", str_replace("\r\n", "\n", $input));
        foreach ($rows as $row) {
            $row = trim($row);
            if ($row === '') continue;
            $lines[] = str_getcsv($row, $delimiter, $enclosure);
        }

        if (empty($lines)) {
            return ['data' => []];
        }

        $outputVar = $config['output_variable'] ?? '';
        if ($hasHeader) {
            $headers = array_shift($lines);
            $result = [];
            foreach ($lines as $line) {
                $row = [];
                foreach ($headers as $i => $header) {
                    $row[trim($header)] = $line[$i] ?? null;
                }
                $result[] = $row;
            }
            if ($outputVar) {
                $context['variables'][$outputVar] = $result;
            }
            return ['data' => $result, 'variables' => $outputVar ? [$outputVar => $result] : []];
        }

        if ($outputVar) {
            $context['variables'][$outputVar] = $lines;
        }
        return ['data' => $lines, 'variables' => $outputVar ? [$outputVar => $lines] : []];
    }

    /**
     * Counter — mantém um contador no contexto de variáveis
     */
    private function execCounter(array $config, array &$context): array
    {
        $name = $config['counter_name'] ?? 'counter';
        $action = $config['counter_action'] ?? 'increment';
        $current = (int)($context['variables'][$name] ?? 0);

        switch ($action) {
            case 'increment':
                $current++;
                break;
            case 'decrement':
                $current--;
                break;
            case 'reset':
                $current = 0;
                break;
            case 'set':
                $current = (int)($config['counter_value'] ?? 0);
                break;
        }

        $context['variables'][$name] = $current;
        return ['data' => $current];
    }

    private function execRotina(array $config, array &$context): array
    {
        $idRotina = $config['rotina_id'] ?? null;
        if (!$idRotina) {
            throw new \Exception('ID da rotina não especificado');
        }

        $stopRaw = $config['stop_on_error'] ?? true;
        $stopOnError = filter_var($stopRaw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;

        $controller = new RotinasController();
        $resultado = $controller->executar((int)$idRotina);

        $metricas = $resultado['metricas'] ?? [];
        $data = [
            'rotina_id' => (int)$idRotina,
            'sucesso' => !empty($resultado['sucesso']),
            'status' => $resultado['status'] ?? ($resultado['sucesso'] ? 'sucesso' : 'falha'),
            'mensagem' => $resultado['mensagem'] ?? '',
            'blocos_executados' => $metricas['blocos_executados'] ?? 0,
            'blocos_sucesso' => $metricas['blocos_sucesso'] ?? 0,
            'blocos_falha' => $metricas['blocos_falha'] ?? 0,
            'registros_total' => $metricas['registros_total'] ?? 0,
            'duracao_ms' => $metricas['duracao_ms'] ?? 0,
        ];

        if (!empty($config['output_variable'])) {
            $context['variables'][$config['output_variable']] = $data;
        }

        if (empty($resultado['sucesso']) && $stopOnError) {
            throw new \Exception($resultado['mensagem'] ?? 'Erro ao executar rotina');
        }

        return ['data' => $data];
    }

    /**
     * Data Format — converte dados (array) em arquivo formatado para uso em email ou download
     * Formatos: csv, json, html_table, pdf
     */
    private function execDataFormat(array $config, array &$context): array
    {
        $inputVar = $config['input_variable'] ?? '';
        $data = $context['variables'][$inputVar] ?? $this->resolveValue($inputVar, $context['variables']);

        // Se não encontrou na variável, tenta no resultado do nó anterior
        if ($data === $inputVar && isset($context['results'])) {
            foreach (array_reverse($context['results'], true) as $nodeResult) {
                if (isset($nodeResult['data'])) {
                    if (is_array($nodeResult['data']) && isset($nodeResult['data']['rows'])) {
                        $data = $nodeResult['data']['rows'];
                    } elseif (is_array($nodeResult['data'])) {
                        $data = $nodeResult['data'];
                    }
                    break;
                }
            }
        }

        if ($data === null || $data === $inputVar) {
            throw new \RuntimeException("Variável '{$inputVar}' não encontrada");
        }

        if (is_string($data)) {
            $decoded = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $data = $decoded;
            } else {
                $data = [['value' => $data]];
            }
        }

        if (!is_array($data)) {
            $data = [['value' => $data]];
        }

        $format = strtolower($config['format'] ?? 'csv');
        $allowedFormats = ['csv', 'json', 'html_table', 'pdf'];
        if (!in_array($format, $allowedFormats)) {
            throw new \RuntimeException("Formato '{$format}' não suportado. Use: " . implode(', ', $allowedFormats));
        }

        // Nome e diretório do arquivo
        $filename = $config['filename'] ?? 'dados';
        $filename = $this->processExportFilename($filename, $config, $context);
        $basePath = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);
        $exportDir = $basePath . '/storage/exports';
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0755, true);
        }

        $title = $config['title'] ?? 'Relatório de Dados';
        $title = $this->replaceVariables($title, $context['variables']);

        $ext = ($format === 'html_table') ? 'html' : $format;
        $fullPath = $exportDir . DIRECTORY_SEPARATOR . $filename . '.' . $ext;

        switch ($format) {
            case 'csv':
                $delimiter = $config['csv_delimiter'] ?? ';';
                $this->exportToCsv($data, $fullPath, ['csv_delimiter' => $delimiter, 'csv_enclosure' => '"']);
                break;

            case 'json':
                $pretty = ($config['json_pretty'] ?? 'true') === 'true';
                $this->exportToJson($data, $fullPath, ['json_pretty' => $pretty ? 'true' : 'false']);
                break;

            case 'html_table':
                $this->formatToHtmlTable($data, $fullPath, $title, $config);
                break;

            case 'pdf':
                $this->formatToPdf($data, $fullPath, $title, $config);
                break;
        }

        $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
        $recordCount = is_array($data) ? count($data) : 1;

        $result = [
            'file_path' => $fullPath,
            'filename' => basename($fullPath),
            'format' => $format,
            'records' => $recordCount,
            'file_size' => $fileSize,
            'file_size_formatted' => $this->formatBytes($fileSize),
        ];

        if (!empty($config['output_variable'])) {
            $context['variables'][$config['output_variable']] = $result;
        }

        return ['data' => $result];
    }

    /**
     * Gera tabela HTML estilizada com cabeçalho, cores e rodapé
     */
    private function formatToHtmlTable(array $data, string $path, string $title, array $config): void
    {
        $accentColor = $config['accent_color'] ?? '#3b82f6';
        $showRowNumbers = ($config['show_row_numbers'] ?? 'false') === 'true';

        $html = '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><title>' . htmlspecialchars($title) . '</title>';
        $html .= '<style>';
        $html .= 'body{font-family:"Segoe UI",Roboto,sans-serif;margin:30px;background:#f8fafc;color:#1e293b}';
        $html .= '.report-header{background:' . htmlspecialchars($accentColor) . ';color:#fff;padding:20px 28px;border-radius:10px 10px 0 0;margin-bottom:0}';
        $html .= '.report-header h1{margin:0;font-size:1.4rem;font-weight:600}';
        $html .= '.report-header .meta{font-size:.82rem;opacity:.85;margin-top:4px}';
        $html .= '.report-body{background:#fff;border:1px solid #e2e8f0;border-top:none;border-radius:0 0 10px 10px;overflow:hidden}';
        $html .= 'table{border-collapse:collapse;width:100%}';
        $html .= 'th{background:#f1f5f9;padding:10px 14px;text-align:left;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px;color:#64748b;border-bottom:2px solid #e2e8f0}';
        $html .= 'td{padding:9px 14px;font-size:.85rem;border-bottom:1px solid #f1f5f9}';
        $html .= 'tr:nth-child(even){background:#fafbfc}';
        $html .= 'tr:hover{background:#f0f4ff}';
        $html .= '.report-footer{text-align:center;padding:14px;font-size:.75rem;color:#94a3b8}';
        $html .= '</style></head><body>';
        $html .= '<div class="report-header"><h1>' . htmlspecialchars($title) . '</h1>';
        $html .= '<div class="meta">Gerado em: ' . date('d/m/Y H:i:s') . ' | Registros: ' . count($data) . '</div></div>';
        $html .= '<div class="report-body"><table>';

        if (!empty($data) && is_array($data[0] ?? null)) {
            $html .= '<thead><tr>';
            if ($showRowNumbers) $html .= '<th>#</th>';
            foreach (array_keys($data[0]) as $col) {
                $html .= '<th>' . htmlspecialchars($col) . '</th>';
            }
            $html .= '</tr></thead><tbody>';
            $i = 1;
            foreach ($data as $row) {
                $html .= '<tr>';
                if ($showRowNumbers) $html .= '<td style="color:#94a3b8;font-size:.78rem">' . $i++ . '</td>';
                foreach ($row as $val) {
                    $html .= '<td>' . htmlspecialchars(is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : (string)$val) . '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</tbody>';
        }

        $html .= '</table></div>';
        $html .= '<div class="report-footer">DMC DataLoad — Pipeline Builder</div>';
        $html .= '</body></html>';

        file_put_contents($path, $html);
    }

    /**
     * Gera PDF a partir de dados tabulares (usando HTML → PDF sem dependências externas)
     * Usa a mesma tabela HTML renderizada, salva como PDF-simulado via wkhtmltopdf ou fallback HTML
     */
    private function formatToPdf(array $data, string $path, string $title, array $config): void
    {
        // Gerar HTML da tabela primeiro
        $htmlPath = str_replace('.pdf', '_temp.html', $path);
        $this->formatToHtmlTable($data, $htmlPath, $title, $config);

        // Tentar usar wkhtmltopdf se disponível
        $wkhtmltopdf = $this->findWkhtmltopdf();
        if ($wkhtmltopdf) {
            $cmd = escapeshellarg($wkhtmltopdf) . ' --quiet --encoding UTF-8 --page-size A4 --margin-top 10 --margin-bottom 10 --margin-left 10 --margin-right 10 ' 
                 . escapeshellarg($htmlPath) . ' ' . escapeshellarg($path);
            exec($cmd, $output, $exitCode);
            @unlink($htmlPath);
            if ($exitCode === 0 && file_exists($path)) {
                return;
            }
        }

        // Fallback: salvar como HTML com extensão .pdf (abre no navegador)
        // Adicionar script de auto-impressão
        $htmlContent = file_get_contents($htmlPath);
        $htmlContent = str_replace('</body>', '<script>window.onload=function(){window.print()}</script></body>', $htmlContent);
        file_put_contents($path, $htmlContent);
        @unlink($htmlPath);
    }

    /**
     * Tenta localizar wkhtmltopdf no sistema
     */
    private function findWkhtmltopdf(): ?string
    {
        $paths = [
            'wkhtmltopdf',
            'C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe',
            'C:\\Program Files (x86)\\wkhtmltopdf\\bin\\wkhtmltopdf.exe',
            '/usr/local/bin/wkhtmltopdf',
            '/usr/bin/wkhtmltopdf',
        ];
        foreach ($paths as $p) {
            if (str_contains($p, DIRECTORY_SEPARATOR) || str_contains($p, '/')) {
                if (file_exists($p)) return $p;
            } else {
                $which = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'where' : 'which';
                exec("$which $p 2>&1", $out, $code);
                if ($code === 0 && !empty($out[0])) return trim($out[0]);
            }
        }
        return null;
    }

    /**
     * File Export — exporta dados para arquivo em formatos variados
     * Suporta variáveis de sequência no nome do arquivo:
     *   {seq} - sequencial auto-incremento
     *   {date} - data no formato YYYY-MM-DD
     *   {time} - hora no formato HH-MM-SS
     *   {datetime} - data e hora YYYY-MM-DD_HH-MM-SS
     *   {timestamp} - unix timestamp
     *   {Y},{m},{d},{H},{i},{s} - componentes individuais
     *   {{variavel}} - variáveis do contexto do pipeline
     */
    private function execFileExport(array $config, array &$context): array
    {
        $inputVar = $config['input_variable'] ?? '';
        $data = $context['variables'][$inputVar] ?? $this->resolveValue($inputVar, $context['variables']);

        // Se não encontrou na variável, tenta no resultado do nó anterior
        if ($data === $inputVar && isset($context['results'])) {
            foreach (array_reverse($context['results'], true) as $nodeResult) {
                if (isset($nodeResult['data'])) {
                    if (is_array($nodeResult['data']) && isset($nodeResult['data']['rows'])) {
                        $data = $nodeResult['data']['rows'];
                    } elseif (is_array($nodeResult['data'])) {
                        $data = $nodeResult['data'];
                    }
                    break;
                }
            }
        }

        if ($data === null || $data === $inputVar) {
            throw new \RuntimeException("Variável '{$inputVar}' não encontrada para exportação");
        }

        // Se é string JSON, decodificar
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $data = $decoded;
            }
        }

        if (!is_array($data)) {
            $data = [['value' => $data]];
        }

        $format = strtolower($config['export_format'] ?? 'csv');
        $allowedFormats = ['csv', 'json', 'xml', 'txt', 'html', 'sql'];
        if (!in_array($format, $allowedFormats)) {
            throw new \RuntimeException("Formato '{$format}' não suportado. Use: " . implode(', ', $allowedFormats));
        }

        // Processar nome do arquivo com variáveis de sequência
        $filename = $config['filename'] ?? 'export';
        $filename = $this->processExportFilename($filename, $config, $context);

        // Determinar diretório de destino
        $exportDir = $config['export_directory'] ?? 'storage/exports';
        $fullDir = $this->resolveExportDirectory($exportDir);

        if (!is_dir($fullDir)) {
            if (!mkdir($fullDir, 0755, true)) {
                throw new \RuntimeException("Não foi possível criar o diretório: {$exportDir}");
            }
        }

        $fullPath = $fullDir . DIRECTORY_SEPARATOR . $filename . '.' . $format;

        // Exportar conforme formato
        switch ($format) {
            case 'csv':
                $this->exportToCsv($data, $fullPath, $config);
                break;
            case 'json':
                $this->exportToJson($data, $fullPath, $config);
                break;
            case 'xml':
                $this->exportToXml($data, $fullPath, $config);
                break;
            case 'txt':
                $this->exportToTxt($data, $fullPath, $config);
                break;
            case 'html':
                $this->exportToHtml($data, $fullPath, $config);
                break;
            case 'sql':
                $this->exportToSql($data, $fullPath, $config);
                break;
        }

        $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
        $recordCount = is_array($data) ? count($data) : 1;

        $result = [
            'file_path' => $fullPath,
            'filename' => basename($fullPath),
            'directory' => dirname($fullPath),
            'format' => $format,
            'records' => $recordCount,
            'file_size' => $fileSize,
            'file_size_formatted' => $this->formatBytes($fileSize)
        ];

        if (!empty($config['output_variable'])) {
            $context['variables'][$config['output_variable']] = $result;
        }
        $context['variables']['last_export_path'] = $fullPath;
        $context['variables']['last_export_filename'] = basename($fullPath);

        return ['data' => $result];
    }

    /**
     * Processa variáveis de sequência no nome do arquivo
     */
    private function processExportFilename(string $filename, array $config, array &$context): string
    {
        $now = new \DateTime();

        // Variáveis de data/hora
        $replacements = [
            '{date}' => $now->format('Y-m-d'),
            '{time}' => $now->format('H-i-s'),
            '{datetime}' => $now->format('Y-m-d_H-i-s'),
            '{timestamp}' => (string) time(),
            '{Y}' => $now->format('Y'),
            '{m}' => $now->format('m'),
            '{d}' => $now->format('d'),
            '{H}' => $now->format('H'),
            '{i}' => $now->format('i'),
            '{s}' => $now->format('s'),
        ];

        $filename = str_replace(array_keys($replacements), array_values($replacements), $filename);

        // Variável {seq} - sequencial auto-incremento por diretório+prefixo
        if (strpos($filename, '{seq}') !== false) {
            $seqKey = 'file_export_seq_' . md5(($config['export_directory'] ?? '') . '_' . ($config['filename'] ?? ''));
            $seq = (int)($context['variables'][$seqKey] ?? 0) + 1;

            // Também verifica arquivos existentes no diretório para continuar a sequência
            $exportDir = $config['export_directory'] ?? 'storage/exports';
            $fullDir = $this->resolveExportDirectory($exportDir);
            if (is_dir($fullDir)) {
                $pattern = str_replace('{seq}', '*', $config['filename'] ?? 'export');
                $existing = glob($fullDir . DIRECTORY_SEPARATOR . $pattern . '.*');
                if (!empty($existing)) {
                    // Extrair o maior número sequencial
                    $maxSeq = 0;
                    foreach ($existing as $file) {
                        $base = pathinfo($file, PATHINFO_FILENAME);
                        if (preg_match('/(\d+)/', str_replace(str_replace('*', '', $pattern), '', $base), $m)) {
                            $maxSeq = max($maxSeq, (int)$m[1]);
                        }
                    }
                    $seq = max($seq, $maxSeq + 1);
                }
            }

            $seqPad = str_pad((string) $seq, (int)($config['seq_padding'] ?? 4), '0', STR_PAD_LEFT);
            $filename = str_replace('{seq}', $seqPad, $filename);
            $context['variables'][$seqKey] = $seq;
        }

        // Variáveis do contexto {{variavel}} 
        $filename = $this->replaceVariables($filename, $context['variables']);

        // Sanitizar nome do arquivo
        $filename = preg_replace('/[^a-zA-Z0-9._\-]/', '_', $filename);
        if (empty($filename)) $filename = 'export_' . time();

        return $filename;
    }

    /**
     * Resolve o diretório de exportação — aceita caminhos absolutos ou atalhos
     */
    private function resolveExportDirectory(string $dir): string
    {
        $basePath = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);

        // Atalhos conhecidos
        $shortcuts = [
            'storage/exports' => $basePath . '/storage/exports',
            'storage/csv' => $basePath . '/storage/csv',
            'desktop' => $this->getUserDesktopPath(),
            'documentos' => $this->getUserDocumentsPath(),
            'downloads' => $this->getUserDownloadsPath(),
        ];

        $dirTrim = trim($dir);
        $dirLower = strtolower($dirTrim);

        // Atalho conhecido
        if (isset($shortcuts[$dirLower])) {
            return $shortcuts[$dirLower];
        }

        // Sub-pastas de storage
        if (strpos($dir, 'storage/') === 0) {
            $resolved = $basePath . '/' . $dir;
            $real = realpath(dirname($resolved));
            $storageReal = realpath($basePath . '/storage');
            if ($real !== false && $storageReal !== false && strpos($real, $storageReal) === 0) {
                return $resolved;
            }
        }

        // Caminho absoluto — verificar se existe e é gravável
        if ($this->isAbsolutePath($dirTrim)) {
            $realDir = realpath($dirTrim);
            if ($realDir !== false && is_dir($realDir) && is_writable($realDir)) {
                return $realDir;
            }
            // Se o diretório não existe ainda, tentar criar
            if (!is_dir($dirTrim) && @mkdir($dirTrim, 0755, true)) {
                return realpath($dirTrim) ?: $dirTrim;
            }
        }

        // Default: storage/exports
        return $basePath . '/storage/exports';
    }

    private function isAbsolutePath(string $path): bool
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return (bool) preg_match('/^[A-Za-z]:[\\\/]/', $path);
        }
        return str_starts_with($path, '/');
    }

    /**
     * Retorna atalhos rápidos + raízes do sistema para o browser de diretórios
     */
    public function listarDiretoriosExport(): array
    {
        $basePath = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);

        // Garantir que storage/exports existe
        $exportsDir = $basePath . '/storage/exports';
        if (!is_dir($exportsDir)) {
            @mkdir($exportsDir, 0755, true);
        }

        $shortcuts = [];
        $shortcuts[] = ['id' => 'storage/exports', 'nome' => 'Storage/Exports (padrão)', 'path' => str_replace('/', DIRECTORY_SEPARATOR, $basePath . '/storage/exports'), 'writable' => true, 'type' => 'shortcut'];
        $shortcuts[] = ['id' => 'storage/csv', 'nome' => 'Storage/CSV', 'path' => str_replace('/', DIRECTORY_SEPARATOR, $basePath . '/storage/csv'), 'writable' => true, 'type' => 'shortcut'];

        $special = [
            ['key' => 'desktop', 'nome' => 'Desktop (Área de Trabalho)', 'path' => $this->getUserDesktopPath()],
            ['key' => 'documentos', 'nome' => 'Documentos', 'path' => $this->getUserDocumentsPath()],
            ['key' => 'downloads', 'nome' => 'Downloads', 'path' => $this->getUserDownloadsPath()],
        ];

        foreach ($special as $s) {
            if ($s['path'] && is_dir($s['path'])) {
                $shortcuts[] = ['id' => $s['key'], 'nome' => $s['nome'], 'path' => $s['path'], 'writable' => is_writable($s['path']), 'type' => 'shortcut'];
            }
        }

        // Drives / raízes do sistema
        $roots = [];
        if (PHP_OS_FAMILY === 'Windows') {
            foreach (range('A', 'Z') as $letter) {
                $drv = $letter . ':\\';
                if (is_dir($drv)) {
                    $roots[] = ['letter' => $letter, 'path' => $drv, 'writable' => @is_writable($drv)];
                }
            }
        } else {
            $roots[] = ['letter' => '/', 'path' => '/', 'writable' => is_writable('/')];
        }

        return ['shortcuts' => $shortcuts, 'roots' => $roots];
    }

    /**
     * Navega um diretório e retorna sub-pastas — para o browser de diretórios
     */
    public function navegarDiretorio(string $path): array
    {
        $realPath = realpath($path);
        if ($realPath === false || !is_dir($realPath)) {
            return ['erro' => 'Diretório não encontrado', 'path' => $path];
        }

        if (!is_readable($realPath)) {
            return ['erro' => 'Sem permissão de leitura', 'path' => $realPath];
        }

        $subdirs = [];
        $entries = @scandir($realPath);
        if ($entries === false) {
            return ['erro' => 'Não foi possível listar o diretório', 'path' => $realPath];
        }

        sort($entries, SORT_NATURAL | SORT_FLAG_CASE);

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') continue;
            if (str_starts_with($entry, '.') && $entry !== '..') continue; // ocultos

            $full = $realPath . DIRECTORY_SEPARATOR . $entry;
            if (!is_dir($full)) continue;

            $subdirs[] = [
                'name' => $entry,
                'path' => $full,
                'writable' => @is_writable($full),
                'has_children' => $this->dirHasSubdirs($full),
            ];
        }

        // Parent path
        $parent = dirname($realPath);
        $parentAvailable = ($parent !== $realPath); // raiz não tem pai

        return [
            'path' => $realPath,
            'parent' => $parentAvailable ? $parent : null,
            'writable' => @is_writable($realPath),
            'dirs' => $subdirs,
        ];
    }

    private function dirHasSubdirs(string $path): bool
    {
        $entries = @scandir($path);
        if (!$entries) return false;
        foreach ($entries as $e) {
            if ($e === '.' || $e === '..') continue;
            if (is_dir($path . DIRECTORY_SEPARATOR . $e)) return true;
        }
        return false;
    }

    private function getUserDesktopPath(): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return getenv('USERPROFILE') . '\\Desktop';
        }
        return getenv('HOME') . '/Desktop';
    }

    private function getUserDocumentsPath(): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return getenv('USERPROFILE') . '\\Documents';
        }
        return getenv('HOME') . '/Documents';
    }

    private function getUserDownloadsPath(): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return getenv('USERPROFILE') . '\\Downloads';
        }
        return getenv('HOME') . '/Downloads';
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    // --- Métodos de exportação por formato ---

    private function exportToCsv(array $data, string $path, array $config): void
    {
        $delimiter = $config['csv_delimiter'] ?? ',';
        $enclosure = $config['csv_enclosure'] ?? '"';
        $fp = fopen($path, 'w');
        if ($fp === false) throw new \RuntimeException('Não foi possível abrir o arquivo para escrita');

        // BOM para UTF-8 (compatibilidade Excel)
        fwrite($fp, "\xEF\xBB\xBF");

        if (!empty($data) && is_array($data[0] ?? null)) {
            fputcsv($fp, array_keys($data[0]), $delimiter, $enclosure);
            foreach ($data as $row) {
                if (is_array($row)) {
                    fputcsv($fp, array_map(function($v) { return is_array($v) ? json_encode($v) : $v; }, $row), $delimiter, $enclosure);
                }
            }
        } else {
            foreach ($data as $item) {
                fputcsv($fp, is_array($item) ? $item : [$item], $delimiter, $enclosure);
            }
        }
        fclose($fp);
    }

    private function exportToJson(array $data, string $path, array $config): void
    {
        $pretty = ($config['json_pretty'] ?? 'true') === 'true';
        $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        if ($pretty) $flags |= JSON_PRETTY_PRINT;
        $json = json_encode($data, $flags);
        if ($json === false) throw new \RuntimeException('Erro ao codificar JSON: ' . json_last_error_msg());
        file_put_contents($path, $json);
    }

    private function exportToXml(array $data, string $path, array $config): void
    {
        $rootName = $config['xml_root'] ?? 'data';
        $rowName = $config['xml_row'] ?? 'row';
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><' . htmlspecialchars($rootName) . '/>');

        foreach ($data as $row) {
            $child = $xml->addChild($rowName);
            if (is_array($row)) {
                foreach ($row as $key => $value) {
                    $safeKey = preg_replace('/[^a-zA-Z0-9_]/', '_', $key);
                    if (is_numeric($safeKey[0] ?? '')) $safeKey = '_' . $safeKey;
                    $child->addChild($safeKey, htmlspecialchars(is_array($value) ? json_encode($value) : (string)$value));
                }
            } else {
                $child->addChild('value', htmlspecialchars((string)$row));
            }
        }

        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        file_put_contents($path, $dom->saveXML());
    }

    private function exportToTxt(array $data, string $path, array $config): void
    {
        $separator = $config['txt_separator'] ?? "\t";
        $lines = [];

        if (!empty($data) && is_array($data[0] ?? null)) {
            $lines[] = implode($separator, array_keys($data[0]));
            foreach ($data as $row) {
                $lines[] = implode($separator, array_map(function($v) { return is_array($v) ? json_encode($v) : (string)$v; }, $row));
            }
        } else {
            foreach ($data as $item) {
                $lines[] = is_array($item) ? json_encode($item) : (string)$item;
            }
        }

        file_put_contents($path, implode("\n", $lines));
    }

    private function exportToHtml(array $data, string $path, array $config): void
    {
        $title = htmlspecialchars($config['html_title'] ?? 'Export Data');
        $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>' . $title . '</title>';
        $html .= '<style>table{border-collapse:collapse;width:100%;font-family:sans-serif}th,td{border:1px solid #ddd;padding:8px;text-align:left}th{background:#f4f4f4;font-weight:bold}tr:nth-child(even){background:#fafafa}h1{font-family:sans-serif;color:#333}</style>';
        $html .= '</head><body><h1>' . $title . '</h1>';
        $html .= '<p>Exportado em: ' . date('d/m/Y H:i:s') . ' | Registros: ' . count($data) . '</p>';
        $html .= '<table>';

        if (!empty($data) && is_array($data[0] ?? null)) {
            $html .= '<thead><tr>';
            foreach (array_keys($data[0]) as $col) {
                $html .= '<th>' . htmlspecialchars($col) . '</th>';
            }
            $html .= '</tr></thead><tbody>';
            foreach ($data as $row) {
                $html .= '<tr>';
                foreach ($row as $val) {
                    $html .= '<td>' . htmlspecialchars(is_array($val) ? json_encode($val) : (string)$val) . '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</tbody>';
        }

        $html .= '</table></body></html>';
        file_put_contents($path, $html);
    }

    private function exportToSql(array $data, string $path, array $config): void
    {
        $table = $config['sql_table_name'] ?? 'exported_data';
        // Sanitizar nome da tabela
        $table = preg_replace('/[^a-zA-Z0-9_.]/', '', $table);
        if (empty($table)) $table = 'exported_data';

        $lines = ['-- Exportação SQL gerada por DMC-DATALOAD Pipeline', '-- Data: ' . date('Y-m-d H:i:s'), ''];

        if (!empty($data) && is_array($data[0] ?? null)) {
            $cols = array_keys($data[0]);
            foreach ($data as $row) {
                $values = array_map(function ($v) {
                    if ($v === null) return 'NULL';
                    if (is_numeric($v)) return $v;
                    return "'" . str_replace("'", "''", (string)$v) . "'";
                }, array_values($row));
                $lines[] = 'INSERT INTO ' . $table . ' (' . implode(', ', $cols) . ') VALUES (' . implode(', ', $values) . ');';
            }
        }

        file_put_contents($path, implode("\n", $lines));
    }
}
