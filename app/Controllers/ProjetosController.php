<?php
/**
 * DMC DataLoad - Projetos Controller
 * CRUD de projetos — Admins podem criar dentro das suas empresas
 */

namespace App\Controllers;

use App\Core\Database;
use App\Core\AuthMiddleware;
use App\Servicos\ServicoPermissao;

class ProjetosController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getConexao();
    }

    /**
     * Listar projetos visíveis ao usuário
     */
    public function listar(): void
    {
        header('Content-Type: application/json');

        try {
            $idUsuario = AuthMiddleware::obterUsuarioId();

            if (ServicoPermissao::ehSuperAdmin()) {
                $sql = "SELECT p.*, e.nome as empresa_nome,
                        (SELECT COUNT(*) FROM tb_usuario_projetos up WHERE up.id_projeto = p.id) as total_usuarios
                        FROM tb_projetos p
                        JOIN tb_empresas e ON e.id = p.id_empresa
                        ORDER BY e.nome, p.nome";
                $stmt = $this->db->query($sql);
            } else {
                $sql = "SELECT p.*, e.nome as empresa_nome,
                        (SELECT COUNT(*) FROM tb_usuario_projetos up WHERE up.id_projeto = p.id) as total_usuarios
                        FROM tb_projetos p
                        JOIN tb_empresas e ON e.id = p.id_empresa
                        JOIN tb_usuario_empresas ue ON ue.id_empresa = p.id_empresa AND ue.id_usuario = :uid
                        ORDER BY e.nome, p.nome";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':uid' => $idUsuario]);
            }

            $projetos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            echo json_encode(['sucesso' => true, 'dados' => $projetos]);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Obter projeto por ID
     */
    public function get(int $id): void
    {
        header('Content-Type: application/json');

        try {
            $stmt = $this->db->prepare("
                SELECT p.*, e.nome as empresa_nome
                FROM tb_projetos p
                JOIN tb_empresas e ON e.id = p.id_empresa
                WHERE p.id = :id
            ");
            $stmt->execute([':id' => $id]);
            $projeto = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$projeto) {
                throw new \Exception('Projeto não encontrado');
            }

            // Verificar acesso
            if (!ServicoPermissao::ehSuperAdmin() && !ServicoPermissao::usuarioPertenceEmpresa($projeto['id_empresa'])) {
                throw new \Exception('Sem acesso a este projeto');
            }

            // Carregar usuários associados
            $stmtUsers = $this->db->prepare("
                SELECT u.id, u.nome_usuario, u.nivel_acesso
                FROM tb_usuarios u
                JOIN tb_usuario_projetos up ON up.id_usuario = u.id
                WHERE up.id_projeto = :id
                ORDER BY u.nome_usuario
            ");
            $stmtUsers->execute([':id' => $id]);
            $projeto['usuarios'] = $stmtUsers->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode(['sucesso' => true, 'dados' => $projeto]);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Criar ou atualizar projeto
     */
    public function salvar(): void
    {
        header('Content-Type: application/json');

        try {
            if (!ServicoPermissao::temNivelMinimo('admin')) {
                throw new \Exception('Apenas Administradores podem gerenciar projetos');
            }

            $id = $_POST['id'] ?? null;
            $nome = trim($_POST['nome'] ?? '');
            $descricao = trim($_POST['descricao'] ?? '');
            $idEmpresa = (int)($_POST['id_empresa'] ?? 0);
            $ativo = isset($_POST['ativo']) ? (bool)$_POST['ativo'] : true;

            if (empty($nome)) {
                throw new \Exception('Nome do projeto é obrigatório');
            }
            if (!$idEmpresa) {
                throw new \Exception('Empresa é obrigatória');
            }

            // Verifica se o admin pertence à empresa
            if (!ServicoPermissao::ehSuperAdmin() && !ServicoPermissao::usuarioPertenceEmpresa($idEmpresa)) {
                throw new \Exception('Você não faz parte desta empresa');
            }

            // Unicidade nome+empresa
            $sqlCheck = "SELECT id FROM tb_projetos WHERE nome = :nome AND id_empresa = :eid";
            $params = [':nome' => $nome, ':eid' => $idEmpresa];
            if ($id) {
                $sqlCheck .= " AND id != :id";
                $params[':id'] = $id;
            }
            $stmtCheck = $this->db->prepare($sqlCheck);
            $stmtCheck->execute($params);
            if ($stmtCheck->fetch()) {
                throw new \Exception('Já existe um projeto com este nome nesta empresa');
            }

            if ($id) {
                // Verificar se o projeto existente pertence à empresa do admin
                $stmtOld = $this->db->prepare("SELECT id_empresa FROM tb_projetos WHERE id = :id");
                $stmtOld->execute([':id' => $id]);
                $old = $stmtOld->fetch(\PDO::FETCH_ASSOC);
                if ($old && !ServicoPermissao::ehSuperAdmin() && !ServicoPermissao::usuarioPertenceEmpresa($old['id_empresa'])) {
                    throw new \Exception('Sem permissão para editar este projeto');
                }

                $stmt = $this->db->prepare("
                    UPDATE tb_projetos 
                    SET nome = :nome, descricao = :desc, id_empresa = :eid, ativo = :ativo, data_atualizacao = NOW()
                    WHERE id = :id
                ");
                $stmt->execute([':nome' => $nome, ':desc' => $descricao, ':eid' => $idEmpresa, ':ativo' => $ativo ? 'true' : 'false', ':id' => $id]);
                echo json_encode(['sucesso' => true, 'mensagem' => 'Projeto atualizado com sucesso']);
            } else {
                $idCriador = AuthMiddleware::obterUsuarioId();
                $stmt = $this->db->prepare("
                    INSERT INTO tb_projetos (nome, descricao, id_empresa, ativo, criado_por)
                    VALUES (:nome, :desc, :eid, :ativo, :criador) RETURNING id
                ");
                $stmt->execute([':nome' => $nome, ':desc' => $descricao, ':eid' => $idEmpresa, ':ativo' => $ativo ? 'true' : 'false', ':criador' => $idCriador]);
                $newId = $stmt->fetchColumn();
                echo json_encode(['sucesso' => true, 'mensagem' => 'Projeto criado com sucesso', 'id' => $newId]);
            }
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Excluir projeto
     */
    public function delete(int $id): void
    {
        header('Content-Type: application/json');

        try {
            if (!ServicoPermissao::temNivelMinimo('admin')) {
                throw new \Exception('Apenas Administradores podem excluir projetos');
            }

            // Verificar acesso
            $stmtCheck = $this->db->prepare("SELECT id_empresa FROM tb_projetos WHERE id = :id");
            $stmtCheck->execute([':id' => $id]);
            $projeto = $stmtCheck->fetch(\PDO::FETCH_ASSOC);

            if (!$projeto) {
                throw new \Exception('Projeto não encontrado');
            }

            if (!ServicoPermissao::ehSuperAdmin() && !ServicoPermissao::usuarioPertenceEmpresa($projeto['id_empresa'])) {
                throw new \Exception('Sem permissão para excluir este projeto');
            }

            $stmt = $this->db->prepare("DELETE FROM tb_projetos WHERE id = :id");
            $stmt->execute([':id' => $id]);

            echo json_encode(['sucesso' => true, 'mensagem' => 'Projeto excluído com sucesso']);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
}
