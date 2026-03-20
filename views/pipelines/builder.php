<?php
$pageTitle = 'Pipeline Builder';
$currentPage = 'pipelines';
$csrfToken = App\Core\AuthMiddleware::gerarTokenCSRF();
$pipelineId = $pipelineId ?? null;
ob_start();
?>

<!-- Builder Full Layout -->
<div id="pipeline-builder" class="builder-container">
    <!-- Top Toolbar -->
    <div class="builder-toolbar">
        <div class="toolbar-row toolbar-row-main">
            <div class="toolbar-left">
                <a href="<?= $baseUrl ?>/pipelines" class="btn btn-sm btn-outline-secondary" title="Voltar">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div class="pipeline-name-wrapper">
                    <input type="text" id="pipelineName" class="pipeline-name-input" 
                           value="Novo Pipeline" placeholder="Nome do pipeline">
                    <button class="btn btn-sm text-muted d-none d-sm-inline-flex" onclick="editDescription()" title="Editar descrição">
                        <i class="bi bi-pencil"></i>
                    </button>
                </div>
                <span id="saveStatus" class="save-status d-none d-md-flex">
                    <i class="bi bi-cloud-check"></i> Salvo
                </span>
            </div>

            <div class="toolbar-center d-none d-lg-flex">
                <div class="mode-switcher">
                    <button class="mode-btn active" data-mode="nocode" onclick="switchMode('nocode')">
                        <i class="bi bi-hand-index-thumb me-1"></i>No-Code
                    </button>
                    <button class="mode-btn" data-mode="lowcode" onclick="switchMode('lowcode')">
                        <i class="bi bi-sliders me-1"></i>Low-Code
                    </button>
                    <button class="mode-btn" data-mode="code" onclick="switchMode('code')">
                        <i class="bi bi-code-slash me-1"></i>Code
                    </button>
                </div>
            </div>

            <div class="toolbar-right">
                <!-- Mobile: hamburger for more actions -->
                <button class="btn btn-sm btn-outline-secondary d-lg-none" onclick="toggleMobileToolbar()" title="Mais opções">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <!-- Mobile: panel toggles -->
                <button class="btn btn-sm btn-outline-secondary d-lg-none" onclick="toggleLeftPanel()" title="Componentes" id="btnToggleLeftMobile">
                    <i class="bi bi-puzzle"></i>
                </button>
                <div class="d-none d-md-flex align-items-center gap-1">
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-secondary" onclick="zoomOut()" title="Zoom Out">
                            <i class="bi bi-zoom-out"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="zoomReset()" title="Reset Zoom">
                            <span id="zoomLevel">100%</span>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="zoomIn()" title="Zoom In">
                            <i class="bi bi-zoom-in"></i>
                        </button>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary" onclick="autoLayout()" title="Auto Layout">
                        <i class="bi bi-grid-3x3-gap"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="clearCanvas()" title="Limpar">
                        <i class="bi bi-eraser"></i>
                    </button>
                </div>
                <button class="btn btn-sm btn-outline-secondary d-none d-sm-inline-flex" onclick="abrirModalCompartilhamento('pipeline', document.getElementById('pipelineId').value)" title="Compartilhar">
                    <i class="bi bi-share"></i>
                </button>
                <button class="btn btn-sm btn-outline-primary d-none d-sm-inline-flex" onclick="validatePipeline()" title="Validar">
                    <i class="bi bi-check2-all"></i>
                </button>
                <button class="btn btn-sm btn-success" onclick="executePipeline()" title="Executar">
                    <i class="bi bi-play-fill"></i><span class="d-none d-md-inline ms-1">Executar</span>
                </button>
                <button class="btn btn-sm btn-primary" onclick="savePipeline()">
                    <i class="bi bi-save"></i><span class="d-none d-sm-inline ms-1">Salvar</span>
                </button>
                <?php 
                $nivelLogado = App\Core\AuthMiddleware::obterUsuario()['nivel_acesso'] ?? 'operador';
                if (in_array($nivelLogado, ['super_admin', 'admin'])): ?>
                <div class="dropdown d-none d-sm-inline-block">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside" title="Visibilidade">
                        <i class="bi bi-building"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-3" style="min-width:280px">
                        <small class="fw-bold text-muted"><i class="bi bi-building me-1"></i>Visibilidade</small>
                        <div class="mt-2">
                            <label class="form-label form-label-sm">Empresas</label>
                            <select class="form-select form-select-sm" name="empresas[]" id="rbac_empresas" multiple size="3"></select>
                        </div>
                        <div class="mt-2">
                            <label class="form-label form-label-sm">Projetos</label>
                            <select class="form-select form-select-sm" name="projetos[]" id="rbac_projetos" multiple size="3"></select>
                        </div>
                        <small class="text-muted">Ctrl+click para múltiplas</small>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Mobile secondary toolbar row (mode + extra actions) -->
        <div class="toolbar-row toolbar-row-secondary d-none" id="mobileToolbarRow">
            <div class="mode-switcher mode-switcher-mobile">
                <button class="mode-btn active" data-mode="nocode" onclick="switchMode('nocode')">
                    <i class="bi bi-hand-index-thumb me-1"></i>No-Code
                </button>
                <button class="mode-btn" data-mode="lowcode" onclick="switchMode('lowcode')">
                    <i class="bi bi-sliders me-1"></i>Low-Code
                </button>
                <button class="mode-btn" data-mode="code" onclick="switchMode('code')">
                    <i class="bi bi-code-slash me-1"></i>Code
                </button>
            </div>
            <div class="d-flex gap-1 d-md-none">
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-secondary" onclick="zoomOut()" title="Zoom Out"><i class="bi bi-zoom-out"></i></button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="zoomReset()" title="Reset"><span id="zoomLevelMobile">100%</span></button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="zoomIn()" title="Zoom In"><i class="bi bi-zoom-in"></i></button>
                </div>
                <button class="btn btn-sm btn-outline-secondary" onclick="autoLayout()" title="Auto Layout"><i class="bi bi-grid-3x3-gap"></i></button>
                <button class="btn btn-sm btn-outline-secondary" onclick="clearCanvas()" title="Limpar"><i class="bi bi-eraser"></i></button>
            </div>
        </div>
    </div>

    <!-- Main Area -->
    <div class="builder-main">
        <!-- Panel Overlay (mobile backdrop) -->
        <div class="panel-overlay" id="panelOverlay" onclick="closeAllPanels()"></div>
        <!-- Left Panel: Node Palette -->
        <div class="builder-left-panel" id="leftPanel">
            <div class="panel-header">
                <span><i class="bi bi-puzzle me-2"></i>Componentes</span>
                <button class="btn btn-sm text-muted" onclick="toggleLeftPanel()"><i class="bi bi-chevron-left"></i></button>
            </div>
            <div class="panel-search">
                <input type="text" class="form-control form-control-sm" id="nodeSearch" placeholder="Buscar nós...">
            </div>
            <div class="node-palette" id="nodePalette">
                <div class="palette-group">
                    <div class="palette-group-title">Entrada / Saída</div>
                    <div class="palette-node" draggable="true" data-type="trigger">
                        <div class="palette-node-icon" style="background:#10b981"><i class="bi bi-play-circle"></i></div>
                        <div class="palette-node-info">
                            <div class="palette-node-name">Trigger</div>
                            <div class="palette-node-desc">Início do pipeline</div>
                        </div>
                    </div>
                    <div class="palette-node" draggable="true" data-type="api_call">
                        <div class="palette-node-icon" style="background:#7c3aed"><i class="bi bi-cloud-check"></i></div>
                        <div class="palette-node-info">
                            <div class="palette-node-name">API Call</div>
                            <div class="palette-node-desc">Chamar API e avaliar condição</div>
                        </div>
                    </div>
                    <div class="palette-node" draggable="true" data-type="end">
                        <div class="palette-node-icon" style="background:#dc2626"><i class="bi bi-stop-circle"></i></div>
                        <div class="palette-node-info">
                            <div class="palette-node-name">End</div>
                            <div class="palette-node-desc">Fim do pipeline</div>
                        </div>
                    </div>
                </div>
                <div class="palette-group">
                    <div class="palette-group-title">Dados</div>
                    <div class="palette-node" draggable="true" data-type="sql_query">
                        <div class="palette-node-icon" style="background:#3b82f6"><i class="bi bi-database"></i></div>
                        <div class="palette-node-info">
                            <div class="palette-node-name">SQL Query</div>
                            <div class="palette-node-desc">Consulta em banco de dados</div>
                        </div>
                    </div>
                    <div class="palette-node" draggable="true" data-type="http_request">
                        <div class="palette-node-icon" style="background:#8b5cf6"><i class="bi bi-cloud-arrow-up"></i></div>
                        <div class="palette-node-info">
                            <div class="palette-node-name">HTTP Request</div>
                            <div class="palette-node-desc">Requisição API externa</div>
                        </div>
                    </div>
                    <div class="palette-node" draggable="true" data-type="transform">
                        <div class="palette-node-icon" style="background:#f59e0b"><i class="bi bi-arrow-left-right"></i></div>
                        <div class="palette-node-info">
                            <div class="palette-node-name">Transform</div>
                            <div class="palette-node-desc">Transformar dados</div>
                        </div>
                    </div>
                </div>
                <div class="palette-group">
                    <div class="palette-group-title">Lógica</div>
                    <div class="palette-node" draggable="true" data-type="condition">
                        <div class="palette-node-icon" style="background:#eab308"><i class="bi bi-signpost-split"></i></div>
                        <div class="palette-node-info">
                            <div class="palette-node-name">Condition</div>
                            <div class="palette-node-desc">If/Else branch</div>
                        </div>
                    </div>
                    <div class="palette-node" draggable="true" data-type="loop">
                        <div class="palette-node-icon" style="background:#06b6d4"><i class="bi bi-arrow-repeat"></i></div>
                        <div class="palette-node-info">
                            <div class="palette-node-name">Loop</div>
                            <div class="palette-node-desc">Iterar sobre dados</div>
                        </div>
                    </div>
                    <div class="palette-node" draggable="true" data-type="delay">
                        <div class="palette-node-icon" style="background:#9ca3af"><i class="bi bi-clock"></i></div>
                        <div class="palette-node-info">
                            <div class="palette-node-name">Delay</div>
                            <div class="palette-node-desc">Aguardar N segundos</div>
                        </div>
                    </div>
                </div>
                <div class="palette-group">
                    <div class="palette-group-title">Ações</div>
                    <div class="palette-node" draggable="true" data-type="script">
                        <div class="palette-node-icon" style="background:#ef4444"><i class="bi bi-code-slash"></i></div>
                        <div class="palette-node-info">
                            <div class="palette-node-name">Script</div>
                            <div class="palette-node-desc">Código customizado</div>
                        </div>
                    </div>
                    <div class="palette-node" draggable="true" data-type="set_variable">
                        <div class="palette-node-icon" style="background:#14b8a6"><i class="bi bi-braces"></i></div>
                        <div class="palette-node-info">
                            <div class="palette-node-name">Set Variable</div>
                            <div class="palette-node-desc">Definir variável</div>
                        </div>
                    </div>
                    <div class="palette-node" draggable="true" data-type="email">
                        <div class="palette-node-icon" style="background:#ec4899"><i class="bi bi-envelope"></i></div>
                        <div class="palette-node-info">
                            <div class="palette-node-name">Email</div>
                            <div class="palette-node-desc">Enviar e-mail</div>
                        </div>
                    </div>
                    <div class="palette-node" draggable="true" data-type="log_node">
                        <div class="palette-node-icon" style="background:#6b7280"><i class="bi bi-journal-text"></i></div>
                        <div class="palette-node-info">
                            <div class="palette-node-name">Log</div>
                            <div class="palette-node-desc">Registrar saída</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Center: Drawflow Canvas -->
        <div class="builder-canvas-wrapper" id="canvasWrapper">
            <!-- Floating button to reopen left panel -->
            <button class="btn-reopen-panel" id="btnReopenLeft" onclick="toggleLeftPanel()" title="Mostrar Componentes" style="display:none">
                <i class="bi bi-puzzle"></i>
            </button>
            <div id="drawflow" class="drawflow-canvas"></div>
            <!-- Code Editor (hidden, shown in code mode) -->
            <div id="codeEditorWrapper" class="code-editor-wrapper d-none">
                <div class="code-editor-toolbar">
                    <span><i class="bi bi-code-slash me-2"></i>Edição em modo Code (JSON)</span>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary me-1" onclick="formatCode()">
                            <i class="bi bi-braces me-1"></i>Formatar
                        </button>
                        <button class="btn btn-sm btn-primary" onclick="applyCode()">
                            <i class="bi bi-check-lg me-1"></i>Aplicar
                        </button>
                    </div>
                </div>
                <textarea id="codeEditor" class="code-editor-textarea"></textarea>
            </div>
            <!-- Empty state overlay -->
            <div id="emptyOverlay" class="empty-canvas-overlay">
                <div class="empty-canvas-content">
                    <i class="bi bi-diagram-3" style="font-size:3rem;color:var(--primary);opacity:0.5"></i>
                    <h5 class="mt-3 text-muted">Arraste componentes para começar</h5>
                    <p class="text-muted small">Ou clique duas vezes em um componente da paleta</p>
                </div>
            </div>
        </div>

        <!-- Right Panel: Node Properties -->
        <div class="builder-right-panel d-none" id="rightPanel">
            <div class="panel-header">
                <span id="propTitle"><i class="bi bi-gear me-2"></i>Propriedades</span>
                <button class="btn btn-sm text-muted" onclick="closeProperties()"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="panel-body" id="propBody">
                <div class="text-center text-muted py-5">
                    <i class="bi bi-cursor" style="font-size:2rem"></i>
                    <p class="mt-2">Selecione um nó para editar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Panel: Execution Logs -->
    <div class="builder-bottom-panel" id="bottomPanel">
        <div class="bottom-panel-header" onclick="toggleBottomPanel()">
            <span><i class="bi bi-terminal me-2"></i>Console de Execução</span>
            <div class="d-flex align-items-center gap-2">
                <span id="execStatus" class="badge bg-secondary">Parado</span>
                <button class="btn btn-sm text-muted" onclick="clearLogs(); event.stopPropagation()"><i class="bi bi-trash"></i></button>
                <i class="bi bi-chevron-up" id="bottomChevron"></i>
            </div>
        </div>
        <div class="bottom-panel-body" id="bottomBody">
            <div id="execLogs" class="exec-logs">
                <div class="log-entry text-muted"><i class="bi bi-info-circle me-1"></i> Pronto. Execute o pipeline para ver os logs aqui.</div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="pipelineId" value="<?= $pipelineId ? htmlspecialchars($pipelineId) : '' ?>">
