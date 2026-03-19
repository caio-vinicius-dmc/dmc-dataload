<?php
namespace App\Servicos;

use App\Core\Database;

class ServicoCanalNotificacao
{
    /**
     * Listar todos os canais de notificação
     */
    public static function listar(): array
    {
        $db = Database::getConexao();
        $stmt = $db->query("SELECT * FROM tb_canais_notificacao ORDER BY nome");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Salvar canal (criar ou atualizar)
     */
    public static function salvar(array $data): array
    {
        $db = Database::getConexao();
        $id = !empty($data['id']) ? (int)$data['id'] : null;
        $nome = trim($data['nome'] ?? '');
        $tipo = $data['tipo'] ?? '';
        $webhookUrl = trim($data['webhook_url'] ?? '');
        $canal = trim($data['canal'] ?? '');
        $ativo = isset($data['ativo']) ? 't' : 'f';
        $notificarSucesso = isset($data['notificar_sucesso']) ? 't' : 'f';
        $notificarFalha = isset($data['notificar_falha']) ? 't' : 'f';
        $notificarInicio = isset($data['notificar_inicio']) ? 't' : 'f';

        if (empty($nome) || empty($tipo) || empty($webhookUrl)) {
            return ['sucesso' => false, 'erro' => 'Nome, tipo e URL do webhook são obrigatórios'];
        }
        if (!in_array($tipo, ['slack', 'teams', 'discord'])) {
            return ['sucesso' => false, 'erro' => 'Tipo deve ser slack, teams ou discord'];
        }
        if (!filter_var($webhookUrl, FILTER_VALIDATE_URL)) {
            return ['sucesso' => false, 'erro' => 'URL do webhook inválida'];
        }

        try {
            if ($id) {
                $db->prepare(
                    "UPDATE tb_canais_notificacao SET nome = :nome, tipo = :tipo, webhook_url = :webhook_url,
                     canal = :canal, ativo = :ativo, notificar_sucesso = :ns, notificar_falha = :nf,
                     notificar_inicio = :ni, atualizado_em = CURRENT_TIMESTAMP
                     WHERE id = :id"
                )->execute([
                    ':nome' => $nome, ':tipo' => $tipo, ':webhook_url' => $webhookUrl,
                    ':canal' => $canal, ':ativo' => $ativo, ':ns' => $notificarSucesso,
                    ':nf' => $notificarFalha, ':ni' => $notificarInicio, ':id' => $id
                ]);
            } else {
                $userId = null;
                try { $userId = \App\Core\AuthMiddleware::obterUsuarioId(); } catch (\Throwable $e) {}
                $stmt = $db->prepare(
                    "INSERT INTO tb_canais_notificacao (nome, tipo, webhook_url, canal, ativo, notificar_sucesso, notificar_falha, notificar_inicio, criado_por)
                     VALUES (:nome, :tipo, :webhook_url, :canal, :ativo, :ns, :nf, :ni, :criado_por) RETURNING id"
                );
                $stmt->execute([
                    ':nome' => $nome, ':tipo' => $tipo, ':webhook_url' => $webhookUrl,
                    ':canal' => $canal, ':ativo' => $ativo, ':ns' => $notificarSucesso,
                    ':nf' => $notificarFalha, ':ni' => $notificarInicio, ':criado_por' => $userId
                ]);
                $id = $stmt->fetchColumn();
            }
            return ['sucesso' => true, 'id' => $id, 'mensagem' => 'Canal salvo com sucesso'];
        } catch (\Exception $e) {
            return ['sucesso' => false, 'erro' => $e->getMessage()];
        }
    }

    /**
     * Deletar canal
     */
    public static function deletar(int $id): array
    {
        $db = Database::getConexao();
        $stmt = $db->prepare("DELETE FROM tb_canais_notificacao WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return ['sucesso' => $stmt->rowCount() > 0, 'mensagem' => 'Canal removido'];
    }

    /**
     * Testar envio para um canal
     */
    public static function testar(int $id): array
    {
        $db = Database::getConexao();
        $stmt = $db->prepare("SELECT * FROM tb_canais_notificacao WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $canal = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$canal) return ['sucesso' => false, 'erro' => 'Canal não encontrado'];

        return self::enviar($canal, 'teste', 'Teste de Notificação', [
            'mensagem' => 'Esta é uma notificação de teste do DMC DataLoad.',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Notificar todos os canais ativos sobre um evento
     */
    public static function notificar(string $evento, string $titulo, array $dados = [], string $tipoRecurso = 'rotina'): void
    {
        $db = Database::getConexao();
        $stmt = $db->prepare(
            "SELECT * FROM tb_canais_notificacao WHERE ativo = true AND :tipo = ANY(tipos_recurso)"
        );
        $stmt->execute([':tipo' => $tipoRecurso]);
        $canais = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($canais as $canal) {
            $enviar = false;
            if ($evento === 'sucesso' && $canal['notificar_sucesso']) $enviar = true;
            if ($evento === 'falha' && $canal['notificar_falha']) $enviar = true;
            if ($evento === 'inicio' && $canal['notificar_inicio']) $enviar = true;

            if ($enviar) {
                self::enviar($canal, $evento, $titulo, $dados);
            }
        }
    }

    /**
     * Enviar notificação para um canal específico
     */
    private static function enviar(array $canal, string $evento, string $titulo, array $dados): array
    {
        $tipo = $canal['tipo'];
        $url = $canal['webhook_url'];

        try {
            $payload = match ($tipo) {
                'slack' => self::formatarSlack($evento, $titulo, $dados),
                'teams' => self::formatarTeams($evento, $titulo, $dados),
                'discord' => self::formatarDiscord($evento, $titulo, $dados),
                default => throw new \Exception("Tipo de canal desconhecido: {$tipo}")
            };

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => true
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return ['sucesso' => false, 'erro' => "Erro curl: {$error}"];
            }
            if ($httpCode >= 400) {
                return ['sucesso' => false, 'erro' => "HTTP {$httpCode}: {$response}"];
            }
            return ['sucesso' => true, 'mensagem' => 'Notificação enviada com sucesso'];
        } catch (\Exception $e) {
            return ['sucesso' => false, 'erro' => $e->getMessage()];
        }
    }

    /**
     * Formatar payload para Slack (Block Kit)
     */
    private static function formatarSlack(string $evento, string $titulo, array $dados): array
    {
        $emoji = match ($evento) {
            'sucesso' => ':white_check_mark:',
            'falha' => ':x:',
            'inicio' => ':arrow_forward:',
            'teste' => ':bell:',
            default => ':information_source:'
        };
        $cor = match ($evento) {
            'sucesso' => '#36a64f',
            'falha' => '#ff0000',
            'inicio' => '#ffaa00',
            default => '#2196f3'
        };

        $fields = [];
        foreach ($dados as $key => $value) {
            $fields[] = ['type' => 'mrkdwn', 'text' => "*" . ucfirst(str_replace('_', ' ', $key)) . ":*\n{$value}"];
        }

        return [
            'attachments' => [[
                'color' => $cor,
                'blocks' => [
                    ['type' => 'header', 'text' => ['type' => 'plain_text', 'text' => "{$emoji} {$titulo}"]],
                    ['type' => 'section', 'fields' => array_slice($fields, 0, 10)]
                ]
            ]]
        ];
    }

    /**
     * Formatar payload para Microsoft Teams (Adaptive Card)
     */
    private static function formatarTeams(string $evento, string $titulo, array $dados): array
    {
        $cor = match ($evento) {
            'sucesso' => '36a64f',
            'falha' => 'ff0000',
            'inicio' => 'ffaa00',
            default => '2196f3'
        };

        $facts = [];
        foreach ($dados as $key => $value) {
            $facts[] = ['name' => ucfirst(str_replace('_', ' ', $key)), 'value' => (string)$value];
        }

        return [
            '@type' => 'MessageCard',
            '@context' => 'http://schema.org/extensions',
            'themeColor' => $cor,
            'summary' => $titulo,
            'sections' => [[
                'activityTitle' => "DMC DataLoad - {$titulo}",
                'activitySubtitle' => date('d/m/Y H:i:s'),
                'facts' => $facts
            ]]
        ];
    }

    /**
     * Formatar payload para Discord (Embed)
     */
    private static function formatarDiscord(string $evento, string $titulo, array $dados): array
    {
        $cor = match ($evento) {
            'sucesso' => 0x36a64f,
            'falha' => 0xff0000,
            'inicio' => 0xffaa00,
            default => 0x2196f3
        };

        $fields = [];
        foreach ($dados as $key => $value) {
            $fields[] = ['name' => ucfirst(str_replace('_', ' ', $key)), 'value' => (string)$value, 'inline' => true];
        }

        return [
            'embeds' => [[
                'title' => $titulo,
                'color' => $cor,
                'fields' => $fields,
                'footer' => ['text' => 'DMC DataLoad'],
                'timestamp' => date('c')
            ]]
        ];
    }
}
