<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Login' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Noto+Sans+Arabic:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --primary-1: #3B82F6; /* lighter */
            --primary-2: #6366F1; /* indigo */
            --primary-dark: #1E3A8A;
            --accent-cyan: #06B6D4;
            --bg-grad-1: #F3F8FF;
            --bg-grad-2: #F7F3FF;
            --card-bg: rgba(255,255,255,0.62);
            --card-border: rgba(255,255,255,0.14);
            --card-radius: 16px;
            --input-radius: 10px;
            --muted: #6B7280;
            --text-strong: #0F172A;
            --shadow-sm: 0 2px 8px rgba(7,10,25,0.06);
            --shadow-md: 0 8px 24px rgba(14,20,40,0.08);
            --shadow-lg: 0 12px 36px rgba(14,20,40,0.12);
            --glass-blur: 14px;
            --bg-radial-1: rgba(6,182,212,0.07);
            --bg-radial-2: rgba(99,102,241,0.06);
        }
        
        /* Dark theme variables */
        .theme-dark { 
            --bg-gradient-start: #0c1424;
            --bg-gradient-mid: #1e293b;
            --bg-gradient-end: #334155;
            --card-bg: rgba(30,41,59,0.95);
            --text-strong: #f1f5f9;
            --muted: #94a3b8;
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.4);
            --shadow-xl: 0 20px 60px rgba(0,0,0,0.5);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body { height: 100%; }
        
        body {
            font-family: 'Inter', 'Noto Sans Arabic', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--bg-grad-1) 0%, var(--bg-grad-2) 100%);
            padding: 32px 20px;
            transition: background 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        
        /* Arabic font family */
        body[dir="rtl"], .lang-ar {
            font-family: 'Noto Sans Arabic', 'Roboto', sans-serif;
        }

        /* Animated background elements */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            opacity: 0.07;
            z-index: 0;
            filter: blur(40px);
        }

        body::before {
            width: 680px;
            height: 680px;
            background: radial-gradient(circle, var(--bg-radial-1), transparent 40%);
            top: -280px;
            right: -220px;
            animation: float 22s ease-in-out infinite;
        }

        body::after {
            width: 720px;
            height: 720px;
            background: radial-gradient(circle, var(--bg-radial-2), transparent 40%);
            bottom: -300px;
            left: -200px;
            animation: float 26s ease-in-out infinite reverse;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(120deg); }
            66% { transform: translate(-20px, 20px) rotate(240deg); }
        }

        /* Login card with modern glass effect */
        .login-card {
            background: var(--card-bg);
            backdrop-filter: blur(var(--glass-blur)) saturate(1.05);
            border-radius: var(--card-radius);
            box-shadow: var(--shadow-lg);
            max-width: 920px;
            width: 100%;
            overflow: hidden;
            border: 1px solid var(--card-border);
            display: grid;
            grid-template-columns: 320px 1fr;
            position: relative;
            z-index: 1;
            transition: transform 0.28s ease, box-shadow 0.28s ease;
        }
        
        .login-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl), 0 0 80px rgba(59,130,246,0.1);
        }

        @media (max-width: 900px) { 
            .login-card { grid-template-columns: 1fr; }
            .promo-side { display: none; }
        }

        /* Left promo panel with gradient */
        .promo-side {
            padding: 44px 32px;
            background: linear-gradient(180deg, rgba(99,102,241,0.03) 0%, rgba(6,182,212,0.02) 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 20px;
            border-right: 1px solid rgba(15,23,42,0.04);
            position: relative;
        }
        
        .promo-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-start), var(--primary-mid), var(--primary-end));
        }
        
        .brand-wrap {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }
        
        .brand-wrap img {
            height: 56px;
            width: 56px;
            border-radius: 12px;
            background: white;
            padding: 8px;
            box-shadow: var(--shadow-sm);
            transition: transform 0.28s ease;
        }
        
        .brand-wrap img:hover {
            transform: rotate(-5deg) scale(1.05);
        }
        
        .brand-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-strong);
            line-height: 1.2;
            letter-spacing: -0.02em;
        }
        
        .promo-desc {
            color: var(--muted);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-top: 8px;
        }
        
        .promo-side .security-note {
            font-size: 0.9rem;
            color: var(--muted);
            line-height: 1.5;
            padding: 16px;
            background: rgba(30,64,175,0.02);
            border-radius: 12px;
            border-left: 3px solid var(--primary-mid);
        }

        /* Right login panel */
        .login-side {
            padding: 40px 44px 36px;
            display: flex;
            flex-direction: column;
        }
        
        .login-title {
            font-size: 2rem;
            font-weight: 600;
            color: var(--text-strong);
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }
        
        .login-sub {
            color: var(--muted);
            margin-bottom: 32px;
            font-size: 1rem;
            font-weight: 400;
        }

        /* Form inputs with modern design */
        .form-control {
            border-radius: var(--input-radius);
            padding: 12px 14px;
            border: 1px solid rgba(15,23,42,0.08);
            background: rgba(255,255,255,0.85);
            box-shadow: var(--shadow-sm);
            transition: box-shadow .18s, border-color .18s, transform .12s;
            font-size: 1rem;
            font-weight: 400;
            color: var(--text-strong);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-1);
            background: rgba(255,255,255,0.96);
            box-shadow: 0 6px 18px rgba(99,102,241,0.12);
        }
        
        .form-control::placeholder {
            color: var(--muted);
            opacity: 0.7;
        }

        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            pointer-events: none;
            font-size: 1.05rem;
            transition: color 0.18s ease;
        }
        
        .input-with-icon input:focus + i,
        .input-with-icon:focus-within i {
            color: var(--primary-mid);
        }
        
        .input-with-icon input {
            padding-left: 52px;
        }

        .input-with-icon.has-pw input {
            padding-right: 60px;
        }

        .pw-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: var(--muted);
            cursor: pointer;
            height: 40px;
            width: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 3;
            transition: all 0.2s ease;
        }
        
        .pw-toggle:hover {
            background: rgba(59,130,246,0.08);
            color: var(--primary-mid);
            transform: translateY(-50%) scale(1.08);
        }
        
        .pw-toggle:active {
            transform: translateY(-50%) scale(0.95);
        }
        
        .pw-toggle i {
            font-size: 1.15rem;
        }

        .row-opts {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            margin-bottom: 24px;
        }
        
        .remember {
            display: flex;
            gap: 10px;
            align-items: center;
            color: var(--muted);
            font-size: 0.95rem;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .remember:hover {
            color: var(--text-strong);
        }
        
        .remember input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary-mid);
        }
        
        .forgot-btn {
            background: transparent;
            border: none;
            color: var(--primary-mid);
            font-weight: 600;
            cursor: pointer;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            padding: 4px 8px;
            border-radius: 6px;
        }
        
        .forgot-btn:hover {
            color: var(--primary-start);
            background: rgba(59,130,246,0.05);
            transform: translateX(-2px);
        }

        /* Modern gradient button */
        .btn-primary {
            background: linear-gradient(90deg, var(--primary-1) 0%, var(--primary-2) 100%);
            border: none;
            border-radius: 12px;
            padding: 14px 20px;
            font-weight: 600;
            font-size: 1rem;
            color: #ffffff;
            box-shadow: 0 8px 24px rgba(59,130,246,0.16);
            transition: transform .12s, box-shadow .12s;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.02em;
            text-transform: none;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent 60%);
            transform: translateX(-100%);
            transition: transform 0.5s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(59,130,246,0.18);
        }
                /* Accessible focus ring */
                .form-control:focus-visible, .btn-primary:focus-visible, .pw-toggle:focus-visible {
                    box-shadow: 0 0 0 4px rgba(99,102,241,0.12);
                    outline: none;
                }
        
        .btn-primary:hover::before {
            transform: translateX(100%);
        }
        
        .btn-primary:active {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(59,130,246,0.35);
        }
        
        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: 0 4px 12px rgba(59,130,246,0.2);
        }

        /* Make the main submit button slightly taller for better touch targets */
        #submitBtn {
            padding-top: 16px;
            padding-bottom: 16px;
            min-height: 56px;
            border-radius: 14px;
        }

        /* Bilingual button */
        .btn-bilingual {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            padding: 0;
            width: 100%;
        }
        
        .btn-bilingual .lang-en,
        .btn-bilingual .lang-ar {
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.03em;
        }
        
        .btn-bilingual .lang-ar {
            direction: rtl;
        }

        /* Locale helpers */
        body.locale-en .lang-en { display: block; }
        body.locale-en .lang-ar { display: none; }
        body.locale-ar .lang-ar { display: block; }
        body.locale-ar .lang-en { display: none; }

        .login-footer {
            padding: 20px 0 0;
            text-align: center;
            color: var(--muted);
            font-size: 0.95rem;
            border-top: 1px solid rgba(30,64,175,0.08);
            margin-top: 32px;
        }
        
        .login-footer a {
            color: var(--primary-mid);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .login-footer a:hover {
            color: var(--primary-start);
            text-decoration: underline;
        }

        /* Theme toggle button */
        .theme-toggle {
            position: absolute;
            top: 24px;
            right: 24px;
            border-radius: 12px;
            padding: 12px;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(30,64,175,0.1);
            cursor: pointer;
            box-shadow: var(--shadow-md);
            z-index: 10;
            transition: all 0.3s ease;
        }
        
        .theme-toggle:hover {
            background: white;
            transform: rotate(15deg) scale(1.1);
            box-shadow: var(--shadow-lg);
        }
        
        .theme-toggle i {
            color: var(--primary-mid);
            font-size: 1.1rem;
        }

        /* Language switch */
        .language-switch {
            position: absolute;
            top: 24px;
            left: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 14px;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            border: 2px solid rgba(30,64,175,0.1);
            box-shadow: var(--shadow-md);
            z-index: 10;
        }
        
        .lang-flag {
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 6px;
            overflow: hidden;
            position: relative;
        }
        
        .lang-flag img {
            width: 32px;
            height: 22px;
            object-fit: cover;
            opacity: 0.5;
            transition: all 0.3s ease;
            display: block;
        }
        
        .lang-flag:hover img {
            opacity: 0.8;
            transform: scale(1.1);
        }
        
        .lang-flag.active {
            box-shadow: 0 0 0 3px rgba(59,130,246,0.2);
        }
        
        .lang-flag.active img {
            opacity: 1;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* Alert styling */
        .login-alert {
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(239,68,68,0.05);
            border: 2px solid rgba(239,68,68,0.2);
            color: #dc2626;
            font-weight: 500;
        }

        /* Responsive adjustments */
        @media (max-width: 560px) { 
            .login-side { padding: 32px 24px; }
            .login-title { font-size: 1.6rem; }
            .theme-toggle, .language-switch { top: 16px; }
            .theme-toggle { right: 16px; }
            .language-switch { left: 16px; }
        }
    </style>
</head>
<body>
    <!-- Language Switch -->
    <div class="language-switch" id="language-switch">
        <a href="#" class="lang-flag" data-lang="en" id="lang-en" title="English">
            <img src="https://flagcdn.com/w40/us.png" alt="EN">
        </a>
        <a href="#" class="lang-flag" data-lang="ar" id="lang-ar" title="Arabic">
            <img src="https://flagcdn.com/w40/eg.png" alt="AR">
        </a>
    </div>

    <div class="theme-toggle" id="themeToggle" title="Toggle theme" aria-label="Toggle dark or light mode">
        <i class="fas fa-moon"></i>
    </div>

    <div class="login-card" role="main">
        <div class="promo-side">
            <div class="brand-wrap">
                    @php
                        // Prefer PNG, then SVG, then the default image in public/default/
                        if (file_exists(public_path('assets/brand-logo.png'))) {
                            $brandImg = asset('assets/brand-logo.png');
                        } elseif (file_exists(public_path('assets/brand-logo.svg'))) {
                            $brandImg = asset('assets/brand-logo.svg');
                        } else {
                            $brandImg = asset('default/images.png');
                        }
                    @endphp
                    <img src="{{ $brandImg }}" alt="Lamavie logo" width="56" height="56" onerror="this.onerror=null;this.src='{{ asset('default/images.png') }}';">
                    <div>
                        <div class="brand-title">Lamavie</div>
                        <div class="promo-desc">
                            <span class="lang-en">Admin Portal</span>
                            <span class="lang-ar">بوابة الإدارة</span>
                        </div>
                    </div>
                </div>
            
            <div style="flex:1;display:flex;flex-direction:column;gap:16px;">
                <div style="color:var(--text-strong);font-weight:500;font-size:1.05rem;">
                    <span class="lang-en">Manage with Confidence</span>
                    <span class="lang-ar">إدارة بثقة</span>
                </div>
                <div style="color:var(--muted);font-size:0.95rem;line-height:1.6;">
                    <span class="lang-en">Complete control over bookings, drivers, and lab operations. Secure, reliable, and efficient.</span>
                    <span class="lang-ar">تحكم كامل في الحجوزات والسائقين وعمليات المعامل. آمن وموثوق وفعال.</span>
                </div>
            </div>
            
            <div class="security-note">
                <i class="fas fa-shield-alt" style="color:var(--primary-mid);margin-right:8px;"></i>
                <span class="lang-en">Secure administrator access only. Unauthorized entry is prohibited.</span>
                <span class="lang-ar">وصول آمن للمسؤولين فقط. الدخول غير المصرح به محظور.</span>
            </div>
        </div>

        <div class="login-side">
            <div class="login-title">
                <span class="lang-en">{{ $roleLabel ?? $title ?? 'Sign in' }}</span>
                <span class="lang-ar">{{ $roleLabel ?? 'لامافي للإدارة' }}</span>
            </div>
            @if(!empty($description))
                <div class="login-sub">
                    <span class="lang-en">{{ $description }}</span>
                    <span class="lang-ar">قم بتسجيل الدخول إلى لوحة الإدارة الخاصة بك</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger login-alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ $action ?? url('/login') }}" id="loginForm" novalidate>
                @csrf
                @if(!empty($hiddenInputs) && is_array($hiddenInputs))
                    @foreach($hiddenInputs as $name => $value)
                        <input type="hidden" name="{{ $name }}" id="{{ $name }}" value="{{ $value }}">
                    @endforeach
                @endif

                <div class="mb-3 input-with-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" class="form-control" id="emailInput" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="mb-3 input-with-icon has-pw" style="position:relative">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="passwordInput" class="form-control" required>
                    <button type="button" class="pw-toggle" id="pwToggle" aria-label="Toggle password visibility"><i class="fas fa-eye"></i></button>
                </div>

                <div class="row-opts">
                    <label class="remember"><input type="checkbox" name="remember"> <span class="lang-en">Remember me</span> <span class="lang-ar" style="direction:rtl;font-size:0.88rem">تذكرني</span></label>
                    <button type="button" class="forgot-btn" id="forgotBtn">
                        <span class="lang-en">Forgot password?</span>
                        <span class="lang-ar" style="direction:rtl;font-size:0.88rem;display:block">نسيت كلمة السر؟</span>
                    </button>
                </div>

                <div style="position:relative">
                    <button class="btn btn-primary w-100 btn-bilingual" id="submitBtn">
                        <span class="lang-en">Login</span>
                        <span class="lang-ar">تسجيل الدخول</span>
                        <span id="submitSpinner" style="display:none;margin-left:8px"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>

                @if(!empty($helpText))
                    <div class="text-center small-note mt-3">{!! $helpText !!}</div>
                @endif
            </form>

            <div class="login-footer mt-3">
                <span class="lang-en">Need access? Contact <a href="mailto:support@lamavie.example">support@lamavie.example</a></span>
                <span class="lang-ar">تحتاج إلى وصول؟ اتصل بـ <a href="mailto:support@lamavie.example">support@lamavie.example</a></span>
            </div>
        </div>
    </div>

    @if(!empty($includeFirebase) && $includeFirebase)
        <!-- Firebase scripts injected when requested -->
        <script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-messaging-compat.js"></script>
        <script>
            // Page author can use this to obtain FCM token and populate hidden inputs
        </script>
    @endif

    @stack('login-scripts')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // UI interactions: forgot password, password toggle, loading state, theme toggle
        (function(){
            const forgotBtn = document.getElementById('forgotBtn');
            if (forgotBtn) {
                forgotBtn.addEventListener('click', function(){
                    alert('Please contact the admin to reset your password');
                });
            }

            const pwToggle = document.getElementById('pwToggle');
            const pwInput = document.getElementById('passwordInput');
            if (pwToggle && pwInput) {
                pwToggle.addEventListener('click', function(){
                    const type = pwInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    pwInput.setAttribute('type', type);
                    pwToggle.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
                });
            }

            const form = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const submitLabel = document.getElementById('submitLabel');
            const submitSpinner = document.getElementById('submitSpinner');
            if (form && submitBtn) {
                form.addEventListener('submit', function(e){
                    // Basic validation
                    if (!form.checkValidity()) {
                        return;
                    }
                    // Show loading state
                    submitLabel.style.opacity = '0.6';
                    submitSpinner.style.display = '';
                    submitBtn.disabled = true;
                });
            }

            const themeToggle = document.getElementById('themeToggle');
            const root = document.documentElement;
            function setDark(enabled){
                if (enabled) document.body.classList.add('theme-dark'); else document.body.classList.remove('theme-dark');
                localStorage.setItem('lamavie_theme_dark', enabled ? '1' : '0');
            }
            if (themeToggle){
                themeToggle.addEventListener('click', function(){
                    const enabled = document.body.classList.toggle('theme-dark');
                    setDark(enabled);
                });
                const saved = localStorage.getItem('lamavie_theme_dark');
                if (saved === '1') setDark(true);
            }

            // Language switching
            const enBtn = document.getElementById('lang-en');
            const arBtn = document.getElementById('lang-ar');
            const emailInput = document.getElementById('emailInput');
            const passwordInput = document.getElementById('passwordInput');

            function applyLanguage(lang) {
                try { localStorage.setItem('language', lang); } catch (e) {}

                // Update direction
                document.documentElement.setAttribute('dir', (lang === 'ar') ? 'rtl' : 'ltr');
                document.body.style.direction = (lang === 'ar') ? 'rtl' : 'ltr';

                // Add locale class to body to show/hide appropriate language elements
                document.body.className = document.body.className.replace(/locale-(en|ar)/g, '');
                document.body.classList.add('locale-' + lang);

                // Update flag opacity
                if (enBtn) enBtn.classList.toggle('active', lang === 'en');
                if (arBtn) arBtn.classList.toggle('active', lang === 'ar');

                // Update input placeholders
                if (emailInput) {
                    emailInput.placeholder = (lang === 'ar') ? 'البريد الإلكتروني' : 'Email';
                }
                if (passwordInput) {
                    passwordInput.placeholder = (lang === 'ar') ? 'كلمة السر' : 'Password';
                }

                // Show/hide language-specific text
                document.querySelectorAll('.lang-en').forEach(el => {
                    el.style.display = (lang === 'en') ? 'block' : 'none';
                });
                document.querySelectorAll('.lang-ar').forEach(el => {
                    el.style.display = (lang === 'ar') ? 'block' : 'none';
                });
            }

            // Initialize language from localStorage or default to English
            let savedLang = 'en';
            try { savedLang = localStorage.getItem('language') || 'en'; } catch (e) {}
            applyLanguage(savedLang);

            // Click handlers for language flags
            if (enBtn) enBtn.addEventListener('click', function(e){ e.preventDefault(); applyLanguage('en'); });
            if (arBtn) arBtn.addEventListener('click', function(e){ e.preventDefault(); applyLanguage('ar'); });
        })();
    </script>
</body>
</html>
