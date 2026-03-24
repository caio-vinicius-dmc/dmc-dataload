<?php
/**
 * DMC DataLoad - Editor de Rotina
 * Nova UI Moderna
 */
$pageTitle = 'Editor de Rotina';
$currentPage = 'rotinas';
$csrfToken = App\Core\AuthMiddleware::gerarTokenCSRF();

ob_start();
?>

<!-- Page Header Modern -->
<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-pencil-square"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Editor de Rotina</h1>
        <p class="page-subtitle-modern">Configure rotinas de ETL com múltiplos blocos SQL</p>
    </div>
    <div class="d-flex gap-2 ms-auto">
        <a href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/rotinas" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </a>
        <button type="button" class="btn btn-outline-secondary" onclick="abrirModalCompartilhamento('rotina', $('#rotina_id').val())" title="Compartilhar">
            <i class="bi bi-share me-2"></i>Compartilhar
        </button>
        <button type="button" id="btnSalvar" class="btn-modern-primary">
            <i class="bi bi-check-lg me-2"></i>Salvar Rotina
        </button>
    </div>
</div>

<!-- Informações da Rotina -->
<div class="card-modern mb-4">
    <div class="card-modern-header">
        <i class="bi bi-info-circle me-2"></i>Informações da Rotina
    </div>
    <div class="card-modern-body">
        <form id="form-editor">
            <input type="hidden" name="id" id="rotina_id">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label-modern">Nome da Rotina *</label>
                    <input type="text" class="form-control-modern" name="nome" id="rotina_nome" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-modern">Conexão *</label>
                    <select class="form-select-modern" name="id_conexao" id="select-conexao" required>
                        <option value="">Selecione uma conexão...</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label-modern">Descrição</label>
                    <textarea class="form-control-modern" name="descricao" id="rotina_descricao" rows="2"></textarea>
                </div>

                <!-- Opções de controle de erro -->
                <div class="col-12">
                    <label class="form-label-modern mb-2"><i class="bi bi-shield-exclamation me-1"></i>Comportamento em Caso de Erro</label>
                    <div class="erro-options-container">
                        <div class="erro-option">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="chk_parar_em_erro" name="parar_em_erro" value="1">
                                <label class="form-check-label" for="chk_parar_em_erro">
                                    <strong>Parar execução em caso de erro</strong>
                                    <small class="d-block text-muted">Se um bloco falhar, os blocos seguintes não serão executados. O status será "parcial".</small>
                                </label>
                            </div>
                        </div>
                        <div class="erro-option">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="chk_rollback_em_erro" name="rollback_em_erro" value="1">
                                <label class="form-check-label" for="chk_rollback_em_erro">
                                    <strong>Rollback em caso de erro</strong>
                                    <small class="d-block text-muted">Se um bloco falhar, todas as alterações anteriores (INSERT/UPDATE/DELETE) serão desfeitas. Requer "Parar execução" ativo. <span class="text-warning">DDL (CREATE/ALTER/DROP) pode não ser revertido em alguns bancos.</span></small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php include __DIR__ . '/../partials/recurso_empresa_projeto.php'; ?>
            </div>
        </form>
    </div>
</div>

