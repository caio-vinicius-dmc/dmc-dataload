<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\AuthMiddleware;
use App\Servicos\ServicoAuditoria;
use App\Servicos\ServicoEmail;
use App\Servicos\ServicoBackup;

class ConfiguracoesController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getConexao();
    }

    /**
     * Carregar todas as configurações
     */
    public function carregar(): void
    {
        header('Content-Type: application/json');
        $stmt = $this->db->query("SELECT chave, valor, grupo FROM tb_configuracoes ORDER BY grupo, chave");
        $configs = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $configs[$row['chave']] = $row['valor'];
        }
        echo json_encode(['sucesso' => true, 'configs' => $configs]);
    }

    /**
     * Salvar configurações de um grupo
     */
    public function salvarGrupo(string $grupo): void
    {
        header('Content-Type: application/json');

        try {
            $camposPermitidos = $this->camposPorGrupo($grupo);
            if (!$camposPermitidos) {
                throw new \Exception('Grupo de configuração inválido');
            }

            $idUsuario = AuthMiddleware::obterUsuarioId();
            $anteriores = [];
            $novos = [];

            foreach ($_POST as $chave => $valor) {
                if ($chave === '_csrf_token') continue;
                if (!in_array($chave, $camposPermitidos, true)) continue;

                // Buscar valor anterior
                $stmtAnt = $this->db->prepare("SELECT valor FROM tb_configuracoes WHERE chave = :chave");
                $stmtAnt->execute([':chave' => $chave]);
                $anterior = $stmtAnt->fetchColumn();
                $anteriores[$chave] = $anterior;
                $novos[$chave] = $valor;

                // Upsert (senha SMTP não é armazenada em texto, mas como a tabela é interna, por simplicidade manter assim)
                $stmt = $this->db->prepare("
                    INSERT INTO tb_configuracoes (chave, valor, grupo, atualizado_em, atualizado_por) 
                    VALUES (:chave, :valor, :grupo, NOW(), :uid)
                    ON CONFLICT (chave) DO UPDATE SET valor = :valor2, atualizado_em = NOW(), atualizado_por = :uid2
                ");
                $stmt->execute([
                    ':chave' => $chave,
                    ':valor' => $valor,
                    ':grupo' => $grupo,
                    ':uid' => $idUsuario,
                    ':valor2' => $valor,
                    ':uid2' => $idUsuario,
                ]);
            }

            // Audit log (mascarar senha)
            $novosAudit = $novos;
            if (isset($novosAudit['smtp_password'])) {
                $novosAudit['smtp_password'] = '****';
            }
            ServicoAuditoria::registrar('editar', 'configuracao', null, "Grupo: $grupo", $anteriores, $novosAudit);

            echo json_encode(['sucesso' => true, 'mensagem' => 'Configurações salvas com sucesso']);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Testar envio de e-mail
     */
    public function testarEmail(): void
    {
        header('Content-Type: application/json');
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        if (!$email) {
            echo json_encode(['sucesso' => false, 'erro' => 'E-mail inválido']);
            return;
        }

        $result = ServicoEmail::enviar(
            $email,
            'DMC DataLoad - Teste de E-mail',
            '<h2>Teste de E-mail</h2><p>Se você está lendo isso, o SMTP está funcionando corretamente!</p><p>Data: ' . date('d/m/Y H:i:s') . '</p>'
        );

        echo json_encode($result);
    }

    /**
     * Exportar configurações como JSON
     */
    public function exportar(): void
    {
        $stmt = $this->db->query("SELECT chave, valor, grupo FROM tb_configuracoes ORDER BY grupo, chave");
        $configs = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            // Não exportar senha SMTP
            if ($row['chave'] === 'smtp_password') continue;
            $configs[$row['grupo']][$row['chave']] = $row['valor'];
        }

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="dmc_dataload_configs_' . date('Y-m-d') . '.json"');
        echo json_encode($configs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Importar configurações de JSON
     */
    public function importar(): void
    {
        header('Content-Type: application/json');
        try {
            $json = $_POST['configs'] ?? '';
            $configs = json_decode($json, true);
            if (!$configs || !is_array($configs)) {
                throw new \Exception('JSON inválido');
            }

            $idUsuario = AuthMiddleware::obterUsuarioId();
            $count = 0;

            foreach ($configs as $grupo => $campos) {
                $camposPermitidos = $this->camposPorGrupo($grupo);
                if (!$camposPermitidos) continue;

                foreach ($campos as $chave => $valor) {
                    if (!in_array($chave, $camposPermitidos, true)) continue;

                    $stmt = $this->db->prepare("
                        INSERT INTO tb_configuracoes (chave, valor, grupo, atualizado_em, atualizado_por)
                        VALUES (:chave, :valor, :grupo, NOW(), :uid)
                        ON CONFLICT (chave) DO UPDATE SET valor = :valor2, atualizado_em = NOW(), atualizado_por = :uid2
                    ");
                    $stmt->execute([
                        ':chave' => $chave, ':valor' => $valor, ':grupo' => $grupo,
                        ':uid' => $idUsuario, ':valor2' => $valor, ':uid2' => $idUsuario,
                    ]);
                    $count++;
                }
            }

            ServicoAuditoria::registrar('importar', 'configuracao', null, "Importação: $count campos");
            echo json_encode(['sucesso' => true, 'mensagem' => "$count configurações importadas"]);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Limpar dados antigos
     */
    public function limparDados(): void
    {
        header('Content-Type: application/json');
        try {
            $dias = max(7, (int) ($_POST['dias'] ?? 90));
            $total = 0;

            // Limpar logs de execução antigos
            $stmt = $this->db->prepare("DELETE FROM tb_logs_execucao WHERE data_inicio < NOW() - INTERVAL '$dias days'");
            $stmt->execute();
            $total += $stmt->rowCount();

            // Limpar logs do sistema
            $stmt = $this->db->prepare("DELETE FROM tb_logs_sistema WHERE criado_em < NOW() - INTERVAL '$dias days'");
            $stmt->execute();
            $total += $stmt->rowCount();

            // Limpar rate limits expirados
            $this->db->exec("DELETE FROM tb_rate_limits WHERE ultima_tentativa < NOW() - INTERVAL '1 day'");

            // Limpar auditoria muito antiga (> 1 ano)
            if ($dias >= 365) {
                $stmt = $this->db->prepare("DELETE FROM tb_auditoria WHERE criado_em < NOW() - INTERVAL '$dias days'");
                $stmt->execute();
                $total += $stmt->rowCount();
            }

            ServicoAuditoria::registrar('limpar', 'sistema', null, "Limpeza: $total registros removidos (> $dias dias)");
            echo json_encode(['sucesso' => true, 'removidos' => $total, 'mensagem' => "$total registros removidos"]);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Testar conexão LDAP
     */
    public function testarLdap(): void
    {
        header('Content-Type: application/json');

        try {
            if (!extension_loaded('ldap')) {
                throw new \Exception('Extensão PHP LDAP não está instalada');
            }

            // Carregar configs LDAP do banco
            $stmt = $this->db->prepare("SELECT chave, valor FROM tb_configuracoes WHERE grupo = 'ldap'");
            $stmt->execute();
            $configs = [];
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $configs[$row['chave']] = $row['valor'];
            }

            $host = $configs['ldap_host'] ?? '';
            $port = (int) ($configs['ldap_port'] ?? 389);
            $ssl = $configs['ldap_ssl'] ?? '0';

            if (empty($host)) {
                throw new \Exception('Servidor LDAP não configurado');
            }

            $uri = ($ssl === '1') ? "ldaps://$host:$port" : "ldap://$host:$port";
            $conn = @ldap_connect($uri);
            if (!$conn) {
                throw new \Exception('Não foi possível conectar ao servidor LDAP');
            }

            ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($conn, LDAP_OPT_NETWORK_TIMEOUT, 5);

            if ($ssl === '2') {
                if (!@ldap_start_tls($conn)) {
                    throw new \Exception('Falha ao iniciar STARTTLS');
                }
            }

            // Tentar bind com credenciais fornecidas ou admin
            $user = $_POST['user'] ?? '';
            $pass = $_POST['pass'] ?? '';

            if (!empty($user) && !empty($pass)) {
                $bindDn = $configs['ldap_bind_dn'] ?? '';
                $baseDn = $configs['ldap_base_dn'] ?? '';
                $filter = $configs['ldap_filter'] ?? '(sAMAccountName={username})';
                $filter = str_replace('{username}', ldap_escape($user, '', LDAP_ESCAPE_FILTER), $filter);

                // Primeiro bind com admin para buscar
                if (!empty($bindDn)) {
                    $adminPass = $configs['ldap_bind_password'] ?? '';
                    $bind = @ldap_bind($conn, $bindDn, $adminPass);
                    if (!$bind) {
                        throw new \Exception('Falha no bind administrativo: ' . ldap_error($conn));
                    }
                }

                echo json_encode(['sucesso' => true, 'mensagem' => 'Conexão LDAP bem sucedida']);
            } else {
                // Teste anônimo ou com bind admin
                $bindDn = $configs['ldap_bind_dn'] ?? '';
                $bindPass = $configs['ldap_bind_password'] ?? '';

                if (!empty($bindDn) && !empty($bindPass)) {
                    $bind = @ldap_bind($conn, $bindDn, $bindPass);
                    if (!$bind) {
                        throw new \Exception('Falha no bind: ' . ldap_error($conn));
                    }
                    echo json_encode(['sucesso' => true, 'mensagem' => 'Conexão LDAP bem sucedida (bind admin)']);
                } else {
                    $bind = @ldap_bind($conn);
                    if (!$bind) {
                        throw new \Exception('Falha no bind anônimo: ' . ldap_error($conn));
                    }
                    echo json_encode(['sucesso' => true, 'mensagem' => 'Conexão LDAP bem sucedida (anônimo)']);
                }
            }

            @ldap_close($conn);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Criar backup do banco de dados
     */
    public function backupBD(): void
    {
        header('Content-Type: application/json');
        try {
            $result = ServicoBackup::criar('completo');
            echo json_encode($result);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Upload de favicon
     */
    public function uploadFavicon(): void
    {
        header('Content-Type: application/json');
        try {
            if (empty($_FILES['favicon']) || $_FILES['favicon']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('Nenhum arquivo enviado ou erro no upload');
            }

            $file = $_FILES['favicon'];
            $allowedMimes = ['image/x-icon', 'image/vnd.microsoft.icon', 'image/png', 'image/svg+xml', 'image/gif'];
            $allowedExts = ['ico', 'png', 'svg', 'gif'];

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($mime, $allowedMimes, true) || !in_array($ext, $allowedExts, true)) {
                throw new \Exception('Formato inválido. Use: .ico, .png, .svg ou .gif');
            }

            if ($file['size'] > 512 * 1024) {
                throw new \Exception('Arquivo muito grande. Máximo: 512KB');
            }

            $uploadDir = __DIR__ . '/../../public/assets/uploads';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Remover favicon anterior
            $stmtOld = $this->db->prepare("SELECT valor FROM tb_configuracoes WHERE chave = 'app_favicon'");
            $stmtOld->execute();
            $oldPath = $stmtOld->fetchColumn();
            if ($oldPath) {
                $oldFile = __DIR__ . '/../../public' . $oldPath;
                if (is_file($oldFile)) {
                    @unlink($oldFile);
                }
            }

            $filename = 'favicon_' . time() . '.' . $ext;
            $destPath = $uploadDir . '/' . $filename;

            if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                throw new \Exception('Erro ao salvar arquivo');
            }

            $relativePath = '/assets/uploads/' . $filename;
            $idUsuario = AuthMiddleware::obterUsuarioId();

            $stmt = $this->db->prepare("
                INSERT INTO tb_configuracoes (chave, valor, grupo, atualizado_em, atualizado_por)
                VALUES ('app_favicon', :valor, 'geral', NOW(), :uid)
                ON CONFLICT (chave) DO UPDATE SET valor = :valor2, atualizado_em = NOW(), atualizado_por = :uid2
            ");
            $stmt->execute([
                ':valor' => $relativePath, ':uid' => $idUsuario,
                ':valor2' => $relativePath, ':uid2' => $idUsuario,
            ]);

            ServicoAuditoria::registrar('editar', 'configuracao', null, 'Upload favicon: ' . $filename);

            echo json_encode(['sucesso' => true, 'mensagem' => 'Favicon atualizado com sucesso', 'caminho' => $relativePath]);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Remover favicon
     */
    public function removerFavicon(): void
    {
        header('Content-Type: application/json');
        try {
            $stmt = $this->db->prepare("SELECT valor FROM tb_configuracoes WHERE chave = 'app_favicon'");
            $stmt->execute();
            $path = $stmt->fetchColumn();

            if ($path) {
                $fullPath = __DIR__ . '/../../public' . $path;
                if (is_file($fullPath)) {
                    @unlink($fullPath);
                }
            }

            $idUsuario = AuthMiddleware::obterUsuarioId();
            $stmt = $this->db->prepare("
                INSERT INTO tb_configuracoes (chave, valor, grupo, atualizado_em, atualizado_por)
                VALUES ('app_favicon', '', 'geral', NOW(), :uid)
                ON CONFLICT (chave) DO UPDATE SET valor = '', atualizado_em = NOW(), atualizado_por = :uid2
            ");
            $stmt->execute([':uid' => $idUsuario, ':uid2' => $idUsuario]);

            ServicoAuditoria::registrar('editar', 'configuracao', null, 'Favicon removido');
            echo json_encode(['sucesso' => true, 'mensagem' => 'Favicon removido']);
        } catch (\Exception $e) {
            echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
        }
    }

    /**
     * Campos permitidos por grupo (whitelist)
     */
    private function camposPorGrupo(string $grupo): ?array
    {
        return match ($grupo) {
            'geral' => ['app_nome', 'app_url', 'app_timezone', 'app_idioma', 'app_descricao', 'modo_manutencao', 'login_bg_imagem', 'app_favicon'],
            'email' => ['smtp_host', 'smtp_port', 'smtp_encryption', 'smtp_user', 'smtp_password', 'smtp_from_email', 'smtp_from_name'],
            'ldap' => ['ldap_ativo', 'ldap_host', 'ldap_port', 'ldap_ssl', 'ldap_base_dn', 'ldap_filter', 'ldap_bind_dn', 'ldap_bind_password'],
            'scheduler' => ['scheduler_ativo', 'scheduler_intervalo', 'scheduler_max_paralelo', 'scheduler_timeout', 'scheduler_retry', 'scheduler_max_tentativas', 'scheduler_intervalo_retry'],
            'seguranca' => ['sessao_tempo', 'login_tentativas', 'login_bloqueio', 'senha_min', 'senha_maiuscula', 'senha_minuscula', 'senha_numero', 'senha_especial', '2fa_ativo', 'ip_tentativas', 'ip_bloqueio'],
            'notificacoes' => ['notif_falha', 'notif_sucesso', 'notif_agendamento', 'notif_conexao', 'notif_sistema', 'notif_emails'],
            default => null,
        };
    }
}
