<?php
/**
 * DMC DataLoad - Histórico de Execuções
 */
$pageTitle = 'Histórico de Execuções';
$usuario = $_SESSION['usuario'] ?? [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - DMC DataLoad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0d6efd;
            --sidebar-width: 260px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }
        
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1e3a5f 0%, #0d1f33 100%);
            padding-top: 1rem;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar .brand {
            padding: 1rem 1.5rem;
            color: white;
            font-size: 1.4rem;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 1rem;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 0.75rem 1.5rem;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left-color: var(--primary-color);
        }
        
        .sidebar .nav-link i {
            width: 24px;
            margin-right: 10px;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        .top-navbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .content-area {
            padding: 2rem;
        }
        
        .status-badge {
            padding: 0.35rem 0.65rem;
            border-radius: 0.25rem;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-sucesso { background: #d1e7dd; color: #0f5132; }
        .status-falha { background: #f8d7da; color: #842029; }
        .status-executando { background: #cff4fc; color: #055160; }
        .status-pendente { background: #fff3cd; color: #664d03; }
        
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-radius: 12px;
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid rgba(0,0,0,0.06);
            padding: 1rem 1.5rem;
        }
        
        .btn-outline-info {
            border-color: #0dcaf0;
            color: #0dcaf0;
        }
        
        .btn-outline-info:hover {
            background: #0dcaf0;
            color: white;
        }
        
        .timeline-item {
            position: relative;
            padding-left: 30px;
            margin-bottom: 1rem;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        
        .timeline-item::after {
            content: '';
            position: absolute;
            left: 4px;
            top: 6px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--primary-color);
            border: 2px solid white;
            box-shadow: 0 0 0 2px var(--primary-color);
        }
        
        .timeline-item.error::after {
            background: #dc3545;
            box-shadow: 0 0 0 2px #dc3545;
        }
        
        .filter-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        
        /* Estilos para cards de detalhamento de blocos */
        .bloco-card {
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .bloco-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
        }
        
        .bloco-card .card-header {
            padding: 1rem 1.5rem;
            border-bottom: none;
        }
        
        .bloco-card .card-header h6 {
            font-size: 1rem;
            font-weight: 600;
        }
        
        .bloco-card .card-body {
            padding: 1.5rem;
        }
        
        .bloco-card pre {
            margin: 0;
            border-radius: 6px;
            border: none;
        }
        
        .bloco-card .badge {
            padding: 0.4rem 0.7rem;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .sql-container {
            position: relative;
        }
        
        .sql-container .btn-copiar {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            opacity: 0.8;
            transition: opacity 0.2s;
        }
        
        .sql-container:hover .btn-copiar {
            opacity: 1;
        }
        
        .info-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.35rem 0.6rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .resultado-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 4px solid #0d6efd;
            padding: 1rem;
            border-radius: 6px;
            font-size: 0.9rem;
        }
        
        .erro-box {
            background: linear-gradient(135deg, #fff5f5 0%, #ffe0e0 100%);
            border-left: 4px solid #dc3545;
        }
        
        .modal-xl {
            max-width: 1200px;
        }
        
        .modal-body {
            max-height: 75vh;
            overflow-y: auto;
        }
        
        .section-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.2rem;
            font-weight: 600;
            color: #1e3a5f;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <?php $base = defined('BASE_URL') ? BASE_URL : ''; ?>
        <div class="brand">
            <i class="bi bi-database-fill"></i> DMC DataLoad
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="<?= $base ?>/dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $base ?>/conexoes">
                    <i class="bi bi-plug"></i> Conexões
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $base ?>/rotinas">
                    <i class="bi bi-gear"></i> Rotinas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="<?= $base ?>/historico">
                    <i class="bi bi-clock-history"></i> Histórico
                </a>
            </li>
            <li class="nav-item mt-auto">
                <a class="nav-link text-danger" href="<?= $base ?>/logout">
                    <i class="bi bi-box-arrow-right"></i> Sair
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div>
                <h4 class="mb-0"><i class="bi bi-clock-history me-2"></i>Histórico de Execuções</h4>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted">
                    <i class="bi bi-person-circle me-1"></i>
                    <?= htmlspecialchars($usuario['nome'] ?? 'Usuário') ?>
                </span>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Filtros -->
            <div class="filter-card">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Rotina</label>
                        <select class="form-select" id="filtroRotina">
                            <option value="">Todas</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="filtroStatus">
                            <option value="">Todos</option>
                            <option value="sucesso">Sucesso</option>
                            <option value="falha">Falha</option>
                            <option value="executando">Executando</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Data Início</label>
                        <input type="date" class="form-control" id="filtroDataInicio">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Data Fim</label>
                        <input type="date" class="form-control" id="filtroDataFim">
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button class="btn btn-primary" id="btnFiltrar">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>
                        <button class="btn btn-outline-secondary" id="btnLimparFiltros">
                            <i class="bi bi-x-circle"></i> Limpar
                        </button>
                        <button class="btn btn-outline-success" id="btnExportar">
                            <i class="bi bi-download"></i> Exportar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Estatísticas Rápidas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small opacity-75">Sucesso (24h)</div>
                                    <div class="h3 mb-0" id="statSucesso24h">-</div>
                                </div>
                                <i class="bi bi-check-circle fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small opacity-75">Falhas (24h)</div>
                                    <div class="h3 mb-0" id="statFalhas24h">-</div>
                                </div>
                                <i class="bi bi-x-circle fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small opacity-75">Em Execução</div>
                                    <div class="h3 mb-0" id="statExecutando">-</div>
                                </div>
                                <i class="bi bi-play-circle fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="small opacity-75">Tempo Médio</div>
                                    <div class="h3 mb-0" id="statTempoMedio">-</div>
                                </div>
                                <i class="bi bi-stopwatch fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Histórico -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Execuções</h5>
                    <button class="btn btn-sm btn-outline-primary" id="btnRefresh">
                        <i class="bi bi-arrow-clockwise"></i> Atualizar
                    </button>
                </div>
                <div class="card-body">
                    <table id="tabelaHistorico" class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Rotina</th>
                                <th>Status</th>
                                <th>Início</th>
                                <th>Fim</th>
                                <th>Duração</th>
                                <th>Registros</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detalhes -->
    <div class="modal fade" id="modalDetalhes" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-info-circle me-2"></i>Detalhes da Execução
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalDetalhesBody">
                    <!-- Conteúdo dinâmico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" id="btnReexecutar">
                        <i class="bi bi-arrow-repeat"></i> Reexecutar Rotina
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        const baseUrl = '<?= defined("BASE_URL") ? BASE_URL : "" ?>';
        let tabela;
        let currentLogId = null;
        
        // Inicializar DataTable
        tabela = $('#tabelaHistorico').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: baseUrl + '/api/historico',
                dataSrc: function(json) {
                    if (json.estatisticas) {
                        $('#statSucesso24h').text(json.estatisticas.sucesso_24h || 0);
                        $('#statFalhas24h').text(json.estatisticas.falhas_24h || 0);
                        $('#statExecutando').text(json.estatisticas.executando || 0);
                        $('#statTempoMedio').text(formatDuracao(json.estatisticas.tempo_medio_ms || 0));
                    }
                    return json.dados || [];
                }
            },
            columns: [
                { data: 'id', width: '60px' },
                { data: 'nome_rotina' },
                { 
                    data: 'status',
                    render: function(data) {
                        const classes = {
                            'sucesso': 'status-sucesso',
                            'falha': 'status-falha',
                            'executando': 'status-executando',
                            'pendente': 'status-pendente'
                        };
                        return `<span class="status-badge ${classes[data] || ''}">${data}</span>`;
                    }
                },
                { 
                    data: 'data_inicio',
                    render: data => data ? new Date(data).toLocaleString('pt-BR') : '-'
                },
                { 
                    data: 'data_fim',
                    render: data => data ? new Date(data).toLocaleString('pt-BR') : '-'
                },
                { 
                    data: 'duracao_ms',
                    render: formatDuracao
                },
                { 
                    data: 'registros_processados',
                    render: data => data?.toLocaleString('pt-BR') || '-'
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data) {
                        return `
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-info btn-detalhes" data-id="${data.id}" title="Detalhes">
                                    <i class="bi bi-eye"></i>
                                </button>
                                ${data.caminho_csv ? `
                                <a class="btn btn-outline-success" href="${baseUrl}/api/download-csv/${data.id}" title="Baixar CSV">
                                    <i class="bi bi-file-earmark-excel"></i>
                                </a>` : ''}
                            </div>
                        `;
                    }
                }
            ],
            order: [[0, 'desc']],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            }
        });
        
        // Carregar rotinas no filtro
        $.get(baseUrl + '/rotinas/list', function(response) {
            if (response.sucesso && response.dados) {
                response.dados.forEach(r => {
                    $('#filtroRotina').append(`<option value="${r.id}">${r.nome}</option>`);
                });
            }
        });
        
        // Filtrar
        $('#btnFiltrar').click(function() {
            const params = new URLSearchParams();
            
            const rotina = $('#filtroRotina').val();
            const status = $('#filtroStatus').val();
            const dataInicio = $('#filtroDataInicio').val();
            const dataFim = $('#filtroDataFim').val();
            
            if (rotina) params.append('rotina', rotina);
            if (status) params.append('status', status);
            if (dataInicio) params.append('data_inicio', dataInicio);
            if (dataFim) params.append('data_fim', dataFim);
            
            tabela.ajax.url(baseUrl + '/api/historico?' + params.toString()).load();
        });
        
        // Limpar filtros
        $('#btnLimparFiltros').click(function() {
            $('#filtroRotina, #filtroStatus').val('');
            $('#filtroDataInicio, #filtroDataFim').val('');
            tabela.ajax.url(baseUrl + '/api/historico').load();
        });
        
        // Refresh
        $('#btnRefresh').click(function() {
            tabela.ajax.reload();
        });
        
        // Detalhes
        $(document).on('click', '.btn-detalhes', function() {
            const id = $(this).data('id');
            currentLogId = id;
            
            console.log('Clicou em Ver Detalhes - ID:', id);
            
            $.get(baseUrl + `/api/historico/${id}`, function(response) {
                console.log('Resposta da API:', response);
                
                if (response.sucesso) {
                    console.log('response.dados:', response.dados);
                    console.log('response.dados.logs:', response.dados.logs);
                    
                    // DEBUG: Mostrar alert com quantidade de logs
                    const qtdLogs = response.dados.logs ? response.dados.logs.length : 0;
                    console.log(`QUANTIDADE DE LOGS: ${qtdLogs}`);
                    
                    if (qtdLogs === 0) {
                        console.warn('⚠️ NENHUM LOG ENCONTRADO! Verifique o campo meta no banco.');
                        Swal.fire({
                            icon: 'warning',
                            title: 'Aviso',
                            html: `<strong>Nenhum log de bloco encontrado!</strong><br><br>
                                   ID: ${response.dados.id}<br>
                                   Status: ${response.dados.status}<br>
                                   Campo meta: ${response.dados.meta ? 'EXISTE' : 'NÃO EXISTE'}<br>
                                   Campo logs: ${response.dados.logs ? 'EXISTE' : 'NÃO EXISTE'}`,
                            confirmButtonText: 'Ver Mesmo Assim'
                        }).then(() => {
                            renderizarDetalhes(response.dados);
                            new bootstrap.Modal('#modalDetalhes').show();
                        });
                    } else {
                        renderizarDetalhes(response.dados);
                        new bootstrap.Modal('#modalDetalhes').show();
                    }
                } else {
                    Swal.fire('Erro', response.erro || 'Erro ao carregar detalhes', 'error');
                }
            }).fail(function(xhr, status, error) {
                console.error('Erro na requisição:', {xhr, status, error});
                Swal.fire({
                    icon: 'error',
                    title: 'Erro na Requisição',
                    text: `Status: ${xhr.status} - ${error}`,
                });
            });
        });
        
        // Reexecutar
        $('#btnReexecutar').click(function() {
            if (!currentLogId) return;
            
            Swal.fire({
                title: 'Reexecutar Rotina?',
                text: 'Deseja executar esta rotina novamente?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sim, executar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    // Obter id da rotina e executar
                    $.post(baseUrl + '/api/executar-rotina', { log_id: currentLogId }, function(response) {
                        if (response.sucesso) {
                            Swal.fire('Sucesso', 'Rotina iniciada!', 'success');
                            bootstrap.Modal.getInstance('#modalDetalhes').hide();
                            tabela.ajax.reload();
                        } else {
                            Swal.fire('Erro', response.erro, 'error');
                        }
                    });
                }
            });
        });
        
        // Exportar
        $('#btnExportar').click(function() {
            const params = new URLSearchParams();
            params.append('format', 'csv');
            
            const rotina = $('#filtroRotina').val();
            const status = $('#filtroStatus').val();
            if (rotina) params.append('rotina', rotina);
            if (status) params.append('status', status);
            
            window.location.href = baseUrl + '/api/historico/exportar?' + params.toString();
        });
        
        // Auto-refresh a cada 30s se houver execuções em andamento
        setInterval(function() {
            const executando = parseInt($('#statExecutando').text()) || 0;
            if (executando > 0) {
                tabela.ajax.reload(null, false);
            }
        }, 30000);
        
        function formatDuracao(ms) {
            if (!ms) return '-';
            if (ms < 1000) return ms + 'ms';
            if (ms < 60000) return (ms / 1000).toFixed(1) + 's';
            return Math.floor(ms / 60000) + 'm ' + Math.floor((ms % 60000) / 1000) + 's';
        }
        
        function renderizarDetalhes(dados) {
            console.log('=== RENDERIZAR DETALHES ===');
            console.log('Dados recebidos:', dados);
            console.log('dados.logs existe?', !!dados.logs);
            console.log('dados.logs é array?', Array.isArray(dados.logs));
            console.log('dados.logs.length:', dados.logs ? dados.logs.length : 'N/A');
            
            const html = `
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="text-muted mb-3"><i class="bi bi-info-circle me-2"></i>Informações Gerais</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted" width="40%"><i class="bi bi-hash me-2"></i>ID Log:</td>
                                        <td class="fw-bold">${dados.id}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-gear me-2"></i>Rotina:</td>
                                        <td class="fw-bold text-primary">${dados.nome_rotina || '-'}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-flag me-2"></i>Status:</td>
                                        <td><span class="status-badge status-${dados.status}">${dados.status}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-calendar-check me-2"></i>Início:</td>
                                        <td>${dados.data_inicio ? new Date(dados.data_inicio).toLocaleString('pt-BR') : '-'}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-calendar-x me-2"></i>Fim:</td>
                                        <td>${dados.data_fim ? new Date(dados.data_fim).toLocaleString('pt-BR') : '-'}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-stopwatch me-2"></i>Duração:</td>
                                        <td class="fw-bold text-info">${formatDuracao(dados.duracao_ms)}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h6 class="text-muted mb-3"><i class="bi bi-bar-chart me-2"></i>Estatísticas</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted" width="50%"><i class="bi bi-box me-2"></i>Blocos Executados:</td>
                                        <td class="text-end"><span class="badge bg-secondary">${dados.blocos_executados || 0}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-check-circle me-2"></i>Blocos Sucesso:</td>
                                        <td class="text-end"><span class="badge bg-success">${dados.blocos_sucesso || 0}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-x-circle me-2"></i>Blocos Falha:</td>
                                        <td class="text-end"><span class="badge bg-danger">${dados.blocos_falha || 0}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-file-text me-2"></i>Registros Processados:</td>
                                        <td class="text-end"><span class="badge bg-primary">${dados.registros_processados?.toLocaleString('pt-BR') || '0'}</span></td>
                                    </tr>
                                    ${dados.caminho_csv ? `
                                    <tr>
                                        <td colspan="2" class="pt-3">
                                            <a href="${baseUrl}/api/download-csv/${dados.id}" class="btn btn-success btn-sm w-100">
                                                <i class="bi bi-download me-2"></i>Download CSV
                                            </a>
                                        </td>
                                    </tr>` : ''}
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                ${dados.mensagem_erro ? `
                <div class="alert alert-danger d-flex align-items-start mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-2">Erro Geral da Execução</h6>
                        <pre class="mb-0 bg-white p-3 rounded" style="white-space: pre-wrap; font-size: 12px; border: 1px solid #f5c2c7;">${dados.mensagem_erro}</pre>
                    </div>
                </div>` : ''}
                
                ${dados.logs && dados.logs.length > 0 ? `
                <div class="section-title">
                    <i class="bi bi-list-check"></i>
                    Detalhamento Completo por Bloco
                    <span class="badge bg-secondary ms-2">${dados.logs.length} bloco${dados.logs.length !== 1 ? 's' : ''}</span>
                </div>
                ${dados.logs.map((log, index) => `
                    <div class="card bloco-card mb-4 shadow-sm border-${log.status === 'falha' ? 'danger' : 'success'} border-2">
                        <div class="card-header bg-${log.status === 'falha' ? 'danger' : 'success'} text-white">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-0 d-flex align-items-center">
                                        <i class="bi bi-${log.status === 'falha' ? 'x-circle-fill' : 'check-circle-fill'} me-2 fs-5"></i>
                                        <span>Bloco #${log.ordem || index + 1}: <strong>${log.bloco || 'Sem nome'}</strong></span>
                                        <span class="badge bg-white text-${log.status === 'falha' ? 'danger' : 'success'} ms-3">${log.tipo || 'UNKNOWN'}</span>
                                    </h6>
                                </div>
                                <div class="col-md-6 text-end">
                                    <span class="info-badge bg-white text-dark me-2">
                                        <i class="bi bi-stopwatch"></i>
                                        <strong>${formatDuracao(log.duracao_ms)}</strong>
                                    </span>
                                    ${log.registros !== undefined && log.registros !== null ? `
                                    <span class="info-badge bg-white text-dark">
                                        <i class="bi bi-file-earmark-text"></i>
                                        <strong>${log.registros.toLocaleString('pt-BR')}</strong> registro${log.registros !== 1 ? 's' : ''}
                                    </span>` : ''}
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            ${log.erro ? `
                            <div class="alert alert-danger erro-box mb-3">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-bug-fill me-3 fs-4"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="alert-heading mb-2"><i class="bi bi-exclamation-triangle me-2"></i>Erro Detectado</h6>
                                        <pre class="mb-2 bg-white p-3 rounded" style="white-space: pre-wrap; font-size: 12px; border: 1px solid #f5c2c7;">${log.erro}</pre>
                                        <div class="alert alert-warning mb-0 py-2">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <small><strong>Atenção:</strong> A execução pode ter parado neste bloco. Verifique se os blocos posteriores foram executados.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>` : ''}
                            
                            ${log.resultado ? `
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-info-circle-fill text-primary me-2"></i>
                                    <strong class="text-muted">Resultado:</strong>
                                </div>
                                <div class="resultado-box">${log.resultado}</div>
                            </div>` : ''}
                            
                            ${log.arquivo_csv ? `
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-file-earmark-arrow-down-fill text-success me-2"></i>
                                    <strong class="text-muted">Arquivo Gerado:</strong>
                                </div>
                                <div class="bg-light p-3 rounded border">
                                    <code style="font-size: 11px; word-break: break-all; color: #0d6efd;">${log.arquivo_csv}</code>
                                </div>
                            </div>` : ''}
                            
                            ${log.sql ? `
                            <div class="sql-container">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-code-slash text-dark me-2 fs-5"></i>
                                        <strong class="text-muted">SQL Executado:</strong>
                                    </div>
                                    <button class="btn btn-sm btn-outline-secondary btn-copiar" onclick="copiarSQL(${index})">
                                        <i class="bi bi-clipboard me-1"></i>Copiar SQL
                                    </button>
                                </div>
                                <pre class="bg-dark text-light p-3 rounded mb-0" id="sql-${index}" style="font-size: 13px; line-height: 1.6; max-height: 400px; overflow-y: auto; white-space: pre-wrap; word-wrap: break-word;">${log.sql}</pre>
                            </div>` : ''}
                        </div>
                    </div>
                `).join('')}` : `
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle me-3 fs-4"></i>
                    <div>
                        <strong>Nenhum log de bloco encontrado</strong>
                        <p class="mb-0 small">Esta execução não possui detalhes de blocos registrados.</p>
                    </div>
                </div>`}
            `;
            
            $('#modalDetalhesBody').html(html);
        }
        
        function copiarSQL(index) {
            const sqlElement = document.getElementById(`sql-${index}`);
            if (sqlElement) {
                navigator.clipboard.writeText(sqlElement.textContent).then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'SQL Copiado!',
                        text: 'O código SQL foi copiado para a área de transferência.',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }).catch(err => {
                    console.error('Erro ao copiar:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao copiar',
                        text: 'Não foi possível copiar o SQL.',
                        timer: 2000
                    });
                });
            }
        }
    });
    </script>
</body>
</html>
