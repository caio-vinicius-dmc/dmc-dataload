<?php
/**
 * Calendário de Agendamentos - UI MODERNIZADA
 * Visualização em modo calendário dos agendamentos de rotinas
 */

$pageTitle = 'Calendário de Agendamentos';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => BASE_URL . '/dashboard'],
    ['label' => 'Calendário', 'url' => '']
];
$isOperador = \App\Servicos\ServicoPermissao::ehOperador();

$extraStyles = <<<'STYLES'
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<style>
:root {
    --primary: #667eea;
    --secondary: #764ba2;
    --success: #10b981;
    --danger: #ef4444;
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --shadow-sm: 0 2px 8px rgba(0,0,0,0.05);
    --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
    --shadow-lg: 0 8px 32px rgba(0,0,0,0.12);
    --shadow-xl: 0 12px 48px rgba(0,0,0,0.15);
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 20px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

body {
    background: #f8f9fb;
}

/* ==== LAYOUT FIXES ==== */
.row {
    margin-left: -0.75rem;
    margin-right: -0.75rem;
}

.row > [class*='col-'] {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
}

.container-fluid {
    overflow-x: hidden;
}

/* ==== PAGE HEADER ==== */
.page-header {
    background: var(--gradient-primary);
    color: white;
    padding: 3rem 2rem;
    margin-bottom: 2.5rem;
    border-radius: 0 0 32px 32px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}

.page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
    animation: float 20s ease-in-out infinite;
}

.page-header::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -10%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: float 25s ease-in-out infinite reverse;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(30px, -30px) scale(1.1); }
}

.page-header h1 {
    font-size: clamp(1.75rem, 4vw, 2.75rem);
    font-weight: 800;
    margin-bottom: 0.75rem;
    position: relative;
    z-index: 2;
    text-shadow: 0 2px 20px rgba(0,0,0,0.2);
    letter-spacing: -0.5px;
}

.page-header p {
    font-size: clamp(1rem, 2vw, 1.15rem);
    opacity: 0.95;
    position: relative;
    z-index: 2;
    font-weight: 400;
}

.header-actions {
    position: relative;
    z-index: 2;
}

.header-actions .btn {
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 2px solid rgba(255,255,255,0.4);
    color: white;
    padding: 0.75rem 1.75rem;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 0.95rem;
    transition: var(--transition);
    white-space: nowrap;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.header-actions .btn:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-color: rgba(255,255,255,0.6);
}

/* ==== FILTROS TOP BAR ==== */
.filtros-top-bar {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
    align-items: stretch;
}

.filtros-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: 1.25rem 1.5rem;
    box-shadow: var(--shadow-md);
    border: 1px solid rgba(0,0,0,0.04);
    transition: var(--transition);
    position: relative;
}

.filtros-card:hover {
    box-shadow: var(--shadow-lg);
}

.filtros-card-main {
    flex: 1;
    min-width: 0;
}

.filtros-card-tips {
    flex: 0 0 280px;
}

.filtros-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.filtros-header h5 {
    font-size: 1.05rem;
    font-weight: 700;
    color: #1a202c;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filtros-header h5 i {
    color: var(--primary);
    font-size: 1.1rem;
}

/* ==== Multi-select dropdown ==== */
.select2-container--default .select2-selection--multiple {
    border: 2px solid #e2e8f0;
    border-radius: var(--radius-md);
    padding: 0.5rem 0.65rem;
    min-height: 52px;
    transition: var(--transition);
    background: linear-gradient(180deg, #ffffff 0%, #fafbff 100%);
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.3rem;
}

.select2-container--default .select2-selection--multiple:focus-within,
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.12), var(--shadow-sm);
    background: white;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background: var(--gradient-primary);
    color: white;
    border: none;
    border-radius: 20px;
    padding: 0.3rem 0.85rem;
    font-size: 0.82rem;
    font-weight: 600;
    letter-spacing: 0.2px;
    box-shadow: 0 2px 6px rgba(102, 126, 234, 0.3);
    transition: var(--transition);
    margin: 2px 0;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: rgba(255,255,255,0.75);
    margin-right: 0.5rem;
    font-size: 1rem;
    font-weight: 700;
    transition: var(--transition);
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: white;
    background: transparent;
}

.select2-container--default .select2-search--inline .select2-search__field {
    margin-top: 0;
    padding: 0.2rem 0.4rem;
    font-size: 0.92rem;
    color: #4a5568;
}

.select2-container--default .select2-search--inline .select2-search__field::placeholder {
    color: #a0aec0;
    font-style: italic;
}

.select2-dropdown {
    border: 2px solid #e2e8f0;
    border-radius: var(--radius-md);
    box-shadow: 0 12px 36px rgba(0,0,0,0.12);
    overflow: hidden;
    margin-top: 4px;
}

