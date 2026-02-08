@extends('dashboard.admin.layouts.main')

@section('content')
<div class="content-header fade-in">
    <h1 class="fw-bold text-primary" data-en="Edit Item" data-ar="تعديل العنصر">Edit Item</h1>
    <p class="text-muted" data-en="Update the item details." data-ar="تحديث تفاصيل العنصر.">Update the item details.</p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-lg border-0" style="background: linear-gradient(145deg, #ffffff, #f8fafc);">
    <div class="card-header bg-primary text-white py-3 rounded-top">
        <h5 class="card-title mb-0" data-en="Edit Item" data-ar="تعديل العنصر">Edit Item</h5>
    </div>
    <div class="card-body p-4" style="max-height: calc(100vh - 220px); overflow-y: auto;">
        <form action="{{ route('admin.your-items.update', $yourItem) }}" method="POST" id="your-items-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4 position-relative">
                        <label for="name_en" class="form-label fw-semibold" data-en="Name (English)" data-ar="الاسم (بالإنجليزية)">Name (English)</label>
                        <input type="text" name="name_en" id="name_en" class="form-control form-control-lg rounded-3 @error('name_en') is-invalid @enderror" value="{{ old('name_en', $yourItem->name['en']) }}" required>
                        @error('name_en')
                            <div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4 position-relative">
                        <label for="name_ar" class="form-label fw-semibold" data-en="Name (Arabic)" data-ar="الاسم (بالعربية)">Name (Arabic)</label>
                        <input type="text" name="name_ar" id="name_ar" class="form-control form-control-lg rounded-3 @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $yourItem->name['ar']) }}" required>
                        @error('name_ar')
                            <div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4 position-relative">
                        <label for="service_category_id" class="form-label fw-semibold" data-en="Service Category" data-ar="فئة الخدمة">Service Category</label>
                        <select name="service_category_id" id="service_category_id" class="form-control form-control-lg rounded-3 @error('service_category_id') is-invalid @enderror" required>
                            <option value="" disabled data-en="Select a service category" data-ar="اختر فئة الخدمة">Select a service category</option>
                            @foreach ($serviceCategories as $serviceCategory)
                                <option value="{{ $serviceCategory->id }}" {{ old('service_category_id', $yourItem->service_category_id) == $serviceCategory->id ? 'selected' : '' }}>
                                    {{ $serviceCategory->name['en'] ?? '' }} @if(!empty($serviceCategory->name['ar'])) / {{ $serviceCategory->name['ar'] }} @endif
                                </option>
                            @endforeach
                        </select>
                        @error('service_category_id')
                            <div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4 position-relative">
                        <label for="logo" class="form-label fw-semibold" data-en="Logo" data-ar="الشعار">Logo</label>
                        <input type="file" name="logo" id="logo" class="form-control form-control-lg rounded-3 @error('logo') is-invalid @enderror">
                        @if ($yourItem->logo)
                            <div class="mt-2">
                                <img src="{{ Storage::url($yourItem->logo) }}" alt="{{ $yourItem->name['en'] }}" style="max-width: 100px; max-height: 100px;">
                            </div>
                        @endif
                        @error('logo')
                            <div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4 position-relative">
                        <label for="washing_price" class="form-label fw-semibold" data-en="Washing Price" data-ar="سعر الغسيل">Washing Price</label>
                        <input type="number" step="0.01" name="washing_price" id="washing_price" class="form-control form-control-lg rounded-3 @error('washing_price') is-invalid @enderror" value="{{ old('washing_price', $yourItem->washing_price ?? $yourItem->price) }}" placeholder="e.g., 50.00" data-en-placeholder="e.g., 50.00" data-ar-placeholder="مثال: 50.00">
                        @error('washing_price')
                            <div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4 position-relative">
                        <label for="ironing_price" class="form-label fw-semibold" data-en="Ironing Price" data-ar="سعر الكي">Ironing Price</label>
                        <input type="number" step="0.01" name="ironing_price" id="ironing_price" class="form-control form-control-lg rounded-3 @error('ironing_price') is-invalid @enderror" value="{{ old('ironing_price', $yourItem->ironing_price) }}" placeholder="e.g., 30.00" data-en-placeholder="e.g., 30.00" data-ar-placeholder="مثال: 30.00">
                        @error('ironing_price')
                            <div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                    <i class="fas fa-save me-2"></i><span data-en="Update Item" data-ar="تحديث العنصر">Update Item</span>
                </button>
                <a href="{{ route('admin.your-items.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i><span data-en="Cancel" data-ar="إلغاء">Cancel</span>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.25);
        border-color: var(--primary);
    }
</style>
<script>
    function applyPlaceholders() {
        const lang = document.documentElement.getAttribute('lang') || 'en';
        document.querySelectorAll('[data-en-placeholder]').forEach(el => {
            el.setAttribute('placeholder', lang === 'ar' ? el.getAttribute('data-ar-placeholder') : el.getAttribute('data-en-placeholder'));
        });
    }

    // Animation for form fields
    document.querySelectorAll('.fade-in, .form-control, .form-label').forEach(element => {
        element.style.opacity = 0;
        setTimeout(() => {
            element.style.transition = 'opacity 0.5s ease';
            element.style.opacity = 1;
        }, 100);
    });

    // Form Submission Progress
    document.getElementById('your-items-form').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    });

    applyPlaceholders();
</script>
@endsection
