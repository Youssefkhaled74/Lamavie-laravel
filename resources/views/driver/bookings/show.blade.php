@extends('driver.layouts.main')

@section('page_title')
    <span class="lang-en">Booking Details</span>
    <span class="lang-ar">تفاصيل الحجز</span>
@endsection

@section('content')
@php
    // -------- Helpers ----------
    $toString = function($val) {
        if (is_null($val)) return '-';
        if (is_string($val) || is_numeric($val)) return (string)$val;

        if (is_object($val)) {
            if (method_exists($val, '__toString')) return (string)$val;
            $val = (array)$val;
        }

        if (is_array($val)) {
            $locale = app()->getLocale();
            if (isset($val[$locale]) && (is_string($val[$locale]) || is_numeric($val[$locale]))) {
                return (string)$val[$locale];
            }
            // find first scalar
            $stack = $val;
            while(!empty($stack)){
                $item = array_shift($stack);
                if (is_string($item) || is_numeric($item)) return (string)$item;
                if (is_array($item) || is_object($item)) $stack = array_merge($stack, (array)$item);
            }
            return json_encode($val, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }

        return (string)$val;
    };

    $serviceName = data_get($booking, 'service.name');
    $serviceName = $toString($serviceName);

    $customerName = data_get($booking, 'user.name') ?: data_get($booking, 'user.phone');
    $customerName = $toString($customerName);

    $payload = [];
    if (is_array($booking->payload)) {
        $payload = $booking->payload;
    } elseif (is_string($booking->payload) && $booking->payload !== '') {
        $decoded = json_decode($booking->payload, true);
        $payload = is_array($decoded) ? $decoded : [];
    } elseif (is_array($booking->payload_data ?? null)) {
        $payload = $booking->payload_data;
    } elseif (is_string($booking->payload_data ?? '') && $booking->payload_data) {
        $decoded = json_decode($booking->payload_data, true);
        $payload = is_array($decoded) ? $decoded : [];
    }

    $status = strtolower($booking->status ?? 'pending');

    $statusMeta = match($status) {
        'pending'   => ['cls' => 'pending',   'icon' => 'fa-hourglass-half'],
        'pickup'    => ['cls' => 'pickup',    'icon' => 'fa-truck-fast'],
        'delivered' => ['cls' => 'delivered', 'icon' => 'fa-circle-check'],
        'canceled'  => ['cls' => 'canceled',  'icon' => 'fa-circle-xmark'],
        default     => ['cls' => 'pending',   'icon' => 'fa-hourglass-half'],
    };

    $paymentName = data_get($booking, 'paymentMethod.name');
    $paymentName = $toString($paymentName);

    $labName = data_get($booking, 'lab.name') ?? '—';
    $labPhone = data_get($booking, 'lab.phone');
@endphp

<link rel="stylesheet" href="{{ asset('assets/driver/booking-show-v2.css') }}">

{{-- Header / Breadcrumb --}}
<div class="bk-head">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('driver.bookings.index') }}" class="btn drv-ghost-btn" style="border-radius:16px;">
                <i class="fa-solid fa-arrow-left me-2"></i>
                <span class="lang-en">Back</span>
                <span class="lang-ar">رجوع</span>
            </a>

            <span class="bk-sep">/</span>

            <div class="bk-title">
                <div class="t1">
                    <span class="lang-en">Order</span>
                    <span class="lang-ar">طلب</span>
                    <span class="bk-order">#{{ $booking->order_number ?? $booking->id }}</span>
                </div>
                <div class="t2">
                    {{ optional($booking->created_at)->toDayDateTimeString() }}
                </div>
            </div>
        </div>

        <div class="bk-badges">
            <span class="bk-pill bk-status {{ $statusMeta['cls'] }}">
                <i class="fa-solid {{ $statusMeta['icon'] }} me-2"></i>
                {{ ucfirst($status) }}
            </span>
            <span class="bk-pill">
                <i class="fa-solid fa-sack-dollar me-2"></i>
                {{ number_format((float)($booking->total ?? 0), 2) }} {{ config('app.currency', 'SAR') }}
            </span>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Left: summary + payload + timeline --}}
    <div class="col-lg-8">
        {{-- Summary Card --}}
        <div class="bk-card">
            <div class="bk-card-head">
                <div class="bk-hero-ico">
                    <i class="fa-solid fa-receipt"></i>
                </div>

                <div class="flex-grow-1">
                    <div class="bk-h1">
                        <span class="lang-en">Service</span>
                        <span class="lang-ar">الخدمة</span>
                        <span class="bk-h1-main">{{ $serviceName }}</span>
                    </div>

                    <div class="bk-sub">
                        <span class="lang-en">Customer:</span>
                        <span class="lang-ar">العميل:</span>
                        <strong>{{ $customerName }}</strong>
                    </div>

                    <div class="bk-meta-row">
                        <span class="bk-chip">
                            <i class="fa-solid fa-credit-card"></i>
                            <span class="txt">
                                <span class="lang-en">Payment:</span>
                                <span class="lang-ar">الدفع:</span>
                                {{ $paymentName }}
                            </span>
                        </span>

                        <span class="bk-chip" id="lab_assigned_chip">
                            <i class="fa-solid fa-flask"></i>
                            <span class="txt">
                                <span class="lang-en">Lab:</span>
                                <span class="lang-ar">المعمل:</span>
                                <span id="lab_assigned_info">
                                    @if(isset($booking->lab) && $booking->lab)
                                        <a href="{{ route('driver.labs.show', $booking->lab) }}" target="_blank" rel="noopener">{{ $labName }}</a>{{ $labPhone ? ' — '.$labPhone : '' }}
                                    @else
                                        {{ $labName }}{{ $labPhone ? ' — '.$labPhone : '' }}
                                    @endif
                                </span>
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Payload --}}
            <div class="bk-section">
                <div class="bk-section-title">
                    <i class="fa-solid fa-circle-info"></i>
                    <span class="lang-en">Details</span>
                    <span class="lang-ar">التفاصيل</span>
                </div>

                @if(empty($payload))
                    <div class="bk-empty">
                        <div class="ico"><i class="fa-solid fa-inbox"></i></div>
                        <div>
                            <div class="t1">
                                <span class="lang-en">No extra payload.</span>
                                <span class="lang-ar">لا توجد بيانات إضافية.</span>
                            </div>
                            <div class="t2">
                                <span class="lang-en">If the booking has extra notes, they will appear here.</span>
                                <span class="lang-ar">لو يوجد ملاحظات أو بيانات إضافية ستظهر هنا.</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bk-kv">
                        @foreach($payload as $k => $v)
                            <div class="bk-kv-item">
                                <div class="k">{{ ucwords(str_replace('_',' ', (string)$k)) }}</div>
                                <div class="v">
                                    @php
                                        $vv = $toString($v);
                                    @endphp
                                    {{ $vv }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Timeline --}}
        <div class="bk-card mt-3">
            <div class="bk-section-title mb-2">
                <i class="fa-solid fa-timeline"></i>
                <span class="lang-en">Booking timeline</span>
                <span class="lang-ar">خط سير الحجز</span>
            </div>

            @php
                $steps = [
                    ['key'=>'lab_assigned_at','label_en'=>'Assigned to lab','label_ar'=>'تم التعيين للمعمل','icon'=>'fa-user-tie'],
                    ['key'=>'lab_arrived_at','label_en'=>'Arrived at lab','label_ar'=>'وصل للمعمل','icon'=>'fa-warehouse'],
                    ['key'=>'lab_picked_at','label_en'=>'Picked from lab','label_ar'=>'تم الاستلام من المعمل','icon'=>'fa-box-open'],
                    ['key'=>'driver_collected_at','label_en'=>'Collected from user','label_ar'=>'تم التحصيل من العميل','icon'=>'fa-handshake'],
                    ['key'=>'driver_returned_at','label_en'=>'Returned to user','label_ar'=>'تم التسليم للعميل','icon'=>'fa-people-roof'],
                ];
            @endphp

            <div class="bk-timeline">
                @foreach($steps as $s)
                        @php $ts = $booking->{$s['key']} ?? null; @endphp
                        <div class="bk-tl-item">
                            <div class="dot">
                                <i class="fa-solid {{ $s['icon'] }}"></i>
                            </div>
                            <div class="content">
                                <div class="title">
                                    <span class="lang-en">{{ $s['label_en'] }}</span>
                                    <span class="lang-ar">{{ $s['label_ar'] }}</span>
                                </div>
                                <div class="time text-muted">
                                    @if($s['key'] === 'lab_assigned_at')
                                        @if($ts)
                                            @php
                                                try {
                                                    echo ($ts instanceof \Carbon\Carbon)
                                                        ? $ts->toDayDateTimeString()
                                                        : \Carbon\Carbon::parse($ts)->toDayDateTimeString();
                                                } catch (\Exception $e) { echo e($ts); }
                                            @endphp
                                        @elseif($booking->lab_id || data_get($booking,'lab.id'))
                                            {{-- Show lab name when assigned but timestamp missing --}}
                                            {{ data_get($booking,'lab.name') ?? 'Assigned' }}
                                        @else
                                            —
                                        @endif
                                    @else
                                        @if($ts)
                                            @php
                                                try {
                                                    echo ($ts instanceof \Carbon\Carbon)
                                                        ? $ts->toDayDateTimeString()
                                                        : \Carbon\Carbon::parse($ts)->toDayDateTimeString();
                                                } catch (\Exception $e) { echo e($ts); }
                                            @endphp
                                        @else
                                            —
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
            </div>
        </div>
    </div>

    {{-- Right: Actions sticky --}}
    <div class="col-lg-4">
        <div class="bk-card bk-actions sticky-lg-top" style="top:90px;">
            <div class="bk-section-title mb-2">
                <i class="fa-solid fa-screwdriver-wrench"></i>
                <span class="lang-en">Actions</span>
                <span class="lang-ar">إجراءات</span>
            </div>



            {{-- Lab operations --}}
            <div class="bk-block">
                <div class="bk-block-head">
                    <div class="t1">
                        <span class="lang-en">Lab operations</span>
                        <span class="lang-ar">عمليات المعمل</span>
                    </div>
                    <div class="t2">
                        <span class="lang-en">Assign / mark steps</span>
                        <span class="lang-ar">تعيين / تحديد المراحل</span>
                    </div>
                </div>

                @if(($booking->service_id ?? null) !== 3)
                    @php
                        $driverId = $driver->id ?? null;
                        $isPickup = ((int)($booking->pickup_driver_id ?? $booking->driver_id ?? 0) === (int)$driverId);
                        $isDelivery = ((int)($booking->delivery_driver_id ?? 0) === (int)$driverId);
                        $isBoth = $isPickup && $isDelivery;
                    @endphp

                    @if($isPickup || $isDelivery)
                        @php
                            // enforce ordered steps: Collected -> Arrived -> Picked -> Returned
                            $canCollect = (!$booking->driver_collected_at) && ($isPickup || $isBoth);
                            $canArrive = ($isPickup) && (!$booking->lab_arrived_at) && (bool)$booking->driver_collected_at;
                            $canPick = ($isDelivery) && (!$booking->lab_picked_at) && (bool)$booking->lab_arrived_at && strtolower($booking->status ?? 'pending') !== 'delivered';
                        @endphp

                        <div class="d-flex gap-2 mt-2">
                            {{-- Arrived: shown to pickup (or both) only; disabled until collected --}}
                            @if($isPickup)
                                <form method="post" action="{{ route('driver.bookings.arrivedAtLab', $booking->id) }}" class="ajax-action flex-grow-1" data-confirm="1" data-confirm-title="Confirm Arrived" data-confirm-body="Are you sure you want to mark this order as Arrived at lab?">
                                    @csrf
                                    <input type="hidden" name="lab_id" id="arrive_lab_id" value="{{ $booking->lab_id }}">
                                    <button type="submit" class="btn btn-secondary w-100" @if(!$canArrive) disabled title="Collect from user first" @endif>
                                        <i class="fa-solid fa-warehouse me-2"></i>
                                        <span class="lang-en">Arrived</span>
                                        <span class="lang-ar">وصل</span>
                                    </button>
                                </form>
                            @endif

                            {{-- Picked: shown to delivery (or both) only; disabled until arrived --}}
                            @if($isDelivery)
                                <form method="post" action="{{ route('driver.bookings.pickedFromLab', $booking->id) }}" class="ajax-action flex-grow-1" data-confirm="1" data-confirm-title="Confirm Picked" data-confirm-body="Are you sure you have picked the items from the lab?">
                                    @csrf
                                    <input type="hidden" name="lab_id" id="picked_lab_id" value="{{ $booking->lab_id }}">
                                    <button type="submit" class="btn btn-success w-100" @if(!$canPick) disabled title="Arrive at lab first" @endif>
                                        <i class="fa-solid fa-box-open me-2"></i>
                                        <span class="lang-en">Picked</span>
                                        <span class="lang-ar">تم الالتقاط</span>
                                    </button>
                                </form>
                            @endif
                        </div>

                        <button type="button" class="btn drv-ghost-btn w-100 mt-2" id="openInvoiceBtn" style="border-radius:16px;">
                            <i class="fa-solid fa-file-invoice me-2"></i>
                            <span class="lang-en">Invoice</span>
                            <span class="lang-ar">فاتورة</span>
                        </button>
                    @else
                        <div class="text-muted small">
                            <span class="lang-en">Lab operations are not assigned to you.</span>
                            <span class="lang-ar">عمليات المعمل غير مخصصة لك.</span>
                        </div>
                    @endif
                @else
                    <div class="text-muted small">
                        <span class="lang-en">Lab operations are not applicable for this service.</span>
                        <span class="lang-ar">عمليات المعمل غير متاحة لهذه الخدمة.</span>
                    </div>
                @endif
            </div>

            <hr class="bk-hr">

            {{-- Collection --}}
            <div class="bk-block">
                <div class="bk-block-head">
                    <div class="t1">
                        <span class="lang-en">Collection</span>
                        <span class="lang-ar">التحصيل</span>
                    </div>
                    <div class="t2">
                        <span class="lang-en">Mark collected from customer</span>
                        <span class="lang-ar">تحديد تم التحصيل من العميل</span>
                    </div>
                </div>

                @php
                    // reuse assignment flags from above; fallback if not present
                    if(!isset($isPickup)){
                        $driverId = $driver->id ?? null;
                        $isPickup = ((int)($booking->pickup_driver_id ?? $booking->driver_id ?? 0) === (int)$driverId);
                        $isDelivery = ((int)($booking->delivery_driver_id ?? 0) === (int)$driverId);
                        $isBoth = $isPickup && $isDelivery;
                    }

                    // enforce order prerequisites for collection/return
                    $canReturn = (!$booking->driver_returned_at) && ($isDelivery || $isBoth) && (bool)$booking->lab_picked_at;
                @endphp

                @if(!$booking->driver_collected_at && ($isPickup || $isBoth))
                    <form method="post" action="{{ route('driver.bookings.driverCollected', $booking->id) }}" class="ajax-action" data-confirm="1" data-confirm-title="Confirm Collected" data-confirm-body="Confirm you collected payment/items from the user.">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fa-solid fa-handshake me-2"></i>
                            <span class="lang-en">Mark Collected</span>
                            <span class="lang-ar">تم التحصيل</span>
                        </button>
                    </form>
                @else
                    <div class="bk-done">
                        <i class="fa-solid fa-circle-check"></i>
                        <div>
                            <div class="t1">
                                <span class="lang-en">Collected</span>
                                <span class="lang-ar">تم التحصيل</span>
                            </div>
                            <div class="t2">
                                @php
                                    try {
                                        echo ($booking->driver_collected_at instanceof \Carbon\Carbon)
                                            ? $booking->driver_collected_at->toDayDateTimeString()
                                            : \Carbon\Carbon::parse($booking->driver_collected_at)->toDayDateTimeString();
                                    } catch (\Exception $e) { echo e($booking->driver_collected_at); }
                                @endphp
                            </div>
                        </div>
                    </div>
                    @if($isDelivery || $isBoth)
                        @if(!$booking->driver_returned_at)
                            <form method="post" action="{{ route('driver.bookings.returnedToUser', $booking->id) }}" class="ajax-action mt-2" data-confirm="1" data-confirm-title="Confirm Return" data-confirm-body="Confirm you returned the items to the user.">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100" @if(!$canReturn) disabled title="Pick from lab first" @endif>
                                    <i class="fa-solid fa-people-roof me-2"></i>
                                    <span class="lang-en">Mark Returned to User</span>
                                    <span class="lang-ar">تم التسليم للعميل</span>
                                </button>
                            </form>
                        @else
                            <div class="bk-done mt-2">
                                <i class="fa-solid fa-circle-check"></i>
                                <div>
                                    <div class="t1">
                                        <span class="lang-en">Returned to user</span>
                                        <span class="lang-ar">تم التسليم للعميل</span>
                                    </div>
                                    <div class="t2">
                                        @php
                                            try {
                                                echo ($booking->driver_returned_at instanceof \Carbon\Carbon)
                                                    ? $booking->driver_returned_at->toDayDateTimeString()
                                                    : \Carbon\Carbon::parse($booking->driver_returned_at)->toDayDateTimeString();
                                            } catch (\Exception $e) { echo e($booking->driver_returned_at); }
                                        @endphp
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                @endif
            </div>

        </div>
    </div>
