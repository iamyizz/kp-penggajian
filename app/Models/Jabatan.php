<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $table = 'jabatan';
    protected $primaryKey = 'id_jabatan';

    protected $fillable = [
        'nama_jabatan',
        'gaji_pokok',
        'tunjangan_jabatan',
    ];

    // Relasi: satu jabatan punya banyak karyawan
    public function karyawans()
    {
        return $this->hasMany(Karyawan::class, 'jabatan_id', 'id_jabatan');
    }
}
