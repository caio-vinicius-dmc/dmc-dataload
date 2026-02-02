<?php
/**
 * DMC DataLoad - Conexões
 * Nova UI Moderna
 */
$pageTitle = 'Conexões';
$currentPage = 'conexoes';

ob_start();
?>

<!-- Page Header Modern -->
<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-hdd-network"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Conexões</h1>
        <p class="page-subtitle-modern">Gerencie as conexões com bancos de dados externos</p>
    </div>
    <button class="btn-modern-primary ms-auto" data-bs-toggle="modal" data-bs-target="#modalConexao" onclick="novaConexao()">
        <i class="bi bi-plus-lg me-2"></i>Nova Conexão
    </button>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4" id="statsCards">
    <div class="col-md-3">
        <div class="stat-card-modern info-card">
            <div class="stat-icon-modern">
                <i class="bi bi-hdd-network"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="totalConexoes">0</div>
                <div class="stat-label-modern">Total de Conexões</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card-modern success-card">
            <div class="stat-icon-modern">
                <i class="bi bi-filetype-sql"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="totalPostgres">0</div>
                <div class="stat-label-modern">PostgreSQL</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card-modern primary-card">
            <div class="stat-icon-modern">
                <i class="bi bi-database"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="totalMySQL">0</div>
                <div class="stat-label-modern">MySQL</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card-modern danger-card">
            <div class="stat-icon-modern">
                <i class="bi bi-microsoft"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="totalSQLServer">0</div>
                <div class="stat-label-modern">SQL Server</div>
            </div>
        </div>
    </div>
</div>

<div class="card-modern">
    <div class="card-modern-header">
        <i class="bi bi-list me-2"></i>Lista de Conexões
    </div>
    <div class="card-modern-body">
        <div class="table-responsive">
            <table class="table-modern" id="tblConexoes">
                <thead>
                    <tr>
                        <th><i class="bi bi-tag me-2"></i>Nome</th>
                        <th><i class="bi bi-database me-2"></i>Tipo</th>
                        <th><i class="bi bi-server me-2"></i>Host</th>
                        <th><i class="bi bi-folder2-open me-2"></i>Banco</th>
                        <th><i class="bi bi-check-circle me-2"></i>Status</th>
                        <th width="150"><i class="bi bi-gear me-2"></i>Ações</th>
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

<!-- Modal Conexão -->
<div class="modal fade modal-modern" id="modalConexao" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header-modern">
                <h5 class="modal-title"><i class="bi bi-hdd-network me-2"></i><span id="modalTitle">Nova Conexão</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formConexao">
                <input type="hidden" name="id" id="conexaoId">
                <div class="modal-body-modern">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-modern">Nome da Conexão *</label>
                            <input type="text" class="form-control-modern" name="nome_conexao" id="nome_conexao" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Tipo de Banco *</label>
                            <select class="form-select-modern" name="tipo_banco" id="tipo_banco" required>
                                <option value="postgres">PostgreSQL</option>
                                <option value="mysql">MySQL</option>
                                <option value="sqlserver">SQL Server</option>
                                <option value="oracle">Oracle</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label-modern">Host *</label>
                            <input type="text" class="form-control-modern" name="host" id="host" placeholder="localhost" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-modern">Porta *</label>
                            <input type="number" class="form-control-modern" name="porta" id="porta" placeholder="5432" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Nome do Banco *</label>
                            <input type="text" class="form-control-modern" name="nome_banco" id="nome_banco" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Usuário *</label>
                            <input type="text" class="form-control-modern" name="usuario" id="usuario" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label-modern">Senha</label>
                            <div class="input-group">
                                <input type="password" class="form-control-modern" name="senha" id="senha" placeholder="Deixe vazio para manter a atual">
                                <button type="button" class="btn btn-outline-secondary" onclick="toggleSenha()">
                                    <i class="bi bi-eye" id="iconSenha"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="resultadoTeste" class="mt-3" style="display: none;"></div>
                </div>
                <div class="modal-footer-modern">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn-modern-outline" id="btnTestar" onclick="testarConexao()">
                        <i class="bi bi-plug me-2"></i>Testar Conexão
                    </button>
                    <button type="submit" class="btn-modern-primary" id="btnSalvar">
                        <i class="bi bi-check-lg me-2"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$extraStyles = <<<'STYLES'
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
:root {
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --gradient-danger: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    --gradient-info: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    --shadow-sm: 0 2px 8px rgba(0,0,0,0.06);
    --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
    --shadow-lg: 0 8px 32px rgba(0,0,0,0.12);
    --radius-md: 12px;
    --radius-lg: 16px;
}

.page-header-modern {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #f1f5f9;
}

.page-icon-modern {
    width: 64px;
    height: 64px;
    border-radius: var(--radius-lg);
    background: var(--gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    box-shadow: var(--shadow-md);
}

.page-title-modern {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-subtitle-modern {
    color: #64748b;
    margin: 0;
    font-size: 0.95rem;
}

.stat-card-modern {
    background: white;
    border-radius: var(--radius-md);
    padding: 1.5rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}

.stat-card-modern:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-md);
}

