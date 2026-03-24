<?php 
$pageTitle = 'Workflow Builder';
$currentPage = 'workflows';

$workflowId = isset($params['id']) ? intval($params['id']) : null;

ob_start();
?>

<style>
/* ==== ESTILOS BASE MODERNOS ==== */
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
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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

.btn-modern-primary {
    background: var(--gradient-primary);
    border: none;
    color: white;
    padding: 0.65rem 1.5rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 0.95rem;
    transition: var(--transition);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-modern-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    color: white;
}

/* ==== WORKFLOW BUILDER ESPECÍFICO ==== */
.workflow-container {
    display: flex;
    height: calc(100vh - 240px);
    gap: 0;
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.node-palette {
    width: 260px;
    background: #f8f9fa;
    border-right: 1px solid #e9ecef;
    padding: 1rem;
    overflow-y: auto;
}
.canvas-container {
    flex: 1;
    position: relative;
    background: #f0f4f8;
    background-image: 
        linear-gradient(rgba(0,0,0,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,0,0,0.03) 1px, transparent 1px);
    background-size: 20px 20px;
    overflow: hidden;
}
.node-palette-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: white;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    cursor: grab;
    border: 2px solid transparent;
    transition: all 0.2s ease;
}
.node-palette-item:hover {
    border-color: #4f46e5;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.node-palette-item:active {
    cursor: grabbing;
}
.node-palette-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
}
.node-trigger .node-palette-icon { background: #10b981; }
.node-rotina .node-palette-icon { background: #3b82f6; }
.node-condition .node-palette-icon { background: #f59e0b; }
.node-delay .node-palette-icon { background: #8b5cf6; }
.node-notification .node-palette-icon { background: #ec4899; }
.node-set_variable .node-palette-icon { background: #06b6d4; }
.node-end .node-palette-icon { background: #6b7280; }

/* Canvas Nodes */
.canvas-node {
    position: absolute;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    min-width: 180px;
    cursor: move;
    user-select: none;
    transition: box-shadow 0.2s ease;
}
.canvas-node:hover {
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}
.canvas-node.selected {
    box-shadow: 0 0 0 3px #4f46e5;
}
.canvas-node-header {
    padding: 0.5rem 0.75rem;
    border-radius: 12px 12px 0 0;
    color: white;
    font-weight: 600;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.canvas-node[data-type="trigger"] .canvas-node-header { background: #10b981; }
.canvas-node[data-type="rotina"] .canvas-node-header { background: #3b82f6; }
.canvas-node[data-type="condition"] .canvas-node-header { background: #f59e0b; }
.canvas-node[data-type="delay"] .canvas-node-header { background: #8b5cf6; }
.canvas-node[data-type="notification"] .canvas-node-header { background: #ec4899; }
.canvas-node[data-type="set_variable"] .canvas-node-header { background: #06b6d4; }
.canvas-node[data-type="end"] .canvas-node-header { background: #6b7280; }

.canvas-node-body {
    padding: 0.75rem;
    font-size: 0.8rem;
    color: #4b5563;
}
.canvas-node-body .config-label {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

/* Pontos de conexão */
.node-connector {
    position: absolute;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #e5e7eb;
    border: 3px solid white;
    box-shadow: 0 1px 4px rgba(0,0,0,0.2);
    cursor: crosshair;
    z-index: 10;
}
.node-connector:hover {
    background: #4f46e5;
    transform: scale(1.2);
}
.node-connector.output {
    bottom: -7px;
    left: 50%;
    transform: translateX(-50%);
}
.node-connector.input {
    top: -7px;
    left: 50%;
    transform: translateX(-50%);
}
.node-connector.output-true {
    bottom: -7px;
    left: 30%;
    transform: translateX(-50%);
    background: #10b981;
}
.node-connector.output-false {
    bottom: -7px;
    left: 70%;
    transform: translateX(-50%);
    background: #ef4444;
}

/* Linhas de conexão */
.edge-line {
    position: absolute;
    pointer-events: none;
}
.edge-line path {
    fill: none;
    stroke: #9ca3af;
    stroke-width: 2;
}
.edge-line path.edge-success { stroke: #10b981; }
.edge-line path.edge-error { stroke: #ef4444; }

/* Properties Panel */
.properties-panel {
    width: 320px;
    background: white;
    border-left: 1px solid #e9ecef;
    padding: 1rem;
    overflow-y: auto;
    display: none;
}
.properties-panel.active {
    display: block;
}
.properties-header {
    font-weight: 700;
    font-size: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #4f46e5;
}
</style>

<!-- Header da Página -->
<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-bezier2"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Workflow Builder</h1>
        <p class="page-subtitle-modern">
            <?php if($workflowId): ?>
                Editar workflow #<?= $workflowId ?>
            <?php else: ?>
                Crie seu fluxo de automação arrastando componentes
            <?php endif; ?>
        </p>
    </div>
    <div class="ms-auto d-flex gap-2">
        <?php
        $nivelWf = \App\Core\AuthMiddleware::obterUsuario()['nivel_acesso'] ?? 'operador';
        if (in_array($nivelWf, ['super_admin', 'admin'])): ?>
        <div class="dropdown">
            <button class="btn btn-outline-info dropdown-toggle" type="button" data-bs-toggle="dropdown" title="Empresas / Projetos">
                <i class="bi bi-building"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end p-3" style="min-width:280px">
                <?php include __DIR__ . '/partials/recurso_empresa_projeto.php'; ?>
            </div>
        </div>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/workflows" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Voltar
        </a>
        <button class="btn btn-success" id="btnSalvar">
            <i class="bi bi-save me-2"></i>Salvar
        </button>
        <button class="btn btn-outline-secondary" onclick="abrirModalCompartilhamento('workflow', <?= $workflowId ?? 'null' ?>)" title="Compartilhar">
            <i class="bi bi-share me-2"></i>Compartilhar
        </button>
        <button class="btn-modern-primary" id="btnExecutar">
            <i class="bi bi-play-fill me-2"></i>Executar
        </button>
    </div>
</div>

<div class="workflow-container">
    <!-- Paleta de Nós -->
    <div class="node-palette">
        <h6 class="fw-bold mb-3"><i class="bi bi-grid-3x3-gap me-2"></i>Componentes</h6>
        
        <!-- Configurações do Workflow -->
        <div class="card bg-light mb-3">
            <div class="card-body p-3">
                <div class="mb-2">
                    <label class="form-label fw-bold mb-1" style="font-size: 0.85rem;">Nome:</label>
                    <input type="text" class="form-control form-control-sm" id="workflowNome" placeholder="Nome do Workflow" value="Novo Workflow">
                </div>
                <div>
                    <label class="form-label fw-bold mb-1" style="font-size: 0.85rem;">Trigger:</label>
                    <select class="form-select form-select-sm" id="triggerTipo">
                        <option value="manual">🖐️ Manual</option>
                        <option value="api_event">☁️ Evento API</option>
                        <option value="cron">⏰ CRON</option>
                        <option value="rotina_finished">▶️ Após Rotina</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="mb-3">
            <small class="text-muted text-uppercase fw-bold">Triggers</small>
        </div>
        <div class="node-palette-item node-trigger" draggable="true" data-type="trigger">
            <div class="node-palette-icon"><i class="bi bi-lightning-charge"></i></div>
            <div>
                <div class="fw-bold">Trigger</div>
                <small class="text-muted">Inicia o workflow</small>
            </div>
        </div>
        
        <div class="mb-3 mt-4">
            <small class="text-muted text-uppercase fw-bold">Ações</small>
        </div>
        <div class="node-palette-item node-rotina" draggable="true" data-type="rotina">
            <div class="node-palette-icon"><i class="bi bi-gear"></i></div>
            <div>
                <div class="fw-bold">Rotina</div>
                <small class="text-muted">Executar rotina</small>
            </div>
        </div>
        <div class="node-palette-item node-notification" draggable="true" data-type="notification">
            <div class="node-palette-icon"><i class="bi bi-bell"></i></div>
            <div>
                <div class="fw-bold">Notificação</div>
                <small class="text-muted">Enviar alerta</small>
            </div>
        </div>
        <div class="node-palette-item node-set_variable" draggable="true" data-type="set_variable">
            <div class="node-palette-icon"><i class="bi bi-braces"></i></div>
            <div>
                <div class="fw-bold">Variável</div>
                <small class="text-muted">Definir valor</small>
            </div>
        </div>
        
        <div class="mb-3 mt-4">
            <small class="text-muted text-uppercase fw-bold">Controle</small>
        </div>
        <div class="node-palette-item node-condition" draggable="true" data-type="condition">
            <div class="node-palette-icon"><i class="bi bi-signpost-split"></i></div>
            <div>
                <div class="fw-bold">Condição</div>
                <small class="text-muted">IF / Else</small>
            </div>
        </div>
        <div class="node-palette-item node-delay" draggable="true" data-type="delay">
            <div class="node-palette-icon"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="fw-bold">Delay</div>
                <small class="text-muted">Aguardar tempo</small>
            </div>
        </div>
        <div class="node-palette-item node-end" draggable="true" data-type="end">
            <div class="node-palette-icon"><i class="bi bi-stop-circle"></i></div>
            <div>
                <div class="fw-bold">Fim</div>
                <small class="text-muted">Encerrar fluxo</small>
            </div>
        </div>
    </div>
    
    <!-- Canvas Principal -->
    <div class="canvas-container" id="canvas">
        <!-- SVG para as linhas -->
        <svg id="edgesSvg" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 5;"></svg>
        
        <!-- Área onde os nós são soltos -->
        <div id="nodesContainer"></div>
    </div>
    
    <!-- Painel de Propriedades -->
    <div class="properties-panel" id="propertiesPanel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="properties-header mb-0">Propriedades</div>
            <button class="btn btn-sm btn-outline-secondary" onclick="fecharPropriedades()">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div id="propertiesContent"></div>
    </div>
</div>

<?php include __DIR__ . '/partials/compartilhamento_modal.php'; ?>

<?php
$content = ob_get_clean();

$extraStyles = <<<STYLES
STYLES;

$extraScripts = '<script>const csrfToken = \'' . htmlspecialchars($csrfToken, ENT_QUOTES) . '\';</script>';
$extraScripts .= <<<'SCRIPTS'
<script>
// Estado do workflow
let nodes = [];
let edges = [];
let selectedNode = null;
let isDraggingNode = false;
let dragOffsetX = 0;
let dragOffsetY = 0;
let isConnectingNodes = false;
let connectingFromNode = null;
let connectingFromPort = null;
let nodeIdCounter = 1;

// Carregar workflow existente
const workflowId = <?= $workflowId ? $workflowId : 'null' ?>;

// Inicializar
$(document).ready(function() {
    inicializarCanvas();
    inicializarPaleta();
    
    if (workflowId) {
        carregarWorkflow(workflowId);
    }
    
    $('#btnSalvar').on('click', salvarWorkflow);
    $('#btnExecutar').on('click', executarWorkflow);
});

// Inicializar canvas
function inicializarCanvas() {
    const canvas = $('#canvas');
    
    // Drop de nós da paleta
    canvas.on('drop', function(e) {
        e.preventDefault();
        const type = e.originalEvent.dataTransfer.getData('nodeType');
        if (!type) return;
        
        const rect = canvas[0].getBoundingClientRect();
        const x = e.originalEvent.clientX - rect.left;
        const y = e.originalEvent.clientY - rect.top;
        
        criarNode(type, x, y);
    });
    
    canvas.on('dragover', function(e) {
        e.preventDefault();
    });
    
    // Clique no canvas (desselecionar)
    canvas.on('click', function(e) {
        if (e.target === canvas[0]) {
            desselecionarNode();
        }
    });
}

// Inicializar paleta
function inicializarPaleta() {
    $('.node-palette-item').on('dragstart', function(e) {
        const type = $(this).data('type');
        e.originalEvent.dataTransfer.setData('nodeType', type);
    });
}

// Criar node no canvas
function criarNode(type, x, y, config = {}) {
    const node = {
        id: nodeIdCounter++,
        type: type,
        x: x,
        y: y,
        config: config
    };
    
    nodes.push(node);
    renderizarNode(node);
    return node;
}

// Renderizar node no canvas
function renderizarNode(node) {
    const icons = {
        trigger: 'lightning-charge',
        rotina: 'gear',
        condition: 'signpost-split',
        delay: 'hourglass-split',
        notification: 'bell',
        set_variable: 'braces',
        end: 'stop-circle'
    };
    
    const labels = {
        trigger: 'Trigger',
        rotina: 'Rotina',
        condition: 'Condição',
        delay: 'Delay',
        notification: 'Notificação',
        set_variable: 'Variável',
        end: 'Fim'
    };
    
    let configText = '';
    if (node.type === 'rotina' && node.config.rotina_nome) {
        configText = `<div class="config-label">\${node.config.rotina_nome}</div>`;
    } else if (node.type === 'condition' && node.config.condition) {
        configText = `<div class="config-label">\${node.config.condition}</div>`;
    } else if (node.type === 'delay' && node.config.seconds) {
        configText = `<div class="config-label">\${node.config.seconds}s</div>`;
    } else if (node.type === 'notification' && node.config.message) {
        configText = `<div class="config-label">\${node.config.message.substring(0, 30)}...</div>`;
    }
    
    const html = `
    <div class="canvas-node" id="node-\${node.id}" data-type="\${node.type}" data-node-id="\${node.id}" 
         style="left: \${node.x}px; top: \${node.y}px;">
        <div class="canvas-node-header">
            <i class="bi bi-\${icons[node.type]}"></i>
            \${labels[node.type]}
            <button class="btn btn-sm btn-link text-white ms-auto p-0" onclick="deletarNode(\${node.id})">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="canvas-node-body">
            \${configText || '<span class="text-muted">Clique para configurar</span>'}
        </div>
        <div class="node-connector input" data-port="input"></div>
        \${node.type === 'condition' ? 
            '<div class="node-connector output-true" data-port="output-true"></div><div class="node-connector output-false" data-port="output-false"></div>' :
            '<div class="node-connector output" data-port="output"></div>'
        }
    </div>
    `;
    
    $('#nodesContainer').append(html);
    
    // Eventos do node
    const nodeEl = \$('#node-' + node.id);
    
    // Mover node
    nodeEl.on('mousedown', '.canvas-node-header', function(e) {
        e.stopPropagation();
        selectedNode = node;
        isDraggingNode = true;
        const rect = nodeEl[0].getBoundingClientRect();
        dragOffsetX = e.clientX - rect.left;
        dragOffsetY = e.clientY - rect.top;
        nodeEl.addClass('selected');
    });
    
    // Clique no node
    nodeEl.on('click', function(e) {
        e.stopPropagation();
        selecionarNode(node);
    });
    
    // Conectores
    nodeEl.find('.node-connector').on('mousedown', function(e) {
        e.stopPropagation();
        e.preventDefault();
        isConnectingNodes = true;
        connectingFromNode = node.id;
        connectingFromPort = $(this).data('port');
    });
}

// Mover node (mousemove global)
$(document).on('mousemove', function(e) {
    if (isDraggingNode && selectedNode) {
        const canvas = $('#canvas');
        const rect = canvas[0].getBoundingClientRect();
        selectedNode.x = e.clientX - rect.left - dragOffsetX;
        selectedNode.y = e.clientY - rect.top - dragOffsetY;
        
        const nodeEl = \$('#node-' + selectedNode.id);
        nodeEl.css({ left: selectedNode.x + 'px', top: selectedNode.y + 'px' });
        
        renderizarEdges();
    }
});

// Soltar node (mouseup global)
$(document).on('mouseup', function(e) {
    if (isDraggingNode) {
        isDraggingNode = false;
    }
    
    if (isConnectingNodes) {
        // Verificar se soltou em um conector
        const target = $(e.target);
        if (target.hasClass('node-connector') && target.data('port') === 'input') {
            const toNode = parseInt(target.closest('.canvas-node').data('node-id'));
            if (toNode !== connectingFromNode) {
                criarEdge(connectingFromNode, connectingFromPort, toNode);
            }
        }
        isConnectingNodes = false;
        connectingFromNode = null;
        connectingFromPort = null;
    }
});

// Criar edge
function criarEdge(fromId, fromPort, toId) {
    // Remover edge existente da mesma origem/porta
    edges = edges.filter(e => !(e.from === fromId && e.fromPort === fromPort));
    
    edges.push({
        from: fromId,
        fromPort: fromPort,
        to: toId
    });
    
    renderizarEdges();
}

// Renderizar todas as edges
function renderizarEdges() {
    const svg = $('#edgesSvg');
    svg.empty();
    
    edges.forEach(edge => {
        const fromNode = nodes.find(n => n.id === edge.from);
        const toNode = nodes.find(n => n.id === edge.to);
        if (!fromNode || !toNode) return;
        
        const fromEl = \$('#node-' + edge.from);
        const toEl = \$('#node-' + edge.to);
        if (!fromEl.length || !toEl.length) return;
        
        const fromConnector = fromEl.find(`[data-port="\${edge.fromPort}"]`);
        const toConnector = toEl.find('[data-port="input"]');
        
        const fromRect = fromConnector[0].getBoundingClientRect();
        const toRect = toConnector[0].getBoundingClientRect();
        const canvasRect = $('#canvas')[0].getBoundingClientRect();
        
        const x1 = fromRect.left + fromRect.width / 2 - canvasRect.left;
        const y1 = fromRect.top + fromRect.height / 2 - canvasRect.top;
        const x2 = toRect.left + toRect.width / 2 - canvasRect.left;
        const y2 = toRect.top + toRect.height / 2 - canvasRect.top;
        
        const path = criarCurvaBezier(x1, y1, x2, y2);
        
        const colorClass = edge.fromPort === 'output-true' ? 'edge-success' : 
                          (edge.fromPort === 'output-false' ? 'edge-error' : '');
        
        svg.append(`<path d="\${path}" class="\${colorClass}"/>`);
    });
}

// Criar curva bezier
function criarCurvaBezier(x1, y1, x2, y2) {
    const dx = x2 - x1;
    const dy = y2 - y1;
    const offset = Math.abs(dy) * 0.3 + 50;
    
    return `M \${x1} \${y1} C \${x1} \${y1 + offset}, \${x2} \${y2 - offset}, \${x2} \${y2}`;
}

// Selecionar node
function selecionarNode(node) {
    $('.canvas-node').removeClass('selected');
    \$('#node-' + node.id).addClass('selected');
    selectedNode = node;
    mostrarPropriedades(node);
}

// Desselecionar node
function desselecionarNode() {
    $('.canvas-node').removeClass('selected');
    selectedNode = null;
    fecharPropriedades();
}

// Deletar node
function deletarNode(nodeId) {
    if (!confirm('Deseja excluir este componente?')) return;
    
    nodes = nodes.filter(n => n.id !== nodeId);
    edges = edges.filter(e => e.from !== nodeId && e.to !== nodeId);
    
    \$('#node-' + nodeId).remove();
    renderizarEdges();
    fecharPropriedades();
}

// Mostrar propriedades
function mostrarPropriedades(node) {
    const panel = $('#propertiesPanel');
    const content = $('#propertiesContent');
    
    let html = `<div class="mb-3"><strong>Tipo:</strong> \${node.type}</div>`;
    
    if (node.type === 'rotina') {
        html += `
            <div class="mb-3">
                <label class="form-label">Rotina:</label>
                <select class="form-select form-select-sm" id="propRotinaId">
                    <option value="">Selecione...</option>
                </select>
            </div>
        `;
        // Carregar rotinas via AJAX
        setTimeout(() => {
            $.getJSON(baseUrl + '/api/rotinas/list', (res) => {
                if (res.sucesso) {
                    const sel = $('#propRotinaId');
                    res.dados.forEach(r => {
                        sel.append(`<option value="\${r.id_rotina}" \${node.config.rotina_id == r.id_rotina ? 'selected' : ''}>\${r.nome}</option>`);
                    });
                    sel.on('change', () => {
                        node.config.rotina_id = parseInt(sel.val());
                        node.config.rotina_nome = sel.find('option:selected').text();
                        \$('#node-' + node.id).find('.config-label').text(node.config.rotina_nome);
                    });
                }
            });
        }, 100);
    } else if (node.type === 'condition') {
        html += `
            <div class="mb-3">
                <label class="form-label">Condição:</label>
                <input type="text" class="form-control form-control-sm" id="propCondition" value="\${node.config.condition || ''}" placeholder="Ex: status == 'success'">
            </div>
        `;
        setTimeout(() => {
            $('#propCondition').on('input', function() {
                node.config.condition = $(this).val();
                \$('#node-' + node.id).find('.config-label').text(node.config.condition);
            });
        }, 100);
    } else if (node.type === 'delay') {
        html += `
            <div class="mb-3">
                <label class="form-label">Segundos:</label>
                <input type="number" class="form-control form-control-sm" id="propSeconds" value="\${node.config.seconds || 10}">
            </div>
        `;
        setTimeout(() => {
            $('#propSeconds').on('input', function() {
                node.config.seconds = parseInt($(this).val());
                \$('#node-' + node.id).find('.config-label').text(node.config.seconds + 's');
            });
        }, 100);
    } else if (node.type === 'notification') {
        html += `
            <div class="mb-3">
                <label class="form-label">Mensagem:</label>
                <textarea class="form-control form-control-sm" id="propMessage" rows="3">\${node.config.message || ''}</textarea>
            </div>
        `;
        setTimeout(() => {
            $('#propMessage').on('input', function() {
                node.config.message = $(this).val();
                \$('#node-' + node.id).find('.config-label').text(node.config.message.substring(0, 30) + '...');
            });
        }, 100);
    } else if (node.type === 'set_variable') {
        html += `
            <div class="mb-3">
                <label class="form-label">Variável:</label>
                <input type="text" class="form-control form-control-sm" id="propVarName" value="\${node.config.var_name || ''}" placeholder="nome_variavel">
            </div>
            <div class="mb-3">
                <label class="form-label">Valor:</label>
                <input type="text" class="form-control form-control-sm" id="propVarValue" value="\${node.config.var_value || ''}" placeholder="valor">
            </div>
        `;
        setTimeout(() => {
            $('#propVarName').on('input', () => { node.config.var_name = $('#propVarName').val(); });
            $('#propVarValue').on('input', () => { node.config.var_value = $('#propVarValue').val(); });
        }, 100);
    }
    
    content.html(html);
    panel.addClass('active');
}

// Fechar propriedades
function fecharPropriedades() {
    $('#propertiesPanel').removeClass('active');
}

// Salvar workflow
function salvarWorkflow() {
    const data = {
        id_workflow: workflowId,
        nome: $('#workflowNome').val() || 'Novo Workflow',
        tipo_trigger: $('#triggerTipo').val(),
        nodes: nodes,
        edges: edges,
        ativo: true
    };
    
    // Empresas/Projetos RBAC
    if (typeof rbacGetSelectedIds === 'function') {
        data.empresas = rbacGetSelectedIds('empresas').map(Number);
        data.projetos = rbacGetSelectedIds('projetos').map(Number);
        data._rbac_presente = '1';
    }
    
    const url = workflowId ? baseUrl + '/api/workflows/update/' + workflowId : baseUrl + '/api/workflows/create';
    
    $.ajax({
        url: url,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(Object.assign({}, data, {_csrf_token: csrfToken})),
        success: function(res) {
            if (res.sucesso) {
                alert('Workflow salvo com sucesso!');
                if (!workflowId && res.id_workflow) {
                    window.location.href = baseUrl + '/workflow-builder/' + res.id_workflow;
                }
            } else {
                alert('Erro ao salvar: ' + (res.erro || 'Erro desconhecido'));
            }
        },
        error: function() {
            alert('Erro ao salvar workflow');
        }
    });
}

// Carregar workflow
function carregarWorkflow(id) {
    $.getJSON(baseUrl + '/api/workflows/get/' + id, function(res) {
        if (res.sucesso && res.dados) {
            const wf = res.dados;
            $('#workflowNome').val(wf.nome);
            $('#triggerTipo').val(wf.tipo_trigger);
            
            if (wf.nodes) {
                nodes = wf.nodes;
                nodeIdCounter = Math.max(...nodes.map(n => n.id)) + 1;
                nodes.forEach(renderizarNode);
            }
            
            if (wf.edges) {
                edges = wf.edges;
                renderizarEdges();
            }
            
            // Preencher empresas/projetos RBAC
            var empIds = (res.empresas || []).map(function(e) { return parseInt(e.id_empresa || e.id || e, 10); });
            var projIds = (res.projetos || []).map(function(p) { return parseInt(p.id_projeto || p.id || p, 10); });
            if (typeof rbacCarregarOpcoes === 'function') {
                rbacCarregarOpcoes(function() { rbacPreencherSelects(empIds, projIds); });
            }
        }
    });
}

// Executar workflow
function executarWorkflow() {
    if (!workflowId) {
        alert('Salve o workflow antes de executar');
        return;
    }
    
    if (!confirm('Deseja executar este workflow agora?')) return;
    
    $.post(baseUrl + '/api/workflows/execute/' + workflowId, {_csrf_token: csrfToken}, function(res) {
        if (res.sucesso) {
            alert('Workflow iniciado! ID de execução: ' + res.id_execucao);
        } else {
            alert('Erro: ' + (res.erro || 'Erro ao executar'));
        }
    });
}
</script>
SCRIPTS;

$extraScripts .= '<script src="' . BASE_URL . '/assets/js/rbac-recurso.js"></script>';
$extraScripts .= '<script src="' . BASE_URL . '/assets/js/rbac-compartilhamento.js"></script>';

include __DIR__ . '/layouts/base.php';
?>
