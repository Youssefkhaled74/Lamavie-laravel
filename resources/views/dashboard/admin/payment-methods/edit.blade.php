@extends('dashboard.admin.layouts.main')

@section('content')
<style>
.pmE{
  --p:#0d6efd; --ink:#0f172a; --muted:#64748b;
  --b:rgba(15,23,42,.10);
  --sh:0 22px 60px rgba(2,6,23,.10);
  --sh2:0 10px 24px rgba(2,6,23,.06);
  --r:18px;
}
.pmE-head{
  border:1px solid var(--b); border-radius:var(--r); padding:16px;
  background:radial-gradient(900px 220px at 10% 0%, rgba(13,110,253,.14), transparent 60%),
             radial-gradient(900px 240px at 90% 0%, rgba(16,185,129,.10), transparent 60%),
             linear-gradient(180deg, rgba(255,255,255,.96), #fff);
  box-shadow:var(--sh2);
  display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;
}
.pmE-head h1{margin:0;font-weight:950;color:var(--p);}
.pmE-head p{margin:6px 0 0;color:var(--muted);font-weight:650;}

.pmE-card{margin-top:14px;border:1px solid var(--b);border-radius:var(--r);background:#fff;box-shadow:var(--sh);overflow:hidden;}
.pmE-card-h{padding:14px 16px;border-bottom:1px solid var(--b);display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;background:linear-gradient(180deg, rgba(13,110,253,.06), rgba(255,255,255,0));}
.pmE-card-h h5{margin:0;font-weight:950;color:var(--ink);}

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

.form-label{font-weight:900;}
.form-control, .form-select{
  border-radius: 14px;
  border:1px solid rgba(15,23,42,.10);
  padding:.6rem .85rem;
  font-weight:650;
}
.form-control:focus, .form-select:focus{
  border-color: rgba(13,110,253,.45);
  box-shadow: 0 0 0 6px rgba(13,110,253,.10);
}
</style>

<div class="pmE">
  <div class="pmE-head fade-in">
    <div>
      <h1 class="fw-bold text-primary" data-en="Edit Payment Method" data-ar="تعديل طريقة الدفع">Edit Payment Method</h1>
      <p class="text-muted" data-en="Update the payment method details." data-ar="تحديث تفاصيل طريقة الدفع.">
        Update the payment method details.
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
      <div class="t" data-en="Editing is disabled" data-ar="التعديل معطل">Editing is disabled</div>
      <div class="s" data-en="You can view details and status, but changes are temporarily locked by the system."
           data-ar="يمكنك عرض التفاصيل والحالة، لكن التعديلات مقفولة مؤقتًا بواسطة النظام.">
        You can view details and status, but changes are temporarily locked by the system.
      </div>
    </div>
  </div>

  <div class="pmE-card">
    <div class="pmE-card-h">
      <h5 class="mb-0" data-en="Edit Payment Method Status" data-ar="تعديل حالة طريقة الدفع">Edit Payment Method Status</h5>
    </div>

    <div class="card-body p-4">
      <form action="{{ route('admin.payment-methods.update', $paymentMethod) }}" method="POST" id="payment-method-form">
        @csrf
        @method('PUT')

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold" data-en="Name (English)" data-ar="الاسم (بالإنجليزية)">Name (English)</label>
            <div class="form-control" style="background:#f8fafc; font-weight:900;" disabled>{{ $paymentMethod->name['en'] }}</div>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" data-en="Name (Arabic)" data-ar="الاسم (بالعربية)">Name (Arabic)</label>
            <div class="form-control" style="background:#f8fafc; font-weight:900;" dir="rtl" disabled>{{ $paymentMethod->name['ar'] }}</div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <label for="status" class="form-label fw-semibold" data-en="Status" data-ar="الحالة">Status</label>
            <select name="status" id="status" class="form-select" disabled>
              <option value="1" {{ old('status', $paymentMethod->status) == 1 ? 'selected' : '' }}>Active</option>
              <option value="0" {{ old('status', $paymentMethod->status) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>
        </div>

        <div class="d-flex gap-3 mt-4">
          <button type="button" class="btn btn-primary btn-lg" disabled style="border-radius:14px;font-weight:950;">
            <i class="fas fa-lock me-2"></i>Update Status
          </button>
          <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-outline-secondary btn-lg" style="border-radius:14px;font-weight:950;">
            <i class="fas fa-arrow-left me-2"></i>Cancel
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
