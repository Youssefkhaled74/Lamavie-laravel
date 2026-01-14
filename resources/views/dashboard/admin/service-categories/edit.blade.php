@extends('dashboard.admin.layouts.main')

@section('content')
<div class="content-header fade-in">
    <h1 class="fw-bold text-primary">Edit Service Category</h1>
    <p class="text-muted">Update the service category details.</p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-lg border-0" style="background: linear-gradient(145deg, #ffffff, #f8fafc);">
    <div class="card-header bg-primary text-white py-3 rounded-top">
        <h5 class="card-title mb-0">Edit Service Category</h5>
    </div>
    <div class="card-body p-4" style="max-height: calc(100vh - 220px); overflow-y: auto;">
        <form action="{{ route('admin.service-categories.update', $serviceCategory) }}" method="POST" enctype="multipart/form-data" id="service-category-form">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4 position-relative">
                        <label for="name_en" class="form-label fw-semibold">Name (English)</label>
                        <input type="text" name="name_en" id="name_en" class="form-control form-control-lg rounded-3 @error('name_en') is-invalid @enderror" value="{{ old('name_en', $serviceCategory->name['en']) }}" required>
                        @error('name_en')
                            <div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4 position-relative">
                        <label for="name_ar" class="form-label fw-semibold">Name (Arabic)</label>
                        <input type="text" name="name_ar" id="name_ar" class="form-control form-control-lg rounded-3 @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $serviceCategory->name['ar']) }}" required>
                        @error('name_ar')
                            <div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4 position-relative">
                        <label for="service_id" class="form-label fw-semibold">Service</label>
                        <select name="service_id" id="service_id" class="form-control form-control-lg rounded-3 @error('service_id') is-invalid @enderror" required>
                            <option value="" disabled>Select a service</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}" {{ old('service_id', $serviceCategory->service_id) == $service->id ? 'selected' : '' }}>{{ $service->name['en'] }}</option>
                            @endforeach
                        </select>
                        @error('service_id')
                            <div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-4">
                        <label for="logo" class="form-label fw-semibold">Logo</label>
                        <div class="dropzone p-3 border border-2 border-dashed rounded-3 bg-light @error('logo') is-invalid @enderror" id="logo-dropzone" style="min-height: 150px;">
                            <input type="file" name="logo" id="logo" class="form-control form-control-lg d-none" accept="image/*">
                            <div class="text-center text-muted" style="pointer-events: none;">
                                <i class="fas fa-upload fa-2x mb-2"></i>
                                <p>Drag and drop an image here or click to select</p>
                            </div>
                        </div>
                        @error('logo')
                            <div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
                        @enderror
                        <div id="logo-preview" class="mt-3 d-flex flex-wrap gap-3" style="max-height: 200px; overflow-y: auto;">
                            @if ($serviceCategory->logo)
                                <div class="preview-card card shadow-sm p-2" style="width: 150px;">
                                    <img src="{{ Storage::url($serviceCategory->logo) }}" class="card-img-top rounded" style="height: 150px; object-fit: cover;">
                                    <div class="card-body p-2">
                                        <p class="card-text text-muted small mb-1">Current Logo</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                    <i class="fas fa-save me-2"></i>Update Service Category
                </button>
                <a href="{{ route('admin.service-categories.index') }}" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .dropzone {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .dropzone:hover, .dropzone.dragover {
        background-color: #e9ecef;
        border-color: var(--primary);
    }
    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.25);
        border-color: var(--primary);
    }
    .preview-card {
        transition: transform 0.2s ease;
        position: relative;
    }
    .preview-card:hover {
        transform: scale(1.05);
    }
    .remove-btn {
        transition: background-color 0.2s ease;
        position: absolute;
        top: -10px;
        right: -10px;
        z-index: 10;
    }
    .remove-btn:hover {
        background-color: var(--danger);
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

    // Drag and Drop for Logo
    const logoDropzone = document.getElementById('logo-dropzone');
    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logo-preview');

    logoDropzone.addEventListener('click', () => logoInput.click());
    logoDropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        logoDropzone.classList.add('dragover');
    });
    logoDropzone.addEventListener('dragleave', () => {
        logoDropzone.classList.remove('dragover');
    });
    logoDropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        logoDropzone.classList.remove('dragover');
        logoInput.files = e.dataTransfer.files;
        logoInput.dispatchEvent(new Event('change'));
    });

    logoInput.addEventListener('change', () => {
        logoPreview.innerHTML = '';
        const file = logoInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const card = document.createElement('div');
                card.className = 'preview-card card shadow-sm p-2';
                card.style.width = '150px';
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'card-img-top rounded';
                img.style.height = '150px';
                img.style.objectFit = 'cover';
                const cardBody = document.createElement('div');
                cardBody.className = 'card-body p-2';
                const fileName = document.createElement('p');
                fileName.className = 'card-text text-muted small mb-1';
                fileName.textContent = file.name.length > 15 ? file.name.substring(0, 12) + '...' : file.name;
                const fileSize = document.createElement('p');
                fileSize.className = 'card-text text-muted small';
                fileSize.textContent = `${(file.size / 1024).toFixed(2)} KB`;
                const removeBtn = document.createElement('button');
                removeBtn.className = 'btn btn-sm btn-outline-danger remove-btn';
                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                removeBtn.onclick = (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    card.remove();
                    logoInput.value = '';
                };
                card.appendChild(img);
                cardBody.appendChild(fileName);
                cardBody.appendChild(fileSize);
                card.appendChild(cardBody);
                card.appendChild(removeBtn);
                logoPreview.appendChild(card);
            };
            reader.readAsDataURL(file);
        }
    });

    // Form Submission Progress
    document.getElementById('service-category-form').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    });
</script>
@endsection