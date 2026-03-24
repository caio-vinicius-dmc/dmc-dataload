<?php
/**
 * DMC DataLoad - Arquivos Gerados
 * Gestão de arquivos CSV gerados pelas execuções de rotinas
 */
$pageTitle = 'Arquivos Gerados';
$currentPage = 'arquivos-gerados';
$csrfToken = App\Core\AuthMiddleware::gerarTokenCSRF();
$usuario = App\Core\AuthMiddleware::obterUsuario();
$ehSuperAdmin = ($usuario['nivel_acesso'] ?? '') === 'super_admin';
$ehAdmin = in_array($usuario['nivel_acesso'] ?? '', ['admin', 'super_admin']);

ob_start();
?>

<!-- Header Section -->
<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-folder2-open"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Arquivos Gerados</h1>
        <p class="page-subtitle-modern">Gerencie os arquivos CSV gerados pelas execuções de rotinas</p>
    </div>
    <div class="d-flex gap-2 ms-auto">
        <button class="btn-modern-outline" onclick="carregarArquivos()">
            <i class="bi bi-arrow-clockwise me-2"></i>Atualizar
        </button>
        <?php if ($ehAdmin): ?>
        <button class="btn-modern-outline" onclick="abrirModalPoliticas()" title="Políticas de Retenção">
            <i class="bi bi-clock-history me-2"></i>Retenção
        </button>
        <button class="btn-modern-primary" onclick="excluirSelecionados()" id="btnExcluirSelecionados" disabled>
            <i class="bi bi-trash me-2"></i>Excluir Selecionados (<span id="contadorSelecionados">0</span>)
        </button>
        <?php endif; ?>
    </div>
</div>

<!-- Filtros -->
<div class="card-modern mb-4">
    <div class="card-modern-header">
        <i class="bi bi-funnel-fill me-2"></i>
        <span>Filtros</span>
    </div>
    <div class="card-modern-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label-modern">Rotina</label>
                <select class="form-select-modern" id="filtroRotina">
                    <option value="">Todas as rotinas</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label-modern">Buscar</label>
                <input type="text" class="form-control-modern" id="filtroBusca" placeholder="Nome do arquivo ou rotina...">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-modern-primary flex-fill" onclick="aplicarFiltros()">
                    <i class="bi bi-search me-2"></i>Buscar
                </button>
                <button class="btn btn-modern-outline" onclick="limparFiltros()" title="Limpar filtros">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern success-card">
            <div class="stat-icon-modern">
                <i class="bi bi-file-earmark-check-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="statTotal">0</div>
                <div class="stat-label-modern">Total de Arquivos</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern info-card">
            <div class="stat-icon-modern">
                <i class="bi bi-hdd-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="statTamanho">0 B</div>
                <div class="stat-label-modern">Espaço Total</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern warning-card">
            <div class="stat-icon-modern">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="statAusentes">0</div>
                <div class="stat-label-modern">Arquivos Ausentes</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern primary-card">
            <div class="stat-icon-modern">
                <i class="bi bi-clock-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="statPoliticas">0</div>
                <div class="stat-label-modern">Políticas Ativas</div>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Arquivos -->
<div class="card-modern">
    <div class="card-modern-header d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-files me-2"></i>
            <span>Arquivos</span>
            <span class="badge-modern-info ms-2" id="totalRegistros">0 registros</span>
        </div>
        <?php if ($ehAdmin): ?>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-primary" onclick="selecionarTodos()" id="btnSelecionarTodos">
                <i class="bi bi-check2-all me-1"></i>Selecionar Todos
            </button>
            <button class="btn btn-sm btn-outline-danger" onclick="excluirTodosVisiveis()" title="Excluir todos os arquivos filtrados">
                <i class="bi bi-trash me-1"></i>Excluir Todos
            </button>
        </div>
        <?php endif; ?>
    </div>
    <div class="card-modern-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tabelaArquivos">
                <thead>
                    <tr>
                        <?php if ($ehAdmin): ?>
                        <th style="width: 40px;"><input type="checkbox" id="checkAll" onchange="toggleCheckAll(this)"></th>
                        <?php endif; ?>
                        <th>Arquivo</th>
                        <th>Rotina</th>
                        <th>Bloco</th>
                        <th>Registros</th>
                        <th>Tamanho</th>
                        <th>Data Execução</th>
                        <th>Status</th>
                        <th style="width: 120px;">Ações</th>
                    </tr>
                </thead>
                <tbody id="corpoTabela">
                    <tr><td colspan="9" class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="text-muted mt-2">Carregando arquivos...</p></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Políticas de Retenção -->
