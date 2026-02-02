<?php
namespace App\Core;

/**
 * Gerenciador de timeout para queries e operações
 */
class TimeoutManager
{
    private static int $defaultTimeout = 300; // 5 minutos
    private static array $timeoutsPorTipo = [
        'query' => 120,      // 2 minutos para queries
        'conexao' => 30,     // 30 segundos para conexão
        'etl' => 600,        // 10 minutos para ETL
        'api' => 30,         // 30 segundos para APIs externas
        'worker' => 1800,    // 30 minutos para worker
    ];
    
    /**
     * Define timeout global padrão
     */
    public static function setDefaultTimeout(int $segundos): void
    {
        self::$defaultTimeout = $segundos;
    }
    
    /**
     * Obtém timeout para um tipo de operação
     */
    public static function getTimeout(string $tipo): int
    {
        return self::$timeoutsPorTipo[$tipo] ?? self::$defaultTimeout;
    }
    
    /**
     * Aplica timeout a uma conexão PDO
     */
    public static function aplicarTimeoutPDO(\PDO $pdo, int $segundos = null): void
    {
        $segundos = $segundos ?? self::$timeoutsPorTipo['query'];
        
        // PostgreSQL
        $pdo->exec("SET statement_timeout = " . ($segundos * 1000));
        
        // Também define atributo PDO
        $pdo->setAttribute(\PDO::ATTR_TIMEOUT, $segundos);
    }
    
    /**
     * Executa operação com timeout usando pcntl (se disponível)
     */
    public static function executarComTimeout(callable $operacao, int $timeout, $valorPadrao = null): mixed
    {
        // Se pcntl disponível, usar alarm
        if (function_exists('pcntl_alarm') && function_exists('pcntl_signal')) {
            $timedOut = false;
            
            pcntl_signal(SIGALRM, function() use (&$timedOut) {
                $timedOut = true;
            });
            
            pcntl_alarm($timeout);
            
            try {
                $resultado = $operacao();
                pcntl_alarm(0); // Cancela alarm
                return $resultado;
            } catch (\Throwable $e) {
                pcntl_alarm(0);
                if ($timedOut) {
                    throw new \RuntimeException("Operação excedeu timeout de {$timeout}s");
                }
                throw $e;
            }
        }
        
        // Fallback: apenas medir tempo (não interrompe)
        $inicio = microtime(true);
        $resultado = $operacao();
        $duracao = microtime(true) - $inicio;
        
        if ($duracao > $timeout) {
            Logger::warning("Operação excedeu timeout esperado", [
                'timeout' => $timeout,
                'duracao' => round($duracao, 2)
            ]);
        }
        
        return $resultado;
    }
    
    /**
     * Cria conexão PDO com timeout configurado
     */
    public static function criarConexaoComTimeout(string $dsn, string $user, string $pass, int $timeout = null): \PDO
    {
        $timeout = $timeout ?? self::$timeoutsPorTipo['conexao'];
        
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_TIMEOUT => $timeout,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ];
        
        // Para PostgreSQL
        if (str_contains($dsn, 'pgsql')) {
            $dsn .= ";connect_timeout={$timeout}";
        }
        
        $pdo = new \PDO($dsn, $user, $pass, $options);
        
        // Aplicar timeout de statement
        self::aplicarTimeoutPDO($pdo);
        
        return $pdo;
    }
    
    /**
     * Executa query com timeout
     */
    public static function executarQueryComTimeout(\PDO $pdo, string $sql, array $params = [], int $timeout = null): \PDOStatement
    {
        $timeout = $timeout ?? self::$timeoutsPorTipo['query'];
        
        // Definir timeout para esta query
        $pdo->exec("SET statement_timeout = " . ($timeout * 1000));
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } finally {
            // Restaurar timeout padrão
            $pdo->exec("SET statement_timeout = " . (self::$timeoutsPorTipo['query'] * 1000));
        }
    }
}
