<?php
/**
 * DMC DataLoad - Gestão de Usuários
 * Nova UI Moderna
 */
$pageTitle = 'Usuários';
$currentPage = 'usuarios';

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <p class="text-muted mb-0">Gerencie os usuários do sistema</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUsuario" onclick="novoUsuario()">
        <i class="bi bi-person-plus me-2"></i>Novo Usuário
    </button>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card primary">
            <div class="stat-icon"><i class="bi bi-people"></i></div>
            <div class="stat-value" id="totalUsuarios">0</div>
            <div class="stat-label">Total de Usuários</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card success">
            <div class="stat-icon"><i class="bi bi-person-check"></i></div>
            <div class="stat-value" id="usuariosAtivos">0</div>
            <div class="stat-label">Usuários Ativos</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card secondary">
            <div class="stat-icon"><i class="bi bi-shield-check"></i></div>
            <div class="stat-value" id="usuariosAdmin">0</div>
            <div class="stat-label">Administradores</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card warning">
            <div class="stat-icon"><i class="bi bi-key"></i></div>
            <div class="stat-value" id="usuariosLdap">0</div>
            <div class="stat-label">Usuários LDAP</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tblUsuarios">
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Tipo</th>
                        <th>Nível de Acesso</th>
                        <th>Criado em</th>
                        <th width="120">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                            <span class="ms-2">Carregando...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Usuário -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person me-2"></i><span id="modalTitle">Novo Usuário</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUsuario">
                <input type="hidden" name="id" id="usuarioId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome de Usuário *</label>
                        <input type="text" class="form-control" name="nome_usuario" id="nome_usuario" required 
                               pattern="[a-zA-Z0-9_]+" title="Apenas letras, números e underscore">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Senha <span id="senhaObrig">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="senha" id="senha">
                            <button type="button" class="btn btn-outline-secondary" onclick="toggleSenha()">
                                <i class="bi bi-eye" id="iconSenha"></i>
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="gerarSenha()" title="Gerar senha">
                                <i class="bi bi-dice-5"></i>
                            </button>
                        </div>
                        <small class="text-muted" id="senhaHelp">Mínimo 6 caracteres</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nível de Acesso *</label>
                        <select class="form-select" name="nivel_acesso" id="nivel_acesso" required>
                            <option value="user">Usuário</option>
                            <option value="operador">Operador</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="eh_ldap" id="eh_ldap" value="1">
                        <label class="form-check-label" for="eh_ldap">
                            <i class="bi bi-key me-1"></i>Autenticação via LDAP
                        </label>
                        <small class="d-block text-muted">Se marcado, a senha será validada pelo Active Directory</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSalvar">
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
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
.stat-card .stat-value { font-size: 1.75rem; }
</style>
';

$extraScripts = '
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let tabela;

const nivelLabels = {
    admin: `<span class="badge bg-danger"><i class="bi bi-shield-check me-1"></i>Admin</span>`,
    operador: `<span class="badge bg-warning text-dark"><i class="bi bi-person-gear me-1"></i>Operador</span>`,
    user: `<span class="badge bg-primary"><i class="bi bi-person me-1"></i>Usuário</span>`
};

function loadTable() {
    $.getJSON(baseUrl + "/admin/usuarios/list", function(res) {
        // Destruir DataTable se já existir
        if ($.fn.DataTable.isDataTable("#tblUsuarios")) {
            $("#tblUsuarios").DataTable().destroy();
        }
        
        const tbody = $("#tblUsuarios tbody");
        tbody.empty();
        
        let total = 0, ativos = 0, admins = 0, ldap = 0;
        
        (res.dados || []).forEach(function(r) {
            total++;
            ativos++; // Todos são considerados ativos por enquanto
            if (r.nivel_acesso === "admin") admins++;
            if (r.eh_ldap) ldap++;
            
            const tipoAuth = r.eh_ldap 
                ? `<span class="badge bg-warning text-dark"><i class="bi bi-key me-1"></i>LDAP</span>`
                : `<span class="badge bg-secondary"><i class="bi bi-lock me-1"></i>Local</span>`;
            
            const dataCriacao = r.data_criacao ? new Date(r.data_criacao).toLocaleDateString("pt-BR") : "-";
            
            tbody.append(`<tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-person text-primary"></i>
                        </div>
                        <strong>${r.nome_usuario}</strong>
                    </div>
                </td>
                <td>${tipoAuth}</td>
                <td>${nivelLabels[r.nivel_acesso] || r.nivel_acesso}</td>
                <td>${dataCriacao}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="editarUsuario(${r.id})" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-warning" onclick="resetarSenha(${r.id})" title="Resetar Senha">
                            <i class="bi bi-key"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="excluirUsuario(${r.id})" title="Excluir">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>`);
        });
        
        // Inicializar DataTable após popular
        tabela = $("#tblUsuarios").DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json" },
            order: [[0, "asc"]]
        });
        
        // Atualizar stats
        $("#totalUsuarios").text(total);
        $("#usuariosAtivos").text(ativos);
        $("#usuariosAdmin").text(admins);
        $("#usuariosLdap").text(ldap);
    });
}

