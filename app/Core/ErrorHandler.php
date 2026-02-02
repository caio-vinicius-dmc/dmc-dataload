<?php
namespace App\Core;

class ErrorHandler
{
    private static string $logPath = '';
    private static bool $prodMode = false;
    private static bool $inicializado = false;

    public static function inicializar(string $logPath = null): void
    {
        self::$logPath = $logPath ?? __DIR__ . '/../../storage/logs/errors.log';
        self::$prodMode = getenv('APP_ENV') === 'production';
        self::$inicializado = true;

        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }
    
    private static function garantirInicializado(): void
    {
        if (!self::$inicializado) {
            self::$logPath = __DIR__ . '/../../storage/logs/errors.log';
        }
    }

    public static function handleError(int $errno, string $errstr, string $errfile = '', int $errline = 0): bool
    {
        $niveis = [
            E_ERROR => 'ERROR',
            E_WARNING => 'WARNING',
            E_NOTICE => 'NOTICE',
            E_USER_ERROR => 'USER_ERROR',
            E_USER_WARNING => 'USER_WARNING',
            E_USER_NOTICE => 'USER_NOTICE',
        ];

        $nivel = $niveis[$errno] ?? 'UNKNOWN';

        self::log($nivel, $errstr, [
            'arquivo' => $errfile,
            'linha' => $errline,
            'tipo' => $errno
        ]);

        return true;
    }

    public static function handleException(\Throwable $e): void
    {
        self::log('EXCEPTION', $e->getMessage(), [
            'tipo' => get_class($e),
            'arquivo' => $e->getFile(),
            'linha' => $e->getLine(),
            'trace' => self::$prodMode ? null : $e->getTraceAsString()
        ]);

        http_response_code(500);
        
        if (self::isJsonRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'sucesso' => false,
                'erro' => self::$prodMode ? 'Erro interno do servidor' : $e->getMessage(),
                'tipo' => get_class($e),
                'detalhes' => self::$prodMode ? null : [
                    'arquivo' => $e->getFile(),
                    'linha' => $e->getLine()
                ]
            ]);
        } else {
            echo self::renderErrorPage($e);
        }
    }

    public static function handleShutdown(): void
    {
        $error = error_get_last();
        
        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            self::log('FATAL', $error['message'], [
                'arquivo' => $error['file'],
                'linha' => $error['line']
            ]);

            if (!headers_sent()) {
                http_response_code(500);
                
                if (self::isJsonRequest()) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'sucesso' => false,
                        'erro' => 'Erro fatal do servidor'
                    ]);
                }
            }
        }
    }

    public static function log(string $nivel, string $mensagem, array $contexto = []): void
    {
        self::garantirInicializado();
        
        $logDir = dirname(self::$logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }

        $entrada = [
            'timestamp' => date('Y-m-d H:i:s'),
            'nivel' => $nivel,
            'mensagem' => $mensagem,
            'contexto' => $contexto,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'CLI',
            'metodo' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            'uri' => $_SERVER['REQUEST_URI'] ?? 'CLI'
        ];

        $linha = json_encode($entrada, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        
        @file_put_contents(self::$logPath, $linha, FILE_APPEND | LOCK_EX);
    }

    private static function isJsonRequest(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        
        return strpos($accept, 'application/json') !== false 
            || strpos($contentType, 'application/json') !== false
            || strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') === 0;
    }

    private static function renderErrorPage(\Throwable $e): string
    {
        if (self::$prodMode) {
            return '<!DOCTYPE html>
<html>
<head>
    <title>Erro - DMC DataLoad</title>
    <style>
        body { font-family: Arial; text-align: center; padding: 50px; background: #f5f5f5; }
        .error-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto; }
        h1 { color: #dc3545; }
    </style>
</head>
<body>
    <div class="error-box">
        <h1>⚠️ Erro Interno</h1>
        <p>Ocorreu um erro ao processar sua solicitação.</p>
        <p><a href="/">Voltar à página inicial</a></p>
    </div>
</body>
</html>';
        }

        $html = '<!DOCTYPE html>
<html>
<head>
    <title>Erro - ' . htmlspecialchars(get_class($e)) . '</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
        .error-header { background: #dc3545; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .error-message { background: #2d2d2d; padding: 15px; border-left: 4px solid #dc3545; margin-bottom: 20px; }
        .error-trace { background: #2d2d2d; padding: 15px; border-radius: 5px; overflow-x: auto; }
        pre { margin: 0; }
    </style>
</head>
<body>
    <div class="error-header">
        <h2>' . htmlspecialchars(get_class($e)) . '</h2>
    </div>
    <div class="error-message">
        <strong>Mensagem:</strong> ' . htmlspecialchars($e->getMessage()) . '<br>
        <strong>Arquivo:</strong> ' . htmlspecialchars($e->getFile()) . '<br>
        <strong>Linha:</strong> ' . $e->getLine() . '
    </div>
    <div class="error-trace">
        <strong>Stack Trace:</strong>
        <pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>
    </div>
</body>
</html>';

        return $html;
    }

    public static function tratarErro(\Throwable $e, string $mensagemUsuario = null): array
    {
        self::log('ERROR', $e->getMessage(), [
            'tipo' => get_class($e),
            'arquivo' => $e->getFile(),
            'linha' => $e->getLine()
        ]);

        return [
            'sucesso' => false,
            'erro' => $mensagemUsuario ?? 'Ocorreu um erro ao processar a solicitação',
            'detalhes' => self::$prodMode ? null : $e->getMessage()
        ];
    }
}
