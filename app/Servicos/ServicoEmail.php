<?php

namespace App\Servicos;

use App\Core\Database;

class ServicoEmail
{
    /**
     * Envia um e-mail via SMTP usando sockets (sem dependência externa)
     */
    public static function enviar(string $para, string $assunto, string $corpo, bool $html = true): array
    {
        $config = self::obterConfigSmtp();

        if (empty($config['smtp_host']) || empty($config['smtp_from_email'])) {
            return ['sucesso' => false, 'erro' => 'SMTP não configurado. Acesse Configurações > E-mail/SMTP.'];
        }

        try {
            $host = $config['smtp_host'];
            $port = (int) ($config['smtp_port'] ?: 587);
            $encryption = $config['smtp_encryption'] ?? 'tls';
            $user = $config['smtp_user'] ?? '';
            $pass = $config['smtp_password'] ?? '';
            $fromEmail = $config['smtp_from_email'];
            $fromName = $config['smtp_from_name'] ?: 'DMC DataLoad';

            $prefix = ($encryption === 'ssl') ? 'ssl://' : '';
            $socket = @fsockopen($prefix . $host, $port, $errno, $errstr, 15);
            if (!$socket) {
                return ['sucesso' => false, 'erro' => "Não foi possível conectar ao SMTP: $errstr ($errno)"];
            }

            stream_set_timeout($socket, 15);
            $response = self::readResponse($socket);

            self::sendCommand($socket, "EHLO " . gethostname());

            // STARTTLS
            if ($encryption === 'tls') {
                self::sendCommand($socket, "STARTTLS");
                $cryptoMethod = STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
                if (!stream_socket_enable_crypto($socket, true, $cryptoMethod)) {
                    fclose($socket);
                    return ['sucesso' => false, 'erro' => 'Falha ao iniciar TLS'];
                }
                self::sendCommand($socket, "EHLO " . gethostname());
            }

            // Auth
            if ($user && $pass) {
                self::sendCommand($socket, "AUTH LOGIN");
                self::sendCommand($socket, base64_encode($user));
                self::sendCommand($socket, base64_encode($pass));
            }

            self::sendCommand($socket, "MAIL FROM:<$fromEmail>");
            self::sendCommand($socket, "RCPT TO:<$para>");
            self::sendCommand($socket, "DATA");

            $boundary = md5(uniqid((string) time()));
            $contentType = $html ? "text/html; charset=UTF-8" : "text/plain; charset=UTF-8";

            $headers = "From: $fromName <$fromEmail>\r\n";
            $headers .= "To: $para\r\n";
            $headers .= "Subject: =?UTF-8?B?" . base64_encode($assunto) . "?=\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: $contentType\r\n";
            $headers .= "Date: " . date('r') . "\r\n";
            $headers .= "Message-ID: <" . md5(uniqid((string) mt_rand(), true)) . "@" . gethostname() . ">\r\n";

            $message = $headers . "\r\n" . $corpo . "\r\n.\r\n";
            fwrite($socket, $message);
            $response = self::readResponse($socket);

            self::sendCommand($socket, "QUIT");
            fclose($socket);

            return ['sucesso' => true, 'mensagem' => 'E-mail enviado com sucesso'];
        } catch (\Exception $e) {
            return ['sucesso' => false, 'erro' => 'Erro ao enviar e-mail: ' . $e->getMessage()];
        }
    }

    /**
     * Envia notificação de falha de execução por e-mail
     */
    public static function notificarFalha(string $tipo, string $nome, string $erro, int $id): void
    {
        $config = self::obterConfigSmtp();
        if (empty($config['smtp_host']) || ($config['notif_email_falha'] ?? '0') !== '1') {
            return;
        }

        $destinatario = $config['smtp_from_email'];
        if (empty($destinatario)) return;

        $assunto = "[DMC DataLoad] Falha na execução: $nome";
        $corpo = self::templateFalha($tipo, $nome, $erro, $id);

        self::enviar($destinatario, $assunto, $corpo);
    }

    /**
     * Envia notificação de falha para todos os usuários que pertencem às empresas/projetos do recurso.
     * Respeita as regras RBAC: só envia para quem faz parte da empresa/projeto associado.
     */
    public static function notificarFalhaParaUsuarios(string $tipo, int $idRecurso, string $nome, string $erro): void
    {
        $config = self::obterConfigSmtp();
        if (empty($config['smtp_host']) || ($config['notif_email_falha'] ?? '0') !== '1') {
            return;
        }

        try {
            $emails = self::obterEmailsUsuariosDoRecurso($tipo, $idRecurso);
            if (empty($emails)) {
                // Fallback: enviar para o admin
                self::notificarFalha($tipo, $nome, $erro, $idRecurso);
                return;
            }

            $assunto = "[DMC DataLoad] Falha na execução: $nome";
            $corpo = self::templateFalha($tipo, $nome, $erro, $idRecurso);

            foreach ($emails as $email) {
                self::enviar($email, $assunto, $corpo);
            }
        } catch (\Throwable $e) {
            error_log("Erro ao enviar emails por recurso: " . $e->getMessage());
        }
    }

