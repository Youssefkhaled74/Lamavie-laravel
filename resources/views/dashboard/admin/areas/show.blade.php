@extends('dashboard.admin.layouts.main')

@section('content')
<div class="content-header fade-in">
    <h1 class="fw-bold text-primary">Area Details</h1>
    <p class="text-muted">Details for area ID #{{ $area->id }}</p>
</div>

<div class="card shadow-lg border-0">
    <div class="card-body p-4">
        <h3 class="mb-3">{{ data_get($area, 'name.en', '') }} <small class="text-muted">({{ data_get($area, 'name.ar', '') }})</small></h3>
    <p><strong>Slug:</strong> {{ $area->slug }}</p>
    <p><strong>Price Increase Percentage:</strong> {{ number_format($area->price_increase_percentage ?? 0, 2) }}%</p>
    <hr>
    <p>{{ $area->description ?? 'No description provided.' }}</p>

        {{-- Demo: show adjusted prices for a sample base price (change basePrice as needed) --}}
        @php $demoBase = 100; @endphp
        @include('dashboard.partials.area_price_table', ['basePrice' => $demoBase])

        <div class="mt-4">
            <a href="{{ route('admin.areas.edit', $area) }}" class="btn btn-warning"><i class="fas fa-edit me-2"></i>Edit</a>
            <a href="{{ route('admin.areas.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to list</a>
        </div>
    </div>
</div>
@endsection
