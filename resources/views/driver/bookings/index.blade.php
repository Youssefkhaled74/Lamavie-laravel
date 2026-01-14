@extends('driver.layouts.main')

@section('content')
<div class="panel">
    <div class="panel-header">
        <div>
            <h3 class="page-title mb-1">
                <span class="lang-en">My Bookings</span>
                <span class="lang-ar">حجوزاتي</span>
            </h3>
            <p class="page-sub">
                <span class="lang-en">Filter and open any booking quickly.</span>
                <span class="lang-ar">قم بالتصفية وافتح أي حجز بسرعة.</span>
            </p>
        </div>

        <div class="d-none d-md-flex gap-2">
            <a href="{{ route('driver.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-house me-1"></i>
                <span class="lang-en">Home</span>
                <span class="lang-ar">الرئيسية</span>
            </a>
        </div>
    </div>

    <div class="panel-body">
        <form class="filters mb-3" method="get" action="{{ route('driver.bookings.index') }}">
            <select name="status" class="form-select" style="max-width:220px">
                <option value="">
                    <span class="lang-en">All statuses</span>
                    <span class="lang-ar">كل الحالات</span>
                </option>
                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                <option value="pickup" {{ request('status')=='pickup' ? 'selected' : '' }}>Pickup</option>
                <option value="delivered" {{ request('status')=='delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="canceled" {{ request('status')=='canceled' ? 'selected' : '' }}>Canceled</option>
            </select>

            <input type="search" id="bookingSearch" class="form-control" style="max-width:320px" placeholder="Search order, customer, service...">

            <button class="btn btn-primary">
                <i class="fa-solid fa-filter me-1"></i>
                <span class="lang-en">Filter</span>
                <span class="lang-ar">تصفية</span>
            </button>

            <a href="{{ route('driver.bookings.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-rotate-right me-1"></i>
                <span class="lang-en">Reset</span>
                <span class="lang-ar">إعادة</span>
            </a>
            <div class="ms-auto small text-muted d-flex align-items-center">
                <i class="fa-solid fa-layer-group me-2"></i>
                {{ method_exists($bookings, 'total') ? $bookings->total() : $bookings->count() }}
                <span class="lang-en ms-1">bookings</span>
                <span class="lang-ar ms-1">حجوزات</span>
            </div>
        </form>

        @if($bookings->count())
            <div class="row g-3">
                @foreach($bookings as $booking)
                    @php
                        $svc = data_get($booking, 'service.name');
                        if (is_array($svc)) $svc = $svc[app()->getLocale()] ?? reset($svc);

                        $customer = data_get($booking, 'user.name') ?: data_get($booking, 'user.phone');
                        $status = strtolower($booking->status ?? 'pending');
                        $driverId = auth('driver')->id();
                        $isPickup = ((int)($booking->pickup_driver_id ?? $booking->driver_id ?? 0) === (int)$driverId);
                        $isDelivery = ((int)($booking->delivery_driver_id ?? 0) === (int)$driverId);
                        $isReturned = !empty($booking->driver_returned_at);
                        $total = (float)($booking->total ?? 0);
                        $href = route('driver.bookings.show', $booking->id);
                        $search = strtolower(($booking->order_number ?? $booking->id).' '.($svc ?? '').' '.($customer ?? '').' '.$status);
                    @endphp

                    <div class="col-12 col-lg-6">
                        <div class="text-decoration-none text-reset booking-link" data-href="{{ $href }}" data-search="{{ $search }}">
                            <div class="booking-card p-3 h-100 is-clickable">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="fw-bold fs-5">
                                            #{{ $booking->order_number ?? $booking->id }}
                                        </div>
                                        <div class="booking-meta">
                                            {{ $svc ?? 'Service' }} • {{ $customer ?? '-' }}
                                        </div>
                                    </div>

                                    <span class="chip {{ $status }}">
                                        <i class="fa-solid fa-circle-dot"></i>
                                        {{ ucfirst($status) }}
                                    </span>
                                </div>

                                <div class="mt-3 d-flex justify-content-between align-items-center">
                                    <div class="booking-meta">
                                        <i class="fa-regular fa-clock me-1"></i>
                                        {{ optional($booking->created_at)->diffForHumans() }}
                                    </div>
                                    <div class="booking-meta text-end">
                                        <div style="font-size:.9rem;color:#6b7280">Lab</div>
                                        <div style="font-weight:700">
                                            @if(data_get($booking,'lab.id'))
                                                <a href="{{ route('driver.labs.show', data_get($booking,'lab.id')) }}" target="_blank" rel="noopener">{{ data_get($booking,'lab.name') ?? '—' }}</a>
                                            @else
                                                {{ data_get($booking,'lab.name') ?? '—' }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="booking-actions"><a href="{{ $href }}" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation();">
                                        <span class="lang-en">Open</span>
                                        <span class="lang-ar">فتح</span>
                                        <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </a>

                                    @if($status === 'delivered')
                                        <a href="{{ $href }}?openInvoice=1" class="btn btn-sm btn-primary" onclick="event.stopPropagation();">
                                            <i class="fa-solid fa-file-invoice me-1"></i>
                                            <span class="lang-en">Invoice</span>
                                            <span class="lang-ar">فاتورة</span>
                                        </a>
                                    @endif
                                    {{-- Badges for assignment/returned --}}
                                    @if($isPickup || $isDelivery || $isReturned)
                                        <div class="mt-2">
                                            @if($isPickup)
                                                <span class="badge bg-primary me-1">
                                                    <span class="lang-en">Pickup</span>
                                                    <span class="lang-ar">استلام</span>
                                                </span>
                                            @endif
                                            @if($isDelivery)
                                                <span class="badge bg-success me-1">
                                                    <span class="lang-en">Delivery</span>
                                                    <span class="lang-ar">تسليم</span>
                                                </span>
                                            @endif
                                            @if($isReturned)
                                                <span class="badge bg-secondary">
                                                    <span class="lang-en">Returned</span>
                                                    <span class="lang-ar">تم التسليم</span>
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div id="bookingNoResults" class="alert alert-info mt-3 d-none mb-0">
                <i class="fa-solid fa-magnifying-glass me-1"></i>
                <span class="lang-en">No results on this page.</span>
                <span class="lang-ar">لا توجد نتائج في هذه الصفحة.</span>
            </div>

            <div class="mt-3">
                {{ $bookings->links('vendor.pagination.bootstrap-5') }}
            </div>
        @else
            <div class="alert alert-info mb-0">
                <i class="fa-solid fa-circle-info me-1"></i>
                <span class="lang-en">No bookings found.</span>
                <span class="lang-ar">لا توجد حجوزات.</span>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    const search = document.getElementById('bookingSearch');
    const wrappers = Array.from(document.querySelectorAll('.booking-link[data-href]'));
    const noResults = document.getElementById('bookingNoResults');

    function applyFilter(){
        const q = (search?.value || '').trim().toLowerCase();
        let visible = 0;
        wrappers.forEach(w => {
            const hay = (w.dataset.search || '').toLowerCase();
            const show = !q || hay.includes(q);
            const col = w.closest('.col-12') || w.parentElement;
            if(col) col.classList.toggle('d-none', !show);
            if(show) visible++;
        });
        if(noResults){
            noResults.classList.toggle('d-none', visible !== 0 || !q);
        }
    }

    if(search){
        search.addEventListener('input', applyFilter);
    }

    wrappers.forEach(w => {
        w.addEventListener('click', (e) => {
            if(e.target.closest('a,button,input,select,textarea,label')) return;
            const href = w.dataset.href;
            if(href) window.location.href = href;
        });
    });
})();
</script>
@endpush
