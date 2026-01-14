@extends('dashboard.admin.layouts.main')

@section('content')
<div class="container-fluid">

    <div class="page-head mb-3 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-0 fw-bold text-primary">
                <i class="fas fa-receipt me-2"></i>Bookings for {{ $user->name }}
            </h3>
            <div class="text-muted small">View all bookings and open any order details.</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-primary">
                <i class="fas fa-user me-1"></i>Profile
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Users
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <div class="fw-semibold">
                <i class="fas fa-list me-2 text-primary"></i>Orders
            </div>
            <span class="badge bg-light text-dark border">
                Total: {{ $bookings->count() }}
            </span>
        </div>

        <div class="card-body p-0">
            @if($bookings->isEmpty())
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-info-circle me-2"></i>This user has no bookings.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-nowrap">Order #</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Driver</th>
                                <th>Lab</th>
                                <th class="text-nowrap">Created</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $b)
                                @php
                                    $locale = app()->getLocale();
                                    $serviceName = '-';
                                    if ($b->service) {
                                        $name = $b->service->name;
                                        $serviceName = is_array($name) ? ($name[$locale] ?? $name['en'] ?? '-') : ($name ?? '-');
                                    } elseif ($b->serviceCategory) {
                                        $name = $b->serviceCategory->name;
                                        $serviceName = is_array($name) ? ($name[$locale] ?? $name['en'] ?? '-') : ($name ?? '-');
                                    }

                                    $driverName = optional($b->driver)->name ?? '-';
                                    if (is_array($driverName)) $driverName = $driverName[$locale] ?? $driverName['en'] ?? '-';

                                    $labName = optional($b->lab)->name ?? '-';
                                    if (is_array($labName)) $labName = $labName[$locale] ?? $labName['en'] ?? '-';

                                    $status = strtolower($b->status ?? '');
                                    $badge = 'bg-secondary';
                                    if (in_array($status, ['pending','new'])) $badge = 'bg-warning text-dark';
                                    if (in_array($status, ['confirmed','accepted','processing','pickup'])) $badge = 'bg-info text-dark';
                                    if (in_array($status, ['delivered','completed','done'])) $badge = 'bg-success';
                                    if (in_array($status, ['canceled','cancelled','rejected','failed'])) $badge = 'bg-danger';
                                @endphp

                                <tr>
                                    <td class="fw-semibold">{{ $b->order_number ?? '#'.$b->id }}</td>

                                    <td>
                                        <div class="fw-semibold">{{ $serviceName }}</div>
                                        <div class="text-muted small">ID: {{ $b->id }}</div>
                                    </td>

                                    <td>
                                        <span class="badge rounded-pill {{ $badge }}">
                                            {{ ucfirst($b->status) }}
                                        </span>
                                    </td>

                                    <td class="fw-semibold">{{ $b->total }}</td>

                                    <td>{{ $driverName }}</td>
                                    <td>{{ $labName }}</td>

                                    <td class="text-muted small">{{ $b->created_at?->format('Y-m-d H:i') }}</td>

                                    <td class="text-end">
                                        <a href="{{ route('admin.bookings.show', $b->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-external-link-alt me-1"></i>Open
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
