<?php
/**
 * DMC DataLoad - Layout Base
 * Template responsivo com sidebar moderna
 */
$pageTitle = $pageTitle ?? 'DMC DataLoad';
$currentPage = $currentPage ?? '';
$usuario = $_SESSION['usuario'] ?? [];
$base = defined('BASE_URL') ? BASE_URL : '';

// Carregar branding configs (app_nome, app_descricao, app_favicon)
$_brandCfg = ['app_nome' => 'DMC DataLoad', 'app_descricao' => 'Database system', 'app_favicon' => ''];
try {
    $_brandDb = \App\Core\Database::getConexao();
    $_brandStmt = $_brandDb->prepare("SELECT chave, valor FROM tb_configuracoes WHERE chave IN ('app_nome', 'app_descricao', 'app_favicon')");
    $_brandStmt->execute();
    foreach ($_brandStmt->fetchAll(\PDO::FETCH_ASSOC) as $_r) {
        if ($_r['valor'] !== '' && $_r['valor'] !== null) {
            $_brandCfg[$_r['chave']] = $_r['valor'];
        }
    }
} catch (\Exception $e) {}
$appNome = $_brandCfg['app_nome'];
$appDescricao = $_brandCfg['app_descricao'];
$appFavicon = $_brandCfg['app_favicon'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1a1f2e">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= htmlspecialchars($appNome) ?></title>
    
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
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: white;
            flex: 1;
            min-width: 0;
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
            overflow-x: hidden;
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

        /* ========== MENU GROUP DROPDOWNS ========== */
        .menu-group {
            margin-bottom: 2px;
        }

        .menu-group-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.7rem 1rem;
            margin: 0 1rem;
            border-radius: 10px;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: var(--transition);
            user-select: none;
            border: none;
            background: none;
            width: calc(100% - 2rem);
            text-align: left;
        }

        .menu-group-toggle:hover {
            background: rgba(255,255,255,0.04);
            color: rgba(255,255,255,0.75);
        }

        .menu-group-toggle .group-icon {
            font-size: 1.15rem;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
        }

        .menu-group-toggle .group-label {
            flex: 1;
            min-width: 0;
        }

        .menu-group-toggle .group-chevron {
            font-size: 0.7rem;
            transition: transform 0.3s ease;
            flex-shrink: 0;
            opacity: 0.6;
        }

        .menu-group.open > .menu-group-toggle .group-chevron {
            transform: rotate(90deg);
        }

        .menu-group.open > .menu-group-toggle {
            color: rgba(255,255,255,0.8);
        }

        .menu-group-items {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                        opacity 0.25s ease;
            opacity: 0;
        }

        .menu-group.open > .menu-group-items {
            max-height: 600px;
            opacity: 1;
        }

        .menu-group-items .menu-item {
            padding: 0 0.75rem 0 1.25rem;
        }

        .menu-item {
            padding: 0 1rem;
            margin-bottom: 2px;
        }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.65rem 1rem;
            border-radius: 10px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            transition: var(--transition);
            position: relative;
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
            background: rgba(255,255,255,0.06);
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
            font-size: 1.15rem;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
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

        /* ========== BACK TO TOP ========== */
        .back-to-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.35);
            z-index: 1060;
            opacity: 0;
            visibility: hidden;
            transform: translateY(12px);
            transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
        }

        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .back-to-top:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
        }

        body.dark-mode .back-to-top {
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.25);
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
            min-width: 18px;
            height: 18px;
            background: var(--danger);
            border-radius: 9px;
            border: 2px solid white;
            color: white;
            font-size: 10px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            line-height: 1;
        }

        /* Notification Dropdown */
        .notif-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 380px;
            max-height: 480px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            z-index: 9999;
            display: none;
            overflow: hidden;
        }
        .notif-dropdown.active { display: block; }
        .notif-dropdown-header {
            padding: 14px 16px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 600;
            font-size: 14px;
        }
        .notif-dropdown-header a {
            font-size: 12px;
            font-weight: 500;
            color: var(--primary);
            text-decoration: none;
            cursor: pointer;
        }
        .notif-dropdown-header a:hover { text-decoration: underline; }
        .notif-dropdown-body {
            max-height: 360px;
            overflow-y: auto;
        }
        .notif-dropdown-body::-webkit-scrollbar { width: 4px; }
        .notif-dropdown-body::-webkit-scrollbar-thumb { background: var(--gray-300); border-radius: 4px; }
        .notif-item {
            display: flex;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--gray-100);
            cursor: pointer;
            transition: background .15s;
        }
        .notif-item:hover { background: var(--gray-50); }
        .notif-item.unread { background: rgba(59,130,246,0.04); }
        .notif-item .notif-icon {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }
        .notif-icon.falha { background: #fef2f2; color: #ef4444; }
        .notif-icon.sucesso { background: #f0fdf4; color: #22c55e; }
        .notif-icon.info { background: #eff6ff; color: #3b82f6; }
        .notif-item .notif-content { flex: 1; min-width: 0; }
        .notif-item .notif-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-800);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .notif-item .notif-msg {
            font-size: 12px;
            color: var(--gray-500);
            margin-top: 2px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .notif-item .notif-time {
            font-size: 11px;
            color: var(--gray-400);
            margin-top: 4px;
        }
        .notif-item .notif-dot {
            flex-shrink: 0;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--primary);
            margin-top: 6px;
        }
        .notif-empty {
            padding: 40px 16px;
            text-align: center;
            color: var(--gray-400);
            font-size: 13px;
        }
        .notif-empty i { font-size: 32px; display: block; margin-bottom: 8px; }
        .notif-dropdown-footer {
            padding: 10px 16px;
            border-top: 1px solid var(--gray-200);
            text-align: center;
        }
        .notif-dropdown-footer a {
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
            text-decoration: none;
        }
        .notif-dropdown-footer a:hover { text-decoration: underline; }

        body.dark-mode .notif-dropdown { background: var(--dark-card); box-shadow: 0 8px 30px rgba(0,0,0,0.4); }
        body.dark-mode .notif-dropdown-header { border-color: var(--dark-border); color: var(--dark-text); }
        body.dark-mode .notif-dropdown-footer { border-color: var(--dark-border); }
        body.dark-mode .notif-item { border-color: var(--dark-border); }
        body.dark-mode .notif-item:hover { background: rgba(255,255,255,0.04); }
        body.dark-mode .notif-item.unread { background: rgba(59,130,246,0.08); }
        body.dark-mode .notif-item .notif-title { color: var(--dark-text); }
        body.dark-mode .notif-item .notif-msg { color: var(--dark-muted); }
        body.dark-mode .notif-empty { color: var(--dark-muted); }

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

        /* ========== PAGE HEADER MODERN ========== */
        .page-header-modern {
            background: white;
            padding: 1.75rem 2rem;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .page-icon-modern {
            width: 60px;
            height: 60px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .page-title-modern {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0 0 0.15rem 0;
            color: var(--dark);
        }

        .page-subtitle-modern {
            color: var(--gray-500);
            margin: 0;
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            .page-header-modern {
                padding: 1.25rem;
                gap: 1rem;
            }
            .page-header-modern .ms-auto {
                width: 100%;
                display: flex;
                gap: 0.5rem;
            }
            .page-header-modern .ms-auto .btn {
                flex: 1;
                font-size: 0.85rem;
                padding: 0.5rem 0.75rem;
            }
            .page-icon-modern {
                width: 48px;
                height: 48px;
                font-size: 1.4rem;
            }
            .page-title-modern {
                font-size: 1.35rem;
            }
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
        .stat-card.info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }

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

        /* ========== CARD MODERN ========== */
        .card-modern {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.04);
            margin-bottom: 1.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-modern:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }

        .card-modern-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 2px solid #f3f4f6;
            font-weight: 700;
            font-size: 1.05rem;
            color: #1a202c;
            display: flex;
            align-items: center;
        }

        .card-modern-body {
            padding: 1.5rem;
        }

        .card-modern-body.p-0 {
            padding: 0 !important;
        }

        .card-modern-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* ========== TABLE MODERN ========== */
        .table-modern {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
        }

        .table-modern thead th {
            background: linear-gradient(135deg, #fafbff 0%, #f0f4ff 100%);
            color: #4b5563;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem 1.25rem;
            border-bottom: 2px solid #e5e7eb;
            border-top: none;
            white-space: nowrap;
        }

        .table-modern tbody td {
            padding: 0.875rem 1.25rem;
            vertical-align: middle;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.9375rem;
            color: #374151;
        }

        .table-modern tbody tr {
            transition: background-color 0.2s ease;
        }

        .table-modern tbody tr:hover {
            background-color: rgba(99, 102, 241, 0.04);
        }

        .table-modern tbody tr:last-child td {
            border-bottom: none;
        }

        /* DataTables com table-modern */
        .table-modern.dataTable thead th {
            background: linear-gradient(135deg, #fafbff 0%, #f0f4ff 100%) !important;
            border-bottom: 2px solid #e5e7eb !important;
        }

        .table-modern.dataTable.table-hover tbody tr:hover > * {
            --bs-table-bg-state: rgba(99, 102, 241, 0.04);
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            padding: 0.75rem 1.25rem;
        }

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

        /* ========== SIDEBAR COLLAPSE (DESKTOP) ========== */
        .sidebar-collapse-btn {
            width: 28px;
            height: 28px;
            border: none;
            background: rgba(255,255,255,0.08);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            color: var(--text-muted);
            flex-shrink: 0;
        }

        .sidebar-collapse-btn:hover {
            background: rgba(255,255,255,0.15);
            color: var(--text-light);
        }

        .sidebar-collapse-btn i {
            font-size: 0.9rem;
            transition: transform 0.3s ease;
        }

        @media (min-width: 1200px) {
            body.sidebar-collapsed .sidebar {
                width: var(--sidebar-collapsed-width);
                overflow: visible;
            }

            body.sidebar-collapsed .sidebar-brand-text,
            body.sidebar-collapsed .menu-label,
            body.sidebar-collapsed .menu-link span,
            body.sidebar-collapsed .menu-badge,
            body.sidebar-collapsed .menu-group-toggle .group-label,
            body.sidebar-collapsed .menu-group-toggle .group-chevron {
                display: none;
            }

            body.sidebar-collapsed .menu-group-toggle {
                justify-content: center;
                padding: 0.85rem 0;
                margin: 0 0.5rem;
                width: calc(100% - 1rem);
                gap: 0;
                overflow: hidden;
            }

            body.sidebar-collapsed .menu-group-toggle .group-icon {
                font-size: 1.3rem;
                margin: 0;
            }

            body.sidebar-collapsed .menu-group-items {
                max-height: 0 !important;
                opacity: 0 !important;
            }

            /* Flyout submenu on hover when collapsed */
            body.sidebar-collapsed .menu-group {
                position: relative;
            }

            body.sidebar-collapsed .menu-group:hover > .menu-group-items {
                display: block;
                position: absolute;
                left: 100%;
                top: 0;
                max-height: none !important;
                opacity: 1 !important;
                overflow: visible;
                width: 220px;
                background: var(--dark);
                border-radius: 10px;
                box-shadow: 4px 4px 20px rgba(0,0,0,0.3);
                padding: 8px 0;
                z-index: 9999;
            }

            body.sidebar-collapsed .menu-group:hover > .menu-group-items .menu-item {
                padding: 0 8px;
            }

            body.sidebar-collapsed .menu-group:hover > .menu-group-items .menu-link {
                justify-content: flex-start;
                gap: 12px;
                padding: 0.55rem 0.75rem;
            }

            body.sidebar-collapsed .menu-group:hover > .menu-group-items .menu-link i {
                font-size: 1.1rem;
            }

            body.sidebar-collapsed .menu-group:hover > .menu-group-items .menu-link span {
                display: inline;
                opacity: 1;
                width: auto;
                pointer-events: auto;
            }

            body.sidebar-collapsed .sidebar-header {
                padding: 1.5rem 0.5rem;
                justify-content: center;
                flex-direction: column;
                gap: 8px;
                overflow: hidden;
            }

            body.sidebar-collapsed .sidebar-brand {
                justify-content: center;
            }

            body.sidebar-collapsed .sidebar-brand-icon {
                width: 40px;
                height: 40px;
                border-radius: 10px;
                font-size: 1.3rem;
            }

            body.sidebar-collapsed .menu-item {
                padding: 0 0.5rem;
            }

            body.sidebar-collapsed .menu-link {
                justify-content: center;
                padding: 0.85rem 0;
                gap: 0;
                overflow: visible;
            }

            body.sidebar-collapsed .menu-link i {
                font-size: 1.3rem;
                margin: 0;
            }

            body.sidebar-collapsed .sidebar-footer {
                padding: 0.5rem;
                position: relative;
                overflow: hidden;
            }

            body.sidebar-collapsed .user-card {
                justify-content: center;
                align-items: center;
                padding: 0;
                background: transparent !important;
                gap: 0;
                position: relative;
                border-radius: 0;
                width: auto;
                max-width: 100%;
                box-sizing: border-box;
            }

            body.sidebar-collapsed .user-card:hover {
                background: transparent !important;
            }

            body.sidebar-collapsed .user-info,
            body.sidebar-collapsed .user-actions {
                display: none !important;
                width: 0 !important;
                height: 0 !important;
                overflow: hidden !important;
            }

            body.sidebar-collapsed .user-avatar {
                width: 40px;
                height: 40px;
                min-width: 40px;
                min-height: 40px;
                max-width: 40px;
                max-height: 40px;
                font-size: 0.95rem;
                flex-shrink: 0;
                line-height: 1;
                border-radius: 10px;
                margin: 0 auto;
            }

            body.sidebar-collapsed .topbar {
                left: var(--sidebar-collapsed-width);
            }

            body.sidebar-collapsed .main-content {
                margin-left: var(--sidebar-collapsed-width);
            }

            body.sidebar-collapsed .sidebar-collapse-btn i {
                transform: rotate(180deg);
            }

            body.sidebar-collapsed .sidebar-collapse-btn {
                width: 36px;
                height: 36px;
                background: rgba(255,255,255,0.12);
                border-radius: 10px;
                position: static;
                transform: none;
            }

            body.sidebar-collapsed .sidebar-collapse-btn:hover {
                background: rgba(255,255,255,0.22);
            }

            /* Tooltip on hover for collapsed items */
            body.sidebar-collapsed .menu-link {
                position: relative;
            }

            body.sidebar-collapsed .menu-link::after {
                content: attr(data-tooltip);
                position: absolute;
                left: calc(100% + 12px);
                top: 50%;
                transform: translateY(-50%);
                background: var(--dark);
                color: white;
                padding: 0.4rem 0.8rem;
                border-radius: 8px;
                font-size: 0.8rem;
                font-weight: 500;
                white-space: nowrap;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.2s ease;
                z-index: 9999;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            }

            body.sidebar-collapsed .menu-link:hover::after {
                opacity: 1;
            }

            /* Tooltip for group toggles when collapsed */
            body.sidebar-collapsed .menu-group-toggle {
                position: relative;
            }

            body.sidebar-collapsed .menu-group-toggle::after {
                content: attr(data-tooltip);
                position: absolute;
                left: calc(100% + 12px);
                top: 50%;
                transform: translateY(-50%);
                background: var(--dark);
                color: white;
                padding: 0.4rem 0.8rem;
                border-radius: 8px;
                font-size: 0.8rem;
                font-weight: 500;
                white-space: nowrap;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.2s ease;
                z-index: 9999;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            }

            body.sidebar-collapsed .menu-group-toggle:hover::after {
                opacity: 1;
            }
        }

        /* ========== USER DROPDOWN (COLLAPSED SIDEBAR) ========== */
        .user-dropdown {
            display: none;
            position: absolute;
            left: 100%;
            bottom: 8px;
            margin-left: 8px;
            min-width: 200px;
            background: var(--dark);
            border-radius: 10px;
            box-shadow: 4px 4px 20px rgba(0,0,0,0.3);
            padding: 6px 0;
            z-index: 9999;
        }

        .user-dropdown.show {
            display: block;
        }

        .user-dropdown-header {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .user-dropdown-name {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-light);
        }

        .user-dropdown-role {
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .user-dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.6rem 1rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: var(--transition);
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
        }

        .user-dropdown-item:hover {
            background: rgba(255,255,255,0.06);
            color: var(--text-light);
        }

        .user-dropdown-item i {
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }

        .user-dropdown-divider {
            height: 1px;
            background: rgba(255,255,255,0.08);
            margin: 4px 0;
        }

        .user-dropdown-item.logout-item {
            color: #f87171;
        }

        .user-dropdown-item.logout-item:hover {
            background: rgba(248, 113, 113, 0.1);
            color: #fca5a5;
        }

        body.dark-mode .user-dropdown {
            background: #0d0f18;
        }

        /* Hide collapse button on mobile (mobile uses its own toggle) */
        @media (max-width: 1199px) {
            .sidebar-collapse-btn {
                display: none !important;
            }
        }

        /* Dark mode flyout */
        body.dark-mode.sidebar-collapsed .menu-group:hover > .menu-group-items {
            background: #0d0f18;
        }

        /* ========== DARK MODE ========== */
        body.dark-mode {
            --dm-bg: #0f1117;
            --dm-bg-secondary: #1a1d2e;
            --dm-bg-card: #1e2235;
            --dm-bg-card-hover: #252a40;
            --dm-bg-input: #252a40;
            --dm-border: #2d3348;
            --dm-border-light: #363d54;
            --dm-text: #e2e8f0;
            --dm-text-secondary: #94a3b8;
            --dm-text-muted: #64748b;
            --dm-shadow: rgba(0,0,0,0.4);
            background: var(--dm-bg) !important;
            color: var(--dm-text);
        }

        /* Topbar dark */
        body.dark-mode .topbar {
            background: var(--dm-bg-secondary);
            box-shadow: 0 2px 15px var(--dm-shadow);
            border-bottom: 1px solid var(--dm-border);
        }

        body.dark-mode .page-title {
            color: var(--dm-text);
        }

        body.dark-mode .breadcrumb-item,
        body.dark-mode .breadcrumb-item a {
            color: var(--dm-text-secondary);
        }

        body.dark-mode .breadcrumb-item.active {
            color: var(--dm-text-muted);
        }

        body.dark-mode .topbar-btn {
            background: var(--dm-bg-card);
            border: 1px solid var(--dm-border);
        }

        body.dark-mode .topbar-btn:hover {
            background: var(--dm-bg-card-hover);
        }

        body.dark-mode .topbar-btn i {
            color: var(--dm-text-secondary);
        }

        body.dark-mode .menu-toggle {
            background: var(--dm-bg-card);
            border: 1px solid var(--dm-border);
        }

        body.dark-mode .menu-toggle:hover {
            background: var(--dm-bg-card-hover);
        }

        body.dark-mode .menu-toggle i {
            color: var(--dm-text-secondary);
        }

        /* Sidebar dark (already dark, just subtle adjustments) */
        body.dark-mode .sidebar {
            background: linear-gradient(180deg, #0d0f18 0%, #080a11 100%);
            box-shadow: 4px 0 25px rgba(0,0,0,0.5);
        }

        body.dark-mode .sidebar-header {
            border-bottom-color: rgba(255,255,255,0.03);
        }

        body.dark-mode .sidebar-footer {
            border-top-color: rgba(255,255,255,0.03);
        }

        /* Cards dark */
        body.dark-mode .card {
            background: var(--dm-bg-card);
            border: 1px solid var(--dm-border);
            box-shadow: 0 2px 12px var(--dm-shadow);
        }

        body.dark-mode .card-header {
            background: var(--dm-bg-secondary);
            border-bottom-color: var(--dm-border);
            color: var(--dm-text);
        }

        body.dark-mode .card-body {
            color: var(--dm-text);
        }

        body.dark-mode .card-footer {
            background: var(--dm-bg-secondary);
            border-top-color: var(--dm-border);
            color: var(--dm-text-secondary);
        }

        /* Card-modern dark */
        body.dark-mode .card-modern {
            background: var(--dm-bg-card);
            border-color: var(--dm-border);
            box-shadow: 0 2px 12px var(--dm-shadow);
        }

        body.dark-mode .card-modern-header {
            background: linear-gradient(to right, var(--dm-bg-secondary), var(--dm-bg-card));
            border-bottom-color: var(--dm-border);
            color: var(--dm-text);
        }

        body.dark-mode .card-modern-body {
            color: var(--dm-text);
        }

        body.dark-mode .card-modern-footer {
            background: var(--dm-bg-secondary);
            border-top-color: var(--dm-border);
            color: var(--dm-text-secondary);
        }

        /* Table-modern dark */
        body.dark-mode .table-modern thead th {
            background: linear-gradient(135deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0.06) 100%);
            color: var(--dm-text-secondary);
            border-bottom-color: var(--dm-border);
        }

        body.dark-mode .table-modern tbody td {
            border-bottom-color: var(--dm-border);
            color: var(--dm-text);
        }

        body.dark-mode .table-modern tbody tr:hover {
            background-color: rgba(255,255,255,0.04);
        }

        /* Tables dark */
        body.dark-mode .table {
            color: var(--dm-text);
            --bs-table-bg: transparent;
            --bs-table-striped-bg: rgba(255,255,255,0.02);
            --bs-table-hover-bg: rgba(255,255,255,0.04);
        }

        body.dark-mode .table th {
            color: var(--dm-text-secondary);
            border-color: var(--dm-border);
        }

        body.dark-mode .table td {
            border-color: var(--dm-border);
        }

        body.dark-mode .table-striped > tbody > tr:nth-of-type(odd) > * {
            --bs-table-bg-type: rgba(255,255,255,0.02);
        }

        body.dark-mode .table-hover > tbody > tr:hover > * {
            --bs-table-bg-state: rgba(255,255,255,0.04);
        }

        /* Forms dark */
        body.dark-mode .form-control,
        body.dark-mode .form-select {
            background-color: var(--dm-bg-input);
            border-color: var(--dm-border);
            color: var(--dm-text);
        }

        body.dark-mode .form-control:focus,
        body.dark-mode .form-select:focus {
            background-color: var(--dm-bg-card-hover);
            border-color: var(--primary);
            color: var(--dm-text);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
        }

        body.dark-mode .form-control::placeholder {
            color: var(--dm-text-muted);
        }

        body.dark-mode .form-control-modern,
        body.dark-mode .form-select-modern {
            background-color: var(--dm-bg-input);
            border-color: var(--dm-border);
            color: var(--dm-text);
        }

        body.dark-mode .form-control-modern:focus,
        body.dark-mode .form-select-modern:focus {
            background-color: var(--dm-bg-card-hover);
            border-color: var(--primary);
            color: var(--dm-text);
        }

        body.dark-mode .form-label,
        body.dark-mode .form-label-modern,
        body.dark-mode .form-check-label,
        body.dark-mode label {
            color: var(--dm-text);
        }

        body.dark-mode .form-check-input {
            background-color: var(--dm-bg-input);
            border-color: var(--dm-border-light);
        }

        body.dark-mode .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        body.dark-mode .input-group-text {
            background-color: var(--dm-bg-secondary);
            border-color: var(--dm-border);
            color: var(--dm-text-secondary);
        }

        /* Text helpers dark */
        body.dark-mode .text-muted {
            color: var(--dm-text-muted) !important;
        }

        body.dark-mode .text-dark {
            color: var(--dm-text) !important;
        }

        body.dark-mode .text-secondary {
            color: var(--dm-text-secondary) !important;
        }

        body.dark-mode h1, body.dark-mode h2, body.dark-mode h3,
        body.dark-mode h4, body.dark-mode h5, body.dark-mode h6 {
            color: var(--dm-text);
        }

        body.dark-mode p {
            color: var(--dm-text-secondary);
        }

        body.dark-mode a:not(.btn):not(.menu-link):not(.sidebar-brand):not(.nav-link):not(.list-group-item):not(.page-link) {
            color: var(--primary-light);
        }

        /* Modals dark */
        body.dark-mode .modal-content {
            background: var(--dm-bg-card);
            border-color: var(--dm-border);
        }

        body.dark-mode .modal-header {
            border-bottom-color: var(--dm-border);
            color: var(--dm-text);
        }

        body.dark-mode .modal-header .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        body.dark-mode .modal-footer {
            border-top-color: var(--dm-border);
        }

        body.dark-mode .modal-title {
            color: var(--dm-text);
        }

        body.dark-mode .modal-body {
            color: var(--dm-text);
        }

        /* Dropdowns dark */
        body.dark-mode .dropdown-menu {
            background: var(--dm-bg-card);
            border-color: var(--dm-border);
            box-shadow: 0 8px 24px var(--dm-shadow);
        }

        body.dark-mode .dropdown-item {
            color: var(--dm-text);
        }

        body.dark-mode .dropdown-item:hover,
        body.dark-mode .dropdown-item:focus {
            background: var(--dm-bg-card-hover);
            color: var(--dm-text);
        }

        body.dark-mode .dropdown-divider {
            border-color: var(--dm-border);
        }

        /* Alerts dark */
        body.dark-mode .alert-info {
            background: rgba(14,165,233,0.1);
            border-color: rgba(14,165,233,0.2);
            color: #7dd3fc;
        }

        body.dark-mode .alert-success {
            background: rgba(16,185,129,0.1);
            border-color: rgba(16,185,129,0.2);
            color: #6ee7b7;
        }

        body.dark-mode .alert-warning {
            background: rgba(245,158,11,0.1);
            border-color: rgba(245,158,11,0.2);
            color: #fcd34d;
        }

        body.dark-mode .alert-danger {
            background: rgba(239,68,68,0.1);
            border-color: rgba(239,68,68,0.2);
            color: #fca5a5;
        }

        /* List group dark */
        body.dark-mode .list-group-item {
            background: var(--dm-bg-card);
            border-color: var(--dm-border);
            color: var(--dm-text);
        }

        body.dark-mode .list-group-item:hover {
            background: var(--dm-bg-card-hover);
        }

        body.dark-mode .list-group-item.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-color: var(--primary);
            color: white;
        }

        body.dark-mode .list-group-item-action {
            color: var(--dm-text);
        }

        body.dark-mode .list-group-item-action:hover {
            background: var(--dm-bg-card-hover);
            color: var(--dm-text);
        }

        /* Pagination dark */
        body.dark-mode .page-item .page-link {
            background: var(--dm-bg-card);
            border-color: var(--dm-border);
            color: var(--dm-text);
        }

        body.dark-mode .page-item .page-link:hover {
            background: var(--dm-bg-card-hover);
        }

        body.dark-mode .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
        }

        body.dark-mode .page-item.disabled .page-link {
            background: var(--dm-bg-secondary);
            color: var(--dm-text-muted);
        }

        /* Nav tabs & pills dark */
        body.dark-mode .nav-tabs {
            border-bottom-color: var(--dm-border);
        }

        body.dark-mode .nav-tabs .nav-link {
            color: var(--dm-text-secondary);
        }

        body.dark-mode .nav-tabs .nav-link:hover {
            border-color: var(--dm-border) var(--dm-border) transparent;
        }

        body.dark-mode .nav-tabs .nav-link.active {
            background: var(--dm-bg-card);
            border-color: var(--dm-border) var(--dm-border) var(--dm-bg-card);
            color: var(--dm-text);
        }

        body.dark-mode .nav-pills .nav-link {
            color: var(--dm-text-secondary);
        }

        body.dark-mode .nav-pills .nav-link.active {
            background: var(--primary);
            color: white;
        }

        /* Buttons dark (outline variants) */
        body.dark-mode .btn-outline-primary {
            color: var(--primary-light);
            border-color: var(--primary);
        }

        body.dark-mode .btn-outline-secondary {
            color: var(--dm-text-secondary);
            border-color: var(--dm-border-light);
        }

        body.dark-mode .btn-outline-secondary:hover {
            background: var(--dm-bg-card-hover);
            color: var(--dm-text);
        }

        body.dark-mode .btn-outline-info {
            color: #38bdf8;
            border-color: #0ea5e9;
        }

        body.dark-mode .btn-outline-warning {
            color: #fbbf24;
            border-color: #f59e0b;
        }

        body.dark-mode .btn-outline-danger {
            color: #f87171;
            border-color: #ef4444;
        }

        body.dark-mode .btn-light {
            background: var(--dm-bg-card);
            border-color: var(--dm-border);
            color: var(--dm-text);
        }

        body.dark-mode .btn-light:hover {
            background: var(--dm-bg-card-hover);
        }

        /* Page header modern dark */
        body.dark-mode .page-header-modern {
            background: var(--dm-bg-card);
            box-shadow: 0 2px 12px var(--dm-shadow);
            border: 1px solid var(--dm-border);
        }

        body.dark-mode .page-title-modern {
            color: var(--dm-text);
        }

        body.dark-mode .page-subtitle-modern {
            color: var(--dm-text-muted);
        }

        /* Stat cards - keep gradient backgrounds, adjust overlays */
        body.dark-mode .stat-card {
            box-shadow: 0 4px 16px var(--dm-shadow);
        }

        /* Badges dark */
        body.dark-mode .badge.bg-light {
            background: var(--dm-bg-card-hover) !important;
            color: var(--dm-text) !important;
        }

        /* HR dark */
        body.dark-mode hr {
            border-color: var(--dm-border);
            opacity: 0.5;
        }

        /* Close button dark */
        body.dark-mode .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        /* Accordion dark */
        body.dark-mode .accordion-item {
            background: var(--dm-bg-card);
            border-color: var(--dm-border);
        }

        body.dark-mode .accordion-button {
            background: var(--dm-bg-card);
            color: var(--dm-text);
        }

        body.dark-mode .accordion-button:not(.collapsed) {
            background: var(--dm-bg-card-hover);
            color: var(--dm-text);
        }

        body.dark-mode .accordion-button::after {
            filter: invert(1) brightness(2);
        }

        body.dark-mode .accordion-body {
            background: var(--dm-bg-card);
            color: var(--dm-text);
        }

        /* Tooltip & Popover dark */
        body.dark-mode .tooltip-inner {
            background: var(--dm-bg-card);
            color: var(--dm-text);
            border: 1px solid var(--dm-border);
        }

        /* CodeMirror / Code blocks */
        body.dark-mode pre, body.dark-mode code {
            background: var(--dm-bg-secondary);
            color: #e2e8f0;
            border-color: var(--dm-border);
        }

        /* Scrollbar dark */
        body.dark-mode ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        body.dark-mode ::-webkit-scrollbar-track {
            background: var(--dm-bg);
        }

        body.dark-mode ::-webkit-scrollbar-thumb {
            background: var(--dm-border-light);
            border-radius: 4px;
        }

        body.dark-mode ::-webkit-scrollbar-thumb:hover {
            background: #4b5563;
        }

        /* SweetAlert2 dark overrides */
        body.dark-mode .swal2-popup {
            background: var(--dm-bg-card) !important;
            color: var(--dm-text) !important;
        }

        body.dark-mode .swal2-title {
            color: var(--dm-text) !important;
        }

        body.dark-mode .swal2-html-container {
            color: var(--dm-text-secondary) !important;
        }

        body.dark-mode .swal2-input,
        body.dark-mode .swal2-select,
        body.dark-mode .swal2-textarea {
            background: var(--dm-bg-input) !important;
            border-color: var(--dm-border) !important;
            color: var(--dm-text) !important;
        }

        body.dark-mode .swal2-validation-message {
            background: var(--dm-bg-secondary) !important;
            color: var(--dm-text) !important;
        }

        /* Selection color */
        body.dark-mode ::selection {
            background: rgba(99,102,241,0.3);
            color: white;
        }

        /* Dark mode theme button active state */
        body.dark-mode #btnTheme {
            background: rgba(99,102,241,0.15);
            border: 1px solid rgba(99,102,241,0.3);
        }

        body.dark-mode #btnTheme i {
            color: #fbbf24;
        }

        /* Notification badge in dark mode */
        body.dark-mode .notification-badge {
            border-color: var(--dm-bg-secondary);
        }

        /* Dark mode transition for smooth toggle */
        body.dark-mode-transition,
        body.dark-mode-transition *,
        body.dark-mode-transition *::before,
        body.dark-mode-transition *::after {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease !important;
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
    
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <?php if ($appFavicon): ?>
    <link rel="icon" href="<?= $base . htmlspecialchars($appFavicon) ?>" type="image/x-icon">
    <?php endif; ?>
    
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
                    <?php if ($appFavicon): ?>
                        <img src="<?= $base . htmlspecialchars($appFavicon) ?>" alt="Logo" style="width:28px;height:28px;object-fit:contain;">
                    <?php else: ?>
                        <i class="bi bi-database"></i>
                    <?php endif; ?>
                </div>
                <div class="sidebar-brand-text">
                    <?= htmlspecialchars($appNome) ?>
                    <small><?= htmlspecialchars($appDescricao) ?></small>
                </div>
            </a>
            <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="Recolher menu">
                <i class="bi bi-chevron-left"></i>
            </button>
        </div>

        <nav class="sidebar-menu">
            <!-- DASHBOARD (todos) -->
            <div class="menu-item" style="margin-top: 4px;">
                <a href="<?= $base ?>/dashboard" class="menu-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" data-tooltip="Dashboard">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <?php 
            $nivelAcesso = $usuario['nivel_acesso'] ?? 'operador';
            $ehSuperAdminSidebar = ($nivelAcesso === 'super_admin');
            $ehAdminSidebar = in_array($nivelAcesso, ['admin', 'super_admin']);
            $ehDevOuSuperior = in_array($nivelAcesso, ['desenvolvedor', 'admin', 'super_admin']);
            $ehOperador = ($nivelAcesso === 'operador');
            ?>

            <!-- CONEXÕES (dev+) -->
            <?php if ($ehDevOuSuperior): ?>
            <div class="menu-item">
                <a href="<?= $base ?>/conexoes" class="menu-link <?= $currentPage === 'conexoes' ? 'active' : '' ?>" data-tooltip="Conexões">
                    <i class="bi bi-hdd-network-fill"></i>
                    <span>Conexões</span>
                </a>
            </div>
            <?php endif; ?>

            <!-- ETL (dev+) -->
            <?php if ($ehDevOuSuperior): ?>
            <div class="menu-group" data-group="etl">
                <button class="menu-group-toggle" data-tooltip="ETL">
                    <i class="bi bi-database-gear group-icon"></i>
                    <span class="group-label">ETL</span>
                    <i class="bi bi-chevron-right group-chevron"></i>
                </button>
                <div class="menu-group-items">
                    <div class="menu-item">
                        <a href="<?= $base ?>/rotinas" class="menu-link <?= $currentPage === 'rotinas' ? 'active' : '' ?>" data-tooltip="Rotinas">
                            <i class="bi bi-play-circle-fill"></i>
                            <span>Rotinas</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a href="<?= $base ?>/pipelines" class="menu-link <?= $currentPage === 'pipelines' ? 'active' : '' ?>" data-tooltip="Pipelines">
                            <i class="bi bi-bezier2"></i>
                            <span>Pipelines</span>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- SQL (dev+ vê tudo, operador vê só Diagrama ER) -->
            <?php if ($ehDevOuSuperior): ?>
            <div class="menu-group" data-group="sql">
                <button class="menu-group-toggle" data-tooltip="SQL">
                    <i class="bi bi-terminal group-icon"></i>
                    <span class="group-label">SQL</span>
                    <i class="bi bi-chevron-right group-chevron"></i>
                </button>
                <div class="menu-group-items">
                    <div class="menu-item">
                        <a href="<?= $base ?>/sql-editor" class="menu-link <?= $currentPage === 'sql-editor' ? 'active' : '' ?>" data-tooltip="SQL Editor">
                            <i class="bi bi-terminal"></i>
                            <span>SQL Editor</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a href="<?= $base ?>/diagrama" class="menu-link <?= $currentPage === 'diagrama' ? 'active' : '' ?>" data-tooltip="Diagrama ER">
                            <i class="bi bi-diagram-3"></i>
                            <span>Diagrama ER</span>
                        </a>
                    </div>
                </div>
            </div>
            <?php elseif ($ehOperador): ?>
            <div class="menu-group" data-group="sql">
                <button class="menu-group-toggle" data-tooltip="SQL">
                    <i class="bi bi-terminal group-icon"></i>
                    <span class="group-label">SQL</span>
                    <i class="bi bi-chevron-right group-chevron"></i>
                </button>
                <div class="menu-group-items">
                    <div class="menu-item">
                        <a href="<?= $base ?>/diagrama" class="menu-link <?= $currentPage === 'diagrama' ? 'active' : '' ?>" data-tooltip="Diagrama ER">
                            <i class="bi bi-diagram-3"></i>
                            <span>Diagrama ER</span>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- AGENDA (todos) -->
            <div class="menu-group" data-group="agenda">
                <button class="menu-group-toggle" data-tooltip="Agenda">
                    <i class="bi bi-calendar-check group-icon"></i>
                    <span class="group-label">Agenda</span>
                    <i class="bi bi-chevron-right group-chevron"></i>
                </button>
                <div class="menu-group-items">
                    <div class="menu-item">
                        <a href="<?= $base ?>/scheduler" class="menu-link <?= $currentPage === 'scheduler' ? 'active' : '' ?>" data-tooltip="Agendamentos">
                            <i class="bi bi-calendar-check-fill"></i>
                            <span>Agendamentos</span>
                            <span class="menu-badge" id="schedulerBadge">0</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a href="<?= $base ?>/calendario" class="menu-link <?= $currentPage === 'calendario' ? 'active' : '' ?>" data-tooltip="Calendário">
                            <i class="bi bi-calendar3"></i>
                            <span>Calendário</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- REGISTROS (misto: Histórico e Notificações = todos; Logs = dev+; Fila/Auditoria/Arquivos = admin+) -->
            <div class="menu-group" data-group="registros">
                <button class="menu-group-toggle" data-tooltip="Registros">
                    <i class="bi bi-clock-history group-icon"></i>
                    <span class="group-label">Registros</span>
                    <i class="bi bi-chevron-right group-chevron"></i>
                </button>
                <div class="menu-group-items">
                    <div class="menu-item">
                        <a href="<?= $base ?>/historico" class="menu-link <?= $currentPage === 'historico' ? 'active' : '' ?>" data-tooltip="Histórico">
                            <i class="bi bi-clock-history"></i>
                            <span>Histórico</span>
                        </a>
                    </div>
                    <?php if ($ehDevOuSuperior): ?>
                    <div class="menu-item">
                        <a href="<?= $base ?>/logs" class="menu-link <?= $currentPage === 'logs' ? 'active' : '' ?>" data-tooltip="Logs do Sistema">
                            <i class="bi bi-file-text-fill"></i>
                            <span>Logs do Sistema</span>
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php if ($ehAdminSidebar): ?>
                    <div class="menu-item">
                        <a href="<?= $base ?>/admin/fila" class="menu-link <?= $currentPage === 'fila' ? 'active' : '' ?>" data-tooltip="Fila Execução">
                            <i class="bi bi-collection"></i>
                            <span>Fila Execução</span>
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php if ($ehSuperAdminSidebar): ?>
                    <div class="menu-item">
                        <a href="<?= $base ?>/admin/auditoria" class="menu-link <?= $currentPage === 'auditoria' ? 'active' : '' ?>" data-tooltip="Auditoria">
                            <i class="bi bi-shield-check"></i>
                            <span>Auditoria</span>
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php if ($ehAdminSidebar): ?>
                    <div class="menu-item">
                        <a href="<?= $base ?>/arquivos-gerados" class="menu-link <?= $currentPage === 'arquivos-gerados' ? 'active' : '' ?>" data-tooltip="Arquivos Gerados">
                            <i class="bi bi-folder2-open"></i>
                            <span>Arquivos Gerados</span>
                        </a>
                    </div>
                    <?php endif; ?>
                    <div class="menu-item">
                        <a href="<?= $base ?>/notificacoes" class="menu-link <?= $currentPage === 'notificacoes' ? 'active' : '' ?>" data-tooltip="Notificações">
                            <i class="bi bi-bell-fill"></i>
                            <span>Notificações</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- APLICAÇÃO (admin+, com itens super_admin only) -->
            <?php if ($ehAdminSidebar): ?>
            <div class="menu-group" data-group="aplicacao">
                <button class="menu-group-toggle" data-tooltip="Aplicação">
                    <i class="bi bi-shield-lock group-icon"></i>
                    <span class="group-label">Aplicação</span>
                    <i class="bi bi-chevron-right group-chevron"></i>
                </button>
                <div class="menu-group-items">
                    <div class="menu-item">
                        <a href="<?= $base ?>/admin/usuarios" class="menu-link <?= $currentPage === 'usuarios' ? 'active' : '' ?>" data-tooltip="Usuários">
                            <i class="bi bi-people-fill"></i>
                            <span>Usuários</span>
                        </a>
                    </div>
                    <?php if ($ehSuperAdminSidebar): ?>
                    <div class="menu-item">
                        <a href="<?= $base ?>/admin/empresas" class="menu-link <?= $currentPage === 'empresas' ? 'active' : '' ?>" data-tooltip="Empresas">
                            <i class="bi bi-building"></i>
                            <span>Empresas</span>
                        </a>
                    </div>
                    <?php endif; ?>
                    <div class="menu-item">
                        <a href="<?= $base ?>/admin/projetos" class="menu-link <?= $currentPage === 'projetos' ? 'active' : '' ?>" data-tooltip="Projetos">
                            <i class="bi bi-folder-fill"></i>
                            <span>Projetos</span>
                        </a>
                    </div>
                    <?php if ($ehSuperAdminSidebar): ?>
                    <div class="menu-item">
                        <a href="<?= $base ?>/configuracoes" class="menu-link <?= $currentPage === 'configuracoes' ? 'active' : '' ?>" data-tooltip="Configurações">
                            <i class="bi bi-gear-fill"></i>
                            <span>Configurações</span>
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php if ($ehSuperAdminSidebar): ?>
                    <div class="menu-item">
                        <a href="<?= $base ?>/admin/webhooks" class="menu-link <?= $currentPage === 'webhooks' ? 'active' : '' ?>" data-tooltip="Webhooks">
                            <i class="bi bi-broadcast"></i>
                            <span>Webhooks</span>
                        </a>
                    </div>
                    <?php endif; ?>
                    <div class="menu-item">
                        <a href="<?= $base ?>/admin/canais" class="menu-link <?= $currentPage === 'canais' ? 'active' : '' ?>" data-tooltip="Slack/Teams">
                            <i class="bi bi-chat-dots"></i>
                            <span>Slack/Teams</span>
                        </a>
                    </div>
                    <?php if ($ehSuperAdminSidebar): ?>
                    <div class="menu-item">
                        <a href="<?= $base ?>/admin/backups" class="menu-link <?= $currentPage === 'backups' ? 'active' : '' ?>" data-tooltip="Backups">
                            <i class="bi bi-cloud-download"></i>
                            <span>Backups</span>
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php if ($ehDevOuSuperior): ?>
                    <div class="menu-item">
                        <a href="<?= $base ?>/apis-externas" class="menu-link <?= $currentPage === 'apis-externas' ? 'active' : '' ?>" data-tooltip="APIs Externas">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                            <span>APIs Externas</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a href="<?= $base ?>/eventos-api" class="menu-link <?= $currentPage === 'eventos-api' ? 'active' : '' ?>" data-tooltip="Eventos de API">
                            <i class="bi bi-broadcast-pin"></i>
                            <span>Eventos de API</span>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <div class="user-card" id="userCard" style="cursor:pointer" title="Meu Perfil">
                <div class="user-avatar" id="userAvatar">
                    <?= strtoupper(substr($usuario['nome_usuario'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="user-info">
                    <div class="user-name" id="userName"><?= htmlspecialchars($usuario['nome_usuario'] ?? 'Usuário') ?></div>
                    <div class="user-role"><?php
                        $labelsPapeis = ['super_admin' => 'Super Admin', 'admin' => 'Administrador', 'desenvolvedor' => 'Desenvolvedor', 'operador' => 'Operador'];
                        echo $labelsPapeis[$usuario['nivel_acesso'] ?? 'operador'] ?? ucfirst($usuario['nivel_acesso'] ?? 'user');
                    ?></div>
                </div>
                <div class="user-actions">
                    <button class="btn" id="btnLogout" title="Sair">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="user-dropdown" id="userDropdown">
            <div class="user-dropdown-header">
                <div class="user-dropdown-name"><?= htmlspecialchars($usuario['nome_usuario'] ?? 'Usuário') ?></div>
                <div class="user-dropdown-role"><?= $labelsPapeis[$usuario['nivel_acesso'] ?? 'operador'] ?? ucfirst($usuario['nivel_acesso'] ?? 'user') ?></div>
            </div>
            <a href="<?= $base ?>/meu-perfil" class="user-dropdown-item">
                <i class="bi bi-person"></i>
                <span>Meu Perfil</span>
            </a>
            <div class="user-dropdown-divider"></div>
            <button class="user-dropdown-item logout-item" id="btnDropdownLogout">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sair</span>
            </button>
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
            <div style="position: relative;">
                <button class="topbar-btn" title="Notificações" id="btnNotifications">
                    <i class="bi bi-bell"></i>
                    <span class="notification-badge" id="notificationBadge" style="display: none;"></span>
                </button>
                <div class="notif-dropdown" id="notifDropdown">
                    <div class="notif-dropdown-header">
                        <span>Notificações</span>
                        <a id="notifMarkAllRead">Marcar todas como lidas</a>
                    </div>
                    <div class="notif-dropdown-body" id="notifDropdownBody">
                        <div class="notif-empty"><i class="bi bi-bell-slash"></i>Nenhuma notificação</div>
                    </div>
                    <div class="notif-dropdown-footer">
                        <a href="<?= $base ?>/notificacoes">Ver todas as notificações</a>
                    </div>
                </div>
            </div>
            <button class="topbar-btn" title="Modo Escuro" id="btnTheme">
                <i class="bi bi-moon" id="themeIcon"></i>
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="content-wrapper">
            <?= $content ?? '' ?>
        </div>
    </main>

    <!-- Back to Top -->
    <button class="back-to-top" id="backToTop" title="Voltar ao topo">
        <i class="bi bi-chevron-up"></i>
    </button>

    <!-- Scripts Base -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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

        // Toggle Sidebar Collapse (Desktop)
        const collapseBtn = document.getElementById('sidebarCollapseBtn');
        
        // Restore collapsed state from localStorage
        if (localStorage.getItem('sidebarCollapsed') === '1') {
            document.body.classList.add('sidebar-collapsed');
        }

        collapseBtn?.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-collapsed');
            const isCollapsed = document.body.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed ? '1' : '0');
            collapseBtn.title = isCollapsed ? 'Expandir menu' : 'Recolher menu';
        });

        // ========== MENU GROUP TOGGLE ==========
        document.querySelectorAll('.menu-group-toggle').forEach(function(btn) {
            btn.addEventListener('click', function() {
                // If sidebar is collapsed on desktop, expand it first
                if (document.body.classList.contains('sidebar-collapsed') && window.innerWidth >= 1200) {
                    document.body.classList.remove('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed', '0');
                }
                var group = this.closest('.menu-group');
                var isOpen = group.classList.contains('open');
                // Close all other groups
                document.querySelectorAll('.menu-group.open').forEach(function(g) {
                    if (g !== group) g.classList.remove('open');
                });
                group.classList.toggle('open', !isOpen);
                // Save open groups
                saveMenuGroupState();
            });
        });

        function saveMenuGroupState() {
            var openGroups = [];
            document.querySelectorAll('.menu-group.open').forEach(function(g) {
                openGroups.push(g.dataset.group);
            });
            localStorage.setItem('menuGroupsOpen', JSON.stringify(openGroups));
        }

        // Restore open groups + auto-open group with active page
        (function() {
            var saved = [];
            try { saved = JSON.parse(localStorage.getItem('menuGroupsOpen') || '[]'); } catch(e) {}
            var activeOpened = false;
            document.querySelectorAll('.menu-group').forEach(function(g) {
                if (g.querySelector('.menu-link.active')) {
                    g.classList.add('open');
                    activeOpened = true;
                } else if (saved.indexOf(g.dataset.group) !== -1) {
                    g.classList.add('open');
                }
            });
        })();

        // ========== BACK TO TOP ==========
        var backToTopBtn = document.getElementById('backToTop');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopBtn.classList.add('visible');
            } else {
                backToTopBtn.classList.remove('visible');
            }
        });
        backToTopBtn?.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Dark Mode Toggle
        const btnTheme = document.getElementById('btnTheme');
        const themeIcon = document.getElementById('themeIcon');

        function applyDarkMode(enabled) {
            if (enabled) {
                document.body.classList.add('dark-mode');
                themeIcon.classList.remove('bi-moon');
                themeIcon.classList.add('bi-sun');
                btnTheme.title = 'Modo Claro';
            } else {
                document.body.classList.remove('dark-mode');
                themeIcon.classList.remove('bi-sun');
                themeIcon.classList.add('bi-moon');
                btnTheme.title = 'Modo Escuro';
            }
        }

        // Restore dark mode from localStorage
        if (localStorage.getItem('darkMode') === '1') {
            applyDarkMode(true);
        }

        btnTheme?.addEventListener('click', () => {
            document.body.classList.add('dark-mode-transition');
            const enabling = !document.body.classList.contains('dark-mode');
            applyDarkMode(enabling);
            localStorage.setItem('darkMode', enabling ? '1' : '0');
            setTimeout(() => document.body.classList.remove('dark-mode-transition'), 400);
        });

        // Logout handler (shared)
        function doLogout() {
            if (confirm('Deseja realmente sair?')) {
                $.post(baseUrl + '/logout', function() {
                    window.location.href = baseUrl + '/login';
                });
            }
        }

        document.getElementById('btnLogout')?.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            doLogout();
        });

        document.getElementById('btnDropdownLogout')?.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            doLogout();
        });

        // User dropdown (collapsed sidebar)
        const userDropdown = document.getElementById('userDropdown');

        document.getElementById('userCard')?.addEventListener('click', function(e) {
            if (e.target.closest('#btnLogout')) return;
            // If sidebar is collapsed on desktop, show dropdown
            if (document.body.classList.contains('sidebar-collapsed') && window.innerWidth >= 1200) {
                e.preventDefault();
                e.stopPropagation();
                userDropdown.classList.toggle('show');
                return;
            }
            window.location.href = baseUrl + '/meu-perfil';
        });

        // Close dropdown on click outside
        document.addEventListener('click', function(e) {
            if (userDropdown && !e.target.closest('.sidebar-footer') && !e.target.closest('.user-dropdown')) {
                userDropdown.classList.remove('show');
            }
        });

        // Close dropdown when sidebar expands
        collapseBtn?.addEventListener('click', function() {
            if (userDropdown) userDropdown.classList.remove('show');
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

        // Load notification count
        function loadNotificationBadge() {
            $.getJSON(baseUrl + '/api/notificacoes/count', function(res) {
                if (res.count > 0) {
                    $('#notificationBadge').text(res.count > 99 ? '99+' : res.count).show();
                } else {
                    $('#notificationBadge').hide();
                }
            }).fail(function() {
                $('#notificationBadge').hide();
            });
        }

        // Notification dropdown
        function notifTimeAgo(dateStr) {
            var now = new Date(), d = new Date(dateStr);
            var diff = Math.floor((now - d) / 1000);
            if (diff < 60) return 'agora';
            if (diff < 3600) return Math.floor(diff / 60) + 'min atrás';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h atrás';
            if (diff < 604800) return Math.floor(diff / 86400) + 'd atrás';
            return d.toLocaleDateString('pt-BR');
        }

        function notifIcon(tipo) {
            if (tipo.includes('falha')) return '<div class="notif-icon falha"><i class="bi bi-exclamation-triangle-fill"></i></div>';
            if (tipo.includes('sucesso')) return '<div class="notif-icon sucesso"><i class="bi bi-check-circle-fill"></i></div>';
            return '<div class="notif-icon info"><i class="bi bi-info-circle-fill"></i></div>';
        }

        function loadNotifDropdown() {
            $.getJSON(baseUrl + '/api/notificacoes/list?limite=8', function(res) {
                var body = $('#notifDropdownBody');
                if (!res.sucesso || !res.dados || res.dados.length === 0) {
                    body.html('<div class="notif-empty"><i class="bi bi-bell-slash"></i>Nenhuma notificação</div>');
                    return;
                }
                var html = '';
                res.dados.forEach(function(n) {
                    html += '<div class="notif-item' + (n.lida ? '' : ' unread') + '" data-id="' + n.id + '">'
                        + notifIcon(n.tipo)
                        + '<div class="notif-content">'
                        + '<div class="notif-title">' + $('<span>').text(n.titulo).html() + '</div>'
                        + '<div class="notif-msg">' + $('<span>').text(n.mensagem || '').html() + '</div>'
                        + '<div class="notif-time">' + notifTimeAgo(n.created_at) + '</div>'
                        + '</div>'
                        + (n.lida ? '' : '<div class="notif-dot"></div>')
                        + '</div>';
                });
                body.html(html);
            });
        }

        $(document).on('click', '#btnNotifications', function(e) {
            e.stopPropagation();
            var dd = $('#notifDropdown');
            if (dd.hasClass('active')) {
                dd.removeClass('active');
            } else {
                loadNotifDropdown();
                dd.addClass('active');
            }
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#notifDropdown, #btnNotifications').length) {
                $('#notifDropdown').removeClass('active');
            }
        });

        $(document).on('click', '.notif-item', function() {
            var id = $(this).data('id');
            var el = $(this);
            if (el.hasClass('unread')) {
                $.post(baseUrl + '/api/notificacoes/lida/' + id);
                el.removeClass('unread').find('.notif-dot').remove();
                loadNotificationBadge();
            }
        });

        $(document).on('click', '#notifMarkAllRead', function(e) {
            e.preventDefault();
            $.post(baseUrl + '/api/notificacoes/lida-todas', function() {
                loadNotificationBadge();
                loadNotifDropdown();
            });
        });

        // Initialize
        $(document).ready(function() {
            loadSchedulerBadge();
            loadNotificationBadge();
            // Refresh notification count every 30 seconds
            setInterval(loadNotificationBadge, 30000);
        });
    </script>
    
    <?php if (isset($extraScripts)) echo $extraScripts; ?>
</body>
</html>
