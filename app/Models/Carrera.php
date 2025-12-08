<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $table = 'Carrera';

    protected $primaryKey = 'idCarrera';

    public $timestamps = false;

    protected $fillable = [
        'idCarrera',
        'carrera'
    ];
}
