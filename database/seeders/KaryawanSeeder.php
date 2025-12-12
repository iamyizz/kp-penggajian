<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        $karyawans = [
            [
                'nama' => 'dr. Andi Saputra',
                'jabatan_id' => 1,
                'tanggal_masuk' => '2022-01-15',
                'status_karyawan' => 'Tetap',
                'rekening_bank' => 'BCA 1234567890',
                'aktif' => true,
            ],
            [
                'nama' => 'Siti Rahmawati',
                'jabatan_id' => 2,
                'tanggal_masuk' => '2023-03-10',
                'status_karyawan' => 'Kontrak',
                'rekening_bank' => 'Mandiri 9876543210',
                'aktif' => true,
            ],
            [
                'nama' => 'Rizky Hidayat',
                'jabatan_id' => 3,
                'tanggal_masuk' => '2022-06-01',
                'status_karyawan' => 'Tetap',
                'rekening_bank' => 'BNI 123987654',
                'aktif' => true,
            ],
            [
                'nama' => 'Dewi Lestari',
                'jabatan_id' => 4,
                'tanggal_masuk' => '2024-02-01',
                'status_karyawan' => 'Magang',
                'rekening_bank' => 'BRI 567890123',
                'aktif' => true,
            ],

            // Tambahan karyawan baru
            [
                'nama' => 'Agung Pratama',
                'jabatan_id' => 2,
                'tanggal_masuk' => '2023-07-12',
                'status_karyawan' => 'Kontrak',
                'rekening_bank' => 'BCA 1122334455',
                'aktif' => true,
            ],
            [
                'nama' => 'Linda Wulansari',
                'jabatan_id' => 5,
                'tanggal_masuk' => '2021-11-20',
                'status_karyawan' => 'Tetap',
                'rekening_bank' => 'Mandiri 2233445566',
                'aktif' => true,
            ],
            [
                'nama' => 'Bagas Setiawan',
                'jabatan_id' => 6,
                'tanggal_masuk' => '2024-04-01',
                'status_karyawan' => 'Magang',
                'rekening_bank' => 'BRI 987654321',
                'aktif' => true,
            ],
        ];

        // Generate NIP otomatis
        foreach ($karyawans as $index => &$karyawan) {
            $number = str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            $karyawan['nip'] = 'KLSM-' . $number;
        }

        DB::table('karyawan')->insert($karyawans);
    }
}
