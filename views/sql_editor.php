<?php
/**
 * DMC DataLoad - SQL Editor
 * Editor SQL profissional com explorador de banco de dados
 */
$pageTitle = 'SQL Editor';
$currentPage = 'sql-editor';

ob_start();
?>

<!-- Page Header -->
<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-terminal-fill"></i>
    </div>
    <div>
        <h1 class="page-title-modern">SQL Editor</h1>
        <p class="page-subtitle-modern">Execute queries e explore seus bancos de dados com facilidade</p>
    </div>
</div>

<!-- Main Layout -->
<div class="sql-editor-container" id="sqlEditorContainer">
    <!-- Fullscreen Header -->
    <div class="fullscreen-header">
        <div class="fullscreen-title">
            <i class="bi bi-database-fill"></i>
            <span id="fullscreenTitle">SQL Editor - DBEaver Mode</span>
        </div>
        <div class="fullscreen-controls">
            <select class="form-select-fullscreen" id="selectRotinaFullscreen" style="min-width: 200px;">
                <option value="">📋 Selecione uma rotina...</option>
            </select>
            <select class="form-select-fullscreen" id="selectConexaoFullscreen" style="min-width: 200px;">
                <option value="">🔌 Selecione uma conexão...</option>
            </select>
            <button class="btn-execute-fullscreen" id="btnExecutarFullscreen" disabled>
                <i class="bi bi-play-fill"></i>
                Executar (F5)
            </button>
        </div>
        <button class="btn-exit-fullscreen" onclick="toggleFullscreen()">
            <i class="bi bi-fullscreen-exit"></i>
            Sair (ESC)
        </button>
    </div>
    
    <!-- Main Editor Area -->
    <div class="sql-main">
        <div class="sql-content">
            <!-- Sidebar Explorer -->
            <div class="sql-sidebar" id="sqlSidebar">
                <!-- Resize Handle -->
                <div class="resize-handle" id="sidebarResizeHandle"></div>
                
                <div class="sidebar-header">
                    <h6>
                        <i class="bi bi-database-fill"></i>
                        Database Explorer
                    </h6>
                    <button class="btn-icon" onclick="toggleSidebar()" title="Fechar Sidebar">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                
                <div class="sidebar-search">
                    <input type="text" class="form-control-modern" id="searchObjects" placeholder="🔍 Pesquisar objetos...">
                </div>
                
                <!-- Blocos da Rotina -->
                <div id="blocosContainer" style="display: none; border-bottom: 2px solid rgba(255,255,255,0.05);">
                    <div style="padding: 1rem 1.25rem; background: rgba(0,0,0,0.2); border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <h6 style="margin: 0; font-size: 0.9rem; color: #a5b4fc; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="bi bi-layers-fill"></i>
                            <span id="rotinaName">Blocos da Rotina</span>
                        </h6>
                    </div>
                    <div id="blocosList" style="padding: 0.75rem; max-height: 250px; overflow-y: auto;"></div>
                </div>
                
                <div class="sidebar-content" id="databaseTree">
                    <div class="empty-state">
                        <i class="bi bi-database"></i>
                        <p>Conecte-se a um banco de dados para explorar</p>
                    </div>
                </div>
            </div>
        
        <div class="sql-editor-wrapper">
        <!-- Toolbar -->
        <div class="sql-toolbar">
            <div class="toolbar-left">
                <button class="btn-toolbar" onclick="novaAba()" title="Nova Aba (Ctrl+T)">
                    <i class="bi bi-file-earmark-plus"></i>
                </button>
                <button class="btn-toolbar" onclick="fecharAbaAtual()" title="Fechar Aba (Ctrl+W)">
                    <i class="bi bi-x-circle"></i>
                </button>
                <div class="toolbar-divider"></div>
                <button class="btn-toolbar" onclick="formatarSQL()" title="Formatar SQL">
                    <i class="bi bi-code-square"></i>
                </button>
                <button class="btn-toolbar" onclick="limparEditor()" title="Limpar Editor">
                    <i class="bi bi-eraser-fill"></i>
                </button>
                <div class="toolbar-divider"></div>
                <button class="btn-toolbar" onclick="toggleSidebar()" title="Ocultar/Mostrar Database Explorer" id="btnToggleSidebar">
                    <i class="bi bi-layout-sidebar-inset"></i>
                </button>
                <button class="btn-toolbar" onclick="toggleResultsPanel()" title="Ocultar/Mostrar Resultados" id="btnToggleResults">
                    <i class="bi bi-layout-text-window-reverse"></i>
                </button>
                <div class="toolbar-divider"></div>
                <button class="btn-toolbar" onclick="toggleFullscreen()" title="Modo Tela Cheia (F11)" id="btnFullscreen">
                    <i class="bi bi-arrows-fullscreen"></i>
                </button>
                <button class="btn-toolbar" onclick="toggleLayoutOrientation()" title="Alternar Layout (Horizontal/Vertical)" id="btnLayout">
                    <i class="bi bi-layout-split"></i>
                </button>
                <div class="toolbar-divider"></div>
                <button class="btn-toolbar" onclick="mostrarAtalhos()" title="Atalhos de Teclado">
                    <i class="bi bi-keyboard"></i>
                </button>
                <div class="toolbar-divider"></div>
                <button class="btn-toolbar btn-executar" onclick="executarQueryToolbar()" title="Executar Query (F5 ou Ctrl+Enter)" id="btnExecutarToolbar" disabled>
                    <i class="bi bi-play-fill"></i>
                </button>
                <button class="btn-toolbar" onclick="toggleAutocomplete()" title="Ativar/Desativar Autocomplete Contínuo" id="btnAutocomplete">
                    <i class="bi bi-lightning"></i>
                </button>
                <div class="toolbar-divider"></div>
                                <button class="btn-toolbar" onclick="abrirArquivo()" title="Abrir Arquivo (Ctrl+O)">
                    <i class="bi bi-folder2-open"></i>
                </button>
                <button class="btn-toolbar" onclick="salvarScriptManual()" title="Salvar (Ctrl+S)" id="btnSalvarScript">
                    <i class="bi bi-floppy"></i>
                </button>
                <button class="btn-toolbar" onclick="salvarComo()" title="Salvar Como (Ctrl+Shift+S)" id="btnSalvarComo">
                    <i class="bi bi-floppy-fill"></i>
                </button>
                <button class="btn-toolbar" onclick="toggleAutoSave()" title="Ativar/Desativar Salvamento Automático" id="btnAutoSave">
                    <i class="bi bi-cloud-arrow-up"></i>
                </button>
            </div>
        </div>
        
        <!-- Input file escondido para abrir arquivos -->
        <input type="file" id="fileInput" accept=".sql,.txt" style="display: none;">

        <!-- Tabs -->
        <div class="sql-tabs" id="sqlTabs">
            <div class="tab-item active" data-tab="tab1">
                <span class="tab-title">Query 1</span>
                <span class="tab-connection-badge" title="Sem conexão">
                    <i class="bi bi-circle-fill"></i>
                </span>
                <button class="tab-close" onclick="fecharAba('tab1')">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>

        <!-- Editor Content -->
        <div class="sql-editor-area">
            <div class="tab-content active" data-tab="tab1">
                <!-- Tab Controls -->
                <div class="tab-controls">
                    <div style="display: flex; flex-direction: column; gap: 0.25rem; flex: 1; max-width: 280px;">
                        <label class="tab-controls-label">
                            <i class="bi bi-layers-fill"></i>
                            Rotina
                        </label>
                        <select class="form-select form-select-sm tab-select-rotina" data-tab="tab1">
                            <option value="">Selecione uma rotina...</option>
                        </select>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.25rem; flex: 1; max-width: 280px;">
                        <label class="tab-controls-label">
                            <i class="bi bi-database-fill"></i>
                            Conexão
                        </label>
                        <select class="form-select form-select-sm tab-select-conexao" data-tab="tab1">
                            <option value="">Selecione uma conexão...</option>
                        </select>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.25rem; max-width: 140px;">
                        <label class="tab-controls-label">
                            <i class="bi bi-hash"></i>
                            Limite de Linhas
                        </label>
                        <input type="number" class="form-control form-control-sm tab-limit-rows" data-tab="tab1" value="100" min="1" max="100000" title="Número máximo de linhas a retornar">
                    </div>
                    <button class="btn btn-sm btn-outline-danger tab-disconnect-btn" data-tab="tab1" title="Desconectar" style="display: none; align-self: flex-end;">
                        <i class="bi bi-plug"></i>
                        Desconectar
                    </button>
                </div>
                <textarea id="sqlEditor_tab1" class="sql-textarea"></textarea>
            </div>
        </div>

        </div>
        </div>
        
        <!-- Results Panel -->
        <div class="sql-results" id="sqlResults">
            <!-- Resize Handle Vertical (modo normal) -->
            <div class="resize-handle-horizontal" id="resultsResizeHandle"></div>
            <!-- Resize Handle Horizontal (modo horizontal 3 colunas) -->
            <div class="resize-handle-results" id="resultsResizeHandleHorizontal"></div>
            
            <div class="results-header">
                <div class="results-tabs">
                    <button class="result-tab active" data-result="table">
                        <i class="bi bi-table"></i>
                        Resultados
                    </button>
                    <button class="result-tab" data-result="messages">
                        <i class="bi bi-chat-left-text"></i>
                        Mensagens
                    </button>
                    <button class="result-tab" data-result="info">
                        <i class="bi bi-info-circle"></i>
                        Informações
                    </button>
                </div>
                <div class="results-actions">
                    <button class="btn-toolbar" onclick="exportarResultados('csv')" title="Exportar para CSV">
                        <i class="bi bi-download"></i>
                    </button>
                    <button class="btn-toolbar" onclick="limparResultados()" title="Limpar Resultados">
                        <i class="bi bi-trash3"></i>
                    </button>
                    <button class="btn-toolbar" onclick="toggleResultsPanel()" title="Expandir Resultados" id="btnToggleResults">
                        <i class="bi bi-chevron-up"></i>
                    </button>
                </div>
            </div>
            
            <div class="results-content">
                <div class="result-panel active" data-result="table">
                    <div class="table-responsive">
                        <table class="table table-modern" id="resultTable" style="display: none;">
                            <thead></thead>
                            <tbody></tbody>
                        </table>
                        <div class="empty-state" id="noResults">
                            <i class="bi bi-inbox"></i>
                            <p>Execute uma query SQL para visualizar os resultados aqui</p>
                        </div>
                    </div>
                </div>
                
                <div class="result-panel" data-result="messages">
                    <div class="messages-content" id="messagesContent">
                        <p class="text-muted">📋 Nenhuma mensagem ainda</p>
                    </div>
                </div>
                
                <div class="result-panel" data-result="info">
                    <div class="info-content" id="infoContent">
                        <p class="text-muted">ℹ️ Execute uma query para ver informações detalhadas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Atalhos -->
<div class="modal fade" id="modalAtalhos" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-keyboard me-2"></i>Atalhos de Teclado
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                            <i class="bi bi-file-earmark-code me-2"></i>Edição
                        </h6>
                        <table class="table table-sm table-hover">
                            <tbody>
                                <tr>
                                    <td><kbd>Ctrl</kbd> + <kbd>/</kbd></td>
                                    <td>Comentar/Descomentar linha</td>
                                </tr>
                                <tr>
                                    <td><kbd>Ctrl</kbd> + <kbd>D</kbd></td>
                                    <td>Deletar linha</td>
                                </tr>
                                <tr>
                                    <td><kbd>Ctrl</kbd> + <kbd>]</kbd></td>
                                    <td>Identar linha</td>
                                </tr>
                                <tr>
                                    <td><kbd>Ctrl</kbd> + <kbd>[</kbd></td>
                                    <td>Desidentar linha</td>
                                </tr>
                                <tr>
                                    <td><kbd>Ctrl</kbd> + <kbd>Scroll</kbd></td>
                                    <td>Zoom (aumentar/diminuir fonte)</td>
                                </tr>
                                <tr>
                                    <td><kbd>Ctrl</kbd> + <kbd>Space</kbd></td>
                                    <td>Autocomplete</td>
                                </tr>
                                <tr>
                                    <td><kbd>Ctrl</kbd> + <kbd>Z</kbd></td>
                                    <td>Desfazer</td>
                                </tr>
                                <tr>
                                    <td><kbd>Ctrl</kbd> + <kbd>Y</kbd></td>
                                    <td>Refazer</td>
                                </tr>
                                <tr>
                                    <td><kbd>Ctrl</kbd> + <kbd>F</kbd></td>
                                    <td>Buscar</td>
                                </tr>
                                <tr>
                                    <td><kbd>Ctrl</kbd> + <kbd>H</kbd></td>
                                    <td>Buscar e substituir</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-success mb-3">
                            <i class="bi bi-lightning-charge me-2"></i>Execução
                        </h6>
                        <table class="table table-sm table-hover">
                            <tbody>
                                <tr>
                                    <td><kbd>F5</kbd> ou <kbd>Ctrl</kbd> + <kbd>Enter</kbd></td>
                                    <td>Executar query</td>
                                </tr>
                                <tr>
                                    <td><kbd>Ctrl</kbd> + <kbd>T</kbd></td>
                                    <td>Nova aba</td>
                                </tr>
                                <tr>
                                    <td><kbd>Ctrl</kbd> + <kbd>W</kbd></td>
                                    <td>Fechar aba</td>
                                </tr>
                                <tr>
                                    <td><kbd>F11</kbd></td>
                                    <td>Modo tela cheia</td>
                                </tr>
                                <tr>
                                    <td><kbd>ESC</kbd></td>
                                    <td>Sair do modo tela cheia</td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <h6 class="text-info mb-3 mt-4">
                            <i class="bi bi-gear me-2"></i>Navegação
                        </h6>
                        <table class="table table-sm table-hover">
                            <tbody>
                                <tr>
                                    <td><kbd>Ctrl</kbd> + <kbd>Home</kbd></td>
                                    <td>Início do documento</td>
                                </tr>
                                <tr>
                                    <td><kbd>Ctrl</kbd> + <kbd>End</kbd></td>
                                    <td>Fim do documento</td>
                                </tr>
                                <tr>
                                    <td><kbd>Ctrl</kbd> + <kbd>G</kbd></td>
                                    <td>Ir para linha</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="alert alert-info mt-3 mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Dica:</strong> Use <kbd>Ctrl</kbd> + <kbd>Scroll</kbd> do mouse para ajustar o tamanho da fonte dinamicamente!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

kbd {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    box-shadow: 0 1px 0 rgba(0,0,0,0.2);
    color: #212529;
    display: inline-block;
    font-family: 'Consolas', 'Monaco', monospace;
    font-size: 0.85em;
    font-weight: 600;
    line-height: 1;
    padding: 2px 6px;
    white-space: nowrap;
}
</style>

<?php
$content = ob_get_clean();

$extraStyles = <<<'STYLES'
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/theme/monokai.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/addon/hint/show-hint.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/addon/dialog/dialog.min.css" />
<style>
:root {
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --gradient-danger: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    --gradient-info: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    --shadow-sm: 0 2px 8px rgba(0,0,0,0.06);
    --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
    --shadow-lg: 0 8px 32px rgba(0,0,0,0.12);
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --sql-sidebar-width: 300px;
    --toolbar-height: 56px;
    --tabs-height: 44px;
    --header-height: 90px;
    --results-height: 44px;
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
    font-size: 2.25rem;
    color: white;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
    flex-shrink: 0;
}

.page-title-modern {
    font-size: 2.25rem;
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

.header-controls {
    display: flex;
    gap: 1rem;
    margin-left: auto;
    align-items: center;
}

.form-select-modern {
    border: 2px solid #e2e8f0;
    border-radius: var(--radius-md);
    padding: 0.75rem 1.25rem;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
    background-color: #ffffff;
    font-weight: 500;
    min-width: 280px;
}

.form-select-modern:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
    outline: none;
}

.btn-modern-primary {
    background: var(--gradient-primary);
    color: white;
    padding: 0.75rem 1.75rem;
    border-radius: var(--radius-md);
    border: none;
    font-weight: 600;
    font-size: 0.9375rem;
    display: inline-flex;
    align-items: center;
    gap: 0.625rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-modern-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-modern-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.btn-modern-primary i {
    font-size: 1.1rem;
}

/* SQL Editor Container */
.sql-editor-container {
    display: flex;
    height: calc(100vh - var(--header-height) - 200px);
    background: white;
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.sql-editor-container.fullscreen {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    height: 100vh;
    border-radius: 0;
    z-index: 9999;
    display: flex;
    flex-direction: column;
}

.fullscreen-header {
    display: none;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 0.75rem 1.5rem;
    color: white;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    flex-shrink: 0;
    gap: 1rem;
}

.sql-editor-container.fullscreen .fullscreen-header {
    display: flex;
}

.fullscreen-controls {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
    justify-content: center;
}

.form-select-fullscreen {
    background: rgba(255,255,255,0.15);
    color: white;
    border: 1px solid rgba(255,255,255,0.3);
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.form-select-fullscreen:hover {
    background: rgba(255,255,255,0.25);
    border-color: rgba(255,255,255,0.5);
}

.form-select-fullscreen:focus {
    outline: none;
    background: rgba(255,255,255,0.2);
    border-color: rgba(255,255,255,0.6);
    box-shadow: 0 0 0 3px rgba(255,255,255,0.1);
}

.form-select-fullscreen option {
    background: #667eea;
    color: white;
}

.btn-execute-fullscreen {
    background: rgba(16,185,129,0.9);
    color: white;
    border: none;
    padding: 0.5rem 1.25rem;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    font-size: 0.9rem;
}

.btn-execute-fullscreen:hover:not(:disabled) {
    background: rgba(16,185,129,1);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16,185,129,0.4);
}

.btn-execute-fullscreen:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.sql-editor-container.fullscreen .sql-main {
    flex: 1;
    display: flex;
    overflow: hidden;
}

.fullscreen-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 600;
    font-size: 1.1rem;
}

.fullscreen-title i {
    font-size: 1.5rem;
}

.btn-exit-fullscreen {
    background: rgba(255,255,255,0.2);
    color: white;
    border: none;
    padding: 0.5rem 1.25rem;
    border-radius: var(--radius-sm);
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
}

.btn-exit-fullscreen:hover {
    background: rgba(255,255,255,0.3);
}

/* Sidebar */
.sql-sidebar {
    width: var(--sql-sidebar-width);
    background: linear-gradient(180deg, #1e293b 0%, #12151f 100%);
    border-right: 1px solid rgba(255,255,255,0.05);
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
    position: relative;
    min-width: 200px;
    max-width: 600px;
}

.sql-sidebar.collapsed {
    width: 0;
    overflow: hidden;
}

.resize-handle {
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    width: 5px;
    cursor: ew-resize;
    background: transparent;
    z-index: 10;
    transition: background 0.2s ease;
}

.resize-handle:hover,
.resize-handle.active {
    background: #667eea;
}

.resize-handle-horizontal {
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    height: 8px;
    cursor: ns-resize;
    background: rgba(102, 126, 234, 0.1);
    z-index: 100;
    transition: background 0.2s ease;
}

.resize-handle-horizontal:hover,
.resize-handle-horizontal.active {
    background: rgba(102, 126, 234, 0.6);
}

/* Resize handle para results (base - escondido por padrão) */
.resize-handle-results {
    display: none;
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 10px;
    cursor: ew-resize;
    background: rgba(102, 126, 234, 0.1);
    z-index: 100;
    transition: background 0.2s ease;
}

.resize-handle-results:hover {
    background: rgba(102, 126, 234, 0.5);
}

.resize-handle-results:active {
    background: rgba(102, 126, 234, 0.8);
}

.sidebar-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.sidebar-header h6 {
    font-weight: 700;
    color: rgba(255,255,255,0.9);
    margin: 0;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.sidebar-header h6 i {
    color: #667eea;
    font-size: 1.1rem;
}

.sidebar-search {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    background: rgba(0,0,0,0.1);
}

.sidebar-content {
    flex: 1;
    overflow-y: auto;
    padding: 0.75rem;
}

.sidebar-content::-webkit-scrollbar {
    width: 6px;
}

.sidebar-content::-webkit-scrollbar-track {
    background: rgba(0,0,0,0.2);
}

.sidebar-content::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.2);
    border-radius: 3px;
}

.sidebar-content::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.3);
}

