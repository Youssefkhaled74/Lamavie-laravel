@extends('dashboard.admin.layouts.main')

@section('content')
<div class="container-fluid">

    <div class="content-header fade-in mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="fw-bold text-primary mb-1" data-en="Roles" data-ar="الأدوار">Roles</h1>
            <p class="text-muted mb-0" data-en="Manage roles and their assigned permissions." data-ar="إدارة الأدوار والصلاحيات المعينة لها.">
                Manage roles and their assigned permissions.
            </p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-plus me-2"></i><span data-en="Create Role" data-ar="إنشاء دور">Create Role</span>
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="background: linear-gradient(145deg, #ffffff, #f8fafc);">
        <div class="card-header py-3 px-3 text-white d-flex justify-content-between align-items-center"
             style="background: linear-gradient(90deg, #0d6efd 0%, #6ea8fe 100%);">
            <h5 class="card-title mb-0" data-en="Roles List" data-ar="قائمة الأدوار">Roles List</h5>
            <span class="badge bg-light text-dark border">
                {{ $roles->count() }} <span data-en="roles" data-ar="أدوار">roles</span>
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3" data-en="Name" data-ar="الاسم">Name</th>
                            <th class="px-4 py-3" data-en="Permissions" data-ar="الصلاحيات">Permissions</th>
                            <th class="px-4 py-3 text-end" data-en="Actions" data-ar="الإجراءات">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td class="px-4 py-3 fw-semibold">{{ $role->name }}</td>

                                <td class="px-4 py-3">
                                    <span class="badge bg-primary-subtle text-primary border role-perm-count"
                                          data-role-id="{{ $role->id }}">
                                        {{ $role->permissions_count }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-end">
                                    <div class="d-flex gap-2 justify-content-end flex-wrap">
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 btn-manage-perms"
                                                data-role-id="{{ $role->id }}"
                                                data-role-name="{{ $role->name }}">
                                            <i class="fas fa-shield-alt me-1"></i>
                                            <span data-en="Manage" data-ar="إدارة">Manage</span>
                                        </button>

                                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="fas fa-edit me-1"></i>
                                            <span data-en="Edit" data-ar="تعديل">Edit</span>
                                        </a>

                                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete role?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                <i class="fas fa-trash me-1"></i>
                                                <span data-en="Delete" data-ar="حذف">Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal -->
<div class="modal fade" id="rolePermsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 overflow-hidden">
            <div class="modal-header text-white"
                 style="background: linear-gradient(90deg, #0d6efd 0%, #6ea8fe 100%);">
                <div>
                    <h5 class="modal-title mb-0">
                        <span data-en="Manage Permissions" data-ar="إدارة الصلاحيات">Manage Permissions</span>
                    </h5>
                    <div class="small opacity-75">
                        <span data-en="Role:" data-ar="الدور:">Role:</span> <span id="rolePermsModalTitle"></span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                    <div class="input-group" style="max-width: 520px;">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input id="modal-perm-search" type="search" class="form-control"
                               placeholder="Search permissions..." data-en-placeholder="Search permissions..." data-ar-placeholder="البحث عن الصلاحيات...">
                        <button class="btn btn-outline-secondary" type="button" id="modal-clear-search"><i class="fas fa-times"></i></button>
                    </div>

                    <div class="d-flex gap-2">
                        <span class="badge bg-light text-dark border">
                            <span data-en="Selected:" data-ar="المحدد:">Selected:</span>
                            <span id="modal-selected-count">0</span>
                        </span>
                        <button type="button" id="modal-select-visible" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-check-double me-1"></i><span data-en="Select visible" data-ar="تحديد الظاهر">Select visible</span>
                        </button>
                        <button type="button" id="modal-unselect-all" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-ban me-1"></i><span data-en="Unselect all" data-ar="إلغاء تحديد الكل">Unselect all</span>
                        </button>
                    </div>
                </div>

                <div id="rolePermsList" class="row g-2"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <span data-en="Close" data-ar="إغلاق">Close</span>
                </button>
                <button type="button" id="rolePermsSave" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i><span data-en="Save changes" data-ar="حفظ التغييرات">Save changes</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .perm-chip { transition: all .12s ease; cursor:pointer; }
    .perm-chip:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(15,23,42,.06); }
