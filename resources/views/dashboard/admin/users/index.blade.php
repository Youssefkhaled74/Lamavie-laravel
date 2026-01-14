@extends('dashboard.admin.layouts.main')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0 fw-bold text-primary" data-en="Users" data-ar="المستخدمون">
                <i class="fas fa-users me-2"></i>Users
            </h3>
            <p class="text-muted small mb-0" data-en="Manage application users and view their bookings." data-ar="إدارة مستخدمي التطبيق وعرض حجوزاتهم.">
                Manage application users and view their bookings.
            </p>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2 align-items-center">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search by name or phone"
                   class="form-control" style="width:240px">

            <select name="area_id" class="form-select" style="width:200px">
                <option value="">All areas</option>
                @if(!empty($areas))
                    @foreach($areas as $area)
                        @php
                            $label = is_array($area->name) ? ($area->name[app()->getLocale()] ?? $area->name['en'] ?? reset($area->name)) : $area->name;
                        @endphp
                        <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                @endif
            </select>

            <button class="btn btn-primary" type="submit"><i class="fas fa-search me-1"></i>Search</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="fas fa-undo me-1"></i>Reset</a>
        </form>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <div class="fw-semibold"><i class="fas fa-table me-2 text-primary"></i>Users Table</div>
            <span class="badge bg-light text-dark border">Showing {{ $users->count() }} users</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold" style="width:70px;">#</th>
                            <th class="fw-semibold" style="width:90px;">Photo</th>
                            <th class="fw-semibold">User</th>
                            <th class="fw-semibold">Phone</th>
                            <th class="fw-semibold">Area</th>
                            <th class="fw-semibold">Joined</th>
                            <th class="fw-semibold text-end" style="width:220px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            @php $avatarPath = $user->profile_photo ?? null; @endphp
                            <tr>
                                <td class="text-muted">{{ $user->id }}</td>

                                <td>
                                    @if($avatarPath && Storage::disk('public')->exists($avatarPath))
                                        <img src="{{ asset('storage/' . $avatarPath) }}" alt="avatar" class="rounded-circle"
                                             style="width:44px;height:44px;object-fit:cover;">
                                    @else
                                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center"
                                             style="width:44px;height:44px;color:#334155;font-weight:700">
                                            {{ strtoupper(substr($user->name,0,1) ?? '?') }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    <div class="fw-bold">{{ $user->name }}</div>
                                    <div class="text-muted small">{{ $user->email }}</div>
                                </td>

                                <td class="fw-semibold">{{ $user->phone ?? '-' }}</td>

                                <td>
                                    @php
                                        $areaName = null;
                                        if ($user->relationLoaded('area') || $user->area) {
                                            $raw = data_get($user, 'area.name');
                                            if (is_array($raw)) $areaName = $raw[app()->getLocale()] ?? $raw['en'] ?? null;
                                            else $areaName = $raw;
                                        }
                                    @endphp
                                    <span class="badge bg-light text-dark border rounded-pill px-3">
                                        {{ $areaName ?? '-' }}
                                    </span>
                                </td>

                                <td class="text-muted small">{{ $user->created_at?->format('Y-m-d') ?? '-' }}</td>

                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary px-3">
                                            <i class="fas fa-user me-1"></i>Profile
                                        </a>
                                        <a href="{{ route('admin.users.bookings', $user->id) }}" class="btn btn-sm btn-primary px-3">
                                            <i class="fas fa-list me-1"></i>Bookings
                                        </a>
                                        @can('manage users')
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary px-3">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
