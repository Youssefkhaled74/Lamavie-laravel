@extends('dashboard.admin.layouts.main')

@section('content')
<style>
.pcS{
  --p:#0d6efd; --ink:#0f172a; --muted:#64748b;
  --b:rgba(15,23,42,.10);
  --sh:0 22px 60px rgba(2,6,23,.10);
  --sh2:0 10px 24px rgba(2,6,23,.06);
  --r:18px;
}
.pcS-head{
  border:1px solid var(--b); border-radius:var(--r); padding:16px;
  background:radial-gradient(900px 220px at 10% 0%, rgba(13,110,253,.14), transparent 60%),
             radial-gradient(900px 240px at 90% 0%, rgba(16,185,129,.10), transparent 60%),
             linear-gradient(180deg, rgba(255,255,255,.96), #fff);
  box-shadow:var(--sh2);
  display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;
}
.pcS-head h1{margin:0;font-weight:950;color:var(--p);}
.pcS-head p{margin:6px 0 0;color:var(--muted);font-weight:650;}

.pcS-card{margin-top:14px;border:1px solid var(--b);border-radius:var(--r);background:#fff;box-shadow:var(--sh);overflow:hidden;}
.pcS-card-h{
  padding:14px 16px;border-bottom:1px solid var(--b);
  display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;
  background:linear-gradient(180deg, rgba(13,110,253,.06), rgba(255,255,255,0));
}
.pcS-card-h h5{margin:0;font-weight:950;color:var(--ink);}

.pcS-btn{
  border-radius:14px; padding:10px 12px; font-weight:950;
  display:inline-flex; gap:8px; align-items:center; text-decoration:none;
}
.pcS-btn.back{border:1px solid rgba(15,23,42,.10); background:#fff; color:var(--ink);}
.pcS-btn.edit{border:1px solid rgba(13,110,253,.25); background:rgba(13,110,253,.10); color:var(--p);}
.pcS-btn:hover{transform:translateY(-1px); box-shadow:0 10px 22px rgba(2,6,23,.08); transition:.15s ease;}

.pcS-grid{display:grid; grid-template-columns: 1fr 1fr; gap:12px; padding:16px;}
@media(max-width: 992px){ .pcS-grid{grid-template-columns:1fr;} }

.pcS-box{
  border:1px solid rgba(15,23,42,.08);
  border-radius:16px;
  background:rgba(248,250,252,.85);
  padding:14px;
}
.pcS-k{font-weight:900; color:#64748b; font-size:12px; margin-bottom:6px;}
.pcS-v{font-weight:950; color:var(--ink); font-size:15px;}
</style>

@php
  $catEn = $placeOfTheCleaning->serviceCategory?->name['en'] ?? 'N/A';
  $priceText = $placeOfTheCleaning->price !== null ? number_format($placeOfTheCleaning->price, 2) : 'N/A';
@endphp

<div class="pcS">
  <div class="pcS-head fade-in">
    <div>
      <h1 class="fw-bold text-primary">Place of the Cleaning Details</h1>
      <p class="text-muted">View the details of the selected place record.</p>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('admin.place-of-the-cleaning.index') }}" class="pcS-btn back">
        <i class="fas fa-arrow-left"></i> Back
      </a>
      <a href="{{ route('admin.place-of-the-cleaning.edit', $placeOfTheCleaning) }}" class="pcS-btn edit">
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

  <div class="pcS-card">
    <div class="pcS-card-h">
      <h5 class="mb-0">Place: {{ $placeOfTheCleaning->name['en'] }}</h5>
      <span class="text-muted" style="font-weight:800;">
        <i class="fas fa-layer-group me-1"></i>{{ $catEn }}
      </span>
    </div>

    <div class="pcS-grid">
      <div class="pcS-box">
        <div class="pcS-k">Name (English)</div>
        <div class="pcS-v">{{ $placeOfTheCleaning->name['en'] }}</div>
      </div>

      <div class="pcS-box" dir="rtl">
        <div class="pcS-k">Name (Arabic)</div>
        <div class="pcS-v">{{ $placeOfTheCleaning->name['ar'] }}</div>
      </div>

      <div class="pcS-box">
        <div class="pcS-k">Service Category</div>
        <div class="pcS-v">{{ $catEn }}</div>
      </div>

      <div class="pcS-box">
        <div class="pcS-k">Price</div>
        <div class="pcS-v">{{ $priceText }}</div>
      </div>

      <div class="pcS-box">
        <div class="pcS-k">Created At</div>
        <div class="pcS-v">{{ $placeOfTheCleaning->created_at }}</div>
      </div>

      <div class="pcS-box">
        <div class="pcS-k">Updated At</div>
        <div class="pcS-v">{{ $placeOfTheCleaning->updated_at }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
