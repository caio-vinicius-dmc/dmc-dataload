<?php
/**
 * DMC DataLoad - Logs de Auditoria
 * Registro de todas as ações administrativas
 */
$pageTitle = 'Auditoria';
$currentPage = 'auditoria';

ob_start();
?>

<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-shield-check"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Logs de Auditoria</h1>
        <p class="page-subtitle-modern">Registro de todas as ações administrativas do sistema</p>
    </div>
    <div class="ms-auto">
        <button class="btn btn-outline-primary" onclick="exportarAuditoria()">
            <i class="bi bi-download me-2"></i>Exportar CSV
        </button>
    </div>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
    <div class="col-6 col-lg-2">
        <div class="stat-card primary">
            <div class="stat-icon"><i class="bi bi-list-check"></i></div>
            <div class="stat-value" id="statTotal">0</div>
            <div class="stat-label">Total</div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card success">
            <div class="stat-icon"><i class="bi bi-plus-circle"></i></div>
            <div class="stat-value" id="statCriados">0</div>
            <div class="stat-label">Criações</div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card info">
            <div class="stat-icon"><i class="bi bi-pencil"></i></div>
            <div class="stat-value" id="statEditados">0</div>
            <div class="stat-label">Edições</div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card danger">
            <div class="stat-icon"><i class="bi bi-trash"></i></div>
            <div class="stat-value" id="statExcluidos">0</div>
            <div class="stat-label">Exclusões</div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card warning">
            <div class="stat-icon"><i class="bi bi-door-open"></i></div>
            <div class="stat-value" id="statLogins">0</div>
            <div class="stat-label">Logins</div>
        </div>
    </div>
    <div class="col-6 col-lg-2">
        <div class="stat-card secondary">
            <div class="stat-icon"><i class="bi bi-people"></i></div>
            <div class="stat-value" id="statUsuarios">0</div>
            <div class="stat-label">Usuários</div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-2">
                <label class="form-label">Ação</label>
                <select class="form-select form-select-sm" id="filtroAcao">
                    <option value="">Todas</option>
                    <option value="criar">Criar</option>
                    <option value="editar">Editar</option>
                    <option value="excluir">Excluir</option>
                    <option value="login">Login</option>
                    <option value="logout">Logout</option>
                    <option value="compartilhar">Compartilhar</option>
                    <option value="executar">Executar</option>
                    <option value="importar">Importar</option>
                    <option value="limpar">Limpar</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Entidade</label>
                <select class="form-select form-select-sm" id="filtroEntidade">
                    <option value="">Todas</option>
                    <option value="rotina">Rotinas</option>
                    <option value="pipeline">Pipelines</option>
                    <option value="workflow">Workflows</option>
                    <option value="conexao">Conexões</option>
                    <option value="usuario">Usuários</option>
                    <option value="empresa">Empresas</option>
                    <option value="projeto">Projetos</option>
                    <option value="configuracao">Configurações</option>
                    <option value="webhook">Webhooks</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">De</label>
                <input type="date" class="form-control form-control-sm" id="filtroDe">
            </div>
            <div class="col-md-2">
                <label class="form-label">Até</label>
                <input type="date" class="form-control form-control-sm" id="filtroAte">
            </div>
            <div class="col-md-3">
                <label class="form-label">Busca</label>
                <input type="text" class="form-control form-control-sm" id="filtroBusca" placeholder="Buscar por nome, ação...">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button class="btn btn-primary btn-sm w-100" onclick="carregarAuditoria()">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tabela -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0" id="tblAuditoria">
                <thead class="table-light">
                    <tr>
                        <th width="140">Data/Hora</th>
                        <th width="90">Ação</th>
                        <th width="100">Entidade</th>
                        <th>Recurso</th>
                        <th width="130">Usuário</th>
                        <th width="90">Nível</th>
                        <th width="110">IP</th>
                        <th width="50">Det.</th>
                    </tr>
                </thead>
                <tbody id="tbodyAuditoria">
                    <tr><td colspan="8" class="text-center py-4"><div class="spinner-border spinner-border-sm text-primary"></div> Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span class="text-muted" id="infoRegistros">-</span>
        <nav>
            <ul class="pagination pagination-sm mb-0" id="paginacao"></ul>
        </nav>
    </div>
