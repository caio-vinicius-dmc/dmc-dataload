<?php
namespace App\Servicos;

use App\Core\Database;
use App\Servicos\ServicoNotificacao;
use App\Utils\Crypto;
use PDO;

class ServicoExecucao
{
    private string $storagePath;
    private string $encryptionKeyBase64;

    public function __construct()
    {
        $this->storagePath = realpath(__DIR__ . '/../../storage/logs') ?: __DIR__ . '/../../storage/logs';
        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0775, true);
            $this->storagePath = realpath($this->storagePath) ?: $this->storagePath;
        }

        $this->encryptionKeyBase64 = $_ENV['ENCRYPTION_KEY'] ?? $_SERVER['ENCRYPTION_KEY'] ?? getenv('ENCRYPTION_KEY') ?: '';
    }

    public function processarVariaveis(string $sql): string
    {
        $map = [
            '{{DATA_ATUAL}}' => date('Y-m-d'),
            '{{DATA_ONTEM}}' => date('Y-m-d', strtotime('-1 day')),
            '{{INICIO_MES}}' => date('Y-m-01'),
            '{{DATA_HORA}}'  => date('Y-m-d H:i:s')
        ];

        return strtr($sql, $map);
    }

    /**
     * Remove comentários SQL (-- e /* *\/) para detectar o verdadeiro tipo de operação
     */
    private function stripSqlComments(string $sql): string
    {
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        $sql = preg_replace('/^--[^\n]*/m', '', $sql);
        return trim($sql);
    }

    /**
     * Detecta o tipo real da operação SQL a partir do conteúdo,
     * independente do campo tipo_bloco (que pode estar errado).
     * 
     * Retorna: 'SELECT', 'DML', 'DDL' ou 'CALL'
     */
    private function detectarTipoSql(string $sql): string
    {
        $clean = $this->stripSqlComments($sql);
        $upper = strtoupper(trim($clean));

        if ($upper === '') return 'DML';

        // CTE: WITH ... seguido da operação real
        if (preg_match('/^WITH\b/', $upper)) {
            // Encontrar a operação final após as CTEs
            if (preg_match('/\)\s*(INSERT|UPDATE|DELETE|MERGE)\b/', $upper)) {
                return 'DML';
            }
            return 'SELECT';
        }

        if (preg_match('/^SELECT\b/', $upper)) return 'SELECT';
        if (preg_match('/^(INSERT|UPDATE|DELETE|MERGE|UPSERT)\b/', $upper)) return 'DML';
        if (preg_match('/^(CREATE|ALTER|DROP|TRUNCATE|GRANT|REVOKE|COMMENT|ANALYZE|VACUUM|REINDEX|CLUSTER|REFRESH|SET)\b/', $upper)) return 'DDL';
        if (preg_match('/^(CALL|DO|EXECUTE)\b/', $upper)) return 'CALL';

        return 'DML';
    }

    /**
     * Verifica se o SQL é uma operação DDL (para controle de transação)
     */
    private function isDDL(string $sql): bool
    {
        return $this->detectarTipoSql($sql) === 'DDL';
    }

    private function decryptSenha(string $enc): string
    {
        if (empty($enc)) return '';

        if (empty($this->encryptionKeyBase64)) {
            error_log("ServicoExecucao: ENCRYPTION_KEY não configurada");
            return '';
        }

        try {
            return Crypto::decrypt($enc, $this->encryptionKeyBase64);
        } catch (\Exception $e) {
            error_log("ServicoExecucao: Erro ao descriptografar senha: " . $e->getMessage());
            return '';
        }
    }

    public function gerarAuditoriaDDL(string $tipoBanco): string
    {
        $tipo = strtolower($tipoBanco);
        switch ($tipo) {
            case 'mysql':
            case 'mariadb':
                return "CREATE TABLE IF NOT EXISTS tb_auditoria_rotina (id BIGINT AUTO_INCREMENT PRIMARY KEY, id_rotina BIGINT, bloco_codigo VARCHAR(255), data_execucao DATETIME DEFAULT CURRENT_TIMESTAMP, resultado TEXT, caminho_csv TEXT)";
            case 'oracle':
                return "CREATE TABLE tb_auditoria_rotina (id NUMBER(10) PRIMARY KEY, id_rotina NUMBER(10), bloco_codigo VARCHAR2(100), data_execucao TIMESTAMP, resultado CLOB, caminho_csv VARCHAR2(4000))";
            case 'sqlserver':
                return "IF OBJECT_ID('dbo.tb_auditoria_rotina','U') IS NULL CREATE TABLE tb_auditoria_rotina (id INT IDENTITY(1,1) PRIMARY KEY, id_rotina INT, bloco_codigo VARCHAR(100), data_execucao DATETIME, resultado VARCHAR(MAX), caminho_csv VARCHAR(MAX))";
            case 'sqlite':
                return "CREATE TABLE IF NOT EXISTS tb_auditoria_rotina (id INTEGER PRIMARY KEY AUTOINCREMENT, id_rotina INTEGER, bloco_codigo TEXT, data_execucao TEXT DEFAULT (datetime('now')), resultado TEXT, caminho_csv TEXT)";
            case 'postgres':
            default:
                return "CREATE TABLE IF NOT EXISTS tb_auditoria_rotina (id BIGSERIAL PRIMARY KEY, id_rotina BIGINT, bloco_codigo TEXT, data_execucao TIMESTAMPTZ, resultado TEXT, caminho_csv TEXT)";
        }
    }

    private function criarConexaoAlvo(array $perfil): ?PDO
    {
        $tipo = $perfil['tipo_banco'] ?? 'postgres';
        $host = $perfil['host'] ?? 'localhost';
        $porta = $perfil['porta'] ?? null;
        $db = $perfil['nome_banco'] ?? '';
        $user = $perfil['usuario'] ?? '';
        $senha = $this->decryptSenha($perfil['senha_encriptada'] ?? '');

        try {
            switch ($tipo) {
                case 'postgres':
                    $port = $porta ?: 5432;
                    $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
                    return new PDO($dsn, $user, $senha, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                case 'mysql':
                case 'mariadb':
                    $port = $porta ?: 3306;
                    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
                    return new PDO($dsn, $user, $senha, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                case 'sqlserver':
                    $port = $porta ?: 1433;
                    $dsn = "sqlsrv:Server={$host},{$port};Database={$db}";
                    return new PDO($dsn, $user, $senha, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                case 'oracle':
                    $port = $porta ?: 1521;
                    $dsn = "oci:dbname=//{$host}:{$port}/{$db}";
                    return new PDO($dsn, $user, $senha, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                case 'odbc':
                    $dsn = "odbc:{$db}";
                    return new PDO($dsn, $user, $senha, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                case 'sqlite':
                    $extras = json_decode($perfil['parametros_extras'] ?? '{}', true) ?: [];
                    $sqlitePath = $extras['sqlite_path'] ?? $db ?? '';
                    if (empty($sqlitePath) || !file_exists($sqlitePath)) {
                        error_log("ServicoExecucao: Arquivo SQLite não encontrado: {$sqlitePath}");
                        return null;
                    }
                    return new PDO("sqlite:{$sqlitePath}", null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                default:
                    error_log("ServicoExecucao: Tipo de banco desconhecido: {$tipo}");
                    return null;
            }
        } catch (\Exception $e) {
            error_log("ServicoExecucao: Erro ao conectar {$tipo}://{$host}:{$porta}/{$db} como {$user}: " . $e->getMessage());
            return null;
        }
    }

    public function executarBloco(PDO $pdoAlvo, array $bloco, int $idRotina, string $codigoBloco): array
    {
        $inicio = microtime(true);
        $sqlRaw = $bloco['script_sql'] ?? '';
        $sql = $this->processarVariaveis($sqlRaw);
        $registrosProcessados = 0;

        // Detectar tipo REAL da operação SQL pelo conteúdo (não confiar em tipo_bloco)
        $tipoReal = $this->detectarTipoSql($sql);
        $tipoBlocoField = $bloco['tipo_bloco'] ?? 'UNKNOWN';
        error_log("ServicoExecucao::executarBloco rotina={$idRotina} bloco={$codigoBloco} tipo_bloco={$tipoBlocoField} tipo_real={$tipoReal} inTransaction=" . ($pdoAlvo->inTransaction() ? 'SIM' : 'NAO') . " sql=" . substr(trim($sql), 0, 120));

        try {
            if ($tipoReal === 'SELECT') {
                // SELECT: executar query, buscar resultados, gerar CSV
                $stmt = $pdoAlvo->query($sql);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $filename = sprintf('execucao_%s_%s_%s.csv', $idRotina, $codigoBloco, time());
                $filepath = $this->storagePath . DIRECTORY_SEPARATOR . $filename;

                $fp = fopen('php://temp', 'w+');
                if ($fp === false) throw new \RuntimeException('Erro ao abrir stream temp');

                if (count($rows) > 0) {
                    fputcsv($fp, array_keys($rows[0]), ';');
                    $count = 0;
                    foreach ($rows as $r) {
                        $r = array_map(function ($v) {
                            if ($v === null) return '';
                            return str_replace(';', ',', (string)$v);
                        }, $r);
                        fputcsv($fp, $r, ';');
                        $count++;
                    }
                } else {
                    fputcsv($fp, [], ';');
                    $count = 0;
                }

                rewind($fp);
                $content = stream_get_contents($fp);
                fclose($fp);

                file_put_contents($filepath, $content);
                $registrosProcessados = $count;

                $resultado = sprintf('Arquivo gerado: %s (Linhas: %d)', $filepath, $count);

                try {
                    $insertSql = "INSERT INTO tb_auditoria_rotina (id_rotina, bloco_codigo, data_execucao, resultado, caminho_csv) VALUES (?, ?, now(), ?, ?)";
                    $ins = $pdoAlvo->prepare($insertSql);
                    $ins->execute([$idRotina, $codigoBloco, $resultado, $filepath]);
                } catch (\Throwable $e) {
                    try {
                        $fins = $pdoAlvo->prepare("INSERT INTO tb_auditoria_rotina (rotina, bloco, inicio, status, resultado) VALUES (?, ?, now(), ?, ?)");
                        $fins->execute([$idRotina, $codigoBloco, 'sucesso', $resultado]);
                    } catch (\Throwable $e2) { /* auditoria não é fatal */ }
                }

                $fim = microtime(true);
                $duracaoMs = round(($fim - $inicio) * 1000);
                return ['sucesso' => true, 'resultado' => $resultado, 'linhas' => $count, 'arquivo' => $filepath, 'duracao_ms' => $duracaoMs, 'registros' => $registrosProcessados];

            } elseif ($tipoReal === 'DDL' || $tipoReal === 'CALL') {
                // DDL (TRUNCATE, CREATE, ALTER, DROP, GRANT, etc.) e CALL: usar exec()
                $af = $pdoAlvo->exec($sql);
                if ($af === false) $af = 0;
                $registrosProcessados = (int)$af;

                $resultado = sprintf('Linhas afetadas: %d', $af);
                try {
                    $ins = $pdoAlvo->prepare("INSERT INTO tb_auditoria_rotina (id_rotina, bloco_codigo, data_execucao, resultado) VALUES (?, ?, now(), ?)");
                    $ins->execute([$idRotina, $codigoBloco, $resultado]);
                } catch (\Throwable $e) {
                    try {
                        $fins = $pdoAlvo->prepare("INSERT INTO tb_auditoria_rotina (rotina, bloco, inicio, status, resultado) VALUES (?, ?, now(), ?, ?)");
                        $fins->execute([$idRotina, $codigoBloco, 'sucesso', $resultado]);
                    } catch (\Throwable $e2) { /* ignore */ }
                }

                $fim = microtime(true);
                $duracaoMs = round(($fim - $inicio) * 1000);
                return ['sucesso' => true, 'resultado' => $resultado, 'linhas' => $af, 'duracao_ms' => $duracaoMs, 'registros' => $registrosProcessados];

            } else {
                // DML (INSERT, UPDATE, DELETE, MERGE, etc.): usar prepare/execute
                $stmt = $pdoAlvo->prepare($sql);
                $stmt->execute();
                $af = $stmt->rowCount();
                $registrosProcessados = (int)$af;

                $resultado = sprintf('Linhas afetadas: %d', $af);
                try {
                    $ins = $pdoAlvo->prepare("INSERT INTO tb_auditoria_rotina (id_rotina, bloco_codigo, data_execucao, resultado) VALUES (?, ?, now(), ?)");
                    $ins->execute([$idRotina, $codigoBloco, $resultado]);
                } catch (\Throwable $e) {
                    try {
                        $fins = $pdoAlvo->prepare("INSERT INTO tb_auditoria_rotina (rotina, bloco, inicio, status, resultado) VALUES (?, ?, now(), ?, ?)");
                        $fins->execute([$idRotina, $codigoBloco, 'sucesso', $resultado]);
                    } catch (\Throwable $e2) { /* ignore */ }
                }

                $fim = microtime(true);
                $duracaoMs = round(($fim - $inicio) * 1000);
                return ['sucesso' => true, 'resultado' => $resultado, 'linhas' => $af, 'duracao_ms' => $duracaoMs, 'registros' => $registrosProcessados];
            }
        } catch (\Throwable $e) {
            $fim = microtime(true);
            $duracaoMs = round(($fim - $inicio) * 1000);
            return ['sucesso' => false, 'erro' => $e->getMessage(), 'duracao_ms' => $duracaoMs, 'registros' => 0];
        }
    }

    public function executarRotina(int $idRotina, int $iniciarDeBloco = 1, array $blocosSelecionados = []): array
    {
        $db = Database::getConexao();
        $inicioExecucao = microtime(true);

        // Carregar rotina e verificar concorrência
        $stmt = $db->prepare('SELECT * FROM tb_rotinas WHERE id = ? FOR UPDATE');
        $stmt->execute([$idRotina]);
        $rotina = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$rotina) return ['sucesso' => false, 'mensagem' => 'Rotina não encontrada'];

        if ($rotina['esta_executando']) {
            // Verificar timeout de 6h
            $ultima = $rotina['ultima_verificacao'] ? strtotime($rotina['ultima_verificacao']) : 0;
            if (time() - $ultima < 6 * 3600) {
                return ['sucesso' => false, 'mensagem' => 'Ignorado por Conflito (esta_executando = true)'];
            }
        }

        // Bloquear
        $u = $db->prepare('UPDATE tb_rotinas SET esta_executando = true, ultima_verificacao = now() WHERE id = ?');
        $u->execute([$idRotina]);

        // Opções de controle de erro
        $pararEmErro = !empty($rotina['parar_em_erro']);
        $rollbackEmErro = !empty($rotina['rollback_em_erro']);

        $logs = [];
        $blocosExecutados = 0;
        $blocosSucesso = 0;
        $blocosFalha = 0;
        $registrosTotal = 0;
        $detalhesExecucao = [];
        $interrompido = false;
        $rollbackRealizado = false;
        try {
            // obter perfil de conexao
            $ps = $db->prepare('SELECT * FROM tb_perfis_conexao WHERE id = ?');
            $ps->execute([$rotina['id_conexao']]);
            $perfil = $ps->fetch(PDO::FETCH_ASSOC);
            if (!$perfil) throw new \RuntimeException('Perfil de conexão não encontrado');

            $pdoAlvo = $this->criarConexaoAlvo($perfil);
            if (!$pdoAlvo) throw new \RuntimeException('Não foi possível conectar ao banco alvo');

            // garantir tabela de auditoria no alvo (fora da transação)
            $ddl = $this->gerarAuditoriaDDL($perfil['tipo_banco']);
            $pdoAlvo->exec($ddl);

            // buscar blocos
            $bs = $db->prepare('SELECT * FROM tb_blocos_rotina WHERE id_rotina = ? ORDER BY ordem ASC');
            $bs->execute([$idRotina]);
            $blocos = $bs->fetchAll(PDO::FETCH_ASSOC);

            // Iniciar transação se rollback estiver ativo
            $emTransacao = false;
            if ($rollbackEmErro) {
                try {
                    $pdoAlvo->beginTransaction();
                    $emTransacao = true;
                } catch (\Throwable $e) {
                    error_log("ServicoExecucao: Não foi possível iniciar transação: " . $e->getMessage());
                }
            }

            // Modo de execução: selecionados ou a partir de
            $modoSelecionados = !empty($blocosSelecionados);

            foreach ($blocos as $idx => $bloco) {
                $codigo = $bloco['codigo_bloco'] ?? $bloco['id'];
                $tipoBlocoUpper = strtoupper($bloco['tipo_bloco'] ?? 'SELECT');
                $ordemBloco = (int)($bloco['ordem'] ?? ($idx + 1));

                // Determinar se deve pular este bloco
                $deveExecutar = true;
                if ($modoSelecionados) {
                    $deveExecutar = in_array($ordemBloco, $blocosSelecionados);
                } elseif ($ordemBloco < $iniciarDeBloco) {
                    $deveExecutar = false;
                }

                if (!$deveExecutar) {
                    $detalhesExecucao[] = [
                        'id_bloco' => $bloco['id'],
                        'bloco' => $codigo,
                        'tipo' => $bloco['tipo_bloco'] ?? 'UNKNOWN',
                        'ordem' => $ordemBloco,
                        'sql' => $bloco['script_sql'] ?? '',
                        'duracao_ms' => 0,
                        'registros' => 0,
                        'erro' => null,
                        'resultado' => '',
                        'arquivo_csv' => null,
                        'status' => 'pulado'
                    ];
                    continue;
                }
                
                // DDL causa auto-commit em MySQL/MariaDB — gerenciar transação
                // Detecção baseada no conteúdo SQL real (não confiar em tipo_bloco)
                $isDDL = $this->isDDL($bloco['script_sql'] ?? '');
                if ($isDDL && $emTransacao) {
                    // Commit antes do DDL, pois DDL força commit implícito em MySQL
                    // e em PostgreSQL queremos que TRUNCATE/DROP persistam independente de rollback posterior
                    try { $pdoAlvo->commit(); } catch (\Throwable $e) { /* ignore */ }
                    $emTransacao = false;
                }
                
                $res = $this->executarBloco($pdoAlvo, $bloco, $idRotina, $codigo);
                
                // Reiniciar transação após DDL se rollback está ativo
                if ($isDDL && $rollbackEmErro && !$emTransacao) {
                    try {
                        $pdoAlvo->beginTransaction();
                        $emTransacao = true;
                    } catch (\Throwable $e) {
                        error_log("ServicoExecucao: Não foi possível reiniciar transação após DDL: " . $e->getMessage());
                    }
                }
                
                $logs[] = ['bloco' => $codigo, 'res' => $res];
                
                $blocosExecutados++;
                if ($res['sucesso']) {
                    $blocosSucesso++;
                    $registrosTotal += ($res['registros'] ?? 0);
                } else {
                    $blocosFalha++;
                }
                
                // Adicionar ao detalhamento SUPER completo para exibir na timeline
                $detalhesExecucao[] = [
                    'id_bloco' => $bloco['id'],
                    'bloco' => $codigo,
                    'tipo' => $bloco['tipo_bloco'] ?? 'UNKNOWN',
                    'ordem' => $bloco['ordem'] ?? 0,
                    'sql' => $bloco['script_sql'] ?? '',
                    'duracao_ms' => $res['duracao_ms'] ?? 0,
                    'registros' => $res['registros'] ?? 0,
                    'erro' => $res['sucesso'] ? null : ($res['erro'] ?? 'Erro desconhecido'),
                    'resultado' => $res['resultado'] ?? '',
                    'arquivo_csv' => $res['arquivo'] ?? null,
                    'status' => $res['sucesso'] ? 'sucesso' : 'falha'
                ];

                // Se o bloco falhou e a opção de parar está ativa
                if (!$res['sucesso'] && $pararEmErro) {
                    $interrompido = true;

                    // Marcar os blocos restantes como "ignorado"
                    $blocoIdx = array_search($bloco, $blocos);
                    $restantes = array_slice($blocos, $blocoIdx + 1);
                    foreach ($restantes as $blocoIgnorado) {
                        $codigoIgnorado = $blocoIgnorado['codigo_bloco'] ?? $blocoIgnorado['id'];
                        $detalhesExecucao[] = [
                            'id_bloco' => $blocoIgnorado['id'],
                            'bloco' => $codigoIgnorado,
                            'tipo' => $blocoIgnorado['tipo_bloco'] ?? 'UNKNOWN',
                            'ordem' => $blocoIgnorado['ordem'] ?? 0,
                            'sql' => $blocoIgnorado['script_sql'] ?? '',
                            'duracao_ms' => 0,
                            'registros' => 0,
                            'erro' => 'Bloco ignorado: execução interrompida por erro no bloco ' . $codigo,
                            'resultado' => '',
                            'arquivo_csv' => null,
                            'status' => 'ignorado'
                        ];
                    }

                    // Rollback se ativo
                    if ($rollbackEmErro && $emTransacao) {
                        try {
                            $pdoAlvo->rollBack();
                            $emTransacao = false;
                            $rollbackRealizado = true;
                            error_log("ServicoExecucao: Rollback realizado para rotina #{$idRotina} após erro no bloco {$codigo}");
                        } catch (\Throwable $rbEx) {
                            error_log("ServicoExecucao: Erro ao realizar rollback: " . $rbEx->getMessage());
                        }
                    }

                    break; // Parar execução dos blocos
                }
            }

            // Commit se transação está aberta e não houve erro
            if ($emTransacao) {
                try {
                    $pdoAlvo->commit();
                    $emTransacao = false;
                } catch (\Throwable $commitEx) {
                    error_log("ServicoExecucao: Erro ao commitar transação: " . $commitEx->getMessage());
                }
            }

            $fimExecucao = microtime(true);
            $duracaoTotal = round(($fimExecucao - $inicioExecucao) * 1000);

            // Determinar status final
            // - "sucesso": todos os blocos passaram
            // - "parcial": alguns blocos falharam (mesmo que não tenha parar_em_erro)
            // - "falha": todos falharam ou erro fatal
            if ($blocosFalha === 0) {
                $statusFinal = 'sucesso';
            } elseif ($blocosSucesso > 0) {
                $statusFinal = 'parcial';
            } else {
                $statusFinal = 'falha';
            }

            // Adicionar info extra no meta
            $metaExtra = [
                'parar_em_erro' => $pararEmErro,
                'rollback_em_erro' => $rollbackEmErro,
                'interrompido' => $interrompido,
                'rollback_realizado' => $rollbackRealizado,
                'iniciar_de_bloco' => (!$modoSelecionados && $iniciarDeBloco > 1) ? $iniciarDeBloco : null,
                'blocos_selecionados' => $modoSelecionados ? $blocosSelecionados : null,
            ];
            $metaCompleto = [
                'blocos' => $detalhesExecucao,
                'opcoes' => $metaExtra,
            ];

            // registrar log execução com todas as métricas
            $mensagemErro = null;
            if ($interrompido) {
                $ultimoErro = end($logs);
                $mensagemErro = 'Execução interrompida' . ($rollbackRealizado ? ' (rollback realizado)' : '') . ': ' . ($ultimoErro['res']['erro'] ?? 'erro desconhecido');
            }
            
            $insLog = $db->prepare('INSERT INTO tb_logs_execucao (id_rotina, data_inicio, data_fim, status, mensagem_erro, duracao_ms, blocos_executados, blocos_sucesso, blocos_falha, registros_processados, meta) VALUES (?, now(), now(), ?, ?, ?, ?, ?, ?, ?, ?::jsonb)');
            $insLog->execute([$idRotina, $statusFinal, $mensagemErro, $duracaoTotal, $blocosExecutados, $blocosSucesso, $blocosFalha, $registrosTotal, json_encode($metaCompleto)]);

            // Notificações baseadas no status
            $metricasNotif = [
                'blocos_executados' => $blocosExecutados, 'blocos_sucesso' => $blocosSucesso,
                'blocos_falha' => $blocosFalha, 'registros_total' => $registrosTotal, 'duracao_ms' => $duracaoTotal
            ];
            if ($statusFinal === 'sucesso') {
                ServicoNotificacao::notificarSucessoRotina(
                    $idRotina,
                    $rotina['nome'] ?? "Rotina #{$idRotina}",
                    $metricasNotif
                );
            } else {
                // Parcial ou com falhas
                ServicoNotificacao::notificarFalhaRotina(
                    $idRotina,
                    $rotina['nome'] ?? "Rotina #{$idRotina}",
                    $mensagemErro ?? 'Erro em blocos',
                    $metricasNotif
                );
            }

            $sucessoGeral = $statusFinal === 'sucesso';
            return ['sucesso' => $sucessoGeral, 'status' => $statusFinal, 'logs' => $logs, 'metricas' => ['blocos_executados' => $blocosExecutados, 'blocos_sucesso' => $blocosSucesso, 'blocos_falha' => $blocosFalha, 'registros_total' => $registrosTotal, 'duracao_ms' => $duracaoTotal, 'interrompido' => $interrompido, 'rollback_realizado' => $rollbackRealizado]];
        } catch (\Throwable $e) {
            $fimExecucao = microtime(true);
            $duracaoTotal = round(($fimExecucao - $inicioExecucao) * 1000);
            
            $db->prepare('INSERT INTO tb_logs_execucao (id_rotina, data_inicio, data_fim, status, mensagem_erro, duracao_ms, blocos_executados, blocos_sucesso, blocos_falha, registros_processados, meta) VALUES (?, now(), now(), ?, ?, ?, ?, ?, ?, ?, ?::jsonb)')
               ->execute([$idRotina, 'falha', $e->getMessage(), $duracaoTotal, $blocosExecutados, $blocosSucesso, $blocosFalha, $registrosTotal, json_encode($detalhesExecucao)]);

            // Notificar falha
            ServicoNotificacao::notificarFalhaRotina(
                $idRotina,
                $rotina['nome'] ?? "Rotina #{$idRotina}",
                $e->getMessage(),
                ['blocos_falha' => $blocosFalha, 'duracao_ms' => $duracaoTotal]
            );

            return ['sucesso' => false, 'status' => 'falha', 'erro' => $e->getMessage(), 'logs' => $logs, 'metricas' => ['blocos_executados' => $blocosExecutados, 'blocos_sucesso' => $blocosSucesso, 'blocos_falha' => $blocosFalha, 'registros_total' => $registrosTotal, 'duracao_ms' => $duracaoTotal]];
        } finally {
            // liberar e registrar data da última execução
            $db->prepare('UPDATE tb_rotinas SET esta_executando = false, ultima_verificacao = now(), ultima_execucao = now() WHERE id = ?')->execute([$idRotina]);
        }
    }
}
