<?php
/**
 * DMC DataLoad - Meu Perfil
 */
$pageTitle = 'Meu Perfil';
$currentPage = 'meu-perfil';

$csrfToken = \App\Core\AuthMiddleware::gerarTokenCSRF();

ob_start();
?>

<input type="hidden" id="csrfToken" value="<?= htmlspecialchars($csrfToken) ?>">

<!-- Page Header Modern -->
<div class="page-header-modern">
    <div class="page-icon-modern">
        <i class="bi bi-person-circle"></i>
    </div>
    <div>
        <h1 class="page-title-modern">Meu Perfil</h1>
        <p class="page-subtitle-modern">Visualize e edite suas informações pessoais</p>
    </div>
</div>

<div class="row g-4">
    <!-- Coluna esquerda: Info do usuário -->
    <div class="col-lg-4">
        <!-- Card Avatar -->
        <div class="card-modern mb-4">
            <div class="card-modern-body text-center py-4">
                <div class="perfil-avatar mx-auto" id="perfilAvatar">U</div>
                <h4 class="mt-3 mb-1 fw-bold" id="perfilNomeUsuario">—</h4>
                <span class="badge-nivel" id="perfilNivel">—</span>
                <div class="text-muted mt-2 small" id="perfilCriacao"></div>
            </div>
        </div>

        <!-- Card Empresas -->
        <div class="card-modern mb-4">
            <div class="card-modern-header">
                <i class="bi bi-building"></i> Empresas
            </div>
            <div class="card-modern-body">
                <div id="perfilEmpresas" class="d-flex flex-wrap gap-2">
                    <span class="text-muted small">Carregando...</span>
                </div>
            </div>
        </div>

        <!-- Card Projetos -->
        <div class="card-modern mb-4">
            <div class="card-modern-header">
                <i class="bi bi-folder"></i> Projetos
            </div>
            <div class="card-modern-body">
                <div id="perfilProjetos">
                    <span class="text-muted small">Carregando...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Coluna direita: Formulários -->
    <div class="col-lg-8">
        <!-- Dados Pessoais -->
        <div class="card-modern mb-4">
            <div class="card-modern-header">
                <i class="bi bi-pencil-square"></i> Dados Pessoais
            </div>
            <div class="card-modern-body">
                <form id="formPerfil">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-modern">Usuário</label>
                            <input type="text" class="form-control-modern" id="perfilUsuario" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">CPF</label>
                            <input type="text" class="form-control-modern" id="perfilCpf" disabled>
                            <small class="text-muted">Apenas administradores podem alterar</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Nome Completo</label>
                            <input type="text" class="form-control-modern" id="perfilNome" name="nome" placeholder="Seu nome completo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">E-mail</label>
                            <input type="email" class="form-control-modern" id="perfilEmail" name="email" placeholder="seu@email.com">
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn-modern-primary" id="btnSalvarPerfil">
                            <i class="bi bi-check2-circle"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Alterar Senha -->
        <div class="card-modern mb-4">
            <div class="card-modern-header">
                <i class="bi bi-shield-lock"></i> Alterar Senha
            </div>
            <div class="card-modern-body">
                <form id="formSenha">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label-modern">Senha Atual</label>
                            <div class="input-senha-wrapper">
                                <input type="password" class="form-control-modern" id="senhaAtual" name="senha_atual" placeholder="Digite sua senha atual" autocomplete="current-password">
                                <button type="button" class="btn-toggle-senha" onclick="toggleSenha('senhaAtual', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Nova Senha</label>
                            <div class="input-senha-wrapper">
                                <input type="password" class="form-control-modern" id="senhaNova" name="senha_nova" placeholder="Mínimo 6 caracteres" autocomplete="new-password">
                                <button type="button" class="btn-toggle-senha" onclick="toggleSenha('senhaNova', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-modern">Confirmar Nova Senha</label>
                            <div class="input-senha-wrapper">
                                <input type="password" class="form-control-modern" id="senhaConfirmar" name="senha_confirmar" placeholder="Repita a nova senha" autocomplete="new-password">
                                <button type="button" class="btn-toggle-senha" onclick="toggleSenha('senhaConfirmar', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn-modern-primary btn-senha-gradient" id="btnAlterarSenha">
                            <i class="bi bi-key"></i> Alterar Senha
                        </button>
                    </div>
                </form>

                <hr class="my-4" style="border-color: rgba(0,0,0,0.08);">

                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-1" style="font-weight:600;"><i class="bi bi-envelope me-2 text-primary"></i>Redefinir via E-mail</h6>
                        <small class="text-muted">Receba um link de redefinição de senha no seu e-mail cadastrado</small>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnResetEmail" onclick="solicitarResetEmail()">
                        <i class="bi bi-send me-1"></i> Enviar Link
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

