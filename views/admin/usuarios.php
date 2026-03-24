<?php
/**
 * DMC DataLoad - Gestão de Usuários
 * RBAC: Super Admin, Administrador, Desenvolvedor, Operador
 */
$pageTitle = 'Usuários';
$currentPage = 'usuarios';
$csrfToken = \App\Core\AuthMiddleware::gerarTokenCSRF();

$nivelAcessoLogado = $usuario['nivel_acesso'] ?? 'operador';
$ehSuperAdmin = ($nivelAcessoLogado === 'super_admin');
$ehAdmin = in_array($nivelAcessoLogado, ['admin', 'super_admin']);

ob_start();
?>

<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-people"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Usuários</h1>
        <p class="page-subtitle-modern">Gerencie os usuários do sistema</p>
    </div>
    <?php if ($ehAdmin): ?>
    <div class="ms-auto">
        <button class="btn btn-primary" onclick="novoUsuario()">
            <i class="bi bi-person-plus me-2"></i>Novo Usuário
        </button>
    </div>
    <?php endif; ?>
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
            <div class="stat-icon"><i class="bi bi-code-slash"></i></div>
            <div class="stat-value" id="usuariosDev">0</div>
            <div class="stat-label">Desenvolvedores</div>
        </div>
    </div>
</div>

