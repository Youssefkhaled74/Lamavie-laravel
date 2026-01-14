@if(!empty($payload['cars']) && is_array($payload['cars']))
<div style="margin-top:20px;">
  <div class="label" style="margin-bottom:12px;">
    <i class="fas fa-car me-2"></i>Cars Details
  </div>

  <div class="cars-section">
    @foreach($payload['cars'] as $carGroup)
      <div class="car-type-block">
        <div class="car-type-header">
          <div class="car-type-title">
            <span class="car-type-badge">{{ $carGroup['car_type'] ?? 'Unknown Type' }}</span>
            <span class="car-qty-badge">Qty: {{ $carGroup['quantity'] ?? 0 }}</span>
          </div>
        </div>

        @if(!empty($carGroup['car_details']) && is_array($carGroup['car_details']))
          <div class="car-details-grid">
            @foreach($carGroup['car_details'] as $car)
              <div class="car-detail-card">
                <div class="car-detail-row">
                  <span class="car-detail-key"><i class="fas fa-car me-1"></i>Model:</span>
                  <span class="car-detail-value">{{ $car['car_model'] ?? '—' }}</span>
                </div>
                <div class="car-detail-row">
                  <span class="car-detail-key"><i class="fas fa-id-card me-1"></i>Plate:</span>
                  <span class="car-detail-value">{{ $car['plate_number'] ?? '—' }}</span>
                </div>
                <div class="car-detail-row">
                  <span class="car-detail-key"><i class="fas fa-palette me-1"></i>Color:</span>
                  <span class="car-detail-value">{{ $car['car_color'] ?? '—' }}</span>
                </div>
                <div class="car-detail-row">
                  <span class="car-detail-key"><i class="fas fa-parking me-1"></i>Parking:</span>
                  <span class="car-detail-value">{{ $car['steady_parking_spot'] ?? '—' }}</span>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="muted" style="margin-top:8px;font-size:13px;">No car details provided.</div>
        @endif
      </div>
    @endforeach
  </div>
</div>
@endif
