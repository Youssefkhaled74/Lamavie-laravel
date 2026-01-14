@extends('dashboard.admin.layouts.main')

@section('content')
<style>
.pmC{
  --p:#0d6efd; --ink:#0f172a; --muted:#64748b;
  --b:rgba(15,23,42,.10);
  --sh:0 22px 60px rgba(2,6,23,.10);
  --sh2:0 10px 24px rgba(2,6,23,.06);
  --r:18px;
}
.pmC-head{
  border:1px solid var(--b); border-radius:var(--r); padding:16px;
  background:radial-gradient(900px 220px at 10% 0%, rgba(13,110,253,.14), transparent 60%),
             radial-gradient(900px 240px at 90% 0%, rgba(16,185,129,.10), transparent 60%),
             linear-gradient(180deg, rgba(255,255,255,.96), #fff);
  box-shadow:var(--sh2);
  display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;
}
.pmC-head h1{margin:0;font-weight:950;color:var(--p);}
.pmC-head p{margin:6px 0 0;color:var(--muted);font-weight:650;}

.lock{
  margin-top:14px;
  border-radius: 18px;
  border:1px solid rgba(245,158,11,.25);
  background: rgba(245,158,11,.10);
  padding: 14px 16px;
  display:flex; align-items:flex-start; gap:12px;
}
.lock .ic{
  width:44px;height:44px;border-radius:14px;
  display:grid;place-items:center;
  background: rgba(245,158,11,.15);
  border:1px solid rgba(245,158,11,.25);
  color:#b45309;
  flex:0 0 auto;
}
.lock .t{font-weight:950;color:#7c2d12;margin-bottom:2px;}
.lock .s{color:#92400e;font-weight:650;font-size:13px;}

.pmC-card{margin-top:14px;border:1px solid var(--b);border-radius:var(--r);background:#fff;box-shadow:var(--sh);overflow:hidden;}
.pmC-card-h{padding:14px 16px;border-bottom:1px solid var(--b);display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;background:linear-gradient(180deg, rgba(13,110,253,.06), rgba(255,255,255,0));}
.pmC-card-h h5{margin:0;font-weight:950;color:var(--ink);}
</style>

<div class="pmC">
  <div class="pmC-head fade-in">
    <div>
      <h1 class="fw-bold text-primary" data-en="Add New Payment Method" data-ar="إضافة طريقة دفع جديدة">Add New Payment Method</h1>
      <p class="text-muted"
         data-en="Create a new payment method with multilingual names and status."
         data-ar="إنشاء طريقة دفع جديدة بأسماء متعددة اللغات والحالة.">
        Create a new payment method with multilingual names and status.
      </p>
    </div>
    <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-outline-secondary" style="border-radius:14px;font-weight:950;">
      <i class="fas fa-arrow-left me-2"></i>Back
    </a>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
      <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="lock">
    <div class="ic"><i class="fas fa-lock"></i></div>
    <div>
      <div class="t" data-en="Creation is disabled" data-ar="الإضافة معطلة">Creation is disabled</div>
      <div class="s" data-en="Adding new payment methods is temporarily locked by the system."
           data-ar="إضافة طرق الدفع مقفولة مؤقتًا بواسطة النظام.">
        Adding new payment methods is temporarily locked by the system.
      </div>
    </div>
  </div>

  <div class="pmC-card">
    <div class="pmC-card-h">
      <h5 class="mb-0">Create Payment Method</h5>
      <span class="text-muted" style="font-weight:700;">Locked</span>
    </div>

    <div class="card-body p-4">
      <div class="text-muted" style="font-weight:750;">
        This feature is disabled right now. If you want, I can style the enabled form too when you re-open it.
      </div>
    </div>
  </div>
</div>
@endsection
