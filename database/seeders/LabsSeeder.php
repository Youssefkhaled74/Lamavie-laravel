<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lab;

class LabsSeeder extends Seeder
{
    public function run(): void
    {
        $labs = [
            ['name' => 'Al Salam Lab', 'email' => 'alsalam.lab@example.com', 'phone' => '+201012345678', 'address' => 'Cairo, Nasr City'],
            ['name' => 'Al Noor Lab', 'email' => 'alnoor.lab@example.com', 'phone' => '+201098765432', 'address' => 'Giza, Dokki'],
            ['name' => 'Cairo Central Lab', 'email' => 'central.lab@example.com', 'phone' => '+201011223344', 'address' => 'Cairo, Downtown'],
        ];

        foreach ($labs as $l) {
            Lab::create([
                'name' => $l['name'],
                'email' => $l['email'],
                'phone' => $l['phone'],
                'address' => $l['address'],
                'password' => bcrypt('password'),
            ]);
        }
    }
}