<div class="card-modern">
    <div class="card-modern-header">
        <i class="bi bi-people-fill me-2"></i>
        <span>Lista de Usuários</span>
    </div>
    <div class="card-modern-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-modern" id="tblUsuarios">
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>Tipo</th>
                        <th>Nível de Acesso</th>
                        <th>Status</th>
                        <th>Empresas</th>
                        <th>Criado em</th>
                        <th width="160">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center py-4">
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person me-2"></i><span id="modalTitle">Novo Usuário</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUsuario">
                <input type="hidden" name="id" id="usuarioId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nome de Usuário *</label>
                                <input type="text" class="form-control" name="nome_usuario" id="nome_usuario" required 
                                       pattern="[a-zA-Z0-9_]+" title="Apenas letras, números e underscore">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" name="nome" id="nome" placeholder="Nome completo do usuário">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="usuario@email.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">CPF</label>
                                <input type="text" class="form-control" name="cpf" id="cpf" placeholder="000.000.000-00" maxlength="14">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
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
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nível de Acesso *</label>
                                <select class="form-select" name="nivel_acesso" id="nivel_acesso" required>
                                    <!-- Opções serão preenchidas dinamicamente -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" name="eh_ldap" id="eh_ldap" value="1">
                                <label class="form-check-label" for="eh_ldap">
                                    <i class="bi bi-key me-1"></i>Autenticação via LDAP
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6" id="bloqueioContainer" style="display:none;">
                            <div class="alert alert-warning py-2 px-3 mb-0 d-flex align-items-center justify-content-between">
                                <span><i class="bi bi-lock-fill me-2"></i><strong>Usuário bloqueado</strong> <small id="bloqueioAteLabel"></small></span>
                                <button type="button" class="btn btn-sm btn-success" onclick="desbloquearNoModal()">
                                    <i class="bi bi-unlock me-1"></i>Desbloquear
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    <h6 class="text-primary"><i class="bi bi-building me-2"></i>Empresas</h6>
                    <div class="mb-3" id="empresasContainer">
                        <div class="d-flex flex-wrap gap-2" id="empresasCheckboxes">
                            <div class="text-muted small">Carregando empresas...</div>
                        </div>
                    </div>
                    
                    <h6 class="text-primary"><i class="bi bi-folder me-2"></i>Projetos</h6>
                    <div class="mb-3" id="projetosContainer">
                        <div class="d-flex flex-wrap gap-2" id="projetosCheckboxes">
                            <div class="text-muted small">Selecione empresas primeiro...</div>
                        </div>
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
<style>
.page-icon-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
}
.page-title-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.empresa-check, .projeto-check {
    border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.5rem 0.75rem;
    transition: all 0.2s; cursor: pointer;
}
.empresa-check:hover, .projeto-check:hover { border-color: #667eea; background: #f8faff; }
.empresa-check input:checked + label, .projeto-check input:checked + label { color: #667eea; font-weight: 600; }
</style>
';

$extraScripts = '
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
let tabela;
const csrfToken = ' . json_encode($csrfToken) . ';
const ehSuperAdmin = ' . json_encode($ehSuperAdmin) . ';
const ehAdmin = ' . json_encode($ehAdmin) . ';
const usuarioLogadoId = ' . json_encode(\App\Core\AuthMiddleware::obterUsuarioId()) . ';
let empresasDisp = [];
let projetosDisp = [];

const nivelLabels = {
    super_admin: `<span class="badge bg-dark"><i class="bi bi-shield-fill-check me-1"></i>Super Admin</span>`,
    admin: `<span class="badge bg-danger"><i class="bi bi-shield-check me-1"></i>Administrador</span>`,
    desenvolvedor: `<span class="badge bg-primary"><i class="bi bi-code-slash me-1"></i>Desenvolvedor</span>`,
    operador: `<span class="badge bg-warning text-dark"><i class="bi bi-eye me-1"></i>Operador</span>`
};

function loadTable() {
    $.getJSON(baseUrl + "/admin/usuarios/list", function(res) {
        if ($.fn.DataTable.isDataTable("#tblUsuarios")) {
            $("#tblUsuarios").DataTable().destroy();
        }
        
        const tbody = $("#tblUsuarios tbody");
        tbody.empty();
        
        let total = 0, ativos = 0, admins = 0, devs = 0;
        
        (res.dados || []).forEach(function(r) {
            total++;
            ativos++;
            if (r.nivel_acesso === "admin" || r.nivel_acesso === "super_admin") admins++;
            if (r.nivel_acesso === "desenvolvedor") devs++;
            
            const tipoAuth = r.eh_ldap 
                ? `<span class="badge bg-warning text-dark"><i class="bi bi-key me-1"></i>LDAP</span>`
                : `<span class="badge bg-secondary"><i class="bi bi-lock me-1"></i>Local</span>`;
            
            const dataCriacao = r.data_criacao ? new Date(r.data_criacao).toLocaleDateString("pt-BR") : "-";
            
            // Status de bloqueio
            let statusBloqueio = "";
            if (r.bloqueado_ate) {
                const bloqueadoAte = new Date(r.bloqueado_ate);
                const agora = new Date();
                if (bloqueadoAte > agora) {
                    statusBloqueio = `<span class="badge bg-danger"><i class="bi bi-lock-fill me-1"></i>Bloqueado</span>`;
                }
            }
            
            // Empresas do usuário
            let empresasHtml = "-";
            if (r.empresas && r.empresas.length > 0) {
                empresasHtml = r.empresas.map(e => `<span class="badge bg-info me-1">${e.nome}</span>`).join("");
            }
            
            // Ações baseadas em permissão
            let acoes = "";
            const podeGerenciar = r.nivel_acesso !== "super_admin" && (ehSuperAdmin || (ehAdmin && r.nivel_acesso !== "admin"));
            
            if (podeGerenciar) {
                let bloqueioBtn = "";
                if (r.bloqueado_ate && new Date(r.bloqueado_ate) > new Date()) {
                    bloqueioBtn = `<button class="btn btn-outline-success" onclick="desbloquearUsuario(${r.id})" title="Desbloquear"><i class="bi bi-unlock"></i></button>`;
                }
                acoes = `<div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="editarUsuario(${r.id})" title="Editar"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-outline-warning" onclick="resetarSenha(${r.id})" title="Resetar Senha"><i class="bi bi-key"></i></button>
                    <button class="btn btn-outline-info" onclick="resetarSenhaEmail(${r.id})" title="Enviar Reset por E-mail"><i class="bi bi-envelope"></i></button>
                    ${bloqueioBtn}
                    <button class="btn btn-outline-danger" onclick="excluirUsuario(${r.id})" title="Excluir"><i class="bi bi-trash"></i></button>
                </div>`;
            } else if (r.id == usuarioLogadoId) {
                acoes = `<span class="badge bg-secondary">Você</span>`;
            }
            
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
                <td>${statusBloqueio || "<span class=\\"badge bg-success\\"><i class=\\"bi bi-check-circle me-1\\"></i>Ativo</span>"}</td>
                <td>${empresasHtml}</td>
                <td>${dataCriacao}</td>
                <td>${acoes}</td>
            </tr>`);
        });
        
        tabela = $("#tblUsuarios").DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json" },
            order: [[0, "asc"]]
        });
        
        $("#totalUsuarios").text(total);
        $("#usuariosAtivos").text(ativos);
        $("#usuariosAdmin").text(admins);
        $("#usuariosDev").text(devs);
    });
}

function carregarEmpresas() {
    $.getJSON(baseUrl + "/api/permissoes/empresas-usuario", function(res) {
        empresasDisp = res.dados || [];
        renderizarEmpresas();
    });
}

function renderizarEmpresas(selecionadas) {
    const container = document.getElementById("empresasCheckboxes");
    if (empresasDisp.length === 0) {
        container.innerHTML = `<div class="text-muted small">Nenhuma empresa disponível</div>`;
        return;
    }
    container.innerHTML = empresasDisp.map(e => `
        <div class="empresa-check">
            <input type="checkbox" class="form-check-input empresa-cb" id="emp_${e.id}" 
                   name="empresas[]" value="${e.id}" ${(selecionadas||[]).includes(e.id) ? "checked" : ""}>
            <label class="form-check-label ms-1" for="emp_${e.id}">${e.nome}</label>
        </div>
    `).join("");
    
    // Atualizar projetos quando empresas mudam
    document.querySelectorAll(".empresa-cb").forEach(cb => {
        cb.addEventListener("change", carregarProjetosParaEmpresas);
    });
    
    if (selecionadas && selecionadas.length > 0) {
        carregarProjetosParaEmpresas();
    }
}

function carregarProjetosParaEmpresas(projetosSelecionados) {
    const empresasSel = Array.from(document.querySelectorAll(".empresa-cb:checked")).map(cb => cb.value);
    const container = document.getElementById("projetosCheckboxes");
    
    if (empresasSel.length === 0) {
        container.innerHTML = `<div class="text-muted small">Selecione empresas primeiro...</div>`;
        return;
    }
    
    $.getJSON(baseUrl + "/api/permissoes/projetos-usuario?empresas=" + empresasSel.join(","), function(res) {
        projetosDisp = res.dados || [];
        if (projetosDisp.length === 0) {
            container.innerHTML = `<div class="text-muted small">Nenhum projeto disponível nestas empresas</div>`;
            return;
        }
        const selIds = Array.isArray(projetosSelecionados) ? projetosSelecionados : [];
        container.innerHTML = projetosDisp.map(p => `
            <div class="projeto-check">
                <input type="checkbox" class="form-check-input" id="proj_${p.id}" 
                       name="projetos[]" value="${p.id}" ${selIds.includes(p.id) ? "checked" : ""}>
                <label class="form-check-label ms-1" for="proj_${p.id}">
                    ${p.nome} <small class="text-muted">(${p.empresa_nome})</small>
                </label>
            </div>
        `).join("");
    });
}

function novoUsuario() {
    document.getElementById("formUsuario").reset();
    document.getElementById("usuarioId").value = "";
    document.getElementById("modalTitle").textContent = "Novo Usuário";
    document.getElementById("senha").required = true;
    document.getElementById("senhaObrig").style.display = "";
    document.getElementById("senhaHelp").textContent = "Mínimo 6 caracteres";
    document.getElementById("bloqueioContainer").style.display = "none";
    
    // Preencher opções de nível de acesso
    carregarOpcoesNivel();
    carregarEmpresas();
    document.getElementById("projetosCheckboxes").innerHTML = `<div class="text-muted small">Selecione empresas primeiro...</div>`;
    bootstrap.Modal.getOrCreateInstance(document.getElementById("modalUsuario")).show();
}

function carregarOpcoesNivel() {
    $.getJSON(baseUrl + "/api/permissoes/papeis-disponiveis", function(res) {
        const sel = document.getElementById("nivel_acesso");
        const labels = {admin: "Administrador", desenvolvedor: "Desenvolvedor", operador: "Operador"};
        sel.innerHTML = (res.dados || []).map(p => `<option value="${p}">${labels[p] || p}</option>`).join("");
    });
}

function editarUsuario(id) {
    $.getJSON(baseUrl + "/admin/usuarios/get/" + id, function(r) {
        document.getElementById("usuarioId").value = r.id;
        document.getElementById("nome_usuario").value = r.nome_usuario;
        document.getElementById("nome").value = r.nome || "";
        document.getElementById("email").value = r.email || "";
        document.getElementById("cpf").value = r.cpf || "";
        document.getElementById("eh_ldap").checked = r.eh_ldap;
        document.getElementById("senha").value = "";
        document.getElementById("senha").required = false;
        document.getElementById("senhaObrig").style.display = "none";
        document.getElementById("senhaHelp").textContent = "Deixe vazio para manter a senha atual";
        document.getElementById("modalTitle").textContent = "Editar Usuário";
        
        // Bloqueio
        if (r.bloqueado_ate && new Date(r.bloqueado_ate) > new Date()) {
            document.getElementById("bloqueioContainer").style.display = "";
            document.getElementById("bloqueioAteLabel").textContent = "até " + new Date(r.bloqueado_ate).toLocaleString("pt-BR");
        } else {
            document.getElementById("bloqueioContainer").style.display = "none";
        }
        
        carregarOpcoesNivel();
        setTimeout(() => {
            document.getElementById("nivel_acesso").value = r.nivel_acesso;
        }, 200);
        
        carregarEmpresas();
        setTimeout(() => {
            const empIds = (r.empresas || []).map(e => e.id);
            renderizarEmpresas(empIds);
            setTimeout(() => {
                const projIds = (r.projetos || []).map(p => p.id);
                carregarProjetosParaEmpresas(projIds);
            }, 300);
        }, 200);
        
        bootstrap.Modal.getOrCreateInstance(document.getElementById("modalUsuario")).show();
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
            $.post(baseUrl + "/admin/usuarios/delete/" + id, {_csrf_token: csrfToken}, function(res) {
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
            if (!value || value.length < 6) return "A senha deve ter no mínimo 6 caracteres";
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(baseUrl + "/admin/usuarios/reset-senha", { id: id, senha: result.value, _csrf_token: csrfToken }, function(res) {
                if (res.sucesso) {
                    Swal.fire("Senha alterada!", "A nova senha foi definida.", "success");
                } else {
                    Swal.fire("Erro!", res.erro || res.mensagem, "error");
                }
            }, "json");
        }
    });
}

function resetarSenhaEmail(id) {
    Swal.fire({
        title: "Enviar reset por e-mail?",
        text: "O usuário receberá um link para redefinir a senha no e-mail cadastrado.",
        icon: "question",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        confirmButtonText: "Enviar E-mail",
        confirmButtonColor: "#0dcaf0"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(baseUrl + "/admin/usuarios/reset-senha-email", { id: id, _csrf_token: csrfToken }, function(res) {
                if (res.sucesso) {
                    Swal.fire("E-mail enviado!", res.mensagem || "Link de redefinição enviado com sucesso.", "success");
                } else {
                    Swal.fire("Erro!", res.erro || "Falha ao enviar e-mail de reset.", "error");
                }
            }, "json");
        }
    });
}

function desbloquearUsuario(id) {
    Swal.fire({
        title: "Desbloquear usuário?",
        text: "O usuário poderá fazer login novamente.",
        icon: "warning",
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        confirmButtonText: "Desbloquear",
        confirmButtonColor: "#198754"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(baseUrl + "/admin/usuarios/desbloquear", { id: id, _csrf_token: csrfToken }, function(res) {
                if (res.sucesso) {
                    Swal.fire("Desbloqueado!", res.mensagem, "success");
                    loadTable();
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
    if (input.type === "password") { input.type = "text"; icon.className = "bi bi-eye-slash"; }
    else { input.type = "password"; icon.className = "bi bi-eye"; }
}

function gerarSenha() {
    const chars = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%";
    let senha = "";
    for (let i = 0; i < 12; i++) senha += chars.charAt(Math.floor(Math.random() * chars.length));
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
        if (!document.getElementById("usuarioId").value) senhaField.required = true;
        senhaField.placeholder = "";
    }
});

// CPF mask
document.getElementById("cpf").addEventListener("input", function() {
    let v = this.value.replace(/\\D/g, "").substring(0, 11);
    if (v.length > 9) v = v.replace(/(\\d{3})(\\d{3})(\\d{3})(\\d{1,2})/, "$1.$2.$3-$4");
    else if (v.length > 6) v = v.replace(/(\\d{3})(\\d{3})(\\d{1,3})/, "$1.$2.$3");
    else if (v.length > 3) v = v.replace(/(\\d{3})(\\d{1,3})/, "$1.$2");
    this.value = v;
});

// Desbloquear usuário dentro do modal
function desbloquearNoModal() {
    const id = document.getElementById("usuarioId").value;
    if (!id) return;
    $.post(baseUrl + "/admin/usuarios/desbloquear", { id: id, _csrf_token: csrfToken }, function(res) {
        if (res.sucesso) {
            document.getElementById("bloqueioContainer").style.display = "none";
            Swal.fire("Desbloqueado!", res.mensagem, "success");
            loadTable();
        } else {
            Swal.fire("Erro!", res.erro || res.mensagem, "error");
        }
    }, "json");
}

// Submit form
document.getElementById("formUsuario").addEventListener("submit", function(e) {
    e.preventDefault();
    const btn = document.getElementById("btnSalvar");
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Salvando...`;
    
    const data = $(this).serialize() + "&_csrf_token=" + encodeURIComponent(csrfToken);
    
    $.post(baseUrl + "/admin/usuarios/salvar", data, function(res) {
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
