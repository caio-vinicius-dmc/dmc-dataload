<?php
$pageTitle = 'Notificações';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1" style="font-weight: 700;">Notificações</h4>
        <p class="text-muted mb-0" style="font-size: 14px;">Gerencie suas notificações</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-sm" id="btnMarcarTodasLidas">
            <i class="bi bi-check-all me-1"></i>Marcar todas como lidas
        </button>
        <button class="btn btn-outline-danger btn-sm" id="btnExcluirLidas">
            <i class="bi bi-trash me-1"></i>Excluir lidas
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-3">
    <div class="card-body py-3">
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <span class="text-muted" style="font-size: 13px; font-weight: 600;">Filtrar:</span>
            <button class="btn btn-sm btn-primary notif-filter active" data-filter="todas">Todas</button>
            <button class="btn btn-sm btn-outline-secondary notif-filter" data-filter="nao_lidas">Não lidas</button>
            <button class="btn btn-sm btn-outline-secondary notif-filter" data-filter="lidas">Lidas</button>
            <div class="vr mx-2"></div>
            <button class="btn btn-sm btn-outline-danger notif-filter-tipo" data-tipo="falha">
                <i class="bi bi-exclamation-triangle me-1"></i>Falhas
            </button>
            <button class="btn btn-sm btn-outline-success notif-filter-tipo" data-tipo="sucesso">
                <i class="bi bi-check-circle me-1"></i>Sucesso
            </button>
        </div>
    </div>
</div>

<!-- Lista -->
<div class="card">
    <div class="card-body p-0" id="notifListContainer">
        <div class="text-center py-5 text-muted">
            <div class="spinner-border spinner-border-sm me-2"></div>Carregando...
        </div>
    </div>
</div>

<!-- Paginação -->
<div class="d-flex justify-content-center mt-3" id="notifPagination" style="display: none !important;"></div>

