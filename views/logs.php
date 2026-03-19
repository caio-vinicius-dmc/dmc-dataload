<?php
/**
 * DMC DataLoad - Logs do Sistema
 * UI Moderna
 */
$pageTitle = 'Logs do Sistema';
$currentPage = 'logs';

ob_start();
?>

<!-- Header Section -->
<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-journal-text"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Logs do Sistema</h1>
        <p class="page-subtitle-modern">Monitore eventos, erros e atividades do sistema</p>
    </div>
    <div class="d-flex gap-2 ms-auto">
        <button class="btn-modern-outline" onclick="carregarLogs()">
            <i class="bi bi-arrow-clockwise me-2"></i>Atualizar
        </button>
        <button class="btn-modern-primary" onclick="exportarLogs()">
            <i class="bi bi-download me-2"></i>Exportar
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="card-modern mb-4">
    <div class="card-modern-header">
        <i class="bi bi-funnel-fill me-2"></i>
        <span>Filtros Avançados</span>
    </div>
    <div class="card-modern-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label-modern">Nível</label>
                <select class="form-select-modern" id="filtroNivel">
                    <option value="">Todos</option>
                    <option value="debug">🔍 Debug</option>
                    <option value="info">ℹ️ Info</option>
                    <option value="warning">⚠️ Warning</option>
                    <option value="error">❌ Error</option>
                    <option value="critical">🔴 Critical</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-modern">Canal</label>
                <select class="form-select-modern" id="filtroCanal">
                    <option value="">Todos</option>
                    <option value="auth">🔐 Auth</option>
                    <option value="etl">📊 ETL</option>
                    <option value="scheduler">⏰ Scheduler</option>
                    <option value="conexao">🔌 Conexão</option>
                    <option value="sistema">⚙️ Sistema</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-modern">Limite</label>
                <select class="form-select-modern" id="filtroLimite">
                    <option value="50" selected>50 registros</option>
                    <option value="100">100 registros</option>
                    <option value="250">250 registros</option>
                    <option value="500">500 registros</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label-modern">Buscar</label>
                <input type="text" class="form-control-modern" id="filtroBusca" placeholder="Texto na mensagem...">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-modern-primary flex-fill" onclick="aplicarFiltros()">
                    <i class="bi bi-search me-2"></i>Buscar
                </button>
                <button class="btn btn-modern-outline" onclick="limparFiltros()" title="Limpar filtros">
                    <i class="bi bi-x-lg"></i>
                </button>
                <button class="btn btn-outline-danger" onclick="limparLogs()" title="Limpar logs antigos">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-2">
        <div class="stat-card-modern primary-card">
            <div class="stat-icon-modern">
                <i class="bi bi-list-ul"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="statTotal">0</div>
                <div class="stat-label-modern">Total de Logs</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card-modern info-card">
            <div class="stat-icon-modern">
                <i class="bi bi-info-circle-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="statInfo">0</div>
                <div class="stat-label-modern">Info</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card-modern" style="background: white; border-left: 4px solid #f59e0b;">
            <div class="stat-icon-modern" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="statWarning">0</div>
                <div class="stat-label-modern">Warnings</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card-modern danger-card">
            <div class="stat-icon-modern">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="statError">0</div>
                <div class="stat-label-modern">Errors</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card-modern" style="background: white; border-left: 4px solid #8b5cf6;">
            <div class="stat-icon-modern" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                <i class="bi bi-shield-exclamation"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="statCritical">0</div>
                <div class="stat-label-modern">Critical</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card-modern" style="background: white; border-left: 4px solid #6b7280;">
            <div class="stat-icon-modern" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;">
                <i class="bi bi-bug-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="statDebug">0</div>
                <div class="stat-label-modern">Debug</div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Logs -->