.stat-icon-modern {
    width: 56px;
    height: 56px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    flex-shrink: 0;
}

.info-card .stat-icon-modern { background: var(--gradient-info); }
.success-card .stat-icon-modern { background: var(--gradient-success); }
.primary-card .stat-icon-modern { background: var(--gradient-primary); }
.danger-card .stat-icon-modern { background: var(--gradient-danger); }

.stat-content {
    flex: 1;
    min-width: 0;
}

.stat-value-modern {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label-modern {
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 500;
}

.card-modern {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.card-modern-header {
    padding: 1.25rem 1.5rem;
    background: linear-gradient(to right, #f8fafc, #f1f5f9);
    border-bottom: 2px solid #e2e8f0;
    font-weight: 600;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-modern-body {
    padding: 1.5rem;
}

.table-modern {
    width: 100%;
    margin: 0;
}

.table-modern thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.table-modern thead th {
    padding: 1rem;
    font-weight: 600;
    border: none;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-modern thead th:first-child {
    border-top-left-radius: var(--radius-md);
}

.table-modern thead th:last-child {
    border-top-right-radius: var(--radius-md);
}

.table-modern tbody tr {
    border-bottom: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.table-modern tbody tr:hover {
    background-color: #f8fafc;
    transform: scale(1.01);
}

.table-modern tbody td {
    padding: 1rem;
    vertical-align: middle;
}

.btn-modern-primary {
    background: var(--gradient-primary);
    color: white;
    padding: 0.625rem 1.25rem;
    border-radius: var(--radius-md);
    border: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-sm);
}

.btn-modern-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: white;
}

.btn-modern-outline {
    background: white;
    color: #667eea;
    padding: 0.625rem 1.25rem;
    border-radius: var(--radius-md);
    border: 2px solid #667eea;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-modern-outline:hover {
    background: var(--gradient-primary);
    color: white;
    border-color: transparent;
}

.form-label-modern {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    display: block;
}

.form-control-modern, .form-select-modern {
    width: 100%;
    border: 2px solid #e2e8f0;
    border-radius: var(--radius-md);
    padding: 0.75rem 1rem;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
    background-color: #ffffff;
    color: #1e293b;
    line-height: 1.5;
}

.form-control-modern:hover, .form-select-modern:hover {
    border-color: #cbd5e1;
}

.form-control-modern:focus, .form-select-modern:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    outline: none;
    background-color: #ffffff;
}

.form-control-modern::placeholder {
    color: #94a3b8;
}

.form-control-modern:disabled,
.form-select-modern:disabled {
    background-color: #f1f5f9;
    cursor: not-allowed;
    opacity: 0.6;
}

textarea.form-control-modern {
    resize: vertical;
    min-height: 80px;
}

.modal-modern .modal-header-modern {
    background: var(--gradient-primary);
    color: white;
    padding: 1.25rem 1.5rem;
    border-bottom: none;
}

.modal-modern .modal-header-modern .modal-title {
    font-weight: 600;
    font-size: 1.25rem;
}

.modal-modern .modal-header-modern .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.8;
}

.modal-modern .modal-body-modern {
    padding: 1.5rem;
}

.modal-modern .modal-footer-modern {
    padding: 1rem 1.5rem;
    border-top: 2px solid #e2e8f0;
    background: #f8fafc;
}

.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.75rem;
}

.badge-info {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

@media (max-width: 991px) {
    .page-header-modern {
        flex-wrap: wrap;
    }
    
    .page-header-modern .ms-auto {
        margin-left: 0 !important;
        width: 100%;
    }
    
    .stat-value-modern {
        font-size: 1.5rem;
    }
}

@media (max-width: 767px) {
    .page-icon-modern {
        width: 48px;
        height: 48px;
        font-size: 1.5rem;
    }
    
    .page-title-modern {
        font-size: 1.5rem;
    }
    
    .stat-card-modern {
        padding: 1rem;
    }
    
    .stat-icon-modern {
        width: 48px;
        height: 48px;
        font-size: 1.25rem;
    }
    
    .table-modern {
        font-size: 0.875rem;
    }
}
</style>
STYLES;

$extraScripts = <<<'SCRIPTS'
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let tabela;

const tipoIcons = {
    postgres: "bi-filetype-sql",
    mysql: "bi-filetype-sql",
    sqlserver: "bi-microsoft",
    oracle: "bi-database"
};

const portasPadrao = {
    postgres: 5432,
    mysql: 3306,
    sqlserver: 1433,
    oracle: 1521
};

function atualizarStats(data) {
    const stats = {
        total: 0,
        postgres: 0,
        mysql: 0,
        sqlserver: 0
    };
    
    (data || []).forEach(r => {
        stats.total++;
        if (r.tipo_banco === 'postgres') stats.postgres++;
        else if (r.tipo_banco === 'mysql') stats.mysql++;
        else if (r.tipo_banco === 'sqlserver') stats.sqlserver++;
    });
    
    document.getElementById('totalConexoes').textContent = stats.total;
    document.getElementById('totalPostgres').textContent = stats.postgres;
    document.getElementById('totalMySQL').textContent = stats.mysql;
    document.getElementById('totalSQLServer').textContent = stats.sqlserver;
}

function loadTable() {
    $.getJSON(baseUrl + "/conexoes/list", function(res) {
        // Atualizar estatísticas
        atualizarStats(res.data);
        
        // Destruir DataTable se já existir
        if ($.fn.DataTable.isDataTable("#tblConexoes")) {
            $("#tblConexoes").DataTable().destroy();
        }
        
        const tbody = $("#tblConexoes tbody");
        tbody.empty();
        
        (res.data || []).forEach(function(r) {
            const icon = tipoIcons[r.tipo_banco] || "bi-database";
            tbody.append(`<tr>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2">
                            <i class="${icon} text-primary"></i>
                        </div>
                        <strong>${r.nome_conexao}</strong>
                    </div>
                </td>
                <td><span class="badge bg-secondary">${r.tipo_banco}</span></td>
                <td>${r.host}</td>
                <td>${r.nome_banco}</td>
                <td><span class="badge-status badge-info"><i class="bi bi-check-circle me-1"></i>Configurada</span></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-info" onclick="testarConexaoId(${r.id})" title="Testar">
                            <i class="bi bi-plug"></i>
                        </button>
                        <button class="btn btn-outline-primary" onclick="editarConexao(${r.id})" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="excluirConexao(${r.id})" title="Excluir">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>`);
        });
        
        // Inicializar DataTable após popular
        tabela = $("#tblConexoes").DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json" },
            order: [[0, "asc"]]
        });
    });
}

