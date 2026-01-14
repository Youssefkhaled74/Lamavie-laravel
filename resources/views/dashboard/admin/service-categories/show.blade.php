@extends('dashboard.admin.layouts.main')

@section('content')
<div class="content-header fade-in">
    <h1 class="fw-bold text-primary">Service Category Details</h1>
    <p class="text-muted">View details of the selected service category.</p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-lg border-0" style="background: linear-gradient(145deg, #ffffff, #f8fafc);">
    <div class="card-header bg-primary text-white py-3 rounded-top d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Service Category: {{ $serviceCategory->name['en'] }}</h5>
        <a href="{{ route('admin.service-categories.index') }}" class="btn btn-light btn-sm">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>
    <div class="card-body p-4">
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-semibold">Name (English)</h6>
                <p class="text-muted">{{ $serviceCategory->name['en'] }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="fw-semibold">Name (Arabic)</h6>
                <p class="text-muted">{{ $serviceCategory->name['ar'] }}</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <h6 class="fw-semibold">Service</h6>
                <p class="text-muted">{{ $serviceCategory->service ? $serviceCategory->service->name['en'] : 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="fw-semibold">Logo</h6>
                @if ($serviceCategory->logo)
                    <img src="{{ Storage::url($serviceCategory->logo) }}" alt="Logo" style="width: 150px; height: 150px; object-fit: cover; border-radius: 5px;">
                @else
                    <p class="text-muted">No Logo</p>
                @endif
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <h6 class="fw-semibold">Created At</h6>
                <p class="text-muted">{{ $serviceCategory->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="fw-semibold">Updated At</h6>
                <p class="text-muted">{{ $serviceCategory->updated_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <h6 class="fw-semibold">Related Data</h6>
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Maintenance or Cleaning:</strong> {{ $serviceCategory->maintenanceOrCleaning->count() }} items
                    </li>
                    <li class="list-group-item">
                        <strong>Carpet Materials:</strong> {{ $serviceCategory->carpetMaterial->count() }} items
                    </li>
                    <li class="list-group-item">
                        <strong>Types of Stain:</strong> {{ $serviceCategory->typeOfStain->count() }} items
                    </li>
                    <li class="list-group-item">
                        <strong>Sizes of Stain:</strong> {{ $serviceCategory->sizeOfStain->count() }} items
                    </li>
                    <li class="list-group-item">
                        <strong>Carpet Sizes:</strong> {{ $serviceCategory->carpetSize->count() }} items
                    </li>
                    <li class="list-group-item">
                        <strong>Your Items:</strong> {{ $serviceCategory->yourItems->count() }} items
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection