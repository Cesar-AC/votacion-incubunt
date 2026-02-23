<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropuestaCandidato extends Model
{
    protected $table = 'PropuestaCandidato';

    protected $primaryKey = 'idPropuesta';

    public $timestamps = false;

    protected $fillable = [
        'idPropuesta',
        'propuesta',
        'descripcion',
        'idCandidato',
        'idElecciones',
    ];

    public function candidato()
    {
        return $this->belongsTo(Candidato::class, 'idCandidato');
    }

    public function elecciones()
    {
        return $this->belongsTo(Elecciones::class, 'idElecciones');
    }
}
