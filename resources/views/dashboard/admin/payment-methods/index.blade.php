@extends('dashboard.admin.layouts.main')

@section('content')
<style>
/* ===== Payment Methods Premium (Scoped) ===== */
.pm{
  --p:#0d6efd; --ink:#0f172a; --muted:#64748b;
  --b:rgba(15,23,42,.10);
  --sh:0 22px 60px rgba(2,6,23,.10);
  --sh2:0 10px 24px rgba(2,6,23,.06);
  --r:18px;
}

.pm-head{
  border:1px solid var(--b);
  border-radius: var(--r);
  padding: 16px;
  background:
    radial-gradient(900px 220px at 10% 0%, rgba(13,110,253,.14), transparent 60%),
    radial-gradient(900px 240px at 90% 0%, rgba(16,185,129,.10), transparent 60%),
    linear-gradient(180deg, rgba(255,255,255,.96), #fff);
  box-shadow: var(--sh2);
  display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;
}
.pm-head h1{margin:0; font-weight:950; color:var(--p);}
.pm-head p{margin:6px 0 0; color:var(--muted); font-weight:650;}

.pm-chip{
  display:inline-flex; align-items:center; gap:8px;
  padding: 8px 12px; border-radius:999px;
  border:1px solid var(--b);
  background:#fff; font-weight:900; font-size:12px; color:var(--ink);
  box-shadow:0 6px 16px rgba(2,6,23,.04);
}

.pm-card{
  margin-top:14px;
  border:1px solid var(--b);
  border-radius: var(--r);
  background:#fff;
  box-shadow: var(--sh);
  overflow:hidden;
}
.pm-card-h{
  padding: 14px 16px;
  border-bottom:1px solid var(--b);
  display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;
  background: linear-gradient(180deg, rgba(13,110,253,.06), rgba(255,255,255,0));
}
.pm-card-h h5{margin:0; font-weight:950; color:var(--ink);}

.pm-filter{
  display:flex; gap:10px; align-items:center; flex-wrap:wrap;
  padding: 14px 16px;
  border-bottom:1px solid rgba(15,23,42,.08);
  background: rgba(248,250,252,.6);
}
.pm-filter label{font-weight:900; color:var(--ink); margin:0;}
.pm-filter .form-select{
  border-radius: 14px;
  border:1px solid rgba(15,23,42,.10);
  padding: .55rem .85rem;
  font-weight:650;
  min-width: 220px;
}
.pm-filter .form-select:focus{
  border-color: rgba(13,110,253,.45);
  box-shadow: 0 0 0 6px rgba(13,110,253,.10);
}

/* Table */
.pm-table thead th{
  font-weight:950;
  color:var(--ink);
  background: rgba(248,250,252,.92);
  border-bottom:1px solid rgba(15,23,42,.10) !important;
}
.pm-row:hover{ background: rgba(13,110,253,.04); transition:.15s ease; }

.pm-td-muted{ color:#94a3b8; font-weight:900; }

.pm-name{
  display:flex; gap:10px; align-items:center;
}
.pm-lang-pill{
  width:34px; height:22px; border-radius:999px;
  display:inline-flex; align-items:center; justify-content:center;
  border:1px solid rgba(15,23,42,.10);
  background:#fff;
  font-size:10px; font-weight:950; color:#334155;
}
.pm-lang-pill.pm-ar{ background: rgba(13,110,253,.08); border-color: rgba(13,110,253,.18); color:#0b5ed7; }
.pm-text{ font-weight:900; color:var(--ink); }

/* Status badge */
.pm-badge{
  display:inline-flex; align-items:center; gap:8px;
  padding: 7px 10px; border-radius:999px;
  font-weight:950; font-size:12px;
  border:1px solid rgba(15,23,42,.10);
  background: rgba(15,23,42,.03);
  color:#334155;
}
.pm-badge.pm-active{ background: rgba(16,185,129,.12); border-color: rgba(16,185,129,.22); color:#065f46; }
.pm-badge.pm-inactive{ background: rgba(100,116,139,.10); border-color: rgba(100,116,139,.18); color:#475569; }

/* Actions */
.pm-actions{ display:inline-flex; gap:10px; align-items:center; }
.pm-icon-btn{
  width: 38px; height: 38px;
  border-radius: 12px;
  display:inline-flex; align-items:center; justify-content:center;
  border:1px solid rgba(15,23,42,.10);
  background:#fff;
  transition:.15s ease;
  text-decoration:none;
}
.pm-icon-btn:hover{ transform: translateY(-1px); box-shadow:0 10px 22px rgba(2,6,23,.08); }
.pm-view{ border-color: rgba(59,130,246,.25); background: rgba(59,130,246,.10); color:#2563eb; }

/* Switch button */
.pm-switch{
  border: 0;
  background: transparent;
  display:inline-flex;
  align-items:center;
  gap:10px;
  font-weight:950;
  color: var(--muted);
}
.pm-switch-track{
  width: 48px; height: 26px;
  border-radius: 999px;
  border:1px solid rgba(15,23,42,.12);
  background: rgba(100,116,139,.14);
  display:flex; align-items:center;
  padding: 3px;
  transition:.15s ease;
}
.pm-switch-thumb{
  width: 20px; height: 20px;
  border-radius: 999px;
  background:#fff;
  box-shadow: 0 8px 18px rgba(2,6,23,.12);
  transform: translateX(0);
  transition:.15s ease;
}
.pm-switch.is-on .pm-switch-track{
  background: rgba(16,185,129,.20);
  border-color: rgba(16,185,129,.30);
}
.pm-switch.is-on .pm-switch-thumb{ transform: translateX(22px); }
.pm-switch-label{ font-size: 12px; color:#334155; }

.pm-foot{
  padding: 12px 16px;
  border-top:1px solid rgba(15,23,42,.08);
  background: rgba(15,23,42,.01);
  display:flex; justify-content:flex-end;
}

@media (max-width: 768px){
  .pm-actions{ flex-wrap:wrap; gap:8px; }
  .pm-filter{ justify-content:flex-start; }
}
</style>

<div class="pm">
  <div class="pm-head fade-in">
    <div>
      <h1 class="fw-bold text-primary" data-en="Payment Methods" data-ar="طرق الدفع">Payment Methods</h1>
      <p class="text-muted" data-en="Manage payment methods with multilingual names and status."
         data-ar="إدارة طرق الدفع بأسماء متعددة اللغات والحالة.">
        Manage payment methods with multilingual names and status.
      </p>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <span class="pm-chip"><i class="fas fa-language"></i> EN / AR</span>
      {{-- create disabled in your code, so we keep it hidden or disabled --}}
      {{-- <a href="{{ route('admin.payment-methods.create') }}" class="btn btn-primary btn-sm">Add</a> --}}
    </div>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
      <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="pm-card">
    <div class="pm-card-h">
      <h5 class="card-title mb-0" data-en="Payment Methods List" data-ar="قائمة طرق الدفع">Payment Methods List</h5>
      <span class="text-muted" style="font-weight:700;">Tip: Use filter + pagination</span>
    </div>

    <div class="pm-filter">
      <label for="status" class="form-label" data-en="Filter by Status" data-ar="تصفية حسب الحالة">Filter by Status</label>
      <select id="status" class="form-select">
        <option value="" {{ request('status') === null ? 'selected' : '' }} data-en="All Statuses" data-ar="جميع الحالات">All Statuses</option>
        <option value="1" {{ request('status') === '1' ? 'selected' : '' }} data-en="Active" data-ar="نشط">Active</option>
        <option value="0" {{ request('status') === '0' ? 'selected' : '' }} data-en="Inactive" data-ar="غير نشط">Inactive</option>
      </select>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 pm-table">
          <thead>
            <tr>
              <th style="width:90px">#</th>
              <th data-en="Name (English)" data-ar="الاسم (بالإنجليزية)">Name (English)</th>
              <th data-en="Name (Arabic)" data-ar="الاسم (بالعربية)">Name (Arabic)</th>
              <th style="width:160px" data-en="Status" data-ar="الحالة">Status</th>
              <th style="width:200px" data-en="Actions" data-ar="الإجراءات">Actions</th>
            </tr>
          </thead>
          <tbody id="items-table-body">
            @if (isset($paymentMethods))
              @include('dashboard.admin.payment-methods.partials.items-table', ['paymentMethods' => $paymentMethods])
            @else
              <tr>
                <td colspan="5" class="text-center text-muted py-4"
                    data-en="No data available. Please try again."
                    data-ar="لا توجد بيانات. حاول مرة أخرى.">
                  No data available. Please try again.
                </td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>

      <div class="pm-foot" id="pagination-links">
        {{ isset($paymentMethods) ? $paymentMethods->appends(['status' => request()->status])->links('vendor.pagination.bootstrap-5') : '' }}
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const statusSelect = document.getElementById('status');
  const tableBody = document.getElementById('items-table-body');
  const paginationLinks = document.getElementById('pagination-links');

  function loadItems(status = '', page = 1) {
    let url = '{{ route('admin.payment-methods.index') }}';
    url += status !== '' ? `?status=${status}&page=${page}` : `?page=${page}`;

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
      .then(r => r.json())
      .then(data => {
        tableBody.innerHTML = data.table;
        paginationLinks.innerHTML = data.pagination;

        document.querySelectorAll('#pagination-links a').forEach(link => {
          link.addEventListener('click', function (e) {
            e.preventDefault();
            const u = new URL(this.href);
            const p = u.searchParams.get('page') || 1;
            loadItems(statusSelect.value, p);
          });
        });
      })
      .catch(() => {
        tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Error loading payment methods.</td></tr>';
      });
  }

  statusSelect.addEventListener('change', function () { loadItems(this.value); });
  loadItems(statusSelect.value);
});
</script>
@endsection
