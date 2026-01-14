@extends('dashboard.admin.layouts.main')

@section('content')
<div class="content-header fade-in py-4 px-3 mb-4 rounded shadow-sm" style="background: linear-gradient(90deg, #e3f2fd 0%, #f8fafc 100%); border-left: 6px solid #0d6efd;">
    <h1 class="fw-bold text-primary mb-2" style="font-size:2.2rem; letter-spacing:1px;" data-en="Areas Management" data-ar="إدارة المناطق">Areas Management</h1>
    <p class="text-muted mb-0" style="font-size:1.1rem;" data-en="Create and manage areas used in registration and filtering." data-ar="إنشاء وإدارة المناطق المستخدمة في التسجيل والتصفية.">Create and manage areas used in registration and filtering.</p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-lg border-0 rounded-4 overflow-hidden">
    <div class="card-header bg-gradient py-4 px-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(90deg, #0d6efd 0%, #6ea8fe 100%);">
        <h5 class="card-title mb-0 text-white" style="font-size:1.3rem; letter-spacing:0.5px;" data-en="Areas List" data-ar="قائمة المناطق">Areas List</h5>
        <a href="{{ route('admin.areas.create') }}" class="btn btn-primary btn-lg" data-en="Add New Area" data-ar="إضافة منطقة جديدة">
            <i class="fas fa-plus me-2"></i>Add New Area
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0" style="font-size:1.05rem;">
                <thead class="bg-light">
                    <tr style="background: linear-gradient(90deg, #e3f2fd 0%, #f8fafc 100%);">
                        <th class="px-4 py-3 text-primary">ID</th>
                        <th class="px-4 py-3 text-primary">Name (EN)</th>
                        <th class="px-4 py-3 text-primary">Name (AR)</th>
                        <th class="px-4 py-3 text-primary">Slug</th>
                           <th class="px-4 py-3 text-primary">Price Increase %</th>
                        <th class="px-4 py-3 text-primary">Description</th>
                        <th class="px-4 py-3 text-primary">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($areas as $area)
                        <tr style="background: #fff;">
                            <td class="px-4 py-3 fw-semibold">{{ $area->id }}</td>
                            <td class="px-4 py-3">{{ data_get($area, 'name.en', '') }}</td>
                            <td class="px-4 py-3">{{ data_get($area, 'name.ar', '') }}</td>
                            <td class="px-4 py-3">{{ $area->slug }}</td>
                                <td class="px-4 py-3">{{ number_format($area->price_increase_percentage ?? 0, 2) }}%</td>
                            <td class="px-4 py-3">{{ \Illuminate\Support\Str::limit($area->description ?? '', 80) }}</td>
                            <td class="px-4 py-3">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.areas.show', $area) }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('admin.areas.edit', $area) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.areas.destroy', $area) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Are you sure you want to delete this area?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No areas found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .card { border-radius: 1.5rem; }
    .rounded-pill { border-radius: 50rem !important; }
</style>
@endsection
