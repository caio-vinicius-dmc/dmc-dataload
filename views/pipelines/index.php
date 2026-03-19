<?php 
$pageTitle = 'Pipelines';
$currentPage = 'pipelines';
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
    --gradient-warning: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    --shadow-sm: 0 2px 8px rgba(0,0,0,0.06);
    --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
    --shadow-lg: 0 8px 32px rgba(0,0,0,0.12);
    --radius-md: 12px;
    --radius-lg: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.page-header-modern {
    background: white; padding: 1.75rem 2rem; border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;
    display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;
}
.page-icon-modern {
    width: 70px; height: 70px; border-radius: var(--radius-lg);
    background: var(--gradient-primary);
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; color: white;
    box-shadow: 0 4px 20px rgba(102,126,234,0.3); flex-shrink: 0;
}
.page-title-modern {
    font-size: 2rem; font-weight: 700; margin: 0 0 0.25rem 0;
    background: var(--gradient-primary);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.page-subtitle-modern { color: #64748b; margin: 0; font-size: 1rem; }

.stat-card-modern {
    background: white; border-radius: var(--radius-lg); padding: 1.5rem;
    box-shadow: var(--shadow-sm); border: 1px solid rgba(0,0,0,0.04);
    transition: var(--transition); position: relative; overflow: hidden;
    display: flex; align-items: center; gap: 1rem;
}
.stat-card-modern:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
.stat-card-modern::before {
    content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%;
    transition: width 0.3s ease;
}
.stat-card-modern:hover::before { width: 6px; }
.success-card::before { background: var(--gradient-success); }
.danger-card::before { background: var(--gradient-danger); }
.info-card::before { background: var(--gradient-info); }
.primary-card::before { background: var(--gradient-primary); }

.stat-icon-modern {
    width: 60px; height: 60px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.75rem; flex-shrink: 0;
}
.success-card .stat-icon-modern { background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(5,150,105,0.15)); color: #10b981; }
.danger-card .stat-icon-modern { background: linear-gradient(135deg, rgba(239,68,68,0.1), rgba(220,38,38,0.15)); color: #ef4444; }
.info-card .stat-icon-modern { background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(37,99,235,0.15)); color: #3b82f6; }
.primary-card .stat-icon-modern { background: linear-gradient(135deg, rgba(102,126,234,0.1), rgba(118,75,162,0.15)); color: #667eea; }

.stat-content { flex: 1; }
.stat-value-modern { font-size: 2rem; font-weight: 800; color: #1a202c; line-height: 1; margin-bottom: 0.25rem; }
.stat-label-modern { font-size: 0.85rem; color: #6b7280; font-weight: 500; }

.card-modern {
    background: white; border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm); border: 1px solid rgba(0,0,0,0.04);
    transition: var(--transition);
}
.card-modern-body { padding: 1.5rem; }

.btn-modern-primary {
    background: var(--gradient-primary); border: none; color: white;
    padding: 0.65rem 1.5rem; border-radius: var(--radius-md);
    font-weight: 600; font-size: 0.95rem; transition: var(--transition);
    box-shadow: 0 4px 12px rgba(102,126,234,0.3);
    display: inline-flex; align-items: center; text-decoration: none;
}
.btn-modern-primary:hover {
    transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102,126,234,0.4); color: white;
}

/* ========== PIPELINE CARDS ========== */
.pipeline-card {
    background: white; border-radius: var(--radius-lg);
    border: 1px solid rgba(0,0,0,0.06);
    transition: var(--transition); overflow: hidden;
}
.pipeline-card:hover {
    transform: translateY(-4px); box-shadow: var(--shadow-lg);
    border-color: rgba(102,126,234,0.2);
}
.pipeline-card-accent {
    height: 4px; width: 100%; transition: height 0.3s;
}
.pipeline-card:hover .pipeline-card-accent { height: 6px; }
.mode-nocode .pipeline-card-accent { background: var(--gradient-success); }
.mode-lowcode .pipeline-card-accent { background: var(--gradient-warning); }
.mode-code .pipeline-card-accent { background: linear-gradient(135deg, #8b5cf6, #ec4899); }

.pipeline-card-body { padding: 1.25rem 1.5rem; cursor: pointer; }
.pipeline-card-header { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; }
.pipeline-card-icon {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; color: white; flex-shrink: 0;
}
.mode-nocode .pipeline-card-icon { background: linear-gradient(135deg, #10b981, #059669); }
.mode-lowcode .pipeline-card-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
.mode-code .pipeline-card-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

.pipeline-card-title {
    font-size: 1.1rem; font-weight: 700; color: #1a202c; margin-bottom: 2px;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 200px;
}
.pipeline-card-desc {
    font-size: 0.85rem; color: #6b7280;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    overflow: hidden; min-height: 2.5em;
}
.pipeline-card-stats {
    display: flex; gap: 16px; padding-top: 12px;
    border-top: 1px solid #f3f4f6; margin-top: 12px;
}
.pipeline-stat {
    font-size: 0.8rem; color: #6b7280;
    display: flex; align-items: center; gap: 4px;
}
.pipeline-stat strong { color: #1a202c; }
.pipeline-card-footer {
    padding: 0.75rem 1.5rem; background: #f9fafb;
    border-top: 1px solid #f3f4f6;
    display: flex; justify-content: space-between; align-items: center;
}

.mode-badge {
    font-size: 0.7rem; padding: 3px 10px; border-radius: 20px;
    font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
}
.mode-badge.nocode { background: #d1fae5; color: #059669; }
.mode-badge.lowcode { background: #fef3c7; color: #d97706; }
.mode-badge.code { background: #ede9fe; color: #7c3aed; }

.status-dot {
    width: 8px; height: 8px; border-radius: 50%; display: inline-block;
}
.status-dot.active { background: #10b981; box-shadow: 0 0 6px rgba(16,185,129,0.5); animation: pulse-dot 2s infinite; }
.status-dot.inactive { background: #d1d5db; }
@keyframes pulse-dot {
    0%,100% { box-shadow: 0 0 4px rgba(16,185,129,0.4); }
    50% { box-shadow: 0 0 12px rgba(16,185,129,0.6); }
}

.trigger-badge {
    font-size: 0.7rem; padding: 2px 8px; border-radius: 12px; font-weight: 500;
}
.trigger-manual { background: #e0e7ff; color: #4338ca; }
.trigger-api_event { background: #ccfbf1; color: #0d9488; }
.trigger-cron { background: #fef3c7; color: #b45309; }
.trigger-rotina_finished { background: #fce7f3; color: #be185d; }

.pipeline-actions { display: flex; gap: 4px; }
.pipeline-actions .btn {
    width: 32px; height: 32px; padding: 0;
    display: flex; align-items: center; justify-content: center;
    border-radius: 8px; font-size: 0.85rem;
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

@media (max-width: 768px) {
    .page-header-modern { padding: 1.25rem; }
    .page-title-modern { font-size: 1.5rem; }
    .page-icon-modern { width: 50px; height: 50px; font-size: 1.5rem; }
    .stat-value-modern { font-size: 1.5rem; }
}
</style>

<!-- Header da Página -->
<div class="page-header-modern">
    <div class="page-icon-modern"><i class="bi bi-bezier2"></i></div>
    <div>
        <h1 class="page-title-modern">Pipelines</h1>
        <p class="page-subtitle-modern">Crie e gerencie fluxos visuais de dados com drag-and-drop</p>
    </div>
    <div class="ms-auto d-flex gap-2 flex-wrap">
        <button class="btn btn-outline-secondary" onclick="importarPipeline()">
            <i class="bi bi-upload me-2"></i>Importar
        </button>
        <a href="<?= BASE_URL ?>/pipelines/builder" class="btn-modern-primary">
            <i class="bi bi-plus-lg me-2"></i>Novo Pipeline
        </a>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern primary-card">
            <div class="stat-icon-modern"><i class="bi bi-bezier2"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="statTotal">0</div>
                <div class="stat-label-modern">Total Pipelines</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern success-card">
            <div class="stat-icon-modern"><i class="bi bi-check-circle"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="statAtivos">0</div>
                <div class="stat-label-modern">Ativos</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern info-card">
            <div class="stat-icon-modern"><i class="bi bi-play-circle"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="statExecHoje">0</div>
                <div class="stat-label-modern">Execuções Hoje</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern danger-card">
            <div class="stat-icon-modern"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="statErros">0</div>
                <div class="stat-label-modern">Erros Hoje</div>
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
                    <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Buscar pipelines...">
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="filterModo">
                    <option value="">Todos os Modos</option>
                    <option value="nocode">No-Code</option>
                    <option value="lowcode">Low-Code</option>
                    <option value="code">Code</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="filterStatus">
                    <option value="">Todos</option>
                    <option value="1">Ativos</option>
                    <option value="0">Inativos</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="filterTrigger">
                    <option value="">Todos os Triggers</option>
                    <option value="manual">Manual</option>
                    <option value="cron">Agendado (CRON)</option>
                    <option value="api_event">Evento API</option>
                </select>
            </div>
            <div class="col-md-2 text-end">
                <button class="btn btn-outline-secondary btn-sm" onclick="loadPipelines()" title="Atualizar">
                    <i class="bi bi-arrow-clockwise me-1"></i> Atualizar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Pipeline Cards Grid -->
<div class="row g-4" id="pipelineGrid">
    <div class="col-12 text-center py-5">
        <div class="spinner-border text-primary"></div>
        <p class="mt-2 text-muted">Carregando pipelines...</p>
    </div>
</div>

<!-- Modal Importar -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: var(--radius-lg); overflow: hidden;">
            <div class="modal-header" style="background: var(--gradient-primary); color: white;">
                <h5 class="modal-title"><i class="bi bi-upload me-2"></i>Importar Pipeline</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Arquivo JSON do Pipeline</label>
                    <input type="file" class="form-control" id="importFile" accept=".json">
                </div>
                <div id="importPreview" class="d-none">
                    <div class="alert alert-info" style="border-radius: var(--radius-md);">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-code fs-4"></i>
                            <div><strong id="importName"></strong><br><small id="importDesc" class="text-muted"></small></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn-modern-primary" id="btnImport" disabled onclick="confirmarImportacao()">
                    <i class="bi bi-upload me-2"></i>Importar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Histórico -->
<div class="modal fade" id="historicoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: var(--radius-lg); overflow: hidden;">
            <div class="modal-header" style="background: var(--gradient-info); color: white;">
                <h5 class="modal-title"><i class="bi bi-clock-history me-2"></i>Histórico de Execuções</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="historicoBody">
                <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$extraStyles = <<<'STYLES'
STYLES;

$extraScripts = <<<SCRIPTS
<script>
const csrfToken = '{$csrfToken}';
let allPipelines = [];

function escapeHtml(t) { if(!t) return ''; const d=document.createElement('div'); d.textContent=t; return d.innerHTML; }

function loadPipelines() {
    $.getJSON(baseUrl + '/pipelines/list', function(res) {
        allPipelines = res.data || [];
        renderGrid(allPipelines);
    }).fail(function() {
        document.getElementById('pipelineGrid').innerHTML = 
            '<div class="col-12 text-center py-5 text-muted"><i class="bi bi-exclamation-circle fs-1"></i><p class="mt-2">Erro ao carregar pipelines</p></div>';
    });
    $.getJSON(baseUrl + '/pipelines/stats', function(res) {
        if(res.sucesso) {
            document.getElementById('statTotal').textContent = res.total||0;
            document.getElementById('statAtivos').textContent = res.ativos||0;
            document.getElementById('statExecHoje').textContent = res.execucoes_hoje||0;
            document.getElementById('statErros').textContent = res.erro_hoje||0;
        }
    });
}

function renderGrid(pipelines) {
    const grid = document.getElementById('pipelineGrid');
    if(!pipelines||!pipelines.length) {
        grid.innerHTML = '<div class="col-12"><div class="empty-state">' +
            '<div class="empty-state-icon"><i class="bi bi-bezier2"></i></div>' +
            '<h4>Nenhum pipeline criado</h4>' +
            '<p>Crie seu primeiro pipeline visual e comece a automatizar seus fluxos de dados</p>' +
            '<a href="'+baseUrl+'/pipelines/builder" class="btn-modern-primary"><i class="bi bi-plus-lg me-2"></i>Criar Primeiro Pipeline</a></div></div>';
        return;
    }
    let html = '';
    pipelines.forEach(function(p) {
        const sN = escapeHtml(p.nome), sD = escapeHtml(p.descricao||'Sem descrição');
        const ml = {nocode:'No-Code',lowcode:'Low-Code',code:'Code'}[p.modo]||p.modo;
        const mi = {nocode:'bi-hand-index-thumb',lowcode:'bi-sliders',code:'bi-code-slash'}[p.modo]||'bi-bezier2';
        const isA = p.ativo===true||p.ativo==='t';
        const tE = parseInt(p.total_execucoes)||0;
        const sR = tE>0 ? Math.round((parseInt(p.execucoes_sucesso)||0)/tE*100) : 0;
        const lE = p.ultima_execucao ? new Date(p.ultima_execucao).toLocaleDateString('pt-BR',{day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'}) : 'Nunca';
        const tL = {'manual':'<span class="trigger-badge trigger-manual"><i class="bi bi-hand-index me-1"></i>Manual</span>',
            'cron':'<span class="trigger-badge trigger-cron"><i class="bi bi-clock me-1"></i>CRON</span>',
            'api_event':'<span class="trigger-badge trigger-api_event"><i class="bi bi-cloud-lightning me-1"></i>API Event</span>',
            'rotina_finished':'<span class="trigger-badge trigger-rotina_finished"><i class="bi bi-play-circle me-1"></i>Rotina</span>'}[p.trigger_tipo]||'<span class="trigger-badge trigger-manual"><i class="bi bi-hand-index me-1"></i>Manual</span>';
        const safeN = sN.replace(/'/g,"\\\\'");
        html += '<div class="col-12 col-md-6 col-xl-4" data-nome="'+sN.toLowerCase()+'" data-modo="'+escapeHtml(p.modo)+'" data-ativo="'+(isA?'1':'0')+'" data-trigger="'+escapeHtml(p.trigger_tipo||'manual')+'">' +
            '<div class="pipeline-card mode-'+escapeHtml(p.modo)+'">' +
            '<div class="pipeline-card-accent"></div>' +
            '<div class="pipeline-card-body" onclick="editarPipeline('+p.id+')">' +
            '<div class="pipeline-card-header">' +
            '<div class="pipeline-card-icon"><i class="bi '+mi+'"></i></div>' +
            '<div class="flex-grow-1" style="min-width:0">' +
            '<div class="d-flex align-items-center gap-2 mb-1">' +
            '<span class="pipeline-card-title">'+sN+'</span>' +
            '<span class="status-dot '+(isA?'active':'inactive')+'" title="'+(isA?'Ativo':'Inativo')+'"></span></div>' +
            '<div class="pipeline-card-desc">'+sD+'</div></div>' +
            '<span class="mode-badge '+escapeHtml(p.modo)+'">'+escapeHtml(ml)+'</span></div>' +
            '<div class="d-flex gap-2 align-items-center mb-2">'+tL+
            (p.agendamento_cron?'<code class="small text-muted" style="font-size:0.7rem">'+escapeHtml(p.agendamento_cron)+'</code>':'')+
            '</div>' +
            '<div class="pipeline-card-stats">' +
            '<div class="pipeline-stat"><i class="bi bi-play-circle"></i> <strong>'+tE+'</strong> exec</div>' +
            '<div class="pipeline-stat"><i class="bi bi-check-circle text-success"></i> <strong>'+sR+'%</strong></div>' +
            '<div class="pipeline-stat"><i class="bi bi-clock"></i> '+escapeHtml(lE)+'</div></div></div>' +
            '<div class="pipeline-card-footer">' +
            '<div class="pipeline-actions">' +
            '<button class="btn btn-outline-success btn-sm" title="Executar" onclick="event.stopPropagation();executarPipeline('+p.id+',\''+safeN+'\')"><i class="bi bi-play-fill"></i></button>' +
            '<button class="btn btn-outline-info btn-sm" title="Histórico" onclick="event.stopPropagation();verHistorico('+p.id+',\''+safeN+'\')"><i class="bi bi-clock-history"></i></button>' +
            '<button class="btn btn-outline-primary btn-sm" title="Duplicar" onclick="event.stopPropagation();duplicarPipeline('+p.id+')"><i class="bi bi-copy"></i></button>' +
            '<button class="btn btn-outline-secondary btn-sm" title="Exportar" onclick="event.stopPropagation();exportarPipeline('+p.id+')"><i class="bi bi-download"></i></button></div>' +
            '<div class="d-flex gap-1">' +
            '<button class="btn btn-sm '+(isA?'btn-outline-warning':'btn-outline-success')+'" title="'+(isA?'Desativar':'Ativar')+'" onclick="event.stopPropagation();toggleAtivo('+p.id+')"><i class="bi bi-power"></i></button>' +
            '<button class="btn btn-outline-danger btn-sm" title="Excluir" onclick="event.stopPropagation();excluirPipeline('+p.id+',\''+safeN+'\')"><i class="bi bi-trash"></i></button>' +
            '</div></div></div></div>';
    });
    grid.innerHTML = html;
}

function filterPipelines() {
    const s = document.getElementById('searchInput').value.toLowerCase();
    const m = document.getElementById('filterModo').value;
    const st = document.getElementById('filterStatus').value;
    const tr = document.getElementById('filterTrigger').value;
    const f = allPipelines.filter(function(p) {
        if(s && !p.nome.toLowerCase().includes(s) && !(p.descricao||'').toLowerCase().includes(s)) return false;
        if(m && p.modo!==m) return false;
        if(st==='1' && p.ativo!==true && p.ativo!=='t') return false;
        if(st==='0' && (p.ativo===true||p.ativo==='t')) return false;
        if(tr && (p.trigger_tipo||'manual')!==tr) return false;
        return true;
    });
    renderGrid(f);
}

function editarPipeline(id) { window.location.href = baseUrl+'/pipelines/builder/'+id; }

function executarPipeline(id, nome) {
    Swal.fire({
        title:'Executar Pipeline?',
        html:'<p>O pipeline <strong>'+nome+'</strong> será executado imediatamente.</p><div class="alert alert-info mt-3" style="border-radius:12px"><i class="bi bi-info-circle me-2"></i>Acompanhe o progresso no histórico.</div>',
        icon:'question', showCancelButton:true, confirmButtonColor:'#10b981',
        confirmButtonText:'<i class="bi bi-play-fill me-2"></i>Executar', cancelButtonText:'Cancelar'
    }).then(function(r) {
        if(r.isConfirmed) {
            Swal.fire({title:'Executando...',text:'Aguarde o processamento',allowOutsideClick:false,didOpen:function(){Swal.showLoading();}});
            $.post(baseUrl+'/pipelines/executar/'+id,{_csrf_token:csrfToken},function(res) {
                if(res.sucesso) Swal.fire({icon:'success',title:'Sucesso!',html:'Executado em <strong>'+res.duracao_ms+'ms</strong><br><span class="text-success">'+res.nodes_sucesso+' nós OK</span>'+(res.nodes_falha>0?', <span class="text-danger">'+res.nodes_falha+' falhas</span>':''),confirmButtonColor:'#667eea'});
                else Swal.fire({icon:'error',title:'Erro na Execução',text:res.mensagem,confirmButtonColor:'#667eea'});
                loadPipelines();
            },'json').fail(function(){Swal.fire('Erro!','Falha na requisição','error');});
        }
    });
}

function verHistorico(id, nome) {
    var modal = new bootstrap.Modal(document.getElementById('historicoModal'));
    document.querySelector('#historicoModal .modal-title').innerHTML = '<i class="bi bi-clock-history me-2"></i>Histórico - '+escapeHtml(nome);
    document.getElementById('historicoBody').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    modal.show();
    $.getJSON(baseUrl+'/pipelines/historico/'+id, function(res) {
        if(res.sucesso && res.data && res.data.length>0) {
            let h = '<div class="table-responsive"><table class="table table-hover mb-0">' +
                '<thead style="background:linear-gradient(135deg,#f8f9fa,#e9ecef)"><tr>' +
                '<th style="padding:12px 16px">ID</th><th>Status</th><th>Início</th><th>Duração</th><th>Nós</th><th>Erro</th></tr></thead><tbody>';
            res.data.forEach(function(e) {
                const sb = {'success':'<span class="badge bg-success">Sucesso</span>','error':'<span class="badge bg-danger">Erro</span>','running':'<span class="badge bg-primary">Executando</span>'}[e.status]||'<span class="badge bg-secondary">'+escapeHtml(e.status)+'</span>';
                h += '<tr><td style="padding:12px 16px"><strong>#'+e.id+'</strong></td><td>'+sb+'</td><td class="small">'+(e.data_inicio?new Date(e.data_inicio).toLocaleString('pt-BR'):'-')+'</td><td>'+(e.duracao_ms?e.duracao_ms+'ms':'-')+'</td><td><span class="text-success">'+(e.nodes_sucesso||0)+'</span>/<span class="text-danger">'+(e.nodes_falha||0)+'</span>/'+(e.nodes_total||0)+'</td><td class="small text-danger">'+escapeHtml(e.erro||'')+'</td></tr>';
            });
            h += '</tbody></table></div>';
            document.getElementById('historicoBody').innerHTML = h;
        } else {
            document.getElementById('historicoBody').innerHTML = '<div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1"></i><p class="mt-2">Nenhuma execução registrada</p></div>';
        }
    });
}

function toggleAtivo(id) {
    $.post(baseUrl+'/pipelines/toggle/'+id,{_csrf_token:csrfToken},function(res) {
        if(res.sucesso)loadPipelines(); else Swal.fire('Erro!','Não foi possível alterar o status','error');
    },'json');
}

function duplicarPipeline(id) {
    $.post(baseUrl+'/pipelines/duplicar/'+id,{_csrf_token:csrfToken},function(res) {
        if(res.sucesso){Swal.fire({icon:'success',title:'Duplicado!',text:res.mensagem,timer:2000,showConfirmButton:false});loadPipelines();}
        else Swal.fire('Erro!',res.mensagem,'error');
    },'json');
}

function exportarPipeline(id) {
    $.getJSON(baseUrl+'/pipelines/exportar/'+id, function(res) {
        if(res.sucesso) {
            const b = new Blob([JSON.stringify(res.data,null,2)],{type:'application/json'});
            const a = document.createElement('a'); a.href=URL.createObjectURL(b); a.download=res.filename||('pipeline_'+id+'.json'); a.click(); URL.revokeObjectURL(a.href);
            Swal.fire({icon:'success',title:'Exportado!',text:'Pipeline baixado como JSON',timer:2000,showConfirmButton:false});
        }
    });
}

function excluirPipeline(id, nome) {
    Swal.fire({
        title:'Excluir Pipeline?',html:'O pipeline <strong>'+nome+'</strong> e todo seu histórico serão removidos permanentemente.',
        icon:'warning',showCancelButton:true,confirmButtonColor:'#ef4444',confirmButtonText:'<i class="bi bi-trash me-2"></i>Excluir',cancelButtonText:'Cancelar'
    }).then(function(r) {
        if(r.isConfirmed) {
            $.post(baseUrl+'/pipelines/delete/'+id,{_csrf_token:csrfToken},function(res) {
                if(res.sucesso){Swal.fire({icon:'success',title:'Excluído!',text:'Pipeline removido.',timer:2000,showConfirmButton:false});loadPipelines();}
                else Swal.fire('Erro!',res.mensagem,'error');
            },'json');
        }
    });
}

function importarPipeline() {
    document.getElementById('importFile').value='';
    document.getElementById('importPreview').classList.add('d-none');
    document.getElementById('btnImport').disabled=true;
    new bootstrap.Modal(document.getElementById('importModal')).show();
}

let importData = null;
document.getElementById('importFile').addEventListener('change',function(e) {
    const file=e.target.files[0]; if(!file)return;
    const reader=new FileReader();
    reader.onload=function(ev) {
        try {
            importData=JSON.parse(ev.target.result);
            document.getElementById('importName').textContent=importData.nome||'Pipeline';
            document.getElementById('importDesc').textContent=importData.descricao||'Sem descrição';
            document.getElementById('importPreview').classList.remove('d-none');
            document.getElementById('btnImport').disabled=false;
        } catch(e){Swal.fire('Erro!','Arquivo JSON inválido','error');}
    };
    reader.readAsText(file);
});

function confirmarImportacao() {
    if(!importData)return;
    $.ajax({url:baseUrl+'/pipelines/importar',method:'POST',contentType:'application/json',
        data:JSON.stringify({pipeline:importData,_csrf_token:csrfToken}),
        success:function(res) {
            bootstrap.Modal.getInstance(document.getElementById('importModal')).hide();
            if(res.sucesso){Swal.fire({icon:'success',title:'Importado!',text:res.mensagem,confirmButtonColor:'#667eea'});loadPipelines();}
            else Swal.fire('Erro!',res.mensagem,'error');
        }
    });
}

document.getElementById('searchInput').addEventListener('input',filterPipelines);
document.getElementById('filterModo').addEventListener('change',filterPipelines);
document.getElementById('filterStatus').addEventListener('change',filterPipelines);
document.getElementById('filterTrigger').addEventListener('change',filterPipelines);

$(document).ready(function(){loadPipelines();});
</script>
SCRIPTS;

include __DIR__ . '/../layouts/base.php';
?>
