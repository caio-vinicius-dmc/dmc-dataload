<?php
namespace App\Controllers;

use App\Core\Database;
use App\Servicos\ServicoExecucao;
use PDO;

class RotinasController
{
    public function executar(int $id, int $iniciarDeBloco = 1, array $blocosSelecionados = []): array
    {
        $svc = new ServicoExecucao();
        return $svc->executarRotina($id, $iniciarDeBloco, $blocosSelecionados);
    }

    public function listar(): array
    {
        $db = Database::getConexao();
        $filtro = \App\Servicos\ServicoPermissao::filtroVisibilidade('rotina', 'r', 'id_usuario_criador');
        $sql = "SELECT r.id, r.nome, r.descricao, r.esta_executando, r.ativa, r.agendamento_cron,
                       r.proxima_execucao, r.ultima_execucao, r.tentativas_falha,
                       p.nome_conexao 
                FROM tb_rotinas r JOIN tb_perfis_conexao p ON r.id_conexao = p.id 
                WHERE ({$filtro['where']}) ORDER BY r.id DESC";
        $s = $db->prepare($sql);
        $s->execute($filtro['params']);
        $rows = $s->fetchAll(PDO::FETCH_ASSOC);
        return ['data' => $rows];
    }

    public function buscar(int $id): array
    {
        $db = Database::getConexao();
        $s = $db->prepare('SELECT * FROM tb_rotinas WHERE id = ?');
        $s->execute([$id]);
        $rotina = $s->fetch(PDO::FETCH_ASSOC);
        
        if (!$rotina) {
            return ['rotina' => null, 'blocos' => []];
        }
        
        // Buscar blocos da rotina
        $b = $db->prepare('SELECT * FROM tb_blocos_rotina WHERE id_rotina = ? ORDER BY ordem');
        $b->execute([$id]);
        $blocos = $b->fetchAll(PDO::FETCH_ASSOC);
        
        return ['rotina' => $rotina, 'blocos' => $blocos];
    }

    public function salvar(array $data): array
    {
        $db = Database::getConexao();
        if (!empty($data['id'])) {
            $pararEmErro = !empty($data['parar_em_erro']);
            $rollbackEmErro = !empty($data['rollback_em_erro']);
            $u = $db->prepare('UPDATE tb_rotinas SET nome=?, descricao=?, id_conexao=?, webhook_sucesso=?, webhook_falha=?, parar_em_erro=?::boolean, rollback_em_erro=?::boolean WHERE id=?');
            $u->execute([$data['nome'], $data['descricao'], $data['id_conexao'], $data['webhook_sucesso'] ?? null, $data['webhook_falha'] ?? null, $pararEmErro ? 't' : 'f', $rollbackEmErro ? 't' : 'f', $data['id']]);
            
            // Remover blocos antigos
            $del = $db->prepare('DELETE FROM tb_blocos_rotina WHERE id_rotina = ?');
            $del->execute([$data['id']]);
            
            // Inserir novos blocos
            if (!empty($data['bloco_codigo']) && is_array($data['bloco_codigo'])) {
                $ord = 1;
                foreach ($data['bloco_codigo'] as $i => $codigo) {
                    $sql = $data['script_sql'][$i] ?? '';
                    $tipo = $data['tipo_bloco'][$i] ?? 'SELECT';
                    $b = $db->prepare('INSERT INTO tb_blocos_rotina (id_rotina, codigo_bloco, ordem, script_sql, tipo_bloco) VALUES (?, ?, ?, ?, ?)');
                    $b->execute([$data['id'], $codigo, $ord++, $sql, $tipo]);
                }
            }
            
            return ['sucesso' => true, 'mensagem' => 'Atualizado', 'id' => $data['id']];
        }

        $pararEmErro = !empty($data['parar_em_erro']);
        $rollbackEmErro = !empty($data['rollback_em_erro']);
        $ins = $db->prepare('INSERT INTO tb_rotinas (nome, descricao, id_conexao, id_usuario_criador, webhook_sucesso, webhook_falha, parar_em_erro, rollback_em_erro) VALUES (?, ?, ?, ?, ?, ?, ?::boolean, ?::boolean) RETURNING id');
        $ins->execute([$data['nome'], $data['descricao'], $data['id_conexao'], $data['id_usuario_criador'] ?? null, $data['webhook_sucesso'] ?? null, $data['webhook_falha'] ?? null, $pararEmErro ? 't' : 'f', $rollbackEmErro ? 't' : 'f']);
        $id = $ins->fetchColumn();
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
        return ['sucesso' => true, 'id' => $id];
    }

    public function deletar(int $id): array
    {
        $db = Database::getConexao();
        $d = $db->prepare('DELETE FROM tb_rotinas WHERE id = ?');
        $d->execute([$id]);
        return ['sucesso' => true];
    }
}

