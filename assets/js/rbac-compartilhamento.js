/**
 * rbac-compartilhamento.js
 * Gerencia UI de compartilhamento de recursos.
 * Depende de: jQuery, Bootstrap 5, variável global baseUrl e csrfToken.
 */
(function () {
    'use strict';

    var _usuarios = null;

    /**
     * Carrega lista de usuários disponíveis para compartilhamento.
     */
    function carregarUsuarios(callback) {
        if (_usuarios) { callback(_usuarios); return; }
        $.getJSON(baseUrl + '/admin/usuarios/list', function (res) {
            var lista = res.dados || res.data || res || [];
            if (Array.isArray(lista)) {
                _usuarios = lista;
            } else if (lista.registros) {
                _usuarios = lista.registros;
            } else {
                _usuarios = [];
            }
            callback(_usuarios);
        }).fail(function () { _usuarios = []; callback([]); });
    }

    /**
     * Abre o modal de compartilhamento para um recurso específico.
     */
    window.abrirModalCompartilhamento = function (tipoRecurso, idRecurso) {
        var modal = document.getElementById('modalCompartilhamento');
        if (!modal) return;

        $('#comp_tipo_recurso').val(tipoRecurso);
        $('#comp_id_recurso').val(idRecurso);

        // Preencher select de usuários
        carregarUsuarios(function (usuarios) {
            var sel = $('#comp_usuario_destino');
            sel.empty().append('<option value="">Selecione um usuário...</option>');
            usuarios.forEach(function (u) {
                var id = u.id || u.id_usuario;
                var nome = u.nome_usuario || u.nome || u.usuario;
                sel.append('<option value="' + id + '">' + nome + '</option>');
            });
        });

        // Carregar compartilhamentos existentes
        carregarCompartilhamentos(tipoRecurso, idRecurso);

        var bsModal = bootstrap.Modal.getOrCreateInstance(modal);
        bsModal.show();
    };

    /**
     * Carrega e renderiza os compartilhamentos existentes.
     */
    function carregarCompartilhamentos(tipoRecurso, idRecurso) {
        var url = baseUrl + '/api/compartilhamentos/listar?tipo_recurso=' +
            encodeURIComponent(tipoRecurso) + '&id_recurso=' + (idRecurso || '');
        $.getJSON(url, function (res) {
            var lista = $('#comp_lista');
            lista.empty();
            var dados = res.dados || [];
            if (dados.length === 0) {
                lista.html('<p class="text-muted small">Nenhum compartilhamento.</p>');
                return;
            }
            dados.forEach(function (c) {
                var badge = c.permissao === 'editar'
                    ? '<span class="badge bg-warning text-dark">editar</span>'
                    : '<span class="badge bg-info">ver</span>';
                lista.append(
                    '<div class="list-group-item d-flex justify-content-between align-items-center">' +
                    '  <span><i class="bi bi-person me-1"></i>' +
                         (c.usuario_destino_nome || 'Usuário #' + c.id_usuario_destino) +
                         ' ' + badge + '</span>' +
                    '  <button class="btn btn-sm btn-outline-danger btn-remover-comp" ' +
                    '    data-destino="' + c.id_usuario_destino + '">' +
                    '    <i class="bi bi-trash"></i>' +
                    '  </button>' +
                    '</div>'
                );
            });
        });
    }

    // Adicionar compartilhamento
    $(document).on('click', '#btnAdicionarCompartilhamento', function () {
        var tipo = $('#comp_tipo_recurso').val();
        var idRecurso = $('#comp_id_recurso').val();
        var destino = $('#comp_usuario_destino').val();
        var permissao = $('#comp_permissao').val();
        if (!destino) { alert('Selecione um usuário.'); return; }

        $.post(baseUrl + '/api/compartilhamentos/salvar', {
            _csrf_token: csrfToken,
            tipo_recurso: tipo,
            id_recurso: idRecurso || '',
            id_usuario_destino: destino,
            permissao: permissao
        }, function (res) {
            if (res.sucesso) {
                carregarCompartilhamentos(tipo, idRecurso || null);
                $('#comp_usuario_destino').val('');
            } else {
                alert(res.erro || 'Erro ao compartilhar');
            }
        }, 'json');
    });

    // Remover compartilhamento
    $(document).on('click', '.btn-remover-comp', function () {
        if (!confirm('Remover compartilhamento?')) return;
        var tipo = $('#comp_tipo_recurso').val();
        var idRecurso = $('#comp_id_recurso').val();
        var destino = $(this).data('destino');

        $.post(baseUrl + '/api/compartilhamentos/remover', {
            _csrf_token: csrfToken,
            tipo_recurso: tipo,
            id_recurso: idRecurso || '',
            id_usuario_destino: destino
        }, function (res) {
            if (res.sucesso) {
                carregarCompartilhamentos(tipo, idRecurso || null);
            } else {
                alert('Erro ao remover');
            }
        }, 'json');
    });
})();
