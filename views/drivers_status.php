<?php
ob_start();
$pageTitle = 'Status dos Drivers';
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="page-header-modern">
        <div class="page-icon-modern">
            <i class="bi bi-plugin"></i>
        </div>
        <div>
            <h1 class="page-title-modern">Status dos Drivers PDO</h1>
            <p class="page-subtitle-modern">Verifique quais bancos de dados estão disponíveis para conexão</p>
        </div>
    </div>

    <!-- PHP Info Card -->
    <div class="card-modern mb-4">
        <div class="card-modern-header">
            <i class="bi bi-info-circle me-2"></i>
            Informações do PHP
        </div>
        <div class="card-modern-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="info-item">
                        <label>Versão do PHP:</label>
                        <strong id="php_version"></strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-item">
                        <label>Arquitetura:</label>
                        <strong id="php_arch"></strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-item">
                        <label>Thread Safe:</label>
                        <strong id="php_ts"></strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-item">
                        <label>Sistema:</label>
                        <strong id="php_os"></strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label>php.ini:</label>
                        <small id="php_ini" class="text-muted"></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label>Extension Dir:</label>
                        <small id="extension_dir" class="text-muted"></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Drivers Status -->
    <div class="card-modern">
        <div class="card-modern-header">
            <i class="bi bi-database me-2"></i>
            Status dos Drivers de Banco de Dados
        </div>
        <div class="card-modern-body">
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Banco de Dados</th>
                            <th>Driver PDO</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="driversTable">
                        <tr>
                            <td colspan="4" class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Instruções de Instalação -->
<div class="modal fade modal-modern" id="modalInstrucoes" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header-modern">
                <h5 class="modal-title">
                    <i class="bi bi-download me-2"></i>
                    <span id="modalInstallTitle">Instruções de Instalação</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body-modern">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Driver:</strong> <span id="driverName"></span>
                </div>
                
                <h6 class="mb-3"><i class="bi bi-list-ol me-2"></i>Passos para Instalação:</h6>
                <div id="instrucoesConteudo" class="install-steps"></div>
                
                <div id="linksUteis" class="mt-4"></div>
            </div>
            <div class="modal-footer-modern">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn-modern-primary" onclick="window.location.reload()">
                    <i class="bi bi-arrow-clockwise me-2"></i>Verificar Novamente
                </button>
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
    --shadow-sm: 0 2px 8px rgba(0,0,0,0.06);
    --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
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

