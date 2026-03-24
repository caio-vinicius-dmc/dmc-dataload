<?php
namespace App\Controllers;

use App\Core\Database;
use App\Utils\Crypto;
use App\Utils\DriverChecker;
use App\Utils\DriverInstaller;
use PDO;

class ConexoesController
{
    public function testarConexao(array $data): array
    {
        $tipo = $data['tipo_banco'] ?? 'postgres';
        $host = $data['host'] ?? 'localhost';
        $porta = $data['porta'] ?? null;
        $db = $data['nome_banco'] ?? '';
        $user = $data['usuario'] ?? '';
        $senha = $data['senha'] ?? '';

        // Verificar se o driver está disponível
        if (!DriverChecker::isDriverAvailable($tipo)) {
            $driverInfo = DriverChecker::getDriverInstallInfo($tipo);
            return [
                'sucesso' => false, 
                'mensagem' => "Driver '{$driverInfo['driver']}' não está disponível para {$tipo}",
                'driver_faltante' => true,
                'tipo_banco' => $tipo,
                'driver_info' => $driverInfo
            ];
        }

        try {
            switch ($tipo) {
                case 'postgres':
                    $port = $porta ?: 5432;
                    $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
                    $pdo = new PDO($dsn, $user, $senha, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                    break;
                    
                case 'mysql':
                case 'mariadb':
                    $port = $porta ?: 3306;
                    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
                    $pdo = new PDO($dsn, $user, $senha, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                    break;
                    
                case 'sqlserver':
                    $port = $porta ?: 1433;
                    $instance = $data['instance_name'] ?? '';
                    if (!empty($instance)) {
                        $dsn = "sqlsrv:Server={$host}\\{$instance},{$port};Database={$db}";
                    } else {
                        $dsn = "sqlsrv:Server={$host},{$port};Database={$db}";
                    }
                    $pdo = new PDO($dsn, $user, $senha, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                    break;
                    
                case 'oracle':
                    $port = $porta ?: 1521;
                    $tipoConexao = $data['tipo_conexao_oracle'] ?? 'sid';
                    $sidOrService = $data['sid'] ?? '';
                    
                    if (empty($sidOrService)) {
                        return ['sucesso' => false, 'mensagem' => 'SID ou Service Name é obrigatório para Oracle'];
                    }
                    
                    if ($tipoConexao === 'service_name') {
                        // Service Name
                        $dsn = "oci:dbname=//{$host}:{$port}/{$sidOrService}";
                    } else {
                        // SID
                        $dsn = "oci:dbname=//{$host}:{$port}/{$sidOrService}";
                    }
                    $pdo = new PDO($dsn, $user, $senha, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                    break;
                    
                case 'odbc':
                    $dsn = $db;
                    $pdo = new PDO("odbc:{$dsn}", $user, $senha, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                    break;
                    
                case 'sqlite':
                    $sqlitePath = $data['sqlite_path'] ?? $data['nome_banco'] ?? '';
                    if (empty($sqlitePath)) {
                        return ['sucesso' => false, 'mensagem' => 'Caminho do banco SQLite é obrigatório'];
                    }
                    $dir = dirname($sqlitePath);
                    if (!is_dir($dir)) {
                        return ['sucesso' => false, 'mensagem' => "Diretório não existe: {$dir}"];
                    }
                    $pdo = new PDO("sqlite:{$sqlitePath}", null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                    break;
                    
                default:
                    return ['sucesso' => false, 'mensagem' => 'Tipo de banco desconhecido'];
            }

            // simples query para testar
            $pdo->exec('SELECT 1');
            return ['sucesso' => true, 'mensagem' => 'Conexão OK'];
        } catch (\Throwable $e) {
            return ['sucesso' => false, 'mensagem' => $e->getMessage()];
        }
    }

    public function salvar(array $data): array
    {
        $db = Database::getConexao();
        
        // Normalizar tipo_banco: mariadb usa o mesmo driver que mysql
        if (isset($data['tipo_banco']) && strtolower($data['tipo_banco']) === 'mariadb') {
            $data['tipo_banco'] = 'mysql';
        }
        
        // Verificar se é edição (id enviado pelo formulário) ou criação
        $exists = null;
        $isEdit = false;
        if (!empty($data['id'])) {
            $s = $db->prepare('SELECT id, senha_encriptada FROM tb_perfis_conexao WHERE id = ?');
            $s->execute([intval($data['id'])]);
            $exists = $s->fetch(PDO::FETCH_ASSOC);
            $isEdit = !empty($exists);
        }
        
        // Se for edição e não tem senha, buscar a senha existente
        if ($isEdit && empty($data['senha']) && !empty($exists['senha_encriptada'])) {
            $key = $_ENV['ENCRYPTION_KEY'] ?? $_SERVER['ENCRYPTION_KEY'] ?? getenv('ENCRYPTION_KEY') ?: '';
            if ($key) {
                try {
                    $data['senha'] = Crypto::decrypt($exists['senha_encriptada'], $key);
                } catch (\Exception $e) {
                    // Se falhar descriptografia, não testar conexão
                    return ['sucesso' => false, 'mensagem' => 'Erro ao recuperar senha: ' . $e->getMessage()];
                }
            }
        }
        
        // Permitir senha vazia (alguns bancos como MySQL/MariaDB podem ter usuário sem senha)
        // A validação real será feita no teste de conexão
        if (!isset($data['senha'])) {
            $data['senha'] = '';
        }
        
        // Preparar parâmetros extras para tipos específicos de banco
        $parametrosExtras = [];
        
        if ($data['tipo_banco'] === 'oracle') {
            $parametrosExtras['tipo_conexao_oracle'] = $data['tipo_conexao_oracle'] ?? 'sid';
            $parametrosExtras['sid'] = $data['sid'] ?? '';
        } elseif ($data['tipo_banco'] === 'sqlserver') {
            $parametrosExtras['instance_name'] = $data['instance_name'] ?? '';
        } elseif ($data['tipo_banco'] === 'sqlite') {
            $parametrosExtras['sqlite_path'] = $data['sqlite_path'] ?? '';
            $data['nome_banco'] = $parametrosExtras['sqlite_path'];
            $data['host'] = '';
            $data['usuario'] = '';
        } elseif ($data['tipo_banco'] === 'odbc') {
            $parametrosExtras['odbc_dsn'] = $data['odbc_dsn'] ?? '';
            $data['nome_banco'] = $parametrosExtras['odbc_dsn'];
        }
        
        // Regras: testar conexão antes de salvar
        $test = $this->testarConexao($data);
        if (!$test['sucesso']) {
            return ['sucesso' => false, 'mensagem' => 'Teste de conexão falhou: ' . $test['mensagem']];
        }

        // encriptar senha
        $key = $_ENV['ENCRYPTION_KEY'] ?? $_SERVER['ENCRYPTION_KEY'] ?? getenv('ENCRYPTION_KEY') ?: '';
        if (!$key) {
            return ['sucesso' => false, 'mensagem' => 'ENCRYPTION_KEY não definida. Configure .env com ENCRYPTION_KEY (32 bytes base64)'];
        }
        $senhaPlain = $data['senha'] ?? '';
        $senhaEnc = Crypto::encrypt($senhaPlain, $key);

        // Se já existe, atualizar; senão, inserir
        if ($isEdit) {
            $u = $db->prepare('UPDATE tb_perfis_conexao SET nome_conexao=?, tipo_banco=?, host=?, porta=?, nome_banco=?, usuario=?, senha_encriptada=?, parametros_extras=?::jsonb WHERE id=?');
            $u->execute([$data['nome_conexao'], $data['tipo_banco'], $data['host'], $data['porta'] ?: null, $data['nome_banco'] ?: null, $data['usuario'] ?: null, $senhaEnc, json_encode($parametrosExtras), $exists['id']]);
            $idRecurso = (int)$exists['id'];
        } else {
            $ins = $db->prepare('INSERT INTO tb_perfis_conexao (nome_conexao, tipo_banco, host, porta, nome_banco, usuario, senha_encriptada, parametros_extras) VALUES (?, ?, ?, ?, ?, ?, ?, ?::jsonb) RETURNING id');
            $ins->execute([$data['nome_conexao'], $data['tipo_banco'], $data['host'], $data['porta'] ?: null, $data['nome_banco'] ?: null, $data['usuario'] ?: null, $senhaEnc, json_encode($parametrosExtras)]);
            $idRecurso = (int)$ins->fetchColumn();
        }

        // Associar empresas/projetos
        // _rbac_presente indica que os selects estavam visíveis no form
        $rbacPresente = !empty($data['_rbac_presente']);
        if ($rbacPresente) {
            $idsEmpresas = isset($data['empresas']) && is_array($data['empresas']) ? array_map('intval', $data['empresas']) : [];
            $idsProjetos = isset($data['projetos']) && is_array($data['projetos']) ? array_map('intval', $data['projetos']) : [];
            \App\Servicos\ServicoPermissao::associarRecursoEmpresas('conexao', $idRecurso, $idsEmpresas);
            \App\Servicos\ServicoPermissao::associarRecursoProjetos('conexao', $idRecurso, $idsProjetos);
        }

        return ['sucesso' => true, 'mensagem' => $isEdit ? 'Atualizado' : 'Criado', 'id' => $idRecurso];
    }

    public function listar(): array
    {
        $db = Database::getConexao();
        $filtro = \App\Servicos\ServicoPermissao::filtroVisibilidade('conexao', 'c', 'criado_por');
        $sql = "SELECT c.id, c.nome_conexao, c.tipo_banco, c.host, c.porta, c.nome_banco, c.usuario 
                FROM tb_perfis_conexao c WHERE ({$filtro['where']}) ORDER BY c.id DESC";
        $s = $db->prepare($sql);
        $s->execute($filtro['params']);
        $rows = $s->fetchAll(PDO::FETCH_ASSOC);
        return ['data' => $rows];
    }

    public function buscar(int $id): array
    {
        $db = Database::getConexao();
        $s = $db->prepare('SELECT id, nome_conexao, tipo_banco, host, porta, nome_banco, usuario, parametros_extras FROM tb_perfis_conexao WHERE id = ?');
        $s->execute([$id]);
        $r = $s->fetch(PDO::FETCH_ASSOC);
        
        if (!$r) {
            return [];
        }
        
        // Decodificar parametros_extras e mesclar com o resultado
        if (!empty($r['parametros_extras'])) {
            $extras = json_decode($r['parametros_extras'], true);
            if (is_array($extras)) {
                $r = array_merge($r, $extras);
            }
        }
        unset($r['parametros_extras']);
        
        return $r;
    }

    public function deletar(int $id): array
    {
        $db = Database::getConexao();
        $d = $db->prepare('DELETE FROM tb_perfis_conexao WHERE id = ?');
        $d->execute([$id]);
        return ['sucesso' => true];
    }

    /**
     * Testa uma conexão existente pelo ID (busca dados internamente, sem expor senha)
     */
    public function testarConexaoPorId(int $id): array
    {
        $db = Database::getConexao();
        $s = $db->prepare('SELECT tipo_banco, host, porta, nome_banco, usuario, senha_encriptada, parametros_extras FROM tb_perfis_conexao WHERE id = ?');
        $s->execute([$id]);
        $r = $s->fetch(PDO::FETCH_ASSOC);

        if (!$r) {
            return ['sucesso' => false, 'mensagem' => 'Conexão não encontrada'];
        }

        // Descriptografar a senha
        $key = $_ENV['ENCRYPTION_KEY'] ?? $_SERVER['ENCRYPTION_KEY'] ?? getenv('ENCRYPTION_KEY') ?: '';
        $senhaPlain = '';
        if (!empty($r['senha_encriptada']) && $key) {
            try {
                $senhaPlain = Crypto::decrypt($r['senha_encriptada'], $key);
            } catch (\Exception $e) {
                return ['sucesso' => false, 'mensagem' => 'Erro ao descriptografar senha: ' . $e->getMessage()];
            }
        }

        // Montar dados para teste
        $data = [
            'tipo_banco' => $r['tipo_banco'],
            'host' => $r['host'],
            'porta' => $r['porta'],
            'nome_banco' => $r['nome_banco'],
            'usuario' => $r['usuario'],
            'senha' => $senhaPlain,
        ];

        // Mesclar parâmetros extras (SID, instance_name, etc.)
        if (!empty($r['parametros_extras'])) {
            $extras = json_decode($r['parametros_extras'], true);
            if (is_array($extras)) {
                $data = array_merge($data, $extras);
            }
        }

        return $this->testarConexao($data);
    }

    /**
     * Retorna status de todos os drivers PDO
     */
    public function driversStatus(): array
    {
        return [
            'sucesso' => true,
            'php_info' => DriverChecker::getPhpInfo(),
            'drivers' => DriverChecker::getAllDriversStatus()
        ];
    }

    /**
     * Retorna informações de instalação de um driver específico
     */
    public function driverInstallInfo(string $tipoBanco): array
    {
        $info = DriverChecker::getDriverInstallInfo($tipoBanco);
        return [
            'sucesso' => true,
            'driver_info' => $info
        ];
    }

    /**
     * Instala automaticamente um driver
     */
    public function installDriver(string $tipoBanco): array
    {
        try {
            // Verificar se usuário aprovou download
            $autoDownload = isset($_POST['auto_download']) && $_POST['auto_download'] === 'true';
            
            $result = DriverInstaller::install($tipoBanco, $autoDownload);
            return $result;
        } catch (\Exception $e) {
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao instalar driver: ' . $e->getMessage()
            ];
        }
    }
}
