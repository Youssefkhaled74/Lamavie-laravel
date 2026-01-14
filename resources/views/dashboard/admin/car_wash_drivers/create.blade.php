@extends('dashboard.admin.layouts.main')

@section('content')
<div class="container py-4">
    <div class="content-header fade-in">
        <h1 class="fw-bold text-primary">Add Car Wash Driver</h1>
        <p class="text-muted">Create a driver and optionally add a vehicle.</p>
    </div>

    <div class="card shadow-lg border-0" style="background: linear-gradient(145deg, #ffffff, #f8fafc);">
        <div class="card-header bg-primary text-white py-3 rounded-top">
            <h5 class="card-title mb-0">New Driver</h5>
        </div>
        <div class="card-body p-4">
            <form method="post" action="{{ route('admin.car-wash-drivers.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Name</label>
                        <input name="name" class="form-control form-control-lg rounded-3 @error('name') is-invalid @enderror" required value="{{ old('name') }}">
                        @error('name')<div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Phone</label>
                        <input name="phone" class="form-control form-control-lg rounded-3 @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                        @error('phone')<div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input name="email" class="form-control form-control-lg rounded-3 @error('email') is-invalid @enderror" type="email" value="{{ old('email') }}">
                        @error('email')<div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">License #</label>
                        <input name="license_number" class="form-control form-control-lg rounded-3 @error('license_number') is-invalid @enderror" value="{{ old('license_number') }}">
                        @error('license_number')<div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Plate number</label>
                        <input name="plate_number" class="form-control form-control-lg rounded-3 @error('plate_number') is-invalid @enderror" value="{{ old('plate_number') }}">
                        @error('plate_number')<div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Make</label>
                        <input name="make" class="form-control form-control-lg rounded-3 @error('make') is-invalid @enderror" value="{{ old('make') }}">
                        @error('make')<div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Model</label>
                        <input name="model" class="form-control form-control-lg rounded-3 @error('model') is-invalid @enderror" value="{{ old('model') }}">
                        @error('model')<div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Color</label>
                        <input name="color" class="form-control form-control-lg rounded-3 @error('color') is-invalid @enderror" value="{{ old('color') }}">
                        @error('color')<div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control form-control-lg rounded-3 @error('notes') is-invalid @enderror" rows="4">{{ old('notes') }}</textarea>
                        @error('notes')<div class="invalid-feedback d-flex align-items-center"><i class="fas fa-exclamation-circle me-2"></i>{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12 d-flex gap-3 mt-2">
                        <button class="btn btn-primary btn-lg"><i class="fas fa-save me-2"></i>Create Driver</button>
                        <a href="{{ route('admin.car-wash-drivers.index') }}" class="btn btn-outline-secondary btn-lg"><i class="fas fa-arrow-left me-2"></i>Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
