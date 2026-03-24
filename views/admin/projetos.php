<?php
/**
 * DMC DataLoad - Gestão de Projetos
 * Administradores podem criar dentro das suas empresas
 */
$pageTitle = 'Projetos';
$currentPage = 'projetos';
$csrfToken = \App\Core\AuthMiddleware::gerarTokenCSRF();

ob_start();
?>

<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-folder"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Projetos</h1>
        <p class="page-subtitle-modern">Gerencie os projetos das empresas</p>
    </div>
    <?php if (in_array($usuario['nivel_acesso'] ?? '', ['super_admin', 'admin'])): ?>
    <div class="ms-auto">
        <button class="btn btn-primary" onclick="novoProjeto()">
            <i class="bi bi-folder-plus me-2"></i>Novo Projeto
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-6 col-lg-4">
        <div class="stat-card primary">
            <div class="stat-icon"><i class="bi bi-folder"></i></div>
            <div class="stat-value" id="totalProjetos">0</div>
            <div class="stat-label">Total de Projetos</div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="stat-card success">
            <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
            <div class="stat-value" id="projetosAtivos">0</div>
            <div class="stat-label">Projetos Ativos</div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="stat-card secondary">
            <div class="stat-icon"><i class="bi bi-people"></i></div>
            <div class="stat-value" id="totalUsuariosProjetos">0</div>
            <div class="stat-label">Usuários em Projetos</div>
        </div>
    </div>
</div>

<div class="card-modern">
    <div class="card-modern-header">
        <i class="bi bi-folder-fill me-2"></i>
        <span>Lista de Projetos</span>
    </div>
    <div class="card-modern-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-modern" id="tblProjetos">
                <thead>
                    <tr>
                        <th>Projeto</th>
                        <th>Empresa</th>
                        <th>Status</th>
                        <th>Usuários</th>
                        <th>Criado em</th>
                        <th width="120">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="6" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> <span class="ms-2">Carregando...</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Projeto -->
<div class="modal fade" id="modalProjeto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-folder me-2"></i><span id="modalProjetoTitulo">Novo Projeto</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formProjeto">
                <input type="hidden" name="id" id="projetoId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome do Projeto *</label>
                        <input type="text" class="form-control" name="nome" id="projetoNome" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Empresa *</label>
                        <select class="form-select" name="id_empresa" id="projetoEmpresa" required>
                            <option value="">Selecione a empresa...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" name="descricao" id="projetoDescricao" rows="3" maxlength="1000"></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="ativo" id="projetoAtivo" value="1" checked>
                        <label class="form-check-label" for="projetoAtivo">Projeto Ativo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSalvarProjeto">
                        <i class="bi bi-check-lg me-2"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$extraStyles = '
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
.page-icon-modern {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.3);
}
.page-title-modern {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
</style>
';

$extraScripts = '
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
const csrfToken = ' . json_encode($csrfToken) . ';
const ehAdminOuSuperAdmin = ' . json_encode(in_array($usuario['nivel_acesso'] ?? '', ['super_admin', 'admin'])) . ';
let tabela;
let empresasCache = [];

function loadEmpresas() {
    $.getJSON(baseUrl + "/admin/empresas/list", function(res) {
        empresasCache = res.dados || [];
        const sel = document.getElementById("projetoEmpresa");
        sel.innerHTML = `<option value="">Selecione a empresa...</option>`;
        empresasCache.forEach(e => {
            sel.innerHTML += `<option value="${e.id}">${e.nome}</option>`;
        });
    });
}

function loadTable() {
    $.getJSON(baseUrl + "/admin/projetos/list", function(res) {
        if ($.fn.DataTable.isDataTable("#tblProjetos")) {
            $("#tblProjetos").DataTable().destroy();
        }
        
        const tbody = $("#tblProjetos tbody");
        tbody.empty();
        
        let total = 0, ativos = 0, totalUsers = 0;
        
        (res.dados || []).forEach(function(r) {
            total++;
            if (r.ativo) ativos++;
            totalUsers += parseInt(r.total_usuarios || 0);
            
            const status = r.ativo 
                ? `<span class="badge bg-success">Ativo</span>`
                : `<span class="badge bg-secondary">Inativo</span>`;
            
            const data = r.data_criacao ? new Date(r.data_criacao).toLocaleDateString("pt-BR") : "-";
            
            let acoes = `<button class="btn btn-outline-info btn-sm" onclick="verDetalhesProjeto(${r.id})" title="Detalhes"><i class="bi bi-eye"></i></button>`;
            if (ehAdminOuSuperAdmin) {
                acoes += ` <button class="btn btn-outline-primary btn-sm" onclick="editarProjeto(${r.id})" title="Editar"><i class="bi bi-pencil"></i></button>`;
                acoes += ` <button class="btn btn-outline-danger btn-sm" onclick="excluirProjeto(${r.id})" title="Excluir"><i class="bi bi-trash"></i></button>`;
            }
            
            tbody.append(`<tr>
                <td><strong>${r.nome}</strong>${r.descricao ? `<br><small class="text-muted">${r.descricao.substring(0,60)}</small>` : ""}</td>
                <td><span class="badge bg-warning text-dark"><i class="bi bi-building me-1"></i>${r.empresa_nome}</span></td>
                <td>${status}</td>
                <td><span class="badge bg-primary">${r.total_usuarios}</span></td>
                <td>${data}</td>
                <td><div class="btn-group btn-group-sm">${acoes}</div></td>
            </tr>`);
        });
        
        tabela = $("#tblProjetos").DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json" },
            order: [[1, "asc"], [0, "asc"]]
        });
        
        $("#totalProjetos").text(total);
        $("#projetosAtivos").text(ativos);
        $("#totalUsuariosProjetos").text(totalUsers);
    });
}

