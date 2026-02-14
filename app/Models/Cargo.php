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
        return $this->belongsToMany(Candidato::class, 'CandidatoEleccion', 'idCargo', 'idCandidato');
    }
}
