
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
$exibirRbac = in_array($nivelLogado, ['super_admin', 'admin']);
?>
<?php if ($exibirRbac): ?>
<div class="row g-3 mt-2 rbac-recursos-section">
    <div class="col-12">
        <hr class="my-2">
        <small class="text-muted fw-bold"><i class="bi bi-building me-1"></i>Visibilidade (Empresa / Projeto)</small>
    </div>
    <div class="col-md-6">
        <label class="form-label-modern">Empresas</label>
        <select class="form-select-modern" name="empresas[]" id="rbac_empresas" multiple size="3">
        </select>
        <small class="text-muted">Ctrl+click para múltiplas</small>
    </div>
    <div class="col-md-6">
        <label class="form-label-modern">Projetos</label>
        <select class="form-select-modern" name="projetos[]" id="rbac_projetos" multiple size="3">
        </select>
        <small class="text-muted">Filtrado pelas empresas selecionadas</small>
    </div>
</div>
<?php endif; ?>
