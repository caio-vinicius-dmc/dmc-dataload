<?php
$pageTitle = 'Backup & Restore';
$currentPage = 'backups';
$csrfToken = \App\Core\AuthMiddleware::gerarTokenCSRF();
ob_start();
?>

<div class="page-header-modern">
    <div class="page-icon-modern"><i class="bi bi-cloud-download"></i></div>
    <div>
        <h1 class="page-title-modern">Backup & Restore</h1>
        <p class="page-subtitle-modern">Backup completo e restauração do sistema</p>
    </div>
    <div class="ms-auto d-flex gap-2">
        <button class="btn btn-primary" onclick="criarBackup('completo')"><i class="bi bi-download me-2"></i>Backup Completo</button>
        <button class="btn btn-outline-primary" onclick="criarBackup('rotinas')"><i class="bi bi-arrow-repeat me-2"></i>Só Rotinas</button>
        <button class="btn btn-outline-warning" onclick="restaurarBackup()"><i class="bi bi-upload me-2"></i>Restaurar</button>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5 class="mb-0"><i class="bi bi-archive me-2"></i>Backups Disponíveis</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>Nome</th><th>Tipo</th><th>Tamanho</th><th>Status</th><th>Usuário</th><th>Data</th><th>Ações</th></tr></thead>
                <tbody id="tblBackups"></tbody>
            </table>
        </div>
    </div>
</div>

<input type="file" id="inputRestore" accept=".json" style="display:none">

<?php
$content = ob_get_clean();

$extraStyles = '<style>
.page-header-modern { background: white; padding: 1.75rem 2rem; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; }
.page-icon-modern { width: 70px; height: 70px; border-radius: 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: white; flex-shrink: 0; }
.page-title-modern { font-size: 2rem; font-weight: 700; margin: 0 0 0.25rem 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.page-subtitle-modern { color: #64748b; margin: 0; font-size: 1rem; }
</style>';

$extraScripts = '
<script>
const csrfToken = ' . json_encode($csrfToken) . ';

function formatBytes(bytes) {
    if (!bytes) return "-";
    const sizes = ["B", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return (bytes / Math.pow(1024, i)).toFixed(1) + " " + sizes[i];
}

function carregarBackups() {
    $.getJSON(baseUrl + "/api/backups/listar", function(res) {
        const tbody = $("#tblBackups");
        tbody.empty();
        (res.dados || []).forEach(function(b) {
            const data = new Date(b.criado_em).toLocaleString("pt-BR");
            const statusBadge = b.status === "concluido" ? `<span class="badge bg-success">Concluído</span>`
                : b.status === "gerando" ? `<span class="badge bg-primary">Gerando...</span>`
                : `<span class="badge bg-danger">Falha</span>`;
            const acoes = b.status === "concluido"
                ? `<div class="btn-group btn-group-sm">
                    <a href="${baseUrl}/api/backups/download/${b.id}" class="btn btn-outline-primary" title="Download"><i class="bi bi-download"></i></a>
                    <button class="btn btn-outline-danger" onclick="deletarBackup(${b.id})" title="Remover"><i class="bi bi-trash"></i></button>
                   </div>` : "";
            tbody.append(`<tr>
                <td><strong>${b.nome}</strong></td>
                <td><span class="badge bg-info">${b.tipo}</span></td>
                <td>${formatBytes(b.tamanho_bytes)}</td>
                <td>${statusBadge}</td>
                <td>${b.nome_usuario || "-"}</td>
                <td>${data}</td>
                <td>${acoes}</td>
            </tr>`);
        });
    });
}

function criarBackup(tipo) {
    Swal.fire({ title: "Criar backup?", text: "Tipo: " + tipo, icon: "question", showCancelButton: true, confirmButtonText: "Sim, criar" }).then(r => {
        if (r.isConfirmed) {
            Swal.fire({ title: "Gerando backup...", allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            $.post(baseUrl + "/api/backups/criar", { tipo: tipo, _csrf_token: csrfToken }, function(res) {
                Swal.fire(res.sucesso ? "Criado!" : "Erro", res.mensagem || res.erro, res.sucesso ? "success" : "error");
                carregarBackups();
            }, "json");
        }
    });
}

function restaurarBackup() {
    Swal.fire({
        title: "Restaurar Backup",
        html: "Selecione um arquivo .json de backup.<br><strong class=\\"text-danger\\">Os dados existentes podem ser afetados!</strong>",
        icon: "warning", showCancelButton: true, confirmButtonText: "Selecionar arquivo", confirmButtonColor: "#dc3545"
    }).then(r => { if (r.isConfirmed) document.getElementById("inputRestore").click(); });
}

document.getElementById("inputRestore").addEventListener("change", function() {
    if (!this.files[0]) return;
    const formData = new FormData();
    formData.append("arquivo", this.files[0]);
    formData.append("_csrf_token", csrfToken);
    Swal.fire({ title: "Restaurando...", allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    fetch(baseUrl + "/api/backups/restaurar", { method: "POST", body: formData })
        .then(r => r.json())
        .then(res => {
            Swal.fire(res.sucesso ? "Restaurado!" : "Erro", res.mensagem || res.erro, res.sucesso ? "success" : "error");
            carregarBackups();
        });
    this.value = "";
});

function deletarBackup(id) {
    Swal.fire({ title: "Remover backup?", icon: "warning", showCancelButton: true, confirmButtonColor: "#ef4444", confirmButtonText: "Sim, remover" }).then(r => {
        if (r.isConfirmed) {
            $.post(baseUrl + "/api/backups/delete/" + id, {_csrf_token: csrfToken}, function(res) {
                if (res.sucesso) { Swal.fire("Removido!", "", "success"); carregarBackups(); }
                else Swal.fire("Erro", res.erro, "error");
            }, "json");
        }
    });
}

$(document).ready(carregarBackups);
</script>';

include __DIR__ . '/../layouts/base.php';
?>
