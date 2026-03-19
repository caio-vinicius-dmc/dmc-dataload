<?php
/**
 * DMC DataLoad - Configurações do Sistema
 * Nova UI Moderna
 */
$pageTitle = 'Configurações';
$currentPage = 'configuracoes';

ob_start();
?>

<!-- Page Header Modern -->
<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-gear"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Configurações</h1>
        <p class="page-subtitle-modern">Configure os parâmetros do sistema DMC DataLoad</p>
    </div>
</div>

<div class="row g-4">
    <!-- Menu Lateral de Configurações -->
    <div class="col-md-3">
        <div class="card-modern">
            <div class="card-modern-body p-0">
                <div class="list-group list-group-flush">
                    <a href="#secao-geral" class="list-group-item list-group-item-action active" data-section="geral">
                        <i class="bi bi-gear me-2"></i>Geral
                    </a>
                    <a href="#secao-email" class="list-group-item list-group-item-action" data-section="email">
                        <i class="bi bi-envelope me-2"></i>E-mail / SMTP
                    </a>
                    <a href="#secao-ldap" class="list-group-item list-group-item-action" data-section="ldap">
                        <i class="bi bi-diagram-3 me-2"></i>LDAP / AD
                    </a>
                    <a href="#secao-scheduler" class="list-group-item list-group-item-action" data-section="scheduler">
                        <i class="bi bi-clock me-2"></i>Scheduler
                    </a>
                    <a href="#secao-seguranca" class="list-group-item list-group-item-action" data-section="seguranca">
                        <i class="bi bi-shield-lock me-2"></i>Segurança
                    </a>
                    <a href="#secao-notificacoes" class="list-group-item list-group-item-action" data-section="notificacoes">
                        <i class="bi bi-bell me-2"></i>Notificações
                    </a>
                    <a href="#secao-backup" class="list-group-item list-group-item-action" data-section="backup">
                        <i class="bi bi-hdd me-2"></i>Backup
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Conteúdo das Seções -->
    <div class="col-md-9">
        <!-- Seção Geral -->
        <div class="config-section" id="secao-geral">
            <div class="card-modern mb-4">
                <div class="card-modern-header">
                    <i class="bi bi-gear me-2"></i>Configurações Gerais
                </div>
                <div class="card-modern-body">
                    <form id="formGeral">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-modern">Nome da Aplicação</label>
                                <input type="text" class="form-control-modern" name="app_nome" value="DMC DataLoad">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-modern">URL Base</label>
                                <input type="text" class="form-control-modern" name="app_url" value="<?= $_ENV['BASE_URL'] ?? '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-modern">Timezone</label>
                                <select class="form-select-modern" name="app_timezone">
                                    <option value="America/Sao_Paulo" selected>America/Sao_Paulo</option>
                                    <option value="UTC">UTC</option>
                                    <option value="America/New_York">America/New_York</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-modern">Idioma</label>
                                <select class="form-select-modern" name="app_idioma">
                                    <option value="pt_BR" selected>Português (Brasil)</option>
                                    <option value="en_US">English (US)</option>
                                    <option value="es_ES">Español</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label-modern">Descrição</label>
                                <textarea class="form-control-modern" name="app_descricao" rows="2">Sistema de ETL para carga e transformação de dados</textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="modo_manutencao" id="modoManutencao">
                                    <label class="form-check-label" for="modoManutencao">Modo Manutenção</label>
                                    <small class="text-muted d-block">Somente administradores poderão acessar o sistema</small>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn-modern-primary">
                            <i class="bi bi-check-lg me-2"></i>Salvar Configurações
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Seção Email -->
        <div class="config-section d-none" id="secao-email">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-envelope me-2"></i>Configurações de E-mail (SMTP)
                </div>
                <div class="card-body">
                    <form id="formEmail">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Servidor SMTP</label>
                                <input type="text" class="form-control" name="smtp_host" placeholder="smtp.exemplo.com">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Porta</label>
                                <input type="number" class="form-control" name="smtp_port" value="587">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Criptografia</label>
                                <select class="form-select" name="smtp_encryption">
                                    <option value="">Nenhuma</option>
                                    <option value="tls" selected>TLS</option>
                                    <option value="ssl">SSL</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Usuário SMTP</label>
                                <input type="text" class="form-control" name="smtp_user">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Senha SMTP</label>
                                <input type="password" class="form-control" name="smtp_password">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">E-mail Remetente</label>
                                <input type="email" class="form-control" name="smtp_from_email" placeholder="noreply@exemplo.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nome Remetente</label>
                                <input type="text" class="form-control" name="smtp_from_name" value="DMC DataLoad">
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Salvar
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="testarEmail()">
                                <i class="bi bi-send me-2"></i>Testar Envio
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Seção LDAP -->
        <div class="config-section d-none" id="secao-ldap">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-diagram-3 me-2"></i>Configurações LDAP / Active Directory
                </div>
                <div class="card-body">
                    <form id="formLdap">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="ldap_ativo" id="ldapAtivo">
                                    <label class="form-check-label" for="ldapAtivo">Habilitar autenticação LDAP</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Servidor LDAP</label>
                                <input type="text" class="form-control" name="ldap_host" placeholder="ldap.exemplo.com">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Porta</label>
                                <input type="number" class="form-control" name="ldap_port" value="389">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">SSL/TLS</label>
                                <select class="form-select" name="ldap_ssl">
                                    <option value="0">Desabilitado</option>
                                    <option value="1">SSL (636)</option>
                                    <option value="2">STARTTLS</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Base DN</label>
                                <input type="text" class="form-control" name="ldap_base_dn" placeholder="dc=exemplo,dc=com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Filtro de Usuário</label>
                                <input type="text" class="form-control" name="ldap_filter" value="(sAMAccountName={username})">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bind DN (Admin)</label>
                                <input type="text" class="form-control" name="ldap_bind_dn" placeholder="cn=admin,dc=exemplo,dc=com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bind Password</label>
                                <input type="password" class="form-control" name="ldap_bind_password">
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Salvar
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="testarLdap()">
                                <i class="bi bi-plug me-2"></i>Testar Conexão
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Seção Scheduler -->
        <div class="config-section d-none" id="secao-scheduler">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-clock me-2"></i>Configurações do Scheduler
                </div>
                <div class="card-body">
                    <form id="formScheduler">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="scheduler_ativo" id="schedulerAtivo" checked>
                                    <label class="form-check-label" for="schedulerAtivo">Scheduler habilitado</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Intervalo de Verificação (seg)</label>
                                <input type="number" class="form-control" name="scheduler_intervalo" value="60" min="10">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Máximo de Execuções Paralelas</label>
                                <input type="number" class="form-control" name="scheduler_max_paralelo" value="5" min="1">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Timeout de Execução (min)</label>
                                <input type="number" class="form-control" name="scheduler_timeout" value="60" min="1">
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="scheduler_retry" id="schedulerRetry" checked>
                                    <label class="form-check-label" for="schedulerRetry">Tentar novamente em caso de falha</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Máximo de Tentativas</label>
                                <input type="number" class="form-control" name="scheduler_max_tentativas" value="3" min="1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Intervalo entre Tentativas (seg)</label>
                                <input type="number" class="form-control" name="scheduler_intervalo_retry" value="300" min="60">
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>Salvar
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Seção Segurança -->
        <div class="config-section d-none" id="secao-seguranca">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-shield-lock me-2"></i>Configurações de Segurança
                </div>
                <div class="card-body">
                    <form id="formSeguranca">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Tempo de Sessão (min)</label>
                                <input type="number" class="form-control" name="sessao_tempo" value="120" min="5">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tentativas de Login</label>
                                <input type="number" class="form-control" name="login_tentativas" value="5" min="1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bloqueio após Tentativas (min)</label>
                                <input type="number" class="form-control" name="login_bloqueio" value="15" min="1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tamanho Mínimo de Senha</label>
                                <input type="number" class="form-control" name="senha_min" value="8" min="6">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Requisitos de Senha</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="senha_maiuscula" id="senhaMaiuscula" checked>
                                    <label class="form-check-label" for="senhaMaiuscula">Letras maiúsculas</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="senha_minuscula" id="senhaMinuscula" checked>
                                    <label class="form-check-label" for="senhaMinuscula">Letras minúsculas</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="senha_numero" id="senhaNumero" checked>
                                    <label class="form-check-label" for="senhaNumero">Números</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="senha_especial" id="senhaEspecial">
                                    <label class="form-check-label" for="senhaEspecial">Caracteres especiais</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="2fa_ativo" id="2faAtivo">
                                    <label class="form-check-label" for="2faAtivo">Habilitar autenticação em dois fatores (2FA)</label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>Salvar
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Seção Notificações -->
        <div class="config-section d-none" id="secao-notificacoes">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-bell me-2"></i>Configurações de Notificações
                </div>
                <div class="card-body">
                    <form id="formNotificacoes">
                        <h6 class="mb-3">Notificar por E-mail</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="notif_falha" id="notifFalha" checked>
                            <label class="form-check-label" for="notifFalha">Falha na execução de rotinas</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="notif_sucesso" id="notifSucesso">
                            <label class="form-check-label" for="notifSucesso">Sucesso na execução de rotinas</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="notif_agendamento" id="notifAgendamento">
                            <label class="form-check-label" for="notifAgendamento">Início de execução agendada</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="notif_conexao" id="notifConexao" checked>
                            <label class="form-check-label" for="notifConexao">Falha de conexão com banco</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="notif_sistema" id="notifSistema">
                            <label class="form-check-label" for="notifSistema">Erros de sistema</label>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">E-mails de Notificação</label>
                            <textarea class="form-control" name="notif_emails" rows="3" placeholder="Um e-mail por linha"></textarea>
                            <small class="text-muted">Digite os e-mails que receberão as notificações (um por linha)</small>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>Salvar
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Seção Backup -->
        <div class="config-section d-none" id="secao-backup">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-hdd me-2"></i>Backup e Restauração
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-cloud-download display-4 text-primary"></i>
                                    <h5 class="mt-3">Exportar Configurações</h5>
                                    <p class="text-muted">Faça backup de todas as configurações do sistema</p>
                                    <button class="btn btn-primary" onclick="exportarConfigs()">
                                        <i class="bi bi-download me-2"></i>Exportar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-cloud-upload display-4 text-success"></i>
                                    <h5 class="mt-3">Importar Configurações</h5>
                                    <p class="text-muted">Restaure configurações de um arquivo de backup</p>
                                    <input type="file" class="d-none" id="inputImportar" accept=".json">
                                    <button class="btn btn-success" onclick="document.getElementById('inputImportar').click()">
                                        <i class="bi bi-upload me-2"></i>Importar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="mb-3"><i class="bi bi-database me-2"></i>Backup do Banco de Dados</h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary" onclick="backupBD()">
                            <i class="bi bi-hdd me-2"></i>Criar Backup do BD
                        </button>
                        <button class="btn btn-outline-warning" onclick="limparDados()">
                            <i class="bi bi-trash me-2"></i>Limpar Dados Antigos
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$extraStyles = <<<'STYLES'
<style>
:root {
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --gradient-danger: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    --gradient-info: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    --shadow-sm: 0 2px 8px rgba(0,0,0,0.06);
    --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
    --shadow-lg: 0 8px 32px rgba(0,0,0,0.12);
    --radius-md: 12px;
    --radius-lg: 16px;
}

