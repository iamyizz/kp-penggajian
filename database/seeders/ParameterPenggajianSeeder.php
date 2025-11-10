<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParameterPenggajianSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('parameter_penggajian')->insert([
            ['nama_param' => 'Tunjangan Harian', 'nilai' => 25000, 'keterangan' => 'Diberikan per hari hadir'],
            ['nama_param' => 'Potongan Terlambat', 'nilai' => 15000, 'keterangan' => 'Potongan per hari terlambat'],
            ['nama_param' => 'Lembur per Jam', 'nilai' => 20000, 'keterangan' => 'Bonus lembur per jam'],
            ['nama_param' => 'Potongan BPJS', 'nilai' => 200000, 'keterangan' => 'Potongan tetap bulanan'],
        ]);
    }
}
