<?php
namespace App\Controladores;

use App\Core\Database;
use App\Utils\Crypto;
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

        try {
            switch ($tipo) {
                case 'postgres':
                    $port = $porta ?: 5432;
                    $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
                    $pdo = new PDO($dsn, $user, $senha, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                    break;
                case 'mysql':
                    $port = $porta ?: 3306;
                    $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
                    $pdo = new PDO($dsn, $user, $senha, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                    break;
                case 'sqlserver':
                    $port = $porta ?: 1433;
                    $dsn = "sqlsrv:Server={$host},{$port};Database={$db}";
                    $pdo = new PDO($dsn, $user, $senha, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                    break;
                case 'oracle':
                    $port = $porta ?: 1521;
                    $dsn = "oci:dbname=//{$host}:{$port}/{$db}";
                    $pdo = new PDO($dsn, $user, $senha, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                    break;
                case 'odbc':
                    $dsn = $db;
                    $pdo = new PDO("odbc:{$dsn}", $user, $senha, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
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
        
        // Verificar se conexão já existe
        $s = $db->prepare('SELECT id, senha_encriptada FROM tb_perfis_conexao WHERE nome_conexao = ?');
        $s->execute([$data['nome_conexao']]);
        $exists = $s->fetch(PDO::FETCH_ASSOC);
        $isEdit = !empty($exists);
        
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
        
        // Validar se tem senha
        if (empty($data['senha'])) {
            return ['sucesso' => false, 'mensagem' => 'Senha é obrigatória'];
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
            $u = $db->prepare('UPDATE tb_perfis_conexao SET tipo_banco=?, host=?, porta=?, nome_banco=?, usuario=?, senha_encriptada=?, parametros_extras=?::jsonb WHERE id=?');
            $u->execute([$data['tipo_banco'], $data['host'], $data['porta'] ?: null, $data['nome_banco'] ?: null, $data['usuario'] ?: null, $senhaEnc, json_encode($data['parametros_extras'] ?? new \stdClass()), $exists['id']]);
            return ['sucesso' => true, 'mensagem' => 'Atualizado', 'id' => $exists['id']];
        }

        $ins = $db->prepare('INSERT INTO tb_perfis_conexao (nome_conexao, tipo_banco, host, porta, nome_banco, usuario, senha_encriptada, parametros_extras) VALUES (?, ?, ?, ?, ?, ?, ?, ?::jsonb) RETURNING id');
        $ins->execute([$data['nome_conexao'], $data['tipo_banco'], $data['host'], $data['porta'] ?: null, $data['nome_banco'] ?: null, $data['usuario'] ?: null, $senhaEnc, json_encode($data['parametros_extras'] ?? new \stdClass())]);
        $id = $ins->fetchColumn();
        return ['sucesso' => true, 'mensagem' => 'Criado', 'id' => $id];
    }

    public function listar(): array
    {
        $db = Database::getConexao();
        $s = $db->query('SELECT id, nome_conexao, tipo_banco, host, porta, nome_banco, usuario FROM tb_perfis_conexao ORDER BY id DESC');
        $rows = $s->fetchAll(PDO::FETCH_ASSOC);
        return ['data' => $rows];
    }

    public function buscar(int $id): array
    {
        $db = Database::getConexao();
        $s = $db->prepare('SELECT id, nome_conexao, tipo_banco, host, porta, nome_banco, usuario, senha_encriptada, parametros_extras FROM tb_perfis_conexao WHERE id = ?');
        $s->execute([$id]);
        $r = $s->fetch(PDO::FETCH_ASSOC);
        return $r ?: [];
    }

    public function deletar(int $id): array
    {
        $db = Database::getConexao();
        $d = $db->prepare('DELETE FROM tb_perfis_conexao WHERE id = ?');
        $d->execute([$id]);
        return ['sucesso' => true];
    }
}