function novoUsuario() {
    document.getElementById("formUsuario").reset();
    document.getElementById("usuarioId").value = "";
    document.getElementById("modalTitle").textContent = "Novo Usuário";
    document.getElementById("senha").required = true;
    document.getElementById("senhaObrig").style.display = "";
    document.getElementById("senhaHelp").textContent = "Mínimo 6 caracteres";
}

function editarUsuario(id) {
    $.getJSON(baseUrl + "/admin/usuarios/get/" + id, function(r) {
        document.getElementById("usuarioId").value = r.id;
        document.getElementById("nome_usuario").value = r.nome_usuario;
        document.getElementById("nivel_acesso").value = r.nivel_acesso;
        document.getElementById("eh_ldap").checked = r.eh_ldap;
        document.getElementById("senha").value = "";
        document.getElementById("senha").required = false;
        document.getElementById("senhaObrig").style.display = "none";
        document.getElementById("senhaHelp").textContent = "Deixe vazio para manter a senha atual";
        document.getElementById("modalTitle").textContent = "Editar Usuário";
        new bootstrap.Modal("#modalUsuario").show();
    });
}

function excluirUsuario(id) {
    Swal.fire({
        title: "Excluir usuário?",
        text: "Esta ação não pode ser desfeita!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sim, excluir"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(baseUrl + "/admin/usuarios/delete/" + id, function(res) {
                if (res.sucesso) {
                    Swal.fire("Excluído!", "Usuário removido com sucesso.", "success");
                    loadTable();
                } else {
                    Swal.fire("Erro!", res.erro || res.mensagem, "error");
                }
            }, "json");
        }
    });
}

function resetarSenha(id) {
    Swal.fire({
        title: "Resetar senha?",
        input: "password",
        inputLabel: "Nova senha",
        inputPlaceholder: "Digite a nova senha",
        inputAttributes: { minlength: 6 },
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        confirmButtonText: "Resetar",
        inputValidator: (value) => {
            if (!value || value.length < 6) {
                return "A senha deve ter no mínimo 6 caracteres";
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(baseUrl + "/admin/usuarios/reset-senha", { id: id, senha: result.value }, function(res) {
                if (res.sucesso) {
                    Swal.fire("Senha alterada!", "A nova senha foi definida.", "success");
                } else {
                    Swal.fire("Erro!", res.erro || res.mensagem, "error");
                }
            }, "json");
        }
    });
}

function toggleSenha() {
    const input = document.getElementById("senha");
    const icon = document.getElementById("iconSenha");
    if (input.type === "password") {
        input.type = "text";
        icon.className = "bi bi-eye-slash";
    } else {
        input.type = "password";
        icon.className = "bi bi-eye";
    }
}

function gerarSenha() {
    const chars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%";
    let senha = "";
    for (let i = 0; i < 12; i++) {
        senha += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById("senha").value = senha;
    document.getElementById("senha").type = "text";
    document.getElementById("iconSenha").className = "bi bi-eye-slash";
}

// LDAP toggle
document.getElementById("eh_ldap").addEventListener("change", function() {
    const senhaField = document.getElementById("senha");
    if (this.checked) {
        senhaField.required = false;
        senhaField.placeholder = "Senha será validada pelo LDAP";
    } else {
        if (!document.getElementById("usuarioId").value) {
            senhaField.required = true;
        }
        senhaField.placeholder = "";
    }
});

// Submit form
document.getElementById("formUsuario").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const btn = document.getElementById("btnSalvar");
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Salvando...`;
    
    $.post(baseUrl + "/admin/usuarios/salvar", $(this).serialize(), function(res) {
        if (res.sucesso) {
            bootstrap.Modal.getInstance("#modalUsuario").hide();
            Swal.fire("Salvo!", res.mensagem || "Usuário salvo com sucesso.", "success");
            loadTable();
        } else {
            Swal.fire("Erro!", res.erro || res.mensagem, "error");
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
';

include __DIR__ . '/../layouts/base.php';
?>
