<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KehadiranSeeder extends Seeder
{
    public function run(): void
    {
        $kehadiran = [];

        for ($i = 1; $i <= 4; $i++) { // 4 karyawan
            for ($d = 1; $d <= 30; $d++) { // untuk 1 bulan
                $kehadiran[] = [
                    'karyawan_id' => $i,
                    'tanggal' => "2025-10-" . str_pad($d, 2, '0', STR_PAD_LEFT),
                    'status_kehadiran' => rand(1, 10) > 1 ? 'Hadir' : 'Sakit',
                    'jam_masuk' => '08:00:00',
                    'jam_keluar' => '16:00:00',
                    'terlambat' => rand(0, 10) > 8, // 20% kemungkinan terlambat
                    'lembur_jam' => rand(0, 5) > 3 ? rand(1, 3) : 0,
                ];
            }
        }

        DB::table('kehadiran')->insert($kehadiran);
    }
}
