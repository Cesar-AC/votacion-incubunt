<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    protected $table = 'Archivo';
    protected $primaryKey = 'idArchivo';

    protected $fillable = [
        'disco',
        'ruta',
        'mime',
        'tamaño',
    ];
}
