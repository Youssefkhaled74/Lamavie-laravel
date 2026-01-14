@extends('dashboard.admin.layouts.main')

@section('content')
<style>
    .details-card{
        border-radius:16px;
        box-shadow:0 12px 30px rgba(2,6,23,.08);
        border:1px solid rgba(15,23,42,.06);
        overflow:hidden;
        background:#fff;
    }
    .details-head{
        background: linear-gradient(90deg, #0d6efd 0%, #6ea8fe 100%);
        color:#fff;
        padding:16px 18px;
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        flex-wrap:wrap;
    }
    .details-head h5{ margin:0; font-weight:900; letter-spacing:-.02em; }
    .pill{
        display:inline-flex;
        align-items:center;
        gap:8px;
        padding:8px 12px;
        border-radius:999px;
        background: rgba(255,255,255,.18);
        border:1px solid rgba(255,255,255,.25);
        font-weight:800;
    }
    .kv{
        display:grid;
        grid-template-columns: 1fr 1fr;
        gap:14px;
    }
    .kv-item{
        background: #f8fafc;
        border:1px solid rgba(15,23,42,.06);
        border-radius:14px;
        padding:14px;
    }
    .kv-item .k{ font-weight:900; color:#475569; font-size:.85rem; text-transform:uppercase; letter-spacing:.06em;}
    .kv-item .v{ margin-top:6px; font-weight:700; color:#0f172a; }
    @media(max-width: 768px){ .kv{ grid-template-columns: 1fr; } }
    .btn-soft{ border-radius:12px; font-weight:900; }
    .badge-chip{
        background:#fff;
        border:1px solid rgba(15,23,42,.08);
        border-radius:12px;
        padding:10px 12px;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:10px;
        box-shadow:0 6px 18px rgba(2,6,23,.06);
    }
</style>

<div class="content-header mb-3">
    <h1 class="fw-bold text-primary" data-en="Driver Details" data-ar="تفاصيل السائق">Driver Details</h1>
    <p class="text-muted mb-0" data-en="View the details of the selected driver." data-ar="عرض تفاصيل السائق المحدد.">View the details of the selected driver.</p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="details-card">
    <div class="details-head">
        <div>
            <h5>
                <i class="fas fa-id-badge me-2"></i>
                <span data-en="Driver:" data-ar="السائق:">Driver:</span> {{ $driver->name }}
            </h5>
            <div class="small" style="opacity:.9">
                <span class="pill">
                    <i class="fas fa-envelope"></i> {{ $driver->email }}
                </span>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.drivers.edit', $driver) }}" class="btn btn-light btn-sm btn-soft">
                <i class="fas fa-pen me-1"></i>
                <span data-en="Edit" data-ar="تعديل">Edit</span>
            </a>
            <a href="{{ route('admin.drivers.index') }}" class="btn btn-outline-light btn-sm btn-soft">
                <i class="fas fa-arrow-left me-1"></i>
                <span data-en="Back" data-ar="رجوع">Back</span>
            </a>
        </div>
    </div>

    <div class="p-4">
        <div class="kv">
            <div class="kv-item">
                <div class="k" data-en="Name" data-ar="الاسم">Name</div>
                <div class="v">{{ $driver->name }}</div>
            </div>
            <div class="kv-item">
                <div class="k" data-en="Email" data-ar="البريد الإلكتروني">Email</div>
                <div class="v">{{ $driver->email }}</div>
            </div>
            <div class="kv-item">
                <div class="k" data-en="Created At" data-ar="تاريخ الإنشاء">Created At</div>
                <div class="v">{{ $driver->created_at }}</div>
            </div>
            <div class="kv-item">
                <div class="k" data-en="Updated At" data-ar="تاريخ التعديل">Updated At</div>
                <div class="v">{{ $driver->updated_at }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Services assignment --}}
<div class="card mt-4 shadow-sm border-0" style="border-radius:16px; overflow:hidden;">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:#f8fafc;">
        <h5 class="mb-0" data-en="Assigned Services" data-ar="الخدمات المعينة">Assigned Services</h5>

        <form method="POST" action="{{ route('admin.drivers.assignService', $driver) }}" class="d-flex align-items-center">
            @csrf
            <div class="input-group input-group-sm" style="min-width:360px;">
                <select name="service_id" class="form-select">
                    <option value="" data-en="-- Select service to assign --" data-ar="-- اختر الخدمة للتعيين --">-- Select service to assign --</option>
                    @foreach($allServices as $service)
                        <option value="{{ $service->id }}">
                            {{ $service->name['en'] ?? "Service #".$service->id }}
                        </option>
                    @endforeach
                </select>
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-plus me-1"></i>
                    <span data-en="Assign" data-ar="تعيين">Assign</span>
                </button>
            </div>
        </form>
    </div>

    <div class="card-body">
        @if($driver->services->isEmpty())
            <p class="text-muted mb-0" data-en="No services assigned to this driver." data-ar="لا توجد خدمات مخصصة لهذا السائق.">No services assigned to this driver.</p>
        @else
            <div class="d-flex flex-wrap" style="gap:0.75rem;">
                @foreach($driver->services as $service)
                    <div class="badge-chip">
                        <div>
                            <div class="fw-bold">{{ $service->name['en'] ?? $service->id }}</div>
                            <div class="text-muted small">
                                {{ (is_array($service->about) && ($service->about['en'] ?? '')) ? \Illuminate\Support\Str::limit($service->about['en'], 60) : '' }}
                            </div>
                        </div>

                        <form method="POST"
                              action="{{ route('admin.drivers.removeService', [$driver, $service->id]) }}"
                              onsubmit="return confirm('Remove service from driver?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Remove service">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
