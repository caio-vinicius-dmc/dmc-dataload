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
    <div class="header-controls">
        <select class="form-select-modern" id="selectRotina" style="min-width: 220px;">
            <option value="">📋 Selecione uma rotina...</option>
        </select>
        <select class="form-select-modern" id="selectConexao">
            <option value="">🔌 Selecione uma conexão...</option>
        </select>
        <button class="btn-modern-primary" id="btnExecutar" disabled>
            <i class="bi bi-play-fill"></i>
            Executar (F5)
        </button>
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
                <div id="blocosContainer" style="display: none; border-bottom: 2px solid #e2e8f0;">
                    <div style="padding: 1rem 1.25rem; background: white; border-bottom: 1px solid #e2e8f0;">
                        <h6 style="margin: 0; font-size: 0.9rem; color: #667eea; display: flex; align-items: center; gap: 0.5rem;">
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
                <button class="btn-toolbar" onclick="salvarQuery()" title="Salvar Query">
                    <i class="bi bi-save"></i>
                </button>
                <div class="toolbar-divider"></div>
                <button class="btn-toolbar" onclick="toggleSidebar()" title="Toggle Sidebar">
                    <i class="bi bi-layout-sidebar-inset"></i>
                </button>
                <button class="btn-toolbar" onclick="toggleFullscreen()" title="Modo Tela Cheia (F11)" id="btnFullscreen">
                    <i class="bi bi-arrows-fullscreen"></i>
                </button>
                <button class="btn-toolbar" onclick="toggleLayoutOrientation()" title="Alternar Layout (Horizontal/Vertical)" id="btnLayout">
                    <i class="bi bi-layout-split"></i>
                </button>
            </div>
            <div class="toolbar-right">
                <span class="toolbar-info" id="connectionInfo">
                    <i class="bi bi-circle-fill text-secondary"></i>
                    Desconectado
                </span>
            </div>
        </div>

        <!-- Tabs -->
        <div class="sql-tabs" id="sqlTabs">
            <div class="tab-item active" data-tab="tab1">
                <span class="tab-title">Query 1</span>
                <button class="tab-close" onclick="fecharAba('tab1')">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>

        <!-- Editor Content -->
        <div class="sql-editor-area">
            <div class="tab-content active" data-tab="tab1">
                <textarea id="sqlEditor_tab1" class="sql-textarea">-- Bem-vindo ao SQL Editor!
-- Escreva suas queries SQL aqui
-- Pressione F5 ou Ctrl+Enter para executar

SELECT 
    *
FROM 
    sua_tabela
LIMIT 10;</textarea>
            </div>
        </div>

        </div>
        </div>
        
        <!-- Results Panel -->
        <div class="sql-results" id="sqlResults">
            <!-- Resize Handle -->
            <div class="resize-handle-horizontal" id="resultsResizeHandle"></div>
            
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
                    <button class="btn-toolbar" onclick="toggleResultsPanel()" title="Ocultar/Mostrar Painel de Resultados" id="btnToggleResults">
                        <i class="bi bi-chevron-down"></i>
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

<?php
$content = ob_get_clean();

$extraStyles = <<<'STYLES'
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/theme/monokai.min.css" />
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
    --results-height: 320px;
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
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    border-right: 1px solid #e2e8f0;
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
    height: 5px;
    cursor: ns-resize;
    background: transparent;
    z-index: 10;
    transition: background 0.2s ease;
}

.resize-handle-horizontal:hover,
.resize-handle-horizontal.active {
    background: #667eea;
}

.sidebar-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: white;
}

.sidebar-header h6 {
    font-weight: 700;
    color: #1e293b;
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
    border-bottom: 1px solid #e2e8f0;
    background: white;
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
    background: #f1f5f9;
}

.sidebar-content::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.sidebar-content::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
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
    color: #475569;
    margin-bottom: 2px;
}

.tree-item:hover {
    background: white;
    color: #1e293b;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.tree-item.active {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    font-weight: 600;
}

.tree-item i {
    width: 20px;
    text-align: center;
    font-size: 1rem;
}

