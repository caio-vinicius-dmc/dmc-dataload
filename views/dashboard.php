<?php
/**
 * DMC DataLoad - Dashboard
 * UI Moderna - Refatorado
 */
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

ob_start();
?>

<!-- Header Section -->
<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-grid-1x2-fill"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Dashboard</h1>
        <p class="page-subtitle-modern">Visão geral das execuções e métricas do sistema</p>
    </div>
    <div class="ms-auto">
        <button class="btn-modern-primary" onclick="carregarMetricas()">
            <i class="bi bi-arrow-clockwise me-2"></i>Atualizar
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern primary-card">
            <div class="stat-icon-modern">
                <i class="bi bi-play-circle-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="totalRotinas">0</div>
                <div class="stat-label-modern">Total de Rotinas</div>
            </div>
            <div class="stat-trend primary-trend">
                <i class="bi bi-graph-up"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern success-card">
            <div class="stat-icon-modern">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="execHoje">0</div>
                <div class="stat-label-modern">Execuções Hoje</div>
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
                <div class="stat-value-modern" id="falhasHoje">0</div>
                <div class="stat-label-modern">Falhas Hoje</div>
            </div>
            <div class="stat-trend danger-trend">
                <i class="bi bi-arrow-down"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card-modern info-card">
            <div class="stat-icon-modern">
                <i class="bi bi-lightning-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value-modern" id="emExec">0</div>
                <div class="stat-label-modern">Em Execução</div>
            </div>
            <div class="stat-trend info-trend">
                <i class="bi bi-activity"></i>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row g-4 mb-4">
    <div class="col-12 col-lg-8">
        <div class="card-modern h-100">
            <div class="card-modern-header">
                <i class="bi bi-bar-chart-line-fill me-2"></i>
                <span>Execuções - Últimos 7 Dias</span>
                <div class="ms-auto">
                    <span class="badge-modern-info" id="totalExecSemana">0 execuções</span>
                </div>
            </div>
            <div class="card-modern-body" style="min-height: 350px;">
                <canvas id="chartExec"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card-modern h-100">
            <div class="card-modern-header">
                <i class="bi bi-pie-chart-fill me-2"></i>
                <span>Taxa de Sucesso</span>
            </div>
            <div class="card-modern-body d-flex align-items-center justify-content-center" style="min-height: 350px;">
                <div style="position: relative; width: 100%; max-width: 280px;">
                    <canvas id="chartSucesso"></canvas>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                        <div style="font-size: 2.5rem; font-weight: 800; color: #1a202c;" id="taxaSucessoValor">0%</div>
                        <div style="font-size: 0.85rem; color: #6b7280; font-weight: 500;">Taxa de Sucesso</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabelas -->
<div class="row g-4">
    <div class="col-12 col-xl-6">
        <div class="card-modern">
            <div class="card-modern-header">
                <i class="bi bi-clock-history me-2"></i>
                <span>Últimas Execuções</span>
                <div class="ms-auto">
                    <a href="<?= defined('BASE_URL') ? BASE_URL : '/DMC-DATALOAD/public' ?>/historico" class="btn btn-sm btn-modern-outline">
                        Ver todas <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-modern-body p-0">
                <div class="table-responsive">
                    <table class="table-modern" id="tblUltimas">
                        <thead>
                            <tr>
                                <th><i class="bi bi-gear-fill me-1"></i>Rotina</th>
                                <th><i class="bi bi-calendar3 me-1"></i>Data/Hora</th>
                                <th><i class="bi bi-check-circle me-1"></i>Status</th>
                                <th><i class="bi bi-clock me-1"></i>Duração</th>
                            </tr>
                        </thead>
                        <tbody id="ultimasExec">
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="spinner-border spinner-border-sm text-primary"></div>
                                    <span class="ms-2">Carregando...</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-6">
        <div class="card-modern">
            <div class="card-modern-header">
                <i class="bi bi-calendar-check me-2"></i>
                <span>Próximas Execuções</span>
                <div class="ms-auto">
                    <a href="<?= defined('BASE_URL') ? BASE_URL : '/DMC-DATALOAD/public' ?>/scheduler" class="btn btn-sm btn-modern-outline">
                        Gerenciar <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
            <div class="card-modern-body p-0">
                <div class="table-responsive">
                    <table class="table-modern" id="tblProximas">
                        <thead>
                            <tr>
                                <th><i class="bi bi-gear-fill me-1"></i>Rotina</th>
                                <th><i class="bi bi-calendar-event me-1"></i>Agendamento</th>
                                <th><i class="bi bi-clock-fill me-1"></i>Próxima</th>
                            </tr>
                        </thead>
                        <tbody id="proximasExec">
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                    <small>Nenhuma rotina agendada</small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$extraStyles = <<<'STYLES'
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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

