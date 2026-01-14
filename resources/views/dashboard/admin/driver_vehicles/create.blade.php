@extends('dashboard.admin.layouts.main')

@section('content')
@include('dashboard.admin.driver_vehicles._ui')

<div class="container py-4">
    <div class="dv-page">
        <div class="dv-head">
            <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                <div>
                    <h3 class="dv-title">Add Vehicle</h3>
                    <p class="dv-sub">Create a vehicle and assign one or more drivers.</p>
                </div>
                <a href="{{ route('admin.driver-vehicles.index') }}" class="btn btn-outline-secondary dv-btn">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>
        </div>

        <div class="dv-card p-3 p-md-4 dv-form">
            <form method="post" action="{{ route('admin.driver-vehicles.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Assign Drivers</label>
                        <select name="driver_ids[]" class="form-select dv-multi" multiple>
                            @foreach($drivers as $dr)
                                <option value="{{ $dr->id }}">{{ $dr->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Plate number</label>
                        <input name="plate_number" class="form-control" placeholder="e.g. ABC-123">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Make</label>
                        <input name="make" class="form-control" placeholder="e.g. Toyota">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Model</label>
                        <input name="model" class="form-control" placeholder="e.g. Corolla">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Color</label>
                        <input name="color" class="form-control" placeholder="e.g. White">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Capacity</label>
                        <input name="capacity" class="form-control" type="number" min="0" placeholder="e.g. 4">
                    </div>

                    <div class="col-12 d-flex gap-2 mt-2">
                        <button class="btn btn-primary dv-btn">
                            <i class="fas fa-check me-2"></i>Create
                        </button>
                        <a href="{{ route('admin.driver-vehicles.index') }}" class="btn btn-outline-secondary dv-btn">Cancel</a>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
