<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusKehadiran extends Model
{
    protected $table = 'bonus_kehadiran';
    protected $primaryKey = 'id_bonus';

    protected $fillable = [
        'karyawan_id', 'bulan', 'tahun',
        'total_hadir', 'total_izin', 'total_sakit', 'total_alpha',
        'total_terlambat', 'dapat_bonus', 'nominal_bonus'
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id', 'id_karyawan');
    }
}
