<?php
$pageTitle = 'Fila de Execução';
$currentPage = 'fila';
$csrfToken = \App\Core\AuthMiddleware::gerarTokenCSRF();
ob_start();
?>

<div class="page-header-modern">
    <div class="page-icon-modern" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
        <i class="bi bi-collection"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Fila de Execução</h1>
        <p class="page-subtitle-modern">Gerencie execuções em background</p>
    </div>
    <div class="ms-auto">
        <button class="btn btn-outline-primary btn-sm" onclick="carregarFila()"><i class="bi bi-arrow-clockwise me-1"></i>Atualizar</button>
    </div>
</div>

<div class="row g-3 mb-4">
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

<div class="card-modern">
    <div class="card-modern-header">
        <i class="bi bi-list-task me-2"></i>
        <span>Itens na Fila</span>
        <div class="ms-auto">
            <select class="form-select form-select-sm" id="filtroStatus" style="width: auto; min-width: 150px;">
                <option value="">Todos os status</option>
                <option value="pendente">Pendentes</option>
                <option value="processando">Processando</option>
                <option value="concluido">Concluídos</option>
                <option value="falha">Falhas</option>
                <option value="cancelado">Cancelados</option>
            </select>
        </div>
    </div>
    <div class="card-modern-body p-0">
        <div class="table-responsive">
            <table class="table-modern" id="tblFila">
                <thead><tr>
                    <th>#</th><th>Tipo</th><th>Recurso</th><th>Status</th><th>Tentativas</th>
                    <th>Usuário</th><th>Criado em</th><th width="80">Ações</th>
                </tr></thead>
                <tbody>
                    <tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> <span class="ms-2">Carregando...</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$extraStyles = '';

$extraScripts = '
<script>
const csrfToken = ' . json_encode($csrfToken) . ';

var statusBadge = {
    pendente: \'<span class="badge bg-warning text-dark">Pendente</span>\',
    processando: \'<span class="badge bg-primary">Processando</span>\',
    concluido: \'<span class="badge bg-success">Concluído</span>\',
    falha: \'<span class="badge bg-danger">Falha</span>\',
    cancelado: \'<span class="badge bg-secondary">Cancelado</span>\'
};

function carregarStats() {
    $.ajax({
        url: baseUrl + "/api/fila/stats",
        type: "GET",
        dataType: "json",
        cache: false,
        success: function(res) {
            if (res.sucesso) {
                var d = res.dados;
                $("#statPendentes").text(d.pendentes || 0);
                $("#statProcessando").text(d.processando || 0);
                $("#statConcluidos").text(d.concluidos || 0);
                $("#statFalhas").text(d.falhas || 0);
                $("#statCancelados").text(d.cancelados || 0);
                $("#statTotal").text(d.total || 0);
            }
        },
        error: function(xhr) {
            console.error("Erro ao carregar stats da fila:", xhr.status, xhr.responseText);
        }
    });
}

function carregarFila() {
    var tbody = $("#tblFila tbody");
    tbody.html(\'<tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> <span class="ms-2">Carregando...</span></td></tr>\');
    var status = $("#filtroStatus").val();
    var url = baseUrl + "/api/fila/listar";
    if (status) url += "?status=" + encodeURIComponent(status);
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        cache: false,
        success: function(res) {
            tbody.empty();
            var dados = res.dados || [];
            if (dados.length === 0) {
                tbody.html(\'<tr><td colspan="8" class="text-center py-4 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Nenhum item na fila</td></tr>\');
                return;
            }
            dados.forEach(function(item) {
                var criado = new Date(item.criado_em).toLocaleString("pt-BR");
                var acoes = item.status === "pendente"
                    ? \'<button class="btn btn-sm btn-outline-danger" onclick="cancelarItem(\' + item.id + \')" title="Cancelar"><i class="bi bi-x-circle"></i></button>\'
                    : \'<span class="text-muted">-</span>\';
                tbody.append(\'<tr>\' +
                    \'<td><strong>\' + item.id + \'</strong></td>\' +
                    \'<td><span class="badge bg-info">\' + $("<span>").text(item.tipo).html() + \'</span></td>\' +
                    \'<td>\' + $("<span>").text(item.nome_recurso || "ID: " + item.id_recurso).html() + \'</td>\' +
                    \'<td>\' + (statusBadge[item.status] || item.status) + \'</td>\' +
                    \'<td>\' + item.tentativas + \'/\' + item.max_tentativas + \'</td>\' +
                    \'<td>\' + $("<span>").text(item.nome_usuario || "-").html() + \'</td>\' +
                    \'<td><small>\' + criado + \'</small></td>\' +
                    \'<td>\' + acoes + \'</td>\' +
                \'</tr>\');
            });
        },
        error: function(xhr) {
            console.error("Erro ao carregar fila:", xhr.status, xhr.responseText);
            tbody.html(\'<tr><td colspan="8" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i>Erro ao carregar dados<br><small class="text-muted">Status: \' + xhr.status + \' — \' + (xhr.responseJSON ? xhr.responseJSON.erro : xhr.statusText) + \'</small></td></tr>\');
        }
    });
    carregarStats();
}

function cancelarItem(id) {
    Swal.fire({ title: "Cancelar execução?", text: "O item será removido da fila.", icon: "warning", showCancelButton: true, confirmButtonText: "Sim, cancelar", cancelButtonText: "Não" }).then(function(r) {
        if (r.isConfirmed) {
            $.post(baseUrl + "/api/fila/cancelar/" + id, {_csrf_token: csrfToken}, function(res) {
                Swal.fire(res.sucesso ? "Cancelado!" : "Erro", res.mensagem || res.erro, res.sucesso ? "success" : "error");
                carregarFila();
            }, "json").fail(function(xhr) {
                console.error("Erro ao cancelar item:", xhr.status, xhr.responseText);
                Swal.fire("Erro", "Falha na comunicação", "error");
            });
        }
    });
}

$("#filtroStatus").on("change", carregarFila);
$(document).ready(function() {
    console.log("[Fila] Página carregada, baseUrl:", baseUrl);
    carregarFila();
    setInterval(carregarStats, 10000);
});
</script>';

include __DIR__ . '/../layouts/base.php';
?>
