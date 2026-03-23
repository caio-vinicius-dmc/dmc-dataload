<?php
$pageTitle = 'Backup & Restore';
$currentPage = 'backups';
$csrfToken = \App\Core\AuthMiddleware::gerarTokenCSRF();
ob_start();
?>

<div class="page-header-modern">
    <div class="page-icon-modern" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
        <i class="bi bi-cloud-download"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Backup & Restore</h1>
        <p class="page-subtitle-modern">Backup completo e restauração do sistema</p>
    </div>
    <div class="ms-auto d-flex gap-2 flex-wrap">
        <button class="btn btn-primary" onclick="criarBackup('completo')"><i class="bi bi-download me-2"></i>Backup Completo</button>
        <button class="btn btn-outline-primary" onclick="criarBackup('rotinas')"><i class="bi bi-arrow-repeat me-2"></i>Só Rotinas</button>
        <button class="btn btn-outline-warning" onclick="restaurarBackup()"><i class="bi bi-upload me-2"></i>Restaurar</button>
    </div>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-6 col-lg-4">
        <div class="stat-card primary">
            <div class="stat-icon"><i class="bi bi-archive"></i></div>
            <div class="stat-value" id="statTotal">0</div>
            <div class="stat-label">Total de Backups</div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="stat-card success">
            <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
            <div class="stat-value" id="statConcluidos">0</div>
            <div class="stat-label">Concluídos</div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="stat-card info">
            <div class="stat-icon"><i class="bi bi-hdd"></i></div>
            <div class="stat-value" id="statTamanho">0</div>
            <div class="stat-label">Tamanho Total</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-archive me-2"></i>Backups Disponíveis</h5>
        <button class="btn btn-sm btn-outline-primary" onclick="carregarBackups()"><i class="bi bi-arrow-clockwise"></i></button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tblBackups">
                <thead><tr><th>Nome</th><th>Tipo</th><th>Tamanho</th><th>Status</th><th>Usuário</th><th>Data</th><th width="100">Ações</th></tr></thead>
                <tbody>
                    <tr><td colspan="7" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> <span class="ms-2">Carregando...</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<input type="file" id="inputRestore" accept=".json" style="display:none">

<?php
$content = ob_get_clean();

$extraScripts = '
<script>
const csrfToken = ' . json_encode($csrfToken) . ';

function formatBytes(bytes) {
    if (!bytes) return "-";
    var sizes = ["B", "KB", "MB", "GB"];
    var i = Math.floor(Math.log(bytes) / Math.log(1024));
    return (bytes / Math.pow(1024, i)).toFixed(1) + " " + sizes[i];
}

function atualizarStats(dados) {
    var total = dados.length, concluidos = 0, tamanhoTotal = 0;
    dados.forEach(function(b) {
        if (b.status === "concluido") concluidos++;
        tamanhoTotal += parseInt(b.tamanho_bytes || 0);
    });
    $("#statTotal").text(total);
    $("#statConcluidos").text(concluidos);
    $("#statTamanho").text(formatBytes(tamanhoTotal));
}

