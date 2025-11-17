<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Dashboard</title>

    <!-- Local Fonts -->
    <link href="{{ asset('assets/css/fonts/inter/inter-fonts.css') }}?v={{ config('app.version', '1.0') }}" rel="stylesheet" />

    <!-- Local Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap/bootstrap.min.css') }}?v={{ config('app.version', '1.0') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/fontawesome/all.min.css') }}?v={{ config('app.version', '1.0') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/chartjs/chart.min.css') }}?v={{ config('app.version', '1.0') }}" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #3730a3;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #0ea5e9;
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 70px;
            --transition-speed: 0.3s;
            --border-radius: 12px;
        }

        [data-theme="dark"] {
            --bs-body-bg: #0f172a;
            --bs-body-color: #e2e8f0;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: var(--bs-body-bg, #f8fafc);
            transition: all var(--transition-speed) ease;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
            overflow: visible;
        }

        /* Completely reset collapsed sidebar layout */
        .sidebar.collapsed * {
            text-align: center;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
        }

        /* Brand Section */
        .sidebar-brand {
            padding: 1.2rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            transition: all var(--transition-speed) ease;
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
        }

        .sidebar.collapsed .sidebar-brand {
            padding: 1rem 0.2rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            min-height: 70px;
        }

        .brand-logo {
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.15));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            transition: all var(--transition-speed) ease;
            flex-shrink: 0;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .logo-text {
            font-size: 1.4rem;
            font-weight: 900;
            color: white;
            letter-spacing: 1.5px;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
            font-family: 'Inter', sans-serif;
        }

        .sidebar.collapsed .brand-logo {
            width: 45px;
            height: 45px;
            margin: 0 auto;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.2));
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
        }

        .sidebar.collapsed .logo-text {
            font-size: 1rem;
            font-weight: 900;
            letter-spacing: 1px;
        }

        .brand-text {
            color: white;
            font-weight: 700;
            font-size: 1.25rem;
            margin: 0;
            transition: all var(--transition-speed) ease;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            letter-spacing: 0.5px;
        }

        .sidebar.collapsed .brand-text {
            opacity: 0;
            transform: scale(0.8);
        }

        .brand-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.75rem;
            margin: 0;
            margin-top: 0.25rem;
            transition: all var(--transition-speed) ease;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .sidebar.collapsed .brand-subtitle {
            opacity: 0;
            transform: scale(0.8);
        }

        /* User Profile Section */
        .user-profile {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1rem;
        }

        .sidebar.collapsed .user-profile {
            padding: 0.6rem 0.2rem;
            text-align: center;
        }

        .sidebar.collapsed .user-avatar {
            margin: 0 auto;
            width: 28px !important;
            height: 28px !important;
            font-size: 0.75rem !important;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .user-info {
            margin-left: 0.75rem;
            transition: all var(--transition-speed) ease;
        }

        .sidebar.collapsed .user-info {
            opacity: 0;
            transform: translateX(-20px);
        }

        .user-name {
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            margin: 0;
        }

        .user-email {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.75rem;
            margin: 0;
        }

        /* Navigation Styles */
        .sidebar-nav {
            padding: 0 1rem 2rem;
        }

        .sidebar.collapsed .sidebar-nav {
            padding: 0.5rem 0.2rem 1rem;
        }

        .nav-section {
            margin-bottom: 0.5rem;
        }

        .sidebar.collapsed .nav-section {
            margin-bottom: 0.2rem;
        }

        .nav-section-header {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            padding: 0 0.75rem;
            transition: all var(--transition-speed) ease;
        }

        .sidebar.collapsed .nav-section-header {
            opacity: 0;
            transform: scale(0.8);
        }

        .nav-item {
            margin-bottom: 0.25rem;
        }
        
        .nav-item:last-child {
            margin-bottom: 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border-radius: var(--border-radius);
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            min-height: 45px;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(4px);
        }

        .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 0 2px 2px 0;
        }

        .nav-icon {
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            font-size: 1.1rem;
            transition: all var(--transition-speed) ease;
            flex-shrink: 0;
            color: inherit;
        }

        .sidebar.collapsed .nav-icon {
            margin: 0 !important;
            width: 18px !important;
            height: 18px !important;
            font-size: 1rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .nav-text {
            transition: all var(--transition-speed) ease;
            white-space: nowrap;
        }

        .sidebar.collapsed .nav-text {
            display: none !important;
            opacity: 0;
            transform: translateX(-20px);
        }

        /* Expandable Menu Styles */
        .nav-submenu {
            max-height: 0;
            overflow: hidden;
            transition: all var(--transition-speed) ease;
            background: rgba(0, 0, 0, 0.1);
            border-radius: var(--border-radius);
            margin-top: 0.25rem;
        }

        .nav-submenu.show {
            max-height: 300px;
            padding: 0.5rem 0;
        }

        /* Ensure submenu items are visible */
        .nav-submenu .nav-link {
            opacity: 1;
            visibility: visible;
        }

        .nav-submenu .nav-link {
            padding: 0.5rem 1rem 0.5rem 3rem;
            font-size: 0.8rem;
            margin-bottom: 0.125rem;
        }

        .sidebar.collapsed .nav-submenu {
            max-height: 0 !important;
            opacity: 0;
        }

        .sidebar.collapsed .nav-link {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            padding: 0.7rem 0.1rem !important;
            margin: 0.2rem auto !important;
            width: 50px !important;
            height: 50px !important;
            border-radius: 8px !important;
            text-align: center !important;
            position: relative;
            background: none;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed .nav-link:hover {
            background: rgba(255, 255, 255, 0.15) !important;
            transform: none;
        }
        
        .sidebar.collapsed .nav-link:hover::after {
            content: attr(data-tooltip);
            position: absolute;
            left: calc(100% + 12px);
            top: 50%;
            transform: translateY(-50%);
            background: rgba(30, 41, 59, 0.95);
            color: white;
            padding: 0.5rem 0.7rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            white-space: nowrap;
            z-index: 1001;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            opacity: 0;
            animation: tooltipFadeIn 0.2s ease forwards;
            backdrop-filter: blur(8px);
        }

        .sidebar.collapsed .nav-link:hover::before {
            content: '';
            position: absolute;
            left: calc(100% + 6px);
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 4px 6px 4px 0;
            border-color: transparent rgba(30, 41, 59, 0.95) transparent transparent;
            z-index: 1000;
            opacity: 0;
            animation: tooltipFadeIn 0.2s ease forwards;
        }

        /* Disable tooltips on mobile */
        @media (max-width: 768px) {
            .sidebar.collapsed .nav-link:hover::after,
            .sidebar.collapsed .nav-link:hover::before {
                display: none;
            }
        }

        /* Top Navbar Dropdown Improvements */
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 0.5rem 0;
            margin-top: 0.5rem;
            background: white;
            min-width: 200px;
        }

        .dropdown-header {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 0.85rem;
            padding: 0.75rem 1rem 0.5rem;
            border-bottom: 1px solid #f0f0f0;
            margin-bottom: 0.5rem;
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            color: #333;
            transition: all 0.2s ease;
            border: none;
            background: none;
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            transform: translateX(5px);
        }

        .dropdown-item i {
            width: 16px;
            margin-right: 8px;
        }

        .dropdown-divider {
            margin: 0.5rem 0;
            border-color: #f0f0f0;
        }

        /* Notification button improvements */
        .btn-link {
            color: #6c757d;
            transition: all 0.2s ease;
            border-radius: 8px;
            position: relative;
        }

        .btn-link:hover {
            color: var(--primary-color);
            background: rgba(79, 70, 229, 0.1);
        }

        .badge-sm {
            font-size: 0.65rem;
            padding: 0.25rem 0.4rem;
            top: 5px !important;
            right: 5px !important;
        }

        /* User dropdown button improvements */
        .dropdown-toggle {
            border: none;
            background: none;
            color: #333;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            transition: all 0.2s ease;
            text-decoration: none !important;
        }

        .dropdown-toggle:hover {
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary-color);
            text-decoration: none !important;
        }

        .dropdown-toggle::after {
            margin-left: 0.5rem;
            font-size: 0.8rem;
        }

        /* Fix for user name display in dropdown button */
        .dropdown .btn-link {
            text-decoration: none !important;
            border: none;
            background: none;
            color: #333;
            padding: 0.5rem 1rem;
            border-radius: 12px;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .dropdown .btn-link:hover,
        .dropdown .btn-link:focus,
        .dropdown .btn-link:active {
            background: rgba(79, 70, 229, 0.1);
            color: var(--primary-color);
            text-decoration: none !important;
            outline: none;
            box-shadow: none;
        }

        .dropdown .btn-link span {
            text-decoration: none !important;
            font-weight: 500;
            color: inherit;
        }

        .user-name-display {
            font-weight: 500;
            color: #333;
            text-decoration: none !important;
        }

        /* Theme Toggle Button */
        .theme-toggle {
            border: none;
            background: none;
            color: #6c757d;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-size: 1.1rem;
        }

        .theme-toggle:hover {
            color: var(--primary-color);
            background: rgba(79, 70, 229, 0.1);
            transform: rotate(15deg);
        }
        
        @keyframes tooltipFadeIn {
            0% {
                opacity: 0;
                transform: translateY(-50%) translateX(-5px);
            }
            100% {
                opacity: 1;
                transform: translateY(-50%) translateX(0);
            }
        }

        .sidebar.collapsed .nav-submenu {
            display: none !important;
        }

        .sidebar.collapsed .nav-submenu .nav-link {
            padding: 0.5rem;
        }

        /* Fix for submenu parent icons when collapsed */
        .sidebar.collapsed .nav-link .nav-toggle {
            display: none !important;
        }

        /* Hide text elements in collapsed state */
        .sidebar.collapsed .user-name,
        .sidebar.collapsed .user-email,
        .sidebar.collapsed .nav-section-header {
            display: none !important;
        }

        /* Ensure STA logo text is always visible */
        .sidebar.collapsed .logo-text {
            display: block !important;
        }

        /* Collapsed sidebar specific improvements */
        .sidebar.collapsed .nav-section:last-child {
            margin-bottom: 0;
        }

        .sidebar.collapsed .nav-link:last-child {
            margin-bottom: 0;
        }

        .sidebar.collapsed .nav-item {
            margin-bottom: 0.1rem;
        }

        /* Better spacing for collapsed state */
        .sidebar.collapsed .user-info {
            display: none;
        }

        /* Ensure proper icon alignment in collapsed state */
        .sidebar.collapsed .nav-icon i {
            width: 100%;
            text-align: center;
        }

        .nav-toggle {
            margin-left: auto;
            transition: all var(--transition-speed) ease;
            font-size: 0.75rem;
        }

        .nav-toggle.rotated {
            transform: rotate(180deg);
        }

        .sidebar.collapsed .nav-toggle {
            opacity: 0;
        }

        .nav-toggle.rotated {
            transform: rotate(180deg);
        }

        .sidebar.collapsed .nav-toggle {
            opacity: 0;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            background: var(--bs-body-bg, #f8fafc);
        }

        .sidebar.collapsed + .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* Top Navigation */
        .top-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .top-navbar h5 {
            text-decoration: none !important;
            border: none !important;
            outline: none !important;
        }

        .top-navbar .text-gradient {
            text-decoration: none !important;
            border-bottom: none !important;
            text-underline-offset: unset !important;
        }

        [data-theme="dark"] .top-navbar {
            background: rgba(15, 23, 42, 0.95);
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }

        /* Sidebar Toggle Button */
        .sidebar-toggle {
            background: none;
            border: none;
            color: var(--secondary-color);
            font-size: 1.25rem;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all var(--transition-speed) ease;
        }

        .sidebar-toggle:hover {
            background: rgba(0, 0, 0, 0.05);
            color: var(--primary-color);
        }

        [data-theme="dark"] .sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        /* Theme Toggle */
        .theme-toggle {
            background: none;
            border: none;
            color: var(--secondary-color);
            font-size: 1.1rem;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all var(--transition-speed) ease;
        }

        .theme-toggle:hover {
            background: rgba(0, 0, 0, 0.05);
            color: var(--warning-color);
        }

        [data-theme="dark"] .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--warning-color);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all var(--transition-speed) ease;
            background: white;
        }

        [data-theme="dark"] .card {
            background: #1e293b;
            color: #e2e8f0;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
            padding: 1.25rem;
        }

        [data-theme="dark"] .card-header {
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width) !important;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar.collapsed {
                width: var(--sidebar-width) !important;
                transform: translateX(-100%);
            }

            .sidebar.collapsed.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar.collapsed + .main-content {
                margin-left: 0;
            }

            .top-navbar {
                padding: 1rem;
            }

            /* Mobile submenu fixes */
            .sidebar .nav-submenu {
                position: static;
                background: rgba(0, 0, 0, 0.2);
            }

            .sidebar .nav-submenu.show {
                max-height: none;
                padding: 0.5rem 0;
            }

            .sidebar .nav-text,
            .sidebar .nav-toggle {
                opacity: 1 !important;
                transform: none !important;
            }

            .sidebar .nav-link {
                justify-content: flex-start !important;
                padding: 0.75rem !important;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease forwards;
        }

        .animate-slide-in-right {
            animation: slideInRight 0.6s ease forwards;
        }

        /* Utility Classes */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none !important;
            display: inline-block;
            font-weight: 600;
        }

        /* Fix for broken gradient text in some browsers */
        @supports not (-webkit-background-clip: text) {
            .text-gradient {
                background: none;
                color: var(--primary-color);
                -webkit-text-fill-color: var(--primary-color);
            }
        }

        /* Global fixes for unwanted underlines */
        h1, h2, h3, h4, h5, h6 {
            text-decoration: none !important;
        }

        .navbar h1, .navbar h2, .navbar h3, .navbar h4, .navbar h5, .navbar h6,
        .top-navbar h1, .top-navbar h2, .top-navbar h3, .top-navbar h4, .top-navbar h5, .top-navbar h6 {
            text-decoration: none !important;
            border-bottom: none !important;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        }

        /* Mobile Overlay */
        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all var(--transition-speed) ease;
        }

        .mobile-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* Logout Button */
        .logout-btn {
            margin-top: auto;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1rem;
        }

        .logout-btn .nav-link {
            color: rgba(255, 255, 255, 0.7);
        }

        .logout-btn .nav-link:hover {
            color: var(--danger-color);
            background: rgba(239, 68, 68, 0.1);
        }
    </style>
