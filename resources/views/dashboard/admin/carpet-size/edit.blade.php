@extends('dashboard.admin.layouts.main')

@section('content')
<div class="content-header fade-in">
    <h1 class="fw-bold text-primary">Edit Carpet Size</h1>
    <p class="text-muted">Update the carpet size details.</p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-lg border-0" style="background: linear-gradient(145deg, #ffffff, #f8fafc);">
    <div class="card-header bg-primary text-white py-3 rounded-top">
        <h5 class="card-title mb-0">Edit Carpet Size</h5>
    </div>
    <div class="card-body p-4" style="max-height: calc(100vh - 220px); overflow-y: auto;">
        <form action="{{ route('admin.carpet-size.update', $carpetSize) }}" method="POST" id="carpet-size-form">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4 position-relative">
                        <label for="name_en" class="form-label fw-semibold">Name (English)</label>
                        <input type="text" name="name_en" id="name_en" class="form-control form-control-lg rounded-3 @error('name_en') is-invalid @enderror" value="{{ old('name_en', $carpetSize->name['en']) }}" required>
                        @error('name_en')
                            <div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4 position-relative">
                        <label for="name_ar" class="form-label fw-semibold">Name (Arabic)</label>
                        <input type="text" name="name_ar" id="name_ar" class="form-control form-control-lg rounded-3 @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $carpetSize->name['ar']) }}" required>
                        @error('name_ar')
                            <div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4 position-relative">
                        <label for="service_category_id" class="form-label fw-semibold">Service Category</label>
                        <select name="service_category_id" id="service_category_id" class="form-control form-control-lg rounded-3 @error('service_category_id') is-invalid @enderror" required>
                            <option value="" disabled>Select a service category</option>
                            @foreach ($serviceCategories as $serviceCategory)
                                <option value="{{ $serviceCategory->id }}" {{ old('service_category_id', $carpetSize->service_category_id) == $serviceCategory->id ? 'selected' : '' }}>{{ $serviceCategory->name['en'] }}</option>
                            @endforeach
                        </select>
                        @error('service_category_id')
                            <div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4 position-relative">
                        <label for="price" class="form-label fw-semibold">Price</label>
                        <input type="number" step="0.01" name="price" id="price" class="form-control form-control-lg rounded-3 @error('price') is-invalid @enderror" value="{{ old('price', $carpetSize->price) }}" placeholder="e.g., 50.00">
                        @error('price')
                            <div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                    <i class="fas fa-save me-2"></i>Update Carpet Size
                </button>
                <a href="{{ route('admin.carpet-size.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Cancel
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
    // Animation for form fields
    document.querySelectorAll('.fade-in, .form-control, .form-label').forEach(element => {
        element.style.opacity = 0;
        setTimeout(() => {
            element.style.transition = 'opacity 0.5s ease';
            element.style.opacity = 1;
        }, 100);
    });

    // Form Submission Progress
    document.getElementById('carpet-size-form').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    });
</script>
@endsection