<div class="modal fade" id="modalPoliticas" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title"><i class="bi bi-clock-history me-2"></i>Políticas de Retenção</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Configure a exclusão automática de arquivos por rotina. Os arquivos mais antigos que o período de retenção serão excluídos automaticamente pelo scheduler.</p>
                
                <!-- Form de nova política -->
                <div class="card mb-4" style="border-radius: 12px;">
                    <div class="card-body">
                        <h6 class="mb-3"><i class="bi bi-plus-circle me-2"></i>Nova Política</h6>
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label-modern">Rotina</label>
                                <select class="form-select-modern" id="politicaRotina">
                                    <option value="">Selecione uma rotina...</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-modern">Retenção (dias)</label>
                                <input type="number" class="form-control-modern" id="politicaDias" min="1" value="30" placeholder="30">
                            </div>
                            <div class="col-md-2">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="politicaAtivo" checked>
                                    <label class="form-check-label" for="politicaAtivo">Ativo</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-modern-primary w-100" onclick="salvarPolitica()">
                                    <i class="bi bi-save me-1"></i>Salvar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de políticas existentes -->
                <h6 class="mb-3"><i class="bi bi-list-check me-2"></i>Políticas Configuradas</h6>
                <div id="listaPoliticas">
                    <div class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm" role="status"></div>
                        Carregando...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$extraStyles = <<<'STYLES'
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

