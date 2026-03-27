<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\AuthMiddleware;
use App\Servicos\ServicoPermissao;
use App\Utils\Crypto;
use PDO;
use Exception;

class MonitoramentoController
{
    /**
     * Lista conexões acessíveis ao usuário para monitoramento
     */
    public function listarConexoes(): array
    {
        try {
            $db = Database::getConexao();
            $filtro = ServicoPermissao::filtroVisibilidadePosicional('conexao', 'c', 'criado_por');

            $sql = "SELECT c.id, c.nome_conexao, c.tipo_banco, c.host, c.porta, c.nome_banco, c.usuario
                    FROM tb_perfis_conexao c
                    WHERE {$filtro['where']}
                    ORDER BY c.nome_conexao";

            $stmt = $db->prepare($sql);
            $stmt->execute($filtro['params']);
            $conexoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return ['sucesso' => true, 'conexoes' => $conexoes];
        } catch (Exception $e) {
            return ['sucesso' => false, 'erro' => $e->getMessage()];
        }
    }

    /**
     * Verifica se o usuário do banco tem acesso a metadados de monitoramento
     */
    public function verificarAcesso(int $conexaoId): array
    {
        try {
            if (!ServicoPermissao::podeVerRecurso('conexao', $conexaoId)) {
                return ['sucesso' => false, 'erro' => 'Sem permissão para acessar esta conexão'];
            }

            $db = Database::getConexao();
            $stmt = $db->prepare('SELECT * FROM tb_perfis_conexao WHERE id = ?');
            $stmt->execute([$conexaoId]);
            $conexao = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$conexao) {
                return ['sucesso' => false, 'erro' => 'Conexão não encontrada'];
            }

            $targetDb = $this->criarConexao($conexao);
            $tipo = $conexao['tipo_banco'];
            $temAcesso = false;
            $mensagem = '';

            switch ($tipo) {
                case 'postgres':
                    $temAcesso = $this->verificarAcessoPostgres($targetDb);
                    $mensagem = $temAcesso
                        ? "Usuário '{$conexao['usuario']}' tem acesso a pg_stat_activity."
                        : "Usuário '{$conexao['usuario']}' NÃO tem permissão para acessar pg_stat_activity. É necessário o privilégio pg_monitor ou superuser.";
                    break;

                case 'mysql':
                    $temAcesso = $this->verificarAcessoMysql($targetDb);
                    $mensagem = $temAcesso
                        ? "Usuário '{$conexao['usuario']}' tem acesso ao PROCESSLIST."
                        : "Usuário '{$conexao['usuario']}' NÃO tem o privilégio PROCESS. Execute: GRANT PROCESS ON *.* TO '{$conexao['usuario']}'@'%';";
                    break;

                case 'sqlserver':
                    $temAcesso = $this->verificarAcessoSqlServer($targetDb);
                    $mensagem = $temAcesso
                        ? "Usuário '{$conexao['usuario']}' tem acesso às DMVs de monitoramento."
                        : "Usuário '{$conexao['usuario']}' NÃO tem permissão VIEW SERVER STATE. Execute: GRANT VIEW SERVER STATE TO [{$conexao['usuario']}];";
                    break;

                case 'oracle':
                    $temAcesso = $this->verificarAcessoOracle($targetDb);
                    $mensagem = $temAcesso
                        ? "Usuário '{$conexao['usuario']}' tem acesso a V\$SESSION."
                        : "Usuário '{$conexao['usuario']}' NÃO tem acesso a V\$SESSION. Execute: GRANT SELECT ON V_\$SESSION TO {$conexao['usuario']};";
                    break;

                default:
                    return ['sucesso' => false, 'erro' => "Monitoramento não suportado para o tipo '{$tipo}'"];
            }

            return [
                'sucesso' => true,
                'tem_acesso' => $temAcesso,
                'mensagem' => $mensagem,
                'tipo_banco' => $tipo,
                'usuario_banco' => $conexao['usuario']
            ];
        } catch (Exception $e) {
            return ['sucesso' => false, 'erro' => 'Erro ao verificar acesso: ' . $e->getMessage()];
        }
    }

    /**
     * Obtém sessões/transações ativas no banco de dados
     */
    public function obterSessoes(int $conexaoId): array
    {
        try {
            if (!ServicoPermissao::podeVerRecurso('conexao', $conexaoId)) {
                return ['sucesso' => false, 'erro' => 'Sem permissão para acessar esta conexão'];
            }

            $db = Database::getConexao();
            $stmt = $db->prepare('SELECT * FROM tb_perfis_conexao WHERE id = ?');
            $stmt->execute([$conexaoId]);
            $conexao = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$conexao) {
                return ['sucesso' => false, 'erro' => 'Conexão não encontrada'];
            }

            $targetDb = $this->criarConexao($conexao);
            $tipo = $conexao['tipo_banco'];

            // Filtros opcionais
            $filtroUsuario = $_GET['usuario'] ?? '';
            $filtroEstado = $_GET['estado'] ?? '';
            $filtroBanco = $_GET['banco'] ?? '';

            $versaoBanco = '';
            if ($tipo === 'postgres') {
                $versaoBanco = $targetDb->query("SHOW server_version")->fetchColumn();
            }

            $sessoes = match ($tipo) {
                'postgres'   => $this->obterSessoesPostgres($targetDb, $filtroUsuario, $filtroEstado, $filtroBanco),
                'mysql'      => $this->obterSessoesMysql($targetDb, $filtroUsuario, $filtroEstado, $filtroBanco),
                'sqlserver'  => $this->obterSessoesSqlServer($targetDb, $filtroUsuario, $filtroEstado, $filtroBanco),
                'oracle'     => $this->obterSessoesOracle($targetDb, $filtroUsuario, $filtroEstado, $filtroBanco),
                default      => throw new Exception("Monitoramento não suportado para '{$tipo}'")
            };

            // Cache server-side de queries para PG < 9.2
            // No PG 8.4, current_query mostra <IDLE> e perde a query real.
            // O cache armazena queries capturadas enquanto ativas e reutiliza quando idle.
            if ($tipo === 'postgres' && $versaoBanco && version_compare($versaoBanco, '9.2', '<')) {
                $sessoes = $this->aplicarCacheQueriesPg($conexaoId, $sessoes, $targetDb);
            }

            return [
                'sucesso' => true,
                'sessoes' => $sessoes,
                'tipo_banco' => $tipo,
                'versao_banco' => $versaoBanco,
                'usuario_conexao' => $conexao['usuario'],
                'timestamp' => date('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            return ['sucesso' => false, 'erro' => $e->getMessage()];
        }
    }

    /**
     * Obtém estatísticas gerais do banco de dados
     */
    public function obterEstatisticas(int $conexaoId): array
    {
        try {
            if (!ServicoPermissao::podeVerRecurso('conexao', $conexaoId)) {
                return ['sucesso' => false, 'erro' => 'Sem permissão'];
            }

            $db = Database::getConexao();
            $stmt = $db->prepare('SELECT * FROM tb_perfis_conexao WHERE id = ?');
            $stmt->execute([$conexaoId]);
            $conexao = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$conexao) {
                return ['sucesso' => false, 'erro' => 'Conexão não encontrada'];
            }

            $targetDb = $this->criarConexao($conexao);
            $tipo = $conexao['tipo_banco'];

            $stats = match ($tipo) {
                'postgres'   => $this->obterEstatisticasPostgres($targetDb),
                'mysql'      => $this->obterEstatisticasMysql($targetDb),
                'sqlserver'  => $this->obterEstatisticasSqlServer($targetDb),
                'oracle'     => $this->obterEstatisticasOracle($targetDb),
                default      => ['erro' => 'Tipo não suportado']
            };

            return [
                'sucesso' => true,
                'estatisticas' => $stats,
                'tipo_banco' => $tipo,
                'timestamp' => date('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            return ['sucesso' => false, 'erro' => $e->getMessage()];
        }
    }

    // ================================================================
    // Verificação de acesso por tipo de banco
    // ================================================================

    private function verificarAcessoPostgres(PDO $db): bool
    {
        try {
            $versao = (int) $db->query("SHOW server_version_num")->fetchColumn();
            // PG < 9.2 usa procpid em vez de pid, current_query em vez de query, e não tem coluna state
            if ($versao < 90200) {
                $db->query("SELECT procpid, usename, current_query FROM pg_stat_activity LIMIT 1");
            } else {
                $db->query("SELECT pid, usename, state FROM pg_stat_activity LIMIT 1");
            }
            return true;
        } catch (Exception) {
            return false;
        }
    }

    private function verificarAcessoMysql(PDO $db): bool
    {
        try {
            $db->query("SHOW PROCESSLIST");
            return true;
        } catch (Exception) {
            return false;
        }
    }

    private function verificarAcessoSqlServer(PDO $db): bool
    {
        try {
            $db->query("SELECT TOP 1 session_id FROM sys.dm_exec_sessions");
            return true;
        } catch (Exception) {
            return false;
        }
    }

    private function verificarAcessoOracle(PDO $db): bool
    {
        try {
            $db->query("SELECT SID FROM V\$SESSION WHERE ROWNUM = 1");
            return true;
        } catch (Exception) {
            return false;
        }
    }

    // ================================================================
    // Cache server-side de queries para PostgreSQL < 9.2
    // ================================================================

    /**
     * No PG 8.4, current_query mostra "<IDLE>" para sessões ociosas.
     * Este cache armazena a última query vista enquanto a sessão estava ativa,
     * e a reutiliza quando a sessão fica ociosa.
     * Usa backend_start para detectar reuso de PID (novo processo = invalida cache).
     */
    private function aplicarCacheQueriesPg(int $conexaoId, array $sessoes, PDO $targetDb = null): array
    {
        $cacheDir = __DIR__ . '/../../storage/cache/monitor';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }
        $cacheFile = $cacheDir . '/pg_queries_' . $conexaoId . '.json';

        // Carregar cache existente
        $cache = [];
        if (file_exists($cacheFile)) {
            $decoded = @json_decode(@file_get_contents($cacheFile), true);
            if (is_array($decoded)) {
                $cache = $decoded;
            }
        }

        // Coletar PIDs sem query para tentar pg_stat_statements
        $pidsSemQuery = [];

        $pidsVivos = [];
        foreach ($sessoes as &$s) {
            $pid = (string) $s['id_sessao'];
            $pidsVivos[] = $pid;
            $backendStart = $s['inicio_sessao'] ?? '';

            if (!empty($s['query_atual']) && $s['query_atual'] !== '(sem privilégio para ver query)') {
                // Sessão com query visível: atualizar cache
                $cache[$pid] = [
                    'query' => $s['query_atual'],
                    'backend_start' => $backendStart,
                    'query_start' => $s['inicio_query'] ?? '',
                    'ts' => time()
                ];
            } elseif (isset($cache[$pid]) && empty($s['query_atual'])) {
                // Sessão sem query: verificar se mesmo processo (backend_start igual)
                $cached = $cache[$pid];
                if ($cached['backend_start'] === $backendStart || empty($backendStart)) {
                    // Verificar se query_start mudou — indica query nova que escapou do polling
                    $queryStartAtual = $s['inicio_query'] ?? '';
                    $queryStartCache = $cached['query_start'] ?? '';
                    if ($queryStartAtual && $queryStartCache && $queryStartAtual !== $queryStartCache) {
                        // query_start mudou: houve uma query mas não capturamos o texto
                        $s['query_atual'] = $cached['query'];
                        $s['inicio_query'] = $queryStartAtual;
                        $s['_query_start_mudou'] = true;
                        // Atualizar cache com novo query_start (mantém query antiga)
                        $cache[$pid]['query_start'] = $queryStartAtual;
                        $cache[$pid]['ts'] = time();
                    } else {
                        $s['query_atual'] = $cached['query'];
                        if (empty($s['inicio_query']) && !empty($cached['query_start'])) {
                            $s['inicio_query'] = $cached['query_start'];
                        }
                    }
                } else {
                    unset($cache[$pid]);
                    $pidsSemQuery[$pid] = $s['usuario'] ?? '';
                }
            } elseif (empty($s['query_atual'])) {
                $pidsSemQuery[$pid] = $s['usuario'] ?? '';
            }
        }
        unset($s);

        // Fallback: tentar pg_stat_statements para sessões idle sem cache
        if (!empty($pidsSemQuery) && $targetDb !== null) {
            $this->tentarPgStatStatements($targetDb, $sessoes, $pidsSemQuery, $cache);
        }

        // Remover PIDs que não existem mais no servidor
        $cache = array_intersect_key($cache, array_flip($pidsVivos));

        // Salvar cache atualizado
        @file_put_contents($cacheFile, json_encode($cache, JSON_UNESCAPED_UNICODE), LOCK_EX);

        return $sessoes;
    }

    /**
     * Tenta usar pg_stat_statements para encontrar a última query executada
     * por um usuário. Funciona como fallback para sessões idle sem cache.
     */
    private function tentarPgStatStatements(PDO $db, array &$sessoes, array $pidsSemQuery, array &$cache): void
    {
        try {
            // Verificar se pg_stat_statements existe
            $check = $db->query("SELECT 1 FROM pg_catalog.pg_tables WHERE tablename = 'pg_stat_statements' AND schemaname = 'public'
                                 UNION ALL
                                 SELECT 1 FROM pg_catalog.pg_views WHERE viewname = 'pg_stat_statements' AND schemaname = 'pg_catalog'");
            if ($check->fetchColumn() === false) {
                return; // pg_stat_statements não disponível
            }

            // Buscar queries por usuário (os que têm sessões idle sem query)
            $usuarios = array_unique(array_values($pidsSemQuery));
            if (empty($usuarios)) return;

            // Buscar as queries mais recentes/frequentes por usuário
            $placeholders = implode(',', array_fill(0, count($usuarios), '?'));
            $stmt = $db->prepare(
                "SELECT u.usename, s.query, s.calls, s.total_time
                 FROM pg_stat_statements s
                 JOIN pg_user u ON u.usesysid = s.userid
                 WHERE u.usename IN ({$placeholders})
                 AND s.query NOT LIKE 'SET %'
                 AND s.query NOT LIKE 'SHOW %'
                 AND s.query NOT LIKE 'SELECT%pg_stat%'
                 ORDER BY s.total_time DESC
                 LIMIT 50"
            );
            $stmt->execute($usuarios);
            $statsRows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Criar mapa de melhor query por usuário (mais tempo gasto = mais relevante)
            $melhorQueryPorUsuario = [];
            foreach ($statsRows as $sr) {
                $user = $sr['usename'];
                if (!isset($melhorQueryPorUsuario[$user])) {
                    $melhorQueryPorUsuario[$user] = $sr['query'];
                }
            }

            // Aplicar às sessões que estão sem query
            foreach ($sessoes as &$s) {
                $pid = (string) $s['id_sessao'];
                if (isset($pidsSemQuery[$pid]) && empty($s['query_atual'])) {
                    $usuario = $s['usuario'] ?? '';
                    if (isset($melhorQueryPorUsuario[$usuario])) {
                        $s['query_atual'] = '≈ ' . $melhorQueryPorUsuario[$usuario];
                        // Atualizar cache também
                        $cache[$pid] = [
                            'query' => $s['query_atual'],
                            'backend_start' => $s['inicio_sessao'] ?? '',
                            'query_start' => $s['inicio_query'] ?? '',
                            'ts' => time(),
                            'fonte' => 'pg_stat_statements'
                        ];
                    }
                }
            }
            unset($s);

        } catch (\Exception $e) {
            // pg_stat_statements não disponível ou sem permissão — ignorar silenciosamente
        }
    }

    // ================================================================
    // Obter sessões ativas por tipo de banco
    // ================================================================

    private function obterSessoesPostgres(PDO $db, string $filtroUsuario, string $filtroEstado, string $filtroBanco): array
    {
        // Detectar versão do PostgreSQL para compatibilidade
        $versao = (int) $db->query("SHOW server_version_num")->fetchColumn();

        // PG < 9.2: usa procpid, current_query, sem state/application_name/state_change
        if ($versao < 90200) {
            return $this->obterSessoesPostgresLegacy($db, $filtroUsuario, $filtroEstado, $filtroBanco);
        }

        $where = ["pid IS NOT NULL", "application_name != 'DMC-Monitor'"];
        $params = [];

        if ($filtroUsuario !== '') {
            $where[] = "usename = ?";
            $params[] = $filtroUsuario;
        }
        if ($filtroEstado !== '') {
            $where[] = "state = ?";
            $params[] = $filtroEstado;
        }
        if ($filtroBanco !== '') {
            $where[] = "datname = ?";
            $params[] = $filtroBanco;
        }

        $whereClause = implode(' AND ', $where);

        // wait_event_type/wait_event: PG 9.6+, backend_type: PG 10+
        $colWaitType   = $versao >= 90600 ? "wait_event_type" : "NULL";
        $colWaitEvent  = $versao >= 90600 ? "wait_event" : "NULL";
        $colBackend    = $versao >= 100000 ? "backend_type" : "'client'";

        $sql = "SELECT 
                    pid AS id_sessao,
                    usename AS usuario,
                    datname AS banco,
                    client_addr AS ip_cliente,
                    application_name AS aplicacao,
                    state AS estado,
                    COALESCE(query, '') AS query_atual,
                    backend_start AS inicio_sessao,
                    query_start AS inicio_query,
                    state_change AS ultima_mudanca,
                    {$colWaitType} AS tipo_espera,
                    {$colWaitEvent} AS evento_espera,
                    CASE WHEN state = 'active' AND query_start IS NOT NULL
                         THEN EXTRACT(EPOCH FROM (NOW() - query_start))::integer
                         ELSE 0
                    END AS duracao_segundos,
                    {$colBackend} AS tipo_backend
                FROM pg_stat_activity
                WHERE {$whereClause}
                ORDER BY 
                    CASE state WHEN 'active' THEN 0 WHEN 'idle in transaction' THEN 1 ELSE 2 END,
                    duracao_segundos DESC NULLS LAST
                LIMIT 200";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * PG < 9.2 (ex: 8.4): procpid, current_query, sem state/application_name
     * O estado é derivado de current_query (<IDLE>, <IDLE> in transaction, etc.)
     */
    private function obterSessoesPostgresLegacy(PDO $db, string $filtroUsuario, string $filtroEstado, string $filtroBanco): array
    {
        $where = ["procpid IS NOT NULL", "procpid != pg_backend_pid()"];
        $params = [];
        // PG 8.x não tem application_name, mantém filtro por pg_backend_pid()

        if ($filtroUsuario !== '') {
            $where[] = "usename = ?";
            $params[] = $filtroUsuario;
        }
        if ($filtroBanco !== '') {
            $where[] = "datname = ?";
            $params[] = $filtroBanco;
        }
        // Para filtro de estado no PG 8.x, filtramos em PHP após a consulta

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT 
                    procpid AS id_sessao,
                    usename AS usuario,
                    datname AS banco,
                    client_addr AS ip_cliente,
                    '' AS aplicacao,
                    current_query AS query_bruta,
                    backend_start AS inicio_sessao,
                    query_start AS inicio_query,
                    CASE WHEN waiting THEN 'Lock' ELSE NULL END AS tipo_espera,
                    NULL AS evento_espera,
                    CASE WHEN current_query NOT LIKE '<%%>'
                         THEN EXTRACT(EPOCH FROM (NOW() - query_start))::integer
                         ELSE 0
                    END AS duracao_segundos,
                    'client' AS tipo_backend
                FROM pg_stat_activity
                WHERE {$whereClause}
                ORDER BY duracao_segundos DESC NULLS LAST
                LIMIT 200";

        $stmt = $db->prepare($sql);

        // Micro-polling: consultar pg_stat_activity múltiplas vezes rapidamente
        // para capturar queries que ficam ativas por milissegundos (ex: DBeaver).
        // 10 iterações x 20ms = ~200ms de janela de captura por chamada API.
        $queriesPorPid = []; // PID => query_bruta (acumula queries reais vistas)
        $queryStartPorPid = []; // PID => query_start
        $ultimoSnapshot = null;
        $iteracoes = 10;
        $intervaloUs = 20000; // 20ms entre leituras

        for ($i = 0; $i < $iteracoes; $i++) {
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $ultimoSnapshot = $rows;

            foreach ($rows as $row) {
                $pid = $row['id_sessao'];
                $qb = $row['query_bruta'] ?? '';
                // Se viu query real (não é <IDLE>, <IDLE> in transaction, <insufficient privilege>)
                if ($qb !== '' && $qb !== '<IDLE>' && stripos($qb, '<IDLE>') !== 0 && $qb !== '<insufficient privilege>') {
                    $queriesPorPid[$pid] = $qb;
                    $queryStartPorPid[$pid] = $row['inicio_query'] ?? '';
                }
            }

            if ($i < $iteracoes - 1) {
                usleep($intervaloUs);
            }
        }

        // Derivar estado e query a partir de current_query (usando último snapshot)
        $result = [];
        foreach ($ultimoSnapshot as $row) {
            $pid = $row['id_sessao'];
            $queryBruta = $row['query_bruta'] ?? '';
            unset($row['query_bruta']);

            if ($queryBruta === '<IDLE>') {
                $row['estado'] = 'idle';
                // Tentar usar query capturada pelo micro-polling
                $row['query_atual'] = $queriesPorPid[$pid] ?? '';
                if (isset($queryStartPorPid[$pid]) && !empty($queryStartPorPid[$pid])) {
                    $row['inicio_query'] = $queryStartPorPid[$pid];
                }
            } elseif (stripos($queryBruta, '<IDLE> in transaction') !== false) {
                $row['estado'] = 'idle in transaction';
                $row['query_atual'] = $queriesPorPid[$pid] ?? '';
                if (isset($queryStartPorPid[$pid]) && !empty($queryStartPorPid[$pid])) {
                    $row['inicio_query'] = $queryStartPorPid[$pid];
                }
            } elseif ($queryBruta === '<insufficient privilege>') {
                $row['estado'] = 'active';
                $row['query_atual'] = '(sem privilégio para ver query)';
            } else {
                $row['estado'] = 'active';
                $row['query_atual'] = $queryBruta;
            }

            // Filtro de estado (feito em PHP pois PG 8.x não tem coluna state)
            if ($filtroEstado !== '' && $row['estado'] !== $filtroEstado) {
                continue;
            }

            $result[] = $row;
        }

        return $result;
    }

    private function obterSessoesMysql(PDO $db, string $filtroUsuario, string $filtroEstado, string $filtroBanco): array
    {
        $where = ["1=1", "ID != CONNECTION_ID()"];
        $params = [];

        if ($filtroUsuario !== '') {
            $where[] = "USER = ?";
            $params[] = $filtroUsuario;
        }
        if ($filtroEstado !== '') {
            $where[] = "COMMAND = ?";
            $params[] = $filtroEstado;
        }
        if ($filtroBanco !== '') {
            $where[] = "DB = ?";
            $params[] = $filtroBanco;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT 
                    ID AS id_sessao,
                    USER AS usuario,
                    DB AS banco,
                    HOST AS ip_cliente,
                    '' AS aplicacao,
                    COMMAND AS estado,
                    COALESCE(INFO, '') AS query_atual,
                    NULL AS inicio_sessao,
                    NULL AS inicio_query,
                    NULL AS ultima_mudanca,
                    STATE AS tipo_espera,
                    '' AS evento_espera,
                    TIME AS duracao_segundos,
                    'client' AS tipo_backend
                FROM information_schema.PROCESSLIST
                WHERE {$whereClause}
                ORDER BY TIME DESC
                LIMIT 200";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function obterSessoesSqlServer(PDO $db, string $filtroUsuario, string $filtroEstado, string $filtroBanco): array
    {
        $where = ["s.session_id > 50", "s.session_id != @@SPID"]; // Ignorar sessões de sistema e a própria
        $params = [];

        if ($filtroUsuario !== '') {
            $where[] = "s.login_name = ?";
            $params[] = $filtroUsuario;
        }
        if ($filtroEstado !== '') {
            $where[] = "r.status = ?";
            $params[] = $filtroEstado;
        }
        if ($filtroBanco !== '') {
            $where[] = "DB_NAME(r.database_id) = ?";
            $params[] = $filtroBanco;
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT 
                    s.session_id AS id_sessao,
                    s.login_name AS usuario,
                    DB_NAME(COALESCE(r.database_id, s.database_id)) AS banco,
                    s.host_name AS ip_cliente,
                    s.program_name AS aplicacao,
                    COALESCE(r.status, s.status) AS estado,
                    COALESCE(t.text, '') AS query_atual,
                    s.login_time AS inicio_sessao,
                    r.start_time AS inicio_query,
                    s.last_request_start_time AS ultima_mudanca,
                    r.wait_type AS tipo_espera,
                    r.last_wait_type AS evento_espera,
                    COALESCE(DATEDIFF(SECOND, r.start_time, GETDATE()), 0) AS duracao_segundos,
                    'client' AS tipo_backend
                FROM sys.dm_exec_sessions s
                LEFT JOIN sys.dm_exec_requests r ON s.session_id = r.session_id
                OUTER APPLY sys.dm_exec_sql_text(r.sql_handle) t
                WHERE {$whereClause}
                ORDER BY duracao_segundos DESC
                OFFSET 0 ROWS FETCH NEXT 200 ROWS ONLY";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function obterSessoesOracle(PDO $db, string $filtroUsuario, string $filtroEstado, string $filtroBanco): array
    {
        // Verificar acesso às V$ views primeiro
        try {
            $db->query("SELECT 1 FROM V\$SESSION WHERE ROWNUM = 1");
        } catch (\Exception $e) {
            throw new Exception("Sem permissão para V\$SESSION. O DBA precisa executar: GRANT SELECT ON SYS.V_\$SESSION TO " . ($db->query("SELECT USER FROM DUAL")->fetchColumn() ?: 'usuario') . "; GRANT SELECT ON SYS.V_\$SQLAREA TO " . ($db->query("SELECT USER FROM DUAL")->fetchColumn() ?: 'usuario') . ";");
        }

        $where = ["s.TYPE = 'USER'", "s.SID != SYS_CONTEXT('USERENV', 'SID')"];
        $params = [];

        if ($filtroUsuario !== '') {
            $where[] = "s.USERNAME = ?";
            $params[] = strtoupper($filtroUsuario);
        }
        if ($filtroEstado !== '') {
            $where[] = "s.STATUS = ?";
            $params[] = strtoupper($filtroEstado);
        }
        if ($filtroBanco !== '') {
            $where[] = "s.SCHEMANAME = ?";
            $params[] = strtoupper($filtroBanco);
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT 
                    s.SID AS id_sessao,
                    s.USERNAME AS usuario,
                    s.SCHEMANAME AS banco,
                    s.MACHINE AS ip_cliente,
                    s.PROGRAM AS aplicacao,
                    s.STATUS AS estado,
                    COALESCE(a.SQL_TEXT, '') AS query_atual,
                    s.LOGON_TIME AS inicio_sessao,
                    NULL AS inicio_query,
                    NULL AS ultima_mudanca,
                    s.WAIT_CLASS AS tipo_espera,
                    s.EVENT AS evento_espera,
                    s.LAST_CALL_ET AS duracao_segundos,
                    'client' AS tipo_backend
                FROM V\$SESSION s
                LEFT JOIN V\$SQLAREA a ON s.SQL_ADDRESS = a.ADDRESS AND s.SQL_HASH_VALUE = a.HASH_VALUE
                WHERE {$whereClause}
                ORDER BY s.LAST_CALL_ET DESC NULLS LAST
                FETCH FIRST 200 ROWS ONLY";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================================================================
    // Estatísticas gerais por tipo de banco
    // ================================================================

    private function obterEstatisticasPostgres(PDO $db): array
    {
        $versao = (int) $db->query("SHOW server_version_num")->fetchColumn();
        $stats = [];

        // PG < 9.2: sem coluna state, sem FILTER
        if ($versao < 90200) {
            return $this->obterEstatisticasPostgresLegacy($db);
        }

        // Sessões por estado
        $result = $db->query("
            SELECT state, COUNT(*) AS total
            FROM pg_stat_activity
            WHERE pid IS NOT NULL
            GROUP BY state
            ORDER BY total DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
        $stats['sessoes_por_estado'] = $result;

        // Totais — adaptar para versão do PG
        $colEsperando = $versao >= 90600
            ? "COUNT(*) FILTER (WHERE wait_event_type IS NOT NULL AND state = 'active')"
            : "COUNT(*) FILTER (WHERE waiting = true AND state = 'active')";

        $totais = $db->query("
            SELECT 
                COUNT(*) AS total_sessoes,
                COUNT(*) FILTER (WHERE state = 'active') AS ativas,
                COUNT(*) FILTER (WHERE state = 'idle') AS ociosas,
                COUNT(*) FILTER (WHERE state = 'idle in transaction') AS idle_transaction,
                {$colEsperando} AS esperando,
                MAX(EXTRACT(EPOCH FROM (NOW() - query_start))::integer) FILTER (WHERE state = 'active') AS query_mais_longa_seg
            FROM pg_stat_activity
            WHERE pid IS NOT NULL
        ")->fetch(PDO::FETCH_ASSOC);
        $stats['totais'] = $totais;

        // Locks bloqueantes
        $stats['locks_bloqueantes'] = $db->query("
            SELECT COUNT(*) AS total
            FROM pg_locks
            WHERE NOT granted
        ")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Tamanho do banco
        $stats['tamanho_banco'] = $db->query("
            SELECT pg_size_pretty(pg_database_size(current_database())) AS tamanho
        ")->fetch(PDO::FETCH_ASSOC)['tamanho'] ?? 'N/A';

        return $stats;
    }

    /**
     * Estatísticas para PG < 9.2 (sem coluna state, sem FILTER)
     */
    private function obterEstatisticasPostgresLegacy(PDO $db): array
    {
        $stats = [];

        // Buscar todas as sessões e computar estados em PHP
        $rows = $db->query("
            SELECT current_query, waiting,
                   CASE WHEN current_query NOT LIKE '<%%>'
                        THEN EXTRACT(EPOCH FROM (NOW() - query_start))::integer
                        ELSE 0
                   END AS duracao_seg
            FROM pg_stat_activity
            WHERE procpid IS NOT NULL
        ")->fetchAll(PDO::FETCH_ASSOC);

        $total = count($rows);
        $ativas = 0;
        $ociosas = 0;
        $idleTx = 0;
        $esperando = 0;
        $maxDuracao = 0;

        foreach ($rows as $r) {
            $q = $r['current_query'] ?? '';
            $dur = (int)($r['duracao_seg'] ?? 0);
            if ($q === '<IDLE>') {
                $ociosas++;
            } elseif (stripos($q, '<IDLE> in transaction') !== false) {
                $idleTx++;
            } else {
                $ativas++;
                if ($dur > $maxDuracao) $maxDuracao = $dur;
            }
            if ($r['waiting']) $esperando++;
        }

        $stats['sessoes_por_estado'] = [
            ['state' => 'active', 'total' => $ativas],
            ['state' => 'idle', 'total' => $ociosas],
            ['state' => 'idle in transaction', 'total' => $idleTx],
        ];
        $stats['totais'] = [
            'total_sessoes' => $total,
            'ativas' => $ativas,
            'ociosas' => $ociosas,
            'idle_transaction' => $idleTx,
            'esperando' => $esperando,
            'query_mais_longa_seg' => $maxDuracao,
        ];

        // Locks
        $stats['locks_bloqueantes'] = $db->query("
            SELECT COUNT(*) AS total FROM pg_locks WHERE NOT granted
        ")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Tamanho do banco
        $stats['tamanho_banco'] = $db->query("
            SELECT pg_size_pretty(pg_database_size(current_database())) AS tamanho
        ")->fetch(PDO::FETCH_ASSOC)['tamanho'] ?? 'N/A';

        return $stats;
    }

    private function obterEstatisticasMysql(PDO $db): array
    {
        $stats = [];

        $result = $db->query("
            SELECT COMMAND AS state, COUNT(*) AS total
            FROM information_schema.PROCESSLIST
            GROUP BY COMMAND
            ORDER BY total DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
        $stats['sessoes_por_estado'] = $result;

        $totais = $db->query("
            SELECT 
                COUNT(*) AS total_sessoes,
                SUM(CASE WHEN COMMAND != 'Sleep' THEN 1 ELSE 0 END) AS ativas,
                SUM(CASE WHEN COMMAND = 'Sleep' THEN 1 ELSE 0 END) AS ociosas,
                0 AS idle_transaction,
                0 AS esperando,
                MAX(TIME) AS query_mais_longa_seg
            FROM information_schema.PROCESSLIST
        ")->fetch(PDO::FETCH_ASSOC);
        $stats['totais'] = $totais;

        $stats['locks_bloqueantes'] = 0;
        $stats['tamanho_banco'] = $db->query("
            SELECT CONCAT(ROUND(SUM(data_length + index_length) / 1024 / 1024, 2), ' MB') AS tamanho
            FROM information_schema.TABLES
            WHERE table_schema = DATABASE()
        ")->fetch(PDO::FETCH_ASSOC)['tamanho'] ?? 'N/A';

        return $stats;
    }

    private function obterEstatisticasSqlServer(PDO $db): array
    {
        $stats = [];

        $result = $db->query("
            SELECT COALESCE(r.status, s.status) AS state, COUNT(*) AS total
            FROM sys.dm_exec_sessions s
            LEFT JOIN sys.dm_exec_requests r ON s.session_id = r.session_id
            WHERE s.session_id > 50
            GROUP BY COALESCE(r.status, s.status)
            ORDER BY total DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
        $stats['sessoes_por_estado'] = $result;

        $totais = $db->query("
            SELECT 
                COUNT(*) AS total_sessoes,
                SUM(CASE WHEN r.session_id IS NOT NULL THEN 1 ELSE 0 END) AS ativas,
                SUM(CASE WHEN r.session_id IS NULL THEN 1 ELSE 0 END) AS ociosas,
                0 AS idle_transaction,
                SUM(CASE WHEN r.wait_type IS NOT NULL THEN 1 ELSE 0 END) AS esperando,
                MAX(DATEDIFF(SECOND, r.start_time, GETDATE())) AS query_mais_longa_seg
            FROM sys.dm_exec_sessions s
            LEFT JOIN sys.dm_exec_requests r ON s.session_id = r.session_id
            WHERE s.session_id > 50
        ")->fetch(PDO::FETCH_ASSOC);
        $stats['totais'] = $totais;

        $stats['locks_bloqueantes'] = $db->query("
            SELECT COUNT(*) AS total FROM sys.dm_exec_requests WHERE blocking_session_id > 0
        ")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $stats['tamanho_banco'] = 'N/A';

        return $stats;
    }

    private function obterEstatisticasOracle(PDO $db): array
    {
        $stats = [];

        $result = $db->query("
            SELECT STATUS AS state, COUNT(*) AS total
            FROM V\$SESSION
            WHERE TYPE = 'USER'
            GROUP BY STATUS
            ORDER BY total DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
        $stats['sessoes_por_estado'] = $result;

        $totais = $db->query("
            SELECT 
                COUNT(*) AS total_sessoes,
                SUM(CASE WHEN STATUS = 'ACTIVE' THEN 1 ELSE 0 END) AS ativas,
                SUM(CASE WHEN STATUS = 'INACTIVE' THEN 1 ELSE 0 END) AS ociosas,
                0 AS idle_transaction,
                SUM(CASE WHEN WAIT_CLASS != 'Idle' THEN 1 ELSE 0 END) AS esperando,
                MAX(LAST_CALL_ET) AS query_mais_longa_seg
            FROM V\$SESSION
            WHERE TYPE = 'USER'
        ")->fetch(PDO::FETCH_ASSOC);
        $stats['totais'] = $totais;

        $stats['locks_bloqueantes'] = $db->query("
            SELECT COUNT(*) AS total FROM V\$LOCK WHERE BLOCK > 0
        ")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        $stats['tamanho_banco'] = 'N/A';

        return $stats;
    }

    // ================================================================
    // Poller background contínuo para PG < 9.2
    // ================================================================

    /**
     * Burst curto de captura: consulta pg_stat_activity ~100 vezes em ~1.5s.
     * Projetado para não bloquear o servidor PHP single-threaded por muito tempo.
     * O frontend chama este endpoint SEQUENCIALMENTE entre os ciclos de atualização.
     */
    public function capturaLegadoContinua(int $conexaoId): void
    {
        header('Content-Type: application/json');

        if (!ServicoPermissao::podeVerRecurso('conexao', $conexaoId)) {
            echo json_encode(['sucesso' => false, 'erro' => 'Sem permissão']);
            return;
        }

        $db = \App\Core\Database::getConexao();
        $stmt = $db->prepare('SELECT * FROM tb_perfis_conexao WHERE id = ?');
        $stmt->execute([$conexaoId]);
        $config = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$config || $config['tipo_banco'] !== 'postgres') {
            echo json_encode(['sucesso' => false, 'erro' => 'Conexão inválida']);
            return;
        }

        try {
            $targetDb = $this->criarConexao($config);
            $versao = $targetDb->query("SHOW server_version")->fetchColumn();
            if (version_compare($versao, '9.2', '>=')) {
                echo json_encode(['sucesso' => false, 'erro' => 'Não necessário para PG >= 9.2']);
                return;
            }
        } catch (Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
            return;
        }

        $cacheDir = __DIR__ . '/../../storage/cache/monitor';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }
        $cacheFile = $cacheDir . '/pg_queries_' . $conexaoId . '.json';

        // Carregar cache existente
        $cache = [];
        if (file_exists($cacheFile)) {
            $decoded = @json_decode(@file_get_contents($cacheFile), true);
            if (is_array($decoded)) {
                $cache = $decoded;
            }
        }

        $pollStmt = $targetDb->prepare(
            "SELECT procpid, usename, current_query, backend_start, query_start
             FROM pg_stat_activity
             WHERE procpid != pg_backend_pid()
             AND current_query NOT LIKE '<%'"
        );

        // Burst: ~50 polls em ~1.5 segundo (15ms entre cada)
        $iteracoes = 50;
        $intervaloUs = 15000; // 15ms
        $capturas = 0;

        for ($i = 0; $i < $iteracoes; $i++) {
            try {
                $pollStmt->execute();
                $rows = $pollStmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rows as $row) {
                    $pid = (string) $row['procpid'];
                    $query = $row['current_query'];

                    if (strpos($query, 'pg_stat_activity') !== false) {
                        continue;
                    }

                    $jaExiste = isset($cache[$pid]) && $cache[$pid]['query'] === $query
                        && $cache[$pid]['query_start'] === ($row['query_start'] ?? '');

                    if (!$jaExiste) {
                        $cache[$pid] = [
                            'query' => $query,
                            'backend_start' => $row['backend_start'] ?? '',
                            'query_start' => $row['query_start'] ?? '',
                            'ts' => time()
                        ];
                        $capturas++;
                    }
                }
            } catch (Exception $e) {
                break;
            }

            if ($i < $iteracoes - 1) {
                usleep($intervaloUs);
            }
        }

        // Salvar cache
        @file_put_contents($cacheFile, json_encode($cache, JSON_UNESCAPED_UNICODE), LOCK_EX);

        echo json_encode(['sucesso' => true, 'capturas' => $capturas, 'ciclos' => $iteracoes]);
    }

    // ================================================================
    // Conexão ao banco alvo (reutiliza padrão do SqlEditorController)
    // ================================================================

    private function criarConexao(array $config): PDO
    {
        $tipo = $config['tipo_banco'];
        $host = $config['host'];
        $porta = $config['porta'];
        $database = $config['nome_banco'];
        $usuario = $config['usuario'];
        $senhaEncriptada = $config['senha_encriptada'] ?? '';
        $parametrosExtras = [];

        if (!empty($config['parametros_extras'])) {
            $parametrosExtras = json_decode($config['parametros_extras'], true) ?? [];
        }

        $senha = '';
        if ($senhaEncriptada) {
            $key = $_ENV['ENCRYPTION_KEY'] ?? $_SERVER['ENCRYPTION_KEY'] ?? getenv('ENCRYPTION_KEY') ?: '';
            if (!$key) {
                throw new Exception('ENCRYPTION_KEY não configurada no sistema');
            }
            $senha = Crypto::decrypt($senhaEncriptada, $key);
        }

        $dsn = match ($tipo) {
            'postgres'   => "pgsql:host={$host};port={$porta};dbname={$database}",
            'mysql'      => "mysql:host={$host};port={$porta};dbname={$database};charset=utf8mb4",
            'sqlserver'  => "sqlsrv:Server={$host},{$porta};Database={$database}",
            'oracle'     => $this->buildOracleDsn($host, $porta, $database, $parametrosExtras),
            'sqlite'     => "sqlite:" . ($parametrosExtras['sqlite_path'] ?? $database ?? ''),
            default      => throw new Exception("Tipo de banco não suportado: {$tipo}")
        };

        if ($tipo === 'sqlite') {
            $usuario = null;
            $senha = null;
        }

        $pdo = new PDO($dsn, $usuario, $senha, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);

        // Identificar conexão como monitor para auto-exclusão nas queries de sessão
        switch ($tipo) {
            case 'postgres':
                try { $pdo->exec("SET application_name = 'DMC-Monitor'"); } catch (Exception $e) {}
                break;
            case 'mysql':
                // MySQL não tem application_name nativo, filtramos por CONNECTION_ID()
                break;
            case 'sqlserver':
                try { $pdo->exec("SET CONTEXT_INFO 0x444D434D6F6E"); } catch (Exception $e) {}
                break;
        }

        return $pdo;
    }

    private function buildOracleDsn(string $host, ?int $porta, ?string $database, array $extras): string
    {
        $tipoConexao = $extras['tipo_conexao_oracle'] ?? 'sid';
        $serviceName = $extras['service_name'] ?? '';
        $sid = $extras['sid'] ?? '';

        if ($tipoConexao === 'service_name' && $serviceName !== '') {
            return "oci:dbname=//{$host}:{$porta}/{$serviceName};charset=UTF8";
        }
        if ($sid !== '') {
            return "oci:dbname=//{$host}:{$porta}/{$sid};charset=UTF8";
        }
        if ($database) {
            return "oci:dbname=//{$host}:{$porta}/{$database};charset=UTF8";
        }
        throw new Exception("Conexão Oracle requer SID ou Service Name configurado");
    }

    // ================================================================
    // Métricas de Performance (CPU, Memória, I/O) - estilo Grafana
    // ================================================================

    public function obterMetricas(int $conexaoId): array
    {
        try {
            if (!ServicoPermissao::podeVerRecurso('conexao', $conexaoId)) {
                return ['sucesso' => false, 'erro' => 'Sem permissão'];
            }

            $db = Database::getConexao();
            $stmt = $db->prepare('SELECT * FROM tb_perfis_conexao WHERE id = ?');
            $stmt->execute([$conexaoId]);
            $conexao = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$conexao) {
                return ['sucesso' => false, 'erro' => 'Conexão não encontrada'];
            }

            $targetDb = $this->criarConexao($conexao);
            $tipo = $conexao['tipo_banco'];

            $metricas = match ($tipo) {
                'postgres'   => $this->obterMetricasPostgres($targetDb),
                'mysql'      => $this->obterMetricasMysql($targetDb),
                'sqlserver'  => $this->obterMetricasSqlServer($targetDb),
                'oracle'     => $this->obterMetricasOracle($targetDb),
                default      => ['erro' => 'Tipo não suportado']
            };

            return [
                'sucesso' => true,
                'metricas' => $metricas,
                'tipo_banco' => $tipo,
                'timestamp' => date('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            return ['sucesso' => false, 'erro' => $e->getMessage()];
        }
    }

    private function obterMetricasPostgres(PDO $db): array
    {
        $metricas = [];

        // Versão do PG
        $versao = 90200;
        try {
            $v = $db->query("SHOW server_version_num")->fetchColumn();
            if ($v) $versao = (int) $v;
        } catch (Exception $e) {}

        // Conexões ativas vs max
        try {
            $maxConn = (int) $db->query("SHOW max_connections")->fetchColumn();
            $metricas['conexoes_max'] = $maxConn;

            if ($versao >= 90200) {
                $atual = (int) $db->query("SELECT COUNT(*) FROM pg_stat_activity WHERE pid IS NOT NULL")->fetchColumn();
            } else {
                $atual = (int) $db->query("SELECT COUNT(*) FROM pg_stat_activity WHERE procpid IS NOT NULL")->fetchColumn();
            }
            $metricas['conexoes_atuais'] = $atual;
            $metricas['conexoes_pct'] = $maxConn > 0 ? round(($atual / $maxConn) * 100, 1) : 0;
        } catch (Exception $e) {
            $metricas['conexoes_max'] = 0;
            $metricas['conexoes_atuais'] = 0;
            $metricas['conexoes_pct'] = 0;
        }

        // Shared buffers (memória alocada para cache)
        try {
            $sharedBuffers = $db->query("SHOW shared_buffers")->fetchColumn();
            $metricas['memoria_alocada'] = $sharedBuffers;

            $workMem = $db->query("SHOW work_mem")->fetchColumn();
            $metricas['work_mem'] = $workMem;

            $effectiveCache = $db->query("SHOW effective_cache_size")->fetchColumn();
            $metricas['effective_cache_size'] = $effectiveCache;
        } catch (Exception $e) {
            $metricas['memoria_alocada'] = 'N/A';
            $metricas['work_mem'] = 'N/A';
            $metricas['effective_cache_size'] = 'N/A';
        }

        // Cache hit ratio
        try {
            $cache = $db->query("
                SELECT 
                    ROUND(100.0 * SUM(blks_hit) / NULLIF(SUM(blks_hit) + SUM(blks_read), 0), 2) AS cache_hit_ratio
                FROM pg_stat_database
                WHERE datname = current_database()
            ")->fetch(PDO::FETCH_ASSOC);
            $metricas['cache_hit_ratio'] = (float) ($cache['cache_hit_ratio'] ?? 0);
        } catch (Exception $e) {
            $metricas['cache_hit_ratio'] = 0;
        }

        // Transações (commits + rollbacks)
        try {
            $tx = $db->query("
                SELECT xact_commit, xact_rollback,
                       tup_returned, tup_fetched, tup_inserted, tup_updated, tup_deleted
                FROM pg_stat_database
                WHERE datname = current_database()
            ")->fetch(PDO::FETCH_ASSOC);
            $metricas['tx_commit'] = (int) ($tx['xact_commit'] ?? 0);
            $metricas['tx_rollback'] = (int) ($tx['xact_rollback'] ?? 0);
            $metricas['tup_returned'] = (int) ($tx['tup_returned'] ?? 0);
            $metricas['tup_fetched'] = (int) ($tx['tup_fetched'] ?? 0);
            $metricas['tup_inserted'] = (int) ($tx['tup_inserted'] ?? 0);
            $metricas['tup_updated'] = (int) ($tx['tup_updated'] ?? 0);
            $metricas['tup_deleted'] = (int) ($tx['tup_deleted'] ?? 0);
        } catch (Exception $e) {
            $metricas['tx_commit'] = 0;
            $metricas['tx_rollback'] = 0;
        }

        // Deadlocks e conflitos
        try {
            $dl = $db->query("
                SELECT deadlocks, conflicts
                FROM pg_stat_database
                WHERE datname = current_database()
            ")->fetch(PDO::FETCH_ASSOC);
            $metricas['deadlocks'] = (int) ($dl['deadlocks'] ?? 0);
            $metricas['conflicts'] = (int) ($dl['conflicts'] ?? 0);
        } catch (Exception $e) {
            $metricas['deadlocks'] = 0;
            $metricas['conflicts'] = 0;
        }

        // I/O — blocos lidos vs cache
        try {
            $io = $db->query("
                SELECT blks_read, blks_hit
                FROM pg_stat_database
                WHERE datname = current_database()
            ")->fetch(PDO::FETCH_ASSOC);
            $metricas['blocos_lidos_disco'] = (int) ($io['blks_read'] ?? 0);
            $metricas['blocos_lidos_cache'] = (int) ($io['blks_hit'] ?? 0);
        } catch (Exception $e) {
            $metricas['blocos_lidos_disco'] = 0;
            $metricas['blocos_lidos_cache'] = 0;
        }

        // Memória em MB (shared_buffers + work_mem * sessões ativas)
        try {
            $sharedPages = (int) $db->query("SELECT setting FROM pg_settings WHERE name = 'shared_buffers'")->fetchColumn();
            $workKb = (int) $db->query("SELECT setting FROM pg_settings WHERE name = 'work_mem'")->fetchColumn();
            $sharedMb = round($sharedPages * 8192 / 1048576, 1);

            $filtroSelf = $versao >= 90200
                ? "state = 'active' AND application_name != 'DMC-Monitor'"
                : "current_query NOT LIKE '<%' AND procpid != pg_backend_pid()";
            $activeQ = (int) $db->query("SELECT COUNT(*) FROM pg_stat_activity WHERE {$filtroSelf}")->fetchColumn();

            $usadaMb = round($sharedMb + ($activeQ * $workKb / 1024), 1);
            $metricas['memoria_usada_mb'] = $usadaMb;
            $metricas['memoria_max_mb'] = round($sharedMb * 1.2, 1); // margem para work_mem dinâmico
            $metricas['memoria_pct'] = $metricas['memoria_max_mb'] > 0 ? min(100, round($usadaMb / $metricas['memoria_max_mb'] * 100, 1)) : 0;

            // Carga do banco (sessões ativas / max_connections)
            $metricas['cpu_pct'] = $metricas['conexoes_max'] > 0 ? min(100, round($activeQ * 100.0 / $metricas['conexoes_max'], 1)) : 0;
        } catch (Exception $e) {
            $metricas['memoria_usada_mb'] = 0;
            $metricas['memoria_max_mb'] = 0;
            $metricas['memoria_pct'] = 0;
            $metricas['cpu_pct'] = 0;
        }

        return $metricas;
    }

    private function obterMetricasMysql(PDO $db): array
    {
        $metricas = [];

        try {
            $vars = $db->query("SHOW GLOBAL VARIABLES WHERE Variable_name IN ('max_connections','innodb_buffer_pool_size','key_buffer_size','query_cache_size','tmp_table_size')")->fetchAll(PDO::FETCH_KEY_PAIR);
            $metricas['conexoes_max'] = (int) ($vars['max_connections'] ?? 0);
            $metricas['memoria_alocada'] = $this->formatarBytes((int) ($vars['innodb_buffer_pool_size'] ?? 0));
        } catch (Exception $e) {
            $metricas['conexoes_max'] = 0;
            $metricas['memoria_alocada'] = 'N/A';
        }

        try {
            $status = $db->query("SHOW GLOBAL STATUS WHERE Variable_name IN ('Threads_connected','Threads_running','Innodb_buffer_pool_read_requests','Innodb_buffer_pool_reads','Queries','Com_commit','Com_rollback','Innodb_data_read','Innodb_data_written','Uptime')")->fetchAll(PDO::FETCH_KEY_PAIR);
            $metricas['conexoes_atuais'] = (int) ($status['Threads_connected'] ?? 0);
            $metricas['conexoes_pct'] = $metricas['conexoes_max'] > 0 ? round(($metricas['conexoes_atuais'] / $metricas['conexoes_max']) * 100, 1) : 0;

            $readReq = (int) ($status['Innodb_buffer_pool_read_requests'] ?? 0);
            $reads = (int) ($status['Innodb_buffer_pool_reads'] ?? 0);
            $metricas['cache_hit_ratio'] = $readReq > 0 ? round(100.0 * ($readReq - $reads) / $readReq, 2) : 0;

            $metricas['tx_commit'] = (int) ($status['Com_commit'] ?? 0);
            $metricas['tx_rollback'] = (int) ($status['Com_rollback'] ?? 0);
            $metricas['queries_total'] = (int) ($status['Queries'] ?? 0);
            $metricas['blocos_lidos_disco'] = (int) ($status['Innodb_data_read'] ?? 0);
            $metricas['blocos_lidos_cache'] = $readReq;

            // Carga do banco
            $running = (int) ($status['Threads_running'] ?? 0);
            $metricas['cpu_pct'] = $metricas['conexoes_max'] > 0 ? min(100, round($running * 100.0 / $metricas['conexoes_max'], 1)) : 0;
        } catch (Exception $e) {
            $metricas['conexoes_atuais'] = 0;
            $metricas['conexoes_pct'] = 0;
            $metricas['cache_hit_ratio'] = 0;
            $metricas['cpu_pct'] = 0;
        }

        // Memória do buffer pool em MB
        try {
            $pool = $db->query("SHOW GLOBAL STATUS WHERE Variable_name IN ('Innodb_buffer_pool_pages_data','Innodb_buffer_pool_pages_total')")->fetchAll(PDO::FETCH_KEY_PAIR);
            $pData = (int) ($pool['Innodb_buffer_pool_pages_data'] ?? 0);
            $pTotal = (int) ($pool['Innodb_buffer_pool_pages_total'] ?? 0);
            $metricas['memoria_usada_mb'] = round($pData * 16384 / 1048576, 1);
            $metricas['memoria_max_mb'] = round($pTotal * 16384 / 1048576, 1);
            $metricas['memoria_pct'] = $pTotal > 0 ? round($pData * 100.0 / $pTotal, 1) : 0;
        } catch (Exception $e) {
            $metricas['memoria_usada_mb'] = 0;
            $metricas['memoria_max_mb'] = 0;
            $metricas['memoria_pct'] = 0;
        }

        return $metricas;
    }

    private function obterMetricasSqlServer(PDO $db): array
    {
        $metricas = [];

        try {
            $perf = $db->query("
                SELECT 
                    (SELECT COUNT(*) FROM sys.dm_exec_sessions WHERE session_id > 50) AS conexoes_atuais,
                    (SELECT CAST(value_in_use AS INT) FROM sys.configurations WHERE name = 'max degree of parallelism') AS max_parallelism,
                    (SELECT cntr_value FROM sys.dm_os_performance_counters WHERE counter_name = 'Buffer cache hit ratio' AND object_name LIKE '%Buffer Manager%') AS cache_hit,
                    (SELECT cntr_value FROM sys.dm_os_performance_counters WHERE counter_name = 'Buffer cache hit ratio base' AND object_name LIKE '%Buffer Manager%') AS cache_hit_base
            ")->fetch(PDO::FETCH_ASSOC);

            $metricas['conexoes_atuais'] = (int) ($perf['conexoes_atuais'] ?? 0);
            $base = (int) ($perf['cache_hit_base'] ?? 0);
            $metricas['cache_hit_ratio'] = $base > 0 ? round(100.0 * (int)($perf['cache_hit'] ?? 0) / $base, 2) : 0;
        } catch (Exception $e) {
            $metricas['conexoes_atuais'] = 0;
            $metricas['cache_hit_ratio'] = 0;
        }

        try {
            $mem = $db->query("
                SELECT 
                    (SELECT CAST(value_in_use AS BIGINT) FROM sys.configurations WHERE name = 'max server memory (MB)') AS mem_max_mb,
                    (SELECT cntr_value / 1024 FROM sys.dm_os_performance_counters WHERE counter_name = 'Total Server Memory (KB)' AND object_name LIKE '%Memory Manager%') AS mem_usada_mb
            ")->fetch(PDO::FETCH_ASSOC);
            $metricas['conexoes_max'] = 32767;
            $metricas['conexoes_pct'] = 0;
            $metricas['memoria_max_mb'] = (int) ($mem['mem_max_mb'] ?? 0);
            $metricas['memoria_usada_mb'] = (int) ($mem['mem_usada_mb'] ?? 0);
            $metricas['memoria_alocada'] = ($metricas['memoria_usada_mb']) . ' MB / ' . ($metricas['memoria_max_mb']) . ' MB';
            $metricas['memoria_pct'] = $metricas['memoria_max_mb'] > 0 ? round(($metricas['memoria_usada_mb'] / $metricas['memoria_max_mb']) * 100, 1) : 0;
        } catch (Exception $e) {
            $metricas['memoria_alocada'] = 'N/A';
            $metricas['memoria_pct'] = 0;
        }

        try {
            $tx = $db->query("
                SELECT 
                    (SELECT cntr_value FROM sys.dm_os_performance_counters WHERE counter_name = 'Transactions/sec' AND instance_name = '_Total' AND object_name LIKE '%Databases%') AS tx_sec,
                    (SELECT SUM(cntr_value) FROM sys.dm_os_performance_counters WHERE counter_name LIKE 'Batch Requests/sec%') AS batch_sec
            ")->fetch(PDO::FETCH_ASSOC);
            $metricas['tx_commit'] = (int) ($tx['tx_sec'] ?? 0);
            $metricas['tx_rollback'] = 0;
        } catch (Exception $e) {
            $metricas['tx_commit'] = 0;
            $metricas['tx_rollback'] = 0;
        }

        try {
            $cpu = $db->query("
                SELECT TOP 1 
                    record.value('(./Record/SchedulerMonitorEvent/SystemHealth/ProcessUtilization)[1]', 'int') AS cpu_sql
                FROM sys.dm_os_ring_buffers
                WHERE ring_buffer_type = 'RING_BUFFER_SCHEDULER_MONITOR'
                ORDER BY timestamp DESC
            ")->fetch(PDO::FETCH_ASSOC);
            $metricas['cpu_pct'] = (int) ($cpu['cpu_sql'] ?? 0);
        } catch (Exception $e) {
            $metricas['cpu_pct'] = 0;
        }

        return $metricas;
    }

    private function obterMetricasOracle(PDO $db): array
    {
        $metricas = [];

        try {
            $sess = $db->query("
                SELECT 
                    (SELECT COUNT(*) FROM V\$SESSION WHERE TYPE = 'USER') AS conexoes_atuais,
                    (SELECT VALUE FROM V\$PARAMETER WHERE NAME = 'sessions') AS conexoes_max
            ")->fetch(PDO::FETCH_ASSOC);
            $metricas['conexoes_atuais'] = (int) ($sess['conexoes_atuais'] ?? 0);
            $metricas['conexoes_max'] = (int) ($sess['conexoes_max'] ?? 0);
            $metricas['conexoes_pct'] = $metricas['conexoes_max'] > 0 ? round(($metricas['conexoes_atuais'] / $metricas['conexoes_max']) * 100, 1) : 0;
        } catch (Exception $e) {
            $metricas['conexoes_atuais'] = 0;
            $metricas['conexoes_max'] = 0;
            $metricas['conexoes_pct'] = 0;
        }

        try {
            $mem = $db->query("
                SELECT 
                    (SELECT SUM(BYTES)/1024/1024 FROM V\$SGAINFO WHERE NAME = 'Maximum SGA Size') AS sga_max_mb,
                    (SELECT SUM(BYTES)/1024/1024 FROM V\$SGASTAT) AS sga_usada_mb
            ")->fetch(PDO::FETCH_ASSOC);
            $metricas['memoria_max_mb'] = round((float) ($mem['sga_max_mb'] ?? 0), 1);
            $metricas['memoria_usada_mb'] = round((float) ($mem['sga_usada_mb'] ?? 0), 1);
            $metricas['memoria_alocada'] = $metricas['memoria_usada_mb'] . ' MB / ' . $metricas['memoria_max_mb'] . ' MB';
            $metricas['memoria_pct'] = $metricas['memoria_max_mb'] > 0 ? round(($metricas['memoria_usada_mb'] / $metricas['memoria_max_mb']) * 100, 1) : 0;
        } catch (Exception $e) {
            $metricas['memoria_alocada'] = 'N/A';
            $metricas['memoria_pct'] = 0;
        }

        try {
            $cache = $db->query("
                SELECT ROUND((1 - (phy.value / (cur.value + con.value))) * 100, 2) AS cache_hit_ratio
                FROM V\$SYSSTAT phy, V\$SYSSTAT cur, V\$SYSSTAT con
                WHERE phy.NAME = 'physical reads'
                  AND cur.NAME = 'db block gets'
                  AND con.NAME = 'consistent gets'
                  AND (cur.value + con.value) > 0
            ")->fetch(PDO::FETCH_ASSOC);
            $metricas['cache_hit_ratio'] = (float) ($cache['cache_hit_ratio'] ?? 0);
        } catch (Exception $e) {
            $metricas['cache_hit_ratio'] = 0;
        }

        $metricas['tx_commit'] = 0;
        $metricas['tx_rollback'] = 0;

        // Carga do banco (sessões ativas / max)
        $metricas['cpu_pct'] = $metricas['conexoes_max'] > 0 ? min(100, round($metricas['conexoes_atuais'] * 100.0 / $metricas['conexoes_max'], 1)) : 0;

        return $metricas;
    }

    private function formatarBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 1) . ' GB';
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
