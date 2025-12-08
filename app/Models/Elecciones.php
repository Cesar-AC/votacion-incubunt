<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Elecciones extends Model
{
    protected $table = 'Elecciones';

    protected $primaryKey = 'idElecciones';

    public $timestamps = false;

    protected $fillable = [
        'idElecciones',
        'titulo',
        'descripcion',
        'fechaInicio',
        'fechaCierre',
        'idEstado',
    ];

    public function estadoEleccion()
    {
        return $this->belongsTo(EstadoElecciones::class, 'idEstado');
    }

    public function partidos()
    {
        return $this->belongsToMany(Partido::class, 'PartidoEleccion', 'idElecciones', 'idPartido');
    }
    
    public function padronElectoral()
    {
        return $this->hasMany(PadronElectoral::class, 'idElecciones');
    }
}
