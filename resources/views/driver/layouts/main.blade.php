<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Driver</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    {{-- Driver theme --}}
    <link href="{{ asset('assets/driver/driver.css') }}" rel="stylesheet">
</head>

<body class="driver-body {{ app()->getLocale() === 'ar' ? 'locale-ar' : 'locale-en' }}">
    {{-- Topbar --}}
    <header class="driver-topbar">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-icon d-md-none" id="toggleSidebar" aria-label="Toggle menu">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div class="d-flex flex-column">
                    <div class="brand">Driver</div>
                    <small class="brand-sub">
                        <span class="lang-en">Driver Panel</span>
                        <span class="lang-ar">لوحة السائق</span>
                    </small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button id="langToggle" class="btn btn-outline-light btn-sm px-3">
                    EN / AR
                </button>

                @auth('driver')
                    <div class="d-none d-md-flex align-items-center gap-2 me-2">
                        <div class="driver-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="text-end">
                            <div class="fw-semibold">{{ auth('driver')->user()->name }}</div>
                            <div class="text-white-50 small">Driver</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('driver.logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-light btn-sm px-3">
                            <i class="fa-solid fa-right-from-bracket me-1"></i>
                            <span class="lang-en">Logout</span>
                            <span class="lang-ar">خروج</span>
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </header>

    <div class="driver-shell">
        {{-- Sidebar --}}
        <aside class="driver-sidebar" id="driverSidebar">
            <nav class="driver-nav">
                <a class="driver-link {{ request()->routeIs('driver.dashboard') ? 'active' : '' }}" href="{{ route('driver.dashboard') }}">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span class="lang-en">Dashboard</span>
                    <span class="lang-ar">الرئيسية</span>
                </a>

                <a class="driver-link {{ request()->routeIs('driver.bookings.*') ? 'active' : '' }}" href="{{ route('driver.bookings.index') }}">
                    <i class="fa-solid fa-receipt"></i>
                    <span class="lang-en">My Bookings</span>
                    <span class="lang-ar">حجوزاتي</span>
                </a>
            </nav>

            <div class="driver-sidebar-footer">
                <div class="small text-muted">
                    <span class="lang-en">Tip: use filters to find bookings fast.</span>
                    <span class="lang-ar">نصيحة: استخدم التصفية للوصول للحجز بسرعة.</span>
                </div>
            </div>
        </aside>

        {{-- Content --}}
        <main class="driver-content">
            <div class="container-fluid py-4">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Toast container --}}
    <div id="toastStack" class="toast-stack"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/driver/driver.js') }}"></script>

    @stack('scripts')
</body>
</html>
