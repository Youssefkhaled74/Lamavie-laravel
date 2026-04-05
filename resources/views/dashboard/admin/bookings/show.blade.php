@extends('dashboard.admin.layouts.main')

@section('content')
<div class="content-header fade-in">
    <h1 class="fw-bold text-primary">Booking Details</h1>
    <p class="text-muted">View and manage details of the selected booking.</p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<style>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');
.booking-page { font-family: 'Roboto', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; color: #2b2b2b; }
.booking-grid { display: grid; grid-template-columns: 1fr 360px; gap: 20px; align-items: start; }
@media (max-width: 991px) { .booking-grid { grid-template-columns: 1fr; } }
.card-modern { background: #ffffff; border-radius: 12px; border: 1px solid rgba(15,23,42,0.06); box-shadow: 0 6px 18px rgba(2,6,23,0.06); padding: 20px; }
.section-title { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
.h1 { font-size:20px; font-weight:700; color:#0f172a; margin:0; }
.label { font-size:14px; font-weight:600; color:#0f172a; }
.value { font-size:14px; color:#374151; margin-top:4px; }
.items-table { width:100%; border-collapse:collapse; margin-top:8px; }
.items-table th { text-align:left; color:#0f172a; font-weight:600; font-size:13px; padding:12px; background:transparent; border-bottom:1px solid rgba(15,23,42,0.06); }
.items-table td { padding:12px; border-bottom:1px solid rgba(15,23,42,0.04); vertical-align:middle; font-size:14px; color:#374151; }
.items-table .price, .items-table .subtotal { text-align:right; white-space:nowrap; }
.totals { display:flex; justify-content:flex-end; margin-top:16px; gap:12px; align-items:center; font-weight:700; color:#0f172a; }
.badge-status { display:inline-flex; align-items:center; gap:8px; padding:6px 10px; border-radius:999px; font-weight:600; font-size:13px; color:#0f172a; background:#fff3cd; border:1px solid #ffeeba; }
.badge-pending { background: linear-gradient(90deg,#fff9f0,#fff3cd); color:#8a5800; border-color:#ffd885; box-shadow: inset 0 -1px 0 rgba(0,0,0,0.02); }
.badge-pickup { background:#e6f3ff; color:#035388; border-color:#bde0ff; }
.meta-list { display:flex; flex-direction:column; gap:12px; }
.meta-item { display:flex; gap:12px; align-items:flex-start; }
.meta-item i { font-size:18px; color:#007BFF; width:28px; }
.actions { display:flex; gap:10px; flex-wrap:wrap; margin-top:8px; }
.btn-primary { background:#007BFF; color:#fff; border:none; padding:10px 14px; border-radius:8px; font-weight:600; box-shadow:0 6px 14px rgba(0,123,255,0.12); }
.btn-outline { background:#fff; border:1px solid rgba(15,23,42,0.06); color:#0f172a; padding:10px 12px; border-radius:8px; }
.btn-lg { font-size:14px; }
.muted { color:#6b7280; font-size:13px; }
/* Avatar styles */
.avatar-wrapper { width:56px; height:56px; border-radius:50%; overflow:hidden; display:inline-flex; align-items:center; justify-content:center; background:#f8fafc; border:1px solid rgba(2,6,23,0.06); box-shadow:0 6px 14px rgba(2,6,23,0.06); flex-shrink:0; }
.avatar-img { width:100%; height:100%; object-fit:cover; display:block; }
.avatar-initials { font-weight:700; color:#0f172a; font-size:18px; }
</style>

<div class="booking-page">
    <div class="content-header">
        <h1 class="h1">Booking Details</h1>
        <p class="muted">Order #{{ $booking->order_number }} <span class="text-muted">(ID: {{ $booking->id }})</span> — Created {{ $booking->created_at->format('d M Y, H:i') }}</p>
    </div>

    <div class="booking-grid mt-3">
        <!-- Left: Order & Items -->
        <div class="card-modern">
            @unless($isCarWash)
            <div class="section-title">
                <i class="fas fa-shopping-cart" style="color:#007BFF;font-size:20px;"></i>
                <div>
                    <div class="label">Order Summary</div>
                    <div class="muted">Service: {{ $booking->service->name[app()->getLocale()] ?? 'N/A' }}</div>
                    </div>

                {{-- Invoice Modal (shows enhanced order card) --}}
                <div style="margin-left:auto; text-align:right;">
                    <div class="label">Status</div>
                    @php $statusClass = $booking->status === 'pending' ? 'badge-pending' : 'badge-pickup'; @endphp
                    <div class="badge-status {{ $statusClass }}">{{ ucfirst($booking->status) }}</div>
                </div>
            </div>

            <div>
                <table class="items-table" role="table" aria-label="Order items">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th style="width:110px">Quantity</th>
                            <th style="width:140px" class="price">Unit Price ({{ config('app.currency') }})</th>
                            <th style="width:140px" class="subtotal">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $payload = is_array($booking->payload_data) ? $booking->payload_data : (array) $booking->payload_data;
                            $items = $payload['item'] ?? [];
                            $prices = $payload['price'] ?? [];
                            $qtys = $payload['quantity'] ?? [];
                            $itemNames = [0=>'مصبغة',1=>'تنظيف',2=>'بطانية'];
                            $grand = 0;
                        @endphp
                        @for($i=0;$i<count($items);$i++)
                            @php
                                $it = $items[$i] ?? $i;
                                $q = isset($qtys[$i]) ? (int)$qtys[$i] : 0;
                                $p = isset($prices[$i]) ? (float)$prices[$i] : 0;
                                $name = $itemNames[$it] ?? (is_string($it)?$it:"Item #{$i}");
                                $sub = $q * $p;
                                $grand += $sub;
                            @endphp
                            <tr>
                                <td>{{ $name }}</td>
                                <td>{{ $q }}</td>
                                <td class="price">{{ number_format($p,2) }}</td>
                                <td class="subtotal">{{ number_format($sub,2) }}</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>

                <div class="totals">
                    <div class="label">Total</div>
                    <div style="min-width:140px; text-align:right;">{{ number_format($grand,2) }} {{ config('app.currency') }}</div>
                </div>
            </div>

            <hr style="border:none;border-top:1px solid rgba(15,23,42,0.06);margin:18px 0;">
            @endunless

            @php
                $payload = is_array($booking->payload_data) ? $booking->payload_data : (array) $booking->payload_data;
            @endphp

            @if(!empty($payload['car_wash_type']) || !empty($payload['number_of_cars']))
                <div class="card-modern mt-3 car-wash-card">
                    <div class="section-title">
                        <i class="fas fa-bath" style="color:#0d6efd;font-size:20px"></i>
                        <div>
                            <div class="label">Car Wash Details</div>
                            <div class="muted">Details submitted by the customer for this car wash booking</div>
                        </div>
                    </div>

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
                                    if ($rawDate) {
                                        // support dd/mm/yyyy coming from mobile
                                        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $rawDate)) {
                                            [$d,$m,$y] = explode('/', $rawDate);
                                            $dateLabel = \Carbon\Carbon::createFromFormat('Y-m-d', "{$y}-{$m}-{$d}")->format('d M Y');
                                        } else {
                                            $dateLabel = \Carbon\Carbon::parse($rawDate)->format('d M Y');
                                        }
                                    }
                                } catch (Exception $e) { $dateLabel = $rawDate ?? '—'; }
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
            @endif

            @unless($isCarWash)
            <div style="display:flex; gap:20px; flex-wrap:wrap;">
                <div>
                    <div class="label">Pickup</div>
                    <div class="value">{{ $payload['pickup_location'] ?? '—' }}</div>
                    <div class="muted">{{ $payload['pickup_date'] ?? '' }} {{ $payload['pickup_time'] ?? '' }}</div>
                </div>
                <div>
                    <div class="label">Delivery</div>
                    <div class="value">{{ $payload['delivery_location'] ?? '—' }}</div>
                </div>
                <div>
                    <div class="label">Clothes Returned</div>
                    <div class="value">{{ $payload['clothes_returned'] ?? '—' }}</div>
                </div>
            </div>
            @endunless
        </div>

        <!-- Right: User & Actions -->
        @unless($isCarWash)
        <aside>
            <div class="card-modern meta-list">
                <div class="section-title">
                    <i class="fas fa-user" style="color:#007BFF;font-size:20px;"></i>
                    @php
                        $userName = $booking->user->name ?? '';
                        $initials = collect(explode(' ', $userName))->filter()->map(function($p){ return mb_substr($p,0,1); })->take(2)->join('');
                        $photoPath = $booking->user->photo ?? null;
                        $photoUrl = null;
                        if ($photoPath && \Illuminate\Support\Facades\Storage::exists(ltrim($photoPath, '/'))) {
                            $photoUrl = asset('storage/' . ltrim($photoPath, '/'));
                        }

                        $paymentProofPath =
                            data_get($payload, 'photo')
                            ?? data_get($payload, 'payment_photo')
                            ?? data_get($payload, 'instapay_photo')
                            ?? data_get($payload, 'instapay_image')
                            ?? data_get($payload, 'receipt_photo')
                            ?? data_get($payload, 'receipt_image');

                        $paymentProofUrl = null;
                        if (is_string($paymentProofPath) && trim($paymentProofPath) !== '') {
                            $candidate = trim($paymentProofPath);
                            if (\Illuminate\Support\Str::startsWith($candidate, ['http://', 'https://'])) {
                                $paymentProofUrl = $candidate;
                            } elseif (\Illuminate\Support\Str::startsWith($candidate, '/storage/')) {
                                $paymentProofUrl = asset(ltrim($candidate, '/'));
                            } elseif (\Illuminate\Support\Str::startsWith($candidate, 'storage/')) {
                                $paymentProofUrl = asset($candidate);
                            } else {
                                $paymentProofUrl = asset('storage/' . ltrim($candidate, '/'));
                            }
                        }
                    @endphp
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="avatar-wrapper" title="{{ $booking->user->name ?? '' }}">
                            @if($photoUrl)
                                <img src="{{ $photoUrl }}" alt="avatar" class="avatar-img">
                            @else
                                <div class="avatar-initials">{{ $initials ?: '?' }}</div>
                            @endif
                        </div>
                        <div>
                            <div class="label">Customer</div>
                            <div class="value">{{ $booking->user->name ?? 'N/A' }}</div>
                            <div class="muted">{{ $booking->user->phone ?? '' }}</div>
                        </div>
                    </div>
                </div>

                <div class="meta-item">
                    <i class="fas fa-credit-card"></i>
                    <div>
                        <div class="label">Payment</div>
                        <div class="value">{{ $booking->paymentMethod->name[app()->getLocale()] ?? '—' }}</div>
                    </div>
                </div>

                @if(!empty($paymentProofUrl))
                <div class="meta-item">
                    <i class="fas fa-image"></i>
                    <div style="width:100%;">
                        <div class="label">Payment Proof</div>
                        <a href="{{ $paymentProofUrl }}" target="_blank" rel="noopener noreferrer">
                            <img src="{{ $paymentProofUrl }}" alt="Payment proof" style="width:100%; max-height:220px; object-fit:cover; border-radius:12px; border:1px solid rgba(15,23,42,0.08); margin-top:6px;">
                        </a>
                    </div>
                </div>
                @endif

                <div class="meta-item">
                    <i class="fas fa-calendar-alt"></i>
                    <div>
                        <div class="label">Created</div>
                        <div class="value">{{ $booking->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>

                <div class="meta-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <div class="label">Updated</div>
                        <div class="value">{{ $booking->updated_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>

                <div class="actions">
                    <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn-primary btn btn-lg"><i class="fas fa-edit"></i> Update</a>

                    <button class="btn-outline btn btn-lg" data-bs-toggle="modal" data-bs-target="#invoiceModal"><i class="fas fa-file-invoice"></i> Invoice</button>

                    <a href="{{ route('admin.bookings.index') }}" class="btn-outline btn btn-lg"><i class="fas fa-arrow-left"></i> Back</a>
                </div>
            </div>
        </aside>
        @endunless
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        try {
            @if (\Illuminate\Support\Facades\Route::has('admin.notifications.markSeen'))
                const markSeenEndpoint = '{{ route('admin.notifications.markSeen') }}';
            @else
                const markSeenEndpoint = null;
            @endif

            if (!markSeenEndpoint) return;

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch(markSeenEndpoint, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf || ''
                },
                body: JSON.stringify({ booking_id: {{ $booking->id }} })
            }).then(r => {
                // optional: handle response
            }).catch(e => console.debug('mark-seen (single) failed', e));
        } catch (e) { console.debug('mark-seen error', e); }
    });
</script>
@endsection

{{-- Car assignment handled on the edit page only --}}

{{-- Invoice Modal (shows enhanced order card) --}}
<div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="invoiceModalLabel"><i class="fas fa-file-invoice me-2"></i>Invoice - Booking #{{ $booking->order_number }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @php
                    // Reuse same payload variables as above
                    $payload = is_array($booking->payload_data) ? $booking->payload_data : (array) $booking->payload_data;
                    $items = $payload['item'] ?? null;
                    $prices = $payload['price'] ?? null;
                    $qtys = $payload['quantity'] ?? null;
                    $itemNames = [0 => 'مصبغة', 1 => 'تنظيف', 2 => 'بطانية'];
                @endphp

                <div class="mb-3">
                    <label for="invoice-status-select" class="form-label">Select new status to include in invoice (shows old → new)</label>
                    <select id="invoice-status-select" class="form-select">
                        @php $statuses = ['pending' => 'Pending','pickup' => 'Pickup','delivered' => 'Delivered','canceled' => 'Canceled']; @endphp
                        @foreach($statuses as $k => $label)
                            <option value="{{ $k }}" {{ $booking->status === $k ? 'selected' : '' }}>{{ $label }} ({{ $k }})</option>
                        @endforeach
                    </select>
                    <div class="small text-muted mt-1">Old status: <strong>{{ $booking->status }}</strong></div>
                </div>

                @if(is_array($items) && is_array($prices) && is_array($qtys) && count($items) === count($prices) && count($items) === count($qtys))
                    <div class="enhanced-order-card p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 text-primary"><i class="fas fa-shopping-cart me-2"></i>تفاصيل الطلب</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr class="table-light">
                                        <th style="width:48%">الخدمة</th>
                                        <th style="width:16%">الكمية</th>
                                        <th style="width:18%">سعر الوحدة ({{ config('app.currency', 'SAR') }})</th>
                                        <th style="width:18%">المجموع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $grand = 0; @endphp
                                    @foreach($items as $i => $it)
                                        @php
                                            $name = $itemNames[$it] ?? (is_string($it) ? $it : "Item #{$i}");
                                            $q = isset($qtys[$i]) ? (int)$qtys[$i] : 0;
                                            $p = isset($prices[$i]) ? (float)$prices[$i] : 0.0;
                                            $sub = $q * $p;
                                            $grand += $sub;
                                        @endphp
                                        <tr>
                                            <td class="fw-medium">{{ $name }}</td>
                                            <td>{{ $q }}</td>
                                            <td>{{ number_format($p, 2) }}</td>
                                            <td class="fw-semibold">{{ number_format($sub, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">المجموع الكلي</td>
                                        <td class="fw-bold">{{ number_format($grand, 2) }} {{ config('app.currency', 'SAR') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="detail-item mb-2"><i class="fas fa-box-open text-primary me-2"></i><strong>Clothes Returned:</strong> <span class="text-muted">{{ $payload['clothes_returned'] ?? '—' }}</span></div>
                                <div class="detail-item mb-2"><i class="fas fa-map-pin text-primary me-2"></i><strong>Pickup Location:</strong> <span class="text-muted">{{ $payload['pickup_location'] ?? '—' }}</span></div>
                                <div class="detail-item mb-2"><i class="fas fa-map-marker-alt text-primary me-2"></i><strong>Delivery Location:</strong> <span class="text-muted">{{ $payload['delivery_location'] ?? '—' }}</span></div>
                            </div>
                            <div class="col-md-6">
                                @php
                                    $pickupRaw = $payload['pickup_date'] ?? null;
                                    $pickupDate = null;
                                    try {
                                        if ($pickupRaw) $pickupDate = \Carbon\Carbon::parse($pickupRaw);
                                    } catch (Exception $e) {
                                        $pickupDate = null;
                                    }
                                    $today = \Carbon\Carbon::today();
                                @endphp
                                <div class="detail-item mb-2"><i class="fas fa-calendar-alt text-primary me-2"></i><strong>Pickup Date:</strong>
                                    <span class="text-muted">
                                        @if($pickupDate)
                                            {{ $pickupDate->format('d/m/Y') }} @if($pickupDate->isSameDay($today)) <span class="badge bg-info text-dark ms-2">Today</span> @endif
                                        @else
                                            —
                                        @endif
                                    </span>
                                </div>
                                <div class="detail-item mb-2"><i class="fas fa-clock text-primary me-2"></i><strong>Pickup Time:</strong> <span class="text-muted">{{ $payload['pickup_time'] ?? '—' }}</span></div>
                            </div>
                        </div>
                    </div>
                @else
                    <pre class="small text-muted">No structured items to show in invoice. Showing raw payload for inspection:
{{ json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @endif
            </div>
            <div class="modal-footer">
                <a id="downloadInvoiceBtn" class="btn btn-primary" href="#" target="_blank"><i class="fas fa-download me-2"></i>Download PDF</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<style>
    /* Enhanced order card styles */
    .enhanced-order-card {
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 6px 18px rgba(2,6,23,0.06);
        border: 1px solid rgba(2,6,23,0.05);
        font-family: 'Roboto', sans-serif;
        color: #2b2b2b;
    }
    .enhanced-order-card h5 { font-size: 16pt; }
    .enhanced-order-card .table th, .enhanced-order-card .table td { vertical-align: middle; font-size: 14px; }
    .enhanced-order-card .detail-item { display: flex; align-items: center; gap: 8px; font-size: 14px; }
    .enhanced-order-card .detail-item i { width: 20px; text-align: center; }
    .enhanced-order-card .badge { border-radius: 8px; }
    @media (max-width: 767px) {
        .enhanced-order-card { padding: 12px; }
        .enhanced-order-card h5 { font-size: 15px; }
    }

    /* Booking actions cleanup */
    .booking-actions {
        margin-top: 18px !important;
        gap: 12px !important;
        align-items: center;
    }
    .booking-actions .btn {
        min-height: 44px;
        padding: .5rem 1rem;
        border-radius: 8px;
        box-shadow: none;
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        font-size: 0.95rem;
    }
    .booking-actions .btn.btn-outline-secondary {
        background: #fff;
        border-color: rgba(2,6,23,0.08);
    }
    .booking-actions .btn.btn-danger {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    .card {
        overflow: visible; /* prevent clipped shadows from enhanced card */
    }
    .json-container {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        max-height: 400px;
        overflow-y: auto;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }
    .json-container:hover {
        box-shadow: inset 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .json-table {
        font-family: 'Inter', sans-serif;
        font-size: 14px;
    }
    .json-table th {
        background-color: #e2e8f0;
        color: #1e3a8a;
        font-weight: 600;
    }
    .json-table td {
        vertical-align: top;
        word-break: break-word;
    }
    .json-table .nested-toggle {
        cursor: pointer;
        color: #2563eb;
        margin-right: 5px;
    }
    .json-table .nested-toggle:hover {
        color: #1e40af;
    }
    .json-table .nested-content {
        padding-left: 20px;
    }
    .json-container::-webkit-scrollbar {
        width: 8px;
    }
    .json-container::-webkit-scrollbar-track {
        background: #e2e8f0;
        border-radius: 4px;
    }
    .json-container::-webkit-scrollbar-thumb {
        background: #2563eb;
        border-radius: 4px;
    }
    .json-container::-webkit-scrollbar-thumb:hover {
        background: #1e40af;
    }
    .collapse.show .json-container {
        animation: fadeIn 0.5s ease forwards;
    }
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
<script>
    // JSON data from Laravel
    const jsonData = @json($booking->payload_data);

    // Function to render JSON as table rows
    function renderJson(data, parent = '', depth = 0) {
        let html = '';
        for (const [key, value] of Object.entries(data)) {
            const fullKey = parent ? `${parent}.${key}` : key;
            if (value && typeof value === 'object' && !Array.isArray(value)) {
                // Object: create collapsible section
                const collapseId = `json-collapse-${fullKey.replace(/\./g, '-')}`;
                html += `
                    <tr class="json-row" data-key="${fullKey}">
                        <td>
                            <i class="fas fa-chevron-right nested-toggle" data-bs-toggle="collapse" data-bs-target="#${collapseId}"></i>
                            ${'&nbsp;'.repeat(depth * 2)}${key}
                        </td>
                        <td><span class="text-muted">Object</span></td>
                    </tr>
                    <tr class="collapse json-nested" id="${collapseId}">
                        <td colspan="2" class="nested-content">
                            <table class="table table-borderless">
                                <tbody>
                                    ${renderJson(value, fullKey, depth + 1)}
                                </tbody>
                            </table>
                        </td>
                    </tr>
                `;
            } else if (Array.isArray(value)) {
                // Array: create collapsible section
                const collapseId = `json-collapse-${fullKey.replace(/\./g, '-')}`;
                html += `
                    <tr class="json-row" data-key="${fullKey}">
                        <td>
                            <i class="fas fa-chevron-right nested-toggle" data-bs-toggle="collapse" data-bs-target="#${collapseId}"></i>
                            ${'&nbsp;'.repeat(depth * 2)}${key}
                        </td>
                        <td><span class="text-muted">Array[${value.length}]</span></td>
                    </tr>
                    <tr class="collapse json-nested" id="${collapseId}">
                        <td colspan="2" class="nested-content">
                            <table class="table table-borderless">
                                <tbody>
                                    ${value.map((item, index) => renderJson({ [index]: item }, `${fullKey}[${index}]`, depth + 1)).join('')}
                                </tbody>
                            </table>
                        </td>
                    </tr>
                `;
            } else {
                // Primitive value or JSON string
                let displayValue = value;
                try {
                    if (typeof value === 'string' && value.trim().startsWith('{')) {
                        const parsed = JSON.parse(value);
                        displayValue = '<pre style="white-space:pre-wrap;">' + escapeHtml(JSON.stringify(parsed, null, 2)) + '</pre>';
                    } else {
                        displayValue = escapeHtml(value.toString());
                    }
                } catch (e) {
                    displayValue = escapeHtml(value.toString());
                }
                html += `
                    <tr class="json-row" data-key="${fullKey}">
                        <td>${'&nbsp;'.repeat(depth * 2)}${key}</td>
                        <td>${value === null ? '<span class="text-muted">null</span>' : displayValue}</td>
                    </tr>
                `;
            }
        }
        return html;
    }

    // Escape HTML to prevent XSS
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Populate table on load
    document.addEventListener('DOMContentLoaded', () => {
        const tableBody = document.getElementById('json-table-body');
        tableBody.innerHTML = renderJson(jsonData);

        // Toggle chevron icon
        document.querySelectorAll('.nested-toggle').forEach(toggle => {
            toggle.addEventListener('click', function () {
                this.classList.toggle('fa-chevron-right');
                this.classList.toggle('fa-chevron-down');
            });
        });

        // Search functionality
        const searchInput = document.getElementById('json-search');
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.json-row').forEach(row => {
                const key = row.getAttribute('data-key').toLowerCase();
                row.style.display = key.includes(query) ? '' : 'none';
                // Show parent rows if child matches
                if (key.includes(query)) {
                    let parent = row;
                    while (parent = parent.closest('.json-nested')) {
                        parent = parent.previousElementSibling;
                        if (parent && parent.classList.contains('json-row')) {
                            parent.style.display = '';
                        }
                    }
                }
            });
        });

        // Toggle main collapse icon
        const toggleButton = document.querySelector('[data-bs-toggle="collapse"]');
        toggleButton.addEventListener('click', function () {
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-chevron-down');
            icon.classList.toggle('fa-chevron-up');
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const invoiceBase = '{{ route("admin.bookings.invoice", $booking) }}';
        const btn = document.getElementById('downloadInvoiceBtn');
        const select = document.getElementById('invoice-status-select');
        if (btn && select) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const status = select.value;
                const url = invoiceBase + '?status=' + encodeURIComponent(status);
                window.open(url, '_blank');
            });
        }
    });
</script>
@endsection
