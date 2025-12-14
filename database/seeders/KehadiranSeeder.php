<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

                $year     = substr($month, 0, 4);
                $monthNum = substr($month, 5, 2);
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthNum, $year);

                for ($d = 1; $d <= $daysInMonth; $d++) {

                    $tanggal = Carbon::create($year, $monthNum, $d);

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
                        'karyawan_id'      => $i,
                        'tanggal'          => $tanggal->format('Y-m-d'),
                        'status_kehadiran' => $status,
                        'terlambat'        => false,
                        'lembur_jam'       => 0,
                        'jam_masuk'        => null,
                        'jam_keluar'       => null,
                    ];

                    if ($status === 'Hadir') {

                        /** =====================
                         * JAM MASUK
                         * ===================== */
                        $jamMasuk = Carbon::createFromTime(8, 0, 0);

                        // 15% kemungkinan terlambat
                        $terlambat = rand(1, 100) <= 15;

                        if ($terlambat) {
                            $jamMasuk->addMinutes(rand(5, 30));
                        }

                        /** =====================
                         * JAM KELUAR NORMAL
                         * ===================== */
                        $jamKeluar = (clone $jamMasuk)->addHours(8);

                        /** =====================
                         * LEMBUR (jika tidak terlambat)
                         * ===================== */
                        $lembur = false;
                        if (!$terlambat && rand(1, 100) <= 20) {
                            $lemburJam = rand(1, 3);
                            $jamKeluar->addHours($lemburJam);
                            $entry['lembur_jam'] = $lemburJam;
                            $lembur = true;
                        }

                        /** =====================
                         * PULANG CEPAT
                         * (tidak boleh jika lembur)
                         * ===================== */
                        if (!$lembur && rand(1, 100) <= 15) {
                            // Pulang cepat 15–90 menit
                            $jamKeluar->subMinutes(rand(15, 90));
                        }

                        $entry['terlambat']  = $terlambat;
                        $entry['jam_masuk']  = $jamMasuk->format('H:i:s');
                        $entry['jam_keluar'] = $jamKeluar->format('H:i:s');
                    }

                    $kehadiran[] = $entry;
                }
            }
        }

        DB::table('kehadiran')->insert($kehadiran);
    }
}
