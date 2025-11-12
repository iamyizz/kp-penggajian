<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Jalankan database seeder.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@samara.test',
            'password' => Hash::make('password123'), // ganti jika perlu
            'role' => 'admin',
        ]);

        // Koor Absen
        User::create([
            'name' => 'Koordinator Absen',
            'email' => 'koorabsen@samara.test',
            'password' => Hash::make('password123'),
            'role' => 'koor_absen',
        ]);
    }
}
