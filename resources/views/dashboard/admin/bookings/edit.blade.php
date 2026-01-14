@extends('dashboard.admin.layouts.main')

@section('content')
<style>
    :root{
        --primary:#0d6efd;
        --muted:#6b7280;
        --surface:#ffffff;
        --card-border: rgba(2,6,23,0.06);
        --accent:#eef6ff;
        --success:#10b981;
        --danger:#ef4444;
    }
    .edit-page { font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; color: #0f172a; }
    .edit-grid { display:grid; grid-template-columns: 1fr 360px; gap:24px; align-items:start; }
    @media (max-width:991px){ .edit-grid{ grid-template-columns:1fr; } }
    .panel { background:var(--surface); border-radius:12px; padding:18px; border:1px solid var(--card-border); box-shadow: 0 6px 18px rgba(2,6,23,0.03); }
    .section-title { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
    .h2 { font-size:18px; font-weight:700; margin:0; }
    .muted { color:var(--muted); font-size:13px; }
    label.form-label { font-weight:600; font-size:13px; color:#0f172a; }
    .form-control, .form-select, textarea { border:1px solid rgba(2,6,23,0.06); padding:10px 12px; border-radius:8px; box-shadow:none; }
    .form-control:focus, .form-select:focus, textarea:focus { outline:none; box-shadow:0 6px 14px rgba(13,110,253,0.08); border-color:var(--primary); }
    .field-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .field-row .mb-3 { margin-bottom:0; }
    .actions { display:flex; gap:12px; margin-top:18px; }
    .btn-primary { background:var(--primary); border:none; color:#fff; padding:10px 16px; border-radius:10px; font-weight:700; }
    .btn-secondary-outline { background:#fff; border:1px solid rgba(2,6,23,0.06); padding:10px 14px; border-radius:10px; }
    .status-badge { display:inline-flex; align-items:center; gap:8px; padding:6px 10px; border-radius:999px; font-weight:700; font-size:13px; }
    .badge-pending { background: linear-gradient(90deg,#fff9f0,#fff3cd); color:#8a5800; border:1px solid #ffd885; }
    .badge-pickup { background:var(--accent); color:#035388; }
    .customer-card { display:flex; gap:12px; align-items:flex-start; }
    .avatar-wrapper { width:68px; height:68px; border-radius:50%; overflow:hidden; display:inline-flex; align-items:center; justify-content:center; background:#f8fafc; border:1px solid rgba(2,6,23,0.06); }
    .avatar-img{ width:100%; height:100%; object-fit:cover; display:block }
    .meta-list { margin-top:12px; display:flex; flex-direction:column; gap:10px; }
    .meta-item{ display:flex; gap:10px; align-items:center; }
    .icon-circle{ width:36px;height:36px;border-radius:8px;background:rgba(13,110,253,0.08); display:inline-flex; align-items:center; justify-content:center; color:var(--primary); }
</style>

<div class="edit-page">
    <div class="section-title">
        <h2 class="h2">Edit Booking #{{ $booking->order_number }}</h2>
        <div style="margin-left:auto;" class="muted">Created {{ $booking->created_at->format('d M Y, H:i') }}</div>
    </div>

    <div class="edit-grid">
        <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
            @csrf @method('PUT')
            <div class="panel">
                <div class="section-title">
                    <div class="icon-circle"><i class="fas fa-edit"></i></div>
                    <div>
                        <div style="font-weight:700">Order Details</div>
                        <div class="muted">Modify booking fields and notify the customer</div>
                    </div>
                    <div style="margin-left:auto;align-self:center">
                        @php $sc = $booking->status; @endphp
                        <span class="status-badge {{ $sc=='pending' ? 'badge-pending' : 'badge-pickup' }}">{{ ucfirst($booking->status) }}</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach($statuses as $k => $label)
                                    <option value="{{ $k }}" {{ $booking->status === $k ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                @if(empty($isCarWash))
                    <div class="field-row mt-3">
                        <div class="mb-3">
                                <label class="form-label">Pickup Driver</label>
                                <select name="pickup_driver_id" class="form-select">
                                    <option value="">—</option>
                                    @foreach($drivers as $d)
                                        <option value="{{ $d->id }}" {{ ($booking->pickup_driver_id ?? $booking->driver_id) == $d->id ? 'selected' : '' }}>{{ $d->name ?? $d->phone }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Delivery Driver</label>
                                <select name="delivery_driver_id" class="form-select">
                                    <option value="">—</option>
                                    @foreach($drivers as $d)
                                        <option value="{{ $d->id }}" {{ ($booking->delivery_driver_id ?? '') == $d->id ? 'selected' : '' }}>{{ $d->name ?? $d->phone }}</option>
                                    @endforeach
                                </select>
                            </div>

                        <div class="mb-3">
                            <label class="form-label">Lab</label>
                            <select name="lab_id" class="form-select">
                                <option value="">—</option>
                                @foreach($labs as $l)
                                    <option value="{{ $l->id }}" {{ $booking->lab_id == $l->id ? 'selected' : '' }}>{{ $l->name ?? $l->phone }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @else
                    <div class="mb-3">
                        <label class="form-label">Assign Car (Car Wash)</label>
                        <div class="form-text small text-muted">Use the car assignment form below to assign a vehicle and time window.</div>
                    </div>
                @endif

                @php
                    $payload = is_array($booking->payload_data) ? $booking->payload_data : (array)$booking->payload_data;
                    $pickupRaw = $payload['pickup_date'] ?? $booking->pickup_date ?? null;
                    $pickupForInput = '';
                    if ($pickupRaw) {
                        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $pickupRaw)) {
                            [$d,$m,$y] = explode('/', $pickupRaw);
                            $pickupForInput = "{$y}-{$m}-{$d}";
                        } else {
                            try { $pickupForInput = \Carbon\Carbon::parse($pickupRaw)->format('Y-m-d'); } catch (Exception $e) { $pickupForInput = ''; }
                        }
                    }
                @endphp

                <div class="mb-3 mt-3">
                    <label class="form-label">Pickup Date</label>
                    <input type="date" name="pickup_date" class="form-control" value="{{ old('pickup_date', $pickupForInput) }}">
                </div>

                @php
                    // prepare delivery date for input similar to pickup
                    $deliveryRaw = $payload['delivery_date'] ?? $booking->delivery_date ?? null;
                    $deliveryForInput = '';
                    if ($deliveryRaw) {
                        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $deliveryRaw)) {
                            [$d,$m,$y] = explode('/', $deliveryRaw);
                            $deliveryForInput = "{$y}-{$m}-{$d}";
                        } else {
                            try { $deliveryForInput = \Carbon\Carbon::parse($deliveryRaw)->format('Y-m-d'); } catch (Exception $e) { $deliveryForInput = ''; }
                        }
                    }
                @endphp

                <div class="mb-3 mt-3">
                    <label class="form-label">Delivery Date</label>
                    <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date', $deliveryForInput) }}">
                </div>

                <div class="field-row mt-3">
                    <div class="mb-3">
                        <label class="form-label">Pickup Location</label>
                        <input type="text" name="pickup_location" class="form-control" value="{{ old('pickup_location', $payload['pickup_location'] ?? $booking->pickup_location ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Delivery Location</label>
                        <input type="text" name="delivery_location" class="form-control" value="{{ old('delivery_location', $payload['delivery_location'] ?? $booking->delivery_location ?? '') }}">
                    </div>
                </div>

                <div class="field-row mt-3">
                    <div class="mb-3">
                        <label class="form-label">Total ({{ config('app.currency') }})</label>
                        <input type="number" step="0.01" name="total" class="form-control" value="{{ old('total', $booking->total) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method_id" class="form-select">
                            <option value="">—</option>
                            @foreach($paymentMethods as $pm)
                                @php
                                    $pmName = data_get($pm, 'name');
                                    if (is_array($pmName)) { $pmName = $pmName[app()->getLocale()] ?? (count($pmName)? reset($pmName) : null); }
                                @endphp
                                <option value="{{ $pm->id }}" {{ $booking->payment_method_id == $pm->id ? 'selected' : '' }}>{{ $pmName ?? $pm->id }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="4">{{ old('notes', $payload['notes'] ?? $booking->notes ?? '') }}</textarea>
                </div>

                <div class="actions">
                    <button class="btn-primary" type="submit"><i class="fas fa-paper-plane me-2"></i>Save changes and notify user</button>
                    <a href="{{ route('admin.bookings.show', $booking) }}" class="btn-secondary-outline"><i class="fas fa-times me-2"></i>Cancel</a>
                </div>
            </div>
        </form>
                <aside>
                    <div class="panel sidebar-panel">
                        @php
                            $userName = $booking->user->name ?? '';
                            $initials = collect(explode(' ', $userName))->filter()->map(function($p){ return mb_substr($p,0,1); })->take(2)->join('');
                            $photoPath = $booking->user->photo ?? null;
                            $photoUrl = null;
                            if ($photoPath && \Illuminate\Support\Facades\Storage::exists(ltrim($photoPath, '/'))) {
                                $photoUrl = asset('storage/' . ltrim($photoPath, '/'));
                            }
                        @endphp

                        <div class="customer-card">
                            <div class="avatar-wrapper" title="{{ $booking->user->name ?? '' }}">
                                @if($photoUrl)
                                    <img src="{{ $photoUrl }}" alt="avatar" class="avatar-img">
                                @else
                                    <div class="avatar-initials">{{ $initials ?: '?' }}</div>
                                @endif
                            </div>
                            <div style="flex:1">
                                <div style="font-weight:700">{{ $booking->user->name ?? '—' }}</div>
                                <div class="muted">{{ $booking->user->phone ?? '—' }}</div>
                                <div style="margin-top:8px">
                                    @php $sc = $booking->status; @endphp
                                    <span class="status-badge {{ $sc=='pending' ? 'badge-pending' : 'badge-pickup' }}">{{ ucfirst($booking->status) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="meta-list" style="margin-top:14px">
                            <div class="meta-item"><div class="icon-circle"><i class="fas fa-credit-card"></i></div><div class="muted">{{ data_get($booking->paymentMethod,'name')[app()->getLocale()] ?? data_get($booking->paymentMethod,'name') ?? '—' }}</div></div>
                            <div class="meta-item"><div class="icon-circle"><i class="fas fa-calendar-alt"></i></div><div class="muted">Created: {{ $booking->created_at->format('d M Y, H:i') }}</div></div>
                            <div class="meta-item"><div class="icon-circle"><i class="fas fa-clock"></i></div><div class="muted">Updated: {{ $booking->updated_at->format('d M Y, H:i') }}</div></div>
                        </div>

                        {{-- Assign Car form moved below into its own panel for clearer separation (car wash only) --}}

                        <hr style="margin:14px 0;border:none;border-top:1px solid rgba(2,6,23,0.04)">
                        <div>
                            <div style="font-weight:700;margin-bottom:6px">Pickup</div>
                            <div class="muted">{{ $payload['pickup_location'] ?? '—' }}</div>
                            <div class="muted">{{ $payload['pickup_date'] ?? $booking->pickup_date ?? '' }}</div>
                        </div>
                        <div style="margin-top:12px">
                            <div style="font-weight:700;margin-bottom:6px">Delivery</div>
                            <div class="muted">{{ $payload['delivery_location'] ?? $booking->delivery_location ?? '—' }}</div>
                            <div class="muted">{{ $payload['delivery_date'] ?? $booking->delivery_date ?? '' }}</div>
                        </div>
                    </div>
                </aside>

                {{-- Separate Assign Car panel placed below client info for clarity --}}
                @if(!empty($isCarWash))
                    @php $canAssign = in_array($booking->status, ['pending','confirmed']); @endphp
                    <aside style="margin-top:12px">
                        <div class="panel sidebar-panel" aria-labelledby="assign-car-title">
                            <div class="section-title">
                                <div class="icon-circle"><i class="fas fa-car"></i></div>
                                <div>
                                    <h5 id="assign-car-title" class="h2" style="font-size:16px;margin:0">Assign Car &amp; Time Window</h5>
                                    <div class="muted" style="font-size:13px">Assign a vehicle and schedule the service window.</div>
                                </div>
                            </div>

                            <form action="{{ route('admin.bookings.assignCar', $booking) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Select Vehicle</label>
                                    <select name="driver_vehicle_id" class="form-select" {{ $canAssign ? '' : 'disabled' }}>
                                        <option value="">— Select vehicle —</option>
                                        @foreach($driverVehicles as $dv)
                                            @php $selectedVehicle = old('driver_vehicle_id', optional($assignment)->driver_vehicle_id ?? null); @endphp
                                            <option value="{{ $dv->id }}" {{ (string)$selectedVehicle === (string)$dv->id ? 'selected' : '' }}>{{ $dv->driver->name ?? $dv->plate_number ?? $dv->id }}{{ $dv->plate_number ? ' – ' . $dv->plate_number : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="form-label">Start At</label>
                                        @php
                                            $startVal = old('start_at');
                                            if (empty($startVal) && !empty($assignment) && $assignment->start_at) {
                                                $startVal = $assignment->start_at->format('Y-m-d\TH:i');
                                            }
                                        @endphp
                                        <input type="datetime-local" name="start_at" class="form-control" {{ $canAssign ? '' : 'disabled' }} value="{{ $startVal }}">
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="form-label">End At</label>
                                        @php
                                            $endVal = old('end_at');
                                            if (empty($endVal) && !empty($assignment) && $assignment->end_at) {
                                                $endVal = $assignment->end_at->format('Y-m-d\TH:i');
                                            }
                                        @endphp
                                        <input type="datetime-local" name="end_at" class="form-control" {{ $canAssign ? '' : 'disabled' }} value="{{ $endVal }}">
                                    </div>
                                </div>

                                <div>
                                    <button class="btn btn-primary" type="submit" {{ $canAssign ? '' : 'disabled' }}><i class="fas fa-check me-2"></i>Assign Car</button>
                                    @unless($canAssign)
                                        <div class="muted small mt-2">Car assignment is disabled for booking status "{{ $booking->status }}".</div>
                                    @endunless
                                </div>
                            </form>
                        </div>
                    </aside>
                @endif
    </div>
</div>

    @include('dashboard.admin.bookings._permission_modal')
    @include('dashboard.admin.bookings._transition_modal')

    @endsection