.page-header-modern {
    background: white;
    padding: 1.75rem 2rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.page-icon-modern {
    width: 70px;
    height: 70px;
    border-radius: var(--radius-lg);
    background: var(--gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
    flex-shrink: 0;
}

.page-title-modern {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 0.25rem 0;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-subtitle-modern {
    color: #64748b;
    margin: 0;
    font-size: 1rem;
}

.card-modern {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.card-modern-header {
    padding: 1.25rem 1.5rem;
    background: linear-gradient(to right, #f8fafc, #f1f5f9);
    border-bottom: 2px solid #e2e8f0;
    font-weight: 600;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-modern-body {
    padding: 1.5rem;
}

.btn-modern-primary {
    background: var(--gradient-primary);
    color: white;
    padding: 0.625rem 1.25rem;
    border-radius: var(--radius-md);
    border: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-sm);
}

.btn-modern-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: white;
}

.form-label-modern {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    display: block;
}

.form-control-modern, .form-select-modern {
    width: 100%;
    border: 2px solid #e2e8f0;
    border-radius: var(--radius-md);
    padding: 0.75rem 1rem;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
    background-color: #ffffff;
    color: #1e293b;
    line-height: 1.5;
}

.form-control-modern:hover, .form-select-modern:hover {
    border-color: #cbd5e1;
}

.form-control-modern:focus, .form-select-modern:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    outline: none;
    background-color: #ffffff;
}

.form-control-modern::placeholder {
    color: #94a3b8;
}

.form-control-modern:disabled,
.form-select-modern:disabled {
    background-color: #f1f5f9;
    cursor: not-allowed;
    opacity: 0.6;
}

textarea.form-control-modern {
    resize: vertical;
    min-height: 80px;
}

.list-group-item {
    border: none;
    border-radius: 0;
    transition: all 0.2s ease;
}

.list-group-item:hover {
    background-color: #f8fafc;
}

.list-group-item.active {
    background: var(--gradient-primary);
    color: white;
    border-color: transparent;
}

@media (max-width: 991px) {
    .page-header-modern {
        flex-wrap: wrap;
    }
}

@media (max-width: 767px) {
    .page-icon-modern {
        width: 48px;
        height: 48px;
        font-size: 1.5rem;
    }
    
    .page-title-modern {
        font-size: 1.5rem;
    }
}
</style>
STYLES;

$extraScripts = <<<'SCRIPTS'

<script>
// Navegação entre seções
$(".list-group-item").click(function(e) {
    e.preventDefault();
    $(".list-group-item").removeClass("active");
    $(this).addClass("active");
    
    const section = $(this).data("section");
    $(".config-section").addClass("d-none");
    $("#secao-" + section).removeClass("d-none");
});

// Carregar configurações
function carregarConfiguracoes() {
    $.getJSON(baseUrl + "/api/configuracoes", function(res) {
        if (res.configs) {
            Object.entries(res.configs).forEach(([key, val]) => {
                const input = $(`[name="${key}"]`);
                if (input.length) {
                    if (input.is(":checkbox")) {
                        input.prop("checked", val == 1 || val === true);
                    } else {
                        input.val(val);
                    }
                }
            });
        }
    });
}

// Salvar configurações
function salvarForm(form, endpoint) {
    const dados = {};
    $(form).serializeArray().forEach(item => {
        dados[item.name] = item.value;
    });
    
    // Checkboxes não marcados
    $(form).find(":checkbox").each(function() {
        if (!$(this).is(":checked")) {
            dados[$(this).attr("name")] = 0;
        } else {
            dados[$(this).attr("name")] = 1;
        }
    });
    
    $.post(baseUrl + endpoint, dados, function(res) {
        if (res.sucesso) {
            Swal.fire("Salvo!", "Configurações salvas com sucesso.", "success");
        } else {
            Swal.fire("Erro!", res.erro, "error");
        }
    }, "json");
}

// Forms
$("#formGeral").submit(function(e) {
    e.preventDefault();
    salvarForm(this, "/api/configuracoes/geral");
});

$("#formEmail").submit(function(e) {
    e.preventDefault();
    salvarForm(this, "/api/configuracoes/email");
});

$("#formLdap").submit(function(e) {
    e.preventDefault();
    salvarForm(this, "/api/configuracoes/ldap");
});

$("#formScheduler").submit(function(e) {
    e.preventDefault();
    salvarForm(this, "/api/configuracoes/scheduler");
});

$("#formSeguranca").submit(function(e) {
    e.preventDefault();
    salvarForm(this, "/api/configuracoes/seguranca");
});

$("#formNotificacoes").submit(function(e) {
    e.preventDefault();
    salvarForm(this, "/api/configuracoes/notificacoes");
});

// Testar email
function testarEmail() {
    Swal.fire({
        title: "Testar envio de e-mail",
        input: "email",
        inputLabel: "E-mail de destino",
        inputPlaceholder: "seu@email.com",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        confirmButtonText: "Enviar"
    }).then(result => {
        if (result.isConfirmed) {
            $.post(baseUrl + "/api/configuracoes/testar-email", { email: result.value }, function(res) {
                if (res.sucesso) {
                    Swal.fire("Sucesso!", "E-mail de teste enviado.", "success");
                } else {
                    Swal.fire("Erro!", res.erro, "error");
                }
            }, "json");
        }
    });
}

// Testar LDAP
function testarLdap() {
    Swal.fire({
        title: "Testar conexão LDAP",
        html: `
            <input type="text" class="form-control mb-2" id="swal-ldap-user" placeholder="Usuário">
            <input type="password" class="form-control" id="swal-ldap-pass" placeholder="Senha">
        `,
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        confirmButtonText: "Testar",
        preConfirm: () => {
            return {
                user: document.getElementById("swal-ldap-user").value,
                pass: document.getElementById("swal-ldap-pass").value
            };
        }
    }).then(result => {
        if (result.isConfirmed) {
            $.post(baseUrl + "/api/configuracoes/testar-ldap", result.value, function(res) {
                if (res.sucesso) {
                    Swal.fire("Sucesso!", "Conexão LDAP bem sucedida.", "success");
                } else {
                    Swal.fire("Erro!", res.erro, "error");
                }
            }, "json");
        }
    });
}

// Exportar configs
function exportarConfigs() {
    window.location.href = baseUrl + "/api/configuracoes/exportar";
}

// Importar configs
$("#inputImportar").change(function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const configs = JSON.parse(e.target.result);
                Swal.fire({
                    title: "Importar configurações?",
                    text: "Isso irá substituir as configurações atuais.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Importar",
                    cancelButtonText: "Cancelar"
                }).then(result => {
                    if (result.isConfirmed) {
                        $.post(baseUrl + "/api/configuracoes/importar", { configs: JSON.stringify(configs) }, function(res) {
                            if (res.sucesso) {
                                Swal.fire("Sucesso!", "Configurações importadas.", "success");
                                carregarConfiguracoes();
                            } else {
                                Swal.fire("Erro!", res.erro, "error");
                            }
                        }, "json");
                    }
                });
            } catch(err) {
                Swal.fire("Erro!", "Arquivo inválido.", "error");
            }
        };
        reader.readAsText(file);
    }
});

