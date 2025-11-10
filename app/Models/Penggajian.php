<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penggajian extends Model
{
    use HasFactory;

    protected $table = 'penggajians';
    protected $primaryKey = 'id_penggajian';

    protected $fillable = [
        'karyawan_id',
        'periode_bulan',
        'periode_tahun',
        'gaji_pokok',
        'tunjangan_jabatan',
        'tunjangan_kehadiran_makan',
        'lembur',
        'potongan_absen',
        'potongan_bpjs',
        'total_gaji',
        'tanggal_proses',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }
}
