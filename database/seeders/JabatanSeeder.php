<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jabatan')->insert([
            ['nama_jabatan' => 'Manajer Operasional', 'gaji_pokok' => 8000000, 'tunjangan_jabatan' => 1500000],
            ['nama_jabatan' => 'Kebersihan', 'gaji_pokok' => 2500000, 'tunjangan_jabatan' => 300000],
            ['nama_jabatan' => 'Bidan', 'gaji_pokok' => 4500000, 'tunjangan_jabatan' => 500000],
            ['nama_jabatan' => 'Dokter Spesialis Anak', 'gaji_pokok' => 15000000, 'tunjangan_jabatan' => 3000000],
            ['nama_jabatan' => 'Pendaftaran dan Rekam Medik', 'gaji_pokok' => 3500000, 'tunjangan_jabatan' => 300000],
            ['nama_jabatan' => 'Akunting', 'gaji_pokok' => 4000000, 'tunjangan_jabatan' => 500000],
            ['nama_jabatan' => 'Keamanan', 'gaji_pokok' => 3000000, 'tunjangan_jabatan' => 300000],
            ['nama_jabatan' => 'Tenaga Teknis Kefarmasian', 'gaji_pokok' => 3800000, 'tunjangan_jabatan' => 400000],
            ['nama_jabatan' => 'PJ Kebersihan dan Linen', 'gaji_pokok' => 2700000, 'tunjangan_jabatan' => 300000],
            ['nama_jabatan' => 'Bidan (Koord. Bidan)', 'gaji_pokok' => 5000000, 'tunjangan_jabatan' => 700000],
            ['nama_jabatan' => 'Bidan (Koord. BPJS)', 'gaji_pokok' => 4800000, 'tunjangan_jabatan' => 650000],
            ['nama_jabatan' => 'Apoteker', 'gaji_pokok' => 5500000, 'tunjangan_jabatan' => 750000],
            ['nama_jabatan' => 'Administrasi (Koord. Admin)', 'gaji_pokok' => 4200000, 'tunjangan_jabatan' => 500000],
            ['nama_jabatan' => 'Asisten TTK', 'gaji_pokok' => 3300000, 'tunjangan_jabatan' => 300000],
            ['nama_jabatan' => 'Umum', 'gaji_pokok' => 3000000, 'tunjangan_jabatan' => 250000],
            ['nama_jabatan' => 'Ahli Tenaga Laboratorium Medik', 'gaji_pokok' => 4800000, 'tunjangan_jabatan' => 600000],
            ['nama_jabatan' => 'Ahli Gizi', 'gaji_pokok' => 4000000, 'tunjangan_jabatan' => 500000],
            ['nama_jabatan' => 'Bidan (Koord. Absen)', 'gaji_pokok' => 4700000, 'tunjangan_jabatan' => 600000],
            ['nama_jabatan' => 'Perawat (Penata Anestesi)', 'gaji_pokok' => 5000000, 'tunjangan_jabatan' => 700000],
        ]);
    }
}
