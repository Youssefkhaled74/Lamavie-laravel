@extends('dashboard.admin.layouts.main')

@section('content')
<style>
.stS{
  --p:#0d6efd; --ink:#0f172a; --muted:#64748b;
  --b:rgba(15,23,42,.10);
  --sh:0 22px 60px rgba(2,6,23,.10);
  --sh2:0 10px 24px rgba(2,6,23,.06);
  --r:18px;
}
.stS-head{
  border:1px solid var(--b); border-radius:var(--r); padding:16px;
  background:radial-gradient(900px 220px at 10% 0%, rgba(13,110,253,.14), transparent 60%),
             radial-gradient(900px 240px at 90% 0%, rgba(16,185,129,.10), transparent 60%),
             linear-gradient(180deg, rgba(255,255,255,.96), #fff);
  box-shadow:var(--sh2);
  display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;
}
.stS-head h1{margin:0;font-weight:950;color:var(--p);}
.stS-head p{margin:6px 0 0;color:var(--muted);font-weight:650;}

.stS-card{margin-top:14px;border:1px solid var(--b);border-radius:var(--r);background:#fff;box-shadow:var(--sh);overflow:hidden;}
.stS-card-h{
  padding:14px 16px;border-bottom:1px solid var(--b);
  display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;
  background:linear-gradient(180deg, rgba(13,110,253,.06), rgba(255,255,255,0));
}
.stS-card-h h5{margin:0;font-weight:950;color:var(--ink);}

.stS-btn{
  border-radius:14px; padding:10px 12px; font-weight:950;
  display:inline-flex; gap:8px; align-items:center; text-decoration:none;
}
.stS-btn.back{border:1px solid rgba(15,23,42,.10); background:#fff; color:var(--ink);}
.stS-btn.edit{border:1px solid rgba(13,110,253,.25); background:rgba(13,110,253,.10); color:var(--p);}
.stS-btn:hover{transform:translateY(-1px); box-shadow:0 10px 22px rgba(2,6,23,.08); transition:.15s ease;}

.stS-grid{display:grid; grid-template-columns: 1fr 1fr; gap:12px; padding:16px;}
@media(max-width: 992px){ .stS-grid{grid-template-columns:1fr;} }

.stS-box{
  border:1px solid rgba(15,23,42,.08);
  border-radius:16px;
  background:rgba(248,250,252,.85);
  padding:14px;
}
.stS-k{font-weight:900; color:#64748b; font-size:12px; margin-bottom:6px;}
.stS-v{font-weight:950; color:var(--ink); font-size:15px;}
.stS-chip{
  display:inline-flex;align-items:center;gap:8px;
  padding:7px 10px;border-radius:12px;
  border:1px solid rgba(15,23,42,.10);
  background: rgba(15,23,42,.03);
  font-weight:900;color:#334155;
}
</style>

<div class="stS">
  <div class="stS-head fade-in">
    <div>
      <h1 class="fw-bold text-primary" data-en="Setting Details" data-ar="تفاصيل الإعداد">Setting Details</h1>
      <p class="text-muted" data-en="View the details of the selected setting."
         data-ar="عرض تفاصيل الإعداد المحدد.">View the details of the selected setting.</p>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('admin.settings.index') }}" class="stS-btn back">
        <i class="fas fa-arrow-left"></i> Back
      </a>
      <a href="{{ route('admin.settings.edit', $setting) }}" class="stS-btn edit">
        <i class="fas fa-pen"></i> Edit
      </a>
    </div>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
      <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="stS-card">
    <div class="stS-card-h">
      <h5 class="mb-0">
        <i class="fas fa-gear me-2 text-primary"></i>
        <span data-en="Setting:" data-ar="الإعداد:">Setting:</span> {{ $setting->name['en'] }}
      </h5>

      <span class="stS-chip" title="{{ $setting->key }}">
        <i class="fas fa-key"></i>{{ $setting->key }}
      </span>
    </div>

    <div class="stS-grid">
      <div class="stS-box">
        <div class="stS-k" data-en="Name (English)" data-ar="الاسم (بالإنجليزية)">Name (English)</div>
        <div class="stS-v">{{ $setting->name['en'] }}</div>
      </div>

      <div class="stS-box" dir="rtl">
        <div class="stS-k" data-en="Name (Arabic)" data-ar="الاسم (بالعربية)">Name (Arabic)</div>
        <div class="stS-v">{{ $setting->name['ar'] }}</div>
      </div>

      <div class="stS-box">
        <div class="stS-k" data-en="Key" data-ar="المفتاح">Key</div>
        <div class="stS-v">{{ $setting->key }}</div>
      </div>

      <div class="stS-box">
        <div class="stS-k" data-en="Value" data-ar="القيمة">Value</div>
        <div class="stS-v">{{ $setting->value ?? 'N/A' }}</div>
      </div>

      <div class="stS-box">
        <div class="stS-k" data-en="Created At" data-ar="تاريخ الإنشاء">Created At</div>
        <div class="stS-v">{{ $setting->created_at }}</div>
      </div>

      <div class="stS-box">
        <div class="stS-k" data-en="Updated At" data-ar="تاريخ التعديل">Updated At</div>
        <div class="stS-v">{{ $setting->updated_at }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
