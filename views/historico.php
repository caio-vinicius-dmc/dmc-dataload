<?php
/**
 * DMC DataLoad - Histórico de Execuções
 * Nova UI Moderna
 */
$pageTitle = 'Histórico';
$currentPage = 'historico';
$csrfToken = App\Core\AuthMiddleware::gerarTokenCSRF();

ob_start();
?>

<!-- Header Section -->
<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-clock-history"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Histórico de Execuções</h1>
        <p class="page-subtitle-modern">Monitore e analise todas as execuções: rotinas, pipelines e workflows</p>
    </div>
    <div class="d-flex gap-2 ms-auto">
        <button class="btn-modern-outline" onclick="recarregar()">
            <i class="bi bi-arrow-clockwise me-2"></i>Atualizar
        </button>
        <button class="btn-modern-primary" onclick="exportarCSV()">
            <i class="bi bi-download me-2"></i>Exportar
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="card-modern mb-4">
    <div class="card-modern-header">
        <i class="bi bi-funnel-fill me-2"></i>
        <span>Filtros Avançados</span>
    </div>
    <div class="card-modern-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-2">
                <label class="form-label-modern">Tipo</label>
                <select class="form-select-modern" id="filtroTipo">
                    <option value="">Todos os tipos</option>
                    <option value="rotina">🔄 Rotinas</option>
                    <option value="pipeline">⚡ Pipelines</option>
                    <option value="workflow">🔀 Workflows</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-modern">Rotina</label>
                <select class="form-select-modern" id="filtroRotina">
                    <option value="">Todas as rotinas</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-modern">Status</label>
                <select class="form-select-modern" id="filtroStatus">
                    <option value="">Todos</option>
                    <option value="sucesso">✓ Sucesso</option>
                    <option value="falha">✗ Falha</option>
                    <option value="erro">⚠ Erro</option>
                    <option value="executando">⟳ Executando</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-modern">Data Início</label>
                <input type="date" class="form-control-modern" id="filtroDataInicio">
            </div>
            <div class="col-md-2">
                <label class="form-label-modern">Data Fim</label>
                <input type="date" class="form-control-modern" id="filtroDataFim">
            </div>
            <div class="col-md-2 d-flex gap-2">
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
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="statSucesso">0</div>
                <div class="stat-label-modern">Sucesso (24h)</div>
            </div>
            <div class="stat-trend success-trend">
                <i class="bi bi-arrow-up"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern danger-card">
            <div class="stat-icon-modern">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="statFalhas">0</div>
                <div class="stat-label-modern">Falhas (24h)</div>
            </div>
            <div class="stat-trend danger-trend">
                <i class="bi bi-arrow-down"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern info-card">
            <div class="stat-icon-modern">
                <i class="bi bi-play-circle-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="statExecutando">0</div>
                <div class="stat-label-modern">Em Execução</div>
            </div>
            <div class="stat-trend info-trend">
                <i class="bi bi-lightning-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern primary-card">
            <div class="stat-icon-modern">
                <i class="bi bi-stopwatch-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="statTempo">-</div>
                <div class="stat-label-modern">Tempo Médio</div>
            </div>
            <div class="stat-trend primary-trend">
                <i class="bi bi-graph-up"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabela -->
<div class="card-modern">
    <div class="card-modern-header">
        <i class="bi bi-table me-2"></i>
        <span>Lista de Execuções</span>
        <div class="ms-auto">
            <span class="badge-modern-info" id="totalRegistros">0 registros</span>
        </div>
    </div>
    <div class="card-modern-body p-0">
        <div class="table-responsive">
            <table class="table-modern" id="tblHistorico">
                <thead>
                    <tr>
                        <th><i class="bi bi-hash me-1"></i>ID</th>
                        <th><i class="bi bi-tag-fill me-1"></i>Tipo</th>
                        <th><i class="bi bi-gear-fill me-1"></i>Origem</th>
                        <th><i class="bi bi-check-circle me-1"></i>Status</th>
                        <th><i class="bi bi-calendar3 me-1"></i>Início</th>
                        <th><i class="bi bi-clock me-1"></i>Duração</th>
                        <th><i class="bi bi-layers me-1"></i>Nós</th>
                        <th><i class="bi bi-three-dots me-1"></i>Ações</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detalhes -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content modal-modern-v2">
            <div class="modal-header-modern-v2">
                <div class="modal-header-icon">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>
                <div class="modal-header-content">
                    <h5 class="modal-title-modern-v2">Detalhes da Execução</h5>
                    <p class="modal-subtitle-v2" id="modalSubtitle">Visualize informações completas</p>
                </div>
                <button type="button" class="btn-close-modern-v2" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body-modern-v2" id="modalDetalhesBody">
                <!-- Conteúdo dinâmico -->
            </div>
            <div class="modal-footer-modern-v2">
                <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Fechar
                </button>
                <button type="button" class="btn-modal-primary" id="btnReexecutar">
                    <i class="bi bi-arrow-repeat me-2"></i>Reexecutar
                </button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$extraStyles = <<<'STYLES'
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ==== PAGE HEADER ==== */
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

