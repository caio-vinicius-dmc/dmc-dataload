
let apis = [];

// Carregar APIs
function carregarApis() {
    $.getJSON(baseUrl + '/api/apis-externas/list', function(res) {
        if (res.sucesso) {
            apis = res.dados || [];
            renderizarApis(apis);
            atualizarEstatisticas();
        }
    }).fail(function() {
        $('#listaApis').html('<div class="col-12 text-center py-5 text-danger">Erro ao carregar APIs</div>');
    });
}

// Renderizar APIs
function renderizarApis(lista) {
    const container = $('#listaApis');
    
    if (!lista || lista.length === 0) {
        container.html('<div class="col-12 text-center py-5"><p class="text-muted">Nenhuma API cadastrada</p><button class="btn btn-primary" onclick="abrirModalApi()"><i class="bi bi-plus-lg me-2"></i>Cadastrar Primeira API</button></div>');
        return;
    }
    
    let html = '';
    lista.forEach(api => {
        const statusClass = api.ativo ? 'active' : 'inactive';
        const badgeClass = api.ativo ? 'bg-success' : 'bg-secondary';
        
        html += \`
        <div class="col-md-6 col-xl-4">
            <div class="card api-card \${statusClass} h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="mb-0">\${api.nome}</h6>
                        <span class="method-badge method-\${api.metodo}">\${api.metodo}</span>
                    </div>
                    <p class="text-muted small text-truncate mb-2" title="\${api.url}">\${api.url}</p>
                    <div class="d-flex gap-2 mb-3">
                        <span class="auth-badge">\${api.tipo_autenticacao || 'none'}</span>
                        <span class="badge \${badgeClass}">\${api.ativo ? 'Ativa' : 'Inativa'}</span>
                    </div>
                    <div class="btn-group w-100 btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="testarApi(\${api.id_api})" title="Testar">
                            <i class="bi bi-play-circle"></i>
                        </button>
                        <button class="btn btn-outline-warning" onclick="editarApi(\${api.id_api})" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <a href="\${baseUrl}/eventos-api?api=\${api.id_api}" class="btn btn-outline-info" title="Eventos">
                            <i class="bi bi-bell"></i>
                        </a>
                        <button class="btn btn-outline-danger" onclick="excluirApi(\${api.id_api})" title="Excluir">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        \`;
    });
    
    container.html(html);
}

// Atualizar estatísticas
function atualizarEstatisticas() {
    const total = apis.length;
    const ativas = apis.filter(a => a.ativo).length;
    
    $.getJSON(baseUrl + '/api/eventos-api/list', function(res) {
        const eventos = (res.sucesso && res.dados) ? res.dados.length : 0;
        
        $('#totalApis').text(total);
        $('#apisAtivas').text(ativas);
        $('#totalEventos').text(eventos);
        $('#ultimaExecucao').text('0h');
    });
}

// Abrir modal API
function abrirModalApi(id = null) {
    if (id) {
        const api = apis.find(a => a.id_api === id);
        if (api) {
            $('#modalApiLabel').text('Editar API');
            $('#apiId').val(api.id_api);
            $('#apiNome').val(api.nome);
            $('#apiDescricao').val(api.descricao);
            $('#apiUrl').val(api.url);
            $('#apiMetodo').val(api.metodo);
            $('#apiTipoAuth').val(api.tipo_autenticacao || 'none');
            $('#apiIntervalo').val(api.intervalo_verificacao);
            $('#apiAtivo').prop('checked', api.ativo);
        }
    } else {
        $('#modalApiLabel').text('Nova API');
        $('#formApi')[0].reset();
        $('#apiId').val('');
        $('#apiAtivo').prop('checked', true);
    }
    new bootstrap.Modal('#modalApi').show();
}

// Salvar API
function salvarApi() {
    const data = {
        id_api: $('#apiId').val() || null,
        nome: $('#apiNome').val(),
        descricao: $('#apiDescricao').val(),
        url: $('#apiUrl').val(),
        metodo: $('#apiMetodo').val(),
        tipo_autenticacao: $('#apiTipoAuth').val(),
        credenciais: $('#apiAuthConfig').val(),
        intervalo_verificacao: parseInt($('#apiIntervalo').val()),
        ativo: $('#apiAtivo').is(':checked')
    };
    
    const url = data.id_api ? baseUrl + '/api/apis-externas/update/' + data.id_api : baseUrl + '/api/apis-externas/create';
    
    $.ajax({
        url: url,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function(res) {
            if (res.sucesso) {
                alert('API salva com sucesso!');
                bootstrap.Modal.getInstance('#modalApi').hide();
                carregarApis();
            } else {
                alert('Erro: ' + (res.erro || 'Erro desconhecido'));
            }
        },
        error: function() {
            alert('Erro ao salvar API');
        }
    });
}

// Editar API
function editarApi(id) {
    abrirModalApi(id);
}

// Excluir API
function excluirApi(id) {
    if (!confirm('Deseja realmente excluir esta API?')) return;
    
    $.post(baseUrl + '/api/apis-externas/delete/' + id, function(res) {
        if (res.sucesso) {
            alert('API excluída!');
            carregarApis();
        } else {
            alert('Erro: ' + (res.erro || 'Erro desconhecido'));
        }
    });
}

// Testar API
function testarApi(id) {
    alert('Funcionalidade de teste em desenvolvimento');
}

// Mostrar campos auth
$('#apiTipoAuth').on('change', function() {
    if (this.value === 'none') {
        $('#authFields').hide();
    } else {
        $('#authFields').show();
    }
});

// Inicializar
$(document).ready(function() {
    carregarApis();
});

