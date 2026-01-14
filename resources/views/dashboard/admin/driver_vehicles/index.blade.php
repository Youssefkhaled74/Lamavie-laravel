@extends('dashboard.admin.layouts.main')

@section('content')
@include('dashboard.admin.driver_vehicles._ui')

<div class="container py-4">
    <div class="dv-page">
        <div class="dv-head">
            <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                <div>
                    <h3 class="dv-title">Vehicles</h3>
                    <p class="dv-sub">Manage vehicles and assign drivers easily.</p>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.driver-vehicles.create') }}" class="btn btn-primary dv-btn">
                        <i class="fas fa-plus me-2"></i>Add Vehicle
                    </a>
                </div>
            </div>
        </div>

        <div class="dv-card">
            <div class="dv-card-header">
                <div>
                    <div class="fw-bold">Vehicles List</div>
                    <div class="muted">Plate, specs, and assigned drivers</div>
                </div>
                <span class="dv-chip">
                    <i class="fas fa-car"></i>
                    {{ $vehicles->total() }} total
                </span>
            </div>

            <div class="table-responsive">
                <table class="table dv-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Plate</th>
                            <th>Make / Model</th>
                            <th>Color</th>
                            <th>Drivers</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($vehicles as $v)
                            <tr>
                                <td data-label="Plate" class="fw-semibold">{{ $v->plate_number }}</td>
                                <td data-label="Make/Model" class="text-muted">{{ $v->make }} {{ $v->model }}</td>
                                <td data-label="Color">{{ $v->color }}</td>
                                <td data-label="Drivers">
                                    @if($v->drivers && $v->drivers->count())
                                        @foreach($v->drivers as $drv)
                                            <div>
                                                <a href="{{ route('admin.car-wash-drivers.show', $drv->id) }}" class="text-decoration-none fw-semibold">
                                                    {{ $drv->name }}
                                                </a>
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td data-label="Actions" class="text-end">
                                    <div class="d-flex gap-2 justify-content-end flex-wrap">
                                        <a href="{{ route('admin.driver-vehicles.show', $v->id) }}" class="btn btn-sm btn-outline-info dv-btn">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                        <a href="{{ route('admin.driver-vehicles.edit', $v->id) }}" class="btn btn-sm btn-outline-warning dv-btn">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a>
                                        <form action="{{ route('admin.driver-vehicles.destroy', $v->id) }}" method="post" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger dv-btn"
                                                onclick="return confirm('Are you sure you want to delete this vehicle?')">
                                                <i class="fas fa-trash me-1"></i>Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">No vehicles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $vehicles->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
