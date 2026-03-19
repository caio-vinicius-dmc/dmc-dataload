<?php
namespace App\Servicos;

use App\Core\Database;
use App\Core\AuthMiddleware;

class ServicoBackup
{
    private static string $backupDir = __DIR__ . '/../../backups';

    /**
     * Listar backups disponíveis
     */
    public static function listar(): array
    {
        $db = Database::getConexao();
        $stmt = $db->query("SELECT * FROM tb_backups ORDER BY criado_em DESC LIMIT 50");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Criar um novo backup
     */
    public static function criar(string $tipo = 'completo'): array
    {
        $db = Database::getConexao();
        self::garantirDiretorio();

        $userId = null;
        $userName = null;
        try {
            $userId = AuthMiddleware::obterUsuarioId();
            $usuario = AuthMiddleware::obterUsuario();
            $userName = $usuario['nome_usuario'] ?? null;
        } catch (\Throwable $e) {}

        $nome = 'backup_' . $tipo . '_' . date('Y-m-d_His');
        $arquivo = self::$backupDir . '/' . $nome . '.json';

        // Registrar na tabela
        $stmt = $db->prepare(
            "INSERT INTO tb_backups (nome, tipo, status, id_usuario, nome_usuario) 
             VALUES (:nome, :tipo, 'gerando', :id_usuario, :nome_usuario) RETURNING id"
        );
        $stmt->execute([
            ':nome' => $nome, ':tipo' => $tipo,
            ':id_usuario' => $userId, ':nome_usuario' => $userName
        ]);
        $backupId = $stmt->fetchColumn();

        try {
            $dados = self::exportarDados($tipo);
            $json = json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            file_put_contents($arquivo, $json);

            $tamanho = filesize($arquivo);
            $checksum = hash_file('sha256', $arquivo);

            $db->prepare(
                "UPDATE tb_backups SET status = 'concluido', caminho_arquivo = :caminho, 
                 tamanho_bytes = :tamanho, checksum = :checksum, concluido_em = CURRENT_TIMESTAMP
                 WHERE id = :id"
            )->execute([
                ':caminho' => $arquivo, ':tamanho' => $tamanho,
                ':checksum' => $checksum, ':id' => $backupId
            ]);

            return [
                'sucesso' => true,
                'id' => $backupId,
                'nome' => $nome,
                'tamanho' => $tamanho,
                'mensagem' => 'Backup criado com sucesso'
            ];
        } catch (\Exception $e) {
            $db->prepare(
                "UPDATE tb_backups SET status = 'falha', erro = :erro WHERE id = :id"
            )->execute([':erro' => $e->getMessage(), ':id' => $backupId]);

            return ['sucesso' => false, 'erro' => $e->getMessage()];
        }
    }

    /**
     * Download de um backup
     */
    public static function download(int $id): void
    {
        $db = Database::getConexao();
        $stmt = $db->prepare("SELECT * FROM tb_backups WHERE id = :id AND status = 'concluido'");
        $stmt->execute([':id' => $id]);
        $backup = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$backup || !file_exists($backup['caminho_arquivo'])) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['sucesso' => false, 'erro' => 'Backup não encontrado']);
            return;
        }

        $filePath = realpath($backup['caminho_arquivo']);
        $allowedDir = realpath(self::$backupDir);
        if ($filePath === false || strpos($filePath, $allowedDir) !== 0) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['sucesso' => false, 'erro' => 'Acesso negado']);
            return;
        }

        // Limpar qualquer output anterior (ex: HTML do root index.php)
        while (ob_get_level()) ob_end_clean();

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . basename($backup['caminho_arquivo']) . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
    }

    /**
     * Restaurar a partir de um arquivo de backup
     */
    public static function restaurar(array $file): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['sucesso' => false, 'erro' => 'Erro no upload do arquivo'];
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if ($ext !== 'json') {
            return ['sucesso' => false, 'erro' => 'Apenas arquivos .json são aceitos'];
        }

        $conteudo = file_get_contents($file['tmp_name']);
        $dados = json_decode($conteudo, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['sucesso' => false, 'erro' => 'Arquivo JSON inválido'];
        }
        if (empty($dados['_meta']['versao']) || empty($dados['_meta']['tipo'])) {
            return ['sucesso' => false, 'erro' => 'Arquivo não parece ser um backup válido do DMC DataLoad'];
        }

        $db = Database::getConexao();

        try {
            $db->beginTransaction();
            $restaurados = self::importarDados($dados);
            $db->commit();

            return [
                'sucesso' => true,
                'mensagem' => 'Backup restaurado com sucesso',
                'detalhes' => $restaurados
            ];
        } catch (\Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            return ['sucesso' => false, 'erro' => $e->getMessage()];
        }
    }

    /**
     * Deletar um backup
     */
    public static function deletar(int $id): array
    {
        $db = Database::getConexao();
        $stmt = $db->prepare("SELECT caminho_arquivo FROM tb_backups WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $caminho = $stmt->fetchColumn();

        if ($caminho && file_exists($caminho)) {
            $filePath = realpath($caminho);
            $allowedDir = realpath(self::$backupDir);
            if ($filePath !== false && strpos($filePath, $allowedDir) === 0) {
                unlink($filePath);
            }
        }

        $db->prepare("DELETE FROM tb_backups WHERE id = :id")->execute([':id' => $id]);
        return ['sucesso' => true, 'mensagem' => 'Backup removido'];
    }

    /**
     * Exportar dados do banco para array
     */
    private static function exportarDados(string $tipo): array
    {
        $db = Database::getConexao();
        $dados = [
            '_meta' => [
                'versao' => '1.0',
                'tipo' => $tipo,
                'data' => date('Y-m-d H:i:s'),
                'app' => 'DMC DataLoad'
            ]
        ];

        $tabelas = match ($tipo) {
            'rotinas' => ['tb_rotinas', 'tb_blocos_rotina', 'tb_perfis_conexao'],
            'configuracoes' => ['tb_configuracoes', 'tb_webhooks', 'tb_canais_notificacao'],
            default => [
                'tb_perfis_conexao', 'tb_rotinas', 'tb_blocos_rotina',
                'tb_pipelines', 'tb_pipeline_nodes', 'tb_pipeline_edges',
                'tb_workflows', 'tb_workflow_etapas',
                'tb_configuracoes', 'tb_webhooks', 'tb_canais_notificacao',
                'tb_empresas', 'tb_projetos'
            ]
        };

        foreach ($tabelas as $tabela) {
            try {
                $stmt = $db->query("SELECT * FROM {$tabela}");
                $dados[$tabela] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\Exception $e) {
                $dados[$tabela] = [];
            }
        }

        return $dados;
    }

    /**
     * Importar dados de um array para o banco
     */
    private static function importarDados(array $dados): array
    {
        $db = Database::getConexao();
        $resultado = [];

        // Ordem de importação (respeitar FK)
        $ordemImport = [
            'tb_empresas', 'tb_projetos', 'tb_perfis_conexao',
            'tb_rotinas', 'tb_blocos_rotina',
            'tb_pipelines', 'tb_pipeline_nodes', 'tb_pipeline_edges',
            'tb_workflows', 'tb_workflow_etapas',
            'tb_configuracoes', 'tb_webhooks', 'tb_canais_notificacao'
        ];

        foreach ($ordemImport as $tabela) {
            if (empty($dados[$tabela])) continue;

            $rows = $dados[$tabela];
            $importados = 0;

            foreach ($rows as $row) {
                $colunas = array_keys($row);
                $placeholders = array_map(fn($c) => ":$c", $colunas);
                $colsStr = implode(', ', $colunas);
                $phStr = implode(', ', $placeholders);

                // Usar ON CONFLICT para evitar duplicatas
                $sql = "INSERT INTO {$tabela} ({$colsStr}) VALUES ({$phStr}) ON CONFLICT DO NOTHING";
                try {
                    $stmt = $db->prepare($sql);
                    $params = [];
                    foreach ($row as $k => $v) {
                        $params[":{$k}"] = $v;
                    }
                    $stmt->execute($params);
                    if ($stmt->rowCount() > 0) $importados++;
                } catch (\Exception $e) {
                    // Ignorar erros individuais (constraint violations, etc.)
                }
            }

            $resultado[$tabela] = ['total' => count($rows), 'importados' => $importados];
        }

        return $resultado;
    }

    /**
     * Garantir que o diretório de backups existe
     */
    private static function garantirDiretorio(): void
    {
        if (!is_dir(self::$backupDir)) {
            mkdir(self::$backupDir, 0750, true);
        }
    }
}
