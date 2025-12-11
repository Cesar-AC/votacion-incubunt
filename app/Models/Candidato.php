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
        'idPartido',
        'idCargo',
        'idUsuario'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'idUsuario');
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
