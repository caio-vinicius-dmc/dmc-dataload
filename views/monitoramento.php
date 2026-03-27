<?php
/**
 * DMC DataLoad - Monitoramento de Banco de Dados
 * Profiler em tempo real similar ao SQL Server Profiler
 */
$pageTitle = 'Monitoramento';
$currentPage = 'monitoramento';

ob_start();
?>

<!-- Page Header -->
<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-speedometer2"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Monitoramento de Banco de Dados</h1>
        <p class="page-subtitle-modern">Visualize sessões e transações ativas em tempo real</p>
    </div>
</div>

<!-- Seleção de Conexão -->
<div class="card mb-4" id="cardConexao">
    <div class="card-body">
        <div class="row align-items-end g-3">
            <div class="col-md-5">
                <label class="form-label fw-semibold">
                    <i class="bi bi-database me-1"></i> Conexão
                </label>
                <select class="form-select" id="selectConexao">
                    <option value="">Selecione uma conexão...</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">
                    <i class="bi bi-arrow-repeat me-1"></i> Intervalo (seg)
                </label>
                <select class="form-select" id="selectIntervalo">
                    <option value="realtime">⚡ Tempo real</option>
                    <option value="1">1 segundo</option>
                    <option value="3" selected>3 segundos</option>
                    <option value="5">5 segundos</option>
                    <option value="10">10 segundos</option>
                    <option value="15">15 segundos</option>
                    <option value="30">30 segundos</option>
                    <option value="60">60 segundos</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-success flex-fill" id="btnIniciar" disabled>
                    <i class="bi bi-play-fill me-1"></i> Iniciar
                </button>
                <button class="btn btn-danger flex-fill d-none" id="btnParar">
                    <i class="bi bi-stop-fill me-1"></i> Parar
                </button>
                <button class="btn btn-outline-secondary" id="btnRefresh" disabled title="Atualizar agora">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>

        <!-- Aviso de acesso -->
        <div class="mt-3 d-none" id="avisoAcesso"></div>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row g-3 mb-4 d-none" id="rowEstatisticas">
    <div class="col-md-2">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-primary" id="statTotal">0</div>
                <small class="text-muted">Total Sessões</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-success" id="statAtivas">0</div>
                <small class="text-muted">Ativas</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-warning" id="statOciosas">0</div>
                <small class="text-muted">Ociosas</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-info" id="statIdleTx">0</div>
                <small class="text-muted">Idle in TX</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-danger" id="statLocks">0</div>
                <small class="text-muted">Locks Bloq.</small>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center h-100">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-secondary" id="statTamanho">-</div>
                <small class="text-muted">Tam. Banco</small>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3 d-none" id="cardFiltros">
    <div class="card-body py-2">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <span class="fw-semibold text-muted"><i class="bi bi-funnel me-1"></i>Filtros:</span>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control form-control-sm" id="filtroUsuario" placeholder="Usuário do banco...">
            </div>
            <div class="col-md-2">
                <select class="form-select form-select-sm" id="filtroEstado">
                    <option value="">Todos os estados</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control form-control-sm" id="filtroBanco" placeholder="Nome do banco...">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control form-control-sm" id="filtroTextoQuery" placeholder="Busca na query...">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-secondary btn-sm" id="btnLimparFiltros" title="Limpar filtros">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Performance Charts - Grafana Style -->
<div class="row g-3 mb-4 d-none" id="rowGraficos">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <span class="fw-semibold small"><i class="bi bi-speedometer me-1"></i>Conexões</span>
                <span class="badge bg-secondary small" id="labelConexoesPct">0%</span>
            </div>
            <div class="card-body p-2" style="height:180px;">
                <canvas id="chartConexoes"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <span class="fw-semibold small"><i class="bi bi-lightning me-1"></i>Cache Hit Ratio</span>
                <span class="badge bg-success small" id="labelCacheHit">0%</span>
            </div>
            <div class="card-body p-2" style="height:180px;">
                <canvas id="chartCacheHit"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <span class="fw-semibold small"><i class="bi bi-arrow-left-right me-1"></i>Transações</span>
                <span class="badge bg-info small" id="labelTxSec">0/s</span>
            </div>
            <div class="card-body p-2" style="height:180px;">
                <canvas id="chartTransacoes"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <span class="fw-semibold small"><i class="bi bi-people me-1"></i>Sessões Ativas vs Ociosas</span>
            </div>
            <div class="card-body p-2" style="height:180px;">
                <canvas id="chartSessoes"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <span class="fw-semibold small"><i class="bi bi-cpu me-1"></i>Carga do Banco</span>
                <span class="badge bg-warning small" id="labelCpuPct">0%</span>
            </div>
            <div class="card-body p-2" style="height:180px;">
                <canvas id="chartCpu"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <span class="fw-semibold small"><i class="bi bi-memory me-1"></i>Memória</span>
                <span class="badge bg-purple small" id="labelMemPct" style="background:#8b5cf6 !important;">0%</span>
            </div>
            <div class="card-body p-2" style="height:180px;">
                <canvas id="chartMemoria"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Sessões -->
