<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} — Lab</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        :root{
            --bg:#f6f8fc;
            --card:#ffffff;
            --text:#0f172a;
            --muted:#64748b;
            --border:rgba(15,23,42,.08);
            --shadow:0 18px 45px rgba(2,6,23,.10);

            --primary:#2563eb;
            --primary2:#60a5fa;
            --success:#16a34a;
            --warning:#f59e0b;
            --danger:#ef4444;
            --radius:18px;

            --sidebarW: 280px;
            --topbarH: 66px;
        }

        html,body{height:100%}
        body{
            background:
                radial-gradient(1200px 650px at 15% -10%, rgba(37,99,235,.18), transparent 55%),
                radial-gradient(900px 520px at 90% 0%, rgba(14,165,233,.14), transparent 60%),
                var(--bg);
            font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            color: var(--text);
        }

        /* Topbar */
        .lab-topbar{
            position: sticky;
            top: 0;
            z-index: 1100;
            height: var(--topbarH);
            display:flex;
            align-items:center;
            padding: 10px 16px;
            background: linear-gradient(90deg, var(--primary), #1d4ed8);
            box-shadow: 0 10px 28px rgba(37,99,235,.18);
        }
        .lab-brand{
            display:flex;
            align-items:center;
            gap: 10px;
            color:#fff;
            text-decoration:none;
        }
        .lab-logo{
            width:42px;height:42px;border-radius:16px;
            display:flex;align-items:center;justify-content:center;
            background: rgba(255,255,255,.16);
            border: 1px solid rgba(255,255,255,.20);
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.08);
        }
        .lab-brand .title{
            font-weight: 900;
            letter-spacing: .2px;
            line-height: 1.1;
        }
        .lab-brand .sub{
            font-size: .82rem;
            color: rgba(255,255,255,.72);
        }

        .lab-topbar .btn-icon{
            width:40px;height:40px;border-radius:14px;
            border: 1px solid rgba(255,255,255,.22);
            background: rgba(255,255,255,.14);
            color:#fff;
            display:flex;align-items:center;justify-content:center;
        }
        .lab-topbar .btn-icon:hover{ background: rgba(255,255,255,.20); }

        .lab-user{
            display:flex;
            align-items:center;
            gap: 10px;
            color:#fff;
        }
        .lab-avatar{
            width:38px;height:38px;border-radius:14px;
            display:flex;align-items:center;justify-content:center;
            background: rgba(255,255,255,.16);
            border: 1px solid rgba(255,255,255,.22);
        }
        .lab-user small{ color: rgba(255,255,255,.72); }

        /* Shell */
        .lab-shell{
            display:flex;
            min-height: calc(100vh - var(--topbarH));
        }

        /* Sidebar */
        .lab-sidebar{
            width: var(--sidebarW);
            padding: 14px;
            border-right: 1px solid rgba(15,23,42,.06);
            background: rgba(255,255,255,.70);
            backdrop-filter: blur(10px);
        }
        .lab-sidecard{
            border-radius: var(--radius);
            padding: 14px;
            border: 1px solid rgba(255,255,255,.55);
            background: rgba(255,255,255,.75);
            box-shadow: var(--shadow);
            margin-bottom: 12px;
        }
        .lab-sidecard .name{ font-weight: 900; }
        .lab-sidecard .email{ color: var(--muted); font-size: .88rem; word-break: break-word; }

        .lab-nav{
            display:flex;
            flex-direction:column;
            gap: 8px;
        }
        .lab-link{
            display:flex;
            align-items:center;
            gap: 10px;
            padding: 12px 12px;
            border-radius: 16px;
            text-decoration:none;
            color: var(--muted);
            border: 1px solid transparent;
            transition: .12s ease;
            font-weight: 800;
        }
        .lab-link i{ width:18px; text-align:center; }
        .lab-link:hover{
            background: rgba(37,99,235,.08);
            color: var(--primary);
            border-color: rgba(37,99,235,.18);
        }
        .lab-link.active{
            background: linear-gradient(90deg, rgba(37,99,235,.16), rgba(255,255,255,.0));
            color: var(--primary);
            border-color: rgba(37,99,235,.22);
            box-shadow: 0 10px 22px rgba(37,99,235,.12);
        }

        .lab-sidebar-footer{
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid rgba(15,23,42,.06);
            color: var(--muted);
            font-size: .88rem;
        }

        /* Content */
        .lab-content{
            flex:1;
            padding: 18px 18px;
        }
        .lab-content-inner{
            border-radius: 22px;
            padding: 10px;
        }

        /* Mobile drawer */
        .lab-backdrop{
            position: fixed;
            inset:0;
            background: rgba(2,6,23,.42);
            z-index: 1200;
            opacity:0;
            pointer-events:none;
            transition: opacity .15s ease;
        }
        .lab-backdrop.show{
            opacity:1;
            pointer-events:auto;
        }
        .lab-drawer{
            position: fixed;
            top: var(--topbarH);
            left: 0;
            width: min(var(--sidebarW), 90vw);
            height: calc(100vh - var(--topbarH));
            z-index: 1250;
            transform: translateX(-105%);
            transition: transform .18s ease;
            background: rgba(255,255,255,.86);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(15,23,42,.10);
            padding: 14px;
            overflow:auto;
        }
        .lab-drawer.show{
            transform: translateX(0);
        }

        /* Responsive */
        @media (max-width: 991.98px){
            .lab-sidebar{ display:none; }
            .lab-content{ padding: 14px; }
        }
    </style>
