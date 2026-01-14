@extends('dashboard.admin.layouts.main')

@section('content')

<style>
    .labs-card {
        border-radius: 16px;
        box-shadow: 0 12px 30px rgba(2,6,23,.08);
        background: #fff;
    }

    .labs-header h1 {
        font-weight: 900;
        color: #1e3a8a;
    }

    .labs-table th {
        font-size: 0.85rem;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: .05em;
    }

    .labs-table td {
        vertical-align: middle;
        font-weight: 600;
        color: #334155;
    }

    .labs-actions .btn {
        border-radius: 12px;
        font-weight: 800;
        padding: 6px 12px;
        box-shadow: 0 6px 18px rgba(2,6,23,.10);
    }

    .labs-actions .btn-outline-primary {
        border-color: rgba(37,99,235,.4);
    }

    .labs-actions .btn-primary {
        background: linear-gradient(90deg,#2563eb,#3b82f6);
        border: none;
    }

    .labs-actions .btn-danger {
        background: linear-gradient(90deg,#ef4444,#f97316);
        border: none;
    }

    @media(max-width: 992px){
        .labs-actions {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<div class="labs-header mb-4">
    <h1>Labs</h1>
    <p class="text-muted">Manage labs that receive dry-clean orders</p>
</div>

<div class="labs-card p-4">

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <form method="GET" action="{{ route('admin.labs.index') }}" class="d-flex gap-2">
            <input type="text"
                   class="form-control"
                   name="q"
                   placeholder="Search labs by name..."
                   value="{{ request('q') }}">
            <button class="btn btn-primary">
                <i class="fas fa-search"></i>
            </button>
        </form>

        <a href="{{ route('admin.labs.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-1"></i> Create Lab
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover labs-table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Lab</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($labs as $lab)
                    <tr>
                        <td>{{ $lab->id }}</td>
                        <td>
                            <div class="fw-bold">{{ $lab->name }}</div>
                        </td>
                        <td>{{ $lab->email }}</td>
                        <td>{{ $lab->phone }}</td>
                        <td class="text-muted">
                            {{ \Illuminate\Support\Str::limit($lab->address, 50) }}
                        </td>
                        <td class="text-end">
                            <div class="labs-actions d-flex justify-content-end gap-2 flex-wrap">

                                <a href="{{ route('admin.labs.show', $lab->id) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('admin.labs.edit', $lab->id) }}"
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-pen"></i>
                                </a>

                                <button
                                    type="button"
                                    class="btn btn-sm btn-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteLabModal"
                                    data-lab-id="{{ $lab->id }}"
                                    data-lab-name="{{ $lab->name }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <form id="delete-lab-form-{{ $lab->id }}"
                                  method="POST"
                                  action="{{ route('admin.labs.destroy', $lab->id) }}"
                                  class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No labs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $labs->withQueryString()->links('vendor.pagination.bootstrap-5') }}
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteLabModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-triangle-exclamation me-2"></i>
                    Delete Lab
                </h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="fw-bold mb-1">
                    Are you sure you want to delete this lab?
                </p>
                <p class="text-muted">
                    Lab name: <strong id="deleteLabName"></strong>
                </p>
                <small class="text-danger">
                    This action cannot be undone.
                </small>
            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>
                <button class="btn btn-danger" id="confirmDeleteLabBtn">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedLabId = null;

    const deleteModal = document.getElementById('deleteLabModal');

    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        selectedLabId = button.getAttribute('data-lab-id');
        document.getElementById('deleteLabName').textContent =
            button.getAttribute('data-lab-name');
    });

    document.getElementById('confirmDeleteLabBtn')
        .addEventListener('click', function () {
            if (!selectedLabId) return;
            document.getElementById('delete-lab-form-' + selectedLabId).submit();
        });
</script>

@endsection