.tree-children {
    margin-left: 1.5rem;
    border-left: 2px solid #e2e8f0;
    padding-left: 0.75rem;
    margin-top: 4px;
    margin-bottom: 8px;
}

#blocosList::-webkit-scrollbar {
    width: 4px;
}

#blocosList::-webkit-scrollbar-track {
    background: #f1f5f9;
}

#blocosList::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

#blocosList::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Main Editor */
.sql-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #fafafa;
    position: relative;
}

/* Content area with sidebar and editor */
.sql-content {
    display: flex;
    flex: 1;
    overflow: hidden;
}

.sql-content .sql-sidebar {
    flex-shrink: 0;
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
}

.tab-content.active {
    display: block;
}

.sql-textarea {
    display: none;
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
    max-height: 80vh;
    width: 100%;
    flex-shrink: 0;
    transition: height 0.3s ease;
}

.sql-results.collapsed {
    height: 44px !important;
    min-height: 44px;
    overflow: hidden;
}

.sql-results.collapsed .results-content {
    display: none;
}

.sql-results.collapsed .resize-handle-horizontal {
    display: none;
}

/* Layout Horizontal (side by side) - Editor e Results lado a lado */
.sql-editor-container.layout-horizontal .sql-main {
    flex-direction: row;
}

.sql-editor-container.layout-horizontal .sql-content {
    flex: 1;
    flex-direction: column;
}

.sql-editor-container.layout-horizontal .sql-editor-wrapper {
    flex: 1;
    min-width: 400px;
}

.sql-editor-container.layout-horizontal .sql-results {
    flex: 1;
    min-width: 300px;
    border-top: none;
    border-left: 1px solid #e2e8f0;
    height: auto;
    max-height: none;
}

.sql-editor-container.layout-horizontal .resize-handle-horizontal {
    display: none;
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
}

.result-panel.active {
    display: block;
}

.table-responsive {
    padding: 0;
    background: white;
    overflow-x: auto;
    border: 1px solid #e2e8f0;
    border-radius: var(--radius-sm);
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
    margin: 1rem;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
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
    color: #94a3b8;
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let tabCounter = 1;
let activeTab = 'tab1';
let editors = {};
let currentConnection = null;
let currentRotina = null;
let sidebarCollapsed = false;
let resultsCollapsed = false;
let isFullscreen = false;

// Resize handling variables
let isResizingSidebar = false;
let isResizingResults = false;
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
    
    // Results panel resize (vertical drag)
    resultsHandle.addEventListener('mousedown', function(e) {
        isResizingResults = true;
        startY = e.clientY;
        startHeight = results.offsetHeight;
        resultsHandle.classList.add('active');
        document.body.style.cursor = 'ns-resize';
        document.body.style.userSelect = 'none';
        e.preventDefault();
    });
    
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
            }
        }
    });
    
    // Mouse up handler
    document.addEventListener('mouseup', function() {
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
    });
}

