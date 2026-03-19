<?php
/**
 * DMC DataLoad - Rate Limiter
 * Controle de taxa de requisições usando banco de dados
 */

namespace App\Core;

use PDO;

class RateLimiter
{
    private PDO $db;

    public function __construct(PDO $db = null)
    {
        $this->db = $db ?? Database::getConexao();
    }

    /**
     * Verificar se a ação é permitida dentro do limite
     * 
     * @param string $chave Identificador único (ex: "api_test:{session_id}")
     * @param int $maxTentativas Máximo de tentativas permitidas
     * @param int $janelaSeg Janela de tempo em segundos
     * @return bool true se permitido, false se excedeu limite
     */
    public function permitir(string $chave, int $maxTentativas = 10, int $janelaSeg = 60): bool
    {
        // Limpar registros expirados
        $this->limparExpirados($janelaSeg);
        
        // Verificar se existe registro e se está dentro da janela (tudo no DB para evitar timezone mismatch)
        $stmt = $this->db->prepare("
            SELECT tentativas, 
                   EXTRACT(EPOCH FROM (NOW() - primeira_tentativa)) as segundos_desde_inicio
            FROM tb_rate_limits 
            WHERE chave = ?
        ");
        $stmt->execute([$chave]);
        $registro = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$registro) {
            // Primeira tentativa - inserir novo registro
            $stmt = $this->db->prepare("
                INSERT INTO tb_rate_limits (chave, tentativas, primeira_tentativa, ultima_tentativa)
                VALUES (?, 1, NOW(), NOW())
                ON CONFLICT (chave) DO UPDATE SET 
                    tentativas = tb_rate_limits.tentativas + 1,
                    ultima_tentativa = NOW()
            ");
            $stmt->execute([$chave]);
            return true;
        }
        
        $segundosDesdeInicio = (float)$registro['segundos_desde_inicio'];
        
        if ($segundosDesdeInicio > $janelaSeg) {
            // Janela expirou, resetar
            $stmt = $this->db->prepare("
                UPDATE tb_rate_limits SET 
                    tentativas = 1, 
                    primeira_tentativa = NOW(), 
                    ultima_tentativa = NOW() 
                WHERE chave = ?
            ");
            $stmt->execute([$chave]);
            return true;
        }
        
        // Dentro da janela, verificar limite
        if ((int)$registro['tentativas'] >= $maxTentativas) {
            return false;
        }
        
        // Incrementar
        $stmt = $this->db->prepare("
            UPDATE tb_rate_limits SET 
                tentativas = tentativas + 1, 
                ultima_tentativa = NOW() 
            WHERE chave = ?
        ");
        $stmt->execute([$chave]);
        return true;
    }

    /**
     * Obter tentativas restantes
     */
    public function tentativasRestantes(string $chave, int $maxTentativas = 10, int $janelaSeg = 60): int
    {
        $stmt = $this->db->prepare("
            SELECT tentativas, 
                   EXTRACT(EPOCH FROM (NOW() - primeira_tentativa)) as segundos_desde_inicio
            FROM tb_rate_limits 
            WHERE chave = ?
        ");
        $stmt->execute([$chave]);
        $registro = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$registro) {
            return $maxTentativas;
        }
        
        if ((float)$registro['segundos_desde_inicio'] > $janelaSeg) {
            return $maxTentativas;
        }
        
        return max(0, $maxTentativas - (int)$registro['tentativas']);
    }

    /**
     * Limpar registros expirados
     */
    private function limparExpirados(int $janelaSeg): void
    {
        $this->db->prepare("
            DELETE FROM tb_rate_limits 
            WHERE ultima_tentativa < NOW() - (? || ' seconds')::interval
        ")->execute([$janelaSeg * 2]);
    }
}
