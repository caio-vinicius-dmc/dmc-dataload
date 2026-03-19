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
        $this->storagePath = __DIR__ . '/../../storage/logs';
        if (!is_dir($this->storagePath)) {
            mkdir($this->storagePath, 0775, true);
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

        try {
            $tipo = strtoupper($bloco['tipo_bloco'] ?? 'SELECT');

            if ($tipo === 'SELECT') {
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
                        // Sanitizar ; dentro dos dados
                        $r = array_map(function ($v) {
                            if ($v === null) return '';
                            return str_replace(';', ',', (string)$v);
                        }, $r);
                        fputcsv($fp, $r, ';');
                        $count++;
                    }
                } else {
                    // se sem linhas, apenas gravar cabeçalho vazio
                    fputcsv($fp, [], ';');
                    $count = 0;
                }

                rewind($fp);
                $content = stream_get_contents($fp);
                fclose($fp);

                file_put_contents($filepath, $content);
                $registrosProcessados = $count;

                $resultado = sprintf('Arquivo gerado: %s (Linhas: %d)', $filepath, $count);

                // Inserir auditoria no banco alvo - tentar esquema novo, senão usar fallback para esquemas legados
                try {
                    $insertSql = "INSERT INTO tb_auditoria_rotina (id_rotina, bloco_codigo, data_execucao, resultado, caminho_csv) VALUES (?, ?, now(), ?, ?)";
                    $ins = $pdoAlvo->prepare($insertSql);
                    $ins->execute([$idRotina, $codigoBloco, $resultado, $filepath]);
                } catch (\Throwable $e) {
                    // fallback para esquema legado (rotina, bloco, inicio, fim, status, resultado, id_arquivo)
                    try {
                        $fins = $pdoAlvo->prepare("INSERT INTO tb_auditoria_rotina (rotina, bloco, inicio, status, resultado) VALUES (?, ?, now(), ?, ?)");
                        $fins->execute([$idRotina, $codigoBloco, 'sucesso', $resultado]);
                    } catch (\Throwable $e2) {
                        // não conseguir escrever auditoria não é fatal aqui
                    }
                }

                $fim = microtime(true);
                $duracaoMs = round(($fim - $inicio) * 1000);
                return ['sucesso' => true, 'resultado' => $resultado, 'linhas' => $count, 'arquivo' => $filepath, 'duracao_ms' => $duracaoMs, 'registros' => $registrosProcessados];
            } else {
                $stmt = $pdoAlvo->prepare($sql);
                $stmt->execute();
                $af = $stmt->rowCount();
                $registrosProcessados = $af;

                $resultado = sprintf('Linhas afetadas: %d', $af);
                try {
                    $ins = $pdoAlvo->prepare("INSERT INTO tb_auditoria_rotina (id_rotina, bloco_codigo, data_execucao, resultado) VALUES (?, ?, now(), ?)");
                    $ins->execute([$idRotina, $codigoBloco, $resultado]);
                } catch (\Throwable $e) {
                    try {
                        $fins = $pdoAlvo->prepare("INSERT INTO tb_auditoria_rotina (rotina, bloco, inicio, status, resultado) VALUES (?, ?, now(), ?, ?)");
                        $fins->execute([$idRotina, $codigoBloco, 'sucesso', $resultado]);
                    } catch (\Throwable $e2) {
                        // ignore
                    }
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

    public function executarRotina(int $idRotina): array
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

        $logs = [];
        $blocosExecutados = 0;
        $blocosSucesso = 0;
        $blocosFalha = 0;
        $registrosTotal = 0;
        $detalhesExecucao = [];
        try {
            // obter perfil de conexao
            $ps = $db->prepare('SELECT * FROM tb_perfis_conexao WHERE id = ?');
            $ps->execute([$rotina['id_conexao']]);
            $perfil = $ps->fetch(PDO::FETCH_ASSOC);
            if (!$perfil) throw new \RuntimeException('Perfil de conexão não encontrado');

            $pdoAlvo = $this->criarConexaoAlvo($perfil);
            if (!$pdoAlvo) throw new \RuntimeException('Não foi possível conectar ao banco alvo');

            // garantir tabela de auditoria no alvo
            $ddl = $this->gerarAuditoriaDDL($perfil['tipo_banco']);
            $pdoAlvo->exec($ddl);

            // buscar blocos
            $bs = $db->prepare('SELECT * FROM tb_blocos_rotina WHERE id_rotina = ? ORDER BY ordem ASC');
            $bs->execute([$idRotina]);
            $blocos = $bs->fetchAll(PDO::FETCH_ASSOC);

            foreach ($blocos as $bloco) {
                $codigo = $bloco['codigo_bloco'] ?? $bloco['id'];
                $res = $this->executarBloco($pdoAlvo, $bloco, $idRotina, $codigo);
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
            }

            $fimExecucao = microtime(true);
            $duracaoTotal = round(($fimExecucao - $inicioExecucao) * 1000);

            // registrar log execução com todas as métricas
            $insLog = $db->prepare('INSERT INTO tb_logs_execucao (id_rotina, data_inicio, data_fim, status, duracao_ms, blocos_executados, blocos_sucesso, blocos_falha, registros_processados, meta) VALUES (?, now(), now(), ?, ?, ?, ?, ?, ?, ?::jsonb)');
            $insLog->execute([$idRotina, 'sucesso', $duracaoTotal, $blocosExecutados, $blocosSucesso, $blocosFalha, $registrosTotal, json_encode($detalhesExecucao)]);

            ServicoWebhook::notificarSucesso('rotina', $rotina['nome'] ?? "Rotina #{$idRotina}", $idRotina, [
                'blocos_executados' => $blocosExecutados, 'blocos_sucesso' => $blocosSucesso,
                'blocos_falha' => $blocosFalha, 'registros_total' => $registrosTotal, 'duracao_ms' => $duracaoTotal
            ]);
            ServicoCanalNotificacao::notificar('sucesso', "Sucesso: " . ($rotina['nome'] ?? "Rotina #{$idRotina}"), [
                'blocos' => "{$blocosSucesso}/{$blocosExecutados}", 'registros' => $registrosTotal, 'duracao' => "{$duracaoTotal}ms"
            ], 'rotina');

            return ['sucesso' => true, 'logs' => $logs, 'metricas' => ['blocos_executados' => $blocosExecutados, 'blocos_sucesso' => $blocosSucesso, 'blocos_falha' => $blocosFalha, 'registros_total' => $registrosTotal, 'duracao_ms' => $duracaoTotal]];
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

            return ['sucesso' => false, 'erro' => $e->getMessage(), 'logs' => $logs, 'metricas' => ['blocos_executados' => $blocosExecutados, 'blocos_sucesso' => $blocosSucesso, 'blocos_falha' => $blocosFalha, 'registros_total' => $registrosTotal, 'duracao_ms' => $duracaoTotal]];
        } finally {
            // liberar
            $db->prepare('UPDATE tb_rotinas SET esta_executando = false, ultima_verificacao = now() WHERE id = ?')->execute([$idRotina]);
        }
    }
}
