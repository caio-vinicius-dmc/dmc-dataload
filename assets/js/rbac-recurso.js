/**
 * RBAC - Checkbox Dropdowns de Empresa/Projeto para recursos
 * Depende de: jQuery, variável global baseUrl
 */
window._rbacEmpresas = [];
window._rbacProjetos = [];
window._rbacCarregado = false;

// ==================== Checkbox Dropdown Engine ====================

function rbacToggleDropdown(trigger) {
    var wasActive = trigger.classList.contains('active');
    // Fechar todos os dropdowns abertos
    document.querySelectorAll('.rbac-dropdown-trigger.active').forEach(function(t) {
        t.classList.remove('active');
    });
    if (!wasActive) {
        trigger.classList.add('active');
        var searchInput = trigger.nextElementSibling.querySelector('.rbac-search-input');
        if (searchInput) setTimeout(function() { searchInput.focus(); }, 50);
    }
}

function rbacRenderOptions(containerId, items, nameAttr, selectedIds) {
    var list = document.getElementById(containerId);
    if (!list) return;
    list.innerHTML = '';
    var emptyMsg = list.parentElement.querySelector('.rbac-empty-msg');
    
    if (items.length === 0) {
        if (emptyMsg) emptyMsg.style.display = 'block';
        return;
    }
    if (emptyMsg) emptyMsg.style.display = 'none';
    
    items.forEach(function(item) {
        var div = document.createElement('div');
        div.className = 'rbac-option-item';
        div.setAttribute('data-value', item.id);
        div.setAttribute('data-label', item.nome);
        
        var cb = document.createElement('input');
        cb.type = 'checkbox';
        cb.name = nameAttr;
        cb.value = item.id;
        cb.id = nameAttr + '_' + item.id;
        if (selectedIds && selectedIds.indexOf(parseInt(item.id)) !== -1) cb.checked = true;
        
        var lbl = document.createElement('label');
        lbl.htmlFor = cb.id;
        lbl.textContent = item.nome;
        
        div.appendChild(cb);
        div.appendChild(lbl);
        
        // Click anywhere on the row to toggle
        div.addEventListener('click', function(e) {
            if (e.target !== cb) cb.checked = !cb.checked;
            var type = nameAttr === 'empresas[]' ? 'empresas' : 'projetos';
            rbacUpdateBadges(type);
            if (type === 'empresas') rbacFiltrarProjetos();
        });
        
        list.appendChild(div);
    });
}

function rbacUpdateBadges(type) {
    var wrapperId = type === 'empresas' ? 'rbac_empresas_wrapper' : 'rbac_projetos_wrapper';
    var listId = type === 'empresas' ? 'rbac_empresas_list' : 'rbac_projetos_list';
    var wrapper = document.getElementById(wrapperId);
    if (!wrapper) return;
    
    var trigger = wrapper.querySelector('.rbac-dropdown-trigger');
    var badgesArea = trigger.querySelector('.rbac-badges-area');
    var placeholder = trigger.querySelector('.rbac-placeholder');
    var list = document.getElementById(listId);
    
    badgesArea.innerHTML = '';
    var checked = list.querySelectorAll('input[type="checkbox"]:checked');
    
    if (checked.length === 0) {
        placeholder.style.display = 'inline';
        return;
    }
    placeholder.style.display = 'none';
    
    checked.forEach(function(cb) {
        var item = cb.closest('.rbac-option-item');
        var label = item ? item.getAttribute('data-label') : cb.value;
        var badge = document.createElement('span');
        badge.className = 'rbac-badge';
        badge.innerHTML = '<span>' + label + '</span><i class="bi bi-x rbac-badge-remove"></i>';
        badge.querySelector('.rbac-badge-remove').addEventListener('click', function(e) {
            e.stopPropagation();
            cb.checked = false;
            rbacUpdateBadges(type);
            if (type === 'empresas') rbacFiltrarProjetos();
        });
        badgesArea.appendChild(badge);
    });
}

function rbacGetSelectedIds(type) {
    var listId = type === 'empresas' ? 'rbac_empresas_list' : 'rbac_projetos_list';
    var list = document.getElementById(listId);
    if (!list) return [];
    return Array.from(list.querySelectorAll('input[type="checkbox"]:checked')).map(function(cb) {
        return parseInt(cb.value);
    });
}

// ==================== RBAC Core Functions (backward-compatible API) ====================

