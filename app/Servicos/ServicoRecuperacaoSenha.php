<?php
/**
 * DMC DataLoad - Serviço de Recuperação de Senha
 * Gera tokens temporários de 30 min com chave hex de 6 dígitos
 */

namespace App\Servicos;

use App\Core\Database;
use PDO;

class ServicoRecuperacaoSenha
{
    /**
     * Solicitar recuperação de senha (via email/username)
     * Gera token + chave hex, invalida tokens anteriores, envia email
     */
    public static function solicitar(string $identificador): array
    {
        $db = Database::getConexao();

        // Buscar usuário por nome_usuario ou email
        $stmt = $db->prepare(
            "SELECT id, nome_usuario, email FROM tb_usuarios WHERE nome_usuario = :id OR email = :id LIMIT 1"
        );
        $stmt->execute([':id' => trim($identificador)]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Resposta genérica para não revelar se o usuário existe
        $respostaPadrao = [
            'sucesso' => true,
            'mensagem' => 'Se o usuário/e-mail existir em nossa base, um link de recuperação será enviado.'
        ];

        if (!$usuario || empty($usuario['email'])) {
            return $respostaPadrao;
        }

        $resultado = self::gerarEEnviarToken($db, $usuario);
        // Sempre retornar a mesma mensagem genérica
        if ($resultado['sucesso']) {
            return $respostaPadrao;
        }
        return $respostaPadrao;
    }

    /**
     * Solicitar reset por admin (envia para o email do usuário alvo)
     */
    public static function solicitarPorAdmin(int $idUsuario): array
    {
        $db = Database::getConexao();

        $stmt = $db->prepare("SELECT id, nome_usuario, email FROM tb_usuarios WHERE id = :id");
        $stmt->execute([':id' => $idUsuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            return ['sucesso' => false, 'erro' => 'Usuário não encontrado.'];
        }

        if (empty($usuario['email'])) {
            return ['sucesso' => false, 'erro' => 'Este usuário não possui e-mail cadastrado.'];
        }

        return self::gerarEEnviarToken($db, $usuario);
    }

    /**
     * Validar token e chave hex
     */
    public static function validarToken(string $token, string $chaveHex): array
    {
        $db = Database::getConexao();
        $tokenHash = hash('sha256', $token);

        $stmt = $db->prepare(
            "SELECT pr.id, pr.id_usuario, pr.chave_hex, pr.expira_em, pr.usado, u.nome_usuario
             FROM tb_password_resets pr
             JOIN tb_usuarios u ON u.id = pr.id_usuario
             WHERE pr.token_hash = :hash
             LIMIT 1"
        );
        $stmt->execute([':hash' => $tokenHash]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reset) {
            return ['sucesso' => false, 'erro' => 'Link de recuperação inválido.'];
        }

        if ($reset['usado']) {
            return ['sucesso' => false, 'erro' => 'Este link já foi utilizado.'];
        }

        if (new \DateTime($reset['expira_em']) < new \DateTime()) {
            return ['sucesso' => false, 'erro' => 'Este link expirou. Solicite uma nova recuperação.'];
        }

        if (strtolower($reset['chave_hex']) !== strtolower(trim($chaveHex))) {
            return ['sucesso' => false, 'erro' => 'Chave de verificação incorreta.'];
        }

        return [
            'sucesso' => true,
            'id_reset' => $reset['id'],
            'id_usuario' => $reset['id_usuario'],
            'nome_usuario' => $reset['nome_usuario']
        ];
    }

    /**
     * Redefinir a senha usando o token validado
     */
    public static function redefinirSenha(string $token, string $chaveHex, string $novaSenha): array
    {
        if (strlen($novaSenha) < 6) {
            return ['sucesso' => false, 'erro' => 'A senha deve ter no mínimo 6 caracteres.'];
        }

        $validacao = self::validarToken($token, $chaveHex);
        if (!$validacao['sucesso']) {
            return $validacao;
        }

        $db = Database::getConexao();

        // Atualizar senha
        $stmt = $db->prepare("UPDATE tb_usuarios SET senha_hash = :hash, bloqueado_ate = NULL WHERE id = :id");
        $stmt->execute([
            ':hash' => password_hash($novaSenha, PASSWORD_DEFAULT),
            ':id' => $validacao['id_usuario']
        ]);

        // Marcar token como usado
        $stmt = $db->prepare("UPDATE tb_password_resets SET usado = TRUE WHERE id = :id");
        $stmt->execute([':id' => $validacao['id_reset']]);

        // Limpar rate limits do usuário
        $db->prepare("DELETE FROM tb_rate_limits WHERE chave LIKE :chave")
           ->execute([':chave' => 'login_user:' . $validacao['id_usuario']]);

        ServicoAuditoria::registrar('senha_redefinida', 'usuario', $validacao['id_usuario'], $validacao['nome_usuario']);

        return ['sucesso' => true, 'mensagem' => 'Senha redefinida com sucesso!'];
    }

    /**
     * Gera token, invalida anteriores, envia email
     */
    private static function gerarEEnviarToken(PDO $db, array $usuario): array
    {
        // Invalidar tokens anteriores deste usuário
        $db->prepare("UPDATE tb_password_resets SET usado = TRUE WHERE id_usuario = :id AND usado = FALSE")
           ->execute([':id' => $usuario['id']]);

        // Gerar token seguro e chave hex de 6 dígitos
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $chaveHex = strtoupper(bin2hex(random_bytes(3))); // 6 hex chars

        // Salvar no banco (expira em 30 minutos)
        $stmt = $db->prepare(
            "INSERT INTO tb_password_resets (id_usuario, token_hash, chave_hex, expira_em)
             VALUES (:id, :hash, :chave, NOW() + INTERVAL '30 minutes')"
        );
        $stmt->execute([
            ':id' => $usuario['id'],
            ':hash' => $tokenHash,
            ':chave' => $chaveHex,
        ]);

        // Montar link de redefinição
        $baseUrl = self::getBaseUrl();
        $link = $baseUrl . '/redefinir-senha?token=' . urlencode($token);

        // Enviar email
        $assunto = 'Recuperação de Senha - DMC DataLoad';
        $corpo = self::templateEmail($usuario['nome_usuario'], $chaveHex, $link);

        $resultado = ServicoEmail::enviar($usuario['email'], $assunto, $corpo, true);

        if (!$resultado['sucesso']) {
            return ['sucesso' => false, 'erro' => 'Falha ao enviar e-mail: ' . ($resultado['erro'] ?? 'Erro desconhecido')];
        }

        return [
            'sucesso' => true,
            'mensagem' => 'Um e-mail de recuperação foi enviado para ' . self::mascarEmail($usuario['email']) . '.'
        ];
    }

    /**
     * Template HTML do e-mail de recuperação
     */
    private static function templateEmail(string $nomeUsuario, string $chaveHex, string $link): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; background: #f0f2f5; padding: 20px; margin: 0;">
<div style="max-width: 560px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 32px 24px; text-align: center;">
        <div style="width: 56px; height: 56px; background: rgba(255,255,255,0.2); border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 12px;">
            <span style="font-size: 28px; color: white;">🔑</span>
        </div>
        <h1 style="color: white; margin: 0; font-size: 22px; font-weight: 700;">Recuperação de Senha</h1>
        <p style="color: rgba(255,255,255,0.85); margin: 8px 0 0; font-size: 14px;">DMC DataLoad</p>
    </div>
    <div style="padding: 32px 24px;">
        <p style="color: #374151; font-size: 15px; line-height: 1.6;">
            Olá <strong>{$nomeUsuario}</strong>,
        </p>
        <p style="color: #374151; font-size: 15px; line-height: 1.6;">
            Recebemos uma solicitação de recuperação de senha para sua conta.
            Clique no botão abaixo para acessar a página de redefinição:
        </p>
        <div style="text-align: center; margin: 24px 0;">
            <a href="{$link}" style="display: inline-block; background: linear-gradient(135deg, #667eea, #764ba2); color: white; text-decoration: none; padding: 14px 32px; border-radius: 12px; font-weight: 700; font-size: 15px; box-shadow: 0 4px 14px rgba(102,126,234,0.3);">
                Redefinir Senha
            </a>
        </div>
        <p style="color: #374151; font-size: 15px; line-height: 1.6;">
            Na página de redefinição, digite a seguinte <strong>chave de verificação</strong>:
        </p>
        <div style="text-align: center; margin: 20px 0;">
            <div style="display: inline-block; background: #f3f4f6; border: 2px dashed #667eea; border-radius: 12px; padding: 16px 32px;">
                <span style="font-family: 'Courier New', monospace; font-size: 28px; font-weight: 800; letter-spacing: 6px; color: #667eea;">{$chaveHex}</span>
            </div>
        </div>
        <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px 16px; border-radius: 0 8px 8px 0; margin: 24px 0;">
            <p style="margin: 0; color: #92400e; font-size: 13px; font-weight: 500;">
                ⚠️ Este link e chave expiram em <strong>30 minutos</strong>. Se você não solicitou esta recuperação, ignore este e-mail.
            </p>
        </div>
    </div>
    <div style="background: #f8fafc; padding: 16px; text-align: center; color: #94a3b8; font-size: 12px;">
        DMC DataLoad &mdash; Este é um e-mail automático, não responda.
    </div>
</div>
</body>
</html>
HTML;
    }

    /**
     * Obter URL base da aplicação
     */
    private static function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base = defined('BASE_URL') ? BASE_URL : '';
        return $protocol . '://' . $host . $base;
    }

    /**
     * Mascara o email para exibição (ex: c***@email.com)
     */
    private static function mascarEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) return '***@***.***';
        $local = $parts[0];
        $masked = substr($local, 0, 1) . str_repeat('*', max(3, strlen($local) - 1));
        return $masked . '@' . $parts[1];
    }
}