/* ==== MODERN CARDS ==== */
.card-modern {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    border: 1px solid rgba(0,0,0,0.04);
    transition: var(--transition);
}

.card-modern:hover {
    box-shadow: var(--shadow-md);
}

.card-modern-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 2px solid #f3f4f6;
    font-weight: 700;
    font-size: 1.05rem;
    color: #1a202c;
    display: flex;
    align-items: center;
}

.card-modern-body {
    padding: 1.5rem;
}

/* ==== MODERN FORM ELEMENTS ==== */
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

/* ==== MODERN BUTTONS ==== */
.btn-modern-primary {
    background: var(--gradient-primary);
    border: none;
    color: white;
    padding: 0.625rem 1.25rem;
    border-radius: var(--radius-md);
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

.btn-modern-outline {
    background: white;
    border: 2px solid #e2e8f0;
    color: #64748b;
    padding: 0.625rem 1.25rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-modern-outline:hover {
    border-color: #667eea;
    color: #667eea;
    background: rgba(102, 126, 234, 0.05);
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

.btn-modern-success {
    background: var(--gradient-success);
    border: none;
    color: white;
    padding: 0.65rem 1.5rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 0.95rem;
    transition: var(--transition);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.btn-modern-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    color: white;
}

.btn-modern-outline {
    background: white;
    border: 2px solid #e5e7eb;
    color: #4b5563;
    padding: 0.6rem 1.4rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 0.95rem;
    transition: var(--transition);
}

.btn-modern-outline:hover {
    border-color: #667eea;
    color: #667eea;
    background: rgba(102, 126, 234, 0.05);
}

/* ==== STAT CARDS ==== */
.stat-card-modern {
    background: white;
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow-sm);
    border: 1px solid rgba(0,0,0,0.04);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-card-modern:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.stat-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    transition: width 0.3s ease;
}

.stat-card-modern:hover::before {
    width: 6px;
}

.success-card::before { background: var(--gradient-success); }
.danger-card::before { background: var(--gradient-danger); }
.info-card::before { background: var(--gradient-info); }
.primary-card::before { background: var(--gradient-primary); }

.stat-icon-modern {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    flex-shrink: 0;
}

.success-card .stat-icon-modern {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.15) 100%);
    color: #10b981;
}

.danger-card .stat-icon-modern {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.15) 100%);
    color: #ef4444;
}

.info-card .stat-icon-modern {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.15) 100%);
    color: #3b82f6;
}

.primary-card .stat-icon-modern {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.15) 100%);
    color: #667eea;
}

.stat-content {
    flex: 1;
}

.stat-value-modern {
    font-size: 2rem;
    font-weight: 800;
    color: #1a202c;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label-modern {
    font-size: 0.85rem;
    color: #6b7280;
    font-weight: 500;
}

