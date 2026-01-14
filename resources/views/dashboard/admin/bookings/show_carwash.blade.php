@extends('dashboard.admin.layouts.main')

@section('content')
<div class="content-header fade-in">
  <h1 class="fw-bold text-primary">Booking Details</h1>
  <p class="text-muted">View and manage details of the selected booking.</p>
</div>

<div class="booking-page">
  @include('dashboard.admin.bookings._booking_styles')

  @php
    $payload = is_array($booking->payload_data) ? $booking->payload_data : (array) $booking->payload_data;
  @endphp

  <div class="booking-grid mt-3">

    <!-- LEFT: Car Wash Details -->
    <div class="card-modern">
      <div class="section-title">
        <i class="fas fa-bath"></i>
        <div>
          <div class="label">Car Wash Details</div>
          <div class="muted">Details submitted by the customer for this car wash booking</div>
        </div>

        <div style="margin-left:auto; text-align:right;">
          <div class="label">Status</div>
          @php
            $status = strtolower($booking->status ?? 'pending');
            $statusClass = $status === 'pending' ? 'badge-pending' : 'badge-pickup';
          @endphp
          <div class="badge-status {{ $statusClass }}">{{ ucfirst($status) }}</div>
        </div>
      </div>

      @if(!empty($payload))
        <div class="inner-card">
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div>
              <div class="label">Wash Type</div>
              <div class="value">{{ ucfirst(str_replace(['-','_'], ' ', $payload['car_wash_type'] ?? '—')) }}</div>
            </div>

            <div>
              <div class="label">Number of Cars</div>
              <div class="value">{{ $payload['number_of_cars'] ?? '—' }}</div>
            </div>

            <div>
              <div class="label">Location</div>
              <div class="value">{{ $payload['location'] ?? '—' }}</div>
            </div>

            <div>
              <div class="label">Date</div>
              @php
                $rawDate = $payload['date'] ?? null;
                $dateLabel = '—';
                try {
                  if ($rawDate) $dateLabel = \Carbon\Carbon::parse($rawDate)->format('d M Y');
                } catch (\Exception $e) { $dateLabel = $rawDate ?? '—'; }
              @endphp
              <div class="value">{{ $dateLabel }}</div>
            </div>

            <div style="grid-column:1 / -1">
              <div class="label">Place of Cleaning</div>
              <div class="value">{{ $payload['place_of_cleaning'] ?? '—' }}</div>
            </div>

            <div style="grid-column:1 / -1">
              <div class="label">Additional Services</div>
              <div class="value">{{ $payload['cars_additional_services'] ?? '—' }}</div>
            </div>

            @if(!empty($payload['notes']))
              <div style="grid-column:1 / -1">
                <div class="label">Customer Notes</div>
                <div class="value">{{ $payload['notes'] }}</div>
              </div>
            @endif
          </div>
        </div>

        {{-- Display cars details --}}
        @include('dashboard.admin.bookings.partials._carwash_cars', ['payload' => $payload])
      @else
        <div class="muted">No car-wash payload available for this booking.</div>
      @endif
    </div>

    <!-- RIGHT: Aside (Customer + Assigned Car) -->
    <aside>
      <div class="aside-stack">

        <!-- Customer Card (existing partial) -->
        <div class="card-modern">
          @include('dashboard.admin.bookings._meta_actions')
        </div>

        <!-- ✅ Assigned Car Card -->
        <div class="card-modern">
          <div class="section-title" style="margin-bottom:8px;">
            <i class="fas fa-car"></i>
            <div>
              <div class="label">Assigned Car</div>
              <div class="muted">Vehicle assigned for this booking</div>
            </div>
          </div>

          @if(!empty($assignment) && !empty($assignment->vehicle))
              @php
                  $v = $assignment->vehicle;
                  $driver = $v->drivers->first() ?? null;
              @endphp
              <div class="kv">
                <div class="k">Vehicle</div>
                <div class="v">
                  {{ trim(($v->make ?? '') . ' ' . ($v->model ?? '')) ?: '—' }}
                  @if(!empty($v->plate_number)) — {{ $v->plate_number }} @endif
                </div>

                <div class="k">Color</div>
                <div class="v">{{ $v->color ?? '—' }}</div>

                <div class="k">Capacity</div>
                <div class="v">{{ $v->capacity ?? '—' }}</div>

                <div class="k">Driver</div>
                <div class="v">{{ $driver->name ?? '—' }} {{ $driver && $driver->phone ? '· '.$driver->phone : '' }}</div>

                <div class="k">Assigned At</div>
                <div class="v">
                  {{ optional($assignment->start_at ?? $assignment->created_at)->format('d M Y, H:i') ?? '—' }}
                </div>
              </div>
          @else
            <div class="muted">No car assigned yet.</div>
          @endif
        </div>

      </div>
    </aside>

  </div>
</div>

@include('dashboard.admin.bookings._invoice_modal')
@include('dashboard.admin.bookings._permission_modal')
@include('dashboard.admin.bookings._transition_modal')
@include('dashboard.admin.bookings._notify_modal')
@endsection