<!-- Blocos SQL -->
<div class="card-modern">
    <div class="card-modern-header d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-braces me-2"></i>Blocos SQL
        </div>
        <div class="dropdown">
            <button class="btn-modern-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-plus-lg me-2"></i>Adicionar Bloco
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="addBloco('', 'SELECT'); return false;"><i class="bi bi-search me-2"></i>SELECT</a></li>
                <li><a class="dropdown-item" href="#" onclick="addBloco('', 'INSERT'); return false;"><i class="bi bi-plus-square me-2"></i>INSERT</a></li>
                <li><a class="dropdown-item" href="#" onclick="addBloco('', 'UPDATE'); return false;"><i class="bi bi-pencil-square me-2"></i>UPDATE</a></li>
                <li><a class="dropdown-item" href="#" onclick="addBloco('', 'DELETE'); return false;"><i class="bi bi-trash me-2"></i>DELETE</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="addBloco('', 'MERGE'); return false;"><i class="bi bi-arrow-down-up me-2"></i>MERGE</a></li>
                <li><a class="dropdown-item" href="#" onclick="addBloco('', 'TRUNCATE'); return false;"><i class="bi bi-eraser me-2"></i>TRUNCATE</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="addBloco('', 'CREATE'); return false;"><i class="bi bi-file-earmark-plus me-2"></i>CREATE</a></li>
                <li><a class="dropdown-item" href="#" onclick="addBloco('', 'ALTER'); return false;"><i class="bi bi-gear me-2"></i>ALTER</a></li>
                <li><a class="dropdown-item" href="#" onclick="addBloco('', 'DROP'); return false;"><i class="bi bi-x-circle me-2"></i>DROP</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="addBloco('', 'PROCEDURE'); return false;"><i class="bi bi-code-square me-2"></i>PROCEDURE</a></li>
                <li><a class="dropdown-item" href="#" onclick="addBloco('', 'FUNCTION'); return false;"><i class="bi bi-box me-2"></i>FUNCTION</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="addBloco('', 'GRANT'); return false;"><i class="bi bi-shield-check me-2"></i>GRANT</a></li>
                <li><a class="dropdown-item" href="#" onclick="addBloco('', 'REVOKE'); return false;"><i class="bi bi-shield-x me-2"></i>REVOKE</a></li>
                <li><a class="dropdown-item" href="#" onclick="addBloco('', 'CALL'); return false;"><i class="bi bi-telephone me-2"></i>CALL / EXEC</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" onclick="addBloco('', 'OUTROS'); return false;"><i class="bi bi-three-dots me-2"></i>OUTROS</a></li>
            </ul>
        </div>
    </div>
    <div class="card-modern-body">
        <div id="lista-blocos" class="sortable-container">
            <div class="text-center py-5 text-muted" id="empty-message">
                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                <p>Nenhum bloco adicionado ainda. Clique em "Adicionar Bloco" para começar.</p>
            </div>
        </div>

        <!-- Botões de ação no rodapé -->
        <div class="blocos-bottom-actions" id="bottomActions" style="display:none;">
            <div class="dropup">
                <button class="btn-modern-outline btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-plus-lg me-2"></i>Adicionar Bloco
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="addBloco('', 'SELECT'); return false;"><i class="bi bi-search me-2"></i>SELECT</a></li>
                    <li><a class="dropdown-item" href="#" onclick="addBloco('', 'INSERT'); return false;"><i class="bi bi-plus-square me-2"></i>INSERT</a></li>
                    <li><a class="dropdown-item" href="#" onclick="addBloco('', 'UPDATE'); return false;"><i class="bi bi-pencil-square me-2"></i>UPDATE</a></li>
                    <li><a class="dropdown-item" href="#" onclick="addBloco('', 'DELETE'); return false;"><i class="bi bi-trash me-2"></i>DELETE</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" onclick="addBloco('', 'MERGE'); return false;"><i class="bi bi-arrow-down-up me-2"></i>MERGE</a></li>
                    <li><a class="dropdown-item" href="#" onclick="addBloco('', 'TRUNCATE'); return false;"><i class="bi bi-eraser me-2"></i>TRUNCATE</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" onclick="addBloco('', 'CREATE'); return false;"><i class="bi bi-file-earmark-plus me-2"></i>CREATE</a></li>
                    <li><a class="dropdown-item" href="#" onclick="addBloco('', 'ALTER'); return false;"><i class="bi bi-gear me-2"></i>ALTER</a></li>
                    <li><a class="dropdown-item" href="#" onclick="addBloco('', 'DROP'); return false;"><i class="bi bi-x-circle me-2"></i>DROP</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" onclick="addBloco('', 'PROCEDURE'); return false;"><i class="bi bi-code-square me-2"></i>PROCEDURE</a></li>
                    <li><a class="dropdown-item" href="#" onclick="addBloco('', 'FUNCTION'); return false;"><i class="bi bi-box me-2"></i>FUNCTION</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" onclick="addBloco('', 'GRANT'); return false;"><i class="bi bi-shield-check me-2"></i>GRANT</a></li>
                    <li><a class="dropdown-item" href="#" onclick="addBloco('', 'REVOKE'); return false;"><i class="bi bi-shield-x me-2"></i>REVOKE</a></li>
                    <li><a class="dropdown-item" href="#" onclick="addBloco('', 'CALL'); return false;"><i class="bi bi-telephone me-2"></i>CALL / EXEC</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" onclick="addBloco('', 'OUTROS'); return false;"><i class="bi bi-three-dots me-2"></i>OUTROS</a></li>
                </ul>
            </div>
            <button type="button" class="btn-modern-primary btn-sm" onclick="$('#btnSalvar').click()">
                <i class="bi bi-check-lg me-2"></i>Salvar Rotina
            </button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/compartilhamento_modal.php'; ?>