.stat-trend {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.success-trend {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.danger-trend {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.info-trend {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.primary-trend {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
}

/* ==== MODERN TABLE ==== */
.table-modern {
    width: 100%;
    margin-bottom: 0;
}

.table-modern thead th {
    background: linear-gradient(135deg, #fafbff 0%, #f0f4ff 100%);
    color: #4b5563;
    font-weight: 700;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1rem 1.25rem;
    border-bottom: 2px solid #e5e7eb;
}

.table-modern tbody td {
    padding: 1rem 1.25rem;
    vertical-align: middle;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.95rem;
}

.table-modern tbody tr {
    transition: var(--transition);
}

.table-modern tbody tr:hover {
    background: rgba(102, 126, 234, 0.03);
}

/* ==== BADGES ==== */
.badge-modern-info {
    padding: 0.4rem 0.9rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(37, 99, 235, 0.15) 100%);
    color: #3b82f6;
}

/* ==== MODERN MODAL ==== */
.modal-modern {
    border-radius: var(--radius-lg);
    border: none;
}

.modal-header-modern {
    padding: 1.5rem 2rem;
    border-bottom: 2px solid #f3f4f6;
    background: linear-gradient(135deg, #fafbff 0%, #f0f4ff 100%);
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
}

.modal-title-modern {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1a202c;
    display: flex;
    align-items: center;
    margin: 0;
}

.modal-title-modern i {
    color: #667eea;
}

.btn-close-modern {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    background: rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
    padding: 0;
}

.btn-close-modern:hover {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    transform: rotate(90deg);
}

.modal-body-modern {
    padding: 2rem;
}

.modal-footer-modern {
    padding: 1.5rem 2rem;
    border-top: 2px solid #f3f4f6;
    background: #fafbfc;
    border-radius: 0 0 var(--radius-lg) var(--radius-lg);
}

/* ==== MODERN MODAL V2 ==== */
.modal-modern-v2 {
    border-radius: 20px;
    border: none;
    box-shadow: 0 25px 80px rgba(0,0,0,0.15);
    overflow: hidden;
}

.modal-header-modern-v2 {
    padding: 1.75rem 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    gap: 1rem;
    border: none;
}

.modal-header-icon {
    width: 56px;
    height: 56px;
    background: rgba(255,255,255,0.2);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    backdrop-filter: blur(10px);
    flex-shrink: 0;
}

.modal-header-content {
    flex: 1;
}

.modal-title-modern-v2 {
    font-size: 1.35rem;
    font-weight: 700;
    margin: 0 0 0.25rem 0;
    letter-spacing: -0.02em;
}

.modal-subtitle-v2 {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.85;
    font-weight: 400;
}

.btn-close-modern-v2 {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    border: none;
    background: rgba(255,255,255,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: white;
    font-size: 1.1rem;
    padding: 0;
}

.btn-close-modern-v2:hover {
    background: rgba(255,255,255,0.3);
    transform: rotate(90deg);
}

.modal-body-modern-v2 {
    padding: 2rem;
    background: #f8fafc;
    max-height: calc(100vh - 280px);
    overflow-y: auto;
}

.modal-footer-modern-v2 {
    padding: 1.25rem 2rem;
    border-top: 1px solid #e5e7eb;
    background: white;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

.btn-modal-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-modal-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-modal-secondary {
    background: white;
    border: 2px solid #e5e7eb;
    color: #64748b;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    transition: all 0.3s ease;
}

.btn-modal-secondary:hover {
    border-color: #667eea;
    color: #667eea;
    background: rgba(102, 126, 234, 0.05);
}

/* Info Cards no Modal */
.info-card-v2 {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border: 1px solid #e5e7eb;
    height: 100%;
}

.info-card-v2 .card-title {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-card-v2 .card-title i {
    font-size: 1rem;
    color: #667eea;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.65rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    color: #64748b;
    font-size: 0.9rem;
    font-weight: 500;
}

.info-value {
    font-weight: 600;
    color: #1e293b;
    font-size: 0.95rem;
}

/* Status Badges V2 */
.status-badge-v2 {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    border-radius: 30px;
    font-size: 0.85rem;
    font-weight: 600;
}

.status-badge-v2.success {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.15) 100%);
    color: #059669;
}

.status-badge-v2.error {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.15) 100%);
    color: #dc2626;
}

.status-badge-v2.warning {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(217, 119, 6, 0.15) 100%);
    color: #d97706;
}

/* Erro Box V2 */
.error-box-v2 {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border-radius: 12px;
    padding: 1.25rem;
    border-left: 4px solid #ef4444;
    margin-top: 1rem;
}

.error-box-v2 .error-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #dc2626;
    margin-bottom: 0.5rem;
}

.error-box-v2 pre {
    margin: 0;
    font-size: 0.85rem;
    color: #7f1d1d;
    white-space: pre-wrap;
    word-break: break-word;
}

/* Empty State */
.empty-state-v2 {
    text-align: center;
    padding: 3rem 2rem;
    background: white;
    border-radius: 16px;
    border: 2px dashed #e5e7eb;
}

.empty-state-v2 .empty-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.15) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
    font-size: 2.5rem;
    color: #667eea;
}

.empty-state-v2 h5 {
    color: #1e293b;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.empty-state-v2 p {
    color: #64748b;
    margin: 0;
    font-size: 0.95rem;
}

/* ==== RESPONSIVE ==== */
@media (max-width: 991px) {
    .page-header-modern {
        flex-wrap: wrap;
    }
    
    .page-header-modern .ms-auto {
        margin-left: 0 !important;
        width: 100%;
        margin-top: 1rem;
    }
    
    .page-icon-modern {
        width: 56px;
        height: 56px;
        font-size: 1.75rem;
    }
    
    .page-title-modern {
        font-size: 1.5rem;
    }
    
    .stat-card-modern {
        padding: 1.25rem;
    }
    
    .stat-icon-modern {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
    }
    
    .stat-value-modern {
        font-size: 1.5rem;
    }
    
    .card-modern-body .row .col-md-3,
    .card-modern-body .row .col-md-2 {
        width: 100%;
        margin-bottom: 0.75rem;
    }
}

@media (max-width: 767px) {
    .page-header-modern {
        padding-bottom: 1rem;
    }
    
    .page-icon-modern {
        width: 48px;
        height: 48px;
        font-size: 1.5rem;
    }
    
    .page-title-modern {
        font-size: 1.25rem;
    }
    
    .page-subtitle-modern {
        font-size: 0.875rem;
    }
    
    .stat-card-modern {
        padding: 1rem;
    }
    
    .card-modern-body {
        padding: 1rem;
    }
    
    .table-modern thead th,
    .table-modern tbody td {
        padding: 0.75rem 0.5rem;
        font-size: 0.85rem;
    }
    
    .btn-modern-primary,
    .btn-modern-outline {
        padding: 0.5rem 0.875rem;
        font-size: 0.875rem;
    }
    
    .page-header-modern .d-flex {
        width: 100%;
        flex-wrap: wrap;
    }
    
    .page-header-modern .d-flex .btn-modern-primary,
    .page-header-modern .d-flex .btn-modern-outline {
        flex: 1;
    }
}
</style>
STYLES;

$extraScripts = '<script>const csrfToken = \'' . htmlspecialchars($csrfToken, ENT_QUOTES) . '\';</script>';
$extraScripts .= <<<'SCRIPTS'
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<style>
.bloco-card {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #e5e7eb;
}

.bloco-card:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    transform: translateY(-2px);
}

