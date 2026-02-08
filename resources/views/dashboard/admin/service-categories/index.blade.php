@extends('dashboard.admin.layouts.main')

@section('content')
@php
    $totalCategories = $serviceCategories->count();
    $logoCount = $serviceCategories->whereNotNull('logo')->count();
    $services = $serviceCategories
        ->map(function($sc){ return $sc->service; })
        ->filter()
        ->unique('id')
        ->values();
@endphp

<div class="content-header fade-in d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="fw-bold text-primary mb-1">Service Categories</h1>
        <p class="text-muted mb-0">Manage service categories with their associated services.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <div class="stat-chip">
            <div class="stat-label">Total</div>
            <div class="stat-value">{{ $totalCategories }}</div>
        </div>
        <div class="stat-chip">
            <div class="stat-label">With Logos</div>
            <div class="stat-value">{{ $logoCount }}</div>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-lg border-0 surface-gradient">
    <div class="card-header bg-primary text-white py-3 rounded-top d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <div class="table-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <h5 class="card-title mb-0">Service Categories List</h5>
        </div>
        <a href="{{ route('admin.service-categories.create') }}" class="btn btn-light btn-sm restrict-settings">
            <i class="fas fa-plus me-2"></i>Add New Service Category
        </a>
    </div>
    <div class="card-body p-4">
        <div class="table-toolbar mb-3">
            <div class="toolbar-left">
                <div class="search-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" class="form-control" id="sc-search" placeholder="Search by name or service...">
                </div>
                <select class="form-select" id="sc-service-filter">
                    <option value="">All Services</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->name['en'] }}">{{ $service->name['en'] }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="sc-logo-toggle" data-value="all">
                    Logo: All
                </button>
            </div>
            <div class="toolbar-right text-muted">
                <span id="sc-count">{{ $totalCategories }}</span> shown
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light sticky-header">
                    <tr>
                        <th class="text-muted">#</th>
                        <th>Name (English)</th>
                        <th class="d-none d-md-table-cell">Name (Arabic)</th>
                        <th>Service</th>
                        <th class="text-center">Logo</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="sc-table-body">
                    @forelse ($serviceCategories as $serviceCategory)
                        @php
                            $serviceName = $serviceCategory->service ? $serviceCategory->service->name['en'] : 'N/A';
                        @endphp
                        <tr data-name-en="{{ strtolower($serviceCategory->name['en']) }}"
                            data-name-ar="{{ strtolower($serviceCategory->name['ar']) }}"
                            data-service="{{ strtolower($serviceName) }}"
                            data-has-logo="{{ $serviceCategory->logo ? 'yes' : 'no' }}">
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-semibold">{{ $serviceCategory->name['en'] }}</div>
                            </td>
                            <td class="d-none d-md-table-cell">{{ $serviceCategory->name['ar'] }}</td>
                            <td>
                                <span class="badge bg-soft-primary">{{ $serviceName }}</span>
                            </td>
                            <td class="text-center">
                                @if ($serviceCategory->logo)
                                    <img src="{{ Storage::url($serviceCategory->logo) }}" alt="Logo" class="logo-thumb">
                                @else
                                    <span class="logo-placeholder">No Logo</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.service-categories.show', $serviceCategory) }}" class="btn btn-outline-info">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                    <a href="{{ route('admin.service-categories.edit', $serviceCategory) }}" class="btn btn-outline-warning restrict-settings">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                    <form action="{{ route('admin.service-categories.destroy', $serviceCategory) }}" method="POST" class="d-inline confirm-delete" data-message="Are you sure you want to delete this service category?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <div class="fw-semibold">No service categories found</div>
                                    <div class="text-muted">Create your first category to get started.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div id="sc-empty-filter" class="text-center text-muted py-5 d-none">
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-filter"></i>
                    </div>
                    <div class="fw-semibold">No results for this filter</div>
                    <div class="text-muted">Try adjusting your search or filters.</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .surface-gradient { background: linear-gradient(145deg, #ffffff, #f7fbff); }
    .table-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.4);
    }
    .stat-chip {
        min-width: 110px;
        background: #ffffff;
        border: 1px solid #e7eef6;
        border-radius: 12px;
        padding: 8px 12px;
        box-shadow: 0 8px 24px rgba(17, 24, 39, 0.05);
    }
    .stat-label { font-size: 12px; color: #6b7280; }
    .stat-value { font-size: 18px; font-weight: 700; color: #111827; }
    .table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }
    .toolbar-left {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .search-wrap {
        position: relative;
        min-width: 240px;
    }
    .search-wrap i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }
    .search-wrap .form-control {
        padding-left: 34px;
        border-radius: 10px;
    }
    .logo-thumb {
        width: 44px;
        height: 44px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
    }
    .logo-placeholder {
        font-size: 12px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #64748b;
        border: 1px dashed #cbd5f5;
    }
    .sticky-header th {
        position: sticky;
        top: 0;
        z-index: 1;
        background: #f8fafc;
    }
    .bg-soft-primary {
        background: #eaf2ff;
        color: #2b5fb8;
        border: 1px solid #d4e3ff;
        font-weight: 600;
    }
    .empty-state {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .empty-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eef2f7;
        color: #64748b;
    }
    @media (max-width: 768px) {
        .btn-group.btn-group-sm {
            flex-direction: column;
        }
        .btn-group.btn-group-sm .btn {
            border-radius: 6px !important;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const restrictSettings = {{ env('RESTRICT_SETTINGS', 1) }};
        const searchInput = document.getElementById('sc-search');
        const serviceFilter = document.getElementById('sc-service-filter');
        const logoToggle = document.getElementById('sc-logo-toggle');
        const tableBody = document.getElementById('sc-table-body');
        const emptyFilter = document.getElementById('sc-empty-filter');
        const countEl = document.getElementById('sc-count');

        // Intercept create/edit restricted links
        document.querySelectorAll('.restrict-settings').forEach(function(el) {
            el.addEventListener('click', function(e) {
                if (restrictSettings === 0) {
                    e.preventDefault();
                    const msg = 'This action is restricted. Please connect the developer to make this action.';
                    if (typeof showCustomAlert !== 'undefined') {
                        showCustomAlert(msg);
                    } else {
                        alert(msg);
                    }
                }
            });
        });

        // Client-side search and filter
        function applyFilters() {
            if (!tableBody) return;
            const term = (searchInput.value || '').trim().toLowerCase();
            const service = (serviceFilter.value || '').trim().toLowerCase();
            const logoPref = logoToggle.getAttribute('data-value');
            let visible = 0;

            Array.from(tableBody.querySelectorAll('tr')).forEach(function(row) {
                const nameEn = row.getAttribute('data-name-en') || '';
                const nameAr = row.getAttribute('data-name-ar') || '';
                const svc = row.getAttribute('data-service') || '';
                const hasLogo = row.getAttribute('data-has-logo') || 'no';

                const matchTerm = !term || nameEn.includes(term) || nameAr.includes(term) || svc.includes(term);
                const matchService = !service || svc === service;
                const matchLogo = logoPref === 'all' || (logoPref === 'yes' && hasLogo === 'yes') || (logoPref === 'no' && hasLogo === 'no');

                if (matchTerm && matchService && matchLogo) {
                    row.classList.remove('d-none');
                    visible += 1;
                } else {
                    row.classList.add('d-none');
                }
            });

            if (countEl) countEl.textContent = visible;
            if (emptyFilter) {
                emptyFilter.classList.toggle('d-none', visible !== 0);
            }
        }

        if (searchInput) searchInput.addEventListener('input', applyFilters);
        if (serviceFilter) serviceFilter.addEventListener('change', applyFilters);
        if (logoToggle) {
            logoToggle.addEventListener('click', function() {
                const current = logoToggle.getAttribute('data-value');
                const next = current === 'all' ? 'yes' : current === 'yes' ? 'no' : 'all';
                logoToggle.setAttribute('data-value', next);
                logoToggle.textContent = next === 'all' ? 'Logo: All' : next === 'yes' ? 'Logo: Yes' : 'Logo: No';
                applyFilters();
            });
        }
        applyFilters();

        // Handle confirm-delete forms
        document.querySelectorAll('.confirm-delete').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (restrictSettings === 0) {
                    const msg = 'This action is restricted. Please connect the developer to make this action.';
                    if (typeof showCustomAlert !== 'undefined') {
                        showCustomAlert(msg);
                    } else {
                        alert(msg);
                    }
                    return;
                }
                const message = form.getAttribute('data-message') || 'Are you sure?';
                showConfirmModal(message, function(confirmed) {
                    if (confirmed) form.submit();
                });
            });
        });

        // Reuse modal function (same as other files)
        function showConfirmModal(message, callback) {
            const existing = document.querySelector('.custom-alert-overlay');
            if (existing) existing.remove();
            const overlay = document.createElement('div');
            overlay.className = 'custom-alert-overlay';
            const modal = document.createElement('div');
            modal.className = 'custom-alert-modal';
            modal.innerHTML = `
                <h3>Confirm</h3>
                <p>${message}</p>
                <div class="custom-alert-actions">
                    <button class="custom-alert-btn btn-confirm">Yes</button>
                    <button class="custom-alert-btn btn-cancel" style="background:#6b7280;">Cancel</button>
                </div>
            `;
            overlay.appendChild(modal);
            document.body.appendChild(overlay);
            modal.querySelector('.btn-confirm').addEventListener('click', function() { overlay.remove(); callback(true); });
            modal.querySelector('.btn-cancel').addEventListener('click', function() { overlay.remove(); callback(false); });
            overlay.addEventListener('click', function(e) { if (e.target === overlay) { overlay.remove(); callback(false); } });
        }
    });
</script>
@endsection
