<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KehadiranSeeder extends Seeder
{
    public function run(): void
    {
        $kehadiran = [];

        $months = [
            '2025-10',
            '2025-11',
            '2025-12'
        ];

        for ($i = 1; $i <= 7; $i++) { // 7 karyawan
            foreach ($months as $month) {

                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, substr($month, 5, 2), substr($month, 0, 4));

                for ($d = 1; $d <= $daysInMonth; $d++) {

                    // Probabilitas status kehadiran
                    $rand = rand(1, 100);

                    if ($rand <= 85) {
                        $status = 'Hadir';
                    } elseif ($rand <= 90) {
                        $status = 'Izin';
                    } elseif ($rand <= 97) {
                        $status = 'Sakit';
                    } else {
                        $status = 'Alpa';
                    }

                    $entry = [
                        'karyawan_id' => $i,
                        'tanggal' => $month . "-" . str_pad($d, 2, '0', STR_PAD_LEFT),
                        'status_kehadiran' => $status,
                        'terlambat' => false,
                        'lembur_jam' => 0,
                    ];

                    if ($status === 'Hadir') {
                        $entry['jam_masuk'] = '08:00:00';
                        $entry['jam_keluar'] = '16:00:00';

                        // 15% kemungkinan terlambat
                        $entry['terlambat'] = rand(1, 100) <= 15;

                        // 25% kemungkinan lembur
                        $entry['lembur_jam'] = rand(1, 100) <= 25 ? rand(1, 3) : 0;

                    } else {
                        $entry['jam_masuk'] = null;
                        $entry['jam_keluar'] = null;
                    }

                    $kehadiran[] = $entry;
                }
            }
        }

        DB::table('kehadiran')->insert($kehadiran);
    }
}
