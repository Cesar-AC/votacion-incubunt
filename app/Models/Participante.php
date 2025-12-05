<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participante extends Model
{
    protected $table = 'Participante';

    protected $primaryKey = 'idParticipante';

    public $timestamps = false;

    protected $fillable = [
        'idParticipante',
        'biografia',
        'experiencia',
        'idUser',
        'idCarrera',
        'idEstadoParticipante'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'idCarrera');
    }

    public function estadoParticipante()
    {
        return $this->belongsTo(EstadoParticipante::class, 'idEstadoParticipante');
    }

    public function candidatos()
    {
        return $this->hasMany(Candidato::class, 'idParticipante');
    }
}