.tree-node {
    margin-bottom: 4px;
}

.tree-item {
    padding: 0.625rem 1rem;
    cursor: pointer;
    border-radius: var(--radius-sm);
    transition: all 0.2s ease;
    user-select: none;
    display: flex;
    align-items: center;
    gap: 0.625rem;
    font-size: 0.9rem;
    font-weight: 500;
    color: rgba(255,255,255,0.7);
    margin-bottom: 2px;
}

.tree-item:hover {
    background: rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.95);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.tree-item.active {
    background: rgba(102, 126, 234, 0.2);
    color: #a5b4fc;
    font-weight: 600;
}

.tree-item i {
    width: 20px;
    text-align: center;
    font-size: 1rem;
}

.tree-chevron {
    transition: transform 0.2s ease;
    font-size: 0.75rem !important;
    width: 14px !important;
}

.tree-chevron.bi-chevron-down {
    transform: rotate(0deg);
}

.tree-schema {
    font-weight: 600;
    color: rgba(255,255,255,0.9);
    padding: 0.75rem 1rem;
    background: rgba(255,255,255,0.05);
}

.tree-schema:hover {
    background: rgba(255,255,255,0.12);
}

.tree-category {
    font-weight: 500;
    color: rgba(255,255,255,0.75);
}

.tree-object {
    font-weight: 400;
    font-size: 0.85rem;
    padding: 0.5rem 1rem;
}

.tree-loading,
.tree-error,
.tree-empty {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
    color: rgba(255,255,255,0.5);
    font-style: italic;
}

.tree-error {
    color: #ef4444;
}

.tree-children {
    margin-left: 1.5rem;
    border-left: 2px solid rgba(255,255,255,0.1);
    padding-left: 0.75rem;
    margin-top: 4px;
    margin-bottom: 8px;
}

#blocosList::-webkit-scrollbar {
    width: 4px;
}

#blocosList::-webkit-scrollbar-track {
    background: rgba(0,0,0,0.2);
}

#blocosList::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.2);
    border-radius: 2px;
}

#blocosList::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.3);
}

/* Main Editor */
.sql-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #fafafa;
    position: relative;
    min-height: 0;
}

/* Content area with sidebar and editor */
.sql-content {
    display: flex;
    flex: 1;
    overflow: hidden;
    min-height: 0;
}

