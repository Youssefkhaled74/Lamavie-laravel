<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserUniqueCodeSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Assigning unique codes to users...');
        User::chunk(100, function($users) {
            foreach ($users as $user) {
                if (empty($user->unique_code)) {
                    $user->unique_code = User::generateUniqueCode();
                    $user->save();
                }
            }
        });
        $this->command->info('Done.');
    }
}
