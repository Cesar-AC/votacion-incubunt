<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidatoEleccion extends Model
{
    protected $table = 'CandidatoEleccion';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'idCandidato',
        'idElecciones'
    ];

    public function candidato()
    {
        return $this->belongsTo(Candidato::class, 'idCandidato');
    }

    public function eleccion()
    {
        return $this->belongsTo(Elecciones::class, 'idElecciones');
    }
}
