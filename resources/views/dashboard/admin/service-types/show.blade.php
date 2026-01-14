@extends('dashboard.admin.layouts.main')

@section('content')
<div class="container-fluid">

    <div class="content-header fade-in mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="fw-bold text-primary mb-1" data-en="Service Type Details" data-ar="تفاصيل نوع الخدمة">Service Type Details</h1>
            <p class="text-muted mb-0" data-en="View details of the selected service type." data-ar="عرض تفاصيل نوع الخدمة المحدد.">
                View details of the selected service type.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.service-types.edit', $serviceType) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i><span data-en="Edit" data-ar="تعديل">Edit</span>
            </a>
            <a href="{{ route('admin.service-types.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i><span data-en="Back to List" data-ar="العودة للقائمة">Back to List</span>
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $en = data_get($serviceType,'name.en','');
        $ar = data_get($serviceType,'name.ar','');
        $serviceEn = data_get($serviceType,'service.name.en','N/A');
        $serviceAr = data_get($serviceType,'service.name.ar','');
    @endphp

    <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="background: linear-gradient(145deg, #ffffff, #f8fafc);">
        <div class="card-header py-3 px-3 text-white d-flex justify-content-between align-items-center"
             style="background: linear-gradient(90deg, #0d6efd 0%, #6ea8fe 100%);">
            <h5 class="card-title mb-0">
                <span data-en="Service Type:" data-ar="نوع الخدمة:">Service Type:</span> {{ $en }}
            </h5>
            <span class="badge bg-light text-dark border">#{{ $serviceType->id }}</span>
        </div>

        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="p-3 rounded-4 border bg-white h-100">
                        <h6 class="fw-bold mb-2" data-en="Name (English)" data-ar="الاسم (إنجليزي)">Name (English)</h6>
                        <p class="text-muted mb-0">{{ $en }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 rounded-4 border bg-white h-100">
                        <h6 class="fw-bold mb-2" data-en="Name (Arabic)" data-ar="الاسم (عربي)">Name (Arabic)</h6>
                        <p class="text-muted mb-0">{{ $ar }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 rounded-4 border bg-white h-100">
                        <h6 class="fw-bold mb-2" data-en="Service" data-ar="الخدمة">Service</h6>
                        <div class="fw-semibold">{{ $serviceEn }}</div>
                        @if($serviceAr)
                            <div class="text-muted small">{{ $serviceAr }}</div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 rounded-4 border bg-white h-100">
                        <h6 class="fw-bold mb-2" data-en="Logo" data-ar="الشعار">Logo</h6>
                        @if ($serviceType->logo)
                            <img src="{{ Storage::url($serviceType->logo) }}"
                                 class="rounded-4 border shadow-sm"
                                 style="width:160px;height:160px;object-fit:cover;">
                        @else
                            <p class="text-muted mb-0" data-en="No Logo" data-ar="لا يوجد شعار">No Logo</p>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 rounded-4 border bg-white h-100">
                        <h6 class="fw-bold mb-2" data-en="Created At" data-ar="تاريخ الإنشاء">Created At</h6>
                        <p class="text-muted mb-0">{{ optional($serviceType->created_at)->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 rounded-4 border bg-white h-100">
                        <h6 class="fw-bold mb-2" data-en="Updated At" data-ar="تاريخ التعديل">Updated At</h6>
                        <p class="text-muted mb-0">{{ optional($serviceType->updated_at)->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('admin.service-types.edit', $serviceType) }}" class="btn btn-warning btn-lg">
                    <i class="fas fa-edit me-2"></i><span data-en="Edit Service Type" data-ar="تعديل نوع الخدمة">Edit Service Type</span>
                </a>
                <a href="{{ route('admin.service-types.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i><span data-en="Back" data-ar="رجوع">Back</span>
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
