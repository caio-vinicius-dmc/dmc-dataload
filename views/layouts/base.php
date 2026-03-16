<?php
/**
 * DMC DataLoad - Layout Base
 * Template responsivo com sidebar moderna
 */
$pageTitle = $pageTitle ?? 'DMC DataLoad';
$currentPage = $currentPage ?? '';
$usuario = $_SESSION['usuario'] ?? [];
$base = defined('BASE_URL') ? BASE_URL : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1a1f2e">
    <title><?= htmlspecialchars($pageTitle) ?> - DMC DataLoad</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons & CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --topbar-height: 70px;
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --secondary: #0ea5e9;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #1a1f2e;
            --dark-lighter: #252b3b;
            --dark-lightest: #313849;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --text-light: rgba(255,255,255,0.9);
            --text-muted: rgba(255,255,255,0.5);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--gray-100);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ========== SIDEBAR ========== */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--dark) 0%, #12151f 100%);
            z-index: 1050;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 25px rgba(0,0,0,0.2);
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: white;
        }

        .sidebar-brand-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }

        .sidebar-brand-text {
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: -0.5px;
        }

        .sidebar-brand-text small {
            display: block;
            font-weight: 400;
            font-size: 0.7rem;
            color: var(--text-muted);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .sidebar-menu {
            flex: 1;
            padding: 1rem 0;
            overflow-y: auto;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: var(--dark-lightest);
            border-radius: 4px;
        }

        .menu-label {
            padding: 0.75rem 1.5rem 0.5rem;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-muted);
        }

        .menu-item {
            padding: 0 1rem;
            margin-bottom: 4px;
        }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.85rem 1rem;
            border-radius: 10px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .menu-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: var(--primary);
            transform: scaleY(0);
            transition: var(--transition);
            border-radius: 0 4px 4px 0;
        }

        .menu-link:hover {
            background: rgba(255,255,255,0.05);
            color: var(--text-light);
        }

        .menu-link.active {
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.2) 0%, transparent 100%);
            color: var(--text-light);
        }

        .menu-link.active::before {
            transform: scaleY(1);
        }

        .menu-link i {
            font-size: 1.25rem;
            width: 24px;
            text-align: center;
        }

        .menu-badge {
            margin-left: auto;
            padding: 0.2rem 0.6rem;
            font-size: 0.7rem;
            font-weight: 600;
            border-radius: 20px;
            background: var(--primary);
            color: white;
        }

        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.75rem;
            background: var(--dark-lighter);
            border-radius: 12px;
            cursor: pointer;
            transition: var(--transition);
        }

        .user-card:hover {
            background: var(--dark-lightest);
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            font-size: 1rem;
        }

        .user-info {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-light);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .user-actions {
            display: flex;
            gap: 4px;
        }

        .user-actions .btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: var(--text-muted);
            background: transparent;
            border: none;
        }

        .user-actions .btn:hover {
            background: rgba(255,255,255,0.1);
            color: var(--text-light);
        }

        /* ========== TOPBAR ========== */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: white;
            z-index: 1040;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            box-shadow: 0 2px 15px rgba(0,0,0,0.04);
            transition: var(--transition);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .menu-toggle {
            width: 40px;
            height: 40px;
            border: none;
            background: var(--gray-100);
            border-radius: 10px;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .menu-toggle:hover {
            background: var(--gray-200);
        }

        .menu-toggle i {
            font-size: 1.25rem;
            color: var(--gray-600);
        }

        .page-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
        }

        .breadcrumb {
            margin: 0;
            font-size: 0.85rem;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .topbar-btn {
            width: 42px;
            height: 42px;
            border: none;
            background: var(--gray-100);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }

        .topbar-btn:hover {
            background: var(--gray-200);
        }

        .topbar-btn i {
            font-size: 1.2rem;
            color: var(--gray-600);
        }

        .notification-badge {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 8px;
            height: 8px;
            background: var(--danger);
            border-radius: 50%;
            border: 2px solid white;
        }

        /* ========== MAIN CONTENT ========== */
        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: var(--topbar-height);
            min-height: 100vh;
            transition: var(--transition);
        }

        .content-wrapper {
            padding: 1.5rem;
        }

        /* ========== CARDS ========== */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            background: white;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid var(--gray-200);
            padding: 1.25rem 1.5rem;
            border-radius: 16px 16px 0 0 !important;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* ========== STAT CARDS ========== */
        .stat-card {
            border-radius: 16px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            color: white;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
        }

        .stat-card.primary { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); }
        .stat-card.success { background: linear-gradient(135deg, var(--success) 0%, #059669 100%); }
        .stat-card.warning { background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%); }
        .stat-card.danger { background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%); }
        .stat-card.secondary { background: linear-gradient(135deg, var(--secondary) 0%, #0284c7 100%); }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .stat-trend {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
            border-radius: 20px;
            background: rgba(255,255,255,0.2);
        }

        /* ========== BUTTONS ========== */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            padding: 0.6rem 1.25rem;
            border-radius: 10px;
            font-weight: 500;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #4338ca 100%);
            transform: translateY(-1px);
        }

        /* ========== TABLES ========== */
        .table {
            margin-bottom: 0;
        }

        .table th {
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--gray-500);
            border-bottom-width: 1px;
        }

        .table td {
            vertical-align: middle;
            padding: 1rem;
        }

        /* ========== STATUS BADGES ========== */
        .badge-status {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .badge-danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .badge-warning { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .badge-info { background: rgba(14, 165, 233, 0.1); color: var(--secondary); }

        /* ========== MOBILE OVERLAY ========== */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1045;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 1199px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .topbar {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: flex;
            }
        }

        @media (max-width: 768px) {
            .content-wrapper {
                padding: 1rem;
            }

            .stat-card {
                margin-bottom: 1rem;
            }

            .topbar {
                padding: 0 1rem;
            }

            .page-title {
                font-size: 1rem;
            }

            .breadcrumb {
                display: none;
            }
        }

        /* ========== UTILITIES ========== */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .shadow-soft {
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
    </style>
    
    <?php if (isset($extraStyles)) echo $extraStyles; ?>
</head>
<body>
    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="<?= $base ?>/dashboard" class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <i class="bi bi-database"></i>
                </div>
                <div class="sidebar-brand-text">
                    DMC DataLoad
                    <small>ETL System</small>
                </div>
            </a>
        </div>

        <nav class="sidebar-menu">
            <div class="menu-label">Principal</div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/dashboard" class="menu-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <div class="menu-label">ETL</div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/conexoes" class="menu-link <?= $currentPage === 'conexoes' ? 'active' : '' ?>">
                    <i class="bi bi-hdd-network-fill"></i>
                    <span>Conexões</span>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/rotinas" class="menu-link <?= $currentPage === 'rotinas' ? 'active' : '' ?>">
                    <i class="bi bi-play-circle-fill"></i>
                    <span>Rotinas</span>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/historico" class="menu-link <?= $currentPage === 'historico' ? 'active' : '' ?>">
                    <i class="bi bi-clock-history"></i>
                    <span>Histórico</span>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/sql-editor" class="menu-link <?= $currentPage === 'sql-editor' ? 'active' : '' ?>">
                    <i class="bi bi-terminal"></i>
                    <span>SQL Editor</span>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/diagrama" class="menu-link <?= $currentPage === 'diagrama' ? 'active' : '' ?>">
                    <i class="bi bi-diagram-3"></i>
                    <span>Diagrama ER</span>
                </a>
            </div>

            <div class="menu-label">Automação</div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/apis-externas" class="menu-link <?= $currentPage === 'apis-externas' ? 'active' : '' ?>">
                    <i class="bi bi-cloud-arrow-up-fill"></i>
                    <span>APIs Externas</span>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/eventos-api" class="menu-link <?= $currentPage === 'eventos-api' ? 'active' : '' ?>">
                    <i class="bi bi-lightning-charge-fill"></i>
                    <span>Eventos de API</span>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/workflows" class="menu-link <?= $currentPage === 'workflows' ? 'active' : '' ?>">
                    <i class="bi bi-diagram-3-fill"></i>
                    <span>Workflows</span>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/workflow-execucoes" class="menu-link <?= $currentPage === 'workflow-execucoes' ? 'active' : '' ?>">
                    <i class="bi bi-play-btn-fill"></i>
                    <span>Execuções</span>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/pipelines" class="menu-link <?= $currentPage === 'pipelines' ? 'active' : '' ?>">
                    <i class="bi bi-bezier2"></i>
                    <span>Pipelines</span>
                </a>
            </div>

            <div class="menu-label">Sistema</div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/scheduler" class="menu-link <?= $currentPage === 'scheduler' ? 'active' : '' ?>">
                    <i class="bi bi-calendar-check-fill"></i>
                    <span>Agendamentos</span>
                    <span class="menu-badge" id="schedulerBadge">0</span>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/calendario" class="menu-link <?= $currentPage === 'calendario' ? 'active' : '' ?>">
                    <i class="bi bi-calendar3"></i>
                    <span>Calendário</span>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/logs" class="menu-link <?= $currentPage === 'logs' ? 'active' : '' ?>">
                    <i class="bi bi-file-text-fill"></i>
                    <span>Logs do Sistema</span>
                </a>
            </div>

            <?php if (($usuario['nivel_acesso'] ?? '') === 'admin'): ?>
            <div class="menu-label">Administração</div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/admin/usuarios" class="menu-link <?= $currentPage === 'usuarios' ? 'active' : '' ?>">
                    <i class="bi bi-people-fill"></i>
                    <span>Usuários</span>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/configuracoes" class="menu-link <?= $currentPage === 'configuracoes' ? 'active' : '' ?>">
                    <i class="bi bi-gear-fill"></i>
                    <span>Configurações</span>
                </a>
            </div>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <div class="user-card" id="userCard">
                <div class="user-avatar" id="userAvatar">
                    <?= strtoupper(substr($usuario['nome_usuario'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="user-info">
                    <div class="user-name" id="userName"><?= htmlspecialchars($usuario['nome_usuario'] ?? 'Usuário') ?></div>
                    <div class="user-role"><?= ucfirst($usuario['nivel_acesso'] ?? 'user') ?></div>
                </div>
                <div class="user-actions">
                    <button class="btn" id="btnLogout" title="Sair">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </aside>

    <!-- Topbar -->
    <header class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle">
                <i class="bi bi-list"></i>
            </button>
            <div>
                <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= $base ?>/dashboard">Home</a></li>
                        <li class="breadcrumb-item active"><?= htmlspecialchars($pageTitle) ?></li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="topbar-right">
            <button class="topbar-btn" title="Atualizar" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
            <button class="topbar-btn" title="Notificações" id="btnNotifications">
                <i class="bi bi-bell"></i>
                <span class="notification-badge" id="notificationBadge" style="display: none;"></span>
            </button>
            <button class="topbar-btn" title="Modo Escuro" id="btnTheme">
                <i class="bi bi-moon"></i>
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-wrapper">
            <?= $content ?? '' ?>
        </div>
    </main>

    <!-- Scripts Base -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const baseUrl = '<?= $base ?>';
        
        // Toggle Sidebar (Mobile)
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuToggle = document.getElementById('menuToggle');

        menuToggle?.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        overlay?.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });

        // Logout
        document.getElementById('btnLogout')?.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Deseja realmente sair?')) {
                $.post(baseUrl + '/logout', function() {
                    window.location.href = baseUrl + '/login';
                });
            }
        });

        // Load active schedules count
        function loadSchedulerBadge() {
            $.getJSON(baseUrl + '/api/scheduler/count', function(res) {
                if (res.count > 0) {
                    $('#schedulerBadge').text(res.count).show();
                } else {
                    $('#schedulerBadge').hide();
                }
            }).fail(function() {
                $('#schedulerBadge').hide();
            });
        }

        // Initialize
        $(document).ready(function() {
            loadSchedulerBadge();
        });
    </script>
    
    <?php if (isset($extraScripts)) echo $extraScripts; ?>
</body>
</html>
