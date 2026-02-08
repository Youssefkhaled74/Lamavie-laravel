@extends('dashboard.admin.layouts.main')

@section('content')
<div class="content-header fade-in">
    <h1 class="fw-bold text-primary">Item Details</h1>
    <p class="text-muted">View the details of the selected item.</p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-lg border-0" style="background: linear-gradient(145deg, #ffffff, #f8fafc);">
    <div class="card-header bg-primary text-white py-3 rounded-top d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Item: {{ $yourItem->name['en'] }}</h5>
        <a href="{{ route('admin.your-items.edit', $yourItem) }}" class="btn btn-light btn-sm">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
    </div>
    <div class="card-body p-4">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <h6 class="fw-semibold text-muted">Name (English)</h6>
                    <p>{{ $yourItem->name['en'] }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-4">
                    <h6 class="fw-semibold text-muted">Name (Arabic)</h6>
                    <p>{{ $yourItem->name['ar'] }}</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <h6 class="fw-semibold text-muted">Service Category</h6>
                    <p>{{ $yourItem->serviceCategory ? $yourItem->serviceCategory->name['en'] : 'N/A' }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-4">
                    <h6 class="fw-semibold text-muted">Logo</h6>
                    @if ($yourItem->logo)
                        <img src="{{ Storage::url($yourItem->logo) }}" alt="{{ $yourItem->name['en'] }}" style="max-width: 100px; max-height: 100px;">
                    @else
                        <p>N/A</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <h6 class="fw-semibold text-muted">Washing Price</h6>
                    <p>{{ ($yourItem->washing_price ?? $yourItem->price) ? number_format($yourItem->washing_price ?? $yourItem->price, 2) : 'N/A' }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-4">
                    <h6 class="fw-semibold text-muted">Ironing Price</h6>
                    <p>{{ $yourItem->ironing_price ? number_format($yourItem->ironing_price, 2) : 'N/A' }}</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-4">
                    <h6 class="fw-semibold text-muted">Created At</h6>
                    <p>{{ $yourItem->created_at }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-4">
                    <h6 class="fw-semibold text-muted">Updated At</h6>
                    <p>{{ $yourItem->updated_at }}</p>
                </div>
            </div>
        </div>
        <div class="d-flex gap-3 mt-4">
            <a href="{{ route('admin.your-items.index') }}" class="btn btn-outline-secondary btn-lg">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
<script>
    // Animation for content
    document.querySelectorAll('.fade-in').forEach(element => {
        element.style.opacity = 0;
        setTimeout(() => {
            element.style.transition = 'opacity 0.5s ease';
            element.style.opacity = 1;
        }, 100);
    });
</script>
@endsection
