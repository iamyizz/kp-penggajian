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

        // ✅ Karyawan teladan (khusus Januari 2026)
        $karyawanTeladanId = 1;

        $months = [
            '2025-10',
            '2025-11',
            '2025-12',
            '2026-01',
        ];

        foreach ($months as $month) {

            $year     = (int) substr($month, 0, 4);
            $monthNum = (int) substr($month, 5, 2);
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthNum, $year);

            // Flag: apakah ini Januari 2026?
            $isJanuari2026 = ($year === 2026 && $monthNum === 1);

            for ($i = 1; $i <= 7; $i++) { // 7 karyawan
                for ($d = 1; $d <= $daysInMonth; $d++) {

                    $tanggal = Carbon::create($year, $monthNum, $d);

                    // =====================
                    // STATUS KEHADIRAN
                    // =====================
                    if ($isJanuari2026 && $i === $karyawanTeladanId) {
                        // ✅ Khusus Januari 2026: selalu hadir
                        $status = 'Hadir';
                    } else {
                        // Random untuk selain itu
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

                        $jamMasuk = Carbon::createFromTime(8, 0, 0);

                        // =====================
                        // TERLAMBAT
                        // =====================
                        if ($isJanuari2026 && $i === $karyawanTeladanId) {
                            // ✅ Khusus Januari 2026: tidak pernah terlambat
                            $terlambat = false;
                        } else {
                            // 15% kemungkinan terlambat
                            $terlambat = rand(1, 100) <= 15;
                            if ($terlambat) {
                                $jamMasuk->addMinutes(rand(5, 30));
                            }
                        }

                        $jamKeluar = (clone $jamMasuk)->addHours(8);

                        // Lembur (jika tidak terlambat)
                        $lembur = false;
                        if (!$terlambat && rand(1, 100) <= 20) {
                            $lemburJam = rand(1, 3);
                            $jamKeluar->addHours($lemburJam);
                            $entry['lembur_jam'] = $lemburJam;
                            $lembur = true;
                        }

                        // Pulang cepat (tidak boleh jika lembur)
                        if (!$lembur && rand(1, 100) <= 15) {
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
