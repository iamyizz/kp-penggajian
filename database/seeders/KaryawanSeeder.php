<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('karyawan')->insert([
            [
                'nip' => 'EMP001',
                'nama' => 'dr. Andi Saputra',
                'jabatan_id' => 1,
                'tanggal_masuk' => '2022-01-15',
                'status_karyawan' => 'Tetap',
                'rekening_bank' => 'BCA 1234567890',
                'aktif' => true,
            ],
            [
                'nip' => 'EMP002',
                'nama' => 'Siti Rahmawati',
                'jabatan_id' => 2,
                'tanggal_masuk' => '2023-03-10',
                'status_karyawan' => 'Kontrak',
                'rekening_bank' => 'Mandiri 9876543210',
                'aktif' => true,
            ],
            [
                'nip' => 'EMP003',
                'nama' => 'Rizky Hidayat',
                'jabatan_id' => 3,
                'tanggal_masuk' => '2022-06-01',
                'status_karyawan' => 'Tetap',
                'rekening_bank' => 'BNI 123987654',
                'aktif' => true,
            ],
            [
                'nip' => 'EMP004',
                'nama' => 'Dewi Lestari',
                'jabatan_id' => 4,
                'tanggal_masuk' => '2024-02-01',
                'status_karyawan' => 'Magang',
                'rekening_bank' => 'BRI 567890123',
                'aktif' => true,
            ],
        ]);
    }
}
