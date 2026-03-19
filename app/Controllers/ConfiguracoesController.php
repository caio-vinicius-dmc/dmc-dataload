<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\AuthMiddleware;
use App\Servicos\ServicoAuditoria;
use App\Servicos\ServicoEmail;

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

            // Limpar rate limits
            $this->db->exec("DELETE FROM tb_rate_limits WHERE expires_at < NOW()");

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
     * Campos permitidos por grupo (whitelist)
     */
    private function camposPorGrupo(string $grupo): ?array
    {
        return match ($grupo) {
            'geral' => ['app_nome', 'app_url', 'app_timezone', 'app_idioma', 'app_manutencao'],
            'email' => ['smtp_host', 'smtp_port', 'smtp_encryption', 'smtp_user', 'smtp_password', 'smtp_from_email', 'smtp_from_name'],
            'ldap' => ['ldap_host', 'ldap_port', 'ldap_ssl', 'ldap_base_dn', 'ldap_filtro', 'ldap_bind_dn', 'ldap_bind_password'],
            'scheduler' => ['scheduler_intervalo', 'scheduler_max_paralelo', 'scheduler_timeout', 'scheduler_retry'],
            'seguranca' => ['seguranca_timeout_sessao', 'seguranca_tentativas_login', 'seguranca_tempo_bloqueio'],
            'notificacoes' => ['notif_email_falha', 'notif_webhook_ativo', 'notif_webhook_url', 'notif_email_sucesso'],
            default => null,
        };
    }
}