.bloco-card-border {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    transition: width 0.3s ease;
}

.bloco-card:hover .bloco-card-border {
    width: 6px;
}

.success-border {
    background: linear-gradient(180deg, #10b981 0%, #059669 100%);
}

.error-border {
    background: linear-gradient(180deg, #ef4444 0%, #dc2626 100%);
}

.bloco-header {
    padding: 1rem 1.25rem;
    cursor: pointer;
    user-select: none;
    transition: all 0.2s ease;
    position: relative;
    padding-left: 1.5rem;
}

.header-success {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    color: #166534;
}

.header-error {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    color: #991b1b;
}

.bloco-header:hover {
    filter: brightness(0.97);
}

.bloco-header strong {
    font-size: 1.05rem;
    font-weight: 600;
    letter-spacing: -0.01em;
}

.bloco-tipo-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.65rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.025em;
    background: rgba(255, 255, 255, 0.9);
    color: #6b7280;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid rgba(0,0,0,0.05);
}

.bloco-status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.35rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.status-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.status-error {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.bloco-time-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 500;
    background: rgba(255, 255, 255, 0.95);
    color: #4b5563;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid rgba(0,0,0,0.05);
}

.info-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 0.85rem;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    border: 1px solid rgba(0,0,0,0.05);
}

.resultado-box {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border-left: 4px solid #0ea5e9;
    padding: 1.25rem;
    border-radius: 10px;
    font-size: 0.9rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.erro-box {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border-left: 4px solid #ef4444;
    padding: 1.25rem;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.sql-container pre {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
    border: 1px solid #334155;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
}

.section-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    padding-bottom: 0.5rem;
    border-bottom: 3px solid #e2e8f0;
    letter-spacing: -0.02em;
}

.bloco-toggle {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 1.1rem;
}

.collapse {
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.collapse:not(.show) {
    display: none;
}

.collapse.show {
    display: block;
    animation: slideDown 0.35s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.filtro-bloco {
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    border-width: 2px;
    font-weight: 500;
}

.filtro-bloco.active {
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.filtro-bloco:hover:not(.active) {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.bloco-item {
    animation: fadeInUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card-body {
    padding: 1.5rem;
    background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
}

.btn-group-sm .btn {
    font-weight: 500;
    border-width: 2px;
    transition: all 0.2s ease;
}

.btn-group-sm .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.alert-info {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-left: 4px solid #3b82f6;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.bg-purple {
    background-color: #7c3aed !important;
}
</style>

<script>
let tabela;
let currentLogId = null;
let currentLogTipo = null;

// Mostrar/ocultar filtro de rotina conforme o tipo selecionado
$(document).ready(function() {
    $("#filtroTipo").change(function() {
        const tipo = $(this).val();
        if (tipo && tipo !== 'rotina') {
            $("#filtroRotina").closest(".col-md-2").hide();
        } else {
            $("#filtroRotina").closest(".col-md-2").show();
        }
    });
});

function formatDuracao(ms) {
    if (!ms) return "-";
    if (ms < 1000) return ms + "ms";
    if (ms < 60000) return (ms / 1000).toFixed(1) + "s";
    return Math.floor(ms / 60000) + "m " + Math.floor((ms % 60000) / 1000) + "s";
}

function loadTable(params = "") {
    tabela.ajax.url(baseUrl + "/api/historico" + params).load();
}

function aplicarFiltros() {
    const params = new URLSearchParams();
    const tipo = $("#filtroTipo").val();
    const rotina = $("#filtroRotina").val();
    const status = $("#filtroStatus").val();
    const dataInicio = $("#filtroDataInicio").val();
    const dataFim = $("#filtroDataFim").val();
    
    if (tipo) params.append("tipo", tipo);
    if (rotina) params.append("rotina", rotina);
    if (status) params.append("status", status);
    if (dataInicio) params.append("data_inicio", dataInicio);
    if (dataFim) params.append("data_fim", dataFim);
    
    loadTable("?" + params.toString());
}

function limparFiltros() {
    $("#filtroTipo, #filtroRotina, #filtroStatus").val("");
    $("#filtroDataInicio, #filtroDataFim").val("");
    $("#filtroRotina").closest(".col-md-2").show();
    loadTable();
}

function exportarCSV() {
    const params = new URLSearchParams();
    params.append("format", "csv");
    if ($("#filtroRotina").val()) params.append("rotina", $("#filtroRotina").val());
    if ($("#filtroStatus").val()) params.append("status", $("#filtroStatus").val());
    window.location.href = baseUrl + "/api/historico/exportar?" + params.toString();
}

function recarregar() {
    tabela.ajax.reload();
}

function verDetalhes(tipo, id) {
    currentLogId = id;
    currentLogTipo = tipo;
    const url = tipo ? `${baseUrl}/api/historico/${tipo}/${id}` : `${baseUrl}/api/historico/${id}`;
    $.get(url, function(res) {
        if (res.sucesso) {
            renderizarDetalhes(res.dados);
            // Mostrar/ocultar botão reexecutar conforme o tipo
            if (tipo === 'rotina') {
                $("#btnReexecutar").show();
            } else {
                $("#btnReexecutar").hide();
            }
            new bootstrap.Modal("#modalDetalhes").show();
        } else {
            Swal.fire("Erro", res.erro || "Erro ao carregar detalhes", "error");
        }
    });
}

function renderCsvRow(dados) {
    const link = dados.caminho_csv 
        ? '<a href="' + baseUrl + '/api/download-csv/' + dados.id + '" class="btn btn-sm btn-success"><i class="bi bi-download me-1"></i>Download</a>'
        : '<span class="text-muted">-</span>';
    return '<div class="info-row"><span class="info-label">Arquivo CSV</span><span class="info-value">' + link + '</span></div>';
}

function renderizarDetalhes(dados) {
    console.log("🎯 renderizarDetalhes chamado com:", dados);
    
    const tipoExec = dados.tipo_execucao || 'rotina';
    const tipoLabels = { rotina: 'Rotina', pipeline: 'Pipeline', workflow: 'Workflow' };
    const tipoIcons = { rotina: '🔄', pipeline: '⚡', workflow: '🔀' };
    
    // Atualizar subtítulo do modal
    const nomeOrigem = dados.nome_rotina || dados.nome_origem || '-';
    const subtitle = `${tipoLabels[tipoExec] || 'Execução'}: ${nomeOrigem}`;
    document.getElementById('modalSubtitle').textContent = subtitle;
    
    // Determinar classe do status
    const statusClass = dados.status === "sucesso" ? "success" : 
                       (dados.status === "falha" || dados.status === "erro" ? "error" : "warning");
    const statusIcon = dados.status === "sucesso" ? "check-circle-fill" : 
                      (dados.status === "falha" || dados.status === "erro" ? "x-circle-fill" : "hourglass-split");
    
    let html = `
        <!-- Cards de Informação -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="info-card-v2">
                    <div class="card-title">
                        <i class="bi bi-info-circle-fill"></i>
                        Informações Gerais
                    </div>
                    <div class="info-row">
                        <span class="info-label">ID do Log</span>
                        <span class="info-value">#${dados.id}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Tipo</span>
                        <span class="info-value">${tipoIcons[tipoExec] || ''} ${tipoLabels[tipoExec] || tipoExec}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Origem</span>
                        <span class="info-value">${nomeOrigem}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="status-badge-v2 ${statusClass}">
                            <i class="bi bi-${statusIcon}"></i>
                            ${(dados.status || 'desconhecido').toUpperCase()}
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Início</span>
                        <span class="info-value">${dados.data_inicio ? new Date(dados.data_inicio).toLocaleString("pt-BR") : "-"}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Fim</span>
                        <span class="info-value">${dados.data_fim ? new Date(dados.data_fim).toLocaleString("pt-BR") : "-"}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Duração</span>
                        <span class="info-value">${formatDuracao(dados.duracao_ms)}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="info-card-v2">
                    <div class="card-title">
                        <i class="bi bi-graph-up-arrow"></i>
                        Estatísticas
                    </div>
                    <div class="info-row">
                        <span class="info-label">${tipoExec === 'rotina' ? 'Blocos Executados' : 'Nós Executados'}</span>
                        <span class="info-value">${dados.blocos_executados || dados.nodes_total || (dados.logs?.length || 0)}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">${tipoExec === 'rotina' ? 'Blocos' : 'Nós'} com Sucesso</span>
                        <span class="info-value text-success">${dados.blocos_sucesso || dados.nodes_sucesso || (dados.logs?.filter(l => l.status === 'sucesso').length || 0)}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">${tipoExec === 'rotina' ? 'Blocos' : 'Nós'} com Falha</span>
                        <span class="info-value text-danger">${dados.blocos_falha || dados.nodes_falha || (dados.logs?.filter(l => l.status !== 'sucesso').length || 0)}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Registros Processados</span>
                        <span class="info-value">${dados.registros_processados?.toLocaleString("pt-BR") || "-"}</span>
                    </div>
                    ${tipoExec === 'rotina' ? renderCsvRow(dados) : ''}
                    ${tipoExec === 'workflow' && dados.triggered_by ? '<div class="info-row"><span class="info-label">Disparado por</span><span class="info-value">' + escapeHtml(dados.triggered_by) + '</span></div>' : ''}
                </div>
            </div>
        </div>
    `;
    
    // Mensagem de erro (se houver)
    if (dados.mensagem_erro) {
        html += `
            <div class="error-box-v2">
                <div class="error-title">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Mensagem de Erro
                </div>
                <pre>${escapeHtml(dados.mensagem_erro)}</pre>
            </div>
        `;
    }

    // SEÇÃO DE BLOCOS EXECUTADOS
    if (dados.logs && Array.isArray(dados.logs) && dados.logs.length > 0) {
        console.log(`✅ Renderizando ${dados.logs.length} blocos`);
        
        // Estatísticas para filtros
        const stats = {
            total: dados.logs.length,
            sucesso: dados.logs.filter(l => l.status === 'sucesso').length,
            erro: dados.logs.filter(l => l.status !== 'sucesso').length,
            tipos: {}
        };
        
        dados.logs.forEach(log => {
            const tipo = (log.tipo || 'SQL').toUpperCase();
            stats.tipos[tipo] = (stats.tipos[tipo] || 0) + 1;
        });
        
        html += `
        <div class="mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="section-title mb-0">
                    <i class="bi bi-layers-fill"></i>
                    <span>${tipoExec === 'rotina' ? 'Blocos Executados' : 'Nós Executados'}</span>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" onclick="expandirTodosBlocos()">
                        <i class="bi bi-arrows-expand"></i> Expandir
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="colapsarTodosBlocos()">
                        <i class="bi bi-arrows-collapse"></i> Colapsar
                    </button>
                </div>
            </div>
            
            <!-- Filtros de Blocos -->
            <div class="card mb-3 border-0 bg-white shadow-sm" style="border-radius: 12px;">
                <div class="card-body py-2">
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <small class="text-muted fw-bold me-2">Filtrar:</small>
                        <button class="btn btn-sm btn-outline-primary filtro-bloco active" data-filtro="todos" onclick="filtrarBlocos('todos')">
                            <i class="bi bi-grid-3x3"></i> Todos <span class="badge bg-primary ms-1">${stats.total}</span>
                        </button>
                        <button class="btn btn-sm btn-outline-success filtro-bloco" data-filtro="sucesso" onclick="filtrarBlocos('sucesso')">
                            <i class="bi bi-check-circle"></i> Sucesso <span class="badge bg-success ms-1">${stats.sucesso}</span>
                        </button>
                        <button class="btn btn-sm btn-outline-danger filtro-bloco" data-filtro="erro" onclick="filtrarBlocos('erro')">
                            <i class="bi bi-x-circle"></i> Erros <span class="badge bg-danger ms-1">${stats.erro}</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <div id="blocos-container">
        `;
        
        dados.logs.forEach((log, index) => {
            const isSuccess = log.status === "sucesso";
            const icon = isSuccess ? "check-circle-fill" : "x-circle-fill";
            const tipo = (log.tipo || 'SQL').toUpperCase();
            const duracao = log.duracao_ms || 0;
            
            html += `
            <div class="bloco-card mb-3 bloco-item" 
                 data-status="${log.status}" 
                 data-tipo="${tipo.toLowerCase()}"
                 data-duracao="${duracao}">
                <div class="bloco-card-border ${isSuccess ? 'success-border' : 'error-border'}"></div>
                <div class="bloco-header ${isSuccess ? 'header-success' : 'header-error'}" onclick="toggleBlocoCollapse(${index})">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-${icon}"></i>
                            <strong>${log.bloco || `Bloco ${index + 1}`}</strong>
                            <span class="bloco-tipo-badge">${tipo}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="bloco-status-badge ${isSuccess ? 'status-success' : 'status-error'}">${(log.status || 'desconhecido').toUpperCase()}</span>
                            ${log.duracao_ms ? `<span class="bloco-time-badge"><i class="bi bi-clock"></i> ${formatDuracao(log.duracao_ms)}</span>` : ''}
                            <i class="bi bi-chevron-up bloco-toggle" id="toggle-icon-${index}"></i>
                        </div>
                    </div>
                </div>
                
                <div class="collapse bloco-body" id="bloco-collapse-${index}">
                    <div class="card-body">
                        <!-- Metadados -->
                        <div class="d-flex flex-wrap gap-2 mb-3">
            `;
            
            if (log.ordem !== undefined && log.ordem !== null) {
                html += `<span class="info-badge bg-secondary text-white"><i class="bi bi-list-ol"></i> Ordem: ${log.ordem}</span>`;
            }
            if (log.registros !== undefined && log.registros !== null) {
                html += `<span class="info-badge bg-success text-white"><i class="bi bi-database"></i> ${log.registros} registros</span>`;
            }
            
            html += `</div>`;

            // SQL
            if (log.sql) {
                const blocoId = (log.bloco || index).toString().replace(/[^a-zA-Z0-9]/g, '_');
                html += `
                        <div class="mb-3 sql-container">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold mb-0"><i class="bi bi-file-code me-1"></i> SQL Executado:</label>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-secondary" onclick="copiarSQL('${blocoId}')">
                                        <i class="bi bi-clipboard"></i> Copiar
                                    </button>
                                    <button class="btn btn-outline-info" onclick="formatarSQL('${blocoId}')">
                                        <i class="bi bi-code-slash"></i> Formatar
                                    </button>
                                </div>
                            </div>
                            <pre class="bg-dark text-light p-3 rounded" style="max-height: 300px; overflow-y: auto; font-size: 0.85rem; line-height: 1.5;" id="sql-${blocoId}">${escapeHtml(log.sql)}</pre>
                        </div>`;
            }

            // Resultado
            if (log.resultado) {
                html += `
                        <div class="resultado-box mb-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-check2-circle text-success fs-5"></i>
                                <label class="form-label fw-bold mb-0">Resultado:</label>
                            </div>
                            <div>${escapeHtml(String(log.resultado))}</div>
                        </div>`;
            }
            
            // Erro
            if (log.erro) {
                html += `
                        <div class="erro-box mb-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                                <label class="form-label fw-bold text-danger mb-0">Erro:</label>
                            </div>
                            <div class="text-danger font-monospace small">${escapeHtml(String(log.erro))}</div>
                        </div>`;
            }

            // Arquivo CSV
            if (log.arquivo_csv) {
                html += `
                        <div class="alert alert-info mb-0">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-text fs-5"></i>
                                <div>
                                    <strong>Arquivo Gerado:</strong><br>
                                    <code>${escapeHtml(log.arquivo_csv)}</code>
                                </div>
                            </div>
                        </div>`;
            }
            
            html += `
                    </div>
                </div>
            </div>
            `;
        });
        
        html += `
            </div>
        </div>
        `;
    } else {
        // Empty state quando não há blocos
        html += `
            <div class="empty-state-v2 mt-4">
                <div class="empty-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <h5>Nenhum Bloco Registrado</h5>
                <p>Esta execução não possui detalhamento de blocos ou foi uma execução automática do scheduler.</p>
            </div>
        `;
    }
    
    $("#modalDetalhesBody").html(html);
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function copiarSQL(blocoId) {
    const sqlElement = document.getElementById('sql-' + blocoId);
    if (!sqlElement) return;
    
    const sql = sqlElement.textContent;
    navigator.clipboard.writeText(sql).then(() => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'SQL copiado!',
            showConfirmButton: false,
            timer: 2000
        });
    }).catch(err => {
        console.error('Erro ao copiar:', err);
    });
}

function formatarSQL(blocoId) {
    const sqlElement = document.getElementById('sql-' + blocoId);
    if (!sqlElement) return;
    
    let sql = sqlElement.textContent.trim();
    
    // Formatação básica de SQL
    sql = sql.replace(/\bSELECT\b/gi, '\nSELECT')
             .replace(/\bFROM\b/gi, '\nFROM')
             .replace(/\bWHERE\b/gi, '\nWHERE')
             .replace(/\bAND\b/gi, '\n  AND')
             .replace(/\bOR\b/gi, '\n  OR')
             .replace(/\bJOIN\b/gi, '\nJOIN')
             .replace(/\bLEFT JOIN\b/gi, '\nLEFT JOIN')
             .replace(/\bRIGHT JOIN\b/gi, '\nRIGHT JOIN')
             .replace(/\bINNER JOIN\b/gi, '\nINNER JOIN')
             .replace(/\bON\b/gi, '\n  ON')
             .replace(/\bGROUP BY\b/gi, '\nGROUP BY')
             .replace(/\bORDER BY\b/gi, '\nORDER BY')
             .replace(/\bLIMIT\b/gi, '\nLIMIT')
             .replace(/\bINSERT INTO\b/gi, '\nINSERT INTO')
             .replace(/\bVALUES\b/gi, '\nVALUES')
             .replace(/\bUPDATE\b/gi, '\nUPDATE')
             .replace(/\bSET\b/gi, '\nSET')
             .replace(/\bDELETE FROM\b/gi, '\nDELETE FROM')
             .trim();
    
    sqlElement.textContent = sql;
    
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'SQL formatado!',
        showConfirmButton: false,
        timer: 1500
    });
}

function toggleBlocoCollapse(index) {
    const collapseEl = document.getElementById('bloco-collapse-' + index);
    const iconEl = document.getElementById('toggle-icon-' + index);
    
    if (collapseEl.classList.contains('show')) {
        collapseEl.classList.remove('show');
        iconEl.classList.remove('bi-chevron-down');
        iconEl.classList.add('bi-chevron-up');
    } else {
        collapseEl.classList.add('show');
        iconEl.classList.remove('bi-chevron-up');
        iconEl.classList.add('bi-chevron-down');
    }
}

function expandirTodosBlocos() {
    document.querySelectorAll('.bloco-body').forEach(el => {
        el.classList.add('show');
    });
    document.querySelectorAll('.bloco-toggle').forEach(icon => {
        icon.classList.remove('bi-chevron-up');
        icon.classList.add('bi-chevron-down');
    });
}

function colapsarTodosBlocos() {
    document.querySelectorAll('.bloco-body').forEach(el => {
        el.classList.remove('show');
    });
    document.querySelectorAll('.bloco-toggle').forEach(icon => {
        icon.classList.remove('bi-chevron-down');
        icon.classList.add('bi-chevron-up');
    });
}

function filtrarBlocos(filtro) {
    const blocos = document.querySelectorAll('.bloco-item');
    let visibleCount = 0;
    
    // Atualizar botões ativos
    document.querySelectorAll('.filtro-bloco').forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-filtro') === filtro) {
            btn.classList.add('active');
        }
    });
    
    blocos.forEach(bloco => {
        let mostrar = false;
        const status = bloco.getAttribute('data-status');
        const tipo = bloco.getAttribute('data-tipo');
        
        if (filtro === 'todos') {
            mostrar = true;
        } else if (filtro === 'sucesso') {
            mostrar = status === 'sucesso';
        } else if (filtro === 'erro') {
            mostrar = status !== 'sucesso';
        } else if (filtro.startsWith('tipo-')) {
            const tipoFiltro = filtro.replace('tipo-', '');
            mostrar = tipo === tipoFiltro;
        }
        
        if (mostrar) {
            bloco.style.display = 'block';
            visibleCount++;
        } else {
            bloco.style.display = 'none';
        }
    });
    
    // Atualizar contador
    const containerTitle = document.querySelector('.section-title span');
    if (containerTitle) {
        containerTitle.textContent = `Blocos Executados (${visibleCount} de ${blocos.length})`;
    }
}

