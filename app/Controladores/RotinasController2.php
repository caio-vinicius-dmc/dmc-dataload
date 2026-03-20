<?php
namespace App\Controllers;

use App\Core\Database;
use App\Servicos\ServicoExecucao;
use PDO;

class RotinasController2
{
    public function executar(int $id): array
    {
        try {
            error_log("RotinasController2::executar - ID: {$id}");
            $svc = new ServicoExecucao();
            $result = $svc->executarRotina($id);
            error_log("RotinasController2::executar - Resultado: " . json_encode($result));
            return $result;
        } catch (\Exception $e) {
            error_log("RotinasController2::executar - Exception: " . $e->getMessage());
            return ['sucesso' => false, 'erro' => $e->getMessage(), 'mensagem' => $e->getMessage()];
        }
    }

    public function listar(): array
    {
        $db = Database::getConexao();
        $filtro = \App\Servicos\ServicoPermissao::filtroVisibilidade('rotina', 'r', 'id_usuario_criador');
        $s = $db->prepare("SELECT r.id, r.nome, r.descricao, r.esta_executando, r.ativa, r.agendamento_cron, 
                                r.proxima_execucao, r.ultima_execucao, r.tentativas_falha,
                                p.nome_conexao 
                         FROM tb_rotinas r 
                         JOIN tb_perfis_conexao p ON r.id_conexao = p.id 
                         WHERE ({$filtro['where']})
                         ORDER BY r.id DESC");
        $s->execute($filtro['params']);
        $rows = $s->fetchAll(PDO::FETCH_ASSOC);
        return ['sucesso' => true, 'dados' => $rows, 'data' => $rows];
    }

    public function buscar(int $id): array
    {
        $db = Database::getConexao();
        $s = $db->prepare('SELECT * FROM tb_rotinas WHERE id = ?');
        $s->execute([$id]);
        $r = $s->fetch(PDO::FETCH_ASSOC);
        if (!$r) return [];

        $b = $db->prepare('SELECT codigo_bloco, ordem, script_sql, tipo_bloco FROM tb_blocos_rotina WHERE id_rotina = ? ORDER BY ordem');
        $b->execute([$id]);
        $blocos = $b->fetchAll(PDO::FETCH_ASSOC);

        return ['rotina' => $r, 'blocos' => $blocos];
    }

    public function salvar(array $data): array
    {
        // debug dump incoming data
        @file_put_contents(__DIR__ . '/../../storage/rotina_debug.json', json_encode($data, JSON_PRETTY_PRINT));
        
        try {
            $db = Database::getConexao();
            
            if (!empty($data['id'])) {
                // Converter ativa para boolean (pode vir como string vazia do form)
                $ativa = false;
                if (isset($data['ativa'])) {
                    if (is_bool($data['ativa'])) {
                        $ativa = $data['ativa'];
                    } else if (is_string($data['ativa'])) {
                        $ativa = in_array(strtolower($data['ativa']), ['1', 'true', 'on', 'yes']);
                    } else {
                        $ativa = (bool)$data['ativa'];
                    }
                }
                
                // Campos opcionais - converter string vazia para null
                $webhookSucesso = !empty($data['webhook_sucesso']) ? $data['webhook_sucesso'] : null;
                $webhookFalha = !empty($data['webhook_falha']) ? $data['webhook_falha'] : null;
                $agendamentoCron = !empty($data['agendamento_cron']) ? $data['agendamento_cron'] : null;
                $maxTentativas = !empty($data['max_tentativas']) ? (int)$data['max_tentativas'] : 3;
                
                // Debug
                $debugParams = [
                    'nome' => $data['nome'],
                    'descricao' => $data['descricao'],
                    'id_conexao' => $data['id_conexao'],
                    'webhook_sucesso' => $webhookSucesso,
                    'webhook_falha' => $webhookFalha,
                    'ativa' => $ativa,
                    'agendamento_cron' => $agendamentoCron,
                    'max_tentativas' => $maxTentativas,
                    'id' => $data['id']
                ];
                @file_put_contents(__DIR__ . '/../../storage/update_params.json', json_encode($debugParams, JSON_PRETTY_PRINT));
                
                $u = $db->prepare('UPDATE tb_rotinas SET nome=?, descricao=?, id_conexao=?, webhook_sucesso=?, webhook_falha=?, ativa=?::boolean, agendamento_cron=?, max_tentativas=? WHERE id=?');
                $u->execute([
                    $data['nome'], 
                    $data['descricao'], 
                    $data['id_conexao'], 
                    $webhookSucesso, 
                    $webhookFalha,
                    $ativa ? 't' : 'f',  // PostgreSQL boolean
                    $agendamentoCron,
                    $maxTentativas,
                    $data['id']
                ]);
            
            // Se ativou agendamento, calcular próxima execução
            if ($ativa && $agendamentoCron) {
                $this->atualizarProximaExecucao($data['id']);
            }
            
            // regravar blocos: apagamos os existentes e re-inserimos na ordem enviada
            if (!empty($data['bloco_codigo']) && is_array($data['bloco_codigo'])) {
                $db->prepare('DELETE FROM tb_blocos_rotina WHERE id_rotina = ?')->execute([$data['id']]);
                $ord = 1;
                foreach ($data['bloco_codigo'] as $i => $codigo) {
                    $sql = $data['script_sql'][$i] ?? '';
                    $tipo = $data['tipo_bloco'][$i] ?? 'SELECT';
                    $b = $db->prepare('INSERT INTO tb_blocos_rotina (id_rotina, codigo_bloco, ordem, script_sql, tipo_bloco) VALUES (?, ?, ?, ?, ?)');
                    $b->execute([$data['id'], $codigo, $ord++, $sql, $tipo]);
                }
            }
            // Associar empresas/projetos
            if (isset($data['empresas']) && is_array($data['empresas'])) {
                \App\Servicos\ServicoPermissao::associarRecursoEmpresas('rotina', (int)$data['id'], array_map('intval', $data['empresas']));
            }
            if (isset($data['projetos']) && is_array($data['projetos'])) {
                \App\Servicos\ServicoPermissao::associarRecursoProjetos('rotina', (int)$data['id'], array_map('intval', $data['projetos']));
            }
            return ['sucesso' => true, 'mensagem' => 'Atualizado'];
        }
        // construir INSERT sem id_usuario_criador quando não informado para evitar FK errors
        $cols = ['nome', 'descricao', 'id_conexao', 'id_usuario_criador'];
        $placeholders = ['?', '?', '?', '?'];
        $params = [$data['nome'], $data['descricao'], $data['id_conexao'], $data['id_usuario_criador'] ?? null];

        if (isset($data['webhook_sucesso'])) {
            $cols[] = 'webhook_sucesso';
            $placeholders[] = '?';
            $params[] = $data['webhook_sucesso'];
        }
        if (isset($data['webhook_falha'])) {
            $cols[] = 'webhook_falha';
            $placeholders[] = '?';
            $params[] = $data['webhook_falha'];
        }

        $sql = 'INSERT INTO tb_rotinas (' . implode(', ', $cols) . ') VALUES (' . implode(', ', $placeholders) . ') RETURNING id';
        $ins = $db->prepare($sql);
        // debug SQL + params
        @file_put_contents(__DIR__ . '/../../storage/rotina_sql_debug.json', json_encode(['sql'=>$sql, 'params'=>$params], JSON_PRETTY_PRINT));
        try {
            $ins->execute($params);
            $id = $ins->fetchColumn();
        } catch (\PDOException $e) {
            return ['sucesso' => false, 'erro' => $e->getMessage(), 'sql' => $sql, 'params' => $params];
        }
        // salvar blocos se enviado
        if (!empty($data['bloco_codigo']) && is_array($data['bloco_codigo'])) {
            $ord = 1;
            foreach ($data['bloco_codigo'] as $i => $codigo) {
                $sql = $data['script_sql'][$i] ?? '';
                $tipo = $data['tipo_bloco'][$i] ?? 'SELECT';
                $b = $db->prepare('INSERT INTO tb_blocos_rotina (id_rotina, codigo_bloco, ordem, script_sql, tipo_bloco) VALUES (?, ?, ?, ?, ?)');
                $b->execute([$id, $codigo, $ord++, $sql, $tipo]);
            }
        }
        // Associar empresas/projetos
        if (isset($data['empresas']) && is_array($data['empresas'])) {
            \App\Servicos\ServicoPermissao::associarRecursoEmpresas('rotina', (int)$id, array_map('intval', $data['empresas']));
        }
        if (isset($data['projetos']) && is_array($data['projetos'])) {
            \App\Servicos\ServicoPermissao::associarRecursoProjetos('rotina', (int)$id, array_map('intval', $data['projetos']));
        }
        return ['sucesso' => true, 'id' => $id];
        
        } catch (\PDOException $e) {
            @file_put_contents(__DIR__ . '/../../storage/error_debug.txt', $e->getMessage() . "\n" . $e->getTraceAsString());
            return ['sucesso' => false, 'erro' => 'Erro ao salvar: ' . $e->getMessage()];
        } catch (\Exception $e) {
            @file_put_contents(__DIR__ . '/../../storage/error_debug.txt', $e->getMessage() . "\n" . $e->getTraceAsString());
            return ['sucesso' => false, 'erro' => $e->getMessage()];
        }
    }

    public function deletar(int $id): array
    {
        $db = Database::getConexao();
        $d = $db->prepare('DELETE FROM tb_rotinas WHERE id = ?');
        $d->execute([$id]);
        return ['sucesso' => true];
    }
    
    /**
     * Alterna o status de ativação de uma rotina
     */
    public function toggleAtiva(int $id): array
    {
        $db = Database::getConexao();
        
        $stmt = $db->prepare('SELECT ativa FROM tb_rotinas WHERE id = ?');
        $stmt->execute([$id]);
        $atual = $stmt->fetchColumn();
        
        $novo = !$atual;
        $db->prepare('UPDATE tb_rotinas SET ativa = ?, tentativas_falha = 0 WHERE id = ?')
           ->execute([$novo, $id]);
        
        if ($novo) {
            $this->atualizarProximaExecucao($id);
        }
        
        return ['sucesso' => true, 'ativa' => $novo];
    }
    
    /**
     * Calcula e atualiza a próxima execução baseado no cron
     */
    private function atualizarProximaExecucao(int $id): void
    {
        $db = Database::getConexao();
        
        $stmt = $db->prepare('SELECT agendamento_cron FROM tb_rotinas WHERE id = ?');
        $stmt->execute([$id]);
        $cron = $stmt->fetchColumn();
        
        if (!$cron) return;
        
        // Parser simples - próxima execução = agora + 1 minuto
        // Em produção, usar biblioteca cron-expression
        $proxima = (new \DateTime())->modify('+1 minute');
        
        $db->prepare('UPDATE tb_rotinas SET proxima_execucao = ? WHERE id = ?')
           ->execute([$proxima->format('Y-m-d H:i:s'), $id]);
    }
    
    /**
     * Obtém estatísticas de uma rotina
     */
    public function estatisticas(int $id): array
    {
        $db = Database::getConexao();
        
        $stats = $db->prepare("SELECT 
            COUNT(*) as total_execucoes,
            COUNT(*) FILTER (WHERE status = 'sucesso') as sucesso,
            COUNT(*) FILTER (WHERE status = 'falha') as falha,
            AVG(duracao_ms) as tempo_medio,
            MAX(data_inicio) as ultima_execucao
            FROM tb_logs_execucao 
            WHERE id_rotina = ?");
        $stats->execute([$id]);
        
        return ['sucesso' => true, 'dados' => $stats->fetch(PDO::FETCH_ASSOC)];
    }
}
