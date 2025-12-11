<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartidoEleccion extends Model
{
    protected $table = 'PartidoEleccion';
    public $timestamps = false;
    protected $fillable = [
        'idPartido',
        'idElecciones'
    ];
    
    public function partido()
    {
        return $this->belongsTo(Partido::class, 'idPartido');
    }

    public function elecciones()
    {
        return $this->belongsTo(Elecciones::class, 'idElecciones');
    }
}
