@extends('dashboard.admin.layouts.main')

@section('content')
<div class="container py-4">
    <div class="content-header fade-in py-3 px-2 mb-3 rounded shadow-sm" style="background: linear-gradient(90deg, #e3f2fd 0%, #f8fafc 100%); border-left: 6px solid #0d6efd;">
        <h2 class="fw-bold text-primary mb-0" style="font-size:1.6rem;">Car Wash Drivers</h2>
        <p class="text-muted mb-0 small">Manage drivers who handle car wash pickups and deliveries.</p>
    </div>

    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-gradient py-3 px-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(90deg, #0d6efd 0%, #6ea8fe 100%);">
            <h5 class="card-title mb-0 text-white" style="font-size:1.1rem;">Drivers List</h5>
            <a href="{{ route('admin.car-wash-drivers.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-plus me-2"></i>Add Driver
            </a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0" style="font-size:1.05rem;">
                    <thead class="bg-light">
                        <tr style="background: linear-gradient(90deg, #e3f2fd 0%, #f8fafc 100%);">
                            <th class="px-4 py-3 text-primary">Name</th>
                            <th class="px-4 py-3 text-primary">Contact</th>
                            <th class="px-4 py-3 text-primary">Vehicles</th>
                            <th class="px-4 py-3 text-primary">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($drivers as $d)
                            <tr style="background:#fff;">
                                <td class="px-4 py-3 fw-semibold">{{ $d->name }}</td>
                                <td class="px-4 py-3 small text-muted">{{ $d->phone }} • {{ $d->email }}</td>
                                <td class="px-4 py-3">
                                    @if($d->vehicles && $d->vehicles->count())
                                        @foreach($d->vehicles as $veh)
                                            <div><a href="{{ route('admin.driver-vehicles.show', $veh->id) }}">{{ $veh->plate_number }}</a> <small class="text-muted">{{ $veh->make }} {{ $veh->model }}</small></div>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('admin.car-wash-drivers.show', $d->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3"><i class="fas fa-eye"></i> View</a>
                                        <a href="{{ route('admin.car-wash-drivers.edit', $d->id) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3"><i class="fas fa-edit"></i> Edit</a>
                                        <form action="{{ route('admin.car-wash-drivers.destroy', $d->id) }}" method="post" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Are you sure you want to delete this driver?')"><i class="fas fa-trash"></i> Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No drivers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $drivers->links('vendor.pagination.bootstrap-5') }}</div>
</div>
@endsection