.select2-results__option {
    padding: 0.65rem 1rem;
    font-size: 0.9rem;
    font-weight: 500;
    color: #374151;
    transition: background 0.15s ease, color 0.15s ease;
    border-bottom: 1px solid #f3f4f6;
}

.select2-results__option:last-child {
    border-bottom: none;
}

.select2-results__option--highlighted[aria-selected] {
    background: var(--gradient-primary) !important;
    color: white !important;
}

.select2-results__option[aria-selected=true] {
    background: #f0f4ff;
    color: var(--primary);
    font-weight: 600;
    position: relative;
}

.select2-results__option[aria-selected=true]::after {
    content: '\F272';
    font-family: 'bootstrap-icons';
    position: absolute;
    right: 1rem;
    font-size: 0.85rem;
    color: var(--primary);
}

.filtros-actions {
    display: flex;
    gap: 0.4rem;
    align-items: center;
}

.filtros-actions .btn {
    padding: 0.45rem 0.9rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    transition: var(--transition);
    border-width: 2px;
    letter-spacing: 0.2px;
}

.filtros-actions .btn-outline-primary {
    color: var(--primary);
    border-color: var(--primary);
}

.filtros-actions .btn-outline-primary:hover {
    background: var(--gradient-primary);
    color: white;
    border-color: transparent;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.filtros-actions .btn-outline-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.filtros-empty-msg {
    text-align: center;
    padding: 2rem;
    color: #9ca3af;
    font-size: 0.95rem;
}



/* ==== CALENDÁRIO ==== */
.calendario-wrapper {
    background: white;
    border-radius: var(--radius-lg);
    padding: 2rem;
    box-shadow: var(--shadow-md);
    border: 1px solid rgba(0,0,0,0.04);
    transition: var(--transition);
    position: relative;
    z-index: 1;
    min-height: 600px;
}

.calendario-wrapper:hover {
    box-shadow: var(--shadow-lg);
}

.fc .fc-toolbar-title {
    font-size: clamp(1.5rem, 3vw, 2rem);
    font-weight: 800;
    color: #1a202c;
    letter-spacing: -0.5px;
}

.fc .fc-button {
    background: var(--gradient-primary);
    border: none;
    border-radius: var(--radius-md);
    padding: 0.7rem 1.4rem;
    font-weight: 600;
    text-transform: none;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    transition: var(--transition);
}

.fc .fc-button:hover:not(:disabled) {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    transform: translateY(-2px) scale(1.02);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.fc .fc-button:disabled {
    background: #e5e7eb;
    opacity: 0.5;
}

.fc-day:hover {
    background: rgba(102, 126, 234, 0.05);
}

.fc-day-today {
    background: rgba(102, 126, 234, 0.08) !important;
}

.fc-day-today .fc-daygrid-day-number {
    background: var(--gradient-primary);
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.fc-event {
    cursor: pointer;
    border-radius: var(--radius-sm);
    padding: 5px 10px;
    font-size: 0.875rem;
    font-weight: 600;
    border: none;
    margin: 3px 0;
    transition: var(--transition);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
}

.fc-event:hover {
    transform: scale(1.05) translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    z-index: 100;
}

.fc-daygrid-day-number {
    font-weight: 600;
    padding: 10px;
    color: #374151;
}

.fc-col-header-cell {
    background: linear-gradient(135deg, #fafbff 0%, #f0f4ff 100%);
    font-weight: 700;
    color: #4b5563;
    padding: 1.25rem;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 1px;
    border-bottom: 3px solid var(--primary);
}

/* ==== LEGENDA ==== */
.legenda-card {
    background: linear-gradient(135deg, #fafbff 0%, #f0f4ff 100%);
    border: 2px solid rgba(102, 126, 234, 0.15);
}

.legenda-card h6 {
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.legenda-item {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 0.65rem 0;
    color: #6b7280;
    font-size: 0.9rem;
}

.legenda-item i {
    color: var(--primary);
    font-size: 1.2rem;
}

/* ==== LOADING ==== */
.loading-state {
    text-align: center;
    padding: 4rem 1rem;
}

.loading-spinner {
    width: 56px;
    height: 56px;
    border: 5px solid rgba(102, 126, 234, 0.2);
    border-top-color: var(--primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1.5rem;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* ==== ANIMATIONS ==== */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}



/* ==== RESPONSIVE ==== */
@media (max-width: 991px) {
    .filtros-top-bar {
        flex-direction: column;
    }
    
    .filtros-card-tips {
        flex: auto;
    }
    
    .page-header {
        padding: 2rem 1.5rem;
    }
    
    .header-actions {
        margin-top: 1.25rem;
    }
}

@media (max-width: 767px) {
    .page-header {
        padding: 1.75rem 1.25rem;
        border-radius: 0 0 20px 20px;
    }
    
    .header-actions .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .fc .fc-toolbar {
        flex-direction: column;
        gap: 1rem;
    }
    
    .fc .fc-toolbar-chunk {
        width: 100%;
        justify-content: center;
    }
    
    .fc .fc-button {
        padding: 0.6rem 1.1rem;
        font-size: 0.85rem;
    }
    
    .calendario-wrapper {
        padding: 1.25rem;
    }
}

@media (max-width: 575px) {
    .filtros-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .fc-event {
        font-size: 0.8rem;
        padding: 4px 8px;
    }
}
</style>
STYLES;

$extraStyles .= '<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">';

$extraScripts = <<<'SCRIPTS'
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/pt-br.global.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
let calendar;
let rotinasData = [];
let rotinaCores = {};
let currentEvents = [];

const cores = [
    '#667eea', '#f093fb', '#4facfe', '#43e97b', '#fa709a',
    '#30cfd0', '#a8edea', '#ff6b6b', '#feca57', '#48dbfb'
];

$(document).ready(function() {
    carregarRotinas();
    inicializarCalendario();
});

function carregarRotinas() {
    $.ajax({
        url: baseUrl + '/rotinas/list',
        method: 'GET',
        success: function(response) {
            if (response.sucesso && response.dados) {
                rotinasData = response.dados.filter(r => r.agendamento_cron);
            } else if (response.sucesso && response.data) {
                rotinasData = response.data.filter(r => r.agendamento_cron);
            }
            $.ajax({
                url: baseUrl + '/pipelines/list',
                method: 'GET',
                success: function(pipRes) {
                    if (pipRes.sucesso && pipRes.data) {
                        pipRes.data.filter(p => p.trigger_tipo === 'cron' && p.agendamento_cron).forEach(p => {
                            rotinasData.push({
                                id: 'pip_' + p.id,
                                nome: '[Pipeline] ' + p.nome,
                                agendamento_cron: p.agendamento_cron,
                                tipo: 'pipeline'
                            });
                        });
                    }
                    renderizarFiltros();
                },
                error: function() {
                    renderizarFiltros();
                }
            });
        },
        error: function() {
            $('#filtrosRotinas').html('<div class="alert alert-danger mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Erro ao carregar rotinas</div>');
        }
    });
}

function renderizarFiltros() {
    // Build select options
    const $select = $('<select id="selectRotinas" multiple="multiple" style="width:100%"></select>');
    rotinasData.forEach((rotina, index) => {
        rotinaCores[rotina.id.toString()] = cores[index % cores.length];
        $select.append($('<option>', {
            value: rotina.id,
            text: rotina.nome + ' (' + rotina.agendamento_cron + ')'
        }));
    });

    $('#filtrosRotinas').empty().append($select);

    $select.select2({
        placeholder: 'Selecione rotinas/pipelines...',
        allowClear: true,
        closeOnSelect: false,
        width: '100%'
    }).on('change', function() {
        carregarEventos();
    });
}

function inicializarCalendario() {
    const calendarEl = document.getElementById('calendario');
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'pt-br',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,listWeek'
        },
        buttonText: {
            today: 'Hoje',
            month: 'Mês',
            week: 'Semana',
            list: 'Lista'
        },
        height: 'auto',
        events: [],
        eventClick: function(info) {
            mostrarDetalhesEvento(info.event);
        },
        datesSet: function() {
            carregarEventos();
        }
    });
    
    calendar.render();
}

function carregarEventos() {
    if (!calendar) return;
    
    const view = calendar.view;
    const inicio = view.activeStart.toISOString().split('T')[0];
    const fim = view.activeEnd.toISOString().split('T')[0];
    
    const selecionadas = $('#selectRotinas').val() || [];
    
    if (selecionadas.length === 0) {
        calendar.getEventSources().forEach(s => s.remove());
        currentEvents = [];
        return;
    }
    
    $.ajax({
        url: baseUrl + '/api/calendario/eventos',
        method: 'GET',
        data: {
            inicio: inicio,
            fim: fim,
            rotinas: selecionadas.join(',')
        },
        success: function(response) {
            if (response.sucesso && response.eventos) {
                currentEvents = response.eventos;
                renderizarEventos();
            } else {
                console.error('Erro ao carregar eventos:', response.erro || 'Resposta inválida');
                calendar.getEventSources().forEach(s => s.remove());
            }
        },
        error: function() {
            console.error('Erro de rede ao carregar eventos');
            calendar.getEventSources().forEach(s => s.remove());
        }
    });
}

function renderizarEventos() {
    if (!calendar) return;
    
    calendar.getEventSources().forEach(s => s.remove());
    
    const selecionadas = ($('#selectRotinas').val() || []).map(String);
    
    const eventosFiltrados = currentEvents.filter(evento => {
        const rid = evento.rotina_id ? evento.rotina_id.toString() : '';
        return selecionadas.includes(rid);
    });
    
    const eventosFormatados = eventosFiltrados.map(evento => {
        const rid = evento.rotina_id ? evento.rotina_id.toString() : '';
        const cor = rotinaCores[rid] || evento.cor || '#6c757d';
        return {
            id: evento.id,
            title: evento.titulo,
            start: evento.data,
            backgroundColor: cor,
            borderColor: cor,
            extendedProps: {
                rotina_id: evento.rotina_id,
                cron: evento.cron,
                descricao: evento.descricao,
                tipo: evento.tipo || 'rotina',
                status: evento.status || null
            }
        };
    });
    
    calendar.addEventSource(eventosFormatados);
}

function mostrarDetalhesEvento(event) {
    const rotina = rotinasData.find(r => r.id == event.extendedProps.rotina_id);
    
    Swal.fire({
        title: event.title,
        html: `
            <div class="text-start">
                <p><strong>Data:</strong> ${new Date(event.start).toLocaleString('pt-BR')}</p>
                <p><strong>Rotina:</strong> ${rotina ? rotina.nome : 'N/A'}</p>
                <p><strong>CRON:</strong> <code>${event.extendedProps.cron}</code></p>
                ${event.extendedProps.descricao ? `<p><strong>Descrição:</strong> ${event.extendedProps.descricao}</p>` : ''}
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Fechar',
        confirmButtonColor: '#667eea'
    });
}

function selecionarTodas() {
    const $sel = $('#selectRotinas');
    $sel.find('option').prop('selected', true);
    $sel.trigger('change');
}

function deselecionarTodas() {
    const $sel = $('#selectRotinas');
    $sel.val(null).trigger('change');
}
</script>
SCRIPTS;

ob_start();
?>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h1>📅 Calendário de Agendamentos</h1>
            <p class="mb-0">Visualize quando suas rotinas serão executadas de forma intuitiva</p>
        </div>
        <?php if (!$isOperador): ?>
        <div class="col-lg-4">
            <div class="header-actions d-flex flex-column flex-lg-row gap-2 mt-3 mt-lg-0">
                <a href="<?= BASE_URL ?>/scheduler" class="btn">
                    <i class="bi bi-clock me-2"></i>Gerenciar
                </a>
                <a href="<?= BASE_URL ?>/rotinas" class="btn">
                    <i class="bi bi-list-task me-2"></i>Rotinas
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <!-- Filtros + Dicas no topo -->
    <div class="col-12">
        <div class="filtros-top-bar">
            <div class="filtros-card filtros-card-main">
                <div class="filtros-header">
                    <h5>
                        <i class="bi bi-funnel-fill"></i>
                        Filtros de Rotinas
                    </h5>
                    <div class="filtros-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="selecionarTodas()" title="Selecionar Todas">
                            <i class="bi bi-check-all me-1"></i>Todas
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="deselecionarTodas()" title="Limpar Seleção">
                            <i class="bi bi-x-lg me-1"></i>Limpar
                        </button>
                    </div>
                </div>
                <p class="text-muted mb-2" style="font-size:0.85rem; margin-top:-0.5rem;">
                    Selecione as rotinas e pipelines para visualizar seus agendamentos no calendário
                </p>
                
                <div id="filtrosRotinas">
                    <div class="loading-state" style="padding: 1rem;">
                        <div class="loading-spinner" style="width:32px;height:32px;border-width:3px;margin-bottom:0.5rem;"></div>
                        <p class="text-muted mb-0" style="font-size:0.9rem;">Carregando rotinas...</p>
                    </div>
                </div>
            </div>
            
            <div class="filtros-card filtros-card-tips legenda-card">
                <h6 style="margin-bottom:0.75rem;">
                    <i class="bi bi-lightbulb-fill"></i>
                    Dicas
                </h6>
                <div class="legenda-item" style="padding:0.35rem 0;">
                    <i class="bi bi-hand-index-thumb"></i>
                    <span>Clique nos eventos para detalhes</span>
                </div>
                <div class="legenda-item" style="padding:0.35rem 0;">
                    <i class="bi bi-funnel"></i>
                    <span>Selecione rotinas no dropdown</span>
                </div>
                <div class="legenda-item" style="padding:0.35rem 0;">
                    <i class="bi bi-calendar-week"></i>
                    <span>Alterne entre mês, semana e lista</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Calendário largura total -->
    <div class="col-12">
        <div class="calendario-wrapper">
            <div id="calendario"></div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/layouts/base.php';