.sql-content .sql-sidebar {
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.sql-content .sql-sidebar.hidden {
    display: none;
}

.sql-editor-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.sql-toolbar {
    height: var(--toolbar-height);
    background: white;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1.5rem;
    gap: 1rem;
}

.toolbar-left, .toolbar-right {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-toolbar, .btn-icon {
    width: 40px;
    height: 40px;
    border: none;
    background: transparent;
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #64748b;
    position: relative;
}

.btn-toolbar:hover, .btn-icon:hover {
    background: #f1f5f9;
    color: #667eea;
}

.btn-toolbar.active {
    background: #667eea;
    color: white;
}

.btn-toolbar.btn-executar {
    background: #10b981;
    color: white;
}

.btn-toolbar.btn-executar:hover:not(:disabled) {
    background: #059669;
}

.btn-toolbar:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-toolbar i, .btn-icon i {
    font-size: 1.1rem;
}

.toolbar-divider {
    width: 1px;
    height: 28px;
    background: #e2e8f0;
    margin: 0 0.25rem;
}

.toolbar-info {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 500;
    padding: 0.5rem 1rem;
    background: #f8fafc;
    border-radius: var(--radius-sm);
}

.toolbar-info i {
    font-size: 0.625rem;
}

.toolbar-info i.text-success {
    color: #10b981;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Tabs */
.sql-tabs {
    height: var(--tabs-height);
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: flex-end;
    padding: 0 0.75rem;
    gap: 6px;
    overflow-x: auto;
}

.sql-tabs::-webkit-scrollbar {
    height: 4px;
}

.sql-tabs::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

.tab-item {
    padding: 0.625rem 1.25rem;
    background: #e2e8f0;
    border-radius: var(--radius-sm) var(--radius-sm) 0 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1px solid #cbd5e1;
    border-bottom: none;
    min-width: 160px;
    position: relative;
}

.tab-item::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 1px;
    background: transparent;
}

.tab-item:hover {
    background: #cbd5e1;
}

.tab-item.active {
    background: white;
    border-color: #e2e8f0;
    box-shadow: 0 -2px 8px rgba(0,0,0,0.04);
}

.tab-item.active::after {
    background: white;
}

.tab-title {
    flex: 1;
    font-size: 0.9rem;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #475569;
}

.tab-item.active .tab-title {
    color: #667eea;
}

.tab-close {
    width: 22px;
    height: 22px;
    border: none;
    background: transparent;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: #64748b;
    padding: 0;
    transition: all 0.2s ease;
}

.tab-close:hover {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.tab-connection-badge {
    display: flex;
    align-items: center;
    font-size: 0.6rem;
    color: #94a3b8;
    transition: color 0.2s ease;
}

.tab-connection-badge i {
    font-size: 0.5rem;
}

.tab-item[data-connected="true"] .tab-connection-badge {
    color: #10b981;
}

.tab-item[data-connected="true"] .tab-connection-badge i {
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Tab Controls */
.tab-controls {
    display: flex;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border-bottom: 2px solid #e2e8f0;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

.tab-select-rotina,
.tab-select-conexao {
    flex: 1;
    max-width: 280px;
    font-size: 0.9rem;
    padding: 0.6rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    transition: all 0.2s ease;
    font-weight: 500;
    background: white;
}

.tab-select-rotina:hover,
.tab-select-conexao:hover {
    border-color: #cbd5e1;
}

.tab-select-rotina:focus,
.tab-select-conexao:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    outline: none;
}

.tab-disconnect-btn {
    flex-shrink: 0;
    padding: 0.6rem 1rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.tab-disconnect-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
}

.tab-controls-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Editor Area */
.sql-editor-area {
    flex: 1;
    overflow: hidden;
    position: relative;
    background: #1e1e1e;
}

.tab-content {
    display: none;
    height: 100%;
    flex-direction: column;
}

.tab-content.active {
    display: flex;
}

.sql-textarea {
    display: none;
}

.tab-content > .CodeMirror {
    flex: 1;
}

.CodeMirror {
    height: 100% !important;
    font-size: 15px;
    font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
    line-height: 1.6;
}

.CodeMirror-gutters {
    background: #272822 !important;
    border-right: 1px solid #3e3e3e !important;
}

.CodeMirror-linenumber {
    color: #75715e !important;
    padding: 0 8px !important;
}

/* Loading States no Database Explorer */
.database-loading {
    padding: 2rem 1rem;
    text-align: center;
    animation: fadeIn 0.3s ease;
}

.database-loading .spinner-border {
    width: 3rem;
    height: 3rem;
    border-width: 0.3em;
    margin-bottom: 1rem;
}

.database-loading p {
    color: rgba(255,255,255,0.9);
    margin: 0.5rem 0;
    font-size: 0.95rem;
    font-weight: 500;
}

.database-loading small {
    color: rgba(255,255,255,0.6);
    font-size: 0.8rem;
}

.progress {
    height: 6px;
    background: rgba(255,255,255,0.1);
    border-radius: 3px;
    overflow: hidden;
    margin-top: 1rem;
}

.progress-bar {
    background: linear-gradient(90deg, #667eea, #764ba2);
    transition: width 0.4s ease;
}

.progress-bar-animated {
    animation: progress-bar-stripes 1s linear infinite;
}

@keyframes progress-bar-stripes {
    0% { background-position: 0 0; }
    100% { background-position: 40px 0; }
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Results Panel */
.sql-results {
    height: var(--results-height);
    border-top: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    background: white;
    box-shadow: 0 -4px 16px rgba(0,0,0,0.04);
    position: relative;
    min-height: 150px;
    max-height: none;
    width: 100%;
    flex: 0 0 auto;
    transition: all 0.3s ease;
}

.sql-results.hidden {
    display: none;
}

.sql-results.collapsed {
    height: 44px;
    min-height: 44px;
    overflow: hidden;
    flex: 0 0 auto;
}

.sql-results.collapsed .results-content {
    display: none;
}

/* Permitir redimensionamento mesmo quando collapsed */
/* .sql-results.collapsed .resize-handle-horizontal {
    display: none;
} */

/* Layout Horizontal (3 colunas) - Database Explorer | Editor SQL | Resultados */
.sql-editor-container.layout-horizontal .sql-main {
    flex-direction: row;
    height: 100%;
}

.sql-editor-container.layout-horizontal .sql-content {
    flex-direction: row;
    flex: 1;
    height: 100%;
    min-height: 0;
}

.sql-editor-container.layout-horizontal .sql-sidebar {
    width: 280px;
    min-width: 200px;
    max-width: 400px;
    border-right: 1px solid #e2e8f0;
    border-bottom: none;
    height: 100%;
}

.sql-editor-container.layout-horizontal .sql-editor-wrapper {
    flex: 1;
    min-width: 400px;
    border-right: 1px solid #e2e8f0;
    height: 100%;
}

.sql-editor-container.layout-horizontal .sql-results {
    width: 400px;
    min-width: 300px;
    max-width: 800px;
    border-top: none;
    border-left: none;
    height: 100% !important;
    max-height: none;
    min-height: 0;
    position: relative;
    flex: 0 0 auto;
}

.sql-editor-container.layout-horizontal .sql-results.collapsed {
    width: 44px !important;
    height: 100% !important;
}

.sql-editor-container.layout-horizontal .sql-results.collapsed {
    width: 44px !important;
    height: 100% !important;
}

.sql-editor-container.layout-horizontal .sql-results.collapsed .results-content {
    display: none;
}

.sql-editor-container.layout-horizontal .sql-results.collapsed .results-header {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    height: 100%;
    width: 44px;
    padding: 1rem 0.5rem;
}

.sql-editor-container.layout-horizontal .sql-results.collapsed .results-tabs {
    flex-direction: column;
    width: 100%;
}

.sql-editor-container.layout-horizontal .sql-results.collapsed .result-tab {
    writing-mode: vertical-rl;
    transform: rotate(180deg);
    padding: 0.5rem;
    font-size: 0.75rem;
}

.sql-editor-container.layout-horizontal .sql-results.collapsed .results-actions {
    display: none;
}

.sql-editor-container.layout-horizontal .resize-handle-horizontal {
    display: none;
}

.sql-editor-container.layout-horizontal .resize-handle {
    display: block;
}

/* Resize handle para results no modo horizontal - Ativar */
.sql-editor-container.layout-horizontal .resize-handle-results {
    display: block !important;
}

.results-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(180deg, white 0%, #f8fafc 100%);
    border-bottom: 1px solid #e2e8f0;
}

.results-tabs {
    display: flex;
    gap: 6px;
}

.result-tab {
    padding: 0.625rem 1.25rem;
    background: transparent;
    border: none;
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: all 0.2s ease;
    color: #64748b;
    font-weight: 600;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.result-tab:hover {
    background: #f1f5f9;
    color: #1e293b;
}

.result-tab.active {
    background: white;
    color: #667eea;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
}

.result-tab i {
    font-size: 1rem;
}

.results-actions {
    display: flex;
    gap: 0.5rem;
}

.results-content {
    flex: 1;
    overflow: auto;
    position: relative;
    background: #fafafa;
    min-height: 0;
    height: 100%;
}

.results-content::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.results-content::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.results-content::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.results-content::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.result-panel {
    display: none;
    height: 100%;
    overflow: auto;
    padding: 1rem;
}

.result-panel.active {
    display: flex;
    flex-direction: column;
}

.table-responsive {
    padding: 0;
    background: white;
    overflow: auto;
    border: 1px solid #e2e8f0;
    border-radius: var(--radius-sm);
    height: 100%;
    flex: 1;
}

.table-modern {
    width: 100%;
    font-size: 0.875rem;
    border-collapse: collapse;
    background: white;
    margin: 0;
}

.table-modern thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: sticky;
    top: 0;
    z-index: 10;
}

.table-modern thead th {
    padding: 0.875rem 1rem;
    text-align: left;
    font-weight: 700;
    color: #1e293b;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-right: 1px solid rgba(255,255,255,0.2);
    border-bottom: 2px solid rgba(255,255,255,0.3);
}

.table-modern thead th:last-child {
    border-right: none;
}

.table-modern tbody td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e2e8f0;
    border-right: 1px solid #f1f5f9;
    color: #1e293b;
    font-family: 'Consolas', 'Monaco', monospace;
    font-size: 0.875rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 300px;
    background: white;
}

.table-modern tbody td:last-child {
    border-right: none;
}

.table-modern tbody tr {
    transition: all 0.15s ease;
}

.table-modern tbody tr:nth-child(even) td {
    background: #f8fafc;
}

.table-modern tbody tr:hover td {
    background: #eff6ff !important;
    color: #1e40af;
}

.table-modern tbody tr:last-child td {
    border-bottom: none;
}

.messages-content, .info-content {
    padding: 1.5rem;
    font-family: 'Consolas', 'Monaco', monospace;
    font-size: 0.9rem;
    background: white;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
    height: 100%;
    overflow: auto;
}

.messages-content p, .info-content p {
    margin-bottom: 0.75rem;
    line-height: 1.6;
}

.messages-content pre {
    background: #1e1e1e;
    color: #d4d4d4;
    padding: 1.25rem;
    border-radius: var(--radius-sm);
    overflow-x: auto;
    margin-top: 1rem;
    border: 1px solid #2d2d2d;
}

.form-control-modern {
    width: 100%;
    border: 2px solid #e2e8f0;
    border-radius: var(--radius-md);
    padding: 0.625rem 1rem;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    background: white;
}

.form-control-modern:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
    outline: none;
}

.text-muted {
    color: #94a3b8 !important;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: rgba(255,255,255,0.5);
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    display: block;
    opacity: 0.5;
}

.empty-state p {
    font-size: 1.05rem;
    margin: 0;
}

/* Responsive */
@media (max-width: 991px) {
    .page-header-modern {
        padding: 1.25rem 1.5rem;
    }
    
    .page-header-modern .header-controls {
        width: 100%;
        margin-left: 0 !important;
    }
    
    .form-select-modern {
        min-width: auto;
        flex: 1;
    }
    
    .sql-editor-container {
        height: calc(100vh - 320px);
    }
    
    .sql-sidebar {
        position: absolute;
        z-index: 100;
        height: 100%;
        box-shadow: var(--shadow-lg);
    }
    
    .sql-results {
        height: 280px;
    }
}

@media (max-width: 767px) {
    .page-icon-modern {
        width: 56px;
        height: 56px;
        font-size: 1.75rem;
    }
    
    .page-title-modern {
        font-size: 1.75rem;
    }
    
    .page-subtitle-modern {
        font-size: 0.9rem;
    }
    
    .sql-results {
        height: 240px;
    }
    
    .btn-modern-primary {
        padding: 0.625rem 1.25rem;
        font-size: 0.875rem;
    }
    
    .toolbar-info {
        font-size: 0.8rem;
        padding: 0.375rem 0.75rem;
    }
    
    .tab-item {
        min-width: 120px;
        padding: 0.5rem 1rem;
    }
}
</style>
STYLES;

$extraScripts = <<<'SCRIPTS'
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/sql/sql.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/addon/comment/comment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/addon/hint/show-hint.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/addon/hint/sql-hint.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/addon/dialog/dialog.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/addon/search/searchcursor.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/addon/search/search.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Garantir que baseUrl está definido (fallback se não vier do layout)
if (typeof baseUrl === 'undefined') {
    console.warn('⚠️ baseUrl não definido pelo layout! Usando fallback...');
    const baseUrl = '/DMC-DATALOAD/public';
}
console.log('✅ baseUrl definido:', baseUrl);

// Função de debug para testar conexão manual
let tabCounter = 1;
let activeTab = 'tab1';
let tabs = {}; // Estrutura: { tabId: { editor, connection, rotina, metadata, fileHandle, fileName, filePath } }
let sidebarCollapsed = false;
let resultsCollapsed = false;
let isFullscreen = false;

// Novas variáveis para autocomplete e auto-save
let autocompleteEnabled = false;
let autoSaveEnabled = false;
let autoSaveInterval = null;
let unsavedChanges = {}; // Track unsaved changes per tab

// Resize handling variables
let isResizingSidebar = false;
let isResizingResults = false;
let isResizingResultsHorizontal = false;
let startX = 0;
let startY = 0;
let startWidth = 0;
let startHeight = 0;
let layoutOrientation = 'vertical';

// Initialize resize handles
function initResizeHandles() {
    const sidebarHandle = document.getElementById('sidebarResizeHandle');
    const sidebar = document.getElementById('sqlSidebar');
    const resultsHandle = document.getElementById('resultsResizeHandle');
    const resultsHandleHorizontal = document.getElementById('resultsResizeHandleHorizontal');
    const results = document.getElementById('sqlResults');
    
    if (!sidebarHandle || !sidebar || !resultsHandle || !results) {
        console.error('Resize handles not found');
        return;
    }
    
    // Sidebar resize (horizontal drag)
    sidebarHandle.addEventListener('mousedown', function(e) {
        isResizingSidebar = true;
        startX = e.clientX;
        startWidth = sidebar.offsetWidth;
        sidebarHandle.classList.add('active');
        document.body.style.cursor = 'ew-resize';
        document.body.style.userSelect = 'none';
        e.preventDefault();
    });
    
    // Results panel resize (vertical drag - modo vertical)
    resultsHandle.addEventListener('mousedown', function(e) {
        console.log('🔵 Mousedown no handle VERTICAL');
        
        // Expandir automaticamente se estiver collapsed
        if (results.classList.contains('collapsed')) {
            results.classList.remove('collapsed');
            resultsCollapsed = false;
            const btnIcon = document.querySelector('#btnToggleResults i');
            if (btnIcon) {
                btnIcon.classList.remove('bi-chevron-up');
                btnIcon.classList.add('bi-chevron-down');
                document.querySelector('#btnToggleResults').setAttribute('title', 'Minimizar Resultados');
            }
        }
        
        isResizingResults = true;
        startY = e.clientY;
        startHeight = results.offsetHeight;
        console.log('📏 Height inicial:', startHeight);
        resultsHandle.classList.add('active');
        document.body.style.cursor = 'ns-resize';
        document.body.style.userSelect = 'none';
        e.preventDefault();
    });
    
    // Results panel resize (horizontal drag - modo horizontal 3 colunas)
    if (resultsHandleHorizontal) {
        console.log('✅ Handle horizontal encontrado:', resultsHandleHorizontal);
        resultsHandleHorizontal.addEventListener('mousedown', function(e) {
            console.log('🖱️ Mousedown no handle horizontal');
            
            // Expandir automaticamente se estiver collapsed
            if (results.classList.contains('collapsed')) {
                results.classList.remove('collapsed');
                resultsCollapsed = false;
                const btnIcon = document.querySelector('#btnToggleResults i');
                if (btnIcon) {
                    btnIcon.classList.remove('bi-chevron-up');
                    btnIcon.classList.add('bi-chevron-down');
                    document.querySelector('#btnToggleResults').setAttribute('title', 'Minimizar Resultados');
                }
            }
            
            isResizingResultsHorizontal = true;
            startX = e.clientX;
            startWidth = results.offsetWidth;
            console.log('📏 Width inicial:', startWidth);
            resultsHandleHorizontal.classList.add('active');
            document.body.style.cursor = 'ew-resize';
            document.body.style.userSelect = 'none';
            e.preventDefault();
        });
    } else {
        console.error('❌ Handle horizontal NÃO encontrado!');
    }
    
    // Mouse move handler
    document.addEventListener('mousemove', function(e) {
        if (isResizingSidebar) {
            const diff = e.clientX - startX;
            const newWidth = startWidth + diff;
            
            // Apply min/max constraints
            if (newWidth >= 200 && newWidth <= 600) {
                sidebar.style.width = newWidth + 'px';
            }
        }
        
        if (isResizingResults) {
            const diff = startY - e.clientY; // Inverted for bottom-up resize
            const newHeight = startHeight + diff;
            const maxHeight = window.innerHeight * 0.8;
            
            // Apply min/max constraints
            if (newHeight >= 150 && newHeight <= maxHeight) {
                results.style.height = newHeight + 'px';
                console.log('⬆️ Resizing vertical:', newHeight + 'px');
            }
        }
        
        if (isResizingResultsHorizontal) {
            const diff = e.clientX - startX;
            const newWidth = startWidth - diff;
            
            if (newWidth >= 300 && newWidth <= 800) {
                results.style.width = newWidth + 'px';
                console.log('📐 Resizing:', newWidth + 'px');
            }
        }
    });
    
    // Mouse up handler
    document.addEventListener('mouseup', function() {
        const resultsHandleHorizontal = document.getElementById('resultsResizeHandleHorizontal');
        
        if (isResizingSidebar) {
            isResizingSidebar = false;
            sidebarHandle.classList.remove('active');
            document.body.style.cursor = '';
            document.body.style.userSelect = '';
            
            // Refresh all CodeMirror editors
            setTimeout(() => {
                Object.keys(editors).forEach(tabId => {
                    if (editors[tabId]) {
                        editors[tabId].refresh();
                    }
                });
            }, 50);
            
            // Save preference
            saveLayoutPreferences();
        }
        
        if (isResizingResults) {
            isResizingResults = false;
            resultsHandle.classList.remove('active');
            document.body.style.cursor = '';
            document.body.style.userSelect = '';
            
            // Refresh all CodeMirror editors
            setTimeout(() => {
                Object.keys(editors).forEach(tabId => {
                    if (editors[tabId]) {
                        editors[tabId].refresh();
                    }
                });
            }, 50);
            
            // Save preference
            saveLayoutPreferences();
        }
        
        if (isResizingResultsHorizontal) {
            isResizingResultsHorizontal = false;
            if (resultsHandleHorizontal) {
                resultsHandleHorizontal.classList.remove('active');
            }
            document.body.style.cursor = '';
            document.body.style.userSelect = '';
            
            // Refresh all CodeMirror editors
            setTimeout(() => {
                Object.keys(tabs).forEach(tabId => {
                    if (tabs[tabId] && tabs[tabId].editor) {
                        tabs[tabId].editor.refresh();
                    }
                });
            }, 50);
            
            // Save preference
            saveLayoutPreferences();
        }
    });
}

// Toggle layout orientation
function toggleLayoutOrientation() {
    const container = document.querySelector('.sql-editor-container');
    const btnLayout = document.getElementById('btnLayout');
    const results = document.getElementById('sqlResults');
    const sidebar = document.getElementById('sqlSidebar');
    
    if (layoutOrientation === 'vertical') {
        // Change to horizontal split
        container.classList.add('layout-horizontal');
        layoutOrientation = 'horizontal';
        
        // Remover collapsed e ajustar para layout horizontal
        if (results) {
            results.classList.remove('collapsed');
            resultsCollapsed = false;
            results.style.height = '';
            results.style.width = '400px';
            
            // Atualizar botão toggle
            const btnIcon = document.querySelector('#btnToggleResults i');
            if (btnIcon) {
                btnIcon.classList.remove('bi-chevron-up');
                btnIcon.classList.add('bi-chevron-down');
                document.querySelector('#btnToggleResults').setAttribute('title', 'Minimizar Resultados');
            }
        }
        
        if (btnLayout) {
            btnLayout.setAttribute('title', 'Layout Vertical');
        }
        
        Swal.fire({
            icon: 'info',
            title: 'Layout Horizontal',
            text: '3 colunas: Database Explorer | Editor SQL | Resultados',
            timer: 2000,
            showConfirmButton: false
        });
    } else {
        // Change to vertical split
        container.classList.remove('layout-horizontal');
        layoutOrientation = 'vertical';
        
        // Minimizar painel no modo vertical
        if (results) {
            results.style.width = '';
            results.style.height = '44px';
            results.classList.add('collapsed');
            resultsCollapsed = true;
            
            // Atualizar botão toggle
            const btnIcon = document.querySelector('#btnToggleResults i');
            if (btnIcon) {
                btnIcon.classList.remove('bi-chevron-down');
                btnIcon.classList.add('bi-chevron-up');
                document.querySelector('#btnToggleResults').setAttribute('title', 'Expandir Resultados');
            }
        }
        
        if (btnLayout) {
            btnLayout.setAttribute('title', 'Layout Horizontal');
        }
        
        Swal.fire({
            icon: 'info',
            title: 'Layout Vertical',
            text: 'Editor acima dos resultados',
            timer: 1500,
            showConfirmButton: false
        });
    }
    
    // Refresh all CodeMirror editors after layout change
    setTimeout(() => {
        Object.keys(tabs).forEach(tabId => {
            if (tabs[tabId] && tabs[tabId].editor) {
                tabs[tabId].editor.refresh();
            }
        });
    }, 100);
    
    // Save preference
    saveLayoutPreferences();
}

// Save layout preferences to localStorage
function saveLayoutPreferences() {
    const sidebar = document.getElementById('sqlSidebar');
    const results = document.getElementById('sqlResults');
    
    if (sidebar && results) {
        const prefs = {
            sidebarWidth: sidebar.offsetWidth,
            resultsHeight: results.offsetHeight,
            layoutOrientation: layoutOrientation
        };
        localStorage.setItem('sqlEditorLayout', JSON.stringify(prefs));
    }
}

// Load layout preferences from localStorage
function loadLayoutPreferences() {
    const prefsStr = localStorage.getItem('sqlEditorLayout');
    let isHorizontal = false;
    
    if (prefsStr) {
        try {
            const prefs = JSON.parse(prefsStr);
            const sidebar = document.getElementById('sqlSidebar');
            const results = document.getElementById('sqlResults');
            
            if (sidebar && prefs.sidebarWidth) {
                sidebar.style.width = prefs.sidebarWidth + 'px';
            }
            
            if (prefs.layoutOrientation === 'horizontal') {
                const container = document.querySelector('.sql-editor-container');
                if (container) {
                    container.classList.add('layout-horizontal');
                    layoutOrientation = 'horizontal';
                    isHorizontal = true;
                }
            }
        } catch (e) {
            console.error('Error loading layout preferences:', e);
        }
    }
    
    // Configurar estado inicial do painel de resultados
    const results = document.getElementById('sqlResults');
    const btnIcon = document.querySelector('#btnToggleResults i');
    
    if (results) {
        if (isHorizontal) {
            // Modo horizontal: expandido, largura 400px, altura 100%
            results.classList.remove('collapsed');
            results.style.width = '400px';
            results.style.height = '';
            resultsCollapsed = false;
            
            if (btnIcon) {
                btnIcon.classList.remove('bi-chevron-up');
                btnIcon.classList.add('bi-chevron-down');
                document.querySelector('#btnToggleResults').setAttribute('title', 'Minimizar Resultados');
            }
        } else {
            // Modo vertical: minimizado, altura 44px
            results.classList.add('collapsed');
            results.style.width = '';
            results.style.height = '44px';
            resultsCollapsed = true;
            
            if (btnIcon) {
                btnIcon.classList.remove('bi-chevron-down');
                btnIcon.classList.add('bi-chevron-up');
                document.querySelector('#btnToggleResults').setAttribute('title', 'Expandir Resultados');
            }
        }
    }
}

// Inicializar editor para tab1
$(document).ready(function() {
    console.log('═══════════════════════════════════════════');
    console.log('🚀 SQL EDITOR INICIALIZANDO');
    console.log('═══════════════════════════════════════════');
    console.log('📍 Base URL:', baseUrl);
    console.log('🔧 jQuery versão:', $.fn.jquery);
    console.log('🌐 Window location:', window.location.href);
    console.log('📋 Verificando elementos do DOM...');
    
    // Esperar um pouco para garantir que o DOM está completamente carregado
    setTimeout(function() {
        console.log('  - #selectConexao:', $('#selectConexao').length ? '✅ Encontrado' : '❌ NÃO encontrado');
        console.log('  - #selectRotina:', $('#selectRotina').length ? '✅ Encontrado' : '❌ NÃO encontrado');
        console.log('  - #selectConexaoFullscreen:', $('#selectConexaoFullscreen').length ? '✅ Encontrado' : '❌ NÃO encontrado');
        console.log('  - #selectRotinaFullscreen:', $('#selectRotinaFullscreen').length ? '✅ Encontrado' : '❌ NÃO encontrado');
        console.log('  - .tab-select-conexao[data-tab="tab1"]:', $('.tab-select-conexao[data-tab="tab1"]').length ? '✅ Encontrado' : '❌ NÃO encontrado');
        console.log('  - .tab-select-rotina[data-tab="tab1"]:', $('.tab-select-rotina[data-tab="tab1"]').length ? '✅ Encontrado' : '❌ NÃO encontrado');
        console.log('═══════════════════════════════════════════');
        
        // Inicializar primeira aba
        tabs['tab1'] = {
            editor: null,
            connection: null,
            rotina: null,
            metadata: null,
            fileName: null,
            filePath: null,
            fileHandle: null
        };
        
        // Inicializar unsaved changes para tab1
        unsavedChanges['tab1'] = false;
        
        console.log('📝 Inicializando editor da tab1...');
        initEditor('tab1');
        
        console.log('🔌 Carregando listas de conexões e rotinas para a tab1...');
        
        // Carregar listas apenas para as abas (selects principais foram removidos)
        setTimeout(function() {
            carregarConexoesParaAba('tab1');
            carregarRotinasParaAba('tab1');
        }, 500);
    }, 100);
    
    // Garantir altura inicial do painel de resultados
    const results = document.getElementById('sqlResults');
    if (results && !results.style.height) {
        results.style.height = '320px';
    }
    
    // Initialize resize functionality
    initResizeHandles();
    
    // Load saved layout preferences
    loadLayoutPreferences();
    
    // Scroll infinito - detectar quando chegar ao final da tabela
    $('.table-responsive').on('scroll', function() {
        const container = $(this);
        const scrollTop = container.scrollTop();
        const scrollHeight = container[0].scrollHeight;
        const containerHeight = container.height();
        
        // Verificar se chegou perto do final (90% da rolagem)
        const scrollPercentage = (scrollTop + containerHeight) / scrollHeight;
        
        if (scrollPercentage > 0.9 && tabs[activeTab]) {
            // Verificar se pode carregar mais
            if (tabs[activeTab].hasMoreResults && 
                !tabs[activeTab].isLoadingMore && 
                tabs[activeTab].lastQuery &&
                $('#resultTable').is(':visible')) {
                
                console.log('🔄 Final da tabela detectado - carregando mais resultados...');
                executarQuery(true);
            }
        }
    });
    
    // Listener para mudanças de fullscreen
    document.addEventListener('fullscreenchange', handleFullscreenChange);
    document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
    document.addEventListener('mozfullscreenchange', handleFullscreenChange);
    document.addEventListener('msfullscreenchange', handleFullscreenChange);
    
    // Atalhos de teclado
    $(document).on('keydown', function(e) {
        // F5 - Executar
        if (e.key === 'F5') {
            e.preventDefault();
            executarQuery();
        }
        // F11 - Fullscreen
        if (e.key === 'F11') {
            e.preventDefault();
            toggleFullscreen();
        }
        // ESC - Sair do fullscreen
        if (e.key === 'Escape' && isFullscreen) {
            e.preventDefault();
            toggleFullscreen();
        }
        // Ctrl+T - Nova aba
        if (e.ctrlKey && e.key === 't') {
            e.preventDefault();
            novaAba();
        }
        // Ctrl+W - Fechar aba
        if (e.ctrlKey && e.key === 'w') {
            e.preventDefault();
            fecharAbaAtual();
        }
    });
    
    // Click em tabs de resultados
    $('.result-tab').on('click', function() {
        $('.result-tab').removeClass('active');
        $(this).addClass('active');
        
        const target = $(this).data('result');
        $('.result-panel').removeClass('active');
        $(`.result-panel[data-result="${target}"]`).addClass('active');
    });
    
    // Search objects
    $('#searchObjects').on('input', function() {
        const search = $(this).val().toLowerCase();
        $('.tree-item').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(search));
        });
    });
});

function initEditor(tabId) {
    const textarea = document.getElementById(`sqlEditor_${tabId}`);
    if (!textarea) return;
    
    const editor = CodeMirror.fromTextArea(textarea, {
        mode: 'text/x-sql',
        theme: 'monokai',
        lineNumbers: true,
        lineWrapping: true,
        indentUnit: 4,
        tabSize: 4,
        autofocus: true,
        matchBrackets: true,
        autoCloseBrackets: true,
        hintOptions: {
            completeSingle: false,
            tables: {},
            keywords: []
        },
        extraKeys: {
            'F5': function(cm) { executarQuery(); },
            'Ctrl-Enter': function(cm) { executarQuery(); },
            'Ctrl-/': 'toggleComment',
            'Ctrl-]': 'indentMore',
            'Ctrl-[': 'indentLess',
            'Ctrl-Space': 'autocomplete',
            'Ctrl-S': function(cm) { 
                salvarScriptManual(); 
                return false; // Prevent default browser save
            },
            'Ctrl-Shift-S': function(cm) {
                salvarComo();
                return false;
            },
            'Ctrl-O': function(cm) {
                abrirArquivo();
                return false;
            },
            'Ctrl-D': function(cm) { cm.execCommand('deleteLine'); },
            'Ctrl-Z': function(cm) { cm.undo(); },
            'Ctrl-Y': function(cm) { cm.redo(); },
            'Ctrl-F': 'find',
            'Ctrl-H': 'replace',
            'Ctrl-G': 'jumpToLine'
        }
    });
    
    // Rastrear mudanças
    editor.on('change', function() {
        unsavedChanges[tabId] = true;
        
        // Adicionar asterisco no título da aba se houver mudanças não salvas
        const tabTitle = $(`.tab-item[data-tab="${tabId}"] .tab-title`);
        const currentTitle = tabTitle.text().replace(' *', '');
        tabTitle.text(currentTitle + ' *');
    });
    
    // Zoom com Ctrl + Scroll
    const editorElement = editor.getWrapperElement();
    let currentFontSize = 15;
    
    editorElement.addEventListener('wheel', function(e) {
        if (e.ctrlKey) {
            e.preventDefault();
            
            if (e.deltaY < 0) {
                // Scroll up - aumentar fonte
                currentFontSize = Math.min(currentFontSize + 1, 30);
            } else {
                // Scroll down - diminuir fonte
                currentFontSize = Math.max(currentFontSize - 1, 10);
            }
            
            editorElement.style.fontSize = currentFontSize + 'px';
            editor.refresh();
        }
    }, { passive: false });
    
    // Inicializar estrutura da aba
    if (!tabs[tabId]) {
        tabs[tabId] = {
            editor: editor,
            connection: null,
            rotina: null,
            metadata: null,
            lastQuery: null,
            queryOffset: 0,
            hasMoreResults: true,
            isLoadingMore: false,
            fileName: null,      // Nome do arquivo salvo/aberto
            filePath: null,      // Caminho do arquivo
            fileHandle: null     // Referência ao arquivo
        };
    } else {
        tabs[tabId].editor = editor;
    }
}

// Função para mostrar modal de atalhos
function mostrarAtalhos() {
    const modal = new bootstrap.Modal(document.getElementById('modalAtalhos'));
    modal.show();
}

// Carregar conexões para uma aba específica
function carregarConexoesParaAba(tabId) {
    console.log(`🔌 Carregando conexões para aba ${tabId}...`);
    
    $.getJSON(baseUrl + '/conexoes/list')
        .done(function(res) {
            console.log(`✅ Resposta recebida:`, res);
            const sel = $(`.tab-select-conexao[data-tab="${tabId}"]`);
            sel.find('option:not(:first)').remove();
            
            if (res.data && res.data.length > 0) {
                console.log(`📊 ${res.data.length} conexões encontradas`);
                res.data.forEach(function(c) {
                    const icon = getTipoBancoIcon(c.tipo_banco);
                    const optText = `${icon} ${c.nome_conexao} (${c.tipo_banco})`;
                    sel.append($('<option>').val(c.id).text(optText));
                });
                console.log(`✅ Conexões carregadas na aba ${tabId}`);
            } else {
                console.warn(`⚠️ Nenhuma conexão encontrada`);
                sel.append($('<option>').val('').text('⚠️ Nenhuma conexão cadastrada'));
            }
        })
        .fail(function(xhr, status, error) {
            console.error(`❌ Erro ao carregar conexões:`, error);
            console.error(`Status:`, status);
            console.error(`Response:`, xhr.responseText);
            const sel = $(`.tab-select-conexao[data-tab="${tabId}"]`);
            sel.append($('<option>').val('').text('❌ Erro ao carregar'));
        });
}

// Função auxiliar para ícones de tipo de banco
function getTipoBancoIcon(tipo) {
    const icons = {
        'postgresql': '🐘',
        'postgres': '🐘',
        'mysql': '🐬',
        'mariadb': '🐬',
        'sqlserver': '🗄️',
        'mssql': '🗄️',
        'oracle': '⚡',
        'sqlite': '📦'
    };
    return icons[tipo.toLowerCase()] || '🔌';
}

// Carregar rotinas para uma aba específica
function carregarRotinasParaAba(tabId) {
    console.log(`📋 Carregando rotinas para aba ${tabId}...`);
    
    $.getJSON(baseUrl + '/rotinas/list')
        .done(function(res) {
            console.log(`✅ Rotinas recebidas:`, res);
            const sel = $(`.tab-select-rotina[data-tab="${tabId}"]`);
            sel.find('option:not(:first)').remove();
            
            if (res.data && res.data.length > 0) {
                console.log(`📊 ${res.data.length} rotinas encontradas`);
                res.data.forEach(function(r) {
                    sel.append($('<option>').val(r.id).text(`📋 ${r.nome}`));
                });
                console.log(`✅ Rotinas carregadas na aba ${tabId}`);
            } else {
                console.warn(`⚠️ Nenhuma rotina encontrada`);
                sel.append($('<option>').val('').text('⚠️ Nenhuma rotina cadastrada'));
            }
        })
        .fail(function(xhr, status, error) {
            console.error(`❌ Erro ao carregar rotinas:`, error);
            console.error(`Response:`, xhr.responseText);
            const sel = $(`.tab-select-rotina[data-tab="${tabId}"]`);
            sel.append($('<option>').val('').text('❌ Erro ao carregar'));
        });
}

// Event handler para mudança de conexão em uma aba
$(document).on('change', '.tab-select-conexao', function() {
    const tabId = $(this).data('tab');
    const conexaoId = $(this).val();
    
    if (conexaoId) {
        conectarBancoNaAba(tabId, conexaoId);
    } else {
        desconectarBancoDaAba(tabId);
    }
});

// Event handler para mudança de rotina em uma aba
$(document).on('change', '.tab-select-rotina', function() {
    const tabId = $(this).data('tab');
    const rotinaId = $(this).val();
    
    if (rotinaId) {
        carregarRotinaParaAba(tabId, rotinaId);
    } else {
        if (tabs[tabId]) {
            tabs[tabId].rotina = null;
        }
        $('#blocosContainer').hide();
    }
});

// Event handler para botão desconectar
$(document).on('click', '.tab-disconnect-btn', function() {
    const tabId = $(this).data('tab');
    desconectarBancoDaAba(tabId);
});

// Atualizar sidebar para mostrar objetos da aba ativa
function atualizarSidebarParaAba(tabId) {
    if (!tabs[tabId]) {
        console.warn(`⚠️ Aba ${tabId} não existe`);
        return;
    }
    
    console.log(`🔄 Atualizando sidebar para aba ${tabId}...`);
    console.log(`   - Conexão:`, tabs[tabId].connection);
    console.log(`   - Metadata:`, tabs[tabId].metadata);
    console.log(`   - Rotina:`, tabs[tabId].rotina);
    
    if (tabs[tabId].connection) {
        // Renderizar árvore com os objetos desta conexão
        if (tabs[tabId].metadata && tabs[tabId].metadata.objetos) {
            console.log(`✅ Renderizando árvore de objetos...`);
            const objetos = tabs[tabId].metadata.objetos;
            
            // Verificar estrutura dos objetos
            if (objetos.counts) {
                renderizarArvore(objetos.counts, tabs[tabId].connection.id);
            } else {
                renderizarArvore(objetos, tabs[tabId].connection.id);
            }
        } else {
            console.log(`⏳ Objetos não carregados ainda, carregando...`);
            mostrarLoadingDatabaseExplorer('Carregando estrutura do banco...', 30);
            carregarObjetosBancoDaAba(tabId, tabs[tabId].connection.id);
        }
        
        // Mostrar blocos se houver rotina
        if (tabs[tabId].rotina && tabs[tabId].rotina.blocos) {
            console.log(`📋 Renderizando blocos da rotina...`);
            renderizarBlocos(tabs[tabId].rotina.blocos, tabs[tabId].rotina.nome);
        } else {
            $('#blocosContainer').hide();
        }
    } else {
        // Sem conexão - mostrar empty state
        console.log(`ℹ️ Sem conexão na aba ${tabId}`);
        $('#databaseTree').html(`
            <div class="empty-state">
                <i class="bi bi-database"></i>
                <p>Conecte-se a um banco de dados para explorar</p>
            </div>
        `);
        $('#blocosContainer').hide();
    }
}

function carregarConexoes() {
    console.log('🔌 Carregando conexões nos selects principais...');
    const url = baseUrl + '/conexoes/list';
    console.log('📍 URL completa:', url);
    console.log('📍 baseUrl:', baseUrl);
    
    // Verificar se os elementos existem ANTES de fazer a requisição
    const sel = $('#selectConexao');
    const selFullscreen = $('#selectConexaoFullscreen');
    
    // Se nenhum elemento existe, não precisa carregar
    if (sel.length === 0 && selFullscreen.length === 0) {
        console.log('ℹ️ Selects principais não existem no DOM (removidos), pulando carregamento...');
        return;
    }
    
    console.log('✅ Elementos encontrados, fazendo requisição AJAX...');
    
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        timeout: 10000,
        beforeSend: function() {
            console.log('📤 Enviando requisição para:', url);
        },
        success: function(res) {
            console.log('✅ Resposta recebida nos selects principais:', res);
            console.log('📊 Tipo de res:', typeof res);
            console.log('📊 res.data existe?', 'data' in res);
            console.log('📊 Estrutura completa:', JSON.stringify(res, null, 2));
            
            if (sel.length > 0) sel.find('option:not(:first)').remove();
            if (selFullscreen.length > 0) selFullscreen.find('option:not(:first)').remove();
            
            if (res && res.data && Array.isArray(res.data) && res.data.length > 0) {
                console.log(`📋 ${res.data.length} conexões encontradas`);
                res.data.forEach(function(c, index) {
                    console.log(`➕ [${index + 1}] Adicionando conexão:`, c.nome_conexao, '(ID:', c.id, 'Tipo:', c.tipo_banco, ')');
                    const optText = `${c.nome_conexao} (${c.tipo_banco})`;
                    if (sel.length > 0) {
                        const opt1 = $('<option>').val(c.id).text(optText);
                        sel.append(opt1);
                    }
                    if (selFullscreen.length > 0) {
                        const opt2 = $('<option>').val(c.id).text(optText);
                        selFullscreen.append(opt2);
                    }
                });
                
                // Verificar se realmente foi adicionado
                const totalOpcoes = sel.length > 0 ? sel.find('option').length : 0;
                console.log('✅ Total de opções após adicionar:', totalOpcoes);
                
                if (totalOpcoes > 1) {
                    console.log('🎉 SUCESSO! Conexões carregadas e adicionadas aos selects!');
                } else {
                    console.error('❌ ERRO: Opções não foram adicionadas ao select!');
                }
            } else {
                console.warn('⚠️ Nenhuma conexão encontrada na resposta');
                console.warn('📦 res:', res);
                console.warn('📦 res.data:', res ? res.data : 'undefined');
                sel.append($('<option>').val('').text('⚠️ Nenhuma conexão cadastrada'));
                selFullscreen.append($('<option>').val('').text('⚠️ Nenhuma conexão cadastrada'));
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ ERRO na requisição AJAX:', error);
            console.error('📊 Status:', status);
            console.error('📊 xhr.status:', xhr.status);
            console.error('📊 xhr.statusText:', xhr.statusText);
            console.error('📊 xhr.responseText:', xhr.responseText);
            console.error('📊 xhr completo:', xhr);
            
            sel.append($('<option>').val('').text('❌ Erro ao carregar'));
            selFullscreen.append($('<option>').val('').text('❌ Erro ao carregar'));
            
            Swal.fire({
                icon: 'error',
                title: 'Erro ao Carregar Conexões',
                html: `<p><strong>Status:</strong> ${status}</p><p><strong>Erro:</strong> ${error}</p><pre>${xhr.responseText}</pre>`,
                width: 600
            });
        }
    });
}