$extraStyles = <<<'STYLES'
<style>
:root {
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --gradient-danger: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    --shadow-sm: 0 2px 8px rgba(0,0,0,0.06);
    --shadow-md: 0 4px 16px rgba(0,0,0,0.08);
    --shadow-lg: 0 8px 32px rgba(0,0,0,0.12);
    --radius-md: 12px;
    --radius-lg: 16px;
}

.page-header-modern {
    background: white;
    padding: 1.75rem 2rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.page-icon-modern {
    width: 70px;
    height: 70px;
    border-radius: var(--radius-lg);
    background: var(--gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
    flex-shrink: 0;
}

.page-title-modern {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 0.25rem 0;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-subtitle-modern {
    color: #64748b;
    margin: 0;
    font-size: 1rem;
}

.card-modern {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.card-modern-header {
    padding: 1.25rem 1.5rem;
    background: linear-gradient(to right, #f8fafc, #f1f5f9);
    border-bottom: 2px solid #e2e8f0;
    font-weight: 600;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-modern-body {
    padding: 1.5rem;
}

.form-label-modern {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    display: block;
}

.form-control-modern {
    width: 100%;
    border: 2px solid #e2e8f0;
    border-radius: var(--radius-md);
    padding: 0.75rem 1rem;
    font-size: 0.9375rem;
    transition: all 0.2s ease;
    background-color: #ffffff;
    color: #1e293b;
    line-height: 1.5;
}

.form-control-modern:hover {
    border-color: #cbd5e1;
}

.form-control-modern:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    outline: none;
}

.form-control-modern::placeholder {
    color: #94a3b8;
}

.form-control-modern:disabled {
    background-color: #f1f5f9;
    cursor: not-allowed;
    opacity: 0.7;
}

.btn-modern-primary {
    background: var(--gradient-primary);
    color: white;
    padding: 0.625rem 1.25rem;
    border-radius: var(--radius-md);
    border: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-sm);
    cursor: pointer;
}

.btn-modern-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: white;
}

.btn-modern-primary:disabled {
    opacity: 0.6;
    transform: none;
    cursor: not-allowed;
}

.btn-senha-gradient {
    background: var(--gradient-success);
}

/* Avatar */
.perfil-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: var(--gradient-primary);
    color: white;
    font-size: 2.5rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
}

/* Badge nível */
.badge-nivel {
    display: inline-block;
    padding: 0.35rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    background: var(--gradient-primary);
    color: white;
}

/* Empresa chip */
.empresa-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.4rem 0.75rem;
    background: linear-gradient(135deg, #f0f4ff, #e8ecff);
    color: #4338ca;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    border: 1px solid #c7d2fe;
}

/* Projeto item */
.projeto-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.875rem;
}

.projeto-item:last-child {
    border-bottom: none;
}

.projeto-item .projeto-nome {
    font-weight: 500;
    color: #1e293b;
}

.projeto-item .projeto-empresa {
    font-size: 0.75rem;
    color: #94a3b8;
}

.projeto-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    color: #16a34a;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* Input senha wrapper */
.input-senha-wrapper {
    position: relative;
}

.input-senha-wrapper .form-control-modern {
    padding-right: 3rem;
}

.btn-toggle-senha {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    padding: 0.25rem;
    font-size: 1.1rem;
    transition: color 0.2s;
}

.btn-toggle-senha:hover {
    color: #667eea;
}

@media (max-width: 767px) {
    .page-icon-modern {
        width: 48px;
        height: 48px;
        font-size: 1.5rem;
    }
    .page-title-modern {
        font-size: 1.5rem;
    }
    .perfil-avatar {
        width: 72px;
        height: 72px;
        font-size: 2rem;
    }
}
</style>
STYLES;

$extraScripts = <<<'SCRIPTS'
<script>
const csrfToken = document.getElementById('csrfToken').value;

const labelsNivel = {
    super_admin: 'Super Admin',
    admin: 'Administrador',
    desenvolvedor: 'Desenvolvedor',
    operador: 'Operador'
};

function toggleSenha(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

function carregarPerfil() {
    $.getJSON(baseUrl + '/api/perfil', function(res) {
        if (!res.sucesso) {
            Swal.fire('Erro', res.erro || 'Não foi possível carregar o perfil', 'error');
            return;
        }

        const d = res.dados;

        // Avatar
        const inicial = (d.nome || d.nome_usuario || 'U').charAt(0).toUpperCase();
        $('#perfilAvatar').text(inicial);

        // Info principal
        $('#perfilNomeUsuario').text(d.nome_usuario);
        $('#perfilNivel').text(labelsNivel[d.nivel_acesso] || d.nivel_acesso);
        if (d.data_criacao) {
            const dt = new Date(d.data_criacao);
            $('#perfilCriacao').html('<i class="bi bi-calendar3 me-1"></i>Membro desde ' + dt.toLocaleDateString('pt-BR'));
        }

        // Campos read-only
        $('#perfilUsuario').val(d.nome_usuario);
        $('#perfilCpf').val(d.cpf || '');

        // Campos editáveis
        $('#perfilNome').val(d.nome || '');
        $('#perfilEmail').val(d.email || '');

        // Empresas
        if (d.empresas && d.empresas.length) {
            let html = '';
            d.empresas.forEach(function(e) {
                html += '<span class="empresa-chip"><i class="bi bi-building"></i> ' + $('<span>').text(e.nome).html() + '</span>';
            });
            $('#perfilEmpresas').html(html);
        } else {
            $('#perfilEmpresas').html('<span class="text-muted small">Nenhuma empresa associada</span>');
        }

        // Projetos
        if (d.projetos && d.projetos.length) {
            let html = '';
            d.projetos.forEach(function(p) {
                html += '<div class="projeto-item">'
                    + '<div class="projeto-icon"><i class="bi bi-folder"></i></div>'
                    + '<div><div class="projeto-nome">' + $('<span>').text(p.nome).html() + '</div>'
                    + '<div class="projeto-empresa">' + $('<span>').text(p.empresa_nome).html() + '</div></div>'
                    + '</div>';
            });
            $('#perfilProjetos').html(html);
        } else {
            $('#perfilProjetos').html('<span class="text-muted small">Nenhum projeto associado</span>');
        }
    }).fail(function() {
        Swal.fire('Erro', 'Falha ao carregar dados do perfil', 'error');
    });
}

// Salvar dados pessoais
$('#formPerfil').submit(function(e) {
    e.preventDefault();
    const btn = $('#btnSalvarPerfil');
    btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Salvando...');

    $.post(baseUrl + '/api/perfil/atualizar', {
        nome: $('#perfilNome').val(),
        email: $('#perfilEmail').val(),
        _csrf_token: csrfToken
    }, function(res) {
        if (res.sucesso) {
            Swal.fire('Sucesso!', res.mensagem, 'success');
            carregarPerfil();
        } else {
            Swal.fire('Erro!', res.erro, 'error');
        }
    }, 'json').fail(function() {
        Swal.fire('Erro!', 'Falha na comunicação com o servidor', 'error');
    }).always(function() {
        btn.prop('disabled', false).html('<i class="bi bi-check2-circle"></i> Salvar Alterações');
    });
});

// Alterar senha
$('#formSenha').submit(function(e) {
    e.preventDefault();
    const btn = $('#btnAlterarSenha');
    btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Alterando...');

    $.post(baseUrl + '/api/perfil/alterar-senha', {
        senha_atual: $('#senhaAtual').val(),
        senha_nova: $('#senhaNova').val(),
        senha_confirmar: $('#senhaConfirmar').val(),
        _csrf_token: csrfToken
    }, function(res) {
        if (res.sucesso) {
            Swal.fire('Sucesso!', res.mensagem, 'success');
            $('#formSenha')[0].reset();
        } else {
            Swal.fire('Erro!', res.erro, 'error');
        }
    }, 'json').fail(function() {
        Swal.fire('Erro!', 'Falha na comunicação com o servidor', 'error');
    }).always(function() {
        btn.prop('disabled', false).html('<i class="bi bi-key"></i> Alterar Senha');
    });
});

$(document).ready(function() {
    carregarPerfil();
});

function solicitarResetEmail() {
    Swal.fire({
        title: 'Redefinir senha por e-mail?',
        text: 'Você receberá um link de redefinição no e-mail cadastrado.',
        icon: 'question',
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Enviar Link',
        confirmButtonColor: '#667eea'
    }).then((result) => {
        if (result.isConfirmed) {
            const btn = $('#btnResetEmail');
            btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Enviando...');
            $.post(baseUrl + '/api/perfil/solicitar-reset-email', {
                _csrf_token: csrfToken
            }, function(res) {
                if (res.sucesso) {
                    Swal.fire('E-mail enviado!', res.mensagem || 'Verifique sua caixa de entrada.', 'success');
                } else {
                    Swal.fire('Erro!', res.erro || 'Falha ao enviar e-mail.', 'error');
                }
            }, 'json').fail(function() {
                Swal.fire('Erro!', 'Falha na comunicação com o servidor', 'error');
            }).always(function() {
                btn.prop('disabled', false).html('<i class="bi bi-send me-1"></i> Enviar Link');
            });
        }
    });
}
</script>
SCRIPTS;

include __DIR__ . '/layouts/base.php';
?>
