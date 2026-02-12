<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartidoEleccion extends Model
{
    protected $table = 'PartidoEleccion';
    protected $primaryKey = 'idPartidoEleccion';
    public $timestamps = false;

    protected $fillable = [
        'idPartido',
        'idElecciones',
    ];

    /**
     * Relación con Partido
     */
    public function partido()
    {
        return $this->belongsTo(Partido::class, 'idPartido', 'idPartido');
    }

    /**
     * Relación con Elecciones
     */
    public function eleccion()
    {
        return $this->belongsTo(Elecciones::class, 'idElecciones', 'idElecciones');
    }
}