<input type="hidden" id="csrfToken" value="<?= htmlspecialchars($csrfToken) ?>">

<?php include __DIR__ . '/../partials/compartilhamento_modal.php'; ?>

<?php
$content = ob_get_clean();

$extraStyles = <<<'STYLES'
<link href="https://cdn.jsdelivr.net/gh/jerosoler/Drawflow@0.0.60/dist/drawflow.min.css" rel="stylesheet">
<style>
/* ========== BUILDER LAYOUT ========== */
/* Override content-wrapper padding for full-screen builder */
.builder-container {
    display: flex;
    flex-direction: column;
    height: calc(100vh - var(--topbar-height));
    margin: -1.5rem;
    overflow: hidden;
}

/* ========== TOOLBAR ========== */
.builder-toolbar {
    display: flex;
    flex-direction: column;
    background: white;
    border-bottom: 2px solid #e0e7ff;
    z-index: 10;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.toolbar-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 12px;
    gap: 8px;
    min-height: 48px;
}
.toolbar-row-secondary {
    border-top: 1px solid var(--gray-100);
    min-height: 40px;
    padding: 4px 12px;
    flex-wrap: wrap;
    justify-content: center;
    gap: 6px;
}
.toolbar-left { display: flex; align-items: center; gap: 6px; flex: 1; min-width: 0; }
.toolbar-center { display: flex; justify-content: center; flex-shrink: 0; }
.toolbar-right { display: flex; align-items: center; gap: 4px; flex-shrink: 0; }

.pipeline-name-wrapper { display: flex; align-items: center; gap: 4px; min-width: 0; flex: 1; }
.pipeline-name-input {
    background: transparent;
    border: none;
    font-size: 1rem;
    font-weight: 700;
    color: #1a202c;
    outline: none;
    padding: 4px 8px;
    border-radius: 8px;
    transition: background 0.2s;
    min-width: 0;
    width: 100%;
    max-width: 350px;
}
.pipeline-name-input:hover { background: var(--gray-100); }
.pipeline-name-input:focus { background: var(--gray-100); box-shadow: 0 0 0 2px var(--primary); }

.mode-switcher-mobile {
    flex-shrink: 0;
}

.save-status {
    font-size: 0.75rem;
    color: var(--gray-400);
    display: flex;
    align-items: center;
    gap: 4px;
}
.save-status.saving { color: var(--warning); }
.save-status.saved { color: var(--success); }

/* Mode Switcher */
.mode-switcher {
    display: flex;
    background: var(--gray-100);
    border-radius: 10px;
    padding: 3px;
}
.mode-btn {
    padding: 6px 16px;
    border: none;
    background: transparent;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--gray-500);
    cursor: pointer;
    transition: all 0.3s;
    white-space: nowrap;
}
.mode-btn:hover { color: var(--gray-600); }
.mode-btn.active {
    background: white;
    color: var(--primary);
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

/* ========== MAIN AREA ========== */
.builder-main {
    display: flex;
    flex: 1;
    overflow: hidden;
    position: relative;
}

/* Left Panel */
.builder-left-panel {
    width: 260px;
    background: white;
    border-right: 1px solid var(--gray-200);
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    transition: width 0.3s ease, transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 5;
}
.builder-left-panel.collapsed { width: 0; overflow: hidden; border: none; }

.panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    font-size: 0.85rem;
    font-weight: 700;
    color: #1a202c;
    border-bottom: 1px solid var(--gray-100);
    flex-shrink: 0;
}
.panel-search {
    padding: 8px 12px;
    border-bottom: 1px solid var(--gray-100);
    flex-shrink: 0;
}
.node-palette {
    flex: 1;
    overflow-y: auto;
    padding: 8px;
}
.palette-group { margin-bottom: 8px; }
.palette-group-title {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--gray-400);
    padding: 8px 8px 4px;
}
.palette-node {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 10px;
    border-radius: 10px;
    cursor: grab;
    transition: all 0.2s;
    border: 1px solid transparent;
    margin-bottom: 4px;
}
.palette-node:hover {
    background: var(--gray-100);
    border-color: var(--gray-200);
}
.palette-node:active { cursor: grabbing; opacity: 0.7; }
.palette-node-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    flex-shrink: 0;
}
.palette-node-name { font-size: 0.85rem; font-weight: 600; color: #1a202c; }
.palette-node-desc { font-size: 0.7rem; color: var(--gray-400); }

/* Center Canvas */
.builder-canvas-wrapper {
    flex: 1;
    position: relative;
    background: #f8f9fc;
    background-image:
        radial-gradient(circle, #ddd 1px, transparent 1px);
    background-size: 24px 24px;
    overflow: hidden;
}
.drawflow-canvas { width: 100%; height: 100%; }

/* Floating reopen button */
.btn-reopen-panel {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 10;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    border: 1px solid var(--gray-200);
    background: white;
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    cursor: pointer;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}
.btn-reopen-panel:hover {
    background: var(--primary);
    color: white;
    transform: scale(1.08);
    box-shadow: 0 4px 16px rgba(99,102,241,0.3);
}
.empty-canvas-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
    z-index: 1;
}
.empty-canvas-content { text-align: center; }

/* Code Editor */
.code-editor-wrapper {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: #1e1e2e;
    z-index: 20;
    display: flex;
    flex-direction: column;
}
.code-editor-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 16px;
    background: #181825;
    color: #cdd6f4;
    font-size: 0.85rem;
    border-bottom: 1px solid #313244;
}
.code-editor-textarea {
    flex: 1;
    width: 100%;
    background: #1e1e2e;
    color: #cdd6f4;
    border: none;
    outline: none;
    padding: 16px;
    font-family: 'JetBrains Mono', 'Fira Code', 'Consolas', monospace;
    font-size: 14px;
    line-height: 1.6;
    resize: none;
    tab-size: 2;
}

/* Right Panel */
.builder-right-panel {
    width: 340px;
    background: white;
    border-left: 1px solid var(--gray-200);
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    z-index: 5;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.panel-body {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
}

/* ========== BOTTOM PANEL ========== */
.builder-bottom-panel {
    background: white;
    border-top: 1px solid var(--gray-200);
    flex-shrink: 0;
    transition: height 0.3s;
}
.bottom-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 16px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    user-select: none;
}
.bottom-panel-body {
    height: 0;
    overflow: hidden;
    transition: height 0.3s;
}
.bottom-panel-body.expanded { height: 200px; overflow-y: auto; }
.exec-logs { padding: 8px 16px; font-family: monospace; font-size: 0.8rem; }
.log-entry { padding: 3px 0; border-bottom: 1px solid var(--gray-100); }
.log-entry.success { color: var(--success); }
.log-entry.error { color: var(--danger); }
.log-entry.warning { color: var(--warning); }