</style>

<script>
(function(){
    const modalEl = document.getElementById('rolePermsModal');
    const bsModal = modalEl ? bootstrap.Modal.getOrCreateInstance(modalEl) : null;

    let currentRoleId = null;

    const list = document.getElementById('rolePermsList');
    const title = document.getElementById('rolePermsModalTitle');
    const saveBtn = document.getElementById('rolePermsSave');

    const searchInput = document.getElementById('modal-perm-search');
    const clearSearch = document.getElementById('modal-clear-search');
    const selectedCount = document.getElementById('modal-selected-count');

    function showMsg(msg){
        if (typeof showCustomAlert !== 'undefined') showCustomAlert(msg);
        else alert(msg);
    }

    function updateModalSelectedCount(){
        const checked = list.querySelectorAll('.perm-checkbox:checked').length;
        selectedCount.textContent = checked;
    }

    function filterModal(term){
        term = (term || '').trim().toLowerCase();
        list.querySelectorAll('.perm-wrap').forEach(w => {
            const txt = (w.dataset.perm || '').toLowerCase();
            w.style.display = txt.includes(term) ? '' : 'none';
        });
    }

    clearSearch?.addEventListener('click', () => {
        searchInput.value = '';
        filterModal('');
        searchInput.focus();
    });

    searchInput?.addEventListener('input', (e) => filterModal(e.target.value));

    document.getElementById('modal-select-visible')?.addEventListener('click', function(){
        list.querySelectorAll('.perm-wrap').forEach(w => {
            if (w.style.display === 'none') return;
            const cb = w.querySelector('.perm-checkbox');
            if (cb) cb.checked = true;
        });
        updateModalSelectedCount();
    });

    document.getElementById('modal-unselect-all')?.addEventListener('click', function(){
        list.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);
        updateModalSelectedCount();
    });

    document.querySelectorAll('.btn-manage-perms').forEach(btn => {
        btn.addEventListener('click', async function(e){
            e.preventDefault();
            currentRoleId = this.dataset.roleId;

            title.textContent = this.dataset.roleName || '';
            list.innerHTML = '<div class="text-center text-muted py-4">Loading…</div>';
            selectedCount.textContent = '0';
            searchInput.value = '';

            try {
                const res = await fetch(`{{ url('admin/roles') }}/${currentRoleId}/permissions`, {credentials: 'same-origin'});
                const data = await res.json();

                list.innerHTML = '';
                data.permissions.forEach(p => {
                    const checked = data.assigned.includes(p.name) ? 'checked' : '';
                    const html = `
                        <div class="col-12 col-md-6 perm-wrap" data-perm="${(p.name||'').toLowerCase()}">
                            <label class="perm-chip d-flex gap-2 align-items-start p-2 rounded-3 border bg-white w-100">
                                <input class="form-check-input mt-1 perm-checkbox" type="checkbox" value="${p.name}" ${checked}>
                                <div>
                                    <div class="fw-semibold small">${p.name}</div>
                                    <div class="text-muted small">Permission</div>
                                </div>
                            </label>
                        </div>`;
                    list.insertAdjacentHTML('beforeend', html);
                });

                list.querySelectorAll('.perm-checkbox').forEach(cb => cb.addEventListener('change', updateModalSelectedCount));
                updateModalSelectedCount();

                bsModal.show();
            } catch (err) {
                console.error(err);
                list.innerHTML = '<div class="text-danger py-4">Failed to load permissions.</div>';
            }
        });
    });

    saveBtn?.addEventListener('click', async function(){
        if (!currentRoleId) return;

        const perms = Array.from(list.querySelectorAll('.perm-checkbox'))
            .filter(c => c.checked)
            .map(c => c.value);

        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch(`{{ url('admin/roles') }}/${currentRoleId}/permissions`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ permissions: perms })
            });

            const data = await res.json();

            if (data.success) {
                const el = document.querySelector('.role-perm-count[data-role-id="' + currentRoleId + '"]');
                if (el) el.textContent = data.count;

                bsModal.hide();
                showMsg(data.message || 'Permissions updated');
            } else {
                showMsg('Failed to update permissions');
            }
        } catch (err) {
            console.error(err);
            showMsg('Error saving permissions');
        }
    });
})();
</script>
@endsection
