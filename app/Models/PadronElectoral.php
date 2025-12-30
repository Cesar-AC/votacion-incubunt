<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PadronElectoral extends Model
{
    protected $table = 'PadronElectoral';

    public $incrementing = false;
    public $timestamps = false;

 
    protected $fillable = [
        'idElecciones',
        'idUsuario',
        'fechaVoto',
    ];

    public function eleccion()
    {
        return $this->belongsTo(Elecciones::class, 'idElecciones');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'idUsuario', 'idUser');
    }
}
