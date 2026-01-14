@php
    /**
     * Blade snippet examples for menu items protected by roles/permissions.
     * Include where you render the admin sidebar: @include('dashboard.partials.permission_menu_examples')
     */
@endphp

<ul class="nav flex-column">
    {{-- Show to any admin with bookings view permission --}}
    @can('bookings.view')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.bookings.index') }}">Bookings</a>
        </li>
    @endcan

    {{-- Show bookings export only to those with export permission --}}
    @can('bookings.export')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.bookings.export') }}">Export Bookings</a>
        </li>
    @endcan

    {{-- Show user management only to super-admins --}}
    @role('super-admin')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.admins.index') }}">Admin Users</a>
        </li>
    @endrole

    {{-- Show settings only to super-admins or users with manage settings permission --}}
    @if(auth()->guard('admin')->user()?->hasRole('super-admin') || auth()->guard('admin')->user()?->can('manage settings'))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.settings.index') }}">Settings</a>
        </li>
    @endif

    {{-- Example of checking multiple roles --}}
    @hasanyrole('manager|admin')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.reports') }}">Reports</a>
        </li>
    @endhasanyrole
</ul>