function ordenarBlocosPorTempo() {
    const container = document.getElementById('blocos-container');
    if (!container) return;
    
    const blocos = Array.from(container.querySelectorAll('.bloco-item'));
    blocos.sort((a, b) => {
        const duracaoA = parseInt(a.getAttribute('data-duracao')) || 0;
        const duracaoB = parseInt(b.getAttribute('data-duracao')) || 0;
        return duracaoB - duracaoA; // Ordem decrescente
    });
    
    blocos.forEach(bloco => container.appendChild(bloco));
    
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Blocos ordenados por tempo!',
        showConfirmButton: false,
        timer: 1500
    });
}

function limparFiltrosBlocos() {
    filtrarBlocos('todos');
}

// Carregar rotinas no filtro
function carregarRotinas() {
    $.get(baseUrl + "/rotinas/list", function(res) {
        if (res.data) {
            res.data.forEach(r => {
                $("#filtroRotina").append(`<option value="${r.id}">${r.nome}</option>`);
            });
        }
    });
}

// Reexecutar rotina
$("#btnReexecutar").click(function() {
    if (!currentLogId) return;
    
    Swal.fire({
        title: "Reexecutar rotina?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sim, executar",
        cancelButtonText: "Cancelar"
    }).then(result => {
        if (result.isConfirmed) {
            $.post(baseUrl + "/api/executar-rotina", { log_id: currentLogId, _csrf_token: csrfToken }, function(res) {
                if (res.sucesso) {
                    Swal.fire("Sucesso", "Rotina iniciada!", "success");
                    bootstrap.Modal.getInstance("#modalDetalhes").hide();
                    tabela.ajax.reload();
                } else {
                    Swal.fire("Erro", res.erro, "error");
                }
            });
        }
    });
});