</head>
<body>
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <!-- Brand Section -->
        <div class="sidebar-brand">
            @php
                $primaryCompany = Auth::user()->primary_company;
                $hasLogo = $primaryCompany && $primaryCompany->logo && file_exists(storage_path('app/public/' . $primaryCompany->logo));
            @endphp
            
            <a href="{{ route('dashboard') }}" style="text-decoration: none; color: inherit;">
                @if($hasLogo)
                    <img src="{{ Auth::user()->primary_company_logo }}" 
                         alt="{{ $primaryCompany->name ?? 'Company' }} Logo" 
                         style="max-height: 40px; width: auto; display: block; margin: 0 auto; cursor: pointer;">
                @else
                    <div class="brand-logo" style="cursor: pointer;">
                        <span class="logo-text">STA</span>
                    </div>
                @endif
            </a>
        </div>

        <!-- User Profile -->
        <div class="user-profile">
            <div class="d-flex align-items-center">
                @if(Auth::user()->photo)
                    <img src="{{ Auth::user()->photo_url }}" alt="{{ Auth::user()->name }}" 
                         class="user-avatar rounded-circle" style="width: 50px; height: 50px; object-fit: cover; display: flex; align-items: center; justify-content: center;">
                @else
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                @endif
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-email">{{ Auth::user()->email }}</div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="sidebar-nav">
            <!-- Dynamic Role-Based Menu -->
            <x-sidebar-menu :user="Auth::user()" />
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navigation -->
        <nav class="top-navbar">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button class="sidebar-toggle me-3" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h5 class="mb-0 text-gradient">@yield('page-title', 'Dashboard')</h5>
                </div>
                
                <div class="d-flex align-items-center">
                    <!-- Language Switcher -->
                    <div class="me-3">
                        <x-language-switcher />
                    </div>

                    <!-- Theme Toggle -->
                    <button class="theme-toggle me-3" onclick="toggleTheme()" title="Toggle Dark Mode">
                        <i class="fas fa-moon" id="themeIcon"></i>
                    </button>
                    
                    <!-- Notification -->
                    <x-notification-dropdown />
                    
                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-link p-2" type="button" data-bs-toggle="dropdown">
                            <div class="d-flex align-items-center">
                                @if(Auth::user()->photo)
                                    <img src="{{ Auth::user()->photo_url }}" alt="{{ Auth::user()->name }}" 
                                         class="rounded-circle me-2" style="width: 35px; height: 35px; object-fit: cover;">
                                @else
                                    <div class="user-avatar me-2" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                    </div>
                                @endif
                                <span class="d-none d-md-inline user-name-display">
                                    @php
                                        $name = Auth::user()->name;
                                        $words = explode(' ', $name);
                                        $displayName = count($words) > 2 ? $words[0] . ' ' . end($words) : $name;
                                    @endphp
                                    {{ $displayName }}
                                </span>
                                <i class="fas fa-chevron-down ms-2 d-none d-md-inline" style="font-size: 0.75rem;"></i>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header"><i class="fas fa-user me-2"></i>{{ Auth::user()->name }}</h6></li>
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user-edit"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="fas fa-sign-out-alt"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid px-4 py-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show animate-fade-in-up" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    @php
                        $successMessage = session('success');
                        echo is_array($successMessage) ? implode(', ', $successMessage) : $successMessage;
                    @endphp
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show animate-fade-in-up" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    @php
                        $errorMessage = session('error');
                        echo is_array($errorMessage) ? implode(', ', $errorMessage) : $errorMessage;
                    @endphp
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="animate-slide-in-right">
                @yield('content')
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/chartjs/chart.min.js') }}"></script>
    
    <script>
        // Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const isMobile = window.innerWidth <= 768;
            
            if (isMobile) {
                sidebar.classList.toggle('show');
                document.getElementById('mobileOverlay').classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            }
        }

        // Submenu Toggle
        function toggleSubmenu(submenuId, event) {
            event.preventDefault();
            const submenu = document.getElementById(submenuId);
            const toggleIcon = event.currentTarget.querySelector('.nav-toggle');
            const sidebar = document.getElementById('sidebar');
            const isMobile = window.innerWidth <= 768;
            
            // If sidebar is collapsed on desktop, expand it first
            if (sidebar.classList.contains('collapsed') && !isMobile) {
                sidebar.classList.remove('collapsed');
                localStorage.setItem('sidebarCollapsed', false);
                // Allow time for sidebar to expand before showing submenu
                setTimeout(() => {
                    submenu.classList.add('show');
                    toggleIcon.classList.add('rotated');
                }, 300);
                return;
            }
            
            // Close other submenus
            document.querySelectorAll('.nav-submenu').forEach(menu => {
                if (menu.id !== submenuId) {
                    menu.classList.remove('show');
                    const otherToggle = document.querySelector(`[onclick*="${menu.id}"] .nav-toggle`);
                    if (otherToggle) {
                        otherToggle.classList.remove('rotated');
                    }
                }
            });
            
            // Toggle current submenu
            submenu.classList.toggle('show');
            toggleIcon.classList.toggle('rotated');
        }

        // Theme Toggle
        function toggleTheme() {
            const html = document.documentElement;
            const themeIcon = document.getElementById('themeIcon');
            const currentTheme = html.getAttribute('data-theme');
            
            if (currentTheme === 'dark') {
                html.setAttribute('data-theme', 'light');
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
                localStorage.setItem('theme', 'light');
            } else {
                html.setAttribute('data-theme', 'dark');
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
                localStorage.setItem('theme', 'dark');
            }
        }

        // Mobile Overlay Click
        document.getElementById('mobileOverlay').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('show');
            this.classList.remove('show');
        });

        // Close mobile menu when clicking submenu items
        document.addEventListener('click', function(e) {
            if (e.target.closest('.nav-submenu .nav-link') && window.innerWidth <= 768) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('mobileOverlay');
                setTimeout(() => {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }, 150);
            }
        });

        // Window Resize Handler
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');
            
            if (window.innerWidth > 768) {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            }
        });

        // Initialize on Page Load
        document.addEventListener('DOMContentLoaded', function() {
            // Restore sidebar state
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (sidebarCollapsed && window.innerWidth > 768) {
                document.getElementById('sidebar').classList.add('collapsed');
            }
            
            // Restore theme
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            const themeIcon = document.getElementById('themeIcon');
            if (savedTheme === 'dark') {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            }
            
            // Auto-expand active submenu
            const activeNavLink = document.querySelector('.nav-submenu .nav-link.active');
            if (activeNavLink) {
                const parentSubmenu = activeNavLink.closest('.nav-submenu');
                if (parentSubmenu) {
                    parentSubmenu.classList.add('show');
                    const parentToggle = parentSubmenu.previousElementSibling.querySelector('.nav-toggle');
                    if (parentToggle) {
                        parentToggle.classList.add('rotated');
                    }
                }
            }
        });

        // Keyboard Shortcuts
        document.addEventListener('keydown', function(e) {
            // Alt + S to toggle sidebar
            if (e.altKey && e.key === 's') {
                e.preventDefault();
                toggleSidebar();
            }
            
            // Alt + T to toggle theme
            if (e.altKey && e.key === 't') {
                e.preventDefault();
                toggleTheme();
            }
        });
    </script>

    <!-- Modals Section (for proper z-index stacking) -->
    @yield('modals')

    @stack('scripts')
</body>
</html>