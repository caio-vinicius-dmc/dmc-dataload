<?php
/**
 * DMC DataLoad - Rotinas
 * Nova UI Moderna
 */
$pageTitle = 'Rotinas';
$currentPage = 'rotinas';

ob_start();
?>

<!-- Page Header Modern -->
<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-gear"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Rotinas</h1>
        <p class="page-subtitle-modern">Gerencie as rotinas</p>
    </div>
    <a href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/rotinas/editor" class="btn-modern-primary ms-auto">
        <i class="bi bi-plus-lg me-2"></i>Nova Rotina
    </a>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4" id="statsCards">
    <div class="col-md-3">
        <div class="stat-card-modern info-card">
            <div class="stat-icon-modern">
                <i class="bi bi-gear"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="totalRotinas">0</div>
                <div class="stat-label-modern">Total de Rotinas</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card-modern success-card">
            <div class="stat-icon-modern">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="totalAtivas">0</div>
                <div class="stat-label-modern">Rotinas Agendadas</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card-modern primary-card">
            <div class="stat-icon-modern">
                <i class="bi bi-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="totalAgendadas">0</div>
                <div class="stat-label-modern">Agendadas</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card-modern danger-card">
            <div class="stat-icon-modern">
                <i class="bi bi-play-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="totalExecutando">0</div>
                <div class="stat-label-modern">Em Execução</div>
            </div>
        </div>
    </div>
</div>

<div class="card-modern">
    <div class="card-modern-header">
        <i class="bi bi-list me-2"></i>Lista de Rotinas
    </div>
    <div class="card-modern-body">
        <div class="table-responsive">
            <table class="table-modern" id="tblRotinas">
                <thead>
                    <tr>
                        <th><i class="bi bi-tag me-2"></i>Nome</th>
                        <th><i class="bi bi-hdd-network me-2"></i>Conexão</th>
                        <th><i class="bi bi-clock me-2"></i>Agendamento</th>
                        <th><i class="bi bi-check-circle me-2"></i>Status</th>
                        <th><i class="bi bi-calendar-event me-2"></i>Última Execução</th>
                        <th width="180"><i class="bi bi-gear me-2"></i>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                            <span class="ms-2">Carregando...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$extraStyles = <<<'STYLES'
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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

.stat-card-modern {
    background: white;
    border-radius: var(--radius-md);
    padding: 1.5rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}

.stat-card-modern:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
}

.stat-icon-modern {
    width: 56px;
    height: 56px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    flex-shrink: 0;
}

.info-card .stat-icon-modern { background: var(--gradient-info); }
.success-card .stat-icon-modern { background: var(--gradient-success); }
.primary-card .stat-icon-modern { background: var(--gradient-primary); }
.danger-card .stat-icon-modern { background: var(--gradient-danger); }

.stat-content {
    flex: 1;
    min-width: 0;
}

