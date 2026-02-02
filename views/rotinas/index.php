<?php
/**
 * DMC DataLoad - Listagem de Rotinas
 */
$pageTitle = 'Rotinas ETL';
$currentPage = 'rotinas';

ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Rotinas ETL</h2>
        <p class="text-muted mb-0">Gerencie rotinas de extração, transformação e carga de dados</p>
    </div>
    <button type="button" id="btnNovo" class="btn btn-primary">
        <i class="bi bi-plus-lg me-2"></i>Nova Rotina
    </button>
</div>

<div class="card">
    <div class="card-body">
        <table id="tblRotinas" class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Conexão</th>
                    <th>Status</th>
                    <th>Ativa</th>
                    <th>Última Execução</th>
                    <th style="width: 200px;">Ações</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();

$extraStyles = '
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
<style>
.badge-ativa { background: #22c55e; }
.badge-inativa { background: #94a3b8; }
.badge-executando { background: #f59e0b; }
</style>
';

$extraScripts = '
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let tabela;

function loadRotinas() {
    $.getJSON(baseUrl + "/rotinas/list", function(res) {
        if (!res.data) {
            console.error("Resposta sem data:", res);
            return;
        }
        
        // Destruir tabela se existir
        if ($.fn.DataTable.isDataTable("#tblRotinas")) {
            $("#tblRotinas").DataTable().destroy();
        }
        
        // Limpar tbody
        $("#tblRotinas tbody").empty();
        
        // Adicionar linhas
        res.data.forEach(function(r) {
            const statusBadge = r.esta_executando 
                ? \'<span class="badge badge-executando">Executando</span>\'
                : \'<span class="badge bg-secondary">Parada</span>\';
            
            const ativaBadge = r.ativa 
                ? \'<span class="badge badge-ativa">Ativa</span>\'
                : \'<span class="badge badge-inativa">Inativa</span>\';
            
            const ultimaExec = r.ultima_execucao 
                ? new Date(r.ultima_execucao).toLocaleString("pt-BR")
                : \'<span class="text-muted">Nunca</span>\';
            
            const btnExecutar = \'<button class="btn btn-sm btn-success btn-executar" data-id="\' + r.id + \'" title="Executar"><i class="bi bi-play-fill"></i></button>\';
            const btnEditar = \'<button class="btn btn-sm btn-primary btn-editar" data-id="\' + r.id + \'" title="Editar"><i class="bi bi-pencil"></i></button>\';
            const btnDeletar = \'<button class="btn btn-sm btn-danger btn-deletar" data-id="\' + r.id + \'" title="Deletar"><i class="bi bi-trash"></i></button>\';
            
            const acoes = \'<div class="btn-group" role="group">\' + btnExecutar + btnEditar + btnDeletar + \'</div>\';
            
            $("#tblRotinas tbody").append(
                \'<tr>\' +
                \'<td>\' + r.id + \'</td>\' +
                \'<td><strong>\' + r.nome + \'</strong><br><small class="text-muted">\' + (r.descricao || \'\') + \'</small></td>\' +
                \'<td>\' + r.nome_conexao + \'</td>\' +
                \'<td>\' + statusBadge + \'</td>\' +
                \'<td>\' + ativaBadge + \'</td>\' +
                \'<td><small>\' + ultimaExec + \'</small></td>\' +
                \'<td>\' + acoes + \'</td>\' +
                \'</tr>\'
            );
        });
        
        // Inicializar DataTable
        tabela = $("#tblRotinas").DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
            },
            order: [[0, "desc"]],
            pageLength: 25
        });
    }).fail(function(xhr) {
        Swal.fire("Erro", "Falha ao carregar rotinas: " + xhr.responseText, "error");
    });
}

// Eventos
$("#btnNovo").on("click", function() {
    window.location.href = baseUrl + "/rotinas/editor";
});

$(document).on("click", ".btn-editar", function() {
    const id = $(this).data("id");
    window.location.href = baseUrl + "/rotinas/editor?id=" + id;
});

$(document).on("click", ".btn-executar", function() {
    const id = $(this).data("id");
    const btn = $(this);
    
    Swal.fire({
        title: "Executar rotina?",
        text: "A rotina será executada agora",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sim, executar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            btn.prop("disabled", true).html(\'<span class="spinner-border spinner-border-sm"></span>\');
            
            $.ajax({
                url: baseUrl + "/rotinas/run/" + id,
                type: "POST",
                dataType: "json",
                success: function(res) {
                    btn.prop("disabled", false).html("<i class=\"bi bi-play-fill\"></i>");
                    console.log("Resposta da execução:", res);
                    
                    if (res.sucesso) {
                        Swal.fire({
                            title: "Sucesso!", 
                            text: res.mensagem || "Rotina executada com sucesso",
                            icon: "success"
                        });
                        loadRotinas();
                    } else {
                        Swal.fire({
                            title: "Erro na Execução", 
                            html: "<strong>Erro:</strong><br>" + (res.erro || res.mensagem || "Erro desconhecido"),
                            icon: "error"
                        });
                    }
                },
                error: function(xhr, status, error) {
                    btn.prop("disabled", false).html("<i class=\"bi bi-play-fill\"></i>");
                    console.error("Erro na execução:", xhr.responseText);
                    console.error("Status:", status, "Error:", error);
                    
                    let errorMsg = "Falha na comunicação com o servidor";
                    try {
                        const jsonResponse = JSON.parse(xhr.responseText);
                        errorMsg = jsonResponse.erro || jsonResponse.mensagem || errorMsg;
                    } catch(e) {
                        if (xhr.responseText) {
                            errorMsg = xhr.responseText;
                        }
                    }
                    
                    Swal.fire({
                        title: "Erro de Comunicação",
                        html: "<strong>Detalhes:</strong><br>" + errorMsg,
                        icon: "error"
                    });
                }
            });
        }
    });
});

$(document).on("click", ".btn-deletar", function() {
    const id = $(this).data("id");
    
    Swal.fire({
        title: "Deletar rotina?",
        text: "Esta ação não pode ser desfeita!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        confirmButtonText: "Sim, deletar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(baseUrl + "/rotinas/delete/" + id, function(res) {
                if (res.sucesso) {
                    Swal.fire("Deletado!", "Rotina removida com sucesso", "success");
                    loadRotinas();
                } else {
                    Swal.fire("Erro", res.mensagem || "Erro ao deletar", "error");
                }
            }, "json").fail(function() {
                Swal.fire("Erro", "Falha na comunicação", "error");
            });
        }
    });
});

// Carregar ao iniciar
$(document).ready(function() {
    loadRotinas();
});
</script>
';

include __DIR__ . '/../layouts/base.php';
?>
