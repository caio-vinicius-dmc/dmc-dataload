<?php
$pageTitle = 'Canais de Notificação';
$currentPage = 'canais';
$csrfToken = \App\Core\AuthMiddleware::gerarTokenCSRF();
ob_start();
?>

<div class="page-header-modern">
    <div class="page-icon-modern" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <i class="bi bi-chat-dots"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Canais de Notificação</h1>
        <p class="page-subtitle-modern">Configure Slack, Teams e Discord</p>
    </div>
    <div class="ms-auto">
        <button class="btn btn-primary" onclick="novoCanal()"><i class="bi bi-plus-lg me-2"></i>Novo Canal</button>
    </div>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card primary">
            <div class="stat-icon"><i class="bi bi-hash"></i></div>
            <div class="stat-value" id="statTotal">0</div>
            <div class="stat-label">Total de Canais</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card success">
            <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
            <div class="stat-value" id="statAtivos">0</div>
            <div class="stat-label">Ativos</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card secondary">
            <div class="stat-icon"><i class="bi bi-pause-circle"></i></div>
            <div class="stat-value" id="statInativos">0</div>
            <div class="stat-label">Inativos</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card warning">
            <div class="stat-icon"><i class="bi bi-send"></i></div>
            <div class="stat-value" id="statEventos">0</div>
            <div class="stat-label">Eventos Monitorados</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-broadcast me-2"></i>Canais Configurados</h5>
        <button class="btn btn-sm btn-outline-primary" onclick="carregarCanais()"><i class="bi bi-arrow-clockwise"></i></button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tblCanais">
                <thead><tr><th>Nome</th><th>Tipo</th><th>Canal</th><th>Status</th><th>Eventos</th><th width="130">Ações</th></tr></thead>
                <tbody>
                    <tr><td colspan="6" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> <span class="ms-2">Carregando...</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Canal -->
<div class="modal fade" id="modalCanal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCanalTitle">Novo Canal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCanal">
                <input type="hidden" name="id" id="canalId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome *</label>
                        <input type="text" class="form-control" name="nome" id="canalNome" required placeholder="Ex: Alertas Produção">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo *</label>
                        <select class="form-select" name="tipo" id="canalTipo" required>
                            <option value="slack">Slack</option>
                            <option value="teams">Microsoft Teams</option>
                            <option value="discord">Discord</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Webhook URL *</label>
                        <input type="url" class="form-control" name="webhook_url" id="canalWebhookUrl" required placeholder="https://hooks.slack.com/services/...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Canal/Sala</label>
                        <input type="text" class="form-control" name="canal" id="canalCanal" placeholder="#alertas">
                    </div>
                    <hr>
                    <h6 class="fw-semibold mb-3">Notificar quando:</h6>
                    <div class="form-check mb-2"><input type="checkbox" class="form-check-input" name="notificar_falha" id="chkFalha" checked><label class="form-check-label" for="chkFalha">Falha na execução</label></div>
                    <div class="form-check mb-2"><input type="checkbox" class="form-check-input" name="notificar_sucesso" id="chkSucesso"><label class="form-check-label" for="chkSucesso">Sucesso na execução</label></div>
                    <div class="form-check mb-2"><input type="checkbox" class="form-check-input" name="notificar_inicio" id="chkInicio"><label class="form-check-label" for="chkInicio">Início da execução</label></div>
                    <hr>
                    <div class="form-check form-switch"><input type="checkbox" class="form-check-input" name="ativo" id="chkAtivo" checked><label class="form-check-label fw-semibold" for="chkAtivo">Canal Ativo</label></div>
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

$extraScripts = '
<script>
const csrfToken = ' . json_encode($csrfToken) . ';

const tipoIcons = { slack: "bi-slack", teams: "bi-microsoft-teams", discord: "bi-discord" };
const tipoBadge = { slack: "bg-success", teams: "bg-primary", discord: "bg-info" };

function atualizarStats(canais) {
    var total = canais.length, ativos = 0, inativos = 0, eventos = 0;
    canais.forEach(function(c) {
        if (c.ativo) ativos++; else inativos++;
        if (c.notificar_falha) eventos++;
        if (c.notificar_sucesso) eventos++;
        if (c.notificar_inicio) eventos++;
    });
    $("#statTotal").text(total);
    $("#statAtivos").text(ativos);
    $("#statInativos").text(inativos);
    $("#statEventos").text(eventos);
}

