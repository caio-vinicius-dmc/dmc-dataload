<?php
namespace App\Servicos;

use App\Core\Database;
use App\Core\AuthMiddleware;

class ServicoFila
{
    /**
     * Enfileirar uma execução para processamento em background
     */
    public static function enfileirar(string $tipo, int $idRecurso, string $nomeRecurso, int $prioridade = 5, ?string $agendadoPara = null): array
    {
        $db = Database::getConexao();
        
        // Verificar se já existe item pendente/processando para este recurso
        $stmt = $db->prepare(
            "SELECT id, status FROM tb_fila_execucao 
             WHERE tipo = :tipo AND id_recurso = :id_recurso 
             AND status IN ('pendente', 'processando')
             LIMIT 1"
        );
        $stmt->execute([':tipo' => $tipo, ':id_recurso' => $idRecurso]);
        $existente = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($existente) {
            return [
                'sucesso' => false,
                'erro' => 'Já existe uma execução pendente/em andamento para este recurso',
                'fila_id' => $existente['id']
            ];
        }
        
        $idUsuario = null;
        $nomeUsuario = null;
        try {
            $idUsuario = AuthMiddleware::obterUsuarioId();
            $usuario = AuthMiddleware::obterUsuario();
            $nomeUsuario = $usuario['nome_usuario'] ?? null;
        } catch (\Throwable $e) {
            // Worker CLI não tem sessão
        }
        
        $stmt = $db->prepare(
            "INSERT INTO tb_fila_execucao (tipo, id_recurso, nome_recurso, prioridade, agendado_para, id_usuario, nome_usuario)
             VALUES (:tipo, :id_recurso, :nome_recurso, :prioridade, :agendado_para, :id_usuario, :nome_usuario)
             RETURNING id"
        );
        $stmt->execute([
            ':tipo' => $tipo,
            ':id_recurso' => $idRecurso,
            ':nome_recurso' => $nomeRecurso,
            ':prioridade' => max(1, min(10, $prioridade)),
            ':agendado_para' => $agendadoPara ?? date('Y-m-d H:i:s'),
            ':id_usuario' => $idUsuario,
            ':nome_usuario' => $nomeUsuario
        ]);
        
        $filaId = $stmt->fetchColumn();
        
        return [
            'sucesso' => true,
            'fila_id' => $filaId,
            'mensagem' => 'Execução enfileirada com sucesso'
        ];
    }
    
    /**
     * Obter próximo item da fila para processamento (com lock)
     */
    public static function obterProximo(string $workerId): ?array
    {
        $db = Database::getConexao();
        
        $stmt = $db->prepare(
            "UPDATE tb_fila_execucao 
             SET status = 'processando', iniciado_em = CURRENT_TIMESTAMP, worker_id = :worker_id, tentativas = tentativas + 1
             WHERE id = (
                SELECT id FROM tb_fila_execucao 
                WHERE status = 'pendente' AND agendado_para <= CURRENT_TIMESTAMP
                ORDER BY prioridade ASC, criado_em ASC
                LIMIT 1
                FOR UPDATE SKIP LOCKED
             )
             RETURNING *"
        );
        $stmt->execute([':worker_id' => $workerId]);
        $item = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $item ?: null;
    }
    
    /**
     * Marcar item como concluído
     */
    public static function concluir(int $filaId, array $resultado): void
    {
        $db = Database::getConexao();
        $db->prepare(
            "UPDATE tb_fila_execucao 
             SET status = 'concluido', concluido_em = CURRENT_TIMESTAMP, resultado = :resultado::jsonb
             WHERE id = :id"
        )->execute([
            ':id' => $filaId,
            ':resultado' => json_encode($resultado)
        ]);
    }
    
    /**
     * Marcar item como falha (com retry automático se não excedeu max_tentativas)
     */
    public static function falhar(int $filaId, string $erro, array $resultado = []): void
    {
        $db = Database::getConexao();
        
        // Verificar se pode tentar novamente
        $stmt = $db->prepare("SELECT tentativas, max_tentativas FROM tb_fila_execucao WHERE id = :id");
        $stmt->execute([':id' => $filaId]);
        $item = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($item && $item['tentativas'] < $item['max_tentativas']) {
            // Reagendar para retry (backoff exponencial: 30s, 2min, 8min)
            $delay = (int)(pow(4, $item['tentativas']) * 30);
            $db->prepare(
                "UPDATE tb_fila_execucao 
                 SET status = 'pendente', erro = :erro, resultado = :resultado::jsonb,
                     agendado_para = CURRENT_TIMESTAMP + make_interval(secs => :delay)
                 WHERE id = :id"
            )->execute([
                ':id' => $filaId,
                ':erro' => $erro,
                ':resultado' => json_encode($resultado),
                ':delay' => $delay
            ]);
        } else {
            $db->prepare(
                "UPDATE tb_fila_execucao 
                 SET status = 'falha', concluido_em = CURRENT_TIMESTAMP, erro = :erro, resultado = :resultado::jsonb
                 WHERE id = :id"
            )->execute([
                ':id' => $filaId,
                ':erro' => $erro,
                ':resultado' => json_encode($resultado)
            ]);
        }
    }
    
