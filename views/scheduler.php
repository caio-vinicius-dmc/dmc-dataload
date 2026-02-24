<?php
/**
 * DMC DataLoad - Scheduler / Agendamentos
 * Nova UI Moderna
 */
$pageTitle = 'Agendamentos';
$currentPage = 'scheduler';

ob_start();
?>

<!-- Page Header Modern -->
<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-clock"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Agendamentos</h1>
        <p class="page-subtitle-modern">Gerencie os agendamentos de execução das rotinas</p>
    </div>
    <div class="d-flex gap-2 ms-auto">
        <button class="btn-modern-primary" onclick="novoAgendamento()">
            <i class="bi bi-plus-circle me-2"></i>Novo Agendamento
        </button>
        <a href="<?= BASE_URL ?>/rotinas" class="btn btn-outline-secondary">
            <i class="bi bi-gear me-2"></i>Gerenciar Rotinas
        </a>
        <button class="btn btn-outline-success" id="btnStartWorker">
            <i class="bi bi-play-fill me-2"></i>Iniciar Worker
        </button>
        <button class="btn btn-outline-danger" id="btnStopWorker" style="display: none;">
            <i class="bi bi-stop-fill me-2"></i>Parar Worker
        </button>
    </div>
</div>

<!-- Worker Status -->
<div class="card-modern mb-4">
    <div class="card-modern-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h6 class="mb-0">
                    <i class="bi bi-cpu me-2"></i>Status do Worker
                </h6>
            </div>
            <div class="col-md-6 text-md-end">
                <span class="badge bg-secondary" id="workerStatus">
                    <i class="bi bi-circle-fill me-1"></i>Verificando...
                </span>
                <span class="ms-3 text-muted small" id="workerLastCheck">-</span>
            </div>
        </div>
        <hr>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="text-muted small">Última Verificação</div>
                <strong id="workerLastRun">-</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Próxima Execução</div>
                <strong id="workerNextRun">-</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Rotinas Agendadas</div>
                <strong id="workerScheduled">0</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Executando Agora</div>
                <strong id="workerRunning">0</strong>
            </div>
        </div>
    </div>
</div>

<!-- Rotinas Agendadas -->
<div class="card-modern">
    <div class="card-modern-header d-flex justify-content-between align-items-center">
        <div><i class="bi bi-calendar-check me-2"></i>Rotinas com Agendamento</div>
        <button class="btn btn-sm btn-outline-primary" onclick="recarregarAgendamentos()">
            <i class="bi bi-arrow-clockwise me-1"></i>Atualizar
        </button>
    </div>
    <div class="card-modern-body p-0">
        <div class="table-responsive">
            <table class="table-modern" id="tblAgendamentos">
                <thead>
                    <tr>
                        <th><i class="bi bi-gear me-2"></i>Rotina</th>
                        <th><i class="bi bi-clock me-2"></i>Expressão CRON</th>
                        <th><i class="bi bi-card-text me-2"></i>Descrição</th>
                        <th><i class="bi bi-calendar me-2"></i>Próxima Execução</th>
                        <th><i class="bi bi-check-circle me-2"></i>Status</th>
                        <th><i class="bi bi-tools me-2"></i>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                            <span class="ms-2">Carregando...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Log de Execuções do Scheduler -->
<div class="card-modern mt-4">
    <div class="card-modern-header">
        <i class="bi bi-terminal me-2"></i>Log do Scheduler
    </div>
    <div class="card-modern-body p-0">
        <div id="schedulerLog" class="bg-dark text-white p-3" style="height: 300px; overflow-y: auto; font-family: monospace; font-size: 0.85rem;">
            <div class="text-muted fst-italic text-white-50">[Aguardando atividade do scheduler...]</div>
        </div>
    </div>
</div>