/* ==== MODERN BUTTONS ==== */
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

.btn-modern-outline {
    background: white;
    border: 2px solid #e5e7eb;
    color: #4b5563;
    padding: 0.4rem 1rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 0.85rem;
    transition: var(--transition);
}

.btn-modern-outline:hover {
    border-color: #667eea;
    color: #667eea;
    background: rgba(102, 126, 234, 0.05);
    text-decoration: none;
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

.badge-status {
    padding: 0.35rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.badge-success { background: rgba(16, 185, 129, 0.15); color: #059669; }
.badge-danger { background: rgba(239, 68, 68, 0.15); color: #dc2626; }
.badge-warning { background: rgba(245, 158, 11, 0.15); color: #d97706; }
.badge-info { background: rgba(59, 130, 246, 0.15); color: #2563eb; }

/* ==== RESPONSIVE ==== */
@media (max-width: 991px) {
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
}

@media (max-width: 767px) {
    .page-header-modern {
        padding: 1rem 0;
    }
    
    .page-title-modern {
        font-size: 1.25rem;
    }
    
    .page-subtitle-modern {
        font-size: 0.9rem;
    }
    
    .stat-card-modern {
        padding: 1rem;
    }
    
    .card-modern-body {
        padding: 1rem;
    }
    
    .table-modern thead th,
    .table-modern tbody td {
        padding: 0.75rem;
        font-size: 0.85rem;
    }
}
</style>
STYLES;

$extraScripts = <<<'SCRIPTS'
<script>
let chartExec = null;
let chartSucesso = null;

// Inicializar gráficos
function initCharts() {
    // Gráfico de Execuções (Barra)
    const ctxExec = document.getElementById('chartExec');
    if (ctxExec) {
        chartExec = new Chart(ctxExec, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Sucesso',
                        data: [],
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderRadius: 8,
                        borderSkipped: false,
                    },
                    {
                        label: 'Falha',
                        data: [],
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderRadius: 8,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                size: 13,
                                weight: 600
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        borderColor: 'rgba(255, 255, 255, 0.1)',
                        borderWidth: 1,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' execuções';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: 500
                            }
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // Gráfico de Taxa de Sucesso (Doughnut)
    const ctxSucesso = document.getElementById('chartSucesso');
    if (ctxSucesso) {
        chartSucesso = new Chart(ctxSucesso, {
            type: 'doughnut',
            data: {
                labels: ['Sucesso', 'Falha'],
                datasets: [{
                    data: [0, 0],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                size: 13,
                                weight: 600
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.parsed;
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return context.label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
}

// Carregar métricas do dashboard
function carregarMetricas() {
    $.getJSON(baseUrl + '/api/dashboard/metricas', function(res) {
        if (!res.sucesso) {
            console.error('Erro ao carregar métricas:', res.erro);
            return;
        }
        
        // Atualizar cards de estatísticas
        $('#totalRotinas').text(res.total_rotinas || 0);
        $('#execHoje').text(res.execucoes_hoje || 0);
        $('#falhasHoje').text(res.falhas_hoje || 0);
        $('#emExec').text(res.em_execucao || 0);
        
        // Atualizar gráfico de execuções dos últimos 7 dias
        if (res.grafico_7dias && res.grafico_7dias.length > 0 && chartExec) {
            const labels = res.grafico_7dias.map(d => {
                const dt = new Date(d.data + 'T00:00:00');
                return dt.toLocaleDateString('pt-BR', { weekday: 'short', day: 'numeric', month: 'short' });
            });
            const sucesso = res.grafico_7dias.map(d => parseInt(d.sucesso) || 0);
            const falha = res.grafico_7dias.map(d => parseInt(d.falha) || 0);
            
            // FIX: Substituir completamente os dados (não usar push)
            chartExec.data.labels = labels;
            chartExec.data.datasets[0].data = sucesso;
            chartExec.data.datasets[1].data = falha;
            chartExec.update('none'); // 'none' desabilita animação para melhor performance
            
            // Calcular total da semana
            const totalSemana = sucesso.reduce((a, b) => a + b, 0) + falha.reduce((a, b) => a + b, 0);
            $('#totalExecSemana').text(totalSemana + ' execuções');
        }
        
        // Atualizar gráfico de taxa de sucesso
        if (res.grafico_7dias && res.grafico_7dias.length > 0 && chartSucesso) {
            const sucesso = res.grafico_7dias.map(d => parseInt(d.sucesso) || 0);
            const falha = res.grafico_7dias.map(d => parseInt(d.falha) || 0);
            const totalSucesso = sucesso.reduce((a, b) => a + b, 0);
            const totalFalha = falha.reduce((a, b) => a + b, 0);
            const total = totalSucesso + totalFalha;
            const taxaSucesso = total > 0 ? ((totalSucesso / total) * 100).toFixed(1) : 0;
            
            // FIX: Substituir completamente os dados
            chartSucesso.data.datasets[0].data = [totalSucesso, totalFalha];
            chartSucesso.update('none');
            
            $('#taxaSucessoValor').text(taxaSucesso + '%');
        }
        
        // Atualizar últimas execuções
        if (res.ultimas_execucoes) {
            const tbody = $('#ultimasExec');
            tbody.empty();
            
            if (res.ultimas_execucoes.length === 0) {
                tbody.html(`<tr><td colspan="4" class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <small>Nenhuma execução recente</small>
                </td></tr>`);
            } else {
                res.ultimas_execucoes.slice(0, 5).forEach(e => {
                    const badgeClass = e.status === 'sucesso' ? 'badge-success' : 
                                      (e.status === 'falha' || e.status === 'erro' ? 'badge-danger' : 'badge-warning');
                    const data = e.data_inicio ? new Date(e.data_inicio).toLocaleString('pt-BR', { 
                        day: '2-digit', 
                        month: '2-digit', 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    }) : '-';
                    const duracao = e.duracao_ms ? 
                        (e.duracao_ms < 1000 ? e.duracao_ms + 'ms' : 
                        e.duracao_ms < 60000 ? (e.duracao_ms / 1000).toFixed(1) + 's' : 
                        Math.floor(e.duracao_ms / 60000) + 'm' + Math.floor((e.duracao_ms % 60000) / 1000) + 's') : '-';
                    
                    tbody.append(`<tr>
                        <td><strong>${e.rotina || 'Desconhecida'}</strong></td>
                        <td><small class="text-muted">${data}</small></td>
                        <td><span class="badge-status ${badgeClass}">${e.status}</span></td>
                        <td><small>${duracao}</small></td>
                    </tr>`);
                });
            }
        }
        
        // Atualizar próximas execuções
        if (res.proximas_execucoes) {
            const tbody = $('#proximasExec');
            tbody.empty();
            
            if (res.proximas_execucoes.length === 0) {
                tbody.html(`<tr><td colspan="3" class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                    <small>Nenhuma rotina agendada</small>
                </td></tr>`);
            } else {
                res.proximas_execucoes.slice(0, 5).forEach(e => {
                    const proxima = e.proxima_execucao ? new Date(e.proxima_execucao).toLocaleString('pt-BR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : '-';
                    
                    tbody.append(`<tr>
                        <td><strong>${e.nome || 'Rotina'}</strong></td>
                        <td><code style="font-size: 0.8rem; background: rgba(0,0,0,0.05); padding: 0.25rem 0.5rem; border-radius: 4px;">${e.agendamento_cron || '-'}</code></td>
                        <td><small class="text-muted">${proxima}</small></td>
                    </tr>`);
                });
            }
        }
    }).fail(function(error) {
        console.error('Erro ao carregar métricas:', error);
    });
}

// Inicializar quando o documento estiver pronto
$(document).ready(function() {
    initCharts();
    carregarMetricas();
    
    // Auto-refresh a cada 30 segundos
    setInterval(carregarMetricas, 30000);
});
</script>
SCRIPTS;

include __DIR__ . '/layouts/base.php';
?>
