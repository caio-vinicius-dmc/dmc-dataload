<?php

namespace App\Servicos;

use App\Core\Database;

class ServicoNotificacao
{
    /**
     * Cria uma notificação no sistema para um usuário específico (ou global se null)
     */
    public static function criar(string $tipo, string $titulo, string $mensagem, array $dados = [], ?int $idUsuario = null): void
    {
        try {
            $db = Database::getConexao();
            $stmt = $db->prepare(
                "INSERT INTO tb_notificacoes (tipo, titulo, mensagem, dados, lida, id_usuario, created_at)
                 VALUES (?, ?, ?, ?::jsonb, false, ?, NOW())"
            );
            $stmt->execute([$tipo, $titulo, $mensagem, json_encode($dados), $idUsuario]);
        } catch (\Throwable $e) {
            error_log("Erro ao criar notificação: " . $e->getMessage());
        }
    }

    /**
     * Cria notificações para todos os usuários que pertencem às empresas/projetos do recurso
     */
    public static function criarParaUsuariosDoRecurso(string $tipoRecurso, int $idRecurso, string $tipo, string $titulo, string $mensagem, array $dados = []): void
    {
        try {
            $usuarios = self::obterUsuariosDoRecurso($tipoRecurso, $idRecurso);
            if (empty($usuarios)) {
                // Fallback: notificação global
                self::criar($tipo, $titulo, $mensagem, $dados, null);
                return;
            }
            foreach ($usuarios as $idUsuario) {
                self::criar($tipo, $titulo, $mensagem, $dados, $idUsuario);
            }
        } catch (\Throwable $e) {
            error_log("Erro ao criar notificações por recurso: " . $e->getMessage());
            self::criar($tipo, $titulo, $mensagem, $dados, null);
        }
    }

    /**
     * Obtém IDs de usuários que pertencem às empresas e projetos do recurso
     */
    private static function obterUsuariosDoRecurso(string $tipoRecurso, int $idRecurso): array
    {
        $db = Database::getConexao();
        $usuarios = [];

        // Usuários por empresa do recurso
        $stmt = $db->prepare("
            SELECT DISTINCT ue.id_usuario
            FROM tb_recurso_empresas re
            JOIN tb_usuario_empresas ue ON ue.id_empresa = re.id_empresa
            WHERE re.tipo_recurso = ? AND re.id_recurso = ?
        ");
        $stmt->execute([$tipoRecurso, $idRecurso]);
        foreach ($stmt->fetchAll(\PDO::FETCH_COLUMN) as $uid) {
            $usuarios[$uid] = true;
        }

        // Usuários por projeto do recurso
        $stmt2 = $db->prepare("
            SELECT DISTINCT up.id_usuario
            FROM tb_recurso_projetos rp
            JOIN tb_usuario_projetos up ON up.id_projeto = rp.id_projeto
            WHERE rp.tipo_recurso = ? AND rp.id_recurso = ?
        ");
        $stmt2->execute([$tipoRecurso, $idRecurso]);
        foreach ($stmt2->fetchAll(\PDO::FETCH_COLUMN) as $uid) {
            $usuarios[$uid] = true;
        }

        // Super admins sempre recebem
        $stmt3 = $db->query("SELECT id FROM tb_usuarios WHERE nivel_acesso = 'super_admin'");
        foreach ($stmt3->fetchAll(\PDO::FETCH_COLUMN) as $uid) {
            $usuarios[$uid] = true;
        }

        return array_map('intval', array_keys($usuarios));
    }

    /**
     * Notifica falha na execução de uma rotina
     */
    public static function notificarFalhaRotina(int $idRotina, string $nomeRotina, string $erro, array $metricas = []): void
    {
        $dados = array_merge(['id_rotina' => $idRotina, 'nome' => $nomeRotina, 'erro' => $erro], $metricas);
        self::criarParaUsuariosDoRecurso(
            'rotina', $idRotina,
            'rotina_falha',
            "Falha na execução: {$nomeRotina}",
            "A rotina \"{$nomeRotina}\" (ID: {$idRotina}) falhou: {$erro}",
            $dados
        );
        ServicoEmail::notificarFalhaParaUsuarios('rotina', $idRotina, $nomeRotina, $erro);
        ServicoWebhook::notificarFalha('rotina', $nomeRotina, $erro, $idRotina, $metricas);
        ServicoCanalNotificacao::notificar('falha', "Falha: {$nomeRotina}", array_merge(['erro' => $erro], $metricas), 'rotina');
    }

    /**
     * Notifica falha na execução de um pipeline
     */
    public static function notificarFalhaPipeline(int $idPipeline, string $nomePipeline, string $erro, int $execId = 0): void
    {
        $dados = ['id_pipeline' => $idPipeline, 'nome' => $nomePipeline, 'erro' => $erro, 'execucao_id' => $execId];
        self::criarParaUsuariosDoRecurso(
            'pipeline', $idPipeline,
            'pipeline_falha',
            "Falha no pipeline: {$nomePipeline}",
            "O pipeline \"{$nomePipeline}\" (ID: {$idPipeline}) falhou: {$erro}",
            $dados
        );
        ServicoEmail::notificarFalhaParaUsuarios('pipeline', $idPipeline, $nomePipeline, $erro);
        ServicoWebhook::notificarFalha('pipeline', $nomePipeline, $erro, $idPipeline, ['execucao_id' => $execId]);
        ServicoCanalNotificacao::notificar('falha', "Falha: {$nomePipeline}", ['erro' => $erro, 'execucao_id' => $execId], 'pipeline');
    }

    /**
     * Notifica falha na execução de um workflow
     */
    public static function notificarFalhaWorkflow(int $idWorkflow, string $nomeWorkflow, string $erro, int $execId = 0): void
    {
        $dados = ['id_workflow' => $idWorkflow, 'nome' => $nomeWorkflow, 'erro' => $erro, 'execucao_id' => $execId];
        self::criarParaUsuariosDoRecurso(
            'workflow', $idWorkflow,
            'workflow_falha',
            "Falha no workflow: {$nomeWorkflow}",
            "O workflow \"{$nomeWorkflow}\" (ID: {$idWorkflow}) falhou: {$erro}",
            $dados
        );
        ServicoEmail::notificarFalhaParaUsuarios('workflow', $idWorkflow, $nomeWorkflow, $erro);
        ServicoWebhook::notificarFalha('workflow', $nomeWorkflow, $erro, $idWorkflow, ['execucao_id' => $execId]);
        ServicoCanalNotificacao::notificar('falha', "Falha: {$nomeWorkflow}", ['erro' => $erro, 'execucao_id' => $execId], 'workflow');
    }
}
