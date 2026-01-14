<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Booking;
use App\Observers\BookingObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Redirect unauthenticated users to guard-specific login pages
        \Illuminate\Auth\Middleware\Authenticate::redirectUsing(function ($request) {
            // If request is for driver area
            if ($request->is('driver') || $request->is('driver/*')) {
                return route('driver.login');
            }

            // If request is for lab area
            if ($request->is('lab') || $request->is('lab/*')) {
                return route('lab.login');
            }

            // Default to admin login
            return route('admin.login');
        });

        // Register Booking observer to trigger admin notifications when a booking is created
        try {
            Booking::observe(BookingObserver::class);
        } catch (\Throwable $e) {
            // Log but don't break the application if model/observer binding fails during boot
            \Illuminate\Support\Facades\Log::warning('Failed to register Booking observer: ' . $e->getMessage());
        }

        // One-time permissive seeding for development/testing: create roles/permissions and assign them.
        // This runs only if the permissions table exists and a sentinel file is not present.
        try {
            $flag = storage_path('app/permissions_seeded');
            if (!file_exists($flag)) {
                // Ensure Spatie package and DB tables are available
                if (\Illuminate\Support\Facades\Schema::hasTable('permissions') && class_exists(\Spatie\Permission\Models\Role::class)) {
                    // Load classes
                    $permissionModel = \Spatie\Permission\Models\Permission::class;
                    $roleModel = \Spatie\Permission\Models\Role::class;

                    // If there are no permissions, attempt to run the PermissionSeeder
                    $allPermissions = $permissionModel::pluck('name')->toArray();
                    if (empty($allPermissions)) {
                        try {
                            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => \Database\Seeders\PermissionSeeder::class, '--force' => true]);
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::warning('Permission seeder failed: ' . $e->getMessage());
                        }
                        $allPermissions = $permissionModel::pluck('name')->toArray();
                    }

                    // Ensure roles exist
                    $roles = ['super-admin', 'admin', 'manager'];
                    foreach ($roles as $r) {
                        $roleModel::firstOrCreate(['name' => $r]);
                    }

                    // If permissions exist, assign all permissions to all roles (per user request)
                    if (!empty($allPermissions)) {
                        foreach ($roles as $r) {
                            try {
                                $role = $roleModel::findByName($r);
                                $role->syncPermissions($allPermissions);
                            } catch (\Throwable $e) {
                                \Illuminate\Support\Facades\Log::warning("Failed to assign permissions to role {$r}: " . $e->getMessage());
                            }
                        }
                    }

                    // Assign super-admin role to admin id 4 for testing (if exists)
                    try {
                        $admin = \App\Models\Admin::find(4);
                        if ($admin && method_exists($admin, 'assignRole')) {
                            $admin->assignRole('super-admin');
                        }
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning('Failed to assign super-admin to admin id 4: ' . $e->getMessage());
                    }

                    // Write flag file so this runs only once
                    try {
                        @file_put_contents($flag, (string) time());
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Permissions bootstrap failed: ' . $e->getMessage());
        }
    }
}