.stat-value-modern {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label-modern {
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 500;
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

.table-modern thead th:first-child {
    border-top-left-radius: var(--radius-md);
}

.table-modern thead th:last-child {
    border-top-right-radius: var(--radius-md);
}

.table-modern tbody tr {
    border-bottom: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.table-modern tbody tr:hover {
    background-color: #f8fafc;
    transform: scale(1.01);
}

.table-modern tbody td {
    padding: 1rem;
    vertical-align: middle;
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

.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.75rem;
}

.badge-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.badge-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.badge-secondary {
    background: #94a3b8;
    color: white;
}

@media (max-width: 991px) {
    .page-header-modern {
        flex-wrap: wrap;
    }
    
    .page-header-modern .ms-auto {
        margin-left: 0 !important;
        width: 100%;
    }
    
    .stat-value-modern {
        font-size: 1.5rem;
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
    
    .stat-card-modern {
        padding: 1rem;
    }
    
    .stat-icon-modern {
        width: 48px;
        height: 48px;
        font-size: 1.25rem;
    }
    
    .table-modern {
        font-size: 0.875rem;
    }
}
</style>
STYLES;

$csrfToken = App\Core\AuthMiddleware::gerarTokenCSRF();
$extraScripts = '<script>const csrfToken = \'' . htmlspecialchars($csrfToken, ENT_QUOTES) . '\';</script>';
$extraScripts .= <<<'SCRIPTS'
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
let tabela;

function atualizarStats(data) {
    const stats = {
        total: 0,
        ativas: 0,
        agendadas: 0,
        executando: 0
    };
    
    (data || []).forEach(r => {
        stats.total++;
        if (r.ativa) stats.ativas++;
        if (r.agendamento_cron) stats.agendadas++;
        if (r.esta_executando) stats.executando++;
    });
    
    document.getElementById('totalRotinas').textContent = stats.total;
    document.getElementById('totalAtivas').textContent = stats.ativas;
    document.getElementById('totalAgendadas').textContent = stats.agendadas;
    document.getElementById('totalExecutando').textContent = stats.executando;
}

function loadTable() {
    $.getJSON(baseUrl + "/rotinas/list", function(res) {
        // Atualizar estatísticas
        atualizarStats(res.data);
        
        // Destruir DataTable se já existir
        if ($.fn.DataTable.isDataTable("#tblRotinas")) {
            $("#tblRotinas").DataTable().destroy();
        }
        
        const tbody = $("#tblRotinas tbody");
        tbody.empty();
        
        (res.data || []).forEach(function(r) {
            const statusBadge = r.esta_executando 
                ? `<span class="badge-status badge-warning"><i class="bi bi-play-fill me-1"></i>Executando</span>`
                : (r.ativa 
                    ? `<span class="badge-status badge-success"><i class="bi bi-check-circle me-1"></i>Agendada</span>`
                    : `<span class="badge-status badge-secondary"><i class="bi bi-pause-circle me-1"></i>Inativa</span>`);
            
            const agendamento = r.agendamento_cron 
                ? `<code class="small">${r.agendamento_cron}</code>`
                : `<span class="text-muted">Manual</span>`;
            
            const ultimaExec = r.ultima_execucao 
                ? new Date(r.ultima_execucao).toLocaleString("pt-BR")
                : "-";
            
            tbody.append(`<tr>
                <td>
                    <div>
                        <strong>${r.nome}</strong>
                        ${r.descricao ? `<br><small class="text-muted">${r.descricao}</small>` : ""}
                    </div>
                </td>
                <td>${r.nome_conexao || "-"}</td>
                <td>${agendamento}</td>
                <td>${statusBadge}</td>
                <td><small>${ultimaExec}</small></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-success" onclick="executarRotina(${r.id})" title="Executar" ${r.esta_executando ? "disabled" : ""}>
                            <i class="bi bi-play-fill"></i>
                        </button>
                        <a href="${baseUrl}/rotinas/editor?id=${r.id}" class="btn btn-outline-primary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button class="btn btn-outline-info" onclick="duplicarRotina(${r.id})" title="Duplicar">
                            <i class="bi bi-copy"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="excluirRotina(${r.id})" title="Excluir">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>`);
        });
        
        // Inicializar DataTable após popular
        tabela = $("#tblRotinas").DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json" },
            order: [[0, "asc"]]
        });
    });
}

function executarRotina(id) {
    // Buscar detalhes da rotina para confirmação informada
    $.getJSON(baseUrl + "/rotinas/get/" + id, function(rotina) {
        const dados = rotina.dados || rotina;
        const nome = dados.nome || dados.rotina?.nome || "Rotina #" + id;
        const blocos = dados.blocos || rotina.blocos || [];
        const totalBlocos = blocos.length;
        
        // Montar opções do dropdown "a partir de"
        let stepOptions = '<option value="1">Step 1 — Início  ·  Todos os blocos</option>';
        // Montar checkboxes para "somente selecionados"
        let checkboxItems = '';
        blocos.forEach(function(b, i) {
            const ordem = b.ordem || (i + 1);
            const codigo = b.codigo_bloco || b.nome || ('Bloco ' + ordem);
            const tipo = b.tipo_bloco || 'SQL';
            const codigoEsc = $('<span>').text(codigo).html();
            if (ordem > 1) {
                stepOptions += `<option value="${ordem}">Step ${ordem} — ${codigoEsc}  ·  ${tipo}</option>`;
            }
            checkboxItems += `
                <label class="swal-step-check-item d-flex align-items-center gap-2 px-2 py-1" style="cursor:pointer;border-radius:8px;transition:all .15s ease;border:1.5px solid transparent;" 
                       onmouseover="this.style.background='#eef2ff';this.style.borderColor='#c7d2fe'" onmouseout="this.style.background='';this.style.borderColor='transparent'" data-search="${codigoEsc.toLowerCase()} ${tipo.toLowerCase()}">
                    <input type="checkbox" class="form-check-input swal-step-checkbox m-0" value="${ordem}" checked style="min-width:18px;accent-color:#667eea;">
                    <span style="font-size:11px;min-width:42px;padding:2px 6px;background:linear-gradient(135deg,#eef2ff,#e0e7ff);color:#4338ca;border-radius:5px;text-align:center;font-weight:600;">Step ${ordem}</span>
                    <span style="font-size:13px;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#334155;" title="${codigoEsc}">${codigoEsc}</span>
                    <span style="font-size:10px;padding:2px 6px;background:#f1f5f9;color:#64748b;border-radius:5px;font-weight:500;">${tipo}</span>
                </label>`;
        });
        
        Swal.fire({
            title: "Confirmar execução",
            html: `<div class="text-start">
                <p class="mb-2"><strong>Rotina:</strong> ${$('<span>').text(nome).html()}</p>
                <p class="mb-3"><strong>Blocos SQL:</strong> ${totalBlocos}</p>
                
                <div class="d-flex gap-2 mb-3">
                    <button type="button" class="btn btn-sm btn-primary flex-fill swal-modo-btn" id="swal-modo-apartir" onclick="swalTrocarModo('apartir')" style="border-radius:10px;padding:8px 12px;font-weight:600;font-size:13px;letter-spacing:.2px;transition:all .2s;">
                        <i class="bi bi-skip-forward-fill me-1"></i>A partir de
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary flex-fill swal-modo-btn" id="swal-modo-selecionados" onclick="swalTrocarModo('selecionados')" style="border-radius:10px;padding:8px 12px;font-weight:600;font-size:13px;letter-spacing:.2px;transition:all .2s;">
                        <i class="bi bi-ui-checks me-1"></i>Somente selecionados
                    </button>
                </div>
                
                <!-- Modo: A partir de -->
                <div id="swal-panel-apartir">
                    <label class="form-label fw-semibold mb-2" style="font-size:13px;color:#475569;"><i class="bi bi-skip-forward me-1 text-primary"></i>Iniciar a partir de:</label>
                    <div class="position-relative mb-2">
                        <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:13px;pointer-events:none;z-index:1;"></i>
                        <input type="text" id="swal-step-filter-apartir" placeholder="Filtrar steps..." style="width:100%;padding:8px 12px 8px 34px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;transition:all .2s;background:#f8fafc;color:#334155;" onfocus="this.style.borderColor='#667eea';this.style.boxShadow='0 0 0 3px rgba(102,126,234,.1)';this.style.background='#fff'" onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none';this.style.background='#f8fafc'">
                    </div>
                    <select id="swal-step-select" size="${Math.min(totalBlocos, 8)}" style="width:100%;font-size:13px;overflow-y:auto;border:1.5px solid #e2e8f0;border-radius:10px;padding:4px;background:#f8fafc;color:#334155;outline:none;transition:border-color .2s;" onfocus="this.style.borderColor='#667eea'" onblur="this.style.borderColor='#e2e8f0'">
                        ${stepOptions}
                    </select>
                    <small class="text-muted d-block mt-2" style="font-size:12px;">Executa todos os blocos a partir do selecionado.</small>
                </div>
                
                <!-- Modo: Somente selecionados -->
                <div id="swal-panel-selecionados" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-semibold mb-0" style="font-size:13px;color:#475569;"><i class="bi bi-ui-checks me-1 text-primary"></i>Selecione os steps:</label>
                        <div class="d-flex gap-1">
                            <button type="button" onclick="swalToggleAll(true)" style="font-size:11px;padding:3px 10px;border:1.5px solid #667eea;color:#667eea;background:transparent;border-radius:6px;cursor:pointer;transition:all .15s;" onmouseover="this.style.background='#667eea';this.style.color='#fff'" onmouseout="this.style.background='transparent';this.style.color='#667eea'">Todos</button>
                            <button type="button" onclick="swalToggleAll(false)" style="font-size:11px;padding:3px 10px;border:1.5px solid #94a3b8;color:#64748b;background:transparent;border-radius:6px;cursor:pointer;transition:all .15s;" onmouseover="this.style.background='#94a3b8';this.style.color='#fff'" onmouseout="this.style.background='transparent';this.style.color='#64748b'">Nenhum</button>
                        </div>
                    </div>
                    <div class="position-relative mb-2">
                        <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:13px;pointer-events:none;z-index:1;"></i>
                        <input type="text" id="swal-step-filter-sel" placeholder="Filtrar steps..." style="width:100%;padding:8px 12px 8px 34px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;transition:all .2s;background:#f8fafc;color:#334155;" onfocus="this.style.borderColor='#667eea';this.style.boxShadow='0 0 0 3px rgba(102,126,234,.1)';this.style.background='#fff'" onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none';this.style.background='#f8fafc'">
                    </div>
                    <div id="swal-checks-container" style="max-height:${Math.min(totalBlocos * 34, 260)}px;overflow-y:auto;border:1.5px solid #e2e8f0;border-radius:10px;padding:6px;background:#f8fafc;">
                        ${checkboxItems}
                    </div>
                    <small class="text-muted d-block mt-2" style="font-size:12px;">Somente os steps marcados serão executados. <span id="swal-sel-count" class="fw-bold text-primary">${totalBlocos}</span> de ${totalBlocos} selecionados.</small>
                </div>
                
                <hr>
                <p class="text-warning mb-0"><i class="bi bi-exclamation-triangle me-1"></i>
                A rotina será executada <strong>imediatamente</strong> no banco de dados de destino.</p>
            </div>`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#10b981",
            cancelButtonText: "Cancelar",
            confirmButtonText: "<i class='bi bi-play-fill me-1'></i>Executar agora",
            width: 560,
            preConfirm: () => {
                const panelSel = document.getElementById('swal-panel-selecionados');
                const modo = panelSel && panelSel.style.display !== 'none' ? 'selecionados' : 'apartir';
                console.log('[DMC-DATALOAD] preConfirm | modo=' + modo + ' | panel display=' + (panelSel ? panelSel.style.display : 'null'));
                if (modo === 'selecionados') {
                    const checks = document.querySelectorAll('.swal-step-checkbox:checked');
                    const selecionados = Array.from(checks).map(c => parseInt(c.value));
                    console.log('[DMC-DATALOAD] preConfirm | selecionados=' + JSON.stringify(selecionados));
                    if (selecionados.length === 0) {
                        Swal.showValidationMessage('Selecione pelo menos um step para executar.');
                        return false;
                    }
                    return { modo: 'selecionados', selecionados: selecionados, iniciarDe: 1 };
                } else {
                    const select = document.getElementById('swal-step-select');
                    const iniciarDe = select ? parseInt(select.value) || 1 : 1;
                    console.log('[DMC-DATALOAD] preConfirm | iniciarDe=' + iniciarDe);
                    return { modo: 'apartir', selecionados: [], iniciarDe: iniciarDe };
                }
            },
            didOpen: () => {
                const select = document.getElementById('swal-step-select');
                if (select && select.options.length > 0) select.options[0].selected = true;
                
                // Filtro do modo "a partir de"
                document.getElementById('swal-step-filter-apartir').addEventListener('input', function() {
                    const termo = this.value.toLowerCase();
                    Array.from(select.options).forEach(opt => {
                        opt.style.display = opt.text.toLowerCase().includes(termo) ? '' : 'none';
                    });
                });
                
                // Filtro do modo "selecionados"
                document.getElementById('swal-step-filter-sel').addEventListener('input', function() {
                    const termo = this.value.toLowerCase();
                    document.querySelectorAll('.swal-step-check-item').forEach(item => {
                        item.style.display = item.getAttribute('data-search').includes(termo) ? '' : 'none';
                    });
                });
                
                // Atualizar contador de selecionados
                document.getElementById('swal-checks-container').addEventListener('change', swalAtualizarContagem);
            }
        }).then((result) => {
            console.log('[DMC-DATALOAD] Swal result:', JSON.stringify(result));
            if (result.isConfirmed && result.value) {
                const { modo, selecionados, iniciarDe } = result.value;
                console.log('[DMC-DATALOAD] then | modo=' + modo + ' | selecionados=' + JSON.stringify(selecionados) + ' | iniciarDe=' + iniciarDe);
                if (modo === 'selecionados') {
                    executarRotinaConfirmada(id, 1, selecionados);
                } else {
                    executarRotinaConfirmada(id, iniciarDe, []);
                }
            }
        });
    }).fail(function() {
        Swal.fire({
            title: "Executar rotina?",
            text: "A rotina será executada imediatamente.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#10b981",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Sim, executar"
        }).then((result) => {
            if (result.isConfirmed) {
                executarRotinaConfirmada(id, 1, []);
            }
        });
    });
}

// Helpers do modal de execução
function swalTrocarModo(modo) {
    const btnApartir = document.getElementById('swal-modo-apartir');
    const btnSel = document.getElementById('swal-modo-selecionados');
    const panelApartir = document.getElementById('swal-panel-apartir');
    const panelSel = document.getElementById('swal-panel-selecionados');
    
    if (modo === 'selecionados') {
        btnSel.className = 'btn btn-sm btn-primary flex-fill swal-modo-btn';
        btnApartir.className = 'btn btn-sm btn-outline-primary flex-fill swal-modo-btn';
        panelApartir.style.display = 'none';
        panelSel.style.display = 'block';
    } else {
        btnApartir.className = 'btn btn-sm btn-primary flex-fill swal-modo-btn';
        btnSel.className = 'btn btn-sm btn-outline-primary flex-fill swal-modo-btn';
        panelApartir.style.display = 'block';
        panelSel.style.display = 'none';
    }
}

function swalToggleAll(estado) {
    document.querySelectorAll('.swal-step-checkbox').forEach(cb => cb.checked = estado);
    swalAtualizarContagem();
}

function swalAtualizarContagem() {
    const total = document.querySelectorAll('.swal-step-checkbox').length;
    const checked = document.querySelectorAll('.swal-step-checkbox:checked').length;
    const el = document.getElementById('swal-sel-count');
    if (el) el.textContent = checked;
}

function executarRotinaConfirmada(id, iniciarDe, blocosSelecionados) {
    iniciarDe = iniciarDe || 1;
    blocosSelecionados = blocosSelecionados || [];
    let msgInicio = '';
    if (blocosSelecionados.length > 0) {
        msgInicio = `<p class="text-info small">Executando ${blocosSelecionados.length} step(s) selecionado(s)</p>`;
    } else if (iniciarDe > 1) {
        msgInicio = `<p class="text-info small">Iniciando a partir do step ${iniciarDe}</p>`;
    }
    Swal.fire({
        title: "Executando...",
        html: `<div class="my-3"><div class="spinner-border text-primary"></div></div>
               ${msgInicio}
               <p class="text-muted">Aguarde a conclusão da rotina</p>`,
        allowOutsideClick: false,
        showConfirmButton: false
    });
    
    const postData = { iniciar_de_bloco: iniciarDe };
    if (blocosSelecionados.length > 0) {
        postData.blocos_selecionados = JSON.stringify(blocosSelecionados);
        postData.modo_execucao = 'selecionados';
    } else if (iniciarDe > 1) {
        postData.modo_execucao = 'apartir';
    } else {
        postData.modo_execucao = 'normal';
    }
    console.log('[DMC-DATALOAD] POST data para execução:', JSON.stringify(postData));
    
    $.ajax({
        url: baseUrl + "/rotinas/run/" + id,
        type: "POST",
        data: postData,
        dataType: "json",
        timeout: 120000, // 2 minutos
        headers: {'X-CSRF-TOKEN': csrfToken},
                success: function(res) {
                    console.log("Resposta da execução:", res);
                    if (res.sucesso) {
                        Swal.fire({
                            title: "Sucesso!",
                            html: `<p>Rotina executada com sucesso.</p>
                                   <p><a href="${baseUrl}/historico">Ver histórico de execuções</a></p>`,
                            icon: "success"
                        });
                    } else if (res.status === 'parcial') {
                        const m = res.metricas || {};
                        Swal.fire({
                            title: "Parcial",
                            html: `<p>A rotina foi executada com erros parciais.</p>
                                   <p class="small text-muted">Blocos: ${m.blocos_sucesso || 0} sucesso / ${m.blocos_falha || 0} falha</p>
                                   <p><a href="${baseUrl}/historico">Ver detalhes no histórico</a></p>`,
                            icon: "warning"
                        });
                    } else {
                        Swal.fire("Erro!", res.erro || res.mensagem, "error");
                    }
                    loadTable();
                },
                error: function(xhr, status, error) {
                    console.error("Erro na execução:", xhr.responseText);
                    console.error("Status:", status, "Error:", error);
                    
                    let errorMsg = "Falha na comunicação com o servidor";
                    if (xhr.responseText) {
                        try {
                            const jsonResponse = JSON.parse(xhr.responseText);
                            errorMsg = jsonResponse.erro || jsonResponse.mensagem || errorMsg;
                        } catch(e) {
                            errorMsg = xhr.responseText.substring(0, 200);
                        }
                    }
                    
                    Swal.fire({
                        title: "Erro!",
                        html: `<strong>Detalhes:</strong><br><small>${errorMsg}</small>`,
                        icon: "error"
                    });
                }
            });
}

function duplicarRotina(id) {
    Swal.fire({
        title: "Duplicar rotina?",
        text: "Será criada uma cópia desta rotina.",
        icon: "question",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sim, duplicar"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(baseUrl + "/rotinas/duplicar/" + id, {_csrf_token: csrfToken}, function(res) {
                if (res.sucesso) {
                    Swal.fire("Duplicada!", "Rotina duplicada com sucesso.", "success");
                    loadTable();
                } else {
                    Swal.fire("Erro!", res.erro || res.mensagem, "error");
                }
            }, "json");
        }
    });
}

function excluirRotina(id) {
    Swal.fire({
        title: "Excluir rotina?",
        text: "Esta ação não pode ser desfeita!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sim, excluir"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(baseUrl + "/rotinas/delete/" + id, {_csrf_token: csrfToken}, function(res) {
                Swal.fire("Excluída!", "Rotina removida com sucesso.", "success");
                loadTable();
            }, "json");
        }
    });
}

$(document).ready(function() {
    loadTable();
});
</script>
SCRIPTS;

include __DIR__ . '/layouts/base.php';
?>
