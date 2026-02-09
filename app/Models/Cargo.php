<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $table = 'Cargo';

    protected $primaryKey = 'idCargo';

    public $timestamps = false;

    protected $fillable = [
        'idCargo',
        'cargo',
        'idArea'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'idArea');
    }

    public function candidatos()
    {
        return $this->hasManyThrough(
            Candidato::class,
            CandidatoEleccion::class,
            'idCargo',      // Foreign key on CandidatoEleccion
            'idCandidato',  // Foreign key on Candidato
            'idCargo',      // Local key on Cargo
            'idCandidato'   // Local key on CandidatoEleccion
        );
    }

    public function candidatoElecciones()
    {
        return $this->hasMany(CandidatoEleccion::class, 'idCargo');
    }
}
