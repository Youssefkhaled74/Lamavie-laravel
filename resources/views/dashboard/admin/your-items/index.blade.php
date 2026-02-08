@extends('dashboard.admin.layouts.main')

@section('content')
<div class="content-header fade-in">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h1 class="fw-bold text-primary mb-1">Your Items</h1>
            <p class="text-muted mb-0">Manage items, prices, and categories with faster search and filters.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.your-items.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Item
            </a>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">Total Items</div>
            <div class="stat-value" id="stat-total">{{ $yourItems->total() }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">Visible Items</div>
            <div class="stat-value" id="stat-count">{{ $yourItems->count() }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">Categories</div>
            <div class="stat-value">{{ $serviceCategories->count() }}</div>
        </div>
    </div>
</div>

<div class="card shadow-lg border-0 card-glass">
    <div class="card-body p-4">
        <div class="filters-bar">
            <div class="filter-item">
                <label for="service_category_id" class="form-label fw-semibold">Category</label>
                <select id="service_category_id" class="form-select form-select-lg rounded-3">
                    <option value="" selected>All Categories</option>
                    @foreach ($serviceCategories as $serviceCategory)
                        <option value="{{ $serviceCategory->id }}">{{ $serviceCategory->name['en'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-item flex-grow-1">
                <label for="items-search" class="form-label fw-semibold">Search</label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="items-search" class="form-control" placeholder="Search by English or Arabic name" value="{{ request('q') }}">
                </div>
            </div>
            <div class="filter-item filter-actions">
                <label class="form-label fw-semibold d-none d-md-block">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="button" id="clear-filters" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times me-2"></i>Clear
                    </button>
                    <a href="{{ route('admin.your-items.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Add
                    </a>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle table-modern">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Category</th>
                        <th>Logo</th>
                        <th class="text-end">Washing</th>
                        <th class="text-end">Ironing</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="items-table-body">
                    @include('dashboard.admin.your-items.partials.items-table', ['yourItems' => $yourItems])
                </tbody>
            </table>
        </div>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2" id="pagination-links">
            <div class="text-muted small" id="range-text">
                Showing {{ $yourItems->firstItem() ?? 0 }} - {{ $yourItems->lastItem() ?? 0 }} of {{ $yourItems->total() }}
            </div>
            <div id="pagination-container">
                {{ $yourItems->appends(['service_category_id' => request()->service_category_id, 'q' => request('q')])->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    :root {
        --card-bg: #ffffff;
        --card-border: rgba(15, 23, 42, 0.08);
        --accent: #0f172a;
        --accent-soft: rgba(15, 23, 42, 0.08);
    }
    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.25);
        border-color: var(--primary);
    }
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    .card-glass {
        background: linear-gradient(140deg, #ffffff 0%, #f4f6fb 100%);
    }
    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 16px;
        padding: 16px 18px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    }
    .stat-label {
        color: #64748b;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--accent);
        margin-top: 4px;
    }
    .filters-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 18px;
        align-items: end;
    }
    .filter-item {
        min-width: 220px;
    }
    .filter-actions {
        min-width: 220px;
        text-align: right;
    }
    .table-modern thead th {
        background: #f8fafc;
        color: #334155;
        border-bottom: 2px solid #e2e8f0;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.08em;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .table-modern tbody tr {
        background: #ffffff;
        border-radius: 10px;
    }
    .table-modern tbody tr:hover {
        background: #f8fafc;
    }
    .price-pill {
        background: var(--accent-soft);
        border-radius: 999px;
        padding: 4px 10px;
        font-weight: 600;
        color: #0f172a;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .table-modern td {
        vertical-align: middle;
    }
    .table-modern tbody tr {
        transition: box-shadow 0.2s ease, transform 0.2s ease;
    }
    .table-modern tbody tr:hover {
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.08);
        transform: translateY(-2px);
    }
    .badge-soft {
        background: rgba(37, 99, 235, 0.12);
        color: #1e3a8a;
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .logo-frame {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #f1f5f9;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    .logo-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .action-group .btn {
        border-radius: 10px;
        padding: 6px 10px;
    }
    @media (max-width: 991px) {
        .filter-item {
            flex: 1 1 100%;
        }
        .filter-actions {
            text-align: left;
        }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categorySelect = document.getElementById('service_category_id');
        const searchInput = document.getElementById('items-search');
        const clearFiltersBtn = document.getElementById('clear-filters');
        const tableBody = document.getElementById('items-table-body');
        const paginationLinks = document.getElementById('pagination-links');
        const paginationContainer = document.getElementById('pagination-container');
        const statTotal = document.getElementById('stat-total');
        const statCount = document.getElementById('stat-count');
        const rangeText = document.getElementById('range-text');
        let searchTimer = null;

        function loadItems(serviceCategoryId = '', page = 1, query = '') {
            let url = '{{ route('admin.your-items.index') }}';
            const params = new URLSearchParams();
            if (serviceCategoryId) {
                params.set('service_category_id', serviceCategoryId);
            }
            if (query) {
                params.set('q', query);
            }
            params.set('page', page);
            url += `?${params.toString()}`;

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                tableBody.innerHTML = data.table;
                if (paginationContainer) {
                    paginationContainer.innerHTML = data.pagination || '';
                }
                if (data.meta) {
                    statTotal.textContent = data.meta.total ?? statTotal.textContent;
                    statCount.textContent = data.meta.count ?? statCount.textContent;
                    const from = data.meta.from ?? 0;
                    const to = data.meta.to ?? 0;
                    const total = data.meta.total ?? 0;
                    rangeText.textContent = `Showing ${from} - ${to} of ${total}`;
                }
                
                // Reattach pagination event listeners
                document.querySelectorAll('#pagination-links a').forEach(link => {
                    link.addEventListener('click', function (e) {
                        e.preventDefault();
                        const url = new URL(this.href);
                        const page = url.searchParams.get('page') || 1;
                        loadItems(categorySelect.value, page, searchInput.value.trim());
                    });
                });
            })
            .catch(error => {
                console.error('Error loading items:', error);
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Error loading items.</td></tr>';
            });
        }

        // Load items when category changes
        categorySelect.addEventListener('change', function () {
            loadItems(this.value, 1, searchInput.value.trim());
        });

        // Debounced search
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                loadItems(categorySelect.value, 1, searchInput.value.trim());
            }, 350);
        });

        clearFiltersBtn.addEventListener('click', function () {
            categorySelect.value = '';
            searchInput.value = '';
            loadItems('', 1, '');
        });

        // Initial load
        loadItems(categorySelect.value, 1, searchInput.value.trim());

        // Animation for table rows
        document.querySelectorAll('.fade-in').forEach(element => {
            element.style.opacity = 0;
            setTimeout(() => {
                element.style.transition = 'opacity 0.5s ease';
                element.style.opacity = 1;
            }, 100);
        });
    });
</script>
@endsection
