@extends('dashboard.admin.layouts.main')

@section('content')
<style>
/* ===== Edit Admin Premium (Scoped) ===== */
.aed{
  --p:#0d6efd; --ink:#0f172a; --muted:#64748b;
  --b:rgba(15,23,42,.10);
  --sh:0 22px 60px rgba(2,6,23,.10);
  --sh2:0 10px 24px rgba(2,6,23,.06);
  --r:18px;
}

.aed-head{
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
.aed-head h1{margin:0; font-weight:950; color:var(--p);}
.aed-head p{margin:6px 0 0; color:var(--muted); font-weight:650;}

.aed-btn{
  border-radius: 14px;
  padding: 10px 12px;
  font-weight:950;
  border:1px solid var(--b);
  background:#fff;
  color:var(--ink);
  display:inline-flex; gap:8px; align-items:center;
  transition:.15s ease;
  text-decoration:none;
}
.aed-btn:hover{transform:translateY(-1px); box-shadow:0 10px 24px rgba(2,6,23,.08);}
.aed-btn.primary{
  border-color:rgba(13,110,253,.25);
  background:rgba(13,110,253,.10);
  color:var(--p);
}
.aed-card{
  margin-top:14px;
  border:1px solid var(--b);
  border-radius: var(--r);
  background:#fff;
  box-shadow: var(--sh);
  overflow:hidden;
}
.aed-card-h{
  padding: 14px 16px;
  border-bottom:1px solid var(--b);
  display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;
  background: linear-gradient(180deg, rgba(13,110,253,.06), rgba(255,255,255,0));
}
.aed-card-h h5{margin:0; font-weight:950; color:var(--ink);}

.aed-section{
  border:1px solid rgba(15,23,42,.08);
  border-radius: 16px;
  background: rgba(248,250,252,.85);
  padding: 14px;
  margin-bottom: 12px;
}
.aed-section .t{font-weight:950; color:var(--ink); margin-bottom:10px;}
.form-label{font-weight:900;}
.form-control{
  border-radius: 14px;
  border:1px solid rgba(15,23,42,.10);
  padding:.6rem .85rem;
  font-weight:650;
}
.form-control:focus{
  border-color: rgba(13,110,253,.45);
  box-shadow: 0 0 0 6px rgba(13,110,253,.10);
}

.aed-photo{
  display:flex; gap:12px; align-items:center; flex-wrap:wrap;
}
.aed-avatar{
  width:76px;height:76px;border-radius: 18px;
  border:1px solid rgba(15,23,42,.10);
  overflow:hidden;
  background:#f1f5f9;
  display:grid; place-items:center;
}
.aed-avatar img{width:100%; height:100%; object-fit:cover;}

.role-grid{
  display:grid;
  grid-template-columns: repeat(2, minmax(0,1fr));
  gap: 10px;
}
@media(max-width:520px){ .role-grid{grid-template-columns:1fr;} }

.role-pill{
  border:1px solid rgba(15,23,42,.10);
  border-radius: 14px;
  padding: 10px 12px;
  background:#fff;
  display:flex; gap:10px; align-items:center;
}
.role-pill input{ transform: scale(1.08); }
.role-pill label{ font-weight:900; color:var(--ink); }

.aed-actions{
  display:flex; gap:10px; flex-wrap:wrap;
}
</style>

<div class="aed">
  <div class="aed-head fade-in">
    <div>
      <h1 data-en="Edit Admin" data-ar="تعديل المسؤول">Edit Admin</h1>
      <p class="text-muted" data-en="Update administrator details." data-ar="تحديث تفاصيل المسؤول.">Update administrator details.</p>
    </div>
    <a href="{{ route('admin.admins.index') }}" class="aed-btn">
      <i class="fas fa-arrow-left"></i> Back to Admins
    </a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
      <i class="fas fa-exclamation-circle me-2"></i>
      <ul class="mb-0">
        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="aed-card">
    <div class="aed-card-h">
      <h5 data-en="Edit Admin" data-ar="تعديل المسؤول">Edit Admin: {{ $admin->name }}</h5>
      <span class="text-muted" style="font-weight:700;">ID: {{ $admin->id }}</span>
    </div>

    <div class="card-body p-3 p-md-4">
      <form action="{{ route('admin.admins.update', $admin) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="aed-section">
          <div class="t"><i class="fas fa-user-gear me-2 text-primary"></i> Basic Info</div>

          <div class="row g-3">
            <div class="col-md-6">
              <label for="name" class="form-label fw-semibold" data-en="Name" data-ar="الاسم">Name</label>
              <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $admin->name) }}" required>
            </div>
            <div class="col-md-6">
              <label for="email" class="form-label fw-semibold" data-en="Email" data-ar="البريد الإلكتروني">Email</label>
              <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $admin->email) }}" required>
            </div>
          </div>
        </div>

        <div class="aed-section">
          <div class="t"><i class="fas fa-lock me-2 text-primary"></i> Security</div>
          <div class="row g-3">
            <div class="col-md-6">
              <label for="password" class="form-label fw-semibold" data-en="Password (leave blank to keep unchanged)" data-ar="كلمة المرور (اتركها فارغة للحفاظ عليها)">
                Password (leave blank to keep unchanged)
              </label>
              <input type="password" name="password" id="password" class="form-control">
            </div>
            <div class="col-md-6">
              <label for="password_confirmation" class="form-label fw-semibold" data-en="Confirm Password" data-ar="تأكيد كلمة المرور">Confirm Password</label>
              <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
            </div>
          </div>
        </div>

        <div class="aed-section">
          <div class="t"><i class="fas fa-shield-halved me-2 text-primary"></i> Roles</div>

          <div class="role-grid">
            @foreach($roles as $role)
              <div class="role-pill">
                <input class="form-check-input m-0" type="checkbox" name="roles[]" value="{{ $role->name }}"
                       id="role_{{ $role->id }}" {{ in_array($role->name, $adminRoles ?? []) ? 'checked' : '' }}>
                <label class="m-0" for="role_{{ $role->id }}">{{ $role->name }}</label>
              </div>
            @endforeach
          </div>
        </div>

        <div class="aed-section">
          <div class="t"><i class="fas fa-image me-2 text-primary"></i> Photo</div>
          <div class="aed-photo">
            <div class="aed-avatar">
              @if($admin->photo)
                <img src="{{ asset('storage/' . $admin->photo) }}" alt="{{ $admin->name }}">
              @else
                <i class="fas fa-user text-muted"></i>
              @endif
            </div>
            <div style="min-width:260px; flex:1;">
              <input type="file" name="photo" accept="image/*" class="form-control">
              <small class="text-muted" style="font-weight:650;">
                Allowed: jpeg,png,gif,webp — Max 2MB
              </small>
            </div>
          </div>
        </div>

        <div class="aed-actions">
          <button type="submit" class="aed-btn primary">
            <i class="fas fa-save"></i> Update Admin
          </button>
          <a href="{{ route('admin.admins.index') }}" class="aed-btn">
            <i class="fas fa-xmark"></i> Cancel
          </a>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection
