<?php
/**
 * DMC DataLoad - Diagrama de Banco de Dados
 * Visualização de Diagramas ER (Entity-Relationship)
 */
$pageTitle = 'Diagrama ER';
$currentPage = 'diagrama';

ob_start();
?>

<!-- Page Header -->
<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-diagram-3-fill"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Diagrama de Banco de Dados</h1>
        <p class="page-subtitle-modern">Visualize a estrutura e relacionamentos do seu banco de dados</p>
    </div>
</div>

<style>
/* ============ PAGE HEADER MODERN ============ */
.page-header-modern {
    background: white;
    padding: 1.75rem 2rem;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.page-icon-modern {
    width: 70px;
    height: 70px;
    border-radius: 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-subtitle-modern {
    color: #64748b;
    margin: 0;
    font-size: 1rem;
}

/* ============ DIAGRAMA STYLES ============ */
:root {
    --diagram-bg: #1e1e1e;
    --diagram-grid: #2d2d2d;
    --table-bg: #252526;
    --table-border: #3c3c3c;
    --table-header: #0d47a1;
    --pk-color: #ffd700;
    --fk-color: #4fc3f7;
    --text-color: #e0e0e0;
    --text-muted: #9e9e9e;
    --relation-color: #4fc3f7;
    --highlight-color: #2196f3;
}

.diagrama-container {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 180px);
    background: var(--diagram-bg);
    overflow: hidden;
    border-radius: 8px;
    margin-top: 20px;
}

.diagrama-toolbar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
    background: #252526;
    border-bottom: 1px solid var(--table-border);
    flex-wrap: wrap;
}

.diagrama-toolbar .toolbar-group {
    display: flex;
    align-items: center;
    gap: 8px;
    padding-right: 15px;
    border-right: 1px solid var(--table-border);
}

.diagrama-toolbar .toolbar-group:last-child {
    border-right: none;
}

.diagrama-toolbar select,
.diagrama-toolbar input {
    background: #1e1e1e;
    border: 1px solid var(--table-border);
    color: var(--text-color);
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 13px;
}

.diagrama-toolbar select:focus,
.diagrama-toolbar input:focus {
    outline: none;
    border-color: var(--highlight-color);
}

.diagrama-toolbar label {
    color: var(--text-muted);
    font-size: 12px;
    margin-right: 5px;
}

.diagrama-toolbar .btn {
    padding: 6px 12px;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.diagrama-toolbar .btn i {
    font-size: 14px;
}

.diagrama-main {
    display: flex;
    flex: 1;
    overflow: hidden;
}

/* ============ OUTLINE / MINIMAP ============ */
.diagrama-outline {
    width: 200px;
    background: #1a1a1a;
    border-right: 1px solid var(--table-border);
    display: flex;
    flex-direction: column;
    transition: width 0.3s ease;
}

.diagrama-outline.collapsed {
    width: 40px;
}

.diagrama-outline .outline-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px;
    background: #252526;
    border-bottom: 1px solid var(--table-border);
}

.diagrama-outline .outline-header h6 {
    margin: 0;
    font-size: 12px;
    color: var(--text-muted);
    text-transform: uppercase;
}

.diagrama-outline.collapsed .outline-header h6 {
    display: none;
}

.diagrama-outline .outline-toggle {
    background: transparent;
    border: none;
    color: var(--text-muted);
    cursor: pointer;
    padding: 4px;
    transition: color 0.2s;
}

.diagrama-outline .outline-toggle:hover {
    color: var(--text-color);
}

.diagrama-outline .minimap-container {
    flex: 1;
    position: relative;
    overflow: hidden;
}

.diagrama-outline.collapsed .minimap-container {
    display: none;
}

#minimap {
    width: 100%;
    height: 150px;
    background: var(--diagram-bg);
    border-bottom: 1px solid var(--table-border);
    cursor: crosshair;
}

.minimap-viewport {
    position: absolute;
    border: 2px solid var(--highlight-color);
    background: rgba(33, 150, 243, 0.1);
    cursor: move;
    min-width: 10px;
    min-height: 10px;
}

.outline-table-list {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
}

.outline-table-item {
    padding: 6px 10px;
    margin-bottom: 4px;
    background: var(--table-bg);
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    color: var(--text-color);
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background 0.2s;
}

.outline-table-item:hover {
    background: #333;
}

.outline-table-item.active {
    background: var(--highlight-color);
    color: white;
}

.outline-table-item.main-table {
    background: linear-gradient(135deg, #ffd700 0%, #ffb300 100%);
    color: #000;
    font-weight: 600;
}

.outline-table-item.main-table:hover {
    background: linear-gradient(135deg, #ffb300 0%, #ff8f00 100%);
}

.outline-table-item i {
    color: var(--fk-color);
    font-size: 12px;
}

/* ============ CANVAS AREA ============ */
.diagrama-canvas-area {
    flex: 1;
    position: relative;
    overflow: hidden;
    background: 
        linear-gradient(var(--diagram-grid) 1px, transparent 1px),
        linear-gradient(90deg, var(--diagram-grid) 1px, transparent 1px);
    background-size: 20px 20px;
    background-position: -1px -1px;
}

#diagrama-canvas {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    cursor: grab;
    z-index: 10;
}

#diagrama-canvas.dragging {
    cursor: grabbing;
}

#relationsSvg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 5;
    overflow: visible;
}

/* ============ TABLE NODES ============ */
.table-node {
    position: absolute;
    min-width: 200px;
    max-width: 350px;
    background: var(--table-bg);
    border: 1px solid var(--table-border);
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    z-index: 10;
    cursor: move;
    user-select: none;
    transition: box-shadow 0.2s, border-color 0.2s;
}

.table-node:hover {
    border-color: var(--highlight-color);
    box-shadow: 0 6px 16px rgba(33, 150, 243, 0.2);
}

.table-node.selected {
    border-color: var(--highlight-color);
    box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.3);
}

.table-node.dragging {
    opacity: 0.9;
    z-index: 1000;
}

.table-node.highlighted {
    border-color: #4caf50;
    box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.3);
}

.table-node.main-table {
    border-color: #ffd700;
    box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.4);
    background: linear-gradient(135deg, #2a2a2b 0%, #1a1a1b 100%);
}

