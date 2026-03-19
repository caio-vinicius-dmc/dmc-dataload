<?php

namespace App\Servicos;

use App\Core\Database;

class ServicoWebhook
{
    /**
     * Dispara webhooks para um evento específico
     */
    public static function disparar(string $evento, array $payload): void
    {
        try {
            $db = Database::getConexao();
            $stmt = $db->prepare("SELECT * FROM tb_webhooks WHERE ativo = true AND :evento = ANY(eventos)");
            $stmt->execute([':evento' => $evento]);
            $webhooks = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($webhooks as $wh) {
                self::enviarWebhook($wh, $evento, $payload);
            }
        } catch (\Exception $e) {
            error_log('[WEBHOOK] Erro ao disparar: ' . $e->getMessage());
        }
    }

    /**
     * Notifica falha por webhook
     */
    public static function notificarFalha(string $tipo, string $nome, string $erro, int $id): void
    {
        self::disparar('falha_execucao', [
            'tipo' => $tipo,
            'nome' => $nome,
            'id' => $id,
            'erro' => $erro,
            'data' => date('c'),
        ]);
    }

    /**
     * Notifica sucesso por webhook
     */
    public static function notificarSucesso(string $tipo, string $nome, int $id, array $metricas = []): void
    {
        self::disparar('sucesso_execucao', [
            'tipo' => $tipo,
            'nome' => $nome,
            'id' => $id,
            'metricas' => $metricas,
            'data' => date('c'),
        ]);
    }

    /**
     * Envia POST para um webhook individual
     */
    private static function enviarWebhook(array $webhook, string $evento, array $payload): void
    {
        $body = json_encode([
            'evento' => $evento,
            'timestamp' => date('c'),
            'payload' => $payload,
        ], JSON_UNESCAPED_UNICODE);

        $headers = [
            'Content-Type: application/json',
            'User-Agent: DMC-DataLoad-Webhook/1.0',
            'X-Webhook-Event: ' . $evento,
        ];

        // Assinatura HMAC se secret definido
        if (!empty($webhook['secret'])) {
            $signature = hash_hmac('sha256', $body, $webhook['secret']);
            $headers[] = 'X-Webhook-Signature: sha256=' . $signature;
        }

        // Headers customizados
        $customHeaders = json_decode($webhook['headers'] ?? '{}', true);
        if ($customHeaders) {
            foreach ($customHeaders as $k => $v) {
                $headers[] = "$k: $v";
            }
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $webhook['url'],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $httpCode >= 400) {
            error_log("[WEBHOOK] Falha ao enviar para {$webhook['url']}: HTTP $httpCode - $curlError");
        }
    }

    // ========== CRUD de Webhooks ==========

    public static function listar(): array
    {
        $db = Database::getConexao();
        $stmt = $db->query("SELECT * FROM tb_webhooks ORDER BY criado_em DESC");
        return ['sucesso' => true, 'dados' => $stmt->fetchAll(\PDO::FETCH_ASSOC)];
    }

    public static function buscar(int $id): array
    {
        $db = Database::getConexao();
        $stmt = $db->prepare("SELECT * FROM tb_webhooks WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $wh = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$wh) return ['sucesso' => false, 'erro' => 'Webhook não encontrado'];
        return ['sucesso' => true, 'dados' => $wh];
    }

    public static function salvar(array $dados): array
    {
        $db = Database::getConexao();
        $id = $dados['id'] ?? null;
        $nome = trim($dados['nome'] ?? '');
        $url = trim($dados['url'] ?? '');
        $eventos = $dados['eventos'] ?? [];
        $headersJson = json_encode($dados['headers'] ?? []);
        $ativo = (bool) ($dados['ativo'] ?? true);
        $secret = $dados['secret'] ?? '';

        if (empty($nome) || empty($url)) {
            return ['sucesso' => false, 'erro' => 'Nome e URL são obrigatórios'];
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['sucesso' => false, 'erro' => 'URL inválida'];
        }

        if (is_string($eventos)) {
            $eventos = array_filter(array_map('trim', explode(',', $eventos)));
        }
        $eventosStr = '{' . implode(',', $eventos) . '}';

        if ($id) {
            $stmt = $db->prepare("
                UPDATE tb_webhooks SET nome=:nome, url=:url, eventos=:eventos, headers=:headers, 
                    ativo=:ativo, secret=:secret, atualizado_em=NOW()
                WHERE id=:id
            ");
            $stmt->execute([
                ':nome' => $nome, ':url' => $url, ':eventos' => $eventosStr,
                ':headers' => $headersJson, ':ativo' => $ativo ? 'true' : 'false',
                ':secret' => $secret, ':id' => $id,
            ]);
            return ['sucesso' => true, 'mensagem' => 'Webhook atualizado'];
        } else {
            $criador = \App\Core\AuthMiddleware::obterUsuarioId();
            $stmt = $db->prepare("
                INSERT INTO tb_webhooks (nome, url, eventos, headers, ativo, secret, criado_por)
                VALUES (:nome, :url, :eventos, :headers, :ativo, :secret, :criador)
                RETURNING id
            ");
            $stmt->execute([
                ':nome' => $nome, ':url' => $url, ':eventos' => $eventosStr,
                ':headers' => $headersJson, ':ativo' => $ativo ? 'true' : 'false',
                ':secret' => $secret, ':criador' => $criador,
            ]);
            $newId = $stmt->fetchColumn();
            return ['sucesso' => true, 'mensagem' => 'Webhook criado', 'id' => $newId];
        }
    }

    public static function excluir(int $id): array
    {
        $db = Database::getConexao();
        $stmt = $db->prepare("DELETE FROM tb_webhooks WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return ['sucesso' => true, 'mensagem' => 'Webhook excluído'];
    }

    public static function testar(int $id): array
    {
        $wh = self::buscar($id);
        if (!$wh['sucesso']) return $wh;

        self::enviarWebhook($wh['dados'], 'teste', [
            'mensagem' => 'Teste de webhook do DMC DataLoad',
            'data' => date('c'),
        ]);

        return ['sucesso' => true, 'mensagem' => 'Webhook de teste enviado'];
    }
}