.info-item {
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.info-item label {
    display: block;
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.info-item strong {
    color: #1e293b;
    font-size: 0.9375rem;
}

.table-modern {
    width: 100%;
    margin: 0;
}

.table-modern thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.table-modern thead th {
    padding: 1rem;
    font-weight: 600;
    border: none;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-modern tbody tr {
    border-bottom: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.table-modern tbody tr:hover {
    background-color: #f8fafc;
}

.table-modern tbody td {
    padding: 1rem;
    vertical-align: middle;
}

.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
}

.badge-success {
    background: var(--gradient-success);
    color: white;
}

.badge-danger {
    background: var(--gradient-danger);
    color: white;
}

.install-steps {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1.5rem;
    font-family: 'Consolas', 'Monaco', monospace;
    font-size: 0.875rem;
    line-height: 1.8;
    white-space: pre-wrap;
    color: #1e293b;
}

.links-uteis {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    padding: 1rem;
}

.links-uteis h6 {
    color: #1e40af;
    margin-bottom: 0.75rem;
}

.links-uteis a {
    color: #2563eb;
    text-decoration: none;
    display: block;
    padding: 0.5rem;
    border-radius: 6px;
    transition: all 0.2s;
}

.links-uteis a:hover {
    background: #dbeafe;
    padding-left: 1rem;
}

.modal-modern .modal-header-modern {
    background: var(--gradient-primary);
    color: white;
    padding: 1.25rem 1.5rem;
    border-bottom: none;
}

.modal-modern .modal-header-modern .btn-close {
    filter: brightness(0) invert(1);
}

.modal-modern .modal-body-modern {
    padding: 1.5rem;
}

.modal-modern .modal-footer-modern {
    padding: 1rem 1.5rem;
    border-top: 2px solid #e2e8f0;
    background: #f8fafc;
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
</style>
STYLES;

$extraScripts = <<<'SCRIPTS'

<script>
const dbIcons = {
    postgres: '🐘',
    mysql: '🐬',
    mariadb: '🐬',
    sqlserver: '🗄️',
    oracle: '⚡',
    sqlite: '📦'
};

function carregarStatus() {
    $.getJSON(baseUrl + "/conexoes/drivers-status", function(res) {
        if (!res.sucesso) {
            console.error("Erro ao carregar status dos drivers");
            return;
        }

        // Preencher informações do PHP
        const phpInfo = res.php_info;
        document.getElementById('php_version').textContent = phpInfo.versao_php;
        document.getElementById('php_arch').textContent = phpInfo.architecture;
        document.getElementById('php_ts').textContent = phpInfo.thread_safe;
        document.getElementById('php_os').textContent = phpInfo.os;
        document.getElementById('php_ini').textContent = phpInfo.php_ini;
        document.getElementById('extension_dir').textContent = phpInfo.extension_dir;

        // Preencher tabela de drivers
        const tbody = document.getElementById('driversTable');
        tbody.innerHTML = '';

        res.drivers.forEach(driver => {
            const icon = dbIcons[driver.tipo_banco] || '🔌';
            const statusBadge = driver.disponivel
                ? '<span class="badge-status badge-success"><i class="bi bi-check-circle me-1"></i>Disponível</span>'
                : '<span class="badge-status badge-danger"><i class="bi bi-x-circle me-1"></i>Não Instalado</span>';

            let actionBtn;
            if (driver.disponivel) {
                actionBtn = '<span class="text-success"><i class="bi bi-check-circle-fill"></i> OK</span>';
            } else {
                // Mostrar botão de instalação automática para Oracle e SQL Server
                if (driver.tipo_banco === 'oracle' || driver.tipo_banco === 'sqlserver') {
                    actionBtn = `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-success" onclick="instalarDriver('${driver.tipo_banco}')">
                                <i class="bi bi-download me-1"></i>Instalar
                            </button>
                            <button class="btn btn-outline-primary" onclick="mostrarInstrucoes('${driver.tipo_banco}')">
                                <i class="bi bi-info-circle me-1"></i>Manual
                            </button>
                        </div>
                    `;
                } else {
                    actionBtn = `<button class="btn btn-sm btn-outline-primary" onclick="mostrarInstrucoes('${driver.tipo_banco}')">
                         <i class="bi bi-download me-1"></i>Como Instalar
                       </button>`;
                }
            }

            tbody.innerHTML += `
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span style="font-size: 1.5rem;">${icon}</span>
                            <strong>${driver.nome_exibicao}</strong>
                        </div>
                    </td>
                    <td><code>${driver.driver}</code></td>
                    <td>${statusBadge}</td>
                    <td>${actionBtn}</td>
                </tr>
            `;
        });
    }).fail(function() {
        Swal.fire('Erro!', 'Não foi possível carregar o status dos drivers.', 'error');
    });
}

function instalarDriver(tipoBanco) {
    const icon = dbIcons[tipoBanco] || '🔌';
    const nomes = {
        'oracle': 'Oracle OCI8',
        'sqlserver': 'SQL Server'
    };
    const nomeBanco = nomes[tipoBanco] || tipoBanco.toUpperCase();

    // Primeira tentativa sem download
    Swal.fire({
        title: 'Verificando...',
        text: 'Analisando ambiente e requisitos',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    $.ajax({
        url: baseUrl + "/conexoes/install-driver/" + tipoBanco,
        method: 'POST',
        data: { auto_download: 'false' },
        dataType: 'json'
    }).then(response => {
        Swal.close();
        
        if (response.sucesso || response.requer_restart_manual) {
            // Instalação concluída sem download
            mostrarResultadoInstalacao(response, tipoBanco);
        } else if (response.requer_download) {
            // Precisa fazer download - solicitar aprovação
            solicitarAprovacaoDownload(response, tipoBanco);
        } else {
            // Erro ou necessita Instant Client
            mostrarErroInstalacao(response, tipoBanco);
        }
    }).catch(error => {
        Swal.fire('Erro!', 'Erro ao verificar requisitos: ' + error.statusText, 'error');
    });
}

function solicitarAprovacaoDownload(info, tipoBanco) {
    const icon = dbIcons[tipoBanco] || '🔌';
    const downloadInfo = info.download_info || {};
    
    Swal.fire({
        title: `${icon} Download Necessário`,
        html: `
            <div class="text-start">
                <p class="mb-3">${info.mensagem}</p>
                
                <div class="alert alert-info">
                    <strong><i class="bi bi-download me-2"></i>Detalhes do Download:</strong><br>
                    <small>
                        • Fonte: ${downloadInfo.url ? 'Repositório Oficial PHP' : 'GitHub Microsoft'}<br>
                        • Tamanho: ~${downloadInfo.tamanho_estimado}<br>
                        • Versão PHP: ${downloadInfo.php_version}<br>
                        • Arquitetura: ${downloadInfo.arch}-bit
                    </small>
                </div>
                
                <div class="alert alert-warning">
                    <strong><i class="bi bi-info-circle me-2"></i>O que será feito:</strong><br>
                    <small>
                        1. Download automático das DLLs necessárias<br>
                        2. Extração dos arquivos<br>
                        3. Cópia para pasta de extensões do PHP<br>
                        4. Configuração do php.ini<br>
                        5. Reinicialização do Apache
                    </small>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="bi bi-download me-2"></i>Sim, Baixar e Instalar',
        cancelButtonText: 'Cancelar',
        width: '600px'
    }).then((result) => {
        if (result.isConfirmed) {
            executarDownloadEInstalacao(tipoBanco);
        }
    });
}

function executarDownloadEInstalacao(tipoBanco) {
    const icon = dbIcons[tipoBanco] || '🔌';
    
    Swal.fire({
        title: `${icon} Instalando...`,
        html: `
            <div class="text-start">
                <p id="install-status">Iniciando download...</p>
                <div class="progress mt-3" style="height: 25px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" 
                         style="width: 100%"
                         id="install-progress">
                        <span>Processando...</span>
                    </div>
                </div>
                <div id="install-steps" class="mt-3" style="max-height: 200px; overflow-y: auto; font-size: 0.875rem; font-family: monospace;">
                </div>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false,
        width: '600px'
    });

    $.ajax({
        url: baseUrl + "/conexoes/install-driver/" + tipoBanco,
        method: 'POST',
        data: { auto_download: 'true' },
        dataType: 'json'
    }).then(response => {
        mostrarResultadoInstalacao(response, tipoBanco);
    }).catch(error => {
        Swal.fire('Erro!', 'Erro durante instalação: ' + error.statusText, 'error');
    });
}

function mostrarResultadoInstalacao(res, tipoBanco) {
    if (res.sucesso || res.requer_restart_manual) {
        let mensagemHtml = `<div class="text-start">`;
        mensagemHtml += `<p class="mb-3">${res.mensagem}</p>`;
        
        if (res.steps && res.steps.length > 0) {
            mensagemHtml += `<div class="alert alert-info"><small>`;
            res.steps.forEach(step => {
                mensagemHtml += `${step}<br>`;
            });
            mensagemHtml += `</small></div>`;
        }
        
        if (res.requer_reload) {
            mensagemHtml += `<div class="alert alert-success">`;
            mensagemHtml += `<i class="bi bi-info-circle me-2"></i>`;
            mensagemHtml += `Aguarde 5 segundos, a página será recarregada.`;
            mensagemHtml += `</div>`;
        }
        
        if (res.requer_restart_manual) {
            mensagemHtml += `<div class="alert alert-warning">`;
            mensagemHtml += `<i class="bi bi-exclamation-triangle me-2"></i>`;
            mensagemHtml += `<strong>Reinicie o XAMPP manualmente</strong> para ativar o driver.`;
            mensagemHtml += `</div>`;
        }
        
        mensagemHtml += `</div>`;
        
        Swal.fire({
            title: '✓ Instalação Concluída!',
            html: mensagemHtml,
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            if (res.requer_reload) {
                setTimeout(() => {
                    window.location.reload();
                }, 5000);
            }
        });
    } else {
        mostrarErroInstalacao(res, tipoBanco);
    }
}

function mostrarErroInstalacao(res, tipoBanco) {
    let mensagemErro = `<div class="text-start">`;
    mensagemErro += `<p class="text-danger"><strong>${res.mensagem}</strong></p>`;
    
    if (res.steps && res.steps.length > 0) {
        mensagemErro += `<div class="alert alert-info mt-3">`;
        mensagemErro += `<strong>Etapas executadas:</strong><br><small>`;
        res.steps.forEach(step => {
            mensagemErro += `${step}<br>`;
        });
        mensagemErro += `</small></div>`;
    }
    
    // Erro de conectividade/download
    if (res.url) {
        mensagemErro += `<div class="alert alert-warning mt-3">`;
        mensagemErro += `<i class="bi bi-wifi-off me-2"></i>`;
        mensagemErro += `<strong>Problema de Conectividade</strong><br>`;
        mensagemErro += `<small>Não foi possível baixar de: <code>${res.url}</code></small><br>`;
        
        if (res.metodos_tentados && res.metodos_tentados.length > 0) {
            mensagemErro += `<small class="mt-2 d-block"><strong>Métodos tentados:</strong><br>`;
            res.metodos_tentados.forEach(metodo => {
                mensagemErro += `• ${metodo}<br>`;
            });
            mensagemErro += `</small>`;
        }
        
        mensagemErro += `<div class="mt-3">`;
        mensagemErro += `<strong>Possíveis soluções:</strong><br>`;
        mensagemErro += `<small>`;
        mensagemErro += `1. Verifique sua conexão com a internet<br>`;
        mensagemErro += `2. Tente novamente em alguns minutos<br>`;
        mensagemErro += `3. Verifique se firewall/antivírus está bloqueando<br>`;
        mensagemErro += `4. Baixe manualmente e copie para php/ext`;
        mensagemErro += `</small>`;
        mensagemErro += `</div>`;
        mensagemErro += `</div>`;
    }
    
    if (res.requer_instant_client) {
        mensagemErro += `<div class="alert alert-warning mt-3">`;
        mensagemErro += `<i class="bi bi-info-circle me-2"></i>`;
        mensagemErro += `<strong>Oracle Instant Client necessário!</strong><br>`;
        mensagemErro += `As DLLs foram configuradas, mas o Oracle Instant Client precisa ser instalado separadamente.<br>`;
        mensagemErro += `<a href="${res.instant_client_url}" target="_blank" class="btn btn-sm btn-primary mt-2">`;
        mensagemErro += `<i class="bi bi-download me-1"></i>Baixar Instant Client`;
        mensagemErro += `</a>`;
        mensagemErro += `</div>`;
    }
    
    if (res.manual_required || res.manual_url) {
        mensagemErro += `<div class="alert alert-info mt-3">`;
        mensagemErro += `<i class="bi bi-book me-2"></i>`;
        mensagemErro += `<strong>Download Manual:</strong><br>`;
        mensagemErro += `Se o download automático não funcionar, você pode baixar manualmente:<br>`;
        if (res.url) {
            mensagemErro += `<a href="${res.url}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">`;
            mensagemErro += `<i class="bi bi-download me-1"></i>Download Manual`;
            mensagemErro += `</a> `;
        }
        if (res.manual_url) {
            mensagemErro += `<a href="${res.manual_url}" target="_blank" class="btn btn-sm btn-outline-secondary mt-2">`;
            mensagemErro += `<i class="bi bi-box-arrow-up-right me-1"></i>Documentação`;
            mensagemErro += `</a>`;
        }
        mensagemErro += `</div>`;
    }
    
    mensagemErro += `</div>`;
    
    Swal.fire({
        title: res.url ? 'Erro de Download' : 'Falha na Instalação',
        html: mensagemErro,
        icon: res.requer_instant_client ? 'warning' : 'error',
        width: '650px',
        confirmButtonText: 'Ver Instruções Manuais'
    }).then((result) => {
        if (result.isConfirmed && !res.requer_instant_client) {
            mostrarInstrucoes(tipoBanco);
        }
    });
}

function mostrarInstrucoes(tipoBanco) {
    $.getJSON(baseUrl + "/conexoes/driver-install-info/" + tipoBanco, function(res) {
        if (!res.sucesso) {
            Swal.fire('Erro!', 'Não foi possível carregar as instruções.', 'error');
            return;
        }

        const info = res.driver_info;
        const icon = dbIcons[tipoBanco] || '🔌';

        document.getElementById('modalInstallTitle').innerHTML = 
            `${icon} Instalar Driver - ${tipoBanco.toUpperCase()}`;
        
        document.getElementById('driverName').textContent = 
            `${info.nome} (${info.driver})`;

        document.getElementById('instrucoesConteudo').textContent = 
            info.instrucoes.join('\n');

        // Links úteis
        const linksDiv = document.getElementById('linksUteis');
        if (info.links && Object.keys(info.links).length > 0) {
            let linksHtml = '<div class="links-uteis">';
            linksHtml += '<h6><i class="bi bi-link-45deg me-2"></i>Links Úteis:</h6>';
            for (const [nome, url] of Object.entries(info.links)) {
                linksHtml += `<a href="${url}" target="_blank">
                    <i class="bi bi-box-arrow-up-right me-2"></i>${nome}
                </a>`;
            }
            linksHtml += '</div>';
            linksDiv.innerHTML = linksHtml;
        } else {
            linksDiv.innerHTML = '';
        }

        new bootstrap.Modal('#modalInstrucoes').show();
    }).fail(function() {
        Swal.fire('Erro!', 'Não foi possível carregar as instruções.', 'error');
    });
}

function instalarDriver(tipoBanco) {
    const icon = dbIcons[tipoBanco] || '🔌';
    const nomes = {
        'oracle': 'Oracle OCI8',
        'sqlserver': 'SQL Server'
    };
    const nomeBanco = nomes[tipoBanco] || tipoBanco.toUpperCase();

    Swal.fire({
        title: `${icon} Instalar ${nomeBanco}?`,
        html: `
            <div class="text-start">
                <p><strong>O sistema irá:</strong></p>
                <ul class="text-muted">
                    <li>Verificar sua versão do PHP</li>
                    <li>Habilitar extensões no php.ini</li>
                    <li>Fazer backup das configurações</li>
                    <li>Tentar reiniciar o Apache automaticamente</li>
                </ul>
                <div class="alert alert-warning mt-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Importante:</strong> Este processo pode levar alguns minutos e requer 
                    que o PHP tenha permissões para modificar arquivos.
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="bi bi-download me-2"></i>Sim, Instalar',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        allowOutsideClick: false,
        preConfirm: () => {
            return $.ajax({
                url: baseUrl + "/conexoes/install-driver/" + tipoBanco,
                method: 'POST',
                dataType: 'json'
            }).then(response => {
                return response;
            }).catch(error => {
                Swal.showValidationMessage(
                    `Erro na requisição: ${error.responseText || error.statusText}`
                );
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const res = result.value;
            
            if (res.sucesso) {
                let mensagemHtml = `<div class="text-start">`;
                mensagemHtml += `<p class="mb-3">${res.mensagem}</p>`;
                
                if (res.steps && res.steps.length > 0) {
                    mensagemHtml += `<div class="alert alert-info">`;
                    mensagemHtml += `<strong>Etapas executadas:</strong><br>`;
                    res.steps.forEach(step => {
                        mensagemHtml += `<small>• ${step}</small><br>`;
                    });
                    mensagemHtml += `</div>`;
                }
                
                if (res.requer_reload) {
                    mensagemHtml += `<div class="alert alert-success">`;
                    mensagemHtml += `<i class="bi bi-info-circle me-2"></i>`;
                    mensagemHtml += `Aguarde 5 segundos e a página será recarregada automaticamente.`;
                    mensagemHtml += `</div>`;
                }
                
                if (res.requer_restart_manual) {
                    mensagemHtml += `<div class="alert alert-warning">`;
                    mensagemHtml += `<i class="bi bi-exclamation-triangle me-2"></i>`;
                    mensagemHtml += `<strong>Ação necessária:</strong> Reinicie o XAMPP manualmente.`;
                    mensagemHtml += `</div>`;
                }
                
                mensagemHtml += `</div>`;
                
                Swal.fire({
                    title: 'Instalação Concluída!',
                    html: mensagemHtml,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    if (res.requer_reload) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 5000);
                    }
                });
            } else {
                let mensagemErro = `<div class="text-start">`;
                mensagemErro += `<p class="text-danger">${res.mensagem}</p>`;
                
                if (res.steps && res.steps.length > 0) {
                    mensagemErro += `<div class="alert alert-info mt-3">`;
                    mensagemErro += `<strong>Etapas tentadas:</strong><br>`;
                    res.steps.forEach(step => {
                        mensagemErro += `<small>• ${step}</small><br>`;
                    });
                    mensagemErro += `</div>`;
                }
                
                if (res.requer_instant_client) {
                    mensagemErro += `<div class="alert alert-warning mt-3">`;
                    mensagemErro += `<i class="bi bi-info-circle me-2"></i>`;
                    mensagemErro += `<strong>Necessário:</strong> Baixe e instale o Oracle Instant Client primeiro.<br>`;
                    mensagemErro += `<a href="${res.instant_client_url}" target="_blank" class="btn btn-sm btn-primary mt-2">`;
                    mensagemErro += `<i class="bi bi-download me-1"></i>Baixar Instant Client`;
                    mensagemErro += `</a>`;
                    mensagemErro += `</div>`;
                }
                
                if (res.manual_required) {
                    mensagemErro += `<div class="alert alert-info mt-3">`;
                    mensagemErro += `<i class="bi bi-book me-2"></i>`;
                    mensagemErro += `Instalação manual necessária. Consulte as instruções detalhadas.`;
                    mensagemErro += `</div>`;
                }
                
                mensagemErro += `</div>`;
                
                Swal.fire({
                    title: 'Falha na Instalação',
                    html: mensagemErro,
                    icon: 'error',
                    confirmButtonText: 'Ver Instruções Manuais'
                }).then((result) => {
                    if (result.isConfirmed) {
                        mostrarInstrucoes(tipoBanco);
                    }
                });
            }
        }
    });
}

$(document).ready(function() {
    carregarStatus();
});
</script>
SCRIPTS;

include __DIR__ . '/layouts/base.php';
?>
