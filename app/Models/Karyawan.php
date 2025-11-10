<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan';

    protected $fillable = [
        'nip',
        'nama',
        'jabatan_id',
        'tanggal_masuk',
        'status_karyawan',
        'rekening_bank',
        'aktif',
    ];

    // Relasi ke tabel Jabatan
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id', 'id_jabatan');
    }

    // Relasi ke Kehadiran
    public function kehadirans()
    {
        return $this->hasMany(Kehadiran::class, 'karyawan_id', 'id_karyawan');
    }

    // Relasi ke Penggajian
    public function penggajians()
    {
        return $this->hasMany(Penggajian::class, 'karyawan_id', 'id_karyawan');
    }

    // Relasi ke Tunjangan Kehadiran Makan
    public function tunjanganKehadiranMakans()
    {
        return $this->hasMany(TunjanganKehadiranMakan::class, 'karyawan_id', 'id_karyawan');
    }
}