// Toggle layout orientation
function toggleLayoutOrientation() {
    const container = document.querySelector('.sql-editor-container');
    const btnLayout = document.getElementById('btnLayout');
    
    if (layoutOrientation === 'vertical') {
        // Change to horizontal split
        container.classList.add('layout-horizontal');
        layoutOrientation = 'horizontal';
        if (btnLayout) {
            btnLayout.setAttribute('title', 'Layout Vertical');
        }
        
        Swal.fire({
            icon: 'info',
            title: 'Layout Horizontal',
            text: 'Editor e resultados lado a lado',
            timer: 1500,
            showConfirmButton: false
        });
    } else {
        // Change to vertical split
        container.classList.remove('layout-horizontal');
        layoutOrientation = 'vertical';
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
        Object.keys(editors).forEach(tabId => {
            if (editors[tabId]) {
                editors[tabId].refresh();
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
    if (prefsStr) {
        try {
            const prefs = JSON.parse(prefsStr);
            const sidebar = document.getElementById('sqlSidebar');
            const results = document.getElementById('sqlResults');
            
            if (sidebar && prefs.sidebarWidth) {
                sidebar.style.width = prefs.sidebarWidth + 'px';
            }
            
            if (results && prefs.resultsHeight) {
                results.style.height = prefs.resultsHeight + 'px';
            }
            
            if (prefs.layoutOrientation === 'horizontal') {
                const container = document.querySelector('.sql-editor-container');
                if (container) {
                    container.classList.add('layout-horizontal');
                    layoutOrientation = 'horizontal';
                }
            }
        } catch (e) {
            console.error('Error loading layout preferences:', e);
        }
    }
}

// Inicializar editor para tab1
$(document).ready(function() {
    initEditor('tab1');
    carregarConexoes();
    carregarRotinas();
    
    // Initialize resize functionality
    initResizeHandles();
    
    // Load saved layout preferences
    loadLayoutPreferences();
    
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
        extraKeys: {
            'F5': function() { executarQuery(); },
            'Ctrl-Enter': function() { executarQuery(); }
        }
    });
    
    editors[tabId] = editor;
}

function carregarConexoes() {
    $.getJSON(baseUrl + '/conexoes/list')
        .done(function(res) {
            const sel = $('#selectConexao');
            const selFullscreen = $('#selectConexaoFullscreen');
            sel.find('option:not(:first)').remove();
            selFullscreen.find('option:not(:first)').remove();
            
            if (res.data && res.data.length > 0) {
                res.data.forEach(function(c) {
                    const optText = `${c.nome_conexao} (${c.tipo_banco})`;
                    sel.append($('<option>').val(c.id).text(optText));
                    selFullscreen.append($('<option>').val(c.id).text(optText));
                });
            }
        })
        .fail(function() {
            Swal.fire('Erro', 'Não foi possível carregar as conexões', 'error');
        });
}

function carregarRotinas() {
    $.getJSON(baseUrl + '/rotinas/list')
        .done(function(res) {
            const sel = $('#selectRotina');
            const selFullscreen = $('#selectRotinaFullscreen');
            sel.find('option:not(:first)').remove();
            selFullscreen.find('option:not(:first)').remove();
            
            if (res.data && res.data.length > 0) {
                res.data.forEach(function(r) {
                    sel.append($('<option>').val(r.id).text(`${r.nome}`));
                    selFullscreen.append($('<option>').val(r.id).text(`${r.nome}`));
                });
            }
        })
        .fail(function() {
            console.error('Erro ao carregar rotinas');
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
                                <i class="bi ${icon}" style="color: #667eea;"></i>
                                <span style="flex: 1;">${title}</span>
                                <small style="color: #94a3b8; font-size: 0.75rem;">${operacao}</small>
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
    console.log('Abrindo bloco:', blocoIndex);
    console.log('Rotina atual:', currentRotina);
    
    if (!currentRotina || !currentRotina.blocos || !currentRotina.blocos[blocoIndex]) {
        console.error('Bloco não encontrado');
        Swal.fire('Erro', 'Bloco não encontrado', 'error');
        return;
    }
    
    const bloco = currentRotina.blocos[blocoIndex];
    console.log('Dados do bloco:', bloco);
    
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
    
    console.log('Criando aba:', tabId, 'com título:', tabTitle);
    
    // Adicionar tab
    const tabHtml = `
        <div class="tab-item" data-tab="${tabId}">
            <span class="tab-title">${tabTitle}</span>
            <button class="tab-close" onclick="fecharAba('${tabId}'); event.stopPropagation();">
                <i class="bi bi-x"></i>
            </button>
        </div>
    `;
    $('#sqlTabs').append(tabHtml);
    
    // Adicionar conteúdo
    const contentHtml = `
        <div class="tab-content" data-tab="${tabId}">
            <textarea id="sqlEditor_${tabId}" class="sql-textarea">${sql}</textarea>
        </div>
    `;
    $('.sql-editor-area').append(contentHtml);
    
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

$('#selectConexao, #selectConexaoFullscreen').on('change', function() {
    const conexaoId = $(this).val();
    
    // Sincronizar ambos os selects
    $('#selectConexao').val(conexaoId);
    $('#selectConexaoFullscreen').val(conexaoId);
    
    if (conexaoId) {
        conectarBanco(conexaoId);
    } else {
        desconectarBanco();
    }
});

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
}

function carregarObjetosBanco(conexaoId) {
    $('#databaseTree').html('<div class="text-center py-3"><div class="spinner-border text-primary"></div></div>');
    
    $.getJSON(baseUrl + '/sql-editor/objects/' + conexaoId)
        .done(function(res) {
            if (res.sucesso) {
                renderizarArvore(res.objetos);
            } else {
                $('#databaseTree').html(`<div class="text-center text-danger py-3">Erro ao carregar objetos</div>`);
            }
        })
        .fail(function() {
            $('#databaseTree').html(`<div class="text-center text-danger py-3">Erro ao carregar objetos</div>`);
        });
}

function renderizarArvore(objetos) {
    let html = '';
    
    // Schemas
    if (objetos.schemas && objetos.schemas.length > 0) {
        html += '<div class="tree-item" onclick="toggleTreeNode(this)"><i class="bi bi-folder"></i> Schemas</div>';
        html += '<div class="tree-children" style="display:none;">';
        objetos.schemas.forEach(schema => {
            html += `<div class="tree-item"><i class="bi bi-folder-fill text-warning"></i> ${schema}</div>`;
        });
        html += '</div>';
    }
    
    // Tables
    if (objetos.tabelas && objetos.tabelas.length > 0) {
        html += '<div class="tree-item" onclick="toggleTreeNode(this)"><i class="bi bi-table"></i> Tables (' + objetos.tabelas.length + ')</div>';
        html += '<div class="tree-children" style="display:none;">';
        objetos.tabelas.forEach(table => {
            html += `<div class="tree-item" onclick="inserirNomeTabela('${table.name}')"><i class="bi bi-table text-primary"></i> ${table.name}</div>`;
        });
        html += '</div>';
    }
    
    // Views
    if (objetos.views && objetos.views.length > 0) {
        html += '<div class="tree-item" onclick="toggleTreeNode(this)"><i class="bi bi-eye"></i> Views (' + objetos.views.length + ')</div>';
        html += '<div class="tree-children" style="display:none;">';
        objetos.views.forEach(view => {
            html += `<div class="tree-item"><i class="bi bi-eye text-info"></i> ${view}</div>`;
        });
        html += '</div>';
    }
    
    // Functions
    if (objetos.funcoes && objetos.funcoes.length > 0) {
        html += '<div class="tree-item" onclick="toggleTreeNode(this)"><i class="bi bi-braces"></i> Functions (' + objetos.funcoes.length + ')</div>';
        html += '<div class="tree-children" style="display:none;">';
        objetos.funcoes.forEach(func => {
            html += `<div class="tree-item"><i class="bi bi-braces text-success"></i> ${func}</div>`;
        });
        html += '</div>';
    }
    
    $('#databaseTree').html(html);
}

function toggleTreeNode(element) {
    $(element).next('.tree-children').slideToggle(200);
}

function inserirNomeTabela(tableName) {
    const editor = editors[activeTab];
    if (editor) {
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
            <button class="tab-close" onclick="fecharAba('${tabId}')">
                <i class="bi bi-x"></i>
            </button>
        </div>
    `;
    $('#sqlTabs').append(tabHtml);
    
    // Adicionar conteúdo
    const contentHtml = `
        <div class="tab-content" data-tab="${tabId}">
            <textarea id="sqlEditor_${tabId}" class="sql-textarea">-- Nova query
-- Escreva seu SQL aqui

SELECT * FROM tabela LIMIT 10;</textarea>
        </div>
    `;
    $('.sql-editor-area').append(contentHtml);
    
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
    
    if (editors[tabId]) {
        editors[tabId].refresh();
        editors[tabId].focus();
    }
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
    
    // Remover tab e conteúdo
    $(`.tab-item[data-tab="${tabId}"]`).remove();
    $(`.tab-content[data-tab="${tabId}"]`).remove();
    delete editors[tabId];
    
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

function executarQuery() {
    if (!currentConnection) {
        Swal.fire('Atenção', 'Selecione uma conexão primeiro', 'warning');
        return;
    }
    
    const editor = editors[activeTab];
    if (!editor) return;
    
    let sql = editor.getSelection() || editor.getValue();
    sql = sql.trim();
    
    if (!sql) {
        Swal.fire('Atenção', 'Digite uma query SQL', 'warning');
        return;
    }
    
    // Mostrar loading
    $('#noResults').html(`
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-3">Executando query...</p>
    `);
    $('#resultTable').hide();
    
    const startTime = Date.now();
    
    $.post(baseUrl + '/sql-editor/execute', {
        conexao_id: currentConnection.id,
        sql: sql
    }, function(res) {
        const duration = Date.now() - startTime;
        
        if (res.sucesso) {
            exibirResultados(res, duration);
        } else {
            exibirErro(res.erro || res.mensagem, duration);
        }
    }, 'json').fail(function(xhr) {
        const duration = Date.now() - startTime;
        exibirErro('Erro na comunicação com o servidor: ' + xhr.responseText, duration);
    });
}

function exibirResultados(res, duration) {
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

function exibirErro(erro, duration) {
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
    const editor = editors[activeTab];
    if (!editor) return;
    
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
    const editor = editors[activeTab];
    if (!editor) return;
    
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
    const editor = editors[activeTab];
    if (!editor) return;
    
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
    $('#sqlSidebar').toggleClass('collapsed');
}

function toggleResultsPanel() {
    resultsCollapsed = !resultsCollapsed;
    const resultsPanel = document.getElementById('sqlResults');
    const btnIcon = document.querySelector('#btnToggleResults i');
    
    if (resultsCollapsed) {
        resultsPanel.classList.add('collapsed');
        btnIcon.classList.remove('bi-chevron-down');
        btnIcon.classList.add('bi-chevron-up');
        document.querySelector('#btnToggleResults').setAttribute('title', 'Mostrar Painel de Resultados');
    } else {
        resultsPanel.classList.remove('collapsed');
        btnIcon.classList.remove('bi-chevron-up');
        btnIcon.classList.add('bi-chevron-down');
        document.querySelector('#btnToggleResults').setAttribute('title', 'Ocultar Painel de Resultados');
    }
    
    // Refresh dos editores após animação
    setTimeout(() => {
        Object.keys(editors).forEach(tabId => {
            if (editors[tabId]) {
                editors[tabId].refresh();
            }
        });
    }, 300);
}

function toggleFullscreen() {
    const container = document.getElementById('sqlEditorContainer');
    
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
        const connName = currentConnection ? currentConnection.nome : 'Desconectado';
        $('#fullscreenTitle').text(`SQL Editor - ${connName}`);
        
        // Refresh dos editores
        setTimeout(() => {
            Object.keys(editors).forEach(tabId => {
                if (editors[tabId]) {
                    editors[tabId].refresh();
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
        
        // Refresh dos editores
        setTimeout(() => {
            Object.keys(editors).forEach(tabId => {
                if (editors[tabId]) {
                    editors[tabId].refresh();
                }
            });
        }, 100);
    }
}

function handleFullscreenChange() {
    const isCurrentlyFullscreen = !!(document.fullscreenElement || 
                                      document.webkitFullscreenElement || 
                                      document.mozFullScreenElement || 
                                      document.msFullscreenElement);
    
    if (!isCurrentlyFullscreen && isFullscreen) {
        // Usuário saiu do fullscreen usando ESC
        const container = document.getElementById('sqlEditorContainer');
        container.classList.remove('fullscreen');
        isFullscreen = false;
        
        $('#btnFullscreen i').removeClass('bi-fullscreen-exit').addClass('bi-arrows-fullscreen');
        $('#btnFullscreen').attr('title', 'Modo Tela Cheia (F11)');
        
        setTimeout(() => {
            Object.keys(editors).forEach(tabId => {
                if (editors[tabId]) {
                    editors[tabId].refresh();
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
            csv += $(this).text() + ',';
        });
        csv = csv.slice(0, -1) + '\n';
        
        // Data
        table.find('tbody tr').each(function() {
            $(this).find('td').each(function() {
                csv += '"' + $(this).text().replace(/"/g, '""') + '",';
            });
            csv = csv.slice(0, -1) + '\n';
        });
        
        // Download
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'resultados_' + Date.now() + '.csv';
        a.click();
    }
}
</script>
SCRIPTS;

include __DIR__ . '/layouts/base.php';
?>
