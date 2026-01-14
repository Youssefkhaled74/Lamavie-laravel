<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'admin';
        // Resources in the admin dashboard to create permissions for
        $resources = [
            'services','service-types','service-categories','areas','your-items','bookings',
            'home-banners','admins','carpet-material','carpet-size','maintenance-or-cleaning',
            'size-of-stain','type-of-stain','type-of-service-needed','level-of-infestation',
            'presence-of-children-or-pets','payment-methods','drivers','cars-additional-service',
            'place-of-the-cleaning','packages-optional','number-of-cleaners','estimated-hours',
            'settings','labs','type-of-package','frequency','measurement','fabric-type',
            'car-wash-drivers','driver-vehicles','vehicle-timeline','users','reports','logs',
        ];

        $actions = ['view', 'create', 'edit', 'delete', 'export', 'manage'];

        // Generate permissions
        $created = [];
        foreach ($resources as $res) {
            foreach ($actions as $act) {
                $name = $act === 'manage' ? "manage {$res}" : "{$res}.{$act}";
                Permission::firstOrCreate(['name' => $name, 'guard_name' => $guard]);
                $created[] = $name;
            }
        }

        // Additional specific permissions
        $specific = [
            'assign driver',
            'assign car',
            'assign lab',
            'driver actions',
        ];
        foreach ($specific as $s) {
            Permission::firstOrCreate(['name' => $s, 'guard_name' => $guard]);
            $created[] = $s;
        }

        // Define roles
        $super = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => $guard]);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => $guard]);

        // Assign permissions to roles
        $super->givePermissionTo(Permission::all());

        // Admin: most manage permissions except system logs/settings by default
        $adminPerms = Permission::where('name', 'like', '%manage%')
            ->orWhere('name', 'like', '%bookings%')
            ->orWhere('name', 'like', '%drivers%')
            ->get();
        $admin->givePermissionTo($adminPerms);

        // Manager: view + export for most resources and reports
        $managerPerms = Permission::where(function($q){
            $q->where('name', 'like', '%.view')->orWhere('name', 'like', '%.export');
        })->get();
        $manager->givePermissionTo($managerPerms);
    }
}
