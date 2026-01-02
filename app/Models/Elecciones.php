<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Elecciones extends Model
{
    use HasFactory;
    
    protected $table = 'Elecciones';

    protected $primaryKey = 'idElecciones';

    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'descripcion',
        'fechaInicio',
        'fechaCierre',
        'idEstado',
    ];
    protected $casts = [
        'fechaInicio' => 'datetime',
        'fechaCierre' => 'datetime',
    ];

    public function estadoEleccion()
    {
        return $this->belongsTo(EstadoElecciones::class, 'idEstado');
    }

    public function partidos()
    {
        return $this->belongsToMany(Partido::class, 'PartidoEleccion', 'idElecciones', 'idPartido');
    }
    
    public function usuarios(){
        return $this->belongsToMany(User::class, 'PadronElectoral', 'idElecciones', 'idUsuario');
    }

    public function estaActivo(){
        return $this->idEstado === EstadoElecciones::ACTIVO;
    }

    public function estaProgramado(){
        return $this->idEstado === EstadoElecciones::PROGRAMADO;
    }

    public function estaFinalizado(){
        return $this->idEstado === EstadoElecciones::FINALIZADO;
    }

    public function estaAnulado(){
        return $this->idEstado === EstadoElecciones::ANULADO;
    }
}
