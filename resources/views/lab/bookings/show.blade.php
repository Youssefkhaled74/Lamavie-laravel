@extends('lab.layouts.main')

@section('content')
<div class="container py-4">
    @php
        $serviceName = data_get($booking, 'service.name');
        if (is_array($serviceName)) {
            $serviceName = $serviceName[app()->getLocale()] ?? ($serviceName['en'] ?? reset($serviceName));
        }

        $customerName = data_get($booking, 'user.name') ?: data_get($booking, 'user.phone', '-');
        $customerPhone = data_get($booking, 'user.phone', '-');

        $paymentName =
            data_get($booking, 'paymentMethod.name.' . app()->getLocale())
            ?? data_get($booking, 'paymentMethod.name.en')
            ?? data_get($booking, 'paymentMethod.name')
            ?? '-';

        $labName = data_get($booking, 'lab.name') ?? '-';
        $labPhone = data_get($booking, 'lab.phone') ?? '-';

        $collectedAt = data_get($booking, 'driver_collected_at');
        $collectedAtText = '-';
        if ($collectedAt) {
            try {
                $collectedAtText = ($collectedAt instanceof \Carbon\Carbon)
                    ? $collectedAt->toDayDateTimeString()
                    : \Carbon\Carbon::parse($collectedAt)->toDayDateTimeString();
            } catch (\Throwable $e) {
                $collectedAtText = (string) $collectedAt;
            }
        }

        $rawPayload = data_get($booking, 'payload_data');
        if (is_array($rawPayload)) {
            $payload = $rawPayload;
        } elseif (is_string($rawPayload)) {
            $payload = json_decode($rawPayload, true) ?? [];
        } elseif (is_object($rawPayload)) {
            $payload = (array) $rawPayload;
        } else {
            $payload = [];
        }

        $items  = $payload['item'] ?? [];
        $prices = $payload['price'] ?? [];
        $qtys   = $payload['quantity'] ?? [];

        $currency = config('app.currency', 'SAR');
        $storedTotal = (float) (data_get($booking, 'total') ?? 0);

        $itemNames = [
            0 => 'Wash & Fold',
            1 => 'Ironing',
            2 => 'Dry Clean',
        ];

        $lineItems = [];
        $grand = 0.0;
        if (is_array($items) && is_array($prices) && is_array($qtys) && count($items) === count($prices) && count($items) === count($qtys)) {
            for ($i = 0; $i < count($items); $i++) {
                $it = $items[$i] ?? $i;
                $q  = isset($qtys[$i]) ? (int) $qtys[$i] : 0;
                $p  = isset($prices[$i]) ? (float) $prices[$i] : 0.0;
                $name = is_numeric($it) ? ($itemNames[(int) $it] ?? "Item #{$i}") : (string) $it;
                $sub = $q * $p;
                $grand += $sub;
                $lineItems[] = ['name' => $name, 'qty' => $q, 'price' => $p, 'subtotal' => $sub];
            }
        }

        $delta = round($storedTotal - $grand, 2);
        $pickupLoc = $payload['pickup_location'] ?? '—';
        $pickupAt  = trim(($payload['pickup_date'] ?? '') . ' ' . ($payload['pickup_time'] ?? ''));
        $deliveryLoc = $payload['delivery_location'] ?? '—';
        $clothesReturned = $payload['clothes_returned'] ?? '—';
    @endphp

    <style>
        .lab-booking-card{
            border: 1px solid rgba(15,23,42,0.08);
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(2,6,23,0.06);
            overflow: hidden;
        }
        .lab-booking-card .card-body{ padding: 18px; }

        .dc-head{ display:flex; gap:14px; align-items:flex-start; flex-wrap:wrap; margin-bottom:14px; }
        .dc-ico{
            width: 46px; height: 46px; border-radius: 14px;
            display:flex; align-items:center; justify-content:center;
            background: linear-gradient(135deg,#0d6efd,#6ea8fe);
            color:#fff;
            box-shadow: 0 10px 20px rgba(13,110,253,0.18);
            flex:0 0 auto;
        }
        .dc-title{ min-width:220px; flex:1; }
        .dc-one-line{ white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .dc-submeta{ display:flex; gap:10px; flex-wrap:wrap; margin-top:10px; }
        .dc-submeta .pill{
            display:inline-flex; align-items:center; gap:8px;
            border-radius: 999px;
            padding: 7px 10px;
            border: 1px solid rgba(15,23,42,0.10);
            background: rgba(255,255,255,0.88);
            color: rgba(15,23,42,0.88);
            font-weight: 800;
            font-size: .86rem;
        }
        .dc-submeta .pill i{ color:#0d6efd; }

        .dc-total{ margin-left:auto; text-align:right; min-width:220px; }
        .total-big{ font-weight:900; font-size:1.45rem; color:#0f172a; line-height:1.1; }
        .dc-warn{
            display:inline-flex; align-items:center; gap:6px;
            margin-left: 8px;
            padding: 3px 8px;
            border-radius: 999px;
            border: 1px solid rgba(245,158,11,0.35);
            background: rgba(245,158,11,0.12);
            color:#b45309;
            font-weight: 900;
            font-size: .82rem;
            vertical-align: middle;
        }

        .dc-grid{ display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:12px; margin-top:8px; }
        @media (max-width: 700px){
            .dc-grid{ grid-template-columns: 1fr; }
            .dc-total{ width:100%; text-align:left; }
        }
        .dc-cell{
            border-radius: 16px;
            border: 1px solid rgba(15,23,42,0.08);
            background: #fff;
            padding: 14px;
        }
        .dc-cell .k{
            font-size: 12px;
            font-weight: 900;
            color: rgba(15,23,42,0.60);
            display:flex;
            align-items:center;
            gap: 8px;
        }
        .dc-cell .k i{ color:#0d6efd; width: 18px; text-align:center; }
        .dc-cell .v{ margin-top: 6px; font-weight: 900; color:#0f172a; }
        .dc-cell .m{ margin-top: 2px; font-size: 12.5px; color: rgba(15,23,42,0.55); }

        .dc-items th{
            text-align:left;
            font-weight: 900;
            font-size: 12.5px;
            color: rgba(15,23,42,0.75);
            padding: 12px;
            border-bottom: 1px solid rgba(15,23,42,0.08);
            background: rgba(248,250,252,0.7);
        }
        .dc-items td{
            padding: 12px;
            border-bottom: 1px solid rgba(15,23,42,0.06);
            vertical-align: middle;
            color: rgba(15,23,42,0.88);
            font-size: 14px;
        }
        .dc-items .price, .dc-items .subtotal{ text-align:right; white-space:nowrap; }
        .dc-actions{ display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
        .dc-actions .btn{ border-radius: 12px; }
        .soft-divider{ border: none; border-top: 1px solid rgba(15,23,42,0.08); margin: 18px 0; }
    </style>
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('lab.dashboard') }}" class="btn btn-sm btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left me-2"></i> Back to dashboard
            </a>
            <div class="text-muted small">/</div>
            <div class="small text-muted">Booking Details</div>
        </div>
        <h3 class="mb-0">Order #{{ $booking->order_number }}</h3>
    </div>

    <div class="card mb-3 lab-booking-card">
        <div class="card-body">
            <div class="dc-head">
                <div class="dc-ico"><i class="fas fa-bag-shopping"></i></div>

                <div class="dc-title" style="min-width:0">
                    <div class="fw-bold">Booking details</div>
                    <div class="text-muted small dc-one-line">
                        Service: <span class="fw-semibold">{{ $serviceName ?? '-' }}</span>
                    </div>
                    <div class="dc-submeta">
                        <span class="pill"><i class="fas fa-user"></i>{{ $customerName }}</span>
                        <span class="pill"><i class="fas fa-phone"></i>{{ $customerPhone }}</span>
                        <span class="pill"><i class="fas fa-credit-card"></i>{{ $paymentName }}</span>
                        <span class="pill"><i class="fas fa-circle-info"></i>{{ ucfirst($booking->status) }}</span>
                    </div>
                </div>

                <div class="dc-total">
                    <div class="text-muted small">Total</div>
                    <div class="total-big">{{ number_format($storedTotal, 2) }} {{ $currency }}</div>
                    <div class="text-muted small">
                        Calculated: {{ number_format($grand, 2) }} {{ $currency }}
                        @if(count($lineItems) && abs($delta) >= 0.01)
                            <span class="dc-warn" title="Stored total does not match calculated items total.">
                                <i class="fas fa-triangle-exclamation"></i>
                                Δ {{ number_format($delta, 2) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="dc-grid">
                <div class="dc-cell">
                    <div class="k"><i class="fas fa-flask"></i>Lab assigned</div>
                    <div class="v" id="lab_assigned_info">{{ $labName }}</div>
                    <div class="m">{{ $labPhone }}</div>
                </div>
                <div class="dc-cell">
                    <div class="k"><i class="fas fa-location-dot"></i>Pickup</div>
                    <div class="v">{{ $pickupLoc }}</div>
                    <div class="m">{{ $pickupAt !== '' ? $pickupAt : '—' }}</div>
                </div>
                <div class="dc-cell">
                    <div class="k"><i class="fas fa-map-marker-alt"></i>Delivery</div>
                    <div class="v">{{ $deliveryLoc }}</div>
                    <div class="m">—</div>
                </div>
                <div class="dc-cell">
                    <div class="k"><i class="fas fa-shirt"></i>Clothes returned</div>
                    <div class="v">{{ $clothesReturned }}</div>
                    <div class="m">—</div>
                </div>
            </div>

            <div class="dc-grid mt-2">
                <div class="dc-cell">
                    <div class="k"><i class="fas fa-calendar-check"></i>Assigned at</div>
                    <div class="v" id="lab_assigned_time">{{ $booking->lab_assigned_at?->toDayDateTimeString() ?? '-' }}</div>
                    <div class="m">—</div>
                </div>
                <div class="dc-cell">
                    <div class="k"><i class="fas fa-warehouse"></i>Arrived at lab</div>
                    <div class="v" id="lab_arrived_time">{{ $booking->lab_arrived_at?->toDayDateTimeString() ?? '-' }}</div>
                    <div class="m">—</div>
                </div>
                <div class="dc-cell">
                    <div class="k"><i class="fas fa-box-open"></i>Picked from lab</div>
                    <div class="v" id="lab_picked_time">{{ $booking->lab_picked_at?->toDayDateTimeString() ?? '-' }}</div>
                    <div class="m">—</div>
                </div>
                <div class="dc-cell">
                    <div class="k"><i class="fas fa-handshake"></i>Collected</div>
                    <div class="v">{{ $collectedAtText }}</div>
                    <div class="m">—</div>
                </div>
            </div>

            <hr class="soft-divider">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="fw-bold">
                    <i class="fas fa-list-check me-2 text-primary"></i>Items
                    <span class="text-muted fw-normal ms-2">({{ count($lineItems) }})</span>
                </div>

                @if($lab && $booking->lab_id === $lab->id)
                    <div class="dc-actions">
                        @if(!$booking->lab_arrived_at)
                            <form method="post" action="{{ route('lab.bookings.arrivedAtLab', $booking->id) }}" class="ajax-action">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-warehouse me-2"></i>Mark arrived
                                </button>
                            </form>
                        @endif

                        @if($booking->lab_arrived_at && !$booking->lab_picked_at)
                            <form method="post" action="{{ route('lab.bookings.pickedFromLab', $booking->id) }}" class="ajax-action">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-box-open me-2"></i>Mark picked
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>

            <div class="table-responsive mt-2">
                <table class="table table-sm align-middle mb-0 dc-items" role="table" aria-label="Order items">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th style="width:110px">Qty</th>
                            <th style="width:140px" class="price">Unit ({{ $currency }})</th>
                            <th style="width:140px" class="subtotal">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($lineItems))
                            @foreach($lineItems as $li)
                                <tr>
                                    <td class="fw-semibold">{{ $li['name'] }}</td>
                                    <td>{{ $li['qty'] }}</td>
                                    <td class="price">{{ number_format((float)$li['price'], 2) }}</td>
                                    <td class="subtotal fw-bold">{{ number_format((float)$li['subtotal'], 2) }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-muted">No items found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end gap-3 mt-3 text-muted small">
                <div><strong>Items total:</strong> {{ number_format($grand, 2) }} {{ $currency }}</div>
            </div>
        </div>
    </div>

    <!-- Visual timeline -->
    <div class="card mb-3">
        <div class="card-body">
            <h6 class="mb-3">Booking timeline</h6>
            <div class="d-flex flex-column gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-light border" style="width:40px;height:40px;display:grid;place-items:center">1</div>
                    <div>
                        <div class="fw-semibold">Assigned to lab</div>
                        <div class="small text-muted">{{ $booking->lab_assigned_at?->toDayDateTimeString() ?? '—' }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-light border" style="width:40px;height:40px;display:grid;place-items:center">2</div>
                    <div>
                        <div class="fw-semibold">Arrived at lab</div>
                        <div class="small text-muted">{{ $booking->lab_arrived_at?->toDayDateTimeString() ?? '—' }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-light border" style="width:40px;height:40px;display:grid;place-items:center">3</div>
                    <div>
                        <div class="fw-semibold">Picked from lab</div>
                        <div class="small text-muted">{{ $booking->lab_picked_at?->toDayDateTimeString() ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky mobile action bar for lab users -->
    <style>
        @media (max-width: 767px) {
            .lab-mobile-action-bar { position: fixed; left: 0; right: 0; bottom: 0; z-index: 1050; padding: 10px; background: rgba(255,255,255,0.98); box-shadow: 0 -4px 20px rgba(2,6,23,0.08); }
            .lab-mobile-action-bar .btn { font-size: 14px; padding: 10px 12px; }
            body { padding-bottom: 76px; }
        }
    </style>

    <div class="lab-mobile-action-bar d-md-none d-flex gap-2 p-2">
        @if($lab && $booking->lab_id === $lab->id)
            <form method="post" action="{{ route('lab.bookings.arrivedAtLab', $booking->id) }}" class="flex-grow-1 ajax-action">
                @csrf
                <input type="hidden" name="lab_id" id="lab_mobile_arrive_id" value="{{ $booking->lab_id }}">
                @if(!$booking->lab_arrived_at)
                    <button type="submit" class="btn btn-sm btn-success w-100">Mark arrived</button>
                @else
                    <button type="button" class="btn btn-sm btn-outline-secondary w-100" disabled>Arrived</button>
                @endif
            </form>
            <form method="post" action="{{ route('lab.bookings.pickedFromLab', $booking->id) }}" class="ajax-action">
                @csrf
                <input type="hidden" name="lab_id" id="lab_mobile_picked_id" value="{{ $booking->lab_id }}">
                @if($booking->lab_arrived_at && !$booking->lab_picked_at)
                    <button class="btn btn-sm btn-primary">Mark picked</button>
                @else
                    <button type="button" class="btn btn-sm btn-outline-secondary" disabled>Picked</button>
                @endif
            </form>
        @endif
    </div>

    <script>
        // ensure mobile hidden inputs are populated (in case of client-side changes)
        (function(){
            try {
                const arrive = document.getElementById('lab_mobile_arrive_id');
                const picked = document.getElementById('lab_mobile_picked_id');
                const v = '{{ $booking->lab_id ?? ($lab->id ?? '') }}';
                if(arrive && !arrive.value) arrive.value = v;
                if(picked && !picked.value) picked.value = v;
            } catch(e){}
        })();
    </script>

    @push('scripts')
    <script>
        (function(){
            function showToast(msg, success=true){
                const el = document.createElement('div');
                el.className = 'toast-notification position-fixed bottom-0 end-0 m-3 p-3 rounded shadow';
                el.style.zIndex = 1200;
                el.style.background = success ? '#0f5132' : '#842029';
                el.style.color = '#fff';
                el.textContent = msg;
                document.body.appendChild(el);
                setTimeout(()=> el.remove(), 3200);
            }

            async function postAjaxForm(form){
                const data = new FormData(form);
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {})
                    },
                    body: data,
                    credentials: 'same-origin'
                });

                const contentType = (res.headers.get('content-type') || '').toLowerCase();
                if(!contentType.includes('application/json')){
                    if(res.redirected && res.url){
                        window.location.href = res.url;
                        return { json: null };
                    }
                    if(res.ok){
                        window.location.reload();
                        return { json: null };
                    }
                    throw new Error(`HTTP ${res.status}`);
                }

                const json = await res.json();
                return { json };
            }

            document.querySelectorAll('form.ajax-action').forEach(form => {
                if(form.dataset.ajaxBound === '1') return;
                form.dataset.ajaxBound = '1';

                form.addEventListener('submit', async function(e){
                    e.preventDefault();
                    const f = e.currentTarget;
                    const btns = Array.from(f.querySelectorAll('button[type="submit"], button:not([type])'));
                    btns.forEach(b => b.disabled = true);
                    f.setAttribute('aria-busy', 'true');

                    try{
                        const { json } = await postAjaxForm(f);
                        if(json && (json.success || json.success === undefined)){
                            showToast(json.message || 'Updated', true);
                            const b = json.booking || {};
                            if(b.lab_assigned_at) document.getElementById('lab_assigned_time')?.textContent = new Date(b.lab_assigned_at).toLocaleString();
                            if(b.lab_arrived_at) document.getElementById('lab_arrived_time')?.textContent = new Date(b.lab_arrived_at).toLocaleString();
                            if(b.lab_picked_at) document.getElementById('lab_picked_time')?.textContent = new Date(b.lab_picked_at).toLocaleString();
                            if(b.lab && b.lab.name){
                                const txt = b.lab.name + (b.lab.phone ? ' - ' + b.lab.phone : '');
                                document.getElementById('lab_assigned_info')?.textContent = txt;
                            }
                        }else if(json){
                            showToast(json.message || 'Action failed', false);
                        }
                    }catch(err){
                        showToast('Request failed. Please try again.', false);
                    }finally{
                        f.removeAttribute('aria-busy');
                        btns.forEach(b => b.disabled = false);
                    }
                });
            });
        })();
    </script>
    @endpush
</div>
@endsection
