<?php
/**
 * DMC DataLoad - Esqueci Minha Senha
 * Tela pública para solicitar recuperação de senha
 */
$loginBg = '';
try {
    $db = \App\Core\Database::getConexao();
    $stmt = $db->prepare("SELECT valor FROM tb_configuracoes WHERE chave = :chave LIMIT 1");
    $stmt->execute([':chave' => 'login_bg_imagem']);
    $loginBg = $stmt->fetchColumn() ?: '';
} catch (\Exception $e) {}
$bgCss = $loginBg ? "url('" . htmlspecialchars($loginBg, ENT_QUOTES) . "') center/cover no-repeat" : 'none';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <title>Esqueci Minha Senha - DMC DataLoad</title>
  <style>
    :root {
      --login-primary: #667eea;
      --login-primary-dark: #5a67d8;
      --login-primary-light: #7c8ff7;
      --login-accent: #764ba2;
      --login-bg: #eef1f8;
      --login-card-bg: rgba(255,255,255,0.97);
      --login-text: #1a1a2e;
      --login-muted: #6c757d;
      --login-radius: 20px;
      --login-input-radius: 14px;
      --login-transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    html, body { height: 100%; margin: 0; padding: 0; font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
    body.login-wrapper { background: var(--login-bg); overflow-x: hidden; }

    .login-image-side {
      background: linear-gradient(145deg, rgba(102,126,234,0.92), rgba(90,103,216,0.88), rgba(118,75,162,0.78)),
                  <?= $bgCss ?>;
      background-color: var(--login-primary);
      min-height: 100vh; display: flex; align-items: center; justify-content: center;
      padding: 3rem; position: relative; overflow: hidden;
    }
    .login-image-side::before, .login-image-side::after {
      content: ''; position: absolute; border-radius: 50%;
      background: radial-gradient(circle, rgba(255,255,255,0.18), transparent 70%);
      animation: loginOrb 10s ease-in-out infinite; pointer-events: none;
    }
    .login-image-side::before { width: 450px; height: 450px; top: -120px; right: -120px; }
    .login-image-side::after { width: 350px; height: 350px; bottom: -80px; left: -80px; animation-duration: 14s; animation-delay: -5s; }
    @keyframes loginOrb {
      0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.15; }
      33% { transform: translate(25px, -35px) scale(1.08); opacity: 0.22; }
      66% { transform: translate(-18px, 20px) scale(0.93); opacity: 0.12; }
    }

    .login-branding-overlay {
      color: #fff; text-align: center; z-index: 2; position: relative;
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      height: 100%; padding: 0 1.5rem;
      animation: loginBrandIn 0.8s 0.2s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    @keyframes loginBrandIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .login-branding-overlay .brand-logo {
      width: 90px; height: 90px; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);
      border-radius: 24px; display: flex; align-items: center; justify-content: center;
      font-size: 2.5rem; color: white; margin-bottom: 1.5rem;
      border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    }
    .login-branding-overlay h1 { font-size: 2.3rem; font-weight: 800; margin: 0 0 0.6rem; letter-spacing: -0.03em; text-shadow: 0 2px 12px rgba(0,0,0,0.2); }
    .login-branding-overlay p { opacity: 0.85; font-size: 1.05rem; line-height: 1.7; max-width: 380px; margin: 0 auto; }

    .mobile-brand-logo {
      width: 64px; height: 64px; background: linear-gradient(135deg, var(--login-primary), var(--login-accent));
      border-radius: 18px; display: inline-flex; align-items: center; justify-content: center;
      font-size: 1.8rem; color: white; margin-bottom: 0.75rem;
      box-shadow: 0 6px 20px rgba(102,126,234,0.3);
    }

    .login-form-side {
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      background: var(--login-bg); padding: 3rem; position: relative; min-height: 100vh;
    }
    .login-form-side::before {
      content: ''; position: absolute; inset: 0;
      background-image: radial-gradient(circle, rgba(102,126,234,0.04) 1px, transparent 1px);
      background-size: 28px 28px; pointer-events: none;
    }

    .login-card {
      max-width: 440px; width: 100%; background: var(--login-card-bg); border-radius: var(--login-radius);
      box-shadow: 0 4px 6px rgba(0,0,0,0.02), 0 12px 28px rgba(102,126,234,0.06), 0 24px 48px rgba(0,0,0,0.04);
      border: 1px solid rgba(102,126,234,0.06); position: relative; z-index: 2; overflow: hidden;
      animation: loginCardIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .login-card::before {
      content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
      background: linear-gradient(90deg, var(--login-primary), var(--login-accent), var(--login-primary-light), var(--login-primary));
      background-size: 300% 100%; animation: loginGradientShift 4s ease infinite;
    }
    @keyframes loginCardIn { from { opacity: 0; transform: translateY(24px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
    @keyframes loginGradientShift { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }

    .login-card .form-label { font-weight: 600; font-size: 0.875rem; color: var(--login-text); letter-spacing: 0.01em; margin-bottom: 6px; }

    .login-card .input-group {
      border-radius: var(--login-input-radius); overflow: hidden; transition: all var(--login-transition);
      box-shadow: 0 2px 4px rgba(0,0,0,0.03); border: 2px solid #e4e8ef; background: #fff;
    }
    .login-card .input-group:focus-within { border-color: var(--login-primary); box-shadow: 0 0 0 4px rgba(102,126,234,0.1), 0 2px 8px rgba(102,126,234,0.08); }
    .login-card .input-group-text { background: transparent; border: none; padding: 0.75rem 0 0.75rem 1rem; color: #b0b8c5; transition: color var(--login-transition); font-size: 1.1rem; }
    .login-card .input-group:focus-within .input-group-text { color: var(--login-primary); }
    .login-card .form-control { background: transparent; border: none; padding: 0.8rem 0.75rem 0.8rem 0.5rem; font-size: 0.95rem; color: var(--login-text); font-weight: 500; }
    .login-card .form-control:focus { box-shadow: none; background: transparent; }
    .login-card .form-control::placeholder { color: #b8c0cc; font-weight: 400; }

    .login-card .btn-primary {
      background: linear-gradient(135deg, var(--login-primary) 0%, var(--login-primary-dark) 100%);
      border: none; border-radius: var(--login-input-radius); padding: 14px; font-size: 1rem;
      font-weight: 700; letter-spacing: 0.02em; transition: all var(--login-transition);
      position: relative; overflow: hidden; box-shadow: 0 4px 14px rgba(102,126,234,0.25);
    }
    .login-card .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(102,126,234,0.35); }
    .login-card .btn-primary:active { transform: translateY(0); }

    .login-card .alert { border-radius: 12px; border: none; font-size: 0.88rem; font-weight: 500; padding: 12px 16px; display: none; }
    .login-card .alert-danger { background: rgba(220,53,69,0.08); color: #842029; border-left: 4px solid #dc3545; }
    .login-card .alert-success { background: rgba(25,135,84,0.08); color: #0f5132; border-left: 4px solid #198754; }

    .login-card a { color: var(--login-primary); font-weight: 600; text-decoration: none; transition: all var(--login-transition); }
    .login-card a:hover { color: var(--login-primary-dark); }

    .login-icon-circle {
      width: 80px; height: 80px; border-radius: 50%;
      background: linear-gradient(135deg, rgba(102,126,234,0.1), rgba(118,75,162,0.06));
      display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem; position: relative;
    }
    .login-icon-circle::before {
      content: ''; position: absolute; inset: -3px; border-radius: 50%;
      background: linear-gradient(135deg, var(--login-primary), var(--login-accent)); opacity: 0.15; z-index: -1;
    }
    .login-icon-circle i {
      font-size: 2rem; background: linear-gradient(135deg, var(--login-primary), var(--login-accent));
      -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }

    .login-footer { font-size: 0.82rem; color: var(--login-muted); text-align: center; margin-top: 2rem; z-index: 2; position: relative; }

    .login-stagger-1 { animation: loginStagger 0.5s 0.15s both; }
    .login-stagger-2 { animation: loginStagger 0.5s 0.25s both; }
    .login-stagger-3 { animation: loginStagger 0.5s 0.35s both; }
    @keyframes loginStagger { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

    .btn-primary .spinner-border { width: 1.1rem; height: 1.1rem; border-width: 2px; }

    @media (max-width: 991px) {
      .login-image-side { display: none; }
      .login-form-side { padding: 1.5rem; background: linear-gradient(160deg, #eef1f8, #e2e8f4, #eef1f8); }
      .login-card { max-width: 480px; box-shadow: 0 12px 40px rgba(0,0,0,0.08), 0 4px 12px rgba(102,126,234,0.08); }
    }
    @media (max-width: 575px) { .login-form-side { padding: 1rem 0.75rem; } .login-card { border-radius: 16px; } }
    @media (prefers-reduced-motion: reduce) { .login-card, .login-branding-overlay, .login-stagger-1, .login-stagger-2, .login-stagger-3, .login-image-side::before, .login-image-side::after { animation: none !important; } .login-card::before { animation: none !important; } }
    .login-image-side { background-color: var(--login-primary); }
  </style>
</head>
<body class="login-wrapper">
  <div class="container-fluid min-vh-100 p-0">
    <div class="row g-0 min-vh-100">
      <!-- Branding Side -->
      <div class="col-lg-7 d-none d-lg-block login-image-side">
        <div class="login-branding-overlay">
          <div class="brand-logo"><i class="bi bi-database"></i></div>
          <h1 class="text-white mt-2 fw-bold">DMC DataLoad</h1>
          <p class="text-white-50 lead">Sistema de banco de dados</p>
        </div>
      </div>

      <!-- Form Side -->
      <div class="col-lg-5 col-md-12 login-form-side">
        <div class="login-card p-4 p-md-5">
          <div class="w-100">
            <div class="text-center mb-4 d-lg-none">
              <div class="mobile-brand-logo"><i class="bi bi-database"></i></div>
            </div>

            <div class="text-center login-stagger-1">
              <div class="login-icon-circle mx-auto"><i class="bi bi-envelope-paper"></i></div>
              <h2 class="fw-bold mb-1" style="font-size:1.5rem; letter-spacing:-0.02em;">Esqueci Minha Senha</h2>
              <p style="color: var(--login-muted); font-size:0.92rem;" class="mb-4">Digite seu usuário ou e-mail para receber o link de recuperação.</p>
            </div>

            <div class="alert alert-danger align-items-center" id="alertErro" role="alert" style="display:none;">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <div id="alertErroMsg"></div>
            </div>
            <div class="alert alert-success align-items-center" id="alertSucesso" role="alert" style="display:none;">
              <i class="bi bi-check-circle-fill me-2"></i>
              <div id="alertSucessoMsg"></div>
            </div>

            <form id="formEsqueciSenha" class="login-stagger-2">
              <div class="form-group mb-4">
                <label for="inputIdentificador" class="form-label">Usuário ou E-mail</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                  <input type="text" class="form-control" id="inputIdentificador" name="identificador" placeholder="Seu usuário ou e-mail" required autofocus autocomplete="username">
                </div>
              </div>
              <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary btn-lg fw-bold" id="btnEnviar">
                  <i class="bi bi-send me-2"></i>Enviar Link de Recuperação
                </button>
              </div>
            </form>

            <div class="text-center login-stagger-3">
              <a href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/login">
                <i class="bi bi-arrow-left me-1"></i>Voltar ao Login
              </a>
            </div>
          </div>
        </div>
        <div class="login-footer">
          <p class="mb-0">&copy; <?= date('Y') ?> DMC DataLoad. Todos os direitos reservados.</p>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script>
    const baseUrl = '<?= defined("BASE_URL") ? BASE_URL : "" ?>';

    $('#formEsqueciSenha').on('submit', function(e){
      e.preventDefault();
      var $btn = $('#btnEnviar');
      var originalHtml = $btn.html();
      $('#alertErro, #alertSucesso').hide();
      $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Enviando...');

      $.ajax({
        url: baseUrl + '/esqueci-senha',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res){
          if (res.sucesso) {
            $('#alertSucessoMsg').text(res.mensagem);
            $('#alertSucesso').css('display', 'flex');
            $('#formEsqueciSenha')[0].reset();
          } else {
            $('#alertErroMsg').text(res.erro || 'Erro ao processar solicitação');
            $('#alertErro').css('display', 'flex');
          }
          $btn.prop('disabled', false).html(originalHtml);
        },
        error: function(){
          $('#alertErroMsg').text('Erro de comunicação com servidor');
          $('#alertErro').css('display', 'flex');
          $btn.prop('disabled', false).html(originalHtml);
        }
      });
    });
  </script>
</body>
</html>
