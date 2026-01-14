<!DOCTYPE html>
<html lang="en">

<head>
    @include('components.custom-alert')
    @if(session('custom_alert'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                try {
                    showCustomAlert({!! json_encode(session('custom_alert')) !!});
                } catch (e) {
                    console.warn('Could not show custom alert', e);
                }
            });
        </script>
    @endif
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lamavie Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-light: #2563eb;
            --secondary: #10b981;
            --dark: #1f2937;
            --light: #f8fafc;
            --gray: #94a3b8;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            /* increased sidebar width slightly for better label visibility */
            --sidebar-width: 300px;
            /* increased collapsed width to match larger icons/spacing */
            --sidebar-collapsed: 100px;
            --header-height: 80px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ef 100%);
            color: #334155;
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(to bottom, var(--primary), var(--primary-light));
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 3px 0 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        .sidebar-brand {
            padding: 1.5rem 1rem;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            height: var(--header-height);
            background: linear-gradient(90deg, #0d6efd 0%, #2563eb 100%);
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.08);
        }

        .sidebar-brand img {
            height: 40px;
            width: 40px;
            object-fit: contain;
            margin-right: 14px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.08);
        }

        /* polished toggle button inside header */
        .toggle-sidebar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.9));
            box-shadow: 0 6px 18px rgba(16, 24, 40, 0.06);
            transition: transform 0.25s ease, background 0.25s ease;
        }

        .toggle-sidebar:hover {
            transform: translateY(-2px);
        }

        /* Sidebar inner nav scroll improvements */
        .sidebar-nav {
            padding: 1rem 0;
            height: calc(100vh - var(--header-height));
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 2.5rem;
        }

        /* Custom scrollbar */
        .sidebar-nav::-webkit-scrollbar {
            width: 10px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            border: 2px solid transparent;
            background-clip: padding-box;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.18);
        }

        /* Firefox */
        .sidebar-nav {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.12) transparent;
        }

        /* overlay for mobile when sidebar opens */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 900;
            transition: opacity 0.2s ease;
        }

        /* nicer active indicator */
        .nav-link.active {
            box-shadow: 0 6px 18px rgba(2, 6, 23, 0.12);
        }

        .sidebar-brand span {
            transition: opacity 0.3s;
            font-size: 1.3rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .sidebar.collapsed .sidebar-brand span {
            opacity: 0;
            display: none;
        }

        .sidebar-nav {
            padding: 1rem 0;
            height: 100vh;
            overflow-y: auto
        }

        /* Submenu / Dry Clean group polishing */
        .nav .collapse .nav-link {
            padding-left: 2.25rem;
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.9);
            transform: none;
            /* don't shift nested items */
        }

        .nav .collapse .nav-link i {
            width: 18px;
            margin-right: 10px;
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .nav .collapse .nav-link:hover {
            background: rgba(255, 255, 255, 0.04);
            transform: none;
            color: #fff;
        }

        /* Chevron rotate when submenu expanded (Bootstrap toggles aria-expanded) */
        .nav-link .fa-chevron-down {
            transition: transform 0.25s ease;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.85);
        }

        .nav-link[aria-expanded="true"] .fa-chevron-down {
            transform: rotate(180deg);
            color: #fff;
        }

        /* Slightly smaller text for child items to create visual hierarchy */
        .nav .collapse .nav-link span {
            font-size: 0.95rem;
        }

        .nav-item {
            margin: 0.5rem 1rem;
            border-radius: 8px;
            overflow: hidden;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.85rem 1rem;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            border-radius: 8px;
            text-decoration: none;
            position: relative;
        }

        .nav-link.active {
            background: linear-gradient(90deg, var(--primary-light) 0%, var(--primary) 100%);
            color: #fff !important;
            box-shadow: 2px 0 8px rgba(30, 58, 138, 0.08);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 10px;
            bottom: 10px;
            width: 4px;
            background: var(--secondary);
            border-radius: 4px;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .nav-link i {
            margin-right: 12px;
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
            transition: margin 0.3s;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        .nav-link span {
            transition: opacity 0.3s;
        }

        .sidebar.collapsed .nav-link span {
            opacity: 0;
            display: none;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s;
            min-height: calc(100vh - var(--header-height));
            max-height: calc(100vh - var(--header-height));
            overflow-y: auto;
            background: linear-gradient(135deg, #f8fafc 0%, #e0e7ef 100%);
        }

        .main-content.expanded {
            margin-left: var(--sidebar-collapsed);
        }

        /* Header (navbar) */
        .header {
            background: rgba(255,255,255,0.98);
            border-radius: 14px;
            padding: 0.6rem 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: var(--header-height);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(15,23,42,0.03);
        }

        /* Ensure right side header elements align horizontally */
        .header-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .toggle-sidebar {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--dark);
            cursor: pointer;
            margin-right: 1rem;
        }


        /* Header search box (pill) */
        .search-box {
            display: flex;
            align-items: center;
            background: #ffffff;
            border-radius: 999px;
            padding: 6px 12px;
            width: 560px;
            max-width: calc(100% - 320px);
            box-shadow: 0 10px 30px rgba(2,6,23,0.06);
            border: 1px solid rgba(15, 23, 42, 0.04);
            gap: 8px;
            min-width: 0;
        }

        .search-box .search-btn {
            background: linear-gradient(135deg, rgba(37,99,235,0.95), rgba(37,99,235,0.75));
            border: none;
            color: #fff;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 999px;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(37,99,235,0.12);
        }

        .search-box input[type="text"] {
            border: none;
            background: transparent;
            width: 100%;
            padding: 10px 12px;
            outline: none;
            font-size: 0.98rem;
            color: var(--dark);
        }

        .search-box input::placeholder {
            color: #9aa6b6;
        }

        @media (max-width: 1200px) {
            .search-box {
                width: 420px;
                max-width: calc(100% - 200px);
            }
        }

        @media (max-width: 992px) {
            .search-box {
                width: 220px;
                max-width: 55%;
            }
        }


        .header-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .user-info {
            margin-right: 0.5rem;
            text-align: right;
            line-height: 1;
        }

        .user-name {
            font-weight: 700;
            margin-bottom: 0.1rem;
            color: var(--dark);
            font-size: 0.98rem;
        }

        .user-role {
            font-size: 0.85rem;
            color: var(--gray);
        }

        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.05rem;
            overflow: hidden;
            border: 2px solid #fff;
            box-shadow: 0 6px 18px rgba(2,6,23,0.08);
        }

        /* Dashboard Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 12px 24px rgba(30, 58, 138, 0.10);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-right: 1rem;
        }

        .icon-primary {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-light);
        }

        .icon-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
        }

        .icon-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }

        .icon-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .stat-info h4 {
            font-size: 1.8rem;
            margin-bottom: 0.2rem;
            font-weight: 700;
        }

        .stat-info p {
            color: var(--gray);
        }

        /* Modal for showing adjusted prices by area (reusable) */
        .area-prices-modal .modal-dialog {
            max-width: 720px;
        }

        .area-prices-modal .modal-body {
            max-height: 60vh;
            overflow: auto;
        }

        margin: 0;
        }

        /* Charts and Tables */
        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .chart-title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        /* Logout Form Fix */
        .logout-form {
            display: inline;
        }

        .logout-btn {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.8);
            padding: 0;
            display: flex;
            align-items: center;
            width: 100%;
            padding: 0.85rem 1rem;
            transition: all 0.3s;
            border-radius: 8px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(5px);
        }

        .logout-btn i {
            margin-right: 12px;
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }

        /* Content Styles */
        .content-header {
            margin-bottom: 1.5rem;
        }

        .content-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
        }

        .content-header p {
            color: var(--gray);
            margin-bottom: 0;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .card-title {
            font-weight: 600;
            color: var(--dark);
        }

        /* Alert Styling */
        .alert-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .55);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000
        }

        .alert-modal {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, .2);
            padding: 28px 32px;
            max-width: 640px;
            width: 92%;
            text-align: center
        }

        .alert-modal h3 {
            font-size: 1.75rem;
            margin-bottom: 12px;
            color: #1e3a8a;
            font-weight: 800
        }

        .alert-modal p {
            font-size: 1rem;
            color: #475569;
            margin-bottom: 18px
        }

        .alert-actions {
            display: flex;
            gap: 12px;
            justify-content: center
        }

        .alert-actions .btn-lg {
            padding: .8rem 1.4rem;
            font-size: 1rem
        }

        /* Language switch / flags */
        .language-switch {
            display: flex;
            align-items: center;
            margin: 0;
            padding: 0 6px;
            gap: 6px;
        }

        .lang-flag img {
            width: 28px;
            height: 18px;
            object-fit: cover;
            border-radius: 4px;
            opacity: 0.65;
            transition: all 0.18s ease;
            border: 1px solid rgba(15,23,42,0.04);
            box-shadow: 0 6px 18px rgba(2,6,23,0.06);
            cursor: pointer;
        }

        .lang-flag img:hover { transform: translateY(-2px); opacity: 1; }

        .lang-flag.active img { opacity: 1; transform: translateY(-1px); box-shadow: 0 8px 22px rgba(2,6,23,0.10); }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: var(--sidebar-collapsed);
                text-align: center;
            }

            .sidebar-brand span {
                display: none;
            }

            .nav-link span {
                display: none;
            }

            .nav-link i {
                margin-right: 0;
                font-size: 1.4rem;
            }

            .main-content {
                margin-left: var(--sidebar-collapsed);
            }

            .search-box {
                width: 200px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                overflow: hidden;
            }

            .sidebar.mobile-visible {
                width: var(--sidebar-width);
                transform: translateX(0);
                box-shadow: 6px 0 24px rgba(2, 6, 23, 0.18);
            }

            .main-content {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                height: auto;
                padding: 1rem;
            }

            .header-left,
            .header-right {
                width: 100%;
            }

            .search-box {
                width: 100%;
                margin-bottom: 1rem;
            }

            .user-menu {
                justify-content: space-between;
                width: 100%;
            }

            .language-switch {
                margin-bottom: 1rem;
            }
        }

        /* Animation Keyframes */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideOutRight {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100px);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        /* Mobile menu button */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--dark);
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('assets/brand-logo.png') }}" alt="Lamavie" width="36" height="36">
            <span data-en="Lamavie Admin" data-ar="لوحة تحكم لامافي">Lamavie Admin</span>
        </div>

        <ul class="sidebar-nav">
            <!-- Primary -->
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span data-en="Dashboard" data-ar="لوحة التحكم">Dashboard</span>
                </a>
            </li>
            @can('bookings.view')
            <li class="nav-item">
                <a href="{{ route('admin.bookings.index') }}"
                    class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i>
                    <span data-en="Bookings" data-ar="الحجوزات">Bookings</span>
                    @php
                        $unseenCount = \App\Models\Booking::where('is_unseen', true)->count();
                    @endphp
                    @if ($unseenCount > 0)
                        <span class="badge rounded-pill bg-danger ms-2">{{ $unseenCount }}</span>
                    @endif
                </a>
            </li>
            @endcan

            @can('users.view')
            <li class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span data-en="Users" data-ar="المستخدمون">Users</span>
                </a>
            </li>
            @endcan

            <!-- Vehicles group -->
            {{-- Vehicle Timeline moved under Car Wash group --}}
            <!-- Car Wash moved below Dry Clean -->

            <!-- Users & Admins (Admins moved into Settings submenu) -->
            <!-- Settings moved below Dry Clean -->

            <!-- Services & Catalog -->
            @can('services.view')
            <li class="nav-item">
                <a href="{{ route('admin.services.index') }}"
                    class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                    <i class="fas fa-concierge-bell"></i>
                    <span data-en="Services" data-ar="الخدمات">Services</span>
                </a>
            </li>
            @endcan
            @can('service-types.view')
            <li class="nav-item">
                <a href="{{ route('admin.service-types.index') }}"
                    class="nav-link {{ request()->routeIs('admin.service-types.*') ? 'active' : '' }}">
                    <i class="fas fa-list"></i>
                    <span data-en="Service Types" data-ar="أنواع الخدمات">Service Types</span>
                </a>
            </li>
            @endcan
            @can('service-categories.view')
            <li class="nav-item">
                <a href="{{ route('admin.service-categories.index') }}"
                    class="nav-link {{ request()->routeIs('admin.service-categories.*') ? 'active' : '' }}">
                    <i class="fas fa-folder"></i>
                    <span data-en="Service Categories" data-ar="فئات الخدمات">Service Categories</span>
                </a>
            </li>
            @endcan
            <!-- House Keeping parent grouping -->
            @php($housekeepingActive = request()->routeIs('admin.packages-optional.*') || request()->routeIs('admin.number-of-cleaners.*') || request()->routeIs('admin.estimated-hours.*'))
            <li class="nav-item">
                <a href="#houseKeepingSubmenu" class="nav-link d-flex justify-content-between align-items-center {{ $housekeepingActive ? 'active' : '' }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ $housekeepingActive ? 'true' : 'false' }}">
                    <div style="display:flex; align-items:center; gap:12px;"><i class="fas fa-screwdriver"></i><span data-en="House Keeping" data-ar="التدبير المنزلي">House Keeping</span></div>
                    <i class="fas fa-chevron-down"></i>
                </a>

                <div class="collapse {{ $housekeepingActive ? 'show' : '' }}" id="houseKeepingSubmenu">
                    <ul class="nav flex-column ms-2">
                        <li class="nav-item">
                            <a href="{{ route('admin.packages-optional.index') }}" class="nav-link {{ request()->routeIs('admin.packages-optional.*') ? 'active' : '' }}">
                                <i class="fas fa-box-open"></i>
                                <span data-en="Packages Optional" data-ar="الباقات الاختيارية">Packages Optional</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.number-of-cleaners.index') }}" class="nav-link {{ request()->routeIs('admin.number-of-cleaners.*') ? 'active' : '' }}">
                                <i class="fas fa-users"></i>
                                <span data-en="Number of Cleaners" data-ar="عدد المنظفين">Number of Cleaners</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.estimated-hours.index') }}" class="nav-link {{ request()->routeIs('admin.estimated-hours.*') ? 'active' : '' }}">
                                <i class="fas fa-stopwatch"></i>
                                <span data-en="Estimated Hours" data-ar="الساعات المقدرة">Estimated Hours</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Areas / Catalog items -->
            @can('areas.view')
            <li class="nav-item">
                <a href="{{ route('admin.areas.index') }}"
                    class="nav-link {{ request()->routeIs('admin.areas.*') ? 'active' : '' }}">
                    <i class="fas fa-map-marked-alt"></i>
                    <span data-en="Areas" data-ar="المناطق">Areas</span>
                </a>
            </li>
            @endcan
            {{-- Dry Clean parent with child links --}}
            @php($dryActive = request()->routeIs('admin.your-items.*') || request()->routeIs('admin.carpet-material.*') || request()->routeIs('admin.carpet-size.*') || request()->routeIs('admin.labs.*') || request()->routeIs('admin.drivers.*') || request()->routeIs('admin.type-of-stain.*'))
            <li class="nav-item">
                <a href="#dryCleanSubmenu"
                    class="nav-link d-flex justify-content-between align-items-center {{ $dryActive ? 'active' : '' }}"
                    data-bs-toggle="collapse" role="button" aria-expanded="{{ $dryActive ? 'true' : 'false' }}">
                    <div style="display:flex; align-items:center; gap:12px;"><i class="fas fa-tshirt"></i><span
                            data-en="Dry Clean" data-ar="تنظيف جاف">Dry Clean</span></div>
                    <i class="fas fa-chevron-down"></i>
                </a>

                <div class="collapse {{ $dryActive ? 'show' : '' }}" id="dryCleanSubmenu">
                    <ul class="nav flex-column ms-2">
                        @can('your-items.view')
                        <li class="nav-item">
                            <a href="{{ route('admin.your-items.index') }}"
                                class="nav-link {{ request()->routeIs('admin.your-items.*') ? 'active' : '' }}">
                                <i class="fas fa-box"></i>
                                <span data-en="Your Items" data-ar="عناصرك">Your Items</span>
                            </a>
                        </li>
                        @endcan
                        @can('carpet-material.view')
                        <li class="nav-item">
                            <a href="{{ route('admin.carpet-material.index') }}"
                                class="nav-link {{ request()->routeIs('admin.carpet-material.*') ? 'active' : '' }}">
                                <i class="fas fa-rug"></i>
                                <span data-en="Carpet Materials" data-ar="مواد السجاد">Carpet Materials</span>
                            </a>
                        </li>
                        @endcan
                        @can('carpet-size.view')
                        <li class="nav-item">
                            <a href="{{ route('admin.carpet-size.index') }}"
                                class="nav-link {{ request()->routeIs('admin.carpet-size.*') ? 'active' : '' }}">
                                <i class="fas fa-ruler"></i>
                                <span data-en="Carpet Sizes" data-ar="أحجام السجاد">Carpet Sizes</span>
                            </a>
                        </li>
                        @endcan
                        @can('labs.view')
                        <li class="nav-item">
                            <a href="{{ route('admin.labs.index') }}"
                                class="nav-link {{ request()->routeIs('admin.labs.*') ? 'active' : '' }}">
                                <i class="fas fa-flask"></i>
                                <span data-en="Labs" data-ar="المعامل">Labs</span>
                            </a>
                        </li>
                        @endcan
                        @can('drivers.view')
                        <li class="nav-item">
                            <a href="{{ route('admin.drivers.index') }}"
                                class="nav-link {{ request()->routeIs('admin.drivers.*') ? 'active' : '' }}">
                                <i class="fas fa-id-badge"></i>
                                <span data-en="Drivers" data-ar="السائقون">Drivers</span>
                            </a>
                        </li>
                        @endcan
                        <li class="nav-item">
                            <a href="{{ route('admin.type-of-stain.index') }}"
                                class="nav-link {{ request()->routeIs('admin.type-of-stain.*') ? 'active' : '' }}">
                                <i class="fas fa-paint-brush"></i>
                                <span data-en="Type of Stains" data-ar="نوع البقع">Type of Stains</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.size-of-stain.index') }}"
                                class="nav-link {{ request()->routeIs('admin.size-of-stain.*') ? 'active' : '' }}">
                                <i class="fas fa-tint"></i>
                                <span data-en="Size of Stains" data-ar="حجم البقع">Size of Stains</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Car Wash (moved here so related groups are together) -->
            @php($carWashActive = request()->routeIs('admin.vehicle-timeline.*') || request()->routeIs('admin.driver-vehicles.*') || request()->routeIs('admin.car-wash-drivers.*') || request()->routeIs('admin.place-of-the-cleaning.*') || request()->routeIs('admin.cars-additional-service.*') || request()->routeIs('admin.frequency.*'))
            <li class="nav-item">
                <a href="#carWashSubmenu" class="nav-link d-flex justify-content-between align-items-center {{ $carWashActive ? 'active' : '' }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ $carWashActive ? 'true' : 'false' }}">
                    <div style="display:flex; align-items:center; gap:12px;"><i class="fas fa-shower"></i><span data-en="Car Wash" data-ar="غسيل السيارات">Car Wash</span></div>
                    <i class="fas fa-chevron-down"></i>
                </a>

                <div class="collapse {{ $carWashActive ? 'show' : '' }}" id="carWashSubmenu">
                    <ul class="nav flex-column ms-2">
                        @can('vehicle-timeline.view')
                        <li class="nav-item">
                                <a href="{{ route('admin.vehicle-timeline.full') }}" class="nav-link {{ request()->routeIs('admin.vehicle-timeline.*') ? 'active' : '' }}">
                                    <i class="fas fa-car"></i>
                                    <span data-en="Vehicle Timeline" data-ar="جدول المركبات">Vehicle Timeline</span>
                                </a>
                            </li>
                        @endcan
                        <li class="nav-item">
                            <a href="{{ route('admin.driver-vehicles.index') }}" class="nav-link {{ request()->routeIs('admin.driver-vehicles.*') ? 'active' : '' }}">
                                <i class="fas fa-car-side"></i>
                                <span data-en="Vehicles" data-ar="المركبات">Vehicles</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.car-wash-drivers.index') }}" class="nav-link {{ request()->routeIs('admin.car-wash-drivers.*') ? 'active' : '' }}">
                                <i class="fas fa-user-tie"></i>
                                <span data-en="Car Wash Drivers" data-ar="سائقو غسيل السيارات">Car Wash Drivers</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.place-of-the-cleaning.index') }}" class="nav-link {{ request()->routeIs('admin.place-of-the-cleaning.*') ? 'active' : '' }}">
                                <i class="fas fa-broom"></i>
                                <span data-en="Place of the Cleaning" data-ar="مكان التنظيف">Place of the Cleaning</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.cars-additional-service.index') }}" class="nav-link {{ request()->routeIs('admin.cars-additional-service.*') ? 'active' : '' }}">
                                <i class="fas fa-tools"></i>
                                <span data-en="Cars Additional Service" data-ar="خدمات إضافية للمركبات">Cars Additional Service</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.frequency.index') }}" class="nav-link {{ request()->routeIs('admin.frequency.*') ? 'active' : '' }}">
                                <i class="fas fa-stopwatch"></i>
                                <span data-en="Frequency" data-ar="التكرار">Frequency</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Settings (moved here) -->
            @php($settingsActive = request()->routeIs('admin.users.*') || request()->routeIs('admin.admins.*') || request()->routeIs('admin.payment-methods.*') || request()->routeIs('admin.settings.*') || request()->routeIs('admin.home-banners.*'))
            <li class="nav-item">
                <a href="#settingsSubmenu" data-bs-target="#settingsSubmenu" aria-controls="settingsSubmenu" class="nav-link d-flex justify-content-between align-items-center {{ $settingsActive ? 'active' : '' }}" data-bs-toggle="collapse" role="button" aria-expanded="{{ $settingsActive ? 'true' : 'false' }}">
                    <div style="display:flex; align-items:center; gap:12px;"><i class="fas fa-sliders-h"></i><span data-en="Settings" data-ar="الإعدادات">Settings</span></div>
                    <i class="fas fa-chevron-down"></i>
                </a>

                <div class="collapse {{ $settingsActive ? 'show' : '' }}" id="settingsSubmenu">
                    <ul class="nav flex-column ms-2">
                        {{-- Users moved to top-level nav for easier access --}}

                        @role('super-admin')
                        <li class="nav-item">
                            <a href="{{ route('admin.admins.index') }}" class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                                <i class="fas fa-users-cog"></i>
                                <span data-en="Admins" data-ar="المشرفون">Admins</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                <i class="fas fa-user-shield"></i>
                                <span data-en="Roles" data-ar="الأدوار">Roles</span>
                            </a>
                        </li>
                        @endrole

                        @can('payment-methods.view')
                        <li class="nav-item">
                            <a href="{{ route('admin.payment-methods.index') }}" class="nav-link {{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}">
                                <i class="fas fa-credit-card"></i>
                                <span data-en="Payment Methods" data-ar="طرق الدفع">Payment Methods</span>
                            </a>
                        </li>
                        @endcan

                        @if(auth()->guard('admin')->user()?->hasRole('super-admin') || auth()->guard('admin')->user()?->can('settings.view'))
                        <li class="nav-item">
                            <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                <i class="fas fa-cog"></i>
                                <span data-en="Settings" data-ar="الإعدادات">Settings</span>
                            </a>
                        </li>
                        @endif

                        @can('home-banners.view')
                        <li class="nav-item">
                            <a href="{{ route('admin.home-banners.index') }}" class="nav-link {{ request()->routeIs('admin.home-banners.*') ? 'active' : '' }}">
                                <i class="fas fa-image"></i>
                                <span data-en="Home Banners" data-ar="لافتات الصفحة الرئيسية">Home Banners</span>
                            </a>
                        </li>
                        @endcan

                        @role('super-admin')
                        <li class="nav-item">
                            <a href="{{ route('admin.logs.index') }}" class="nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
                                <i class="fas fa-file-alt"></i>
                                <span data-en="Logs" data-ar="السجلات">Logs</span>
                            </a>
                        </li>
                        @endrole
                    </ul>
                </div>
            </li>

            <!-- Stain / fabric related -->

            {{-- Type of Stains moved into Dry Clean group above --}}
            <li class="nav-item">
                <a href="{{ route('admin.fabric-type.index') }}"
                    class="nav-link {{ request()->routeIs('admin.fabric-type.*') ? 'active' : '' }}">
                    <i class="fas fa-shirt"></i>
                    <span data-en="Fabric Types" data-ar="أنواع الأقمشة">Fabric Types</span>
                </a>
            </li>

            <!-- Misc / utilities -->

            <li class="nav-item">
                <form class="logout-form" action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span data-en="Logout" data-ar="تسجيل الخروج">Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Header -->
        <div class="header">
            <div class="header-left d-flex align-items-center"
                style="gap:0.75rem; display:flex; align-items:center; flex-wrap:nowrap;">
                <button class="toggle-sidebar" id="toggle-sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <form class="search-box" method="GET" action="{{ route('admin.bookings.search') }}">
                    <button type="submit" class="search-btn" aria-label="Search"><i
                            class="fas fa-search"></i></button>
                    <input type="text" name="q" placeholder="Search booking by user name or phone..."
                        value="{{ request('q') }}">
                </form>
            </div>

            <div class="header-right">
                <?php
                    $notifCount = \App\Models\Notification::where('notifiable_type', \App\Models\Admin::class)
                        ->where('notifiable_id', auth()->id())
                        ->whereNull('read_at')
                        ->count();
                ?>
                <div class="me-2" style="display:flex;align-items:center;">
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-light position-relative" title="Notifications">
                        <i class="fas fa-bell"></i>
                        <span id="notif-badge-holder" class="position-absolute" style="top:-6px;right:-6px;">
                            @if($notifCount > 0)
                                <span id="notif-badge" class="badge rounded-pill bg-danger">{{ $notifCount }}</span>
                            @else
                                <span id="notif-badge" class="badge rounded-pill bg-danger" style="display:none;">0</span>
                            @endif
                        </span>
                    </a>
                </div>
                <div class="language-switch" id="language-switch" style="display:flex;align-items:center;gap:8px;">
                    <a href="#" class="lang-flag" data-lang="en" id="lang-en" title="English" style="display:inline-block;">
                        <img src="https://flagcdn.com/w20/us.png" srcset="https://flagcdn.com/w40/us.png 2x" alt="EN" style="width:28px;height:18px;object-fit:cover;border-radius:3px;opacity:0.6;">
                    </a>
                    <a href="#" class="lang-flag" data-lang="ar" id="lang-ar" title="Arabic" style="display:inline-block;">
                        <img src="https://flagcdn.com/w20/eg.png" srcset="https://flagcdn.com/w40/eg.png 2x" alt="AR" style="width:28px;height:18px;object-fit:cover;border-radius:3px;opacity:0.6;">
                    </a>
                    <input type="hidden" id="language-toggle" value="">
                </div>
                <div class="user-menu d-flex align-items-center">
                    <?php $u = auth()->guard('admin')->user(); ?>
                    <div class="avatar me-2">
                        @if($u && $u->photo)
                            <img src="{{ asset('storage/' . $u->photo) }}" alt="{{ $u->name ?? 'Admin' }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;display:block;">
                        @else
                            {{ isset($u->name) ? strtoupper(substr($u->name, 0, 1)) : 'A' }}
                        @endif
                    </div>
                    <div class="user-info text-end">
                        <div class="user-name">{{ $u->name ?? 'Admin' }}</div>
                        <div class="user-role">Administrator • @include('dashboard.admin.partials.online_badge', ['admin' => $u])</div>
                    </div>
                    <div class="dropdown ms-3">
                        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-cog"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form class="logout-form" action="{{ route('admin.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i
                                            class="fas fa-sign-out-alt me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        @include('dashboard.admin.partials.global_export_button')
        @yield('content')

        <script>
            (function() {
                const enBtn = document.getElementById('lang-en');
                const arBtn = document.getElementById('lang-ar');
                const hiddenToggle = document.getElementById('language-toggle');

                function applyLanguage(lang) {
                    try { localStorage.setItem('language', lang); } catch (e) {}

                    // update direction (and html dir)
                    document.documentElement.setAttribute('dir', (lang === 'ar') ? 'rtl' : 'ltr');
                    document.body.style.direction = (lang === 'ar') ? 'rtl' : 'ltr';

                    // Find all elements that provide translations via data-en / data-ar
                    document.querySelectorAll('[data-en], [data-ar]').forEach(el => {
                        const text = el.getAttribute(`data-${lang}`);
                        if (text === null) return;

                        const tag = (el.tagName || '').toLowerCase();

                        // Inputs / textareas / selects: set placeholder or value
                        if (tag === 'input' || tag === 'textarea' || tag === 'select' || el instanceof HTMLButtonElement) {
                            const type = (el.getAttribute('type') || '').toLowerCase();
                            if (type === 'submit' || type === 'button' || tag === 'button') {
                                el.value = text;
                                if (el.tagName.toLowerCase() === 'button') el.textContent = text;
                            } else if (el.hasAttribute('placeholder')) {
                                el.placeholder = text;
                            } else if (el.hasAttribute('value')) {
                                el.value = text;
                            } else {
                                el.textContent = text;
                            }
                        } else {
                            // Other elements: update textContent
                            el.textContent = text;
                        }

                        // common attributes: title and aria-label
                        if (el.hasAttribute('title')) el.setAttribute('title', text);
                        if (el.hasAttribute('aria-label')) el.setAttribute('aria-label', text);
                    });

                    // Visual state for the flags
                    if (enBtn && enBtn.querySelector) enBtn.querySelector('img').style.opacity = (lang === 'en') ? '1' : '0.6';
                    if (arBtn && arBtn.querySelector) arBtn.querySelector('img').style.opacity = (lang === 'ar') ? '1' : '0.6';
                    if (hiddenToggle) hiddenToggle.value = lang;
                }

                // initialize
                let saved = 'en';
                try { saved = localStorage.getItem('language') || saved; } catch (e) {}
                applyLanguage(saved);

                // click handlers
                if (enBtn) enBtn.addEventListener('click', function(e){ e.preventDefault(); applyLanguage('en'); });
                if (arBtn) arBtn.addEventListener('click', function(e){ e.preventDefault(); applyLanguage('ar'); });
            })();
        </script>

        {{-- Global modal used to display area-adjusted prices for any base price --}}
        <div class="modal fade area-prices-modal" id="areaPricesModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Prices by Area</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="area-prices-modal-body">
                        <div class="text-center text-muted">Loading…</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Firebase SDKs (for admin dashboard FCM token registration) -->
    <script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-messaging-compat.js"></script>
    <script>
        // Replace these with your Firebase client config in .env or a secure place
        const firebaseConfig = {
            apiKey: "{{ env('FIREBASE_API_KEY', '') }}",
            authDomain: "{{ env('FIREBASE_AUTH_DOMAIN', '') }}",
            projectId: "{{ env('FIREBASE_PROJECT_ID', '') }}",
            messagingSenderId: "{{ env('FIREBASE_MESSAGING_SENDER_ID', '') }}",
            appId: "{{ env('FIREBASE_APP_ID', '') }}",
            vapidKey: "{{ env('FIREBASE_VAPID_KEY', '') }}"
        };

        // Only attempt to register FCM token when on admin pages and admin is authenticated.
        (function() {
            try {
                // Basic sanity check: we need a messagingSenderId and vapidKey
                        console.debug('Firebase config (admin):', firebaseConfig);
                        if (!firebaseConfig.messagingSenderId || !firebaseConfig.vapidKey) {
                            console.warn('Firebase messaging config not set. Skipping FCM registration.');
                            return;
                        }

                // Initialize firebase app
                try {
                    console.debug('Before firebase.initializeApp, firebase.apps.length =', (window.firebase && window.firebase.apps && window.firebase.apps.length) || 0);
                    console.debug('firebaseConfig summary:', { apiKey: !!firebaseConfig.apiKey, messagingSenderId: firebaseConfig.messagingSenderId, vapidKeyLength: firebaseConfig.vapidKey ? firebaseConfig.vapidKey.length : 0 });
                    if (!window.firebase || !firebase.apps.length) {
                        firebase.initializeApp(firebaseConfig);
                        console.debug('firebase.initializeApp called successfully');
                    } else {
                        console.debug('firebase already initialized, skipping initializeApp');
                    }
                } catch (initErr) {
                    console.error('firebase.initializeApp threw an error', initErr, firebaseConfig);
                }

                const messaging = (function() {
                    try {
                        return firebase.messaging();
                    } catch (e) {
                        console.error('Failed to get firebase.messaging()', e);
                        return null;
                    }
                })();

                // Register Service Worker and provide a user-friendly enable-button flow
                if ('serviceWorker' in navigator) {
                    let swRegistration = null;

                    const ensureRegistered = () => {
                        if (swRegistration) return Promise.resolve(swRegistration);
                        return navigator.serviceWorker.register('/firebase-messaging-sw.js').then(reg => {
                            swRegistration = reg;
                            console.debug('Service worker registration result (admin):', reg, { scope: reg.scope, activeScript: reg.active && reg.active.scriptURL });
                            try {
                                const msg = { type: 'INIT_FIREBASE', config: firebaseConfig };
                                if (reg.active) {
                                    reg.active.postMessage(msg);
                                } else if (reg.waiting) {
                                    reg.waiting.postMessage(msg);
                                } else {
                                    navigator.serviceWorker.ready.then(r => r.active && r.active.postMessage(msg));
                                }
                                    console.debug('Posted INIT_FIREBASE to service worker (admin)');
                            } catch (e) {
                                console.warn('Failed to postMessage config to SW', e);
                            }
                            return reg;
                        });
                    };

                    const showEnableButton = (denied=false) => {
                        if (document.getElementById('enable-notifications')) return;
                        const btn = document.createElement('button');
                        btn.id = 'enable-notifications';
                        btn.className = denied ? 'btn btn-sm btn-warning' : 'btn btn-sm btn-outline-primary';
                        btn.style.position = 'fixed';
                        btn.style.right = '16px';
                        btn.style.bottom = '16px';
                        btn.style.zIndex = '9999';
                        btn.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
                        btn.innerHTML = denied 
                            ? '<i class="fas fa-exclamation-triangle"></i> Notifications Blocked - Click for Help' 
                            : '<i class="fas fa-bell"></i> Enable Notifications';
                        btn.title = denied 
                            ? 'Notifications are blocked. Click for instructions to enable them.' 
                            : 'Click to enable push notifications for new bookings.';
                        btn.onclick = async function() {
                            if (denied) {
                                const helpText = `🚨 Notifications are currently BLOCKED for this site.

To enable notifications, click "View Instructions" below or see the detailed guide.`;
                                
                                const shouldOpenHelp = confirm(helpText + '\n\nClick OK to view step-by-step instructions, or Cancel to dismiss.');
                                
                                if (shouldOpenHelp) {
                                    window.open('/enable-notifications-help.html', '_blank');
                                }
                                
                                console.log('📖 Notification blocked - user can view /enable-notifications-help.html');
                                console.log('📄 See QUICK_FIX.md for step-by-step instructions');
                                return;
                            }
                            try {
                                const reg = await ensureRegistered();
                                console.log('🔔 Requesting notification permission via button...');
                                const permission = await Notification.requestPermission();
                                console.log('📬 Notification permission result:', permission);
                                
                                if (permission !== 'granted') {
                                    console.warn('⚠️ User did not grant notification permission');
                                    if (permission === 'denied') {
                                        alert('Notifications were denied. Please enable them in your browser settings and reload the page.');
                                    }
                                    return;
                                }
                                
                                console.log('✅ Permission granted, getting FCM token...');
                                try {
                                    const token = await messaging.getToken({ vapidKey: firebaseConfig.vapidKey, serviceWorkerRegistration: reg });
                                    console.log('🎉 FCM token obtained via button:', token ? (token.substr(0,20) + '...') : 'null');
                                    if (token) {
                                        const url = "{{ route('admin.fcm-token.store') }}";
                                        const body = JSON.stringify({ fcm_token: token });
                                        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                                        console.log('📤 Posting token to server...');
                                        fetch(url, {
                                            method: 'POST',
                                            credentials: 'same-origin',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': csrf || ''
                                            },
                                            body
                                        }).then(r => r.ok ? r.json() : Promise.reject('Failed to store FCM token'))
                                          .then(data => {
                                              console.log('✅ FCM token stored successfully:', data);
                                              alert('✅ Notifications enabled! You will now receive push notifications for new bookings.');
                                          })
                                          .catch(err => {
                                              console.error('❌ Failed to store token on server:', err);
                                              alert('⚠️ Token obtained but failed to save. Check console for details.');
                                          });
                                    } else {
                                        console.warn('⚠️ getToken returned null/empty');
                                    }
                                } catch (tokenErr) {
                                    console.error('❌ Failed to get FCM token via button:', tokenErr);
                                }
                            } catch (err) {
                                console.warn('Enable notifications flow failed:', err);
                            } finally {
                                // remove button after attempt
                                try { btn.remove(); } catch (e) {}
                            }
                        };
                        document.body.appendChild(btn);
                    };

                    // Ensure SW registered but don't auto-request permission. Show UI based on current permission.
                    ensureRegistered().then(registration => {
                        console.log('Service worker registered for FCM:', registration);
                        
                        // ============================================
                        // 🔥 FOREGROUND NOTIFICATION HANDLER
                        // ============================================
                        // Handle notifications when the page is open (foreground)
                        messaging.onMessage((payload) => {
                            console.log('🔔 Foreground notification received!', payload);
                            
                            try {
                                const notificationTitle = payload.notification?.title || payload.data?.title || 'New Notification';
                                const notificationBody = payload.notification?.body || payload.data?.body || '';
                                const notificationOptions = {
                                    body: notificationBody,
                                    icon: payload.notification?.icon || '/favicon.ico',
                                    badge: '/favicon.ico',
                                    data: payload.data || {},
                                    tag: payload.data?.booking_id || 'notification',
                                    requireInteraction: true,
                                    vibrate: [200, 100, 200]
                                };
                                
                                console.log('📱 Showing foreground notification:', { title: notificationTitle, options: notificationOptions });
                                
                                // Show notification using service worker registration
                                registration.showNotification(notificationTitle, notificationOptions)
                                    .then(() => {
                                        console.log('✅ Foreground notification displayed successfully!');
                                        
                                        // Also play a sound
                                        try {
                                            const audio = new Audio('/notification-sound.mp3');
                                            audio.play().catch(e => console.debug('Audio play failed:', e));
                                        } catch (e) {
                                            console.debug('Audio not available:', e);
                                        }
                                        
                                        // Show in-page alert as well
                                        const alertDiv = document.createElement('div');
                                        alertDiv.style.cssText = `
                                            position: fixed;
                                            top: 20px;
                                            right: 20px;
                                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                            color: white;
                                            padding: 20px 25px;
                                            border-radius: 12px;
                                            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                                            z-index: 10000;
                                            max-width: 400px;
                                            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                                            animation: slideInRight 0.3s ease-out;
                                        `;
                                        alertDiv.innerHTML = `
                                            <div style="display: flex; align-items: start; gap: 15px;">
                                                <div style="font-size: 28px;">🔔</div>
                                                <div style="flex: 1;">
                                                    <div style="font-weight: 600; font-size: 16px; margin-bottom: 5px;">${notificationTitle}</div>
                                                    <div style="font-size: 14px; opacity: 0.95;">${notificationBody}</div>
                                                </div>
                                                <button onclick="this.parentElement.parentElement.remove()" style="background: rgba(255,255,255,0.2); border: none; color: white; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; font-size: 16px; line-height: 1;">×</button>
                                            </div>
                                        `;
                                        document.body.appendChild(alertDiv);
                                        
                                        // Auto-remove after 5 seconds
                                        setTimeout(() => {
                                            alertDiv.style.animation = 'slideOutRight 0.3s ease-out';
                                            setTimeout(() => alertDiv.remove(), 300);
                                        }, 5000);
                                        
                                    })
                                    .catch(err => {
                                        console.error('❌ Failed to show foreground notification:', err);
                                    });
                                    
                            } catch (err) {
                                console.error('❌ Error handling foreground notification:', err);
                            }
                        });
                        
                        console.log('✅ Foreground notification handler (onMessage) registered!');
                        // ============================================
                        
                        if (Notification.permission === 'granted') {
                            // Get token silently
                            messaging.getToken({ vapidKey: firebaseConfig.vapidKey, serviceWorkerRegistration: registration }).then(token => {
                                if (!token) return;
                                console.debug('Obtained FCM token for admin (auto):', token);
                                const url = "{{ route('admin.fcm-token.store') }}";
                                const body = JSON.stringify({ fcm_token: token });
                                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                                fetch(url, {
                                    method: 'POST',
                                    credentials: 'same-origin',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrf || ''
                                    },
                                    body
                                }).then(r => r.ok ? r.json() : Promise.reject('Failed to store FCM token')).then(data => console.debug('FCM token stored (auto):', data)).catch(err => console.error(err));
                            }).catch(err => console.warn('Failed to get FCM token (auto):', err));
                        } else if (Notification.permission === 'default') {
                            console.log('📱 Notification permission not yet requested. Showing enable button.');
                            showEnableButton(false);
                        } else {
                            // denied
                            console.error('❌ Notification permission DENIED for this origin.');
                            console.log('🔒 To enable: Click lock icon in address bar → Site settings → Notifications → Allow');
                            console.log('📖 See QUICK_FIX.md for detailed instructions');
                            showEnableButton(true);
                        }
                    }).catch(err => console.warn('FCM registration failed:', err));

                    // Listen for messages from the service worker and try to retrieve/post token when appropriate
                    try {
                        if (navigator.serviceWorker && navigator.serviceWorker.addEventListener) {
                            navigator.serviceWorker.addEventListener('message', async (event) => {
                                try {
                                    const d = event && event.data;
                                    if (!d) return;
                                    if (d.type === 'NEW_PUSH_SUBSCRIPTION' || d.type === 'NEW_FCM_TOKEN') {
                                        console.debug('Received message from SW indicating new subscription/token:', d.type);
                                        if (Notification.permission === 'granted') {
                                            try {
                                                const reg = await ensureRegistered();
                                                const token = await messaging.getToken({ vapidKey: firebaseConfig.vapidKey, serviceWorkerRegistration: reg });
                                                if (token) {
                                                    console.debug('Posting token received after SW message:', token);
                                                    const url = "{{ route('admin.fcm-token.store') }}";
                                                    const body = JSON.stringify({ fcm_token: token });
                                                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                                                    fetch(url, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf || '' }, body })
                                                        .then(r => r.ok ? r.json() : Promise.reject('Failed to store FCM token')).then(data => console.debug('FCM token stored (sw-msg):', data)).catch(err => console.error(err));
                                                }
                                            } catch (err) {
                                                console.warn('Failed to get/post token after SW message:', err);
                                            }
                                        }
                                    }
                                } catch (e) {
                                    console.warn('Error handling SW message', e);
                                }
                            });
                        }
                    } catch (e) {
                        console.warn('Failed to attach SW message listener', e);
                    }

                    // As a fallback, attempt to get token on first user interaction after login
                    (function() {
                        let attempted = false;
                        const tryOnce = async () => {
                            if (attempted) return; attempted = true;
                            try {
                                if (Notification.permission !== 'granted') return;
                                const reg = await ensureRegistered();
                                const token = await messaging.getToken({ vapidKey: firebaseConfig.vapidKey, serviceWorkerRegistration: reg });
                                if (token) {
                                    console.debug('Posting token obtained on first interaction:', token);
                                    const url = "{{ route('admin.fcm-token.store') }}";
                                    const body = JSON.stringify({ fcm_token: token });
                                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                                    fetch(url, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf || '' }, body })
                                        .then(r => r.ok ? r.json() : Promise.reject('Failed to store FCM token')).then(data => console.debug('FCM token stored (interaction):', data)).catch(err => console.error(err));
                                }
                            } catch (e) {
                                console.warn('First-interaction token attempt failed', e);
                            } finally {
                                window.removeEventListener('click', tryOnce);
                                window.removeEventListener('keydown', tryOnce);
                            }
                        };
                        window.addEventListener('click', tryOnce, { once: true });
                        window.addEventListener('keydown', tryOnce, { once: true });
                    })();
                }
            } catch (e) {
                console.error('Error initializing FCM for admin dashboard', e);
            }
        })();
    </script>
    <script>
        // DOM Elements
        const toggleSidebarBtn = document.getElementById('toggle-sidebar');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const languageToggle = document.getElementById('language-toggle');

        // create sidebar overlay element (for mobile)
        let sidebarOverlay = document.getElementById('sidebar-overlay');
        if (!sidebarOverlay) {
            sidebarOverlay = document.createElement('div');
            sidebarOverlay.id = 'sidebar-overlay';
            sidebarOverlay.className = 'sidebar-overlay d-none';
            document.body.appendChild(sidebarOverlay);
        }

        // Web Audio Context for notification sound
        let audioContext = null;
        let audioUnlocked = false;

        // Initialize AudioContext after user interaction
        function unlockAudio() {
            if (audioUnlocked) return;
            try {
                audioContext = new(window.AudioContext || window.webkitAudioContext)();
                audioUnlocked = true;
                console.log('AudioContext unlocked successfully');
            } catch (e) {
                console.error('Failed to unlock AudioContext:', e);
            }
        }

        // Play a "ding" sound (C5 + E5, triangle wave, 2 seconds)
        function playDing() {
            if (!audioUnlocked || !audioContext) {
                console.warn('AudioContext not initialized, cannot play ding');
                return;
            }
            try {
                const oscillator1 = audioContext.createOscillator();
                const oscillator2 = audioContext.createOscillator();
                const gainNode = audioContext.createGain();

                oscillator1.type = 'triangle';
                oscillator2.type = 'triangle';
                oscillator1.frequency.setValueAtTime(523.25, audioContext.currentTime); // C5
                oscillator2.frequency.setValueAtTime(659.25, audioContext.currentTime); // E5
                gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);

                oscillator1.connect(gainNode);
                oscillator2.connect(gainNode);
                gainNode.connect(audioContext.destination);

                oscillator1.start();
                oscillator2.start();
                gainNode.gain.exponentialRampToValueAtTime(0.0001, audioContext.currentTime + 2.0);
                oscillator1.stop(audioContext.currentTime + 2.0);
                oscillator2.stop(audioContext.currentTime + 2.0);

                console.log('Ding played successfully');
            } catch (e) {
                console.error('Failed to play ding:', e);
            }
        }

        // Unlock audio on first user interaction
        window.addEventListener('click', unlockAudio, {
            once: true
        });
        window.addEventListener('keydown', unlockAudio, {
            once: true
        });

        // Language Switching
        function updateLanguage() {
            const language = languageToggle.checked ? 'ar' : 'en';
            localStorage.setItem('language', language);
            document.querySelectorAll('.sidebar-brand span, .nav-link span, .logout-btn span').forEach(span => {
                span.textContent = span.getAttribute(`data-${language}`);
            });
            // Update text direction for Arabic
            document.body.style.direction = language === 'ar' ? 'rtl' : 'ltr';
            sidebar.style.textAlign = language === 'ar' ? 'right' : 'left';
        }

        // Initialize language from localStorage
        const savedLanguage = localStorage.getItem('language') || 'en';
        languageToggle.checked = savedLanguage === 'ar';
        updateLanguage();

        // Language toggle event
        languageToggle.addEventListener('change', updateLanguage);

        // Unseen bookings polling removed: backend route `admin.bookings.unseen.poll` was deprecated.
        // Badge visibility is still handled server-side; keep a simple defensive badge sanitization on load.
        document.addEventListener('DOMContentLoaded', () => {
            const badge = document.querySelector('.nav-link[href="{{ route('admin.bookings.index') }}"] .badge');
            if (badge) {
                const val = parseInt(badge.textContent || '0', 10) || 0;
                if (val <= 0) {
                    badge.classList.add('d-none');
                } else {
                    badge.textContent = val;
                    badge.classList.remove('d-none');
                }
            }
        });

        // Poll unseen notifications as a fallback when FCM isn't available.
        (function() {
            let lastCount = null;
            @if (\Illuminate\Support\Facades\Route::has('admin.notifications.unseen'))
                const endpoint = '{{ route('admin.notifications.unseen') }}';
            @else
                const endpoint = null;
            @endif

            async function checkUnseen() {
                try {
                    if (!endpoint) return;
                    const res = await fetch(endpoint, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    if (!res.ok) return;
                    const json = await res.json();
                    if (!json || !json.data) return;
                    const count = parseInt(json.data.unseen_count || 0, 10);
                    const latest = json.data.latest || null;

                    // Update sidebar badge
                    const badge = document.querySelector('.nav-link[href="{{ route('admin.bookings.index') }}"] .badge');
                    if (badge) {
                        if (count > 0) {
                            badge.textContent = count;
                            badge.classList.remove('d-none');
                        } else {
                            badge.classList.add('d-none');
                        }
                    }

                    // If new unseen arrived, show in-page alert (but avoid duplicate alerts)
                    // If cache broadcast exists, show it immediately (broadcast takes precedence)
                    if (json.data && json.data.broadcast) {
                        const b = json.data.broadcast;
                        try {
                            const title = b.title || ('New Booking ' + (b.order_number ? ('#' + b.order_number) : ''));
                            const body = b.body || 'A new booking was created.';
                            const alertDiv = document.createElement('div');
                            alertDiv.style.cssText = `
                                position: fixed;
                                top: 20px;
                                right: 20px;
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                color: white;
                                padding: 20px 25px;
                                border-radius: 12px;
                                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                                z-index: 10000;
                                max-width: 400px;
                                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                                animation: slideInRight 0.3s ease-out;
                            `;
                            alertDiv.innerHTML = `
                                <div style="display: flex; align-items: start; gap: 15px;">
                                    <div style="font-size: 28px;">🔔</div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; font-size: 16px; margin-bottom: 5px;">${title}</div>
                                        <div style="font-size: 14px; opacity: 0.95;">${body}</div>
                                    </div>
                                    <button onclick="this.parentElement.parentElement.remove()" style="background: rgba(255,255,255,0.2); border: none; color: white; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; font-size: 16px; line-height: 1;">×</button>
                                </div>
                            `;
                            document.body.appendChild(alertDiv);
                            setTimeout(() => { alertDiv.style.animation = 'slideOutRight 0.3s ease-out'; setTimeout(() => alertDiv.remove(), 300); }, 5000);
                        } catch (e) {
                            console.warn('Failed to show broadcast alert', e);
                        }
                    } else if (lastCount !== null && count > lastCount && latest) {
                        try {
                            const title = 'New Booking #' + (latest.order_number || latest.id);
                            const body = (latest.user_name ? latest.user_name + ' placed a booking.' : 'A new booking was created.');

                            // create alert div (same style used by FCM foreground handler)
                            const alertDiv = document.createElement('div');
                            alertDiv.style.cssText = `
                                position: fixed;
                                top: 20px;
                                right: 20px;
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                color: white;
                                padding: 20px 25px;
                                border-radius: 12px;
                                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                                z-index: 10000;
                                max-width: 400px;
                                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                                animation: slideInRight 0.3s ease-out;
                            `;
                            alertDiv.innerHTML = `
                                <div style="display: flex; align-items: start; gap: 15px;">
                                    <div style="font-size: 28px;">🔔</div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; font-size: 16px; margin-bottom: 5px;">${title}</div>
                                        <div style="font-size: 14px; opacity: 0.95;">${body}</div>
                                    </div>
                                    <button onclick="this.parentElement.parentElement.remove()" style="background: rgba(255,255,255,0.2); border: none; color: white; width: 24px; height: 24px; border-radius: 50%; cursor: pointer; font-size: 16px; line-height: 1;">×</button>
                                </div>
                            `;
                            document.body.appendChild(alertDiv);
                            setTimeout(() => { alertDiv.style.animation = 'slideOutRight 0.3s ease-out'; setTimeout(() => alertDiv.remove(), 300); }, 5000);
                        } catch (e) {
                            console.warn('Failed to show unseen booking alert', e);
                        }
                    }

                    lastCount = count;
                } catch (e) {
                    console.debug('Unseen poll failed', e);
                }
            }

            // Only start polling when admin is authenticated (layout only rendered for admin)
            document.addEventListener('DOMContentLoaded', function() {
                // initial check immediately, then every 10 seconds
                checkUnseen();
                setInterval(checkUnseen, 10000);
            });
        })();

        // Add active class to nav items on click
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.getAttribute('href') === '#') {
                    e.preventDefault();
                }
                navLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Responsive sidebar for mobile
        function handleMobileView() {
            if (window.innerWidth <= 768) {
                // On small screens keep sidebar hidden by default
                sidebar.classList.add('collapsed');
                sidebar.classList.remove('mobile-visible');
                mainContent.classList.remove('expanded');
                sidebarOverlay.classList.add('d-none');
            } else {
                // Desktop default: sidebar visible
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
                sidebarOverlay.classList.add('d-none');
            }
        }

        // Initial call and event listener for window resize
        handleMobileView();
        window.addEventListener('resize', handleMobileView);

        // Update the toggle icon depending on state
        function updateToggleIcon() {
            const isDesktopCollapsed = sidebar.classList.contains('collapsed') && window.innerWidth > 768;
            if (isDesktopCollapsed) {
                toggleSidebarBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
            } else if (window.innerWidth <= 768 && sidebar.classList.contains('mobile-visible')) {
                toggleSidebarBtn.innerHTML = '<i class="fas fa-times"></i>';
            } else {
                toggleSidebarBtn.innerHTML = '<i class="fas fa-bars"></i>';
            }
            toggleSidebarBtn.setAttribute('aria-expanded', String(!isDesktopCollapsed));
        }

        // Close mobile sidebar
        function closeMobileSidebar() {
            sidebar.classList.remove('mobile-visible');
            sidebarOverlay.classList.add('d-none');
            mainContent.classList.remove('expanded');
            updateToggleIcon();
        }

        // Toggle button behavior
        toggleSidebarBtn.addEventListener('click', function(e) {
            const isMobile = window.innerWidth <= 768;
            if (isMobile) {
                const opening = !sidebar.classList.contains('mobile-visible');
                if (opening) {
                    sidebar.classList.add('mobile-visible');
                    sidebar.classList.remove('collapsed');
                    sidebarOverlay.classList.remove('d-none');
                    mainContent.classList.add('expanded');
                } else {
                    closeMobileSidebar();
                }
            } else {
                // desktop collapse/expand
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            }
            updateToggleIcon();
        });

        // close when clicking the overlay
        sidebarOverlay.addEventListener('click', closeMobileSidebar);

        // ensure icon is correct on load
        updateToggleIcon();
    </script>

    <script>
        // Area prices modal loader - delegate clicks on elements with .btn-area-prices
        (function() {
            // Use a simple client-side URL to avoid calling route() during view render
            const areaPricesUrl = '/admin/partials/area-prices';
            document.addEventListener('click', function(e) {
                const btn = e.target.closest && e.target.closest('.btn-area-prices');
                if (!btn) return;
                e.preventDefault();
                const base = btn.dataset.base || btn.getAttribute('data-base') || 0;
                const modalBody = document.getElementById('area-prices-modal-body');
                if (!modalBody) return;
                modalBody.innerHTML = '<div class="text-center text-muted">Loading…</div>';
                fetch(areaPricesUrl + '?base=' + encodeURIComponent(base), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.text())
                    .then(html => {
                        modalBody.innerHTML = html;
                        const modalEl = document.getElementById('areaPricesModal');
                        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        bsModal.show();
                    })
                    .catch(err => {
                        console.error('Failed to load area prices:', err);
                        modalBody.innerHTML = '<div class="text-danger">Failed to load prices.</div>';
                        const modalEl = document.getElementById('areaPricesModal');
                        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        bsModal.show();
                    });
            });
        })();
    </script>

    <script>
        // Debug helper: log Settings toggle clicks and global JS errors
        (function() {
            try {
                const settingsToggle = document.querySelector('a[data-bs-target="#settingsSubmenu"]');
                const target = document.getElementById('settingsSubmenu');
                console.log('[DEBUG] settingsToggle found:', !!settingsToggle, 'target found:', !!target);
                if (settingsToggle && target) {
                    settingsToggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        console.log('[DEBUG] settingsToggle click', { href: settingsToggle.getAttribute('href'), dataset: settingsToggle.dataset });
                        try {
                            let toggled = false;
                            if (window.bootstrap && bootstrap.Collapse) {
                                const instance = bootstrap.Collapse.getOrCreateInstance(target);
                                console.log('[DEBUG] bootstrap.Collapse instance obtained, before show:', target.classList.contains('show'));
                                instance.toggle();
                                // allow Collapse to run; then check
                                toggled = target.classList.contains('show');
                                console.log('[DEBUG] toggled (bootstrap), after show:', toggled);
                            }

                            if (!toggled) {
                                // Bootstrap didn't toggle (or not available) — force toggle class and aria state
                                const willShow = !target.classList.contains('show');
                                console.log('[DEBUG] forcing toggle, willShow=', willShow);
                                if (willShow) {
                                    target.classList.add('show');
                                    target.classList.add('collapse');
                                    settingsToggle.setAttribute('aria-expanded', 'true');
                                    // force visible in case CSS/transitions prevent display
                                    target.style.display = 'block';
                                } else {
                                    target.classList.remove('show');
                                    settingsToggle.setAttribute('aria-expanded', 'false');
                                    target.style.display = '';
                                }
                                // Also adjust collapse classes for transitional support
                                target.classList.toggle('collapsing', false);

                                // Log computed styles and outerHTML to help diagnose visibility issues
                                try {
                                    const cs = window.getComputedStyle(target);
                                    console.log('[DEBUG] computedStyle after force toggle', { display: cs.display, height: cs.height, visibility: cs.visibility, overflow: cs.overflow });
                                } catch (e) {
                                    console.warn('[DEBUG] could not read computed style', e);
                                }

                                try {
                                    console.log('[DEBUG] outerHTML (trimmed):', target.outerHTML.slice(0, 1000));
                                } catch (e) {}

                                // Watch for external code that may remove the 'show' class
                                try {
                                    if (!target.__debugObserverAttached) {
                                        const mo = new MutationObserver(muts => {
                                            muts.forEach(m => {
                                                console.log('[DEBUG] mutation:', m.type, m.attributeName, target.className);
                                                try {
                                                    const cs = window.getComputedStyle(target);
                                                    const rect = target.getBoundingClientRect();
                                                    console.log('[DEBUG] mutation computedStyle', { display: cs.display, visibility: cs.visibility, height: cs.height, overflow: cs.overflow });
                                                    console.log('[DEBUG] mutation rect', { top: rect.top, left: rect.left, width: rect.width, height: rect.height });
                                                } catch (e) {
                                                    console.warn('[DEBUG] mutation: failed to read styles/rect', e);
                                                }
                                            });
                                        });
                                        mo.observe(target, { attributes: true, attributeFilter: ['class', 'style'] });
                                        target.__debugObserverAttached = true;
                                        console.log('[DEBUG] mutation observer attached to settings submenu');
                                    }
                                } catch (e) {
                                    console.warn('[DEBUG] failed to attach mutation observer', e);
                                }
                            }
                        } catch (err) {
                            console.error('[DEBUG] error toggling settings submenu:', err);
                        }
                    });
                }
            } catch (e) {
                console.error('[DEBUG] failed to attach settings debug handler', e);
            }

            // Global JS error catcher to print to console for easier debugging
            window.addEventListener('error', function(ev) {
                console.error('[GLOBAL JS ERROR]', ev.error || ev.message, ev.filename + ':' + ev.lineno + ':' + ev.colno, ev.error);
            });
            window.addEventListener('unhandledrejection', function(ev) {
                console.error('[UNHANDLED PROMISE REJECTION]', ev.reason);
            });
        })();
    </script>

    @yield('scripts')
</body>

</html>