function carregarBackups() {
    var tbody = $("#tblBackups tbody");
    tbody.html(\'<tr><td colspan="7" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> <span class="ms-2">Carregando...</span></td></tr>\');
    $.ajax({
        url: baseUrl + "/api/backups/listar",
        type: "GET",
        dataType: "json",
        cache: false,
        success: function(res) {
            tbody.empty();
            var dados = res.dados || [];
            atualizarStats(dados);
            if (dados.length === 0) {
                tbody.html(\'<tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-archive fs-3 d-block mb-2"></i>Nenhum backup encontrado</td></tr>\');
                return;
            }
            dados.forEach(function(b) {
                var data = new Date(b.criado_em).toLocaleString("pt-BR");
                var sBadge = b.status === "concluido" ? \'<span class="badge bg-success">Concluído</span>\'
                    : b.status === "gerando" ? \'<span class="badge bg-primary">Gerando...</span>\'
                    : \'<span class="badge bg-danger">Falha</span>\';
                var acoes = b.status === "concluido"
                    ? \'<div class="btn-group btn-group-sm">\' +
                        \'<a href="\' + baseUrl + \'/api/backups/download/\' + b.id + \'" class="btn btn-outline-primary" title="Download"><i class="bi bi-download"></i></a>\' +
                        \'<button class="btn btn-outline-danger" onclick="deletarBackup(\' + b.id + \')" title="Remover"><i class="bi bi-trash"></i></button>\' +
                      \'</div>\' : \'<span class="text-muted">-</span>\';
                tbody.append(\'<tr>\' +
                    \'<td><strong>\' + $("<span>").text(b.nome).html() + \'</strong></td>\' +
                    \'<td><span class="badge bg-info">\' + $("<span>").text(b.tipo).html() + \'</span></td>\' +
                    \'<td>\' + formatBytes(b.tamanho_bytes) + \'</td>\' +
                    \'<td>\' + sBadge + \'</td>\' +
                    \'<td>\' + $("<span>").text(b.nome_usuario || "-").html() + \'</td>\' +
                    \'<td><small>\' + data + \'</small></td>\' +
                    \'<td>\' + acoes + \'</td>\' +
                \'</tr>\');
            });
        },
        error: function(xhr) {
            console.error("Erro ao carregar backups:", xhr.status, xhr.responseText);
            tbody.html(\'<tr><td colspan="7" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i>Erro ao carregar dados<br><small class="text-muted">Status: \' + xhr.status + \' — \' + (xhr.responseJSON ? xhr.responseJSON.erro : xhr.statusText) + \'</small></td></tr>\');
        }
    });
}

function criarBackup(tipo) {
    Swal.fire({ title: "Criar backup?", text: "Tipo: " + tipo, icon: "question", showCancelButton: true, confirmButtonText: "Sim, criar", cancelButtonText: "Cancelar" }).then(function(r) {
        if (r.isConfirmed) {
            Swal.fire({ title: "Gerando backup...", text: "Aguarde, isso pode levar alguns instantes.", allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
            $.post(baseUrl + "/api/backups/criar", { tipo: tipo, _csrf_token: csrfToken }, function(res) {
                Swal.fire(res.sucesso ? "Criado!" : "Erro", res.mensagem || res.erro, res.sucesso ? "success" : "error");
                carregarBackups();
            }, "json").fail(function(xhr) {
                console.error("Erro ao criar backup:", xhr.status, xhr.responseText);
                Swal.fire("Erro", "Falha na comunicação com o servidor", "error");
            });
        }
    });
}

function restaurarBackup() {
    Swal.fire({
        title: "Restaurar Backup",
        html: \'Selecione um arquivo <strong>.json</strong> de backup.<br><span class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Os dados existentes podem ser afetados!</span>\',
        icon: "warning", showCancelButton: true, confirmButtonText: "Selecionar arquivo", confirmButtonColor: "#dc3545", cancelButtonText: "Cancelar"
    }).then(function(r) { if (r.isConfirmed) document.getElementById("inputRestore").click(); });
}

document.getElementById("inputRestore").addEventListener("change", function() {
    if (!this.files[0]) return;
    var formData = new FormData();
    formData.append("arquivo", this.files[0]);
    formData.append("_csrf_token", csrfToken);
    Swal.fire({ title: "Restaurando...", text: "Aguarde, isso pode levar alguns instantes.", allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
    fetch(baseUrl + "/api/backups/restaurar", { method: "POST", body: formData })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            Swal.fire(res.sucesso ? "Restaurado!" : "Erro", res.mensagem || res.erro, res.sucesso ? "success" : "error");
            carregarBackups();
        })
        .catch(function(err) {
            console.error("Erro ao restaurar:", err);
            Swal.fire("Erro", "Falha na comunicação com o servidor", "error");
        });
    this.value = "";
});

function deletarBackup(id) {
    Swal.fire({ title: "Remover backup?", text: "Esta ação não pode ser desfeita.", icon: "warning", showCancelButton: true, confirmButtonColor: "#ef4444", confirmButtonText: "Sim, remover", cancelButtonText: "Cancelar" }).then(function(r) {
        if (r.isConfirmed) {
            $.post(baseUrl + "/api/backups/delete/" + id, {_csrf_token: csrfToken}, function(res) {
                if (res.sucesso) { Swal.fire("Removido!", "", "success"); carregarBackups(); }
                else Swal.fire("Erro", res.erro, "error");
            }, "json").fail(function(xhr) {
                console.error("Erro ao deletar backup:", xhr.status, xhr.responseText);
                Swal.fire("Erro", "Falha na comunicação", "error");
            });
        }
    });
}

$(document).ready(function() {
    console.log("[Backups] Página carregada, baseUrl:", baseUrl);
    carregarBackups();
});
</script>';

include __DIR__ . '/../layouts/base.php';
?>