<style>
.notif-page-item {
    display: flex;
    gap: 14px;
    padding: 16px 20px;
    border-bottom: 1px solid var(--gray-100, #f1f5f9);
    transition: background .15s;
    align-items: flex-start;
}
.notif-page-item:last-child { border-bottom: none; }
.notif-page-item:hover { background: var(--gray-50, #f8fafc); }
.notif-page-item.unread { background: rgba(59,130,246,0.04); }
.notif-page-item.unread:hover { background: rgba(59,130,246,0.07); }
.notif-page-item .notif-check { flex-shrink: 0; margin-top: 2px; }
.notif-page-item .notif-check input { width: 16px; height: 16px; cursor: pointer; }
.notif-page-item .ni-icon {
    flex-shrink: 0; width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; font-size: 18px;
}
.ni-icon.falha { background: #fef2f2; color: #ef4444; }
.ni-icon.sucesso { background: #f0fdf4; color: #22c55e; }
.ni-icon.info { background: #eff6ff; color: #3b82f6; }
.notif-page-item .ni-body { flex: 1; min-width: 0; }
.ni-body .ni-title { font-weight: 600; font-size: 14px; color: var(--gray-800, #1e293b); }
.ni-body .ni-msg { font-size: 13px; color: var(--gray-500, #64748b); margin-top: 2px; }
.ni-body .ni-time { font-size: 12px; color: var(--gray-400, #94a3b8); margin-top: 4px; }
.notif-page-item .ni-actions { flex-shrink: 0; display: flex; gap: 6px; }
.ni-actions .btn { padding: 4px 8px; font-size: 12px; }
.notif-page-empty { text-align: center; padding: 60px 20px; color: var(--gray-400, #94a3b8); }
.notif-page-empty i { font-size: 48px; display: block; margin-bottom: 12px; }
.notif-filter.active { pointer-events: none; }

body.dark-mode .notif-page-item { border-color: var(--dark-border, #334155); }
body.dark-mode .notif-page-item:hover { background: rgba(255,255,255,0.03); }
body.dark-mode .notif-page-item.unread { background: rgba(59,130,246,0.08); }
body.dark-mode .ni-body .ni-title { color: var(--dark-text, #e2e8f0); }
body.dark-mode .ni-body .ni-msg { color: var(--dark-muted, #94a3b8); }
</style>

<?php
$content = ob_get_clean();
$extraScripts = <<<'SCRIPTS'
<script>
(function() {
    var page = 1, limit = 20, filter = 'todas', tipoFilter = '';

    function timeAgo(dateStr) {
        var now = new Date(), d = new Date(dateStr);
        var diff = Math.floor((now - d) / 1000);
        if (diff < 60) return 'agora';
        if (diff < 3600) return Math.floor(diff / 60) + ' min atrás';
        if (diff < 86400) return Math.floor(diff / 3600) + 'h atrás';
        if (diff < 604800) return Math.floor(diff / 86400) + 'd atrás';
        return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', {hour:'2-digit',minute:'2-digit'});
    }

    function iconHtml(tipo) {
        if (tipo.includes('falha')) return '<div class="ni-icon falha"><i class="bi bi-exclamation-triangle-fill"></i></div>';
        if (tipo.includes('sucesso')) return '<div class="ni-icon sucesso"><i class="bi bi-check-circle-fill"></i></div>';
        return '<div class="ni-icon info"><i class="bi bi-info-circle-fill"></i></div>';
    }

    function escHtml(s) { return $('<span>').text(s || '').html(); }

    function loadNotifs() {
        var url = baseUrl + '/api/notificacoes/list?limite=' + limit + '&pagina=' + page;
        if (filter === 'nao_lidas') url += '&lida=0';
        if (filter === 'lidas') url += '&lida=1';
        if (tipoFilter) url += '&tipo=' + tipoFilter;

        $('#notifListContainer').html('<div class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm me-2"></div>Carregando...</div>');
        $.getJSON(url, function(res) {
            if (!res.sucesso || !res.dados || res.dados.length === 0) {
                $('#notifListContainer').html('<div class="notif-page-empty"><i class="bi bi-bell-slash"></i>Nenhuma notificação encontrada</div>');
                $('#notifPagination').hide();
                return;
            }
            var html = '';
            res.dados.forEach(function(n) {
                var cls = n.lida ? '' : ' unread';
                html += '<div class="notif-page-item' + cls + '" data-id="' + n.id + '">'
                    + '<div class="notif-check"><input type="checkbox" class="form-check-input notif-cb" value="' + n.id + '"></div>'
                    + iconHtml(n.tipo)
                    + '<div class="ni-body">'
                    + '<div class="ni-title">' + escHtml(n.titulo) + '</div>'
                    + '<div class="ni-msg">' + escHtml(n.mensagem) + '</div>'
                    + '<div class="ni-time"><i class="bi bi-clock me-1"></i>' + timeAgo(n.created_at) + '</div>'
                    + '</div>'
                    + '<div class="ni-actions">';
                if (!n.lida) {
                    html += '<button class="btn btn-outline-primary btn-mark-read" data-id="' + n.id + '" title="Marcar como lida"><i class="bi bi-check2"></i></button>';
                }
                html += '<button class="btn btn-outline-danger btn-delete-notif" data-id="' + n.id + '" title="Excluir"><i class="bi bi-trash"></i></button>'
                    + '</div></div>';
            });
            $('#notifListContainer').html(html);

            // Paginação simples
            if (res.total && res.total > limit) {
                var pages = Math.ceil(res.total / limit);
                var pHtml = '<nav><ul class="pagination pagination-sm">';
                for (var i = 1; i <= pages; i++) {
                    pHtml += '<li class="page-item' + (i === page ? ' active' : '') + '"><a class="page-link notif-page-link" data-page="' + i + '" href="#">' + i + '</a></li>';
                }
                pHtml += '</ul></nav>';
                $('#notifPagination').html(pHtml).show().css('display', '');
            } else {
                $('#notifPagination').hide();
            }
        });
    }

    // Filtros lida/não lida
    $(document).on('click', '.notif-filter', function() {
        $('.notif-filter').removeClass('active btn-primary').addClass('btn-outline-secondary');
        $(this).removeClass('btn-outline-secondary').addClass('active btn-primary');
        filter = $(this).data('filter');
        page = 1;
        loadNotifs();
    });

    // Filtros tipo
    $(document).on('click', '.notif-filter-tipo', function() {
        var tipo = $(this).data('tipo');
        if (tipoFilter === tipo) {
            tipoFilter = '';
            $(this).removeClass('active');
        } else {
            $('.notif-filter-tipo').removeClass('active');
            tipoFilter = tipo;
            $(this).addClass('active');
        }
        page = 1;
        loadNotifs();
    });

    // Paginação
    $(document).on('click', '.notif-page-link', function(e) {
        e.preventDefault();
        page = $(this).data('page');
        loadNotifs();
    });

    // Marcar como lida
    $(document).on('click', '.btn-mark-read', function(e) {
        e.stopPropagation();
        var id = $(this).data('id');
        $.post(baseUrl + '/api/notificacoes/lida/' + id, function() { loadNotifs(); if (typeof loadNotificationBadge === 'function') loadNotificationBadge(); });
    });

    // Excluir
    $(document).on('click', '.btn-delete-notif', function(e) {
        e.stopPropagation();
        var id = $(this).data('id');
        $.post(baseUrl + '/api/notificacoes/excluir/' + id, function() { loadNotifs(); if (typeof loadNotificationBadge === 'function') loadNotificationBadge(); });
    });

    // Marcar todas como lidas
    $('#btnMarcarTodasLidas').on('click', function() {
        Swal.fire({title:'Marcar todas como lidas?',icon:'question',showCancelButton:true,confirmButtonText:'Sim',cancelButtonText:'Cancelar'}).then(function(r) {
            if (r.isConfirmed) {
                $.post(baseUrl + '/api/notificacoes/lida-todas', function() { loadNotifs(); if (typeof loadNotificationBadge === 'function') loadNotificationBadge(); Swal.fire('Pronto!','Todas marcadas como lidas.','success'); });
            }
        });
    });

    // Excluir lidas
    $('#btnExcluirLidas').on('click', function() {
        Swal.fire({title:'Excluir todas as notificações lidas?',icon:'warning',showCancelButton:true,confirmButtonColor:'#ef4444',confirmButtonText:'Excluir',cancelButtonText:'Cancelar'}).then(function(r) {
            if (r.isConfirmed) {
                $.post(baseUrl + '/api/notificacoes/excluir-lidas', function() { loadNotifs(); Swal.fire('Pronto!','Notificações lidas excluídas.','success'); });
            }
        });
    });

    $(document).ready(function() { loadNotifs(); });
})();
</script>
SCRIPTS;

include __DIR__ . '/layouts/base.php';
?>
