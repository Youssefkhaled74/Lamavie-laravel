@php
    $currentRoute = \Illuminate\Support\Facades\Route::currentRouteName() ?? '';
    $exportRoute = null;
    if (preg_match('/^admin\.([a-z0-9\-_.]+)\.index$/', $currentRoute, $matches)) {
        $resource = $matches[1];
        $exportRouteName = 'admin.' . $resource . '.export';
        if (\Illuminate\Support\Facades\Route::has($exportRouteName)) {
            $exportRoute = $exportRouteName;
            $permissionName = $resource . '.export';
        }
    }
@endphp

@if($exportRoute && auth('admin')->check() && auth('admin')->user()->can($permissionName))
    <div class="container-fluid">
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route($exportRoute, request()->all()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel me-1"></i> Export
            </a>
        </div>
    </div>
@endif
