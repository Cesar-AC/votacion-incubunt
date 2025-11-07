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
        'nombre',
        'apellidos',
        'idUser',
        'idCarrera',
        'biografia',
        'experiencia',
        'estado'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'idCarrera');
    }

    public function candidatos()
    {
        return $this->hasMany(Candidato::class, 'idParticipante');
    }
}
