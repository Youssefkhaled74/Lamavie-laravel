@extends('dashboard.admin.layouts.main')

@section('content')
<div class="container-fluid">

    <div class="content-header fade-in mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="fw-bold text-primary mb-1" data-en="Service Types" data-ar="أنواع الخدمات">Service Types</h1>
            <p class="text-muted mb-0" data-en="Manage service types with their associated services." data-ar="إدارة أنواع الخدمات والخدمات المرتبطة بها.">
                Manage service types with their associated services.
            </p>
        </div>

        <a href="{{ route('admin.service-types.create') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-plus me-2"></i><span data-en="Add New" data-ar="إضافة جديد">Add New</span>
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="background: linear-gradient(145deg, #ffffff, #f8fafc);">
        <div class="card-header py-3 px-3 text-white d-flex justify-content-between align-items-center"
             style="background: linear-gradient(90deg, #0d6efd 0%, #6ea8fe 100%);">
            <h5 class="card-title mb-0" data-en="Service Types List" data-ar="قائمة أنواع الخدمات">Service Types List</h5>
            <span class="badge bg-light text-dark border">
                {{ $serviceTypes->count() }}
                <span data-en="items" data-ar="عنصر">items</span>
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3" data-en="#" data-ar="#">#</th>
                            <th class="px-4 py-3" data-en="Name (EN)" data-ar="الاسم (EN)">Name (EN)</th>
                            <th class="px-4 py-3" data-en="Name (AR)" data-ar="الاسم (AR)">Name (AR)</th>
                            <th class="px-4 py-3" data-en="Service" data-ar="الخدمة">Service</th>
                            <th class="px-4 py-3" data-en="Logo" data-ar="الشعار">Logo</th>
                            <th class="px-4 py-3 text-end" data-en="Actions" data-ar="الإجراءات">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($serviceTypes as $serviceType)
                            @php
                                $en = data_get($serviceType,'name.en','');
                                $ar = data_get($serviceType,'name.ar','');
                                $serviceEn = data_get($serviceType,'service.name.en','N/A');
                                $serviceAr = data_get($serviceType,'service.name.ar','');
                            @endphp
                            <tr>
                                <td class="px-4 py-3 fw-semibold">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">{{ $en }}</td>
                                <td class="px-4 py-3">{{ $ar }}</td>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold">{{ $serviceEn }}</div>
                                    @if($serviceAr)
                                        <div class="text-muted small">{{ $serviceAr }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($serviceType->logo)
                                        <img src="{{ Storage::url($serviceType->logo) }}" alt="Logo"
                                             class="rounded-3 border shadow-sm"
                                             style="width:56px;height:56px;object-fit:cover;">
                                    @else
                                        <span class="text-muted" data-en="No Logo" data-ar="لا يوجد شعار">No Logo</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('admin.service-types.show', $serviceType) }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                            <i class="fas fa-eye me-1"></i><span data-en="View" data-ar="عرض">View</span>
                                        </a>

                                        <a href="{{ route('admin.service-types.edit', $serviceType) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                            <i class="fas fa-edit me-1"></i><span data-en="Edit" data-ar="تعديل">Edit</span>
                                        </a>

                                        <form action="{{ route('admin.service-types.destroy', $serviceType) }}"
                                              method="POST"
                                              class="d-inline confirm-delete"
                                              data-message="Are you sure you want to delete this service type?">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger rounded-pill px-3 {{ env('RESTRICT_SERVICES', 1) == 0 ? 'disabled' : '' }}"
                                                    @if(env('RESTRICT_SERVICES', 1) == 0) tabindex="-1" aria-disabled="true" @endif>
                                                <i class="fas fa-trash me-1"></i><span data-en="Delete" data-ar="حذف">Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4" data-en="No service types found." data-ar="لم يتم العثور على أنواع خدمات.">
                                    No service types found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const restrictSettings = {{ env('RESTRICT_SETTINGS', 1) }};

    document.querySelectorAll('.confirm-delete').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (restrictSettings === 0) {
                const msg = 'This action is restricted. Please connect the developer to make this action.';
                if (typeof showCustomAlert !== 'undefined') showCustomAlert(msg);
                else alert(msg);
                return;
            }

            const message = form.getAttribute('data-message') || 'Are you sure?';
            if (confirm(message)) form.submit();
        });
    });
});
</script>
@endsection
