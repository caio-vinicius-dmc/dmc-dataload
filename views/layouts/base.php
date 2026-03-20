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
            }

            body.sidebar-collapsed .sidebar-brand-text,
            body.sidebar-collapsed .menu-label,
            body.sidebar-collapsed .menu-link span,
            body.sidebar-collapsed .menu-badge,
            body.sidebar-collapsed .user-info,
            body.sidebar-collapsed .user-actions {
                opacity: 0;
                width: 0;
                overflow: hidden;
                white-space: nowrap;
                pointer-events: none;
                transition: opacity 0.2s ease, width 0.2s ease;
            }

            body.sidebar-collapsed .sidebar-header {
                padding: 1.5rem 0.5rem;
                justify-content: center;
                flex-direction: column;
                gap: 8px;
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
                padding: 0.85rem 0.5rem;
            }

            body.sidebar-collapsed .menu-link i {
                font-size: 1.3rem;
                margin: 0;
            }

            body.sidebar-collapsed .sidebar-footer {
                padding: 0.75rem 0.5rem;
            }

            body.sidebar-collapsed .user-card {
                justify-content: center;
                padding: 0;
                background: transparent;
                gap: 0;
            }

            body.sidebar-collapsed .user-avatar {
                width: 40px;
                height: 40px;
                min-width: 40px;
                min-height: 40px;
                font-size: 0.95rem;
                flex-shrink: 0;
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
        }

        /* Hide collapse button on mobile (mobile uses its own toggle) */
        @media (max-width: 1199px) {
            .sidebar-collapse-btn {
                display: none !important;
            }
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
                    DMC - DataLoad
                    <small>Database system</small>
                </div>
            </a>
            <button class="sidebar-collapse-btn" id="sidebarCollapseBtn" title="Recolher menu">
                <i class="bi bi-chevron-left"></i>
            </button>
        </div>

        <nav class="sidebar-menu">
            <div class="menu-label">Principal</div>
            
            <div class="menu-item">
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

            <?php if ($ehDevOuSuperior): ?>
            <div class="menu-label">ETL</div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/conexoes" class="menu-link <?= $currentPage === 'conexoes' ? 'active' : '' ?>" data-tooltip="Conexões">
                    <i class="bi bi-hdd-network-fill"></i>
                    <span>Conexões</span>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/rotinas" class="menu-link <?= $currentPage === 'rotinas' ? 'active' : '' ?>" data-tooltip="Rotinas">
                    <i class="bi bi-play-circle-fill"></i>
                    <span>Rotinas</span>
                </a>
            </div>
            <?php endif; ?>
            
            <div class="menu-item">
                <a href="<?= $base ?>/historico" class="menu-link <?= $currentPage === 'historico' ? 'active' : '' ?>" data-tooltip="Histórico">
                    <i class="bi bi-clock-history"></i>
                    <span>Histórico</span>
                </a>
            </div>
            
            <?php if ($ehDevOuSuperior): ?>
            <div class="menu-item">
                <a href="<?= $base ?>/sql-editor" class="menu-link <?= $currentPage === 'sql-editor' ? 'active' : '' ?>" data-tooltip="SQL Editor">
                    <i class="bi bi-terminal"></i>
                    <span>SQL Editor</span>
                </a>
            </div>
            <?php endif; ?>
            
            <div class="menu-item">
                <a href="<?= $base ?>/diagrama" class="menu-link <?= $currentPage === 'diagrama' ? 'active' : '' ?>" data-tooltip="Diagrama ER">
                    <i class="bi bi-diagram-3"></i>
                    <span>Diagrama ER</span>
                </a>
            </div>

            <?php if ($ehDevOuSuperior): ?>
            <div class="menu-label">Automação</div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/apis-externas" class="menu-link <?= $currentPage === 'apis-externas' ? 'active' : '' ?>" data-tooltip="APIs Externas">
                    <i class="bi bi-cloud-arrow-up-fill"></i>
                    <span>APIs Externas</span>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/eventos-api" class="menu-link <?= $currentPage === 'eventos-api' ? 'active' : '' ?>" data-tooltip="Eventos de API">
                    <i class="bi bi-lightning-charge-fill"></i>
                    <span>Eventos de API</span>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/workflows" class="menu-link <?= $currentPage === 'workflows' ? 'active' : '' ?>" data-tooltip="Workflows">
                    <i class="bi bi-diagram-3-fill"></i>
                    <span>Workflows</span>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/workflow-execucoes" class="menu-link <?= $currentPage === 'workflow-execucoes' ? 'active' : '' ?>" data-tooltip="Execuções">
                    <i class="bi bi-play-btn-fill"></i>
                    <span>Execuções</span>
                </a>
            </div>
            
            <div class="menu-item">
                <a href="<?= $base ?>/pipelines" class="menu-link <?= $currentPage === 'pipelines' ? 'active' : '' ?>" data-tooltip="Pipelines">
                    <i class="bi bi-bezier2"></i>
                    <span>Pipelines</span>
                </a>
            </div>
            <?php endif; ?>

            <div class="menu-label">Sistema</div>
            
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
            
            <?php if ($ehDevOuSuperior): ?>
            <div class="menu-item">
                <a href="<?= $base ?>/logs" class="menu-link <?= $currentPage === 'logs' ? 'active' : '' ?>" data-tooltip="Logs do Sistema">
                    <i class="bi bi-file-text-fill"></i>
                    <span>Logs do Sistema</span>
                </a>
            </div>
            <?php endif; ?>

            <?php if ($ehAdminSidebar): ?>
            <div class="menu-label">Administração</div>
            
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
            
            <div class="menu-item">
                <a href="<?= $base ?>/admin/auditoria" class="menu-link <?= $currentPage === 'auditoria' ? 'active' : '' ?>" data-tooltip="Auditoria">
                    <i class="bi bi-shield-check"></i>
                    <span>Auditoria</span>
                </a>
            </div>
            
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
            
            <div class="menu-item">
                <a href="<?= $base ?>/admin/fila" class="menu-link <?= $currentPage === 'fila' ? 'active' : '' ?>" data-tooltip="Fila Execução">
                    <i class="bi bi-collection"></i>
                    <span>Fila Execução</span>
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

        // Logout
        document.getElementById('btnLogout')?.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (confirm('Deseja realmente sair?')) {
                $.post(baseUrl + '/logout', function() {
                    window.location.href = baseUrl + '/login';
                });
            }
        });

        // User card → Meu Perfil
        document.getElementById('userCard')?.addEventListener('click', function(e) {
            if (e.target.closest('#btnLogout')) return;
            window.location.href = baseUrl + '/meu-perfil';
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
                    $('#notificationBadge').text(res.count).show();
                } else {
                    $('#notificationBadge').hide();
                }
            }).fail(function() {
                $('#notificationBadge').hide();
            });
        }

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
