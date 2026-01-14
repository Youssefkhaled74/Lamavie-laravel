@extends('dashboard.admin.layouts.main')

@section('content')
<style>
.stC{
  --p:#0d6efd; --ink:#0f172a; --muted:#64748b;
  --b:rgba(15,23,42,.10);
  --sh:0 22px 60px rgba(2,6,23,.10);
  --sh2:0 10px 24px rgba(2,6,23,.06);
  --r:18px;
}
.stC-head{
  border:1px solid var(--b); border-radius:var(--r); padding:16px;
  background:radial-gradient(900px 220px at 10% 0%, rgba(13,110,253,.14), transparent 60%),
             radial-gradient(900px 240px at 90% 0%, rgba(16,185,129,.10), transparent 60%),
             linear-gradient(180deg, rgba(255,255,255,.96), #fff);
  box-shadow:var(--sh2);
  display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;
}
.stC-head h1{margin:0;font-weight:950;color:var(--p);}
.stC-head p{margin:6px 0 0;color:var(--muted);font-weight:650;}

.stC-card{margin-top:14px;border:1px solid var(--b);border-radius:var(--r);background:#fff;box-shadow:var(--sh);overflow:hidden;}
.stC-card-h{padding:14px 16px;border-bottom:1px solid var(--b);background:linear-gradient(180deg, rgba(13,110,253,.06), rgba(255,255,255,0));}
.stC-card-h h5{margin:0;font-weight:950;color:var(--ink);}

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
.stHint{font-size:12px;color:#94a3b8;font-weight:800;}
</style>

@php
  $restrict = (int) env('RESTRICT_SETTINGS', 1);
  $canManage = $restrict !== 0;
@endphp

<div class="stC">
  <div class="stC-head fade-in">
    <div>
      <h1 class="fw-bold text-primary" data-en="Add New Setting" data-ar="إضافة إعداد جديد">Add New Setting</h1>
      <p class="text-muted"
         data-en="Create a new setting with a key, value, and multilingual names."
         data-ar="إنشاء إعداد جديد بمفتاح وقيمة وأسماء متعددة اللغات.">
        Create a new setting with a key, value, and multilingual names.
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

  @if(!$canManage)
    <div class="alert alert-warning mt-3">
      <i class="fas fa-lock me-2"></i>
      <span data-en="Creation is restricted. Contact the developer." data-ar="الإضافة مقيدة. تواصل مع المطور.">
        Creation is restricted. Contact the developer.
      </span>
    </div>
  @endif

  <div class="stC-card">
    <div class="stC-card-h">
      <h5 class="card-title mb-0">Create Setting</h5>
    </div>

    <div class="card-body p-4">
      <form action="{{ route('admin.settings.store') }}" method="POST" id="settings-form">
        @csrf

        <div class="row g-3">
          <div class="col-md-6">
            <label for="name_en" class="form-label" data-en="Name (English)" data-ar="الاسم (بالإنجليزية)">Name (English)</label>
            <input type="text" name="name_en" id="name_en"
                   class="form-control @error('name_en') is-invalid @enderror"
                   value="{{ old('name_en') }}" required @if(!$canManage) disabled @endif>
            @error('name_en')
              <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label for="name_ar" class="form-label" data-en="Name (Arabic)" data-ar="الاسم (بالعربية)">Name (Arabic)</label>
            <input type="text" name="name_ar" id="name_ar"
                   class="form-control @error('name_ar') is-invalid @enderror"
                   value="{{ old('name_ar') }}" required @if(!$canManage) disabled @endif>
            @error('name_ar')
              <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label for="key" class="form-label" data-en="Key" data-ar="المفتاح">Key</label>
            <input type="text" name="key" id="key"
                   class="form-control @error('key') is-invalid @enderror"
                   value="{{ old('key') }}" required @if(!$canManage) disabled @endif>
            <div class="stHint">Example: support_email, facebook_link, vat_percent</div>
            @error('key')
              <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6">
            <label for="value" class="form-label" data-en="Value" data-ar="القيمة">Value</label>
            <input type="text" name="value" id="value"
                   class="form-control @error('value') is-invalid @enderror"
                   value="{{ old('value') }}"
                   placeholder="e.g., support@lamavie.com" @if(!$canManage) disabled @endif>
            @error('value')
              <div class="invalid-feedback"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>
            @enderror
          </div>
        </div>

        <div class="d-flex gap-3 mt-4">
          <button type="submit" class="btn btn-primary btn-lg" id="submit-btn"
                  @if(!$canManage) disabled @endif>
            <i class="fas fa-save me-2"></i>Save Setting
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
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('settings-form');
  const submitBtn = document.getElementById('submit-btn');
  if(form && submitBtn){
    form.addEventListener('submit', () => {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    });
  }
});
</script>
@endsection
