<?php 
$pageTitle = 'Eventos de API';
$currentPage = 'eventos-api';

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

/* ==== ESTILOS ESPECÍFICOS ==== */
.operator-badge {
    font-size: 0.75rem;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    background: #e0e7ff;
    color: #4338ca;
    font-family: monospace;
}
.jsonpath-preview {
    font-family: 'Fira Code', monospace;
    font-size: 0.85rem;
    background: #f8fafc;
    padding: 0.5rem;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
}
.test-json-area {
    font-family: 'Fira Code', monospace;
    font-size: 0.85rem;
    min-height: 200px;
}
.match-success { background: #dcfce7; border-color: #86efac; }
.match-fail { background: #fee2e2; border-color: #fca5a5; }
</style>

<!-- Header da Página -->
<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-bell-fill"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Eventos de API</h1>
        <p class="page-subtitle-modern">Configure condições e ações para valores capturados das APIs</p>
    </div>
    <div class="ms-auto d-flex gap-2">
        <select class="form-select" id="filtroApi" style="width: 250px;">
            <option value="">Todas as APIs</option>
        </select>
        <button class="btn-modern-primary" onclick="abrirModalEvento()">
            <i class="bi bi-plus-lg me-2"></i>Novo Evento
        </button>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern primary-card">
            <div class="stat-icon-modern"><i class="bi bi-bell"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="totalEventos">0</div>
                <div class="stat-label-modern">Total de Eventos</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern success-card">
            <div class="stat-icon-modern"><i class="bi bi-check-circle"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="eventosAtivos">0</div>
                <div class="stat-label-modern">Ativos</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern info-card">
            <div class="stat-icon-modern"><i class="bi bi-diagram-3"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="eventosComWorkflow">0</div>
                <div class="stat-label-modern">Com Workflow</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern danger-card">
            <div class="stat-icon-modern"><i class="bi bi-bullseye"></i></div>
            <div class="stat-content">
                <div class="stat-value-modern" id="totalMatches">0</div>
                <div class="stat-label-modern">Total Matches</div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Eventos -->
<div class="card-modern">
    <div class="card-modern-header">
        <i class="bi bi-list-ul me-2"></i>
        <span>Eventos Configurados</span>
    </div>
    <div class="card-modern-body p-0">
        <div class="table-responsive">
            <table class="table-modern" id="tblEventos">
                <thead>
                    <tr>
                        <th>Evento</th>
                        <th>API</th>
                        <th>JSONPath</th>
                        <th>Condição</th>
                        <th>Ação</th>
                        <th>Matches</th>
                        <th>Status</th>
                        <th width="120">Ações</th>
                    </tr>
                </thead>
                <tbody id="listaEventos">
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                            <span class="ms-2">Carregando...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Criar/Editar Evento -->
<div class="modal fade" id="modalEvento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-bell-plus me-2"></i>
                    <span id="modalEventoTitulo">Novo Evento</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEvento">
                <div class="modal-body">
                    <input type="hidden" name="id" id="eventoId">
                    
                    <!-- Informações Básicas -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nome do Evento *</label>
                            <input type="text" class="form-control" name="nome" id="eventoNome" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">API *</label>
                            <select class="form-select" name="id_api" id="eventoApi" required>
                                <option value="">Selecione uma API...</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Descrição</label>
                            <textarea class="form-control" name="descricao" id="eventoDescricao" rows="2"></textarea>
                        </div>
                    </div>
                    
                    <!-- Extração de Valor -->
                    <div class="card mb-4">
                        <div class="card-header py-2">
                            <i class="bi bi-braces me-2"></i>Extração de Valor
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">JSONPath</label>
                                    <input type="text" class="form-control font-monospace" name="jsonpath" id="eventoJsonpath" placeholder="$.data.status">
                                    <small class="text-muted">Ex: $.data.status, $.results[0].value, $.user.name</small>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tipo de Valor</label>
                                    <select class="form-select" name="tipo_valor" id="eventoTipoValor">
                                        <option value="string">String</option>
                                        <option value="number">Número</option>
                                        <option value="boolean">Booleano</option>
                                        <option value="json">JSON</option>
                                        <option value="array">Array</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Condição -->
                    <div class="card mb-4">
                        <div class="card-header py-2">
                            <i class="bi bi-funnel me-2"></i>Condição
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Operador</label>
                                    <select class="form-select" name="operador" id="eventoOperador">
                                        <option value="equals">Igual a (==)</option>
                                        <option value="not_equals">Diferente de (!=)</option>
                                        <option value="contains">Contém</option>
                                        <option value="not_contains">Não contém</option>
                                        <option value="greater_than">Maior que (>)</option>
                                        <option value="less_than">Menor que (<)</option>
                                        <option value="greater_or_equal">Maior ou igual (>=)</option>
                                        <option value="less_or_equal">Menor ou igual (<=)</option>
                                        <option value="is_true">É verdadeiro</option>
                                        <option value="is_false">É falso</option>
                                        <option value="is_null">É nulo</option>
                                        <option value="is_not_null">Não é nulo</option>
                                        <option value="regex">Regex</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Valor Esperado</label>
                                    <input type="text" class="form-control" name="valor_esperado" id="eventoValorEsperado">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ação -->
                    <div class="card mb-4">
                        <div class="card-header py-2">
                            <i class="bi bi-lightning me-2"></i>Ação quando a condição for atendida
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Ação</label>
                                    <select class="form-select" name="acao" id="eventoAcao" onchange="toggleWorkflowSelect()">
                                        <option value="store_value">Apenas armazenar valor</option>
                                        <option value="trigger_workflow">Disparar workflow</option>
                                        <option value="store_and_trigger">Armazenar e disparar workflow</option>
                                        <option value="notify">Notificar</option>
                                    </select>
                                </div>
                                <div class="col-md-6" id="workflowSelectContainer" style="display: none;">
                                    <label class="form-label">Workflow</label>
                                    <select class="form-select" name="id_workflow" id="eventoWorkflow">
                                        <option value="">Selecione um workflow...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="armazenar_valor" id="eventoArmazenarValor" checked>
                                <label class="form-check-label" for="eventoArmazenarValor">
                                    Armazenar valor capturado no histórico
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="ativo" id="eventoAtivo" checked>
                        <label class="form-check-label" for="eventoAtivo">Evento ativo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-info" onclick="abrirTesteJsonPath()">
                        <i class="bi bi-bug me-1"></i>Testar JSONPath
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Teste JSONPath -->
<div class="modal fade" id="modalTesteJsonPath" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-bug me-2"></i>Testar JSONPath</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">JSON de Teste</label>
                        <textarea class="form-control test-json-area" id="testeJson" placeholder='{"data": {"status": true, "count": 42}}'></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">JSONPath</label>
                        <input type="text" class="form-control font-monospace" id="testeJsonPath" placeholder="$.data.status">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Operador</label>
                        <select class="form-select" id="testeOperador">
                            <option value="equals">Igual a</option>
                            <option value="not_equals">Diferente</option>
                            <option value="greater_than">Maior que</option>
                            <option value="is_true">É verdadeiro</option>
                            <option value="is_not_null">Não é nulo</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Valor Esperado</label>
                        <input type="text" class="form-control" id="testeValorEsperado">
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn btn-primary" onclick="executarTesteJsonPath()">
                            <i class="bi bi-play-fill me-1"></i>Testar
                        </button>
                    </div>
                    <div class="col-12" id="resultadoTeste" style="display: none;">
                        <hr>
                        <div class="card">
                            <div class="card-body" id="resultadoTesteConteudo">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$extraStyles = <<<STYLES
STYLES;

$extraScripts = '<script>const csrfToken = \'' . htmlspecialchars($csrfToken, ENT_QUOTES) . '\';</script>' . <<<'SCRIPTS'
<script>
let eventos = [];
let apis = [];
let workflows = [];

function mostrarErro(mensagem) {
    Swal.fire({icon: 'error', title: 'Erro', text: mensagem, toast: true, position: 'top-end', timer: 4000, showConfirmButton: false});
}

function mostrarSucesso(mensagem) {
    Swal.fire({icon: 'success', title: 'Sucesso', text: mensagem, toast: true, position: 'top-end', timer: 3000, showConfirmButton: false});
}

// Carregar dados iniciais
function carregarDados() {
    // Carregar APIs para os selects
    $.getJSON(baseUrl + '/api/apis-externas/list', function(res) {
        if (res.sucesso) {
            apis = res.dados || [];
            popularFiltroApi();
            popularSelectApi();
        }
    });
    
    // Carregar workflows para o select
    $.getJSON(baseUrl + '/api/workflows/list', function(res) {
        if (res.sucesso) {
            workflows = res.dados || [];
            popularSelectWorkflow();
        }
    }).fail(function() {
        // Workflows podem não existir, ignorar
    });
    
    carregarEventos();
}

// Carregar eventos
function carregarEventos() {
    const apiId = $('#filtroApi').val();
    const url = apiId ? baseUrl + '/api/eventos-api/list?api=' + apiId : baseUrl + '/api/eventos-api/list';
    
    $.getJSON(url, function(res) {
        if (res.sucesso) {
            eventos = res.dados || [];
            renderizarEventos(eventos);
            atualizarEstatisticas();
        }
    }).fail(function() {
        $('#listaEventos').html('<tr><td colspan="8" class="text-center py-4 text-danger">Erro ao carregar eventos</td></tr>');
    });
}

// Renderizar eventos na tabela
function renderizarEventos(lista) {
    const tbody = $('#listaEventos');
    
    if (!lista || lista.length === 0) {
        tbody.html('<tr><td colspan="8" class="text-center py-5"><p class="text-muted mb-2">Nenhum evento configurado</p><button class="btn btn-primary btn-sm" onclick="abrirModalEvento()"><i class="bi bi-plus-lg me-1"></i>Criar Evento</button></td></tr>');
        return;
    }
    
    const acaoLabels = {
        'store_value': 'Armazenar',
        'trigger_workflow': 'Disparar Workflow',
        'store_and_trigger': 'Armazenar + Workflow',
        'notify': 'Notificar'
    };
    
    let html = '';
    lista.forEach(ev => {
        const badgeClass = ev.ativo ? 'bg-success' : 'bg-secondary';
        const acaoLabel = acaoLabels[ev.acao] || ev.acao;
        const workflowInfo = ev.workflow_nome ? ' <small class="text-muted">(' + ev.workflow_nome + ')</small>' : '';
        
        html += `
        <tr>
            <td><strong>${ev.nome}</strong>${ev.descricao ? '<br><small class="text-muted">' + ev.descricao.substring(0, 50) + '</small>' : ''}</td>
            <td>${ev.api_nome || 'API #' + ev.id_api}</td>
            <td><code class="small">${ev.jsonpath || '-'}</code></td>
            <td><span class="operator-badge">${ev.operador}</span> <small>${ev.valor_esperado || ''}</small></td>
            <td>${acaoLabel}${workflowInfo}</td>
            <td><span class="badge bg-info">${ev.total_matches || 0}</span></td>
            <td><span class="badge ${badgeClass}">${ev.ativo ? 'Ativo' : 'Inativo'}</span></td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-warning" onclick="editarEvento(${ev.id})" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-danger" onclick="excluirEvento(${ev.id})" title="Excluir">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
        `;
    });
    
    tbody.html(html);
}

// Atualizar estatísticas
function atualizarEstatisticas() {
    const total = eventos.length;
    const ativos = eventos.filter(e => e.ativo).length;
    const comWorkflow = eventos.filter(e => (e.acao === 'trigger_workflow' || e.acao === 'store_and_trigger') && e.id_workflow).length;
    const totalMatches = eventos.reduce((sum, e) => sum + (parseInt(e.total_matches) || 0), 0);
    
    $('#totalEventos').text(total);
    $('#eventosAtivos').text(ativos);
    $('#eventosComWorkflow').text(comWorkflow);
    $('#totalMatches').text(totalMatches);
}

// Popular filtro API no header
function popularFiltroApi() {
    const select = $('#filtroApi');
    select.find('option:not(:first)').remove();
    apis.forEach(api => {
        select.append(`<option value="${api.id}">${api.nome}</option>`);
    });
    
    // Se veio com ?api=X na URL, selecionar
    const urlParams = new URLSearchParams(window.location.search);
    const apiParam = urlParams.get('api');
    if (apiParam) {
        select.val(apiParam);
        carregarEventos();
    }
}

// Popular select de API no modal
function popularSelectApi() {
    const select = $('#eventoApi');
    select.find('option:not(:first)').remove();
    apis.forEach(api => {
        select.append(`<option value="${api.id}">${api.nome}${api.ativo ? '' : ' (Inativa)'}</option>`);
    });
}

// Popular select de workflows no modal
function popularSelectWorkflow() {
    const select = $('#eventoWorkflow');
    select.find('option:not(:first)').remove();
    workflows.forEach(wf => {
        select.append(`<option value="${wf.id}">${wf.nome}</option>`);
    });
}

// Toggle workflow select visibility
function toggleWorkflowSelect() {
    const acao = $('#eventoAcao').val();
    if (acao === 'trigger_workflow' || acao === 'store_and_trigger') {
        $('#workflowSelectContainer').show();
    } else {
        $('#workflowSelectContainer').hide();
    }
}

// Abrir modal evento
function abrirModalEvento(id) {
    $('#formEvento')[0].reset();
    $('#eventoId').val('');
    $('#eventoAtivo').prop('checked', true);
    $('#eventoArmazenarValor').prop('checked', true);
    toggleWorkflowSelect();
    
    if (id) {
        $('#modalEventoTitulo').text('Editar Evento');
        $.getJSON(baseUrl + '/api/eventos-api/get/' + id, function(res) {
            if (res.sucesso && res.dados) {
                const ev = res.dados;
                $('#eventoId').val(ev.id);
                $('#eventoNome').val(ev.nome);
                $('#eventoDescricao').val(ev.descricao);
                $('#eventoApi').val(ev.id_api);
                $('#eventoJsonpath').val(ev.jsonpath);
                $('#eventoTipoValor').val(ev.tipo_valor);
                $('#eventoOperador').val(ev.operador);
                $('#eventoValorEsperado').val(ev.valor_esperado);
                $('#eventoAcao').val(ev.acao);
                $('#eventoWorkflow').val(ev.id_workflow);
                $('#eventoAtivo').prop('checked', ev.ativo);
                $('#eventoArmazenarValor').prop('checked', ev.armazenar_valor);
                toggleWorkflowSelect();
                new bootstrap.Modal('#modalEvento').show();
            } else {
                mostrarErro('Erro ao carregar evento');
            }
        });
    } else {
        $('#modalEventoTitulo').text('Novo Evento');
        new bootstrap.Modal('#modalEvento').show();
    }
}

// Salvar evento
function salvarEvento(e) {
    if (e) e.preventDefault();
    
    const nome = $('#eventoNome').val().trim();
    const idApi = parseInt($('#eventoApi').val());
    const jsonpath = $('#eventoJsonpath').val().trim();
    
    if (!nome) { mostrarErro('O campo Nome é obrigatório'); $('#eventoNome').focus(); return false; }
    if (!idApi || isNaN(idApi)) { mostrarErro('Selecione uma API'); $('#eventoApi').focus(); return false; }
    if (!jsonpath) { mostrarErro('O campo JSONPath é obrigatório'); $('#eventoJsonpath').focus(); return false; }
    
    const data = {
        id: $('#eventoId').val() || null,
        id_api: idApi,
        nome: nome,
        descricao: $('#eventoDescricao').val().trim(),
        jsonpath: jsonpath,
        tipo_valor: $('#eventoTipoValor').val(),
        operador: $('#eventoOperador').val(),
        valor_esperado: $('#eventoValorEsperado').val(),
        acao: $('#eventoAcao').val(),
        id_workflow: $('#eventoWorkflow').val() || null,
        armazenar_valor: $('#eventoArmazenarValor').is(':checked') ? '1' : '0',
        ativo: $('#eventoAtivo').is(':checked') ? '1' : '0'
    };
    
    const btnSalvar = $('#formEvento button[type="submit"]');
    btnSalvar.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Salvando...');
    
    $.ajax({
        url: baseUrl + '/api/eventos-api/salvar',
        method: 'POST',
        contentType: 'application/json',
        headers: {'X-CSRF-TOKEN': csrfToken},
        data: JSON.stringify(data),
        success: function(res) {
            btnSalvar.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i>Salvar');
            if (res.sucesso) {
                mostrarSucesso(res.mensagem || 'Evento salvo!');
                bootstrap.Modal.getInstance('#modalEvento').hide();
                carregarEventos();
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

// Editar evento
function editarEvento(id) {
    abrirModalEvento(id);
}

// Excluir evento
function excluirEvento(id) {
    const ev = eventos.find(e => e.id === id);
    Swal.fire({
        title: 'Excluir Evento?',
        text: 'Deseja excluir "' + (ev ? ev.nome : 'este evento') + '"?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Sim, excluir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(baseUrl + '/api/eventos-api/delete/' + id, {_csrf_token: csrfToken}, function(res) {
                if (res.sucesso) {
                    mostrarSucesso('Evento excluído!');
                    carregarEventos();
                } else {
                    mostrarErro(res.erro || 'Erro ao excluir');
                }
            }).fail(function() {
                mostrarErro('Erro ao comunicar com o servidor');
            });
        }
    });
}

// Abrir modal de teste JSONPath
function abrirTesteJsonPath() {
    // Copiar valores do formulário de evento
    const jsonpath = $('#eventoJsonpath').val();
    const operador = $('#eventoOperador').val();
    const valorEsperado = $('#eventoValorEsperado').val();
    
    if (jsonpath) $('#testeJsonPath').val(jsonpath);
    if (operador) $('#testeOperador').val(operador);
    if (valorEsperado) $('#testeValorEsperado').val(valorEsperado);
    
    // Pré-carregar JSON de exemplo da API selecionada
    const apiId = $('#eventoApi').val();
    if (apiId && !$('#testeJson').val()) {
        $('#testeJson').val('Carregando resposta da API...');
        $.ajax({
            url: baseUrl + '/api/apis-externas/testar',
            method: 'POST',
            contentType: 'application/json',
            headers: {'X-CSRF-TOKEN': csrfToken},
            data: JSON.stringify({ url: '', metodo: 'GET' }), // Will get API data first
            timeout: 10000
        });
        // Buscar dados da API e testar
        $.getJSON(baseUrl + '/api/apis-externas/get/' + apiId, function(res) {
            if (res.sucesso && res.dados) {
                const apiData = res.dados;
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
                    success: function(testRes) {
                        if (testRes.response) {
                            const jsonStr = typeof testRes.response === 'object' ? JSON.stringify(testRes.response, null, 2) : testRes.response_raw;
                            $('#testeJson').val(jsonStr);
                        } else {
                            $('#testeJson').val('{"exemplo": "cole aqui o JSON de resposta da API"}');
                        }
                    },
                    error: function() {
                        $('#testeJson').val('{"exemplo": "cole aqui o JSON de resposta da API"}');
                    }
                });
            }
        });
    }
    
    $('#resultadoTeste').hide();
    new bootstrap.Modal('#modalTesteJsonPath').show();
}

// Executar teste JSONPath
function executarTesteJsonPath() {
    const json = $('#testeJson').val().trim();
    const jsonpath = $('#testeJsonPath').val().trim();
    const operador = $('#testeOperador').val();
    const valorEsperado = $('#testeValorEsperado').val();
    
    if (!json) { mostrarErro('Insira um JSON para testar'); return; }
    if (!jsonpath) { mostrarErro('Insira um JSONPath'); return; }
    
    $.ajax({
        url: baseUrl + '/api/eventos-api/testar-jsonpath',
        method: 'POST',
        contentType: 'application/json',
        headers: {'X-CSRF-TOKEN': csrfToken},
        data: JSON.stringify({
            json: json,
            jsonpath: jsonpath,
            operador: operador,
            valor_esperado: valorEsperado
        }),
        success: function(res) {
            $('#resultadoTeste').show();
            if (res.sucesso) {
                const matchClass = res.match ? 'match-success' : 'match-fail';
                const valorStr = typeof res.valor_extraido === 'object' ? JSON.stringify(res.valor_extraido) : String(res.valor_extraido);
                $('#resultadoTesteConteudo').html(`
                    <div class="p-3 rounded ${matchClass}">
                        <h6><i class="bi bi-${res.match ? 'check-circle text-success' : 'x-circle text-danger'} me-2"></i>
                            Condição ${res.match ? 'ATENDIDA' : 'NÃO atendida'}</h6>
                        <div class="mt-2">
                            <strong>Valor extraído:</strong> <code>${$('<span>').text(valorStr).html()}</code><br>
                            <strong>Tipo:</strong> ${res.tipo_valor}<br>
                            <strong>JSONPath:</strong> <code>${res.jsonpath}</code>
                        </div>
                    </div>
                `);
            } else {
                $('#resultadoTesteConteudo').html('<div class="alert alert-danger">' + (res.erro || 'Erro no teste') + '</div>');
            }
        },
        error: function(xhr) {
            $('#resultadoTeste').show();
            $('#resultadoTesteConteudo').html('<div class="alert alert-danger">Erro: ' + (xhr.responseJSON?.erro || xhr.statusText) + '</div>');
        }
    });
}

// Filtro API
$('#filtroApi').on('change', carregarEventos);

// Inicializar
$(document).ready(function() {
    carregarDados();
    
    // Form submit handler
    $('#formEvento').on('submit', function(e) {
        return salvarEvento(e);
    });
});
</script>
SCRIPTS;

include __DIR__ . '/layouts/base.php';
?>
