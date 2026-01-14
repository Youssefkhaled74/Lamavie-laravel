@extends('lab.layouts.main')

@section('content')
<div class="lab-page">

    {{-- Header --}}
    <div class="lab-header mb-3">
        <div class="lab-header__left">
            <div class="lab-title">
                <div class="lab-mark"><i class="fa-solid fa-flask"></i></div>
                <div>
                    <h3 class="mb-0 fw-bold">Lab Dashboard</h3>
                    <div class="text-muted small">Manage incoming items and process orders</div>
                </div>
            </div>
        </div>

        <div class="lab-header__right">
            <form class="lab-search" method="get" action="{{ route('lab.bookings.index') }}">
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" class="form-control"
                           placeholder="Search order # or customer"
                           value="{{ request('q') }}">
                    <button class="btn btn-primary">
                        <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="kpi-card kpi-primary">
                <div class="kpi-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
                <div class="kpi-body">
                    <div class="kpi-label">Total orders</div>
                    <div class="kpi-value">{{ $bookings->total() }}</div>
                </div>
                <div class="kpi-fade"></div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="kpi-card kpi-success">
                <div class="kpi-icon"><i class="fa-solid fa-truck"></i></div>
                <div class="kpi-body">
                    <div class="kpi-label">Awaiting pickup</div>
                    <div class="kpi-value">{{ $bookings->where('status','pickup')->count() }}</div>
                </div>
                <div class="kpi-fade"></div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="kpi-card kpi-info">
                <div class="kpi-icon"><i class="fa-solid fa-clock"></i></div>
                <div class="kpi-body">
                    <div class="kpi-label">Recent</div>
                    <div class="kpi-value">{{ $bookings->count() }}</div>
                </div>
                <div class="kpi-fade"></div>
            </div>
        </div>
    </div>

    {{-- Processing queue --}}
    <div class="card glass-card mb-3">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h6 class="mb-0 fw-bold">Processing queue</h6>
                    <small class="text-muted">Items assigned to this lab that need action</small>
                </div>
                <a href="{{ route('lab.bookings.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fa-solid fa-list me-1"></i> View all
                </a>
            </div>

            <div class="mt-3">
                @php
                    $queue = $bookings->whereIn('status', ['pending','processing','pickup'])->take(6);
                @endphp

                @if($queue->count())
                    <div class="queue-list">
                        @foreach($queue as $q)
                            @php
                                $qServiceName = data_get($q, 'service.name');
                                if (is_array($qServiceName)) {
                                    $qServiceName = $qServiceName[app()->getLocale()] ?? reset($qServiceName);
                                }

                                $customer = $q->user->name ?? $q->user->phone ?? '-';

                                $status = strtolower($q->status ?? 'pending');
                                $badgeCls = match($status){
                                    'pickup' => 'badge-soft-info',
                                    'processing' => 'badge-soft-warning',
                                    'pending' => 'badge-soft-secondary',
                                    'delivered' => 'badge-soft-success',
                                    'canceled','cancelled' => 'badge-soft-danger',
                                    default => 'badge-soft-secondary'
                                };
                            @endphp

                            <a href="{{ route('lab.bookings.show', $q->id) }}" class="queue-item">
                                <div class="queue-left">
                                    <div class="queue-title">
                                        <span class="fw-bold">Order #{{ $q->order_number }}</span>
                                        <span class="badge badge-soft {{ $badgeCls }}">{{ ucfirst($status) }}</span>
                                    </div>
                                    <div class="queue-sub">
                                        {{ $qServiceName ?? '' }} • {{ $customer }}
                                    </div>
                                </div>

                                <div class="queue-right">
                                    <span class="queue-open">
                                        Open <i class="fa-solid fa-chevron-right"></i>
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fa-regular fa-circle-check"></i></div>
                        <div class="fw-bold">No items right now</div>
                        <div class="text-muted small">Queue is empty — you’re all caught up.</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Orders list --}}
    @if($bookings->count())
        <div class="card glass-card">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h6 class="mb-0 fw-bold">All assigned orders</h6>
                        <small class="text-muted">Browse and open any booking</small>
                    </div>
                </div>

                <div class="orders-grid">
                    @foreach($bookings as $booking)
                        @php
                            $serviceName = data_get($booking,'service.name');
                            if (is_array($serviceName)) {
                                $serviceName = $serviceName['en'] ?? reset($serviceName);
                            }
                            $customerName = $booking->user->name ?? $booking->user->phone ?? '-';

                            $status = strtolower($booking->status ?? 'pending');
                            $badgeCls = match($status){
                                'pickup' => 'badge-soft-info',
                                'processing' => 'badge-soft-warning',
                                'pending' => 'badge-soft-secondary',
                                'delivered' => 'badge-soft-success',
                                'canceled','cancelled' => 'badge-soft-danger',
                                default => 'badge-soft-secondary'
                            };
                        @endphp

                        <a href="{{ route('lab.bookings.show', $booking->id) }}" class="order-card">
                            <div class="order-head">
                                <div class="order-id">
                                    <div class="order-dot"></div>
                                    <div>
                                        <div class="fw-bold">{{ $booking->order_number }} — {{ $serviceName ?? 'Service' }}</div>
                                        <div class="text-muted small">
                                            Customer: {{ $customerName }} • {{ optional($booking->created_at)->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>

                                <span class="badge badge-soft {{ $badgeCls }}">{{ ucfirst($status) }}</span>
                            </div>

                            <div class="order-foot">
                                <span class="text-muted small">
                                    <i class="fa-regular fa-clock me-1"></i>
                                    {{ optional($booking->created_at)->format('Y-m-d H:i') }}
                                </span>
                                <span class="btn btn-sm btn-primary px-3">
                                    Open <i class="fa-solid fa-arrow-right ms-1"></i>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-3">
                    {{ $bookings->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">No orders assigned to this lab yet.</div>
    @endif

</div>

{{-- ✅ CSS + JS same file --}}
<style>
    :root{
        --bg:#f6f8fc;
        --card:#fff;
        --text:#0f172a;
        --muted:#64748b;
        --border:rgba(15,23,42,.08);
        --shadow:0 16px 40px rgba(2,6,23,.10);

        --primary:#2563eb;
        --primary2:#60a5fa;
        --success:#16a34a;
        --warning:#f59e0b;
        --danger:#ef4444;
        --info:#0ea5e9;

        --radius:18px;
    }

    .lab-page{
        background:
            radial-gradient(1200px 650px at 15% -10%, rgba(37,99,235,.18), transparent 55%),
            radial-gradient(900px 520px at 90% 0%, rgba(14,165,233,.14), transparent 60%),
            var(--bg);
        border-radius: 22px;
        padding: 16px;
    }

    /* Header */
    .lab-header{
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap: 12px;
        flex-wrap: wrap;
    }

    .lab-title{
        display:flex;
        gap: 12px;
        align-items:center;
    }

    .lab-mark{
        width:46px;height:46px;border-radius:16px;
        display:flex;align-items:center;justify-content:center;
        color:#fff;
        background: linear-gradient(135deg, var(--primary), var(--primary2));
        box-shadow: 0 14px 28px rgba(37,99,235,.22);
    }

    .lab-search .input-group{
        border-radius: 999px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(2,6,23,.08);
    }
    .lab-search .input-group-text{
        background:#fff;
        border:1px solid rgba(15,23,42,.08);
        border-right: 0;
    }
    .lab-search .form-control{
        border:1px solid rgba(15,23,42,.08);
        border-left: 0;
        border-right: 0;
        padding: 10px 12px;
    }
    .lab-search .btn{
        border-radius: 0;
        padding: 10px 14px;
        font-weight: 800;
    }

    /* Glass card */
    .glass-card{
        border-radius: var(--radius);
        border: 1px solid rgba(255,255,255,.55);
        background: rgba(255,255,255,.78);
        backdrop-filter: blur(10px);
        box-shadow: var(--shadow);
    }

    /* KPI */
    .kpi-card{
        position: relative;
        border-radius: var(--radius);
        border: 1px solid rgba(255,255,255,.55);
        background: rgba(255,255,255,.78);
        backdrop-filter: blur(10px);
        box-shadow: var(--shadow);
        padding: 16px;
        overflow:hidden;
        display:flex;
        align-items:center;
        gap: 14px;
        min-height: 96px;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .kpi-card:hover{
        transform: translateY(-2px);
        box-shadow: 0 22px 52px rgba(2,6,23,.12);
    }
    .kpi-icon{
        width:52px;height:52px;border-radius:18px;
        display:flex;align-items:center;justify-content:center;
        color:#fff;
        font-size: 1.2rem;
        flex: 0 0 auto;
    }
    .kpi-label{color: var(--muted); font-weight: 700;}
    .kpi-value{font-size: 1.8rem; font-weight: 900; color: var(--text); line-height:1;}
    .kpi-fade{
        position:absolute;
        right:-40px; top:-40px;
        width:120px; height:120px;
        border-radius: 999px;
        opacity:.22;
        filter: blur(0px);
        pointer-events:none;
    }

    .kpi-primary .kpi-icon{background: linear-gradient(135deg, var(--primary), var(--primary2));}
    .kpi-primary .kpi-fade{background: radial-gradient(circle, rgba(37,99,235,.65), transparent 60%);}

    .kpi-success .kpi-icon{background: linear-gradient(135deg, #16a34a, #34d399);}
    .kpi-success .kpi-fade{background: radial-gradient(circle, rgba(22,163,74,.60), transparent 60%);}

    .kpi-info .kpi-icon{background: linear-gradient(135deg, #0ea5e9, #38bdf8);}
    .kpi-info .kpi-fade{background: radial-gradient(circle, rgba(14,165,233,.55), transparent 60%);}

    /* Soft badges */
    .badge-soft{
        border-radius: 999px;
        padding: 7px 10px;
        font-weight: 900;
        font-size: .78rem;
        border: 1px solid rgba(15,23,42,.08);
    }
    .badge-soft-secondary{background: rgba(100,116,139,.12); color:#334155; border-color: rgba(100,116,139,.22);}
    .badge-soft-info{background: rgba(14,165,233,.12); color:#0369a1; border-color: rgba(14,165,233,.22);}
    .badge-soft-warning{background: rgba(245,158,11,.14); color:#92400e; border-color: rgba(245,158,11,.22);}
    .badge-soft-success{background: rgba(34,197,94,.14); color:#166534; border-color: rgba(34,197,94,.22);}
    .badge-soft-danger{background: rgba(239,68,68,.14); color:#991b1b; border-color: rgba(239,68,68,.22);}

    /* Queue */
    .queue-list{
        display:flex;
        flex-direction:column;
        gap: 10px;
    }
    .queue-item{
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 16px;
        border: 1px solid rgba(15,23,42,.06);
        background: rgba(255,255,255,.72);
        text-decoration:none;
        color: var(--text);
        transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
    }
    .queue-item:hover{
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(2,6,23,.08);
        border-color: rgba(37,99,235,.22);
    }
    .queue-title{
        display:flex;
        align-items:center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .queue-sub{
        color: var(--muted);
        font-size: .88rem;
        margin-top: 2px;
    }
    .queue-open{
        font-weight: 900;
        color: var(--primary);
        display:inline-flex;
        gap: 8px;
        align-items:center;
    }

    /* Empty state */
    .empty-state{
        padding: 20px;
        border-radius: 18px;
        border: 1px dashed rgba(15,23,42,.14);
        background: rgba(255,255,255,.55);
        text-align:center;
    }
    .empty-icon{
        width:56px;height:56px;border-radius:18px;
        display:flex;align-items:center;justify-content:center;
        background: rgba(34,197,94,.12);
        color:#16a34a;
        margin: 0 auto 10px;
        font-size: 1.35rem;
    }

    /* Orders grid */
    .orders-grid{
        display:grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }
    @media (min-width: 992px){
        .orders-grid{grid-template-columns: 1fr 1fr;}
    }

    .order-card{
        display:flex;
        flex-direction:column;
        gap: 10px;
        padding: 14px 14px;
        border-radius: 18px;
        border: 1px solid rgba(15,23,42,.06);
        background: rgba(255,255,255,.75);
        text-decoration:none;
        color: var(--text);
        transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
    }
    .order-card:hover{
        transform: translateY(-2px);
        box-shadow: 0 18px 40px rgba(2,6,23,.10);
        border-color: rgba(37,99,235,.20);
    }
    .order-head{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap: 12px;
    }
    .order-id{
        display:flex;
        gap: 10px;
        align-items:flex-start;
        min-width:0;
    }
    .order-dot{
        width:12px;height:12px;border-radius:999px;
        background: linear-gradient(135deg, var(--primary), var(--primary2));
        margin-top: 6px;
        flex:0 0 auto;
    }
    .order-foot{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap: 10px;
        padding-top: 6px;
        border-top: 1px solid rgba(15,23,42,.06);
    }
</style>

<script>
    // (Optional) Small UX polish: focus search on /
    (function(){
        document.addEventListener('keydown', function(e){
            if (e.key === '/' && !e.ctrlKey && !e.metaKey && !e.altKey) {
                const input = document.querySelector('.lab-search input[name="q"]');
                if (input) { e.preventDefault(); input.focus(); }
            }
        });
    })();
</script>
@endsection
