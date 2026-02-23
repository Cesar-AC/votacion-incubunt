<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Thiagoprz\CompositeKey\HasCompositeKey;

class CandidatoEleccion extends Model
{
    use HasCompositeKey;

    protected $table = 'CandidatoEleccion';

    protected $primaryKey = ['idCandidato', 'idElecciones'];

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'idCandidato',
        'idElecciones',
        'idPartido',
        'idCargo'
    ];

    public function candidato()
    {
        return $this->belongsTo(Candidato::class, 'idCandidato');
    }

    public function eleccion()
    {
        return $this->belongsTo(Elecciones::class, 'idElecciones');
    }

    public function partido()
    {
        return $this->belongsTo(Partido::class, 'idPartido');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'idCargo');
    }
}
