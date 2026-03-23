
<?php
/**
 * Partial: Selects de Empresa e Projeto para associar a recursos
 * Incluir dentro do <form> de qualquer recurso.
 * 
 * Variáveis disponíveis via JS global:
 *   window._rbacEmpresas - array de {id, nome}
 *   window._rbacProjetos - array de {id, nome, id_empresa}
 * 
 * Funções JS globais:
 *   rbacCarregarOpcoes() - carrega empresas e projetos do servidor
 *   rbacPreencherSelects(empresasIds, projetosIds) - marca seleções
 *   rbacLimparSelects() - limpa seleções
 */
$nivelLogado = App\Core\AuthMiddleware::obterUsuario()['nivel_acesso'] ?? 'operador';
$exibirRbac = in_array($nivelLogado, ['super_admin', 'admin', 'desenvolvedor']);
?>
<?php if ($exibirRbac): ?>
<input type="hidden" name="_rbac_presente" value="1">
<div class="row g-3 mt-3 rbac-recursos-section">
    <div class="col-12">
        <hr class="my-2">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-shield-lock text-primary" style="font-size:1.1rem"></i>
            <span class="fw-semibold" style="font-size:.9rem">Visibilidade (Empresa / Projeto)</span>
        </div>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-medium mb-1" style="font-size:.82rem">
            <i class="bi bi-building me-1 text-muted"></i>Empresas
        </label>
        <div class="rbac-checkbox-dropdown" id="rbac_empresas_wrapper">
            <div class="rbac-dropdown-trigger" tabindex="0" data-target="empresas">
                <span class="rbac-placeholder">Selecione empresas...</span>
                <div class="rbac-badges-area"></div>
                <i class="bi bi-chevron-down rbac-chevron"></i>
            </div>
            <div class="rbac-dropdown-panel">
                <div class="rbac-search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Buscar empresa..." class="rbac-search-input" autocomplete="off">
                </div>
                <div class="rbac-options-list" id="rbac_empresas_list"></div>
                <div class="rbac-empty-msg" style="display:none">
                    <i class="bi bi-inbox text-muted"></i> Nenhuma empresa encontrada
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-medium mb-1" style="font-size:.82rem">
            <i class="bi bi-folder me-1 text-muted"></i>Projetos
            <small class="text-muted ms-1">(filtrado pelas empresas)</small>
        </label>
        <div class="rbac-checkbox-dropdown" id="rbac_projetos_wrapper">
            <div class="rbac-dropdown-trigger" tabindex="0" data-target="projetos">
                <span class="rbac-placeholder">Selecione projetos...</span>
                <div class="rbac-badges-area"></div>
                <i class="bi bi-chevron-down rbac-chevron"></i>
            </div>
            <div class="rbac-dropdown-panel">
                <div class="rbac-search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Buscar projeto..." class="rbac-search-input" autocomplete="off">
                </div>
                <div class="rbac-options-list" id="rbac_projetos_list"></div>
                <div class="rbac-empty-msg" style="display:none">
                    <i class="bi bi-inbox text-muted"></i> Nenhum projeto encontrado
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rbac-checkbox-dropdown {
    position: relative;
}
.rbac-dropdown-trigger {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
    min-height: 42px;
    padding: 6px 36px 6px 12px;
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: 8px;
    background: var(--bs-body-bg, #fff);
    cursor: pointer;
    transition: border-color .2s, box-shadow .2s;
    position: relative;
}
.rbac-dropdown-trigger:hover,
.rbac-dropdown-trigger:focus {
    border-color: var(--bs-primary, #0d6efd);
    box-shadow: 0 0 0 3px rgba(13,110,253,.12);
    outline: none;
}
.rbac-dropdown-trigger.active {
    border-color: var(--bs-primary, #0d6efd);
    box-shadow: 0 0 0 3px rgba(13,110,253,.18);
}
.rbac-chevron {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    transition: transform .25s;
    font-size: .85rem;
}
.rbac-dropdown-trigger.active .rbac-chevron {
    transform: translateY(-50%) rotate(180deg);
}
.rbac-placeholder {
    color: #999;
    font-size: .85rem;
    user-select: none;
}
.rbac-badges-area {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}
.rbac-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: .75rem;
    font-weight: 500;
    background: linear-gradient(135deg, rgba(13,110,253,.1), rgba(13,110,253,.05));
    color: var(--bs-primary, #0d6efd);
    border: 1px solid rgba(13,110,253,.15);
    max-width: 160px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.rbac-badge .rbac-badge-remove {
    cursor: pointer;
    opacity: .6;
    font-size: .7rem;
    flex-shrink: 0;
}
.rbac-badge .rbac-badge-remove:hover {
    opacity: 1;
}
.rbac-dropdown-panel {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    z-index: 1060;
    background: var(--bs-body-bg, #fff);
    border: 1px solid var(--bs-border-color, #dee2e6);
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    overflow: hidden;
    max-height: 260px;
}
.rbac-dropdown-trigger.active + .rbac-dropdown-panel {
    display: block;
}
.rbac-search-box {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border-bottom: 1px solid var(--bs-border-color, #eee);
}
.rbac-search-box i {
    color: #aaa;
    font-size: .82rem;
}
.rbac-search-input {
    border: none;
    outline: none;
    background: transparent;
    font-size: .82rem;
    width: 100%;
    color: var(--bs-body-color, #333);
}
.rbac-options-list {
    overflow-y: auto;
    max-height: 192px;
    padding: 4px 0;
}
.rbac-option-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px 12px;
    cursor: pointer;
    transition: background .15s;
    font-size: .82rem;
}
.rbac-option-item:hover {
    background: rgba(13,110,253,.06);
}
.rbac-option-item input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: var(--bs-primary, #0d6efd);
    cursor: pointer;
    flex-shrink: 0;
    border-radius: 4px;
}
.rbac-option-item label {
    cursor: pointer;
    margin: 0;
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.rbac-empty-msg {
    padding: 16px;
    text-align: center;
    font-size: .82rem;
    color: #999;
}
</style>
<?php endif; ?>