function carregarRotinas() {
    console.log('📋 Carregando rotinas nos selects principais...');
    const url = baseUrl + '/rotinas/list';
    console.log('📍 URL completa:', url);
    
    const sel = $('#selectRotina');
    const selFullscreen = $('#selectRotinaFullscreen');
    
    // Se nenhum elemento existe, não precisa carregar
    if (sel.length === 0 && selFullscreen.length === 0) {
        console.log('ℹ️ Selects principais não existem no DOM (removidos), pulando carregamento...');
        return;
    }
    
    console.log('✅ Elementos encontrados, fazendo requisição AJAX...');
    
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        timeout: 10000,
        beforeSend: function() {
            console.log('📤 Enviando requisição para:', url);
        },
        success: function(res) {
            console.log('✅ Resposta recebida (rotinas):', res);
            console.log('📊 Estrutura completa:', JSON.stringify(res, null, 2));
            
            if (sel.length > 0) sel.find('option:not(:first)').remove();
            if (selFullscreen.length > 0) selFullscreen.find('option:not(:first)').remove();
            
            if (res && res.data && Array.isArray(res.data) && res.data.length > 0) {
                console.log(`📋 ${res.data.length} rotinas encontradas`);
                res.data.forEach(function(r, index) {
                    console.log(`➕ [${index + 1}] Adicionando rotina:`, r.nome, '(ID:', r.id, ')');
                    if (sel.length > 0) {
                        const opt1 = $('<option>').val(r.id).text(r.nome);
                        sel.append(opt1);
                    }
                    if (selFullscreen.length > 0) {
                        const opt2 = $('<option>').val(r.id).text(r.nome);
                        selFullscreen.append(opt2);
                    }
                });
                
                const totalOpcoes = sel.length > 0 ? sel.find('option').length : 0;
                console.log('✅ Total de opções após adicionar:', totalOpcoes);
                
                if (totalOpcoes > 1) {
                    console.log('🎉 SUCESSO! Rotinas carregadas e adicionadas aos selects!');
                } else {
                    console.error('❌ ERRO: Opções não foram adicionadas ao select!');
                }
            } else {
                console.warn('⚠️ Nenhuma rotina encontrada na resposta');
                sel.append($('<option>').val('').text('⚠️ Nenhuma rotina cadastrada'));
                selFullscreen.append($('<option>').val('').text('⚠️ Nenhuma rotina cadastrada'));
            }
        },
        error: function(xhr, status, error) {
            console.error('❌ ERRO na requisição AJAX:', error);
            console.error('📊 Status:', status);
            console.error('📊 xhr.responseText:', xhr.responseText);
            
            sel.append($('<option>').val('').text('❌ Erro ao carregar'));
            selFullscreen.append($('<option>').val('').text('❌ Erro ao carregar'));
            
            Swal.fire({
                icon: 'error',
                title: 'Erro ao Carregar Rotinas',
                html: `<p><strong>Status:</strong> ${status}</p><p><strong>Erro:</strong> ${error}</p>`,
                width: 600
            });
        }
    });
}

