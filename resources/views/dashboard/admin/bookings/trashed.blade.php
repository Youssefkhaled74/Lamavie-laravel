@extends('dashboard.admin.layouts.main')

@section('content')
<div class="content-header fade-in">
    <h1 class="fw-bold text-primary">Trashed Bookings</h1>
    <p class="text-muted">Manage deleted bookings and restore if needed.</p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-lg border-0" style="background: linear-gradient(145deg, #ffffff, #f8fafc);">
    <div class="card-header bg-primary text-white py-3 rounded-top d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Trashed Bookings List</h5>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-light btn-sm">
            <i class="fas fa-arrow-left me-2"></i>Back to Active Bookings
        </a>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-striped">
                <thead class="bg-light">
                    <tr>
                        <th>Order #</th>
                        <th>User</th>
                        <th>Service</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Total</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td>{{ $booking->order_number }}</td>
                            <td>{{ $booking->user->name ?? 'N/A' }}</td>
                            <td>{{ $booking->service->name[app()->getLocale()] ?? 'N/A' }}</td>
                            <td>{{ $booking->serviceCategory->name[app()->getLocale()] ?? 'N/A' }}</td>
                            <td>{{ $booking->serviceType->name[app()->getLocale()] ?? 'N/A' }}</td>
                            <td>{{ number_format($booking->total, 2) }}</td>
                            <td>{{ $booking->paymentMethod->name[app()->getLocale()] ?? 'N/A' }}</td>
                            <td>
                                @switch($booking->status)
                                    @case('pending')
                                        <span class="badge bg-warning">{{ ucfirst($booking->status) }}</span>
                                        @break
                                    @case('pickup')
                                        <span class="badge bg-info">{{ ucfirst($booking->status) }}</span>
                                        @break
                                    @case('delivered')
                                        <span class="badge bg-success">{{ ucfirst($booking->status) }}</span>
                                        @break
                                    @case('canceled')
                                        <span class="badge bg-danger">{{ ucfirst($booking->status) }}</span>
                                        @break
                                @endswitch
                            </td>
                            <td>{{ $booking->deleted_at->format('d M Y, H:i') }}</td>
                            <td>
                                <form action="{{ route('admin.bookings.restore', $booking->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to restore this booking?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-undo"></i> Restore
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">No trashed bookings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection