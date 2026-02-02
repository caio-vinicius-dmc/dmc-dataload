<?php
namespace App\Core;

/**
 * Wrapper para criptografia AES-256-CBC
 */
class Crypto
{
    private static ?string $key = null;
    
    /**
     * Obtém a chave de criptografia do .env
     */
    private static function getKey(): string
    {
        if (self::$key === null) {
            self::$key = $_ENV['ENCRYPTION_KEY'] ?? $_SERVER['ENCRYPTION_KEY'] ?? getenv('ENCRYPTION_KEY') ?: '';
            
            // Se não tem chave, lançar erro ao invés de gerar temporária
            if (empty(self::$key)) {
                throw new \RuntimeException('ENCRYPTION_KEY não configurada no .env');
            }
        }
        return self::$key;
    }
    
    /**
     * Criptografa um texto
     */
    public static function criptografar(string $texto): string
    {
        $key = base64_decode(self::getKey());
        if (strlen($key) < 32) {
            $key = str_pad($key, 32, "\0");
        }
        
        $iv = random_bytes(16);
        $cipher = openssl_encrypt($texto, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        
        if ($cipher === false) {
            throw new \RuntimeException('Falha na criptografia');
        }
        
        return base64_encode($iv . $cipher);
    }
    
    /**
     * Descriptografa um texto
     */
    public static function descriptografar(string $criptografado): ?string
    {
        try {
            $key = base64_decode(self::getKey());
            if (strlen($key) < 32) {
                $key = str_pad($key, 32, "\0");
            }
            
            $dados = base64_decode($criptografado);
            if ($dados === false || strlen($dados) < 17) {
                return null;
            }
            
            $iv = substr($dados, 0, 16);
            $cipher = substr($dados, 16);
            
            $plain = openssl_decrypt($cipher, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
            
            return $plain === false ? null : $plain;
        } catch (\Throwable $e) {
            return null;
        }
    }
    
    /**
     * Define a chave manualmente (útil para testes)
     */
    public static function setKey(string $key): void
    {
        self::$key = $key;
    }
}
