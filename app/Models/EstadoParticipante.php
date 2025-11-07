<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoParticipante extends Model
{
    protected $table = 'EstadoParticipante';

    protected $primaryKey = 'idEstadoParticipante';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'idEstadoParticipante',
        'estadoParticipante'
    ];

    public function padronElectoral()
    {
        return $this->hasMany(PadronElectoral::class, 'idEstadoParticipante');
    }
}
