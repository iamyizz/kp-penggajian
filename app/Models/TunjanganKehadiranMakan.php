<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TunjanganKehadiranMakan extends Model
{
    use HasFactory;

    protected $table = 'tunjangan_kehadiran_makans';
    protected $primaryKey = 'id_tkm';

    protected $fillable = [
        'karyawan_id',
        'bulan',
        'tahun',
        'total_hadir',
        'total_terlambat',
        'tunjangan_harian',
        'potongan_terlambat',
        'total_tunjangan',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }
}