function novoProjeto() {
    document.getElementById("formProjeto").reset();
    document.getElementById("projetoId").value = "";
    document.getElementById("modalProjetoTitulo").textContent = "Novo Projeto";
    document.getElementById("projetoAtivo").checked = true;
    new bootstrap.Modal("#modalProjeto").show();
}

function editarProjeto(id) {
    $.getJSON(baseUrl + "/admin/projetos/get/" + id, function(res) {
        if (!res.sucesso) { Swal.fire("Erro", res.erro, "error"); return; }
        const d = res.dados;
        document.getElementById("projetoId").value = d.id;
        document.getElementById("projetoNome").value = d.nome;
        document.getElementById("projetoEmpresa").value = d.id_empresa;
        document.getElementById("projetoDescricao").value = d.descricao || "";
        document.getElementById("projetoAtivo").checked = d.ativo;
        document.getElementById("modalProjetoTitulo").textContent = "Editar Projeto";
        new bootstrap.Modal("#modalProjeto").show();
    });
}

function excluirProjeto(id) {
    Swal.fire({
        title: "Excluir projeto?",
        text: "Todas as associações serão removidas!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sim, excluir"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(baseUrl + "/admin/projetos/delete/" + id, {_csrf_token: csrfToken}, function(res) {
                if (res.sucesso) {
                    Swal.fire("Excluído!", res.mensagem, "success");
                    loadTable();
                } else {
                    Swal.fire("Erro!", res.erro, "error");
                }
            }, "json");
        }
    });
}

function verDetalhesProjeto(id) {
    $.getJSON(baseUrl + "/admin/projetos/get/" + id, function(res) {
        if (!res.sucesso) { Swal.fire("Erro", res.erro, "error"); return; }
        const d = res.dados;
        const nivelLabels = {super_admin:"Super Admin",admin:"Administrador",desenvolvedor:"Desenvolvedor",operador:"Operador"};
        let usersHtml = d.usuarios && d.usuarios.length > 0
            ? d.usuarios.map(u => `<li class="list-group-item d-flex justify-content-between align-items-center">${u.nome_usuario}<span class="badge bg-primary">${nivelLabels[u.nivel_acesso]||u.nivel_acesso}</span></li>`).join("")
            : `<li class="list-group-item text-muted">Nenhum usuário associado</li>`;
        
        Swal.fire({
            title: d.nome,
            html: `<div class="text-start">
                <p><strong>Empresa:</strong> ${d.empresa_nome}</p>
                <p><strong>Descrição:</strong> ${d.descricao||"-"}</p>
                <p><strong>Status:</strong> ${d.ativo ? "Ativo" : "Inativo"}</p>
                <h6 class="mt-3">Usuários:</h6>
                <ul class="list-group">${usersHtml}</ul>
            </div>`,
            width: 600
        });
    });
}

// Submit form
document.getElementById("formProjeto").addEventListener("submit", function(e) {
    e.preventDefault();
    const btn = document.getElementById("btnSalvarProjeto");
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Salvando...`;
    
    const data = $(this).serialize() + "&_csrf_token=" + encodeURIComponent(csrfToken);
    
    $.post(baseUrl + "/admin/projetos/salvar", data, function(res) {
        if (res.sucesso) {
            bootstrap.Modal.getInstance("#modalProjeto").hide();
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
    loadEmpresas();
    loadTable();
});
</script>
';

include __DIR__ . '/../layouts/base.php';
?>
