@extends('dashboard.admin.layouts.main')

@section('content')
<div class="booking-page">

    {{-- Keep your existing shared styles --}}
    @include('dashboard.admin.bookings._booking_styles')

    <style>
        /* ===== Booking Details UI (Scoped) ===== */
        .bk-wrap{
            --bk-primary:#0d6efd;
            --bk-ink:#0f172a;
            --bk-muted:#64748b;
            --bk-border: rgba(15,23,42,.08);
            --bk-bg:#ffffff;
            --bk-surface:#ffffff;
            --bk-soft: rgba(13,110,253,.10);
            --bk-soft2: rgba(16,185,129,.10);
            --bk-warn: rgba(245,158,11,.12);
            --bk-danger: rgba(239,68,68,.10);
            --bk-shadow: 0 18px 45px rgba(2,6,23,.08);
            --bk-shadow2: 0 10px 26px rgba(2,6,23,.06);
        }

        .booking-head-card{
            background:
                radial-gradient(900px 220px at 0% 0%, rgba(13,110,253,.16), transparent 55%),
                radial-gradient(900px 220px at 100% 0%, rgba(16,185,129,.10), transparent 60%),
                linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,255,255,1));
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 18px;
            padding: 18px 18px;
            box-shadow: 0 10px 30px rgba(2,6,23,0.06);
            position: relative;
            overflow: hidden;
        }
        .booking-head-card:before{
            content:"";
            position:absolute;
            left:0; top:0; bottom:0;
            width:6px;
            background: linear-gradient(180deg,#0d6efd,#6ea8fe);
            border-radius: 18px 0 0 18px;
        }

        .chip{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding: 7px 12px;
            border-radius: 999px;
            border: 1px solid rgba(15,23,42,0.08);
            background: rgba(255,255,255,.9);
            font-weight: 900;
            font-size: .88rem;
            color: #0f172a;
            white-space: nowrap;
        }
        .chip-success{ background: rgba(16,185,129,0.10); border-color: rgba(16,185,129,0.22); color:#059669; }
        .chip-danger{ background: rgba(239,68,68,0.10); border-color: rgba(239,68,68,0.22); color:#dc2626; }
        .chip-warning{ background: rgba(245,158,11,0.12); border-color: rgba(245,158,11,0.25); color:#b45309; }
        .chip-info{ background: rgba(59,130,246,0.10); border-color: rgba(59,130,246,0.22); color:#2563eb; }

        .icon-box{
            width:46px;height:46px;border-radius:14px;
            display:flex;align-items:center;justify-content:center;
            background: linear-gradient(135deg,#0d6efd,#6ea8fe);
            color:#fff;
            box-shadow: 0 10px 20px rgba(13,110,253,0.18);
            flex:0 0 auto;
        }

        .card-modern{
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 18px;
            background: #fff;
            box-shadow: var(--bk-shadow2);
            overflow: hidden;
        }

        .section-title{
            display:flex;
            align-items:center;
            gap:12px;
            padding: 14px 16px;
            border-bottom: 1px solid rgba(15,23,42,0.06);
            background: linear-gradient(180deg, rgba(13,110,253,0.06), rgba(255,255,255,0));
        }
        .label{ font-weight: 900; color: var(--bk-ink); }
        .muted{ color: var(--bk-muted); font-weight: 700; font-size: .9rem; }
        .total-big{ font-weight: 950; font-size: 1.45rem; color:#0f172a; line-height: 1.1; }
        .soft-divider{ border: none; border-top: 1px solid rgba(15,23,42,0.06); margin: 18px 0; }

        .info-cards{
            display:flex;
            gap:14px;
            flex-wrap: wrap;
            padding: 14px 16px 16px;
        }
        .info-card{
            flex: 1;
            min-width: 200px;
            border-radius: 16px;
            border: 1px solid rgba(15,23,42,0.06);
            padding: 14px 14px;
            background: rgba(248,250,252,.8);
        }
        .info-card .value{ font-weight: 900; color: var(--bk-ink); }

        /* Right column stack */
        .aside-stack{ display:flex; flex-direction:column; gap: 12px; }

        /* Profile style cards (Customer/Lab/Driver) */
        .profile-card{
            border: 1px solid rgba(15,23,42,0.06);
            border-radius: 18px;
            background:#fff;
            box-shadow: var(--bk-shadow);
            overflow:hidden;
        }

        .profile-head{
            padding: 14px 16px;
            display:flex;
            align-items:center;
            justify-content: space-between;
            gap: 12px;
            border-bottom: 1px solid rgba(15,23,42,0.06);
            background:
                radial-gradient(700px 180px at 0% 0%, rgba(13,110,253,.12), transparent 55%),
                radial-gradient(600px 180px at 100% 0%, rgba(16,185,129,.10), transparent 55%),
                linear-gradient(180deg, rgba(255,255,255,.96), rgba(255,255,255,1));
        }

        .head-left{
            display:flex;
            align-items:center;
            gap: 12px;
            min-width: 0;
        }

        .avatar{
            width: 54px; height: 54px;
            border-radius: 18px;
            overflow:hidden;
            border: 1px solid rgba(15,23,42,0.08);
            background:#f1f5f9;
            display:flex; align-items:center; justify-content:center;
            flex: 0 0 auto;
            position: relative;
        }
        .avatar img{ width:100%; height:100%; object-fit:cover; }
        .avatar span{ font-weight: 950; color:#334155; font-size: 1.05rem; }

        .avatar .role-badge{
            position:absolute;
            left: 6px;
            bottom: 6px;
            font-size: 10px;
            font-weight: 900;
            padding: 4px 7px;
            border-radius: 999px;
            border: 1px solid rgba(15,23,42,0.10);
            background: rgba(255,255,255,.92);
            color: #0f172a;
        }

        .profile-name{
            font-weight: 950;
            color: var(--bk-ink);
            margin:0;
            line-height: 1.2;
            white-space: nowrap;
            overflow:hidden;
            text-overflow: ellipsis;
            max-width: 250px;
        }
        .profile-sub{
            margin:4px 0 0;
            color: var(--bk-muted);
            font-weight: 800;
            font-size:.9rem;
            white-space: nowrap;
            overflow:hidden;
            text-overflow: ellipsis;
            max-width: 300px;
        }

        .head-right{
            display:flex;
            flex-direction: column;
            align-items:flex-end;
            gap: 6px;
            flex: 0 0 auto;
        }

        .tiny{
            font-size: 12px;
            color: #94a3b8;
            font-weight: 800;
        }

        .profile-body{ padding: 14px 16px 16px; }

        .meta-row{
            display:flex;
            gap: 10px;
            align-items:flex-start;
            padding: 10px 10px;
            border-radius: 16px;
            border: 1px solid rgba(15,23,42,0.06);
            background: rgba(248,250,252,.8);
            margin-bottom: 10px;
        }
        .meta-row i{
            width: 22px;
            text-align:center;
            color: var(--bk-primary);
            margin-top: 2px;
        }
        .meta-k{ font-size:.82rem; color: var(--bk-muted); font-weight: 900; margin-bottom: 1px; }
        .meta-v{ font-weight: 900; color: var(--bk-ink); word-break: break-word; }

        .actions-grid{
            display:grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 10px;
        }
        .actions-grid.one{ grid-template-columns: 1fr; }

        .btn-soft{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap: 8px;
            border-radius: 14px;
            padding: 11px 12px;
            font-weight: 950;
            border: 1px solid rgba(15,23,42,0.08);
            transition: transform .08s ease, box-shadow .15s ease, background .15s ease;
            text-decoration:none !important;
            width: 100%;
        }
        .btn-soft:hover{ transform: translateY(-1px); box-shadow: 0 10px 24px rgba(2,6,23,0.08); }
        .btn-soft-primary{ background: linear-gradient(135deg,#0d6efd,#6ea8fe); color:#fff; border-color: transparent; }
        .btn-soft-outline{ background:#fff; color:#0f172a; }
        .btn-soft-success{ background: linear-gradient(135deg,#10b981,#34d399); color:#fff; border-color: transparent; }

        .empty-pill{
            display:inline-flex;
            align-items:center;
            gap: 8px;
            padding: 8px 10px;
            border-radius: 999px;
            font-weight: 950;
            font-size: 12px;
            background: rgba(245,158,11,.14);
            border: 1px solid rgba(245,158,11,.25);
            color: #b45309;
        }

        /* Responsive booking grid */
        .booking-grid{
            display:grid;
            grid-template-columns: 1fr 420px;
            gap: 14px;
        }
        @media (max-width: 1100px){
            .booking-grid{ grid-template-columns: 1fr; }
            .profile-name{ max-width: 100%; }
            .profile-sub{ max-width: 100%; }
        }
        @media (max-width: 520px){
            .actions-grid{ grid-template-columns: 1fr; }
        }

        /* ===== Timeline (NEW premium UI) ===== */
        .timeline-wrap{
            padding: 14px 16px 16px;
            background:
                radial-gradient(900px 240px at 0% 0%, rgba(13,110,253,.10), transparent 55%),
                radial-gradient(900px 240px at 100% 0%, rgba(16,185,129,.08), transparent 60%),
                linear-gradient(180deg, rgba(255,255,255,1), rgba(248,250,252,.9));
        }

        .timeline-top{
            display:flex;
            align-items:flex-start;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .timeline-chips{
            display:flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items:center;
        }

        .stat-chip{
            display:inline-flex;
            align-items:center;
            gap: 8px;
            padding: 8px 10px;
            border-radius: 999px;
            border: 1px solid rgba(15,23,42,0.08);
            background: rgba(255,255,255,.92);
            font-weight: 950;
            color: var(--bk-ink);
            font-size: 12px;
        }
        .stat-chip i{ color: var(--bk-primary); }
        .stat-chip.ok i{ color:#059669; }
        .stat-chip.wait i{ color:#b45309; }

        .timeline-grid{
            display:grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 12px;
        }

        .timeline{
            border: 1px solid rgba(15,23,42,0.06);
            background: rgba(255,255,255,.9);
            border-radius: 18px;
            box-shadow: 0 10px 24px rgba(2,6,23,.05);
            padding: 14px 14px;
        }

        .t-item{
            display:grid;
            grid-template-columns: 28px 1fr auto;
            gap: 12px;
            align-items:flex-start;
            padding: 12px 6px;
            position: relative;
        }
        .t-item:not(:last-child){
            border-bottom: 1px dashed rgba(15,23,42,0.10);
        }

        .t-dot{
            width: 18px; height: 18px;
            border-radius: 999px;
            border: 2px solid rgba(148,163,184,.7);
            background: #fff;
            margin-top: 2px;
            position: relative;
        }
        .t-dot::after{
            content:"";
            position:absolute;
            inset: 4px;
            border-radius: 999px;
            background: rgba(148,163,184,.6);
        }
        .t-item.done .t-dot{
            border-color: rgba(16,185,129,.55);
            box-shadow: 0 0 0 6px rgba(16,185,129,.10);
        }
        .t-item.done .t-dot::after{ background: #10b981; }

        .t-item.pending .t-dot{
            border-color: rgba(245,158,11,.55);
            box-shadow: 0 0 0 6px rgba(245,158,11,.10);
        }
        .t-item.pending .t-dot::after{ background: #f59e0b; }

        .t-title{
            font-weight: 950;
            color: var(--bk-ink);
            margin:0;
            line-height: 1.2;
        }
        .t-sub{
            margin: 5px 0 0;
            color: var(--bk-muted);
            font-weight: 800;
            font-size: 12px;
        }
        .t-badge{
            display:inline-flex;
            align-items:center;
            gap: 7px;
            font-weight: 950;
            font-size: 12px;
            padding: 7px 10px;
            border-radius: 999px;
            border: 1px solid rgba(15,23,42,0.08);
            background: rgba(255,255,255,.96);
            white-space: nowrap;
        }
        .t-item.done .t-badge{
            border-color: rgba(16,185,129,.22);
            background: rgba(16,185,129,.10);
            color:#059669;
        }
        .t-item.pending .t-badge{
            border-color: rgba(245,158,11,.25);
            background: rgba(245,158,11,.12);
            color:#b45309;
        }

        .timeline-table{
            border: 1px solid rgba(15,23,42,0.06);
            background: rgba(255,255,255,.9);
            border-radius: 18px;
            box-shadow: 0 10px 24px rgba(2,6,23,.05);
            overflow:hidden;
        }
        .timeline-table .tt-head{
            padding: 12px 14px;
            border-bottom: 1px solid rgba(15,23,42,0.06);
            background: linear-gradient(180deg, rgba(13,110,253,0.06), rgba(255,255,255,0));
            display:flex;
            align-items:center;
            justify-content: space-between;
            gap: 10px;
        }
        .tt-title{
            font-weight: 950;
            color: var(--bk-ink);
            margin:0;
        }
        .tt-sub{
            margin: 0;
            color: var(--bk-muted);
            font-size: 12px;
            font-weight: 800;
        }
        .tt-body{ padding: 10px 12px 12px; }
        .tt-row{
            display:flex;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 10px;
            border-radius: 14px;
            border: 1px solid rgba(15,23,42,0.06);
            background: rgba(248,250,252,.8);
            margin-bottom: 10px;
        }
        .tt-row:last-child{ margin-bottom:0; }
        .tt-k{
            font-weight: 950;
            color: var(--bk-ink);
            display:flex;
            align-items:center;
            gap: 8px;
        }
        .tt-k i{ color: var(--bk-primary); }
        .tt-v{
            font-weight: 900;
            color: #0f172a;
            text-align:right;
            white-space: nowrap;
        }
        .tt-v.muted{
            color: #94a3b8 !important;
            font-weight: 900;
        }

        @media (max-width: 1100px){
            .timeline-grid{ grid-template-columns: 1fr; }
        }
        @media (max-width: 520px){
            .t-item{ grid-template-columns: 24px 1fr; }
            .t-badge{ grid-column: 2 / -1; justify-self: start; margin-top: 6px; }
        }
    </style>

    <div class="bk-wrap">

        {{-- Page Header --}}
        <div class="content-header fade-in mb-3">
            <div class="booking-head-card">
                <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                    <div>
                        <h1 class="fw-bold text-primary mb-1">Booking Details</h1>
                        <p class="text-muted mb-0">View and manage details of the selected booking.</p>

                        <div class="d-flex flex-wrap gap-2 mt-3">
                            @if(data_get($booking,'order_number'))
                                <span class="chip">
                                    <i class="fas fa-hashtag"></i>
                                    {{ data_get($booking,'order_number') }}
                                </span>
                            @endif

                            @php
                                $status = strtolower(data_get($booking,'status','pending'));
                                $statusUi = match($status){
                                    'completed' => ['text'=>'Completed','cls'=>'chip-success','icon'=>'fa-check-circle'],
                                    'cancelled','canceled' => ['text'=>'Cancelled','cls'=>'chip-danger','icon'=>'fa-times-circle'],
                                    'in_progress' => ['text'=>'In Progress','cls'=>'chip-info','icon'=>'fa-spinner'],
                                    'pickup','picked_up' => ['text'=>'Pickup','cls'=>'chip-warning','icon'=>'fa-truck'],
                                    default => ['text'=>ucfirst($status),'cls'=>'chip-warning','icon'=>'fa-hourglass-half'],
                                };
                            @endphp

                            <span class="chip {{ $statusUi['cls'] }}">
                                <i class="fas {{ $statusUi['icon'] }}"></i>
                                {{ $statusUi['text'] }}
                            </span>

                            @if(data_get($booking,'created_at'))
                                <span class="chip">
                                    <i class="fas fa-calendar-alt"></i>
                                    {{ \Carbon\Carbon::parse(data_get($booking,'created_at'))->format('Y-m-d H:i') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                        <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#invoiceModal">
                            <i class="fas fa-file-invoice me-2"></i>Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @php
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

            $itemNames = [
                0 => 'مصبغة',
                1 => 'تنظيف',
                2 => 'بطانية',
            ];

            $grand = 0;

            $serviceName =
                data_get($booking, 'service.name.'.app()->getLocale())
                ?? data_get($booking, 'service.name.en')
                ?? data_get($booking, 'service.name')
                ?? 'N/A';

            $user = data_get($booking, 'user');
            $userName = data_get($user, 'name', '');
            $initials = collect(preg_split('/\s+/', trim($userName)))
                ->filter()
                ->map(fn($p) => mb_substr($p, 0, 1))
                ->take(2)
                ->join('');

            $photoPath = data_get($user, 'profile_photo');
            $photoUrl = null;
            if ($photoPath) {
                $clean = ltrim($photoPath, '/');
                try {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($clean)) {
                        $photoUrl = asset('storage/' . $clean);
                    }
                } catch (\Throwable $e) {
                    $photoUrl = null;
                }
            }

            $payName = data_get($booking, 'paymentMethod.name.' . app()->getLocale())
                ?? data_get($booking, 'paymentMethod.name.en')
                ?? data_get($booking, 'paymentMethod.name')
                ?? '—';

            $createdAt = data_get($booking, 'created_at');
            $updatedAt = data_get($booking, 'updated_at');

            $lab = data_get($booking, 'lab');

            // Timeline values
            $timeline = [
                ['key'=>'created_at', 'label'=>'Created', 'icon'=>'fa-plus-circle', 'value'=>optional($booking->created_at)->format('Y-m-d H:i')],
                ['key'=>'updated_at', 'label'=>'Last Updated', 'icon'=>'fa-pen', 'value'=>optional($booking->updated_at)->format('Y-m-d H:i')],
                ['key'=>'lab_assigned_at', 'label'=>'Lab Assigned', 'icon'=>'fa-flask', 'value'=>optional($booking->lab_assigned_at)->format('Y-m-d H:i')],
                ['key'=>'lab_arrived_at', 'label'=>'Lab Arrived', 'icon'=>'fa-map-marker-alt', 'value'=>optional($booking->lab_arrived_at)->format('Y-m-d H:i')],
                ['key'=>'lab_picked_at', 'label'=>'Lab Picked', 'icon'=>'fa-box-open', 'value'=>optional($booking->lab_picked_at)->format('Y-m-d H:i')],
                ['key'=>'driver_collected_at', 'label'=>'Driver Collected', 'icon'=>'fa-truck-loading', 'value'=>optional($booking->driver_collected_at)->format('Y-m-d H:i')],
                ['key'=>'driver_returned_at', 'label'=>'Returned to User', 'icon'=>'fa-people-roof', 'value'=>optional($booking->driver_returned_at)->format('Y-m-d H:i')],
            ];

            $doneCount = collect($timeline)->filter(fn($t)=>!empty($t['value']))->count();
            $totalCount = count($timeline);
            $pendingCount = $totalCount - $doneCount;

            $rawPickupId = $booking->getOriginal('pickup_driver_id');
            $rawDeliveryId = $booking->getOriginal('delivery_driver_id');
        @endphp

        <div class="booking-grid mt-3">
            {{-- LEFT CARD --}}
            <div class="card-modern">
                <div class="section-title">
                    <div class="icon-box">
                        <i class="fas fa-shopping-cart"></i>
                    </div>

                    <div style="min-width:0">
                        <div class="label">Order Summary</div>
                        <div class="muted" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            Service: <span class="fw-semibold">{{ $serviceName }}</span>
                        </div>
                    </div>

                    <div class="ms-auto text-end">
                        <div class="label">{{ __('Total') }}</div>
                        <div class="total-big">
                            {{ number_format((float)($booking->total ?? 0), 2) }} {{ config('app.currency') }}
                        </div>

                        <div class="mt-2">
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#timelineModal">
                                <i class="fas fa-stream me-1"></i> {{ __('Show Timeline') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="items-table" role="table" aria-label="Order items">
                        <thead>
                        <tr>
                            <th>Item</th>
                            <th style="width:110px">Quantity</th>
                            <th style="width:140px" class="price">Unit Price ({{ config('app.currency') }})</th>
                            <th style="width:140px" class="subtotal">Subtotal</th>
                        </tr>
                        </thead>

                        <tbody>
                        @if(is_array($items) && count($items))
                            @for($i=0; $i<count($items); $i++)
                                @php
                                    $it = $items[$i] ?? $i;

                                    $q  = isset($qtys[$i]) ? (int) $qtys[$i] : 0;
                                    $p  = isset($prices[$i]) ? (float) $prices[$i] : 0;

                                    $name = '-';
                                    if (is_numeric($it)) {
                                        $name = $itemNames[(int)$it] ?? "Item #{$i}";
                                    } else {
                                        $name = (string) $it;
                                    }

                                    $sub  = $q * $p;
                                    $grand += $sub;
                                @endphp

                                <tr>
                                    <td class="fw-semibold">{{ $name }}</td>
                                    <td>{{ $q }}</td>
                                    <td class="price">{{ number_format($p, 2) }}</td>
                                    <td class="subtotal fw-bold">{{ number_format($sub, 2) }}</td>
                                </tr>
                            @endfor
                        @else
                            <tr>
                                <td colspan="4" class="text-muted">No items found.</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>

                <div class="totals" style="padding: 0 16px 16px;">
                    <div class="label">Calculated Total</div>
                    <div style="min-width:140px; text-align:right;" class="fw-bold">
                        {{ number_format($grand, 2) }} {{ config('app.currency') }}
                    </div>
                </div>

                <hr class="soft-divider">

                <div class="info-cards">
                    <div class="info-card">
                        <div class="label">Pickup</div>
                        <div class="value">{{ $payload['pickup_location'] ?? '—' }}</div>
                        <div class="muted">{{ ($payload['pickup_date'] ?? '') . ' ' . ($payload['pickup_time'] ?? '') }}</div>
                    </div>

                    <div class="info-card">
                        <div class="label">Delivery</div>
                        <div class="value">{{ $payload['delivery_location'] ?? '—' }}</div>
                    </div>

                    <div class="info-card">
                        <div class="label">Clothes Returned</div>
                        <div class="value">{{ $payload['clothes_returned'] ?? '—' }}</div>
                    </div>

                    <div class="info-card">
                        <div class="label">Collected</div>
                        <div class="value">
                            @php $col = data_get($booking, 'driver_collected_at'); @endphp
                            @if($col)
                                {{ \Carbon\Carbon::parse($col)->format('Y-m-d H:i') }}
                            @else
                                —
                            @endif
                        </div>
                    </div>
                </div>

                @if(!empty($payload['notes']))
                    <hr class="soft-divider">
                    <div style="padding: 0 16px 16px;">
                        <div class="label">Notes</div>
                        <div class="value">{{ $payload['notes'] }}</div>
                    </div>
                @endif
            </div>

            {{-- RIGHT ASIDE --}}
            <aside class="aside-stack">

                {{-- Customer Card --}}
                <div class="profile-card">
                    <div class="profile-head">
                        <div class="head-left">
                            <div class="avatar" title="{{ data_get($user,'name','') }}">
                                @if($photoUrl)
                                    <img src="{{ $photoUrl }}" alt="avatar">
                                @else
                                    <span>{{ $initials ?: '?' }}</span>
                                @endif
                                <span class="role-badge">Customer</span>
                            </div>

                            <div style="min-width:0">
                                <p class="profile-name mb-0">{{ data_get($user,'name','N/A') }}</p>
                                <p class="profile-sub mb-0">
                                    <i class="fas fa-phone-alt me-1"></i> {{ data_get($user,'phone','—') }}
                                </p>
                            </div>
                        </div>

                        <div class="head-right">
                            <span class="chip {{ $statusUi['cls'] }}" style="font-size:12px; padding:6px 10px;">
                                <i class="fas {{ $statusUi['icon'] }}"></i> {{ $statusUi['text'] }}
                            </span>
                            <span class="tiny">
                                @if($createdAt)
                                    <i class="far fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($createdAt)->format('d M Y') }}
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="profile-body">
                        <div class="meta-row">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <div class="meta-k">Email</div>
                                <div class="meta-v">{{ data_get($user,'email','—') }}</div>
                            </div>
                        </div>

                        <div class="meta-row">
                            <i class="fas fa-credit-card"></i>
                            <div>
                                <div class="meta-k">Payment</div>
                                <div class="meta-v">{{ $payName }}</div>
                            </div>
                        </div>

                        <div class="meta-row" style="margin-bottom:0;">
                            <i class="fas fa-clock"></i>
                            <div>
                                <div class="meta-k">Last Update</div>
                                <div class="meta-v">
                                    @if($updatedAt)
                                        {{ \Carbon\Carbon::parse($updatedAt)->format('d M Y, H:i') }}
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="actions-grid">
                            <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn-soft btn-soft-primary">
                                <i class="fas fa-edit"></i> Update
                            </a>

                            <button type="button" class="btn-soft btn-soft-outline" data-bs-toggle="modal" data-bs-target="#invoiceModal">
                                <i class="fas fa-file-invoice"></i> Invoice
                            </button>

                            <a href="{{ route('admin.bookings.index') }}" class="btn-soft btn-soft-outline">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>

                                    <button type="button" class="btn-soft btn-soft-outline" data-bs-toggle="modal" data-bs-target="#notifyModal">
                                        <i class="fas fa-bell"></i> Send Notification
                                    </button>

                            @if(data_get($user,'phone'))
                                <a href="tel:{{ data_get($user,'phone') }}" class="btn-soft btn-soft-success">
                                    <i class="fas fa-phone"></i> Call
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Lab Card --}}
                <div class="profile-card">
                    <div class="profile-head">
                        <div class="head-left">
                            <div class="avatar">
                                <span>{{ $lab ? mb_substr((string)data_get($lab,'name','L'),0,1) : 'L' }}</span>
                                <span class="role-badge">Lab</span>
                            </div>

                            <div style="min-width:0">
                                <p class="profile-name mb-0">
                                    {{ $lab ? data_get($lab,'name','Assigned Lab') : 'No Lab Assigned' }}
                                </p>
                                <p class="profile-sub mb-0">
                                    @if($lab && data_get($lab,'phone'))
                                        <i class="fas fa-phone-alt me-1"></i> {{ data_get($lab,'phone') }}
                                    @else
                                        <span class="empty-pill"><i class="fas fa-info-circle"></i> Unassigned</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="head-right">
                            @if($lab)
                                <span class="chip chip-info" style="font-size:12px; padding:6px 10px;">
                                    <i class="fas fa-flask"></i> Assigned
                                </span>
                            @else
                                <span class="chip chip-warning" style="font-size:12px; padding:6px 10px;">
                                    <i class="fas fa-hourglass-half"></i> Pending
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="profile-body">
                        <div class="meta-row">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <div class="meta-k">Email</div>
                                <div class="meta-v">{{ $lab ? (data_get($lab,'email','—')) : '—' }}</div>
                            </div>
                        </div>

                        <div class="meta-row" style="margin-bottom:0;">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <div class="meta-k">Address</div>
                                <div class="meta-v">{{ $lab ? (data_get($lab,'address','—')) : '—' }}</div>
                            </div>
                        </div>

                        <div class="actions-grid {{ $lab ? '' : 'one' }}">
                            @if($lab && data_get($lab,'id') && Route::has('admin.labs.show'))
                                <a href="{{ route('admin.labs.show', data_get($lab,'id')) }}" class="btn-soft btn-soft-outline">
                                    <i class="fas fa-eye"></i> View Lab
                                </a>
                            @endif

                            @if($lab && data_get($lab,'phone'))
                                <a href="tel:{{ data_get($lab,'phone') }}" class="btn-soft btn-soft-success">
                                    <i class="fas fa-phone"></i> Call Lab
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Drivers --}}
                @php
                    $pickup = $rawPickupId ? $booking->pickupDriver : null;
                    $delivery = $rawDeliveryId ? $booking->deliveryDriver : null;

                    $initialsOf = function($name){
                        $name = trim((string)$name);
                        if(!$name) return '';
                        $parts = preg_split('/\s+/', $name);
                        $letters = collect($parts)->filter()->map(fn($p)=>mb_substr($p,0,1))->take(2)->join('');
                        return $letters ?: mb_substr($name,0,1);
                    };
                @endphp

                {{-- Pickup Driver --}}
                <div class="profile-card">
                    <div class="profile-head">
                        <div class="head-left">
                            <div class="avatar">
                                <span>{{ $pickup ? $initialsOf(data_get($pickup,'name')) : 'P' }}</span>
                                <span class="role-badge">Pickup</span>
                            </div>

                            <div style="min-width:0">
                                <p class="profile-name mb-0">{{ $pickup ? data_get($pickup,'name') : 'Unassigned Driver' }}</p>
                                <p class="profile-sub mb-0">
                                    @if($pickup && data_get($pickup,'email'))
                                        <i class="fas fa-envelope me-1"></i> {{ data_get($pickup,'email') }}
                                    @else
                                        <span class="empty-pill"><i class="fas fa-info-circle"></i> No driver assigned</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="head-right">
                            <span class="chip {{ $pickup ? 'chip-success' : 'chip-warning' }}" style="font-size:12px; padding:6px 10px;">
                                <i class="fas {{ $pickup ? 'fa-check-circle' : 'fa-hourglass-half' }}"></i>
                                {{ $pickup ? 'Assigned' : 'Pending' }}
                            </span>
                            <span class="tiny">
                                <i class="fas fa-database me-1"></i>
                                {{ $rawPickupId ? 'Driver ID: '.$rawPickupId : 'No DB assignment' }}
                            </span>
                        </div>
                    </div>

                    <div class="profile-body">
                        <div class="meta-row">
                            <i class="fas fa-at"></i>
                            <div>
                                <div class="meta-k">Email</div>
                                <div class="meta-v">{{ $pickup ? (data_get($pickup,'email','—')) : '—' }}</div>
                            </div>
                        </div>

                        <div class="actions-grid {{ $pickup ? '' : 'one' }}">
                            @if($pickup && Route::has('admin.drivers.show'))
                                <a href="{{ route('admin.drivers.show', $pickup) }}" class="btn-soft btn-soft-outline">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            @endif

                            @if($pickup && data_get($pickup,'email'))
                                <a href="mailto:{{ data_get($pickup,'email') }}" class="btn-soft btn-soft-primary">
                                    <i class="fas fa-paper-plane"></i> Email
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Delivery Driver --}}
                <div class="profile-card">
                    <div class="profile-head">
                        <div class="head-left">
                            <div class="avatar">
                                <span>{{ $delivery ? $initialsOf(data_get($delivery,'name')) : 'D' }}</span>
                                <span class="role-badge">Delivery</span>
                            </div>

                            <div style="min-width:0">
                                <p class="profile-name mb-0">{{ $delivery ? data_get($delivery,'name') : 'Unassigned Driver' }}</p>
                                <p class="profile-sub mb-0">
                                    @if($delivery && data_get($delivery,'email'))
                                        <i class="fas fa-envelope me-1"></i> {{ data_get($delivery,'email') }}
                                    @else
                                        <span class="empty-pill"><i class="fas fa-info-circle"></i> No driver assigned</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="head-right">
                            <span class="chip {{ $delivery ? 'chip-success' : 'chip-warning' }}" style="font-size:12px; padding:6px 10px;">
                                <i class="fas {{ $delivery ? 'fa-check-circle' : 'fa-hourglass-half' }}"></i>
                                {{ $delivery ? 'Assigned' : 'Pending' }}
                            </span>
                            <span class="tiny">
                                <i class="fas fa-database me-1"></i>
                                {{ $rawDeliveryId ? 'Driver ID: '.$rawDeliveryId : 'No DB assignment' }}
                            </span>
                        </div>
                    </div>

                    <div class="profile-body">
                        <div class="meta-row">
                            <i class="fas fa-at"></i>
                            <div>
                                <div class="meta-k">Email</div>
                                <div class="meta-v">{{ $delivery ? (data_get($delivery,'email','—')) : '—' }}</div>
                            </div>
                        </div>

                        <div class="actions-grid {{ $delivery ? '' : 'one' }}">
                            @if($delivery && Route::has('admin.drivers.show'))
                                <a href="{{ route('admin.drivers.show', $delivery) }}" class="btn-soft btn-soft-outline">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            @endif

                            @if($delivery && data_get($delivery,'email'))
                                <a href="mailto:{{ data_get($delivery,'email') }}" class="btn-soft btn-soft-primary">
                                    <i class="fas fa-paper-plane"></i> Email
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

            </aside>
        </div>

        {{-- Timeline: hidden inline, available in modal via "Show Timeline" button --}}
        <div class="mt-4 d-flex justify-content-end">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#timelineModal">
                <i class="fas fa-stream me-1"></i> {{ __('Show Timeline') }}
            </button>
        </div>

        <!-- Timeline Modal -->
        <div class="modal fade" id="timelineModal" tabindex="-1" aria-labelledby="timelineModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="timelineModalLabel"><i class="fas fa-stream me-2"></i> {{ __('Timeline') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                    </div>
                    <div class="modal-body">
                        <div class="section-title">
                            <div style="min-width:0">
                                <div class="label">{{ __('Timeline') }}</div>
                                <div class="muted">{{ __('A clear view of all booking milestones') }}</div>
                            </div>
                        </div>

                        <div class="timeline-wrap">
                            <div class="timeline-top d-flex justify-content-between align-items-start">
                                <div class="timeline-chips">
                                    <span class="stat-chip ok">
                                        <i class="fas fa-check-circle"></i>
                                        {{ __('Done') }}: <b>{{ $doneCount }}</b>
                                    </span>
                                    <span class="stat-chip wait">
                                        <i class="fas fa-hourglass-half"></i>
                                        {{ __('Pending') }}: <b>{{ $pendingCount }}</b>
                                    </span>
                                    <span class="stat-chip">
                                        <i class="fas fa-list"></i>
                                        {{ __('Total') }}: <b>{{ $totalCount }}</b>
                                    </span>
                                </div>

                                <div class="timeline-chips">
                                    <span class="stat-chip">
                                        <i class="fas fa-hashtag"></i>
                                        {{ data_get($booking,'order_number','—') }}
                                    </span>
                                    <span class="stat-chip">
                                        <i class="fas fa-circle" style="font-size:9px;"></i>
                                        {{ $statusUi['text'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="timeline-grid mt-3">
                                <div class="timeline">
                                    @foreach($timeline as $t)
                                        @php $isDone = !empty($t['value']); @endphp
                                        <div class="t-item {{ $isDone ? 'done' : 'pending' }}">
                                            <div class="t-dot" aria-hidden="true"></div>

                                            <div>
                                                <p class="t-title mb-0">
                                                    <i class="fas {{ $t['icon'] }} me-2" style="color:{{ $isDone ? '#059669' : '#b45309' }}"></i>
                                                    {{ __($t['label']) }}
                                                </p>
                                                <p class="t-sub mb-0">
                                                    {{ $isDone ? $t['value'] : __('Not yet') }}
                                                </p>
                                            </div>

                                            <div class="t-badge">
                                                    @if($isDone)
                                                    <i class="fas fa-check-circle"></i> {{ __('Completed') }}
                                                @else
                                                    <i class="fas fa-hourglass-half"></i> {{ __('Pending') }}
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="timeline-table mt-4">
                                    <div class="tt-head">
                                        <div>
                                            <p class="tt-title mb-0">{{ __('Quick Details') }}</p>
                                            <p class="tt-sub mb-0">{{ __('Key timestamps & assignments') }}</p>
                                        </div>
                                        <span class="chip chip-info" style="font-size:12px; padding:6px 10px;">
                                            <i class="fas fa-clock"></i> Live
                                        </span>
                                    </div>

                                    <div class="tt-body">
                                        <div class="tt-row">
                                            <div class="tt-k"><i class="fas fa-calendar-plus"></i> {{ __('Created') }}</div>
                                            <div class="tt-v {{ $booking->created_at ? '' : 'muted' }}">
                                                {{ optional($booking->created_at)->format('Y-m-d H:i') ?? '—' }}
                                            </div>
                                        </div>

                                        <div class="tt-row">
                                            <div class="tt-k"><i class="fas fa-pen"></i> {{ __('Updated') }}</div>
                                            <div class="tt-v {{ $booking->updated_at ? '' : 'muted' }}">
                                                {{ optional($booking->updated_at)->format('Y-m-d H:i') ?? '—' }}
                                            </div>
                                        </div>

                                        <div class="tt-row">
                                            <div class="tt-k"><i class="fas fa-flask"></i> {{ __('Lab Assigned') }}</div>
                                            <div class="tt-v {{ $booking->lab_assigned_at ? '' : 'muted' }}">
                                                {{ optional($booking->lab_assigned_at)->format('Y-m-d H:i') ?? '—' }}
                                            </div>
                                        </div>

                                        <div class="tt-row">
                                            <div class="tt-k"><i class="fas fa-database"></i> {{ __('Pickup Driver ID') }}</div>
                                            <div class="tt-v {{ $rawPickupId ? '' : 'muted' }}">
                                                {{ $rawPickupId ? $rawPickupId : '—' }}
                                            </div>
                                        </div>

                                        <div class="tt-row">
                                            <div class="tt-k"><i class="fas fa-database"></i> {{ __('Delivery Driver ID') }}</div>
                                            <div class="tt-v {{ $rawDeliveryId ? '' : 'muted' }}">
                                                {{ $rawDeliveryId ? $rawDeliveryId : '—' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modals --}}
@include('dashboard.admin.bookings._invoice_modal')
@include('dashboard.admin.bookings._permission_modal')
@include('dashboard.admin.bookings._transition_modal')
@include('dashboard.admin.bookings._notify_modal')
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const invoiceBase = @json(\Route::has('admin.bookings.invoice') ? route('admin.bookings.invoice', $booking) : '');
    const btn = document.getElementById('downloadInvoiceBtn');
    const select = document.getElementById('invoice-status-select');

    if (btn && select && invoiceBase) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const status = select.value || '';
            const url = invoiceBase + '?status=' + encodeURIComponent(status);
            window.open(url, '_blank');
        });
    }
});
</script>
@endpush
