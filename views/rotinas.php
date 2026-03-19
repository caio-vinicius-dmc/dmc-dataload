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
        <p class="page-subtitle-modern">Gerencie as rotinas de ETL e scripts SQL</p>
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
                <div class="stat-label-modern">Rotinas Ativas</div>
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
                    ? `<span class="badge-status badge-success"><i class="bi bi-check-circle me-1"></i>Ativa</span>`
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
        const nome = dados.nome || "Rotina #" + id;
        const tipo = dados.tipo_execucao || dados.tipo || "N/A";
        const blocos = dados.blocos ? dados.blocos.length : "?";
        
        Swal.fire({
            title: "Confirmar execução",
            html: `<div class="text-start">
                <p><strong>Rotina:</strong> ${nome}</p>
                <p><strong>Tipo:</strong> ${tipo}</p>
                <p><strong>Blocos SQL:</strong> ${blocos}</p>
                <hr>
                <p class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>
                A rotina será executada <strong>imediatamente</strong> no banco de dados de destino.</p>
            </div>`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#10b981",
            cancelButtonText: "Cancelar",
            confirmButtonText: "<i class='bi bi-play-fill me-1'></i>Executar agora"
        }).then((result) => {
            if (result.isConfirmed) {
                executarRotinaConfirmada(id);
            }
        });
    }).fail(function() {
        // Fallback: confirmação simples se não conseguir buscar detalhes
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
                executarRotinaConfirmada(id);
            }
        });
    });
}

function executarRotinaConfirmada(id) {
    Swal.fire({
        title: "Executando...",
        html: `<div class="my-3"><div class="spinner-border text-primary"></div></div>
               <p class="text-muted">Aguarde a conclusão da rotina</p>`,
        allowOutsideClick: false,
        showConfirmButton: false
    });
    
    $.ajax({
        url: baseUrl + "/rotinas/run/" + id,
        type: "POST",
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
