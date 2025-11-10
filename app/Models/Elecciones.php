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
        'fecha_inicio',
        'fecha_cierre',
        'estado'
    ];

    public function estadoEleccion()
    {
        return $this->belongsTo(EstadoElecciones::class, 'estado');
    }

    public function partidos()
    {
        return $this->hasMany(Partido::class, 'idElecciones');
    }

    public function listaVotantes()
    {
        return $this->hasMany(ListaVotante::class, 'idElecciones');
    }

    public function votos()
    {
        return $this->hasMany(Voto::class, 'idElecciones');
    }

    public function padronElectoral()
    {
        return $this->hasMany(PadronElectoral::class, 'idElecciones');
    }
}
