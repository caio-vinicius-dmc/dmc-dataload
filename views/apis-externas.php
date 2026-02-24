<?php 
$pageTitle = 'APIs Externas';
$currentPage = 'apis-externas';

ob_start();
?>

<style>
/* ==== ESTILOS BASE MODERNOS ==== */
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
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
    border: 1px solid rgba(0,0,0,0.04);
    transition: var(--transition);
}

.card-modern:hover {
    box-shadow: var(--shadow-md);
}

.card-modern-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 2px solid #f3f4f6;
    font-weight: 700;
    font-size: 1.05rem;
    color: #1a202c;
    display: flex;
    align-items: center;
}

.card-modern-body {
    padding: 1.5rem;
}

.btn-modern-primary {
    background: var(--gradient-primary);
    border: none;
    color: white;
    padding: 0.65rem 1.5rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 0.95rem;
    transition: var(--transition);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-modern-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    color: white;
}

.stat-card-modern {
    background: white;
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid rgba(0,0,0,0.04);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-card-modern:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.stat-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    transition: width 0.3s ease;
}

.stat-card-modern:hover::before {
    width: 6px;
}

.success-card::before { background: var(--gradient-success); }
.danger-card::before { background: var(--gradient-danger); }
.info-card::before { background: var(--gradient-info); }
.primary-card::before { background: var(--gradient-primary); }

.stat-icon-modern {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    flex-shrink: 0;
}

.success-card .stat-icon-modern {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.15) 100%);
    color: #10b981;
}

.danger-card .stat-icon-modern {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.15) 100%);
    color: #ef4444;
}

.info-card .stat-icon-modern {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.15) 100%);
    color: #3b82f6;
}

.primary-card .stat-icon-modern {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.15) 100%);
    color: #667eea;
}

.stat-content {
    flex: 1;
}

.stat-value-modern {
    font-size: 2rem;
    font-weight: 800;
    color: #1a202c;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label-modern {
    font-size: 0.85rem;
    color: #6b7280;
    font-weight: 500;
}

.table-modern {
    width: 100%;
    margin-bottom: 0;
}

