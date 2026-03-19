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

/* ==== FILTROS SIDEBAR ==== */
.filtros-sidebar {
    position: sticky;
    top: 20px;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    z-index: 10;
    max-height: calc(100vh - 100px);
    overflow-y: auto;
    overflow-x: hidden;
}

.filtros-sidebar::-webkit-scrollbar {
    width: 6px;
}

.filtros-sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.filtros-sidebar::-webkit-scrollbar-thumb {
    background: rgba(102, 126, 234, 0.3);
    border-radius: 3px;
}

.filtros-sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(102, 126, 234, 0.5);
}

.filtros-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: 1.75rem;
    box-shadow: var(--shadow-md);
    border: 1px solid rgba(0,0,0,0.04);
    transition: var(--transition);
    position: relative;
}

.filtros-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--gradient-primary);
    transform: scaleY(0);
    transition: transform 0.4s ease;
}

.filtros-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.filtros-card:hover::before {
    transform: scaleY(1);
}

.filtros-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f0f2f5;
}

.filtros-header h5 {
    font-size: 1.15rem;
    font-weight: 700;
    color: #1a202c;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.65rem;
}

.filtros-header h5 i {
    color: var(--primary);
    font-size: 1.25rem;
}

.filtros-actions {
    display: flex;
    gap: 0.5rem;
}

.filtros-actions .btn {
    padding: 0.5rem 0.9rem;
    border-radius: var(--radius-sm);
    font-size: 0.875rem;
    font-weight: 600;
    transition: var(--transition);
    border-width: 2px;
}

.filtros-actions .btn:hover {
    transform: scale(1.08);
}

/* ==== ROTINA CHECKBOXES ==== */
.rotina-checkbox {
    padding: 1rem 1.25rem;
    border: 2px solid #e5e7eb;
    border-radius: var(--radius-md);
    margin-bottom: 0.875rem;
    transition: var(--transition);
    cursor: pointer;
    background: white;
    position: relative;
}

.rotina-checkbox:hover {
    background: linear-gradient(135deg, #fafbff 0%, #f3f5ff 100%);
    border-color: var(--primary);
    transform: translateX(6px);
    box-shadow: var(--shadow-sm);
}

.rotina-checkbox input:checked ~ label {
    font-weight: 700;
    color: var(--primary);
}

.rotina-checkbox .form-check-label {
    cursor: pointer;
    margin: 0;
    width: 100%;
    font-size: 0.95rem;
}

.evento-badge {
    display: inline-block;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    margin-right: 0.85rem;
    box-shadow: 0 0 0 3px currentColor;
    transition: var(--transition);
    position: relative;
}

.evento-badge::after {
    content: '';
    position: absolute;
    inset: -3px;
    border-radius: 50%;
    background: currentColor;
    opacity: 0;
}

.rotina-checkbox:hover .evento-badge {
    transform: scale(1.3);
}

.rotina-checkbox:hover .evento-badge::after {
    opacity: 0.2;
    animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
}

@keyframes ping {
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}

.rotina-cron {
    font-size: 0.75rem;
    font-family: 'Courier New', Consolas, monospace;
    background: #f3f4f6;
    padding: 0.3rem 0.6rem;
    border-radius: 6px;
    color: #6b7280;
    font-weight: 500;
    margin-top: 0.4rem;
    display: inline-block;
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

.rotina-checkbox {
    animation: fadeInUp 0.4s cubic-bezier(0.4, 0, 0.2, 1) backwards;
}

.rotina-checkbox:nth-child(1) { animation-delay: 0.05s; }
.rotina-checkbox:nth-child(2) { animation-delay: 0.1s; }
.rotina-checkbox:nth-child(3) { animation-delay: 0.15s; }
.rotina-checkbox:nth-child(4) { animation-delay: 0.2s; }
.rotina-checkbox:nth-child(5) { animation-delay: 0.25s; }

/* ==== RESPONSIVE ==== */
@media (max-width: 991px) {
    .filtros-sidebar {
        position: relative;
        top: 0;
        margin-bottom: 2rem;
        max-height: none;
        overflow-y: visible;
    }
    
    .page-header {
        padding: 2rem 1.5rem;
    }
    
    .header-actions {
        margin-top: 1.25rem;
    }
    
    .row {
        margin-left: 0;
        margin-right: 0;
    }
    
    .row > [class*='col-'] {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
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
        gap: 1rem;
    }
    
    .fc-event {
        font-size: 0.8rem;
        padding: 4px 8px;
    }
}
</style>
STYLES;

$extraScripts = <<<'SCRIPTS'
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/pt-br.global.js"></script>
<script>
let calendar;
let rotinasData = [];
let currentEvents = [];

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
            // Buscar pipelines com cron e adicionar ao filtro
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
                    carregarEventos();
                },
                error: function() {
                    renderizarFiltros();
                    carregarEventos();
                }
            });
        },
        error: function() {
            $('#filtrosRotinas').html(`
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Erro ao carregar rotinas
                </div>
            `);
        }
    });
}

