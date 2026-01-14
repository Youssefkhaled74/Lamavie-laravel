@extends('dashboard.admin.layouts.main')

@section('content')
<style>
    .drivers-card{
        border-radius:16px;
        box-shadow:0 12px 30px rgba(2,6,23,.08);
        background:#fff;
        border:1px solid rgba(15,23,42,.06);
        overflow:hidden;
    }
    .drivers-head{
        background: linear-gradient(90deg, #0d6efd 0%, #6ea8fe 100%);
        padding:16px 18px;
        color:#fff;
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        flex-wrap:wrap;
    }
    .drivers-head h5{ margin:0; font-weight:900; letter-spacing:-.02em; }
    .drivers-tools{
        display:flex;
        gap:10px;
        align-items:center;
        flex-wrap:wrap;
    }
    .drivers-tools .form-control{
        border-radius:12px;
        border:1px solid rgba(255,255,255,.35);
        background: rgba(255,255,255,.95);
        min-width:280px;
    }
    .drivers-tools .btn{
        border-radius:12px;
        font-weight:900;
        padding:10px 14px;
    }
    .btn-primary{
        border:none;
        background: linear-gradient(90deg,#2563eb,#3b82f6);
        box-shadow:0 10px 24px rgba(37,99,235,.18);
    }
    .drivers-table th{
        font-size:.82rem;
        text-transform:uppercase;
        letter-spacing:.06em;
        color:#64748b;
        background: rgba(2,6,23,.03);
    }
    .drivers-table td{ vertical-align:middle; font-weight:600; color:#334155; }
    .action-btn{
        border-radius:12px;
        font-weight:900;
        padding:7px 10px;
        box-shadow:0 6px 18px rgba(2,6,23,.10);
    }
    .btn-grad-danger{
        background: linear-gradient(90deg,#ef4444,#f97316);
        border:none;
        color:#fff;
    }
</style>

<div class="content-header mb-3">
    <h1 class="fw-bold text-primary" data-en="Drivers" data-ar="السائقون">Drivers</h1>
    <p class="text-muted mb-0" data-en="Manage drivers accounts." data-ar="إدارة حسابات السائقين.">Manage drivers accounts.</p>
</div>

<div class="drivers-card p-0">

    <div class="drivers-head">
        <div>
            <h5 data-en="Drivers List" data-ar="قائمة السائقين">Drivers List</h5>
            <div class="small" style="opacity:.9" data-en="Search, view, edit, and delete drivers." data-ar="ابحث واعرض وعدّل واحذف السائقين.">Search, view, edit, and delete drivers.</div>
        </div>

        <div class="drivers-tools">
            <form method="GET" action="{{ route('admin.drivers.index') }}" class="d-flex gap-2 align-items-center">
                <input type="text" class="form-control" name="q"
                       placeholder="Search by name or email..." value="{{ request('q') }}"
                       data-en="Search by name or email..." data-ar="ابحث بالاسم أو البريد...">
                <button class="btn btn-light action-btn" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <a href="{{ route('admin.drivers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                <span data-en="Add Driver" data-ar="إضافة سائق">Add Driver</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success m-3 mb-0">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover mb-0 drivers-table align-middle">
            <thead>
                <tr>
                    <th class="px-4 py-3" data-en="#" data-ar="#">#</th>
                    <th class="px-4 py-3" data-en="Name" data-ar="الاسم">Name</th>
                    <th class="px-4 py-3" data-en="Email" data-ar="البريد الإلكتروني">Email</th>
                    <th class="px-4 py-3 text-end" data-en="Actions" data-ar="الإجراءات">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($drivers as $driver)
                    <tr>
                        <td class="px-4 py-3">{{ $driver->id }}</td>
                        <td class="px-4 py-3">{{ $driver->name }}</td>
                        <td class="px-4 py-3 text-muted small">{{ $driver->email }}</td>
                        <td class="px-4 py-3 text-end">
                            <div class="d-flex gap-2 justify-content-end flex-wrap">
                                <a href="{{ route('admin.drivers.show', $driver) }}" class="btn btn-sm btn-outline-primary action-btn">
                                    <i class="fas fa-eye"></i>
                                    <span class="ms-1" data-en="View" data-ar="عرض">View</span>
                                </a>

                                <a href="{{ route('admin.drivers.edit', $driver) }}" class="btn btn-sm btn-primary action-btn">
                                    <i class="fas fa-pen"></i>
                                    <span class="ms-1" data-en="Edit" data-ar="تعديل">Edit</span>
                                </a>

                                <button type="button"
                                        class="btn btn-sm btn-grad-danger action-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteDriverModal"
                                        data-driver-id="{{ $driver->id }}"
                                        data-driver-name="{{ $driver->name }}">
                                    <i class="fas fa-trash"></i>
                                    <span class="ms-1" data-en="Delete" data-ar="حذف">Delete</span>
                                </button>

                                <form id="delete-driver-form-{{ $driver->id }}"
                                      action="{{ route('admin.drivers.destroy', $driver) }}"
                                      method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4" data-en="No drivers found." data-ar="لم يتم العثور على سائقين.">No drivers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3">
        {{ $drivers->withQueryString()->links('vendor.pagination.bootstrap-5') }}
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteDriverModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px; overflow:hidden;">
            <div class="modal-header text-white" style="background:linear-gradient(90deg,#ef4444,#f97316);">
                <h5 class="modal-title">
                    <i class="fas fa-triangle-exclamation me-2"></i>
                    <span data-en="Delete Driver" data-ar="حذف السائق">Delete Driver</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="fw-bold mb-1" data-en="Are you sure you want to delete this driver?" data-ar="هل أنت متأكد أنك تريد حذف هذا السائق؟">
                    Are you sure you want to delete this driver?
                </p>
                <p class="text-muted mb-0">
                    <span data-en="Driver name:" data-ar="اسم السائق:">Driver name:</span>
                    <strong id="deleteDriverName"></strong>
                </p>
                <small class="text-danger d-block mt-2" data-en="This action cannot be undone." data-ar="لا يمكن التراجع عن هذا الإجراء.">
                    This action cannot be undone.
                </small>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal" data-en="Cancel" data-ar="إلغاء">Cancel</button>
                <button class="btn btn-danger" id="confirmDeleteDriverBtn">
                    <i class="fas fa-trash me-1"></i>
                    <span data-en="Delete" data-ar="حذف">Delete</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedDriverId = null;

    const deleteDriverModal = document.getElementById('deleteDriverModal');
    if (deleteDriverModal) {
        deleteDriverModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            selectedDriverId = button.getAttribute('data-driver-id');
            document.getElementById('deleteDriverName').textContent =
                button.getAttribute('data-driver-name') || '';
        });
    }

    const confirmBtn = document.getElementById('confirmDeleteDriverBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {
            if (!selectedDriverId) return;
            const form = document.getElementById('delete-driver-form-' + selectedDriverId);
            if (form) form.submit();
        });
    }
</script>
@endsection
