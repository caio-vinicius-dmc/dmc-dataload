<?php
namespace App\Servicos;

use App\Core\Database;
use PDO;

class ServicoAutenticacao
{
    public function autenticar(string $usuario, string $senha): array
    {
        // Tentar LDAP primeiro (se configurado)
        $ldapHost = getenv('LDAP_HOST') ?: null;
        if ($ldapHost) {
            try {
                $conn = ldap_connect($ldapHost);
                if ($conn) {
                    ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
                    // Bind anônimo para buscar DN; comportamento depende do AD
                    $bind = @ldap_bind($conn, $usuario, $senha);
                    if ($bind) {
                        // Auto-provisioning na base local
                        $db = Database::getConexao();
                        $stmt = $db->prepare('SELECT * FROM tb_usuarios WHERE nome_usuario = ?');
                        $stmt->execute([$usuario]);
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        if (!$row) {
                            $ins = $db->prepare('INSERT INTO tb_usuarios (nome_usuario, senha_hash, eh_ldap, nivel_acesso) VALUES (?, NULL, true, ?) RETURNING id');
                            $ins->execute([$usuario, 'user']);
                            $id = $ins->fetchColumn();
                            $row = ['id' => $id, 'nome_usuario' => $usuario, 'nivel_acesso' => 'user'];
                        }
                        unset($row['senha_hash']);
                        return ['sucesso' => true, 'metodo' => 'ldap', 'usuario' => $row];
                    }
                }
            } catch (\Throwable $e) {
                // falha ldap — continuará para autenticação local
            }
        }

        // Autenticação local por hash (assume password_hash)
        $db = Database::getConexao();
        $stmt = $db->prepare('SELECT * FROM tb_usuarios WHERE nome_usuario = ?');
        $stmt->execute([$usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) return ['sucesso' => false, 'mensagem' => 'Usuário não encontrado'];

        if (empty($user['senha_hash'])) {
            return ['sucesso' => false, 'mensagem' => 'Usuário configurado apenas para LDAP'];
        }

        if (password_verify($senha, $user['senha_hash'])) {
            // Remover senha do array antes de retornar
            unset($user['senha_hash']);
            return ['sucesso' => true, 'metodo' => 'local', 'usuario' => $user];
        }

        return ['sucesso' => false, 'mensagem' => 'Senha inválida'];
    }
}
