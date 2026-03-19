<?php
namespace App\Core;

class AuthMiddleware
{
    public static function iniciarSessao(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            
            // Regenerar ID a cada 30 minutos para segurança
            if (!isset($_SESSION['LAST_REGENERATE'])) {
                $_SESSION['LAST_REGENERATE'] = time();
            } elseif (time() - $_SESSION['LAST_REGENERATE'] > 1800) {
                session_regenerate_id(true);
                $_SESSION['LAST_REGENERATE'] = time();
            }
        }
    }

    public static function verificarAutenticacao(): bool
    {
        self::iniciarSessao();
        return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_autenticado']);
    }

    public static function obterUsuarioId(): ?int
    {
        self::iniciarSessao();
        return $_SESSION['usuario_id'] ?? null;
    }

    public static function obterUsuario(): ?array
    {
        self::iniciarSessao();
        return $_SESSION['usuario'] ?? null;
    }

    public static function definirUsuario(array $usuario): void
    {
        self::iniciarSessao();
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario'] = $usuario;
        $_SESSION['usuario_autenticado'] = true;
        $_SESSION['login_timestamp'] = time();
    }

    public static function destruirSessao(): void
    {
        self::iniciarSessao();
        $_SESSION = [];
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
    }

    public static function verificarNivelAcesso(string $nivelRequerido): bool
    {
        if (!self::verificarAutenticacao()) {
            return false;
        }

        $usuario = self::obterUsuario();
        $nivel = $usuario['nivel_acesso'] ?? 'operador';

        $niveis = [
            'operador' => 1,
            'desenvolvedor' => 2,
            'admin' => 3,
            'super_admin' => 4,
            // Aliases legados
            'user' => 2,
            'editor' => 2,
        ];
        $nivelAtual = $niveis[$nivel] ?? 0;
        $nivelMin = $niveis[$nivelRequerido] ?? 999;

        return $nivelAtual >= $nivelMin;
    }

    public static function exigirAutenticacao(): void
    {
        if (!self::verificarAutenticacao()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Não autenticado', 'redirect' => '/login']);
            exit;
        }
    }

    public static function exigirNivelAcesso(string $nivel): void
    {
        self::exigirAutenticacao();
        
        if (!self::verificarNivelAcesso($nivel)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['erro' => 'Acesso negado. Nível requerido: ' . $nivel]);
            exit;
        }
    }

    public static function verificarTimeout(int $tempoMaximo = 3600): bool
    {
        self::iniciarSessao();
        
        if (!isset($_SESSION['login_timestamp'])) {
            return true; // Timeout
        }

        $tempoDecorrido = time() - $_SESSION['login_timestamp'];
        
        if ($tempoDecorrido > $tempoMaximo) {
            self::destruirSessao();
            return true;
        }

        // Atualizar timestamp da última atividade
        $_SESSION['ultima_atividade'] = time();
        
        return false;
    }

    public static function gerarTokenCSRF(): string
    {
        self::iniciarSessao();
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }

    public static function validarTokenCSRF(string $token): bool
    {
        self::iniciarSessao();
        
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
