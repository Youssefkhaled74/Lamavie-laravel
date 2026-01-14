@extends('dashboard.admin.layouts.main')

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h3 class="mb-0 fw-bold text-primary"><i class="fas fa-user me-2"></i>User Profile</h3>
            <p class="text-muted small mb-0">View user details and recent activity.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i>Edit Profile
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Users
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">

            {{-- Main profile card --}}
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body p-4 d-flex gap-3 align-items-center">
                    @php $avatarPath = $user->profile_photo ?? $user->photo ?? null; @endphp

                    <div style="width:100px;height:100px;border-radius:16px;overflow:hidden;background:#f8fafc;display:flex;align-items:center;justify-content:center;border:1px solid #e5e7eb;">
                        @if($avatarPath && Storage::disk('public')->exists($avatarPath))
                            <img src="{{ asset('storage/' . $avatarPath) }}" style="width:100%;height:100%;object-fit:cover;" alt="avatar">
                        @else
                            <div style="font-size:34px;font-weight:900;color:#334155">
                                {{ strtoupper(substr($user->name,0,1) ?? '?') }}
                            </div>
                        @endif
                    </div>

                    <div class="flex-grow-1">
                        <h4 class="mb-1 fw-bold">{{ $user->name }}</h4>
                        <div class="text-muted">{{ $user->email }}</div>
                        <div class="mt-2 d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark border rounded-pill px-3">
                                <i class="fas fa-phone me-1"></i>{{ $user->phone ?? '-' }}
                            </span>
                            <span class="badge bg-light text-dark border rounded-pill px-3">
                                <i class="fas fa-calendar me-1"></i>Joined {{ $user->created_at? $user->created_at->format('d M Y') : '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('admin.users.bookings', $user->id) }}" class="btn btn-primary">
                            <i class="fas fa-list me-1"></i>Bookings
                        </a>
                    </div>
                </div>
            </div>

            {{-- Unique code --}}
            <div class="card shadow-sm border-0 rounded-4 mt-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="fw-semibold"><i class="fas fa-qrcode me-2 text-primary"></i>Unique Code</div>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <div class="text-muted small">Code</div>
                            <div class="fs-3 fw-bold" style="letter-spacing:2px;">
                                {{ $user->unique_code ?? '—' }}
                            </div>
                        </div>
                        <a href="{{ route('admin.users.code_pdf', $user) }}" class="btn btn-outline-primary">
                            <i class="fas fa-download me-1"></i>Download Code (PDF)
                        </a>
                    </div>
                </div>
            </div>

            {{-- Recent bookings --}}
            <div class="card shadow-sm border-0 rounded-4 mt-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div class="fw-semibold"><i class="fas fa-history me-2 text-primary"></i>Recent Bookings</div>
                    <a href="{{ route('admin.users.bookings', $user->id) }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th class="text-nowrap">Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->bookings()->latest()->limit(8)->get() as $b)
                                    @php
                                        $serviceName = data_get($b, 'service.name');
                                        if (is_array($serviceName)) {
                                            $serviceName = $serviceName[app()->getLocale()] ?? $serviceName['en'] ?? reset($serviceName);
                                        }

                                        $status = strtolower($b->status ?? '');
                                        $badge = 'bg-secondary';
                                        if (in_array($status, ['pending','new'])) $badge = 'bg-warning text-dark';
                                        if (in_array($status, ['confirmed','accepted','processing','pickup'])) $badge = 'bg-info text-dark';
                                        if (in_array($status, ['delivered','completed','done'])) $badge = 'bg-success';
                                        if (in_array($status, ['canceled','cancelled','rejected','failed'])) $badge = 'bg-danger';
                                    @endphp
                                    <tr>
                                        <td>
                                            <a class="fw-semibold text-decoration-none" href="{{ route('admin.bookings.show', $b->id) }}">
                                                {{ $b->order_number ?? '#'.$b->id }}
                                            </a>
                                        </td>
                                        <td>{{ $serviceName ?? '-' }}</td>
                                        <td><span class="badge rounded-pill {{ $badge }}">{{ ucfirst($b->status) }}</span></td>
                                        <td class="text-muted small">{{ $b->created_at?->format('d M Y') ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right side --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="fw-semibold"><i class="fas fa-address-card me-2 text-primary"></i>Contact & Account</div>
                </div>
                <div class="card-body p-4">
                    <div class="mb-2"><strong>Email:</strong> <span class="text-muted">{{ $user->email }}</span></div>
                    <div class="mb-2"><strong>Phone:</strong> <span class="text-muted">{{ $user->phone ?? '-' }}</span></div>

                    @php
                        $areaName = null;
                        if ($user->relationLoaded('area') || $user->area) {
                            $raw = data_get($user, 'area.name');
                            if (is_array($raw)) $areaName = $raw[app()->getLocale()] ?? $raw['en'] ?? null;
                            else $areaName = $raw;
                        }
                    @endphp

                    <div class="mb-2"><strong>Area:</strong>
                        <span class="badge bg-light text-dark border rounded-pill px-3">{{ $areaName ?? '-' }}</span>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('admin.users.bookings', $user->id) }}" class="btn btn-primary">
                            <i class="fas fa-list me-1"></i>Bookings
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
