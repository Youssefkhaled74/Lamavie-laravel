@extends('dashboard.admin.layouts.main')

@section('content')
<div class="container-fluid">

    <div class="content-header fade-in mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="fw-bold text-primary mb-1" data-en="Add New Service Type" data-ar="إضافة نوع خدمة جديد">Add New Service Type</h1>
            <p class="text-muted mb-0" data-en="Create a new service type with multilingual names and logo." data-ar="إنشاء نوع خدمة جديد بأسماء متعددة اللغات وشعار.">
                Create a new service type with multilingual names and logo.
            </p>
        </div>
        <a href="{{ route('admin.service-types.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i><span data-en="Back" data-ar="رجوع">Back</span>
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-2" data-en="Please fix the following errors:" data-ar="يرجى إصلاح الأخطاء التالية:">Please fix the following errors:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="background: linear-gradient(145deg, #ffffff, #f8fafc);">
        <div class="card-header py-3 px-3 text-white d-flex justify-content-between align-items-center"
             style="background: linear-gradient(90deg, #0d6efd 0%, #6ea8fe 100%);">
            <h5 class="card-title mb-0" data-en="Create Service Type" data-ar="إنشاء نوع خدمة">Create Service Type</h5>
            <span class="badge bg-light text-dark border" data-en="New" data-ar="جديد">New</span>
        </div>

        <div class="card-body p-4" style="max-height: calc(100vh - 220px); overflow-y: auto;">
            <form action="{{ route('admin.service-types.store') }}" method="POST" enctype="multipart/form-data" id="service-type-form">
                @csrf

                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="name_en" class="form-label fw-semibold" data-en="Name (English)" data-ar="الاسم (إنجليزي)">Name (English)</label>
                        <input type="text"
                               name="name_en"
                               id="name_en"
                               class="form-control form-control-lg rounded-3 @error('name_en') is-invalid @enderror"
                               value="{{ old('name_en') }}"
                               required>
                        @error('name_en')
                            <div class="invalid-feedback d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="name_ar" class="form-label fw-semibold" data-en="Name (Arabic)" data-ar="الاسم (عربي)">Name (Arabic)</label>
                        <input type="text"
                               name="name_ar"
                               id="name_ar"
                               class="form-control form-control-lg rounded-3 @error('name_ar') is-invalid @enderror"
                               value="{{ old('name_ar') }}"
                               required>
                        @error('name_ar')
                            <div class="invalid-feedback d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="service_id" class="form-label fw-semibold" data-en="Service" data-ar="الخدمة">Service</label>
                        <select name="service_id"
                                id="service_id"
                                class="form-select form-select-lg rounded-3 @error('service_id') is-invalid @enderror"
                                required>
                            <option value="" disabled selected data-en="Select a service" data-ar="اختر خدمة">Select a service</option>
                            @foreach ($services as $service)
                                @php
                                    $enName = data_get($service, 'name.en', '');
                                    $arName = data_get($service, 'name.ar', '');
                                @endphp
                                <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                    {{ $enName ?: ('Service #'.$service->id) }} @if($arName) — {{ $arName }} @endif
                                </option>
                            @endforeach
                        </select>
                        @error('service_id')
                            <div class="invalid-feedback d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold" data-en="Logo" data-ar="الشعار">Logo</label>

                        <div class="dropzone p-3 border border-2 border-dashed rounded-4 bg-light @error('logo') is-invalid @enderror"
                             id="logo-dropzone" style="min-height: 150px;">
                            <input type="file" name="logo" id="logo" class="d-none" accept="image/*">
                            <div class="text-center text-muted" style="pointer-events:none;">
                                <i class="fas fa-upload fa-2x mb-2"></i>
                                <p class="mb-0" data-en="Drag & drop an image here, or click to select" data-ar="اسحب وأفلت صورة هنا أو اضغط للاختيار">
                                    Drag & drop an image here, or click to select
                                </p>
                                <div class="small" data-en="PNG/JPG recommended" data-ar="يفضل PNG/JPG">PNG/JPG recommended</div>
                            </div>
                        </div>

                        @error('logo')
                            <div class="invalid-feedback d-flex align-items-center">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
                            </div>
                        @enderror

                        <div id="logo-preview" class="mt-3 d-flex flex-wrap gap-3"></div>
                    </div>
                </div>

                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                        <i class="fas fa-save me-2"></i>
                        <span data-en="Save Service Type" data-ar="حفظ نوع الخدمة">Save Service Type</span>
                    </button>
                    <a href="{{ route('admin.service-types.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times me-2"></i>
                        <span data-en="Cancel" data-ar="إلغاء">Cancel</span>
                    </a>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<style>
    .dropzone { transition: all .25s ease; cursor:pointer; }
    .dropzone:hover, .dropzone.dragover { background-color:#eef2ff; border-color:#0d6efd; }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 .25rem rgba(13,110,253,.18);
        border-color:#0d6efd;
    }
    .preview-card { transition: transform .2s ease; position:relative; border-radius:14px; overflow:hidden; }
    .preview-card:hover { transform: scale(1.03); }
    .remove-btn { position:absolute; top:10px; right:10px; z-index:10; }
</style>

<script>
    // fade-in
    document.querySelectorAll('.fade-in').forEach(el => {
        el.style.opacity = 0;
        setTimeout(() => { el.style.transition = 'opacity .5s ease'; el.style.opacity = 1; }, 100);
    });

    // dropzone (logo)
    const logoDropzone = document.getElementById('logo-dropzone');
    const logoInput    = document.getElementById('logo');
    const logoPreview  = document.getElementById('logo-preview');

    logoDropzone.addEventListener('click', () => logoInput.click());
    logoDropzone.addEventListener('dragover', (e) => { e.preventDefault(); logoDropzone.classList.add('dragover'); });
    logoDropzone.addEventListener('dragleave', () => logoDropzone.classList.remove('dragover'));
    logoDropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        logoDropzone.classList.remove('dragover');
        logoInput.files = e.dataTransfer.files;
        logoInput.dispatchEvent(new Event('change'));
    });

    logoInput.addEventListener('change', () => {
        logoPreview.innerHTML = '';
        const file = logoInput.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const card = document.createElement('div');
            card.className = 'preview-card card shadow-sm p-2';
            card.style.width = '170px';

            card.innerHTML = `
                <img src="${e.target.result}" class="w-100 rounded-3" style="height:150px;object-fit:cover;">
                <div class="pt-2 px-1">
                    <div class="text-muted small">${file.name.length > 18 ? file.name.substring(0, 15) + '...' : file.name}</div>
                    <div class="text-muted small">${(file.size / 1024).toFixed(1)} KB</div>
                </div>
            `;

            const removeBtn = document.createElement('button');
            removeBtn.className = 'btn btn-sm btn-danger remove-btn';
            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
            removeBtn.onclick = (ev) => {
                ev.preventDefault();
                ev.stopPropagation();
                logoInput.value = '';
                card.remove();
            };

            card.appendChild(removeBtn);
            logoPreview.appendChild(card);
        };
        reader.readAsDataURL(file);
    });

    // submit loading
    document.getElementById('service-type-form').addEventListener('submit', function() {
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    });
</script>
@endsection
