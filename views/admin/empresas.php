<?php
/**
 * DMC DataLoad - Gestão de Empresas
 * Somente Super Administrador pode criar/editar/excluir
 */
$pageTitle = 'Empresas';
$currentPage = 'empresas';
$csrfToken = \App\Core\AuthMiddleware::gerarTokenCSRF();

ob_start();
?>

<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-building"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Empresas</h1>
        <p class="page-subtitle-modern">Gerencie as empresas do sistema</p>
    </div>
    <?php if (($usuario['nivel_acesso'] ?? '') === 'super_admin'): ?>
    <div class="ms-auto">
        <button class="btn btn-primary" onclick="novaEmpresa()">
            <i class="bi bi-building-add me-2"></i>Nova Empresa
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card primary">
            <div class="stat-icon"><i class="bi bi-building"></i></div>
            <div class="stat-value" id="totalEmpresas">0</div>
            <div class="stat-label">Total de Empresas</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card success">
            <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
            <div class="stat-value" id="empresasAtivas">0</div>
            <div class="stat-label">Empresas Ativas</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card secondary">
            <div class="stat-icon"><i class="bi bi-people"></i></div>
            <div class="stat-value" id="totalUsuariosEmpresas">0</div>
            <div class="stat-label">Usuários Associados</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card warning">
            <div class="stat-icon"><i class="bi bi-folder"></i></div>
            <div class="stat-value" id="totalProjetosEmpresas">0</div>
            <div class="stat-label">Projetos</div>
        </div>
    </div>
</div>

<div class="card-modern">
    <div class="card-modern-header">
        <i class="bi bi-building-fill me-2"></i>
        <span>Lista de Empresas</span>
    </div>
    <div class="card-modern-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-modern" id="tblEmpresas">
                <thead>
                    <tr>
                        <th>Empresa</th>
                        <th>Status</th>
                        <th>Usuários</th>
                        <th>Projetos</th>
                        <th>Criada em</th>
                        <th width="120">Ações</th>
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

<!-- Modal Empresa -->
<div class="modal fade" id="modalEmpresa" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-building me-2"></i><span id="modalTitle">Nova Empresa</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEmpresa">
                <input type="hidden" name="id" id="empresaId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome da Empresa *</label>
                        <input type="text" class="form-control" name="nome" id="empresaNome" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" name="descricao" id="empresaDescricao" rows="3" maxlength="1000"></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="ativa" id="empresaAtiva" value="1" checked>
                        <label class="form-check-label" for="empresaAtiva">Empresa Ativa</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSalvarEmpresa">
                        <i class="bi bi-check-lg me-2"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detalhes -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-building me-2"></i>Detalhes da Empresa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalhesConteudo">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$extraStyles = '
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
.page-icon-modern {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    box-shadow: 0 4px 20px rgba(249, 115, 22, 0.3);
}
.page-title-modern {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.user-list-item { display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; border-bottom: 1px solid #f1f5f9; }
.user-list-item:last-child { border-bottom: none; }
</style>
';

$extraScripts = '<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>const csrfToken = ' . json_encode($csrfToken) . '; const ehSuperAdmin = ' . json_encode(($usuario['nivel_acesso'] ?? '') === 'super_admin') . ';</script>';

$extraScripts .= <<<'SCRIPTS'
<script>
let tabela;

function loadTable() {
    $.getJSON(baseUrl + "/admin/empresas/list", function(res) {
        if ($.fn.DataTable.isDataTable("#tblEmpresas")) {
            $("#tblEmpresas").DataTable().destroy();
        }
        
        const tbody = $("#tblEmpresas tbody");
        tbody.empty();
        
        let total = 0, ativas = 0, totalUsers = 0, totalProj = 0;
        
        (res.dados || []).forEach(function(r) {
            total++;
            if (r.ativa) ativas++;
            totalUsers += parseInt(r.total_usuarios || 0);
            totalProj += parseInt(r.total_projetos || 0);
            
            const status = r.ativa 
                ? `<span class="badge bg-success">Ativa</span>`
                : `<span class="badge bg-secondary">Inativa</span>`;
            
            const data = r.data_criacao ? new Date(r.data_criacao).toLocaleDateString("pt-BR") : "-";
            
            let acoes = `<button class="btn btn-outline-info btn-sm" onclick="verDetalhes(${r.id})" title="Detalhes"><i class="bi bi-eye"></i></button>`;
            if (ehSuperAdmin) {
                acoes += ` <button class="btn btn-outline-primary btn-sm" onclick="editarEmpresa(${r.id})" title="Editar"><i class="bi bi-pencil"></i></button>`;
                acoes += ` <button class="btn btn-outline-danger btn-sm" onclick="excluirEmpresa(${r.id})" title="Excluir"><i class="bi bi-trash"></i></button>`;
            }
            
            tbody.append(`<tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-2" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-building text-warning"></i>
                        </div>
                        <div>
                            <strong>${r.nome}</strong>
                            ${r.descricao ? `<br><small class="text-muted">${r.descricao.substring(0, 60)}</small>` : ""}
                        </div>
                    </div>
                </td>
                <td>${status}</td>
                <td><span class="badge bg-primary">${r.total_usuarios}</span></td>
                <td><span class="badge bg-info">${r.total_projetos}</span></td>
                <td>${data}</td>
                <td><div class="btn-group btn-group-sm">${acoes}</div></td>
            </tr>`);
        });
        
        tabela = $("#tblEmpresas").DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json" },
            order: [[0, "asc"]]
        });
        
        $("#totalEmpresas").text(total);
        $("#empresasAtivas").text(ativas);
        $("#totalUsuariosEmpresas").text(totalUsers);
        $("#totalProjetosEmpresas").text(totalProj);
    });
}