function renderizarFiltros() {
    const cores = [
        '#667eea', '#f093fb', '#4facfe', '#43e97b', '#fa709a',
        '#30cfd0', '#a8edea', '#ff6b6b', '#feca57', '#48dbfb'
    ];
    
    let html = '';
    rotinasData.forEach((rotina, index) => {
        const cor = cores[index % cores.length];
        html += `
            <div class="rotina-checkbox">
                <div class="form-check">
                    <input class="form-check-input" 
                           type="checkbox" 
                           value="${rotina.id}" 
                           id="rotina${rotina.id}"
                           data-color="${cor}"
                           checked
                           onchange="filtrarEventos()">
                    <label class="form-check-label" for="rotina${rotina.id}">
                        <span class="evento-badge" style="background-color: ${cor};"></span>
                        <strong>${rotina.nome}</strong>
                        <br>
                        <small class="rotina-cron">${rotina.agendamento_cron}</small>
                    </label>
                </div>
            </div>
        `;
    });
    
    $('#filtrosRotinas').html(html);
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
    
    const rotinasSelecionadas = [];
    $('.rotina-checkbox input:checked').each(function() {
        rotinasSelecionadas.push($(this).val());
    });
    
    if (rotinasSelecionadas.length === 0) {
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
            rotinas: rotinasSelecionadas.join(',')
        },
        success: function(response) {
            if (response.sucesso && response.eventos) {
                currentEvents = response.eventos;
                filtrarEventos();
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

function filtrarEventos() {
    if (!calendar) return;
    
    // Remover todas as fontes de eventos (não apenas events)
    calendar.getEventSources().forEach(s => s.remove());
    
    const rotinasSelecionadas = [];
    const coresRotinas = {};
    
    $('.rotina-checkbox input').each(function() {
        const rotinaId = $(this).val();
        const cor = $(this).data('color');
        coresRotinas[rotinaId] = cor;
        
        if ($(this).is(':checked')) {
            rotinasSelecionadas.push(rotinaId);
        }
    });
    
    const eventosFiltrados = currentEvents.filter(evento => {
        const rid = evento.rotina_id ? evento.rotina_id.toString() : '';
        return rotinasSelecionadas.includes(rid);
    });
    
    const eventosFormatados = eventosFiltrados.map(evento => {
        const rid = evento.rotina_id ? evento.rotina_id.toString() : '';
        return {
            id: evento.id,
            title: evento.titulo,
            start: evento.data,
            backgroundColor: coresRotinas[rid] || evento.cor || '#6c757d',
            borderColor: coresRotinas[rid] || evento.cor || '#6c757d',
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
    $('.rotina-checkbox input').prop('checked', true);
    filtrarEventos();
}

function deselecionarTodas() {
    $('.rotina-checkbox input').prop('checked', false);
    filtrarEventos();
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
    </div>
</div>

<div class="row">
    <!-- Filtros -->
    <div class="col-lg-3 col-md-4">
        <div class="filtros-sidebar">
            <div class="filtros-card">
                <div class="filtros-header">
                    <h5>
                        <i class="bi bi-funnel-fill"></i>
                        Filtros
                    </h5>
                    <div class="filtros-actions">
                        <button class="btn btn-sm btn-outline-primary" onclick="selecionarTodas()" title="Selecionar Todas">
                            <i class="bi bi-check-all"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="deselecionarTodas()" title="Limpar">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                
                <div id="filtrosRotinas">
                    <div class="loading-state">
                        <div class="loading-spinner"></div>
                        <p class="text-muted mb-0">Carregando rotinas...</p>
                    </div>
                </div>
            </div>
            
            <div class="filtros-card legenda-card">
                <h6>
                    <i class="bi bi-lightbulb-fill"></i>
                    Dicas
                </h6>
                <div class="legenda-item">
                    <i class="bi bi-hand-index-thumb"></i>
                    <span>Clique nos eventos para detalhes</span>
                </div>
                <div class="legenda-item">
                    <i class="bi bi-toggles"></i>
                    <span>Use filtros para ocultar rotinas</span>
                </div>
                <div class="legenda-item">
                    <i class="bi bi-palette"></i>
                    <span>Cores representam rotinas diferentes</span>
                </div>
                <div class="legenda-item">
                    <i class="bi bi-calendar-week"></i>
                    <span>Alterne entre mês, semana e lista</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Calendário -->
    <div class="col-lg-9 col-md-8">
        <div class="calendario-wrapper">
            <div id="calendario"></div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/layouts/base.php';
