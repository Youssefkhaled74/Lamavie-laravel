@extends('dashboard.admin.layouts.main')

@section('content')
<style>
    /* نفس CSS بتاع create (مختصر عشان ما نكرر كتير) */
    .page-shell{ max-width: 1100px; margin:0 auto; }
    .panel{ background:#fff;border-radius:16px;border:1px solid rgba(15,23,42,.06);box-shadow:0 12px 30px rgba(2,6,23,.08);overflow:hidden; }
    .panel-header{ background:linear-gradient(90deg,#0d6efd 0%,#6ea8fe 100%);color:#fff;padding:16px 18px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap; }
    .panel-title{ margin:0;font-weight:900;letter-spacing:-.02em;display:flex;align-items:center;gap:10px; }
    .panel-body{ padding:18px;max-height: calc(100vh - 220px);overflow:auto; }
    .form-label{ font-weight:800;color:#334155; }
    .form-control{ border-radius:12px;border:1px solid rgba(15,23,42,.12);padding:10px 12px;box-shadow:none; }
    .form-control:focus{ border-color: rgba(37,99,235,.55);box-shadow:0 0 0 .2rem rgba(37,99,235,.12); }
    .btn-soft{ border-radius:12px;font-weight:900;padding:10px 14px; }
    .btn-primary{ border:none;background:linear-gradient(90deg,#2563eb,#3b82f6);box-shadow:0 10px 24px rgba(37,99,235,.18); }
    .dropzone{ border:2px dashed rgba(15,23,42,.18);border-radius:14px;background:linear-gradient(145deg,#ffffff,#f8fafc);padding:14px;cursor:pointer;transition:all .18s ease;min-height:160px;display:flex;align-items:center;justify-content:center;text-align:center; }
    .dropzone:hover,.dropzone.dragover{ background:#eef2ff;border-color:rgba(37,99,235,.6);box-shadow:0 10px 24px rgba(37,99,235,.10); }
    .dz-inner{ pointer-events:none;color:#64748b; }
    .dz-inner i{ font-size:30px;margin-bottom:10px;color:#2563eb; }
    .preview-wrap{ display:flex;gap:12px;flex-wrap:wrap; }
    .preview-card{ width:160px;border-radius:14px;border:1px solid rgba(15,23,42,.06);box-shadow:0 10px 20px rgba(2,6,23,.06);overflow:hidden;position:relative;background:#fff; }
    .preview-card img{ width:100%;height:150px;object-fit:cover;display:block;background:#f8fafc; }
    .preview-card .meta{ padding:10px 10px; }
    .remove-btn{ position:absolute;top:10px;right:10px;width:34px;height:34px;border-radius:999px;border:1px solid rgba(255,255,255,.6);background:rgba(15,23,42,.45);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer; }
    .remove-btn:hover{ background:rgba(239,68,68,.92); }
    .section-title{ font-weight:900;color:#0f172a;margin:0 0 10px;display:flex;align-items:center;gap:10px; }
    .section-title i{ color:#2563eb; }
    .existing-media img{ background:#f8fafc;border:1px solid rgba(15,23,42,.08); }
</style>

<div class="content-header mb-3">
    <h1 class="fw-bold text-primary">Edit Service</h1>
    <p class="text-muted mb-0">Update the service details with multilingual support.</p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="page-shell">
    <div class="panel">
        <div class="panel-header">
            <h5 class="panel-title"><i class="fas fa-pen"></i> Edit Service</h5>
            <a href="{{ route('admin.services.index') }}" class="btn btn-light btn-soft">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="panel-body">
            <form action="{{ route('admin.services.update', $service) }}" method="POST" enctype="multipart/form-data" id="service-form">
                @csrf
                @method('PUT')

                <h6 class="section-title"><i class="fas fa-language"></i> Multilingual Content</h6>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name_en" class="form-label">Name (English)</label>
                        <input type="text" name="name_en" id="name_en"
                               class="form-control @error('name_en') is-invalid @enderror"
                               value="{{ old('name_en', data_get($service, 'name.en', '')) }}" required>
                        @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="name_ar" class="form-label">Name (Arabic)</label>
                        <input type="text" name="name_ar" id="name_ar"
                               class="form-control @error('name_ar') is-invalid @enderror"
                               value="{{ old('name_ar', data_get($service, 'name.ar', '')) }}" required>
                        @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="about_en" class="form-label">About (English)</label>
                        <textarea name="about_en" id="about_en" rows="4"
                                  class="form-control @error('about_en') is-invalid @enderror"
                                  required>{{ old('about_en', data_get($service, 'about.en', '')) }}</textarea>
                        @error('about_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="about_ar" class="form-label">About (Arabic)</label>
                        <textarea name="about_ar" id="about_ar" rows="4"
                                  class="form-control @error('about_ar') is-invalid @enderror"
                                  required>{{ old('about_ar', data_get($service, 'about.ar', '')) }}</textarea>
                        @error('about_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="description_en" class="form-label">Description (English)</label>
                        <textarea name="description_en" id="description_en" rows="6"
                                  class="form-control @error('description_en') is-invalid @enderror"
                                  required>{{ old('description_en', data_get($service, 'description.en', '')) }}</textarea>
                        @error('description_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="description_ar" class="form-label">Description (Arabic)</label>
                        <textarea name="description_ar" id="description_ar" rows="6"
                                  class="form-control @error('description_ar') is-invalid @enderror"
                                  required>{{ old('description_ar', data_get($service, 'description.ar', '')) }}</textarea>
                        @error('description_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="section-title"><i class="fas fa-images"></i> Media</h6>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Logo</label>

                        @if ($service->logo)
                            <div class="existing-media mb-2">
                                <div class="text-muted small mb-1">Current Logo</div>
                                <img src="{{ asset('storage/' . $service->logo) }}" alt="Logo"
                                     class="rounded shadow-sm" style="width:110px;height:110px;object-fit:cover;">
                            </div>
                        @endif

                        <div class="dropzone @error('logo') border-danger @enderror" id="logo-dropzone">
                            <input type="file" name="logo" id="logo" class="d-none" accept="image/*">
                            <div class="dz-inner">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <div class="fw-bold">Drop new logo here</div>
                                <div class="hint">or click to browse</div>
                            </div>
                        </div>
                        @error('logo') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                        <div id="logo-preview" class="preview-wrap mt-3"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Add Photos</label>
                        <div class="dropzone @error('photos.*') border-danger @enderror" id="photos-dropzone">
                            <input type="file" name="photos[]" id="photos" class="d-none" accept="image/*" multiple>
                            <div class="dz-inner">
                                <i class="fas fa-images"></i>
                                <div class="fw-bold">Drop photos here</div>
                                <div class="hint">or click to select multiple</div>
                            </div>
                        </div>
                        @error('photos.*') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                        <div id="photos-preview" class="preview-wrap mt-3"></div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 flex-wrap">
                    <button type="submit" class="btn btn-primary btn-soft" id="submit-btn">
                        <i class="fas fa-save me-1"></i> Update Service
                    </button>
                    <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary btn-soft">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    @if ($service->photoServices->count())
        <div class="panel mt-3">
            <div class="panel-header" style="background:linear-gradient(90deg,#0ea5e9,#6366f1);">
                <h5 class="panel-title"><i class="fas fa-photo-video"></i> Existing Photos</h5>
            </div>
            <div class="panel-body">
                <div class="row g-3">
                    @foreach ($service->photoServices as $photo)
                        <div class="col-md-3 col-sm-6">
                            <div class="preview-card" style="width:100%;">
                                <img src="{{ asset('storage/' . $photo->file_path) }}" alt="{{ $photo->photo_name }}">
                                <div class="meta">
                                    <div class="n">{{ \Illuminate\Support\Str::limit($photo->photo_name ?? 'Photo', 22) }}</div>
                                </div>

                                <form action="{{ route('admin.services.destroyPhoto', ['photo' => $photo->id]) }}"
                                      method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="remove-btn"
                                            onclick="return confirm('Are you sure you want to delete this photo?')"
                                            title="Delete photo">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
(function () {
    function bytesToKB(bytes){ return (bytes/1024).toFixed(2) + ' KB'; }
    function shortName(name){ return name.length > 18 ? name.substring(0, 15) + '...' : name; }

    function bindDropzone(dropzoneId, inputId, previewId, multiple=false){
        const dz = document.getElementById(dropzoneId);
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!dz || !input || !preview) return;

        dz.addEventListener('click', () => input.click());
        dz.addEventListener('dragover', (e) => { e.preventDefault(); dz.classList.add('dragover'); });
        dz.addEventListener('dragleave', () => dz.classList.remove('dragover'));
        dz.addEventListener('drop', (e) => {
            e.preventDefault();
            dz.classList.remove('dragover');
            input.files = e.dataTransfer.files;
            input.dispatchEvent(new Event('change'));
        });

        input.addEventListener('change', () => {
            preview.innerHTML = '';
            const files = Array.from(input.files || []);
            if (!files.length) return;

            files.forEach((file) => {
                const reader = new FileReader();
                reader.onload = function(ev){
                    const card = document.createElement('div');
                    card.className = 'preview-card';

                    const img = document.createElement('img');
                    img.src = ev.target.result;

                    const meta = document.createElement('div');
                    meta.className = 'meta';
                    meta.innerHTML = `<div class="n">${shortName(file.name)}</div><div class="s">${bytesToKB(file.size)}</div>`;

                    const remove = document.createElement('button');
                    remove.type = 'button';
                    remove.className = 'remove-btn';
                    remove.innerHTML = '<i class="fas fa-times"></i>';

                    remove.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        card.remove();

                        if (!multiple){
                            input.value = '';
                            return;
                        }
                        const dt = new DataTransfer();
                        Array.from(input.files).forEach(f => { if (f !== file) dt.items.add(f); });
                        input.files = dt.files;
                    });

                    card.appendChild(img);
                    card.appendChild(meta);
                    card.appendChild(remove);
                    preview.appendChild(card);
                };
                reader.readAsDataURL(file);
            });
        });
    }

    bindDropzone('logo-dropzone', 'logo', 'logo-preview', false);
    bindDropzone('photos-dropzone', 'photos', 'photos-preview', true);

    const form = document.getElementById('service-form');
    const btn = document.getElementById('submit-btn');
    if (form && btn){
        form.addEventListener('submit', function(){
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
        });
    }
})();
</script>
@endsection
