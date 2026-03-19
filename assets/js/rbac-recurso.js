/**
 * RBAC - Selects de Empresa/Projeto para recursos
 * Depende de: jQuery, variável global baseUrl
 */
window._rbacEmpresas = [];
window._rbacProjetos = [];
window._rbacCarregado = false;

function rbacCarregarOpcoes(callback) {
    if (window._rbacCarregado) {
        if (callback) callback();
        return;
    }
    $.getJSON(baseUrl + '/api/permissoes/empresas-usuario', function(res) {
        window._rbacEmpresas = res.dados || [];
        var sel = document.getElementById('rbac_empresas');
        if (!sel) return;
        sel.innerHTML = '';
        window._rbacEmpresas.forEach(function(e) {
            var opt = document.createElement('option');
            opt.value = e.id;
            opt.textContent = e.nome;
            sel.appendChild(opt);
        });
        // Carregar todos os projetos
        var ids = window._rbacEmpresas.map(function(e) { return e.id; }).join(',');
        if (ids) {
            $.getJSON(baseUrl + '/api/permissoes/projetos-usuario?empresas=' + ids, function(res2) {
                window._rbacProjetos = res2.dados || [];
                rbacFiltrarProjetos();
                window._rbacCarregado = true;
                if (callback) callback();
            });
        } else {
            window._rbacCarregado = true;
            if (callback) callback();
        }
    });
}

function rbacFiltrarProjetos() {
    var sel = document.getElementById('rbac_projetos');
    var empSel = document.getElementById('rbac_empresas');
    if (!sel || !empSel) return;
    
    var empresasSelecionadas = Array.from(empSel.selectedOptions).map(function(o) { return parseInt(o.value); });
    var savedValues = Array.from(sel.selectedOptions).map(function(o) { return parseInt(o.value); });
    
    sel.innerHTML = '';
    window._rbacProjetos.forEach(function(p) {
        if (empresasSelecionadas.length === 0 || empresasSelecionadas.indexOf(p.id_empresa) !== -1) {
            var opt = document.createElement('option');
            opt.value = p.id;
            opt.textContent = p.nome;
            if (savedValues.indexOf(p.id) !== -1) opt.selected = true;
            sel.appendChild(opt);
        }
    });
}

function rbacPreencherSelects(empresasIds, projetosIds) {
    var empSel = document.getElementById('rbac_empresas');
    var projSel = document.getElementById('rbac_projetos');
    if (!empSel) return;
    
    // Marcar empresas
    Array.from(empSel.options).forEach(function(o) {
        o.selected = empresasIds.indexOf(parseInt(o.value)) !== -1;
    });
    
    // Filtrar e marcar projetos
    rbacFiltrarProjetos();
    if (projSel) {
        Array.from(projSel.options).forEach(function(o) {
            o.selected = projetosIds.indexOf(parseInt(o.value)) !== -1;
        });
    }
}

function rbacLimparSelects() {
    var empSel = document.getElementById('rbac_empresas');
    var projSel = document.getElementById('rbac_projetos');
    if (empSel) Array.from(empSel.options).forEach(function(o) { o.selected = false; });
    if (projSel) Array.from(projSel.options).forEach(function(o) { o.selected = false; });
}

// Listener para filtrar projetos ao mudar empresas
$(document).ready(function() {
    $(document).on('change', '#rbac_empresas', rbacFiltrarProjetos);
    rbacCarregarOpcoes();
});