</div>

{{-- Lab Search Modal --}}
<div class="modal fade" id="labSearchModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bk-modal">
      <div class="modal-header">
        <h5 class="modal-title">
            <span class="lang-en">Search labs</span>
            <span class="lang-ar">بحث عن المعامل</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="input-group mb-3">
            <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="text" id="labSearchInput" class="form-control" placeholder="Type lab name or phone">
        </div>

        <div id="labSearchResults" class="bk-results"></div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn drv-ghost-btn" data-bs-dismiss="modal" style="border-radius:16px;">
            <span class="lang-en">Close</span>
            <span class="lang-ar">إغلاق</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Confirmation Modal (custom styled using bk-modal) -->
<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bk-modal confirm-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmActionTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="confirmActionBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn drv-ghost-btn" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmActionBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

        @include('driver.bookings._invoice_modal')

        @endsection

@push('scripts')
<script>
(function(){
    // Hook used by public/assets/driver/driver.js (avoids duplicate listeners + false "Network error")
    window.onDriverAjaxSuccess = function(json){
        const b = json?.booking || {};
        if(b.lab && b.lab.name){
            const info = document.getElementById('lab_assigned_info');
            if(info) info.textContent = b.lab.name + (b.lab.phone ? ' - ' + b.lab.phone : '');
        }
    };

    // keep hidden inputs synced with desktop select
    const labSelect = document.getElementById('labSelect');
    const arriveInput = document.getElementById('arrive_lab_id');
    const pickedInput = document.getElementById('picked_lab_id');

    function syncHidden(v){
        if(arriveInput) arriveInput.value = v || '';
        if(pickedInput) pickedInput.value = v || '';
    }

    if(labSelect){
        syncHidden(labSelect.value);
        labSelect.addEventListener('change', () => syncHidden(labSelect.value));
    }

    // AJAX forms are handled globally by public/assets/driver/driver.js
    if(false){
    document.querySelectorAll('form.ajax-action').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const f = e.currentTarget;
            const data = new FormData(f);

            try{
                const res = await fetch(f.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: data,
                    credentials: 'same-origin'
                });
                const json = await res.json();

                if(json?.success){
                    window.driverToast?.(json.message || 'Updated', 'success');

                    const b = json.booking || {};

                    // update lab chip
                    if(b.lab && b.lab.name){
                        const info = document.getElementById('lab_assigned_info');
                        if(info) info.textContent = b.lab.name + (b.lab.phone ? ' — ' + b.lab.phone : '');
                    }

                } else {
                    window.driverToast?.(json?.message || 'Action failed', 'error');
                }
            }catch(err){
                window.driverToast?.('Network error', 'error');
            }
        });
    });
    }

    // Modal open: invoice + lab search elements
    const openInvoice = document.getElementById('openInvoiceBtn');
    const invoiceModalEl = document.getElementById('invoiceModal');

    const labModalEl = document.getElementById('labSearchModal');
    const input = document.getElementById('labSearchInput');
    const results = document.getElementById('labSearchResults');

    if(openInvoice && invoiceModalEl){
        openInvoice.addEventListener('click', () => {
            bootstrap.Modal.getOrCreateInstance(invoiceModalEl).show();
        });
    }

    // Auto-open invoice from list page (e.g. ?openInvoice=1)
    try{
        const params = new URLSearchParams(window.location.search);
        if(invoiceModalEl && (params.get('openInvoice') === '1' || params.get('invoice') === '1')){
            bootstrap.Modal.getOrCreateInstance(invoiceModalEl).show();
        }
    }catch(e){}

    // Print invoice (uses print CSS in public/assets/driver/driver.css)
    const printBtn = document.getElementById('invoicePrintBtn');
    if(printBtn && invoiceModalEl){
        printBtn.addEventListener('click', () => {
            document.body.classList.add('print-invoice');
            bootstrap.Modal.getOrCreateInstance(invoiceModalEl).show();
            setTimeout(() => window.print(), 50);
        });
        window.addEventListener('afterprint', () => document.body.classList.remove('print-invoice'));
    }

    let t = null;
    if(input){
        input.addEventListener('input', () => {
            clearTimeout(t);
            const q = input.value.trim();
            if(!q){ results.innerHTML = ''; return; }
            t = setTimeout(async () => {
                try{
                    const r = await fetch('/api/labs?query=' + encodeURIComponent(q));
                    const data = await r.json();

                    if(!Array.isArray(data) || data.length === 0){

    // Confirmation modal wiring for forms with data-confirm="1"
    const confirmModalEl = document.getElementById('confirmActionModal');
    const confirmTitle = document.getElementById('confirmActionTitle');
    const confirmBody = document.getElementById('confirmActionBody');
    const confirmBtn = document.getElementById('confirmActionBtn');
    let pendingForm = null;

    function showConfirmForForm(form){
        const title = form.dataset.confirmTitle || 'Confirm action';
        const body = form.dataset.confirmBody || 'Are you sure?';
        if(confirmTitle) confirmTitle.textContent = title;
        if(confirmBody) confirmBody.textContent = body;
        pendingForm = form;
        bootstrap.Modal.getOrCreateInstance(confirmModalEl).show();
    }

    // Intercept submit and show modal when required
    document.querySelectorAll('form.ajax-action[data-confirm="1"]').forEach(f => {
        f.addEventListener('submit', (e) => {
            // if already confirmed, allow submission to continue
            if(f.dataset.__confirmed === '1'){
                // remove marker and allow normal flow
                delete f.dataset.__confirmed;
                return;
            }

            e.preventDefault();
            showConfirmForForm(f);
        });
    });

    if(confirmBtn && confirmModalEl){
        confirmBtn.addEventListener('click', () => {
            if(!pendingForm) return;
            // mark as confirmed so submit handler allows it through
            pendingForm.dataset.__confirmed = '1';
            // hide modal then trigger submit
            bootstrap.Modal.getInstance(confirmModalEl)?.hide();
            // trigger submit event which will be handled by driver.js global handler
            pendingForm.requestSubmit?.() || pendingForm.submit();
            pendingForm = null;
        });
    }

})();
                                <div class="ico"><i class="fa-solid fa-inbox"></i></div>
                                <div>
                                    <div class="t1">No results</div>
                                    <div class="t2">Try another keyword</div>
                                </div>
                            </div>
                        `;
                        return;
                    }

                    results.innerHTML = data.map(l => `
                        <div class="bk-result-item" data-id="${l.id}" data-name="${(l.name||'').replace(/"/g,'&quot;')}" data-phone="${(l.phone||'').replace(/"/g,'&quot;')}">
                            <div class="left">
                                <div class="n">${l.name || ''}</div>
                                <div class="p">${l.phone || ''}</div>
                            </div>
                            <button class="btn btn-primary btn-sm pick">
                                <span class="lang-en">Select</span>
                                <span class="lang-ar">اختيار</span>
                            </button>
                        </div>
                    `).join('');

                    results.querySelectorAll('.bk-result-item .pick').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            const item = e.target.closest('.bk-result-item');
                            const id = item.getAttribute('data-id');
                            const name = item.getAttribute('data-name');
                            const phone = item.getAttribute('data-phone');

                            // set select (desktop)
                            if(labSelect){
                                let opt = labSelect.querySelector(`option[value="${id}"]`);
                                if(!opt){
                                    opt = document.createElement('option');
                                    opt.value = id;
                                    opt.textContent = `${name}${phone ? ' — ' + phone : ''}`;
                                    labSelect.appendChild(opt);
                                }
                                labSelect.value = id;
                                syncHidden(id);
                            }

                            bootstrap.Modal.getInstance(labModalEl)?.hide();
                            window.driverToast?.('Lab selected', 'success');
                        });
                    });

                }catch(err){
                    results.innerHTML = `<div class="text-danger p-2">Search failed</div>`;
                }
            }, 250);
        });
    }
})();
</script>
@endpush
