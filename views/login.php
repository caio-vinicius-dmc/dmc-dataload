<?php
// Buscar configurações de branding
$loginBg = '';
$appNome = 'DMC DataLoad';
$appDescricao = 'Sistema de banco de dados';
$appFavicon = '';
try {
    $db = \App\Core\Database::getConexao();
    $stmt = $db->prepare("SELECT chave, valor FROM tb_configuracoes WHERE chave IN ('login_bg_imagem', 'app_nome', 'app_descricao', 'app_favicon')");
    $stmt->execute();
    foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
        if ($row['valor'] !== '' && $row['valor'] !== null) {
            match ($row['chave']) {
                'login_bg_imagem' => $loginBg = $row['valor'],
                'app_nome' => $appNome = $row['valor'],
                'app_descricao' => $appDescricao = $row['valor'],
                'app_favicon' => $appFavicon = $row['valor'],
            };
        }
    }
} catch (\Exception $e) {
    $loginBg = '';
}
$bgCss = $loginBg ? "url('" . htmlspecialchars($loginBg, ENT_QUOTES) . "') center/cover no-repeat" : 'none';
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <title>Login - <?= htmlspecialchars($appNome) ?></title>
  <?php if ($appFavicon): ?>
  <link rel="icon" href="<?= $baseUrl . htmlspecialchars($appFavicon) ?>" type="image/x-icon">
  <?php endif; ?>
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

    /* === IMAGE SIDE (Desktop Left Panel) === */
    .login-image-side {
      background: linear-gradient(145deg, rgba(102,126,234,0.92), rgba(90,103,216,0.88), rgba(118,75,162,0.78)),
                  <?= $bgCss ?>;
      background-color: var(--login-primary);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 3rem;
      position: relative;
      overflow: hidden;
    }

    /* Animated floating orbs */
    .login-image-side::before,
    .login-image-side::after {
      content: '';
      position: absolute;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(255,255,255,0.18), transparent 70%);
      animation: loginOrb 10s ease-in-out infinite;
      pointer-events: none;
    }
    .login-image-side::before {
      width: 450px; height: 450px;
      top: -120px; right: -120px;
      animation-duration: 10s;
    }
    .login-image-side::after {
      width: 350px; height: 350px;
      bottom: -80px; left: -80px;
      animation-duration: 14s;
      animation-delay: -5s;
    }
    @keyframes loginOrb {
      0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.15; }
      33% { transform: translate(25px, -35px) scale(1.08); opacity: 0.22; }
      66% { transform: translate(-18px, 20px) scale(0.93); opacity: 0.12; }
    }

    /* Branding overlay */
    .login-branding-overlay {
      color: #fff;
      text-align: center;
      z-index: 2;
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100%;
      padding: 0 1.5rem;
      animation: loginBrandIn 0.8s 0.2s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    @keyframes loginBrandIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .login-branding-overlay .brand-logo {
      width: 90px;
      height: 90px;
      background: rgba(255,255,255,0.15);
      backdrop-filter: blur(10px);
      border-radius: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.5rem;
      color: white;
      margin-bottom: 1.5rem;
      border: 1px solid rgba(255,255,255,0.2);
      box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    }
    .login-branding-overlay h1 {
      font-size: 2.3rem;
      font-weight: 800;
      margin: 0 0 0.6rem;
      letter-spacing: -0.03em;
      text-shadow: 0 2px 12px rgba(0,0,0,0.2);
    }
    .login-branding-overlay p {
      opacity: 0.85;
      font-size: 1.05rem;
      line-height: 1.7;
      max-width: 380px;
      margin: 0 auto;
    }

    /* Features list */
    .login-features {
      margin-top: 2.5rem;
      display: flex;
      flex-direction: column;
      gap: 14px;
      text-align: left;
      width: 100%;
      max-width: 340px;
    }
    .login-feature-item {
      display: flex;
      align-items: center;
      gap: 14px;
      font-size: 0.95rem;
      font-weight: 500;
      color: rgba(255,255,255,0.9);
      background: rgba(255,255,255,0.08);
      backdrop-filter: blur(4px);
      border-radius: 12px;
      padding: 12px 16px;
      border: 1px solid rgba(255,255,255,0.1);
      transition: all var(--login-transition);
    }
    .login-feature-item:hover {
      background: rgba(255,255,255,0.14);
      transform: translateX(4px);
    }
    .login-feature-item i {
      font-size: 1.2rem;
      width: 24px;
      text-align: center;
      flex-shrink: 0;
      opacity: 0.9;
    }

    /* Mobile logo */
    .mobile-brand-logo {
      width: 64px;
      height: 64px;
      background: linear-gradient(135deg, var(--login-primary), var(--login-accent));
      border-radius: 18px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      color: white;
      margin-bottom: 0.75rem;
      box-shadow: 0 6px 20px rgba(102,126,234,0.3);
    }

    /* === FORM SIDE === */
    .login-form-side {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background: var(--login-bg);
      padding: 3rem;
      position: relative;
      min-height: 100vh;
    }
    .login-form-side::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image: radial-gradient(circle, rgba(102,126,234,0.04) 1px, transparent 1px);
      background-size: 28px 28px;
      pointer-events: none;
    }

    /* === LOGIN CARD === */
    .login-card {
      max-width: 440px;
      width: 100%;
      background: var(--login-card-bg);
      border-radius: var(--login-radius);
      box-shadow:
        0 4px 6px rgba(0,0,0,0.02),
        0 12px 28px rgba(102,126,234,0.06),
        0 24px 48px rgba(0,0,0,0.04);
      border: 1px solid rgba(102,126,234,0.06);
      position: relative;
      z-index: 2;
      overflow: hidden;
      animation: loginCardIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .login-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--login-primary), var(--login-accent), var(--login-primary-light), var(--login-primary));
      background-size: 300% 100%;
      animation: loginGradientShift 4s ease infinite;
    }
    @keyframes loginCardIn {
      from { opacity: 0; transform: translateY(24px) scale(0.97); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes loginGradientShift {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }

    /* Form labels */
    .login-card .form-label {
      font-weight: 600;
      font-size: 0.875rem;
      color: var(--login-text);
      letter-spacing: 0.01em;
      margin-bottom: 6px;
    }

    /* === MODERN INPUTS === */
    .login-card .input-group {
      border-radius: var(--login-input-radius);
      overflow: hidden;
      transition: all var(--login-transition);
      box-shadow: 0 2px 4px rgba(0,0,0,0.03);
      border: 2px solid #e4e8ef;
      background: #fff;
    }
    .login-card .input-group:focus-within {
      border-color: var(--login-primary);
      box-shadow: 0 0 0 4px rgba(102,126,234,0.1), 0 2px 8px rgba(102,126,234,0.08);
    }
    .login-card .input-group-text {
      background: transparent;
      border: none;
      padding: 0.75rem 0 0.75rem 1rem;
      color: #b0b8c5;
      transition: color var(--login-transition);
      font-size: 1.1rem;
    }
    .login-card .input-group:focus-within .input-group-text {
      color: var(--login-primary);
    }
    .login-card .input-group:focus-within .input-group-text i {
      transform: scale(1.1);
    }
    .login-card .input-group-text i {
      transition: transform var(--login-transition), color var(--login-transition);
    }
    .login-card .form-control {
      background: transparent;
      border: none;
      padding: 0.8rem 0.75rem 0.8rem 0.5rem;
      font-size: 0.95rem;
      color: var(--login-text);
      font-weight: 500;
    }
    .login-card .form-control:focus {
      box-shadow: none;
      background: transparent;
    }
    .login-card .form-control::placeholder {
      color: #b8c0cc;
      font-weight: 400;
    }

    /* Toggle password button */
    .btn-toggle-pw {
      background: transparent;
      border: none;
      padding: 0.75rem 1rem;
      color: #b0b8c5;
      cursor: pointer;
      transition: all var(--login-transition);
      display: flex;
      align-items: center;
    }
    .btn-toggle-pw:hover {
      color: var(--login-primary);
    }

    /* === PRIMARY BUTTON === */
    .login-card .btn-primary {
      background: linear-gradient(135deg, var(--login-primary) 0%, var(--login-primary-dark) 100%);
      border: none;
      border-radius: var(--login-input-radius);
      padding: 14px;
      font-size: 1rem;
      font-weight: 700;
      letter-spacing: 0.02em;
      transition: all var(--login-transition);
      position: relative;
      overflow: hidden;
      box-shadow: 0 4px 14px rgba(102,126,234,0.25);
    }
    .login-card .btn-primary::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(255,255,255,0.18), transparent 60%);
      opacity: 0;
      transition: opacity var(--login-transition);
    }
    .login-card .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 28px rgba(102,126,234,0.35);
      background: linear-gradient(135deg, var(--login-primary-light) 0%, var(--login-primary) 100%);
    }
    .login-card .btn-primary:hover::after { opacity: 1; }
    .login-card .btn-primary:active {
      transform: translateY(0);
      box-shadow: 0 4px 12px rgba(102,126,234,0.2);
    }

    /* === ALERTS === */
    .login-card .alert {
      border-radius: 12px;
      border: none;
      font-size: 0.88rem;
      font-weight: 500;
      animation: loginAlertIn 0.4s ease both;
      padding: 12px 16px;
      display: none;
    }
    .login-card .alert-danger {
      background: rgba(220,53,69,0.08);
      color: #842029;
      border-left: 4px solid #dc3545;
    }
    @keyframes loginAlertIn {
      from { opacity: 0; transform: translateY(-8px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* === FOOTER === */
    .login-footer {
      font-size: 0.82rem;
      color: var(--login-muted);
      text-align: center;
      margin-top: 2rem;
      z-index: 2;
      position: relative;
    }

    /* === STAGGER ANIMATION === */
    .login-stagger-1 { animation: loginStagger 0.5s 0.15s both; }
    .login-stagger-2 { animation: loginStagger 0.5s 0.25s both; }
    .login-stagger-3 { animation: loginStagger 0.5s 0.35s both; }
    .login-stagger-4 { animation: loginStagger 0.5s 0.45s both; }
    @keyframes loginStagger {
      from { opacity: 0; transform: translateY(12px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Loading spinner on button */
    .btn-primary .spinner-border {
      width: 1.1rem;
      height: 1.1rem;
      border-width: 2px;
    }

    /* === RESPONSIVE === */
    @media (max-width: 991px) {
      .login-image-side { display: none; }
      .login-form-side {
        padding: 1.5rem;
        min-height: 100vh;
        background: linear-gradient(160deg, #eef1f8 0%, #e2e8f4 50%, #eef1f8 100%);
      }
      .login-card {
        max-width: 480px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.08), 0 4px 12px rgba(102,126,234,0.08);
      }
    }
    @media (max-width: 575px) {
      .login-form-side { padding: 1rem 0.75rem; }
      .login-card { border-radius: 16px; }
      .login-card .btn-primary { padding: 13px; }
    }
    @media (min-width: 992px) and (max-height: 700px) {
      .login-form-side { padding: 1.5rem; }
      .login-card { transform: scale(0.95); }
    }
    @media (min-height: 900px) {
      .login-form-side { padding: 4rem 3rem; }
    }

    /* === Reduced motion === */
    @media (prefers-reduced-motion: reduce) {
      .login-card, .login-branding-overlay,
      .login-stagger-1, .login-stagger-2, .login-stagger-3, .login-stagger-4,
      .login-card .alert,
      .login-image-side::before, .login-image-side::after {
        animation: none !important;
      }
      .login-card::before { animation: none !important; }
    }

    .login-image-side { background-color: var(--login-primary); }
  </style>
</head>
<body class="login-wrapper">
  <div class="container-fluid min-vh-100 p-0">
    <div class="row g-0 min-vh-100">
      <!-- Branding Side -->
      <div class="col-lg-7 d-none d-lg-block login-image-side">
        <div class="login-branding-overlay">
          <div class="brand-logo">
            <?php if ($appFavicon): ?>
              <img src="<?= $baseUrl . htmlspecialchars($appFavicon) ?>" alt="Logo" style="width:48px;height:48px;object-fit:contain;">
            <?php else: ?>
              <i class="bi bi-database"></i>
            <?php endif; ?>
          </div>
          <h1 class="text-white mt-2 fw-bold"><?= htmlspecialchars($appNome) ?></h1>
          <p class="text-white-50 lead"><?= htmlspecialchars($appDescricao) ?></p>
          <div class="login-features">
            <div class="login-feature-item">
              <i class="bi bi-arrow-left-right"></i>
              <span>ETL completo com múltiplas conexões</span>
            </div>
            <div class="login-feature-item">
              <i class="bi bi-graph-up-arrow"></i>
              <span>Dashboard com métricas em tempo real</span>
            </div>
            <div class="login-feature-item">
              <i class="bi bi-shield-check"></i>
              <span>Controle de permissões por RBAC</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Form Side -->
      <div class="col-lg-5 col-md-12 login-form-side">
        <div class="login-card p-4 p-md-5">
          <div class="w-100">
            <!-- Mobile logo -->
            <div class="text-center mb-4 d-lg-none">
              <div class="mobile-brand-logo">
                <?php if ($appFavicon): ?>
                  <img src="<?= $baseUrl . htmlspecialchars($appFavicon) ?>" alt="Logo" style="width:32px;height:32px;object-fit:contain;">
                <?php else: ?>
                  <i class="bi bi-database"></i>
                <?php endif; ?>
              </div>
              <h3 class="fw-bold mt-2" style="color: var(--login-primary);"><?= htmlspecialchars($appNome) ?></h3>
            </div>

            <div class="login-stagger-1">
              <h2 class="fw-bold mb-1" style="font-size:1.65rem; letter-spacing:-0.02em;">Bem-vindo de volta</h2>
              <p style="color: var(--login-muted); font-size:0.92rem;" class="mb-4">Acesse sua conta para continuar.</p>
            </div>

            <div class="alert alert-danger align-items-center" id="alertErro" role="alert" style="display:none;">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <div id="alertErroMsg"></div>
            </div>

            <form id="formLogin">
              <div class="form-group mb-3 login-stagger-2">
                <label for="inputUsuario" class="form-label">Usuário</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                  <input type="text" class="form-control" id="inputUsuario" name="usuario" placeholder="Seu usuário" required autofocus autocomplete="username">
                </div>
              </div>

              <div class="form-group mb-4 login-stagger-3">
                <label for="inputSenha" class="form-label">Senha</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                  <input type="password" class="form-control" id="inputSenha" name="senha" placeholder="Sua senha" required autocomplete="current-password">
                  <button class="btn-toggle-pw" type="button" id="togglePw" tabindex="-1" aria-label="Mostrar senha">
                    <i class="bi bi-eye"></i>
                  </button>
                </div>
              </div>

              <div class="d-grid login-stagger-4">
                <button type="submit" class="btn btn-primary btn-lg fw-bold" id="btnLogin">
                  <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
                </button>
              </div>

              <div class="text-center mt-3 login-stagger-4">
                <a href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/esqueci-senha" style="font-size:0.9rem;">
                  <i class="bi bi-question-circle me-1"></i>Esqueci minha senha
                </a>
              </div>
            </form>
          </div>
        </div>
        <div class="login-footer">
          <p class="mb-0">&copy; <?= date('Y') ?> <?= htmlspecialchars($appNome) ?>. Todos os direitos reservados.</p>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script>
    const baseUrl = '<?= defined("BASE_URL") ? BASE_URL : "" ?>';

    // Toggle password visibility
    (function(){
      var btn = document.getElementById('togglePw');
      var input = document.getElementById('inputSenha');
      if(btn && input){
        btn.addEventListener('click', function(){
          var icon = btn.querySelector('i');
          if(input.type === 'password'){
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
          } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
          }
        });
      }
    })();

    // Form submit
    $('#formLogin').on('submit', function(e){
      e.preventDefault();
      var $btn = $('#btnLogin');
      var originalHtml = $btn.html();

      $('#alertErro').hide();
      $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Entrando...');

      $.ajax({
        url: baseUrl + '/login',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res){
          if (res.sucesso) {
            window.location.href = baseUrl + '/dashboard';
          } else {
            $('#alertErroMsg').text(res.erro || 'Erro ao autenticar');
            $('#alertErro').css('display', 'flex');
            $btn.prop('disabled', false).html(originalHtml);
          }
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
