@extends('dashboard.admin.layouts.main')

@section('content')
<div class="content-header fade-in">
    <h1 class="fw-bold text-primary">Service Categories</h1>
    <p class="text-muted">Manage service categories with their associated services.</p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-lg border-0" style="background: linear-gradient(145deg, #ffffff, #f8fafc);">
    <div class="card-header bg-primary text-white py-3 rounded-top d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Service Categories List</h5>
        <a href="{{ route('admin.service-categories.create') }}" class="btn btn-light btn-sm restrict-settings">
            <i class="fas fa-plus me-2"></i>Add New Service Category
        </a>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>Name (English)</th>
                        <th>Name (Arabic)</th>
                        <th>Service</th>
                        <th>Logo</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($serviceCategories as $serviceCategory)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $serviceCategory->name['en'] }}</td>
                            <td>{{ $serviceCategory->name['ar'] }}</td>
                            <td>{{ $serviceCategory->service ? $serviceCategory->service->name['en'] : 'N/A' }}</td>
                            <td>
                                @if ($serviceCategory->logo)
                                    <img src="{{ Storage::url($serviceCategory->logo) }}" alt="Logo" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                @else
                                    No Logo
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.service-categories.show', $serviceCategory) }}" class="btn btn-sm btn-info me-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.service-categories.edit', $serviceCategory) }}" class="btn btn-sm btn-warning me-1 restrict-settings">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.service-categories.destroy', $serviceCategory) }}" method="POST" class="d-inline confirm-delete" data-message="Are you sure you want to delete this service category?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No service categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const restrictSettings = {{ env('RESTRICT_SETTINGS', 1) }};

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