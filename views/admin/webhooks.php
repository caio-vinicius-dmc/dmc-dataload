<?php
/**
 * DMC DataLoad - Webhooks de Notificação
 */
$pageTitle = 'Webhooks';
$currentPage = 'webhooks';
$csrfToken = \App\Core\AuthMiddleware::gerarTokenCSRF();

ob_start();
?>

<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-broadcast"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Webhooks</h1>
        <p class="page-subtitle-modern">Envie notificações automáticas para URLs externas quando eventos ocorrerem</p>
    </div>
    <div class="ms-auto">
        <button class="btn btn-primary" onclick="novoWebhook()">
            <i class="bi bi-plus-lg me-2"></i>Novo Webhook
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tblWebhooks">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>URL</th>
                        <th>Eventos</th>
                        <th>Status</th>
                        <th width="150">Ações</th>
                    </tr>
                </thead>
                <tbody id="tbodyWebhooks">
                    <tr><td colspan="5" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalWebhook" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-broadcast me-2"></i><span id="modalTitle">Novo Webhook</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formWebhook">
                <input type="hidden" name="id" id="whId">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nome *</label>
                            <input type="text" class="form-control" name="nome" id="whNome" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">URL *</label>
                            <input type="url" class="form-control" name="url" id="whUrl" required placeholder="https://...">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Eventos</label>
                            <div class="row g-2">
                                <div class="col-auto"><div class="form-check"><input class="form-check-input" type="checkbox" name="eventos[]" value="falha_execucao" id="evt1" checked><label class="form-check-label" for="evt1">Falha de execução</label></div></div>
                                <div class="col-auto"><div class="form-check"><input class="form-check-input" type="checkbox" name="eventos[]" value="sucesso_execucao" id="evt2"><label class="form-check-label" for="evt2">Sucesso de execução</label></div></div>
                                <div class="col-auto"><div class="form-check"><input class="form-check-input" type="checkbox" name="eventos[]" value="usuario_criado" id="evt3"><label class="form-check-label" for="evt3">Usuário criado</label></div></div>
                                <div class="col-auto"><div class="form-check"><input class="form-check-input" type="checkbox" name="eventos[]" value="recurso_compartilhado" id="evt4"><label class="form-check-label" for="evt4">Recurso compartilhado</label></div></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Secret (HMAC)</label>
                            <input type="text" class="form-control" name="secret" id="whSecret" placeholder="Opcional - para assinatura">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="ativo" id="whAtivo" value="1" checked>
                                <label class="form-check-label" for="whAtivo">Ativo</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSalvar"><i class="bi bi-check-lg me-2"></i>Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$extraStyles = '
<style>
.page-header-modern { background: white; padding: 1.75rem 2rem; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; }
.page-icon-modern { width: 70px; height: 70px; border-radius: 16px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: white; box-shadow: 0 4px 20px rgba(139, 92, 246, 0.3); flex-shrink: 0; }
.page-title-modern { font-size: 2rem; font-weight: 700; margin: 0 0 0.25rem 0; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.page-subtitle-modern { color: #64748b; margin: 0; font-size: 1rem; }
</style>
';

$extraScripts = '<script>const csrfToken = ' . json_encode($csrfToken) . ';</script>';
$extraScripts .= <<<'SCRIPTS'
<script>
function loadTable() {
    $.getJSON(baseUrl + "/api/webhooks/list", function(res) {
        const tbody = document.getElementById('tbodyWebhooks');
        tbody.innerHTML = '';
        const dados = res.dados || [];
        if (dados.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Nenhum webhook configurado</td></tr>';
            return;
        }
        dados.forEach(function(w) {
            const eventos = (w.eventos || '').replace(/[{}]/g, '').split(',').map(e => 
                `<span class="badge bg-light text-dark me-1">${e.trim()}</span>`
            ).join('');
            const status = w.ativo ? '<span class="badge bg-success">Ativo</span>' : '<span class="badge bg-secondary">Inativo</span>';
            const urlShort = w.url.length > 40 ? w.url.substring(0, 40) + '...' : w.url;
            tbody.innerHTML += `<tr>
                <td><strong>${w.nome}</strong></td>
                <td><small class="text-muted" title="${w.url}">${urlShort}</small></td>
                <td>${eventos}</td>
                <td>${status}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-info" onclick="testarWebhook(${w.id})" title="Testar"><i class="bi bi-send"></i></button>
                        <button class="btn btn-outline-primary" onclick="editarWebhook(${w.id})" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-outline-danger" onclick="excluirWebhook(${w.id})" title="Excluir"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>`;
        });
    });
}

function novoWebhook() {
    document.getElementById('formWebhook').reset();
    document.getElementById('whId').value = '';
    document.getElementById('modalTitle').textContent = 'Novo Webhook';
    document.getElementById('evt1').checked = true;
    new bootstrap.Modal('#modalWebhook').show();
}

function editarWebhook(id) {
    $.getJSON(baseUrl + "/api/webhooks/get/" + id, function(res) {
        if (!res.sucesso) { Swal.fire('Erro', res.erro, 'error'); return; }
        const d = res.dados;
        document.getElementById('whId').value = d.id;
        document.getElementById('whNome').value = d.nome;
        document.getElementById('whUrl').value = d.url;
        document.getElementById('whSecret').value = d.secret || '';
        document.getElementById('whAtivo').checked = d.ativo;
        document.getElementById('modalTitle').textContent = 'Editar Webhook';
        // Check eventos
        const evts = (d.eventos || '').replace(/[{}]/g, '').split(',').map(e => e.trim());
        document.querySelectorAll('[name="eventos[]"]').forEach(cb => cb.checked = evts.includes(cb.value));
        new bootstrap.Modal('#modalWebhook').show();
    });
}

function excluirWebhook(id) {
    Swal.fire({
        title: 'Excluir webhook?', icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#ef4444', confirmButtonText: 'Excluir', cancelButtonText: 'Cancelar'
    }).then(r => {
        if (r.isConfirmed) {
            $.post(baseUrl + "/api/webhooks/delete/" + id, {_csrf_token: csrfToken}, function(res) {
                if (res.sucesso) { Swal.fire('Excluído!', res.mensagem, 'success'); loadTable(); }
                else Swal.fire('Erro', res.erro, 'error');
            }, 'json');
        }
    });
}

function testarWebhook(id) {
    Swal.fire({ title: 'Enviando teste...', allowOutsideClick: false, didOpen: () => {
        Swal.showLoading();
        $.post(baseUrl + "/api/webhooks/testar/" + id, {_csrf_token: csrfToken}, function(res) {
            Swal.fire(res.sucesso ? 'Enviado!' : 'Erro', res.mensagem || res.erro, res.sucesso ? 'success' : 'error');
        }, 'json');
    }});
}

document.getElementById('formWebhook').addEventListener('submit', function(e) {
    e.preventDefault();
    const data = $(this).serialize() + '&_csrf_token=' + encodeURIComponent(csrfToken);
    $.post(baseUrl + "/api/webhooks/salvar", data, function(res) {
        if (res.sucesso) {
            bootstrap.Modal.getInstance('#modalWebhook').hide();
            Swal.fire('Salvo!', res.mensagem, 'success');
            loadTable();
        } else {
            Swal.fire('Erro', res.erro, 'error');
        }
    }, 'json');
});

$(document).ready(function() { loadTable(); });
</script>
SCRIPTS;

include __DIR__ . '/../layouts/base.php';
?>
