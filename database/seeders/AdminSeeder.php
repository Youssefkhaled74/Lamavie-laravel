<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admins = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@lamavie.com',
                'password' => Hash::make('password123'),
                'fcm_token' => 'fcm_token_john_123456',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@lamavie.com',
                'password' => Hash::make('password123'),
                'fcm_token' => 'fcm_token_jane_789012',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@lamavie.com',
                'password' => Hash::make('password123'),
                'fcm_token' => 'fcm_token_admin_345678',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($admins as $admin) {
            Admin::updateOrCreate([
                'email' => $admin['email'],
            ], $admin);
        }

        // Assign `super-admin` to the first admin created (best-effort)
        $first = Admin::orderBy('id')->first();
        if ($first) {
            $role = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'admin']);
            try {
                $first->assignRole($role->name);
            } catch (\Throwable $e) {
                // ignore if roles/permissions package not yet installed
            }
        }
    }
}