/* ========== DRAWFLOW CUSTOM NODES ========== */
.drawflow .drawflow-node {
    border-radius: 12px !important;
    border: 2px solid var(--gray-200) !important;
    background: white !important;
    padding: 0 !important;
    min-width: 200px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06) !important;
    transition: box-shadow 0.2s, border-color 0.2s;
}
.drawflow .drawflow-node:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.12) !important;
}
.drawflow .drawflow-node.selected {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.2), 0 8px 25px rgba(0,0,0,0.12) !important;
}
.drawflow .drawflow-node .drawflow_content_node {
    padding: 0 !important;
    width: 100%;
}

/* Node internal structure */
.df-node-container {
    display: flex;
    flex-direction: column;
}
.df-node-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 10px 10px 0 0;
    position: relative;
}
.df-node-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.95rem;
    flex-shrink: 0;
}
.df-node-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: #1a202c;
}
.df-node-subtitle {
    font-size: 0.7rem;
    color: var(--gray-400);
}
.df-node-body {
    padding: 8px 16px 12px;
    font-size: 0.75rem;
    color: var(--gray-500);
    border-top: 1px solid var(--gray-100);
}
.df-node-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.6rem;
    color: white;
    background: var(--danger);
    cursor: pointer;
    z-index: 10;
    border: 2px solid white;
}

/* Drawflow connection points */
.drawflow .drawflow-node .input, .drawflow .drawflow-node .output {
    width: 14px !important;
    height: 14px !important;
    border: 3px solid white !important;
    box-shadow: 0 1px 4px rgba(0,0,0,0.2);
}
.drawflow .drawflow-node .input { background: var(--secondary) !important; }
.drawflow .drawflow-node .output { background: var(--primary) !important; }

/* Connection line */
.drawflow .connection .main-path {
    stroke: var(--primary) !important;
    stroke-width: 2.5px !important;
}

/* Node status indicators for execution */
.df-node-container.exec-success .df-node-header { background: #ecfdf5 !important; }
.df-node-container.exec-error .df-node-header { background: #fef2f2 !important; }
.df-node-container.exec-running .df-node-header { background: #eff6ff !important; }
.df-node-container.exec-skipped .df-node-header { background: #f9fafb !important; }

/* ========== PROPERTIES FORM ========== */
.prop-group { margin-bottom: 16px; }
.prop-group-title {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--gray-400);
    margin-bottom: 8px;
}
.prop-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--gray-600);
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.prop-control {
    font-size: 0.85rem;
    border-radius: 8px;
    border: 1px solid var(--gray-200);
    padding: 8px 12px;
    width: 100%;
    transition: border-color 0.2s;
}
.prop-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 2px rgba(99,102,241,0.15);
    outline: none;
}
textarea.prop-control { min-height: 100px; font-family: 'Consolas', monospace; font-size: 0.8rem; }
select.prop-control { padding-right: 30px; }
.prop-help { font-size: 0.7rem; color: var(--gray-400); margin-top: 2px; }

.prop-delete-btn {
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid var(--gray-100);
}

/* ========== PANEL OVERLAY (mobile) ========== */
.panel-overlay {
    display: none;
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.3);
    z-index: 14;
    cursor: pointer;
}
.panel-overlay.active { display: block; }

/* ========== RESPONSIVE: DESKTOP >1200px ========== */
@media (min-width: 1201px) {
    .builder-left-panel { width: 260px; }
    .builder-right-panel { width: 340px; }
    .pipeline-name-input { font-size: 1.1rem; }
}

/* ========== RESPONSIVE: TABLET LANDSCAPE 993-1200px ========== */
@media (min-width: 993px) and (max-width: 1200px) {
    .builder-left-panel { width: 220px; }
    .builder-right-panel { width: 300px; }
    .palette-node-desc { display: none; }
    .palette-node { padding: 6px 8px; }
    .palette-node-icon { width: 30px; height: 30px; font-size: 0.85rem; }
}

