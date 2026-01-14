@extends('dashboard.admin.layouts.main')

@section('content')
<style>
/* ===== Place Cleaning Premium (Scoped) ===== */
.pc{
  --p:#0d6efd; --ink:#0f172a; --muted:#64748b;
  --b:rgba(15,23,42,.10);
  --sh:0 22px 60px rgba(2,6,23,.10);
  --sh2:0 10px 24px rgba(2,6,23,.06);
  --r:18px;
}
.pc-head{
  border:1px solid var(--b);
  border-radius: var(--r);
  padding:16px;
  background:
    radial-gradient(900px 220px at 10% 0%, rgba(13,110,253,.14), transparent 60%),
    radial-gradient(900px 240px at 90% 0%, rgba(16,185,129,.10), transparent 60%),
    linear-gradient(180deg, rgba(255,255,255,.96), #fff);
  box-shadow: var(--sh2);
  display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;
}
.pc-head h1{margin:0;font-weight:950;color:var(--p);}
.pc-head p{margin:6px 0 0;color:var(--muted);font-weight:650;}

.pc-chipTop{
  display:inline-flex; align-items:center; gap:8px;
  padding: 8px 12px; border-radius:999px;
  border:1px solid var(--b);
  background:#fff; font-weight:900; font-size:12px; color:var(--ink);
  box-shadow:0 6px 16px rgba(2,6,23,.04);
}

.pc-card{
  margin-top:14px;
  border:1px solid var(--b);
  border-radius: var(--r);
  background:#fff;
  box-shadow: var(--sh);
  overflow:hidden;
}
.pc-card-h{
  padding:14px 16px;
  border-bottom:1px solid var(--b);
  display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;
  background: linear-gradient(180deg, rgba(13,110,253,.06), rgba(255,255,255,0));
}
.pc-card-h h5{margin:0;font-weight:950;color:var(--ink);}

.pc-filter{
  border-radius: 14px;
  border:1px solid rgba(15,23,42,.10);
  padding:.65rem .85rem;
  font-weight:650;
}
.pc-filter:focus{
  border-color: rgba(13,110,253,.45);
  box-shadow: 0 0 0 6px rgba(13,110,253,.10);
  outline:none;
}

.pc-table thead th{
  font-weight:950;
  color:var(--ink);
  background: rgba(248,250,252,.92);
  border-bottom:1px solid rgba(15,23,42,.10) !important;
}

.pc-row:hover{ background: rgba(13,110,253,.04); transition:.15s ease; }
.pc-td-muted{ color:#94a3b8; font-weight:900; }

.pc-name{ display:flex; gap:10px; align-items:center; }
.pc-lang{
  width:34px; height:22px; border-radius:999px;
  display:inline-flex; align-items:center; justify-content:center;
  border:1px solid rgba(15,23,42,.10);
  background:#fff;
  font-size:10px; font-weight:950; color:#334155;
}
.pc-lang.pc-ar{ background: rgba(13,110,253,.08); border-color: rgba(13,110,253,.18); color:#0b5ed7; }
.pc-text{ font-weight:900; color:var(--ink); }

.pc-chip{
  display:inline-flex; align-items:center; gap:8px;
  padding: 7px 10px;
  border-radius: 12px;
  border:1px solid rgba(15,23,42,.10);
  background: rgba(15,23,42,.03);
  font-weight:900; color:#334155;
  max-width: 360px;
}
.pc-chip-text{ white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width: 290px; }

.pc-price{
  display:inline-flex; align-items:center; gap:8px;
  padding: 7px 10px;
  border-radius: 12px;
  border:1px solid rgba(16,185,129,.18);
  background: rgba(16,185,129,.10);
  font-weight:950; color:#065f46;
}
.pc-price.is-empty{
  border-color: rgba(100,116,139,.14);
  background: rgba(100,116,139,.06);
  color:#94a3b8;
}

.pc-actions{ display:inline-flex; gap:10px; align-items:center; }
.pc-icon{
  width: 38px; height: 38px;
  border-radius: 12px;
  display:inline-flex; align-items:center; justify-content:center;
  border:1px solid rgba(15,23,42,.10);
  background:#fff;
  transition:.15s ease;
  text-decoration:none;
  color:#0f172a;
}
.pc-icon:hover{ transform: translateY(-1px); box-shadow:0 10px 22px rgba(2,6,23,.08); }
.pc-view{ border-color: rgba(59,130,246,.25); background: rgba(59,130,246,.10); color:#2563eb; }
.pc-edit{ border-color: rgba(245,158,11,.25); background: rgba(245,158,11,.12); color:#b45309; }
.pc-del{ border-color: rgba(239,68,68,.22); background: rgba(239,68,68,.10); color:#b91c1c; }

.pc-foot{
  padding: 12px 16px;
  border-top:1px solid rgba(15,23,42,.08);
  background: rgba(15,23,42,.01);
  display:flex; justify-content:flex-end;
}
</style>

<div class="pc">
  <div class="pc-head fade-in">
    <div>
      <h1 class="fw-bold text-primary">Place of the Cleaning</h1>
      <p class="text-muted">Manage place of the cleaning records with multilingual names and associated service categories.</p>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <span class="pc-chipTop"><i class="fas fa-language"></i> EN / AR</span>

      <a href="{{ route('admin.place-of-the-cleaning.create') }}" class="btn btn-primary" style="border-radius:14px;font-weight:950;">
        <i class="fas fa-plus me-2"></i>Add New Place
      </a>
    </div>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
      <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="pc-card">
    <div class="pc-card-h">
      <h5 class="mb-0">Place of the Cleaning List</h5>

      <div class="d-flex align-items-center gap-2 flex-wrap">
        <label for="service_category_id" class="mb-0 fw-bold text-muted">Category</label>
        <select id="service_category_id" class="pc-filter">
          <option value="" {{ request('service_category_id') === null ? 'selected' : '' }}>All Categories</option>
          @foreach ($serviceCategories as $serviceCategory)
            <option value="{{ $serviceCategory->id }}" {{ request('service_category_id') == $serviceCategory->id ? 'selected' : '' }}>
              {{ $serviceCategory->name['en'] }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 pc-table">
          <thead>
            <tr>
              <th style="width:90px">#</th>
              <th>Name (English)</th>
              <th>Name (Arabic)</th>
              <th>Service Category</th>
              <th style="width:160px">Price</th>
              <th style="width:190px">Actions</th>
            </tr>
          </thead>

          <tbody id="items-table-body">
            @include('dashboard.admin.place-of-the-cleaning.partials.items-table', ['placeOfTheCleanings' => $placeOfTheCleanings])
          </tbody>
        </table>
      </div>

      <div class="pc-foot" id="pagination-links">
        {{ $placeOfTheCleanings->appends(['service_category_id' => request()->service_category_id])->links('vendor.pagination.bootstrap-5') }}
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const categorySelect = document.getElementById('service_category_id');
  const tableBody = document.getElementById('items-table-body');
  const paginationLinks = document.getElementById('pagination-links');

  function loadItems(serviceCategoryId = '', page = 1) {
    let url = '{{ route('admin.place-of-the-cleaning.index') }}';
    url += serviceCategoryId ? `?service_category_id=${serviceCategoryId}&page=${page}` : `?page=${page}`;

    fetch(url, {
      headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
      tableBody.innerHTML = data.table;
      paginationLinks.innerHTML = data.pagination;

      document.querySelectorAll('#pagination-links a').forEach(link => {
        link.addEventListener('click', function (e) {
          e.preventDefault();
          const u = new URL(this.href);
          const p = u.searchParams.get('page') || 1;
          loadItems(categorySelect.value, p);
        });
      });
    })
    .catch(() => {
      tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Error loading records.</td></tr>';
    });
  }

  categorySelect.addEventListener('change', function () {
    loadItems(this.value);
  });

  loadItems(categorySelect.value);
});
</script>
@endsection
