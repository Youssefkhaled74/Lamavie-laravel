<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>User Code - {{ $user->name }}</title>
    <style>
        @font-face {
            font-family: 'NotoNaskhArabic';
            src: url('{{ public_path("fonts/NotoNaskhArabic-Regular.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        body { font-family: 'NotoNaskhArabic', 'DejaVu Sans', Arial, sans-serif; font-size:14px; color:#111; background:#f6f7fb; }
        .wrap { width: 520px; margin: 40px auto; }
        .card {
            border:1px solid #e5e7eb;
            background:#fff;
            padding:22px;
            border-radius:14px;
            box-shadow: 0 10px 22px rgba(0,0,0,0.06);
        }
        .brand-row { display:flex; align-items:center; justify-content:space-between; }
        .brand { font-weight:800; font-size:16px; }
        .pill { font-size:12px; color:#1f2937; background:#f3f4f6; border:1px solid #e5e7eb; padding:6px 10px; border-radius:999px; }
        .divider { height:1px; background:#e5e7eb; margin:14px 0; }
        .meta { color:#374151; font-size:12px; }
        .code-box {
            margin:14px 0 10px;
            padding:16px;
            border-radius:12px;
            border:1px dashed #cbd5e1;
            background:#f8fafc;
            text-align:center;
        }
        .code { font-size:34px; font-weight:900; letter-spacing:4px; margin:0; }
        .small { color:#6b7280; font-size:12px; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <div class="brand-row">
                <div class="brand">{{ config('app.name') }}</div>
                <div class="pill">USER CODE</div>
            </div>

            <div class="divider"></div>

            <div class="meta">
                <div><strong>User:</strong> {{ $user->name }}</div>
                <div><strong>Phone:</strong> {{ $user->phone ?? '—' }}</div>
            </div>

            <div class="code-box">
                <p class="small" style="margin:0 0 6px;">Unique Code</p>
                <p class="code">{{ $user->unique_code ?? '—' }}</p>
            </div>

            <div class="small">Generated: {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>
</body>
</html>
