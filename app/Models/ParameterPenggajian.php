<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParameterPenggajian extends Model
{
    protected $table = 'parameter_penggajian';
    protected $primaryKey = 'id_param';

    protected $fillable = [
        'nama_param',
        'key',
        'nilai',
        'satuan',
        'keterangan'
    ];
}
