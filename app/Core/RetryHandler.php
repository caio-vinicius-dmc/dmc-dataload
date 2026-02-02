<?php
namespace App\Core;

/**
 * Retry com backoff exponencial para operações falhas
 */
class RetryHandler
{
    /**
     * Executa uma operação com retry automático
     * 
     * @param callable $operacao Função a executar
     * @param int $maxTentativas Número máximo de tentativas
     * @param int $delayBase Delay base em segundos (dobra a cada tentativa)
     * @param array $excecoesFatais Exceções que não devem ter retry
     */
    public static function executar(
        callable $operacao,
        int $maxTentativas = 3,
        int $delayBase = 1,
        array $excecoesFatais = []
    ): mixed {
        $tentativa = 0;
        $ultimaExcecao = null;
        
        while ($tentativa < $maxTentativas) {
            try {
                return $operacao();
            } catch (\Throwable $e) {
                $ultimaExcecao = $e;
                $tentativa++;
                
                // Verificar se é exceção fatal (não deve tentar novamente)
                foreach ($excecoesFatais as $tipoFatal) {
                    if ($e instanceof $tipoFatal) {
                        throw $e;
                    }
                }
                
                // Verificar se deve tentar novamente
                if (!self::deveRetry($e)) {
                    throw $e;
                }
                
                if ($tentativa < $maxTentativas) {
                    $delay = $delayBase * pow(2, $tentativa - 1);
                    
                    Logger::warning("Retry {$tentativa}/{$maxTentativas}", [
                        'erro' => $e->getMessage(),
                        'delay_segundos' => $delay
                    ]);
                    
                    sleep($delay);
                }
            }
        }
        
        Logger::error("Todas as tentativas falharam", [
            'tentativas' => $maxTentativas,
            'ultimo_erro' => $ultimaExcecao?->getMessage()
        ]);
        
        throw $ultimaExcecao;
    }
    
    /**
     * Executa operação de banco com retry
     */
    public static function executarDb(callable $operacao, int $maxTentativas = 3): mixed
    {
        return self::executar(
            $operacao,
            $maxTentativas,
            1,
            [\InvalidArgumentException::class] // Erros de lógica não devem ter retry
        );
    }
    
    /**
     * Executa requisição HTTP com retry
     */
    public static function executarHttp(callable $operacao, int $maxTentativas = 3): mixed
    {
        return self::executar(
            $operacao,
            $maxTentativas,
            2, // Delay maior para HTTP
            []
        );
    }
    
    /**
     * Verifica se a exceção é transiente e pode ter retry
     */
    private static function deveRetry(\Throwable $e): bool
    {
        $mensagem = strtolower($e->getMessage());
        
        // Erros de conexão transientes
        $errosTransientes = [
            'connection refused',
            'connection timed out',
            'connection reset',
            'no route to host',
            'network is unreachable',
            'temporary failure',
            'too many connections',
            'server has gone away',
            'deadlock',
            'lock wait timeout',
            'serialization failure',
            'could not connect',
            'broken pipe',
            'ssl connection',
            'operation timed out',
            'timed out',
            'timeout',
        ];
        
        foreach ($errosTransientes as $erro) {
            if (str_contains($mensagem, $erro)) {
                return true;
            }
        }
        
        // Códigos de erro SQL transientes
        if ($e instanceof \PDOException) {
            $codigosTransientes = ['08006', '08001', '08004', '40001', '40P01', '57P01'];
            if (in_array($e->getCode(), $codigosTransientes)) {
                return true;
            }
        }
        
        return false;
    }
}