<div class="card d-none" id="cardSessoes">
    <div class="card-header py-2">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <ul class="nav nav-tabs card-header-tabs" id="tabsMonitor" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-1 px-3" id="tab-sessoes" data-bs-toggle="tab" data-bs-target="#pane-sessoes" type="button" role="tab">
                            <i class="bi bi-list-ul me-1"></i>Sessões
                            <span class="badge bg-primary ms-1" id="badgeTotal">0</span>
                            <span class="badge bg-success ms-1 d-none" id="badgeAtivas">0 ativas</span>
                            <span class="badge bg-secondary ms-1 d-none" id="badgeFinalizadas">0 finalizadas</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-1 px-3" id="tab-transacoes" data-bs-toggle="tab" data-bs-target="#pane-transacoes" type="button" role="tab">
                            <i class="bi bi-arrow-left-right me-1"></i>Transações & I/O
                        </button>
                    </li>
                </ul>
            </div>
            <div>
                <span class="badge bg-info ms-1 d-none" id="badgeUsuarioConexao"></span>
                <span class="badge bg-warning text-dark ms-1 d-none" id="badgePgLegacy" title="PostgreSQL < 9.2: poller background ativo capturando queries a cada 15ms."><i class="bi bi-exclamation-triangle-fill me-1"></i>PG 8.x — captura contínua</span>
                <span class="text-muted small" id="ultimaAtualizacao"></span>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="tab-content">
            <!-- Aba Sessões -->
            <div class="tab-pane fade show active" id="pane-sessoes" role="tabpanel">
                <div class="table-responsive" style="max-height: 65vh; overflow-y: auto;">
                    <table class="table table-sm table-hover table-striped mb-0" id="tabelaSessoes">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th style="width:70px; cursor:pointer;" data-sort="id_sessao">PID <i class="bi bi-arrow-down-up small"></i></th>
                                <th style="width:110px; cursor:pointer;" data-sort="usuario">Usuário <i class="bi bi-arrow-down-up small"></i></th>
                                <th style="width:110px; cursor:pointer;" data-sort="banco">Banco <i class="bi bi-arrow-down-up small"></i></th>
                                <th style="width:90px; cursor:pointer;" data-sort="estado">Estado <i class="bi bi-arrow-down-up small"></i></th>
                                <th style="width:140px; cursor:pointer;" data-sort="inicio_query">Início <i class="bi bi-arrow-down-up small"></i></th>
                                <th style="width:100px; cursor:pointer;" data-sort="duracao_segundos">Duração<i class="bi bi-arrow-down-up small"></i></th>
                                <th style="width:110px; cursor:pointer;" data-sort="tipo_espera">Espera <i class="bi bi-arrow-down-up small"></i></th>
                                <th style="width:130px; cursor:pointer;" data-sort="aplicacao">Aplicação <i class="bi bi-arrow-down-up small"></i></th>
                                <th style="width:120px; cursor:pointer;" data-sort="ip_cliente">IP Cliente <i class="bi bi-arrow-down-up small"></i></th>
                                <th>Query</th>
                            </tr>
                        </thead>
                        <tbody id="tbodySessoes">
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">
                                    <i class="bi bi-database-slash fs-1 d-block mb-2"></i>
                                    Selecione uma conexão e clique em <strong>Iniciar</strong> para monitorar.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Aba Transações & I/O -->
            <div class="tab-pane fade" id="pane-transacoes" role="tabpanel">
                <div class="p-3">
                    <div class="row g-3 mb-3">
                        <!-- Commits -->
                        <div class="col-md-3 col-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center py-3">
                                    <i class="bi bi-check-circle fs-3 text-success d-block mb-1"></i>
                                    <div class="fs-3 fw-bold text-success" id="txCommitTotal">0</div>
                                    <small class="text-muted">Total Commits</small>
                                    <div class="mt-1"><span class="badge bg-success" id="txCommitSec">0/s</span></div>
                                </div>
                            </div>
                        </div>
                        <!-- Rollbacks -->
                        <div class="col-md-3 col-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center py-3">
                                    <i class="bi bi-x-circle fs-3 text-danger d-block mb-1"></i>
                                    <div class="fs-3 fw-bold text-danger" id="txRollbackTotal">0</div>
                                    <small class="text-muted">Total Rollbacks</small>
                                    <div class="mt-1"><span class="badge bg-danger" id="txRollbackSec">0/s</span></div>
                                </div>
                            </div>
                        </div>
                        <!-- Deadlocks -->
                        <div class="col-md-3 col-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center py-3">
                                    <i class="bi bi-lock fs-3 text-warning d-block mb-1"></i>
                                    <div class="fs-3 fw-bold text-warning" id="txDeadlocks">0</div>
                                    <small class="text-muted">Deadlocks</small>
                                </div>
                            </div>
                        </div>
                        <!-- Conflitos -->
                        <div class="col-md-3 col-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center py-3">
                                    <i class="bi bi-exclamation-triangle fs-3 text-info d-block mb-1"></i>
                                    <div class="fs-3 fw-bold text-info" id="txConflicts">0</div>
                                    <small class="text-muted">Conflitos</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Throughput de Tuplas -->
                    <h6 class="fw-semibold text-muted mb-2"><i class="bi bi-table me-1"></i>Throughput de Tuplas (acumulado desde início do banco)</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-2 col-4">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center py-2">
                                    <div class="fw-bold text-primary" id="tupReturned">0</div>
                                    <small class="text-muted" style="font-size:0.7rem">Retornadas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-4">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center py-2">
                                    <div class="fw-bold text-primary" id="tupFetched">0</div>
                                    <small class="text-muted" style="font-size:0.7rem">Buscadas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-4">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center py-2">
                                    <div class="fw-bold text-success" id="tupInserted">0</div>
                                    <small class="text-muted" style="font-size:0.7rem">Inseridas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-4">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center py-2">
                                    <div class="fw-bold text-warning" id="tupUpdated">0</div>
                                    <small class="text-muted" style="font-size:0.7rem">Atualizadas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-4">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center py-2">
                                    <div class="fw-bold text-danger" id="tupDeleted">0</div>
                                    <small class="text-muted" style="font-size:0.7rem">Deletadas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- I/O -->
                    <h6 class="fw-semibold text-muted mb-2"><i class="bi bi-hdd me-1"></i>I/O de Disco</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4 col-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center py-2">
                                    <div class="fw-bold" id="ioBlocosDisco">0</div>
                                    <small class="text-muted">Blocos lidos do disco</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center py-2">
                                    <div class="fw-bold text-success" id="ioBlocosCache">0</div>
                                    <small class="text-muted">Blocos lidos do cache</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-body py-2">
                                    <small class="text-muted d-block mb-1">Eficiência do Cache</small>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" id="ioCacheBar" style="width: 0%;">0%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Memória -->
                    <h6 class="fw-semibold text-muted mb-2"><i class="bi bi-memory me-1"></i>Memória do Banco</h6>
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center py-2">
                                    <div class="fw-bold" id="memAlocada">-</div>
                                    <small class="text-muted">Shared Buffers</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center py-2">
                                    <div class="fw-bold" id="memWorkMem">-</div>
                                    <small class="text-muted">Work Mem</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center py-2">
                                    <div class="fw-bold" id="memEffective">-</div>
                                    <small class="text-muted">Effective Cache Size</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center py-2">
                                    <div class="fw-bold" id="memConexoesMax">-</div>
                                    <small class="text-muted">Max Conexões</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Query Completa -->
