<?php
$pageTitle = 'Fila de Execução';
$currentPage = 'fila';
$csrfToken = \App\Core\AuthMiddleware::gerarTokenCSRF();
ob_start();
?>

<div class="page-header-modern">
    <div class="page-icon-modern"><i class="bi bi-collection"></i></div>
    <div>
        <h1 class="page-title-modern">Fila de Execução</h1>
        <p class="page-subtitle-modern">Gerencie execuções em background</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-6 col-lg-2">
        <div class="stat-card warning">
            <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-value" id="statPendentes">0</div>
            <div class="stat-label">Pendentes</div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card primary">
            <div class="stat-icon"><i class="bi bi-gear-wide-connected"></i></div>
            <div class="stat-value" id="statProcessando">0</div>
            <div class="stat-label">Processando</div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card success">
            <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
            <div class="stat-value" id="statConcluidos">0</div>
            <div class="stat-label">Concluídos</div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card danger">
            <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="stat-value" id="statFalhas">0</div>
            <div class="stat-label">Falhas</div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card secondary">
            <div class="stat-icon"><i class="bi bi-x-circle"></i></div>
            <div class="stat-value" id="statCancelados">0</div>
            <div class="stat-label">Cancelados</div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card info">
            <div class="stat-icon"><i class="bi bi-collection"></i></div>
            <div class="stat-value" id="statTotal">0</div>
            <div class="stat-label">Total</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-list-task me-2"></i>Itens na Fila</h5>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" id="filtroStatus" style="width:auto">
                <option value="">Todos</option>
                <option value="pendente">Pendentes</option>
                <option value="processando">Processando</option>
                <option value="concluido">Concluídos</option>
                <option value="falha">Falhas</option>
                <option value="cancelado">Cancelados</option>
            </select>
            <button class="btn btn-sm btn-outline-primary" onclick="carregarFila()"><i class="bi bi-arrow-clockwise"></i></button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tblFila">
                <thead><tr>
                    <th>#</th><th>Tipo</th><th>Recurso</th><th>Status</th><th>Tentativas</th>
                    <th>Usuário</th><th>Criado em</th><th>Ações</th>
                </tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$extraStyles = '<style>
.stat-card .stat-value { font-size: 1.75rem; }
.page-header-modern { background: white; padding: 1.75rem 2rem; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; }
.page-icon-modern { width: 70px; height: 70px; border-radius: 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: white; flex-shrink: 0; }
.page-title-modern { font-size: 2rem; font-weight: 700; margin: 0 0 0.25rem 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.page-subtitle-modern { color: #64748b; margin: 0; font-size: 1rem; }
</style>';

$extraScripts = '
<script>
const csrfToken = ' . json_encode($csrfToken) . ';

const statusBadge = {
    pendente: `<span class="badge bg-warning text-dark">Pendente</span>`,
    processando: `<span class="badge bg-primary">Processando</span>`,
    concluido: `<span class="badge bg-success">Concluído</span>`,
    falha: `<span class="badge bg-danger">Falha</span>`,
    cancelado: `<span class="badge bg-secondary">Cancelado</span>`
};

function carregarStats() {
    $.getJSON(baseUrl + "/api/fila/stats", function(res) {
        if (res.sucesso) {
            const d = res.dados;
            $("#statPendentes").text(d.pendentes);
            $("#statProcessando").text(d.processando);
            $("#statConcluidos").text(d.concluidos);
            $("#statFalhas").text(d.falhas);
            $("#statCancelados").text(d.cancelados);
            $("#statTotal").text(d.total);
        }
    });
}

function carregarFila() {
    const status = $("#filtroStatus").val();
    let url = baseUrl + "/api/fila/listar";
    if (status) url += "?status=" + status;
    $.getJSON(url, function(res) {
        const tbody = $("#tblFila tbody");
        tbody.empty();
        (res.dados || []).forEach(function(item) {
            const criado = new Date(item.criado_em).toLocaleString("pt-BR");
            const acoes = item.status === "pendente"
                ? `<button class="btn btn-sm btn-outline-danger" onclick="cancelarItem(${item.id})"><i class="bi bi-x-circle"></i></button>`
                : "";
            tbody.append(`<tr>
                <td>${item.id}</td>
                <td><span class="badge bg-info">${item.tipo}</span></td>
                <td>${item.nome_recurso || "ID: " + item.id_recurso}</td>
                <td>${statusBadge[item.status] || item.status}</td>
                <td>${item.tentativas}/${item.max_tentativas}</td>
                <td>${item.nome_usuario || "-"}</td>
                <td>${criado}</td>
                <td>${acoes}</td>
            </tr>`);
        });
    });
    carregarStats();
}

function cancelarItem(id) {
    Swal.fire({ title: "Cancelar execução?", icon: "warning", showCancelButton: true, confirmButtonText: "Sim, cancelar" }).then(r => {
        if (r.isConfirmed) {
            $.post(baseUrl + "/api/fila/cancelar/" + id, {_csrf_token: csrfToken}, function(res) {
                Swal.fire(res.sucesso ? "Cancelado!" : "Erro", res.mensagem || res.erro, res.sucesso ? "success" : "error");
                carregarFila();
            }, "json");
        }
    });
}

$("#filtroStatus").on("change", carregarFila);
$(document).ready(function() { carregarFila(); setInterval(carregarStats, 10000); });
</script>';

include __DIR__ . '/../layouts/base.php';
?>