    /**
     * Cancelar item da fila
     */
    public static function cancelar(int $filaId): bool
    {
        $db = Database::getConexao();
        $stmt = $db->prepare(
            "UPDATE tb_fila_execucao 
             SET status = 'cancelado', concluido_em = CURRENT_TIMESTAMP
             WHERE id = :id AND status IN ('pendente')
             RETURNING id"
        );
        $stmt->execute([':id' => $filaId]);
        return (bool)$stmt->fetchColumn();
    }
    
    /**
     * Obter status de um item da fila
     */
    public static function status(int $filaId): ?array
    {
        $db = Database::getConexao();
        $stmt = $db->prepare("SELECT * FROM tb_fila_execucao WHERE id = :id");
        $stmt->execute([':id' => $filaId]);
        $item = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$item) return null;
        
        if ($item['resultado']) {
            $item['resultado'] = json_decode($item['resultado'], true);
        }
        
        return $item;
    }
    
    /**
     * Listar itens da fila com filtros
     */
    public static function listar(?string $status = null, ?string $tipo = null, int $limite = 50, int $offset = 0): array
    {
        $db = Database::getConexao();
        $where = [];
        $params = [];
        
        if ($status) {
            $where[] = "status = :status";
            $params[':status'] = $status;
        }
        if ($tipo) {
            $where[] = "tipo = :tipo";
            $params[':tipo'] = $tipo;
        }
        
        $whereStr = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $stmt = $db->prepare(
            "SELECT id, tipo, id_recurso, nome_recurso, status, prioridade, tentativas, max_tentativas,
                    agendado_para, iniciado_em, concluido_em, erro, id_usuario, nome_usuario, worker_id, criado_em
             FROM tb_fila_execucao $whereStr
             ORDER BY criado_em DESC LIMIT :limite OFFSET :offset"
        );
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        $total = $db->prepare("SELECT COUNT(*) FROM tb_fila_execucao $whereStr");
        foreach ($params as $k => $v) {
            $total->bindValue($k, $v);
        }
        $total->execute();
        
        return [
            'dados' => $stmt->fetchAll(\PDO::FETCH_ASSOC),
            'total' => (int)$total->fetchColumn()
        ];
    }
    
    /**
     * Limpar itens processados/antigos da fila
     */
    public static function limpar(int $diasAntigos = 30): int
    {
        $db = Database::getConexao();
        $stmt = $db->prepare(
            "DELETE FROM tb_fila_execucao 
             WHERE status IN ('concluido', 'falha', 'cancelado') 
             AND criado_em < CURRENT_TIMESTAMP - make_interval(days => :dias)"
        );
        $stmt->execute([':dias' => $diasAntigos]);
        return $stmt->rowCount();
    }
    
    /**
     * Detectar e recuperar items travados (processando há mais de 1 hora)
     */
    public static function recuperarTravados(): int
    {
        $db = Database::getConexao();
        $stmt = $db->prepare(
            "UPDATE tb_fila_execucao 
             SET status = 'pendente', worker_id = NULL
             WHERE status = 'processando' 
             AND iniciado_em < CURRENT_TIMESTAMP - interval '1 hour'
             AND tentativas < max_tentativas"
        );
        $stmt->execute();
        return $stmt->rowCount();
    }
    
    /**
     * Estatísticas da fila
     */
    public static function estatisticas(): array
    {
        $db = Database::getConexao();
        $stats = $db->query(
            "SELECT status, COUNT(*) as total FROM tb_fila_execucao GROUP BY status"
        )->fetchAll(\PDO::FETCH_KEY_PAIR);
        
        $pendentes = (int)($stats['pendente'] ?? 0);
        $processando = (int)($stats['processando'] ?? 0);
        $concluidos = (int)($stats['concluido'] ?? 0);
        $falhas = (int)($stats['falha'] ?? 0);
        $cancelados = (int)($stats['cancelado'] ?? 0);
        
        return [
            'pendentes' => $pendentes,
            'processando' => $processando,
            'concluidos' => $concluidos,
            'falhas' => $falhas,
            'cancelados' => $cancelados,
            'total' => $pendentes + $processando + $concluidos + $falhas + $cancelados
        ];
    }
}
