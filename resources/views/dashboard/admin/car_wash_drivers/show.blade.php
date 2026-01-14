@extends('dashboard.admin.layouts.main')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Driver Details</h5>
                <small class="text-white-50">Profile & assigned vehicles</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.car-wash-drivers.edit', $driver->id) }}" class="btn btn-light btn-sm"><i class="fas fa-edit me-1"></i>Edit</a>
                <a href="{{ route('admin.car-wash-drivers.index') }}" class="btn btn-outline-light btn-sm"><i class="fas fa-list me-1"></i>All Drivers</a>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted">Name</h6>
                    <div class="mb-3 fs-5">{{ $driver->name }}</div>

                    <h6 class="text-muted">Phone</h6>
                    <div class="mb-3">{{ $driver->phone }}</div>

                    <h6 class="text-muted">Email</h6>
                    <div class="mb-3">{{ $driver->email }}</div>

                    <h6 class="text-muted">License</h6>
                    <div class="mb-3">{{ $driver->license_number }}</div>

                    <h6 class="text-muted">Notes</h6>
                    <div class="mb-3 text-break">{{ $driver->notes }}</div>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted">Assigned Vehicles</h6>
                    @php $vehicles = $driver->vehicles ?? collect(); @endphp
                    @if($vehicles->count())
                        <ul class="list-group list-group-flush">
                            @foreach($vehicles as $v)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $v->plate_number }}</strong>
                                        <div class="small text-muted">{{ $v->make }} {{ $v->model }} — {{ $v->color }}</div>
                                    </div>
                                    <div class="text-end">
                                        <a href="{{ route('admin.driver-vehicles.show', $v->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-muted">No vehicles assigned.</div>
                    @endif
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <form action="{{ route('admin.car-wash-drivers.destroy', $driver->id) }}" method="post" onsubmit="return confirm('Delete this driver?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger"><i class="fas fa-trash me-1"></i>Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
