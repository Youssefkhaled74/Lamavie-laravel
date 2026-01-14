@extends('dashboard.admin.layouts.main')

@section('content')
<div class="container-fluid">

    <div class="content-header fade-in mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="fw-bold text-primary mb-1" data-en="Edit Role" data-ar="تعديل الدور">Edit Role</h1>
            <p class="text-muted mb-0">
                <span data-en="Editing:" data-ar="تعديل:">Editing:</span> <span class="fw-semibold">{{ $role->name }}</span>
            </p>
        </div>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i><span data-en="Back" data-ar="رجوع">Back</span>
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-2" data-en="Please fix the following errors:" data-ar="يرجى إصلاح الأخطاء التالية:">Please fix the following errors:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-lg border-0 rounded-4 overflow-hidden" style="background: linear-gradient(145deg, #ffffff, #f8fafc);">
        <div class="card-header py-3 px-3 text-white d-flex justify-content-between align-items-center"
             style="background: linear-gradient(90deg, #0d6efd 0%, #6ea8fe 100%);">
            <h5 class="card-title mb-0" data-en="Role Settings" data-ar="إعدادات الدور">Role Settings</h5>
            <span class="badge bg-light text-dark border">#{{ $role->id }}</span>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('admin.roles.update', $role) }}" method="POST" id="role-form">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-lg-4">
                        <label class="form-label fw-semibold" data-en="Name" data-ar="الاسم">Name</label>
                        <input type="text" name="name" class="form-control form-control-lg rounded-3" value="{{ old('name', $role->name) }}" required>

                        <div class="mt-4 p-3 border rounded-4 bg-white">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="fw-semibold" data-en="Selected Permissions" data-ar="الصلاحيات المحددة">Selected Permissions</div>
                                <span class="badge bg-primary" id="selected-count">0</span>
                            </div>
                            <div class="text-muted small mt-1" data-en="Tip: Use group checkbox to select all." data-ar="نصيحة: استخدم تحديد المجموعة لاختيار الكل.">
                                Tip: Use group checkbox to select all.
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <label class="form-label fw-semibold mb-2" data-en="Permissions" data-ar="الصلاحيات">Permissions</label>

                        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
                            <div class="input-group" style="max-width: 420px;">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input id="perm-search" type="search" class="form-control"
                                       placeholder="Search permissions..." data-en-placeholder="Search permissions..." data-ar-placeholder="البحث عن الصلاحيات...">
                                <button class="btn btn-outline-secondary" type="button" id="clear-search"><i class="fas fa-times"></i></button>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" id="expand-all" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-chevron-down me-1"></i><span data-en="Expand all" data-ar="توسيع الكل">Expand all</span>
                                </button>
                                <button type="button" id="collapse-all" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-chevron-up me-1"></i><span data-en="Collapse all" data-ar="طي الكل">Collapse all</span>
                                </button>
                                <button type="button" id="select-visible" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-check-double me-1"></i><span data-en="Select visible" data-ar="تحديد الظاهر">Select visible</span>
                                </button>
                                <button type="button" id="unselect-all" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-ban me-1"></i><span data-en="Unselect all" data-ar="إلغاء تحديد الكل">Unselect all</span>
                                </button>
                            </div>
                        </div>

                        @php
                            $grouped = $permissions->groupBy(function($p){
                                if (str_contains($p->name, '.')) return explode('.', $p->name)[0];
                                return explode(' ', $p->name)[0];
                            });
                        @endphp

                        <div id="permissions-groups">
                            @foreach($grouped as $group => $perms)
                                @php $gid = \Illuminate\Support\Str::slug($group); @endphp

                                <div class="perm-group card mb-3 border-0 shadow-sm rounded-4 overflow-hidden" data-group="{{ $gid }}">
                                    <div class="card-header bg-white d-flex justify-content-between align-items-center"
                                         style="cursor:pointer;"
                                         data-bs-toggle="collapse"
                                         data-bs-target="#group-{{ $gid }}">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="form-check mb-0">
                                                <input class="form-check-input group-select" type="checkbox" data-group="{{ $gid }}" id="group_chk_{{ $gid }}">
                                                <label class="form-check-label fw-semibold ms-2" for="group_chk_{{ $gid }}">
                                                    {{ ucfirst(str_replace('-', ' ', $group)) }}
                                                    <small class="text-muted">(<span class="group-count">{{ count($perms) }}</span>)</small>
                                                </label>
                                            </div>
                                            <span class="badge bg-light text-dark border group-selected-badge">0 selected</span>
                                        </div>
                                        <div class="text-muted"><i class="fas fa-chevron-down"></i></div>
                                    </div>

                                    <div id="group-{{ $gid }}" class="collapse show">
                                        <div class="card-body">
                                            <div class="row g-2">
                                                @foreach($perms as $p)
                                                    <div class="col-12 col-md-6 perm-item" data-perm="{{ strtolower($p->name) }}" data-group="{{ $gid }}">
                                                        <label class="perm-tile d-flex gap-2 align-items-start p-2 rounded-3 border bg-white w-100">
                                                            <input class="form-check-input mt-1 perm-checkbox"
                                                                   type="checkbox"
                                                                   name="permissions[]"
                                                                   value="{{ $p->name }}"
                                                                   id="perm_{{ $p->id }}"
                                                                   {{ in_array($p->name, $rolePermissions) ? 'checked' : '' }}
                                                                   data-group="{{ $gid }}">
                                                            <div>
                                                                <div class="fw-semibold small">{{ $p->name }}</div>
                                                                <div class="text-muted small" data-en="Permission" data-ar="صلاحية">Permission</div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i><span data-en="Save" data-ar="حفظ">Save</span>
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times me-2"></i><span data-en="Cancel" data-ar="إلغاء">Cancel</span>
                    </a>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<style>
    .perm-tile { transition: all .15s ease; cursor:pointer; }
    .perm-tile:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(15,23,42,.06); }
    .form-control:focus {
        box-shadow: 0 0 0 .25rem rgba(13,110,253,.18);
        border-color:#0d6efd;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const searchInput = document.getElementById('perm-search');
    const clearBtn = document.getElementById('clear-search');
    const selectedCountEl = document.getElementById('selected-count');

    function updateSelectedCount(){
        const checked = document.querySelectorAll('.perm-checkbox:checked').length;
        selectedCountEl.textContent = checked;

        document.querySelectorAll('.perm-group').forEach(groupEl => {
            const gid = groupEl.dataset.group;
            const groupChecked = groupEl.querySelectorAll('.perm-checkbox[data-group="'+gid+'"]:checked').length;
            const badge = groupEl.querySelector('.group-selected-badge');
            if (badge) badge.textContent = groupChecked + ' selected';
        });
    }

    function syncGroupCheckbox(group){
        const all = Array.from(document.querySelectorAll('.perm-checkbox[data-group="'+group+'"]'));
        const allChecked = all.length ? all.every(c => c.checked) : false;
        const grp = document.querySelector('.group-select[data-group="'+group+'"]');
        if(grp) grp.checked = allChecked;
    }

    function filterPerms(term){
        term = (term || '').trim().toLowerCase();

        document.querySelectorAll('.perm-item').forEach(function(el){
            const label = (el.dataset.perm || '').toLowerCase();
            el.style.display = label.includes(term) ? '' : 'none';
        });

        document.querySelectorAll('.perm-group').forEach(function(groupEl){
            const anyVisible = Array.from(groupEl.querySelectorAll('.perm-item')).some(x => x.style.display !== 'none');
            groupEl.style.display = anyVisible ? '' : 'none';
        });
    }

    // initial sync
    document.querySelectorAll('.perm-group').forEach(g => syncGroupCheckbox(g.dataset.group));
    updateSelectedCount();

    searchInput?.addEventListener('input', (e) => filterPerms(e.target.value));
    clearBtn?.addEventListener('click', () => { searchInput.value=''; filterPerms(''); searchInput.focus(); });

    document.querySelectorAll('.group-select').forEach(function(chk){
        chk.addEventListener('change', function(){
            const group = chk.dataset.group;
            document.querySelectorAll('.perm-checkbox[data-group="'+group+'"]').forEach(function(cb){
                const item = cb.closest('.perm-item');
                if (item && item.style.display === 'none') return;
                cb.checked = chk.checked;
            });
            updateSelectedCount();
        });
    });

    document.querySelectorAll('.perm-checkbox').forEach(function(cb){
        cb.addEventListener('change', function(){
            const group = cb.dataset.group;
            syncGroupCheckbox(group);
            updateSelectedCount();
        });
    });

    document.getElementById('expand-all')?.addEventListener('click', function(){
        document.querySelectorAll('#permissions-groups .collapse').forEach(c => c.classList.add('show'));
    });
    document.getElementById('collapse-all')?.addEventListener('click', function(){
        document.querySelectorAll('#permissions-groups .collapse').forEach(c => c.classList.remove('show'));
    });

    document.getElementById('select-visible')?.addEventListener('click', function(){
        document.querySelectorAll('.perm-item').forEach(item => {
            if (item.style.display === 'none') return;
            const cb = item.querySelector('.perm-checkbox');
            if (cb) cb.checked = true;
        });
        document.querySelectorAll('.perm-group').forEach(g => syncGroupCheckbox(g.dataset.group));
        updateSelectedCount();
    });

    document.getElementById('unselect-all')?.addEventListener('click', function(){
        document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('.group-select').forEach(cb => cb.checked = false);
        updateSelectedCount();
    });
});
</script>
@endsection