<?php
$content = ob_get_clean();

$extraStyles = <<<'STYLES'
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/theme/dracula.min.css" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
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
    background: white;
    padding: 1.75rem 2rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.page-icon-modern {
    width: 70px;
    height: 70px;
    border-radius: var(--radius-lg);
    background: var(--gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
    flex-shrink: 0;
}

.page-title-modern {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 0.25rem 0;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-subtitle-modern {
    color: #64748b;
    margin: 0;
    font-size: 1rem;
}

.card-modern {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    border: 1px solid #e2e8f0;
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
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
}

.card-modern-body {
    padding: 1.5rem;
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

.bloco-item {
    background: white;
    border-radius: 12px;
    padding: 0;
    margin-bottom: 16px;
    border: 2px solid #e5e7eb;
    transition: all 0.3s;
}
.bloco-item:hover {
    border-color: #6366f1;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
}
.bloco-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
    cursor: pointer;
    user-select: none;
    border-radius: 12px 12px 0 0;
}
.bloco-header:hover {
    background: #f3f4f6;
}
.bloco-content {
    padding: 20px;
    display: block;
}
.bloco-content.collapsed {
    display: none;
}
.bloco-summary {
    padding: 12px 20px;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
    font-size: 0.875rem;
    color: #6b7280;
    display: none;
}
.bloco-summary.show {
    display: block;
}
.drag-handle {
    cursor: move;
    padding: 8px 12px;
    background: white;
    border-radius: 8px;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 8px;
    user-select: none;
    border: 1px solid #e5e7eb;
}
.drag-handle:hover {
    background: #f3f4f6;
    color: #374151;
}
.CodeMirror {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    height: auto;
}
.CodeMirror-scroll {
    min-height: 150px;
}
.tipo-badge {
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.tipo-SELECT { background: #dbeafe; color: #1e40af; }
.tipo-INSERT { background: #dcfce7; color: #166534; }
.tipo-UPDATE { background: #fef3c7; color: #92400e; }
.tipo-DELETE { background: #fee2e2; color: #991b1b; }
.tipo-MERGE { background: #e0e7ff; color: #3730a3; }
.tipo-TRUNCATE { background: #fecaca; color: #7f1d1d; }
.tipo-CREATE { background: #d1fae5; color: #065f46; }
.tipo-ALTER { background: #fde68a; color: #78350f; }
.tipo-DROP { background: #fca5a5; color: #7f1d1d; }
.tipo-PROCEDURE { background: #c7d2fe; color: #3730a3; }
.tipo-FUNCTION { background: #bfdbfe; color: #1e3a8a; }
.tipo-GRANT { background: #d1fae5; color: #047857; }
.tipo-REVOKE { background: #fce7f3; color: #9d174d; }
.tipo-CALL { background: #e0e7ff; color: #4338ca; }
.tipo-OUTROS { background: #f3f4f6; color: #374151; }
.sortable-container {
    min-height: 100px;
}
.card-modern .dropdown-menu,
.blocos-bottom-actions .dropdown-menu {
    max-height: 350px;
    overflow-y: auto;
    z-index: 1060;
}
.blocos-bottom-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    padding: 1.5rem;
    margin-top: 1rem;
    border-top: 2px dashed #e5e7eb;
    border-radius: 0 0 12px 12px;
}
.blocos-bottom-actions .btn-modern-outline {
    background: white;
    border: 2px solid #e2e8f0;
    color: #64748b;
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}
.blocos-bottom-actions .btn-modern-outline:hover {
    border-color: #667eea;
    color: #667eea;
    background: rgba(102, 126, 234, 0.05);
}
.erro-options-container {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}
.erro-option {
    flex: 1;
    min-width: 280px;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: var(--radius-md);
    padding: 1rem 1.25rem;
    transition: all 0.2s ease;
}
.erro-option:hover {
    border-color: #cbd5e1;
}
.erro-option .form-check-input:checked ~ .form-check-label {
    color: #1e293b;
}
.erro-option .form-check-input {
    width: 3em;
    height: 1.5em;
    cursor: pointer;
}
.erro-option .form-check-label {
    cursor: pointer;
    margin-left: 0.5rem;
}
.ui-sortable-helper {
    opacity: 0.8;
    transform: rotate(2deg);
}
.toggle-icon {
    transition: transform 0.3s;
}
.bloco-header:hover .toggle-icon {
    color: #6366f1;
}

@media (max-width: 991px) {
    .page-header-modern {
        flex-wrap: wrap;
    }
    
    .page-header-modern .ms-auto {
        margin-left: 0 !important;
        width: 100%;
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
}
</style>
STYLES;

$extraScripts = '<script>const csrfToken = \'' . htmlspecialchars($csrfToken, ENT_QUOTES) . '\';</script>';
$extraScripts .= <<<'SCRIPTS'
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/sql/sql.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
let blocoCounter = 0;

// Vincular rollback ao parar_em_erro
$("#chk_rollback_em_erro").on("change", function() {
    if ($(this).is(":checked")) {
        $("#chk_parar_em_erro").prop("checked", true);
    }
});
$("#chk_parar_em_erro").on("change", function() {
    if (!$(this).is(":checked")) {
        $("#chk_rollback_em_erro").prop("checked", false);
    }
});

function addBloco(codigo = "", tipo = "SELECT", sql = "") {
    blocoCounter++;
    const blocoId = "bloco_" + blocoCounter;
    
    // Ocultar mensagem vazia
    $("#empty-message").hide();
    
    const blocoHtml = `
        <div class="bloco-item" data-bloco-id="${blocoId}">
            <div class="bloco-header" onclick="toggleBloco('${blocoId}')">
                <div class="d-flex align-items-center gap-3">
                    <div class="drag-handle" onclick="event.stopPropagation()">
                        <i class="bi bi-grip-vertical"></i>
                    </div>
                    <span class="tipo-badge tipo-${tipo}">${tipo}</span>
                    <span class="bloco-titulo text-muted">${codigo || "Novo Bloco"}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-chevron-down toggle-icon"></i>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove" onclick="event.stopPropagation()">
                        <i class="bi bi-trash me-1"></i>Remover
                    </button>
                </div>
            </div>
            
            <div class="bloco-summary">
                <i class="bi bi-code-slash me-2"></i>
                <span class="sql-preview">SQL definido</span>
            </div>
            
            <div class="bloco-content">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label-modern">Código do Bloco</label>
                        <input name="bloco_codigo[]" class="form-control-modern bloco-codigo-input" placeholder="Ex: STEP_001" value="${codigo}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-modern">Tipo de Operação</label>
                        <select name="tipo_bloco[]" class="form-select-modern tipo-select">
                            <option value="SELECT">SELECT - Consulta</option>
                            <option value="INSERT">INSERT - Inserir</option>
                            <option value="UPDATE">UPDATE - Atualizar</option>
                            <option value="DELETE">DELETE - Deletar</option>
                            <option value="MERGE">MERGE - Mesclar</option>
                            <option value="TRUNCATE">TRUNCATE - Limpar Tabela</option>
                            <option value="CREATE">CREATE - Criar Objeto</option>
                            <option value="ALTER">ALTER - Alterar Objeto</option>
                            <option value="DROP">DROP - Remover Objeto</option>
                            <option value="PROCEDURE">PROCEDURE - Procedimento</option>
                            <option value="FUNCTION">FUNCTION - Função</option>
                            <option value="GRANT">GRANT - Conceder Permissão</option>
                            <option value="REVOKE">REVOKE - Revogar Permissão</option>
                            <option value="CALL">CALL / EXEC - Executar</option>
                            <option value="OUTROS">OUTROS - Comando Livre</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-0">
                    <label class="form-label-modern">Script SQL</label>
                    <textarea name="script_sql[]" class="form-control sql-editor" rows="6">${sql}</textarea>
                </div>
            </div>
        </div>
    `;
    
    $("#lista-blocos").append(blocoHtml);
    $("#bottomActions").show();
    
    const blocoElement = $(`[data-bloco-id="${blocoId}"]`);
    const textarea = blocoElement.find(".sql-editor")[0];
    
    // Inicializar CodeMirror
    const cm = CodeMirror.fromTextArea(textarea, {
        mode: "text/x-sql",
        theme: "dracula",
        lineNumbers: true,
        lineWrapping: true,
        autofocus: false,
        indentUnit: 2,
        tabSize: 2
    });
    
    textarea._cm = cm;
    
    // Set tipo value
    blocoElement.find(".tipo-select").val(tipo);
    
    // Atualizar título quando código muda
    blocoElement.find(".bloco-codigo-input").on("input", function() {
        const valor = $(this).val() || "Novo Bloco";
        blocoElement.find(".bloco-titulo").text(valor);
    });
    
    // Atualizar badge quando tipo muda
    blocoElement.find(".tipo-select").on("change", function() {
        const novoTipo = $(this).val();
        blocoElement.find(".tipo-badge")
            .removeClass(function(index, className) {
                return (className.match(/tipo-\S+/g) || []).join(" ");
            })
            .addClass("tipo-" + novoTipo)
            .text(novoTipo);
    });
    
    // Remover bloco
    blocoElement.find(".btn-remove").on("click", function() {
        Swal.fire({
            title: "Remover bloco?",
            text: "Esta ação não pode ser desfeita!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            cancelButtonText: "Cancelar",
            confirmButtonText: "Sim, remover"
        }).then((result) => {
            if (result.isConfirmed) {
                blocoElement.remove();
                if ($("#lista-blocos .bloco-item").length === 0) {
                    $("#empty-message").show();
                    $("#bottomActions").hide();
                }
            }
        });
    });
    
    return blocoElement;
}

// Toggle bloco
function toggleBloco(blocoId) {
    const bloco = $(`[data-bloco-id="${blocoId}"]`);
    const content = bloco.find(".bloco-content");
    const summary = bloco.find(".bloco-summary");
    const icon = bloco.find(".toggle-icon");
    
    if (content.hasClass("collapsed")) {
        content.removeClass("collapsed");
        summary.removeClass("show");
        icon.removeClass("bi-chevron-right").addClass("bi-chevron-down");
    } else {
        content.addClass("collapsed");
        summary.addClass("show");
        icon.removeClass("bi-chevron-down").addClass("bi-chevron-right");
    }
}

// Carregar conexões
function loadConexoes() {
    return $.getJSON(baseUrl + "/conexoes/list")
        .done(function(res) {
            console.log("Conexões carregadas:", res);
            const sel = $("#select-conexao");
            sel.find("option:not(:first)").remove();
            (res.data || []).forEach(function(c) {
                sel.append($("<option>").val(c.id).text(c.nome_conexao));
            });
            console.log("Total de conexões no select:", sel.find("option").length - 1);
        })
        .fail(function(xhr) {
            console.error("Erro ao carregar conexões:", xhr.responseText);
            Swal.fire("Erro", "Não foi possível carregar as conexões disponíveis", "error");
        });
}

// Salvar rotina
$("#btnSalvar").on("click", function() {
    // Validar campos obrigatórios
    const nome = $("#rotina_nome").val().trim();
    const conexao = $("#select-conexao").val();
    
    if (!nome) {
        Swal.fire("Atenção", "Informe o nome da rotina", "warning");
        return;
    }
    if (!conexao) {
        Swal.fire("Atenção", "Selecione uma conexão", "warning");
        return;
    }
    if ($("#lista-blocos .bloco-item").length === 0) {
        Swal.fire("Atenção", "Adicione pelo menos um bloco SQL", "warning");
        return;
    }
    
    // Validar empresa obrigatória
    if (document.querySelector('input[name="_rbac_presente"]')) {
        var empSel = (typeof rbacGetSelectedIds === 'function') ? rbacGetSelectedIds('empresas') : [];
        if (empSel.length === 0) {
            Swal.fire("Atenção", "Selecione ao menos uma empresa na seção de Visibilidade.", "warning");
            return;
        }
    }
    
    // Salvar CodeMirror para textareas
    $(".sql-editor").each(function() {
        try {
            if (this._cm && this._cm.save) {
                this._cm.save();
            }
        } catch(e) {}
    });
    
    // Coletar dados do formulário principal
    const formData = $("#form-editor").serializeArray();
    const payload = {};
    
    formData.forEach(function(field) {
        if (field.name.endsWith('[]')) {
            if (!payload[field.name]) payload[field.name] = [];
            payload[field.name].push(field.value);
        } else {
            payload[field.name] = field.value;
        }
    });
    
    // Coletar blocos manualmente (estão fora do form)
    const blocos = $("#lista-blocos .bloco-item");
    payload.bloco_codigo = [];
    payload.tipo_bloco = [];
    payload.script_sql = [];
    
    blocos.each(function() {
        const codigo = $(this).find(".bloco-codigo-input").val() || "";
        const tipo = $(this).find(".tipo-select").val() || "SELECT";
        const sql = $(this).find(".sql-editor").val() || ""
        
        payload.bloco_codigo.push(codigo);
        payload.tipo_bloco.push(tipo);
        payload.script_sql.push(sql);
    });
    
    // Debug - mostrar no console
    console.log("Payload a ser enviado:", payload);
    console.log("Total de blocos:", payload.bloco_codigo.length);
    
SCRIPTS;

// Adicionar ID do usuário da sessão
$extraScripts .= '    payload.id_usuario_criador = ' . ($_SESSION['usuario']['id'] ?? 1) . ';' . "\n";

$extraScripts .= <<<'SCRIPTS'
    
    // Adicionar CSRF token ao payload
    payload._csrf_token = csrfToken;
    
    // Salvar
    Swal.fire({
        title: "Salvando...",
        html: "<div class=\"spinner-border text-primary\"></div>",
        allowOutsideClick: false,
        showConfirmButton: false
    });
    
    $.post(baseUrl + "/rotinas/salvar", payload, function(res) {
        Swal.close();
        if (res.sucesso) {
            Swal.fire({
                title: "Sucesso!",
                text: "Rotina salva com sucesso",
                icon: "success"
            }).then(() => {
                window.location.href = baseUrl + "/rotinas";
            });
        } else {
            Swal.fire("Erro", res.erro || res.mensagem || "Erro ao salvar rotina", "error");
        }
    }, "json").fail(function(xhr, status, error) {
        Swal.close();
        console.error("Erro ao salvar:", {xhr, status, error, response: xhr.responseText});
        Swal.fire({
            title: "Erro ao Salvar",
            html: "Falha na comunicação com o servidor<br><small>Status: " + xhr.status + "</small><br><small>" + error + "</small>",
            icon: "error"
        });
    });
});

// Inicialização
$(document).ready(function() {
    console.log("=== EDITOR INICIALIZANDO ===");
    console.log("Base URL:", baseUrl);
    
    // Habilitar ordenação por drag & drop
    $("#lista-blocos").sortable({
        handle: ".drag-handle",
        axis: "y",
        placeholder: "ui-state-highlight",
        start: function(e, ui) {
            ui.placeholder.height(ui.item.height());
        }
    });
    
    // Verificar se deve carregar rotina
    const params = new URLSearchParams(window.location.search);
    const id = params.get("id");
    console.log("ID da rotina na URL:", id);
    
    // Carregar conexões PRIMEIRO (retorna Promise)
    loadConexoes().then(function() {
        console.log("Conexões carregadas com sucesso!");
        
        // Se houver ID, carregar rotina APÓS conexões estarem prontas
        if (id) {
            console.log("Carregando rotina ID:", id);
            
            // Mostrar loading
            Swal.fire({
                title: "Carregando...",
                html: "<div class=\"spinner-border text-primary\"></div>",
                allowOutsideClick: false,
                showConfirmButton: false
            });
            
            $.ajax({
                url: baseUrl + "/rotinas/get/" + id,
                dataType: 'json',
                cache: false
            })
                .done(function(res) {
                    console.log("=== RESPOSTA DA API ===");
                    console.log("Response completo:", res);
                    Swal.close();
                    
                    if (!res.rotina) {
                        console.error("Rotina não encontrada na resposta");
                        Swal.fire("Erro", "Rotina não encontrada", "error");
                        return;
                    }
                    
                    console.log("=== DADOS DA ROTINA ===");
                    console.log("ID:", res.rotina.id);
                    console.log("Nome:", res.rotina.nome);
                    console.log("Descrição:", res.rotina.descricao);
                    console.log("ID Conexão:", res.rotina.id_conexao);
                    
                    // Preencher campos com verificação
                    $("#rotina_id").val(res.rotina.id);
                    console.log("Campo rotina_id preenchido com:", $("#rotina_id").val());
                    
                    $("#rotina_nome").val(res.rotina.nome);
                    console.log("Campo rotina_nome preenchido com:", $("#rotina_nome").val());
                    
                    $("#rotina_descricao").val(res.rotina.descricao || "");
                    console.log("Campo rotina_descricao preenchido com:", $("#rotina_descricao").val());
                    
                    // Preencher opções de erro
                    if (res.rotina.parar_em_erro === true || res.rotina.parar_em_erro === 't' || res.rotina.parar_em_erro === '1') {
                        $("#chk_parar_em_erro").prop("checked", true);
                    }
                    if (res.rotina.rollback_em_erro === true || res.rotina.rollback_em_erro === 't' || res.rotina.rollback_em_erro === '1') {
                        $("#chk_rollback_em_erro").prop("checked", true);
                    }
                    
                    // Verificar se a conexão existe no select
                    const selectConexao = $("#select-conexao");
                    const opcaoExiste = selectConexao.find("option[value='" + res.rotina.id_conexao + "']").length > 0;
                    console.log("Conexão ID " + res.rotina.id_conexao + " existe no select?", opcaoExiste);
                    
                    if (opcaoExiste) {
                        selectConexao.val(res.rotina.id_conexao);
                        console.log("Select conexão definido para:", selectConexao.val());
                    } else {
                        console.warn("Conexão ID " + res.rotina.id_conexao + " não encontrada no select!");
                    }
                    
                    // Carregar blocos
                    console.log("=== BLOCOS ===");
                    console.log("Total de blocos:", res.blocos ? res.blocos.length : 0);
                    
                    if (Array.isArray(res.blocos) && res.blocos.length > 0) {
                        $("#empty-message").hide();
                        res.blocos.forEach(function(b, index) {
                            console.log("Bloco " + (index + 1) + ":", {
                                codigo: b.codigo_bloco,
                                tipo: b.tipo_bloco,
                                sql_length: (b.script_sql || "").length
                            });
                            addBloco(b.codigo_bloco, b.tipo_bloco, b.script_sql);
                        });
                        console.log("Todos os blocos adicionados!");
                    } else {
                        console.log("Nenhum bloco para carregar");
                    }
                    
                    console.log("=== CARREGAMENTO CONCLUÍDO ===");
                    
                    // Preencher empresas/projetos RBAC
                    var empIds = (res.empresas || []).map(function(e) { return parseInt(e.id_empresa || e.id || e, 10); });
                    var projIds = (res.projetos || []).map(function(p) { return parseInt(p.id_projeto || p.id || p, 10); });
                    if (typeof rbacCarregarOpcoes === 'function') {
                        window._rbacCarregado = false;
                        rbacCarregarOpcoes(function() { rbacPreencherSelects(empIds, projIds); });
                    }
                })
                .fail(function(xhr, status, error) {
                    Swal.close();
                    console.error("=== ERRO AO CARREGAR ROTINA ===");
                    console.error("Status:", status);
                    console.error("Error:", error);
                    console.error("Response Text:", xhr.responseText);
                    console.error("Status Code:", xhr.status);
                    
                    Swal.fire({
                        title: "Erro ao Carregar",
                        html: "Falha na comunicação com o servidor<br><small>Status: " + xhr.status + "</small><br><small>" + error + "</small>",
                        icon: "error"
                    });
                });
        } else {
            console.log("Nenhum ID na URL - modo criação");
        }
    }).catch(function(error) {
        console.error("Erro ao carregar conexões:", error);
    });
});
</script>
SCRIPTS;

$extraScripts .= '<script src="' . (defined('BASE_URL') ? BASE_URL : '') . '/assets/js/rbac-recurso.js"></script>';
$extraScripts .= '<script src="' . (defined('BASE_URL') ? BASE_URL : '') . '/assets/js/rbac-compartilhamento.js"></script>';

include __DIR__ . '/../layouts/base.php';
?>