.page-header-modern { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
.page-icon-modern { width: 56px; height: 56px; display: flex; align-items: center; justify-content: center; border-radius: 16px; background: var(--gradient-primary); color: white; font-size: 1.5rem; box-shadow: var(--shadow-md); }
.page-title-modern { font-size: 1.75rem; font-weight: 700; color: #1e293b; margin: 0; }
.page-subtitle-modern { color: #64748b; margin: 0; font-size: 0.9rem; }

.card-modern { background: white; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm); border: 1px solid #e2e8f0; overflow: hidden; }
.card-modern-header { padding: 1rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #1e293b; display: flex; align-items: center; }
.card-modern-body { padding: 1.5rem; }

.stat-card-modern { position: relative; display: flex; align-items: center; gap: 1rem; padding: 1.25rem; border-radius: var(--radius-md); background: white; box-shadow: var(--shadow-sm); border: 1px solid #e2e8f0; overflow: hidden; }
.stat-icon-modern { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
.success-card .stat-icon-modern { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.info-card .stat-icon-modern { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.warning-card .stat-icon-modern { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.primary-card .stat-icon-modern { background: rgba(102, 126, 234, 0.1); color: #667eea; }
.stat-value-modern { font-size: 1.5rem; font-weight: 700; color: #1e293b; line-height: 1; }
.stat-label-modern { font-size: 0.8rem; color: #64748b; margin-top: 2px; }

.form-label-modern { font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.25rem; display: block; }
.form-control-modern, .form-select-modern { width: 100%; border: 2px solid #e2e8f0; border-radius: var(--radius-md); padding: 0.75rem 1rem; font-size: 0.9375rem; transition: all 0.2s ease; background-color: #ffffff; color: #1e293b; }
.form-control-modern:focus, .form-select-modern:focus { border-color: #667eea; box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1); outline: none; }

.btn-modern-primary { background: var(--gradient-primary); color: white; border: none; padding: 0.6rem 1.25rem; border-radius: var(--radius-md); font-weight: 600; transition: all 0.2s; cursor: pointer; }
.btn-modern-primary:hover { transform: translateY(-1px); box-shadow: var(--shadow-md); color: white; }
.btn-modern-outline { background: white; color: #667eea; border: 2px solid #667eea; padding: 0.6rem 1.25rem; border-radius: var(--radius-md); font-weight: 600; transition: all 0.2s; cursor: pointer; }
.btn-modern-outline:hover { background: #667eea; color: white; }

.badge-modern-info { background: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 0.35rem 0.75rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }

.table th { background: #f8fafc; color: #64748b; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 0.75rem 1rem; border-bottom: 2px solid #e2e8f0; }
.table td { padding: 0.75rem 1rem; vertical-align: middle; border-bottom: 1px solid #f1f5f9; color: #374151; font-size: 0.9rem; }
.table tbody tr:hover { background: #f8fafc; }

.arquivo-nome { font-weight: 600; color: #1e293b; font-size: 0.85rem; }
.arquivo-nome i { color: #667eea; }

.status-badge { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
.status-badge.sucesso { background: rgba(16, 185, 129, 0.1); color: #059669; }
.status-badge.falha, .status-badge.erro { background: rgba(239, 68, 68, 0.1); color: #dc2626; }
.status-badge.executando { background: rgba(245, 158, 11, 0.1); color: #d97706; }

.ausente-badge { background: rgba(239, 68, 68, 0.08); color: #dc2626; font-size: 0.75rem; padding: 0.15rem 0.5rem; border-radius: 8px; }

.politica-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; margin-bottom: 0.75rem; display: flex; justify-content: space-between; align-items: center; }
.politica-card .politica-info { flex: 1; }
.politica-card .politica-nome { font-weight: 600; color: #1e293b; }
.politica-card .politica-detalhe { font-size: 0.85rem; color: #64748b; }

.empty-state { text-align: center; padding: 3rem 1rem; }
.empty-state i { font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem; display: block; }
.empty-state h5 { color: #64748b; font-weight: 600; }
.empty-state p { color: #94a3b8; }
</style>
STYLES;

$extraScripts = '<script>const csrfToken = \'' . htmlspecialchars($csrfToken, ENT_QUOTES) . '\'; const ehAdmin = ' . ($ehAdmin ? 'true' : 'false') . ';</script>';
$extraScripts .= <<<'SCRIPTS'
<script>
let todosArquivos = [];
let arquivosFiltrados = [];
let selecionados = new Set();
let todosSelecionados = false;

function formatBytes(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function carregarArquivos() {
    const rotina = document.getElementById('filtroRotina').value;
    const busca = document.getElementById('filtroBusca').value;
    let url = baseUrl + '/api/arquivos-gerados?';
    if (rotina) url += 'id_rotina=' + rotina + '&';
    if (busca) url += 'busca=' + encodeURIComponent(busca) + '&';

    fetch(url)
        .then(r => r.json())
        .then(res => {
            if (!res.sucesso) { Swal.fire('Erro', res.erro, 'error'); return; }
            todosArquivos = res.arquivos;
            arquivosFiltrados = [...todosArquivos];
            selecionados.clear();
            atualizarContadorSelecionados();
            renderizarTabela();
            atualizarEstatisticas(res);
        })
        .catch(err => {
            Swal.fire('Erro', 'Falha ao carregar arquivos', 'error');
            console.error(err);
        });
}

function atualizarEstatisticas(res) {
    document.getElementById('statTotal').textContent = res.total;
    document.getElementById('statTamanho').textContent = formatBytes(res.tamanho_total);
    const ausentes = res.arquivos.filter(a => !a.existe).length;
    document.getElementById('statAusentes').textContent = ausentes;
    document.getElementById('totalRegistros').textContent = res.total + ' registros';

    // Carregar políticas para o stat
    fetch(baseUrl + '/api/arquivos-gerados/politicas')
        .then(r => r.json())
        .then(data => {
            if (data.sucesso) {
                const ativas = data.politicas.filter(p => p.ativo).length;
                document.getElementById('statPoliticas').textContent = ativas;
            }
        });
}

function renderizarTabela() {
    const tbody = document.getElementById('corpoTabela');
    if (arquivosFiltrados.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9"><div class="empty-state"><i class="bi bi-folder2-open"></i><h5>Nenhum arquivo encontrado</h5><p>Nenhum arquivo CSV foi gerado ou os filtros aplicados não retornaram resultados.</p></div></td></tr>';
        return;
    }

    let html = '';
    arquivosFiltrados.forEach((arq, idx) => {
        const chave = arq.log_id + '-' + (arq.bloco_index ?? 'main');
        const checked = selecionados.has(chave) ? 'checked' : '';
        const statusClass = arq.status_execucao === 'sucesso' ? 'sucesso' : (arq.status_execucao === 'falha' || arq.status_execucao === 'erro' ? 'falha' : 'executando');

        html += '<tr>';
        if (ehAdmin) {
            html += '<td><input type="checkbox" class="check-arquivo" data-chave="' + chave + '" data-idx="' + idx + '" ' + checked + ' onchange="toggleSelecao(this)"></td>';
        }
        html += '<td><div class="arquivo-nome"><i class="bi bi-file-earmark-spreadsheet me-2"></i>' + escapeHtml(arq.nome_arquivo) + '</div>';
        if (!arq.existe) html += '<span class="ausente-badge"><i class="bi bi-exclamation-circle me-1"></i>Ausente no disco</span>';
        html += '</td>';
        html += '<td>' + escapeHtml(arq.nome_rotina) + '</td>';
        html += '<td><span class="text-muted">' + escapeHtml(arq.bloco_nome) + '</span></td>';
        html += '<td>' + (arq.registros !== null ? arq.registros.toLocaleString('pt-BR') : '-') + '</td>';
        html += '<td>' + (arq.existe ? formatBytes(arq.tamanho) : '<span class="text-muted">-</span>') + '</td>';
        html += '<td>' + (arq.data_execucao ? new Date(arq.data_execucao).toLocaleString('pt-BR') : '-') + '</td>';
        html += '<td><span class="status-badge ' + statusClass + '">' + (arq.status_execucao || '-').toUpperCase() + '</span></td>';
        html += '<td><div class="d-flex gap-1">';
        if (arq.existe) {
            const downloadUrl = arq.bloco_index !== null
                ? baseUrl + '/api/download-csv-bloco/' + arq.log_id + '/' + arq.bloco_index
                : baseUrl + '/api/download-csv/' + arq.log_id;
            html += '<a href="' + downloadUrl + '" class="btn btn-sm btn-outline-success" title="Download"><i class="bi bi-download"></i></a>';
        }
        if (ehAdmin) {
            html += '<button class="btn btn-sm btn-outline-danger" onclick="excluirArquivo(' + idx + ')" title="Excluir"><i class="bi bi-trash"></i></button>';
        }
        html += '</div></td></tr>';
    });

    tbody.innerHTML = html;
}

function escapeHtml(text) {
    if (!text) return '';
    const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
    return String(text).replace(/[&<>"']/g, m => map[m]);
}

function toggleSelecao(cb) {
    const chave = cb.dataset.chave;
    if (cb.checked) {
        selecionados.add(chave);
    } else {
        selecionados.delete(chave);
    }
    atualizarContadorSelecionados();
}

function toggleCheckAll(cb) {
    const checkboxes = document.querySelectorAll('.check-arquivo');
    checkboxes.forEach(c => {
        const chave = c.dataset.chave;
        c.checked = cb.checked;
        if (cb.checked) selecionados.add(chave); else selecionados.delete(chave);
    });
    atualizarContadorSelecionados();
}

function selecionarTodos() {
    todosSelecionados = !todosSelecionados;
    const btn = document.getElementById('btnSelecionarTodos');
    if (todosSelecionados) {
        arquivosFiltrados.forEach((arq, idx) => {
            selecionados.add(arq.log_id + '-' + (arq.bloco_index ?? 'main'));
        });
        btn.innerHTML = '<i class="bi bi-x-lg me-1"></i>Desmarcar Todos';
    } else {
        selecionados.clear();
        btn.innerHTML = '<i class="bi bi-check2-all me-1"></i>Selecionar Todos';
    }
    atualizarContadorSelecionados();
    renderizarTabela();
}

function atualizarContadorSelecionados() {
    const span = document.getElementById('contadorSelecionados');
    const btn = document.getElementById('btnExcluirSelecionados');
    if (span) span.textContent = selecionados.size;
    if (btn) btn.disabled = selecionados.size === 0;
}

function excluirArquivo(idx) {
    const arq = arquivosFiltrados[idx];
    Swal.fire({
        title: 'Excluir arquivo?',
        html: 'O arquivo <strong>' + escapeHtml(arq.nome_arquivo) + '</strong> será excluído permanentemente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: '<i class="bi bi-trash me-1"></i>Excluir',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            executarExclusao([{log_id: arq.log_id, bloco_index: arq.bloco_index}]);
        }
    });
}

function excluirSelecionados() {
    if (selecionados.size === 0) return;
    const arquivosParaExcluir = [];
    selecionados.forEach(chave => {
        const parts = chave.split('-');
        const logId = parseInt(parts[0]);
        const blocoIndex = parts[1] === 'main' ? null : parseInt(parts[1]);
        arquivosParaExcluir.push({log_id: logId, bloco_index: blocoIndex});
    });

    Swal.fire({
        title: 'Excluir ' + arquivosParaExcluir.length + ' arquivo(s)?',
        text: 'Esta ação não pode ser desfeita.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: '<i class="bi bi-trash me-1"></i>Excluir Todos',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            executarExclusao(arquivosParaExcluir);
        }
    });
}

function excluirTodosVisiveis() {
    if (arquivosFiltrados.length === 0) return;
    const arquivosParaExcluir = arquivosFiltrados.map(a => ({log_id: a.log_id, bloco_index: a.bloco_index}));

    Swal.fire({
        title: 'Excluir TODOS os ' + arquivosParaExcluir.length + ' arquivo(s) visíveis?',
        html: '<strong class="text-danger">Esta ação é irreversível!</strong><br>Todos os arquivos listados serão excluídos permanentemente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: '<i class="bi bi-trash me-1"></i>Sim, excluir todos',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) {
            executarExclusao(arquivosParaExcluir);
        }
    });
}

function executarExclusao(arquivos) {
    Swal.fire({title: 'Excluindo...', allowOutsideClick: false, didOpen: () => Swal.showLoading()});

    fetch(baseUrl + '/api/arquivos-gerados/excluir', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({arquivos: arquivos, _csrf_token: csrfToken})
    })
    .then(r => r.json())
    .then(res => {
        Swal.close();
        if (res.sucesso) {
            Swal.fire({
                icon: 'success',
                title: 'Concluído',
                text: res.mensagem,
                timer: 2000,
                showConfirmButton: false
            });
            carregarArquivos();
        } else {
            Swal.fire('Erro', res.erro, 'error');
        }
    })
    .catch(err => {
        Swal.close();
        Swal.fire('Erro', 'Falha na comunicação com o servidor', 'error');
    });
}

function aplicarFiltros() {
    carregarArquivos();
}

function limparFiltros() {
    document.getElementById('filtroRotina').value = '';
    document.getElementById('filtroBusca').value = '';
    carregarArquivos();
}

// ========== POLÍTICAS DE RETENÇÃO ==========

function abrirModalPoliticas() {
    const modal = new bootstrap.Modal('#modalPoliticas');
    modal.show();
    carregarPoliticas();
    carregarRotinasDropdown();
}

function carregarRotinasDropdown() {
    fetch(baseUrl + '/api/arquivos-gerados/rotinas')
        .then(r => r.json())
        .then(res => {
            if (!res.sucesso) return;
            const select = document.getElementById('politicaRotina');
            const selectFiltro = document.getElementById('filtroRotina');
            // Preencher dropdown do modal
            select.innerHTML = '<option value="">Selecione uma rotina...</option>';
            res.rotinas.forEach(r => {
                select.innerHTML += '<option value="' + r.id + '">' + escapeHtml(r.nome) + '</option>';
            });
            // Preencher dropdown de filtro se estiver vazio
            if (selectFiltro.options.length <= 1) {
                selectFiltro.innerHTML = '<option value="">Todas as rotinas</option>';
                res.rotinas.forEach(r => {
                    selectFiltro.innerHTML += '<option value="' + r.id + '">' + escapeHtml(r.nome) + '</option>';
                });
            }
        });
}

function carregarPoliticas() {
    const container = document.getElementById('listaPoliticas');
    fetch(baseUrl + '/api/arquivos-gerados/politicas')
        .then(r => r.json())
        .then(res => {
            if (!res.sucesso) { container.innerHTML = '<div class="text-danger">Erro: ' + res.erro + '</div>'; return; }
            if (res.politicas.length === 0) {
                container.innerHTML = '<div class="text-center py-3 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2"></i>Nenhuma política configurada</div>';
                return;
            }
            let html = '';
            res.politicas.forEach(p => {
                const statusBadge = p.ativo
                    ? '<span class="badge bg-success">Ativo</span>'
                    : '<span class="badge bg-secondary">Inativo</span>';
                html += '<div class="politica-card">';
                html += '<div class="politica-info">';
                html += '<div class="politica-nome">' + escapeHtml(p.nome_rotina) + ' ' + statusBadge + '</div>';
                html += '<div class="politica-detalhe"><i class="bi bi-clock me-1"></i>Excluir arquivos com mais de <strong>' + p.dias_retencao + '</strong> dia(s)</div>';
                html += '</div>';
                html += '<div class="d-flex gap-2">';
                html += '<button class="btn btn-sm btn-outline-danger" onclick="excluirPolitica(' + p.id + ')" title="Excluir política"><i class="bi bi-trash"></i></button>';
                html += '</div>';
                html += '</div>';
            });
            container.innerHTML = html;
        })
        .catch(() => { container.innerHTML = '<div class="text-danger">Erro ao carregar políticas</div>'; });
}

function salvarPolitica() {
    const idRotina = document.getElementById('politicaRotina').value;
    const dias = document.getElementById('politicaDias').value;
    const ativo = document.getElementById('politicaAtivo').checked;

    if (!idRotina) { Swal.fire('Atenção', 'Selecione uma rotina', 'warning'); return; }
    if (!dias || dias < 1) { Swal.fire('Atenção', 'Informe um período válido (mínimo 1 dia)', 'warning'); return; }

    fetch(baseUrl + '/api/arquivos-gerados/politicas/salvar', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
        body: JSON.stringify({id_rotina: parseInt(idRotina), dias_retencao: parseInt(dias), ativo: ativo, _csrf_token: csrfToken})
    })
    .then(r => r.json())
    .then(res => {
        if (res.sucesso) {
            Swal.fire({icon: 'success', title: 'Salvo!', text: res.mensagem, timer: 1500, showConfirmButton: false});
            carregarPoliticas();
            document.getElementById('politicaRotina').value = '';
            document.getElementById('politicaDias').value = '30';
        } else {
            Swal.fire('Erro', res.erro, 'error');
        }
    });
}

function excluirPolitica(id) {
    Swal.fire({
        title: 'Excluir política?',
        text: 'A política de retenção será removida.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Excluir'
    }).then(result => {
        if (result.isConfirmed) {
            fetch(baseUrl + '/api/arquivos-gerados/politicas/excluir', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken},
                body: JSON.stringify({id: id, _csrf_token: csrfToken})
            })
            .then(r => r.json())
            .then(res => {
                if (res.sucesso) {
                    Swal.fire({icon: 'success', title: 'Excluído!', timer: 1500, showConfirmButton: false});
                    carregarPoliticas();
                } else {
                    Swal.fire('Erro', res.erro, 'error');
                }
            });
        }
    });
}

// ========== INIT ==========
$(document).ready(function() {
    carregarArquivos();
    carregarRotinasDropdown();
});
</script>
SCRIPTS;

include __DIR__ . '/layouts/base.php';
