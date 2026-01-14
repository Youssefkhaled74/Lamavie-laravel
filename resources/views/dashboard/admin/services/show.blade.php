@extends('dashboard.admin.layouts.main')

@section('content')
<style>
    .info-card{
        border-radius:16px;
        box-shadow:0 12px 30px rgba(2,6,23,.08);
        border:1px solid rgba(15,23,42,.06);
        overflow:hidden;
        background:#fff;
    }
    .info-head{
        background: linear-gradient(90deg, #0d6efd 0%, #6ea8fe 100%);
        color:#fff;
        padding:16px 18px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        flex-wrap:wrap;
    }
    .info-head h5{ margin:0; font-weight:900; letter-spacing:-.02em; }
    .btn-soft{ border-radius:12px; font-weight:900; }
    .kv{
        display:grid;
        grid-template-columns: 1fr 1fr;
        gap:14px;
    }
    .kv-item{
        background:#f8fafc;
        border:1px solid rgba(15,23,42,.06);
        border-radius:14px;
        padding:14px;
    }
    .kv-item .k{
        font-weight:900;
        color:#475569;
        font-size:.82rem;
        text-transform:uppercase;
        letter-spacing:.06em;
    }
    .kv-item .v{ margin-top:6px; font-weight:700; color:#0f172a; white-space:pre-wrap; }
    @media(max-width: 768px){ .kv{ grid-template-columns: 1fr; } }

    .logo{
        width:180px;height:180px;
        border-radius:16px;
        object-fit:cover;
        border:1px solid rgba(15,23,42,.08);
        background:#f8fafc;
        box-shadow:0 10px 24px rgba(2,6,23,.10);
    }
    .photo{
        width:100%;
        height:150px;
        object-fit:cover;
        border-radius:14px;
        border:1px solid rgba(15,23,42,.08);
        background:#f8fafc;
        box-shadow:0 10px 24px rgba(2,6,23,.08);
    }
</style>

<div class="content-header mb-3">
    <h1 class="fw-bold text-primary">Service Details</h1>
    <p class="text-muted mb-0">View details of the selected service.</p>
</div>

<div class="info-card">
    <div class="info-head">
        <h5><i class="fas fa-concierge-bell me-2"></i> Service Information</h5>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-light btn-sm btn-soft">
                <i class="fas fa-pen me-1"></i> Edit
            </a>
            <a href="{{ route('admin.services.index') }}" class="btn btn-outline-light btn-sm btn-soft">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="p-4">
        <div class="kv">
            <div class="kv-item">
                <div class="k">Name (English)</div>
                <div class="v">{{ data_get($service, 'name.en', '') }}</div>
            </div>
            <div class="kv-item">
                <div class="k">Name (Arabic)</div>
                <div class="v">{{ data_get($service, 'name.ar', '') }}</div>
            </div>
            <div class="kv-item">
                <div class="k">About (English)</div>
                <div class="v">{{ data_get($service, 'about.en', '') }}</div>
            </div>
            <div class="kv-item">
                <div class="k">About (Arabic)</div>
                <div class="v">{{ data_get($service, 'about.ar', '') }}</div>
            </div>
            <div class="kv-item">
                <div class="k">Description (English)</div>
                <div class="v">{{ data_get($service, 'description.en', '') }}</div>
            </div>
            <div class="kv-item">
                <div class="k">Description (Arabic)</div>
                <div class="v">{{ data_get($service, 'description.ar', '') }}</div>
            </div>
        </div>

        <hr class="my-4">

        <div class="row g-4">
            <div class="col-lg-4">
                <h6 class="fw-bold mb-2">Logo</h6>
                @if ($service->logo)
                    <img src="{{ asset('storage/' . $service->logo) }}" alt="Logo" class="logo">
                @else
                    <div class="text-muted">No Logo</div>
                @endif
            </div>

            <div class="col-lg-8">
                <h6 class="fw-bold mb-2">Photos</h6>
                @if ($service->photoServices->count())
                    <div class="row g-3">
                        @foreach ($service->photoServices as $photo)
                            <div class="col-md-4 col-sm-6">
                                <img src="{{ asset('storage/' . $photo->file_path) }}"
                                     alt="{{ $photo->photo_name }}"
                                     class="photo">
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-muted">No Photos</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
