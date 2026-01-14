@extends('dashboard.admin.layouts.main')

@section('content')
<div class="content-header fade-in">
    <h1 class="fw-bold text-primary" data-en="Bookings" data-ar="الحجوزات">Bookings</h1>
    <p class="text-muted" data-en="Manage bookings with their associated services, categories, and types." data-ar="إدارة الحجوزات والخدمات والفئات والأنواع المرتبطة بها.">Manage bookings with their associated services, categories, and types.</p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card bookings-card shadow-lg border-0">
    <div class="card-header bg-primary text-white py-3 rounded-top">
        <h5 class="card-title mb-0">
            <i class="fas fa-calendar-check me-2"></i><span data-en="Bookings List" data-ar="قائمة الحجوزات">Bookings List</span>
        </h5>
    </div>
    <div class="card-body p-4">
        <!-- Advanced Filters -->
        <form method="GET" action="{{ route('admin.bookings.index') }}" id="filterForm" class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-secondary btn-sm me-2" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters" aria-expanded="true" aria-controls="advancedFilters" data-en="Advanced Filters" data-ar="فلاتر متقدمة">
                        <i class="fas fa-sliders-h me-1"></i>Advanced Filters
                    </button>
                    <div id="quickStatusChips" class="btn-group" role="group" aria-label="Quick status filters">
                        <button type="button" class="btn btn-chip btn-sm" data-status="" data-en="All" data-ar="الكل">All</button>
                        <button type="button" class="btn btn-chip btn-sm" data-status="pending" data-en="Pending" data-ar="قيد الانتظار">Pending</button>
                        <button type="button" class="btn btn-chip btn-sm" data-status="pickup" data-en="Pickup" data-ar="جاهز للاستلام">Pickup</button>
                        <button type="button" class="btn btn-chip btn-sm" data-status="delivered" data-en="Delivered" data-ar="تم التوصيل">Delivered</button>
                        <button type="button" class="btn btn-chip btn-sm" data-status="canceled" data-en="Canceled" data-ar="ملغاة">Canceled</button>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="text-muted small me-3">
                        Showing <strong id="visibleCount">{{ $bookings->count() }}</strong> of <strong id="totalCount">{{ $bookings->total() }}</strong> results
                    </div>
                    <button id="compactToggle" class="btn btn-outline-secondary btn-sm" type="button" aria-pressed="false" data-en="Compact View" data-ar="عرض مضغوط">
                        <i class="fas fa-compress me-1"></i>Compact View
                    </button>
                </div>
            </div>
            <div class="collapse show" id="advancedFilters">
                <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Name, phone, email...">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Service</label>
                    <select name="service_id" class="form-select">
                        <option value="">All Services</option>
                        @foreach($services as $service)
                            @php
                                $serviceName = is_array($service->name) ? ($service->name['en'] ?? $service->name['ar'] ?? 'N/A') : $service->name;
                            @endphp
                            <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $serviceName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Category</label>
                    <select name="service_category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            @php
                                $categoryName = is_array($category->name) ? ($category->name['en'] ?? $category->name['ar'] ?? 'N/A') : $category->name;
                            @endphp
                            <option value="{{ $category->id }}" {{ request('service_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $categoryName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Type</label>
                    <select name="service_type_id" class="form-select">
                        <option value="">All Types</option>
                        @foreach($types as $type)
                            @php
                                $typeName = is_array($type->name) ? ($type->name['en'] ?? $type->name['ar'] ?? 'N/A') : $type->name;
                            @endphp
                            <option value="{{ $type->id }}" {{ request('service_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $typeName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                </div>
                <div class="row g-3 mt-2">
                    
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="pickup" {{ request('status') == 'pickup' ? 'selected' : '' }}>Pickup</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Canceled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Payment Method</label>
                    <select name="payment_method_id" class="form-select">
                        <option value="">All Payment Methods</option>
                        @foreach($paymentMethods as $method)
                            @php
                                $methodName = is_array($method->name) ? ($method->name['en'] ?? $method->name['ar'] ?? 'N/A') : $method->name;
                            @endphp
                            <option value="{{ $method->id }}" {{ request('payment_method_id') == $method->id ? 'selected' : '' }}>
                                {{ $methodName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="d-flex gap-2 justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary btn-sm" data-en="Reset Filters" data-ar="إعادة تعيين الفلاتر">
                                <i class="fas fa-rotate-left me-1"></i>Reset Filters
                            </a>
                            <a href="{{ route('admin.bookings.trashed') }}" class="btn btn-danger btn-sm" data-en="View Trashed" data-ar="المحذوفات">
                                <i class="fas fa-trash me-2"></i>View Trashed
                            </a>
                        </div>
                        <button type="button" id="exportBtn" class="btn btn-success btn-sm" data-en="Export to Excel" data-ar="تصدير إلى إكسل">
                            <i class="fas fa-file-excel me-1"></i>Export to Excel
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <!-- End Advanced Filters -->

        

        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped" id="bookings-table">
                <thead class="bg-light">
                    <tr>
                        <th data-en="Order #" data-ar="# الطلب">Order #</th>
                        <th data-en="User" data-ar="المستخدم">User</th>
                        <th data-en="Service" data-ar="الخدمة">Service</th>
                        <th data-en="Category" data-ar="الفئة">Category</th>
                        <th data-en="Type" data-ar="النوع">Type</th>
                        <th data-en="Total" data-ar="الإجمالي">Total</th>
                        <th data-en="Payment Method" data-ar="طريقة الدفع">Payment Method</th>
                        <th data-en="Status" data-ar="الحالة">Status</th>
                        <th data-en="Created At" data-ar="تاريخ الإنشاء">Created At</th>
                        <th data-en="Actions" data-ar="الإجراءات">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td>{{ $booking->order_number }}</td>
                            <td>
                                <div class="fw-semibold">{{ $booking->user->name ?? 'N/A' }}</div>
                                <div class="text-muted" style="font-size: 0.85rem;">{{ $booking->user->phone ?? '' }}</div>
                            </td>
                            <td>
                                @php
                                    $serviceName = $booking->service->name ?? 'N/A';
                                    if (is_array($serviceName)) {
                                        $serviceName = $serviceName[app()->getLocale()] ?? $serviceName['en'] ?? $serviceName['ar'] ?? 'N/A';
                                    }
                                @endphp
                                {{ $serviceName }}
                            </td>
                            <td>
                                @php
                                    $categoryName = $booking->serviceCategory->name ?? 'N/A';
                                    if (is_array($categoryName)) {
                                        $categoryName = $categoryName[app()->getLocale()] ?? $categoryName['en'] ?? $categoryName['ar'] ?? 'N/A';
                                    }
                                @endphp
                                {{ $categoryName }}
                            </td>
                            <td>
                                @php
                                    $typeName = $booking->serviceType->name ?? 'N/A';
                                    if (is_array($typeName)) {
                                        $typeName = $typeName[app()->getLocale()] ?? $typeName['en'] ?? $typeName['ar'] ?? 'N/A';
                                    }
                                @endphp
                                {{ $typeName }}
                            </td>
                            <td>{{ number_format($booking->total, 2) }}</td>
                            <td>
                                @php
                                    $paymentMethodName = $booking->paymentMethod->name ?? 'N/A';
                                    if (is_array($paymentMethodName)) {
                                        $paymentMethodName = $paymentMethodName[app()->getLocale()] ?? $paymentMethodName['en'] ?? $paymentMethodName['ar'] ?? 'N/A';
                                    }
                                @endphp
                                {{ $paymentMethodName }}
                            </td>
                            <td>
                                @switch($booking->status)
                                    @case('pending')
                                        <span class="badge bg-warning">Pending</span>
                                        @break
                                    @case('pickup')
                                        <span class="badge bg-info">Pickup</span>
                                        @break
                                    @case('delivered')
                                        <span class="badge bg-success">Delivered</span>
                                        @break
                                    @case('canceled')
                                        <span class="badge bg-danger">Canceled</span>
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $booking->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                <div class="action-buttons d-flex flex-column align-items-center">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="action-button action-view" title="View booking" aria-label="View booking">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </a>
                                    <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" class="d-inline mt-2" onsubmit="return confirm('Are you sure you want to delete this booking?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-button action-delete" title="Delete booking" aria-label="Delete booking">
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <td colspan="10" class="text-center text-muted">No bookings found.</td>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $bookings->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
    /* ===== DESIGN TOKENS & GLOBAL IMPROVEMENTS ===== */
    :root {
        --space-unit: 8px;
        --primary-start: #2563eb;
        --primary-end: #3b82f6;
        --neutral-100: #f9fafb;
        --neutral-300: #e5e7eb;
        --text-default: #111827;
        --muted: #6b7280;
        --badge-pending: #f59e0b;
        --badge-pickup: #0284c7;
        --badge-delivered: #059669;
        --badge-canceled: #dc2626;
        --radius: 8px;
        --font-heading: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
        --font-body: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
    }

    /* ===== GLOBAL IMPROVEMENTS ===== */
    .content-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 3px solid #e5e7eb;
    }
    
    .content-header h1 {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* ===== CARD ENHANCEMENTS ===== */
    .bookings-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .bookings-card:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .card-header.bg-primary {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%) !important;
        padding: 1.5rem !important;
        border: none;
    }
    
    .card-header h5 {
        font-size: 1.25rem;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    /* ===== FILTER FORM STYLING ===== */
    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .form-control, .form-select {
        border: 2px solid var(--neutral-300);
        border-radius: var(--radius);
        padding: 0.625rem 1rem;
        font-size: 0.9375rem;
        transition: all 0.2s ease;
        background-color: #fff;
        color: var(--text-default);
        font-family: var(--font-body);
    }
    
    .form-control:focus, 
    .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }
    
    .form-control::placeholder {
        color: #9ca3af;
    }
    
    /* REMOVED problematic CSS that was overriding native select behavior */
    
    /* Date inputs */
    input[type="date"] {
        position: relative;
        color: #1f2937;
    }
    
    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        filter: invert(0.5);
    }
    
    /* ===== BUTTON ENHANCEMENTS ===== */
    .btn {
        border-radius: 8px;
        font-weight: 600;
        padding: 0.625rem 1.25rem;
        transition: all 0.2s ease;
        text-transform: none;
        letter-spacing: 0.3px;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        border: none;
        color: white;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        border: none;
        color: white;
    }
    
    .btn-success:hover {
        background: linear-gradient(135deg, #047857 0%, #059669 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.4);
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        border: none;
        color: white;
    }
    
    .btn-danger:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.4);
    }
    
    .btn-outline-secondary {
        border: 2px solid #d1d5db;
        color: #6b7280;
        background: white;
    }
    
    .btn-outline-secondary:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
        color: #374151;
        transform: translateY(-1px);
    }
    
    .btn-info {
        background: linear-gradient(135deg, #0284c7 0%, #06b6d4 100%);
        border: none;
    }
    
    .btn-info:hover {
        background: linear-gradient(135deg, #0369a1 0%, #0284c7 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(6, 182, 212, 0.3);
    }
    
    .btn-sm {
        padding: 0.4rem 0.875rem;
        font-size: 0.875rem;
    }
    
    /* ===== TABLE ENHANCEMENTS ===== */
    .table-responsive {
        border-radius: 12px;
        overflow: auto;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
    
    #bookings-table {
        margin-bottom: 0;
        font-size: 0.9375rem;
    }
    
    #bookings-table thead {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    }
    
    #bookings-table thead th {
        color: black;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.78rem;
        letter-spacing: 0.4px;
        padding: 0.6rem 0.5rem;
        border: none;
        white-space: nowrap;
    }
    
    #bookings-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #e5e7eb;
    }
    
    #bookings-table tbody tr:nth-child(even) {
        background-color: #f9fafb;
    }
    
    #bookings-table tbody tr:hover {
        background-color: #eff6ff;
        transform: scale(1.01);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 1;
    }
    
    #bookings-table tbody td {
        padding: 0.6rem 0.5rem;
        vertical-align: middle;
        color: #000 !important;
        font-family: var(--font-body);
        font-size: 0.95rem;
    }
    
    /* User info in table */
    #bookings-table .fw-semibold {
        color: #000 !important;
        font-weight: 600;
        font-family: var(--font-heading);
    }
    
    #bookings-table .text-muted {
        color: #000 !important;
        font-size: 0.8125rem;
    }

    /* Quick filter chips */
    .btn-chip {
        border-radius: 999px;
        border: 1px solid var(--neutral-300);
        background: white;
        color: var(--text-default);
        padding: 0.25rem 0.6rem;
        margin-right: 4px;
    }

    .btn-chip.active {
        background: linear-gradient(90deg, var(--primary-start), var(--primary-end));
        color: white;
        border-color: transparent;
    }
    
    /* Badge enhancements */
    .badge {
        padding: 0.4rem 0.75rem;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.3px;
        border-radius: 6px;
        text-transform: uppercase;
    }
    
    .badge.bg-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%) !important;
        color: #78350f;
    }
    
    .badge.bg-info {
        background: linear-gradient(135deg, #0284c7 0%, #06b6d4 100%) !important;
        color: white;
    }
    
    .badge.bg-success {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
        color: white;
    }
    
    .badge.bg-danger {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%) !important;
        color: white;
    }
    
    /* Action buttons in table */
    #bookings-table .btn-sm {
        padding: 0.375rem 0.625rem;
        font-size: 0.8125rem;
    }

    /* Polished stacked action buttons */
    .action-buttons {
        gap: 0.5rem;
    }

    .action-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 10px;
        color: #fff;
        text-decoration: none;
        border: none;
        box-shadow: 0 4px 10px rgba(2,6,23,0.06);
        transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
        font-size: 1.05rem;
        cursor: pointer;
    }

    .action-button i { pointer-events: none; }

    .action-button.action-view {
        background: linear-gradient(135deg, var(--primary-start), var(--primary-end));
    }

    .action-button.action-delete {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .action-button:hover, .action-button:focus {
        transform: translateY(-3px);
        box-shadow: 0 8px 18px rgba(2,6,23,0.12);
        opacity: 0.98;
    }

    /* Make sure buttons are accessible on small screens */
    @media (max-width: 576px) {
        .action-button { width: 40px; height: 40px; }
    }

    /* Compact mode: denser table */
    #bookings-table.compact thead th {
        padding: 0.45rem 0.45rem;
        font-size: 0.72rem;
    }

    #bookings-table.compact tbody td {
        padding: 0.35rem 0.45rem;
        font-size: 0.86rem;
    }

    #bookings-table.compact tbody tr:hover {
        transform: none;
        box-shadow: none;
    }

    #bookings-table.compact .action-button {
        width: 36px;
        height: 36px;
        font-size: 0.95rem;
        border-radius: 8px;
    }
    
    /* ===== ALERT IMPROVEMENTS ===== */
    .alert {
        border-radius: 12px;
        border: none;
        padding: 1rem 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .alert-success {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
    }
    
    .alert-success .fas {
        color: #059669;
    }
    
    /* ===== PAGINATION ===== */
    .pagination {
        margin-top: 1.5rem;
        gap: 0.5rem;
    }
    
    .pagination .page-link {
        border-radius: 8px;
        border: 2px solid #e5e7eb;
        color: #374151;
        padding: 0.5rem 0.75rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    
    .pagination .page-link:hover {
        background: #eff6ff;
        border-color: #3b82f6;
        color: #1e40af;
        transform: translateY(-2px);
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        border-color: #2563eb;
        color: white;
    }
    
    /* ===== RESPONSIVE DESIGN ===== */
    @media (max-width: 768px) {
        .form-label {
            font-size: 0.8125rem;
        }
        
        .form-control, .form-select {
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        #bookings-table {
            font-size: 0.875rem;
            color: #000
        }
        
        #bookings-table thead th,
        #bookings-table tbody td {
            padding: 0.75rem 0.5rem;
            color: #000 !important;
        }
    }
    
    /* ===== LOADING ANIMATION ===== */
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
    
    .fade-in {
        animation: fadeIn 0.5s ease-out;
    }
    
    /* ===== SCROLLBAR STYLING ===== */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }
    
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    
    .table-responsive::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 4px;
    }
    
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const exportBtn = document.getElementById('exportBtn');
    const filterForm = document.getElementById('filterForm');
    
    if (exportBtn && filterForm) {
        exportBtn.addEventListener('click', function() {
            // Add loading state
            exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Exporting...';
            exportBtn.disabled = true;
            
            // Get all form data
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            
            // Build export URL with filters
            const exportUrl = '{{ route("admin.bookings.export") }}?' + params.toString();
            
            // Open export in new window/download
            window.location.href = exportUrl;
            
            // Reset button after 2 seconds
            setTimeout(() => {
                exportBtn.innerHTML = '<i class="fas fa-file-excel me-1"></i>Export to Excel';
                exportBtn.disabled = false;
            }, 2000);
        });
    }
    
    // Add smooth scroll for long tables
    const tableContainer = document.querySelector('.table-responsive');
    if (tableContainer) {
        tableContainer.style.scrollBehavior = 'smooth';
    }
    
    // Enhance form interactions
    const formInputs = document.querySelectorAll('.form-control, .form-select');
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
            this.parentElement.style.transition = 'transform 0.2s ease';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });

    // Quick status chips behavior and live results counter
    const chipButtons = document.querySelectorAll('#quickStatusChips .btn-chip');
    const statusSelect = document.querySelector('select[name="status"]');
    const visibleCountEl = document.getElementById('visibleCount');
    const totalCountEl = document.getElementById('totalCount');

    function updateVisibleCount() {
        const rows = document.querySelectorAll('#bookings-table tbody tr');
        let visible = 0;
        rows.forEach(r => {
            if (r.style.display !== 'none') visible++;
        });
        if (visibleCountEl) visibleCountEl.textContent = visible;
    }

    // Initialize visible count and total (server-provided)
    updateVisibleCount();

    chipButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const val = this.getAttribute('data-status');
            // toggle active class
            chipButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            // set select value
            if (statusSelect) {
                statusSelect.value = val;
            }
            // For immediate feedback update visible count by filtering current page rows
            // (Note: server-side filtering will run on submit)
            const rows = document.querySelectorAll('#bookings-table tbody tr');
            rows.forEach(r => {
                if (!val) {
                    r.style.display = '';
                    return;
                }
                const badge = r.querySelector('.badge');
                if (!badge) {
                    r.style.display = 'none';
                    return;
                }
                const text = badge.textContent.trim().toLowerCase();
                r.style.display = (text === val) ? '' : 'none';
            });
            updateVisibleCount();
            // submit form to apply server-side filter
            filterForm.submit();
        });
    });

    // Compact view toggle: toggles denser table layout and persists to localStorage
    const compactToggle = document.getElementById('compactToggle');
    const bookingsTable = document.getElementById('bookings-table');
    function setCompact(enabled, persist = true) {
        if (!bookingsTable) return;
        if (enabled) {
            bookingsTable.classList.add('compact');
            compactToggle.classList.add('active');
            compactToggle.setAttribute('aria-pressed', 'true');
        } else {
            bookingsTable.classList.remove('compact');
            compactToggle.classList.remove('active');
            compactToggle.setAttribute('aria-pressed', 'false');
        }
        if (persist) localStorage.setItem('bookings_compact', enabled ? '1' : '0');
    }

    if (compactToggle) {
        compactToggle.addEventListener('click', function() {
            const isActive = bookingsTable && bookingsTable.classList.contains('compact');
            setCompact(!isActive);
        });
        // restore state
        const saved = localStorage.getItem('bookings_compact');
        if (saved === '1') setCompact(true, false);
    }
    // Mark all bookings as seen when admin opens the bookings index
    (function markAllSeen() {
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
                body: JSON.stringify({})
            }).then(r => {
                // ignore response; optionally you can check r.ok
            }).catch(e => console.debug('mark-seen failed', e));
        } catch (e) { console.debug('mark-seen error', e); }
    })();
});
</script>
@endsection