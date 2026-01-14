@extends('driver.layouts.main')

@section('content')
@php
    $toString = function($val) {
        if (is_null($val)) return '-';
        if (is_string($val) || is_numeric($val)) return (string) $val;
        if (is_object($val)) $val = (array) $val;
        if (is_array($val)) {
            $locale = app()->getLocale();
            if (isset($val[$locale]) && (is_string($val[$locale]) || is_numeric($val[$locale]))) return (string) $val[$locale];
            if (isset($val['en']) && (is_string($val['en']) || is_numeric($val['en']))) return (string) $val['en'];
            if (isset($val['ar']) && (is_string($val['ar']) || is_numeric($val['ar']))) return (string) $val['ar'];
            foreach ($val as $v) {
                if (is_string($v) || is_numeric($v)) return (string) $v;
            }
            return json_encode($val, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        return (string) $val;
    };

    $driverName = $toString($driver->name ?? 'Driver');
    $currency = config('app.currency', 'SAR');

    $collection = method_exists($bookings, 'getCollection') ? $bookings->getCollection() : collect($bookings);
    $totalCount = method_exists($bookings, 'total') ? $bookings->total() : $collection->count();

    $active = $collection->whereIn('status', ['pending', 'pickup']);
    $tasks = $active->take(5);
    $recent = $collection->take(6);
@endphp

<div class="panel drv-dash">
    <div class="panel-header">
        <div>
            <h3 class="page-title mb-1">
                <span class="lang-en">Dashboard</span>
                <span class="lang-ar">لوحة التحكم</span>
            </h3>
            <p class="page-sub">
                <span class="lang-en">Welcome back, {{ $driverName }}. Here’s what needs your attention.</span>
                <span class="lang-ar">مرحباً بعودتك، {{ $driverName }}. هذه نظرة سريعة على مهامك.</span>
            </p>
        </div>

        <div class="d-none d-md-flex gap-2">
            <a href="{{ route('driver.bookings.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-receipt me-1"></i>
                <span class="lang-en">Bookings</span>
                <span class="lang-ar">الحجوزات</span>
            </a>
            <a href="{{ route('driver.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-rotate-right me-1"></i>
                <span class="lang-en">Refresh</span>
                <span class="lang-ar">تحديث</span>
            </a>
        </div>
    </div>

    <div class="panel-body">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="booking-card p-3 stat-card h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-ico stat-ico-primary"><i class="fa-solid fa-layer-group"></i></div>
                        <div>
                            <div class="text-muted small">
                                <span class="lang-en">Total bookings</span>
                                <span class="lang-ar">إجمالي الحجوزات</span>
                            </div>
                            <div class="fw-bold fs-3">{{ $totalCount }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="booking-card p-3 stat-card h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-ico stat-ico-warning"><i class="fa-solid fa-truck-fast"></i></div>
                        <div>
                            <div class="text-muted small">
                                <span class="lang-en">Active (this page)</span>
                                <span class="lang-ar">نشطة (في هذه الصفحة)</span>
                            </div>
                            <div class="fw-bold fs-3">{{ $active->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="booking-card p-3 stat-card h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-ico stat-ico-success"><i class="fa-solid fa-sack-dollar"></i></div>
                        <div>
                            <div class="text-muted small">
                                <span class="lang-en">Revenue (this page)</span>
                                <span class="lang-ar">الإيراد (في هذه الصفحة)</span>
                            </div>
                            <div class="fw-bold fs-3">
                                {{ number_format((float) $collection->sum(fn($b) => (float) ($b->total ?? 0)), 2) }}
                                <span class="fs-6 fw-semibold text-muted">{{ $currency }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12 col-lg-7">
                <div class="bk-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <div class="fw-bold">
                                <span class="lang-en">Next tasks</span>
                                <span class="lang-ar">المهام القادمة</span>
                            </div>
                            <div class="text-muted small">
                                <span class="lang-en">Pending + pickup bookings shown on this page.</span>
                                <span class="lang-ar">حجوزات قيد الانتظار والاستلام المعروضة في هذه الصفحة.</span>
                            </div>
                        </div>
                        <a href="{{ route('driver.bookings.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-secondary">
                            <span class="lang-en">View pending</span>
                            <span class="lang-ar">عرض المعلّقة</span>
                        </a>
                    </div>

                    <div class="dash-list mt-3">
                        @if($tasks->count())
                            @foreach($tasks as $t)
                                @php
                                    $svcName = $toString(data_get($t, 'service.name'));
                                    $customer = $toString(data_get($t, 'user.name') ?: data_get($t, 'user.phone'));
                                    $st = strtolower($t->status ?? 'pending');
                                    $driverId = auth('driver')->id();
                                    $isPickup = ((int)($t->pickup_driver_id ?? $t->driver_id ?? 0) === (int)$driverId);
                                    $isDelivery = ((int)($t->delivery_driver_id ?? 0) === (int)$driverId);
                                @endphp
                                <div class="dash-item">
                                    <div class="left">
                                        <div class="fw-bold">#{{ $t->order_number ?? $t->id }}</div>
                                        <div class="text-muted small">{{ $svcName }} • {{ $customer }}</div>
                                        @if($isPickup || $isDelivery)
                                            <div class="mt-1">
                                                @if($isPickup)
                                                    <span class="badge bg-primary">
                                                        <span class="lang-en">Pickup</span>
                                                        <span class="lang-ar">استلام</span>
                                                    </span>
                                                @endif
                                                @if($isDelivery)
                                                    <span class="badge bg-success">
                                                        <span class="lang-en">Delivery</span>
                                                        <span class="lang-ar">تسليم</span>
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="right">
                                        <span class="chip {{ $st }}"><i class="fa-solid fa-circle-dot"></i>{{ ucfirst($st) }}</span>
                                        <a class="btn btn-sm btn-primary" href="{{ route('driver.bookings.show', $t) }}">
                                            <span class="lang-en">Open</span>
                                            <span class="lang-ar">فتح</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="dash-empty">
                                <i class="fa-solid fa-circle-check me-2"></i>
                                <span class="lang-en">No pending tasks on this page.</span>
                                <span class="lang-ar">لا توجد مهام معلّقة في هذه الصفحة.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="bk-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <div class="fw-bold">
                                <span class="lang-en">Recent</span>
                                <span class="lang-ar">الأحدث</span>
                            </div>
                            <div class="text-muted small">
                                <span class="lang-en">Recently shown bookings.</span>
                                <span class="lang-ar">آخر الحجوزات المعروضة.</span>
                            </div>
                        </div>
                        <a href="{{ route('driver.bookings.index') }}" class="btn btn-sm btn-outline-secondary">
                            <span class="lang-en">See all</span>
                            <span class="lang-ar">عرض الكل</span>
                        </a>
                    </div>

                    <div class="dash-list mt-3">
                        @if($recent->count())
                            @foreach($recent as $b)
                                @php
                                    $svcName = $toString(data_get($b, 'service.name'));
                                    $st = strtolower($b->status ?? 'pending');
                                    $driverId = auth('driver')->id();
                                    $isPickup = ((int)($b->pickup_driver_id ?? $b->driver_id ?? 0) === (int)$driverId);
                                    $isDelivery = ((int)($b->delivery_driver_id ?? 0) === (int)$driverId);
                                @endphp
                                <a class="dash-link" href="{{ route('driver.bookings.show', $b) }}">
                                    <div class="left">
                                        <div class="fw-bold">#{{ $b->order_number ?? $b->id }}</div>
                                        <div class="text-muted small">{{ $svcName }}</div>
                                        @if($isPickup || $isDelivery)
                                            <div class="mt-1">
                                                @if($isPickup)
                                                    <span class="badge bg-primary">
                                                        <span class="lang-en">Pickup</span>
                                                        <span class="lang-ar">استلام</span>
                                                    </span>
                                                @endif
                                                @if($isDelivery)
                                                    <span class="badge bg-success">
                                                        <span class="lang-en">Delivery</span>
                                                        <span class="lang-ar">تسليم</span>
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="right">
                                        <span class="chip {{ $st }}"><i class="fa-solid fa-circle-dot"></i>{{ ucfirst($st) }}</span>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <div class="dash-empty">
                                <i class="fa-solid fa-inbox me-2"></i>
                                <span class="lang-en">No bookings yet.</span>
                                <span class="lang-ar">لا توجد حجوزات بعد.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $bookings->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

