<?php
namespace App\Servicos;

use App\Core\Database;
use App\Core\RateLimiter;
use PDO;

class ServicoAutenticacao
{
    public function autenticar(string $usuario, string $senha): array
    {
        $db = Database::getConexao();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        // Carregar configurações de segurança
        $configs = $this->carregarConfigSeguranca($db);
        $loginTentativas = (int)($configs['login_tentativas'] ?? 5);
        $loginBloqueio = (int)($configs['login_bloqueio'] ?? 15);
        $ipTentativas = (int)($configs['ip_tentativas'] ?? 10);
        $ipBloqueio = (int)($configs['ip_bloqueio'] ?? 15);

        // Verificar bloqueio por IP
        $rateLimiter = new RateLimiter($db);
        $chaveIp = 'login_ip:' . $ip;
        if (!$rateLimiter->permitir($chaveIp, $ipTentativas, $ipBloqueio * 60)) {
            $restantes = $rateLimiter->tentativasRestantes($chaveIp, $ipTentativas, $ipBloqueio * 60);
            return [
                'sucesso' => false,
                'mensagem' => "IP bloqueado por excesso de tentativas. Aguarde {$ipBloqueio} minutos.",
                'bloqueio_ip' => true
            ];
        }

        // Buscar usuário
        $stmt = $db->prepare('SELECT * FROM tb_usuarios WHERE nome_usuario = ?');
        $stmt->execute([$usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['sucesso' => false, 'mensagem' => 'Credenciais inválidas'];
        }

        // Verificar se o usuário está bloqueado temporariamente
        if (!empty($user['bloqueado_ate'])) {
            $bloqueadoAte = new \DateTime($user['bloqueado_ate']);
            $agora = new \DateTime();
            if ($agora < $bloqueadoAte) {
                $diff = $agora->diff($bloqueadoAte);
                $minutos = $diff->i + ($diff->h * 60);
                if ($minutos < 1) $minutos = 1;
                return [
                    'sucesso' => false,
                    'mensagem' => "Conta bloqueada por excesso de tentativas. Tente novamente em {$minutos} minuto(s).",
                    'bloqueio_usuario' => true
                ];
            }
            // Bloqueio expirou, limpar
            $stmtClear = $db->prepare('UPDATE tb_usuarios SET bloqueado_ate = NULL WHERE id = ?');
            $stmtClear->execute([$user['id']]);
        }

        // Verificar bloqueio por tentativas do usuário
        $chaveUser = 'login_user:' . $user['id'];
        if (!$rateLimiter->permitir($chaveUser, $loginTentativas, $loginBloqueio * 60)) {
            // Bloquear o usuário na tabela
            $bloqueioAte = (new \DateTime())->modify("+{$loginBloqueio} minutes")->format('Y-m-d H:i:s');
            $stmtBlock = $db->prepare('UPDATE tb_usuarios SET bloqueado_ate = ? WHERE id = ?');
            $stmtBlock->execute([$bloqueioAte, $user['id']]);
            return [
                'sucesso' => false,
                'mensagem' => "Conta bloqueada por excesso de tentativas. Aguarde {$loginBloqueio} minutos.",
                'bloqueio_usuario' => true
            ];
        }

        // Tentar LDAP primeiro (se configurado)
        $ldapHost = getenv('LDAP_HOST') ?: null;
        if ($ldapHost) {
            try {
                $conn = ldap_connect($ldapHost);
                if ($conn) {
                    ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
                    $bind = @ldap_bind($conn, $usuario, $senha);
                    if ($bind) {
                        // Limpar tentativas
                        $this->limparTentativas($db, $chaveUser, $chaveIp);
                        unset($user['senha_hash']);
                        return ['sucesso' => true, 'metodo' => 'ldap', 'usuario' => $user];
                    }
                }
            } catch (\Throwable $e) {
                // falha ldap — continuará para autenticação local
            }
        }

        // Autenticação local por hash
        if (empty($user['senha_hash'])) {
            return ['sucesso' => false, 'mensagem' => 'Usuário configurado apenas para LDAP'];
        }

        if (password_verify($senha, $user['senha_hash'])) {
            // Sucesso: limpar tentativas e bloqueio
            $this->limparTentativas($db, $chaveUser, $chaveIp);
            $stmtClear = $db->prepare('UPDATE tb_usuarios SET bloqueado_ate = NULL WHERE id = ?');
            $stmtClear->execute([$user['id']]);
            unset($user['senha_hash']);
            return ['sucesso' => true, 'metodo' => 'local', 'usuario' => $user];
        }

        return ['sucesso' => false, 'mensagem' => 'Credenciais inválidas'];
    }

    private function carregarConfigSeguranca(PDO $db): array
    {
        $stmt = $db->prepare("SELECT chave, valor FROM tb_configuracoes WHERE grupo = 'seguranca' AND chave IN ('login_tentativas', 'login_bloqueio', 'ip_tentativas', 'ip_bloqueio')");
        $stmt->execute();
        $configs = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $configs[$row['chave']] = $row['valor'];
        }
        return $configs;
    }

    private function limparTentativas(PDO $db, string $chaveUser, string $chaveIp): void
    {
        $stmt = $db->prepare("DELETE FROM tb_rate_limits WHERE chave IN (?, ?)");
        $stmt->execute([$chaveUser, $chaveIp]);
    }
}
