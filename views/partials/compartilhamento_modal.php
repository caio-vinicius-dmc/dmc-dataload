<?php
/**
 * Modal de compartilhamento de recursos.
 * Visível para super_admin, admin e desenvolvedor (quem pode criar recursos).
 * Operador não compartilha nada.
 *
 * Uso: include este arquivo em qualquer view e chame abrirModalCompartilhamento(tipoRecurso, idRecurso)
 */
$nivelComp = \App\Core\AuthMiddleware::obterUsuario()['nivel_acesso'] ?? 'operador';
$exibirComp = in_array($nivelComp, ['super_admin', 'admin', 'desenvolvedor']);
?>
<?php if ($exibirComp): ?>
<div class="modal fade" id="modalCompartilhamento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-share me-2"></i>Compartilhar recurso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="comp_tipo_recurso">
                <input type="hidden" id="comp_id_recurso">

                <!-- Adicionar compartilhamento -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Compartilhar com</label>
                    <div class="input-group">
                        <select class="form-select" id="comp_usuario_destino">
                            <option value="">Selecione um usuário...</option>
                        </select>
                        <select class="form-select" id="comp_permissao" style="max-width:120px">
                            <option value="ver">Ver</option>
                            <option value="editar">Editar</option>
                        </select>
                        <button class="btn btn-success" type="button" id="btnAdicionarCompartilhamento">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>

                <hr>

                <!-- Lista de compartilhamentos existentes -->
                <h6 class="fw-bold mb-2"><i class="bi bi-people me-1"></i>Compartilhado com</h6>
                <div id="comp_lista" class="list-group list-group-flush">
                    <p class="text-muted small">Nenhum compartilhamento.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