function rbacCarregarOpcoes(callback) {
    if (window._rbacCarregado) {
        if (callback) callback();
        return;
    }
    console.log('[RBAC] Carregando empresas, baseUrl:', baseUrl);
    $.ajax({
        url: baseUrl + '/api/permissoes/empresas-usuario',
        type: 'GET',
        dataType: 'json',
        cache: false,
        success: function(res) {
            console.log('[RBAC] Empresas recebidas:', res.dados ? res.dados.length : 0);
            window._rbacEmpresas = res.dados || [];
            rbacRenderOptions('rbac_empresas_list', window._rbacEmpresas, 'empresas[]', []);
            rbacUpdateBadges('empresas');
            
            var ids = window._rbacEmpresas.map(function(e) { return e.id; }).join(',');
            if (ids) {
                $.ajax({
                    url: baseUrl + '/api/permissoes/projetos-usuario?empresas=' + ids,
                    type: 'GET',
                    dataType: 'json',
                    cache: false,
                    success: function(res2) {
                        console.log('[RBAC] Projetos recebidos:', res2.dados ? res2.dados.length : 0);
                        window._rbacProjetos = res2.dados || [];
                        rbacFiltrarProjetos();
                        window._rbacCarregado = true;
                        if (callback) callback();
                    },
                    error: function(xhr) {
                        console.error('[RBAC] Erro ao carregar projetos:', xhr.status, xhr.responseText);
                        window._rbacCarregado = true;
                        if (callback) callback();
                    }
                });
            } else {
                console.warn('[RBAC] Nenhuma empresa encontrada para o usuário');
                window._rbacCarregado = true;
                if (callback) callback();
            }
        },
        error: function(xhr) {
            console.error('[RBAC] Erro ao carregar empresas:', xhr.status, xhr.responseText);
            window._rbacCarregado = true;
            if (callback) callback();
        }
    });
}

function rbacFiltrarProjetos() {
    var empresasSelecionadas = rbacGetSelectedIds('empresas');
    var projetosSelecionados = rbacGetSelectedIds('projetos');
    
    var filtered = window._rbacProjetos.filter(function(p) {
        return empresasSelecionadas.length === 0 || empresasSelecionadas.indexOf(p.id_empresa) !== -1;
    });
    
    rbacRenderOptions('rbac_projetos_list', filtered, 'projetos[]', projetosSelecionados);
    rbacUpdateBadges('projetos');
}

function rbacPreencherSelects(empresasIds, projetosIds) {
    // Re-render com IDs selecionados
    rbacRenderOptions('rbac_empresas_list', window._rbacEmpresas, 'empresas[]', empresasIds);
    rbacUpdateBadges('empresas');
    
    var empresasSelecionadas = empresasIds;
    var filtered = window._rbacProjetos.filter(function(p) {
        return empresasSelecionadas.length === 0 || empresasSelecionadas.indexOf(p.id_empresa) !== -1;
    });
    rbacRenderOptions('rbac_projetos_list', filtered, 'projetos[]', projetosIds);
    rbacUpdateBadges('projetos');
}

function rbacLimparSelects() {
    ['rbac_empresas_list', 'rbac_projetos_list'].forEach(function(id) {
        var list = document.getElementById(id);
        if (list) list.querySelectorAll('input[type="checkbox"]').forEach(function(cb) { cb.checked = false; });
    });
    rbacUpdateBadges('empresas');
    rbacUpdateBadges('projetos');
}

// ==================== Event Listeners ====================

$(document).ready(function() {
    // Toggle dropdown on trigger click
    $(document).on('click', '.rbac-dropdown-trigger', function(e) {
        if (e.target.closest('.rbac-badge-remove')) return;
        rbacToggleDropdown(this);
    });
    
    // Close dropdowns on outside click
    $(document).on('click', function(e) {
        if (!e.target.closest('.rbac-checkbox-dropdown')) {
            document.querySelectorAll('.rbac-dropdown-trigger.active').forEach(function(t) {
                t.classList.remove('active');
            });
        }
    });
    
    // Search filtering
    $(document).on('input', '.rbac-search-input', function() {
        var term = this.value.toLowerCase();
        var panel = this.closest('.rbac-dropdown-panel');
        var items = panel.querySelectorAll('.rbac-option-item');
        var emptyMsg = panel.querySelector('.rbac-empty-msg');
        var visibleCount = 0;
        items.forEach(function(item) {
            var label = (item.getAttribute('data-label') || '').toLowerCase();
            var visible = label.indexOf(term) !== -1;
            item.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });
        if (emptyMsg) emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
    });
    
    // Keyboard support: Enter/Space to toggle, Escape to close
    $(document).on('keydown', '.rbac-dropdown-trigger', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            rbacToggleDropdown(this);
        } else if (e.key === 'Escape') {
            this.classList.remove('active');
        }
    });
    
    rbacCarregarOpcoes();
});
