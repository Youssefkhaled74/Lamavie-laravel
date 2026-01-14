@extends('dashboard.admin.layouts.main')

@section('content')
<style>
.stE{
  --p:#0d6efd; --ink:#0f172a; --muted:#64748b;
  --b:rgba(15,23,42,.10);
  --sh:0 22px 60px rgba(2,6,23,.10);
  --sh2:0 10px 24px rgba(2,6,23,.06);
  --r:18px;
}
.stE-head{
  border:1px solid var(--b); border-radius:var(--r); padding:16px;
  background:radial-gradient(900px 220px at 10% 0%, rgba(13,110,253,.14), transparent 60%),
             radial-gradient(900px 240px at 90% 0%, rgba(16,185,129,.10), transparent 60%),
             linear-gradient(180deg, rgba(255,255,255,.96), #fff);
  box-shadow:var(--sh2);
  display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;
}
.stE-head h1{margin:0;font-weight:950;color:var(--p);}
.stE-head p{margin:6px 0 0;color:var(--muted);font-weight:650;}

.stE-card{margin-top:14px;border:1px solid var(--b);border-radius:var(--r);background:#fff;box-shadow:var(--sh);overflow:hidden;}
.stE-card-h{padding:14px 16px;border-bottom:1px solid var(--b);background:linear-gradient(180deg, rgba(13,110,253,.06), rgba(255,255,255,0));display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;}
.stE-card-h h5{margin:0;font-weight:950;color:var(--ink);}

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
}
.form-control:disabled{
  background: #f8fafc;
  opacity: .75;
}

.stLockField{
  margin-top:8px;
  border-radius: 14px;
  border:1px solid rgba(245,158,11,.22);
  background: rgba(245,158,11,.10);
  padding: 10px 12px;
  color:#92400e;
  font-weight:750;
  font-size:13px;
}
</style>

@php
  $restrictKey = (int) config('app.restrict_settings', 1); // your logic
@endphp

<div class="stE">
  <div class="stE-head fade-in">
    <div>
      <h1 class="fw-bold text-primary" data-en="Edit Setting" data-ar="تعديل الإعداد">Edit Setting</h1>
      <p class="text-muted" data-en="Update the setting details." data-ar="تحديث تفاصيل الإعداد.">
        Update the setting details.
      </p>
    </div>

    <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary" style="border-radius:14px;font-weight:950;">
      <i class="fas fa-arrow-left me-2"></i>Back
    </a>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
      <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="stE-card">
    <div class="stE-card-h">
      <h5 class="mb-0" data-en="Edit Setting" data-ar="تعديل الإعداد">Edit Setting</h5>
      <span class="text-muted" style="font-weight:800;">
        <i class="fas fa-gear me-1"></i>{{ $setting->key }}
      </span>
    </div>

    <div class="card-body p-4">
      <form action="{{ route('admin.settings.update', $setting) }}" method="POST" id="settings-form">
        @csrf
        @method('PUT')

        <div class="row g-3">
          <div class="col-md-6">
            <label for="name_en" class="form-label" data-en="Name (English)" data-ar="الاسم (بالإنجليزية)">Name (English)</label>
            <input type="text" name="name_en" id="name_en"
                   class="form-control @error('name_en') is-invalid @enderror"
                   value="{{ old('name_en', $setting->name['en']) }}" required>
            @error('name_en')
              <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label for="name_ar" class="form-label" data-en="Name (Arabic)" data-ar="الاسم (بالعربية)">Name (Arabic)</label>
            <input type="text" name="name_ar" id="name_ar"
                   class="form-control @error('name_ar') is-invalid @enderror"
                   value="{{ old('name_ar', $setting->name['ar']) }}" required>
            @error('name_ar')
              <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label for="key" class="form-label" data-en="Key" data-ar="المفتاح">Key</label>
            <input type="text"
                   name="key"
                   id="key"
                   class="form-control @error('key') is-invalid @enderror"
                   value="{{ old('key', $setting->key) }}"
                   data-original-value="{{ $setting->key }}"
                   @if($restrictKey == 0) disabled readonly @endif>
            @error('key')
              <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
            @enderror

            @if($restrictKey == 0)
              <div class="stLockField" data-en="Key is locked. Contact developer to change it."
                   data-ar="المفتاح مقفول. تواصل مع المطور لتغييره.">
                <i class="fas fa-lock me-2"></i>Key is locked. Contact developer to change it.
              </div>
            @endif
          </div>

          <div class="col-md-6">
            <label for="value" class="form-label" data-en="Value" data-ar="القيمة">Value</label>
            <input type="text" name="value" id="value"
                   class="form-control @error('value') is-invalid @enderror"
                   value="{{ old('value', $setting->value) }}"
                   placeholder="e.g., support@lamavie.com">
            @error('value')
              <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="d-flex gap-3 mt-4">
          <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
            <i class="fas fa-save me-2"></i>Update Setting
          </button>
          <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary btn-lg">
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
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('settings-form');
  const submitBtn = document.getElementById('submit-btn');

  if(form && submitBtn){
    form.addEventListener('submit', () => {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    });
  }

  // If key is locked, prevent edits in case it isn't disabled (extra safety)
  const keyInput = document.getElementById('key');
  if(keyInput && keyInput.dataset.originalValue){
    const originalKey = keyInput.dataset.originalValue;
    keyInput.addEventListener('input', function () {
      if(this.value !== originalKey){
        this.value = originalKey;
        if(typeof showCustomAlert === 'function') showCustomAlert('Key is locked. Contact developer.');
        else alert('Key is locked. Contact developer.');
      }
    });
  }
});
</script>
@endsection