function carregarCanais() {
    var tbody = $("#tblCanais tbody");
    tbody.html(\'<tr><td colspan="6" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> <span class="ms-2">Carregando...</span></td></tr>\');
    $.ajax({
        url: baseUrl + "/api/canais/listar",
        type: "GET",
        dataType: "json",
        cache: false,
        success: function(res) {
            tbody.empty();
            var dados = res.dados || [];
            atualizarStats(dados);
            if (dados.length === 0) {
                tbody.html(\'<tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-chat-dots fs-3 d-block mb-2"></i>Nenhum canal configurado</td></tr>\');
                return;
            }
            dados.forEach(function(c) {
                var eventos = [];
                if (c.notificar_falha) eventos.push(\'<span class="badge bg-danger">Falha</span>\');
                if (c.notificar_sucesso) eventos.push(\'<span class="badge bg-success">Sucesso</span>\');
                if (c.notificar_inicio) eventos.push(\'<span class="badge bg-warning text-dark">Início</span>\');
                var nome = $("<span>").text(c.nome).html();
                var canal = $("<span>").text(c.canal || "-").html();
                var tipoClass = tipoBadge[c.tipo] || "bg-secondary";
                var tipoIcon = tipoIcons[c.tipo] || "bi-globe";
                var statusHtml = c.ativo ? \'<span class="badge bg-success">Ativo</span>\' : \'<span class="badge bg-secondary">Inativo</span>\';
                var eventosHtml = eventos.length > 0 ? eventos.join(" ") : \'<span class="text-muted">-</span>\';
                tbody.append(
                    \'<tr>\' +
                    \'<td><strong>\' + nome + \'</strong></td>\' +
                    \'<td><span class="badge \' + tipoClass + \'"><i class="bi \' + tipoIcon + \' me-1"></i>\' + c.tipo + \'</span></td>\' +
                    \'<td><code>\' + canal + \'</code></td>\' +
                    \'<td>\' + statusHtml + \'</td>\' +
                    \'<td>\' + eventosHtml + \'</td>\' +
                    \'<td><div class="btn-group btn-group-sm">\' +
                        \'<button class="btn btn-outline-info" onclick="testarCanal(\' + c.id + \')" title="Testar"><i class="bi bi-send"></i></button>\' +
                        \'<button class="btn btn-outline-primary" onclick="editarCanal(\' + c.id + \')" title="Editar"><i class="bi bi-pencil"></i></button>\' +
                        \'<button class="btn btn-outline-danger" onclick="deletarCanal(\' + c.id + \')" title="Remover"><i class="bi bi-trash"></i></button>\' +
                    \'</div></td>\' +
                    \'</tr>\'
                );
            });
        },
        error: function(xhr) {
            console.error("Erro ao carregar canais:", xhr.status, xhr.responseText);
            tbody.html(\'<tr><td colspan="6" class="text-center py-4 text-danger"><i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i>Erro ao carregar dados<br><small class="text-muted">Status: \' + xhr.status + \' — \' + (xhr.responseJSON ? xhr.responseJSON.erro : xhr.statusText) + \'</small></td></tr>\');
        }
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
    $.ajax({
        url: baseUrl + "/api/canais/listar",
        type: "GET",
        dataType: "json",
        cache: false,
        success: function(res) {
            var canal = (res.dados || []).find(function(c) { return c.id == id; });
            if (!canal) { Swal.fire("Erro", "Canal não encontrado", "error"); return; }
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
        },
        error: function(xhr) {
            console.error("Erro ao buscar canal:", xhr.status, xhr.responseText);
            Swal.fire("Erro", "Falha ao buscar dados do canal", "error");
        }
    });
}

function testarCanal(id) {
    Swal.fire({ title: "Enviando mensagem de teste...", allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
    $.post(baseUrl + "/api/canais/testar/" + id, {_csrf_token: csrfToken}, function(res) {
        Swal.fire(res.sucesso ? "Enviado!" : "Erro", res.mensagem || res.erro, res.sucesso ? "success" : "error");
    }, "json").fail(function(xhr) {
        console.error("Erro ao testar canal:", xhr.status, xhr.responseText);
        Swal.fire("Erro", "Falha na comunicação com o servidor", "error");
    });
}

function deletarCanal(id) {
    Swal.fire({ title: "Remover canal?", text: "Esta ação não pode ser desfeita.", icon: "warning", showCancelButton: true, confirmButtonColor: "#ef4444", confirmButtonText: "Sim, remover", cancelButtonText: "Cancelar" }).then(function(r) {
        if (r.isConfirmed) {
            $.post(baseUrl + "/api/canais/delete/" + id, {_csrf_token: csrfToken}, function(res) {
                if (res.sucesso) { Swal.fire("Removido!", "", "success"); carregarCanais(); }
                else Swal.fire("Erro", res.erro, "error");
            }, "json").fail(function(xhr) {
                console.error("Erro ao deletar canal:", xhr.status, xhr.responseText);
                Swal.fire("Erro", "Falha na comunicação com o servidor", "error");
            });
        }
    });
}

document.getElementById("formCanal").addEventListener("submit", function(e) {
    e.preventDefault();
    var data = $(this).serialize() + "&_csrf_token=" + encodeURIComponent(csrfToken);
    $.post(baseUrl + "/api/canais/salvar", data, function(res) {
        if (res.sucesso) { bootstrap.Modal.getOrCreateInstance(document.getElementById("modalCanal")).hide(); Swal.fire("Salvo!", "", "success"); carregarCanais(); }
        else Swal.fire("Erro", res.erro, "error");
    }, "json").fail(function(xhr) {
        console.error("Erro ao salvar canal:", xhr.status, xhr.responseText);
        Swal.fire("Erro", "Falha na comunicação com o servidor", "error");
    });
});

$(document).ready(function() {
    console.log("[Canais] Página carregada, baseUrl:", baseUrl);
    carregarCanais();
});
</script>';

include __DIR__ . '/../layouts/base.php';
?>
