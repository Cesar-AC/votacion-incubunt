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

    public function padronElectoral()
    {
        return $this->hasMany(PadronElectoral::class, 'idElecciones');
    }

    public function partidos()
    {
        return $this->belongsToMany(Partido::class, 'PartidoEleccion', 'idElecciones', 'idPartido');
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'PadronElectoral', 'idElecciones', 'idUsuario');
    }

    public function candidatos()
    {
        return $this->belongsToMany(Candidato::class, 'CandidatoEleccion', 'idElecciones', 'idCandidato');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoElecciones::class, 'idEstado');
    }

    public function estaProgramado()
    {
        return $this->estado->esProgramado();
    }
    public function estaActivo()
    {
        return $this->estado->esActivo();
    }

    public function estaFinalizado()
    {
        return $this->estado->esFinalizado();
    }

    public function estaAnulado()
    {
        return $this->estado->esAnulado();
    }

    public function marcarComoProgramada()
    {
        $this->estado()->associate(EstadoElecciones::programado());
        $this->save();
    }

    public function marcarComoFinalizado()
    {
        $this->estado()->associate(EstadoElecciones::finalizado());
        $this->save();
    }

    public function marcarComoAnulado()
    {
        $this->estado()->associate(EstadoElecciones::anulado());
        $this->save();
    }
}
