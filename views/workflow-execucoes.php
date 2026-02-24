<?php 
$pageTitle = 'Execuções de Workflows';
$currentPage = 'workflow-execucoes';

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

/* ==== ESTILOS ESPECÍFICOS ==== */
.execution-card {
    transition: all 0.2s ease;
    cursor: pointer;
}
.execution-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.status-badge {
    padding: 0.3rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
.status-running { background: #dbeafe; color: #2563eb; }
.status-success { background: #d1fae5; color: #059669; }
.status-error { background: #fee2e2; color: #dc2626; }
.status-pending { background: #fef3c7; color: #b45309; }

.node-exec-item {
    padding: 0.75rem;
    border-left: 4px solid #e5e7eb;
    margin-bottom: 0.5rem;
    background: #f9fafb;
    border-radius: 0 8px 8px 0;
}
.node-exec-item.success { border-left-color: #10b981; }
.node-exec-item.error { border-left-color: #ef4444; }
.node-exec-item.running { border-left-color: #3b82f6; }
.node-exec-item.skipped { border-left-color: #9ca3af; opacity: 0.6; }

.timeline-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #e5e7eb;
}
.timeline-dot.success { background: #10b981; }
.timeline-dot.error { background: #ef4444; }
.timeline-dot.running { background: #3b82f6; animation: pulse 1s infinite; }

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}
</style>

<!-- Header da Página -->
<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-clock-history"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Execuções de Workflows</h1>
        <p class="page-subtitle-modern">Histórico e detalhes de execuções</p>
    </div>
    <div class="ms-auto d-flex gap-2">
        <a href="<?= BASE_URL ?>/workflows" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Workflows
        </a>
        <button class="btn btn-outline-primary" onclick="carregarExecucoes()">
            <i class="bi bi-arrow-clockwise me-2"></i>Atualizar
        </button>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern primary-card">
            <div class="stat-icon-modern"><i class="bi bi-play-circle"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="totalExecucoes">0</div>
                <div class="stat-label-modern">Total Execuções</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern success-card">
            <div class="stat-icon-modern"><i class="bi bi-check-circle"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="execSucesso">0</div>
                <div class="stat-label-modern">Sucesso</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern danger-card">
            <div class="stat-icon-modern"><i class="bi bi-x-circle"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="execErro">0</div>
                <div class="stat-label-modern">Erro</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern info-card">
            <div class="stat-icon-modern"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="execRunning">0</div>
                <div class="stat-label-modern">Em Execução</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Lista de Execuções -->
    <div class="col-lg-5">
        <div class="card-modern">
            <div class="card-modern-header">
                <i class="bi bi-list-ul me-2"></i>
                <span>Execuções Recentes</span>
                <div class="ms-auto">
                    <select class="form-select form-select-sm" id="filtroWorkflow" style="width: 150px;">
                        <option value="">Todos Workflows</option>
                    </select>
                </div>
            </div>
            <div class="card-modern-body p-0">
                <div id="listaExecucoes" style="max-height: 600px; overflow-y: auto;">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Detalhes da Execução -->
    <div class="col-lg-7">
        <div class="card-modern">
            <div class="card-modern-header">
                <i class="bi bi-info-circle me-2"></i>
                <span>Detalhes da Execução</span>
                <span class="ms-auto badge bg-secondary" id="execIdBadge">-</span>
            </div>
            <div class="card-modern-body" id="detalhesExecucao">
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-arrow-left-circle fs-1"></i>
                    <p class="mt-3">Selecione uma execução para ver os detalhes</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Log Completo -->
<div class="modal fade" id="modalLog" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-file-text me-2"></i>Log Completo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <pre id="logCompleto" style="max-height: 500px; overflow: auto; background: #1e293b; color: #e2e8f0; padding: 1rem; border-radius: 8px; font-size: 0.8rem;"></pre>
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
let execucoes = [];
let execucaoSelecionada = null;
let autoRefreshInterval = null;

// Carregar execuções
function carregarExecucoes() {
    $.getJSON(baseUrl + '/api/workflows/execucoes/list', function(res) {
        if (res.sucesso) {
            execucoes = res.dados || [];
            renderizarListaExecucoes(execucoes);
            atualizarEstatisticas();
            
            // Auto-refresh se tiver execução rodando
            const temRunning = execucoes.some(e => e.status === 'running');
            if (temRunning && !autoRefreshInterval) {
                autoRefreshInterval = setInterval(carregarExecucoes, 3000);
            } else if (!temRunning && autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
            }
        }
    }).fail(function() {
        $('#listaExecucoes').html('<div class="text-center py-4 text-danger">Erro ao carregar execuções</div>');
    });
}

// Render lista de execuções
function renderizarListaExecucoes(lista) {
    const container = $('#listaExecucoes');
    
    if (!lista || lista.length === 0) {
        container.html('<div class="text-center py-5 text-muted">Nenhuma execução registrada</div>');
        $('#detalhesExecucao').html('<div class="text-center py-5 text-muted">Selecione uma execução</div>');
        return;
    }
    
    let html = '';
    lista.forEach(exec => {
        const statusBadge = {
            'running': 'status-running',
            'success': 'status-success',
            'error': 'status-error',
            'pending': 'status-pending'
        }[exec.status] || 'status-pending';
        
        const statusLabel = {
            'running': 'Em Execução',
            'success': 'Sucesso',
            'error': 'Erro',
            'pending': 'Pendente'
        }[exec.status] || exec.status;
        
        html += `
        <div class="execution-card p-3 mb-2 \${execucaoSelecionada === exec.id_execucao ? 'border-primary' : ''}" onclick="selecionarExecucao(\${exec.id_execucao})">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <strong>\${exec.workflow_nome || 'Workflow #' + exec.id_workflow}</strong>
                <span class="status-badge \${statusBadge}">\${statusLabel}</span>
            </div>
            <div class="text-muted small">
                <div><i class="bi bi-calendar me-1"></i>\${new Date(exec.iniciado_em).toLocaleString('pt-BR')}</div>
                <div><i class="bi bi-clock me-1"></i>Duração: \${exec.duracao_segundos || 0}s</div>
            </div>
        </div>
        `;
    });
    
    container.html(html);
    
    // Auto-selecionar primeira
    if (!execucaoSelecionada && lista.length > 0) {
        selecionarExecucao(lista[0].id_execucao);
    }
}

// Selecionar execução
function selecionarExecucao(id) {
    execucaoSelecionada = id;
    renderizarListaExecucoes(execucoes);
    
    $.getJSON(baseUrl + '/api/workflows/execucoes/get/' + id, function(res) {
        if (res.sucesso) {
            renderizarDetalhesExecucao(res.dados);
        }
    });
}

// Render detalhes da execução
function renderizarDetalhesExecucao(exec) {
    const container = $('#detalhesExecucao');
    
    let nodesHtml = '';
    if (exec.nodes && exec.nodes.length > 0) {
        exec.nodes.forEach(node => {
            const statusClass = {
                'success': 'success',
                'error': 'error',
                'running': 'running',
                'skipped': 'skipped'
            }[node.status] || '';
            
            const icon = {
                'success': 'check-circle-fill',
                'error': 'x-circle-fill',
                'running': 'arrow-repeat',
                'skipped': 'dash-circle'
            }[node.status] || 'circle';
            
            nodesHtml += `
            <div class="node-exec-item \${statusClass}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-bold"><i class="bi bi-\${icon} me-2"></i>\${node.node_tipo}: \${node.label || 'Node'}</div>
                        <small class="text-muted">\${node.iniciado_em ? new Date(node.iniciado_em).toLocaleTimeString('pt-BR') : ''}</small>
                    </div>
                    <small class="text-muted">\${node.duracao_ms || 0}ms</small>
                </div>
                \${node.erro ? '<div class="text-danger small mt-2">' + node.erro + '</div>' : ''}
                \${node.log ? '<pre class="small mt-2 mb-0" style="font-size: 0.75rem;">' + node.log + '</pre>' : ''}
            </div>
            `;
        });
    } else {
        nodesHtml = '<div class="text-muted text-center py-3">Nenhum node executado</div>';
    }
    
    const statusBadge = {
        'running': '<span class="badge bg-primary">Em Execução</span>',
        'success': '<span class="badge bg-success">Sucesso</span>',
        'error': '<span class="badge bg-danger">Erro</span>',
        'pending': '<span class="badge bg-warning">Pendente</span>'
    }[exec.status] || '<span class="badge bg-secondary">' + exec.status + '</span>';
    
    const html = `
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h5>\${exec.workflow_nome || 'Workflow #' + exec.id_workflow}</h5>
                <div class="text-muted small">Execução #\${exec.id_execucao}</div>
            </div>
            \${statusBadge}
        </div>
        <div class="row mt-3 g-2">
            <div class="col-6">
                <div class="text-muted small">Início:</div>
                <div>\${new Date(exec.iniciado_em).toLocaleString('pt-BR')}</div>
            </div>
            <div class="col-6">
                <div class="text-muted small">Duração:</div>
                <div>\${exec.duracao_segundos || 0}s</div>
            </div>
        </div>
    </div>
    
    <h6 class="mb-3">Timeline de Execução</h6>
    <div>\${nodesHtml}</div>
    `;
    
    container.html(html);
}

// Atualizar estatísticas
function atualizarEstatisticas() {
    const total = execucoes.length;
    const sucesso = execucoes.filter(e => e.status === 'success').length;
    const erro = execucoes.filter(e => e.status === 'error').length;
    const running = execucoes.filter(e => e.status === 'running').length;
    
    $('#totalExecucoes').text(total);
    $('#execSucesso').text(sucesso);
    $('#execErro').text(erro);
    $('#execRunning').text(running);
}

// Inicializar
$(document).ready(function() {
    carregarExecucoes();
});

// Limpar interval ao sair
$(window).on('beforeunload', function() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
});
</script>
SCRIPTS;

include __DIR__ . '/layouts/base.php';
?>
