<?php

namespace App\Models;

use App\Models\Enum\TablasVoto;
use Illuminate\Database\Eloquent\Model;

class VotoCandidato extends Model
{
    protected $table = TablasVoto::CANDIDATO->value;

    protected $primaryKey = 'idVotoCandidato';

    public $timestamps = false;

    protected $fillable = [
        'idVotoCandidato',
        'idCandidato',
        'idElecciones',
        'idTipoVoto'
    ];

    public function candidato()
    {
        return $this->belongsTo(Candidato::class, 'idCandidato');
    }

    public function eleccion()
    {
        return $this->belongsTo(Elecciones::class, 'idElecciones');
    }

    public function tipoVoto()
    {
        return $this->belongsTo(TipoVoto::class, 'idTipoVoto');
    }
}
