@php
    /**
     * Usage: include this partial from any Blade view and pass a $basePrice float.
     * Example: @include('dashboard.partials.area_price_table', ['basePrice' => 100])
     */
    $areas = \App\Models\Area::orderBy('name->en')->get();
    $base = isset($basePrice) ? floatval($basePrice) : 0.0;
    $label = $label ?? null;
    
    // human friendly formatting helper
    $fmt = function ($v) {
        return number_format($v, 2);
    };
@endphp

<div class="card mt-3">
    <div class="card-body">
        <h5 class="card-title">
            Adjusted prices by area
            @if($label)
                ({{ $label }})
            @endif
            (base: {{ $fmt($base) }})
        </h5>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Area</th>
                        <th>Increase %</th>
                        <th>Adjusted Price</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($areas as $a)
                        <tr>
                            <td>{{ data_get($a, 'name.en', json_encode($a->name)) }} <small class="text-muted">({{ data_get($a, 'name.ar', '') }})</small></td>
                            <td>{{ $fmt($a->price_increase_percentage ?? 0) }}%</td>
                            <td>{{ $fmt($a->adjustedPrice($base)) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-muted">No areas defined.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
