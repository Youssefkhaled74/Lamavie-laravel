@extends('dashboard.admin.layouts.main')

@section('content')
<style>
    .list-card{
        border-radius:16px;
        box-shadow:0 12px 30px rgba(2,6,23,.08);
        border:1px solid rgba(15,23,42,.06);
        overflow:hidden;
        background:#fff;
    }
    .list-head{
        background: linear-gradient(90deg, #0d6efd 0%, #6ea8fe 100%);
        padding:16px 18px;
        color:#fff;
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        flex-wrap:wrap;
    }
    .list-head h5{ margin:0; font-weight:900; letter-spacing:-.02em; }
    .btn-pill{ border-radius:999px; font-weight:900; }
    .btn-primary{
        border:none;
        background: linear-gradient(90deg,#2563eb,#3b82f6);
        box-shadow:0 10px 24px rgba(37,99,235,.18);
    }
    .table thead th{
        font-size:.82rem;
        text-transform:uppercase;
        letter-spacing:.06em;
        color:#64748b;
        background: rgba(2,6,23,.03);
        vertical-align:middle;
    }
    .table td{ vertical-align:middle; }
    .logo-thumb{
        width:70px;height:70px;
        border-radius:14px;
        object-fit:cover;
        border:1px solid rgba(15,23,42,.08);
        background:#f8fafc;
        box-shadow:0 8px 18px rgba(2,6,23,.06);
    }
    .actions .btn{ border-radius:12px; font-weight:900; }
    .disabled.btn{ pointer-events:none; opacity:.6; }
</style>

<div class="content-header mb-3">
    <h1 class="fw-bold text-primary" data-en="Services Management" data-ar="إدارة الخدمات">Services Management</h1>
    <p class="text-muted mb-0" data-en="Manage all services and their media." data-ar="إدارة جميع الخدمات والوسائط الخاصة بها.">
        Manage all services and their media.
    </p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="list-card">
    <div class="list-head">
        <div>
            <h5 data-en="Services List" data-ar="قائمة الخدمات">Services List</h5>
            <div class="small" style="opacity:.92" data-en="View and edit services." data-ar="عرض الخدمات وتعديلها.">View and edit services.</div>
        </div>

        <a href="{{ env('RESTRICT_SERVICES', 1) == 0 ? '#' : route('admin.services.create') }}"
           class="btn btn-primary btn-pill {{ env('RESTRICT_SERVICES', 1) == 0 ? 'disabled' : '' }} restrict-create"
           @if(env('RESTRICT_SERVICES', 1) == 0) tabindex="-1" aria-disabled="true" @endif>
            <i class="fas fa-plus me-2"></i>
            <span data-en="Add New Service" data-ar="إضافة خدمة جديدة">Add New Service</span>
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead>
                <tr>
                    <th class="px-4 py-3" data-en="ID" data-ar="المعرف">ID</th>
                    <th class="px-4 py-3" data-en="Name (EN)" data-ar="الاسم (EN)">Name (EN)</th>
                    <th class="px-4 py-3" data-en="Name (AR)" data-ar="الاسم (AR)">Name (AR)</th>
                    <th class="px-4 py-3" data-en="Logo" data-ar="الشعار">Logo</th>
                    <th class="px-4 py-3 text-end" data-en="Actions" data-ar="الإجراءات">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($services as $service)
                    <tr>
                        <td class="px-4 py-3 fw-bold">{{ $service->id }}</td>
                        <td class="px-4 py-3">{{ data_get($service, 'name.en', '') }}</td>
                        <td class="px-4 py-3">{{ data_get($service, 'name.ar', '') }}</td>
                        <td class="px-4 py-3">
                            @if ($service->logo)
                                <img src="{{ asset('storage/' . $service->logo) }}" alt="Logo" class="logo-thumb">
                            @else
                                <span class="text-muted">No Logo</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-end actions">
                            <div class="d-flex gap-2 justify-content-end flex-wrap">
                                <a href="{{ route('admin.services.show', $service) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>

                                <a href="{{ env('RESTRICT_SERVICES', 1) == 0 ? '#' : route('admin.services.edit', $service) }}"
                                   class="btn btn-sm btn-primary restrict-create {{ env('RESTRICT_SERVICES', 1) == 0 ? 'disabled' : '' }}"
                                   @if(env('RESTRICT_SERVICES', 1) == 0) tabindex="-1" aria-disabled="true" @endif>
                                    <i class="fas fa-pen"></i> Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No services found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const restrictServices = {{ env('RESTRICT_SERVICES', 1) }};
    if (restrictServices === 0) {
        document.querySelectorAll('.restrict-create').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if (typeof showCustomAlert === 'function') {
                    showCustomAlert('Service creation/editing is restricted. Please contact the developer.');
                } else {
                    alert('Service creation/editing is restricted. Please contact the developer.');
                }
            });
        });
    }
});
</script>
@endsection
