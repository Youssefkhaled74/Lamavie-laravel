@extends('driver.layouts.main')

@section('page_title')
    <span class="lang-en">Lab Profile</span>
    <span class="lang-ar">صفحة المعمل</span>
@endsection

@section('content')
<style>
    /* ===== Lab Profile UI (Scoped) ===== */
    .lab-wrap{
        --lp-primary:#0d6efd;
        --lp-ink:#0f172a;
        --lp-muted:#64748b;
        --lp-border: rgba(15,23,42,.08);
        --lp-soft: rgba(13,110,253,.10);
        --lp-shadow: 0 18px 45px rgba(2,6,23,.08);
        --lp-shadow2: 0 10px 25px rgba(2,6,23,.06);
    }

    .lab-head{
        border: 1px solid var(--lp-border);
        border-radius: 18px;
        padding: 16px 16px;
        background:
            radial-gradient(900px 220px at 0% 0%, rgba(13,110,253,.14), transparent 55%),
            radial-gradient(900px 220px at 100% 0%, rgba(16,185,129,.10), transparent 60%),
            linear-gradient(180deg, rgba(255,255,255,.95), rgba(255,255,255,1));
        box-shadow: var(--lp-shadow2);
        position: relative;
        overflow: hidden;
        margin-bottom: 14px;
    }

    .lab-head:before{
        content:"";
        position:absolute;
        left:0; top:0; bottom:0;
        width: 6px;
        background: linear-gradient(180deg, #0d6efd, #6ea8fe);
        border-radius: 18px 0 0 18px;
    }

    .lab-title-row{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap: 14px;
        flex-wrap: wrap;
    }

    .lab-title{
        display:flex;
        gap: 12px;
        align-items:flex-start;
        min-width: 0;
    }

    .lab-icon{
        width: 52px; height: 52px;
        border-radius: 18px;
        display:flex; align-items:center; justify-content:center;
        background: rgba(13,110,253,.14);
        border: 1px solid rgba(13,110,253,.22);
        color: var(--lp-primary);
        flex: 0 0 auto;
        box-shadow: 0 10px 24px rgba(13,110,253,.12);
        font-size: 18px;
    }

    .lab-title h3{
        margin:0;
        font-weight: 950;
        color: var(--lp-ink);
        letter-spacing: .2px;
        line-height: 1.2;
        font-size: 1.35rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 72vw;
    }

    .lab-title p{
        margin: 6px 0 0 0;
        color: var(--lp-muted);
        font-weight: 700;
        font-size: .92rem;
    }

    .lab-actions{
        display:flex;
        gap: 10px;
        align-items:center;
        flex-wrap: wrap;
    }

    .btn-pill{
        border-radius: 14px;
        padding: 10px 12px;
        font-weight: 900;
        font-size: .92rem;
        border: 1px solid var(--lp-border);
        background:#fff;
        transition: transform .10s ease, box-shadow .15s ease, background .15s ease;
        text-decoration:none !important;
        display:inline-flex;
        align-items:center;
        gap: 8px;
    }
    .btn-pill:hover{
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(2,6,23,.08);
    }
    .btn-pill-primary{
        background: linear-gradient(135deg,#0d6efd,#6ea8fe);
        color:#fff !important;
        border-color: transparent;
    }
    .btn-pill-outline{
        color: var(--lp-ink);
    }

    .chips{
        display:flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }
    .chip{
        display:inline-flex;
        align-items:center;
        gap: 8px;
        padding: 8px 10px;
        border-radius: 999px;
        background: rgba(255,255,255,.85);
        border: 1px solid var(--lp-border);
        color: var(--lp-ink);
        font-weight: 900;
        font-size: .85rem;
        white-space: nowrap;
    }
    .chip i{ color: var(--lp-primary); }

    .lab-card{
        border: 1px solid var(--lp-border);
        border-radius: 18px;
        background:#fff;
        box-shadow: var(--lp-shadow);
        overflow: hidden;
    }

    .lab-card-head{
        padding: 14px 16px;
        border-bottom: 1px solid var(--lp-border);
        background: linear-gradient(180deg, rgba(13,110,253,.07), rgba(255,255,255,0));
        display:flex;
        justify-content: space-between;
        align-items:center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .lab-card-head .h{
        font-weight: 950;
        color: var(--lp-ink);
        margin:0;
        font-size: 1.05rem;
        display:flex;
        align-items:center;
        gap: 10px;
    }
    .lab-card-head .h .mini{
        width: 40px; height: 40px;
        border-radius: 14px;
        display:flex; align-items:center; justify-content:center;
        background: rgba(13,110,253,.12);
        color: var(--lp-primary);
        border: 1px solid rgba(13,110,253,.22);
    }

    .lab-card-body{
        padding: 14px 16px 16px;
    }

    .kv-grid{
        display:grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .kv{
        border: 1px solid var(--lp-border);
        background: rgba(248,250,252,.8);
        border-radius: 16px;
        padding: 12px 12px;
        display:flex;
        gap: 10px;
        align-items:flex-start;
        min-width:0;
    }
    .kv .ic{
        width: 40px; height: 40px;
        border-radius: 14px;
        display:flex; align-items:center; justify-content:center;
        background:#fff;
        border: 1px solid var(--lp-border);
        color: var(--lp-primary);
        flex: 0 0 auto;
    }
    .kv .k{
        font-size: .80rem;
        color: var(--lp-muted);
        font-weight: 900;
        margin-bottom: 2px;
    }
    .kv .v{
        font-weight: 950;
        color: var(--lp-ink);
        line-height: 1.35;
        word-break: break-word;
    }

    .lab-footer{
        margin-top: 14px;
        display:flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items:center;
        justify-content: space-between;
    }

    .quick-actions{
        display:flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items:center;
    }

    .btn-soft{
        border-radius: 14px;
        padding: 10px 12px;
        font-weight: 900;
        border: 1px solid rgba(13,110,253,.25);
        background: rgba(13,110,253,.08);
        color: var(--lp-primary);
        display:inline-flex;
        align-items:center;
        gap: 8px;
        text-decoration:none !important;
        transition: transform .10s ease, box-shadow .15s ease;
    }
    .btn-soft:hover{
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(2,6,23,.08);
    }

    @media (max-width: 768px){
        .kv-grid{ grid-template-columns: 1fr; }
        .lab-title h3{ max-width: 86vw; }
        .lab-actions{ width: 100%; justify-content: flex-start; }
    }
</style>

<div class="lab-wrap">
    {{-- Premium Header --}}
    <div class="lab-head">
        <div class="lab-title-row">
            <div class="lab-title">
                <div class="lab-icon"><i class="fas fa-flask"></i></div>
                <div style="min-width:0">
                    <h3 title="{{ $lab->name }}">{{ $lab->name }}</h3>
                    <p class="page-sub">Lab profile and contact details.</p>

                    <div class="chips">
                        <span class="chip"><i class="fas fa-id-badge"></i>#{{ $lab->id }}</span>
                        <span class="chip"><i class="fas fa-phone"></i>{{ $lab->phone ?? '—' }}</span>
                        <span class="chip"><i class="fas fa-envelope"></i>{{ $lab->email ?? '—' }}</span>
                    </div>
                </div>
            </div>

            <div class="lab-actions">
                <a href="{{ route('driver.bookings.index') }}" class="btn-pill btn-pill-outline">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                @if($lab->phone)
                    <a href="tel:{{ $lab->phone }}" class="btn-pill btn-pill-primary">
                        <i class="fas fa-phone"></i> Call
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="lab-card">
        <div class="lab-card-head">
            <p class="h">
                <span class="mini"><i class="fas fa-address-card"></i></span>
                Contact Information
            </p>

            <div class="quick-actions">
                @if($lab->email)
                    <a class="btn-soft" href="mailto:{{ $lab->email }}">
                        <i class="fas fa-paper-plane"></i> Email
                    </a>
                @endif

                @if($lab->phone)
                    <a class="btn-soft" target="_blank"
                       href="https://wa.me/{{ preg_replace('/\D+/', '', $lab->phone) }}">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                @endif
            </div>
        </div>

        <div class="lab-card-body">
            <div class="kv-grid">
                <div class="kv">
                    <div class="ic"><i class="fas fa-user-tag"></i></div>
                    <div style="min-width:0">
                        <div class="k">Name</div>
                        <div class="v">{{ $lab->name }}</div>
                    </div>
                </div>

                <div class="kv">
                    <div class="ic"><i class="fas fa-phone-alt"></i></div>
                    <div style="min-width:0">
                        <div class="k">Phone</div>
                        <div class="v">{{ $lab->phone ?? '—' }}</div>
                    </div>
                </div>

                <div class="kv">
                    <div class="ic"><i class="fas fa-envelope"></i></div>
                    <div style="min-width:0">
                        <div class="k">Email</div>
                        <div class="v">{{ $lab->email ?? '—' }}</div>
                    </div>
                </div>

                <div class="kv">
                    <div class="ic"><i class="fas fa-map-marker-alt"></i></div>
                    <div style="min-width:0">
                        <div class="k">Address</div>
                        <div class="v">{{ $lab->address ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="lab-footer">
                <div class="text-muted" style="font-weight:800;">
                    <i class="far fa-lightbulb me-1"></i>
                    Tip: Use the actions above to contact the lab quickly.
                </div>

                @if($lab->bookings()->exists())
                    <a href="{{ route('driver.bookings.index') }}?lab={{ $lab->id }}" class="btn-pill btn-pill-primary">
                        <i class="fas fa-folder-open"></i> Open related bookings
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
