@extends('dashboard.admin.layouts.main')

@section('content')
<style>
.pmS{
  --p:#0d6efd; --ink:#0f172a; --muted:#64748b;
  --b:rgba(15,23,42,.10);
  --sh:0 22px 60px rgba(2,6,23,.10);
  --sh2:0 10px 24px rgba(2,6,23,.06);
  --r:18px;
}
.pmS-head{
  border:1px solid var(--b); border-radius:var(--r); padding:16px;
  background:radial-gradient(900px 220px at 10% 0%, rgba(13,110,253,.14), transparent 60%),
             radial-gradient(900px 240px at 90% 0%, rgba(16,185,129,.10), transparent 60%),
             linear-gradient(180deg, rgba(255,255,255,.96), #fff);
  box-shadow:var(--sh2);
  display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;
}
.pmS-head h1{margin:0;font-weight:950;color:var(--p);}
.pmS-head p{margin:6px 0 0;color:var(--muted);font-weight:650;}

.pmS-card{
  margin-top:14px;
  border:1px solid var(--b);
  border-radius:var(--r);
  background:#fff;
  box-shadow:var(--sh);
  overflow:hidden;
}
.pmS-card-h{
  padding:14px 16px;
  border-bottom:1px solid var(--b);
  display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;
  background:linear-gradient(180deg, rgba(13,110,253,.06), rgba(255,255,255,0));
}
.pmS-card-h h5{margin:0;font-weight:950;color:var(--ink);}
.pmS-btn{
  border-radius:14px; padding:10px 12px; font-weight:950;
  display:inline-flex; gap:8px; align-items:center; text-decoration:none;
}
.pmS-btn.back{border:1px solid rgba(15,23,42,.10); background:#fff; color:var(--ink);}
.pmS-btn.edit{border:1px solid rgba(13,110,253,.25); background:rgba(13,110,253,.10); color:var(--p);}
.pmS-btn:hover{transform:translateY(-1px); box-shadow:0 10px 22px rgba(2,6,23,.08); transition:.15s ease;}

.pmS-grid{display:grid; grid-template-columns: 1fr 1fr; gap:12px; padding:16px;}
@media(max-width: 992px){ .pmS-grid{grid-template-columns:1fr;} }

.pmS-box{
  border:1px solid rgba(15,23,42,.08);
  border-radius:16px;
  background:rgba(248,250,252,.85);
  padding:14px;
}
.pmS-k{font-weight:900; color:#64748b; font-size:12px; margin-bottom:6px;}
.pmS-v{font-weight:950; color:var(--ink); font-size:15px;}

.pmS-badge{
  display:inline-flex; align-items:center; gap:8px;
  padding:7px 10px; border-radius:999px;
  font-weight:950; font-size:12px;
  border:1px solid rgba(15,23,42,.10);
  background: rgba(15,23,42,.03);
  color:#334155;
}
.pmS-badge.on{ background: rgba(16,185,129,.12); border-color: rgba(16,185,129,.22); color:#065f46;}
.pmS-badge.off{ background: rgba(100,116,139,.10); border-color: rgba(100,116,139,.18); color:#475569;}
</style>

@php $isActive = (bool)$paymentMethod->status; @endphp

<div class="pmS">
  <div class="pmS-head fade-in">
    <div>
      <h1 class="fw-bold text-primary" data-en="Payment Method Details" data-ar="تفاصيل طريقة الدفع">Payment Method Details</h1>
      <p class="text-muted" data-en="View the details of the selected payment method."
         data-ar="عرض تفاصيل طريقة الدفع المحددة.">View the details of the selected payment method.</p>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('admin.payment-methods.index') }}" class="pmS-btn back">
        <i class="fas fa-arrow-left"></i> Back
      </a>
      <a href="{{ route('admin.payment-methods.edit', $paymentMethod) }}" class="pmS-btn edit">
        <i class="fas fa-pen"></i> Edit Status
      </a>
    </div>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
      <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="pmS-card">
    <div class="pmS-card-h">
      <h5 class="mb-0">
        <i class="fas fa-wallet me-2 text-primary"></i>
        {{ $paymentMethod->name['en'] }}
      </h5>

      <span class="pmS-badge {{ $isActive ? 'on' : 'off' }}"
            data-en="{{ $isActive ? 'Active' : 'Inactive' }}"
            data-ar="{{ $isActive ? 'نشط' : 'غير نشط' }}">
        <i class="fas {{ $isActive ? 'fa-circle-check' : 'fa-circle-minus' }}"></i>
        {{ $isActive ? 'Active' : 'Inactive' }}
      </span>
    </div>

    <div class="pmS-grid">
      <div class="pmS-box">
        <div class="pmS-k" data-en="Name (English)" data-ar="الاسم (بالإنجليزية)">Name (English)</div>
        <div class="pmS-v">{{ $paymentMethod->name['en'] }}</div>
      </div>

      <div class="pmS-box" dir="rtl">
        <div class="pmS-k" data-en="Name (Arabic)" data-ar="الاسم (بالعربية)">Name (Arabic)</div>
        <div class="pmS-v">{{ $paymentMethod->name['ar'] }}</div>
      </div>

      <div class="pmS-box">
        <div class="pmS-k" data-en="Created At" data-ar="تاريخ الإنشاء">Created At</div>
        <div class="pmS-v">{{ $paymentMethod->created_at }}</div>
      </div>

      <div class="pmS-box">
        <div class="pmS-k" data-en="Updated At" data-ar="تاريخ التعديل">Updated At</div>
        <div class="pmS-v">{{ $paymentMethod->updated_at }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