$('#selectRotina, #selectRotinaFullscreen').on('change', function() {
    const rotinaId = $(this).val();
    console.log('Rotina selecionada:', rotinaId);
    
    // Sincronizar ambos os selects
    $('#selectRotina').val(rotinaId);
    $('#selectRotinaFullscreen').val(rotinaId);
    
    if (rotinaId) {
        carregarBlocosRotina(rotinaId);
    } else {
        $('#blocosContainer').hide();
        $('#databaseTree').show();
        currentRotina = null;
    }
});

function carregarBlocosRotina(rotinaId) {
    console.log('Carregando blocos da rotina:', rotinaId);
    
    $('#databaseTree').hide();
    $('#blocosContainer').show();
    $('#blocosList').html('<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div><p class="text-muted mt-2" style="font-size: 0.85rem;">Carregando blocos...</p></div>');
    
    $.getJSON(baseUrl + '/rotinas/get/' + rotinaId)
        .done(function(res) {
            console.log('Resposta da API:', res);
            console.log('Estrutura da resposta:', JSON.stringify(res, null, 2));
            
            // Suportar diferentes estruturas de resposta
            let rotina = null;
            let blocos = [];
            
            if (res.rotina) {
                rotina = res.rotina;
                blocos = res.blocos || [];
            } else if (res.sucesso && res.data) {
                rotina = res.data;
                blocos = res.data.blocos || [];
            } else if (res.id) {
                rotina = res;
                blocos = res.blocos || [];
            }
            
            if (rotina) {
                currentRotina = {
                    ...rotina,
                    blocos: blocos
                };
                $('#rotinaName').text(rotina.nome || 'Rotina');
                
                console.log('Total de blocos:', blocos.length);
                console.log('Blocos:', blocos);
                
                if (blocos.length > 0) {
                    let html = '';
                    blocos.forEach(function(bloco, index) {
                        const operacao = bloco.tipo_bloco || bloco.operacao || 'outros';
                        const icon = getIconForOperacao(operacao.toLowerCase());
                        const title = bloco.codigo_bloco || bloco.descricao || bloco.nome || `Bloco ${index + 1}`;
                        html += `
                            <div class="tree-item" onclick="abrirBlocoEmAba(${index})" style="margin-bottom: 4px; cursor: pointer;">
                                <i class="bi ${icon}" style="color: #a5b4fc;"></i>
                                <span style="flex: 1;">${title}</span>
                                <small style="color: rgba(255,255,255,0.5); font-size: 0.75rem;">${operacao}</small>
                            </div>
                        `;
                    });
                    $('#blocosList').html(html);
                    $('#blocosContainer').show();
                } else {
                    $('#blocosList').html('<div class="empty-state" style="padding: 2rem 1rem;"><i class="bi bi-inbox" style="font-size: 2rem;"></i><p style="font-size: 0.85rem;">Nenhum bloco encontrado nesta rotina</p></div>');
                    $('#blocosContainer').show();
                }
            } else {
                console.error('Estrutura de resposta desconhecida:', res);
                $('#blocosList').html('<div class="text-danger text-center py-3" style="font-size: 0.85rem;">Erro: estrutura de resposta inválida</div>');
            }
        })
        .fail(function(xhr, status, error) {
            console.error('Erro ao carregar blocos:', error);
            $('#blocosList').html('<div class="text-danger text-center py-3" style="font-size: 0.85rem;">Erro ao carregar blocos</div>');
            Swal.fire('Erro', 'Não foi possível carregar os blocos da rotina', 'error');
        });
}

function getIconForOperacao(operacao) {
    const icons = {
        'select': 'bi-search',
        'insert': 'bi-plus-circle',
        'update': 'bi-pencil-square',
        'delete': 'bi-trash',
        'execute': 'bi-play-circle',
        'script': 'bi-file-code',
        'stored_procedure': 'bi-gear',
        'function': 'bi-braces',
        'view': 'bi-eye',
        'trigger': 'bi-lightning',
        'outros': 'bi-code-slash'
    };
    return icons[operacao] || 'bi-code-square';
}

function abrirBlocoEmAba(blocoIndex) {
    // Usar rotina da aba ativa
    if (!tabs[activeTab] || !tabs[activeTab].rotina || !tabs[activeTab].rotina.blocos) {
        Swal.fire('Erro', 'Nenhuma rotina carregada na aba atual', 'error');
        return;
    }
    
    const rotina = tabs[activeTab].rotina;
    if (!rotina.blocos[blocoIndex]) {
        Swal.fire('Erro', 'Bloco não encontrado', 'error');
        return;
    }
    
    const bloco = rotina.blocos[blocoIndex];
    const sql = bloco.script_sql || '';
    
    if (!sql.trim()) {
        Swal.fire('Atenção', 'Este bloco não possui código SQL', 'warning');
        return;
    }
    
    // Criar nova aba com o código do bloco
    tabCounter++;
    const tabId = 'tab' + tabCounter;
    const operacao = bloco.tipo_bloco || bloco.operacao || 'SQL';
    const tabTitle = bloco.codigo_bloco || bloco.descricao || bloco.nome || `Bloco ${blocoIndex + 1}`;
    
    // Adicionar tab
    const tabHtml = `
        <div class="tab-item" data-tab="${tabId}">
            <span class="tab-title">${tabTitle}</span>
            <span class="tab-connection-badge" title="Sem conexão">
                <i class="bi bi-circle-fill"></i>
            </span>
            <button class="tab-close" onclick="fecharAba('${tabId}'); event.stopPropagation();">
                <i class="bi bi-x"></i>
            </button>
        </div>
    `;
    $('#sqlTabs').append(tabHtml);
    
    // Adicionar conteúdo
    const contentHtml = `
        <div class="tab-content" data-tab="${tabId}">
            <div class="tab-controls">
                <div style="display: flex; flex-direction: column; gap: 0.25rem; flex: 1; max-width: 280px;">
                    <label class="tab-controls-label">
                        <i class="bi bi-layers-fill"></i>
                        Rotina
                    </label>
                    <select class="form-select form-select-sm tab-select-rotina" data-tab="${tabId}">
                        <option value="">Selecione uma rotina...</option>
                    </select>
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.25rem; flex: 1; max-width: 280px;">
                    <label class="tab-controls-label">
                        <i class="bi bi-database-fill"></i>
                        Conexão
                    </label>
                    <select class="form-select form-select-sm tab-select-conexao" data-tab="${tabId}">
                        <option value="">Selecione uma conexão...</option>
                    </select>
                </div>
                <button class="btn btn-sm btn-outline-danger tab-disconnect-btn" data-tab="${tabId}" title="Desconectar" style="display: none; align-self: flex-end;">
                    <i class="bi bi-plug"></i>
                    Desconectar
                </button>
            </div>
            <textarea id="sqlEditor_${tabId}" class="sql-textarea">${sql}</textarea>
        </div>
    `;
    $('.sql-editor-area').append(contentHtml);
    
    // Inicializar estrutura da aba
    tabs[tabId] = {
        editor: null,
        connection: null,
        rotina: null,
        metadata: null
    };
    
    // Carregar listas de conexões e rotinas
    carregarConexoesParaAba(tabId);
    carregarRotinasParaAba(tabId);
    
    // Inicializar editor
    initEditor(tabId);
    
    // Ativar nova aba
    ativarAba(tabId);
    
    // Notificar
    Swal.fire({
        icon: 'success',
        title: 'Bloco Carregado!',
        html: `<p><strong>${tabTitle}</strong> aberto em nova aba</p><p class="text-muted" style="font-size: 0.9rem;">Tipo: ${operacao}</p>`,
        timer: 2000,
        showConfirmButton: false
    });
    
    console.log('Bloco aberto com sucesso na aba:', tabId);
}

// Conectar banco em uma aba específica
function conectarBancoNaAba(tabId, conexaoId) {
    if (!tabs[tabId]) return;
    
    Swal.fire({
        title: 'Conectando...',
        html: '<div class="spinner-border text-primary"></div><p class="mt-3">Estabelecendo conexão com o banco de dados</p>',
        allowOutsideClick: false,
        showConfirmButton: false
    });
    
    // Mostrar loading no Database Explorer imediatamente
    if (tabId === activeTab) {
        mostrarLoadingDatabaseExplorer('Conectando ao banco de dados...');
    }
    
    $.getJSON(baseUrl + '/sql-editor/connect/' + conexaoId)
        .done(function(res) {
            Swal.close();
            if (res.sucesso) {
                // Armazenar conexão na aba
                tabs[tabId].connection = {
                    id: conexaoId,
                    nome: res.conexao.nome_conexao,
                    tipo: res.conexao.tipo_banco
                };
                
                // Atualizar badge da aba
                $(`.tab-item[data-tab="${tabId}"]`)
                    .attr('data-connected', 'true')
                    .find('.tab-connection-badge')
                    .attr('title', `${res.conexao.nome_conexao} (${res.conexao.tipo_banco})`);
                
                // Mostrar botão desconectar
                $(`.tab-disconnect-btn[data-tab="${tabId}"]`).show();
                
                // Habilitar botão executar
                $('#btnExecutarToolbar').prop('disabled', false);
                
                // Se for a aba ativa, mostrar progresso no explorer
                if (tabId === activeTab) {
                    mostrarLoadingDatabaseExplorer(`Carregando estrutura do banco...<br><small>Buscando schemas, tabelas, views...</small>`);
                }
                
                // Carregar metadados e objetos do banco
                carregarMetadadosDaAba(tabId, conexaoId);
                carregarObjetosBancoDaAba(tabId, conexaoId);
                
                // Se for a aba ativa, atualizar botão executar
                if (tabId === activeTab) {
                    $('#btnExecutar').prop('disabled', false);
                    $('#btnExecutarFullscreen').prop('disabled', false);
                }
                
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `${res.conexao.nome_conexao}`,
                    text: 'Conectado com sucesso!',
                    showConfirmButton: false,
                    timer: 2500
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro de Conexão',
                    html: `<p>${res.mensagem || 'Falha ao conectar'}</p>`,
                    footer: '<small>Verifique as credenciais e configurações da conexão</small>'
                });
                $(`.tab-select-conexao[data-tab="${tabId}"]`).val('');
                
                // Limpar loading
                if (tabId === activeTab) {
                    $('#databaseTree').html(`
                        <div class="empty-state">
                            <i class="bi bi-database"></i>
                            <p>Conecte-se a um banco de dados para explorar</p>
                        </div>
                    `);
                }
            }
        })
        .fail(function(xhr) {
            Swal.close();
            let errorMsg = 'Erro na comunicação com o servidor';
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.mensagem) errorMsg = response.mensagem;
            } catch (e) {
                errorMsg = xhr.responseText || errorMsg;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Falha na Conexão',
                html: `<p class="text-danger fw-bold">${errorMsg}</p>`,
                width: 600
            });
            $(`.tab-select-conexao[data-tab="${tabId}"]`).val('');
            
            // Limpar loading
            if (tabId === activeTab) {
                $('#databaseTree').html(`
                    <div class="empty-state">
                        <i class="bi bi-x-circle text-danger"></i>
                        <p>Erro ao conectar ao banco de dados</p>
                    </div>
                `);
            }
        });
}

// Desconectar banco de uma aba específica
function desconectarBancoDaAba(tabId) {
    if (!tabs[tabId]) return;
    
    tabs[tabId].connection = null;
    tabs[tabId].metadata = null;
    tabs[tabId].rotina = null;
    
    // Atualizar UI da aba
    $(`.tab-item[data-tab="${tabId}"]`)
        .attr('data-connected', 'false')
        .find('.tab-connection-badge')
        .attr('title', 'Sem conexão');
    
    $(`.tab-select-conexao[data-tab="${tabId}"]`).val('');
    $(`.tab-select-rotina[data-tab="${tabId}"]`).val('');
    $(`.tab-disconnect-btn[data-tab="${tabId}"]`).hide();
    
    // Desabilitar botão executar se a aba ativa não tiver conexão
    if (tabId === activeTab) {
        $('#btnExecutarToolbar').prop('disabled', true);
    }
    
    // Se for a aba ativa, atualizar sidebar e botão
    if (tabId === activeTab) {
        $('#databaseTree').html(`
            <div class="empty-state">
                <i class="bi bi-database"></i>
                <p>Conecte-se a um banco de dados para explorar</p>
            </div>
        `);
        $('#blocosContainer').hide();
        $('#btnExecutar').prop('disabled', true);
        $('#btnExecutarFullscreen').prop('disabled', true);
    }
    
    // Atualizar autocomplete do editor
    if (tabs[tabId].editor) {
        tabs[tabId].editor.setOption('hintOptions', {
            completeSingle: false,
            tables: {},
            keywords: []
        });
    }
}

