<?php

namespace App\Servicos;

use App\Core\Database;
use App\Core\AuthMiddleware;

class ServicoAuditoria
{
    /**
     * Registra uma ação de auditoria
     */
    public static function registrar(
        string $acao,
        string $entidade,
        ?int $entidadeId = null,
        ?string $entidadeNome = null,
        array $dadosAnteriores = [],
        array $dadosNovos = []
    ): void {
        try {
            $db = Database::getConexao();
            $usuario = AuthMiddleware::obterUsuario();
            $idUsuario = $usuario['id'] ?? 0;
            $nomeUsuario = $usuario['nome_usuario'] ?? 'sistema';
            $nivelAcesso = $usuario['nivel_acesso'] ?? '';

            $stmt = $db->prepare("
                INSERT INTO tb_auditoria 
                    (acao, entidade, entidade_id, entidade_nome, id_usuario, nome_usuario, nivel_acesso, 
                     dados_anteriores, dados_novos, ip_address, user_agent)
                VALUES 
                    (:acao, :entidade, :entidade_id, :entidade_nome, :id_usuario, :nome_usuario, :nivel_acesso,
                     :dados_anteriores, :dados_novos, :ip, :ua)
            ");
            $stmt->execute([
                ':acao' => $acao,
                ':entidade' => $entidade,
                ':entidade_id' => $entidadeId,
                ':entidade_nome' => $entidadeNome,
                ':id_usuario' => $idUsuario,
                ':nome_usuario' => $nomeUsuario,
                ':nivel_acesso' => $nivelAcesso,
                ':dados_anteriores' => json_encode($dadosAnteriores, JSON_UNESCAPED_UNICODE),
                ':dados_novos' => json_encode($dadosNovos, JSON_UNESCAPED_UNICODE),
                ':ip' => $_SERVER['REMOTE_ADDR'] ?? '',
                ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            ]);
        } catch (\Exception $e) {
            // Auditoria não deve quebrar a operação principal
            error_log('[AUDITORIA] Erro: ' . $e->getMessage());
        }
    }

    /**
     * Listar registros de auditoria com filtros
     */
    public static function listar(array $filtros = []): array
    {
        $db = Database::getConexao();

        $where = [];
        $params = [];

        if (!empty($filtros['acao'])) {
            $where[] = "a.acao = :acao";
            $params[':acao'] = $filtros['acao'];
        }
        if (!empty($filtros['entidade'])) {
            $where[] = "a.entidade = :entidade";
            $params[':entidade'] = $filtros['entidade'];
        }
        if (!empty($filtros['id_usuario'])) {
            $where[] = "a.id_usuario = :id_usuario";
            $params[':id_usuario'] = (int) $filtros['id_usuario'];
        }
        if (!empty($filtros['data_de'])) {
            $where[] = "a.criado_em >= :data_de";
            $params[':data_de'] = $filtros['data_de'];
        }
        if (!empty($filtros['data_ate'])) {
            $where[] = "a.criado_em <= :data_ate";
            $params[':data_ate'] = $filtros['data_ate'] . ' 23:59:59';
        }
        if (!empty($filtros['busca'])) {
            $where[] = "(a.entidade_nome ILIKE :busca OR a.nome_usuario ILIKE :busca OR a.acao ILIKE :busca)";
            $params[':busca'] = '%' . $filtros['busca'] . '%';
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $pagina = max(1, (int) ($filtros['pagina'] ?? 1));
        $porPagina = min(100, max(10, (int) ($filtros['por_pagina'] ?? 50)));
        $offset = ($pagina - 1) * $porPagina;

        // Total
        $stmtTotal = $db->prepare("SELECT COUNT(*) FROM tb_auditoria a $whereClause");
        $stmtTotal->execute($params);
        $total = (int) $stmtTotal->fetchColumn();

        // Dados
        $stmt = $db->prepare("
            SELECT a.* FROM tb_auditoria a
            $whereClause
            ORDER BY a.criado_em DESC
            LIMIT :limite OFFSET :offset
        ");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limite', $porPagina, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $registros = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Estatísticas
        $stmtStats = $db->query("
            SELECT 
                COUNT(*) as total,
                COUNT(*) FILTER (WHERE acao = 'criar') as criados,
                COUNT(*) FILTER (WHERE acao = 'editar') as editados,
                COUNT(*) FILTER (WHERE acao = 'excluir') as excluidos,
                COUNT(*) FILTER (WHERE acao = 'login') as logins,
                COUNT(DISTINCT id_usuario) as usuarios_unicos
            FROM tb_auditoria
        ");
        $stats = $stmtStats->fetch(\PDO::FETCH_ASSOC);

        return [
            'sucesso' => true,
            'registros' => $registros,
            'total' => $total,
            'pagina' => $pagina,
            'por_pagina' => $porPagina,
            'estatisticas' => $stats,
        ];
    }

    /**
     * Exportar auditoria como CSV
     */
    public static function exportar(array $filtros = []): void
    {
        $dados = self::listar(array_merge($filtros, ['por_pagina' => 10000]));

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="auditoria_' . date('Y-m-d_His') . '.csv"');

        $fp = fopen('php://output', 'w');
        fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
        fputcsv($fp, ['ID', 'Data', 'Ação', 'Entidade', 'ID Recurso', 'Nome Recurso', 'Usuário', 'Nível', 'IP']);

        foreach ($dados['registros'] as $r) {
            fputcsv($fp, [
                $r['id'],
                $r['criado_em'],
                $r['acao'],
                $r['entidade'],
                $r['entidade_id'],
                $r['entidade_nome'],
                $r['nome_usuario'],
                $r['nivel_acesso'],
                $r['ip_address'],
            ]);
        }
        fclose($fp);
    }
}
