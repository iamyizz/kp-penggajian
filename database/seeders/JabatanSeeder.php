<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jabatan')->insert([
            [
                'nama_jabatan' => 'Dokter Umum',
                'gaji_pokok' => 7000000,
                'tunjangan_jabatan' => 1000000,
            ],
            [
                'nama_jabatan' => 'Perawat',
                'gaji_pokok' => 4500000,
                'tunjangan_jabatan' => 500000,
            ],
            [
                'nama_jabatan' => 'Apoteker',
                'gaji_pokok' => 5500000,
                'tunjangan_jabatan' => 750000,
            ],
            [
                'nama_jabatan' => 'Staff Administrasi',
                'gaji_pokok' => 4000000,
                'tunjangan_jabatan' => 300000,
            ],
        ]);
    }
}
