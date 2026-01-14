<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Car Wash Invoice - {{ $booking->order_number }}</title>
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
        body { font-family: 'NotoNaskhArabic', 'CairoWeb', 'DejaVu Sans', Arial, sans-serif; font-size:13px; color:#111; direction:rtl; }
        .container { width:100%; padding:18px; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
        .brand { font-size:18px; font-weight:700; }
        .box { border:1px solid #e6e6e6; border-radius:8px; padding:12px; margin-bottom:12px; }
        table { width:100%; border-collapse:collapse; margin-top:8px; }
        th, td { padding:8px 10px; border:1px solid #eee; }
        th { background:#f9f9f9; font-weight:700; }
        .text-right { text-align:right; }
        .text-left { text-align:left; }
        .small { font-size:12px; color:#666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <div class="brand">فاتورة غسيل سيارات</div>
                <div class="small">Order #{{ $booking->order_number }}</div>
            </div>
            <div class="text-left small">
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
            <strong>تفاصيل الحجز</strong>
            @php
                $svc = data_get($booking, 'service.name');
                if (is_array($svc)) {
                    $svc = $svc[app()->getLocale()] ?? (count($svc) ? reset($svc) : null);
                }
                $render = function($v){
                    if (is_null($v)) return '—';
                    if (is_array($v) || is_object($v)) return json_encode((array)$v, JSON_UNESCAPED_UNICODE);
                    return (string)$v;
                };
            @endphp
            <div class="small">الخدمة: {{ $svc ?? '—' }}</div>
            <div class="small">الرقم: {{ $booking->order_number }}</div>
            <div class="small">Created: {{ $booking->created_at->format('d/m/Y H:i') }}</div>
        </div>

        <div class="box">
            <strong>تفاصيل غسيل السيارات</strong>
            @php
                // Ensure Arabic renderer is defined before use
                $renderArabic = $renderArabic ?? function($v) {
                    if (is_null($v)) return '—';
                    if (is_array($v) || is_object($v)) {
                        $arr = (array) $v;
                        if (isset($arr['ar']) && $arr['ar'] !== '') return $arr['ar'];
                        foreach ($arr as $val) {
                            if (is_string($val) && $val !== '') return $val;
                        }
                        return json_encode($arr, JSON_UNESCAPED_UNICODE);
                    }
                    if (is_string($v)) {
                        $trim = trim($v);
                        if (strlen($trim) > 0 && ($trim[0] === '{' || $trim[0] === '[')) {
                            $decoded = json_decode($trim, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                if (isset($decoded['ar']) && $decoded['ar'] !== '') return $decoded['ar'];
                                foreach ($decoded as $val) {
                                    if (is_string($val) && $val !== '') return $val;
                                }
                            }
                        }
                        return $v;
                    }
                    return (string) $v;
                };
            @endphp
            <div class="small">نوع الغسيل: {{ $render($payload['car_wash_type'] ?? null) }}</div>
            <div class="small">عدد السيارات: {{ $render($payload['number_of_cars'] ?? null) }}</div>
            <div class="small">مكان التنظيف: {{ $render($payload['place_of_cleaning'] ?? $payload['location'] ?? null) }}</div>
            <div class="small">ملاحظات: {{ $render($payload['notes'] ?? null) }}</div>
            <div class="small">طريقة الدفع: {{ $renderArabic(data_get($booking, 'paymentMethod.name') ?? data_get($booking, 'payment_method') ?? null) }}</div>
        </div>

        @php
            $additional = $payload['cars_additional_services'] ?? $payload['additional_services'] ?? null;
            $grand = 0;

            $render = function($v){
                if (is_null($v)) return '—';
                if (is_array($v) || is_object($v)) {
                    $arr = (array) $v;
                    $locale = app()->getLocale() ?? config('app.locale', 'en');
                    if (isset($arr[$locale]) && is_string($arr[$locale]) && $arr[$locale] !== '') return $arr[$locale];
                    if (isset($arr['ar']) && is_string($arr['ar']) && $arr['ar'] !== '') return $arr['ar'];
                    if (isset($arr['en']) && is_string($arr['en']) && $arr['en'] !== '') return $arr['en'];
                    foreach ($arr as $val) {
                        if (is_string($val) && $val !== '') return $val;
                    }
                    return json_encode($arr, JSON_UNESCAPED_UNICODE);
                }
                return (string) $v;
            };

            // Render Arabic-only value (handles arrays/objects or JSON strings)
            $renderArabic = function($v) {
                if (is_null($v)) return '—';
                if (is_array($v) || is_object($v)) {
                    $arr = (array) $v;
                    if (isset($arr['ar']) && $arr['ar'] !== '') return $arr['ar'];
                    // fallback to any string
                    foreach ($arr as $val) {
                        if (is_string($val) && $val !== '') return $val;
                    }
                    return json_encode($arr, JSON_UNESCAPED_UNICODE);
                }
                if (is_string($v)) {
                    $trim = trim($v);
                    if (strlen($trim) > 0 && ($trim[0] === '{' || $trim[0] === '[')) {
                        $decoded = json_decode($trim, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            if (isset($decoded['ar']) && $decoded['ar'] !== '') return $decoded['ar'];
                            // fallback
                            foreach ($decoded as $val) {
                                if (is_string($val) && $val !== '') return $val;
                            }
                        }
                    }
                    return $v;
                }
                return (string) $v;
            };

            // Normalize additional services into an array of items
            if (is_string($additional) && trim($additional) !== '') {
                // split by comma or newline
                $parts = preg_split('/[,\n]+/', trim($additional));
                $additional = array_values(array_filter(array_map('trim', $parts)));
            }
        @endphp
        @if((is_array($additional) && count($additional) > 0))
            <div class="box">
                <table>
                    <thead>
                        <tr><th>الخدمة</th><th>الكمية</th><th>سعر الوحدة ({{ config('app.currency','SAR') }})</th><th>المجموع</th></tr>
                    </thead>
                    <tbody>
                        @foreach($additional as $s)
                            @php
                                if(is_array($s) || is_object($s)) {
                                    $sArr = (array)$s;
                                    // prefer localized name
                                    $name = $sArr['name'] ?? $sArr['title'] ?? null;
                                    if (is_array($name) || is_object($name)) {
                                        $name = (is_array($name) ? ($name[app()->getLocale()] ?? $name['ar'] ?? $name['en'] ?? reset($name)) : (string)$name);
                                    }
                                    $name = $name ?? json_encode($sArr, JSON_UNESCAPED_UNICODE);
                                    $qty = isset($sArr['quantity']) ? (int)$sArr['quantity'] : (isset($sArr['qty']) ? (int)$sArr['qty'] : 1);
                                    $unit = isset($sArr['price']) ? (float)$sArr['price'] : (isset($sArr['unit_price']) ? (float)$sArr['unit_price'] : 0.0);
                                } else {
                                    $name = $render($s);
                                    $qty = 1;
                                    $unit = 0.0;
                                }
                                $sub = $qty * $unit; $grand += $sub;
                            @endphp
                            <tr>
                                <td>{{ $name }}</td>
                                <td class="text-right">{{ $qty }}</td>
                                <td class="text-right">{{ number_format($unit,2) }}</td>
                                <td class="text-right">{{ number_format($sub,2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>المجموع الفرعي</strong></td>
                            <td class="text-right"><strong>{{ number_format($grand,2) }} {{ config('app.currency','SAR') }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right"><strong>المجموع (الحجز)</strong></td>
                            <td class="text-right"><strong>{{ number_format($booking->total ?? ($payload['total'] ?? 0),2) }} {{ config('app.currency','SAR') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="box">
                <div class="small">لا توجد خدمات إضافية مفصّلة.</div>
                <div class="small">المجموع (الحجز): <strong>{{ number_format($booking->total ?? ($payload['total'] ?? 0),2) }} {{ config('app.currency','SAR') }}</strong></div>
            </div>
        @endif

        <div class="small">Generated by {{ config('app.name') }} - Admin: {{ $admin->name ?? 'System' }}</div>
    </div>
</body>
</html>
