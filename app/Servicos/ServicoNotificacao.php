<?php

namespace App\Servicos;

use App\Core\Database;

class ServicoNotificacao
{
    /**
     * Cria uma notificação no sistema
     */
    public static function criar(string $tipo, string $titulo, string $mensagem, array $dados = []): void
    {
        try {
            $db = Database::getConexao();
            $stmt = $db->prepare(
                "INSERT INTO tb_notificacoes (tipo, titulo, mensagem, dados, lida, created_at)
                 VALUES (?, ?, ?, ?::jsonb, false, NOW())"
            );
            $stmt->execute([$tipo, $titulo, $mensagem, json_encode($dados)]);
        } catch (\Throwable $e) {
            error_log("Erro ao criar notificação: " . $e->getMessage());
        }
    }

    /**
     * Notifica falha na execução de uma rotina
     */
    public static function notificarFalhaRotina(int $idRotina, string $nomeRotina, string $erro, array $metricas = []): void
    {
        self::criar(
            'rotina_falha',
            "Falha na execução: {$nomeRotina}",
            "A rotina \"{$nomeRotina}\" (ID: {$idRotina}) falhou: {$erro}",
            array_merge(['id_rotina' => $idRotina, 'nome' => $nomeRotina, 'erro' => $erro], $metricas)
        );
        ServicoEmail::notificarFalha('rotina', $nomeRotina, $erro, $idRotina);
        ServicoWebhook::notificarFalha('rotina', $nomeRotina, $erro, $idRotina, $metricas);
        ServicoCanalNotificacao::notificar('falha', "Falha: {$nomeRotina}", array_merge(['erro' => $erro], $metricas), 'rotina');
    }

    /**
     * Notifica falha na execução de um pipeline
     */
    public static function notificarFalhaPipeline(int $idPipeline, string $nomePipeline, string $erro, int $execId = 0): void
    {
        self::criar(
            'pipeline_falha',
            "Falha no pipeline: {$nomePipeline}",
            "O pipeline \"{$nomePipeline}\" (ID: {$idPipeline}) falhou: {$erro}",
            ['id_pipeline' => $idPipeline, 'nome' => $nomePipeline, 'erro' => $erro, 'execucao_id' => $execId]
        );
        ServicoEmail::notificarFalha('pipeline', $nomePipeline, $erro, $idPipeline);
        ServicoWebhook::notificarFalha('pipeline', $nomePipeline, $erro, $idPipeline, ['execucao_id' => $execId]);
        ServicoCanalNotificacao::notificar('falha', "Falha: {$nomePipeline}", ['erro' => $erro, 'execucao_id' => $execId], 'pipeline');
    }

    /**
     * Notifica falha na execução de um workflow
     */
    public static function notificarFalhaWorkflow(int $idWorkflow, string $nomeWorkflow, string $erro, int $execId = 0): void
    {
        self::criar(
            'workflow_falha',
            "Falha no workflow: {$nomeWorkflow}",
            "O workflow \"{$nomeWorkflow}\" (ID: {$idWorkflow}) falhou: {$erro}",
            ['id_workflow' => $idWorkflow, 'nome' => $nomeWorkflow, 'erro' => $erro, 'execucao_id' => $execId]
        );
        ServicoEmail::notificarFalha('workflow', $nomeWorkflow, $erro, $idWorkflow);
        ServicoWebhook::notificarFalha('workflow', $nomeWorkflow, $erro, $idWorkflow, ['execucao_id' => $execId]);
        ServicoCanalNotificacao::notificar('falha', "Falha: {$nomeWorkflow}", ['erro' => $erro, 'execucao_id' => $execId], 'workflow');
    }
}