// Carregar metadados para autocomplete de uma aba
function carregarMetadadosDaAba(tabId, conexaoId) {
    console.log(`📚 [${tabId}] Carregando metadados para autocomplete...`);
    
    $.ajax({
        url: baseUrl + '/sql-editor/metadata/' + conexaoId,
        type: 'GET',
        dataType: 'json',
        timeout: 30000,
        success: function(res) {
            if (res.sucesso && tabs[tabId]) {
                tabs[tabId].metadata = {
                    ...tabs[tabId].metadata,
                    autocomplete: res.metadata
                };
                
                // Atualizar autocomplete do editor desta aba
                if (tabs[tabId].editor) {
                    tabs[tabId].editor.setOption('hintOptions', {
                        completeSingle: false,
                        tables: res.metadata.tables || {},
                        keywords: res.metadata.keywords || []
                    });
                }
                
                const totalTables = Object.keys(res.metadata.tables || {}).length;
                const totalKeywords = (res.metadata.keywords || []).length;
                console.log(`✅ [${tabId}] Autocomplete: ${totalTables} tabelas, ${totalKeywords} keywords`);
                
                // Toast de feedback
                if (tabId === activeTab && totalTables > 0) {
                    Swal.fire({
                        toast: true,
                        position: 'bottom-end',
                        icon: 'success',
                        title: 'Autocomplete Ativo',
                        html: `<small>${totalTables} tabelas disponíveis</small>`,
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                }
            }
        },
        error: function(xhr, status, error) {
            console.error(`❌ [${tabId}] Erro ao carregar metadados:`, error);
        }
    });
}

// Função auxiliar para mostrar loading no Database Explorer
function mostrarLoadingDatabaseExplorer(mensagem, progresso) {
    const progressHtml = progresso ? `
        <div class="progress mt-3" style="height: 6px; background: rgba(255,255,255,0.1);">
            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                 style="width: ${progresso}%; background: linear-gradient(90deg, #667eea, #764ba2);"></div>
        </div>
        <small style="color: rgba(255,255,255,0.6); margin-top: 0.5rem; display: block;">${progresso}% concluído</small>
    ` : '';
    
    $('#databaseTree').html(`
        <div style="padding: 2rem 1rem; text-align: center;">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem; border-width: 0.3em;">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 0.95rem;">${mensagem}</p>
            ${progressHtml}
        </div>
    `);
}

// Carregar objetos do banco para uma aba
function carregarObjetosBancoDaAba(tabId, conexaoId) {
    console.log(`🔍 [${tabId}] Iniciando carregamento de objetos do banco...`);
    
    // Mostrar progresso inicial
    if (tabId === activeTab) {
        mostrarLoadingDatabaseExplorer('Buscando schemas e estruturas...', 20);
    }
    
    $.ajax({
        url: baseUrl + '/sql-editor/objects/' + conexaoId,
        type: 'GET',
        dataType: 'json',
        timeout: 30000,
        success: function(res) {
            console.log(`📦 [${tabId}] Resposta completa recebida:`, res);
            
            if (res.sucesso && tabs[tabId]) {
                // A API retorna { sucesso: true, counts: { schemas: [...] } }
                const counts = res.counts;
                console.log(`✅ [${tabId}] Counts recebidos:`, counts);
                
                if (!counts || !counts.schemas) {
                    console.error(`❌ [${tabId}] Estrutura 'counts' inválida ou vazia`);
                    if (tabId === activeTab) {
                        $('#databaseTree').html(`
                            <div class="empty-state">
                                <i class="bi bi-exclamation-triangle text-warning"></i>
                                <p>Estrutura de dados inválida</p>
                                <small style="color: rgba(255,255,255,0.5);">A API retornou: ${JSON.stringify(res)}</small>
                            </div>
                        `);
                    }
                    return;
                }
                
                // Atualizar progresso
                if (tabId === activeTab) {
                    mostrarLoadingDatabaseExplorer('Processando estrutura...', 60);
                }
                
                // Salvar counts na aba
                tabs[tabId].metadata = {
                    ...tabs[tabId].metadata,
                    counts: counts
                };
                
                // Se for a aba ativa, renderizar árvore
                if (tabId === activeTab) {
                    setTimeout(() => {
                        mostrarLoadingDatabaseExplorer('Finalizando...', 90);
                        
                        setTimeout(() => {
                            console.log(`🌳 [${tabId}] Renderizando ${counts.schemas.length} schemas`);
                            renderizarArvore(counts, conexaoId);
                            
                            // Log de sucesso
                            console.log(`🎉 [${tabId}] ${counts.schemas.length} schemas carregados com sucesso!`);
                        }, 300);
                    }, 200);
                }
            } else {
                console.error(`❌ [${tabId}] Resposta inválida:`, res);
                if (tabId === activeTab) {
                    $('#databaseTree').html(`
                        <div class="empty-state">
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                            <p>Não foi possível carregar a estrutura do banco</p>
                            <small style="color: rgba(255,255,255,0.5);">Tente reconectar</small>
                        </div>
                    `);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error(`❌ [${tabId}] Erro ao carregar objetos:`, error);
            console.error(`Status: ${status}, Response:`, xhr.responseText);
            
            if (tabId === activeTab) {
                $('#databaseTree').html(`
                    <div class="empty-state">
                        <i class="bi bi-x-circle text-danger"></i>
                        <p>Erro ao carregar objetos do banco</p>
                        <small style="color: rgba(255,255,255,0.5);">${error || 'Erro desconhecido'}</small>
                        <button class="btn btn-sm btn-outline-light mt-3" onclick="carregarObjetosBancoDaAba('${tabId}', ${conexaoId})">
                            <i class="bi bi-arrow-clockwise"></i> Tentar Novamente
                        </button>
                    </div>
                `);
            }
        }
    });
}

// Carregar rotina para uma aba
function carregarRotinaParaAba(tabId, rotinaId) {
    $('#blocosList').html('<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></div>');
    $('#blocosContainer').show();
    
    $.getJSON(baseUrl + '/rotinas/get/' + rotinaId)
        .done(function(res) {
            let rotina = null;
            let blocos = [];
            
            if (res.rotina) {
                rotina = res.rotina;
                blocos = res.blocos || [];
            } else if (res.sucesso && res.data) {
                rotina = res.data;
                blocos = res.data.blocos || [];
            } else if (res.id) {
                rotina = res;
                blocos = res.blocos || [];
            }
            
            if (rotina && tabs[tabId]) {
                tabs[tabId].rotina = {
                    ...rotina,
                    blocos: blocos
                };
                
                // Se for a aba ativa, renderizar blocos
                if (tabId === activeTab) {
                    renderizarBlocos(blocos, rotina.nome || 'Rotina');
                }
            }
        })
        .fail(function() {
            Swal.fire('Erro', 'Não foi possível carregar a rotina', 'error');
        });
}

// Renderizar blocos na sidebar
function renderizarBlocos(blocos, nomeRotina) {
    $('#rotinaName').text(nomeRotina);
    
    if (blocos.length > 0) {
        let html = '';
        blocos.forEach(function(bloco, index) {
            const operacao = bloco.tipo_bloco || bloco.operacao || 'outros';
            const icon = getIconForOperacao(operacao.toLowerCase());
            const title = bloco.codigo_bloco || bloco.descricao || bloco.nome || `Bloco ${index + 1}`;
            html += `
                <div class="tree-item" onclick="abrirBlocoEmAba(${index})" style="margin-bottom: 4px; cursor: pointer;">
                    <i class="bi ${icon}" style="color: #a5b4fc;"></i>
                    <span style="flex: 1;">${title}</span>
                    <small style="color: rgba(255,255,255,0.5); font-size: 0.75rem;">${operacao}</small>
                </div>
            `;
        });
        $('#blocosList').html(html);
        $('#blocosContainer').show();
    } else {
        $('#blocosList').html('<div class="empty-state" style="padding: 2rem 1rem;"><i class="bi bi-inbox" style="font-size: 2rem;"></i><p style="font-size: 0.85rem;">Nenhum bloco encontrado</p></div>');
    }
}

function conectarBanco(conexaoId) {
    Swal.fire({
        title: 'Conectando...',
        html: '<div class="spinner-border text-primary"></div><p class="mt-3">Estabelecendo conexão com o banco de dados</p>',
        allowOutsideClick: false,
        showConfirmButton: false
    });
    
    $.getJSON(baseUrl + '/sql-editor/connect/' + conexaoId)
        .done(function(res) {
            Swal.close();
            if (res.sucesso) {
                currentConnection = {
                    id: conexaoId,
                    nome: res.conexao.nome_conexao,
                    tipo: res.conexao.tipo_banco
                };
                
                $('#connectionInfo').html(`
                    <i class="bi bi-circle-fill text-success"></i>
                    ${res.conexao.nome_conexao}
                `);
                $('#btnExecutar').prop('disabled', false);
                $('#btnExecutarFullscreen').prop('disabled', false);
                
                carregarObjetosBanco(conexaoId);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Conectado!',
                    html: `<p>Conexão estabelecida com sucesso</p><p class="text-muted"><strong>${res.conexao.nome_conexao}</strong> (${res.conexao.tipo_banco})</p>`,
                    timer: 2500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro de Conexão',
                    html: `<p>${res.mensagem || 'Falha ao conectar'}</p>`,
                    footer: '<small>Verifique as credenciais e configurações da conexão</small>'
                });
                desconectarBanco();
            }
        })
        .fail(function(xhr) {
            Swal.close();
            let errorMsg = 'Erro na comunicação com o servidor';
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.mensagem) {
                    errorMsg = response.mensagem;
                }
            } catch (e) {
                errorMsg = xhr.responseText || errorMsg;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Falha na Conexão',
                html: `<p class="text-danger fw-bold">${errorMsg}</p>`,
                footer: '<small>💡 Dica: Verifique se as credenciais estão corretas e se a ENCRYPTION_KEY está configurada</small>',
                width: 600
            });
            desconectarBanco();
        });
}

function desconectarBanco() {
    currentConnection = null;
    databaseMetadata = null;
    
    $('#connectionInfo').html(`
        <i class="bi bi-circle-fill text-secondary"></i>
        Desconectado
    `);
    $('#btnExecutar').prop('disabled', true);
    $('#btnExecutarFullscreen').prop('disabled', true);
    $('#databaseTree').html(`
        <div class="empty-state">
            <i class="bi bi-database"></i>
            <p>Conecte-se a um banco de dados para explorar</p>
        </div>
    `);
    
    // Atualizar autocomplete de todos os editores
    atualizarAutocomplete();
}

function carregarMetadados(conexaoId) {
    $.getJSON(baseUrl + '/sql-editor/metadata/' + conexaoId)
        .done(function(res) {
            if (res.sucesso) {
                databaseMetadata = res.metadata;
                console.log('✅ Metadados carregados:', databaseMetadata);
                
                // Atualizar autocomplete de todos os editores ativos
                atualizarAutocomplete();
                
                // Mostrar notificação de sucesso
                const totalTables = Object.keys(databaseMetadata.tables || {}).length;
                const totalKeywords = (databaseMetadata.keywords || []).length;
                
                console.log(`📊 ${totalTables} tabelas e ${totalKeywords} palavras-chave disponíveis para autocomplete`);
                
                // Toast notification
                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: 'success',
                    title: 'Autocomplete Ativo',
                    html: `<small>${totalTables} tabelas com colunas carregadas</small>`,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            } else {
                console.error('❌ Erro ao carregar metadados:', res.mensagem);
            }
        })
        .fail(function(xhr) {
            console.error('❌ Falha ao buscar metadados:', xhr.responseText);
        });
}

function atualizarAutocomplete() {
    // Deprecated: Cada aba agora gerencia seu próprio autocomplete
    // Esta função é mantida para compatibilidade mas não faz nada
    console.log('atualizarAutocomplete() deprecated - autocomplete é por aba agora');
}

function carregarObjetosBanco(conexaoId) {
    $('#databaseTree').html('<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>');
    
    // Carregar metadados para autocomplete
    carregarMetadados(conexaoId);
    
    $.getJSON(baseUrl + '/sql-editor/objects/' + conexaoId)
        .done(function(res) {
            if (res.sucesso) {
                renderizarArvore(res.counts, conexaoId);
            } else {
                $('#databaseTree').html(`<div class="text-center text-danger py-3">Erro ao carregar objetos</div>`);
            }
        })
        .fail(function() {
            $('#databaseTree').html(`<div class="text-center text-danger py-3">Erro ao carregar objetos</div>`);
        });
}

