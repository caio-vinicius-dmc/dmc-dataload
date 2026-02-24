<?php 
$pageTitle = 'Workflows';
$currentPage = 'workflows';

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
.workflow-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}
.workflow-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.workflow-card.active {
    border-color: #10b981;
}
.workflow-card.inactive {
    opacity: 0.7;
}
.trigger-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.5rem;
    border-radius: 12px;
}
.trigger-manual { background: #e0e7ff; color: #4338ca; }
.trigger-api_event { background: #ccfbf1; color: #0d9488; }
.trigger-cron { background: #fef3c7; color: #b45309; }
.trigger-rotina_finished { background: #fce7f3; color: #be185d; }
</style>

<!-- Header da Página -->
<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-diagram-3-fill"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Workflows</h1>
        <p class="page-subtitle-modern">Gerencie automações e fluxos de trabalho</p>
    </div>
    <div class="ms-auto d-flex gap-2">
        <a href="<?= BASE_URL ?>/workflow-execucoes" class="btn btn-outline-primary">
            <i class="bi bi-clock-history me-2"></i>Execuções
        </a>
        <a href="<?= BASE_URL ?>/workflow-builder" class="btn-modern-primary">
            <i class="bi bi-plus-lg me-2"></i>Novo Workflow
        </a>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern primary-card">
            <div class="stat-icon-modern"><i class="bi bi-diagram-3"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="totalWorkflows">0</div>
                <div class="stat-label-modern">Total Workflows</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern success-card">
            <div class="stat-icon-modern"><i class="bi bi-check-circle"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="workflowsAtivos">0</div>
                <div class="stat-label-modern">Ativos</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern info-card">
            <div class="stat-icon-modern"><i class="bi bi-play-circle"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="totalExecucoes">0</div>
                <div class="stat-label-modern">Execuções</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern danger-card">
            <div class="stat-icon-modern"><i class="bi bi-trophy"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="taxaSucesso">0%</div>
                <div class="stat-label-modern">Taxa Sucesso</div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Workflows -->
<div class="card-modern">
    <div class="card-modern-header">
        <i class="bi bi-list-ul me-2"></i>
        <span>Workflows</span>
        <div class="ms-auto d-flex gap-2">
            <input type="text" class="form-control form-control-sm" placeholder="Buscar..." id="searchWorkflows" style="width: 200px;">
            <button class="btn btn-sm btn-outline-secondary" onclick="carregarWorkflows()">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>
    <div class="card-modern-body">
        <div class="row g-3" id="listaWorkflows">
            <div class="col-12 text-center py-5">
                <div class="spinner-border text-primary"></div>
                <p class="mt-2 text-muted">Carregando workflows...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Executar Workflow -->
<div class="modal fade" id="modalExecutar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-play-fill me-2"></i>Executar Workflow</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Deseja executar o workflow <strong id="execWorkflowNome"></strong>?</p>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    A execução será iniciada imediatamente e você poderá acompanhar o progresso no histórico.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="confirmarExecucao()">
                    <i class="bi bi-play-fill me-1"></i>Executar
                </button>
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
let workflows = [];
let workflowParaExecutar = null;

// Carregar workflows
function carregarWorkflows() {
    $.getJSON(baseUrl + '/api/workflows/list', function(res) {
        if (res.sucesso) {
            workflows = res.dados || [];
            renderizarWorkflows(workflows);
            atualizarEstatisticas();
        }
    }).fail(function() {
        $('#listaWorkflows').html('<div class="col-12 text-center py-5 text-muted">Erro ao carregar workflows</div>');
    });
}

// Renderizar workflows
function renderizarWorkflows(lista) {
    const container = $('#listaWorkflows');
    
    if (!lista || lista.length === 0) {
        container.html('<div class="col-12 text-center py-5"><p class="text-muted">Nenhum workflow cadastrado</p><a href="' + baseUrl + '/workflow-builder" class="btn btn-primary">Criar Primeiro Workflow</a></div>');
        return;
    }
    
    let html = '';
    lista.forEach(wf => {
        const badgeClass = wf.ativo ? 'bg-success' : 'bg-secondary';
        const triggerBadge = {
            'manual': '🖐️ Manual',
            'api_event': '☁️ API Event',
            'cron': '⏰ CRON',
            'rotina_finished': '▶️ Rotina'
        }[wf.trigger_tipo] || wf.trigger_tipo;
        
        html += `
        <div class="col-md-6 col-xl-4">
            <div class="card workflow-card \${wf.ativo ? 'active' : 'inactive'} h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="mb-0">\${wf.nome}</h5>
                        <span class="badge \${badgeClass}">\${wf.ativo ? 'Ativo' : 'Inativo'}</span>
                    </div>
                    <div class="mb-3">
                        <span class="trigger-badge trigger-\${wf.trigger_tipo}">\${triggerBadge}</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted small mb-3">
                        <span><i class="bi bi-play-circle me-1"></i>\${wf.total_execucoes || 0} exec.</span>
                        <span><i class="bi bi-check-circle me-1"></i>\${wf.ultima_execucao_em ? new Date(wf.ultima_execucao_em).toLocaleDateString('pt-BR') : 'Nunca'}</span>
                    </div>
                    <div class="btn-group w-100 btn-group-sm">
                        <button class="btn btn-outline-success" onclick="executarWorkflow(\${wf.id_workflow})" title="Executar">
                            <i class="bi bi-play-fill"></i>
                        </button>
                        <a href="\${baseUrl}/workflow-builder?id=\${wf.id_workflow}" class="btn btn-outline-primary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button class="btn btn-outline-warning" onclick="toggleAtivo(\${wf.id_workflow}, \${!wf.ativo})" title="Ativar/Desativar">
                            <i class="bi bi-power"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="excluirWorkflow(\${wf.id_workflow})" title="Excluir">
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
    const total = workflows.length;
    const ativos = workflows.filter(w => w.ativo).length;
    
    $.getJSON(baseUrl + '/api/workflows/stats', function(res) {
        if (res.sucesso) {
            $('#totalWorkflows').text(total);
            $('#workflowsAtivos').text(ativos);
            $('#totalExecucoes').text(res.total_execucoes || 0);
            $('#taxaSucesso').text(res.taxa_sucesso || '0%');
        }
    });
}

// Executar workflow
function executarWorkflow(id) {
    workflowParaExecutar = id;
    new bootstrap.Modal('#modalExecutar').show();
}

function confirmarExecucao() {
    if (!workflowParaExecutar) return;
    
    $.post(baseUrl + '/api/workflows/execute/' + workflowParaExecutar, function(res) {
        if (res.sucesso) {
            alert('Workflow iniciado! ID Execução: ' + res.id_execucao);
            bootstrap.Modal.getInstance('#modalExecutar').hide();
            setTimeout(() => window.location.href = baseUrl + '/workflow-execucoes', 1000);
        } else {
            alert('Erro: ' + (res.erro || 'Erro desconhecido'));
        }
    });
}

// Toggle ativo
function toggleAtivo(id, ativo) {
    $.post(baseUrl + '/api/workflows/toggle/' + id, { ativo: ativo }, function(res) {
        if (res.sucesso) {
            carregarWorkflows();
        } else {
            alert('Erro: ' + (res.erro || 'Erro desconhecido'));
        }
    });
}

// Excluir workflow
function excluirWorkflow(id) {
    if (!confirm('Deseja realmente excluir este workflow?')) return;
    
    $.post(baseUrl + '/api/workflows/delete/' + id, function(res) {
        if (res.sucesso) {
            alert('Workflow excluído!');
            carregarWorkflows();
        } else {
            alert('Erro: ' + (res.erro || 'Erro desconhecido'));
        }
    });
}

// Busca
$('#searchWorkflows').on('input', function() {
    const termo = $(this).val().toLowerCase();
    const filtrados = workflows.filter(w => w.nome.toLowerCase().includes(termo));
    renderizarWorkflows(filtrados);
});

// Inicializar
$(document).ready(function() {
    carregarWorkflows();
});
</script>
SCRIPTS;

include __DIR__ . '/layouts/base.php';
?>
