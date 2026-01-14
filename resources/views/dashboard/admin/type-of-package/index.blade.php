@extends('dashboard.admin.layouts.main')

@section('content')
<div class="content-header fade-in">
    <h1 class="fw-bold text-primary">Package Types</h1>
    <p class="text-muted">Manage your package types with their associated service categories.</p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-lg border-0" style="background: linear-gradient(145deg, #ffffff, #f8fafc);">
    <div class="card-header bg-primary text-white py-3 rounded-top d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Package Types List</h5>
        <a href="{{ route('admin.type-of-package.create') }}" class="btn btn-light btn-sm">
            <i class="fas fa-plus me-2"></i>Add New Package Type
        </a>
    </div>
    <div class="card-body p-4">
        <div class="mb-4">
            <label for="service_category_id" class="form-label fw-semibold">Select Service Category</label>
            <select id="service_category_id" class="form-control form-control-lg rounded-3">
                <option value="" selected>All Categories</option>
                @foreach ($serviceCategories as $serviceCategory)
                    <option value="{{ $serviceCategory->id }}">{{ $serviceCategory->name['en'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>Name (English)</th>
                        <th>Name (Arabic)</th>
                        <th>Service Category</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="packages-table-body">
                    @include('dashboard.admin.type-of-package.partials.packages-table', ['typeOfPackages' => $typeOfPackages])
                </tbody>
            </table>
        </div>
        <div id="pagination-links">
            {{ $typeOfPackages->appends(['service_category_id' => request()->service_category_id])->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(37, 99, 235, 0.25);
        border-color: var(--primary);
    }
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categorySelect = document.getElementById('service_category_id');
        const tableBody = document.getElementById('packages-table-body');
        const paginationLinks = document.getElementById('pagination-links');

        function loadPackages(serviceCategoryId = '', page = 1) {
            let url = '{{ route('admin.type-of-package.index') }}';
            if (serviceCategoryId) {
                url += `?service_category_id=${serviceCategoryId}&page=${page}`;
            } else {
                url += `?page=${page}`;
            }

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                tableBody.innerHTML = data.table;
                paginationLinks.innerHTML = data.pagination;
                
                // Reattach pagination event listeners
                document.querySelectorAll('#pagination-links a').forEach(link => {
                    link.addEventListener('click', function (e) {
                        e.preventDefault();
                        const url = new URL(this.href);
                        const page = url.searchParams.get('page') || 1;
                        loadPackages(categorySelect.value, page);
                    });
                });
            })
            .catch(error => {
                console.error('Error loading package types:', error);
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Error loading package types.</td></tr>';
            });
        }

        // Load packages when category changes
        categorySelect.addEventListener('change', function () {
            loadPackages(this.value);
        });

        // Initial load
        loadPackages(categorySelect.value);

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