$(document).ready(function() {
    tabela = $("#tblHistorico").DataTable({
        processing: true,
        ajax: {
            url: baseUrl + "/api/historico",
            dataSrc: function(json) {
                if (json.estatisticas) {
                    $("#statSucesso").text(json.estatisticas.sucesso_24h || 0);
                    $("#statFalhas").text(json.estatisticas.falhas_24h || 0);
                    $("#statExecutando").text(json.estatisticas.executando || 0);
                    $("#statTempo").text(formatDuracao(json.estatisticas.tempo_medio_ms || 0));
                }
                return json.dados || [];
            }
        },
        columns: [
            { data: "id" },
            { 
                data: "tipo_execucao",
                render: function(data) {
                    const badges = {
                        rotina: '<span class="badge bg-primary"><i class="bi bi-arrow-repeat me-1"></i>Rotina</span>',
                        pipeline: '<span class="badge bg-info text-dark"><i class="bi bi-lightning-fill me-1"></i>Pipeline</span>',
                        workflow: '<span class="badge bg-purple text-white"><i class="bi bi-shuffle me-1"></i>Workflow</span>'
                    };
                    return badges[data] || `<span class="badge bg-secondary">${data}</span>`;
                }
            },
            { data: "nome_origem", defaultContent: "-" },
            { 
                data: "status",
                render: function(data) {
                    const classes = {
                        sucesso: "badge-success",
                        falha: "badge-danger",
                        erro: "badge-danger",
                        executando: "badge-warning",
                        pendente: "badge-info",
                        cancelado: "badge-secondary",
                        pausado: "badge-warning"
                    };
                    return `<span class="badge-status ${classes[data] || "badge-info"}">${data}</span>`;
                }
            },
            { data: "data_inicio", render: d => d ? new Date(d).toLocaleString("pt-BR") : "-" },
            { data: "duracao_ms", render: formatDuracao },
            { 
                data: null,
                render: function(data) {
                    const total = data.nodes_total || data.registros_processados || 0;
                    const suc = data.nodes_sucesso || 0;
                    const fail = data.nodes_falha || 0;
                    if (data.tipo_execucao === 'rotina') {
                        return data.registros_processados?.toLocaleString("pt-BR") || "-";
                    }
                    if (!total) return "-";
                    return `<span title="${suc} sucesso / ${fail} falha">${suc}/${total}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data) {
                    return `<button class="btn btn-sm btn-outline-info" onclick="verDetalhes('${data.tipo_execucao}', ${data.id})">
                        <i class="bi bi-eye"></i>
                    </button>`;
                }
            }
        ],
        order: [[0, "desc"]],
        language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json" }
    });
    
    carregarRotinas();
    
    // Auto-refresh se houver execuções
    setInterval(function() {
        const exec = parseInt($("#statExecutando").text()) || 0;
        if (exec > 0) tabela.ajax.reload(null, false);
    }, 15000);
});
</script>
SCRIPTS;

include __DIR__ . '/layouts/base.php';
?>
