@extends('dashboard.admin.layouts.main')

@section('content')
<div class="content-header fade-in">
    <h1 class="fw-bold text-primary" data-en="Edit Area" data-ar="تعديل المنطقة">Edit Area</h1>
    <p class="text-muted" data-en="Modify area details." data-ar="تعديل تفاصيل المنطقة.">Modify area details.</p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-lg border-0" style="background: linear-gradient(145deg, #ffffff, #f8fafc);">
    <div class="card-header bg-primary text-white py-3 rounded-top">
        <h5 class="card-title mb-0" data-en="Edit Area" data-ar="تعديل المنطقة">Edit Area</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.areas.update', $area) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4 position-relative">
                        <label for="name_en" class="form-label fw-semibold" data-en="Name (English)" data-ar="الاسم (بالإنجليزية)">Name (English)</label>
                        <input type="text" name="name_en" id="name_en" class="form-control form-control-lg rounded-3 @error('name_en') is-invalid @enderror" value="{{ old('name_en', data_get($area, 'name.en')) }}" required>
                        @error('name_en')
                            <div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4 position-relative">
                        <label for="name_ar" class="form-label fw-semibold" data-en="Name (Arabic)" data-ar="الاسم (بالعربية)">Name (Arabic)</label>
                        <input type="text" name="name_ar" id="name_ar" class="form-control form-control-lg rounded-3 @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', data_get($area, 'name.ar')) }}" required>
                        @error('name_ar')
                            <div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="slug" class="form-label fw-semibold" data-en="Slug (optional)" data-ar="الاسم المختصر (اختياري)">Slug (optional)</label>
                <input type="text" name="slug" id="slug" class="form-control form-control-lg rounded-3 @error('slug') is-invalid @enderror" value="{{ old('slug', $area->slug) }}">
                @error('slug')
                    <div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="form-label fw-semibold" data-en="Description" data-ar="الوصف">Description</label>
                <textarea name="description" id="description" class="form-control form-control-lg rounded-3 @error('description') is-invalid @enderror" rows="4">{{ old('description', $area->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="price_increase_percentage" class="form-label fw-semibold" data-en="Price Increase Percentage (%)" data-ar="نسبة زيادة السعر (%)">Price Increase Percentage (%)</label>
                <input type="number" step="0.01" min="0" name="price_increase_percentage" id="price_increase_percentage" class="form-control form-control-lg rounded-3 @error('price_increase_percentage') is-invalid @enderror" value="{{ old('price_increase_percentage', $area->price_increase_percentage ?? 0) }}" placeholder="e.g., 10.00">
                @error('price_increase_percentage')
                    <div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn btn-primary btn-lg" data-en="Update Area" data-ar="تحديث المنطقة">
                    <i class="fas fa-save me-2"></i>Update Area
                </button>
                <a href="{{ route('admin.areas.index') }}" class="btn btn-outline-secondary btn-lg" data-en="Cancel" data-ar="إلغاء">
                    <i class="fas fa-arrow-left me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