<div class="modal fade" id="modalQuery" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable" style="max-width: 95vw; width: 95vw; margin: 1rem auto;">
        <div class="modal-content" style="max-height: 90vh;">
            <div class="modal-header py-2">
                <h5 class="modal-title"><i class="bi bi-code-square me-2"></i>Query Completa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-2 p-md-3" style="overflow-y: auto;">
                <div class="mb-2">
                    <span class="badge bg-secondary me-2" id="modalQueryPid"></span>
                    <span class="badge bg-info" id="modalQueryUsuario"></span>
                </div>
                <pre class="bg-dark text-light p-3 rounded" id="modalQueryTexto" style="white-space: pre-wrap; word-break: break-word; overflow-wrap: break-word; overflow-y: auto; font-size: 0.85rem; margin: 0;"></pre>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-outline-primary btn-sm" id="btnCopiarQuery">
                    <i class="bi bi-clipboard me-1"></i> Copiar
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<style>
.mon-status {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.mon-status-active { background: rgba(16,185,129,0.15); color: #059669; }
.mon-status-idle { background: rgba(156,163,175,0.2); color: #6b7280; }
.mon-status-idle-tx { background: rgba(245,158,11,0.15); color: #d97706; }
.mon-status-waiting { background: rgba(239,68,68,0.15); color: #dc2626; }
.mon-status-other { background: rgba(99,102,241,0.15); color: #6366f1; }
.mon-status-finished { background: rgba(156,163,175,0.15); color: #9ca3af; }
tr.mon-row-finished { opacity: 0.55; }
tr.mon-row-finished td { text-decoration: line-through; text-decoration-color: #d1d5db; }
tr.mon-row-finished td:last-child, tr.mon-row-finished td .mon-status { text-decoration: none; }

.mon-query-cell {
    max-width: 350px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-family: 'Cascadia Code', 'Fira Code', monospace;
    font-size: 0.8rem;
    cursor: pointer;
}
.mon-query-cell:hover {
    color: var(--primary);
    text-decoration: underline;
}

.mon-duracao-alta {
    color: #dc2626;
    font-weight: 700;
}
.mon-duracao-media {
    color: #d97706;
    font-weight: 600;
}

.mon-pulse {
    animation: monPulse 1.5s ease-in-out infinite;
}
@keyframes monPulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}

.page-icon-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
}

#tabelaSessoes thead.table-dark th {
    color: #fff !important;
}

#tabelaSessoes th {
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
#tabelaSessoes td {
    vertical-align: middle;
    font-size: 0.82rem;
}
</style>

<?php
$content = ob_get_clean();

$extraScripts = <<<'SCRIPTS'
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    // baseUrl já definido globalmente no base.php
    
    let intervaloTimer = null;
    let monitorandoAtivo = false;
    let conexaoSelecionada = null;
    let tipoBancoAtual = '';
    let usuarioConexao = '';
    let pgLegacy = false; // true = PG < 9.2 (não preserva query de idle)
    let sessoesAtuais = [];
    let sessoesCapturadas = new Map();
    let queryStore = new Map(); // Armazena queries completas por PID para evitar inline onclick // Acumulador: PID -> sessão (persiste mesmo após finalizar)
    let sortCol = null;
    let sortAsc = true;
    let modoTempoReal = false;
    let realtimeAbort = null;
    let monitoramentoInicioTs = null; // timestamp de quando o usuário clicou Iniciar
    let legacyBurstRunning = false; // flag para burst de captura PG < 9.2

    // Histórico para gráficos (últimos 60 pontos)
    const MAX_PONTOS = 60;
    let histLabels = [];
    let histConexoes = [];
    let histConexoesMax = [];
    let histCacheHit = [];
    let histTxCommit = [];
    let histTxRollback = [];
    let histAtivas = [];
    let histOciosas = [];
    let histCpu = [];
    let histMemUsada = [];
    let histMemMax = [];
    let lastTxCommit = null;
    let lastTxRollback = null;
    let lastTxTime = null;

    // Charts
    let chartConexoes = null;
    let chartCacheHit = null;
    let chartTransacoes = null;
    let chartSessoes = null;
    let chartCpu = null;
    let chartMemoria = null;

    // ========== ELEMENTOS ==========
    const els = {
        selectConexao:    document.getElementById('selectConexao'),
        selectIntervalo:  document.getElementById('selectIntervalo'),
        btnIniciar:       document.getElementById('btnIniciar'),
        btnParar:         document.getElementById('btnParar'),
        btnRefresh:       document.getElementById('btnRefresh'),
        avisoAcesso:      document.getElementById('avisoAcesso'),
        rowEstatisticas:  document.getElementById('rowEstatisticas'),
        cardFiltros:      document.getElementById('cardFiltros'),
        cardSessoes:      document.getElementById('cardSessoes'),
        tbodySessoes:     document.getElementById('tbodySessoes'),
        badgeTotal:       document.getElementById('badgeTotal'),
        ultimaAtualizacao:document.getElementById('ultimaAtualizacao'),
        filtroUsuario:    document.getElementById('filtroUsuario'),
        filtroEstado:     document.getElementById('filtroEstado'),
        filtroBanco:      document.getElementById('filtroBanco'),
        filtroTextoQuery: document.getElementById('filtroTextoQuery'),
        badgeUsuarioConexao: document.getElementById('badgeUsuarioConexao'),
        badgePgLegacy:    document.getElementById('badgePgLegacy'),
        rowGraficos:      document.getElementById('rowGraficos'),
        badgeAtivas:      document.getElementById('badgeAtivas'),
        badgeFinalizadas: document.getElementById('badgeFinalizadas'),
    };

    // ========== INICIALIZAÇÃO ==========
    carregarConexoes();

    els.selectConexao.addEventListener('change', onConexaoChange);
    els.btnIniciar.addEventListener('click', iniciarMonitoramento);
    els.btnParar.addEventListener('click', pararMonitoramento);
    els.btnRefresh.addEventListener('click', () => atualizarDados());
    document.getElementById('btnLimparFiltros').addEventListener('click', limparFiltros);
    document.getElementById('btnCopiarQuery').addEventListener('click', copiarQuery);

    // Filtro local na query
    els.filtroTextoQuery.addEventListener('input', aplicarFiltroLocal);

    // Ordenação ao clicar nos cabeçalhos
    document.querySelectorAll('#tabelaSessoes thead th[data-sort]').forEach(th => {
        th.addEventListener('click', function() {
            const col = this.dataset.sort;
            if (sortCol === col) {
                sortAsc = !sortAsc;
            } else {
                sortCol = col;
                sortAsc = true;
            }
            // Atualizar indicador visual
            document.querySelectorAll('#tabelaSessoes thead th[data-sort] i').forEach(i => {
                i.className = 'bi bi-arrow-down-up small';
            });
            this.querySelector('i').className = sortAsc ? 'bi bi-arrow-up small' : 'bi bi-arrow-down small';
            renderizarSessoesCapturadas();
        });
    });

    // ========== CARREGAR CONEXÕES ==========
    function carregarConexoes() {
        fetch(baseUrl + '/api/monitoramento/conexoes')
            .then(r => r.json())
            .then(data => {
                if (!data.sucesso) {
                    Swal.fire('Erro', data.erro || 'Erro ao carregar conexões', 'error');
                    return;
                }
                data.conexoes.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = `${c.nome_conexao} (${c.tipo_banco} - ${c.host}:${c.porta || ''})`;
                    opt.dataset.tipo = c.tipo_banco;
                    els.selectConexao.appendChild(opt);
                });
            })
            .catch(err => {
                console.error('Erro ao carregar conexões:', err);
            });
    }

    // ========== SELEÇÃO DE CONEXÃO ==========
    function onConexaoChange() {
        const id = els.selectConexao.value;
        pararMonitoramento();
        els.avisoAcesso.classList.add('d-none');
        els.btnIniciar.disabled = true;

        if (!id) {
            conexaoSelecionada = null;
            return;
        }

        conexaoSelecionada = parseInt(id);

        // Verificar acesso
        els.avisoAcesso.classList.remove('d-none');
        els.avisoAcesso.innerHTML = `
            <div class="alert alert-info mb-0 py-2">
                <i class="bi bi-hourglass-split me-1 mon-pulse"></i>
                Verificando permissões de monitoramento...
            </div>`;

        fetch(baseUrl + '/api/monitoramento/verificar/' + id)
            .then(r => r.json())
            .then(data => {
                if (!data.sucesso) {
                    els.avisoAcesso.innerHTML = `
                        <div class="alert alert-danger mb-0 py-2">
                            <i class="bi bi-x-circle me-1"></i>
                            ${escapeHtml(data.erro)}
                        </div>`;
                    return;
                }

                tipoBancoAtual = data.tipo_banco;
                atualizarFiltroEstados(data.tipo_banco);

                if (data.tem_acesso) {
                    els.avisoAcesso.innerHTML = `
                        <div class="alert alert-success mb-0 py-2">
                            <i class="bi bi-check-circle me-1"></i>
                            ${escapeHtml(data.mensagem)}
                        </div>`;
                    els.btnIniciar.disabled = false;
                } else {
                    els.avisoAcesso.innerHTML = `
                        <div class="alert alert-warning mb-0 py-2">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            ${escapeHtml(data.mensagem)}
                        </div>`;
                }
            })
            .catch(err => {
                els.avisoAcesso.innerHTML = `
                    <div class="alert alert-danger mb-0 py-2">
                        <i class="bi bi-x-circle me-1"></i>
                        Erro de rede ao verificar acesso.
                    </div>`;
            });
    }

    // ========== ESTADOS POR TIPO DE BANCO ==========
    function atualizarFiltroEstados(tipo) {
        const opcoes = {
            postgres:  ['active', 'idle', 'idle in transaction', 'fastpath function call', 'disabled'],
            mysql:     ['Query', 'Sleep', 'Connect', 'Execute', 'Fetch', 'Init DB', 'Prepare'],
            sqlserver: ['running', 'suspended', 'sleeping', 'runnable', 'background'],
            oracle:    ['ACTIVE', 'INACTIVE', 'KILLED', 'CACHED', 'SNIPED']
        };

        els.filtroEstado.innerHTML = '<option value="">Todos os estados</option>';
        (opcoes[tipo] || []).forEach(e => {
            const opt = document.createElement('option');
            opt.value = e;
            opt.textContent = e;
            els.filtroEstado.appendChild(opt);
        });
    }

    // ========== INICIAR / PARAR ==========
    function iniciarMonitoramento() {
        if (!conexaoSelecionada) return;
        monitorandoAtivo = true;
        monitoramentoInicioTs = new Date().toISOString();

        els.btnIniciar.classList.add('d-none');
        els.btnParar.classList.remove('d-none');
        els.btnRefresh.disabled = false;
        els.selectConexao.disabled = true;
        els.rowEstatisticas.classList.remove('d-none');
        els.cardFiltros.classList.remove('d-none');
        els.cardSessoes.classList.remove('d-none');
        els.rowGraficos.classList.remove('d-none');

        // Reset acumulador de sessões capturadas
        sessoesCapturadas = new Map();
        queryStore.clear();
        pgLegacy = false;

        // Reset histórico dos gráficos
        histLabels = []; histConexoes = []; histConexoesMax = [];
        histCacheHit = []; histTxCommit = []; histTxRollback = [];
        histAtivas = []; histOciosas = [];
        histCpu = []; histMemUsada = []; histMemMax = [];
        lastTxCommit = null; lastTxRollback = null; lastTxTime = null;
        inicializarCharts();

        atualizarDados();

        const val = els.selectIntervalo.value;
        if (val === 'realtime') {
            modoTempoReal = true;
            // Loop contínuo: dispara próxima requisição assim que a anterior terminar
            iniciarLoopTempoReal();
        } else {
            modoTempoReal = false;
            const seg = parseInt(val) * 1000;
            intervaloTimer = setInterval(() => {
                if (monitorandoAtivo) atualizarDados();
            }, seg);
        }
    }

    function pararMonitoramento() {
        monitorandoAtivo = false;
        modoTempoReal = false;
        monitoramentoInicioTs = null;
        if (realtimeAbort) {
            realtimeAbort.abort();
            realtimeAbort = null;
        }
        legacyBurstRunning = false;
        if (intervaloTimer) {
            clearInterval(intervaloTimer);
            intervaloTimer = null;
        }
        destruirCharts();
        els.btnParar.classList.add('d-none');
        els.btnIniciar.classList.remove('d-none');
        els.btnRefresh.disabled = true;
        els.selectConexao.disabled = false;
        els.badgePgLegacy.classList.add('d-none');
    }

    // ========== BURST DE CAPTURA PG < 9.2 ==========
    // Chamado SEQUENCIALMENTE entre ciclos de atualização.
    // Faz ~100 polls em ~1.5s e retorna.
    async function executarBurstLegacy() {
        if (!monitorandoAtivo || !pgLegacy || !conexaoSelecionada || legacyBurstRunning) return;
        legacyBurstRunning = true;
        const url = baseUrl + '/api/monitoramento/captura-legado/' + conexaoSelecionada;
        try {
            await fetch(url);
        } catch (e) {
            // ignorar erros — não bloquear o loop principal
        }
        legacyBurstRunning = false;
    }

    // ========== LOOP TEMPO REAL ==========
    function iniciarLoopTempoReal() {
        if (!monitorandoAtivo || !modoTempoReal) return;
        realtimeAbort = new AbortController();

        const params = new URLSearchParams();
        if (els.filtroUsuario.value) params.set('usuario', els.filtroUsuario.value);
        if (els.filtroEstado.value)  params.set('estado', els.filtroEstado.value);
        if (els.filtroBanco.value)   params.set('banco', els.filtroBanco.value);

        const urlSessoes = baseUrl + '/api/monitoramento/sessoes/' + conexaoSelecionada + '?' + params.toString();
        const urlStats   = baseUrl + '/api/monitoramento/estatisticas/' + conexaoSelecionada;
        const urlMetrics = baseUrl + '/api/monitoramento/metricas/' + conexaoSelecionada;
        const signal = realtimeAbort.signal;

        Promise.all([
            fetch(urlSessoes, { signal }).then(r => r.json()),
            fetch(urlStats, { signal }).then(r => r.json()),
            fetch(urlMetrics, { signal }).then(r => r.json())
        ]).then(([dataSessoes, dataStats, dataMetricas]) => {
            if (!monitorandoAtivo || !modoTempoReal) return;
            if (dataSessoes.sucesso) {
                sessoesAtuais = dataSessoes.sessoes;
                if (dataSessoes.usuario_conexao) {
                    usuarioConexao = dataSessoes.usuario_conexao;
                    els.badgeUsuarioConexao.textContent = 'Conectado como: ' + usuarioConexao;
                    els.badgeUsuarioConexao.classList.remove('d-none');
                }
                pgLegacy = !!(dataSessoes.versao_banco && /^[0-8]\.|^9\.[01]/.test(dataSessoes.versao_banco));
                els.badgePgLegacy.classList.toggle('d-none', !pgLegacy);
                acumularSessoes(sessoesAtuais);
                renderizarSessoesCapturadas();
                els.ultimaAtualizacao.textContent = '⚡ Tempo real — ' + new Date().toLocaleTimeString();
            }
            if (dataStats.sucesso) {
                renderizarEstatisticas(dataStats.estatisticas);
            }
            if (dataMetricas.sucesso) {
                atualizarGraficos(dataMetricas.metricas, dataStats.sucesso ? dataStats.estatisticas : null);
            }
            // Se PG legacy, fazer burst de captura antes do próximo ciclo
            if (pgLegacy) {
                executarBurstLegacy().then(() => {
                    if (monitorandoAtivo && modoTempoReal) iniciarLoopTempoReal();
                });
            } else {
                requestAnimationFrame(() => iniciarLoopTempoReal());
            }
        }).catch(err => {
            if (err.name === 'AbortError') return;
            console.error('Erro tempo real:', err);
            // Retry com pequeno delay após erro
            setTimeout(() => iniciarLoopTempoReal(), 500);
        });
    }

    // ========== ATUALIZAR DADOS ==========
    function atualizarDados() {
        if (!conexaoSelecionada) return;

        const params = new URLSearchParams();
        if (els.filtroUsuario.value) params.set('usuario', els.filtroUsuario.value);
        if (els.filtroEstado.value)  params.set('estado', els.filtroEstado.value);
        if (els.filtroBanco.value)   params.set('banco', els.filtroBanco.value);

        const urlSessoes = baseUrl + '/api/monitoramento/sessoes/' + conexaoSelecionada + '?' + params.toString();
        const urlStats   = baseUrl + '/api/monitoramento/estatisticas/' + conexaoSelecionada;
        const urlMetrics = baseUrl + '/api/monitoramento/metricas/' + conexaoSelecionada;

        Promise.all([
            fetch(urlSessoes).then(r => r.json()),
            fetch(urlStats).then(r => r.json()),
            fetch(urlMetrics).then(r => r.json())
        ]).then(([dataSessoes, dataStats, dataMetricas]) => {
            if (dataSessoes.sucesso) {
                sessoesAtuais = dataSessoes.sessoes;
                if (dataSessoes.usuario_conexao) {
                    usuarioConexao = dataSessoes.usuario_conexao;
                    els.badgeUsuarioConexao.textContent = 'Conectado como: ' + usuarioConexao;
                    els.badgeUsuarioConexao.classList.remove('d-none');
                }
                pgLegacy = !!(dataSessoes.versao_banco && /^[0-8]\.|^9\.[01]/.test(dataSessoes.versao_banco));
                els.badgePgLegacy.classList.toggle('d-none', !pgLegacy);
                // Em modo intervalo, executar burst de captura após renderizar
                if (pgLegacy) executarBurstLegacy();
                acumularSessoes(sessoesAtuais);
                renderizarSessoesCapturadas();
                els.ultimaAtualizacao.textContent = 'Atualizado: ' + dataSessoes.timestamp;
            } else {
                els.tbodySessoes.innerHTML = `<tr><td colspan="10" class="text-center text-danger py-3">
                    <i class="bi bi-exclamation-triangle me-1"></i>${escapeHtml(dataSessoes.erro)}</td></tr>`;
            }

            if (dataStats.sucesso) {
                renderizarEstatisticas(dataStats.estatisticas);
            }

            if (dataMetricas.sucesso) {
                atualizarGraficos(dataMetricas.metricas, dataStats.sucesso ? dataStats.estatisticas : null);
            }
        }).catch(err => {
            console.error('Erro ao atualizar:', err);
            els.tbodySessoes.innerHTML = `<tr><td colspan="10" class="text-center text-danger py-3">
                Erro de rede ao buscar dados.</td></tr>`;
        });
    }

    // ========== ACUMULADOR DE SESSÕES ==========
    function acumularSessoes(sessoesDoServidor) {
        // Set de PIDs ativos neste snapshot
        const pidsAtivos = new Set(sessoesDoServidor.map(s => String(s.id_sessao)));

        // Filtrar por timestamp de início (só capturar do momento que clicou Iniciar)
        const tsInicio = monitoramentoInicioTs ? new Date(monitoramentoInicioTs).getTime() : 0;

        // Adicionar/atualizar sessões ativas
        sessoesDoServidor.forEach(s => {
            const key = String(s.id_sessao);

            // Filtro temporal: só capturar sessões que iniciaram após clique em Iniciar
            if (tsInicio > 0) {
                const tsSessao = s.inicio_query ? new Date(s.inicio_query).getTime()
                               : s.inicio_sessao ? new Date(s.inicio_sessao).getTime() : 0;
                if (tsSessao > 0 && tsSessao < tsInicio) return;
            }

            // Gerar chave única: PID + texto da query (mesma query do mesmo PID = mesma entrada, evita duplicatas)
            const queryTexto = s.query_atual || '';
            const chaveUnica = queryTexto
                ? key + '|' + queryTexto.substring(0, 200)
                : key + '|t:' + (s.inicio_query || s.inicio_sessao || '');

            if (sessoesCapturadas.has(chaveUnica)) {
                // Atualizar dados (duração, estado etc) mantendo a sessão
                const existente = sessoesCapturadas.get(chaveUnica);
                const queryAnterior = existente.query_atual || '';
                Object.assign(existente, s);
                // Preservar query capturada se a nova vier vazia (PG 8.4 idle perde query)
                if (!existente.query_atual && queryAnterior) {
                    existente.query_atual = queryAnterior;
                }
                existente._finalizada = false;
                existente._hora_captura = existente._hora_captura; // manter original
            } else {
                // Nova sessão capturada
                const copia = Object.assign({}, s);
                copia._finalizada = false;
                copia._hora_captura = new Date().toLocaleTimeString('pt-BR');
                copia._chave = chaveUnica;
                sessoesCapturadas.set(chaveUnica, copia);
            }
        });

        // Marcar como finalizadas as sessões que sumiram do servidor
        sessoesCapturadas.forEach((sessao, chave) => {
            const pid = chave.split('|')[0];
            if (!pidsAtivos.has(pid) && !sessao._finalizada) {
                sessao._finalizada = true;
                sessao._hora_fim = new Date().toLocaleTimeString('pt-BR');
                sessao.estado = 'finalizada';
            }
        });
    }

    // ========== RENDERIZAR SESSÕES CAPTURADAS ==========
    function renderizarSessoesCapturadas() {
        const filtroQuery = els.filtroTextoQuery.value.toLowerCase();

        let rows = Array.from(sessoesCapturadas.values());

        if (filtroQuery) {
            rows = rows.filter(s => (s.query_atual || '').toLowerCase().includes(filtroQuery));
        }

        // Ordenação
        if (sortCol) {
            rows.sort((a, b) => {
                let va = a[sortCol] ?? '';
                let vb = b[sortCol] ?? '';
                if (sortCol === 'duracao_segundos' || sortCol === 'id_sessao') {
                    va = Number(va) || 0;
                    vb = Number(vb) || 0;
                    return sortAsc ? va - vb : vb - va;
                }
                if (sortCol === 'inicio_query') {
                    va = va ? new Date(va).getTime() || 0 : 0;
                    vb = vb ? new Date(vb).getTime() || 0 : 0;
                    return sortAsc ? va - vb : vb - va;
                }
                va = String(va).toLowerCase();
                vb = String(vb).toLowerCase();
                if (va < vb) return sortAsc ? -1 : 1;
                if (va > vb) return sortAsc ? 1 : -1;
                return 0;
            });
        } else {
            // Padrão: ativas primeiro, finalizadas por último (ordenadas por hora de captura desc)
            rows.sort((a, b) => {
                if (a._finalizada !== b._finalizada) return a._finalizada ? 1 : -1;
                return 0;
            });
        }

        const totalAtivas = rows.filter(r => !r._finalizada).length;
        const totalFinalizadas = rows.filter(r => r._finalizada).length;

        els.badgeTotal.textContent = rows.length;
        els.badgeAtivas.textContent = totalAtivas + ' ativas';
        els.badgeAtivas.classList.remove('d-none');
        els.badgeFinalizadas.textContent = totalFinalizadas + ' finalizadas';
        els.badgeFinalizadas.classList.toggle('d-none', totalFinalizadas === 0);

        if (rows.length === 0) {
            els.tbodySessoes.innerHTML = `<tr><td colspan="10" class="text-center text-muted py-4">
                <i class="bi bi-check-circle fs-4 d-block mb-1"></i>Aguardando captura de sessões...</td></tr>`;
            return;
        }

        let html = '';
        queryStore.clear();
        let rowIdx = 0;
        rows.forEach(s => {
            const queryTexto = s.query_atual || '';
            queryStore.set(rowIdx, { usuario: s.usuario || '', query: queryTexto, pid: s.id_sessao });
            const isFinalizada = s._finalizada;
            const estadoClass = isFinalizada ? 'mon-status-finished' : getEstadoClass(s.estado);
            const estadoTexto = isFinalizada ? 'finalizada' : (s.estado || '-');
            const duracaoClass = isFinalizada ? '' : getDuracaoClass(s.duracao_segundos);
            const duracaoFmt = formatarDuracao(s.duracao_segundos);
            const rowClass = isFinalizada ? 'mon-row-finished' : '';

            // Determinar preview da query e lidar com PG 8.4 idle sem query
            let queryPreview = queryTexto.substring(0, 120);
            let queryCell = '';
            if (queryPreview) {
                queryCell = `<td class="mon-query-cell" onclick="verQuery(${rowIdx});"
                    title="Clique para ver completa">${escapeHtml(queryPreview)}</td>`;
            } else if (pgLegacy && (s.estado === 'idle' || s.estado === 'idle in transaction' || isFinalizada)) {
                const ultimaAtiv = s.inicio_query ? formatarHorario(s.inicio_query) : '';
                const dica = 'PG 8.x não preserva query de sessões ociosas. Use o modo "⚡ Tempo real" para maximizar a captura. A query será capturada automaticamente quando a sessão executar uma nova consulta durante o monitoramento.';
                queryCell = `<td class="text-muted small" title="${escapeHtml(dica)}"><i class="bi bi-eye-slash me-1"></i>Não visível (PG 8.x)${ultimaAtiv ? ' — última ativ. '+ultimaAtiv : ''}</td>`;
            } else {
                queryCell = `<td class="mon-query-cell" onclick="verQuery(${rowIdx});"
                    title="Clique para ver completa"><span class="text-muted">-</span></td>`;
            }

            html += `<tr class="${rowClass}">
                <td><code>${escapeHtml(String(s.id_sessao))}</code></td>
                <td>${escapeHtml(s.usuario || '-')}</td>
                <td>${escapeHtml(s.banco || '-')}</td>
                <td><span class="mon-status ${estadoClass}">${escapeHtml(estadoTexto)}</span></td>
                <td class="small">${formatarHorario(s.inicio_query)}</td>
                <td class="${duracaoClass}">${duracaoFmt}</td>
                <td><span class="text-muted small">${escapeHtml(s.tipo_espera || '-')}</span></td>
                <td class="text-truncate" style="max-width:130px" title="${escapeHtml(s.aplicacao || '')}">${escapeHtml(s.aplicacao || '-')}</td>
                <td>${escapeHtml(s.ip_cliente || '-')}</td>
                ${queryCell}
            </tr>`;
            rowIdx++;
        });

        els.tbodySessoes.innerHTML = html;
    }

    // ========== RENDERIZAR ESTATÍSTICAS ==========
    function renderizarEstatisticas(stats) {
        if (!stats || !stats.totais) return;
        const t = stats.totais;
        document.getElementById('statTotal').textContent = t.total_sessoes || 0;
        document.getElementById('statAtivas').textContent = t.ativas || 0;
        document.getElementById('statOciosas').textContent = t.ociosas || 0;
        document.getElementById('statIdleTx').textContent = t.idle_transaction || 0;
        document.getElementById('statLocks').textContent = stats.locks_bloqueantes || 0;
        document.getElementById('statTamanho').textContent = stats.tamanho_banco || '-';
    }

    // ========== HELPERS ==========
    function getEstadoClass(estado) {
        if (!estado) return 'mon-status-other';
        const s = estado.toLowerCase();
        if (s === 'active' || s === 'running' || s === 'query') return 'mon-status-active';
        if (s === 'idle' || s === 'sleep' || s === 'sleeping' || s === 'inactive') return 'mon-status-idle';
        if (s.includes('idle in transaction')) return 'mon-status-idle-tx';
        if (s === 'suspended' || s === 'waiting') return 'mon-status-waiting';
        return 'mon-status-other';
    }

    function getDuracaoClass(seg) {
        if (!seg || seg < 10) return '';
        if (seg >= 60) return 'mon-duracao-alta';
        if (seg >= 10) return 'mon-duracao-media';
        return '';
    }

    function formatarHorario(dt) {
        if (!dt) return '-';
        try {
            const d = new Date(dt);
            if (isNaN(d.getTime())) return dt;
            return d.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        } catch(e) { return dt; }
    }

    function formatarDuracao(seg) {
        if (!seg || seg <= 0) return '-';
        const min = (seg / 60).toFixed(1);
        if (seg < 60) return min + ' min';
        if (seg < 3600) return Math.floor(seg / 60) + ' min ' + (seg % 60) + 's';
        return Math.floor(seg / 3600) + 'h ' + Math.floor((seg % 3600) / 60) + ' min';
    }

    // ========== CHARTS - GRAFANA STYLE ==========
    const chartOpts = {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 300 },
        plugins: { legend: { display: true, position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } },
        scales: {
            x: { display: false },
            y: { beginAtZero: true, ticks: { font: { size: 10 } }, grid: { color: 'rgba(0,0,0,0.05)' } }
        }
    };

    function inicializarCharts() {
        destruirCharts();

        chartConexoes = new Chart(document.getElementById('chartConexoes'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    { label: 'Atuais', data: [], borderColor: '#667eea', backgroundColor: 'rgba(102,126,234,0.1)', fill: true, tension: 0.3, pointRadius: 0, borderWidth: 2 },
                    { label: 'Max', data: [], borderColor: '#ef4444', borderDash: [5,5], pointRadius: 0, borderWidth: 1, fill: false }
                ]
            },
            options: chartOpts
        });

        chartCacheHit = new Chart(document.getElementById('chartCacheHit'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    { label: 'Cache Hit %', data: [], borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.1)', fill: true, tension: 0.3, pointRadius: 0, borderWidth: 2 }
                ]
            },
            options: { ...chartOpts, scales: { ...chartOpts.scales, y: { ...chartOpts.scales.y, min: 0, max: 100 } } }
        });

        chartTransacoes = new Chart(document.getElementById('chartTransacoes'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    { label: 'Commits/s', data: [], borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.1)', fill: true, tension: 0.3, pointRadius: 0, borderWidth: 2 },
                    { label: 'Rollbacks/s', data: [], borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.1)', fill: true, tension: 0.3, pointRadius: 0, borderWidth: 2 }
                ]
            },
            options: chartOpts
        });

        chartSessoes = new Chart(document.getElementById('chartSessoes'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    { label: 'Ativas', data: [], borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,0.15)', fill: true, tension: 0.3, pointRadius: 0, borderWidth: 2 },
                    { label: 'Ociosas', data: [], borderColor: '#9ca3af', backgroundColor: 'rgba(156,163,175,0.15)', fill: true, tension: 0.3, pointRadius: 0, borderWidth: 2 }
                ]
            },
            options: chartOpts
        });

        chartCpu = new Chart(document.getElementById('chartCpu'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    { label: 'Carga %', data: [], borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,0.15)', fill: true, tension: 0.3, pointRadius: 0, borderWidth: 2 }
                ]
            },
            options: { ...chartOpts, scales: { ...chartOpts.scales, y: { ...chartOpts.scales.y, min: 0, max: 100 } } }
        });

        chartMemoria = new Chart(document.getElementById('chartMemoria'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    { label: 'Usada (MB)', data: [], borderColor: '#8b5cf6', backgroundColor: 'rgba(139,92,246,0.15)', fill: true, tension: 0.3, pointRadius: 0, borderWidth: 2 },
                    { label: 'Max (MB)', data: [], borderColor: '#ef4444', borderDash: [5,5], pointRadius: 0, borderWidth: 1, fill: false }
                ]
            },
            options: chartOpts
        });
    }

    function destruirCharts() {
        [chartConexoes, chartCacheHit, chartTransacoes, chartSessoes, chartCpu, chartMemoria].forEach(c => { if (c) c.destroy(); });
        chartConexoes = chartCacheHit = chartTransacoes = chartSessoes = chartCpu = chartMemoria = null;
    }

    function atualizarGraficos(metricas, estatisticas) {
        const agora = new Date().toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

        // Push label
        histLabels.push(agora);
        if (histLabels.length > MAX_PONTOS) histLabels.shift();

        // Conexões
        histConexoes.push(metricas.conexoes_atuais || 0);
        histConexoesMax.push(metricas.conexoes_max || 0);
        if (histConexoes.length > MAX_PONTOS) { histConexoes.shift(); histConexoesMax.shift(); }
        document.getElementById('labelConexoesPct').textContent = (metricas.conexoes_pct || 0) + '%';

        // Cache hit
        histCacheHit.push(metricas.cache_hit_ratio || 0);
        if (histCacheHit.length > MAX_PONTOS) histCacheHit.shift();
        document.getElementById('labelCacheHit').textContent = (metricas.cache_hit_ratio || 0) + '%';

        // Transações por segundo (delta)
        const agora_ms = Date.now();
        let txCommitSec = 0, txRollbackSec = 0;
        if (lastTxCommit !== null && lastTxTime !== null) {
            const dtSec = (agora_ms - lastTxTime) / 1000;
            if (dtSec > 0) {
                txCommitSec = Math.max(0, Math.round(((metricas.tx_commit || 0) - lastTxCommit) / dtSec));
                txRollbackSec = Math.max(0, Math.round(((metricas.tx_rollback || 0) - lastTxRollback) / dtSec));
            }
        }
        lastTxCommit = metricas.tx_commit || 0;
        lastTxRollback = metricas.tx_rollback || 0;
        lastTxTime = agora_ms;
        histTxCommit.push(txCommitSec);
        histTxRollback.push(txRollbackSec);
        if (histTxCommit.length > MAX_PONTOS) { histTxCommit.shift(); histTxRollback.shift(); }
        document.getElementById('labelTxSec').textContent = txCommitSec + '/s';

        // Sessões ativas vs ociosas
        const ativas = estatisticas && estatisticas.totais ? (estatisticas.totais.ativas || 0) : 0;
        const ociosas = estatisticas && estatisticas.totais ? (estatisticas.totais.ociosas || 0) : 0;
        histAtivas.push(ativas);
        histOciosas.push(ociosas);
        if (histAtivas.length > MAX_PONTOS) { histAtivas.shift(); histOciosas.shift(); }

        // CPU / Carga do banco
        const cpuVal = metricas.cpu_pct || 0;
        histCpu.push(cpuVal);
        if (histCpu.length > MAX_PONTOS) histCpu.shift();
        document.getElementById('labelCpuPct').textContent = cpuVal + '%';
        const labelCpu = document.getElementById('labelCpuPct');
        labelCpu.className = 'badge small ' + (cpuVal > 80 ? 'bg-danger' : cpuVal > 50 ? 'bg-warning' : 'bg-success');

        // Memória
        const memUsada = metricas.memoria_usada_mb || 0;
        const memMax = metricas.memoria_max_mb || 0;
        const memPct = metricas.memoria_pct || 0;
        histMemUsada.push(memUsada);
        histMemMax.push(memMax);
        if (histMemUsada.length > MAX_PONTOS) { histMemUsada.shift(); histMemMax.shift(); }
        const labelMem = document.getElementById('labelMemPct');
        labelMem.textContent = memPct + '% (' + memUsada + ' MB)';
        labelMem.className = 'badge small ' + (memPct > 90 ? 'bg-danger' : memPct > 70 ? 'bg-warning' : '');
        if (memPct <= 70) labelMem.style.background = '#8b5cf6';
        else labelMem.style.background = '';

        // Atualizar charts
        if (chartConexoes) {
            chartConexoes.data.labels = histLabels.slice();
            chartConexoes.data.datasets[0].data = histConexoes.slice();
            chartConexoes.data.datasets[1].data = histConexoesMax.slice();
            chartConexoes.update('none');
        }
        if (chartCacheHit) {
            chartCacheHit.data.labels = histLabels.slice();
            chartCacheHit.data.datasets[0].data = histCacheHit.slice();
            chartCacheHit.update('none');
        }
        if (chartTransacoes) {
            chartTransacoes.data.labels = histLabels.slice();
            chartTransacoes.data.datasets[0].data = histTxCommit.slice();
            chartTransacoes.data.datasets[1].data = histTxRollback.slice();
            chartTransacoes.update('none');
        }
        if (chartSessoes) {
            chartSessoes.data.labels = histLabels.slice();
            chartSessoes.data.datasets[0].data = histAtivas.slice();
            chartSessoes.data.datasets[1].data = histOciosas.slice();
            chartSessoes.update('none');
        }
        if (chartCpu) {
            chartCpu.data.labels = histLabels.slice();
            chartCpu.data.datasets[0].data = histCpu.slice();
            chartCpu.update('none');
        }
        if (chartMemoria) {
            chartMemoria.data.labels = histLabels.slice();
            chartMemoria.data.datasets[0].data = histMemUsada.slice();
            chartMemoria.data.datasets[1].data = histMemMax.slice();
            chartMemoria.update('none');
        }

        // Painel de Transações & I/O
        atualizarPainelTransacoes(metricas, txCommitSec, txRollbackSec);
    }

    function formatarNumeroGrande(n) {
        if (n === null || n === undefined) return '0';
        n = Number(n);
        if (n >= 1e12) return (n / 1e12).toFixed(1) + 'T';
        if (n >= 1e9) return (n / 1e9).toFixed(1) + 'B';
        if (n >= 1e6) return (n / 1e6).toFixed(1) + 'M';
        if (n >= 1e3) return (n / 1e3).toFixed(1) + 'K';
        return n.toLocaleString('pt-BR');
    }

    function atualizarPainelTransacoes(m, commitSec, rollbackSec) {
        document.getElementById('txCommitTotal').textContent = formatarNumeroGrande(m.tx_commit);
        document.getElementById('txRollbackTotal').textContent = formatarNumeroGrande(m.tx_rollback);
        document.getElementById('txCommitSec').textContent = commitSec + '/s';
        document.getElementById('txRollbackSec').textContent = rollbackSec + '/s';
        document.getElementById('txDeadlocks').textContent = m.deadlocks ?? 0;
        document.getElementById('txConflicts').textContent = m.conflicts ?? 0;

        // Tuplas
        document.getElementById('tupReturned').textContent = formatarNumeroGrande(m.tup_returned);
        document.getElementById('tupFetched').textContent = formatarNumeroGrande(m.tup_fetched);
        document.getElementById('tupInserted').textContent = formatarNumeroGrande(m.tup_inserted);
        document.getElementById('tupUpdated').textContent = formatarNumeroGrande(m.tup_updated);
        document.getElementById('tupDeleted').textContent = formatarNumeroGrande(m.tup_deleted);

        // I/O
        document.getElementById('ioBlocosDisco').textContent = formatarNumeroGrande(m.blocos_lidos_disco);
        document.getElementById('ioBlocosCache').textContent = formatarNumeroGrande(m.blocos_lidos_cache);
        const cachePct = m.cache_hit_ratio || 0;
        const bar = document.getElementById('ioCacheBar');
        bar.style.width = cachePct + '%';
        bar.textContent = cachePct + '%';
        bar.className = 'progress-bar ' + (cachePct >= 95 ? 'bg-success' : cachePct >= 80 ? 'bg-warning' : 'bg-danger');

        // Memória
        document.getElementById('memAlocada').textContent = m.memoria_alocada || '-';
        document.getElementById('memWorkMem').textContent = m.work_mem || '-';
        document.getElementById('memEffective').textContent = m.effective_cache_size || '-';
        document.getElementById('memConexoesMax').textContent = m.conexoes_max || '-';
    }

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }

    function escapeJs(text) {
        return (text || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/\n/g, '\\n').replace(/\r/g, '');
    }

    function limparFiltros() {
        els.filtroUsuario.value = '';
        els.filtroEstado.value = '';
        els.filtroBanco.value = '';
        els.filtroTextoQuery.value = '';
        if (monitorandoAtivo) atualizarDados();
    }

    function aplicarFiltroLocal() {
        renderizarSessoesCapturadas();
    }

    // ========== MODAL QUERY ==========
    window.verQuery = function(idx) {
        const info = queryStore.get(idx) || {};
        document.getElementById('modalQueryPid').textContent = 'PID: ' + (info.pid || '');
        document.getElementById('modalQueryUsuario').textContent = info.usuario || '';
        document.getElementById('modalQueryTexto').textContent = info.query || '(vazia)';
        new bootstrap.Modal(document.getElementById('modalQuery')).show();
    };

    function copiarQuery() {
        const txt = document.getElementById('modalQueryTexto').textContent;
        navigator.clipboard.writeText(txt).then(() => {
            Swal.fire({ icon: 'success', title: 'Copiado!', timer: 1200, showConfirmButton: false });
        });
    }

    // Cleanup ao sair da página
    window.addEventListener('beforeunload', pararMonitoramento);
})();
</script>
SCRIPTS;

include __DIR__ . '/layouts/base.php';
