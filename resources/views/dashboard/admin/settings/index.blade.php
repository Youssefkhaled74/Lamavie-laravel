@extends('dashboard.admin.layouts.main')

@section('content')
<style>
/* ===== Settings Premium (Scoped) ===== */
.st{
  --p:#0d6efd; --ink:#0f172a; --muted:#64748b;
  --b:rgba(15,23,42,.10);
  --sh:0 22px 60px rgba(2,6,23,.10);
  --sh2:0 10px 24px rgba(2,6,23,.06);
  --r:18px;
}

.st-head{
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
.st-head h1{margin:0; font-weight:950; color:var(--p);}
.st-head p{margin:6px 0 0; color:var(--muted); font-weight:650;}

.st-chip{
  display:inline-flex; align-items:center; gap:8px;
  padding: 8px 12px; border-radius:999px;
  border:1px solid var(--b);
  background:#fff; font-weight:900; font-size:12px; color:var(--ink);
  box-shadow:0 6px 16px rgba(2,6,23,.04);
}

.st-lock{
  margin-top:12px;
  border-radius: 18px;
  border:1px solid rgba(245,158,11,.25);
  background: rgba(245,158,11,.10);
  padding: 12px 14px;
  display:flex; align-items:flex-start; gap:12px;
}
.st-lock .ic{
  width:40px;height:40px;border-radius:14px;
  display:grid;place-items:center;
  background: rgba(245,158,11,.15);
  border:1px solid rgba(245,158,11,.25);
  color:#b45309;
  flex:0 0 auto;
}
.st-lock .t{font-weight:950;color:#7c2d12;margin-bottom:2px;}
.st-lock .s{color:#92400e;font-weight:650;font-size:13px;}

.st-card{
  margin-top:14px;
  border:1px solid var(--b);
  border-radius: var(--r);
  background:#fff;
  box-shadow: var(--sh);
  overflow:hidden;
}
.st-card-h{
  padding: 14px 16px;
  border-bottom:1px solid var(--b);
  display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;
  background: linear-gradient(180deg, rgba(13,110,253,.06), rgba(255,255,255,0));
}
.st-card-h h5{margin:0; font-weight:950; color:var(--ink);}

.st-btn{
  border-radius:14px;
  font-weight:950;
  padding: 9px 12px;
  display:inline-flex; align-items:center; gap:8px;
}
.st-btn.disabled{
  opacity:.6; cursor:not-allowed;
}

.st-table thead th{
  font-weight:950;
  color:var(--ink);
  background: rgba(248,250,252,.92);
  border-bottom:1px solid rgba(15,23,42,.10) !important;
}
.st-row:hover{ background: rgba(13,110,253,.04); transition:.15s ease; }
.st-td-muted{ color:#94a3b8; font-weight:900; }

.st-name{ display:flex; gap:10px; align-items:center; }
.st-lang-pill{
  width:34px; height:22px; border-radius:999px;
  display:inline-flex; align-items:center; justify-content:center;
  border:1px solid rgba(15,23,42,.10);
  background:#fff;
  font-size:10px; font-weight:950; color:#334155;
}
.st-lang-pill.st-ar{ background: rgba(13,110,253,.08); border-color: rgba(13,110,253,.18); color:#0b5ed7; }
.st-text{ font-weight:900; color:var(--ink); }

.st-key{
  display:inline-flex; gap:8px; align-items:center;
  padding: 7px 10px; border-radius: 12px;
  border:1px solid rgba(15,23,42,.10);
  background: rgba(15,23,42,.03);
  font-weight:900; color:#334155;
  max-width: 360px;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}

.st-value{
  display:inline-block;
  padding: 7px 10px;
  border-radius: 12px;
  border:1px solid rgba(15,23,42,.10);
  background: rgba(248,250,252,.9);
  font-weight:800;
  color:#0f172a;
  max-width: 420px;
  white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.st-value.is-empty{ color:#94a3b8; font-weight:900; background: rgba(100,116,139,.06); }

.st-actions{ display:inline-flex; gap:10px; align-items:center; }
.st-icon-btn{
  width: 38px; height: 38px;
  border-radius: 12px;
  display:inline-flex; align-items:center; justify-content:center;
  border:1px solid rgba(15,23,42,.10);
  background:#fff;
  transition:.15s ease;
  text-decoration:none;
  color:#0f172a;
}
.st-icon-btn:hover{ transform: translateY(-1px); box-shadow:0 10px 22px rgba(2,6,23,.08); }
.st-view{ border-color: rgba(59,130,246,.25); background: rgba(59,130,246,.10); color:#2563eb; }
.st-edit{ border-color: rgba(245,158,11,.25); background: rgba(245,158,11,.12); color:#b45309; }
.st-del{ border-color: rgba(239,68,68,.22); background: rgba(239,68,68,.10); color:#b91c1c; }

.st-disabled{ pointer-events:none; }
.st-icon-btn.st-disabled,
.st-icon-btn:disabled{
  opacity:.55; cursor:not-allowed; transform:none;
  box-shadow:none;
}

.st-foot{
  padding: 12px 16px;
  border-top:1px solid rgba(15,23,42,.08);
  background: rgba(15,23,42,.01);
  display:flex; justify-content:flex-end;
}
</style>

@php
  $restrict = (int) env('RESTRICT_SETTINGS', 1); // 0 restricted
  $canManage = $restrict !== 0;
@endphp

<div class="st">
  <div class="st-head fade-in">
    <div>
      <h1 class="fw-bold text-primary" data-en="Settings" data-ar="الإعدادات">Settings</h1>
      <p class="text-muted" data-en="Manage application settings with key-value pairs."
         data-ar="إدارة إعدادات التطبيق بأزواج مفتاح-قيمة.">
        Manage application settings with key-value pairs.
      </p>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <span class="st-chip"><i class="fas fa-language"></i> EN / AR</span>

      <a href="{{ route('admin.settings.create') }}"
         class="btn btn-primary st-btn {{ !$canManage ? 'disabled restricted-action' : '' }}"
         data-restrict="{{ $restrict }}"
         @if(!$canManage) aria-disabled="true" @endif>
        <i class="fas fa-plus"></i> Add New Setting
      </a>
    </div>
  </div>

  @if(!$canManage)
    <div class="st-lock">
      <div class="ic"><i class="fas fa-lock"></i></div>
      <div>
        <div class="t" data-en="Settings management is restricted" data-ar="إدارة الإعدادات مقيدة">Settings management is restricted</div>
        <div class="s" data-en="Create/Edit/Delete are disabled. Contact the developer to manage this module."
             data-ar="الإضافة/التعديل/الحذف معطلة. تواصل مع المطور لإدارة هذه الوحدة.">
          Create/Edit/Delete are disabled. Contact the developer to manage this module.
        </div>
      </div>
    </div>
  @endif

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
      <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="st-card">
    <div class="st-card-h">
      <h5 class="mb-0" data-en="Settings List" data-ar="قائمة الإعدادات">Settings List</h5>
      <span class="text-muted" style="font-weight:750;">Tip: Pagination updates without full refresh</span>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 st-table">
          <thead>
            <tr>
              <th style="width:90px">#</th>
              <th data-en="Name (English)" data-ar="الاسم (بالإنجليزية)">Name (English)</th>
              <th data-en="Name (Arabic)" data-ar="الاسم (بالعربية)">Name (Arabic)</th>
              <th style="width:260px" data-en="Key" data-ar="المفتاح">Key</th>
              <th style="width:320px" data-en="Value" data-ar="القيمة">Value</th>
              <th style="width:180px" data-en="Actions" data-ar="الإجراءات">Actions</th>
            </tr>
          </thead>
          <tbody id="items-table-body">
            @include('dashboard.admin.settings.partials.items-table', ['settings' => $settings])
          </tbody>
        </table>
      </div>

      <div class="st-foot" id="pagination-links">
        {{ $settings->links('vendor.pagination.bootstrap-5') }}
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const restrict = {{ (int) env('RESTRICT_SETTINGS', 1) }};

  function attachRestrictedListeners() {
    document.querySelectorAll('.restricted-action, .st-disabled, [data-restrict="0"]').forEach(el => {
      el.addEventListener('click', function (e) {
        if (String(this.getAttribute('data-restrict')) === '0') {
          e.preventDefault();
          if (typeof showCustomAlert === 'function') {
            showCustomAlert('Connect with the developer to manage this.');
          } else {
            alert('Connect with the developer to manage this.');
          }
        }
      });
    });
  }

  const tableBody = document.getElementById('items-table-body');
  const paginationLinks = document.getElementById('pagination-links');

  function loadItems(page = 1) {
    let url = '{{ route('admin.settings.index') }}?page=' + page;

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
            loadItems(p);
          });
        });

        attachRestrictedListeners();
      })
      .catch(() => {
        tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Error loading settings.</td></tr>';
      });
  }

  // first load
  loadItems();
  attachRestrictedListeners();
});
</script>
@endsection