.table-node.main-table .table-header {
    background: linear-gradient(135deg, #ffd700 0%, #ffb300 100%);
    color: #000;
    font-weight: 700;
}

.table-node.main-table .table-header .table-name {
    color: #000;
}

.table-node.main-table .table-header .table-actions button {
    background: rgba(0,0,0,0.1);
    color: #000;
}

.table-node.main-table .table-header .table-actions button:hover {
    background: rgba(0,0,0,0.2);
}

.table-header {
    background: var(--table-header);
    padding: 10px 12px;
    border-radius: 5px 5px 0 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.table-header .table-name {
    font-weight: 600;
    color: white;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.table-header .table-name i {
    opacity: 0.8;
}

.table-header .table-actions {
    display: flex;
    gap: 5px;
    opacity: 0;
    transition: opacity 0.2s;
}

.table-node:hover .table-header .table-actions {
    opacity: 1;
}

.table-header .table-actions button {
    background: rgba(255,255,255,0.1);
    border: none;
    color: white;
    padding: 3px 6px;
    border-radius: 3px;
    cursor: pointer;
    font-size: 11px;
}

.table-header .table-actions button:hover {
    background: rgba(255,255,255,0.2);
}

.table-columns {
    max-height: 300px;
    overflow-y: auto;
}

.table-column {
    display: flex;
    align-items: center;
    padding: 6px 12px;
    border-bottom: 1px solid #333;
    font-size: 12px;
    gap: 8px;
}

.table-column:last-child {
    border-bottom: none;
}

.table-column .col-icon {
    width: 16px;
    text-align: center;
}

.table-column .col-icon i {
    font-size: 11px;
}

.table-column .col-icon.pk i {
    color: var(--pk-color);
}

.table-column .col-icon.fk i {
    color: var(--fk-color);
}

.table-column .col-name {
    flex: 1;
    color: var(--text-color);
    font-family: 'Consolas', monospace;
}

.table-column .col-type {
    color: var(--text-muted);
    font-size: 11px;
    font-family: 'Consolas', monospace;
}

.table-column .col-null {
    color: #ff9800;
    font-size: 10px;
    opacity: 0.6;
}

.table-column.relation-highlight {
    background: rgba(255, 152, 0, 0.15);
    border-left: 3px solid #ff9800;
    animation: pulseRelation 1.5s ease-in-out infinite alternate;
}

@keyframes pulseRelation {
    0% { background: rgba(255, 152, 0, 0.15); }
    100% { background: rgba(255, 152, 0, 0.25); }
}

/* ============ ZOOM CONTROLS ============ */
.zoom-controls {
    position: absolute;
    bottom: 20px;
    right: 20px;
    display: flex;
    flex-direction: column;
    gap: 5px;
    z-index: 100;
}

.zoom-controls button {
    width: 36px;
    height: 36px;
    background: var(--table-bg);
    border: 1px solid var(--table-border);
    color: var(--text-color);
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s, border-color 0.2s;
}

.zoom-controls button:hover {
    background: #333;
    border-color: var(--highlight-color);
}

.zoom-indicator {
    text-align: center;
    font-size: 11px;
    color: var(--text-muted);
    padding: 4px;
    background: var(--table-bg);
    border: 1px solid var(--table-border);
    border-radius: 4px;
}

/* ============ LOADING / EMPTY STATES ============ */
.diagrama-loading,
.diagrama-empty {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: var(--text-muted);
}

.diagrama-loading i {
    font-size: 40px;
    color: var(--highlight-color);
    animation: spin 1s linear infinite;
}

.diagrama-empty i {
    font-size: 60px;
    opacity: 0.3;
    margin-bottom: 15px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* ============ RELATION LINES SVG ============ */
.relation-line {
    fill: none;
    stroke: var(--relation-color);
    stroke-width: 2;
    marker-end: url(#arrowhead);
    cursor: pointer;
    pointer-events: stroke;
    stroke-linecap: round;
}

.relation-line:hover {
    stroke: #64b5f6;
    stroke-width: 3;
    filter: drop-shadow(0 0 5px rgba(100, 181, 246, 0.6));
}

.relation-line.highlighted {
    stroke: #4caf50;
    stroke-width: 3;
}

.relation-line.selected {
    stroke: #ff9800;
    stroke-width: 4;
    filter: drop-shadow(0 0 8px rgba(255, 152, 0, 0.8));
}

/* ============ CONTEXT MENU ============ */
.diagram-context-menu {
    position: fixed;
    background: var(--table-bg);
    border: 1px solid var(--table-border);
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.4);
    z-index: 10000;
    min-width: 180px;
    display: none;
}

.diagram-context-menu.show {
    display: block;
}

.diagram-context-menu .menu-item {
    padding: 10px 15px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text-color);
    font-size: 13px;
    transition: background 0.2s;
}

.diagram-context-menu .menu-item:hover {
    background: #333;
}

.diagram-context-menu .menu-item:first-child {
    border-radius: 5px 5px 0 0;
}

.diagram-context-menu .menu-item:last-child {
    border-radius: 0 0 5px 5px;
}

.diagram-context-menu .menu-divider {
    height: 1px;
    background: var(--table-border);
    margin: 5px 0;
}

.diagram-context-menu .menu-item i {
    width: 18px;
    color: var(--text-muted);
}

/* ============ EXPORT MODAL ============ */
.export-preview {
    background: var(--diagram-bg);
    border: 1px solid var(--table-border);
    border-radius: 6px;
    overflow: hidden;
    margin-top: 15px;
}

.export-preview img {
    max-width: 100%;
    max-height: 300px;
    display: block;
    margin: 0 auto;
}

/* ============ SCROLLBAR ============ */
.table-columns::-webkit-scrollbar,
.outline-table-list::-webkit-scrollbar {
    width: 6px;
}

.table-columns::-webkit-scrollbar-track,
.outline-table-list::-webkit-scrollbar-track {
    background: transparent;
}

.table-columns::-webkit-scrollbar-thumb,
.outline-table-list::-webkit-scrollbar-thumb {
    background: #555;
    border-radius: 3px;
}

/* ============ INFO TOOLTIP ============ */
.column-tooltip {
    position: fixed;
    background: #1a1a1a;
    border: 1px solid var(--table-border);
    border-radius: 6px;
    padding: 10px;
    z-index: 10001;
    max-width: 300px;
    display: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.4);
}

.column-tooltip.show {
    display: block;
}

.column-tooltip h6 {
    margin: 0 0 8px 0;
    color: var(--text-color);
    font-size: 13px;
}

.column-tooltip p {
    margin: 4px 0;
    color: var(--text-muted);
    font-size: 12px;
}
</style>

<div class="diagrama-container">
    <!-- Toolbar -->
    <div class="diagrama-toolbar">
        <div class="toolbar-group">
            <label for="selectConexaoDiagrama"><i class="bi bi-database"></i></label>
            <select id="selectConexaoDiagrama" style="width: 200px;">
                <option value="">Selecione uma conexão...</option>
                <option value="no-connections" disabled style="color: #999;">Nenhuma conexão disponível</option>
            </select>
        </div>
        
        <div class="toolbar-group">
            <label for="selectModoDiagrama"><i class="bi bi-diagram-3"></i></label>
            <select id="selectModoDiagrama">
                <option value="completo">Banco Completo</option>
                <option value="tabela">Tabela Específica</option>
            </select>
            
            <select id="selectTabelaDiagrama" style="width: 200px; display: none;">
                <option value="">Selecione uma tabela...</option>
            </select>
        </div>
        
        <div class="toolbar-group">
            <button class="btn btn-primary btn-sm" id="btnCarregarDiagrama">
                <i class="bi bi-arrow-clockwise"></i> Carregar
            </button>
            <button class="btn btn-outline-secondary btn-sm" id="btnOrganizar" title="Auto-organizar">
                <i class="bi bi-grid-3x3-gap"></i>
            </button>
            <button class="btn btn-outline-secondary btn-sm" id="btnSalvarPosicoes" title="Salvar posições">
                <i class="bi bi-save"></i>
            </button>
        </div>
        
        <div class="toolbar-group">
            <button class="btn btn-outline-info btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-download"></i> Exportar
            </button>
            <ul class="dropdown-menu dropdown-menu-dark">
                <li><a class="dropdown-item" href="#" data-format="png"><i class="bi bi-file-image"></i> PNG</a></li>
                <li><a class="dropdown-item" href="#" data-format="jpg"><i class="bi bi-file-image"></i> JPG</a></li>
                <li><a class="dropdown-item" href="#" data-format="pdf"><i class="bi bi-file-pdf"></i> PDF</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#" data-format="svg"><i class="bi bi-filetype-svg"></i> SVG</a></li>
            </ul>
        </div>
        
        <div class="toolbar-group">
            <button class="btn btn-outline-light btn-sm" id="btnFitToScreen" title="Ajustar à tela">
                <i class="bi bi-fullscreen"></i>
            </button>
            <button class="btn btn-outline-light btn-sm" id="btnResetZoom" title="Zoom 100%">
                <i class="bi bi-aspect-ratio"></i> 100%
            </button>
        </div>
    </div>
    
    <!-- Main Area -->
    <div class="diagrama-main">
        <!-- Outline / Minimap -->
        <div class="diagrama-outline" id="diagramaOutline">
            <div class="outline-header">
                <h6>Estrutura</h6>
                <button class="outline-toggle" id="toggleOutline" title="Recolher">
                    <i class="bi bi-chevron-left"></i>
                </button>
            </div>
            <div class="minimap-container">
                <canvas id="minimap"></canvas>
                <div class="minimap-viewport" id="minimapViewport"></div>
            </div>
            <div class="outline-table-list" id="outlineTableList">
                <!-- Tables will be listed here -->
            </div>
        </div>
        
        <!-- Canvas -->
        <div class="diagrama-canvas-area" id="diagramaCanvasArea">
            <!-- SVG for relations - positioned to cover entire canvas area -->
            <svg id="relationsSvg">
                <defs>
                    <marker id="arrowhead" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto">
                        <polygon points="0 0, 10 3.5, 0 7" fill="var(--relation-color)" />
                    </marker>
                    <marker id="arrowhead-highlight" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto">
                        <polygon points="0 0, 10 3.5, 0 7" fill="#4caf50" />
                    </marker>
                </defs>
            </svg>
            
            <div id="diagrama-canvas">
                <!-- Table nodes will be rendered here -->
            </div>
            
            <!-- Empty State -->
            <div class="diagrama-empty" id="diagramaEmpty">
                <i class="bi bi-diagram-3"></i>
                <h5>Nenhum diagrama carregado</h5>
                <p>Selecione uma conexão e clique em "Carregar" para visualizar</p>
            </div>
            
            <!-- Loading State -->
            <div class="diagrama-loading" id="diagramaLoading" style="display: none;">
                <i class="bi bi-arrow-repeat"></i>
                <p>Carregando estrutura...</p>
            </div>
            
            <!-- Zoom Controls -->
            <div class="zoom-controls">
                <button id="btnZoomIn" title="Zoom In"><i class="bi bi-plus-lg"></i></button>
                <div class="zoom-indicator" id="zoomIndicator">100%</div>
                <button id="btnZoomOut" title="Zoom Out"><i class="bi bi-dash-lg"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- Context Menu -->
<div class="diagram-context-menu" id="contextMenu">
    <div class="menu-item" data-action="focus"><i class="bi bi-bullseye"></i> Focar nesta tabela</div>
    <div class="menu-item" data-action="highlight"><i class="bi bi-highlighter"></i> Destacar relações</div>
    <div class="menu-divider"></div>
    <div class="menu-item" data-action="hide"><i class="bi bi-eye-slash"></i> Ocultar tabela</div>
    <div class="menu-item" data-action="show-all"><i class="bi bi-eye"></i> Mostrar todas</div>
</div>

<!-- Column Tooltip -->
<div class="column-tooltip" id="columnTooltip"></div>

<?php
$content = ob_get_clean();
$base = defined('BASE_URL') ? BASE_URL : '';
$extraScripts = <<<'SCRIPTS'
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
window.diagramaConfig = {
    baseUrl: 'REPLACE_BASE_URL'
};

$(document).ready(function() {
    const BASE_URL = window.diagramaConfig.baseUrl;
    
    // ============ STATE ============
    let estrutura = null;
    let posicoesSalvas = {};
    let zoom = 1;
    let pan = { x: 0, y: 0 };
    let isDragging = false;
    let dragStart = { x: 0, y: 0 };
    let selectedNode = null;
    let nodeDragData = null;
    let hiddenTables = new Set();
    let relationUpdateTimeout = null;
    
    // ============ ELEMENTS ============
    const $canvas = $('#diagrama-canvas');
    const $canvasArea = $('#diagramaCanvasArea');
    const $relationsSvg = $('#relationsSvg');
    const $outline = $('#diagramaOutline');
    const $outlineList = $('#outlineTableList');
    const $minimap = $('#minimap');
    const $minimapViewport = $('#minimapViewport');
    const $contextMenu = $('#contextMenu');
    const $columnTooltip = $('#columnTooltip');
    
    // ============ INIT ============
    verificarAutenticacao().then(autenticado => {
        if (autenticado) {
            carregarConexoes();
        } else {
            window.location.href = BASE_URL + '/login';
        }
    });
    
    // ============ CHECK AUTH ============
    async function verificarAutenticacao() {
        try {
            const res = await $.get(BASE_URL + '/api/sessao');
            return res.autenticado === true;
        } catch (err) {
            console.error('Erro ao verificar autenticação:', err);
            return false;
        }
    }
    
    // ============ LOAD CONNECTIONS ============
    async function carregarConexoes() {
        try {
            console.log('🔌 Carregando conexões para diagrama...');
            
            const res = await $.get(BASE_URL + '/conexoes/list');
            const $select = $('#selectConexaoDiagrama');
            $select.find('option:not(:first)').remove();
            
            console.log('📊 Resposta do endpoint:', res);
            
            if (res && res.data && Array.isArray(res.data)) {
                if (res.data.length > 0) {
                    // Remove placeholder de "nenhuma conexão"
                    $select.find('option[value="no-connections"]').remove();
                    
                    res.data.forEach(c => {
                        $select.append(`<option value="${c.id}">${c.nome_conexao} (${c.tipo_banco})</option>`);
                    });
                    
                    console.log(`✅ ${res.data.length} conexões carregadas no diagrama`);
                } else {
                    console.log('ℹ️ Nenhuma conexão encontrada no banco de dados');
                    $select.append('<option value="" disabled style="color: #ff6b6b;">❌ Nenhuma conexão cadastrada</option>');
                }
            } else {
                console.log('⚠️ Formato de resposta inesperado:', res);
                $select.append('<option value="" disabled style="color: #ff6b6b;">❌ Erro no formato dos dados</option>');
            }
        } catch (err) {
            console.error('❌ Erro ao carregar conexões:', err);
            
            const $select = $('#selectConexaoDiagrama');
            $select.find('option:not(:first)').remove();
            
            // Mostrar mensagem de erro baseada no código de status
            if (err.status === 401 || err.responseText?.includes('<!doctype html>')) {
                console.log('🔐 Erro de autenticação detectado');
                $select.append('<option value="" disabled style="color: #ff6b6b;">❌ Sessão expirada - Faça login novamente</option>');
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Sessão Expirada',
                    text: 'Sua sessão expirou. Você será redirecionado para o login.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = BASE_URL + '/login';
                });
            } else {
                $select.append('<option value="" disabled style="color: #ff6b6b;">❌ Erro ao carregar conexões</option>');
                
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao Carregar Conexões',
                    text: 'Não foi possível carregar a lista de conexões. Verifique se há conexões cadastradas.',
                    footer: '<small>Código do erro: ' + (err.status || 'Desconhecido') + '</small>'
                });
            }
        }
    }
    
    // ============ MODE CHANGE ============
    $('#selectModoDiagrama').on('change', function() {
        const modo = $(this).val();
        if (modo === 'tabela') {
            $('#selectTabelaDiagrama').show();
            carregarListaTabelas();
        } else {
            $('#selectTabelaDiagrama').hide();
        }
    });
    
    // ============ CONNECTION CHANGE ============
    $('#selectConexaoDiagrama').on('change', function() {
        if ($('#selectModoDiagrama').val() === 'tabela') {
            carregarListaTabelas();
        }
    });
    
    // ============ LOAD TABLE LIST ============
    async function carregarListaTabelas() {
        const conexaoId = $('#selectConexaoDiagrama').val();
        if (!conexaoId) return;
        
        try {
            const res = await $.get(BASE_URL + '/diagrama/tabelas/' + conexaoId);
            const $select = $('#selectTabelaDiagrama');
            $select.find('option:not(:first)').remove();
            
            if (res.sucesso && res.tabelas) {
                res.tabelas.forEach(t => {
                    $select.append(`<option value="${t.schema}|${t.nome}">${t.completo}</option>`);
                });
            }
        } catch (err) {
            console.error('Erro ao carregar tabelas:', err);
        }
    }
    
    // ============ LOAD DIAGRAM ============
    $('#btnCarregarDiagrama').on('click', carregarDiagrama);
    
    async function carregarDiagrama() {
        const conexaoId = $('#selectConexaoDiagrama').val();
        if (!conexaoId) {
            Swal.fire('Atenção', 'Selecione uma conexão', 'warning');
            return;
        }
        
        const modo = $('#selectModoDiagrama').val();
        let url = BASE_URL + '/diagrama/estrutura/' + conexaoId;
        
        if (modo === 'tabela') {
            const tabelaSelecionada = $('#selectTabelaDiagrama').val();
            if (!tabelaSelecionada) {
                Swal.fire('Atenção', 'Selecione uma tabela', 'warning');
                return;
            }
            const [schema, tabela] = tabelaSelecionada.split('|');
            url = BASE_URL + '/diagrama/estrutura-tabela/' + conexaoId + '/' + encodeURIComponent(schema) + '/' + encodeURIComponent(tabela);
        }
        
        $('#diagramaEmpty').hide();
        $('#diagramaLoading').show();
        
        try {
            // Carregar posições salvas
            const posRes = await $.get(BASE_URL + '/diagrama/posicoes/' + conexaoId);
            if (posRes.sucesso) {
                posicoesSalvas = posRes.posicoes || {};
            }
            
            // Carregar estrutura
            const res = await $.get(url);
            
            if (res.sucesso) {
                estrutura = res.estrutura;
                renderizarDiagrama();
                $('#diagramaLoading').hide();
            } else {
                throw new Error(res.mensagem || 'Erro ao carregar estrutura');
            }
        } catch (err) {
            $('#diagramaLoading').hide();
            $('#diagramaEmpty').show();
            Swal.fire('Erro', err.message || 'Erro ao carregar diagrama', 'error');
        }
    }
    
    // ============ RENDER DIAGRAM ============
    function renderizarDiagrama() {
        // Clear canvas
        $canvas.find('.table-node').remove();
        $relationsSvg.find('path').remove();
        $outlineList.empty();
        hiddenTables.clear();
        
        if (!estrutura || !estrutura.tabelas.length) {
            $('#diagramaEmpty').show().find('h5').text('Nenhuma tabela encontrada');
            return;
        }
        
        $('#diagramaEmpty').hide();
        
        // Calculate positions
        const positions = calcularPosicoes(estrutura.tabelas);
        
        // Verificar se é modo de tabela específica
        const modoTabela = $('#selectModoDiagrama').val();
        const tabelaEspecifica = $('#selectTabelaDiagrama').val();
        let tabelaPrincipal = null;
        
        if (modoTabela === 'tabela' && tabelaEspecifica) {
            const [schema, tabela] = tabelaEspecifica.split('|');
            tabelaPrincipal = schema ? `${schema}.${tabela}` : tabela;
        }
        
        // Render table nodes
        estrutura.tabelas.forEach((tabela, index) => {
            const pos = positions[tabela.completo] || positions[tabela.nome] || { x: 50 + (index % 4) * 280, y: 50 + Math.floor(index / 4) * 350 };
            
            // Verificar se esta é a tabela principal
            const isMainTable = tabelaPrincipal && (tabela.completo === tabelaPrincipal || tabela.nome === tabelaPrincipal);
            
            criarNodeTabela(tabela, pos.x, pos.y, isMainTable);
            
            // Add to outline
            $outlineList.append(`
                <div class="outline-table-item ${isMainTable ? 'main-table' : ''}" data-table="${tabela.completo}">
                    <i class="bi bi-table"></i>
                    <span>${tabela.nome}${isMainTable ? ' ⭐' : ''}</span>
                </div>
            `);
        });
        
        // Render relationships
        renderizarRelacionamentos();
        
        // Update minimap
        atualizarMinimap();
    }
    
    // ============ CALCULATE POSITIONS ============
    function calcularPosicoes(tabelas) {
        const positions = {};
        const cols = Math.ceil(Math.sqrt(tabelas.length));
        const spacing = { x: 300, y: 380 };
        
        tabelas.forEach((tabela, i) => {
            const key = tabela.completo || tabela.nome;
            
            // Check saved positions first
            if (posicoesSalvas[key]) {
                positions[key] = posicoesSalvas[key];
            } else {
                positions[key] = {
                    x: 50 + (i % cols) * spacing.x,
                    y: 50 + Math.floor(i / cols) * spacing.y
                };
            }
        });
        
        return positions;
    }
    
    // ============ CREATE TABLE NODE ============
    function criarNodeTabela(tabela, x, y, isMainTable = false) {
        const mainClass = isMainTable ? ' main-table' : '';
        const node = $(`
            <div class="table-node${mainClass}" data-table="${tabela.completo}" style="left: ${x}px; top: ${y}px;">
                <div class="table-header">
                    <div class="table-name">
                        <i class="bi bi-table"></i>
                        <span>${tabela.nome}${isMainTable ? ' ⭐' : ''}</span>
                    </div>
                    <div class="table-actions">
                        <button class="btn-collapse" title="Recolher"><i class="bi bi-chevron-up"></i></button>
                    </div>
                </div>
                <div class="table-columns">
                    ${tabela.colunas.map(col => criarColuna(col, tabela)).join('')}
                </div>
            </div>
        `);
        
        $canvas.append(node);
        
        // Drag handling
        node.on('mousedown', function(e) {
            if (e.button !== 0) return;
            if ($(e.target).is('button, button *')) return;
            
            selectedNode = $(this);
            nodeDragData = {
                startX: e.clientX,
                startY: e.clientY,
                nodeX: parseInt($(this).css('left')) || 0,
                nodeY: parseInt($(this).css('top')) || 0
            };
            
            $(this).addClass('dragging');
            e.stopPropagation();
        });
        
        // Collapse/expand
        node.find('.btn-collapse').on('click', function(e) {
            e.stopPropagation();
            const $cols = node.find('.table-columns');
            const $icon = $(this).find('i');
            
            if ($cols.is(':visible')) {
                $cols.slideUp(200);
                $icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
            } else {
                $cols.slideDown(200);
                $icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
            }
            
            setTimeout(renderizarRelacionamentos, 250);
        });
        
        // Context menu
        node.on('contextmenu', function(e) {
            e.preventDefault();
            mostrarContextMenu(e.clientX, e.clientY, $(this).data('table'));
        });
    }
    
    // ============ RELATIONSHIP LINE CLICK ============
    $(document).on('click', '.relation-line', function(e) {
        e.stopPropagation();
        
        // Remove seleção anterior
        $('.relation-line').removeClass('selected');
        $('.table-column').removeClass('relation-highlight');
        
        // Selecionar linha atual
        $(this).addClass('selected');
        
        const fromTable = $(this).attr('data-from');
        const toTable = $(this).attr('data-to');
        const fromColumn = $(this).attr('data-from-column');
        const toColumn = $(this).attr('data-to-column');
        
        // Destacar colunas envolvidas
        if (fromColumn && toColumn) {
            $(`.table-node[data-table="${fromTable}"] .table-column[data-column="${fromColumn}"]`).addClass('relation-highlight');
            $(`.table-node[data-table="${toTable}"] .table-column[data-column="${toColumn}"]`).addClass('relation-highlight');
            
            // Mostrar informações da relação
            const fromTableName = fromTable.split('.').pop();
            const toTableName = toTable.split('.').pop();
            
            Swal.fire({
                title: '<i class="bi bi-link-45deg text-warning"></i> Relacionamento',
                html: `
                    <div class="text-start">
                        <h6 class="text-primary mb-2"><i class="bi bi-table"></i> Tabela Origem:</h6>
                        <p class="mb-1"><strong>${fromTableName}</strong></p>
                        <p class="text-muted mb-3">${fromColumn}</p>
                        
                        <h6 class="text-success mb-2"><i class="bi bi-table"></i> Tabela Destino:</h6>
                        <p class="mb-1"><strong>${toTableName}</strong></p>
                        <p class="text-muted mb-0">${toColumn}</p>
                        
                        <hr>
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> 
                            As colunas relacionadas estão destacadas em laranja.
                        </small>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Entendi',
                confirmButtonColor: '#6366f1',
                customClass: {
                    popup: 'relation-popup'
                }
            });
        } else {
            // Fallback se não tiver informações das colunas
            Swal.fire({
                title: '<i class="bi bi-link-45deg text-warning"></i> Relacionamento',
                html: `
                    <div class="text-start">
                        <p><strong>Entre:</strong> ${fromTable}</p>
                        <p><strong>E:</strong> ${toTable}</p>
                        <hr>
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> 
                            Informações detalhadas das colunas não disponíveis.
                        </small>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Entendi',
                confirmButtonColor: '#6366f1'
            });
        }
    });
    
    // Limpar seleção ao clicar fora
    $canvasArea.on('click', function(e) {
        if (e.target === this || $(e.target).closest('#diagrama-canvas').length) {
            $('.relation-line').removeClass('selected');
            $('.table-column').removeClass('relation-highlight');
        }
    });
    
    // ============ CREATE COLUMN HTML ============
    function criarColuna(col, tabela) {
        let iconClass = '';
        let iconHtml = '';
        
        // Check if FK
        const isFK = estrutura.relacionamentos.some(r => 
            (r.from_table === tabela.completo || r.from_table === tabela.nome) && 
            r.from_column === col.nome
        );
        
        if (col.pk) {
            iconClass = 'pk';
            iconHtml = '<i class="bi bi-key-fill"></i>';
        } else if (isFK) {
            iconClass = 'fk';
            iconHtml = '<i class="bi bi-link-45deg"></i>';
        }
        
        return `
            <div class="table-column" data-column="${col.nome}" title="${col.comentario || ''}">
                <div class="col-icon ${iconClass}">${iconHtml}</div>
                <span class="col-name">${col.nome}</span>
                <span class="col-type">${col.tipo}</span>
                ${!col.nulo ? '' : '<span class="col-null">NULL</span>'}
            </div>
        `;
    }
    
    // ============ RENDER RELATIONSHIPS ============
    function renderizarRelacionamentos() {
        $relationsSvg.find('path').remove();
        
        if (!estrutura || !estrutura.relacionamentos) return;
        
        estrutura.relacionamentos.forEach(rel => {
            const $fromNode = $(`.table-node[data-table="${rel.from_table}"]`);
            const $toNode = $(`.table-node[data-table="${rel.to_table}"]`);
            
            if (!$fromNode.length || !$toNode.length) return;
            if (hiddenTables.has(rel.from_table) || hiddenTables.has(rel.to_table)) return;
            
            // Get positions relative to the canvas area, considering zoom and pan
            const fromPos = getNodePosition($fromNode);
            const toPos = getNodePosition($toNode);
            
            // Calculate connection points
            const fromPoint = calcularPontoConexao(fromPos, toPos);
            const toPoint = calcularPontoConexao(toPos, fromPos);
            
            // Create bezier curve
            const midX = (fromPoint.x + toPoint.x) / 2;
            const path = `M ${fromPoint.x} ${fromPoint.y} C ${midX} ${fromPoint.y}, ${midX} ${toPoint.y}, ${toPoint.x} ${toPoint.y}`;
            
            const pathEl = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            pathEl.setAttribute('d', path);
            pathEl.setAttribute('class', 'relation-line');
            pathEl.setAttribute('data-from', rel.from_table);
            pathEl.setAttribute('data-to', rel.to_table);
            pathEl.setAttribute('data-from-column', rel.from_column || '');
            pathEl.setAttribute('data-to-column', rel.to_column || '');
            
            $relationsSvg[0].appendChild(pathEl);
        });
    }
    
    function getNodePosition($node) {
        const left = parseInt($node.css('left')) || 0;
        const top = parseInt($node.css('top')) || 0;
        const width = $node.outerWidth();
        const height = $node.outerHeight();
        
        return {
            x: (left * zoom) + pan.x,
            y: (top * zoom) + pan.y,
            width: width * zoom,
            height: height * zoom,
            centerX: (left * zoom) + pan.x + (width * zoom) / 2,
            centerY: (top * zoom) + pan.y + (height * zoom) / 2
        };
    }
    
    function calcularPontoConexao(fromPos, toPos) {
        const dx = toPos.centerX - fromPos.centerX;
        const dy = toPos.centerY - fromPos.centerY;
        
        if (Math.abs(dx) > Math.abs(dy)) {
            // Connect on left or right
            return {
                x: fromPos.centerX + (dx > 0 ? fromPos.width / 2 : -fromPos.width / 2),
                y: fromPos.centerY
            };
        } else {
            // Connect on top or bottom  
            return {
                x: fromPos.centerX,
                y: fromPos.centerY + (dy > 0 ? fromPos.height / 2 : -fromPos.height / 2)
            };
        }
    }
    
    // ============ CANVAS INTERACTION ============
    
    // Pan canvas
    $canvasArea.on('mousedown', function(e) {
        if (e.button !== 0 || nodeDragData) return;
        
        isDragging = true;
        dragStart = { x: e.clientX - pan.x, y: e.clientY - pan.y };
        $canvas.addClass('dragging');
    });
    
    $(document).on('mousemove', function(e) {
        if (nodeDragData && selectedNode) {
            // Dragging a node
            const newX = nodeDragData.nodeX + (e.clientX - nodeDragData.startX) / zoom;
            const newY = nodeDragData.nodeY + (e.clientY - nodeDragData.startY) / zoom;
            
            selectedNode.css({ left: newX + 'px', top: newY + 'px' });
            atualizarRelacionamentosThrottled();
        } else if (isDragging) {
            // Panning canvas
            pan.x = e.clientX - dragStart.x;
            pan.y = e.clientY - dragStart.y;
            aplicarTransformacao();
        }
    });
    
    $(document).on('mouseup', function() {
        if (nodeDragData && selectedNode) {
            selectedNode.removeClass('dragging');
            nodeDragData = null;
            selectedNode = null;
            atualizarMinimap();
        }
        
        isDragging = false;
        $canvas.removeClass('dragging');
    });
    
    // Zoom with wheel
    $canvasArea.on('wheel', function(e) {
        e.preventDefault();
        
        const delta = e.originalEvent.deltaY > 0 ? -0.1 : 0.1;
        const newZoom = Math.max(0.2, Math.min(2, zoom + delta));
        
        // Zoom toward mouse position
        const rect = $canvasArea[0].getBoundingClientRect();
        const mouseX = e.clientX - rect.left;
        const mouseY = e.clientY - rect.top;
        
        const ratio = newZoom / zoom;
        pan.x = mouseX - (mouseX - pan.x) * ratio;
        pan.y = mouseY - (mouseY - pan.y) * ratio;
        
        zoom = newZoom;
        aplicarTransformacao();
        atualizarMinimap();
    });
    
    function aplicarTransformacao() {
        $canvas.css('transform', `translate(${pan.x}px, ${pan.y}px) scale(${zoom})`);
        $canvas.css('transform-origin', '0 0');
        $('#zoomIndicator').text(Math.round(zoom * 100) + '%');
        
        // Atualizar linhas de relacionamento sempre que há transformação
        renderizarRelacionamentos();
    }
    
    // Função otimizada para atualizar relacionamentos com throttling
    function atualizarRelacionamentosThrottled() {
        if (relationUpdateTimeout) {
            clearTimeout(relationUpdateTimeout);
        }
        relationUpdateTimeout = setTimeout(() => {
            renderizarRelacionamentos();
            relationUpdateTimeout = null;
        }, 16); // ~60fps
    }
    
    // ============ ZOOM CONTROLS ============
    $('#btnZoomIn').on('click', function() {
        zoom = Math.min(2, zoom + 0.1);
        aplicarTransformacao();
        atualizarMinimap();
    });
    
    $('#btnZoomOut').on('click', function() {
        zoom = Math.max(0.2, zoom - 0.1);
        aplicarTransformacao();
        atualizarMinimap();
    });
    
    $('#btnResetZoom').on('click', function() {
        zoom = 1;
        pan = { x: 0, y: 0 };
        aplicarTransformacao();
        atualizarMinimap();
    });
    
    $('#btnFitToScreen').on('click', fitToScreen);
    
    function fitToScreen() {
        const nodes = $('.table-node');
        if (!nodes.length) return;
        
        let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
        
        nodes.each(function() {
            const left = parseInt($(this).css('left')) || 0;
            const top = parseInt($(this).css('top')) || 0;
            const width = $(this).outerWidth();
            const height = $(this).outerHeight();
            
            minX = Math.min(minX, left);
            minY = Math.min(minY, top);
            maxX = Math.max(maxX, left + width);
            maxY = Math.max(maxY, top + height);
        });
        
        const contentWidth = maxX - minX + 100;
        const contentHeight = maxY - minY + 100;
        const areaWidth = $canvasArea.width();
        const areaHeight = $canvasArea.height();
        
        zoom = Math.min(areaWidth / contentWidth, areaHeight / contentHeight, 1);
        pan.x = (areaWidth - contentWidth * zoom) / 2 - minX * zoom + 50;
        pan.y = (areaHeight - contentHeight * zoom) / 2 - minY * zoom + 50;
        
        aplicarTransformacao();
        atualizarMinimap();
    }
    
    // ============ AUTO-ORGANIZE ============
    $('#btnOrganizar').on('click', function() {
        if (!estrutura || !estrutura.tabelas.length) return;
        
        const cols = Math.ceil(Math.sqrt(estrutura.tabelas.length));
        const spacing = { x: 300, y: 380 };
        
        $('.table-node').each(function(i) {
            const newX = 50 + (i % cols) * spacing.x;
            const newY = 50 + Math.floor(i / cols) * spacing.y;
            
            $(this).animate({ left: newX + 'px', top: newY + 'px' }, {
                duration: 300,
                step: function() {
                    // Atualizar linhas durante a animação
                    renderizarRelacionamentos();
                }
            });
        });
        
        setTimeout(() => {
            renderizarRelacionamentos();
            atualizarMinimap();
        }, 350);
    });
    
    // ============ SAVE POSITIONS ============
    $('#btnSalvarPosicoes').on('click', async function() {
        const conexaoId = $('#selectConexaoDiagrama').val();
        if (!conexaoId) return;
        
        const posicoes = [];
        $('.table-node').each(function() {
            posicoes.push({
                tabela: $(this).data('table'),
                x: parseInt($(this).css('left')) || 0,
                y: parseInt($(this).css('top')) || 0
            });
        });
        
        try {
            const res = await $.ajax({
                url: BASE_URL + '/diagrama/posicoes/' + conexaoId,
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(posicoes)
            });
            
            if (res.sucesso) {
                Swal.fire({
                    icon: 'success',
                    title: 'Salvo!',
                    text: 'Posições salvas com sucesso',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                throw new Error(res.mensagem);
            }
        } catch (err) {
            Swal.fire('Erro', err.message || 'Erro ao salvar posições', 'error');
        }
    });
    
    // ============ OUTLINE ============
    $('#toggleOutline').on('click', function() {
        $outline.toggleClass('collapsed');
        const $icon = $(this).find('i');
        
        if ($outline.hasClass('collapsed')) {
            $icon.removeClass('bi-chevron-left').addClass('bi-chevron-right');
        } else {
            $icon.removeClass('bi-chevron-right').addClass('bi-chevron-left');
        }
    });
    
    $(document).on('click', '.outline-table-item', function() {
        const tableName = $(this).data('table');
        const $node = $(`.table-node[data-table="${tableName}"]`);
        
        if ($node.length) {
            // Center on this node
            const left = parseInt($node.css('left')) || 0;
            const top = parseInt($node.css('top')) || 0;
            const areaWidth = $canvasArea.width();
            const areaHeight = $canvasArea.height();
            
            pan.x = areaWidth / 2 - left * zoom - $node.width() * zoom / 2;
            pan.y = areaHeight / 2 - top * zoom - $node.height() * zoom / 2;
            
            aplicarTransformacao();
            
            // Highlight
            $('.table-node').removeClass('selected');
            $node.addClass('selected');
            
            $('.outline-table-item').removeClass('active');
            $(this).addClass('active');
        }
    });
    
    // ============ MINIMAP ============
    function atualizarMinimap() {
        const canvas = $minimap[0];
        const ctx = canvas.getContext('2d', { willReadFrequently: true });
        const scale = 0.05;
        
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#1e1e1e';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Draw tables
        $('.table-node').each(function() {
            if (hiddenTables.has($(this).data('table'))) return;
            
            const left = (parseInt($(this).css('left')) || 0) * scale;
            const top = (parseInt($(this).css('top')) || 0) * scale;
            const width = $(this).outerWidth() * scale;
            const height = $(this).outerHeight() * scale;
            
            ctx.fillStyle = '#0d47a1';
            ctx.fillRect(left + 5, top + 5, width, height);
        });
        
        // Update viewport indicator
        const areaWidth = $canvasArea.width();
        const areaHeight = $canvasArea.height();
        
        $minimapViewport.css({
            left: (-pan.x * scale / zoom) + 5 + 'px',
            top: (-pan.y * scale / zoom) + 5 + 'px',
            width: (areaWidth * scale / zoom) + 'px',
            height: (areaHeight * scale / zoom) + 'px'
        });
    }
    
    // ============ CONTEXT MENU ============
    let contextMenuTable = null;
    
    function mostrarContextMenu(x, y, tableName) {
        contextMenuTable = tableName;
        $contextMenu.addClass('show').css({ left: x + 'px', top: y + 'px' });
    }
    
    $(document).on('click', function() {
        $contextMenu.removeClass('show');
    });
    
    $contextMenu.on('click', '.menu-item', function() {
        const action = $(this).data('action');
        
        switch (action) {
            case 'focus':
                carregarDiagramaTabela(contextMenuTable);
                break;
            case 'highlight':
                destacarRelacoes(contextMenuTable);
                break;
            case 'hide':
                ocultarTabela(contextMenuTable);
                break;
            case 'show-all':
                mostrarTodasTabelas();
                break;
        }
        
        $contextMenu.removeClass('show');
    });
    
    async function carregarDiagramaTabela(tableName) {
        const conexaoId = $('#selectConexaoDiagrama').val();
        if (!conexaoId) return;
        
        const parts = tableName.split('.');
        const schema = parts.length > 1 ? parts[0] : '';
        const tabela = parts.length > 1 ? parts[1] : parts[0];
        
        $('#selectModoDiagrama').val('tabela').trigger('change');
        
        setTimeout(() => {
            $('#selectTabelaDiagrama').val(schema + '|' + tabela);
            carregarDiagrama();
        }, 300);
    }
    
    function destacarRelacoes(tableName) {
        // Reset highlights
        $('.table-node').removeClass('highlighted');
        $relationsSvg.find('path').removeClass('highlighted');
        
        // Highlight related tables
        estrutura.relacionamentos.forEach(rel => {
            if (rel.from_table === tableName || rel.to_table === tableName) {
                $(`.table-node[data-table="${rel.from_table}"]`).addClass('highlighted');
                $(`.table-node[data-table="${rel.to_table}"]`).addClass('highlighted');
                $relationsSvg.find(`path[data-from="${rel.from_table}"][data-to="${rel.to_table}"]`).addClass('highlighted');
            }
        });
    }
    
    function ocultarTabela(tableName) {
        hiddenTables.add(tableName);
        $(`.table-node[data-table="${tableName}"]`).fadeOut(200);
        renderizarRelacionamentos();
        atualizarMinimap();
    }
    
    function mostrarTodasTabelas() {
        hiddenTables.clear();
        $('.table-node').fadeIn(200);
        renderizarRelacionamentos();
        atualizarMinimap();
    }
    
    // ============ EXPORT ============
    $('.dropdown-item[data-format]').on('click', async function(e) {
        e.preventDefault();
        const format = $(this).data('format');
        
        Swal.fire({
            title: 'Exportando...',
            text: 'Preparando diagrama para exportação',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        try {
            // Clone canvas for export
            const $exportCanvas = $canvas.clone();
            $exportCanvas.css({
                transform: 'none',
                position: 'relative',
                left: 0,
                top: 0
            });
            
            // Create temp container
            const $tempContainer = $('<div>').css({
                position: 'absolute',
                left: '-9999px',
                background: '#1e1e1e',
                padding: '20px'
            }).append($exportCanvas);
            
            $('body').append($tempContainer);
            
            // Calculate bounds
            let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
            $canvas.find('.table-node:visible').each(function() {
                const left = parseInt($(this).css('left')) || 0;
                const top = parseInt($(this).css('top')) || 0;
                minX = Math.min(minX, left);
                minY = Math.min(minY, top);
                maxX = Math.max(maxX, left + $(this).outerWidth());
                maxY = Math.max(maxY, top + $(this).outerHeight());
            });
            
            $tempContainer.css({
                width: (maxX - minX + 100) + 'px',
                height: (maxY - minY + 100) + 'px'
            });
            
            $exportCanvas.find('.table-node').each(function() {
                const left = parseInt($(this).css('left')) || 0;
                const top = parseInt($(this).css('top')) || 0;
                $(this).css({
                    left: (left - minX + 30) + 'px',
                    top: (top - minY + 30) + 'px'
                });
            });
            
            // Generate image
            const canvasEl = await html2canvas($tempContainer[0], {
                backgroundColor: '#1e1e1e',
                scale: 2
            });
            
            $tempContainer.remove();
            
            const conexaoNome = $('#selectConexaoDiagrama option:selected').text() || 'diagrama';
            const fileName = `diagrama_${conexaoNome.replace(/[^a-z0-9]/gi, '_')}_${Date.now()}`;
            
            if (format === 'png') {
                baixarImagem(canvasEl, fileName + '.png', 'image/png');
            } else if (format === 'jpg') {
                baixarImagem(canvasEl, fileName + '.jpg', 'image/jpeg');
            } else if (format === 'pdf') {
                const { jsPDF } = window.jspdf;
                const imgData = canvasEl.toDataURL('image/jpeg', 0.95);
                const pdf = new jsPDF({
                    orientation: canvasEl.width > canvasEl.height ? 'l' : 'p',
                    unit: 'px',
                    format: [canvasEl.width, canvasEl.height]
                });
                pdf.addImage(imgData, 'JPEG', 0, 0, canvasEl.width, canvasEl.height);
                pdf.save(fileName + '.pdf');
            } else if (format === 'svg') {
                exportarSVG(fileName);
            }
            
            Swal.close();
            
        } catch (err) {
            Swal.fire('Erro', 'Erro ao exportar: ' + err.message, 'error');
        }
    });
    
    function baixarImagem(canvas, filename, type) {
        const link = document.createElement('a');
        link.download = filename;
        link.href = canvas.toDataURL(type);
        link.click();
    }
    
    function exportarSVG(fileName) {
        // Create SVG export
        const nodes = [];
        const relations = [];
        
        let minX = 0, minY = 0;
        
        $('.table-node:visible').each(function() {
            const left = parseInt($(this).css('left')) || 0;
            const top = parseInt($(this).css('top')) || 0;
            const width = $(this).outerWidth();
            const height = $(this).outerHeight();
            const name = $(this).data('table');
            const cols = [];
            
            $(this).find('.table-column').each(function() {
                cols.push({
                    name: $(this).find('.col-name').text(),
                    type: $(this).find('.col-type').text(),
                    isPK: $(this).find('.col-icon').hasClass('pk'),
                    isFK: $(this).find('.col-icon').hasClass('fk')
                });
            });
            
            nodes.push({ x: left, y: top, width, height, name, cols });
        });
        
        // Build SVG string
        const svgWidth = 2000;
        const svgHeight = 2000;
        
        let svg = '<' + '?xml version="1.0" encoding="UTF-8"?' + '>\n';
        svg += `<svg xmlns="http://www.w3.org/2000/svg" width="${svgWidth}" height="${svgHeight}" viewBox="0 0 ${svgWidth} ${svgHeight}">
<style>
    .table-rect { fill: #252526; stroke: #3c3c3c; }
    .table-header { fill: #0d47a1; }
    .table-name { fill: white; font-family: Arial; font-size: 13px; font-weight: bold; }
    .col-name { fill: #e0e0e0; font-family: Consolas, monospace; font-size: 12px; }
    .col-type { fill: #9e9e9e; font-family: Consolas, monospace; font-size: 11px; }
    .pk-icon { fill: #ffd700; }
    .fk-icon { fill: #4fc3f7; }
    .relation { fill: none; stroke: #4fc3f7; stroke-width: 2; }
</style>
<rect width="100%" height="100%" fill="#1e1e1e"/>
`;
        
        // Draw nodes
        nodes.forEach(node => {
            svg += `<g transform="translate(${node.x + 30}, ${node.y + 30})">
    <rect class="table-rect" width="${node.width}" height="${node.height}" rx="6"/>
    <rect class="table-header" width="${node.width}" height="36" rx="6"/>
    <rect class="table-header" y="30" width="${node.width}" height="6"/>
    <text class="table-name" x="12" y="24">${escapeXML(node.name.split('.').pop())}</text>
`;
            
            let y = 50;
            node.cols.forEach(col => {
                if (col.isPK) {
                    svg += `<text class="pk-icon" x="12" y="${y}">🔑</text>`;
                } else if (col.isFK) {
                    svg += `<text class="fk-icon" x="12" y="${y}">🔗</text>`;
                }
                svg += `<text class="col-name" x="30" y="${y}">${escapeXML(col.name)}</text>`;
                svg += `<text class="col-type" x="${node.width - 10}" y="${y}" text-anchor="end">${escapeXML(col.type)}</text>`;
                y += 24;
            });
            
            svg += `</g>\n`;
        });
        
        svg += `</svg>`;
        
        // Download
        const blob = new Blob([svg], { type: 'image/svg+xml' });
        const link = document.createElement('a');
        link.download = fileName + '.svg';
        link.href = URL.createObjectURL(blob);
        link.click();
    }
    
    function escapeXML(str) {
        return str.replace(/&/g, '&amp;')
                  .replace(/</g, '&lt;')
                  .replace(/>/g, '&gt;')
                  .replace(/"/g, '&quot;')
                  .replace(/'/g, '&apos;');
    }
    
    // ============ KEYBOARD SHORTCUTS ============
    $(document).on('keydown', function(e) {
        // Ctrl+S = Save positions
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            $('#btnSalvarPosicoes').click();
        }
        
        // Escape = Deselect
        if (e.key === 'Escape') {
            $('.table-node').removeClass('selected highlighted');
            $relationsSvg.find('path').removeClass('highlighted');
        }
        
        // + / - = Zoom
        if (e.key === '+' || e.key === '=') {
            $('#btnZoomIn').click();
        }
        if (e.key === '-') {
            $('#btnZoomOut').click();
        }
        
        // 0 = Reset zoom
        if (e.key === '0') {
            $('#btnResetZoom').click();
        }
        
        // F = Fit to screen
        if (e.key === 'f' && !e.ctrlKey && !e.altKey) {
            fitToScreen();
        }
    });
    
    // ============ MINIMAP NAVIGATION ============
    let minimapDragging = false;
    
    // Navegação por clique no minimap
    $minimap.on('click', function(e) {
        if (minimapDragging) return;
        
        const rect = this.getBoundingClientRect();
        const x = e.clientX - rect.left - 5; // Ajustar pela margem
        const y = e.clientY - rect.top - 5;
        
        const scale = 0.05;
        const newPanX = -x / scale * zoom + $canvasArea.width() / 2;
        const newPanY = -y / scale * zoom + $canvasArea.height() / 2;
        
        pan.x = newPanX;
        pan.y = newPanY;
        
        aplicarTransformacao();
        atualizarMinimap();
    });
    
    // Arrastar viewport no minimap
    $minimapViewport.on('mousedown', function(e) {
        e.preventDefault();
        minimapDragging = true;
        
        const startX = e.clientX;
        const startY = e.clientY;
        const startPanX = pan.x;
        const startPanY = pan.y;
        const scale = 0.05;
        
        $(document).on('mousemove.minimap', function(e) {
            if (!minimapDragging) return;
            
            const deltaX = e.clientX - startX;
            const deltaY = e.clientY - startY;
            
            pan.x = startPanX - deltaX / scale * zoom;
            pan.y = startPanY - deltaY / scale * zoom;
            
            aplicarTransformacao();
            atualizarMinimap();
        });
        
        $(document).on('mouseup.minimap', function() {
            minimapDragging = false;
            $(document).off('.minimap');
        });
    });
    
    // ============ WINDOW RESIZE ============
    $(window).on('resize', function() {
        atualizarMinimap();
    });
});
</script>
SCRIPTS;

// Replace the BASE_URL placeholder
$extraScripts = str_replace('REPLACE_BASE_URL', $base, $extraScripts);

include __DIR__ . '/layouts/base.php';
?>