function novaEmpresa() {
    document.getElementById("formEmpresa").reset();
    document.getElementById("empresaId").value = "";
    document.getElementById("modalTitle").textContent = "Nova Empresa";
    document.getElementById("empresaAtiva").checked = true;
    new bootstrap.Modal("#modalEmpresa").show();
}

function editarEmpresa(id) {
    $.getJSON(baseUrl + "/admin/empresas/get/" + id, function(res) {
        if (!res.sucesso) { Swal.fire("Erro", res.erro, "error"); return; }
        const d = res.dados;
        document.getElementById("empresaId").value = d.id;
        document.getElementById("empresaNome").value = d.nome;
        document.getElementById("empresaDescricao").value = d.descricao || "";
        document.getElementById("empresaAtiva").checked = d.ativa;
        document.getElementById("modalTitle").textContent = "Editar Empresa";
        new bootstrap.Modal("#modalEmpresa").show();
    });
}

function excluirEmpresa(id) {
    Swal.fire({
        title: "Excluir empresa?",
        text: "Todos os projetos e associações serão removidos!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sim, excluir"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(baseUrl + "/admin/empresas/delete/" + id, {_csrf_token: csrfToken}, function(res) {
                if (res.sucesso) {
                    Swal.fire("Excluída!", res.mensagem, "success");
                    loadTable();
                } else {
                    Swal.fire("Erro!", res.erro, "error");
                }
            }, "json");
        }
    });
}

function verDetalhes(id) {
    const modal = new bootstrap.Modal("#modalDetalhes");
    $("#detalhesConteudo").html(`<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>`);
    modal.show();
    
    $.getJSON(baseUrl + "/admin/empresas/get/" + id, function(res) {
        if (!res.sucesso) { $("#detalhesConteudo").html(`<div class="alert alert-danger">${res.erro}</div>`); return; }
        const d = res.dados;
        let usersHtml = "";
        if (d.usuarios && d.usuarios.length > 0) {
            const nivelLabels = {super_admin:"Super Admin",admin:"Administrador",desenvolvedor:"Desenvolvedor",operador:"Operador"};
            const nivelColors = {super_admin:"danger",admin:"warning",desenvolvedor:"primary",operador:"secondary"};
            usersHtml = d.usuarios.map(u => `<div class="user-list-item">
                <div class="rounded-circle bg-primary bg-opacity-10 p-1" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-person text-primary" style="font-size:0.8rem"></i>
                </div>
                <strong>${u.nome_usuario}</strong>
                <span class="badge bg-${nivelColors[u.nivel_acesso]||"secondary"} ms-auto">${nivelLabels[u.nivel_acesso]||u.nivel_acesso}</span>
            </div>`).join("");
        } else {
            usersHtml = `<div class="text-muted text-center py-3">Nenhum usuário associado</div>`;
        }
        
        $("#detalhesConteudo").html(`
            <div class="row">
                <div class="col-md-6">
                    <h6>Informações</h6>
                    <table class="table table-sm">
                        <tr><td class="text-muted">Nome</td><td><strong>${d.nome}</strong></td></tr>
                        <tr><td class="text-muted">Descrição</td><td>${d.descricao || "-"}</td></tr>
                        <tr><td class="text-muted">Status</td><td>${d.ativa ? '<span class="badge bg-success">Ativa</span>' : '<span class="badge bg-secondary">Inativa</span>'}</td></tr>
                        <tr><td class="text-muted">Criada em</td><td>${new Date(d.data_criacao).toLocaleDateString("pt-BR")}</td></tr>
                        <tr><td class="text-muted">Projetos</td><td><span class="badge bg-info">${d.total_projetos}</span></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Usuários Associados (${d.total_usuarios})</h6>
                    <div class="border rounded" style="max-height:300px;overflow-y:auto">${usersHtml}</div>
                </div>
            </div>
        `);
    });
}

// Submit form
document.getElementById("formEmpresa").addEventListener("submit", function(e) {
    e.preventDefault();
    const btn = document.getElementById("btnSalvarEmpresa");
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Salvando...`;
    
    const data = $(this).serialize() + "&_csrf_token=" + encodeURIComponent(csrfToken);
    
    $.post(baseUrl + "/admin/empresas/salvar", data, function(res) {
        if (res.sucesso) {
            bootstrap.Modal.getInstance("#modalEmpresa").hide();
            Swal.fire("Salvo!", res.mensagem, "success");
            loadTable();
        } else {
            Swal.fire("Erro!", res.erro, "error");
        }
    }, "json").always(function() {
        btn.disabled = false;
        btn.innerHTML = `<i class="bi bi-check-lg me-2"></i>Salvar`;
    });
});

$(document).ready(function() {
    loadTable();
});
</script>
SCRIPTS;

include __DIR__ . '/../layouts/base.php';
?>
