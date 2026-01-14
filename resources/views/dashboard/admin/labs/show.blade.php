@extends('dashboard.admin.layouts.main')

@section('content')
<style>
    /* Page polish */
    .lab-page {
        --card-radius: 16px;
        --border: rgba(15, 23, 42, 0.08);
        --shadow: 0 14px 40px rgba(2, 6, 23, 0.08);
        --shadow-sm: 0 10px 28px rgba(2, 6, 23, 0.06);
        --muted: rgba(15, 23, 42, 0.62);
        --text: #0f172a;
        --primary: #2563eb;
        --bg: #f6f8ff;
    }

    .lab-page .page-hero {
        background: radial-gradient(1200px 500px at 15% 0%, rgba(37,99,235,.18) 0%, transparent 60%),
                    radial-gradient(900px 500px at 100% 30%, rgba(16,185,129,.14) 0%, transparent 55%),
                    linear-gradient(180deg, #ffffff, var(--bg));
        border: 1px solid var(--border);
        border-radius: var(--card-radius);
        box-shadow: var(--shadow-sm);
        padding: 18px;
        margin-bottom: 18px;
    }

    .lab-page .hero-title {
        font-weight: 800;
        letter-spacing: -0.02em;
        margin: 0;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .lab-page .hero-title .icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(37,99,235,.14), rgba(37,99,235,.06));
        color: var(--primary);
        border: 1px solid rgba(37,99,235,.18);
    }

    .lab-page .hero-sub {
        margin-top: 6px;
        color: var(--muted);
        font-size: 0.95rem;
    }

    /* Modern card */
    .lab-page .card-modern {
        border: 1px solid var(--border);
        border-radius: var(--card-radius);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        background: #fff;
    }

    .lab-page .card-modern .card-header {
        background: linear-gradient(180deg, rgba(37,99,235,.06), rgba(37,99,235,.02));
        border-bottom: 1px solid rgba(15, 23, 42, 0.06);
        font-weight: 800;
        color: var(--text);
        padding: 14px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .lab-page .card-modern .card-body {
        padding: 16px;
    }

    /* Details grid */
    .lab-page .details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-top: 12px;
    }
    @media (max-width: 768px) {
        .lab-page .details-grid { grid-template-columns: 1fr; }
    }

    .lab-page .info-tile {
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: 14px;
        padding: 12px 12px;
        background: linear-gradient(180deg, #ffffff, rgba(248,250,252,.7));
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .lab-page .info-tile .i {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(37,99,235,.10);
        color: var(--primary);
        flex: 0 0 auto;
    }

    .lab-page .info-tile .k {
        font-size: 0.85rem;
        font-weight: 800;
        color: rgba(15,23,42,.75);
        margin: 0;
    }

    .lab-page .info-tile .v {
        margin: 3px 0 0 0;
        font-weight: 700;
        color: var(--text);
        word-break: break-word;
    }

    /* Meta JSON */
    .lab-page .meta-box {
        margin-top: 14px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 14px;
        background: #0b1220;
        color: #e5e7eb;
        box-shadow: 0 10px 28px rgba(2, 6, 23, 0.14);
        overflow: hidden;
    }

    .lab-page .meta-box .meta-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        background: linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.04));
        border-bottom: 1px solid rgba(255,255,255,.10);
        font-weight: 800;
        font-size: 0.9rem;
    }

    .lab-page .meta-box pre {
        margin: 0;
        padding: 12px;
        max-height: 320px;
        overflow: auto;
        font-size: 0.85rem;
        line-height: 1.4;
        color: #e5e7eb;
        background: transparent;
    }

    /* Booking list */
    .lab-page .booking-item {
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: 14px;
        padding: 12px;
        background: #fff;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        transition: transform .12s ease, box-shadow .12s ease;
    }
    .lab-page .booking-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 34px rgba(2, 6, 23, 0.10);
    }

    .lab-page .booking-title a {
        font-weight: 900;
        color: #1d4ed8;
        text-decoration: none;
    }
    .lab-page .booking-title a:hover { text-decoration: underline; }

    .lab-page .booking-sub {
        margin-top: 4px;
        color: var(--muted);
        font-size: 0.9rem;
    }

    .lab-page .badge-soft {
        border-radius: 999px;
        padding: 6px 10px;
        font-weight: 800;
        font-size: 0.78rem;
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: rgba(248,250,252,.8);
        color: rgba(15,23,42,.78);
        white-space: nowrap;
    }

    /* Actions */
    .lab-page .actions-wrap {
        position: sticky;
        top: 92px;
    }
    @media(max-width: 991px){
        .lab-page .actions-wrap { position: static; }
    }

    .lab-page .btn-modern {
        border-radius: 12px;
        font-weight: 900;
        padding: 10px 12px;
    }
    .lab-page .btn-primary.btn-modern {
        background: linear-gradient(90deg,#2563eb,#3b82f6);
        border: none;
        box-shadow: 0 12px 26px rgba(37,99,235,.22);
    }
    .lab-page .btn-outline.btn-modern {
        border: 1px solid rgba(15, 23, 42, 0.12);
        background: #fff;
    }

    .lab-page .mini-note {
        margin-top: 10px;
        color: var(--muted);
        font-size: 0.88rem;
        line-height: 1.4;
    }
</style>

<div class="lab-page container py-4">

    {{-- HERO --}}
    <div class="page-hero">
        <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
            <div>
                <h3 class="hero-title">
                    <span class="icon"><i class="fas fa-flask"></i></span>
                    <span>{{ $lab->name }}</span>
                </h3>
                <div class="hero-sub">
                    View lab details, recent bookings, and manage actions from one place.
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.labs.edit', $lab) }}" class="btn btn-primary btn-modern">
                    <i class="fas fa-pen me-2"></i>Edit Lab
                </a>
                <a href="{{ route('admin.labs.index') }}" class="btn btn-outline-secondary btn-modern">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- LEFT --}}
        <div class="col-lg-8">

            {{-- LAB DETAILS --}}
            <div class="card-modern">
                <div class="card-header">
                    <span><i class="fas fa-info-circle me-2 text-primary"></i>Lab Details</span>
                    <span class="badge-soft">
                        <i class="fas fa-hashtag me-1"></i> ID: {{ $lab->id ?? '—' }}
                    </span>
                </div>

                <div class="card-body">

                    <div class="details-grid">
                        <div class="info-tile">
                            <div class="i"><i class="fas fa-envelope"></i></div>
                            <div>
                                <p class="k mb-0">Email</p>
                                <p class="v mb-0">{{ $lab->email ?? '—' }}</p>
                            </div>
                        </div>

                        <div class="info-tile">
                            <div class="i"><i class="fas fa-phone"></i></div>
                            <div>
                                <p class="k mb-0">Phone</p>
                                <p class="v mb-0">{{ $lab->phone ?? '—' }}</p>
                            </div>
                        </div>

                        <div class="info-tile" style="grid-column: 1 / -1;">
                            <div class="i"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <p class="k mb-0">Address</p>
                                <p class="v mb-0">{{ $lab->address ?? '—' }}</p>
                            </div>
                        </div>
                    </div>

                    @if($lab->meta)
                        <div class="meta-box">
                            <div class="meta-head">
                                <span><i class="fas fa-code me-2"></i>Meta (JSON)</span>
                                <span class="badge-soft" style="background:rgba(255,255,255,.08); border-color:rgba(255,255,255,.14); color:#e5e7eb;">
                                    <i class="fas fa-database me-1"></i>Raw Data
                                </span>
                            </div>
                            <pre>{{ json_encode($lab->meta, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    @endif
                </div>
            </div>

            {{-- RECENT BOOKINGS --}}
            <div class="card-modern mt-3">
                <div class="card-header">
                    <span><i class="fas fa-receipt me-2 text-primary"></i>Recent Bookings</span>
                    <span class="badge-soft">
                        <i class="fas fa-list me-1"></i>
                        {{ $lab->bookings ? $lab->bookings->count() : 0 }} total
                    </span>
                </div>

                <div class="card-body">
                    @if($lab->bookings && $lab->bookings->count())
                        <div class="d-flex flex-column gap-2">
                            @foreach($lab->bookings->take(20) as $booking)
                                @php
                                    $serviceName = '';
                                    if ($booking->service) {
                                        $sname = $booking->service->name;
                                        if (is_array($sname)) {
                                            $serviceName = $sname[app()->getLocale()] ?? (array_values($sname)[0] ?? '');
                                        } else {
                                            $serviceName = $sname;
                                        }
                                    }
                                    $customer = $booking->customer_name ?? ($booking->user->name ?? 'Customer');
                                    $status = $booking->status ?? '—';
                                @endphp

                                <div class="booking-item">
                                    <div>
                                        <div class="booking-title">
                                            <a href="{{ route('admin.bookings.show', $booking) }}">
                                                #{{ $booking->id }} — {{ $customer }}
                                            </a>
                                        </div>
                                        <div class="booking-sub">
                                            <span class="me-2"><strong>Status:</strong> {{ $status }}</span>
                                            <span class="me-2">•</span>
                                            <span><strong>Service:</strong> {{ $serviceName ?: '—' }}</span>
                                        </div>
                                    </div>
                                    <div class="badge-soft">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted">No bookings found for this lab.</div>
                    @endif
                </div>
            </div>

        </div>

        {{-- RIGHT --}}
        <div class="col-lg-4">
            <div class="actions-wrap">
                <div class="card-modern">
                    <div class="card-header">
                        <span><i class="fas fa-bolt me-2 text-primary"></i>Actions</span>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.labs.edit', $lab) }}" class="btn btn-primary btn-modern">
                                <i class="fas fa-pen me-2"></i>Edit Lab
                            </a>
                            <a href="{{ route('admin.labs.index') }}" class="btn btn-outline-secondary btn-modern">
                                <i class="fas fa-arrow-left me-2"></i>Back to Labs
                            </a>
                        </div>

                        <div class="mini-note">
                            Tip: You can review recent bookings to ensure lab operations are aligned with current workload.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
