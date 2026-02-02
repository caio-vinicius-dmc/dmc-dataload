<?php
namespace App\Core;

/**
 * Logger estruturado com níveis e formato JSON
 */
class Logger
{
    public const DEBUG = 'DEBUG';
    public const INFO = 'INFO';
    public const WARNING = 'WARNING';
    public const ERROR = 'ERROR';
    public const CRITICAL = 'CRITICAL';
    
    private static ?string $logPath = null;
    private static string $minLevel = self::DEBUG;
    private static array $niveis = [
        'DEBUG' => 0,
        'INFO' => 1,
        'WARNING' => 2,
        'ERROR' => 3,
        'CRITICAL' => 4
    ];
    
    /**
     * Inicializa o logger
     */
    public static function inicializar(string $logPath = null, string $minLevel = 'DEBUG'): void
    {
        self::$logPath = $logPath ?? __DIR__ . '/../../storage/logs/app.log';
        self::$minLevel = $minLevel;
        
        $logDir = dirname(self::$logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }
    }
    
    /**
     * Registra uma mensagem
     * @param string $nivel Nível do log (DEBUG, INFO, WARNING, ERROR, CRITICAL)
     * @param string $mensagem Mensagem a ser registrada
     * @param array $contexto Dados adicionais
     * @param string|null $arquivo Caminho do arquivo de log (opcional, para testes)
     */
    public static function log(string $nivel, string $mensagem, array $contexto = [], ?string $arquivo = null): void
    {
        $caminhoLog = $arquivo ?? self::$logPath;
        
        if ($caminhoLog === null) {
            self::inicializar();
            $caminhoLog = self::$logPath;
        }
        
        // Verificar nível mínimo
        if (isset(self::$niveis[$nivel]) && isset(self::$niveis[self::$minLevel])) {
            if (self::$niveis[$nivel] < self::$niveis[self::$minLevel]) {
                return;
            }
        }
        
        // Garantir que o diretório existe
        $dir = dirname($caminhoLog);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        
        $entrada = [
            'timestamp' => date('c'),
            'nivel' => $nivel,
            'mensagem' => $mensagem,
            'contexto' => $contexto,
            'request' => [
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'CLI',
                'metodo' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
                'uri' => $_SERVER['REQUEST_URI'] ?? 'CLI',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI'
            ],
            'memoria_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'pid' => getmypid()
        ];
        
        // Adicionar stack trace para erros
        if (in_array($nivel, [self::ERROR, self::CRITICAL])) {
            $entrada['trace'] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        }
        
        $linha = json_encode($entrada, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        
        @file_put_contents($caminhoLog, $linha, FILE_APPEND | LOCK_EX);
    }
    
    public static function debug(string $mensagem, array $contexto = [], ?string $arquivo = null): void
    {
        self::log(self::DEBUG, $mensagem, $contexto, $arquivo);
    }
    
    public static function info(string $mensagem, array $contexto = [], ?string $arquivo = null): void
    {
        self::log(self::INFO, $mensagem, $contexto, $arquivo);
    }
    
    public static function warning(string $mensagem, array $contexto = [], ?string $arquivo = null): void
    {
        self::log(self::WARNING, $mensagem, $contexto, $arquivo);
    }
    
    public static function error(string $mensagem, array $contexto = [], ?string $arquivo = null): void
    {
        self::log(self::ERROR, $mensagem, $contexto, $arquivo);
    }
    
    public static function critical(string $mensagem, array $contexto = [], ?string $arquivo = null): void
    {
        self::log(self::CRITICAL, $mensagem, $contexto, $arquivo);
    }
    
    /**
     * Salva log em banco de dados
     */
    public static function logToDatabase(string $nivel, string $mensagem, array $contexto = []): void
    {
        try {
            $db = Database::getConexao();
            
            $stmt = $db->prepare("INSERT INTO tb_logs_sistema 
                (nivel, mensagem, contexto, ip, uri, user_agent, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())");
            
            $stmt->execute([
                $nivel,
                $mensagem,
                json_encode($contexto),
                $_SERVER['REMOTE_ADDR'] ?? 'CLI',
                $_SERVER['REQUEST_URI'] ?? 'CLI',
                $_SERVER['HTTP_USER_AGENT'] ?? 'CLI'
            ]);
        } catch (\Throwable $e) {
            // Fallback para arquivo se DB falhar
            self::log($nivel, $mensagem, array_merge($contexto, ['db_error' => $e->getMessage()]));
        }
    }
    
    /**
     * Retorna logs recentes do arquivo
     */
    public static function lerLogsRecentes(int $linhas = 100): array
    {
        if (self::$logPath === null || !file_exists(self::$logPath)) {
            return [];
        }
        
        $file = new \SplFileObject(self::$logPath, 'r');
        $file->seek(PHP_INT_MAX);
        $totalLinhas = $file->key();
        
        $inicio = max(0, $totalLinhas - $linhas);
        $file->seek($inicio);
        
        $logs = [];
        while (!$file->eof()) {
            $linha = $file->fgets();
            if (trim($linha)) {
                $decoded = json_decode($linha, true);
                if ($decoded) {
                    $logs[] = $decoded;
                }
            }
        }
        
        return array_reverse($logs);
    }
}
