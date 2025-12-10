<?php

namespace App\Models;

<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
>>>>>>> 4e98af530a1c52172cbb55e67993dd36fbf28406
use Illuminate\Database\Eloquent\Model;

class ParameterPenggajian extends Model
{
<<<<<<< HEAD
=======
    use HasFactory;

>>>>>>> 4e98af530a1c52172cbb55e67993dd36fbf28406
    protected $table = 'parameter_penggajian';
    protected $primaryKey = 'id_param';

    protected $fillable = [
        'nama_param',
<<<<<<< HEAD
        'key',
        'nilai',
        'satuan',
        'keterangan'
=======
        'nilai',
        'keterangan',
>>>>>>> 4e98af530a1c52172cbb55e67993dd36fbf28406
    ];
}
