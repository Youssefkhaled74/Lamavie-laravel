@extends('dashboard.admin.layouts.main')

@section('content')
<style>
/* ===== Home Banners Index Premium (Scoped) ===== */
.hbi{
  --p:#0d6efd; --ink:#0f172a; --muted:#64748b;
  --b:rgba(15,23,42,.10);
  --sh:0 22px 60px rgba(2,6,23,.10);
  --sh2:0 10px 24px rgba(2,6,23,.06);
  --r:18px;
}

.hbi-head{
  border:1px solid var(--b);
  border-radius: var(--r);
  padding: 16px 16px;
  background:
    radial-gradient(900px 220px at 10% 0%, rgba(13,110,253,.14), transparent 60%),
    radial-gradient(900px 240px at 90% 0%, rgba(16,185,129,.10), transparent 60%),
    linear-gradient(180deg, rgba(255,255,255,.96), #fff);
  box-shadow: var(--sh2);
  display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;
}
.hbi-head h1{margin:0; font-weight:950; color:var(--p);}
.hbi-head p{margin:6px 0 0; color:var(--muted); font-weight:650;}

.hbi-btn{
  border-radius: 14px;
  padding: 10px 12px;
  font-weight:950;
  border:1px solid rgba(13,110,253,.25);
  background: rgba(13,110,253,.10);
  color:var(--p);
  display:inline-flex; gap:8px; align-items:center;
  transition:.15s ease;
  text-decoration:none;
}
.hbi-btn:hover{transform:translateY(-1px); box-shadow:0 10px 24px rgba(2,6,23,.08);}

.hbi-card{
  margin-top:14px;
  border:1px solid var(--b);
  border-radius: var(--r);
  background:#fff;
  box-shadow: var(--sh);
  overflow:hidden;
}
.hbi-card-h{
  padding: 14px 16px;
  border-bottom:1px solid var(--b);
  display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;
  background: linear-gradient(180deg, rgba(13,110,253,.06), rgba(255,255,255,0));
}
.hbi-card-h h5{margin:0; font-weight:950; color:var(--ink);}

.hbi-table thead th{
  font-weight:950;
  color:var(--ink);
  background: rgba(248,250,252,.9);
  border-bottom:1px solid rgba(15,23,42,.10) !important;
}
.hbi-table tbody tr:hover{ background: rgba(13,110,253,.04); transition:.15s ease; }

.thumb{
  width: 120px; height: 60px;
  border-radius: 14px;
  border:1px solid rgba(15,23,42,.10);
  overflow:hidden;
  background:#f1f5f9;
}
.thumb img{ width:100%; height:100%; object-fit:cover; }

.badge-pill{
  border-radius:999px;
  padding: 7px 10px;
  font-weight:950;
  font-size: 12px;
  border:1px solid rgba(15,23,42,.10);
  background: rgba(15,23,42,.03);
  color:#334155;
}
.badge-pill.active{ background: rgba(16,185,129,.12); border-color: rgba(16,185,129,.25); color:#065f46; }
.badge-pill.inactive{ background: rgba(100,116,139,.10); border-color: rgba(100,116,139,.20); color:#475569; }

.act{
  display:inline-flex; gap:8px; align-items:center;
}
.icon-btn{
  width: 38px; height: 38px;
  border-radius: 12px;
  display:inline-flex; align-items:center; justify-content:center;
  border:1px solid rgba(15,23,42,.10);
  background:#fff;
  transition:.15s ease;
}
.icon-btn:hover{ transform: translateY(-1px); box-shadow:0 10px 22px rgba(2,6,23,.08); }
.icon-btn.edit{ border-color: rgba(245,158,11,.30); background: rgba(245,158,11,.10); color:#b45309; }
.icon-btn.del{ border-color: rgba(239,68,68,.28); background: rgba(239,68,68,.10); color:#dc2626; }

.hbi-foot{padding: 12px 16px; border-top:1px solid var(--b); background: rgba(15,23,42,.01); display:flex; justify-content:flex-end;}
</style>

<div class="hbi">
  <div class="hbi-head fade-in">
    <div>
      <h1 class="fw-bold text-primary">Home Banners</h1>
      <p class="text-muted">Manage homepage banner images.</p>
    </div>
    <a href="{{ route('admin.home-banners.create') }}" class="hbi-btn">
      <i class="fas fa-plus"></i> Add Banner
    </a>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
      <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="hbi-card">
    <div class="hbi-card-h">
      <h5 class="mb-0">Banners</h5>
      <span class="text-muted" style="font-weight:700;">Tip: Sort order controls display priority</span>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 hbi-table">
          <thead>
            <tr>
              <th style="width:80px">#</th>
              <th style="width:160px">Image</th>
              <th>Status</th>
              <th style="width:120px">Sort</th>
              <th style="width:140px">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($banners as $banner)
              <tr>
                <td class="fw-bold">#{{ $banner->id }}</td>
                <td>
                  <div class="thumb">
                    <img src="{{ url('storage/' . ltrim($banner->image, '/')) }}" alt="banner">
                  </div>
                </td>
                <td>
                  @if($banner->status)
                    <span class="badge-pill active"><i class="fas fa-circle-check me-1"></i> Active</span>
                  @else
                    <span class="badge-pill inactive"><i class="fas fa-circle-minus me-1"></i> Inactive</span>
                  @endif
                </td>
                <td class="fw-bold">{{ $banner->sort_order }}</td>
                <td>
                  <div class="act">
                    <a href="{{ route('admin.home-banners.edit', $banner) }}" class="icon-btn edit" title="Edit">
                      <i class="fas fa-pen"></i>
                    </a>

                    <form action="{{ route('admin.home-banners.destroy', $banner) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete banner?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="icon-btn del" title="Delete">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4" style="font-weight:800;">
                  No banners yet.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="hbi-foot">
        {{ $banners->links('vendor.pagination.bootstrap-5') }}
      </div>
    </div>
  </div>
</div>
@endsection
