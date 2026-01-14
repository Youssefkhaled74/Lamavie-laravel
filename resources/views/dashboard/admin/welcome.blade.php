@extends('dashboard.admin.layouts.main')

@section('content')
<style>
    :root{
        --p:#2563eb;
        --p2:#60a5fa;
        --g1:#0d6efd;
        --g2:#6ea8fe;
        --bg:#f6f8fc;
        --text:#0f172a;
        --muted:#64748b;
        --card:#ffffff;
        --border: rgba(15,23,42,.08);
        --shadow: 0 14px 34px rgba(2,6,23,.10);
        --shadow2: 0 10px 24px rgba(2,6,23,.08);
        --radius: 18px;
    }

    .dash-wrap{ max-width: 1280px; margin:0 auto; }
    .hero{
        border-radius: var(--radius);
        background: linear-gradient(90deg, rgba(37,99,235,.96), rgba(96,165,250,.92));
        box-shadow: var(--shadow);
        color:#fff;
        overflow:hidden;
        position:relative;
    }
    .hero:before{
        content:"";
        position:absolute;
        inset:-40px -60px auto auto;
        width:240px;height:240px;
        background: radial-gradient(circle, rgba(255,255,255,.22) 0%, rgba(255,255,255,0) 60%);
        transform: rotate(15deg);
    }
    .hero:after{
        content:"";
        position:absolute;
        inset:auto auto -80px -80px;
        width:260px;height:260px;
        background: radial-gradient(circle, rgba(255,255,255,.16) 0%, rgba(255,255,255,0) 65%);
    }
    .hero-inner{ position:relative; padding:26px 24px; }
    .hero-title{ font-weight: 900; letter-spacing:-.02em; margin:0; }
    .hero-sub{ margin:8px 0 0; opacity:.92; font-size:1.06rem; }
    .quick-actions{
        display:flex; gap:10px; flex-wrap:wrap;
        margin-top:14px;
    }
    .btn-hero{
        border-radius: 999px;
        font-weight: 900;
        border: 1px solid rgba(255,255,255,.35);
        color:#fff;
        background: rgba(255,255,255,.12);
        backdrop-filter: blur(6px);
        padding:10px 14px;
        transition: transform .15s ease, background .15s ease;
    }
    .btn-hero:hover{
        transform: translateY(-1px);
        background: rgba(255,255,255,.18);
        color:#fff;
    }

    .cardx{
        background: var(--card);
        border:1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow2);
        overflow:hidden;
    }
    .cardx-head{
        padding:14px 18px;
        border-bottom:1px solid rgba(15,23,42,.06);
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        flex-wrap:wrap;
    }
    .cardx-title{
        margin:0;
        font-weight: 900;
        letter-spacing:-.01em;
        color: var(--text);
        display:flex; align-items:center; gap:10px;
    }
    .cardx-sub{
        margin:0;
        color: var(--muted);
        font-size:.92rem;
    }
    .cardx-body{ padding:16px 18px; }

    /* Stat Cards */
    .stat{
        border-radius: var(--radius);
        background: linear-gradient(145deg, #ffffff, #f8fafc);
        border:1px solid rgba(15,23,42,.06);
        box-shadow: 0 12px 26px rgba(2,6,23,.08);
        padding:18px 18px;
        min-height: 170px;
        display:flex;
        gap:14px;
        align-items:center;
        transition: transform .16s ease, box-shadow .16s ease;
        position:relative;
        overflow:hidden;
    }
    .stat:hover{
        transform: translateY(-2px);
        box-shadow: 0 18px 36px rgba(2,6,23,.12);
    }
    .stat:before{
        content:"";
        position:absolute;
        width:220px;height:220px;
        right:-130px; top:-120px;
        background: radial-gradient(circle, rgba(37,99,235,.14), rgba(37,99,235,0) 65%);
        transform: rotate(15deg);
    }
    .stat-ic{
        width:56px;height:56px;
        border-radius: 16px;
        display:flex;align-items:center;justify-content:center;
        color:#fff;
        box-shadow: 0 12px 24px rgba(2,6,23,.12);
        flex:0 0 auto;
        position:relative;
    }
    .bg-primaryx{ background: linear-gradient(135deg, #2563eb, #60a5fa); }
    .bg-successx{ background: linear-gradient(135deg, #10b981, #34d399); }
    .bg-warningx{ background: linear-gradient(135deg, #f59e0b, #fbbf24); }
    .bg-infox{ background: linear-gradient(135deg, #06b6d4, #60a5fa); }
    .bg-purplex{ background: linear-gradient(135deg, #6366f1, #a78bfa); }
    .bg-dangerx{ background: linear-gradient(135deg, #ef4444, #fb7185); }

    .stat-num{ font-weight: 950; font-size: 1.55rem; margin:0; color: var(--text); letter-spacing:-.02em; }
    .stat-lbl{ margin:4px 0 0; color: var(--muted); font-weight:700; }

    /* Pills / Tabs */
    .pill-tabs{
        display:flex; gap:8px; flex-wrap:wrap;
    }
    .pill{
        border-radius:999px;
        padding:8px 12px;
        border:1px solid rgba(15,23,42,.12);
        background:#fff;
        font-weight:900;
        color:#334155;
        cursor:pointer;
        transition: all .14s ease;
        user-select:none;
    }
    .pill.active{
        background: linear-gradient(90deg, #2563eb, #60a5fa);
        color:#fff;
        border-color: transparent;
        box-shadow: 0 10px 22px rgba(37,99,235,.18);
    }

    /* Areas list */
    .area-item{
        border:1px solid rgba(15,23,42,.06);
        border-radius: 14px;
        padding:12px 14px;
        background: linear-gradient(145deg,#fff,#f8fafc);
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        transition: transform .14s ease, box-shadow .14s ease;
    }
    .area-item:hover{
        transform: translateY(-1px);
        box-shadow: 0 12px 24px rgba(2,6,23,.08);
    }
    .badge-soft{
        background: rgba(100,116,139,.12);
        color:#334155;
        border:1px solid rgba(100,116,139,.18);
        font-weight:900;
    }

    /* Fade in (no external lib needed) */
    .fade-up{ opacity:0; transform: translateY(8px); }
    .fade-up.show{ opacity:1; transform: translateY(0); transition: all .5s ease; }

    /* Charts */
    .chart-box{ height:320px; }
    canvas{ max-width:100%; }
</style>

<div class="dash-wrap">

    {{-- HERO --}}
    <div class="hero mb-4 fade-up">
        <div class="hero-inner">
            <h1 class="hero-title">Welcome, {{ $admin->name }} 👋</h1>
            <p class="hero-sub">Lamavie Admin Dashboard — manage services, bookings, and insights from one place.</p>

            <div class="quick-actions">
                <a class="btn-hero" href="{{ route('admin.services.index') }}">
                    <i class="fas fa-concierge-bell me-2"></i>Services
                </a>
                <a class="btn-hero" href="{{ route('admin.bookings.index') }}">
                    <i class="fas fa-calendar-check me-2"></i>Bookings
                </a>
                <a class="btn-hero" href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users me-2"></i>Users
                </a>
            </div>

            @if(session('error'))
                <div class="alert alert-danger mt-3 mb-0">{{ session('error') }}</div>
            @endif
        </div>
    </div>

    {{-- STATS ROW 1 --}}
    <div class="row g-3 mb-3">
        <div class="col-md-3 fade-up">
            <div class="stat">
                <div class="stat-ic bg-primaryx"><i class="fas fa-users fa-lg"></i></div>
                <div>
                    <p class="stat-num">{{ number_format($totalUsers) }}</p>
                    <p class="stat-lbl">Total Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 fade-up">
            <div class="stat">
                <div class="stat-ic bg-successx"><i class="fas fa-calendar-check fa-lg"></i></div>
                <div>
                    <p class="stat-num">{{ number_format($appointments) }}</p>
                    <p class="stat-lbl">Appointments</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 fade-up">
            <div class="stat">
                <div class="stat-ic bg-warningx"><i class="fas fa-concierge-bell fa-lg"></i></div>
                <div>
                    <p class="stat-num">{{ number_format($services) }}</p>
                    <p class="stat-lbl">Services</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 fade-up">
            <div class="stat">
                <div class="stat-ic bg-infox"><i class="fas fa-layer-group fa-lg"></i></div>
                <div>
                    <p class="stat-num">{{ number_format($serviceCategories) }}</p>
                    <p class="stat-lbl">Service Categories</p>
                </div>
            </div>
        </div>
    </div>

    {{-- STATS ROW 2 --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 fade-up">
            <div class="stat">
                <div class="stat-ic bg-primaryx"><i class="fas fa-dollar-sign fa-lg"></i></div>
                <div>
                    <p class="stat-num">{{ number_format($totalRevenue, 2) }}</p>
                    <p class="stat-lbl">Total Revenue</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 fade-up">
            <div class="stat">
                <div class="stat-ic bg-successx"><i class="fas fa-chart-line fa-lg"></i></div>
                <div>
                    <p class="stat-num">{{ number_format($averageOrderValue, 2) }}</p>
                    <p class="stat-lbl">Average Order Value</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 fade-up">
            <div class="stat">
                <div class="stat-ic bg-warningx"><i class="fas fa-hourglass-half fa-lg"></i></div>
                <div>
                    <p class="stat-num">{{ number_format($bookingsByStatus['pending'] ?? 0) }}</p>
                    <p class="stat-lbl">Pending Bookings</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 fade-up">
            <div class="stat">
                <div class="stat-ic bg-purplex"><i class="fas fa-check fa-lg"></i></div>
                <div>
                    <p class="stat-num">{{ number_format($bookingsByStatus['completed'] ?? 0) }}</p>
                    <p class="stat-lbl">Completed Bookings</p>
                </div>
            </div>
        </div>
    </div>

    {{-- USER GROWTH --}}
    <div class="cardx mb-4 fade-up">
        <div class="cardx-head">
            <div>
                <h3 class="cardx-title"><i class="fas fa-user-plus text-primary"></i> User Growth</h3>
                <p class="cardx-sub">Track user signups over time.</p>
            </div>

            {{-- nicer tabs instead of select --}}
            <div class="pill-tabs" id="growthTabs">
                <div class="pill active" data-period="last7">Last 7 Days</div>
                <div class="pill" data-period="last30">Last 30 Days</div>
                <div class="pill" data-period="last90">Last 90 Days</div>
            </div>
        </div>
        <div class="cardx-body">
            <div class="chart-box">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        {{-- BOOKINGS OVER TIME --}}
        <div class="col-md-8 fade-up">
            <div class="cardx">
                <div class="cardx-head">
                    <div>
                        <h3 class="cardx-title"><i class="fas fa-chart-bar text-primary"></i> Bookings Over Time</h3>
                        <p class="cardx-sub">Bookings & Revenue (Last 30 Days)</p>
                    </div>
                </div>
                <div class="cardx-body">
                    <div class="chart-box">
                        <canvas id="bookingsOverTimeChart"></canvas>
                    </div>
                    <div id="bookings-empty" class="text-center text-muted py-5 d-none">
                        No bookings data available for the selected period.
                    </div>
                </div>
            </div>
        </div>

        {{-- STATUS --}}
        <div class="col-md-4 fade-up">
            <div class="cardx">
                <div class="cardx-head">
                    <div>
                        <h5 class="cardx-title"><i class="fas fa-chart-pie text-primary"></i> Bookings by Status</h5>
                        <p class="cardx-sub">Distribution of booking statuses.</p>
                    </div>
                </div>
                <div class="cardx-body">
                    @if(empty($bookingsByStatus))
                        <div class="text-center text-muted py-5">No bookings status data available.</div>
                    @else
                        <div class="chart-box" style="height:280px;">
                            <canvas id="bookingsStatusChart"></canvas>
                        </div>
                        <div class="mt-3">
                            @foreach($bookingsByStatus as $status => $count)
                                <div class="d-flex justify-content-between align-items-center py-1">
                                    <span class="fw-bold text-capitalize">{{ $status }}</span>
                                    <span class="badge badge-soft">{{ number_format($count) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- OVERVIEW --}}
    <div class="cardx mb-4 fade-up">
        <div class="cardx-head">
            <h5 class="cardx-title"><i class="fas fa-info-circle text-primary"></i> Overview</h5>
        </div>
        <div class="cardx-body">
            <p class="mb-0 text-secondary">
                This dashboard helps you oversee and manage Lamavie services, bookings, and operational insights.
            </p>
        </div>
    </div>

    {{-- AREAS --}}
    <div class="cardx fade-up">
        <div class="cardx-head">
            <div>
                <h5 class="cardx-title"><i class="fas fa-map-marked-alt text-primary"></i> Areas (EN / AR)</h5>
                <p class="cardx-sub">Quick view of coverage areas.</p>
            </div>
        </div>
        <div class="cardx-body">
            @if(isset($areas) && $areas->count() > 0)
                <div class="row g-3">
                    @foreach($areas as $area)
                        <div class="col-md-6">
                            <div class="area-item">
                                <div>
                                    <div class="fw-bold">{{ $area->name['en'] ?? '-' }}</div>
                                    <div class="text-muted small">{{ $area->name['ar'] ?? '-' }}</div>
                                </div>
                                <span class="badge badge-soft">{{ $area->slug }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-muted">No areas defined yet.</div>
            @endif
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Smooth fade-up
    const items = document.querySelectorAll('.fade-up');
    items.forEach((el, i) => {
        setTimeout(() => el.classList.add('show'), 60 + (i * 70));
    });

    // User Growth Chart
    const userGrowthData = @json($userGrowth['daily'] ?? []);
    const periods = {
        last7: userGrowthData.last7 ?? [],
        last30: userGrowthData.last30 ?? [],
        last90: userGrowthData.last90 ?? []
    };

    const ctx = document.getElementById('userGrowthChart');
    let growthChart = null;

    function renderGrowth(periodKey){
        const dataArr = periods[periodKey] || [];
        const labels = dataArr.map(x => x.date);
        const values = dataArr.map(x => x.count);

        if (!growthChart){
            growthChart = new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: periodKey === 'last7' ? 'Last 7 Days' : (periodKey === 'last30' ? 'Last 30 Days' : 'Last 90 Days'),
                        data: values,
                        borderColor: '#ffffff',
                        backgroundColor: 'rgba(255,255,255,0.16)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true }
                    }
                }
            });
        } else {
            growthChart.data.labels = labels;
            growthChart.data.datasets[0].label =
                periodKey === 'last7' ? 'Last 7 Days' : (periodKey === 'last30' ? 'Last 30 Days' : 'Last 90 Days');
            growthChart.data.datasets[0].data = values;
            growthChart.update();
        }
    }
    renderGrowth('last7');

    // Tabs
    const tabs = document.querySelectorAll('#growthTabs .pill');
    tabs.forEach(t => {
        t.addEventListener('click', () => {
            tabs.forEach(x => x.classList.remove('active'));
            t.classList.add('active');
            renderGrowth(t.dataset.period);
        });
    });

    // Bookings Over Time Chart
    const bookingsData = @json($bookingsOverTime->toArray() ?? []);
    const bookingsCanvas = document.getElementById('bookingsOverTimeChart');
    const emptyBox = document.getElementById('bookings-empty');

    if (Array.isArray(bookingsData) && bookingsData.length > 0) {
        new Chart(bookingsCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: bookingsData.map(item => item.date),
                datasets: [{
                    label: 'Bookings',
                    data: bookingsData.map(item => item.count),
                    backgroundColor: 'rgba(37,99,235,0.75)'
                },{
                    label: 'Revenue',
                    data: bookingsData.map(item => item.revenue),
                    type: 'line',
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.12)',
                    fill: true,
                    yAxisID: 'y1',
                    tension: 0.35,
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins:{ legend:{ position:'top' } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true },
                    y1: { type: 'linear', position: 'right', beginAtZero: true, grid:{ drawOnChartArea:false } }
                }
            }
        });
    } else {
        bookingsCanvas.classList.add('d-none');
        emptyBox.classList.remove('d-none');
    }

    // Bookings Status Chart
    const bookingsStatus = @json($bookingsByStatus ?? []);
    const statusLabels = Object.keys(bookingsStatus || {});
    const statusValues = Object.values(bookingsStatus || {});
    const statusCanvas = document.getElementById('bookingsStatusChart');

    if (statusCanvas && statusLabels.length > 0) {
        new Chart(statusCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusValues,
                    backgroundColor: ['#2563eb','#10b981','#f59e0b','#ef4444','#6366f1','#06b6d4']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: { legend: { position:'bottom' } }
            }
        });
    }

});
</script>
@endsection