function novaConexao() {
    document.getElementById("formConexao").reset();
    document.getElementById("conexaoId").value = "";
    document.getElementById("modalTitle").textContent = "Nova Conexão";
    document.getElementById("resultadoTeste").style.display = "none";
}

function editarConexao(id) {
    $.getJSON(baseUrl + "/conexoes/get/" + id, function(r) {
        document.getElementById("conexaoId").value = r.id;
        document.getElementById("nome_conexao").value = r.nome_conexao;
        document.getElementById("tipo_banco").value = r.tipo_banco;
        document.getElementById("host").value = r.host;
        document.getElementById("porta").value = r.porta;
        document.getElementById("nome_banco").value = r.nome_banco;
        document.getElementById("usuario").value = r.usuario;
        document.getElementById("senha").value = "";
        document.getElementById("modalTitle").textContent = "Editar Conexão";
        document.getElementById("resultadoTeste").style.display = "none";
        new bootstrap.Modal("#modalConexao").show();
    });
}

function excluirConexao(id) {
    Swal.fire({
        title: "Excluir conexão?",
        text: "Esta ação não pode ser desfeita!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Sim, excluir"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(baseUrl + "/conexoes/delete/" + id, function(res) {
                Swal.fire("Excluída!", "Conexão removida com sucesso.", "success");
                loadTable();
            });
        }
    });
}

function testarConexao() {
    const btn = document.getElementById("btnTestar");
    const resultado = document.getElementById("resultadoTeste");
    
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Testando...`;
    
    $.post(baseUrl + "/conexoes/test", $("#formConexao").serialize(), function(res) {
        resultado.style.display = "block";
        if (res.sucesso) {
            resultado.className = "alert alert-success";
            resultado.innerHTML = `<i class="bi bi-check-circle me-2"></i>${res.mensagem}`;
        } else {
            resultado.className = "alert alert-danger";
            resultado.innerHTML = `<i class="bi bi-x-circle me-2"></i>${res.mensagem || res.erro}`;
        }
    }, "json").always(function() {
        btn.disabled = false;
        btn.innerHTML = `<i class="bi bi-plug me-2"></i>Testar Conexão`;
    });
}

function testarConexaoId(id) {
    Swal.fire({
        title: "Testando conexão...",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    $.getJSON(baseUrl + "/conexoes/get/" + id, function(r) {
        $.post(baseUrl + "/conexoes/test", r, function(res) {
            if (res.sucesso) {
                Swal.fire("Sucesso!", res.mensagem, "success");
            } else {
                Swal.fire("Erro!", res.mensagem || res.erro, "error");
            }
        }, "json");
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

// Alterar porta padrão ao mudar tipo de banco
document.getElementById("tipo_banco").addEventListener("change", function() {
    document.getElementById("porta").value = portasPadrao[this.value] || "";
});

// Submit form
document.getElementById("formConexao").addEventListener("submit", function(e) {
    e.preventDefault();
    
    const btn = document.getElementById("btnSalvar");
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Salvando...`;
    
    $.post(baseUrl + "/conexoes/salvar", $(this).serialize(), function(res) {
        if (res.sucesso) {
            bootstrap.Modal.getInstance("#modalConexao").hide();
            Swal.fire("Salvo!", res.mensagem || "Conexão salva com sucesso.", "success");
            loadTable();
        } else {
            Swal.fire("Erro!", res.mensagem || res.erro, "error");
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

include __DIR__ . '/layouts/base.php';
?>