.table-modern thead th {
    background: linear-gradient(135deg, #fafbff 0%, #f0f4ff 100%);
    color: #4b5563;
    font-weight: 700;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1rem 1.25rem;
    border-bottom: 2px solid #e5e7eb;
}

.table-modern tbody td {
    padding: 1rem 1.25rem;
    vertical-align: middle;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.95rem;
}

.table-modern tbody tr {
    transition: var(--transition);
}

.table-modern tbody tr:hover {
    background: rgba(102, 126, 234, 0.03);
}

/* ==== ESTILOS ESPECÍFICOS ==== */
/* Estilos específicos para APIs */
.api-card {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}
.api-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}
.api-card.active { border-left-color: #10b981; }
.api-card.inactive { border-left-color: #ef4444; }

.method-badge {
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}
.method-GET { background: #dbeafe; color: #1d4ed8; }
.method-POST { background: #dcfce7; color: #15803d; }
.method-PUT { background: #fef3c7; color: #b45309; }
.method-DELETE { background: #fee2e2; color: #dc2626; }

.auth-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
    background: #f3f4f6;
    color: #6b7280;
}
.auth-bearer { background: #e0e7ff; color: #4338ca; }
.auth-basic { background: #fce7f3; color: #be185d; }
.auth-api_key { background: #ccfbf1; color: #0d9488; }

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 6px;
}
.status-success { background: #10b981; }
.status-error { background: #ef4444; }
.status-unknown { background: #9ca3af; }

.response-preview {
    max-height: 300px;
    overflow: auto;
    background: #1e293b;
    color: #e2e8f0;
    padding: 1rem;
    border-radius: 8px;
    font-family: 'Fira Code', monospace;
    font-size: 0.85rem;
}

.header-row {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}
.header-row input {
    flex: 1;
}
</style>

<!-- Header da Página -->
<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-cloud-arrow-down-fill"></i>
    </div>
    <div>
        <h1 class="page-title-modern">APIs Externas</h1>
        <p class="page-subtitle-modern">Gerencie conexões com APIs externas para monitoramento e integração</p>
    </div>
    <div class="ms-auto">
        <button class="btn-modern-primary" onclick="abrirModalApi()">
            <i class="bi bi-plus-lg me-2"></i>Nova API
        </button>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern primary-card">
            <div class="stat-icon-modern"><i class="bi bi-cloud"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="totalApis">0</div>
                <div class="stat-label-modern">Total de APIs</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern success-card">
            <div class="stat-icon-modern"><i class="bi bi-check-circle"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="apisAtivas">0</div>
                <div class="stat-label-modern">Ativas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern info-card">
            <div class="stat-icon-modern"><i class="bi bi-bell"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="totalEventos">0</div>
                <div class="stat-label-modern">Eventos Configurados</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern danger-card">
            <div class="stat-icon-modern"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="apisComErro">0</div>
                <div class="stat-label-modern">Com Erro</div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de APIs -->
<div class="card-modern">
    <div class="card-modern-header">
        <i class="bi bi-list-ul me-2"></i>
        <span>APIs Cadastradas</span>
        <div class="ms-auto d-flex gap-2">
            <input type="text" class="form-control form-control-sm" placeholder="Buscar..." id="searchApis" style="width: 200px;">
            <button class="btn btn-sm btn-outline-secondary" onclick="carregarApis()">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>
    <div class="card-modern-body">
        <div class="row g-3" id="listaApis">
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary"></div>
                <p class="mt-2 text-muted">Carregando APIs...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Criar/Editar API -->
<div class="modal fade" id="modalApi" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-cloud-plus me-2"></i>
                    <span id="modalApiTitulo">Nova API</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formApi">
                <div class="modal-body">
                    <input type="hidden" name="id" id="apiId">
                    
                    <!-- Informações Básicas -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nome *</label>
                            <input type="text" class="form-control" name="nome" id="apiNome" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tipo de Resposta</label>
                            <select class="form-select" name="tipo_resposta" id="apiTipoResposta">
                                <option value="json">JSON</option>
                                <option value="xml">XML</option>
                                <option value="text">Texto</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descrição</label>
                            <textarea class="form-control" name="descricao" id="apiDescricao" rows="2"></textarea>
                        </div>
                    </div>
                    
                    <!-- URL e Método -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Método</label>
                            <select class="form-select" name="metodo" id="apiMetodo">
                                <option value="GET">GET</option>
                                <option value="POST">POST</option>
                                <option value="PUT">PUT</option>
                                <option value="DELETE">DELETE</option>
                                <option value="PATCH">PATCH</option>
                            </select>
                        </div>
                        <div class="col-md-10">
                            <label class="form-label fw-semibold">URL *</label>
                            <input type="url" class="form-control" name="url" id="apiUrl" required placeholder="https://api.example.com/endpoint">
                        </div>
                    </div>
                    
                    <!-- Autenticação -->
                    <div class="card mb-4">
                        <div class="card-header py-2">
                            <i class="bi bi-shield-lock me-2"></i>Autenticação
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Tipo</label>
                                    <select class="form-select" name="auth_tipo" id="apiAuthTipo" onchange="toggleAuthFields()">
                                        <option value="none">Nenhuma</option>
                                        <option value="bearer">Bearer Token</option>
                                        <option value="basic">Basic Auth</option>
                                        <option value="api_key">API Key</option>
                                    </select>
                                </div>
                                <div class="col-md-8" id="authFieldsContainer">
                                    <!-- Campos dinâmicos de autenticação -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Headers -->
                    <div class="card mb-4">
                        <div class="card-header py-2 d-flex justify-content-between">
                            <span><i class="bi bi-list-columns me-2"></i>Headers</span>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="adicionarHeader()">
                                <i class="bi bi-plus"></i> Adicionar
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="headersContainer">
                                <!-- Headers dinâmicos -->
                            </div>
                            <small class="text-muted">Headers customizados para a requisição</small>
                        </div>
                    </div>
                    
                    <!-- Body (para POST/PUT) -->
                    <div class="card mb-4" id="bodyCard" style="display: none;">
                        <div class="card-header py-2">
                            <i class="bi bi-code-square me-2"></i>Body Template
                        </div>
                        <div class="card-body">
                            <textarea class="form-control font-monospace" name="body_template" id="apiBodyTemplate" rows="4" placeholder='{"key": "value"}'></textarea>
                        </div>
                    </div>
                    
                    <!-- Configurações -->
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Intervalo de Polling (seg)</label>
                            <input type="number" class="form-control" name="intervalo_polling" id="apiIntervalo" value="60" min="10" max="3600">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Timeout (seg)</label>
                            <input type="number" class="form-control" name="timeout" id="apiTimeout" value="30" min="5" max="120">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="ativo" id="apiAtivo" checked>
                                <label class="form-check-label" for="apiAtivo">Ativa</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" onclick="testarApi()">
                        <i class="bi bi-play-fill me-1"></i>Testar Conexão
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Teste de API -->
<div class="modal fade" id="modalTesteApi" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="bi bi-lightning-charge me-2"></i>
                    Teste de API
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-4"><strong>Nome:</strong></div>
                        <div class="col-8" id="testeApiNome">-</div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-4"><strong>URL:</strong></div>
                        <div class="col-8"><small class="text-muted" id="testeApiUrl">-</small></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-4"><strong>Método:</strong></div>
                        <div class="col-8"><span class="badge bg-primary" id="testeApiMetodo">-</span></div>
                    </div>
                </div>
                <hr>
                <div id="testeResultado">
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
                        <p class="mt-2">Clique no botão "Testar" para executar a requisição</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$extraStyles = <<<STYLES
STYLES;

$extraScripts = <<<'SCRIPTS'
<script>
let apis = [];

// Funções auxiliares de UI
function mostrarErro(mensagem) {
    // Criar toast de erro
    const toast = `
        <div class="toast align-items-center text-white bg-danger border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-exclamation-triangle me-2"></i>${mensagem}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    // Container de toasts (criar se não existir)
    let container = $('#toast-container');
    if (container.length === 0) {
        $('body').append('<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999"></div>');
        container = $('#toast-container');
    }
    
    // Adicionar e mostrar toast
    const toastEl = $(toast);
    container.append(toastEl);
    const bsToast = new bootstrap.Toast(toastEl[0]);
    bsToast.show();
    
    // Remover após ocultar
    toastEl.on('hidden.bs.toast', function() {
        $(this).remove();
    });
}

function mostrarSucesso(mensagem) {
    const toast = `
        <div class="toast align-items-center text-white bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle me-2"></i>${mensagem}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    let container = $('#toast-container');
    if (container.length === 0) {
        $('body').append('<div id="toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999"></div>');
        container = $('#toast-container');
    }
    
    const toastEl = $(toast);
    container.append(toastEl);
    const bsToast = new bootstrap.Toast(toastEl[0]);
    bsToast.show();
    
    toastEl.on('hidden.bs.toast', function() {
        $(this).remove();
    });
}

// Carregar APIs
function carregarApis() {
    $.getJSON(baseUrl + '/api/apis-externas/list', function(res) {
        if (res.sucesso) {
            apis = res.dados || [];
            renderizarApis(apis);
            atualizarEstatisticas();
        }
    }).fail(function() {
        $('#listaApis').html('<div class="col-12 text-center py-5 text-danger">Erro ao carregar APIs</div>');
    });
}

// Renderizar APIs
function renderizarApis(lista) {
    const container = $('#listaApis');
    
    if (!lista || lista.length === 0) {
        container.html('<div class="col-12 text-center py-5"><p class="text-muted">Nenhuma API cadastrada</p><button class="btn btn-primary" onclick="abrirModalApi()"><i class="bi bi-plus-lg me-2"></i>Cadastrar Primeira API</button></div>');
        return;
    }
    
    let html = '';
    lista.forEach(api => {
        const statusClass = api.ativo ? 'active' : 'inactive';
        const badgeClass = api.ativo ? 'bg-success' : 'bg-secondary';
        
        html += `
        <div class="col-md-6 col-xl-4">
            <div class="card api-card \${statusClass} h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0">\${api.nome}</h6>
                        <span class="method-badge method-\${api.metodo}">\${api.metodo}</span>
                    </div>
                    <p class="text-muted small text-truncate mb-2" title="\${api.url}">\${api.url}</p>
                    <div class="d-flex gap-2 mb-3">
                        <span class="auth-badge">\${api.tipo_autenticacao || 'none'}</span>
                        <span class="badge \${badgeClass}">\${api.ativo ? 'Ativa' : 'Inativa'}</span>
                    </div>
                    <div class="btn-group w-100 btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="testarApi(\${api.id_api})" title="Testar">
                            <i class="bi bi-play-circle"></i>
                        </button>
                        <button class="btn btn-outline-warning" onclick="editarApi(\${api.id_api})" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <a href="\${baseUrl}/eventos-api?api=\${api.id_api}" class="btn btn-outline-info" title="Eventos">
                            <i class="bi bi-bell"></i>
                        </a>
                        <button class="btn btn-outline-danger" onclick="excluirApi(\${api.id_api})" title="Excluir">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        `;
    });
    
    container.html(html);
}

// Atualizar estatísticas
function atualizarEstatisticas() {
    const total = apis.length;
    const ativas = apis.filter(a => a.ativo).length;
    
    $.getJSON(baseUrl + '/api/eventos-api/list', function(res) {
        const eventos = (res.sucesso && res.dados) ? res.dados.length : 0;
        
        $('#totalApis').text(total);
        $('#apisAtivas').text(ativas);
        $('#totalEventos').text(eventos);
        $('#ultimaExecucao').text('0h');
    });
}

// Abrir modal API
function abrirModalApi(id = null) {
    if (id) {
        const api = apis.find(a => a.id_api === id);
        if (api) {
            $('#modalApiLabel').text('Editar API');
            $('#apiId').val(api.id_api);
            $('#apiNome').val(api.nome);
            $('#apiDescricao').val(api.descricao);
            $('#apiUrl').val(api.url);
            $('#apiMetodo').val(api.metodo);
            $('#apiTipoAuth').val(api.tipo_autenticacao || 'none');
            $('#apiIntervalo').val(api.intervalo_verificacao);
            $('#apiAtivo').prop('checked', api.ativo);
        }
    } else {
        $('#modalApiLabel').text('Nova API');
        $('#formApi')[0].reset();
        $('#apiId').val('');
        $('#apiAtivo').prop('checked', true);
    }
    new bootstrap.Modal('#modalApi').show();
}

// Salvar API
function salvarApi() {
    // Validação dos campos
    const nome = $('#apiNome').val().trim();
    const url = $('#apiUrl').val().trim();
    const intervalo = parseInt($('#apiIntervalo').val());
    
    // Validações
    if (!nome) {
        mostrarErro('O campo Nome é obrigatório');
        $('#apiNome').focus();
        return false;
    }
    
    if (nome.length < 3) {
        mostrarErro('O nome deve ter pelo menos 3 caracteres');
        $('#apiNome').focus();
        return false;
    }
    
    if (!url) {
        mostrarErro('O campo URL é obrigatório');
        $('#apiUrl').focus();
        return false;
    }
    
    // Validar formato de URL
    const urlPattern = /^https?:\/\/.+/i;
    if (!urlPattern.test(url)) {
        mostrarErro('URL inválida. Deve começar com http:// ou https://');
        $('#apiUrl').focus();
        return false;
    }
    
    if (isNaN(intervalo) || intervalo < 10) {
        mostrarErro('Intervalo deve ser no mínimo 10 segundos');
        $('#apiIntervalo').focus();
        return false;
    }
    
    const data = {
        id_api: $('#apiId').val() || null,
        nome: nome,
        descricao: $('#apiDescricao').val().trim(),
        url: url,
        metodo: $('#apiMetodo').val(),
        tipo_autenticacao: $('#apiTipoAuth').val(),
        credenciais: $('#apiAuthConfig').val(),
        intervalo_verificacao: intervalo,
        ativo: $('#apiAtivo').is(':checked')
    };
    
    // Desabilitar botão para evitar duplo clique
    const btnSalvar = $('#modalApi .btn-primary');
    btnSalvar.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Salvando...');
    
    $.ajax({
        url: baseUrl + '/api/apis-externas/salvar',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function(res) {
            btnSalvar.prop('disabled', false).html('Salvar');
            if (res.sucesso) {
                mostrarSucesso(res.mensagem || 'API salva com sucesso!');
                bootstrap.Modal.getInstance('#modalApi').hide();
                carregarApis();
            } else {
                mostrarErro('Erro: ' + (res.erro || 'Erro desconhecido'));
            }
        },
        error: function(xhr) {
            btnSalvar.prop('disabled', false).html('Salvar');
            const msg = xhr.responseJSON?.erro || 'Erro ao comunicar com servidor';
            mostrarErro(msg);
        }
    });
}

// Editar API
function editarApi(id) {
    abrirModalApi(id);
}

// Excluir API
function excluirApi(id) {
    if (!confirm('Deseja realmente excluir esta API?')) return;
    
    $.post(baseUrl + '/api/apis-externas/delete/' + id, function(res) {
        if (res.sucesso) {
            alert('API excluída!');
            carregarApis();
        } else {
            alert('Erro: ' + (res.erro || 'Erro desconhecido'));
        }
    });
}

// Testar API
function testarApi(id) {
    const api = apis.find(a => a.id_api === id);
    if (!api) return;
    
    $('#modalTesteApi #testeApiNome').text(api.nome);
    $('#modalTesteApi #testeApiUrl').text(api.url);
    $('#modalTesteApi #testeApiMetodo').text(api.metodo);
    $('#testeResultado').html('<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Testando API...</p></div>');
    
    new bootstrap.Modal('#modalTesteApi').show();
    
    $.ajax({
        url: baseUrl + '/api/apis-externas/testar',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ id_api: id }),
        success: function(res) {
            if (res.sucesso) {
                const resposta = typeof res.resposta === 'object' ? JSON.stringify(res.resposta, null, 2) : res.resposta;
                $('#testeResultado').html(`
                    <div class="alert alert-success">
                        <strong><i class="bi bi-check-circle me-2"></i>Sucesso!</strong><br>
                        <small>Status: ${res.status_code || '200'} | Tempo: ${res.tempo_ms || 0}ms</small>
                    </div>
                    <div class="card">
                        <div class="card-header">Resposta</div>
                        <div class="card-body">
                            <pre style="max-height: 300px; overflow-y: auto;" class="mb-0">${resposta}</pre>
                        </div>
                    </div>
                `);
            } else {
                $('#testeResultado').html(`
                    <div class="alert alert-danger">
                        <strong><i class="bi bi-x-circle me-2"></i>Erro!</strong><br>
                        ${res.erro || 'Erro desconhecido'}
                    </div>
                `);
            }
        },
        error: function(xhr) {
            $('#testeResultado').html(`
                <div class="alert alert-danger">
                    <strong><i class="bi bi-exclamation-triangle me-2"></i>Erro na Requisição</strong><br>
                    Status: ${xhr.status} - ${xhr.statusText}
                </div>
            `);
        }
    });
}

// Mostrar campos auth
$('#apiTipoAuth').on('change', function() {
    if (this.value === 'none') {
        $('#authFields').hide();
    } else {
        $('#authFields').show();
    }
});

// Inicializar
$(document).ready(function() {
    carregarApis();
});
</script>
SCRIPTS;

include __DIR__ . '/layouts/base.php';
?>
