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
        // Staf Absen
        User::create([
            'name' => 'Staf Absen',
            'email' => 'stafabsen@samara.test',
            'password' => Hash::make('password123'),
            'role' => 'staf_absen',
        ]);

        // Manajer
        User::create([
            'name' => 'Manajer',
            'email' => 'manajer@samara.test',
            'password' => Hash::make('password123'),
            'role' => 'manajer',
        ]);

        // Direktur
        User::create([
            'name' => 'Direktur',
            'email' => 'direktur@samara.test',
            'password' => Hash::make('password123'),
            'role' => 'direktur',
        ]);
    }
}
