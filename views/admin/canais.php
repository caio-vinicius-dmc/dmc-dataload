<?php
$pageTitle = 'Canais de Notificação';
$currentPage = 'canais';
$csrfToken = \App\Core\AuthMiddleware::gerarTokenCSRF();
ob_start();
?>

<div class="page-header-modern">
    <div class="page-icon-modern"><i class="bi bi-chat-dots"></i></div>
    <div>
        <h1 class="page-title-modern">Canais de Notificação</h1>
        <p class="page-subtitle-modern">Configure Slack, Teams e Discord</p>
    </div>
    <div class="ms-auto">
        <button class="btn btn-primary" onclick="novoCanal()"><i class="bi bi-plus-lg me-2"></i>Novo Canal</button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>Nome</th><th>Tipo</th><th>Canal</th><th>Status</th><th>Eventos</th><th>Ações</th></tr></thead>
                <tbody id="tblCanais"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Canal -->
<div class="modal fade" id="modalCanal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="modalCanalTitle">Novo Canal</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="formCanal">
                <input type="hidden" name="id" id="canalId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome *</label>
                        <input type="text" class="form-control" name="nome" id="canalNome" required placeholder="Ex: Alertas Produção">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo *</label>
                        <select class="form-select" name="tipo" id="canalTipo" required>
                            <option value="slack">Slack</option>
                            <option value="teams">Microsoft Teams</option>
                            <option value="discord">Discord</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Webhook URL *</label>
                        <input type="url" class="form-control" name="webhook_url" id="canalWebhookUrl" required placeholder="https://hooks.slack.com/services/...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Canal/Sala</label>
                        <input type="text" class="form-control" name="canal" id="canalCanal" placeholder="#alertas">
                    </div>
                    <hr>
                    <h6>Notificar quando:</h6>
                    <div class="form-check"><input type="checkbox" class="form-check-input" name="notificar_falha" id="chkFalha" checked><label class="form-check-label" for="chkFalha">Falha na execução</label></div>
                    <div class="form-check"><input type="checkbox" class="form-check-input" name="notificar_sucesso" id="chkSucesso"><label class="form-check-label" for="chkSucesso">Sucesso na execução</label></div>
                    <div class="form-check"><input type="checkbox" class="form-check-input" name="notificar_inicio" id="chkInicio"><label class="form-check-label" for="chkInicio">Início da execução</label></div>
                    <div class="form-check mt-2"><input type="checkbox" class="form-check-input" name="ativo" id="chkAtivo" checked><label class="form-check-label" for="chkAtivo">Ativo</label></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-2"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

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

const tipoIcons = { slack: "bi-slack", teams: "bi-microsoft-teams", discord: "bi-discord" };

function carregarCanais() {
    $.getJSON(baseUrl + "/api/canais/listar", function(res) {
        const tbody = $("#tblCanais");
        tbody.empty();
        (res.dados || []).forEach(function(c) {
            const eventos = [];
            if (c.notificar_falha) eventos.push(`<span class="badge bg-danger">Falha</span>`);
            if (c.notificar_sucesso) eventos.push(`<span class="badge bg-success">Sucesso</span>`);
            if (c.notificar_inicio) eventos.push(`<span class="badge bg-warning text-dark">Início</span>`);
            tbody.append(`<tr>
                <td><strong>${c.nome}</strong></td>
                <td><i class="bi ${tipoIcons[c.tipo] || "bi-globe"} me-1"></i>${c.tipo}</td>
                <td>${c.canal || "-"}</td>
                <td>${c.ativo ? `<span class="badge bg-success">Ativo</span>` : `<span class="badge bg-secondary">Inativo</span>`}</td>
                <td>${eventos.join(" ")}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-info" onclick="testarCanal(${c.id})" title="Testar"><i class="bi bi-send"></i></button>
                        <button class="btn btn-outline-primary" onclick="editarCanal(${c.id})" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-outline-danger" onclick="deletarCanal(${c.id})" title="Remover"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>`);
        });
    });
}

function novoCanal() {
    document.getElementById("formCanal").reset();
    document.getElementById("canalId").value = "";
    document.getElementById("modalCanalTitle").textContent = "Novo Canal";
    document.getElementById("chkFalha").checked = true;
    document.getElementById("chkAtivo").checked = true;
    bootstrap.Modal.getOrCreateInstance(document.getElementById("modalCanal")).show();
}

function editarCanal(id) {
    $.getJSON(baseUrl + "/api/canais/listar", function(res) {
        const canal = (res.dados || []).find(c => c.id == id);
        if (!canal) return;
        document.getElementById("canalId").value = canal.id;
        document.getElementById("canalNome").value = canal.nome;
        document.getElementById("canalTipo").value = canal.tipo;
        document.getElementById("canalWebhookUrl").value = canal.webhook_url;
        document.getElementById("canalCanal").value = canal.canal || "";
        document.getElementById("chkFalha").checked = canal.notificar_falha;
        document.getElementById("chkSucesso").checked = canal.notificar_sucesso;
        document.getElementById("chkInicio").checked = canal.notificar_inicio;
        document.getElementById("chkAtivo").checked = canal.ativo;
        document.getElementById("modalCanalTitle").textContent = "Editar Canal";
        bootstrap.Modal.getOrCreateInstance(document.getElementById("modalCanal")).show();
    });
}

function testarCanal(id) {
    $.post(baseUrl + "/api/canais/testar/" + id, {_csrf_token: csrfToken}, function(res) {
        Swal.fire(res.sucesso ? "Enviado!" : "Erro", res.mensagem || res.erro, res.sucesso ? "success" : "error");
    }, "json");
}

function deletarCanal(id) {
    Swal.fire({ title: "Remover canal?", icon: "warning", showCancelButton: true, confirmButtonColor: "#ef4444", confirmButtonText: "Sim, remover" }).then(r => {
        if (r.isConfirmed) {
            $.post(baseUrl + "/api/canais/delete/" + id, {_csrf_token: csrfToken}, function(res) {
                if (res.sucesso) { Swal.fire("Removido!", "", "success"); carregarCanais(); }
                else Swal.fire("Erro", res.erro, "error");
            }, "json");
        }
    });
}

document.getElementById("formCanal").addEventListener("submit", function(e) {
    e.preventDefault();
    const data = $(this).serialize() + "&_csrf_token=" + encodeURIComponent(csrfToken);
    $.post(baseUrl + "/api/canais/salvar", data, function(res) {
        if (res.sucesso) { bootstrap.Modal.getOrCreateInstance(document.getElementById("modalCanal")).hide(); Swal.fire("Salvo!", "", "success"); carregarCanais(); }
        else Swal.fire("Erro", res.erro, "error");
    }, "json");
});

$(document).ready(carregarCanais);
</script>';

include __DIR__ . '/../layouts/base.php';
?>
