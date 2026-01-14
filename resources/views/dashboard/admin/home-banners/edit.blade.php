@extends('dashboard.admin.layouts.main')

@section('content')
<style>
/* reuse same hb styles (scoped) */
.hb{--p:#0d6efd;--ink:#0f172a;--muted:#64748b;--b:rgba(15,23,42,.10);--sh:0 22px 60px rgba(2,6,23,.10);--sh2:0 10px 24px rgba(2,6,23,.06);--r:18px;}
.hb-head{border:1px solid var(--b);border-radius:var(--r);padding:16px;background:radial-gradient(900px 220px at 10% 0%, rgba(13,110,253,.14), transparent 60%),radial-gradient(900px 240px at 90% 0%, rgba(16,185,129,.10), transparent 60%),linear-gradient(180deg, rgba(255,255,255,.96), #fff);box-shadow:var(--sh2);display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;}
.hb-head h1{margin:0;font-weight:950;color:var(--p);} .hb-head p{margin:6px 0 0;color:var(--muted);font-weight:650;}
.hb-btn{border-radius:14px;padding:10px 12px;font-weight:950;border:1px solid var(--b);background:#fff;color:var(--ink);display:inline-flex;gap:8px;align-items:center;transition:.15s;text-decoration:none;}
.hb-btn:hover{transform:translateY(-1px);box-shadow:0 10px 24px rgba(2,6,23,.08);}
.hb-card{margin-top:14px;border:1px solid var(--b);border-radius:var(--r);background:#fff;box-shadow:var(--sh);overflow:hidden;}
.hb-card-h{padding:14px 16px;border-bottom:1px solid var(--b);display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;background:linear-gradient(180deg, rgba(13,110,253,.06), rgba(255,255,255,0));}
.hb-card-h h5{margin:0;font-weight:950;color:var(--ink);}
.hb-section{border:1px solid rgba(15,23,42,.08);border-radius:16px;background:rgba(248,250,252,.85);padding:14px;margin-bottom:12px;}
.hb-section .t{font-weight:950;color:var(--ink);margin-bottom:10px;}
.form-label{font-weight:900;}
.form-control,.form-select{border-radius:14px;border:1px solid rgba(15,23,42,.10);padding:.6rem .85rem;font-weight:650;}
.form-control:focus,.form-select:focus{border-color:rgba(13,110,253,.45);box-shadow:0 0 0 6px rgba(13,110,253,.10);}
.hb-preview{display:flex;gap:12px;align-items:center;flex-wrap:wrap;}
.hb-thumb{width:220px;max-width:100%;height:120px;border-radius:16px;border:1px solid rgba(15,23,42,.10);background:#f1f5f9;overflow:hidden;display:grid;place-items:center;color:#94a3b8;font-weight:900;}
.hb-thumb img{width:100%;height:100%;object-fit:cover;}
.hb-actions{display:flex;gap:10px;flex-wrap:wrap;}
.hb-actions .btn{border-radius:14px;font-weight:950;padding:10px 12px;}
</style>

<div class="hb">
  <div class="hb-head fade-in">
    <div>
      <h1 class="fw-bold text-primary">Edit Banner</h1>
      <p class="text-muted">Update the selected banner.</p>
    </div>
    <a href="{{ route('admin.home-banners.index') }}" class="hb-btn">
      <i class="fas fa-arrow-left"></i> Back
    </a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
      <i class="fas fa-exclamation-circle me-2"></i>
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="hb-card">
    <div class="hb-card-h">
      <h5 class="mb-0">Edit Banner #{{ $banner->id }}</h5>
      <span class="text-muted" style="font-weight:700;">Replace image if needed</span>
    </div>

    <div class="card-body p-3 p-md-4">
      <form method="POST" action="{{ route('admin.home-banners.update', $banner) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="hb-section">
          <div class="t"><i class="fas fa-image me-2 text-primary"></i> Current / New Image</div>

          <div class="hb-preview">
            <div class="hb-thumb">
              <img
                id="hbEditImg"
                src="{{ url('storage/' . ltrim($banner->image, '/')) }}"
                alt="banner"
              >
            </div>

            <div style="min-width:260px; flex:1;">
              <label class="form-label">Replace Image</label>
              <input type="file" name="image" class="form-control" id="hbEditInput" accept="image/*">
              <small class="text-muted" style="font-weight:650;">Leave empty to keep current image.</small>
            </div>
          </div>
        </div>

        <div class="hb-section">
          <div class="t"><i class="fas fa-sliders-h me-2 text-primary"></i> Settings</div>

          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="1" {{ $banner->status ? 'selected' : '' }}>Active</option>
                <option value="0" {{ !$banner->status ? 'selected' : '' }}>Inactive</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Sort Order</label>
              <input type="number" name="sort_order" class="form-control" min="0" value="{{ $banner->sort_order }}">
              <small class="text-muted" style="font-weight:650;">Lower number appears first.</small>
            </div>
          </div>
        </div>

        <div class="hb-actions">
          <button class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Update
          </button>
          <a href="{{ route('admin.home-banners.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-xmark me-2"></i>Cancel
          </a>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
(function(){
  const input = document.getElementById('hbEditInput');
  const img = document.getElementById('hbEditImg');
  if(!input || !img) return;

  input.addEventListener('change', () => {
    const f = input.files && input.files[0];
    if(!f) return;
    img.src = URL.createObjectURL(f);
  });
})();
</script>
@endsection