/* ========== RESPONSIVE: TABLET PORTRAIT 769-992px ========== */
@media (min-width: 769px) and (max-width: 992px) {
    .builder-left-panel {
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 240px;
        z-index: 15;
        box-shadow: 4px 0 20px rgba(0,0,0,0.1);
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .builder-left-panel.open { transform: translateX(0); }
    .builder-left-panel.collapsed { transform: translateX(-100%); }
    .builder-right-panel {
        position: absolute;
        right: 0; top: 0; bottom: 0;
        width: 320px;
        z-index: 15;
        box-shadow: -4px 0 20px rgba(0,0,0,0.1);
        transform: translateX(100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .builder-right-panel.open { transform: translateX(0); }
    .bottom-panel-body.expanded { height: 160px; }
}

/* ========== RESPONSIVE: MOBILE <=768px ========== */
@media (max-width: 768px) {
    .builder-container { height: calc(100vh - var(--topbar-height, 56px)); margin: -1rem; }
    .toolbar-row-main { padding: 4px 8px; min-height: 44px; gap: 6px; }
    .pipeline-name-input { font-size: 0.85rem; padding: 3px 6px; max-width: 180px; }

    .builder-left-panel {
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 250px;
        max-width: 80vw;
        z-index: 15;
        box-shadow: 4px 0 25px rgba(0,0,0,0.15);
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .builder-left-panel.open { transform: translateX(0); }
    .builder-left-panel.collapsed { transform: translateX(-100%); }

    .builder-right-panel {
        position: absolute;
        right: 0; top: 0; bottom: 0;
        width: 100%;
        max-width: 100vw;
        z-index: 15;
        box-shadow: -4px 0 25px rgba(0,0,0,0.15);
        transform: translateX(100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .builder-right-panel.open { transform: translateX(0); }

    .bottom-panel-body.expanded { height: 140px; }
    .bottom-panel-header { padding: 6px 12px; font-size: 0.8rem; }
    .exec-logs { padding: 6px 12px; font-size: 0.75rem; }

    .drawflow .drawflow-node { min-width: 160px; }
    .df-node-header { padding: 8px 10px; gap: 8px; }
    .df-node-icon { width: 26px; height: 26px; font-size: 0.8rem; }
    .df-node-title { font-size: 0.8rem; }
    .df-node-body { padding: 6px 10px 8px; font-size: 0.7rem; }

    .prop-control { font-size: 0.8rem; padding: 6px 10px; }
    textarea.prop-control { min-height: 80px; }
    .panel-body { padding: 12px; }
}

/* ========== RESPONSIVE: VERY SMALL <=480px ========== */
@media (max-width: 480px) {
    .pipeline-name-input { max-width: 120px; font-size: 0.8rem; }
    .toolbar-row-main { padding: 4px 6px; min-height: 40px; gap: 4px; }
    .toolbar-row-main .btn-sm { padding: 0.2rem 0.4rem; font-size: 0.75rem; }
    .builder-left-panel { width: 220px; }
    .palette-node-icon { width: 28px; height: 28px; }
    .palette-node-name { font-size: 0.8rem; }
    .palette-node-desc { font-size: 0.65rem; }
}

/* ========== TOUCH IMPROVEMENTS ========== */
@media (hover: none) and (pointer: coarse) {
    .palette-node { padding: 10px 12px; min-height: 44px; }
    .btn-sm { min-height: 36px; min-width: 36px; }
    .prop-control { min-height: 40px; }
    .mode-btn { padding: 8px 14px; }
    .bottom-panel-header { min-height: 44px; }
}

/* ========== MINIMAP ========== */
.drawflow .drawflow-minimap { display: none; }
#minimap {
    position: absolute;
    bottom: 16px;
    right: 16px;
    width: 180px;
    height: 120px;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    z-index: 5;
    overflow: hidden;
}

/* ========== TABLE BROWSER ========== */
.table-browser { max-height: 200px; overflow-y: auto; border: 1px solid var(--gray-200); border-radius: 8px; margin-top: 6px; }
.table-browser-item {
    padding: 6px 10px; font-size: 0.8rem; cursor: pointer;
    display: flex; align-items: center; gap: 6px;
    border-bottom: 1px solid var(--gray-100); transition: background 0.15s;
}
.table-browser-item:hover { background: #eff6ff; }
.table-browser-item:last-child { border-bottom: none; }
.table-browser-item i { color: var(--gray-400); font-size: 0.75rem; }
.table-browser-item .table-name { font-weight: 600; color: #1a202c; }
.table-browser-item .schema-name { color: var(--gray-400); font-size: 0.7rem; }

.column-list { max-height: 150px; overflow-y: auto; background: var(--gray-100); border-radius: 8px; padding: 6px; margin-top: 6px; }
.column-item {
    padding: 3px 8px; font-size: 0.75rem; display: flex; justify-content: space-between;
    cursor: pointer; border-radius: 4px; transition: background 0.15s;
}
.column-item:hover { background: white; }
.column-item .col-name { font-weight: 600; color: #1a202c; }
.column-item .col-type { color: var(--gray-400); font-size: 0.7rem; }

.api-select-card {
    padding: 8px 10px; border: 1px solid var(--gray-200); border-radius: 8px;
    cursor: pointer; transition: all 0.2s; margin-bottom: 6px;
    display: flex; align-items: center; gap: 8px;
}
.api-select-card:hover { border-color: var(--primary); background: #f5f3ff; }
.api-select-card.selected { border-color: var(--primary); background: #eff6ff; }
.api-method-badge {
    font-size: 0.65rem; font-weight: 700; padding: 2px 6px; border-radius: 4px;
    letter-spacing: 0.5px;
}
.api-method-badge.GET { background: #d1fae5; color: #059669; }
.api-method-badge.POST { background: #dbeafe; color: #2563eb; }
.api-method-badge.PUT { background: #fef3c7; color: #d97706; }
.api-method-badge.DELETE { background: #fecaca; color: #dc2626; }
.api-method-badge.PATCH { background: #ede9fe; color: #7c3aed; }

.cron-helper { background: var(--gray-100); border-radius: 8px; padding: 8px 10px; margin-top: 6px; }
.cron-preset {
    display: inline-block; padding: 3px 8px; font-size: 0.7rem;
    border-radius: 12px; background: white; border: 1px solid var(--gray-200);
    cursor: pointer; margin: 2px; transition: all 0.15s;
}
.cron-preset:hover { border-color: var(--primary); color: var(--primary); }
</style>
STYLES;

$extraScripts = <<<SCRIPTS
<script src="https://cdn.jsdelivr.net/gh/jerosoler/Drawflow@0.0.60/dist/drawflow.min.js"></script>
<script>
// ============================================================
// PIPELINE BUILDER - Main Application
// ============================================================

const csrfToken = document.getElementById('csrfToken').value;
const pipelineIdField = document.getElementById('pipelineId');
let pipelineId = pipelineIdField.value ? parseInt(pipelineIdField.value) : null;

let editor = null;
let currentMode = 'nocode';
let selectedNodeId = null;
let connections = []; // DB connections list
let apisExternas = []; // External APIs
let eventosApi = []; // API events
let connectionTables = {}; // Cache: connId -> tables
let pipelineDescription = '';
let isDirty = false;
let zoom = 1;

// ============================================================
// NODE TYPE DEFINITIONS
// ============================================================
const NODE_TYPES = {
    trigger:      { label: 'Trigger',       icon: 'bi-play-circle',       color: '#10b981', inputs: 0, outputs: 1, category: 'io' },
    end:          { label: 'End',           icon: 'bi-stop-circle',       color: '#dc2626', inputs: 1, outputs: 0, category: 'io' },
    sql_query:    { label: 'SQL Query',     icon: 'bi-database',          color: '#3b82f6', inputs: 1, outputs: 1, category: 'data' },
    http_request: { label: 'HTTP Request',  icon: 'bi-cloud-arrow-up',    color: '#8b5cf6', inputs: 1, outputs: 1, category: 'data' },
    transform:    { label: 'Transform',     icon: 'bi-arrow-left-right',  color: '#f59e0b', inputs: 1, outputs: 1, category: 'data' },
    condition:    { label: 'Condition',     icon: 'bi-signpost-split',    color: '#eab308', inputs: 1, outputs: 2, category: 'logic' },
    loop:         { label: 'Loop',          icon: 'bi-arrow-repeat',      color: '#06b6d4', inputs: 1, outputs: 1, category: 'logic' },
    delay:        { label: 'Delay',         icon: 'bi-clock',             color: '#9ca3af', inputs: 1, outputs: 1, category: 'logic' },
    script:       { label: 'Script',        icon: 'bi-code-slash',        color: '#ef4444', inputs: 1, outputs: 1, category: 'action' },
    set_variable: { label: 'Set Variable',  icon: 'bi-braces',            color: '#14b8a6', inputs: 1, outputs: 1, category: 'action' },
    email:        { label: 'Email',         icon: 'bi-envelope',          color: '#ec4899', inputs: 1, outputs: 1, category: 'action' },
    log_node:     { label: 'Log',           icon: 'bi-journal-text',      color: '#6b7280', inputs: 1, outputs: 1, category: 'action' },
    api_call:     { label: 'API Call',      icon: 'bi-cloud-check',       color: '#7c3aed', inputs: 1, outputs: 2, category: 'io' }
};

// ============================================================
// INITIALIZATION
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    initDrawflow();
    initDragDrop();
    loadConnections();
    loadApisExternas();
    loadEventosApi();
    
    if (pipelineId) {
        loadPipeline(pipelineId);
    }

    // Search filter for nodes
    document.getElementById('nodeSearch').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.palette-node').forEach(function(el) {
            const name = el.querySelector('.palette-node-name').textContent.toLowerCase();
            const desc = el.querySelector('.palette-node-desc').textContent.toLowerCase();
            el.style.display = (name.includes(q) || desc.includes(q)) ? '' : 'none';
        });
    });

    // Auto-save on name change
    document.getElementById('pipelineName').addEventListener('change', function() { markDirty(); });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            savePipeline();
        }
        if (e.key === 'Delete' && selectedNodeId && !e.target.matches('input, textarea, select')) {
            editor.removeNodeId('node-' + selectedNodeId);
        }
    });
});

function initDrawflow() {
    const container = document.getElementById('drawflow');
    editor = new Drawflow(container);
    editor.reroute = true;
    editor.reroute_fix_curvature = true;
    editor.force_first_input = false;

    editor.start();
    
    // Events
    editor.on('nodeCreated', function(id) {
        updateEmptyOverlay();
        markDirty();
    });

    editor.on('nodeRemoved', function(id) {
        if (selectedNodeId == id) {
            closeProperties();
        }
        updateEmptyOverlay();
        markDirty();
    });

    editor.on('nodeSelected', function(id) {
        selectedNodeId = id;
        showProperties(id);
    });

    editor.on('nodeUnselected', function(id) {
        // Keep properties open for the previously selected node
    });

    editor.on('connectionCreated', function(conn) {
        markDirty();
    });

    editor.on('connectionRemoved', function(conn) {
        markDirty();
    });

    editor.on('nodeMoved', function(id) {
        markDirty();
    });

    editor.on('zoom', function(z) {
        zoom = z;
        document.getElementById('zoomLevel').textContent = Math.round(z * 100) + '%';
        var mobileZoom = document.getElementById('zoomLevelMobile');
        if (mobileZoom) mobileZoom.textContent = Math.round(z * 100) + '%';
    });

    // Double-click palette node to add
    document.querySelectorAll('.palette-node').forEach(function(el) {
        el.addEventListener('dblclick', function() {
            const type = this.dataset.type;
            addNode(type, 350, 200);
        });
    });
}

function initDragDrop() {
    const canvas = document.getElementById('drawflow');

    document.querySelectorAll('.palette-node').forEach(function(el) {
        el.addEventListener('dragstart', function(e) {
            e.dataTransfer.setData('nodeType', this.dataset.type);
            e.dataTransfer.effectAllowed = 'copy';
        });
    });

    canvas.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'copy';
    });

    canvas.addEventListener('drop', function(e) {
        e.preventDefault();
        const type = e.dataTransfer.getData('nodeType');
        if (!type || !NODE_TYPES[type]) return;

        // Calculate position relative to canvas
        const rect = canvas.getBoundingClientRect();
        const x = (e.clientX - rect.left) / zoom;
        const y = (e.clientY - rect.top) / zoom;
        
        addNode(type, x, y);
    });
}

// ============================================================
// NODE MANAGEMENT
// ============================================================
function addNode(type, posX, posY) {
    const def = NODE_TYPES[type];
    if (!def) return;

    const nodeData = { type: type, label: def.label };
    const html = buildNodeHtml(type, nodeData);
    
    const numOutputs = def.outputs;
    const numInputs = def.inputs;

    editor.addNode(
        type,                // name
        numInputs,           // inputs
        numOutputs,          // outputs
        posX,                // pos_x
        posY,                // pos_y
        type,                // class
        nodeData,            // data
        html                 // html
    );

    updateEmptyOverlay();
}

function buildNodeHtml(type, data) {
    const def = NODE_TYPES[type];
    const label = escapeHtml(data.label || def.label);
    let subtitle = getNodeSubtitle(type, data);
    let bodyContent = getNodeBody(type, data);
    
    let headerBg = 'background: ' + def.color + '11;';
    
    let html = '<div class="df-node-container" data-type="' + type + '">';
    html += '<div class="df-node-header" style="' + headerBg + '">';
    html += '<div class="df-node-icon" style="background:' + def.color + '"><i class="bi ' + def.icon + '"></i></div>';
    html += '<div>';
    html += '<div class="df-node-title">' + label + '</div>';
    if (subtitle) html += '<div class="df-node-subtitle">' + escapeHtml(subtitle) + '</div>';
    html += '</div></div>';
    
    if (bodyContent) {
        html += '<div class="df-node-body">' + bodyContent + '</div>';
    }
    html += '</div>';
    
    return html;
}

function getNodeSubtitle(type, data) {
    switch(type) {
        case 'trigger': return data.trigger_type || 'Manual';
        case 'sql_query': return data.connection_name || 'Selecione conexão';
        case 'http_request': return data.method ? (data.method + ' ' + (data.url || '').substring(0, 30)) : 'Configurar URL';
        case 'transform': return data.transform_type || 'Configurar';
        case 'condition': return data.operator || 'Configurar condição';
        case 'script': return data.script_language || 'Expressão';
        case 'set_variable': return data.variable_name || 'Configurar';
        case 'email': return data.email_to || 'Configurar';
        case 'delay': return (data.delay_seconds || '1') + 's';
        case 'api_call':
            var selApi = data.api_id ? apisExternas.find(function(a){return a.id==data.api_id}) : null;
            return selApi ? selApi.nome : 'Selecione API';
        default: return '';
    }
}

function getNodeBody(type, data) {
    switch(type) {
        case 'sql_query':
            if (data.sql_query) return '<code>' + escapeHtml(data.sql_query.substring(0, 60)) + (data.sql_query.length > 60 ? '...' : '') + '</code>';
            return '<span class="text-muted">SQL não configurado</span>';
        case 'condition':
            if (data.left_operand) return escapeHtml(data.left_operand) + ' ' + escapeHtml(data.operator || '==') + ' ' + escapeHtml(data.right_operand || '');
            return '<span class="text-muted fst-italic">Configurar condição</span>';
        case 'script':
            if (data.script_code) return '<code>' + escapeHtml(data.script_code.substring(0, 60)) + '</code>';
            return '';
        case 'http_request':
            if (data.url) return '<code>' + escapeHtml(data.url.substring(0, 50)) + '</code>';
            return '';
        case 'api_call':
            if (data.jsonpath) return '<code>' + escapeHtml(data.jsonpath) + '</code> ' + escapeHtml(data.condition_op || '==') + ' ' + escapeHtml(data.condition_value || '');
            return '<span class="text-muted">Configurar condição</span>';
        default: return '';
    }
}

function updateNodeVisual(nodeId) {
    const nodeData = editor.getNodeFromId(nodeId);
    if (!nodeData) return;
    
    const type = nodeData.data.type || nodeData.name;
    const html = buildNodeHtml(type, nodeData.data);
    
    const el = document.querySelector('#node-' + nodeId + ' .drawflow_content_node');
    if (el) el.innerHTML = html;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
}

// ============================================================
// PROPERTIES PANEL
// ============================================================
function showProperties(nodeId) {
    selectedNodeId = nodeId;
    const node = editor.getNodeFromId(nodeId);
    if (!node) return;

    const panel = document.getElementById('rightPanel');
    panel.classList.remove('d-none');
    if (isMobileOrTablet()) {
        document.getElementById('leftPanel').classList.remove('open');
        panel.classList.add('open');
        document.getElementById('panelOverlay').classList.add('active');
    }
    
    const type = node.data.type || node.name;
    const def = NODE_TYPES[type] || {};
    
    document.getElementById('propTitle').innerHTML = 
        '<i class="bi ' + (def.icon || 'bi-gear') + ' me-2" style="color:' + (def.color || '#666') + '"></i>' + 
        escapeHtml(def.label || type);

    const body = document.getElementById('propBody');
    let html = '';
    
    // Common: Label
    html += '<div class="prop-group">';
    html += '<div class="prop-group-title">Geral</div>';
    html += '<div class="mb-3">';
    html += '<label class="prop-label">Nome do Nó</label>';
    html += '<input type="text" class="prop-control" id="propLabel" value="' + escapeHtml(node.data.label || def.label) + '" onchange="updateNodeProp(' + nodeId + ', \'label\', this.value)">';
    html += '</div>';
    html += '<div class="mb-3">';
    html += '<label class="prop-label">Parar em caso de erro</label>';
    html += '<select class="prop-control" onchange="updateNodeProp(' + nodeId + ', \'stop_on_error\', this.value)">';
    html += '<option value="true"' + (node.data.stop_on_error !== 'false' ? ' selected' : '') + '>Sim</option>';
    html += '<option value="false"' + (node.data.stop_on_error === 'false' ? ' selected' : '') + '>Não</option>';
    html += '</select>';
    html += '</div>';
    html += '</div>';

    // Type-specific properties
    html += getTypeProperties(type, node.data, nodeId);
    
    // Output variable (for data-producing nodes)
    if (['sql_query', 'http_request', 'transform', 'loop', 'script', 'set_variable'].includes(type)) {
        html += '<div class="prop-group">';
        html += '<div class="prop-group-title">Saída</div>';
        html += '<div class="mb-3">';
        html += '<label class="prop-label"><i class="bi bi-box-arrow-right me-1"></i>Variável de Saída</label>';
        html += '<input type="text" class="prop-control" placeholder="ex: resultado" value="' + escapeHtml(node.data.output_variable || '') + '" onchange="updateNodeProp(' + nodeId + ', \'output_variable\', this.value)">';
        html += '<div class="prop-help">Nome da variável para armazenar o resultado. Use {{nome}} para referenciar.</div>';
        html += '</div></div>';
    }
    
    // Delete button
    html += '<div class="prop-delete-btn">';
    html += '<button class="btn btn-outline-danger w-100" onclick="deleteSelectedNode(' + nodeId + ')">';
    html += '<i class="bi bi-trash me-2"></i>Remover Nó</button>';
    html += '</div>';
    
    body.innerHTML = html;
}

function getTypeProperties(type, data, nodeId) {
    let h = '<div class="prop-group"><div class="prop-group-title">Configuração</div>';
    
    switch(type) {
        case 'trigger':
            h += propSelect(nodeId, 'trigger_type', 'Tipo de Trigger', data.trigger_type || 'manual', [
                {v:'manual', l:'Manual'}, {v:'cron', l:'Agendamento (CRON)'}, {v:'api_event', l:'Evento de API'}, {v:'webhook', l:'Webhook'}
            ]);
            if (data.trigger_type === 'cron') {
                h += propInput(nodeId, 'cron_expression', 'Expressão Cron', data.cron_expression || '', '0 8 * * *');
                h += '<div class="cron-helper"><div class="small fw-bold mb-1"><i class="bi bi-clock me-1"></i>Presets rápidos:</div>';
                h += '<span class="cron-preset" onclick="setCronPreset('+nodeId+',\'* * * * *\')">A cada minuto</span>';
                h += '<span class="cron-preset" onclick="setCronPreset('+nodeId+',\'*/5 * * * *\')">A cada 5 min</span>';
                h += '<span class="cron-preset" onclick="setCronPreset('+nodeId+',\'0 * * * *\')">A cada hora</span>';
                h += '<span class="cron-preset" onclick="setCronPreset('+nodeId+',\'0 8 * * *\')">Diário 08h</span>';
                h += '<span class="cron-preset" onclick="setCronPreset('+nodeId+',\'0 8 * * 1\')">Seg 08h</span>';
                h += '<span class="cron-preset" onclick="setCronPreset('+nodeId+',\'0 0 1 * *\')">Mensal</span>';
                h += '</div>';
            }
            if (data.trigger_type === 'api_event') {
                if (eventosApi.length > 0) {
                    h += propSelect(nodeId, 'evento_api_id', 'Evento de API', data.evento_api_id || '', 
                        [{v:'', l:'Selecione um evento...'}].concat(eventosApi.map(function(ev) {
                            return {v: ev.id, l: ev.nome + ' (' + (ev.api_nome||'API') + ')'};
                        }))
                    );
                    var selEvt = data.evento_api_id ? eventosApi.find(function(e){return e.id==data.evento_api_id}) : null;
                    if (selEvt) {
                        h += '<div class="cron-helper"><div class="small"><strong>JSONPath:</strong> '+escapeHtml(selEvt.jsonpath||'-')+'</div>';
                        h += '<div class="small"><strong>Operador:</strong> '+escapeHtml(selEvt.operador||'-')+' '+escapeHtml(selEvt.valor_esperado||'')+'</div></div>';
                    }
                } else {
                    h += '<div class="alert alert-info small py-2" style="border-radius:8px"><i class="bi bi-info-circle me-1"></i>Nenhum evento de API cadastrado. <a href="'+baseUrl+'/eventos-api" target="_blank">Configurar</a></div>';
                }
            }
            if (data.trigger_type === 'webhook') {
                var webhookUrl = baseUrl + '/pipelines/webhook/' + (pipelineId || 'NOVO');
                h += '<div class="mb-3"><label class="prop-label">URL do Webhook</label>';
                h += '<div class="input-group input-group-sm"><input type="text" class="prop-control font-monospace" value="'+escapeHtml(webhookUrl)+'" readonly id="webhookUrl_'+nodeId+'">';
                h += '<button class="btn btn-outline-secondary btn-sm" onclick="navigator.clipboard.writeText(document.getElementById(\'webhookUrl_'+nodeId+'\').value);Swal.fire({icon:\'success\',title:\'Copiado!\',toast:true,position:\'top-end\',timer:2000,showConfirmButton:false})" title="Copiar"><i class="bi bi-clipboard"></i></button></div>';
                h += '<div class="prop-help mt-1"><i class="bi bi-info-circle me-1"></i>Envie um POST para esta URL para disparar o pipeline</div></div>';
                h += propInput(nodeId, 'webhook_secret', 'Secret (opcional)', data.webhook_secret || '', 'meu-secret-seguro');
                h += '<div class="prop-help"><i class="bi bi-shield-lock me-1"></i>Se definido, valida o header X-Webhook-Secret</div>';
            }
            break;

        case 'sql_query':
            h += propSelect(nodeId, 'connection_id', 'Conexão', data.connection_id || '', 
                [{v:'', l:'Selecione...'}].concat(connections.map(function(c) { 
                    return {v: c.id, l: c.nome_conexao + ' (' + c.tipo_banco + ')'}; 
                }))
            );
            if (data.connection_id) {
                h += '<div class="mb-2"><button type="button" class="btn btn-sm btn-outline-primary w-100" onclick="showTableBrowser('+escapeHtml(data.connection_id)+','+nodeId+')">';
                h += '<i class="bi bi-table me-1"></i>Explorar Tabelas</button></div>';
                h += '<div id="tableBrowser_'+nodeId+'" style="display:none"></div>';
            }
            h += propTextarea(nodeId, 'sql_query', 'SQL Query', data.sql_query || '', 'SELECT * FROM tabela LIMIT 10');
            h += propInput(nodeId, 'max_rows', 'Máximo de Linhas', data.max_rows || '1000', '1000');
            h += propInput(nodeId, 'timeout', 'Timeout (s)', data.timeout || '30', '30');
            break;

        case 'http_request':
            if (apisExternas.length > 0) {
                h += '<div class="mb-3"><label class="prop-label"><i class="bi bi-cloud-lightning me-1"></i>Importar de API Cadastrada</label>';
                h += '<select class="prop-control" onchange="if(this.value)applyApiExterna('+nodeId+',this.value);this.value=\'\'">';
                h += '<option value="">Selecione para importar...</option>';
                apisExternas.forEach(function(api) {
                    h += '<option value="'+api.id+'">'+escapeHtml(api.nome)+' ('+escapeHtml(api.metodo||'GET')+' '+escapeHtml((api.url||'').substring(0,40))+')</option>';
                });
                h += '</select></div>';
            }
            h += propSelect(nodeId, 'method', 'Método', data.method || 'GET', [
                {v:'GET',l:'GET'}, {v:'POST',l:'POST'}, {v:'PUT',l:'PUT'}, {v:'DELETE',l:'DELETE'}, {v:'PATCH',l:'PATCH'}
            ]);
            h += propInput(nodeId, 'url', 'URL', data.url || '', 'https://api.exemplo.com/dados');
            h += propSelect(nodeId, 'auth_type', 'Autenticação', data.auth_type || 'none', [
                {v:'none',l:'Nenhuma'}, {v:'bearer',l:'Bearer Token'}, {v:'basic',l:'Basic Auth'}
            ]);
            if (data.auth_type === 'bearer') {
                h += propInput(nodeId, 'auth_token', 'Token', data.auth_token || '', '');
            } else if (data.auth_type === 'basic') {
                h += propInput(nodeId, 'auth_user', 'Usuário', data.auth_user || '', '');
                h += propInput(nodeId, 'auth_pass', 'Senha', data.auth_pass || '', '');
            }
            h += propTextarea(nodeId, 'headers', 'Headers (JSON)', data.headers || '', '{"Content-Type":"application/json"}');
            h += propTextarea(nodeId, 'body', 'Body', data.body || '', '');
            h += propInput(nodeId, 'timeout', 'Timeout (s)', data.timeout || '30', '30');
            break;

        case 'transform':
            h += propSelect(nodeId, 'transform_type', 'Tipo', data.transform_type || 'map', [
                {v:'map',l:'Mapear Campos'}, {v:'filter',l:'Filtrar'}, {v:'sort',l:'Ordenar'}, 
                {v:'limit',l:'Limitar'}, {v:'aggregate',l:'Agregar'}
            ]);
            h += propInput(nodeId, 'input_variable', 'Variável de Entrada', data.input_variable || '', 'resultado');

            if (data.transform_type === 'map') {
                h += propTextarea(nodeId, 'field_mapping', 'Mapeamento (JSON)', data.field_mapping || '', '{"campo_origem":"campo_destino"}');
            } else if (data.transform_type === 'filter') {
                h += propInput(nodeId, 'filter_field', 'Campo', data.filter_field || '', '');
                h += propSelect(nodeId, 'filter_operator', 'Operador', data.filter_operator || 'equals', [
                    {v:'equals',l:'Igual'}, {v:'not_equals',l:'Diferente'}, {v:'contains',l:'Contém'},
                    {v:'greater',l:'Maior que'}, {v:'less',l:'Menor que'}, {v:'is_null',l:'É nulo'}, {v:'not_null',l:'Não é nulo'}
                ]);
                h += propInput(nodeId, 'filter_value', 'Valor', data.filter_value || '', '');
            } else if (data.transform_type === 'sort') {
                h += propInput(nodeId, 'sort_field', 'Campo', data.sort_field || '', '');
                h += propSelect(nodeId, 'sort_direction', 'Direção', data.sort_direction || 'asc', [
                    {v:'asc',l:'Ascendente'}, {v:'desc',l:'Descendente'}
                ]);
            } else if (data.transform_type === 'limit') {
                h += propInput(nodeId, 'limit_count', 'Quantidade', data.limit_count || '10', '10');
                h += propInput(nodeId, 'limit_offset', 'Pular (offset)', data.limit_offset || '0', '0');
            } else if (data.transform_type === 'aggregate') {
                h += propInput(nodeId, 'agg_field', 'Campo', data.agg_field || '', '');
                h += propSelect(nodeId, 'agg_operation', 'Operação', data.agg_operation || 'count', [
                    {v:'count',l:'Contar'}, {v:'sum',l:'Somar'}, {v:'avg',l:'Média'}, {v:'min',l:'Mínimo'}, {v:'max',l:'Máximo'}
                ]);
            }
            break;

        case 'condition':
            h += propInput(nodeId, 'left_operand', 'Valor Esquerdo', data.left_operand || '', '{{variavel}}');
            h += propSelect(nodeId, 'operator', 'Operador', data.operator || '==', [
                {v:'==',l:'Igual (==)'}, {v:'!=',l:'Diferente (!=)'}, {v:'>',l:'Maior (>)'},
                {v:'<',l:'Menor (<)'}, {v:'>=',l:'Maior ou igual (>=)'}, {v:'<=',l:'Menor ou igual (<=)'},
                {v:'contains',l:'Contém'}, {v:'starts_with',l:'Começa com'}, {v:'ends_with',l:'Termina com'},
                {v:'is_empty',l:'Está vazio'}, {v:'not_empty',l:'Não está vazio'}, {v:'matches',l:'Regex'}
            ]);
            h += propInput(nodeId, 'right_operand', 'Valor Direito', data.right_operand || '', 'valor');
            h += '<div class="prop-help mt-2"><i class="bi bi-info-circle me-1"></i>Output 1 = Verdadeiro, Output 2 = Falso</div>';
            break;

        case 'loop':
            h += propInput(nodeId, 'input_variable', 'Variável de Lista', data.input_variable || '', 'resultado');
            h += propInput(nodeId, 'iterator_variable', 'Nome do Iterador', data.iterator_variable || 'item', 'item');
            h += propInput(nodeId, 'max_iterations', 'Máx. Iterações', data.max_iterations || '100', '100');
            break;

        case 'script':
            h += propSelect(nodeId, 'script_language', 'Tipo', data.script_language || 'expression', [
                {v:'expression',l:'Expressão PHP'}, {v:'sql',l:'SQL (usar conexão)'}
            ]);
            if (data.script_language === 'sql') {
                h += propSelect(nodeId, 'connection_id', 'Conexão', data.connection_id || '', 
                    [{v:'', l:'Selecione...'}].concat(connections.map(function(c) { 
                        return {v: c.id, l: c.nome_conexao + ' (' + c.tipo_banco + ')'}; 
                    }))
                );
            }
            h += propTextarea(nodeId, 'script_code', 'Código', data.script_code || '', 'count({{resultado}})');
            h += '<div class="prop-help"><i class="bi bi-shield-check me-1"></i>Use {{variavel}} para acessar valores. Funções perigosas são bloqueadas.</div>';
            break;

        case 'set_variable':
            h += propInput(nodeId, 'variable_name', 'Nome da Variável', data.variable_name || '', 'minhaVar');
            h += propInput(nodeId, 'variable_value', 'Valor', data.variable_value || '', '{{resultado}}');
            h += '<div class="prop-help">Use {{variavel}} para referenciar outras variáveis.</div>';
            break;

        case 'email':
            h += propInput(nodeId, 'email_to', 'Destinatário', data.email_to || '', 'user@example.com');
            h += propInput(nodeId, 'email_subject', 'Assunto', data.email_subject || '', 'Pipeline concluído');
            h += propTextarea(nodeId, 'email_body', 'Corpo do E-mail', data.email_body || '', '<h1>Resultado</h1><p>{{resultado}}</p>');
            break;

        case 'log_node':
            h += propTextarea(nodeId, 'message', 'Mensagem', data.message || '', 'Total: {{resultado}}');
            h += propSelect(nodeId, 'level', 'Nível', data.level || 'info', [
                {v:'info',l:'Info'}, {v:'warning',l:'Warning'}, {v:'error',l:'Error'}
            ]);
            break;

        case 'delay':
            h += propInput(nodeId, 'delay_seconds', 'Segundos', data.delay_seconds || '1', '5');
            h += '<div class="prop-help">Máximo: 30 segundos</div>';
            break;

        case 'end':
            h += '<p class="text-muted small">Nó final do pipeline. Sem configurações adicionais.</p>';
            break;

        case 'api_call':
            if (apisExternas.length > 0) {
                h += propSelect(nodeId, 'api_id', 'API Externa', data.api_id || '', 
                    [{v:'', l:'Selecione uma API...'}].concat(apisExternas.map(function(api) {
                        return {v: api.id, l: api.nome + ' (' + (api.metodo||'GET') + ')'};
                    }))
                );
                var selApiCall = data.api_id ? apisExternas.find(function(a){return a.id==data.api_id}) : null;
                if (selApiCall) {
                    h += '<div class="cron-helper mb-2"><div class="small"><strong>URL:</strong> '+escapeHtml((selApiCall.url||'').substring(0,60))+'</div>';
                    h += '<div class="small"><strong>Método:</strong> '+escapeHtml(selApiCall.metodo||'GET')+'</div></div>';
                }
            } else {
                h += '<div class="alert alert-info small py-2" style="border-radius:8px"><i class="bi bi-info-circle me-1"></i>Nenhuma API cadastrada. <a href="'+baseUrl+'/apis-externas" target="_blank">Cadastrar</a></div>';
            }
            h += '</div><div class="prop-group"><div class="prop-group-title">Extração JSONPath</div>';
            h += propInput(nodeId, 'jsonpath', 'JSONPath', data.jsonpath || '', '$.data.status');
            h += '<div class="prop-help mb-2"><i class="bi bi-braces me-1"></i>Ex: $.data.status, $.results[0].value</div>';
            h += '</div><div class="prop-group"><div class="prop-group-title">Condição (opcional)</div>';
            h += propSelect(nodeId, 'condition_op', 'Operador', data.condition_op || 'equals', [
                {v:'equals',l:'Igual a'}, {v:'not_equals',l:'Diferente de'}, {v:'contains',l:'Contém'},
                {v:'greater_than',l:'Maior que'}, {v:'less_than',l:'Menor que'},
                {v:'is_true',l:'É verdadeiro'}, {v:'is_false',l:'É falso'},
                {v:'is_null',l:'É nulo'}, {v:'is_not_null',l:'Não é nulo'}
            ]);
            h += propInput(nodeId, 'condition_value', 'Valor Esperado', data.condition_value || '', 'true');
            h += propInput(nodeId, 'output_variable', 'Salvar resultado em', data.output_variable || 'api_result', 'api_result');
            h += '<div class="prop-help mt-2"><i class="bi bi-info-circle me-1"></i>Output 1 = Condição atendida, Output 2 = Não atendida</div>';
            break;
    }
    
    h += '</div>';
    return h;
}

// Property field builders
function propInput(nodeId, field, label, value, placeholder) {
    return '<div class="mb-3"><label class="prop-label">' + escapeHtml(label) + '</label>' +
           '<input type="text" class="prop-control" value="' + escapeHtml(value) + '" placeholder="' + escapeHtml(placeholder) + '" ' +
           'onchange="updateNodeProp(' + nodeId + ', \'' + field + '\', this.value)"></div>';
}

function propTextarea(nodeId, field, label, value, placeholder) {
    return '<div class="mb-3"><label class="prop-label">' + escapeHtml(label) + '</label>' +
           '<textarea class="prop-control" placeholder="' + escapeHtml(placeholder) + '" ' +
           'onchange="updateNodeProp(' + nodeId + ', \'' + field + '\', this.value)">' + escapeHtml(value) + '</textarea></div>';
}

function propSelect(nodeId, field, label, selected, options) {
    let h = '<div class="mb-3"><label class="prop-label">' + escapeHtml(label) + '</label>';
    h += '<select class="prop-control" onchange="updateNodeProp(' + nodeId + ', \'' + field + '\', this.value); showProperties(' + nodeId + ')">';
    options.forEach(function(opt) {
        h += '<option value="' + escapeHtml(String(opt.v)) + '"' + (String(selected) === String(opt.v) ? ' selected' : '') + '>' + escapeHtml(opt.l) + '</option>';
    });
    h += '</select></div>';
    return h;
}

function updateNodeProp(nodeId, field, value) {
    const node = editor.getNodeFromId(nodeId);
    if (!node) return;
    node.data[field] = value;
    editor.updateNodeDataFromId(nodeId, node.data);
    updateNodeVisual(nodeId);
    markDirty();

    // Update connection name for sql_query nodes
    if (field === 'connection_id') {
        const conn = connections.find(function(c) { return String(c.id) === String(value); });
        if (conn) {
            node.data.connection_name = conn.nome_conexao;
            editor.updateNodeDataFromId(nodeId, node.data);
            updateNodeVisual(nodeId);
        }
    }
}

function closeProperties() {
    selectedNodeId = null;
    var panel = document.getElementById('rightPanel');
    panel.classList.add('d-none');
    panel.classList.remove('open');
    if (isMobileOrTablet()) {
        document.getElementById('panelOverlay').classList.remove('active');
    }
}

function deleteSelectedNode(nodeId) {
    editor.removeNodeId('node-' + nodeId);
    closeProperties();
}

// ============================================================
// MODE SWITCHING
// ============================================================
function switchMode(mode) {
    currentMode = mode;
    document.querySelectorAll('.mode-btn').forEach(function(btn) {
        btn.classList.toggle('active', btn.dataset.mode === mode);
    });

    const codeWrapper = document.getElementById('codeEditorWrapper');
    const canvas = document.getElementById('drawflow');

    if (mode === 'code') {
        const data = editor.export();
        document.getElementById('codeEditor').value = JSON.stringify(data, null, 2);
        codeWrapper.classList.remove('d-none');
        canvas.style.display = 'none';
    } else {
        codeWrapper.classList.add('d-none');
        canvas.style.display = '';
    }

    // Close mobile toolbar after selecting mode
    if (isMobileOrTablet()) {
        document.getElementById('mobileToolbarRow').classList.add('d-none');
    }
}

function formatCode() {
    const textarea = document.getElementById('codeEditor');
    try {
        const data = JSON.parse(textarea.value);
        textarea.value = JSON.stringify(data, null, 2);
    } catch(e) {
        Swal.fire('Erro!', 'JSON inválido: ' + e.message, 'error');
    }
}

function applyCode() {
    const textarea = document.getElementById('codeEditor');
    try {
        const data = JSON.parse(textarea.value);
        if (!data.drawflow) throw new Error('Formato Drawflow inválido');
        editor.import(data);
        Swal.fire({icon: 'success', title: 'Aplicado!', text: 'Fluxo atualizado a partir do código.', timer: 1500, showConfirmButton: false});
        markDirty();
    } catch(e) {
        Swal.fire('Erro!', 'Não foi possível aplicar: ' + e.message, 'error');
    }
}

// ============================================================
// SAVE / LOAD
// ============================================================
function savePipeline() {
    const name = document.getElementById('pipelineName').value.trim();
    if (!name) {
        Swal.fire('Atenção', 'Nome do pipeline é obrigatório.', 'warning');
        return;
    }

    const flowData = editor.export();
    const statusEl = document.getElementById('saveStatus');
    statusEl.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i> Salvando...';
    statusEl.className = 'save-status saving';

    $.ajax({
        url: baseUrl + '/pipelines/salvar',
        method: 'POST',
        data: {
            _csrf_token: csrfToken,
            id: pipelineId || '',
            nome: name,
            descricao: pipelineDescription,
            modo: currentMode,
            dados_flow: JSON.stringify(flowData),
            dados_code: document.getElementById('codeEditor').value,
            trigger_tipo: getTriggerType(flowData),
            agendamento_cron: getTriggerCron(flowData),
            trigger_config: JSON.stringify(getTriggerConfig(flowData)),
            'empresas[]': (function() { var s = document.getElementById('rbac_empresas'); return s ? Array.from(s.selectedOptions).map(function(o){return o.value}) : []; })(),
            'projetos[]': (function() { var s = document.getElementById('rbac_projetos'); return s ? Array.from(s.selectedOptions).map(function(o){return o.value}) : []; })()
        },
        dataType: 'json'
    }).then(function(res) {
        if (res.sucesso) {
            if (!pipelineId && res.id) {
                pipelineId = res.id;
                pipelineIdField.value = res.id;
                history.replaceState(null, '', baseUrl + '/pipelines/builder/' + res.id);
            }
            statusEl.innerHTML = '<i class="bi bi-cloud-check"></i> Salvo';
            statusEl.className = 'save-status saved';
            isDirty = false;
        } else {
            Swal.fire('Erro!', res.mensagem, 'error');
            statusEl.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Erro';
            statusEl.className = 'save-status';
        }
    }).catch(function(err) {
        Swal.fire('Erro!', 'Falha ao salvar.', 'error');
        statusEl.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Erro';
        statusEl.className = 'save-status';
    });
}

function loadPipeline(id) {
    $.getJSON(baseUrl + '/pipelines/get/' + id, function(res) {
        if (res.sucesso && res.data) {
            const p = res.data;
            document.getElementById('pipelineName').value = p.nome;
            pipelineDescription = p.descricao || '';
            currentMode = p.modo || 'nocode';

            // Import flow
            if (p.dados_flow && p.dados_flow.drawflow) {
                editor.import(p.dados_flow);
            }

            // Set mode
            switchMode(currentMode);
            if (currentMode !== 'code') {
                document.getElementById('codeEditorWrapper').classList.add('d-none');
                document.getElementById('drawflow').style.display = '';
            }

            // Code editor content
            if (p.dados_code) {
                document.getElementById('codeEditor').value = p.dados_code;
            }

            // Preencher empresas/projetos RBAC
            var empIds = (res.empresas || []).map(function(e) { return parseInt(e.id_empresa || e.id || e, 10); });
            var projIds = (res.projetos || []).map(function(p) { return parseInt(p.id_projeto || p.id || p, 10); });
            if (typeof rbacCarregarOpcoes === 'function') {
                rbacCarregarOpcoes(function() { rbacPreencherSelects(empIds, projIds); });
            }

            updateEmptyOverlay();
            isDirty = false;
        }
    });
}

function loadConnections() {
    $.getJSON(baseUrl + '/pipelines/conexoes', function(res) {
        if (res.sucesso) {
            connections = res.data || [];
        }
    });
}

function loadApisExternas() {
    $.getJSON(baseUrl + '/pipelines/apis-externas', function(res) {
        if (res.sucesso) apisExternas = res.data || [];
    });
}

function loadEventosApi() {
    $.getJSON(baseUrl + '/pipelines/eventos-api', function(res) {
        if (res.sucesso) eventosApi = res.data || [];
    });
}

function loadTablesForConnection(connId, callback) {
    if (connectionTables[connId]) { callback(connectionTables[connId]); return; }
    $.getJSON(baseUrl + '/pipelines/tabelas/' + connId, function(res) {
        if (res.sucesso) { connectionTables[connId] = res.data || []; callback(res.data || []); }
        else callback([]);
    }).fail(function() { callback([]); });
}

function loadColumnsForTable(connId, table, schema, callback) {
    var url = baseUrl + '/pipelines/colunas/' + connId + '/' + encodeURIComponent(table);
    if (schema) url += '?schema=' + encodeURIComponent(schema);
    $.getJSON(url, function(res) {
        callback(res.sucesso ? (res.data || []) : []);
    }).fail(function() { callback([]); });
}

function showTableBrowser(connId, nodeId) {
    if (!connId) { Swal.fire('Atenção', 'Selecione uma conexão primeiro.', 'info'); return; }
    var container = document.getElementById('tableBrowser_' + nodeId);
    if (!container) return;
    container.innerHTML = '<div class="text-center py-2"><div class="spinner-border spinner-border-sm text-primary"></div> Carregando...</div>';
    container.style.display = 'block';
    loadTablesForConnection(connId, function(tables) {
        if (!tables.length) { container.innerHTML = '<div class="p-2 text-muted small">Nenhuma tabela encontrada</div>'; return; }
        var h = '<div class="table-browser">';
        tables.forEach(function(t) {
            var name = t.table_name || t.nome;
            var schema = t.table_schema || t.schema || '';
            var display = schema ? schema + '.' + name : name;
            h += '<div class="table-browser-item" onclick="insertTableName(' + nodeId + ',\'' + escapeHtml(display).replace(/'/g,"\\'") + '\')">';
            h += '<i class="bi bi-table"></i><span class="table-name">' + escapeHtml(name) + '</span>';
            if (schema) h += '<span class="schema-name">(' + escapeHtml(schema) + ')</span>';
            h += '</div>';
        });
        h += '</div>';
        container.innerHTML = h;
    });
}

function insertTableName(nodeId, tableName) {
    var textarea = document.querySelector('#propBody textarea[onchange*="sql_query"]');
    if (textarea) {
        var cursor = textarea.selectionStart || textarea.value.length;
        textarea.value = textarea.value.substring(0, cursor) + tableName + textarea.value.substring(cursor);
        textarea.focus();
        updateNodeProp(nodeId, 'sql_query', textarea.value);
    }
}

function applyApiExterna(nodeId, apiId) {
    var api = apisExternas.find(function(a) { return a.id == apiId; });
    if (!api) return;
    updateNodeProp(nodeId, 'url', api.url || '');
    updateNodeProp(nodeId, 'method', api.metodo || 'GET');
    if (api.headers) {
        try { updateNodeProp(nodeId, 'headers', JSON.stringify(api.headers)); } catch(e) {}
    }
    if (api.auth_tipo && api.auth_tipo !== 'none') {
        updateNodeProp(nodeId, 'auth_type', api.auth_tipo === 'bearer_token' ? 'bearer' : api.auth_tipo);
    }
    if (api.body_template) updateNodeProp(nodeId, 'body', api.body_template);
    showProperties(nodeId);
    Swal.fire({icon:'success',title:'API aplicada!',text:'Configurações de "'+api.nome+'" importadas.',timer:1500,showConfirmButton:false});
}

// ============================================================
// EXECUTION
// ============================================================
function executePipeline() {
    if (!pipelineId) {
        Swal.fire('Atenção', 'Salve o pipeline antes de executar.', 'warning');
        return;
    }

    if (isDirty) {
        Swal.fire({
            title: 'Alterações não salvas',
            text: 'Deseja salvar antes de executar?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Salvar e Executar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                savePipeline();
                setTimeout(function() { runExecution(); }, 1000);
            }
        });
        return;
    }

    runExecution();
}

function runExecution() {
    // Open bottom panel
    const body = document.getElementById('bottomBody');
    body.classList.add('expanded');
    document.getElementById('bottomChevron').className = 'bi bi-chevron-down';
    
    const statusBadge = document.getElementById('execStatus');
    statusBadge.className = 'badge bg-primary';
    statusBadge.textContent = 'Executando...';
    
    clearLogs();
    addLog('info', 'Iniciando execução do pipeline...');

    // Clear previous execution visual states
    document.querySelectorAll('.df-node-container').forEach(function(el) {
        el.classList.remove('exec-success', 'exec-error', 'exec-running', 'exec-skipped');
    });

    $.ajax({
        url: baseUrl + '/pipelines/executar/' + pipelineId,
        method: 'POST',
        data: { _csrf_token: csrfToken },
        dataType: 'json'
    }).then(function(res) {
        if (res.sucesso) {
            statusBadge.className = 'badge bg-success';
            statusBadge.textContent = 'Sucesso';
            addLog('success', 'Pipeline executado com sucesso em ' + res.duracao_ms + 'ms');
        } else {
            statusBadge.className = 'badge bg-danger';
            statusBadge.textContent = 'Erro';
            addLog('error', 'Falha: ' + res.mensagem);
        }

        // Show node-level logs
        if (res.logs) {
            res.logs.forEach(function(log) {
                const icon = log.status === 'success' ? '✅' : log.status === 'error' ? '❌' : log.status === 'skipped' ? '⏭️' : '⏳';
                const msg = icon + ' [' + escapeHtml(log.label) + '] ' + 
                    (log.duration_ms ? log.duration_ms + 'ms' : '') +
                    (log.result_preview ? ' → ' + escapeHtml(log.result_preview) : '') +
                    (log.error ? ' — ' + escapeHtml(log.error) : '');
                addLog(log.status === 'success' ? 'success' : log.status === 'error' ? 'error' : '', msg);
                
                // Visual feedback on nodes
                const nodeEl = document.querySelector('#node-' + log.node_id + ' .df-node-container');
                if (nodeEl) {
                    nodeEl.classList.add('exec-' + log.status);
                }
            });
        }

        addLog('', '─── Execução finalizada (' + res.nodes_sucesso + ' OK, ' + res.nodes_falha + ' falhas) ───');
    }).catch(function(err) {
        statusBadge.className = 'badge bg-danger';
        statusBadge.textContent = 'Erro';
        addLog('error', 'Erro na requisição: ' + err.statusText);
    });
}

function addLog(type, message) {
    const el = document.getElementById('execLogs');
    const entry = document.createElement('div');
    entry.className = 'log-entry ' + type;
    entry.innerHTML = '<small class="text-muted me-2">' + new Date().toLocaleTimeString('pt-BR') + '</small>' + message;
    el.appendChild(entry);
    el.scrollTop = el.scrollHeight;
}

function clearLogs() {
    document.getElementById('execLogs').innerHTML = '';
}

// ============================================================
// VALIDATION
// ============================================================
function validatePipeline() {
    const data = editor.export();
    const nodes = data.drawflow.Home.data;
    const issues = [];

    const nodeKeys = Object.keys(nodes);

    if (nodeKeys.length === 0) {
        issues.push('Pipeline está vazio — adicione pelo menos um nó.');
    }

    // Check for trigger
    const hasTrigger = nodeKeys.some(function(k) { return nodes[k].name === 'trigger' || (nodes[k].data && nodes[k].data.type === 'trigger'); });
    if (!hasTrigger && nodeKeys.length > 0) {
        issues.push('Nenhum nó Trigger encontrado. Todo pipeline deve começar com um Trigger.');
    }

    // Check for unconnected nodes
    nodeKeys.forEach(function(k) {
        const node = nodes[k];
        const type = node.data ? node.data.type : node.name;
        const def = NODE_TYPES[type];
        if (!def) return;

        const hasInputConn = Object.values(node.inputs || {}).some(function(i) { return i.connections.length > 0; });
        const hasOutputConn = Object.values(node.outputs || {}).some(function(o) { return o.connections.length > 0; });

        if (def.inputs > 0 && !hasInputConn) {
            issues.push('Nó "' + (node.data.label || def.label) + '" não tem entrada conectada.');
        }
        if (def.outputs > 0 && !hasOutputConn) {
            issues.push('Nó "' + (node.data.label || def.label) + '" não tem saída conectada.');
        }

        // Check required fields
        if (type === 'sql_query' && !node.data.connection_id) {
            issues.push('Nó "' + (node.data.label || 'SQL Query') + '" precisa de uma conexão de banco.');
        }
        if (type === 'sql_query' && !node.data.sql_query) {
            issues.push('Nó "' + (node.data.label || 'SQL Query') + '" precisa de um SQL.');
        }
        if (type === 'http_request' && !node.data.url) {
            issues.push('Nó "' + (node.data.label || 'HTTP Request') + '" precisa de uma URL.');
        }
    });

    if (issues.length === 0) {
        Swal.fire({icon: 'success', title: 'Pipeline Válido!', text: 'Nenhum problema encontrado.', timer: 2000, showConfirmButton: false});
    } else {
        let html = '<ul class="text-start" style="font-size:0.9rem">';
        issues.forEach(function(issue) { html += '<li class="mb-1">' + escapeHtml(issue) + '</li>'; });
        html += '</ul>';
        Swal.fire({title: 'Problemas Encontrados', html: html, icon: 'warning', confirmButtonText: 'Entendi'});
    }
}

// ============================================================
// UI HELPERS
// ============================================================
function isMobileOrTablet() {
    return window.innerWidth <= 992;
}

function toggleLeftPanel() {
    var panel = document.getElementById('leftPanel');
    var overlay = document.getElementById('panelOverlay');
    var reopenBtn = document.getElementById('btnReopenLeft');
    if (isMobileOrTablet()) {
        var isOpen = panel.classList.contains('open');
        if (isOpen) {
            panel.classList.remove('open');
            overlay.classList.remove('active');
        } else {
            // Close right panel first
            document.getElementById('rightPanel').classList.remove('open');
            panel.classList.add('open');
            overlay.classList.add('active');
        }
    } else {
        panel.classList.toggle('collapsed');
        if (reopenBtn) {
            reopenBtn.style.display = panel.classList.contains('collapsed') ? 'flex' : 'none';
        }
    }
}

function closeAllPanels() {
    document.getElementById('leftPanel').classList.remove('open');
    document.getElementById('rightPanel').classList.remove('open');
    document.getElementById('panelOverlay').classList.remove('active');
}

function openRightPanel() {
    var panel = document.getElementById('rightPanel');
    var overlay = document.getElementById('panelOverlay');
    panel.classList.remove('d-none');
    if (isMobileOrTablet()) {
        document.getElementById('leftPanel').classList.remove('open');
        panel.classList.add('open');
        overlay.classList.add('active');
    }
}

function toggleMobileToolbar() {
    document.getElementById('mobileToolbarRow').classList.toggle('d-none');
}

function toggleBottomPanel() {
    const body = document.getElementById('bottomBody');
    const chevron = document.getElementById('bottomChevron');
    body.classList.toggle('expanded');
    chevron.className = body.classList.contains('expanded') ? 'bi bi-chevron-down' : 'bi bi-chevron-up';
}

function updateEmptyOverlay() {
    const data = editor.export();
    const hasNodes = Object.keys(data.drawflow.Home.data).length > 0;
    document.getElementById('emptyOverlay').style.display = hasNodes ? 'none' : '';
}

function markDirty() {
    isDirty = true;
    const el = document.getElementById('saveStatus');
    el.innerHTML = '<i class="bi bi-circle-fill" style="font-size:6px"></i> Não salvo';
    el.className = 'save-status';
}

function editDescription() {
    Swal.fire({
        title: 'Descrição do Pipeline',
        input: 'textarea',
        inputValue: pipelineDescription,
        inputPlaceholder: 'Descreva o objetivo deste pipeline...',
        showCancelButton: true,
        confirmButtonText: 'Salvar'
    }).then(function(result) {
        if (result.isConfirmed) {
            pipelineDescription = result.value || '';
            markDirty();
        }
    });
}

function zoomIn() { editor.zoom_in(); }
function zoomOut() { editor.zoom_out(); }
function zoomReset() { editor.zoom_reset(); }

function autoLayout() {
    const data = editor.export();
    const nodes = data.drawflow.Home.data;
    const keys = Object.keys(nodes);
    if (keys.length === 0) return;

    // Simple auto-layout: arrange nodes in a grid
    const startX = 80;
    const startY = 80;
    const spacingX = 280;
    const spacingY = 160;
    const cols = Math.ceil(Math.sqrt(keys.length));

    // Topological sort for better layout
    const sorted = topologicalSortJS(nodes);
    
    sorted.forEach(function(nodeId, i) {
        const col = i % cols;
        const row = Math.floor(i / cols);
        const x = startX + col * spacingX;
        const y = startY + row * spacingY;
        
        const nodeEl = document.querySelector('#node-' + nodeId);
        if (nodeEl) {
            nodeEl.style.left = x + 'px';
            nodeEl.style.top = y + 'px';
            nodes[nodeId].pos_x = x;
            nodes[nodeId].pos_y = y;
        }
    });

    // Re-import with new positions
    editor.import(data);
    markDirty();
}

function topologicalSortJS(nodes) {
    const graph = {};
    const inDegree = {};
    const keys = Object.keys(nodes);

    keys.forEach(function(id) {
        graph[id] = [];
        if (!inDegree[id]) inDegree[id] = 0;
    });

    keys.forEach(function(id) {
        const outputs = nodes[id].outputs || {};
        Object.values(outputs).forEach(function(output) {
            (output.connections || []).forEach(function(conn) {
                const target = String(conn.node);
                graph[id].push(target);
                inDegree[target] = (inDegree[target] || 0) + 1;
            });
        });
    });

    const queue = [];
    Object.keys(inDegree).forEach(function(id) {
        if (inDegree[id] === 0) queue.push(id);
    });

    const sorted = [];
    while (queue.length > 0) {
        const current = queue.shift();
        sorted.push(current);
        (graph[current] || []).forEach(function(n) {
            inDegree[n]--;
            if (inDegree[n] === 0) queue.push(n);
        });
    }

    // Add any remaining nodes (cycles)
    keys.forEach(function(k) {
        if (!sorted.includes(k)) sorted.push(k);
    });

    return sorted;
}

function clearCanvas() {
    Swal.fire({
        title: 'Limpar Canvas?',
        text: 'Todos os nós serão removidos. Esta ação não pode ser desfeita.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Limpar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            editor.clear();
            closeProperties();
            updateEmptyOverlay();
            markDirty();
        }
    });
}

// ============================================================
// TRIGGER HELPERS
// ============================================================
function getTriggerType(flowData) {
    var nodes = flowData.drawflow && flowData.drawflow.Home ? flowData.drawflow.Home.data : {};
    for (var k in nodes) {
        if (nodes[k].name === 'trigger' || (nodes[k].data && nodes[k].data.type === 'trigger')) {
            return nodes[k].data.trigger_type || 'manual';
        }
    }
    return 'manual';
}

function getTriggerCron(flowData) {
    var nodes = flowData.drawflow && flowData.drawflow.Home ? flowData.drawflow.Home.data : {};
    for (var k in nodes) {
        if (nodes[k].name === 'trigger' || (nodes[k].data && nodes[k].data.type === 'trigger')) {
            return nodes[k].data.cron_expression || '';
        }
    }
    return '';
}

function getTriggerConfig(flowData) {
    var nodes = flowData.drawflow && flowData.drawflow.Home ? flowData.drawflow.Home.data : {};
    for (var k in nodes) {
        if (nodes[k].name === 'trigger' || (nodes[k].data && nodes[k].data.type === 'trigger')) {
            var d = nodes[k].data;
            var config = { trigger_type: d.trigger_type || 'manual' };
            if (d.trigger_type === 'cron') config.cron_expression = d.cron_expression || '';
            if (d.trigger_type === 'api_event') config.evento_api_id = d.evento_api_id || '';
            if (d.trigger_type === 'webhook') config.webhook_secret = d.webhook_secret || '';
            return config;
        }
    }
    return { trigger_type: 'manual' };
}

function setCronPreset(nodeId, expr) {
    updateNodeProp(nodeId, 'cron_expression', expr);
    showProperties(nodeId);
}

// CSS animation for save spinner
const spinStyle = document.createElement('style');
spinStyle.textContent = '@keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}.spin{animation:spin 1s linear infinite;display:inline-block}';
document.head.appendChild(spinStyle);
</script>
SCRIPTS;

$extraScripts .= '<script src="' . (defined('BASE_URL') ? BASE_URL : '') . '/assets/js/rbac-recurso.js"></script>';
$extraScripts .= '<script src="' . (defined('BASE_URL') ? BASE_URL : '') . '/assets/js/rbac-compartilhamento.js"></script>';

include __DIR__ . '/../layouts/base.php';
?>