<div class="card-modern">
    <div class="card-modern-header">
        <i class="bi bi-table me-2"></i>
        <span>Registros de Logs</span>
        <div class="ms-auto d-flex align-items-center gap-3">
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" id="autoRefresh">
                <label class="form-check-label small" for="autoRefresh">Auto-atualizar (5s)</label>
            </div>
            <span class="badge-modern-info" id="totalRegistros">0 registros</span>
        </div>
    </div>
    <div class="card-modern-body p-0">
        <div class="table-responsive">
            <table class="table-modern" id="tblLogs">
                <thead>
                    <tr>
                        <th width="50"><i class="bi bi-hash me-1"></i>ID</th>
                        <th width="150"><i class="bi bi-calendar3 me-1"></i>Data/Hora</th>
                        <th width="90"><i class="bi bi-layers-fill me-1"></i>Nível</th>
                        <th width="120"><i class="bi bi-tag-fill me-1"></i>Canal</th>
                        <th><i class="bi bi-chat-left-text me-1"></i>Mensagem</th>
                        <th width="80"><i class="bi bi-three-dots me-1"></i>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                            <span class="ms-2">Carregando logs...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detalhes -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-modern">
            <div class="modal-header-modern">
                <h5 class="modal-title-modern">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Detalhes do Log
                </h5>
                <button type="button" class="btn-close-modern" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body-modern">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label-modern">ID</label>
                        <p id="detId" class="mb-0 fw-bold">-</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-modern">Nível</label>
                        <p id="detNivel" class="mb-0">-</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-modern">Canal</label>
                        <p id="detCanal" class="mb-0">-</p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-modern">Data/Hora</label>
                        <p id="detDataHora" class="mb-0">-</p>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label-modern">IP Address</label>
                        <p id="detIP" class="mb-0"><code>-</code></p>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label-modern">User Agent</label>
                        <p id="detUserAgent" class="mb-0 small text-muted">-</p>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label-modern">Mensagem</label>
                    <div class="alert alert-light mb-0">
                        <pre id="detMensagem" class="mb-0" style="white-space: pre-wrap;"></pre>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label-modern">Contexto / Dados Adicionais (JSON)</label>
                    <div style="background: #1e293b; padding: 1.5rem; border-radius: 12px;">
                        <pre id="detContexto" class="mb-0 text-white" style="white-space: pre-wrap; font-size: 0.85rem; font-family: 'Courier New', monospace;"></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer-modern">
                <button type="button" class="btn btn-modern-outline" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Fechar
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
    --gradient-info: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    --shadow-sm: 0 2px 8px rgba(0,0,0,0.06);
    --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
    --shadow-lg: 0 8px 32px rgba(0,0,0,0.12);
    --radius-md: 12px;
    --radius-lg: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ==== PAGE HEADER ==== */
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

/* ==== MODERN CARDS ==== */
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

/* ==== MODERN FORM ELEMENTS ==== */
.form-label-modern {
    font-size: 0.85rem;
    font-weight: 600;
    color: #4b5563;
    margin-bottom: 0.5rem;
    display: block;
}

.form-select-modern,
.form-control-modern {
    border: 2px solid #e5e7eb;
    border-radius: var(--radius-md);
    padding: 0.65rem 1rem;
    font-size: 0.95rem;
    transition: var(--transition);
}

.form-select-modern:focus,
.form-control-modern:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    outline: none;
}

/* ==== MODERN BUTTONS ==== */
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

.btn-modern-success {
    background: var(--gradient-success);
    border: none;
    color: white;
    padding: 0.65rem 1.5rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 0.95rem;
    transition: var(--transition);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-modern-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    color: white;
}

.btn-modern-outline {
    background: white;
    border: 2px solid #e5e7eb;
    color: #4b5563;
    padding: 0.6rem 1.4rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 0.95rem;
    transition: var(--transition);
}

.btn-modern-outline:hover {
    border-color: #667eea;
    color: #667eea;
    background: rgba(102, 126, 234, 0.05);
}

/* ==== STAT CARDS ==== */
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

/* ==== MODERN TABLE ==== */
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

/* ==== BADGES ==== */
.badge-modern-info {
    padding: 0.4rem 0.9rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.15) 100%);
    color: #3b82f6;
}

.badge-nivel {
    padding: 0.35rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
}

