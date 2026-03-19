<?php
/**
 * DMC DataLoad - Empresas Controller
 * CRUD de empresas — somente Super Administrador pode criar/editar/excluir
 */

namespace App\Controllers;

use App\Core\Database;
use App\Core\AuthMiddleware;
use App\Servicos\ServicoPermissao;
use App\Servicos\ServicoAuditoria;

class EmpresasController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getConexao();
    }

    /**
     * Listar todas as empresas
     */
    public function listar(): void
    {
        header('Content-Type: application/json');

        try {
            $usuario = AuthMiddleware::obterUsuario();
            $idUsuario = AuthMiddleware::obterUsuarioId();

            if (ServicoPermissao::ehSuperAdmin()) {
                // Super admin vê todas
                $sql = "SELECT e.*, 
                        (SELECT COUNT(*) FROM tb_usuario_empresas ue WHERE ue.id_empresa = e.id) as total_usuarios,
                        (SELECT COUNT(*) FROM tb_projetos p WHERE p.id_empresa = e.id) as total_projetos
                        FROM tb_empresas e ORDER BY e.nome";
                $stmt = $this->db->query($sql);
            } else {
                // Admin vê apenas as suas
                $sql = "SELECT e.*, 
                        (SELECT COUNT(*) FROM tb_usuario_empresas ue WHERE ue.id_empresa = e.id) as total_usuarios,
                        (SELECT COUNT(*) FROM tb_projetos p WHERE p.id_empresa = e.id) as total_projetos
                        FROM tb_empresas e
                        JOIN tb_usuario_empresas ue2 ON ue2.id_empresa = e.id AND ue2.id_usuario = :uid
                        ORDER BY e.nome";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':uid' => $idUsuario]);
            }

            $empresas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode(['sucesso' => true, 'dados' => $empresas]);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Obter empresa por ID
     */
    public function get(int $id): void
    {
        header('Content-Type: application/json');

        try {
            $stmt = $this->db->prepare("
                SELECT e.*,
                (SELECT COUNT(*) FROM tb_usuario_empresas ue WHERE ue.id_empresa = e.id) as total_usuarios,
                (SELECT COUNT(*) FROM tb_projetos p WHERE p.id_empresa = e.id) as total_projetos
                FROM tb_empresas e WHERE e.id = :id
            ");
            $stmt->execute([':id' => $id]);
            $empresa = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$empresa) {
                throw new \Exception('Empresa não encontrada');
            }

            // Carregar usuários associados
            $stmtUsers = $this->db->prepare("
                SELECT u.id, u.nome_usuario, u.nivel_acesso
                FROM tb_usuarios u
                JOIN tb_usuario_empresas ue ON ue.id_usuario = u.id
                WHERE ue.id_empresa = :id
                ORDER BY u.nome_usuario
            ");
            $stmtUsers->execute([':id' => $id]);
            $empresa['usuarios'] = $stmtUsers->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode(['sucesso' => true, 'dados' => $empresa]);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Criar ou atualizar empresa (somente super_admin)
     */
    public function salvar(): void
    {
        header('Content-Type: application/json');

        try {
            if (!ServicoPermissao::ehSuperAdmin()) {
                throw new \Exception('Apenas o Super Administrador pode gerenciar empresas');
            }

            $id = $_POST['id'] ?? null;
            $nome = trim($_POST['nome'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $ativa = isset($_POST['ativa']) ? (bool)$_POST['ativa'] : true;

            if (empty($nome)) {
                throw new \Exception('Nome da empresa é obrigatório');
            }

            // Unicidade do nome
            $sqlCheck = "SELECT id FROM tb_empresas WHERE nome = :nome";
            $params = [':nome' => $nome];
            if ($id) {
                $sqlCheck .= " AND id != :id";
                $params[':id'] = $id;
            }
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute($params);
            if ($stmtCheck->fetch()) {
                throw new \Exception('Já existe uma empresa com este nome');
            }

            if ($id) {
                $stmt = $this->db->prepare("
                    UPDATE tb_empresas 
                    SET nome = :nome, descricao = :desc, ativa = :ativa, data_atualizacao = NOW()
                    WHERE id = :id
                ");
                $stmt->execute([':nome' => $nome, ':desc' => $descricao, ':ativa' => $ativa ? 'true' : 'false', ':id' => $id]);
                ServicoAuditoria::registrar('editar', 'empresa', (int)$id, $nome);
                echo json_encode(['sucesso' => true, 'mensagem' => 'Empresa atualizada com sucesso']);
            } else {
                $idCriador = AuthMiddleware::obterUsuarioId();
                $stmt = $this->db->prepare("
                    INSERT INTO tb_empresas (nome, descricao, ativa, criado_por)
                    VALUES (:nome, :desc, :ativa, :criador) RETURNING id
                ");
                $stmt->execute([':nome' => $nome, ':desc' => $descricao, ':ativa' => $ativa ? 'true' : 'false', ':criador' => $idCriador]);
                $newId = $stmt->fetchColumn();
                ServicoAuditoria::registrar('criar', 'empresa', (int)$newId, $nome);
                echo json_encode(['sucesso' => true, 'mensagem' => 'Empresa criada com sucesso', 'id' => $newId]);
            }
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Excluir empresa (somente super_admin)
     */
    public function delete(int $id): void
    {
        header('Content-Type: application/json');

        try {
            if (!ServicoPermissao::ehSuperAdmin()) {
                throw new \Exception('Apenas o Super Administrador pode excluir empresas');
            }

            $stmtNome = $this->db->prepare("SELECT nome FROM tb_empresas WHERE id = :id");
            $stmtNome->execute([':id' => $id]);
            $nomeEmpresa = $stmtNome->fetchColumn();

            $stmt = $this->db->prepare("DELETE FROM tb_empresas WHERE id = :id");
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() === 0) {
                throw new \Exception('Empresa não encontrada');
            }

            ServicoAuditoria::registrar('excluir', 'empresa', $id, $nomeEmpresa ?: 'ID:' . $id);
            echo json_encode(['sucesso' => true, 'mensagem' => 'Empresa excluída com sucesso']);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
}
