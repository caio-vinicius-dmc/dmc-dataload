<?php 
$pageTitle = 'APIs Externas';
$currentPage = 'apis-externas';

// Gerar CSRF token para proteção dos formulários
$csrfToken = App\Core\AuthMiddleware::gerarTokenCSRF();

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

/* ========== API CARDS (estilo pipeline) ========== */
.api-card {
    background: white; border-radius: var(--radius-lg);
    border: 1px solid rgba(0,0,0,0.06);
    transition: var(--transition); overflow: hidden;
}
.api-card:hover {
    transform: translateY(-4px); box-shadow: var(--shadow-lg);
    border-color: rgba(102,126,234,0.2);
}
.api-card-accent {
    height: 4px; width: 100%; transition: height 0.3s;
}
.api-card:hover .api-card-accent { height: 6px; }
.method-get .api-card-accent { background: var(--gradient-info); }
.method-post .api-card-accent { background: var(--gradient-success); }
.method-put .api-card-accent { background: linear-gradient(135deg, #f59e0b, #d97706); }
.method-delete .api-card-accent { background: var(--gradient-danger); }
.method-patch .api-card-accent { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

.api-card-body { padding: 1.25rem 1.5rem; cursor: pointer; }
.api-card-header { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; }
.api-card-icon {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; color: white; flex-shrink: 0;
}
.method-get .api-card-icon { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.method-post .api-card-icon { background: linear-gradient(135deg, #10b981, #059669); }
.method-put .api-card-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
.method-delete .api-card-icon { background: linear-gradient(135deg, #ef4444, #dc2626); }
.method-patch .api-card-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

.api-card-title {
    font-size: 1.1rem; font-weight: 700; color: #1a202c; margin-bottom: 2px;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 200px;
}
.api-card-url {
    font-size: 0.78rem; color: #94a3b8; font-family: 'Fira Code', monospace;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    margin-bottom: 4px;
}
.api-card-desc {
    font-size: 0.85rem; color: #6b7280;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    overflow: hidden; min-height: 2.5em;
}
.api-card-stats {
    display: flex; gap: 16px; padding-top: 12px;
    border-top: 1px solid #f3f4f6; margin-top: 12px;
}
.api-stat {
    font-size: 0.8rem; color: #6b7280;
    display: flex; align-items: center; gap: 4px;
}
.api-stat strong { color: #1a202c; }
.api-card-footer {
    padding: 0.75rem 1.5rem; background: #f9fafb;
    border-top: 1px solid #f3f4f6;
    display: flex; justify-content: space-between; align-items: center;
}
.api-actions { display: flex; gap: 4px; }
.api-actions .btn {
    width: 32px; height: 32px; padding: 0;
    display: flex; align-items: center; justify-content: center;
    border-radius: 8px; font-size: 0.85rem;
}

.method-badge {
    font-size: 0.7rem; padding: 3px 10px; border-radius: 20px;
    font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
}
.method-GET { background: #dbeafe; color: #1d4ed8; }
.method-POST { background: #dcfce7; color: #15803d; }
.method-PUT { background: #fef3c7; color: #b45309; }
.method-DELETE { background: #fee2e2; color: #dc2626; }
.method-PATCH { background: #ede9fe; color: #7c3aed; }

.auth-badge {
    font-size: 0.7rem; padding: 2px 8px; border-radius: 12px; font-weight: 500;
}
.auth-none { background: #f3f4f6; color: #6b7280; }
.auth-bearer { background: #e0e7ff; color: #4338ca; }
.auth-basic { background: #fce7f3; color: #be185d; }
.auth-api_key { background: #ccfbf1; color: #0d9488; }

.status-dot {
    width: 8px; height: 8px; border-radius: 50%; display: inline-block;
}
.status-dot.active { background: #10b981; box-shadow: 0 0 6px rgba(16,185,129,0.5); animation: pulse-dot 2s infinite; }
.status-dot.inactive { background: #d1d5db; }
@keyframes pulse-dot {
    0%,100% { box-shadow: 0 0 4px rgba(16,185,129,0.4); }
    50% { box-shadow: 0 0 12px rgba(16,185,129,0.6); }
}

.empty-state { text-align: center; padding: 80px 20px; }
.empty-state-icon {
    width: 100px; height: 100px; border-radius: 24px;
    background: linear-gradient(135deg, #ede9fe, #e0e7ff);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 2.5rem; color: #667eea; margin-bottom: 24px;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.col-12.col-md-6.col-xl-4 { animation: fadeInUp 0.4s ease both; }
.col-12.col-md-6.col-xl-4:nth-child(2) { animation-delay: 0.05s; }
.col-12.col-md-6.col-xl-4:nth-child(3) { animation-delay: 0.1s; }
.col-12.col-md-6.col-xl-4:nth-child(4) { animation-delay: 0.15s; }
.col-12.col-md-6.col-xl-4:nth-child(5) { animation-delay: 0.2s; }
.col-12.col-md-6.col-xl-4:nth-child(6) { animation-delay: 0.25s; }

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

<!-- Filtros -->
<div class="card-modern mb-4">
    <div class="card-modern-body py-3">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0" id="searchApis" placeholder="Buscar APIs...">
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="filterMetodo">
                    <option value="">Todos os Métodos</option>
                    <option value="GET">GET</option>
                    <option value="POST">POST</option>
                    <option value="PUT">PUT</option>
                    <option value="DELETE">DELETE</option>
                    <option value="PATCH">PATCH</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="filterStatus">
                    <option value="">Todos</option>
                    <option value="1">Ativas</option>
                    <option value="0">Inativas</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="filterAuth">
                    <option value="">Todas as Auth</option>
                    <option value="none">Nenhuma</option>
                    <option value="bearer">Bearer</option>
                    <option value="basic">Basic</option>
                    <option value="api_key">API Key</option>
                </select>
            </div>
            <div class="col-md-2 text-end">
                <button class="btn btn-outline-secondary btn-sm" onclick="carregarApis()" title="Atualizar">
                    <i class="bi bi-arrow-clockwise me-1"></i> Atualizar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- API Cards Grid -->
<div class="row g-4" id="listaApis">
    <div class="col-12 text-center py-5">
        <div class="spinner-border text-primary"></div>
        <p class="mt-2 text-muted">Carregando APIs...</p>
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
                    
                    <?php include __DIR__ . '/partials/recurso_empresa_projeto.php'; ?>
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

<?php include __DIR__ . '/partials/compartilhamento_modal.php'; ?>

<?php
$content = ob_get_clean();

$extraStyles = <<<STYLES
STYLES;

$extraScripts = '<script>const csrfToken = \'' . htmlspecialchars($csrfToken, ENT_QUOTES) . '\';</script>' . <<<'SCRIPTS'
<script>
let apis = [];

// Funções auxiliares de UI
function mostrarErro(mensagem) {
    Swal.fire({icon: 'error', title: 'Erro', text: mensagem, toast: true, position: 'top-end', timer: 4000, showConfirmButton: false});
}

function mostrarSucesso(mensagem) {
    Swal.fire({icon: 'success', title: 'Sucesso', text: mensagem, toast: true, position: 'top-end', timer: 3000, showConfirmButton: false});
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
        container.html('<div class="col-12"><div class="empty-state">' +
            '<div class="empty-state-icon"><i class="bi bi-cloud-arrow-down-fill"></i></div>' +
            '<h4>Nenhuma API cadastrada</h4>' +
            '<p>Cadastre sua primeira API externa e comece a integrar seus dados</p>' +
            '<button class="btn-modern-primary" onclick="abrirModalApi()"><i class="bi bi-plus-lg me-2"></i>Cadastrar Primeira API</button></div></div>');
        return;
    }
    
    let html = '';
    lista.forEach(api => {
        const isA = !!api.ativo;
        const m = (api.metodo || 'GET').toUpperCase();
        const mLow = m.toLowerCase();
        const mIcon = {GET:'bi-cloud-download',POST:'bi-cloud-upload',PUT:'bi-pencil-square',DELETE:'bi-trash3',PATCH:'bi-wrench'}[m]||'bi-cloud';
        const authLabel = {none:'Sem Auth',bearer:'Bearer',basic:'Basic',api_key:'API Key'}[api.auth_tipo||'none']||'Sem Auth';
        const authClass = 'auth-'+(api.auth_tipo||'none');
        const eventos = parseInt(api.total_eventos)||0;
        const polling = parseInt(api.intervalo_polling)||60;
        const safeNome = $('<div>').text(api.nome).html();
        const safeUrl = $('<div>').text(api.url||'').html();
        const safeDesc = $('<div>').text(api.descricao||'Sem descrição').html();
        const descTrunc = safeDesc.length>80 ? safeDesc.substring(0,80)+'...' : safeDesc;
        
        html += '<div class="col-12 col-md-6 col-xl-4" data-nome="'+safeNome.toLowerCase()+'" data-metodo="'+m+'" data-ativo="'+(isA?'1':'0')+'" data-auth="'+(api.auth_tipo||'none')+'">' +
            '<div class="api-card method-'+mLow+'">' +
            '<div class="api-card-accent"></div>' +
            '<div class="api-card-body" onclick="editarApi('+api.id+')">' +
            '<div class="api-card-header">' +
            '<div class="api-card-icon"><i class="bi '+mIcon+'"></i></div>' +
            '<div class="flex-grow-1" style="min-width:0">' +
            '<div class="d-flex align-items-center gap-2 mb-1">' +
            '<span class="api-card-title" title="'+safeNome+'">'+safeNome+'</span>' +
            '<span class="status-dot '+(isA?'active':'inactive')+'" title="'+(isA?'Ativa':'Inativa')+'"></span></div>' +
            '<div class="api-card-url" title="'+safeUrl+'">'+safeUrl+'</div></div>' +
            '<span class="method-badge method-'+m+'">'+m+'</span></div>' +
            '<div class="api-card-desc">'+descTrunc+'</div>' +
            '<div class="d-flex gap-2 align-items-center mb-0 mt-2">' +
            '<span class="auth-badge '+authClass+'"><i class="bi bi-shield-lock me-1"></i>'+authLabel+'</span>' +
            (eventos>0?'<span class="auth-badge" style="background:#dbeafe;color:#1d4ed8"><i class="bi bi-bell me-1"></i>'+eventos+' evento(s)</span>':'')+
            '</div>' +
            '<div class="api-card-stats">' +
            '<div class="api-stat"><i class="bi bi-arrow-repeat"></i> <strong>'+polling+'s</strong> polling</div>' +
            '<div class="api-stat"><i class="bi bi-bell"></i> <strong>'+eventos+'</strong> eventos</div>' +
            '<div class="api-stat"><i class="bi bi-'+(isA?'check-circle text-success':'x-circle text-danger')+'"></i> '+(isA?'Ativa':'Inativa')+'</div></div></div>' +
            '<div class="api-card-footer">' +
            '<div class="api-actions">' +
            '<button class="btn btn-outline-success btn-sm" title="Testar" onclick="event.stopPropagation();testarApiExistente('+api.id+')"><i class="bi bi-play-fill"></i></button>' +
            '<button class="btn btn-outline-primary btn-sm" title="Editar" onclick="event.stopPropagation();editarApi('+api.id+')"><i class="bi bi-pencil"></i></button>' +
            '<a href="'+baseUrl+'/eventos-api?api='+api.id+'" class="btn btn-outline-info btn-sm" title="Eventos" onclick="event.stopPropagation()"><i class="bi bi-bell"></i></a>' +
            '<button class="btn btn-outline-secondary btn-sm" title="Compartilhar" onclick="event.stopPropagation();abrirModalCompartilhamento(\'api\','+api.id+')"><i class="bi bi-share"></i></button></div>' +
            '<div class="d-flex gap-1">' +
            '<button class="btn btn-outline-danger btn-sm" title="Excluir" onclick="event.stopPropagation();excluirApi('+api.id+')"><i class="bi bi-trash"></i></button>' +
            '</div></div></div></div>';
    });
    
    container.html(html);
}

// Atualizar estatísticas
function atualizarEstatisticas() {
    const total = apis.length;
    const ativas = apis.filter(a => a.ativo).length;
    const comErro = apis.filter(a => a.ultimo_status === 'error').length;
    const totalEventos = apis.reduce((sum, a) => sum + (parseInt(a.total_eventos) || 0), 0);
    
    $('#totalApis').text(total);
    $('#apisAtivas').text(ativas);
    $('#totalEventos').text(totalEventos);
    $('#apisComErro').text(comErro);
}

// Toggle auth fields
function toggleAuthFields() {
    const tipo = $('#apiAuthTipo').val();
    const container = $('#authFieldsContainer');
    
    if (tipo === 'none') {
        container.html('<p class="text-muted mb-0 mt-2">Sem autenticação</p>');
        return;
    }
    
    let html = '';
    if (tipo === 'bearer') {
        html = `<label class="form-label">Bearer Token</label>
                <input type="text" class="form-control" name="bearer_token" id="apiBearerToken" placeholder="Token de autenticação">`;
    } else if (tipo === 'basic') {
        html = `<div class="row g-2">
                    <div class="col-6">
                        <label class="form-label">Usuário</label>
                        <input type="text" class="form-control" name="basic_username" id="apiBasicUsername">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Senha</label>
                        <input type="password" class="form-control" name="basic_password" id="apiBasicPassword">
                    </div>
                </div>`;
    } else if (tipo === 'api_key') {
        html = `<div class="row g-2">
                    <div class="col-6">
                        <label class="form-label">API Key</label>
                        <input type="text" class="form-control" name="api_key" id="apiKeyValue">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Header Name</label>
                        <input type="text" class="form-control" name="api_key_header" id="apiKeyHeader" value="X-API-Key">
                    </div>
                </div>`;
    }
    container.html(html);
}

// Adicionar header
function adicionarHeader() {
    const container = $('#headersContainer');
    const idx = container.find('.header-row').length;
    container.append(`
        <div class="header-row">
            <input type="text" class="form-control form-control-sm" name="header_keys[]" placeholder="Header Key" value="">
            <input type="text" class="form-control form-control-sm" name="header_values[]" placeholder="Header Value" value="">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="$(this).closest('.header-row').remove()">
                <i class="bi bi-x"></i>
            </button>
        </div>
    `);
}

// Toggle body card
function toggleBodyCard() {
    const metodo = $('#apiMetodo').val();
    if (['POST', 'PUT', 'PATCH'].includes(metodo)) {
        $('#bodyCard').show();
    } else {
        $('#bodyCard').hide();
    }
}

// Abrir modal API
function abrirModalApi(id) {
    $('#formApi')[0].reset();
    $('#apiId').val('');
    $('#apiAtivo').prop('checked', true);
    $('#headersContainer').empty();
    toggleAuthFields();
    toggleBodyCard();
    rbacLimparSelects();
    
    if (id) {
        $('#modalApiTitulo').text('Editar API');
        // Buscar dados completos da API
        $.ajax({
            url: baseUrl + '/api/apis-externas/get/' + id,
            dataType: 'json',
            cache: false,
            success: function(res) {
            if (res.sucesso && res.dados) {
                const api = res.dados;
                $('#apiId').val(api.id);
                $('#apiNome').val(api.nome);
                $('#apiDescricao').val(api.descricao);
                $('#apiUrl').val(api.url);
                $('#apiMetodo').val(api.metodo);
                $('#apiTipoResposta').val(api.tipo_resposta);
                $('#apiAuthTipo').val(api.auth_tipo || 'none');
                $('#apiIntervalo').val(api.intervalo_polling);
                $('#apiTimeout').val(api.timeout);
                $('#apiAtivo').prop('checked', api.ativo);
                $('#apiBodyTemplate').val(api.body_template);
                
                toggleAuthFields();
                toggleBodyCard();
                
                // Preencher credenciais
                if (api.auth_tipo === 'bearer' && api.credenciais) {
                    $('#apiBearerToken').val(api.credenciais.token || '');
                } else if (api.auth_tipo === 'basic' && api.credenciais) {
                    $('#apiBasicUsername').val(api.credenciais.username || '');
                    $('#apiBasicPassword').val(api.credenciais.password || '');
                } else if (api.auth_tipo === 'api_key' && api.credenciais) {
                    $('#apiKeyValue').val(api.credenciais.api_key || '');
                    $('#apiKeyHeader').val(api.credenciais.api_key_header || 'X-API-Key');
                }
                
                // Preencher headers
                if (api.headers && typeof api.headers === 'object') {
                    Object.entries(api.headers).forEach(([key, value]) => {
                        $('#headersContainer').append(`
                            <div class="header-row">
                                <input type="text" class="form-control form-control-sm" name="header_keys[]" value="${key}">
                                <input type="text" class="form-control form-control-sm" name="header_values[]" value="${value}">
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="$(this).closest('.header-row').remove()">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        `);
                    });
                }
                
                // Preencher empresas/projetos RBAC
                var empIds = (res.empresas || []).map(function(e) { return parseInt(e.id_empresa || e.id || e, 10); });
                var projIds = (res.projetos || []).map(function(p) { return parseInt(p.id_projeto || p.id || p, 10); });
                window._rbacCarregado = false;
                rbacCarregarOpcoes(function() { rbacPreencherSelects(empIds, projIds); });
                
                new bootstrap.Modal('#modalApi').show();
            } else {
                mostrarErro('Erro ao carregar dados da API');
            }
        }});
    } else {
        $('#modalApiTitulo').text('Nova API');
        new bootstrap.Modal('#modalApi').show();
    }
}

// Salvar API - coleta dados do formulário nativo
function salvarApi(e) {
    if (e) e.preventDefault();
    
    const nome = $('#apiNome').val().trim();
    const url = $('#apiUrl').val().trim();
    
    if (!nome || nome.length < 3) {
        mostrarErro('O nome deve ter pelo menos 3 caracteres');
        $('#apiNome').focus();
        return false;
    }
    
    if (!url || !/^https?:\/\/.+/i.test(url)) {
        mostrarErro('URL inválida. Deve começar com http:// ou https://');
        $('#apiUrl').focus();
        return false;
    }
    
    // Coletar headers do formulário
    const headerKeys = [];
    const headerValues = [];
    $('input[name="header_keys[]"]').each(function() { headerKeys.push($(this).val()); });
    $('input[name="header_values[]"]').each(function() { headerValues.push($(this).val()); });
    
    const data = {
        id: $('#apiId').val() || null,
        nome: nome,
        descricao: $('#apiDescricao').val().trim(),
        url: url,
        metodo: $('#apiMetodo').val(),
        tipo_resposta: $('#apiTipoResposta').val(),
        auth_tipo: $('#apiAuthTipo').val(),
        header_keys: headerKeys,
        header_values: headerValues,
        body_template: $('#apiBodyTemplate').val(),
        intervalo_polling: parseInt($('#apiIntervalo').val()) || 60,
        timeout: parseInt($('#apiTimeout').val()) || 30,
        ativo: $('#apiAtivo').is(':checked') ? '1' : '0'
    };
    
    // Adicionar credenciais conforme tipo
    const authTipo = data.auth_tipo;
    if (authTipo === 'bearer') {
        data.bearer_token = $('#apiBearerToken').val();
    } else if (authTipo === 'basic') {
        data.basic_username = $('#apiBasicUsername').val();
        data.basic_password = $('#apiBasicPassword').val();
    } else if (authTipo === 'api_key') {
        data.api_key = $('#apiKeyValue').val();
        data.api_key_header = $('#apiKeyHeader').val();
    }
    
    // Empresas/Projetos RBAC
    if (typeof rbacGetSelectedIds === 'function') {
        data.empresas = rbacGetSelectedIds('empresas');
        data.projetos = rbacGetSelectedIds('projetos');
    }
    
    const btnSalvar = $('#formApi button[type="submit"]');
    btnSalvar.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Salvando...');
    
    $.ajax({
        url: baseUrl + '/api/apis-externas/salvar',
        method: 'POST',
        contentType: 'application/json',
        headers: {'X-CSRF-TOKEN': csrfToken},
        data: JSON.stringify(data),
        success: function(res) {
            btnSalvar.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i>Salvar');
            if (res.sucesso) {
                mostrarSucesso(res.mensagem || 'API salva com sucesso!');
                bootstrap.Modal.getInstance('#modalApi').hide();
                carregarApis();
            } else {
                mostrarErro(res.erro || 'Erro ao salvar');
            }
        },
        error: function(xhr) {
            btnSalvar.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i>Salvar');
            mostrarErro(xhr.responseJSON?.erro || 'Erro ao comunicar com servidor');
        }
    });
    
    return false;
}

// Editar API
function editarApi(id) {
    abrirModalApi(id);
}

// Excluir API
function excluirApi(id) {
    const api = apis.find(a => a.id === id);
    Swal.fire({
        title: 'Excluir API?',
        text: 'Deseja realmente excluir "' + (api ? api.nome : 'esta API') + '"? Esta ação não pode ser desfeita.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Sim, excluir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(baseUrl + '/api/apis-externas/delete/' + id, {_csrf_token: csrfToken}, function(res) {
                if (res.sucesso) {
                    mostrarSucesso('API excluída com sucesso!');
                    carregarApis();
                } else {
                    mostrarErro(res.erro || 'Erro ao excluir');
                }
            }).fail(function() {
                mostrarErro('Erro ao comunicar com o servidor');
            });
        }
    });
}

// Testar API existente (por ID)
function testarApiExistente(id) {
    const api = apis.find(a => a.id === id);
    if (!api) return;
    
    $('#testeApiNome').text(api.nome);
    $('#testeApiUrl').text(api.url);
    $('#testeApiMetodo').text(api.metodo);
    $('#testeResultado').html('<div class="text-center py-3"><div class="spinner-border text-primary"></div><p class="mt-2">Testando API...</p></div>');
    
    new bootstrap.Modal('#modalTesteApi').show();
    
    // Buscar dados completos para testar
    $.getJSON(baseUrl + '/api/apis-externas/get/' + id, function(getRes) {
        if (!getRes.sucesso) {
            $('#testeResultado').html('<div class="alert alert-danger">Erro ao buscar dados da API</div>');
            return;
        }
        const apiData = getRes.dados;
        
        $.ajax({
            url: baseUrl + '/api/apis-externas/testar',
            method: 'POST',
            contentType: 'application/json',
            headers: {'X-CSRF-TOKEN': csrfToken},
            data: JSON.stringify({
                url: apiData.url,
                metodo: apiData.metodo,
                auth_tipo: apiData.auth_tipo,
                tipo_resposta: apiData.tipo_resposta,
                timeout: apiData.timeout,
                header_keys: apiData.headers ? Object.keys(apiData.headers) : [],
                header_values: apiData.headers ? Object.values(apiData.headers) : [],
                bearer_token: apiData.credenciais?.token,
                basic_username: apiData.credenciais?.username,
                basic_password: apiData.credenciais?.password,
                api_key: apiData.credenciais?.api_key,
                api_key_header: apiData.credenciais?.api_key_header
            }),
            success: function(res) {
                const isOk = res.sucesso;
                const resposta = typeof res.response === 'object' ? JSON.stringify(res.response, null, 2) : (res.response_raw || res.response || '');
                $('#testeResultado').html(`
                    <div class="alert alert-${isOk ? 'success' : 'danger'}">
                        <strong><i class="bi bi-${isOk ? 'check-circle' : 'x-circle'} me-2"></i>${isOk ? 'Sucesso!' : 'Erro'}</strong><br>
                        <small>HTTP ${res.http_code || '?'} | Tempo: ${res.tempo_ms || 0}ms</small>
                    </div>
                    <div class="response-preview"><pre class="mb-0" style="color: #e2e8f0;">${$('<div>').text(resposta).html()}</pre></div>
                `);
            },
            error: function(xhr) {
                $('#testeResultado').html('<div class="alert alert-danger">Erro na requisição: ' + xhr.status + ' ' + xhr.statusText + '</div>');
            }
        });
    });
}

// Testar API a partir do modal de formulário (sem salvar)
function testarApi() {
    const url = $('#apiUrl').val().trim();
    if (!url) {
        mostrarErro('Preencha a URL antes de testar');
        return;
    }
    
    const headerKeys = [];
    const headerValues = [];
    $('input[name="header_keys[]"]').each(function() { headerKeys.push($(this).val()); });
    $('input[name="header_values[]"]').each(function() { headerValues.push($(this).val()); });
    
    const data = {
        url: url,
        metodo: $('#apiMetodo').val(),
        auth_tipo: $('#apiAuthTipo').val(),
        tipo_resposta: $('#apiTipoResposta').val(),
        timeout: parseInt($('#apiTimeout').val()) || 30,
        header_keys: headerKeys,
        header_values: headerValues,
        body_template: $('#apiBodyTemplate').val(),
        bearer_token: $('#apiBearerToken').val(),
        basic_username: $('#apiBasicUsername').val(),
        basic_password: $('#apiBasicPassword').val(),
        api_key: $('#apiKeyValue').val(),
        api_key_header: $('#apiKeyHeader').val()
    };
    
    Swal.fire({title: 'Testando...', text: 'Conectando à API', allowOutsideClick: false, didOpen: () => Swal.showLoading()});
    
    $.ajax({
        url: baseUrl + '/api/apis-externas/testar',
        method: 'POST',
        contentType: 'application/json',
        headers: {'X-CSRF-TOKEN': csrfToken},
        data: JSON.stringify(data),
        success: function(res) {
            if (res.sucesso) {
                const resposta = typeof res.response === 'object' ? JSON.stringify(res.response, null, 2) : (res.response_raw || '');
                Swal.fire({
                    icon: 'success',
                    title: 'Conexão OK!',
                    html: '<small>HTTP ' + (res.http_code || '200') + ' | Tempo: ' + (res.tempo_ms || 0) + 'ms</small><pre class="text-start mt-2" style="max-height:200px;overflow:auto;font-size:0.8rem;background:#f8f9fa;padding:0.5rem;border-radius:4px;">' + $('<div>').text(resposta).html() + '</pre>'
                });
            } else {
                Swal.fire({icon: 'error', title: 'Falha', text: res.erro || 'HTTP ' + res.http_code});
            }
        },
        error: function(xhr) {
            Swal.fire({icon: 'error', title: 'Erro', text: 'Erro ao comunicar: ' + xhr.status});
        }
    });
}

// Filtrar APIs
function filterApis() {
    const s = $('#searchApis').val().toLowerCase();
    const m = $('#filterMetodo').val();
    const st = $('#filterStatus').val();
    const au = $('#filterAuth').val();
    const filtered = apis.filter(a => {
        if(s && !a.nome.toLowerCase().includes(s) && !a.url.toLowerCase().includes(s) && !(a.descricao||'').toLowerCase().includes(s)) return false;
        if(m && (a.metodo||'GET')!==m) return false;
        if(st==='1' && !a.ativo) return false;
        if(st==='0' && a.ativo) return false;
        if(au && (a.auth_tipo||'none')!==au) return false;
        return true;
    });
    renderizarApis(filtered);
}

$('#searchApis').on('input', filterApis);
$('#filterMetodo').on('change', filterApis);
$('#filterStatus').on('change', filterApis);
$('#filterAuth').on('change', filterApis);

// Inicializar
$(document).ready(function() {
    carregarApis();
    
    // Form submit handler
    $('#formApi').on('submit', function(e) {
        return salvarApi(e);
    });
    
    // Toggle body card on method change
    $('#apiMetodo').on('change', toggleBodyCard);
});
</script>
SCRIPTS;

$extraScripts .= '<script src="' . (defined('BASE_URL') ? BASE_URL : '') . '/assets/js/rbac-recurso.js"></script>';
$extraScripts .= '<script src="' . (defined('BASE_URL') ? BASE_URL : '') . '/assets/js/rbac-compartilhamento.js"></script>';

include __DIR__ . '/layouts/base.php';
?>
