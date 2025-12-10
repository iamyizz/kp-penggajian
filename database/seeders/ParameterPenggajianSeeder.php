<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParameterPenggajianSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('parameter_penggajian')->insert([
<<<<<<< HEAD
            [
                'nama_param' => 'Tunjangan Makan Per Hari',
                'key' => 'tunjangan_makan_per_hari',
                'nilai' => 15000,
                'satuan' => 'Rupiah/Hari',
                'keterangan' => 'Nominal tunjangan makan per hari hadir.'
            ],
            [
                'nama_param' => 'Minimal Kehadiran untuk Bonus',
                'key' => 'bonus_kehadiran_min_hadir',
                'nilai' => 26,
                'satuan' => 'Hari',
                'keterangan' => 'Minimal hadir dalam sebulan untuk dapat bonus.'
            ],
            [
                'nama_param' => 'Nominal Bonus Kehadiran',
                'key' => 'bonus_kehadiran_nominal',
                'nilai' => 200000,
                'satuan' => 'Rupiah',
                'keterangan' => 'Bonus kehadiran full.'
            ],
            [
                'nama_param' => 'Bonus Shift Malam',
                'key' => 'bonus_shift_malam',
                'nilai' => 25000,
                'satuan' => 'Rupiah/Shift',
                'keterangan' => 'Nominal per shift malam.'
            ],
            [
                'nama_param' => 'Potongan Telat Per Menit',
                'key' => 'potongan_telat_per_menit',
                'nilai' => 500,
                'satuan' => 'Rupiah/Menit',
                'keterangan' => 'Potongan per menit keterlambatan.'
            ],
            [
                'nama_param' => 'Potongan Alpa',
                'key' => 'potongan_alpa',
                'nilai' => 50000,
                'satuan' => 'Rupiah/Hari',
                'keterangan' => 'Potongan jika tidak hadir.'
            ],
            [
                'nama_param' => 'Tarif Lembur Per Jam',
                'key' => 'lembur_per_jam',
                'nilai' => 20000,
                'satuan' => 'Rupiah/Jam',
                'keterangan' => 'Nominal lembur per jam.'
            ],
            [
                'nama_param' => 'Hari Kerja Normal',
                'key' => 'hari_kerja_normal',
                'nilai' => 26,
                'satuan' => 'Hari',
                'keterangan' => 'Jumlah hari kerja standar per bulan.'
            ]
=======
            ['nama_param' => 'Tunjangan Harian', 'nilai' => 25000, 'keterangan' => 'Diberikan per hari hadir'],
            ['nama_param' => 'Potongan Terlambat', 'nilai' => 15000, 'keterangan' => 'Potongan per hari terlambat'],
            ['nama_param' => 'Lembur per Jam', 'nilai' => 20000, 'keterangan' => 'Bonus lembur per jam'],
            ['nama_param' => 'Potongan BPJS', 'nilai' => 200000, 'keterangan' => 'Potongan tetap bulanan'],
>>>>>>> 4e98af530a1c52172cbb55e67993dd36fbf28406
        ]);
    }
}
