<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParameterPenggajian extends Model
{
    use HasFactory;

    protected $table = 'parameter_penggajian';
    protected $primaryKey = 'id_param';

    protected $fillable = [
        'nama_param',
        'nilai',
        'keterangan',
    ];
}