// Backup BD
function backupBD() {
    Swal.fire({
        title: "Criando backup...",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
            $.getJSON(baseUrl + "/api/configuracoes/backup-bd", function(res) {
                if (res.sucesso) {
                    Swal.fire("Sucesso!", "Backup criado: " + res.arquivo, "success");
                } else {
                    Swal.fire("Erro!", res.erro, "error");
                }
            });
        }
    });
}

// Limpar dados
function limparDados() {
    Swal.fire({
        title: "Limpar dados antigos?",
        html: `
            <p>Selecione o período de retenção:</p>
            <select class="form-select" id="swal-retencao">
                <option value="30">Manter últimos 30 dias</option>
                <option value="60">Manter últimos 60 dias</option>
                <option value="90" selected>Manter últimos 90 dias</option>
                <option value="180">Manter últimos 180 dias</option>
            </select>
        `,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        confirmButtonText: "Limpar",
        cancelButtonText: "Cancelar",
        preConfirm: () => document.getElementById("swal-retencao").value
    }).then(result => {
        if (result.isConfirmed) {
            $.post(baseUrl + "/api/configuracoes/limpar-dados", { dias: result.value }, function(res) {
                if (res.sucesso) {
                    Swal.fire("Sucesso!", `${res.removidos} registros removidos.`, "success");
                } else {
                    Swal.fire("Erro!", res.erro, "error");
                }
            }, "json");
        }
    });
}

$(document).ready(function() {
    carregarConfiguracoes();
});
</script>
SCRIPTS;

include __DIR__ . '/layouts/base.php';
?>
