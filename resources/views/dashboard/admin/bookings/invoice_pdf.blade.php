<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Invoice - {{ $booking->order_number }}</title>
    <style>
        @font-face {
            font-family: 'NotoNaskhArabic';
            src: url('{{ public_path("fonts/NotoNaskhArabic-Regular.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'CairoWeb';
            src: url('{{ public_path("fonts/Cairo-Regular.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        body { font-family: 'NotoNaskhArabic', 'CairoWeb', 'DejaVu Sans', 'Helvetica', 'Arial', sans-serif; font-size:14px; color:#111; direction:rtl; }
        .container { width: 100%; padding: 18px; }
        .header { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:18px; }
        .brand { font-size:18px; font-weight:700; }
        .meta { text-align:left; }
        .box { border:1px solid #ddd; border-radius:8px; padding:12px; margin-bottom:12px; }
        table { width:100%; border-collapse:collapse; margin-top:8px; }
        th, td { padding:8px 10px; border:1px solid #e6e6e6; }
        th { background:#f5f5f5; font-weight:700; }
        .text-right { text-align:right; }
        .text-left { text-align:left; }
        .small { font-size:12px; color:#666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <div class="brand">فاتورة الطلب</div>
                <div class="small">Order #{{ $booking->order_number }}</div>
            </div>
            <div class="meta text-left">
                <div>التاريخ: {{ now()->format('d/m/Y H:i') }}</div>
                <div>الحالة (قديم / جديد): {{ $oldStatus }} / {{ $newStatus }}</div>
            </div>
        </div>

        <div class="box">
            <strong>الزبون</strong>
            <div>{{ $booking->user->name ?? '—' }} — {{ $booking->user->phone ?? '—' }}</div>
            <div class="small">البريد الإلكتروني: {{ $booking->user->email ?? '—' }}</div>
        </div>

        <div class="box">
            <strong>تفاصيل الطلب</strong>
            @php
                $serviceName = data_get($booking, 'service.name');
                if (is_array($serviceName)) {
                    $serviceName = $serviceName[app()->getLocale()] ?? (count($serviceName) ? reset($serviceName) : null);
                }
            @endphp
            <div class="small">الخدمة: {{ $serviceName ?? '—' }}</div>
            <div class="small">الرقم: {{ $booking->order_number }}</div>
            <div class="small">Created: {{ $booking->created_at->format('d/m/Y H:i') }}</div>
        </div>

        <div class="box">
            <table>
                <thead>
                    <tr>
                        <th class="text-right">الخدمة</th>
                        <th class="text-right">الكمية</th>
                        <th class="text-right">سعر الوحدة ({{ config('app.currency') }})</th>
                        <th class="text-right">المجموع</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $items = $payload['item'] ?? [];
                        $prices = $payload['price'] ?? [];
                        $qtys = $payload['quantity'] ?? [];
                        $grand = 0;
                    @endphp
                    @for($i=0;$i<count($items);$i++)
                        @php
                            $it = $items[$i] ?? $i;
                            $q = isset($qtys[$i]) ? (int)$qtys[$i] : 0;
                            $p = isset($prices[$i]) ? (float)$prices[$i] : 0;
                            $sub = $q * $p;
                            $grand += $sub;
                        @endphp
                        <tr>
                            <td class="text-right">{{ is_string($it) ? $it : 'Item #' . $i }}</td>
                            <td class="text-right">{{ $q }}</td>
                            <td class="text-right">{{ number_format($p,2) }}</td>
                            <td class="text-right">{{ number_format($sub,2) }}</td>
                        </tr>
                    @endfor
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right"><strong>المجموع الكلي</strong></td>
                        <td class="text-right"><strong>{{ number_format($grand,2) }} {{ config('app.currency') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="box">
            <strong>معلومات إضافية</strong>
            <div>مكان الاستلام: {{ $payload['pickup_location'] ?? '—' }}</div>
            <div>مكان التسليم: {{ $payload['delivery_location'] ?? '—' }}</div>
            <div>تاريخ الاستلام: {{ $payload['pickup_date'] ?? '—' }}</div>
            <div>ملاحظات: {{ $payload['notes'] ?? '—' }}</div>
        </div>

        <div class="small">Generated by {{ config('app.name') }} - Admin: {{ $admin->name ?? 'System' }}</div>
    </div>
</body>
</html>