function renderizarArvore(counts, conexaoId) {
    console.log('🌳 renderizarArvore() chamada com:', counts);
    
    // Validar entrada
    if (!counts) {
        console.error('❌ renderizarArvore: counts é undefined/null');
        $('#databaseTree').html(`
            <div class="empty-state">
                <i class="bi bi-exclamation-triangle text-warning"></i>
                <p>Dados de estrutura não disponíveis</p>
                <small style="color: rgba(255,255,255,0.5);">A resposta da API não contém dados válidos</small>
            </div>
        `);
        return;
    }
    
    let html = '';
    
    // Verificar se há schemas
    if (!counts.schemas || !Array.isArray(counts.schemas) || counts.schemas.length === 0) {
        console.warn('⚠️ Nenhum schema encontrado');
        $('#databaseTree').html(`
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <p>Nenhum schema encontrado</p>
                <small style="color: rgba(255,255,255,0.5);">O banco de dados não possui schemas visíveis</small>
            </div>
        `);
        return;
    }
    
    console.log(`📊 Renderizando ${counts.schemas.length} schemas...`);
    
    counts.schemas.forEach(schema => {
            const schemaName = schema.name;
            const tablesCount = schema.tables || 0;
            const viewsCount = schema.views || 0;
            const functionsCount = schema.functions || 0;
            const proceduresCount = schema.procedures || 0;
            const packagesCount = schema.packages || 0;
            
            // Schema container
            html += `
                <div class="tree-node">
                    <div class="tree-item tree-schema" onclick="toggleTreeNode(this)">
                        <i class="bi bi-chevron-right tree-chevron"></i>
                        <i class="bi bi-database-fill text-warning"></i>
                        <span>${schemaName}</span>
                    </div>
                    <div class="tree-children" style="display:none;">
            `;
            
            // Tables
            if (tablesCount > 0) {
                html += `
                    <div class="tree-node">
                        <div class="tree-item tree-category" onclick="expandirObjetos(this, ${conexaoId}, '${schemaName}', 'tables')">
                            <i class="bi bi-chevron-right tree-chevron"></i>
                            <i class="bi bi-table"></i>
                            <span>Tables (${tablesCount})</span>
                        </div>
                        <div class="tree-children" style="display:none;">
                            <div class="tree-loading">
                                <i class="bi bi-hourglass-split"></i>
                                Carregando...
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Views
            if (viewsCount > 0) {
                html += `
                    <div class="tree-node">
                        <div class="tree-item tree-category" onclick="expandirObjetos(this, ${conexaoId}, '${schemaName}', 'views')">
                            <i class="bi bi-chevron-right tree-chevron"></i>
                            <i class="bi bi-eye"></i>
                            <span>Views (${viewsCount})</span>
                        </div>
                        <div class="tree-children" style="display:none;">
                            <div class="tree-loading">
                                <i class="bi bi-hourglass-split"></i>
                                Carregando...
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Functions
            if (functionsCount > 0 || functionsCount === '?') {
                const displayCount = functionsCount === '?' ? '...' : functionsCount;
                html += `
                    <div class="tree-node">
                        <div class="tree-item tree-category" onclick="expandirObjetos(this, ${conexaoId}, '${schemaName}', 'functions')">
                            <i class="bi bi-chevron-right tree-chevron"></i>
                            <i class="bi bi-braces"></i>
                            <span>Functions (${displayCount})</span>
                        </div>
                        <div class="tree-children" style="display:none;">
                            <div class="tree-loading">
                                <i class="bi bi-hourglass-split"></i>
                                Carregando...
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Procedures
            if (proceduresCount > 0 || proceduresCount === '?') {
                const displayCount = proceduresCount === '?' ? '...' : proceduresCount;
                html += `
                    <div class="tree-node">
                        <div class="tree-item tree-category" onclick="expandirObjetos(this, ${conexaoId}, '${schemaName}', 'procedures')">
                            <i class="bi bi-chevron-right tree-chevron"></i>
                            <i class="bi bi-gear"></i>
                            <span>Procedures (${displayCount})</span>
                        </div>
                        <div class="tree-children" style="display:none;">
                            <div class="tree-loading">
                                <i class="bi bi-hourglass-split"></i>
                                Carregando...
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Packages (Oracle)
            if (packagesCount > 0 || packagesCount === '?') {
                const displayCount = packagesCount === '?' ? '...' : packagesCount;
                html += `
                    <div class="tree-node">
                        <div class="tree-item tree-category" onclick="expandirObjetos(this, ${conexaoId}, '${schemaName}', 'packages')">
                            <i class="bi bi-chevron-right tree-chevron"></i>
                            <i class="bi bi-box"></i>
                            <span>Packages (${displayCount})</span>
                        </div>
                        <div class="tree-children" style="display:none;">
                            <div class="tree-loading">
                                <i class="bi bi-hourglass-split"></i>
                                Carregando...
                            </div>
                        </div>
                    </div>
                `;
            }
            
            html += `
                    </div>
                </div>
            `;
        });
    
    // Renderizar árvore
    $('#databaseTree').html(html);
    console.log(`✅ Árvore renderizada com sucesso!`);
}

function toggleTreeNode(element) {
    const $element = $(element);
    const $children = $element.next('.tree-children');
    const $chevron = $element.find('.tree-chevron');
    
    $children.slideToggle(200);
    $chevron.toggleClass('bi-chevron-right bi-chevron-down');
}

function expandirObjetos(element, conexaoId, schema, tipo) {
    const $element = $(element);
    const $children = $element.next('.tree-children');
    const $chevron = $element.find('.tree-chevron');
    
    // Se já foi carregado, apenas toggle
    if ($children.data('loaded')) {
        $children.slideToggle(200);
        $chevron.toggleClass('bi-chevron-right bi-chevron-down');
        return;
    }
    
    // Marcar como carregado
    $children.data('loaded', true);
    
    // Buscar objetos
    const url = `${baseUrl}/sql-editor/${tipo}/${conexaoId}/${encodeURIComponent(schema)}`;
    
    $.getJSON(url)
        .done(function(res) {
            console.log(`📦 Objetos recebidos (${tipo}, schema: ${schema}):`, res);
            
            if (res.sucesso) {
                let html = '';
                
                // Mapeamento: frontend -> backend
                const tipoMap = {
                    'tables': 'tabelas',
                    'views': 'views',
                    'functions': 'functions',
                    'procedures': 'procedures',
                    'packages': 'packages'
                };
                
                const objetos = res[tipoMap[tipo]] || res[tipo] || [];
                console.log(`✅ ${objetos.length} ${tipo} encontrados no schema ${schema}`);
                
                objetos.forEach(obj => {
                    const icon = getObjectIcon(tipo);
                    const color = getObjectColor(tipo);
                    const fullName = schema + '.' + obj;
                    html += `<div class="tree-item tree-object" onclick="inserirNomeTabela('${fullName}')">
                        <i class="bi ${icon} ${color}"></i>
                        <span>${obj}</span>
                    </div>`;
                });
                
                if (html === '') {
                    html = '<div class="tree-empty">Nenhum objeto encontrado</div>';
                }
                
                $children.html(html);
                $children.slideDown(200);
                $chevron.removeClass('bi-chevron-right').addClass('bi-chevron-down');
            } else {
                console.error(`❌ Erro ao buscar ${tipo}:`, res.mensagem);
                $children.html(`<div class="tree-error">Erro: ${res.mensagem || 'Desconhecido'}</div>`);
            }
        })
        .fail(function(xhr, status, error) {
            console.error(`❌ Falha na requisição (${tipo}, schema: ${schema}):`, error);
            $children.html('<div class="tree-error">Erro ao carregar</div>');
        });
}

function getObjectIcon(tipo) {
    const icons = {
        'tables': 'bi-table',
        'views': 'bi-eye',
        'functions': 'bi-braces',
        'procedures': 'bi-gear',
        'packages': 'bi-box'
    };
    return icons[tipo] || 'bi-circle';
}

function getObjectColor(tipo) {
    const colors = {
        'tables': 'text-primary',
        'views': 'text-info',
        'functions': 'text-success',
        'procedures': 'text-danger',
        'packages': 'text-purple'
    };
    return colors[tipo] || 'text-muted';
}

function inserirNomeTabela(tableName) {
    if (tabs[activeTab] && tabs[activeTab].editor) {
        const editor = tabs[activeTab].editor;
        const cursor = editor.getCursor();
        editor.replaceRange(tableName, cursor);
        editor.focus();
    }
}

function novaAba() {
    tabCounter++;
    const tabId = 'tab' + tabCounter;
    
    // Adicionar tab
    const tabHtml = `
        <div class="tab-item" data-tab="${tabId}">
            <span class="tab-title">Query ${tabCounter}</span>
            <span class="tab-connection-badge" title="Sem conexão">
                <i class="bi bi-circle-fill"></i>
            </span>
            <button class="tab-close" onclick="fecharAba('${tabId}')">
                <i class="bi bi-x"></i>
            </button>
        </div>
    `;
    $('#sqlTabs').append(tabHtml);
    
    // Adicionar conteúdo
    const contentHtml = `
        <div class="tab-content" data-tab="${tabId}">
            <div class="tab-controls">
                <div style="display: flex; flex-direction: column; gap: 0.25rem; flex: 1; max-width: 280px;">
                    <label class="tab-controls-label">
                        <i class="bi bi-layers-fill"></i>
                        Rotina
                    </label>
                    <select class="form-select form-select-sm tab-select-rotina" data-tab="${tabId}">
                        <option value="">Selecione uma rotina...</option>
                    </select>
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.25rem; flex: 1; max-width: 280px;">
                    <label class="tab-controls-label">
                        <i class="bi bi-database-fill"></i>
                        Conexão
                    </label>
                    <select class="form-select form-select-sm tab-select-conexao" data-tab="${tabId}">
                        <option value="">Selecione uma conexão...</option>
                    </select>
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.25rem; max-width: 140px;">
                    <label class="tab-controls-label">
                        <i class="bi bi-hash"></i>
                        Limite de Linhas
                    </label>
                    <input type="number" class="form-control form-control-sm tab-limit-rows" data-tab="${tabId}" value="100" min="1" max="100000" title="Número máximo de linhas a retornar">
                </div>
                <button class="btn btn-sm btn-outline-danger tab-disconnect-btn" data-tab="${tabId}" title="Desconectar" style="display: none; align-self: flex-end;">
                    <i class="bi bi-plug"></i>
                    Desconectar
                </button>
            </div>
            <textarea id="sqlEditor_${tabId}" class="sql-textarea"></textarea>
        </div>
    `;
    $('.sql-editor-area').append(contentHtml);
    
    // Inicializar estrutura da aba
    tabs[tabId] = {
        editor: null,
        connection: null,
        rotina: null,
        metadata: null,
        fileName: null,
        filePath: null,
        fileHandle: null
    };
    
    // Inicializar unsaved changes
    unsavedChanges[tabId] = false;
    
    // Carregar listas de conexões e rotinas para a nova aba
    carregarConexoesParaAba(tabId);
    carregarRotinasParaAba(tabId);
    
    // Inicializar editor
    initEditor(tabId);
    
    // Ativar nova aba
    ativarAba(tabId);
}

function ativarAba(tabId) {
    activeTab = tabId;
    $('.tab-item').removeClass('active');
    $(`.tab-item[data-tab="${tabId}"]`).addClass('active');
    $('.tab-content').removeClass('active');
    $(`.tab-content[data-tab="${tabId}"]`).addClass('active');
    
    if (tabs[tabId] && tabs[tabId].editor) {
        tabs[tabId].editor.refresh();
        tabs[tabId].editor.focus();
    }
    
    console.log(`🔄 Aba ativada: ${tabId}`);
    
    // Atualizar sidebar com objetos do banco conectado nesta aba
    if (tabs[tabId] && tabs[tabId].connection) {
        console.log(`📊 Atualizando sidebar para conexão:`, tabs[tabId].connection.nome);
        atualizarSidebarParaAba(tabId);
    } else {
        console.log(`⚠️ Aba ${tabId} não possui conexão ativa`);
        $('#databaseTree').html(`
            <div class="empty-state">
                <i class="bi bi-database"></i>
                <p>Conecte-se a um banco de dados para explorar</p>
            </div>
        `);
        $('#blocosContainer').hide();
    }
    
    // Atualizar estado do botão executar
    const temConexao = tabs[tabId] && tabs[tabId].connection;
    $('#btnExecutarToolbar').prop('disabled', !temConexao);
}

$(document).on('click', '.tab-item', function() {
    const tabId = $(this).data('tab');
    ativarAba(tabId);
});

function fecharAba(tabId) {
    // Não permitir fechar última aba
    if ($('.tab-item').length === 1) {
        Swal.fire('Atenção', 'Não é possível fechar a última aba', 'warning');
        return;
    }
    
    // Verificar mudanças não salvas
    if (!verificarMudancasNaoSalvas(tabId)) {
        return;
    }
    
    // Remover tab e conteúdo
    $(`.tab-item[data-tab="${tabId}"]`).remove();
    $(`.tab-content[data-tab="${tabId}"]`).remove();
    delete tabs[tabId];
    delete unsavedChanges[tabId];
    
    // Ativar primeira aba restante
    if (activeTab === tabId) {
        const firstTab = $('.tab-item').first().data('tab');
        ativarAba(firstTab);
    }
}

function fecharAbaAtual() {
    fecharAba(activeTab);
}

$('#btnExecutar, #btnExecutarFullscreen').on('click', executarQuery);

function executarQuery(isLoadMore = false) {
    // Verificar se a aba ativa tem conexão
    if (!tabs[activeTab] || !tabs[activeTab].connection) {
        Swal.fire('Atenção', 'Conecte-se a um banco de dados primeiro', 'warning');
        return;
    }
    
    const editor = tabs[activeTab].editor;
    if (!editor) return;
    
    let sql;
    if (isLoadMore && tabs[activeTab].lastQuery) {
        // Usar a última query salva para carregar mais
        sql = tabs[activeTab].lastQuery;
    } else {
        // Nova query
        sql = editor.getSelection() || editor.getValue();
        sql = sql.trim();
        
        if (!sql) {
            Swal.fire('Atenção', 'Digite uma query SQL', 'warning');
            return;
        }
        
        // Reset offset para nova query
        tabs[activeTab].queryOffset = 0;
        tabs[activeTab].hasMoreResults = true;
        tabs[activeTab].lastQuery = sql;
    }
    
    // Obter limite de linhas da aba ativa
    const limitInput = $(`.tab-limit-rows[data-tab="${activeTab}"]`);
    let limit = parseInt(limitInput.val()) || 100;
    
    // Validar limite
    if (limit < 1) limit = 1;
    if (limit > 100000) limit = 100000;
    
    const offset = tabs[activeTab].queryOffset;
    
    // Remover ponto e vírgula do final (Oracle não aceita)
    sql = sql.replace(/;\s*$/, '');
    
    // Aplicar LIMIT/OFFSET automaticamente se for SELECT e não tiver LIMIT
    const sqlUpper = sql.toUpperCase();
    const isSelect = sqlUpper.trim().startsWith('SELECT') || sqlUpper.includes('\nSELECT');
    const hasLimit = sqlUpper.includes('LIMIT') || sqlUpper.includes('ROWNUM') || 
                     sqlUpper.includes('FETCH FIRST') || sqlUpper.includes('TOP ');
    
    if (isSelect && !hasLimit) {
        // Detectar tipo de banco
        const tipoBanco = tabs[activeTab].connection.tipo || 'mysql';
        
        if (tipoBanco === 'oracle') {
            // Oracle 12c+ usa OFFSET/FETCH
            sql = sql + `\nOFFSET ${offset} ROWS FETCH NEXT ${limit} ROWS ONLY`;
        } else if (tipoBanco === 'sqlserver') {
            // SQL Server 2012+ usa OFFSET/FETCH (precisa ORDER BY)
            if (!sqlUpper.includes('ORDER BY')) {
                // Adicionar ORDER BY fictício se não existir
                sql = sql + ' ORDER BY (SELECT NULL)';
            }
            sql = sql + `\nOFFSET ${offset} ROWS FETCH NEXT ${limit} ROWS ONLY`;
        } else if (tipoBanco === 'postgres' || tipoBanco === 'postgresql') {
            // PostgreSQL usa LIMIT/OFFSET
            sql = sql + `\nLIMIT ${limit} OFFSET ${offset}`;
        } else {
            // MySQL usa LIMIT/OFFSET
            sql = sql + `\nLIMIT ${limit} OFFSET ${offset}`;
        }
        
        console.log(`📊 Limite: ${limit}, Offset: ${offset} (${tipoBanco})`);
        console.log(`📝 SQL modificado:`, sql);
    }
    
    // Mostrar loading
    if (isLoadMore) {
        tabs[activeTab].isLoadingMore = true;
        // Adicionar indicador de loading no final da tabela
        $('#resultTable tbody').append(`
            <tr id="loadingMoreRow" style="background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%);">
                <td colspan="100" style="text-align: center; padding: 30px;">
                    <div class="d-flex align-items-center justify-content-center gap-3">
                        <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <div>
                            <div style="font-size: 1.1rem; font-weight: 600; color: #667eea;">
                                <i class="bi bi-arrow-down-circle-fill me-2"></i>Carregando mais resultados...
                            </div>
                            <div style="font-size: 0.85rem; color: #6c757d; margin-top: 0.25rem;">
                                Buscando próximas ${limit} linhas
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        `);
    } else {
        $('#noResults').html(`
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-3">Executando query...</p>
            <p class="text-muted" style="font-size: 0.85rem;">${tabs[activeTab].connection.nome}</p>
            <small class="text-muted">Limite: ${limit} linhas</small>
        `);
        $('#resultTable').hide();
    }
    
    const startTime = Date.now();
    
    $.post(baseUrl + '/sql-editor/execute', {
        conexao_id: tabs[activeTab].connection.id,
        sql: sql
    }, function(res) {
        const duration = Date.now() - startTime;
        tabs[activeTab].isLoadingMore = false;
        $('#loadingMoreRow').remove();
        
        if (res.sucesso) {
            if (isLoadMore) {
                // Append novos resultados
                if (res.dados && res.dados.length > 0) {
                    appendResultados(res);
                    tabs[activeTab].queryOffset += limit;
                } else {
                    // Não há mais resultados - mostrar "FIM!"
                    tabs[activeTab].hasMoreResults = false;
                    $('#resultTable tbody').append(`
                        <tr id="endOfResultsRow" style="background: linear-gradient(90deg, #d4edda 0%, #c3e6cb 100%); animation: fadeIn 0.5s ease;">
                            <td colspan="100" style="text-align: center; padding: 35px;">
                                <div style="font-size: 2.5rem; color: #28a745; margin-bottom: 0.5rem;">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div style="font-size: 1.5rem; font-weight: 700; color: #155724; margin-bottom: 0.5rem;">
                                    FIM!
                                </div>
                                <div style="font-size: 0.95rem; color: #28a745;">
                                    Todos os resultados foram carregados
                                </div>
                                <div style="font-size: 0.85rem; color: #6c757d; margin-top: 0.5rem;">
                                    <i class="bi bi-info-circle"></i> Total de ${tabs[activeTab].queryOffset} linhas carregadas
                                </div>
                            </td>
                        </tr>
                    `);
                }
            } else {
                // Nova query - substituir resultados
                exibirResultados(res, duration);
                tabs[activeTab].queryOffset = limit;
            }
        } else {
            exibirErro(res.erro || res.mensagem, duration);
        }
    }, 'json').fail(function(xhr) {
        const duration = Date.now() - startTime;
        tabs[activeTab].isLoadingMore = false;
        $('#loadingMoreRow').remove();
        exibirErro('Erro na comunicação com o servidor: ' + xhr.responseText, duration);
    });
}

function exibirResultados(res, duration) {
    // Expandir painel de resultados automaticamente se estiver minimizado
    const resultsPanel = document.getElementById('sqlResults');
    if (resultsPanel && resultsPanel.classList.contains('collapsed')) {
        toggleResultsPanel();
    }
    
    // Messages
    let msg = `<p class="text-success fw-bold"><i class="bi bi-check-circle-fill me-2"></i>Query executada com sucesso!</p>`;
    msg += `<p class="text-muted">⏱️ Tempo de execução: <strong>${duration}ms</strong></p>`;
    if (res.linhas_afetadas !== undefined) {
        msg += `<p class="text-info">📊 Linhas afetadas: <strong>${res.linhas_afetadas}</strong></p>`;
    }
    if (res.linhas > 0) {
        msg += `<p class="text-primary">📈 Total de registros: <strong>${res.linhas}</strong></p>`;
    }
    $('#messagesContent').html(msg);
    
    // Info
    let info = `<table class="table table-sm table-borderless">`;
    info += `<tr><th style="width: 40%">⏱️ Duração:</th><td><strong>${duration}ms</strong></td></tr>`;
    info += `<tr><th>📊 Linhas Retornadas:</th><td><strong>${res.linhas || 0}</strong></td></tr>`;
    info += `<tr><th>📋 Colunas:</th><td><strong>${res.colunas ? res.colunas.length : 0}</strong></td></tr>`;
    if (res.colunas && res.colunas.length > 0) {
        info += `<tr><th>🔤 Nomes das Colunas:</th><td><code>${res.colunas.join(', ')}</code></td></tr>`;
    }
    info += `</table>`;
    $('#infoContent').html(info);
    
    // Table
    if (res.dados && res.dados.length > 0) {
        renderizarTabela(res);
    } else {
        $('#noResults').html(`
            <i class="bi bi-check-circle text-success"></i>
            <p>✅ Query executada com sucesso! Nenhum dado retornado.</p>
            <p class="text-muted"><small>Tempo: ${duration}ms</small></p>
        `);
        $('#resultTable').hide();
    }
}

function renderizarTabela(res) {
    const table = $('#resultTable');
    const thead = table.find('thead');
    const tbody = table.find('tbody');
    
    // Clear
    thead.empty();
    tbody.empty();
    
    // Header
    if (res.colunas && res.colunas.length > 0) {
        let headerHtml = '<tr>';
        res.colunas.forEach(col => {
            headerHtml += `<th>${col}</th>`;
        });
        headerHtml += '</tr>';
        thead.html(headerHtml);
    }
    
    // Data
    if (res.dados && res.dados.length > 0) {
        res.dados.forEach(row => {
            let rowHtml = '<tr>';
            res.colunas.forEach(col => {
                const value = row[col];
                rowHtml += `<td>${value !== null && value !== undefined ? value : '<span class="text-muted">NULL</span>'}</td>`;
            });
            rowHtml += '</tr>';
            tbody.append(rowHtml);
        });
    }
    
    $('#noResults').hide();
    table.show();
}

// Adicionar resultados à tabela existente (para scroll infinito)
function appendResultados(res) {
    const table = $('#resultTable');
    const tbody = table.find('tbody');
    
    // Data
    if (res.dados && res.dados.length > 0) {
        res.dados.forEach(row => {
            let rowHtml = '<tr>';
            res.colunas.forEach(col => {
                const value = row[col];
                rowHtml += `<td>${value !== null && value !== undefined ? value : '<span class="text-muted">NULL</span>'}</td>`;
            });
            rowHtml += '</tr>';
            tbody.append(rowHtml);
        });
        
        console.log(`✅ +${res.dados.length} linhas adicionadas à tabela`);
    }
}

function exibirErro(erro, duration) {
    // Expandir painel de resultados automaticamente se estiver minimizado
    const resultsPanel = document.getElementById('sqlResults');
    if (resultsPanel && resultsPanel.classList.contains('collapsed')) {
        toggleResultsPanel();
    }
    
    // Messages
    let msg = `<p class="text-danger fw-bold"><i class="bi bi-x-circle-fill me-2"></i>Erro ao executar query</p>`;
    msg += `<p class="text-muted">⏱️ Tempo: ${duration}ms</p>`;
    msg += `<pre class="bg-dark text-light p-3 rounded mt-3">${erro}</pre>`;
    $('#messagesContent').html(msg);
    
    // Switch to messages tab
    $('.result-tab[data-result="messages"]').click();
    
    // Clear table
    $('#resultTable').hide();
    $('#noResults').html(`
        <i class="bi bi-x-circle text-danger"></i>
        <p>❌ Erro ao executar query</p>
        <p class="text-muted"><small>Verifique a sintaxe SQL e tente novamente</small></p>
    `);
}

function formatarSQL() {
    if (!tabs[activeTab] || !tabs[activeTab].editor) return;
    const editor = tabs[activeTab].editor;
    
    // Simples formatação (pode ser melhorada com biblioteca)
    let sql = editor.getValue();
    sql = sql.replace(/\s+/g, ' ').trim();
    sql = sql.replace(/SELECT/gi, '\nSELECT');
    sql = sql.replace(/FROM/gi, '\nFROM');
    sql = sql.replace(/WHERE/gi, '\nWHERE');
    sql = sql.replace(/AND/gi, '\n  AND');
    sql = sql.replace(/OR/gi, '\n  OR');
    sql = sql.replace(/ORDER BY/gi, '\nORDER BY');
    sql = sql.replace(/GROUP BY/gi, '\nGROUP BY');
    sql = sql.replace(/LIMIT/gi, '\nLIMIT');
    
    editor.setValue(sql);
}

function limparEditor() {
    if (!tabs[activeTab] || !tabs[activeTab].editor) return;
    const editor = tabs[activeTab].editor;
    
    Swal.fire({
        title: 'Limpar editor?',
        text: 'Todo o conteúdo será removido',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, limpar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#667eea',
        cancelButtonColor: '#64748b'
    }).then((result) => {
        if (result.isConfirmed) {
            editor.setValue('');
            Swal.fire({
                title: 'Limpo!',
                text: 'Editor foi limpo',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
}

function salvarQuery() {
    if (!tabs[activeTab] || !tabs[activeTab].editor) return;
    const editor = tabs[activeTab].editor;
    
    const sql = editor.getValue().trim();
    if (!sql) {
        Swal.fire('Atenção', 'Não há conteúdo para salvar', 'warning');
        return;
    }
    
    // Criar arquivo para download
    const blob = new Blob([sql], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `query_${Date.now()}.sql`;
    a.click();
    window.URL.revokeObjectURL(url);
    
    Swal.fire({
        title: 'Salvo!',
        text: 'Query salva com sucesso',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
    });
}

function limparResultados() {
    $('#resultTable').hide();
    $('#resultTable thead, #resultTable tbody').empty();
    $('#noResults').html(`
        <i class="bi bi-inbox"></i>
        <p>Execute uma query SQL para visualizar os resultados aqui</p>
    `).show();
    $('#messagesContent').html('<p class="text-muted">📋 Nenhuma mensagem ainda</p>');
    $('#infoContent').html('<p class="text-muted">ℹ️ Execute uma query para ver informações detalhadas</p>');
}

function toggleSidebar() {
    sidebarCollapsed = !sidebarCollapsed;
    const sidebar = document.getElementById('sqlSidebar');
    const btnIcon = document.querySelector('#btnToggleSidebar i');
    
    if (sidebarCollapsed) {
        sidebar.classList.add('hidden');
        btnIcon.classList.remove('bi-layout-sidebar-inset');
        btnIcon.classList.add('bi-layout-sidebar-inset-reverse');
        document.querySelector('#btnToggleSidebar').setAttribute('title', 'Mostrar Database Explorer');
    } else {
        sidebar.classList.remove('hidden');
        btnIcon.classList.remove('bi-layout-sidebar-inset-reverse');
        btnIcon.classList.add('bi-layout-sidebar-inset');
        document.querySelector('#btnToggleSidebar').setAttribute('title', 'Ocultar Database Explorer');
    }
    
    // Refresh dos editores após animação
    setTimeout(() => {
        Object.keys(tabs).forEach(tabId => {
            if (tabs[tabId] && tabs[tabId].editor) {
                tabs[tabId].editor.refresh();
            }
        });
    }, 300);
}

function toggleResultsPanel() {
    resultsCollapsed = !resultsCollapsed;
    const resultsPanel = document.getElementById('sqlResults');
    const btnIcon = document.querySelector('#btnToggleResults i');
    
    if (resultsCollapsed) {
        resultsPanel.classList.add('hidden');
        btnIcon.classList.remove('bi-chevron-down');
        btnIcon.classList.add('bi-chevron-up');
        document.querySelector('#btnToggleResults').setAttribute('title', 'Mostrar Resultados');
    } else {
        resultsPanel.classList.remove('hidden');
        btnIcon.classList.remove('bi-chevron-up');
        btnIcon.classList.add('bi-chevron-down');
        document.querySelector('#btnToggleResults').setAttribute('title', 'Ocultar Resultados');
    }
    
    // Refresh dos editores após animação
    setTimeout(() => {
        Object.keys(tabs).forEach(tabId => {
            if (tabs[tabId] && tabs[tabId].editor) {
                tabs[tabId].editor.refresh();
            }
        });
    }, 300);
}

function toggleFullscreen() {
    const container = document.getElementById('sqlEditorContainer');
    const results = document.getElementById('sqlResults');
    
    if (!isFullscreen) {
        // Entrar em fullscreen
        if (container.requestFullscreen) {
            container.requestFullscreen();
        } else if (container.webkitRequestFullscreen) {
            container.webkitRequestFullscreen();
        } else if (container.mozRequestFullScreen) {
            container.mozRequestFullScreen();
        } else if (container.msRequestFullscreen) {
            container.msRequestFullscreen();
        }
        
        container.classList.add('fullscreen');
        isFullscreen = true;
        
        // Atualizar ícone do botão
        $('#btnFullscreen i').removeClass('bi-arrows-fullscreen').addClass('bi-fullscreen-exit');
        $('#btnFullscreen').attr('title', 'Sair do Modo Tela Cheia (ESC)');
        
        // Atualizar título
        const connName = (tabs[activeTab] && tabs[activeTab].connection) ? tabs[activeTab].connection.nome : 'Desconectado';
        $('#fullscreenTitle').text(`SQL Editor - ${connName}`);
        
        // Garantir que o painel de resultados preenche corretamente
        if (results) {
            if (layoutOrientation === 'horizontal') {
                results.style.width = results.style.width || '400px';
                results.style.height = '100%';
            } else {
                results.style.height = results.style.height || '300px';
                results.style.width = '100%';
            }
        }
        
        // Refresh dos editores
        setTimeout(() => {
            Object.keys(tabs).forEach(tabId => {
                if (tabs[tabId] && tabs[tabId].editor) {
                    tabs[tabId].editor.refresh();
                }
            });
        }, 100);
    } else {
        // Sair do fullscreen
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
        
        container.classList.remove('fullscreen');
        isFullscreen = false;
        
        // Atualizar ícone do botão
        $('#btnFullscreen i').removeClass('bi-fullscreen-exit').addClass('bi-arrows-fullscreen');
        $('#btnFullscreen').attr('title', 'Modo Tela Cheia (F11)');
        
        // Garantir que o painel de resultados mantenha dimensões corretas
        if (results) {
            if (layoutOrientation === 'horizontal') {
                results.style.width = results.style.width || '400px';
                results.style.height = '100%';
            } else {
                results.style.height = results.style.height || '300px';
                results.style.width = '100%';
            }
        }
        
        // Refresh dos editores
        setTimeout(() => {
            Object.keys(tabs).forEach(tabId => {
                if (tabs[tabId] && tabs[tabId].editor) {
                    tabs[tabId].editor.refresh();
                }
            });
        }, 100);
    }
}

// Nova função: Executar query pela toolbar
function executarQueryToolbar() {
    executarQuery();
}

// Nova função: Toggle autocomplete contínuo
function toggleAutocomplete() {
    autocompleteEnabled = !autocompleteEnabled;
    const btn = document.getElementById('btnAutocomplete');
    
    if (autocompleteEnabled) {
        btn.classList.add('active');
        btn.setAttribute('title', 'Desativar Autocomplete Contínuo (Ativo)');
        
        // Ativar autocomplete em todos os editores
        Object.keys(tabs).forEach(tabId => {
            if (tabs[tabId] && tabs[tabId].editor) {
                tabs[tabId].editor.setOption('extraKeys', {
                    ...tabs[tabId].editor.getOption('extraKeys'),
                    'Ctrl-Space': 'autocomplete'
                });
                // Trigger autocomplete on every keyup
                tabs[tabId].editor.on('inputRead', function(cm, change) {
                    if (autocompleteEnabled && change.text[0] && /\w/.test(change.text[0])) {
                        cm.showHint({completeSingle: false});
                    }
                });
            }
        });
        
        Swal.fire({
            icon: 'success',
            title: 'Autocomplete Ativado',
            text: 'O autocomplete será exibido automaticamente enquanto você digita',
            timer: 2000,
            showConfirmButton: false
        });
    } else {
        btn.classList.remove('active');
        btn.setAttribute('title', 'Ativar Autocomplete Contínuo');
        
        Swal.fire({
            icon: 'info',
            title: 'Autocomplete Desativado',
            text: 'Use Ctrl+Space para ativar o autocomplete manualmente',
            timer: 2000,
            showConfirmButton: false
        });
    }
}

// Nova função: Abrir arquivo do computador
function abrirArquivo() {
    const fileInput = document.getElementById('fileInput');
    fileInput.onchange = function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        const reader = new FileReader();
        reader.onload = function(event) {
            const content = event.target.result;
            const tabId = activeTab;
            
            if (tabs[tabId] && tabs[tabId].editor) {
                tabs[tabId].editor.setValue(content);
                tabs[tabId].fileName = file.name;
                tabs[tabId].filePath = file.name; // Navegador não fornece path completo por segurança
                tabs[tabId].fileHandle = file; // Guardar referência ao arquivo
                unsavedChanges[tabId] = false;
                
                // Atualizar título da aba
                const tabTitle = file.name.replace(/\.sql$/i, '');
                $(`.tab-item[data-tab="${tabId}"] .tab-title`).text(tabTitle);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Arquivo Aberto!',
                    html: `<strong>${file.name}</strong><br><small>${(file.size / 1024).toFixed(2)} KB</small>`,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        };
        reader.readAsText(file);
    };
    fileInput.click();
}

// Nova função: Salvar Como (sempre pergunta onde salvar)
function salvarComo() {
    const tabId = activeTab;
    if (!tabs[tabId]) return;
    
    const content = tabs[tabId].editor.getValue();
    if (!content.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Editor Vazio',
            text: 'Não há conteúdo para salvar'
        });
        return;
    }
    
    Swal.fire({
        title: 'Salvar Como',
        input: 'text',
        inputLabel: 'Nome do arquivo',
        inputValue: tabs[tabId].fileName || 'script.sql',
        inputPlaceholder: 'Ex: minha_query.sql',
        showCancelButton: true,
        confirmButtonText: 'Salvar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value) {
                return 'Digite um nome para o arquivo!';
            }
            if (!value.endsWith('.sql')) {
                return 'O arquivo deve ter extensão .sql';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const fileName = result.value;
            baixarArquivo(content, fileName);
            
            // Atualizar info da aba
            tabs[tabId].fileName = fileName;
            tabs[tabId].filePath = fileName;
            unsavedChanges[tabId] = false;
            
            // Atualizar título da aba (remover asterisco)
            const tabTitle = fileName.replace(/\.sql$/i, '');
            $(`.tab-item[data-tab="${tabId}"] .tab-title`).text(tabTitle);
            
            Swal.fire({
                icon: 'success',
                title: 'Arquivo Salvo!',
                html: `<strong>${fileName}</strong> foi salvo com sucesso`,
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

// Nova função: Salvar script manualmente
function salvarScriptManual() {
    const tabId = activeTab;
    if (!tabs[tabId]) return;
    
    const content = tabs[tabId].editor.getValue();
    if (!content.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Editor Vazio',
            text: 'Não há conteúdo para salvar'
        });
        return;
    }
    
    // Se o arquivo já foi salvo/aberto antes, sobrescrever
    if (tabs[tabId].fileName && tabs[tabId].filePath) {
        baixarArquivo(content, tabs[tabId].fileName);
        unsavedChanges[tabId] = false;
        
        // Remover asterisco do título da aba
        const tabTitle = $(`.tab-item[data-tab="${tabId}"] .tab-title`);
        const currentTitle = tabTitle.text().replace(' *', '');
        tabTitle.text(currentTitle);
        
        Swal.fire({
            icon: 'success',
            title: 'Arquivo Salvo!',
            html: `<strong>${tabs[tabId].fileName}</strong> foi atualizado`,
            timer: 1500,
            showConfirmButton: false
        });
    } else {
        // Se nunca foi salvo, usar "Salvar Como"
        salvarComo();
    }
}

// Função auxiliar para baixar arquivo
function baixarArquivo(content, fileName) {
    const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(link.href);
}

// Nova função: Toggle auto-save
function toggleAutoSave() {
    autoSaveEnabled = !autoSaveEnabled;
    const btn = document.getElementById('btnAutoSave');
    
    if (autoSaveEnabled) {
        btn.classList.add('active');
        btn.setAttribute('title', 'Desativar Salvamento Automático (Ativo)');
        
        // Iniciar auto-save a cada 30 segundos
        autoSaveInterval = setInterval(() => {
            Object.keys(tabs).forEach(tabId => {
                if (tabs[tabId] && unsavedChanges[tabId]) {
                    const content = tabs[tabId].editor.getValue();
                    if (content.trim()) {
                        const scripts = JSON.parse(localStorage.getItem('sqlEditorAutoSave') || '{}');
                        scripts[tabId] = {
                            content: content,
                            timestamp: new Date().toISOString(),
                            connection: tabs[tabId].connection ? tabs[tabId].connection.nome : 'Sem conexão'
                        };
                        localStorage.setItem('sqlEditorAutoSave', JSON.stringify(scripts));
                    }
                }
            });
        }, 30000); // 30 segundos
        
        Swal.fire({
            icon: 'success',
            title: 'Auto-Save Ativado',
            text: 'Seus scripts serão salvos automaticamente a cada 30 segundos',
            timer: 2000,
            showConfirmButton: false
        });
    } else {
        btn.classList.remove('active');
        btn.setAttribute('title', 'Ativar Salvamento Automático');
        
        if (autoSaveInterval) {
            clearInterval(autoSaveInterval);
            autoSaveInterval = null;
        }
        
        Swal.fire({
            icon: 'info',
            title: 'Auto-Save Desativado',
            text: 'Use Ctrl+S para salvar manualmente',
            timer: 2000,
            showConfirmButton: false
        });
    }
}

// Função para verificar mudanças não salvas ao fechar aba
function verificarMudancasNaoSalvas(tabId) {
    if (!tabs[tabId]) return true;
    
    const content = tabs[tabId].editor.getValue();
    if (content.trim() && unsavedChanges[tabId]) {
        const fileName = tabs[tabId].fileName || 'script sem nome';
        return confirm(`O arquivo "${fileName}" contém alterações não salvas. Deseja realmente fechar?`);
    }
    return true;
}

// Adicionar event listener para beforeunload
window.addEventListener('beforeunload', function(e) {
    // Verificar se há conteúdo não salvo em alguma aba
    let hasUnsaved = false;
    Object.keys(tabs).forEach(tabId => {
        const content = tabs[tabId] && tabs[tabId].editor ? tabs[tabId].editor.getValue() : '';
        if (content.trim() && unsavedChanges[tabId]) {
            hasUnsaved = true;
        }
    });
    
    if (hasUnsaved) {
        e.preventDefault();
        e.returnValue = 'Você tem alterações não salvas. Deseja realmente sair?';
        return e.returnValue;
    }
});

function handleFullscreenChange() {
    const isCurrentlyFullscreen = !!(document.fullscreenElement || 
                                      document.webkitFullscreenElement || 
                                      document.mozFullScreenElement || 
                                      document.msFullscreenElement);
    
    const results = document.getElementById('sqlResults');
    
    if (!isCurrentlyFullscreen && isFullscreen) {
        // Usuário saiu do fullscreen usando ESC
        const container = document.getElementById('sqlEditorContainer');
        container.classList.remove('fullscreen');
        isFullscreen = false;
        
        $('#btnFullscreen i').removeClass('bi-fullscreen-exit').addClass('bi-arrows-fullscreen');
        $('#btnFullscreen').attr('title', 'Modo Tela Cheia (F11)');
        
        // Garantir que o painel de resultados mantenha dimensões corretas
        if (results) {
            if (layoutOrientation === 'horizontal') {
                results.style.width = results.style.width || '400px';
                results.style.height = '100%';
            } else {
                results.style.height = results.style.height || '300px';
                results.style.width = '100%';
            }
        }
        
        setTimeout(() => {
            Object.keys(tabs).forEach(tabId => {
                if (tabs[tabId] && tabs[tabId].editor) {
                    tabs[tabId].editor.refresh();
                }
            });
        }, 100);
    }
}

function exportarResultados(formato) {
    const table = $('#resultTable');
    if (!table.is(':visible')) {
        Swal.fire('Atenção', 'Nenhum resultado para exportar', 'warning');
        return;
    }
    
    if (formato === 'csv') {
        let csv = '';
        
        // Header
        table.find('thead th').each(function() {
            csv += $(this).text() + ';';
        });
        csv = csv.slice(0, -1) + '\n';
        
        // Data
        table.find('tbody tr').each(function() {
            $(this).find('td').each(function() {
                const text = $(this).text();
                // Escapar aspas duplas e adicionar aspas se contiver ponto e vírgula
                const escapedText = text.replace(/"/g, '""');
                if (escapedText.includes(';') || escapedText.includes('\n') || escapedText.includes('"')) {
                    csv += '"' + escapedText + '";';
                } else {
                    csv += escapedText + ';';
                }
            });
            csv = csv.slice(0, -1) + '\n';
        });
        
        // Download com BOM UTF-8 para Excel reconhecer corretamente
        const BOM = '\uFEFF';
        const blob = new Blob([BOM + csv], { type: 'text/csv;charset=utf-8;' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'resultados_' + Date.now() + '.csv';
        a.click();
        window.URL.revokeObjectURL(url);
        
        Swal.fire({
            icon: 'success',
            title: 'Exportado!',
            text: 'Arquivo CSV gerado com sucesso (UTF-8, delimitador ;)',
            timer: 2000,
            showConfirmButton: false
        });
    }
}
</script>
SCRIPTS;

include __DIR__ . '/layouts/base.php';
?>
