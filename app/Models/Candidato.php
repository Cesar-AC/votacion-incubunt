<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidato extends Model
{
    protected $table = 'Candidato';

    protected $primaryKey = 'idCandidato';

    public $timestamps = false;

    protected $fillable = [
        'idCandidato',
        'idParticipante',
        'idCargo',
        'idPartido'
    ];

    public function participante()
    {
        return $this->belongsTo(Participante::class, 'idParticipante');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'idCargo');
    }

    public function partido()
    {
        return $this->belongsTo(Partido::class, 'idPartido');
    }

    public function propuestas()
    {
        return $this->hasMany(PropuestaCandidato::class, 'idCandidato');
    }

    public function votos()
    {
        return $this->hasMany(Voto::class, 'idCandidato');
    }
}