<!-- Modal de Configuração de Agendamento -->
<div class="modal fade" id="modalAgendamento" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <!-- Header com gradiente -->
            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem 2rem;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-calendar-event fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fw-bold" id="modalAgendamentoTitle">Configurar Agendamento</h5>
                        <small class="opacity-75">Defina quando a rotina deve executar</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-0">
                <form id="formAgendamento">
                    <input type="hidden" id="agendamento_id_rotina">
                    
                    <!-- Tabs de navegação -->
                    <ul class="nav nav-pills nav-fill p-3 bg-light border-bottom" id="agendamentoTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill" id="tab-rotina" data-bs-toggle="pill" data-bs-target="#pane-rotina" type="button">
                                <i class="bi bi-box-seam me-2"></i>1. Rotina
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill" id="tab-frequencia" data-bs-toggle="pill" data-bs-target="#pane-frequencia" type="button">
                                <i class="bi bi-clock me-2"></i>2. Frequência
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill" id="tab-periodo" data-bs-toggle="pill" data-bs-target="#pane-periodo" type="button">
                                <i class="bi bi-calendar-range me-2"></i>3. Período
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill" id="tab-avancado" data-bs-toggle="pill" data-bs-target="#pane-avancado" type="button">
                                <i class="bi bi-gear me-2"></i>4. Avançado
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content p-4">
                        <!-- Tab 1: Seleção de Rotina -->
                        <div class="tab-pane fade show active" id="pane-rotina" role="tabpanel">
                            <div class="text-center mb-4">
                                <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi bi-box-seam text-primary" style="font-size: 2.5rem;"></i>
                                </div>
                                <h4 class="fw-bold">Selecione a Rotina</h4>
                                <p class="text-muted">Escolha qual rotina deseja agendar para execução automática</p>
                            </div>
                            
                            <div class="mx-auto" style="max-width: 500px;">
                                <label class="form-label fw-semibold"><i class="bi bi-search me-2"></i>Rotina</label>
                                <select class="form-select form-select-lg" id="agendamento_rotina" required style="border-radius: 12px; padding: 1rem;">
                                    <option value="">Selecione uma rotina...</option>
                                </select>
                                <div class="form-text mt-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Apenas rotinas cadastradas e configuradas aparecem nesta lista
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tab 2: Frequência -->
                        <div class="tab-pane fade" id="pane-frequencia" role="tabpanel">
                            <div class="text-center mb-4">
                                <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi bi-clock-history text-success" style="font-size: 2.5rem;"></i>
                                </div>
                                <h4 class="fw-bold">Defina a Frequência</h4>
                                <p class="text-muted">Com que frequência a rotina deve ser executada?</p>
                            </div>
                            
                            <!-- Modo de Configuração -->
                            <div class="btn-group w-100 mb-4" role="group" style="max-width: 400px; margin: 0 auto; display: flex !important;">
                                <input type="radio" class="btn-check" name="modo_config" id="modo_visual" value="visual" checked>
                                <label class="btn btn-outline-primary rounded-start-pill" for="modo_visual">
                                    <i class="bi bi-palette me-2"></i>Visual
                                </label>
                                <input type="radio" class="btn-check" name="modo_config" id="modo_cron" value="cron">
                                <label class="btn btn-outline-primary rounded-end-pill" for="modo_cron">
                                    <i class="bi bi-code-square me-2"></i>CRON Manual
                                </label>
                            </div>
                            
                            <!-- Config Visual -->
                            <div id="config_visual">
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Tipo de Frequência</label>
                                        <select class="form-select" id="freq_tipo" style="border-radius: 10px;">
                                            <option value="minutos">⏱️ A cada X minutos</option>
                                            <option value="horas">🕐 A cada X horas</option>
                                            <option value="diario">📅 Diariamente</option>
                                            <option value="semanal">📆 Semanalmente</option>
                                            <option value="mensal">🗓️ Mensalmente</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4" id="campo_intervalo">
                                        <label class="form-label fw-semibold">Intervalo</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="freq_intervalo" value="5" min="1" style="border-radius: 10px 0 0 10px;">
                                            <span class="input-group-text" style="border-radius: 0 10px 10px 0;">minuto(s)</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4" id="campo_hora" style="display:none;">
                                        <label class="form-label fw-semibold">Horário</label>
                                        <input type="time" class="form-control" id="freq_hora" value="08:00" style="border-radius: 10px;">
                                    </div>
                                    <div class="col-md-4" id="campo_minuto" style="display:none;">
                                        <label class="form-label fw-semibold">No minuto</label>
                                        <input type="number" class="form-control" id="freq_minuto" value="0" min="0" max="59" style="border-radius: 10px;">
                                    </div>
                                    <div class="col-md-4" id="campo_pular_dias" style="display:none;">
                                        <label class="form-label fw-semibold">Pular Dias</label>
                                        <input type="number" class="form-control" id="freq_pular_dias" value="0" min="0" max="30" style="border-radius: 10px;">
                                        <small class="text-muted">0 = todos, 1 = dia sim/não</small>
                                    </div>
                                </div>
                                
                                <!-- Dias da Semana -->
                                <div class="mt-4" id="campo_dias_semana" style="display:none;">
                                    <label class="form-label fw-semibold">Dias da Semana</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <input type="checkbox" class="btn-check" id="dia_0" value="0">
                                        <label class="btn btn-outline-secondary rounded-pill px-3" for="dia_0">Dom</label>
                                        <input type="checkbox" class="btn-check" id="dia_1" value="1" checked>
                                        <label class="btn btn-outline-secondary rounded-pill px-3" for="dia_1">Seg</label>
                                        <input type="checkbox" class="btn-check" id="dia_2" value="2" checked>
                                        <label class="btn btn-outline-secondary rounded-pill px-3" for="dia_2">Ter</label>
                                        <input type="checkbox" class="btn-check" id="dia_3" value="3" checked>
                                        <label class="btn btn-outline-secondary rounded-pill px-3" for="dia_3">Qua</label>
                                        <input type="checkbox" class="btn-check" id="dia_4" value="4" checked>
                                        <label class="btn btn-outline-secondary rounded-pill px-3" for="dia_4">Qui</label>
                                        <input type="checkbox" class="btn-check" id="dia_5" value="5" checked>
                                        <label class="btn btn-outline-secondary rounded-pill px-3" for="dia_5">Sex</label>
                                        <input type="checkbox" class="btn-check" id="dia_6" value="6">
                                        <label class="btn btn-outline-secondary rounded-pill px-3" for="dia_6">Sáb</label>
                                    </div>
                                </div>
                                
                                <!-- Dia do Mês -->
                                <div class="mt-4" id="campo_dia_mes" style="display:none;">
                                    <label class="form-label fw-semibold">Dia do Mês</label>
                                    <input type="number" class="form-control" id="freq_dia_mes" value="1" min="1" max="31" style="max-width: 200px; border-radius: 10px;">
                                </div>
                                
                                <!-- Dias Intercalados -->
                                <div class="mt-4" id="campo_dias_intercalados" style="display:none;">
                                    <div class="alert alert-info rounded-3 border-0">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <strong>Dias Intercalados:</strong> Executa a cada X dias.
                                    </div>
                                    <label class="form-label fw-semibold">Executar a cada quantos dias?</label>
                                    <input type="number" class="form-control" id="freq_dias_intervalo" value="2" min="1" max="365" style="max-width: 200px; border-radius: 10px;">
                                </div>
                            </div>
                            
                            <!-- Config CRON Manual -->
                            <div id="config_cron" style="display:none;">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold"><i class="bi bi-terminal me-2"></i>Expressão CRON</label>
                                    <input type="text" class="form-control form-control-lg font-monospace text-center" id="cron_manual" placeholder="*/5 * * * *" style="border-radius: 12px; font-size: 1.25rem;">
                                    <div class="form-text">Formato: minuto hora dia mês dia_da_semana</div>
                                </div>
                                
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Presets Rápidos:</label>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <button type="button" class="btn btn-outline-secondary w-100 rounded-pill" onclick="aplicarPreset('*/5 * * * *')">5 min</button>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <button type="button" class="btn btn-outline-secondary w-100 rounded-pill" onclick="aplicarPreset('*/15 * * * *')">15 min</button>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <button type="button" class="btn btn-outline-secondary w-100 rounded-pill" onclick="aplicarPreset('0 * * * *')">1 hora</button>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <button type="button" class="btn btn-outline-secondary w-100 rounded-pill" onclick="aplicarPreset('0 8 * * *')">08:00</button>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <button type="button" class="btn btn-outline-secondary w-100 rounded-pill" onclick="aplicarPreset('0 8 * * 1-5')">Úteis 08h</button>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <button type="button" class="btn btn-outline-secondary w-100 rounded-pill" onclick="aplicarPreset('0 0 * * *')">Meia-noite</button>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <button type="button" class="btn btn-outline-secondary w-100 rounded-pill" onclick="aplicarPreset('0 0 1 * *')">Mensal</button>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <button type="button" class="btn btn-outline-secondary w-100 rounded-pill" onclick="aplicarPreset('0 0 * * 0')">Semanal</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tab 3: Período -->
                        <div class="tab-pane fade" id="pane-periodo" role="tabpanel">
                            <div class="text-center mb-4">
                                <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi bi-calendar-range text-info" style="font-size: 2.5rem;"></i>
                                </div>
                                <h4 class="fw-bold">Defina o Período</h4>
                                <p class="text-muted">Por quanto tempo a rotina deve ficar agendada?</p>
                            </div>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                    <i class="bi bi-play-circle text-success fs-4"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">Data/Hora de Início</h6>
                                                    <small class="text-muted">Quando começar?</small>
                                                </div>
                                            </div>
                                            <input type="datetime-local" class="form-control" id="data_inicio" style="border-radius: 10px;">
                                            <small class="text-muted mt-2 d-block">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Vazio = inicia imediatamente
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center gap-3 mb-3">
                                                <div class="rounded-circle bg-danger bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                    <i class="bi bi-stop-circle text-danger fs-4"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">Data/Hora de Término</h6>
                                                    <small class="text-muted">Quando parar?</small>
                                                </div>
                                            </div>
                                            <input type="datetime-local" class="form-control" id="data_fim" style="border-radius: 10px;">
                                            <small class="text-muted mt-2 d-block">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Vazio = executa indefinidamente
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold mb-3">
                                            <i class="bi bi-calendar-x me-2 text-warning"></i>Datas para Ignorar
                                        </h6>
                                        <textarea class="form-control" id="datas_ignorar" rows="3" placeholder="Datas que a rotina NÃO deve executar (uma por linha)&#10;Exemplo: 25/12/2025" style="border-radius: 10px;"></textarea>
                                        <div class="form-check mt-3">
                                            <input class="form-check-input" type="checkbox" id="ignorar_feriados">
                                            <label class="form-check-label" for="ignorar_feriados">
                                                🎉 Ignorar feriados nacionais automaticamente
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tab 4: Avançado -->
                        <div class="tab-pane fade" id="pane-avancado" role="tabpanel">
                            <div class="text-center mb-4">
                                <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="bi bi-gear text-warning" style="font-size: 2.5rem;"></i>
                                </div>
                                <h4 class="fw-bold">Configurações Avançadas</h4>
                                <p class="text-muted">Opções adicionais de comportamento</p>
                            </div>
                            
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                                        <div class="card-body p-4 text-center">
                                            <i class="bi bi-arrow-repeat text-primary fs-1 mb-3"></i>
                                            <h6 class="fw-bold">Tentativas em Falha</h6>
                                            <input type="number" class="form-control text-center mx-auto mt-3" id="max_tentativas" value="3" min="1" max="10" style="max-width: 120px; border-radius: 10px; font-size: 1.25rem;">
                                            <small class="text-muted d-block mt-2">vezes</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                                        <div class="card-body p-4 text-center">
                                            <i class="bi bi-hourglass-split text-info fs-1 mb-3"></i>
                                            <h6 class="fw-bold">Intervalo entre Tentativas</h6>
                                            <input type="number" class="form-control text-center mx-auto mt-3" id="intervalo_tentativas" value="5" min="1" style="max-width: 120px; border-radius: 10px; font-size: 1.25rem;">
                                            <small class="text-muted d-block mt-2">minutos</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                                        <div class="card-body p-4 text-center">
                                            <i class="bi bi-stopwatch text-danger fs-1 mb-3"></i>
                                            <h6 class="fw-bold">Timeout Máximo</h6>
                                            <input type="number" class="form-control text-center mx-auto mt-3" id="timeout" value="300" min="30" style="max-width: 120px; border-radius: 10px; font-size: 1.25rem;">
                                            <small class="text-muted d-block mt-2">segundos</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="notificar_falha" checked style="width: 3rem; height: 1.5rem;">
                                    <label class="form-check-label ms-2" for="notificar_falha">
                                        <strong>📧 Notificar por e-mail em caso de falha</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Preview fixo na parte inferior -->
                    <div class="border-top bg-light p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <i class="bi bi-eye text-primary fs-4"></i>
                            <h6 class="mb-0 fw-bold">Preview do Agendamento</h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="bg-white rounded-3 p-3 shadow-sm">
                                    <small class="text-muted d-block mb-1">Expressão CRON</small>
                                    <code class="fs-5" id="preview_cron">-</code>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-white rounded-3 p-3 shadow-sm">
                                    <small class="text-muted d-block mb-1">Descrição</small>
                                    <span class="fw-semibold" id="preview_descricao">-</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-white rounded-3 p-3 shadow-sm">
                                    <small class="text-muted d-block mb-1">Próxima Execução</small>
                                    <span class="fw-semibold text-success" id="preview_proxima">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer border-0 bg-white px-4 py-3">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="salvarAgendamento()" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                    <i class="bi bi-check-lg me-2"></i>Salvar Agendamento
                </button>
            </div>
        <?php
