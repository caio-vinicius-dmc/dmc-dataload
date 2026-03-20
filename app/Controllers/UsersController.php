<?php
/**
 * DMC DataLoad - Users Controller
 * Gerencia usuários do sistema com RBAC
 */

namespace App\Controllers;

use App\Core\AuthMiddleware;
use App\Servicos\ServicoPermissao;
use App\Servicos\ServicoAuditoria;

class UsersController
{
    private $db;
    private int $usuarioLogadoId;
    private string $nivelLogado;
    
    public function __construct()
    {
        $this->db = \App\Core\Database::getConexao();
        $this->usuarioLogadoId = AuthMiddleware::obterUsuarioId();
        $this->nivelLogado = AuthMiddleware::obterUsuario()['nivel_acesso'] ?? 'operador';
    }
    
    /**
     * Listar usuários (filtrado por permissão)
     */
    public function listar(): void
    {
        header('Content-Type: application/json');
        
        try {
            if ($this->nivelLogado === 'super_admin') {
                // Super admin vê todos
                $sql = "SELECT id, nome_usuario, eh_ldap, nivel_acesso, data_criacao, bloqueado_ate
                        FROM tb_usuarios ORDER BY nome_usuario";
                $stmt = $this->db->query($sql);
            } else {
                // Admin vê apenas usuários de suas empresas
                $sql = "SELECT DISTINCT u.id, u.nome_usuario, u.eh_ldap, u.nivel_acesso, u.data_criacao, u.bloqueado_ate
                        FROM tb_usuarios u
                        INNER JOIN tb_usuario_empresas ue ON u.id = ue.id_usuario
                        WHERE ue.id_empresa IN (
                            SELECT id_empresa FROM tb_usuario_empresas WHERE id_usuario = :uid
                        )
                        ORDER BY u.nome_usuario";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':uid' => $this->usuarioLogadoId]);
            }
            
            $usuarios = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Buscar empresas de cada usuário
            foreach ($usuarios as &$u) {
                $stmtEmp = $this->db->prepare(
                    "SELECT e.id, e.nome FROM tb_empresas e 
                     INNER JOIN tb_usuario_empresas ue ON e.id = ue.id_empresa 
                     WHERE ue.id_usuario = :uid ORDER BY e.nome"
                );
                $stmtEmp->execute([':uid' => $u['id']]);
                $u['empresas'] = $stmtEmp->fetchAll(\PDO::FETCH_ASSOC);
            }
            
            echo json_encode(['sucesso' => true, 'dados' => $usuarios]);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
    
    /**
     * Obter usuário por ID com empresas e projetos
     */
    public function get($id): void
    {
        header('Content-Type: application/json');
        
        try {
            // Verificar permissão para ver este usuário
            if (!$this->podeGerenciarUsuario((int)$id)) {
                throw new \Exception('Sem permissão para acessar este usuário');
            }
            
            $stmt = $this->db->prepare(
                "SELECT id, nome_usuario, nome, email, cpf, eh_ldap, nivel_acesso, data_criacao, bloqueado_ate
                 FROM tb_usuarios WHERE id = :id"
            );
            $stmt->execute([':id' => $id]);
            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$usuario) {
                throw new \Exception('Usuário não encontrado');
            }
            
            // Buscar empresas
            $stmtEmp = $this->db->prepare(
                "SELECT e.id, e.nome FROM tb_empresas e
                 INNER JOIN tb_usuario_empresas ue ON e.id = ue.id_empresa
                 WHERE ue.id_usuario = :uid ORDER BY e.nome"
            );
            $stmtEmp->execute([':uid' => $id]);
            $usuario['empresas'] = $stmtEmp->fetchAll(\PDO::FETCH_ASSOC);
            
            // Buscar projetos
            $stmtProj = $this->db->prepare(
                "SELECT p.id, p.nome, e.nome as empresa_nome FROM tb_projetos p
                 INNER JOIN tb_usuario_projetos up ON p.id = up.id_projeto
                 INNER JOIN tb_empresas e ON p.id_empresa = e.id
                 WHERE up.id_usuario = :uid ORDER BY e.nome, p.nome"
            );
            $stmtProj->execute([':uid' => $id]);
            $usuario['projetos'] = $stmtProj->fetchAll(\PDO::FETCH_ASSOC);
            
            echo json_encode($usuario);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
    
    /**
     * Salvar usuário (criar ou atualizar) com associações empresa/projeto
     */
    public function salvar(): void
    {
        header('Content-Type: application/json');
        
        try {
            $id = !empty($_POST['id']) ? (int)$_POST['id'] : null;
            $nomeUsuario = trim($_POST['nome_usuario'] ?? '');
            $senha = $_POST['senha'] ?? '';
            $nivelAcesso = $_POST['nivel_acesso'] ?? 'operador';
            $ehLdap = isset($_POST['eh_ldap']) ? 1 : 0;
            $empresas = $_POST['empresas'] ?? [];
            $projetos = $_POST['projetos'] ?? [];
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $cpf = trim($_POST['cpf'] ?? '');
            
            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('E-mail inválido');
            }
            if ($cpf && !preg_match('/^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$/', $cpf)) {
                throw new \Exception('CPF inválido');
            }
            
            // Validar nível de acesso permitido
            $papeisPermitidos = ServicoPermissao::papeisDisponiveis();
            if (!in_array($nivelAcesso, $papeisPermitidos)) {
                throw new \Exception('Nível de acesso não permitido');
            }
            
            // Validações básicas
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
            
            // Se editando, verificar permissão
            if ($id) {
                if (!$this->podeGerenciarUsuario($id)) {
                    throw new \Exception('Sem permissão para editar este usuário');
                }
                // Não pode alterar super_admin
                $stmtCheck = $this->db->prepare("SELECT nivel_acesso FROM tb_usuarios WHERE id = :id");
                $stmtCheck->execute([':id' => $id]);
                $nivelAtual = $stmtCheck->fetchColumn();
                if ($nivelAtual === 'super_admin') {
                    throw new \Exception('Não é possível editar o Super Administrador');
                }
            }
            
            // Validar empresas (admin só pode atribuir suas próprias empresas)
            if ($this->nivelLogado !== 'super_admin' && !empty($empresas)) {
                $stmtMinhas = $this->db->prepare(
                    "SELECT id_empresa FROM tb_usuario_empresas WHERE id_usuario = :uid"
                );
                $stmtMinhas->execute([':uid' => $this->usuarioLogadoId]);
                $minhasEmpresas = array_column($stmtMinhas->fetchAll(\PDO::FETCH_ASSOC), 'id_empresa');
                
                foreach ($empresas as $empId) {
                    if (!in_array((int)$empId, array_map('intval', $minhasEmpresas))) {
                        throw new \Exception('Você não tem permissão para atribuir esta empresa');
                    }
                }
            }
            
            // Verificar nome_usuario único
            $sqlCheck = "SELECT id FROM tb_usuarios WHERE nome_usuario = :nome_usuario";
            $paramCheck = [':nome_usuario' => $nomeUsuario];
            if ($id) {
                $sqlCheck .= " AND id != :id";
                $paramCheck[':id'] = $id;
            }
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute($paramCheck);
            if ($stmtCheck->fetch()) {
                throw new \Exception('Nome de usuário já está em uso');
            }
            
            $this->db->beginTransaction();
            
            if ($id) {
                // Atualizar
                $sql = "UPDATE tb_usuarios SET 
                        nome_usuario = :nome_usuario,
                        nome = :nome,
                        email = :email,
                        cpf = :cpf,
                        nivel_acesso = :nivel_acesso,
                        eh_ldap = :eh_ldap";
                $params = [
                    ':nome_usuario' => $nomeUsuario,
                    ':nome' => $nome ?: null,
                    ':email' => $email ?: null,
                    ':cpf' => $cpf ?: null,
                    ':nivel_acesso' => $nivelAcesso,
                    ':eh_ldap' => $ehLdap,
                    ':id' => $id
                ];
                if ($senha) {
                    $sql .= ", senha_hash = :senha_hash";
                    $params[':senha_hash'] = password_hash($senha, PASSWORD_DEFAULT);
                }
                $sql .= " WHERE id = :id";
                $this->db->prepare($sql)->execute($params);
                
                $userId = $id;
            } else {
                // Criar
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $this->db->prepare(
                    "INSERT INTO tb_usuarios (nome_usuario, nome, email, cpf, senha_hash, nivel_acesso, eh_ldap) 
                     VALUES (:nome_usuario, :nome, :email, :cpf, :senha_hash, :nivel_acesso, :eh_ldap) RETURNING id"
                );
                $stmt->execute([
                    ':nome_usuario' => $nomeUsuario,
                    ':nome' => $nome ?: null,
                    ':email' => $email ?: null,
                    ':cpf' => $cpf ?: null,
                    ':senha_hash' => $senhaHash,
                    ':nivel_acesso' => $nivelAcesso,
                    ':eh_ldap' => $ehLdap
                ]);
                $userId = $stmt->fetchColumn();
            }
            
            // Atualizar associações empresas
            $this->db->prepare("DELETE FROM tb_usuario_empresas WHERE id_usuario = :uid")
                     ->execute([':uid' => $userId]);
            if (!empty($empresas)) {
                $stmtIns = $this->db->prepare(
                    "INSERT INTO tb_usuario_empresas (id_usuario, id_empresa) VALUES (:uid, :eid)
                     ON CONFLICT (id_usuario, id_empresa) DO NOTHING"
                );
                foreach ($empresas as $empId) {
                    $stmtIns->execute([':uid' => $userId, ':eid' => (int)$empId]);
                }
            }
            
            // Atualizar associações projetos
            $this->db->prepare("DELETE FROM tb_usuario_projetos WHERE id_usuario = :uid")
                     ->execute([':uid' => $userId]);
            if (!empty($projetos)) {
                $stmtIns = $this->db->prepare(
                    "INSERT INTO tb_usuario_projetos (id_usuario, id_projeto) VALUES (:uid, :pid)
                     ON CONFLICT (id_usuario, id_projeto) DO NOTHING"
                );
                foreach ($projetos as $projId) {
                    $stmtIns->execute([':uid' => $userId, ':pid' => (int)$projId]);
                }
            }
            
            $this->db->commit();
            
            ServicoAuditoria::registrar($id ? 'editar' : 'criar', 'usuario', $userId, $nomeUsuario, [], ['nivel_acesso' => $nivelAcesso]);
            
            echo json_encode([
                'sucesso' => true, 
                'mensagem' => $id ? 'Usuário atualizado com sucesso' : 'Usuário criado com sucesso',
                'id' => $userId
            ]);
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
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
            if (!$this->podeGerenciarUsuario((int)$id)) {
                throw new \Exception('Sem permissão para excluir este usuário');
            }
            
            // Não pode excluir super_admin
            $stmt = $this->db->prepare("SELECT nivel_acesso FROM tb_usuarios WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $nivel = $stmt->fetchColumn();
            if ($nivel === 'super_admin') {
                throw new \Exception('Não é possível excluir o Super Administrador');
            }
            
            // Não pode se auto-excluir
            if ((int)$id === $this->usuarioLogadoId) {
                throw new \Exception('Não é possível excluir seu próprio usuário');
            }
            
            // Buscar nome antes de excluir
            $stmtNome = $this->db->prepare("SELECT nome_usuario FROM tb_usuarios WHERE id = :id");
            $stmtNome->execute([':id' => $id]);
            $nomeExcluido = $stmtNome->fetchColumn();

            $stmt = $this->db->prepare("DELETE FROM tb_usuarios WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            if ($stmt->rowCount() === 0) {
                throw new \Exception('Usuário não encontrado');
            }
            
            ServicoAuditoria::registrar('excluir', 'usuario', (int)$id, $nomeExcluido ?: 'ID:' . $id);
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
            if (!$this->podeGerenciarUsuario((int)$id)) {
                throw new \Exception('Sem permissão para alterar senha deste usuário');
            }
            if (empty($senha)) {
                throw new \Exception('Nova senha não fornecida');
            }
            if (strlen($senha) < 6) {
                throw new \Exception('A senha deve ter no mínimo 6 caracteres');
            }
            
            $stmt = $this->db->prepare("UPDATE tb_usuarios SET senha_hash = :senha_hash WHERE id = :id");
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

    /**
     * Resetar senha via e-mail (admin envia link de redefinição)
     */
    public function resetSenhaEmail(): void
    {
        header('Content-Type: application/json');
        
        try {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                throw new \Exception('ID de usuário não fornecido');
            }
            if (!$this->podeGerenciarUsuario((int)$id)) {
                throw new \Exception('Sem permissão para resetar senha deste usuário');
            }
            
            $svc = new \App\Servicos\ServicoRecuperacaoSenha();
            $resultado = $svc->solicitarPorAdmin((int)$id);
            echo json_encode($resultado);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Desbloquear usuário (admin)
     */
    public function desbloquearUsuario(): void
    {
        header('Content-Type: application/json');
        
        try {
            $id = $_POST['id'] ?? null;
            if (!$id) {
                throw new \Exception('ID de usuário não fornecido');
            }
            if (!$this->podeGerenciarUsuario((int)$id)) {
                throw new \Exception('Sem permissão para desbloquear este usuário');
            }
            
            $stmt = $this->db->prepare("UPDATE tb_usuarios SET bloqueado_ate = NULL WHERE id = :id");
            $stmt->execute([':id' => $id]);
            
            // Limpar rate limits do usuário
            $stmtClear = $this->db->prepare("DELETE FROM tb_rate_limits WHERE chave = :chave");
            $stmtClear->execute([':chave' => 'login_user:' . $id]);
            
            echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário desbloqueado com sucesso']);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
    
    /**
     * Verifica se o usuário logado pode gerenciar o usuário alvo
     */
    private function podeGerenciarUsuario(int $idAlvo): bool
    {
        if ($this->nivelLogado === 'super_admin') return true;
        if ($this->nivelLogado !== 'admin') return false;
        
        // Buscar nível do alvo
        $stmt = $this->db->prepare("SELECT nivel_acesso FROM tb_usuarios WHERE id = :id");
        $stmt->execute([':id' => $idAlvo]);
        $nivelAlvo = $stmt->fetchColumn();
        
        // Admin não pode gerenciar outro admin ou super_admin
        if (in_array($nivelAlvo, ['admin', 'super_admin'])) return false;
        
        // Admin só pode gerenciar usuários de suas empresas
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM tb_usuario_empresas ue1
             INNER JOIN tb_usuario_empresas ue2 ON ue1.id_empresa = ue2.id_empresa
             WHERE ue1.id_usuario = :admin AND ue2.id_usuario = :alvo"
        );
        $stmt->execute([':admin' => $this->usuarioLogadoId, ':alvo' => $idAlvo]);
        return (int)$stmt->fetchColumn() > 0;
    }

    // ================================================================
    // Perfil do Usuário Logado
    // ================================================================

    /**
     * Retorna dados do perfil do usuário logado
     */
    public function meuPerfil(): void
    {
        header('Content-Type: application/json');

        try {
            $id = $this->usuarioLogadoId;

            $stmt = $this->db->prepare(
                "SELECT id, nome_usuario, nome, email, cpf, eh_ldap, nivel_acesso, data_criacao
                 FROM tb_usuarios WHERE id = :id"
            );
            $stmt->execute([':id' => $id]);
            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$usuario) {
                throw new \Exception('Usuário não encontrado');
            }

            // Empresas
            $stmtEmp = $this->db->prepare(
                "SELECT e.id, e.nome FROM tb_empresas e
                 INNER JOIN tb_usuario_empresas ue ON e.id = ue.id_empresa
                 WHERE ue.id_usuario = :uid ORDER BY e.nome"
            );
            $stmtEmp->execute([':uid' => $id]);
            $usuario['empresas'] = $stmtEmp->fetchAll(\PDO::FETCH_ASSOC);

            // Projetos
            $stmtProj = $this->db->prepare(
                "SELECT p.id, p.nome, e.nome as empresa_nome FROM tb_projetos p
                 INNER JOIN tb_usuario_projetos up ON p.id = up.id_projeto
                 INNER JOIN tb_empresas e ON p.id_empresa = e.id
                 WHERE up.id_usuario = :uid ORDER BY e.nome, p.nome"
            );
            $stmtProj->execute([':uid' => $id]);
            $usuario['projetos'] = $stmtProj->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode(['sucesso' => true, 'dados' => $usuario]);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Atualizar perfil do próprio usuário (campos permitidos: nome, email)
     */
    public function atualizarPerfil(): void
    {
        header('Content-Type: application/json');

        try {
            $id = $this->usuarioLogadoId;
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');

            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('E-mail inválido');
            }

            $stmt = $this->db->prepare(
                "UPDATE tb_usuarios SET nome = :nome, email = :email WHERE id = :id"
            );
            $stmt->execute([
                ':nome'  => $nome ?: null,
                ':email' => $email ?: null,
                ':id'    => $id
            ]);

            echo json_encode(['sucesso' => true, 'mensagem' => 'Perfil atualizado com sucesso']);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Alterar própria senha
     */
    public function alterarMinhaSenha(): void
    {
        header('Content-Type: application/json');

        try {
            $id = $this->usuarioLogadoId;
            $senhaAtual  = $_POST['senha_atual'] ?? '';
            $senhaNova   = $_POST['senha_nova'] ?? '';
            $senhaConfirm = $_POST['senha_confirmar'] ?? '';

            if (empty($senhaAtual) || empty($senhaNova)) {
                throw new \Exception('Preencha todos os campos de senha');
            }
            if ($senhaNova !== $senhaConfirm) {
                throw new \Exception('A nova senha e a confirmação não coincidem');
            }
            if (strlen($senhaNova) < 6) {
                throw new \Exception('A nova senha deve ter no mínimo 6 caracteres');
            }

            // Verificar senha atual
            $stmt = $this->db->prepare("SELECT senha_hash FROM tb_usuarios WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $hash = $stmt->fetchColumn();

            if (!$hash || !password_verify($senhaAtual, $hash)) {
                throw new \Exception('Senha atual incorreta');
            }

            $stmt = $this->db->prepare("UPDATE tb_usuarios SET senha_hash = :hash WHERE id = :id");
            $stmt->execute([
                ':hash' => password_hash($senhaNova, PASSWORD_DEFAULT),
                ':id'   => $id
            ]);

            echo json_encode(['sucesso' => true, 'mensagem' => 'Senha alterada com sucesso']);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
}