</head>

<body>
    {{-- Topbar --}}
    <header class="lab-topbar">
        <div class="d-flex align-items-center gap-2">
            <button class="btn-icon d-lg-none" id="openLabMenu" aria-label="Open menu">
                <i class="fa-solid fa-bars"></i>
            </button>

            <a class="lab-brand" href="{{ route('lab.dashboard') }}">
                <div class="lab-logo"><i class="fa-solid fa-flask"></i></div>
                <div class="d-flex flex-column">
                    <div class="title">{{ config('app.name') }}</div>
                    <div class="sub">Lab Panel</div>
                </div>
            </a>
        </div>

        <div class="ms-auto d-flex align-items-center gap-2">
            @auth('lab')
                <div class="lab-user d-none d-md-flex">
                    <div class="lab-avatar"><i class="fa-solid fa-user"></i></div>
                    <div class="text-end">
                        <div class="fw-bold">{{ auth('lab')->user()->name }}</div>
                        <small>Lab</small>
                    </div>
                </div>

                <form method="POST" action="{{ route('lab.logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm px-3">
                        <i class="fa-solid fa-right-from-bracket me-1"></i>
                        Logout
                    </button>
                </form>
            @endauth
        </div>
    </header>

    {{-- Backdrop + Drawer (Mobile) --}}
    <div id="labBackdrop" class="lab-backdrop"></div>
    <aside id="labDrawer" class="lab-drawer d-lg-none">
        <div class="lab-sidecard">
            <div class="name">{{ auth('lab')->user()->name ?? 'Lab' }}</div>
            <div class="email">{{ auth('lab')->user()?->email }}</div>
        </div>

        <nav class="lab-nav">
            <a class="lab-link {{ request()->routeIs('lab.dashboard') ? 'active' : '' }}" href="{{ route('lab.dashboard') }}">
                <i class="fa-solid fa-gauge-high"></i> Dashboard
            </a>
            <a class="lab-link {{ request()->routeIs('lab.bookings.*') ? 'active' : '' }}" href="{{ route('lab.bookings.index') }}">
                <i class="fa-solid fa-receipt"></i> My Orders
            </a>
        </nav>

        <div class="lab-sidebar-footer">
            Tip: use search to find orders fast.
        </div>
    </aside>

    <div class="lab-shell">
        {{-- Sidebar (Desktop) --}}
        <aside class="lab-sidebar d-none d-lg-block">
            <div class="lab-sidecard">
                <div class="name">{{ auth('lab')->user()->name ?? 'Lab' }}</div>
                <div class="email">{{ auth('lab')->user()?->email }}</div>
            </div>

            <nav class="lab-nav">
                <a class="lab-link {{ request()->routeIs('lab.dashboard') ? 'active' : '' }}" href="{{ route('lab.dashboard') }}">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <a class="lab-link {{ request()->routeIs('lab.bookings.*') ? 'active' : '' }}" href="{{ route('lab.bookings.index') }}">
                    <i class="fa-solid fa-receipt"></i> My Orders
                </a>
            </nav>

            <div class="lab-sidebar-footer">
                Tip: use search to find orders fast.
            </div>
        </aside>

        {{-- Content --}}
        <main class="lab-content">
            <div class="lab-content-inner">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        (function(){
            const openBtn = document.getElementById('openLabMenu');
            const drawer  = document.getElementById('labDrawer');
            const backdrop= document.getElementById('labBackdrop');

            function open(){
                drawer.classList.add('show');
                backdrop.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
            function close(){
                drawer.classList.remove('show');
                backdrop.classList.remove('show');
                document.body.style.overflow = '';
            }

            if(openBtn) openBtn.addEventListener('click', open);
            if(backdrop) backdrop.addEventListener('click', close);

            document.addEventListener('keydown', function(e){
                if(e.key === 'Escape') close();
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
