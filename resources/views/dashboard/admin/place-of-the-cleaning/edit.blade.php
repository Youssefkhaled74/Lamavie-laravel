@extends('dashboard.admin.layouts.main')

@section('content')
<style>
/* reuse same styles as create */
</style>

<div class="pcF">
  <div class="pcF-head fade-in">
    <div>
      <h1 class="fw-bold text-primary">Edit Place of the Cleaning</h1>
      <p class="text-muted">Update the place record details.</p>
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
      <h5 class="card-title mb-0">Edit Place of the Cleaning</h5>
    </div>

    <div class="card-body p-4">
      <form action="{{ route('admin.place-of-the-cleaning.update', $placeOfTheCleaning) }}" method="POST" id="place-form">
        @csrf
        @method('PUT')

        <div class="row g-3">
          <div class="col-md-6">
            <label for="name_en" class="form-label">Name (English)</label>
            <input type="text" name="name_en" id="name_en"
                   class="form-control @error('name_en') is-invalid @enderror"
                   value="{{ old('name_en', $placeOfTheCleaning->name['en']) }}" required>
            @error('name_en')
              <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label for="name_ar" class="form-label">Name (Arabic)</label>
            <input type="text" name="name_ar" id="name_ar"
                   class="form-control @error('name_ar') is-invalid @enderror"
                   value="{{ old('name_ar', $placeOfTheCleaning->name['ar']) }}" required>
            @error('name_ar')
              <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label for="service_category_id" class="form-label">Service Category</label>
            <select name="service_category_id" id="service_category_id"
                    class="form-control @error('service_category_id') is-invalid @enderror" required>
              <option value="" disabled>Select a service category</option>
              @foreach ($serviceCategories as $serviceCategory)
                <option value="{{ $serviceCategory->id }}"
                        {{ old('service_category_id', $placeOfTheCleaning->service_category_id) == $serviceCategory->id ? 'selected' : '' }}>
                  {{ $serviceCategory->name['en'] }}
                </option>
              @endforeach
            </select>
            @error('service_category_id')
              <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label for="price" class="form-label">Price</label>
            <input type="number" step="0.01" name="price" id="price"
                   class="form-control @error('price') is-invalid @enderror"
                   value="{{ old('price', $placeOfTheCleaning->price) }}" placeholder="e.g., 50.00">
            @error('price')
              <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="d-flex gap-3 mt-4">
          <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
            <i class="fas fa-save me-2"></i>Update Place
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