</div>

<!-- Modal Detalhes -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Detalhes da Ação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalhesConteudo"></div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$extraStyles = '
<style>
.stat-card .stat-value { font-size: 1.5rem; }
.stat-card.info { border-left-color: #0ea5e9; }
.stat-card.info .stat-icon { color: #0ea5e9; }
.stat-card.danger { border-left-color: #ef4444; }
.stat-card.danger .stat-icon { color: #ef4444; }
.stat-card.secondary { border-left-color: #64748b; }
.stat-card.secondary .stat-icon { color: #64748b; }
.page-header-modern { background: white; padding: 1.75rem 2rem; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; }
.page-icon-modern { width: 70px; height: 70px; border-radius: 16px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: white; box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3); flex-shrink: 0; }
.page-title-modern { font-size: 2rem; font-weight: 700; margin: 0 0 0.25rem 0; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.page-subtitle-modern { color: #64748b; margin: 0; font-size: 1rem; }
.badge-acao { font-size: 0.7rem; font-weight: 600; }
</style>
';

$extraScripts = <<<'SCRIPTS'
<script>
const acaoColors = {
    criar: 'success', editar: 'info', excluir: 'danger', login: 'warning',
    logout: 'secondary', compartilhar: 'primary', executar: 'dark', importar: 'info', limpar: 'danger'
};
const acaoIcons = {
    criar: 'plus-circle', editar: 'pencil', excluir: 'trash', login: 'door-open',
    logout: 'door-closed', compartilhar: 'share', executar: 'play', importar: 'upload', limpar: 'eraser'
};
let paginaAtual = 1;

function carregarAuditoria(pagina) {
    paginaAtual = pagina || 1;
    const params = new URLSearchParams();
    params.set('pagina', paginaAtual);
    
    const acao = document.getElementById('filtroAcao').value;
    const entidade = document.getElementById('filtroEntidade').value;
    const de = document.getElementById('filtroDe').value;
    const ate = document.getElementById('filtroAte').value;
    const busca = document.getElementById('filtroBusca').value;
    
    if (acao) params.set('acao', acao);
    if (entidade) params.set('entidade', entidade);
    if (de) params.set('data_de', de);
    if (ate) params.set('data_ate', ate);
    if (busca) params.set('busca', busca);
    
    $.getJSON(baseUrl + "/api/auditoria?" + params.toString(), function(res) {
        const tbody = document.getElementById('tbodyAuditoria');
        tbody.innerHTML = '';
        
        if (!res.registros || res.registros.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Nenhum registro encontrado</td></tr>';
        } else {
            res.registros.forEach(function(r) {
                const dt = new Date(r.criado_em);
                const data = dt.toLocaleDateString('pt-BR') + ' ' + dt.toLocaleTimeString('pt-BR');
                const color = acaoColors[r.acao] || 'secondary';
                const icon = acaoIcons[r.acao] || 'circle';
                
                tbody.innerHTML += `<tr>
                    <td><small>${data}</small></td>
                    <td><span class="badge bg-${color} badge-acao"><i class="bi bi-${icon} me-1"></i>${r.acao}</span></td>
                    <td><small class="text-muted">${r.entidade}</small></td>
                    <td>${r.entidade_nome || '-'} ${r.entidade_id ? '<small class="text-muted">#'+r.entidade_id+'</small>' : ''}</td>
                    <td><strong>${r.nome_usuario}</strong></td>
                    <td><span class="badge bg-outline-secondary">${r.nivel_acesso || '-'}</span></td>
                    <td><small class="text-muted">${r.ip_address || '-'}</small></td>
                    <td><button class="btn btn-outline-primary btn-sm py-0 px-1" onclick='verDetalhes(${JSON.stringify(r)})'><i class="bi bi-eye"></i></button></td>
                </tr>`;
            });
        }
        
        // Stats
        if (res.estatisticas) {
            const s = res.estatisticas;
            document.getElementById('statTotal').textContent = s.total || 0;
            document.getElementById('statCriados').textContent = s.criados || 0;
            document.getElementById('statEditados').textContent = s.editados || 0;
            document.getElementById('statExcluidos').textContent = s.excluidos || 0;
            document.getElementById('statLogins').textContent = s.logins || 0;
            document.getElementById('statUsuarios').textContent = s.usuarios_unicos || 0;
        }
        
        // Info
        document.getElementById('infoRegistros').textContent = 
            `Mostrando ${res.registros.length} de ${res.total} registros (Página ${res.pagina})`;
        
        // Pagination
        const totalPages = Math.ceil(res.total / res.por_pagina);
        const pag = document.getElementById('paginacao');
        pag.innerHTML = '';
        for (let i = 1; i <= Math.min(totalPages, 10); i++) {
            pag.innerHTML += `<li class="page-item ${i === paginaAtual ? 'active' : ''}">
                <a class="page-link" href="#" onclick="carregarAuditoria(${i}); return false;">${i}</a>
            </li>`;
        }
    });
}

function verDetalhes(r) {
    let anteriores = '{}', novos = '{}';
    try { anteriores = JSON.stringify(JSON.parse(r.dados_anteriores || '{}'), null, 2); } catch(e) { anteriores = r.dados_anteriores || '{}'; }
    try { novos = JSON.stringify(JSON.parse(r.dados_novos || '{}'), null, 2); } catch(e) { novos = r.dados_novos || '{}'; }
    
    document.getElementById('detalhesConteudo').innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Informações</h6>
                <table class="table table-sm">
                    <tr><td class="text-muted">Ação</td><td><strong>${r.acao}</strong></td></tr>
                    <tr><td class="text-muted">Entidade</td><td>${r.entidade}</td></tr>
                    <tr><td class="text-muted">ID Recurso</td><td>${r.entidade_id || '-'}</td></tr>
                    <tr><td class="text-muted">Nome</td><td>${r.entidade_nome || '-'}</td></tr>
                    <tr><td class="text-muted">Usuário</td><td>${r.nome_usuario} (${r.nivel_acesso})</td></tr>
                    <tr><td class="text-muted">IP</td><td>${r.ip_address || '-'}</td></tr>
                    <tr><td class="text-muted">Data</td><td>${new Date(r.criado_em).toLocaleString('pt-BR')}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>User Agent</h6>
                <small class="text-muted">${r.user_agent || '-'}</small>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <h6>Dados Anteriores</h6>
                <pre class="bg-light p-2 rounded" style="max-height:200px;overflow:auto;font-size:11px;">${anteriores}</pre>
            </div>
            <div class="col-md-6">
                <h6>Dados Novos</h6>
                <pre class="bg-light p-2 rounded" style="max-height:200px;overflow:auto;font-size:11px;">${novos}</pre>
            </div>
        </div>
    `;
    new bootstrap.Modal('#modalDetalhes').show();
}

function exportarAuditoria() {
    const params = new URLSearchParams();
    const acao = document.getElementById('filtroAcao').value;
    const entidade = document.getElementById('filtroEntidade').value;
    const de = document.getElementById('filtroDe').value;
    const ate = document.getElementById('filtroAte').value;
    if (acao) params.set('acao', acao);
    if (entidade) params.set('entidade', entidade);
    if (de) params.set('data_de', de);
    if (ate) params.set('data_ate', ate);
    window.location.href = baseUrl + '/api/auditoria/exportar?' + params.toString();
}

$(document).ready(function() {
    carregarAuditoria(1);
});
</script>
SCRIPTS;

include __DIR__ . '/../layouts/base.php';
?>
