<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\AuthMiddleware;
use App\Servicos\ServicoPermissao;
use App\Servicos\ServicoAuditoria;

class ArquivosGeradosController
{
    private string $storagePath;

    public function __construct()
    {
        $this->storagePath = realpath(__DIR__ . '/../../storage/logs') ?: (__DIR__ . '/../../storage/logs');
    }

    /**
     * Garante que a tabela de políticas de retenção existe
     */
    private function garantirTabelaPoliticas(): void
    {
        $db = Database::getConexao();
        $db->exec("
            CREATE TABLE IF NOT EXISTS tb_politica_retencao_arquivos (
                id SERIAL PRIMARY KEY,
                id_rotina INTEGER NOT NULL,
                dias_retencao INTEGER NOT NULL DEFAULT 30,
                ativo BOOLEAN DEFAULT true,
                criado_por INTEGER,
                criado_em TIMESTAMPTZ DEFAULT now(),
                atualizado_em TIMESTAMPTZ DEFAULT now(),
                CONSTRAINT fk_politica_rotina FOREIGN KEY (id_rotina) REFERENCES tb_rotinas(id) ON DELETE CASCADE,
                CONSTRAINT uq_politica_rotina UNIQUE (id_rotina)
            )
        ");
    }

    /**
     * Lista arquivos gerados com RBAC
     */
    public function listar(): void
    {
        header('Content-Type: application/json');
        try {
            $db = Database::getConexao();

            // Calcular IDs de rotinas visíveis via RBAC
            $filtro = ServicoPermissao::filtroVisibilidadePosicional('rotina', 'r', 'id_usuario_criador');
            $stmt = $db->prepare("SELECT r.id FROM tb_rotinas r WHERE ({$filtro['where']})");
            $stmt->execute($filtro['params']);
            $rotinasVisiveis = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            if (empty($rotinasVisiveis)) {
                echo json_encode([
                    'sucesso' => true,
                    'arquivos' => [],
                    'total' => 0,
                    'tamanho_total' => 0
                ]);
                return;
            }

            $inSql = '(' . implode(',', array_map('intval', $rotinasVisiveis)) . ')';

            // Buscar logs com arquivos CSV gerados (individual por bloco via meta)
            $sql = "
                SELECT 
                    l.id as log_id,
                    l.id_rotina,
                    l.data_inicio,
                    l.status,
                    l.meta,
                    l.caminho_csv,
                    r.nome as nome_rotina
                FROM tb_logs_execucao l
                JOIN tb_rotinas r ON l.id_rotina = r.id
                WHERE l.id_rotina IN {$inSql}
                AND (l.caminho_csv IS NOT NULL OR l.meta IS NOT NULL)
                ORDER BY l.data_inicio DESC
            ";
            $stmt = $db->query($sql);
            $logs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $arquivos = [];
            $tamanhoTotal = 0;

            foreach ($logs as $log) {
                $detalhes = json_decode($log['meta'] ?? '[]', true);
                if (!is_array($detalhes)) $detalhes = [];

                foreach ($detalhes as $idx => $bloco) {
                    if (empty($bloco['arquivo_csv'])) continue;
                    $caminho = $bloco['arquivo_csv'];
                    $existe = file_exists($caminho);
                    $tamanho = $existe ? filesize($caminho) : 0;
                    $tamanhoTotal += $tamanho;

                    $arquivos[] = [
                        'log_id' => (int)$log['log_id'],
                        'bloco_index' => $idx,
                        'bloco_nome' => $bloco['bloco'] ?? "Bloco " . ($idx + 1),
                        'id_rotina' => (int)$log['id_rotina'],
                        'nome_rotina' => $log['nome_rotina'],
                        'nome_arquivo' => basename($caminho),
                        'caminho' => $caminho,
                        'tamanho' => $tamanho,
                        'existe' => $existe,
                        'data_execucao' => $log['data_inicio'],
                        'status_execucao' => $log['status'],
                        'registros' => $bloco['registros'] ?? null,
                    ];
                }

                // Também incluir caminho_csv da execução (arquivo principal)
                if (!empty($log['caminho_csv']) && empty($detalhes)) {
                    $caminho = $log['caminho_csv'];
                    $existe = file_exists($caminho);
                    $tamanho = $existe ? filesize($caminho) : 0;
                    $tamanhoTotal += $tamanho;

                    $arquivos[] = [
                        'log_id' => (int)$log['log_id'],
                        'bloco_index' => null,
                        'bloco_nome' => 'Principal',
                        'id_rotina' => (int)$log['id_rotina'],
                        'nome_rotina' => $log['nome_rotina'],
                        'nome_arquivo' => basename($caminho),
                        'caminho' => $caminho,
                        'tamanho' => $tamanho,
                        'existe' => $existe,
                        'data_execucao' => $log['data_inicio'],
                        'status_execucao' => $log['status'],
                        'registros' => null,
                    ];
                }
            }

            // Filtros opcionais (query params)
            $filtroRotina = isset($_GET['id_rotina']) ? (int)$_GET['id_rotina'] : null;
            $filtroBusca = trim($_GET['busca'] ?? '');

            if ($filtroRotina) {
                $arquivos = array_values(array_filter($arquivos, fn($a) => $a['id_rotina'] === $filtroRotina));
            }
            if ($filtroBusca) {
                $busca = mb_strtolower($filtroBusca);
                $arquivos = array_values(array_filter($arquivos, fn($a) =>
                    str_contains(mb_strtolower($a['nome_arquivo']), $busca) ||
                    str_contains(mb_strtolower($a['nome_rotina']), $busca)
                ));
            }

            echo json_encode([
                'sucesso' => true,
                'arquivos' => $arquivos,
                'total' => count($arquivos),
                'tamanho_total' => $tamanhoTotal
            ]);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Excluir um ou múltiplos arquivos
     */
    public function excluir(?array $inputParsed = null): void
    {
        header('Content-Type: application/json');
        try {
            $input = $inputParsed ?? json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $arquivos = $input['arquivos'] ?? [];

            if (empty($arquivos) || !is_array($arquivos)) {
                echo json_encode(['sucesso' => false, 'erro' => 'Nenhum arquivo informado']);
                return;
            }

            $db = Database::getConexao();
            $storagePath = realpath($this->storagePath);
            $excluidos = 0;
            $erros = [];

            foreach ($arquivos as $arq) {
                $logId = (int)($arq['log_id'] ?? 0);
                $blocoIndex = isset($arq['bloco_index']) ? (int)$arq['bloco_index'] : null;

                // Buscar o caminho do arquivo no banco
                $stmt = $db->prepare("SELECT meta, caminho_csv, id_rotina FROM tb_logs_execucao WHERE id = ?");
                $stmt->execute([$logId]);
                $log = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (!$log) {
                    $erros[] = "Log #{$logId} não encontrado";
                    continue;
                }

                // Verificar RBAC - rotina precisa ser visível
                if (!ServicoPermissao::podeVerRecurso('rotina', (int)$log['id_rotina'])) {
                    $erros[] = "Sem permissão para arquivo do log #{$logId}";
                    continue;
                }

                $caminho = null;
                $nomeArquivo = null;

                if ($blocoIndex !== null) {
                    $detalhes = json_decode($log['meta'] ?? '[]', true);
                    if (isset($detalhes[$blocoIndex]['arquivo_csv'])) {
                        $caminho = $detalhes[$blocoIndex]['arquivo_csv'];
                    }
                } else {
                    $caminho = $log['caminho_csv'];
                }

                if (!$caminho) {
                    $erros[] = "Caminho do arquivo não encontrado para log #{$logId}";
                    continue;
                }

                $realCaminho = realpath($caminho);
                if (!$realCaminho || !$storagePath || strpos($realCaminho, $storagePath) !== 0) {
                    $erros[] = "Arquivo fora do diretório permitido: " . basename($caminho);
                    continue;
                }

                $nomeArquivo = basename($realCaminho);

                if (file_exists($realCaminho)) {
                    if (unlink($realCaminho)) {
                        $excluidos++;

                        // Limpar referência no banco
                        if ($blocoIndex !== null) {
                            $detalhes = json_decode($log['meta'] ?? '[]', true);
                            unset($detalhes[$blocoIndex]['arquivo_csv']);
                            $stmt = $db->prepare("UPDATE tb_logs_execucao SET meta = ?::jsonb WHERE id = ?");
                            $stmt->execute([json_encode(array_values($detalhes)), $logId]);
                        } else {
                            $stmt = $db->prepare("UPDATE tb_logs_execucao SET caminho_csv = NULL WHERE id = ?");
                            $stmt->execute([$logId]);
                        }

                        // Auditoria
                        ServicoAuditoria::registrar(
                            'excluir',
                            'arquivo_gerado',
                            $logId,
                            $nomeArquivo,
                            ['caminho' => $caminho, 'log_id' => $logId, 'bloco_index' => $blocoIndex],
                            []
                        );
                    } else {
                        $erros[] = "Falha ao excluir: {$nomeArquivo}";
                    }
                } else {
                    // Arquivo não existe no disco, limpar referência
                    if ($blocoIndex !== null) {
                        $detalhes = json_decode($log['meta'] ?? '[]', true);
                        unset($detalhes[$blocoIndex]['arquivo_csv']);
                        $stmt = $db->prepare("UPDATE tb_logs_execucao SET meta = ?::jsonb WHERE id = ?");
                        $stmt->execute([json_encode(array_values($detalhes)), $logId]);
                    } else {
                        $stmt = $db->prepare("UPDATE tb_logs_execucao SET caminho_csv = NULL WHERE id = ?");
                        $stmt->execute([$logId]);
                    }
                    $excluidos++;
                }
            }

            echo json_encode([
                'sucesso' => true,
                'excluidos' => $excluidos,
                'erros' => $erros,
                'mensagem' => "{$excluidos} arquivo(s) excluído(s)" . (count($erros) ? ". " . count($erros) . " erro(s)." : ".")
            ]);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Listar políticas de retenção
     */
    public function listarPoliticas(): void
    {
        header('Content-Type: application/json');
        try {
            $this->garantirTabelaPoliticas();
            $db = Database::getConexao();

            // RBAC: filtrar rotinas visíveis
            $filtro = ServicoPermissao::filtroVisibilidadePosicional('rotina', 'r', 'id_usuario_criador');
            $sql = "
                SELECT p.*, r.nome as nome_rotina
                FROM tb_politica_retencao_arquivos p
                JOIN tb_rotinas r ON p.id_rotina = r.id
                WHERE ({$filtro['where']})
                ORDER BY r.nome
            ";
            // Substituir alias: a query usa 'r' como alias da rotina
            $stmt = $db->prepare($sql);
            $stmt->execute($filtro['params']);
            $politicas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode(['sucesso' => true, 'politicas' => $politicas]);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Salvar política de retenção
     */
    public function salvarPolitica(?array $inputParsed = null): void
    {
        header('Content-Type: application/json');
        try {
            $this->garantirTabelaPoliticas();
            $input = $inputParsed ?? json_decode(file_get_contents('php://input'), true) ?? $_POST;

            $idRotina = (int)($input['id_rotina'] ?? 0);
            $diasRetencao = (int)($input['dias_retencao'] ?? 30);
            $ativo = (bool)($input['ativo'] ?? true);

            if (!$idRotina || $diasRetencao < 1) {
                echo json_encode(['sucesso' => false, 'erro' => 'Rotina e dias de retenção são obrigatórios (mínimo 1 dia)']);
                return;
            }

            // Verificar permissão sobre a rotina
            if (!ServicoPermissao::podeVerRecurso('rotina', $idRotina)) {
                echo json_encode(['sucesso' => false, 'erro' => 'Sem permissão para configurar esta rotina']);
                return;
            }

            $db = Database::getConexao();
            $usuario = AuthMiddleware::obterUsuario();

            // Upsert
            $stmt = $db->prepare("
                INSERT INTO tb_politica_retencao_arquivos (id_rotina, dias_retencao, ativo, criado_por)
                VALUES (?, ?, ?, ?)
                ON CONFLICT (id_rotina) DO UPDATE SET
                    dias_retencao = EXCLUDED.dias_retencao,
                    ativo = EXCLUDED.ativo,
                    atualizado_em = now()
                RETURNING id
            ");
            $stmt->execute([$idRotina, $diasRetencao, $ativo ? 'true' : 'false', $usuario['id'] ?? 0]);
            $id = $stmt->fetchColumn();

            ServicoAuditoria::registrar(
                'criar',
                'politica_retencao',
                (int)$id,
                "Rotina #{$idRotina} - {$diasRetencao} dias",
                [],
                ['id_rotina' => $idRotina, 'dias_retencao' => $diasRetencao, 'ativo' => $ativo]
            );

            echo json_encode(['sucesso' => true, 'id' => $id, 'mensagem' => 'Política de retenção salva com sucesso']);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Excluir política de retenção
     */
    public function excluirPolitica(?array $inputParsed = null): void
    {
        header('Content-Type: application/json');
        try {
            $this->garantirTabelaPoliticas();
            $input = $inputParsed ?? json_decode(file_get_contents('php://input'), true) ?? $_POST;
            $id = (int)($input['id'] ?? 0);

            if (!$id) {
                echo json_encode(['sucesso' => false, 'erro' => 'ID da política não informado']);
                return;
            }

            $db = Database::getConexao();

            // Buscar política para auditoria
            $stmt = $db->prepare("SELECT p.*, r.nome as nome_rotina FROM tb_politica_retencao_arquivos p JOIN tb_rotinas r ON p.id_rotina = r.id WHERE p.id = ?");
            $stmt->execute([$id]);
            $politica = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$politica) {
                echo json_encode(['sucesso' => false, 'erro' => 'Política não encontrada']);
                return;
            }

            // Verificar permissão sobre a rotina
            if (!ServicoPermissao::podeVerRecurso('rotina', (int)$politica['id_rotina'])) {
                echo json_encode(['sucesso' => false, 'erro' => 'Sem permissão']);
                return;
            }

            $stmt = $db->prepare("DELETE FROM tb_politica_retencao_arquivos WHERE id = ?");
            $stmt->execute([$id]);

            ServicoAuditoria::registrar(
                'excluir',
                'politica_retencao',
                $id,
                $politica['nome_rotina'] ?? '',
                ['id_rotina' => $politica['id_rotina'], 'dias_retencao' => $politica['dias_retencao']],
                []
            );

            echo json_encode(['sucesso' => true, 'mensagem' => 'Política excluída com sucesso']);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Executar limpeza automática baseada em políticas de retenção
     * Chamado pelo scheduler ou manualmente
     */
    public function executarLimpeza(): array
    {
        $this->garantirTabelaPoliticas();
        $db = Database::getConexao();
        $storagePath = realpath($this->storagePath);

        // Buscar políticas ativas
        $stmt = $db->query("
            SELECT p.id_rotina, p.dias_retencao, r.nome as nome_rotina
            FROM tb_politica_retencao_arquivos p
            JOIN tb_rotinas r ON p.id_rotina = r.id
            WHERE p.ativo = true
        ");
        $politicas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $totalExcluidos = 0;
        $detalhes = [];

        foreach ($politicas as $pol) {
            $idRotina = (int)$pol['id_rotina'];
            $diasRetencao = (int)$pol['dias_retencao'];
            $dataLimite = date('Y-m-d H:i:s', strtotime("-{$diasRetencao} days"));

            // Buscar logs antigos com arquivos
            $stmt = $db->prepare("
                SELECT id, meta, caminho_csv
                FROM tb_logs_execucao
                WHERE id_rotina = ?
                AND data_inicio < ?
                AND (caminho_csv IS NOT NULL OR meta IS NOT NULL)
            ");
            $stmt->execute([$idRotina, $dataLimite]);
            $logs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $excluidos = 0;
            foreach ($logs as $log) {
                // Excluir arquivos dos blocos
                $detalhesJson = json_decode($log['meta'] ?? '[]', true);
                $modificado = false;
                if (is_array($detalhesJson)) {
                    foreach ($detalhesJson as &$bloco) {
                        if (!empty($bloco['arquivo_csv'])) {
                            $real = realpath($bloco['arquivo_csv']);
                            if ($real && $storagePath && strpos($real, $storagePath) === 0 && file_exists($real)) {
                                unlink($real);
                                $excluidos++;
                            }
                            unset($bloco['arquivo_csv']);
                            $modificado = true;
                        }
                    }
                    unset($bloco);
                }

                // Excluir arquivo principal
                if (!empty($log['caminho_csv'])) {
                    $real = realpath($log['caminho_csv']);
                    if ($real && $storagePath && strpos($real, $storagePath) === 0 && file_exists($real)) {
                        unlink($real);
                        $excluidos++;
                    }
                }

                // Atualizar referências no banco
                $stmtUpd = $db->prepare("UPDATE tb_logs_execucao SET caminho_csv = NULL, meta = ?::jsonb WHERE id = ?");
                $stmtUpd->execute([
                    $modificado ? json_encode($detalhesJson) : $log['meta'],
                    $log['id']
                ]);
            }

            if ($excluidos > 0) {
                $totalExcluidos += $excluidos;
                $detalhes[] = "{$pol['nome_rotina']}: {$excluidos} arquivo(s)";

                ServicoAuditoria::registrar(
                    'excluir',
                    'limpeza_automatica',
                    $idRotina,
                    $pol['nome_rotina'],
                    ['dias_retencao' => $diasRetencao, 'data_limite' => $dataLimite],
                    ['arquivos_excluidos' => $excluidos]
                );
            }
        }

        return [
            'sucesso' => true,
            'total_excluidos' => $totalExcluidos,
            'detalhes' => $detalhes,
            'mensagem' => "Limpeza concluída: {$totalExcluidos} arquivo(s) excluído(s)"
        ];
    }

    /**
     * Listar rotinas disponíveis para o dropdown de políticas
     */
    public function listarRotinas(): void
    {
        header('Content-Type: application/json');
        try {
            $db = Database::getConexao();
            $filtro = ServicoPermissao::filtroVisibilidadePosicional('rotina', 'r', 'id_usuario_criador');
            $stmt = $db->prepare("SELECT r.id, r.nome FROM tb_rotinas r WHERE ({$filtro['where']}) ORDER BY r.nome");
            $stmt->execute($filtro['params']);
            echo json_encode(['sucesso' => true, 'rotinas' => $stmt->fetchAll(\PDO::FETCH_ASSOC)]);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }
}