$content = ob_get_clean();

$extraStyles = <<<'STYLES'
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
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

.page-header-modern {
    background: white;
    padding: 1.75rem 2rem;
    border-radius: var(--radius-lg);
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
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

.card-modern {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.card-modern-header {
    padding: 1.25rem 1.5rem;
    background: linear-gradient(to right, #f8fafc, #f1f5f9);
    border-bottom: 2px solid #e2e8f0;
    font-weight: 600;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-modern-body {
    padding: 1.5rem;
}

.table-modern {
    width: 100%;
    margin: 0;
}

.table-modern thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.table-modern thead th {
    padding: 1rem;
    font-weight: 600;
    border: none;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-modern thead th:first-child {
    border-top-left-radius: var(--radius-md);
}

.table-modern thead th:last-child {
    border-top-right-radius: var(--radius-md);
}

.table-modern tbody tr {
    border-bottom: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.table-modern tbody tr:hover {
    background-color: #f8fafc;
    transform: scale(1.01);
}

.table-modern tbody td {
    padding: 1rem;
    vertical-align: middle;
}

.btn-modern-primary {
    background: var(--gradient-primary);
    color: white;
    padding: 0.625rem 1.25rem;
    border-radius: var(--radius-md);
    border: none;
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

.modal-modern .modal-header-modern {
    background: var(--gradient-primary);
    color: white;
    padding: 1.25rem 1.5rem;
    border-bottom: none;
}

.modal-modern .modal-header-modern .modal-title {
    font-weight: 600;
    font-size: 1.25rem;
}

.modal-modern .modal-header-modern .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.8;
}

.modal-modern .modal-body-modern {
    padding: 1.5rem;
}

.modal-modern .modal-footer-modern {
    padding: 1rem 1.5rem;
    border-top: 2px solid #e2e8f0;
    background: #f8fafc;
}

@media (max-width: 991px) {
    .page-header-modern {
        flex-wrap: wrap;
    }
    
    .page-header-modern .ms-auto {
        margin-left: 0 !important;
        width: 100%;
    }
}

@media (max-width: 767px) {
    .page-icon-modern {
        width: 48px;
        height: 48px;
        font-size: 1.5rem;
    }
    
    .page-title-modern {
        font-size: 1.5rem;
    }
    
    .table-modern {
        font-size: 0.875rem;
    }
}
</style>
STYLES;

$extraScripts = <<<'SCRIPTS'
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let workerRunning = false;
let logInterval = null;
let isEditingMode = false;
let editingRotinaId = null;
let editingOriginalAtiva = true;

// Presets CRON
const cronPresets = {
    "* * * * *": "A cada minuto",
    "*/5 * * * *": "A cada 5 minutos",
    "*/15 * * * *": "A cada 15 minutos",
    "*/30 * * * *": "A cada 30 minutos",
    "0 * * * *": "A cada hora",
    "0 */2 * * *": "A cada 2 horas",
    "0 */6 * * *": "A cada 6 horas",
    "0 0 * * *": "Diariamente à meia-noite",
    "0 8 * * *": "Diariamente às 8h",
    "0 8 * * 1-5": "Dias úteis às 8h",
    "0 0 * * 0": "Semanalmente (domingo)",
    "0 0 1 * *": "Mensalmente (dia 1)"
};

function descricaoCron(expr) {
    return cronPresets[expr] || expr;
}

function carregarAgendamentos() {
    $.getJSON(baseUrl + "/api/scheduler/rotinas", function(res) {
        const tbody = $("#tblAgendamentos tbody");
        tbody.empty();
        
        if (!res.dados || res.dados.length === 0) {
            tbody.html(`<tr><td colspan="6" class="text-center py-5">
                <div class="d-flex flex-column align-items-center gap-3">
                    <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                    <div>
                        <h5 class="text-muted mb-2">Nenhuma rotina com agendamento</h5>
                        <p class="text-muted mb-3">Configure o agendamento CRON nas rotinas para executá-las automaticamente</p>
                        <a href="${baseUrl}/rotinas" class="btn btn-primary">
                            <i class="bi bi-gear me-2"></i>Configurar Rotinas
                        </a>
                    </div>
                </div>
            </td></tr>`);
            return;
        }
        
        res.dados.forEach(r => {
            const proxExec = r.proxima_execucao ? new Date(r.proxima_execucao).toLocaleString("pt-BR") : "-";
            const statusBadge = r.ativa 
                ? `<span class="badge-status badge-success"><i class="bi bi-check-circle me-1"></i>Ativa</span>`
                : `<span class="badge-status badge-secondary"><i class="bi bi-pause-circle me-1"></i>Inativa</span>`;
            
            tbody.append(`<tr>
                <td><strong>${r.nome}</strong></td>
                <td><code>${r.agendamento_cron}</code></td>
                <td><small class="text-muted">${descricaoCron(r.agendamento_cron)}</small></td>
                <td>${proxExec}</td>
                <td>${statusBadge}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-${r.ativa ? "warning" : "success"}" onclick="toggleAtiva(${r.id}, ${!r.ativa})" title="${r.ativa ? "Desativar" : "Ativar"}">
                            <i class="bi bi-${r.ativa ? "pause" : "play"}-fill"></i>
                        </button>
                        <button class="btn btn-outline-primary" onclick="editarAgendamento(${r.id})" title="Editar Agendamento">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="excluirAgendamento(${r.id}, '${r.nome}')" title="Excluir Agendamento">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>
                </td>
            </tr>`);
        });
        
        $("#workerScheduled").text(res.dados.filter(r => r.ativa).length);
    }).fail(function(xhr, status, error) {
        console.error("Erro ao carregar agendamentos:", error);
        console.error("Status:", status);
        console.error("Response:", xhr.responseText);
        const tbody = $("#tblAgendamentos tbody");
        tbody.html(`<tr><td colspan="6" class="text-center py-4">
            <i class="bi bi-exclamation-triangle text-danger" style="font-size: 2rem;"></i>
            <div class="mt-2">
                <h6 class="text-danger">Erro ao carregar agendamentos</h6>
                <small class="text-muted">${xhr.responseText || error}</small>
            </div>
        </td></tr>`);
    });
}

function verificarWorker() {
    $.getJSON(baseUrl + "/api/scheduler/status", function(res) {
        const badge = $("#workerStatus");
        if (res.running) {
            badge.removeClass("bg-secondary bg-danger").addClass("bg-success");
            badge.html(`<i class="bi bi-circle-fill me-1"></i>Em execução`);
            $("#btnStartWorker").hide();
            $("#btnStopWorker").show();
            workerRunning = true;
        } else {
            badge.removeClass("bg-secondary bg-success").addClass("bg-danger");
            badge.html(`<i class="bi bi-circle-fill me-1"></i>Parado`);
            $("#btnStartWorker").show();
            $("#btnStopWorker").hide();
            workerRunning = false;
        }
        
        if (res.last_run) {
            $("#workerLastRun").text(new Date(res.last_run).toLocaleString("pt-BR"));
        }
        if (res.next_run) {
            $("#workerNextRun").text(new Date(res.next_run).toLocaleString("pt-BR"));
        }
        if (res.running_count !== undefined) {
            $("#workerRunning").text(res.running_count);
        }
        
        $("#workerLastCheck").text("Atualizado: " + new Date().toLocaleTimeString("pt-BR"));
    }).fail(function() {
        $("#workerStatus").removeClass("bg-success bg-danger").addClass("bg-secondary");
        $("#workerStatus").html(`<i class="bi bi-circle-fill me-1"></i>Desconhecido`);
    });
}

function toggleAtiva(id, ativar) {
    $.post(baseUrl + "/api/scheduler/toggle", { id: id, ativa: ativar ? 1 : 0 }, function(res) {
        if (res.sucesso) {
            Swal.fire("Sucesso!", ativar ? "Rotina ativada." : "Rotina desativada.", "success");
            carregarAgendamentos();
        } else {
            Swal.fire("Erro!", res.erro, "error");
        }
    }, "json");
}

function editarAgendamento(id) {
    $.getJSON(baseUrl + "/rotinas/get/" + id, function(r) {
        const rotina = r.rotina;
        Swal.fire({
            title: "Editar Agendamento",
            html: `
                <div class="text-start">
                    <label class="form-label">Expressão CRON</label>
                    <input type="text" class="form-control mb-2" id="swal-cron" value="${rotina.agendamento_cron || ""}" placeholder="*/5 * * * *">
                    <small class="text-muted">Formato: minuto hora dia mês dia_semana</small>
                    <hr>
                    <label class="form-label">Presets</label>
                    <select class="form-select" id="swal-preset" onchange="document.getElementById(\'swal-cron\').value = this.value">
                        <option value="">Selecione um preset...</option>
                        ${Object.entries(cronPresets).map(([k,v]) => `<option value="${k}">${v} (${k})</option>`).join("")}
                    </select>
                </div>
            `,
            showCancelButton: true,
            cancelButtonText: "Cancelar",
            confirmButtonText: "Salvar",
            preConfirm: () => {
                return document.getElementById("swal-cron").value;
            }
        }).then(result => {
            if (result.isConfirmed) {
                $.post(baseUrl + "/api/scheduler/atualizar", { id: id, cron: result.value }, function(res) {
                    if (res.sucesso) {
                        Swal.fire("Salvo!", "Agendamento atualizado.", "success");
                        carregarAgendamentos();
                    } else {
                        Swal.fire("Erro!", res.erro, "error");
                    }
                }, "json");
            }
        });
    });
}

function recarregarAgendamentos() {
    carregarAgendamentos();
    verificarWorker();
}

function adicionarLog(msg, tipo = "info") {
    const log = $("#schedulerLog");
    const timestamp = new Date().toLocaleTimeString("pt-BR");
    const colors = {
        info: "#0ea5e9",
        success: "#10b981",
        warning: "#f59e0b",
        error: "#ef4444"
    };
    log.append(`<div style="color: ${colors[tipo] || "white"}">[${timestamp}] ${msg}</div>`);
    log.scrollTop(log[0].scrollHeight);
}

// Start Worker
$("#btnStartWorker").click(function() {
    $(this).prop("disabled", true).html(`<span class="spinner-border spinner-border-sm me-2"></span>Iniciando...`);
    
    $.post(baseUrl + "/api/scheduler/start", function(res) {
        if (res.sucesso) {
            adicionarLog("Worker iniciado com sucesso!", "success");
            verificarWorker();
        } else {
            Swal.fire("Erro!", res.erro, "error");
            adicionarLog("Erro ao iniciar worker: " + res.erro, "error");
        }
    }, "json").always(function() {
        $("#btnStartWorker").prop("disabled", false).html(`<i class="bi bi-play-fill me-2"></i>Iniciar Worker`);
    });
});

// Stop Worker
$("#btnStopWorker").click(function() {
    $(this).prop("disabled", true);
    
    $.post(baseUrl + "/api/scheduler/stop", function(res) {
        if (res.sucesso) {
            adicionarLog("Worker parado.", "warning");
            verificarWorker();
        } else {
            Swal.fire("Erro!", res.erro, "error");
        }
    }, "json").always(function() {
        $("#btnStopWorker").prop("disabled", false);
    });
});

// Carregar logs
function carregarLogs() {
    $.getJSON(baseUrl + "/api/scheduler/logs", function(res) {
        if (res.logs && res.logs.length > 0) {
            res.logs.forEach(l => {
                adicionarLog(l.mensagem, l.tipo || "info");
            });
        }
    });
}

$(document).ready(function() {
    carregarAgendamentos();
    verificarWorker();
    
    // Atualizar a cada 10s
    setInterval(verificarWorker, 10000);
    setInterval(carregarAgendamentos, 30000);
    
    // Carregar rotinas no select
    carregarRotinasSelect();
    
    // Event listeners para o modal
    $("input[name='modo_config']").change(function() {
        if ($(this).val() === "visual") {
            $("#config_visual").show();
            $("#config_cron").hide();
        } else {
            $("#config_visual").hide();
            $("#config_cron").show();
        }
        atualizarPreview();
    });
    
    // Atualizar preview ao mudar campos
    $("#freq_tipo, #freq_intervalo, #freq_hora, #freq_minuto, #freq_dia_mes, input[name^='dia_']").change(atualizarPreview);
    $("#cron_manual").on("input", atualizarPreview);
    
    // Mostrar/ocultar campos baseado no tipo de frequência
    $("#freq_tipo").change(function() {
        const tipo = $(this).val();
        $("#campo_intervalo, #campo_hora, #campo_minuto, #campo_dias_semana, #campo_dia_mes, #campo_dias_intercalados, #campo_pular_dias").hide();
        
        if (tipo === "minutos" || tipo === "horas") {
            $("#campo_intervalo").show();
        } else if (tipo === "diario") {
            $("#campo_hora, #campo_pular_dias").show();
        } else if (tipo === "dias_intercalados") {
            $("#campo_hora, #campo_dias_intercalados").show();
        } else if (tipo === "semanal") {
            $("#campo_hora, #campo_dias_semana").show();
        } else if (tipo === "mensal") {
            $("#campo_hora, #campo_dia_mes").show();
        }
        atualizarPreview();
    });
});

// Carregar rotinas no select
function carregarRotinasSelect() {
    $.getJSON(baseUrl + "/rotinas/list", function(res) {
        const select = $("#agendamento_rotina");
        select.find("option:not(:first)").remove();
        if (res.data) {
            res.data.forEach(r => {
                select.append(`<option value="${r.id}">${r.nome}</option>`);
            });
        }
    }).fail(function(xhr, status, error) {
        console.error("Erro ao carregar rotinas:", error, xhr.responseText);
    });
}

// Abrir modal de novo agendamento
function novoAgendamento() {
    // Resetar modo de edição
    isEditingMode = false;
    editingRotinaId = null;
    editingOriginalAtiva = true;
    
    $("#formAgendamento")[0].reset();
    $("#agendamento_id_rotina").val("");
    $("#modo_visual").prop("checked", true).trigger("change");
    $("#freq_tipo").val("minutos").trigger("change");
    $("#modalAgendamentoTitle").html('<i class="bi bi-plus-circle me-2"></i>Novo Agendamento');
    atualizarPreview();
    new bootstrap.Modal("#modalAgendamento").show();
}

// Editar agendamento existente
function editarAgendamento(id) {
    $.getJSON(baseUrl + "/api/scheduler/rotinas", function(res) {
        const rotina = res.dados.find(r => r.id == id);
        if (!rotina) {
            Swal.fire("Erro!", "Rotina não encontrada.", "error");
            return;
        }
        
        // Configurar modo de edição
        isEditingMode = true;
        editingRotinaId = id;
        editingOriginalAtiva = rotina.ativa;
        
        // Configurar modo de edição
        isEditingMode = true;
        editingRotinaId = id;
        editingOriginalAtiva = rotina.ativa;
        
        // Atualizar título do modal
        $("#modalAgendamentoTitle").html('<i class="bi bi-pencil-fill me-2"></i>Editar Agendamento');
        
        // Preencher campos do modal
        $("#agendamento_id_rotina").val(rotina.id);
        $("#agendamento_rotina").val(rotina.id);
        
        // Carregar dados extras da rotina
        $.getJSON(baseUrl + "/api/scheduler/detalhes/" + id, function(detalhes) {
            // Preencher campos de período
            if (detalhes.data_inicio) {
                $("#data_inicio").val(detalhes.data_inicio);
            }
            if (detalhes.data_fim) {
                $("#data_fim").val(detalhes.data_fim);
            }
            
            // Preencher datas para ignorar
            if (detalhes.datas_ignorar_json) {
                try {
                    const datas = JSON.parse(detalhes.datas_ignorar_json);
                    $("#datas_ignorar").val(datas.join("\n"));
                } catch(e) {
                    $("#datas_ignorar").val(detalhes.datas_ignorar_json);
                }
            }
            
            // Preencher configurações avançadas
            $("#ignorar_feriados").prop("checked", detalhes.ignorar_feriados == 1);
            $("#max_tentativas").val(detalhes.max_tentativas || 3);
            $("#timeout").val(detalhes.timeout || 300);
            $("#notificar_falha").prop("checked", detalhes.notificar_falha !== 0);
            
            // Modo CRON manual
            $("#modo_cron").prop("checked", true).trigger("change");
            $("#cron_manual").val(rotina.agendamento_cron);
            
            atualizarPreview();
            new bootstrap.Modal("#modalAgendamento").show();
        }).fail(function() {
            // Se falhar, usar apenas CRON
            $("#modo_cron").prop("checked", true).trigger("change");
            $("#cron_manual").val(rotina.agendamento_cron);
            atualizarPreview();
            new bootstrap.Modal("#modalAgendamento").show();
        });
    });
}

// Excluir agendamento
function excluirAgendamento(id, nome) {
    Swal.fire({
        title: "Excluir Agendamento?",
        html: `Tem certeza que deseja remover o agendamento da rotina <strong>${nome}</strong>?<br><br><small class="text-muted">A rotina continuará existindo, mas não será mais executada automaticamente.</small>`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Sim, excluir!",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(baseUrl + "/api/scheduler/excluir", { id_rotina: id }, function(res) {
                if (res.sucesso) {
                    Swal.fire("Excluído!", "Agendamento removido com sucesso.", "success");
                    carregarAgendamentos();
                } else {
                    Swal.fire("Erro!", res.erro || "Erro ao excluir agendamento.", "error");
                }
            }, "json").fail(function() {
                Swal.fire("Erro!", "Erro ao comunicar com o servidor.", "error");
            });
        }
    });
}

// Converter configuração visual para CRON
function gerarCronDeConfig() {
    const tipo = $("#freq_tipo").val();
    let cron = "";
    
    switch(tipo) {
        case "minutos":
            const minIntervalo = $("#freq_intervalo").val();
            cron = `*/${minIntervalo} * * * *`;
            break;
            
        case "horas":
            const horaIntervalo = $("#freq_intervalo").val();
            const minuto = $("#freq_minuto").val() || "0";
            cron = `${minuto} */${horaIntervalo} * * *`;
            break;
            
        case "diario":
            const hora = $("#freq_hora").val() || "08:00";
            const [h, m] = hora.split(":");
            const pularDias = parseInt($("#freq_pular_dias").val()) || 0;
            if (pularDias > 0) {
                cron = `${m} ${h} */${pularDias + 1} * *`;
            } else {
                cron = `${m} ${h} * * *`;
            }
            break;
            
        case "dias_intercalados":
            const horaIntercalado = $("#freq_hora").val() || "08:00";
            const [hi, mi] = horaIntercalado.split(":");
            const diasIntervalo = $("#freq_dias_intervalo").val() || "2";
            cron = `${mi} ${hi} */${diasIntervalo} * *`;
            break;
            
        case "semanal":
            const horaS = $("#freq_hora").val() || "08:00";
            const [hs, ms] = horaS.split(":");
            const dias = [];
            for(let i = 0; i <= 6; i++) {
                if ($(`#dia_${i}`).is(":checked")) {
                    dias.push(i);
                }
            }
            cron = `${ms} ${hs} * * ${dias.join(",")}`;
            break;
            
        case "mensal":
            const horaM = $("#freq_hora").val() || "08:00";
            const [hm, mm] = horaM.split(":");
            const dia = $("#freq_dia_mes").val() || "1";
            cron = `${mm} ${hm} ${dia} * *`;
            break;
    }
    
    return cron;
}

// Atualizar preview do agendamento
function atualizarPreview() {
    let cron = "";
    
    if ($("input[name='modo_config']:checked").val() === "visual") {
        cron = gerarCronDeConfig();
    } else {
        cron = $("#cron_manual").val();
    }
    
    $("#preview_cron").text(cron || "-");
    
    // Descrição
    const descricao = descricaoCron(cron) || cronToDescricao(cron);
    $("#preview_descricao").text(descricao);
    
    // Próxima execução (simulada)
    if (cron) {
        const proxima = calcularProximaExecucao(cron);
        $("#preview_proxima").text(proxima || "-");
    } else {
        $("#preview_proxima").text("-");
    }
}

// Converter CRON para descrição legível
function cronToDescricao(cron) {
    if (!cron) return "-";
    
    const partes = cron.split(" ");
    if (partes.length !== 5) return cron;
    
    const [min, hora, dia, mes, diaSemana] = partes;
    
    if (min.startsWith("*/")) {
        return `A cada ${min.substring(2)} minutos`;
    }
    if (hora.startsWith("*/")) {
        return `A cada ${hora.substring(2)} horas`;
    }
    if (hora !== "*" && dia === "*" && mes === "*" && diaSemana === "*") {
        return `Diariamente às ${hora}:${min}`;
    }
    if (dia.startsWith("*/")) {
        const intervalo = dia.substring(2);
        return `A cada ${intervalo} dias às ${hora}:${min}`;
    }
    if (diaSemana !== "*") {
        return `Semanalmente em dias específicos às ${hora}:${min}`;
    }
    if (dia !== "*") {
        return `Mensalmente no dia ${dia} às ${hora}:${min}`;
    }
    
    return cron;
}

// Calcular próxima execução (simplificado - backend fará o cálculo real)
function calcularProximaExecucao(cron) {
    const agora = new Date();
    const proxima = new Date(agora.getTime() + 5 * 60000); // +5 min simplificado
    return proxima.toLocaleString("pt-BR");
}

// Aplicar preset CRON
function aplicarPreset(valor) {
    if (valor) {
        $("#cron_manual").val(valor);
        atualizarPreview();
    }
}

// Salvar agendamento
function salvarAgendamento() {
    const rotinaId = $("#agendamento_rotina").val();
    if (!rotinaId) {
        Swal.fire("Atenção!", "Selecione uma rotina.", "warning");
        return;
    }
    
    let cron = "";
    if ($("input[name='modo_config']:checked").val() === "visual") {
        cron = gerarCronDeConfig();
    } else {
        cron = $("#cron_manual").val();
    }
    
    if (!cron) {
        Swal.fire("Atenção!", "Configure a frequência de execução.", "warning");
        return;
    }
    
    // Determinar status ativo: preservar original ao editar, ativar ao criar novo
    let ativaStatus = 1;
    if (isEditingMode && editingRotinaId == rotinaId) {
        ativaStatus = editingOriginalAtiva ? 1 : 0;
    }
    
    const dados = {
        id_rotina: rotinaId,
        agendamento_cron: cron,
        data_inicio: $("#data_inicio").val() || null,
        data_fim: $("#data_fim").val() || null,
        datas_ignorar: $("#datas_ignorar").val() || null,
        ignorar_feriados: $("#ignorar_feriados").is(":checked") ? 1 : 0,
        max_tentativas: $("#max_tentativas").val() || 3,
        timeout: $("#timeout").val() || 300,
        notificar_falha: $("#notificar_falha").is(":checked") ? 1 : 0,
        ativa: ativaStatus
    };
    
    $.post(baseUrl + "/api/scheduler/salvar", dados, function(res) {
        if (res.sucesso) {
            Swal.fire("Sucesso!", "Agendamento configurado com sucesso!", "success");
            bootstrap.Modal.getInstance("#modalAgendamento").hide();
            carregarAgendamentos();
        } else {
            Swal.fire("Erro!", res.erro || "Erro ao salvar agendamento.", "error");
        }
    }, "json").fail(function() {
        Swal.fire("Erro!", "Erro ao comunicar com o servidor.", "error");
    });
}

</script>
SCRIPTS;

include __DIR__ . '/layouts/base.php';
?>
