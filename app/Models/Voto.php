<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voto extends Model
{
    protected $table = 'Voto';

    protected $primaryKey = 'idVoto';

    public $timestamps = false;

    protected $fillable = [
        'idVoto',
        'idCandidato'
    ];

    public function candidato()
    {
        return $this->belongsTo(Candidato::class, 'idCandidato');
    }
}