    /**
     * Obtém emails dos usuários que pertencem às empresas e projetos do recurso
     */
    private static function obterEmailsUsuariosDoRecurso(string $tipoRecurso, int $idRecurso): array
    {
        $db = Database::getConexao();
        $emails = [];

        // Usuários por empresa
        $stmt = $db->prepare("
            SELECT DISTINCT u.email FROM tb_usuarios u
            JOIN tb_usuario_empresas ue ON ue.id_usuario = u.id
            JOIN tb_recurso_empresas re ON re.id_empresa = ue.id_empresa
            WHERE re.tipo_recurso = ? AND re.id_recurso = ?
            AND u.email IS NOT NULL AND u.email != ''
        ");
        $stmt->execute([$tipoRecurso, $idRecurso]);
        foreach ($stmt->fetchAll(\PDO::FETCH_COLUMN) as $e) {
            $emails[$e] = true;
        }

        // Usuários por projeto
        $stmt2 = $db->prepare("
            SELECT DISTINCT u.email FROM tb_usuarios u
            JOIN tb_usuario_projetos up ON up.id_usuario = u.id
            JOIN tb_recurso_projetos rp ON rp.id_projeto = up.id_projeto
            WHERE rp.tipo_recurso = ? AND rp.id_recurso = ?
            AND u.email IS NOT NULL AND u.email != ''
        ");
        $stmt2->execute([$tipoRecurso, $idRecurso]);
        foreach ($stmt2->fetchAll(\PDO::FETCH_COLUMN) as $e) {
            $emails[$e] = true;
        }

        // Super admins sempre recebem
        $stmt3 = $db->query("SELECT email FROM tb_usuarios WHERE nivel_acesso = 'super_admin' AND email IS NOT NULL AND email != ''");
        foreach ($stmt3->fetchAll(\PDO::FETCH_COLUMN) as $e) {
            $emails[$e] = true;
        }

        return array_keys($emails);
    }

    /**
     * Obtém configurações SMTP do banco
     */
    public static function obterConfigSmtp(): array
    {
        try {
            $db = Database::getConexao();
            $stmt = $db->query("SELECT chave, valor FROM tb_configuracoes WHERE grupo IN ('email', 'notificacoes')");
            $configs = [];
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $configs[$row['chave']] = $row['valor'];
            }
            return $configs;
        } catch (\Exception $e) {
            return [];
        }
    }

    private static function templateFalha(string $tipo, string $nome, string $erro, int $id): string
    {
        $tipoLabel = match ($tipo) {
            'rotina' => 'Rotina',
            'pipeline' => 'Pipeline',
            'workflow' => 'Workflow',
            default => ucfirst($tipo),
        };
        $data = date('d/m/Y H:i:s');

        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px;">
<div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <div style="background: #ef4444; color: white; padding: 20px; text-align: center;">
        <h2 style="margin: 0;">&#x26A0; Falha na Execução</h2>
    </div>
    <div style="padding: 24px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr><td style="padding: 8px 0; color: #666; width: 120px;">Tipo:</td><td style="padding: 8px 0; font-weight: bold;">$tipoLabel</td></tr>
            <tr><td style="padding: 8px 0; color: #666;">Nome:</td><td style="padding: 8px 0; font-weight: bold;">$nome</td></tr>
            <tr><td style="padding: 8px 0; color: #666;">ID:</td><td style="padding: 8px 0;">$id</td></tr>
            <tr><td style="padding: 8px 0; color: #666;">Data:</td><td style="padding: 8px 0;">$data</td></tr>
        </table>
        <div style="margin-top: 16px; padding: 12px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px;">
            <strong style="color: #dc2626;">Erro:</strong>
            <pre style="margin: 8px 0 0; white-space: pre-wrap; color: #991b1b; font-size: 13px;">$erro</pre>
        </div>
    </div>
    <div style="background: #f8fafc; padding: 16px; text-align: center; color: #94a3b8; font-size: 12px;">
        DMC DataLoad &mdash; Notificação automática
    </div>
</div>
</body>
</html>
HTML;
    }

    private static function sendCommand($socket, string $command): string
    {
        fwrite($socket, $command . "\r\n");
        return self::readResponse($socket);
    }

    private static function readResponse($socket): string
    {
        $response = '';
        while ($line = fgets($socket, 512)) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        return $response;
    }
}
