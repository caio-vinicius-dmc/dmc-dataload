<?php
/**
 * DMC DataLoad - Users Controller
 * Gerencia usuários do sistema
 */

namespace App\Controllers;

class UsersController
{
    private $db;
    
    public function __construct()
    {
        $this->db = \App\Core\Database::getConexao();
    }
    
    /**
     * Listar todos os usuários
     */
    public function listar(): void
    {
        header('Content-Type: application/json');
        
        try {
            $sql = "SELECT id, nome_usuario, eh_ldap, nivel_acesso, data_criacao
                    FROM tb_usuarios 
                    ORDER BY nome_usuario";
            
            $stmt = $this->db->query($sql);
            $usuarios = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Estatísticas
            $stats = [
                'total' => count($usuarios),
                'ativos' => count($usuarios), // Todos ativos por enquanto
                'admins' => count(array_filter($usuarios, fn($u) => $u['nivel_acesso'] === 'admin')),
                'ldap' => count(array_filter($usuarios, fn($u) => $u['eh_ldap']))
            ];
            
            echo json_encode([
                'sucesso' => true, 
                'dados' => $usuarios,
                'estatisticas' => $stats
            ]);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
    
    /**
     * Obter usuário por ID
     */
    public function get($id): void
    {
        header('Content-Type: application/json');
        
        try {
            $sql = "SELECT id, nome_usuario, eh_ldap, nivel_acesso, data_criacao
                    FROM tb_usuarios 
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$usuario) {
                throw new \Exception('Usuário não encontrado');
            }
            
            echo json_encode($usuario);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
    
    /**
     * Salvar usuário (criar ou atualizar)
     */
    public function salvar(): void
    {
        header('Content-Type: application/json');
        
        try {
            $id = $_POST['id'] ?? null;
            $nomeUsuario = trim($_POST['nome_usuario'] ?? '');
            $senha = $_POST['senha'] ?? '';
            $nivelAcesso = $_POST['nivel_acesso'] ?? 'user';
            $ehLdap = isset($_POST['eh_ldap']) ? (int)$_POST['eh_ldap'] : 0;
            
            // Validações
            if (empty($nomeUsuario)) {
                throw new \Exception('Nome de usuário é obrigatório');
            }
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $nomeUsuario)) {
                throw new \Exception('Nome de usuário deve conter apenas letras, números e underscore');
            }
            if (!$id && empty($senha) && !$ehLdap) {
                throw new \Exception('Senha é obrigatória para novos usuários');
            }
            if ($senha && strlen($senha) < 6) {
                throw new \Exception('A senha deve ter no mínimo 6 caracteres');
            }
            
            // Verificar nome_usuario único
            $sqlCheck = "SELECT id FROM tb_usuarios WHERE nome_usuario = :nome_usuario";
            if ($id) {
                $sqlCheck .= " AND id != :id";
            }
            $stmtCheck = $this->db->prepare($sqlCheck);
            $params = [':nome_usuario' => $nomeUsuario];
            if ($id) $params[':id'] = $id;
            $stmtCheck->execute($params);
            
            if ($stmtCheck->fetch()) {
                throw new \Exception('Nome de usuário já está em uso');
            }
            
            if ($id) {
                // Atualizar
                $sql = "UPDATE tb_usuarios SET 
                        nome_usuario = :nome_usuario,
                        nivel_acesso = :nivel_acesso,
                        eh_ldap = :eh_ldap";
                
                $params = [
                    ':nome_usuario' => $nomeUsuario,
                    ':nivel_acesso' => $nivelAcesso,
                    ':eh_ldap' => $ehLdap,
                    ':id' => $id
                ];
                
                if ($senha) {
                    $sql .= ", senha_hash = :senha_hash";
                    $params[':senha_hash'] = password_hash($senha, PASSWORD_DEFAULT);
                }
                
                $sql .= " WHERE id = :id";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                
                echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário atualizado com sucesso']);
            } else {
                // Criar
                $sql = "INSERT INTO tb_usuarios (nome_usuario, senha_hash, nivel_acesso, eh_ldap) 
                        VALUES (:nome_usuario, :senha_hash, :nivel_acesso, :eh_ldap) 
                        RETURNING id";
                
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    ':nome_usuario' => $nomeUsuario,
                    ':senha_hash' => $senhaHash,
                    ':nivel_acesso' => $nivelAcesso,
                    ':eh_ldap' => $ehLdap
                ]);
                
                $newId = $stmt->fetchColumn();
                
                echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário criado com sucesso', 'id' => $newId]);
            }
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
    
    /**
     * Excluir usuário
     */
    public function delete($id): void
    {
        header('Content-Type: application/json');
        
        try {
            $sql = "DELETE FROM tb_usuarios WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            if ($stmt->rowCount() === 0) {
                throw new \Exception('Usuário não encontrado');
            }
            
            echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário excluído com sucesso']);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
    
    /**
     * Resetar senha
     */
    public function resetSenha(): void
    {
        header('Content-Type: application/json');
        
        try {
            $id = $_POST['id'] ?? null;
            $senha = $_POST['senha'] ?? '';
            
            if (!$id) {
                throw new \Exception('ID de usuário não fornecido');
            }
            if (empty($senha)) {
                throw new \Exception('Nova senha não fornecida');
            }
            if (strlen($senha) < 6) {
                throw new \Exception('A senha deve ter no mínimo 6 caracteres');
            }
            
            $sql = "UPDATE tb_usuarios SET senha_hash = :senha_hash WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':senha_hash' => password_hash($senha, PASSWORD_DEFAULT),
                ':id' => $id
            ]);
            
            if ($stmt->rowCount() === 0) {
                throw new \Exception('Usuário não encontrado');
            }
            
            echo json_encode(['sucesso' => true, 'mensagem' => 'Senha alterada com sucesso']);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
}
