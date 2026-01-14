@extends('dashboard.admin.layouts.main')

@section('content')
<style>
.pcF{
  --p:#0d6efd; --ink:#0f172a; --muted:#64748b;
  --b:rgba(15,23,42,.10);
  --sh:0 22px 60px rgba(2,6,23,.10);
  --sh2:0 10px 24px rgba(2,6,23,.06);
  --r:18px;
}
.pcF-head{
  border:1px solid var(--b); border-radius:var(--r); padding:16px;
  background:radial-gradient(900px 220px at 10% 0%, rgba(13,110,253,.14), transparent 60%),
             radial-gradient(900px 240px at 90% 0%, rgba(16,185,129,.10), transparent 60%),
             linear-gradient(180deg, rgba(255,255,255,.96), #fff);
  box-shadow:var(--sh2);
  display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;
}
.pcF-head h1{margin:0;font-weight:950;color:var(--p);}
.pcF-head p{margin:6px 0 0;color:var(--muted);font-weight:650;}
.pcF-card{margin-top:14px;border:1px solid var(--b);border-radius:var(--r);background:#fff;box-shadow:var(--sh);overflow:hidden;}
.pcF-card-h{padding:14px 16px;border-bottom:1px solid var(--b);background:linear-gradient(180deg, rgba(13,110,253,.06), rgba(255,255,255,0));}
.pcF-card-h h5{margin:0;font-weight:950;color:var(--ink);}
.form-label{font-weight:900;}
.form-control{
  border-radius: 14px;
  border:1px solid rgba(15,23,42,.10);
  padding:.65rem .85rem;
  font-weight:650;
}
.form-control:focus{
  border-color: rgba(13,110,253,.45);
  box-shadow: 0 0 0 6px rgba(13,110,253,.10);
  outline:none;
}
.pcHint{font-size:12px;color:#94a3b8;font-weight:800;}
</style>

<div class="pcF">
  <div class="pcF-head fade-in">
    <div>
      <h1 class="fw-bold text-primary">Add New Place of the Cleaning</h1>
      <p class="text-muted">Create a new place record with multilingual names, service category, and price.</p>
    </div>

    <a href="{{ route('admin.place-of-the-cleaning.index') }}" class="btn btn-outline-secondary" style="border-radius:14px;font-weight:950;">
      <i class="fas fa-arrow-left me-2"></i>Back
    </a>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
      <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="pcF-card">
    <div class="pcF-card-h">
      <h5 class="card-title mb-0">Create Place of the Cleaning</h5>
    </div>

    <div class="card-body p-4">
      <form action="{{ route('admin.place-of-the-cleaning.store') }}" method="POST" id="place-form">
        @csrf

        <div class="row g-3">
          <div class="col-md-6">
            <label for="name_en" class="form-label">Name (English)</label>
            <input type="text" name="name_en" id="name_en"
                   class="form-control @error('name_en') is-invalid @enderror"
                   value="{{ old('name_en') }}" required>
            @error('name_en')
              <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label for="name_ar" class="form-label">Name (Arabic)</label>
            <input type="text" name="name_ar" id="name_ar"
                   class="form-control @error('name_ar') is-invalid @enderror"
                   value="{{ old('name_ar') }}" required>
            @error('name_ar')
              <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label for="service_category_id" class="form-label">Service Category</label>
            <select name="service_category_id" id="service_category_id"
                    class="form-control @error('service_category_id') is-invalid @enderror" required>
              <option value="" disabled selected>Select a service category</option>
              @foreach ($serviceCategories as $serviceCategory)
                <option value="{{ $serviceCategory->id }}" {{ old('service_category_id') == $serviceCategory->id ? 'selected' : '' }}>
                  {{ $serviceCategory->name['en'] }}
                </option>
              @endforeach
            </select>
            @error('service_category_id')
              <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
            @enderror
            <div class="pcHint mt-1">Choose the category this place belongs to.</div>
          </div>

          <div class="col-md-6">
            <label for="price" class="form-label">Price</label>
            <input type="number" step="0.01" name="price" id="price"
                   class="form-control @error('price') is-invalid @enderror"
                   value="{{ old('price') }}" placeholder="e.g., 50.00">
            @error('price')
              <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
            @enderror
            <div class="pcHint mt-1">Leave empty if not applicable.</div>
          </div>
        </div>

        <div class="d-flex gap-3 mt-4">
          <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
            <i class="fas fa-save me-2"></i>Save Place
          </button>
          <a href="{{ route('admin.place-of-the-cleaning.index') }}" class="btn btn-outline-secondary btn-lg">
            <i class="fas fa-arrow-left me-2"></i>Cancel
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('place-form');
  const btn  = document.getElementById('submit-btn');
  if(form && btn){
    form.addEventListener('submit', () => {
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    });
  }
});
</script>
@endsection
