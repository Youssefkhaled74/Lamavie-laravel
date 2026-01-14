<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ExportRouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Scan app/Exports for exporter classes and register admin export routes.
        $exportFiles = glob(app_path('Exports') . '/*.php');
        if (empty($exportFiles)) {
            return;
        }

        foreach ($exportFiles as $file) {
            $base = pathinfo($file, PATHINFO_FILENAME);
            $exportClass = "App\\Exports\\{$base}";

            if (!class_exists($exportClass)) {
                continue;
            }

            $resource = Str::kebab(Str::replaceLast('Export', '', $base));

            // Register route inside 'admin' prefix and 'admin.' name group
            Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () use ($resource, $exportClass) {
                if (!Route::has('admin.' . $resource . '.export')) {
                    Route::get($resource . '/export', function (Request $request) use ($exportClass, $resource) {
                        return Excel::download(new $exportClass($request), $resource . '-' . now()->format('Ymd-His') . '.xlsx');
                    })->name($resource . '.export')->middleware(['web','auth:admin', "permission:{$resource}.export"]);
                }
            });
        }
    }
}
