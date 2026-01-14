@extends('dashboard.admin.layouts.main')

@section('content')
<style>
/* ===== Admins List Premium (Scoped) ===== */
.adl{
  --p:#0d6efd; --ink:#0f172a; --muted:#64748b;
  --b:rgba(15,23,42,.10);
  --sh:0 22px 60px rgba(2,6,23,.10);
  --sh2:0 10px 24px rgba(2,6,23,.06);
  --r:18px;
}
.adl-head{
  border:1px solid var(--b);
  border-radius: var(--r);
  padding: 16px 16px;
  background:
    radial-gradient(900px 220px at 10% 0%, rgba(13,110,253,.14), transparent 60%),
    radial-gradient(900px 240px at 90% 0%, rgba(16,185,129,.10), transparent 60%),
    linear-gradient(180deg, rgba(255,255,255,.96), #fff);
  box-shadow: var(--sh2);
}
.adl-head h1{margin:0; font-weight:950; color:var(--p);}
.adl-head p{margin:6px 0 0; color:var(--muted); font-weight:650;}
.adl-actions{display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end; align-items:center;}

.adl-btn{
  border-radius: 14px;
  padding: 10px 12px;
  font-weight:950;
  border:1px solid var(--b);
  background:#fff;
  color:var(--ink);
  display:inline-flex; gap:8px; align-items:center;
  transition:.15s ease;
}
.adl-btn:hover{transform:translateY(-1px); box-shadow:0 10px 24px rgba(2,6,23,.08);}
.adl-btn.primary{
  border-color:rgba(13,110,253,.25);
  background:rgba(13,110,253,.10);
  color:var(--p);
}

.adl-card{
  margin-top:14px;
  border:1px solid var(--b);
  border-radius: var(--r);
  background:#fff;
  box-shadow: var(--sh);
  overflow:hidden;
}
.adl-card-h{
  padding: 14px 16px;
  border-bottom:1px solid var(--b);
  display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap; align-items:center;
  background: linear-gradient(180deg, rgba(13,110,253,.06), rgba(255,255,255,0));
}
.adl-card-h h5{margin:0; font-weight:950; color:var(--ink);}

.role-badge{
  font-size: 12px;
  font-weight: 950;
  padding: 6px 10px;
  border-radius: 999px;
  border:1px solid rgba(13,110,253,.22);
  background: rgba(13,110,253,.10);
  color: var(--p);
  display:inline-flex;
  gap:6px;
  align-items:center;
}

.adl-avatar{
  width:42px; height:42px; border-radius: 14px;
  border:1px solid rgba(15,23,42,.10);
  background:#f1f5f9;
  display:grid; place-items:center;
  overflow:hidden;
  flex:0 0 auto;
}
.adl-avatar img{width:100%; height:100%; object-fit:cover;}

.table thead th{font-weight:950; color:#0f172a;}
.table tbody td{vertical-align:middle;}

.adl-td-name{display:flex; align-items:center; gap:10px;}
.adl-name{font-weight:950; color:var(--ink); line-height:1.2;}
.adl-mail{color:var(--muted); font-weight:650; font-size:12px;}

.adl-actions-cell .btn{
  border-radius: 12px;
}

/* DataTables polish */
.dataTables_wrapper .dataTables_filter input{
  border-radius: 12px !important;
  border:1px solid var(--b) !important;
  padding: .55rem .8rem !important;
  outline:none;
}
.dataTables_wrapper .dataTables_length select{
  border-radius: 12px !important;
  border:1px solid var(--b) !important;
  padding: .35rem .55rem !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button{
  border:1px solid var(--b) !important;
  border-radius: 12px !important;
  background:#fff !important;
  padding: .45rem .8rem !important;
  margin: 0 .2rem !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current{
  background: rgba(13,110,253,.12) !important;
  border-color: rgba(13,110,253,.30) !important;
  color: var(--p) !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover{
  background: rgba(15,23,42,.03) !important;
  color: var(--ink) !important;
}

table.table-striped tbody tr:nth-of-type(odd){background-color: rgba(13,110,253,0.03) !important;}
table.table-hover tbody tr:hover{background-color: rgba(13,110,253,0.06) !important;}
</style>

<div class="adl">
  <div class="adl-head fade-in d-flex justify-content-between align-items-start gap-3 flex-wrap">
    <div>
      <h1 data-en="Admins" data-ar="المسؤولون">Admins</h1>
      <p class="text-muted">Manage administrators for the dashboard.</p>
    </div>
    <div class="adl-actions">
      <a href="{{ route('admin.admins.create') }}" class="adl-btn primary" data-en="Add New Admin" data-ar="إضافة مسؤول جديد">
        <i class="fas fa-plus"></i> Add New Admin
      </a>
    </div>
  </div>

  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
      <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
      <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="adl-card">
    <div class="adl-card-h">
      <h5 data-en="Admins List" data-ar="قائمة المسؤولين">Admins List</h5>
      <span class="text-muted" style="font-weight:700;">{{ $admins->count() }} admins</span>
    </div>

    <div class="card-body p-3 p-md-4">
      <div class="table-responsive">
        <table class="table table-hover table-striped align-middle" id="admins-table">
          <thead class="bg-light">
            <tr>
              <th style="width:70px">#</th>
              <th>Name</th>
              <th>Email</th>
              <th style="width:220px">Roles</th>
              <th style="width:140px">Status</th>
              <th style="width:210px">Last Login</th>
              <th class="text-nowrap" style="width:140px">Actions</th>
            </tr>
          </thead>
          <tbody>
          @forelse ($admins as $admin)
            <tr>
              <td>{{ $loop->iteration }}</td>

              <td>
                <div class="adl-td-name">
                  <div class="adl-avatar">
                    @if($admin->photo)
                      <img src="{{ asset('storage/' . $admin->photo) }}" alt="{{ $admin->name }}">
                    @else
                      <i class="fas fa-user text-muted"></i>
                    @endif
                  </div>
                  <div style="min-width:0;">
                    <div class="adl-name">{{ $admin->name }}</div>
                    <div class="adl-mail">{{ $admin->email }}</div>
                  </div>
                </div>
              </td>

              <td><small class="text-muted">{{ $admin->email }}</small></td>

              <td>
                @if($admin->getRoleNames()->isNotEmpty())
                  @foreach($admin->getRoleNames() as $role)
                    <span class="role-badge me-1"><i class="fas fa-shield-halved"></i>{{ $role }}</span>
                  @endforeach
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>

              <td>@include('dashboard.admin.partials.online_badge', ['admin' => $admin])</td>

              <td>
                {{ Cache::get('admin_last_seen:' . $admin->id)
                  ? \Carbon\Carbon::parse(Cache::get('admin_last_seen:' . $admin->id))->format('d M Y, H:i')
                  : ($admin->last_login_at ? $admin->last_login_at->format('d M Y, H:i') : 'Never') }}
              </td>

              <td class="adl-actions-cell">
                <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-sm btn-info me-1" title="Edit">
                  <i class="fas fa-edit"></i>
                </a>
                @if ($admin->id !== $currentAdmin->id)
                  <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Are you sure you want to delete this admin?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted py-4">No admins found.</td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.css">
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  $('#admins-table').DataTable({
    paging:true,
    lengthChange:true,
    searching:true,
    ordering:true,
    info:true,
    autoWidth:false,
    responsive:true,
    order:[[5,'desc']],
  });
});
</script>
@endsection