.badge-debug { background: rgba(107, 114, 128, 0.15); color: #4b5563; }
.badge-info { background: rgba(59, 130, 246, 0.15); color: #2563eb; }
.badge-warning { background: rgba(245, 158, 11, 0.15); color: #d97706; }
.badge-error { background: rgba(239, 68, 68, 0.15); color: #dc2626; }
.badge-critical { background: rgba(139, 92, 246, 0.15); color: #7c3aed; }

.badge-canal {
    padding: 0.25rem 0.6rem;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 600;
    background: rgba(0,0,0,0.05);
    color: #6b7280;
}

/* ==== MODERN MODAL ==== */
.modal-modern {
    border-radius: var(--radius-lg);
    border: none;
}

.modal-header-modern {
    padding: 1.5rem 2rem;
    border-bottom: 2px solid #f3f4f6;
    background: linear-gradient(135deg, #fafbff 0%, #f0f4ff 100%);
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
}

.modal-title-modern {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1a202c;
    display: flex;
    align-items: center;
    margin: 0;
}

.modal-title-modern i {
    color: #667eea;
}

.btn-close-modern {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
    padding: 0;
}

.btn-close-modern:hover {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    transform: rotate(90deg);
}

.modal-body-modern {
    padding: 2rem;
}

.modal-footer-modern {
    padding: 1.5rem 2rem;
    border-top: 2px solid #f3f4f6;
    background: #fafbfc;
    border-radius: 0 0 var(--radius-lg) var(--radius-lg);
}

/* ==== RESPONSIVE ==== */
@media (max-width: 991px) {
    .page-title-modern {
        font-size: 1.5rem;
    }
    
    .stat-card-modern {
        padding: 1.25rem;
    }
    
    .stat-icon-modern {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
    }
    
    .stat-value-modern {
        font-size: 1.5rem;
    }
}

@media (max-width: 767px) {
    .page-header-modern {
        padding: 1rem 0;
    }
    
    .page-title-modern {
        font-size: 1.25rem;
    }
    
    .page-subtitle-modern {
        font-size: 0.9rem;
    }
    
    .stat-card-modern {
        padding: 1rem;
    }
    
    .card-modern-body {
        padding: 1rem;
    }
    
    .table-modern thead th,
    .table-modern tbody td {
        padding: 0.75rem;
        font-size: 0.85rem;
    }
}
</style>
STYLES;

$extraScripts = <<<'SCRIPTS'

<script>
let autoRefreshInterval = null;

function nivelBadge(nivel) {
    const badges = {
        'DEBUG': '<span class="badge-nivel badge-debug">🔍 DEBUG</span>',
        'INFO': '<span class="badge-nivel badge-info">ℹ️ INFO</span>',
        'WARNING': '<span class="badge-nivel badge-warning">⚠️ WARNING</span>',
        'ERROR': '<span class="badge-nivel badge-error">❌ ERROR</span>',
        'CRITICAL': '<span class="badge-nivel badge-critical">🔴 CRITICAL</span>'
    };
    return badges[nivel?.toUpperCase()] || `<span class="badge-nivel">${nivel}</span>`;
}

function carregarLogs() {
    const params = new URLSearchParams();
    const nivel = $('#filtroNivel').val();
    const canal = $('#filtroCanal').val();
    const limite = $('#filtroLimite').val() || 100;
    
    if (nivel) params.append('nivel', nivel);
    if (canal) params.append('canal', canal);
    params.append('limite', limite);
    
    $.getJSON(baseUrl + '/api/logs?' + params.toString(), function(res) {
        const tbody = $('#tblLogs tbody');
        tbody.empty();
        
        if (!res.sucesso || !res.dados || res.dados.length === 0) {
            tbody.html(`<tr><td colspan="6" class="text-center py-5 text-muted">
                <i class="bi bi-journal-x fs-1 d-block mb-3"></i>
                <h5>Nenhum log encontrado</h5>
                <p class="mb-0">Tente ajustar os filtros ou aguarde novos eventos</p>
            </td></tr>`);
            $('#totalRegistros').text('0 registros');
            return;
        }
        
        $('#totalRegistros').text(`${res.dados.length} registros`);
        
        res.dados.forEach(log => {
            const dataHora = new Date(log.criado_em).toLocaleString('pt-BR');
            const mensagemCurta = (log.mensagem || '').substring(0, 80) + ((log.mensagem || '').length > 80 ? '...' : '');
            
            tbody.append(`<tr>
                <td><small class="text-muted">#${log.id}</small></td>
                <td><small>${dataHora}</small></td>
                <td>${nivelBadge(log.nivel)}</td>
                <td><span class="badge-canal">${log.canal || '-'}</span></td>
                <td><span title="${log.mensagem}">${mensagemCurta}</span></td>
                <td>
                    <button class="btn btn-sm btn-outline-info" onclick='verDetalhes(${JSON.stringify(log)})'>
                        <i class="bi bi-eye"></i>
                    </button>
                </td>
            </tr>`);
        });
        
        // Estatísticas simples (contagem local)
        atualizarStats(res.dados);
    }).fail(function() {
        $('#tblLogs tbody').html(`<tr><td colspan="6" class="text-center py-5 text-danger">
            <i class="bi bi-exclamation-triangle fs-1 d-block mb-3"></i>
            <h5>Erro ao carregar logs</h5>
        </td></tr>`);
    });
}

function atualizarStats(logs) {
    $('#statTotal').text(logs.length);
    $('#statDebug').text(logs.filter(l => l.nivel === 'DEBUG').length);
    $('#statInfo').text(logs.filter(l => l.nivel === 'INFO').length);
    $('#statWarning').text(logs.filter(l => l.nivel === 'WARNING').length);
    $('#statError').text(logs.filter(l => l.nivel === 'ERROR').length);
    $('#statCritical').text(logs.filter(l => l.nivel === 'CRITICAL').length);
}

function aplicarFiltros() {
    carregarLogs();
}

function limparFiltros() {
    $('#filtroNivel, #filtroCanal').val('');
    $('#filtroLimite').val('100');
    $('#filtroBusca').val('');
    carregarLogs();
}

function verDetalhes(log) {
    $('#detId').text(log.id || '-');
    $('#detNivel').html(nivelBadge(log.nivel));
    $('#detCanal').html(`<span class="badge-canal">${log.canal || '-'}</span>`);
    $('#detDataHora').text(new Date(log.criado_em).toLocaleString('pt-BR'));
    $('#detIP').text(log.ip_address || '-');
    $('#detUserAgent').text(log.user_agent || '-');
    $('#detMensagem').text(log.mensagem || '-');
    
    try {
        const ctx = typeof log.contexto === 'string' ? JSON.parse(log.contexto) : log.contexto;
        $('#detContexto').text(JSON.stringify(ctx, null, 2));
    } catch(e) {
        $('#detContexto').text(JSON.stringify(log.contexto, null, 2) || '{}');
    }
    
    new bootstrap.Modal('#modalDetalhes').show();
}

function exportarLogs() {
    const params = new URLSearchParams();
    const nivel = $('#filtroNivel').val();
    const canal = $('#filtroCanal').val();
    
    if (nivel) params.append('nivel', nivel);
    if (canal) params.append('canal', canal);
    params.append('formato', 'csv');
    
    window.location.href = baseUrl + '/api/logs/exportar?' + params.toString();
    
    Swal.fire({
        icon: 'info',
        title: 'Exportando...',
        text: 'Download iniciado!',
        timer: 2000,
        showConfirmButton: false
    });
}

function limparLogs() {
    Swal.fire({
        title: 'Limpar logs antigos?',
        html: `
            <p class="mb-3">Esta ação irá remover os logs mais antigos que:</p>
            <select class="form-select" id="swalPeriodo">
                <option value="7">7 dias</option>
                <option value="30" selected>30 dias</option>
                <option value="60">60 dias</option>
                <option value="90">90 dias</option>
            </select>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, limpar!',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ef4444',
        preConfirm: () => document.getElementById('swalPeriodo').value
    }).then(result => {
        if (result.isConfirmed) {
            $.post(baseUrl + '/api/logs/limpar', { dias: result.value }, function(res) {
                if (res.sucesso) {
                    Swal.fire('Sucesso!', `${res.removidos || 0} logs removidos`, 'success');
                    carregarLogs();
                } else {
                    Swal.fire('Erro!', res.erro || 'Erro ao limpar logs', 'error');
                }
            }, 'json').fail(() => {
                Swal.fire('Erro!', 'Falha na comunicação com servidor', 'error');
            });
        }
    });
}

// Auto-refresh
$('#autoRefresh').change(function() {
    if ($(this).is(':checked')) {
        autoRefreshInterval = setInterval(carregarLogs, 5000);
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Auto-atualização ativada!',
            showConfirmButton: false,
            timer: 2000
        });
    } else {
        clearInterval(autoRefreshInterval);
    }
});

$(document).ready(function() {
    carregarLogs();
});
</script>
SCRIPTS;

include __DIR__ . '/layouts/base.php';
?>
