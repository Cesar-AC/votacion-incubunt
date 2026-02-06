<?php

namespace App\Models;

use App\Models\Enum\TablasVoto;
use App\Models\Interfaces\IElegibleAVoto;
use App\Models\Traits\ElegibleAVoto;
use Illuminate\Database\Eloquent\Model;

class Candidato extends Model implements IElegibleAVoto
{
    use ElegibleAVoto;

    protected $tablaVoto = TablasVoto::CANDIDATO->value;

    protected $table = 'Candidato';

    protected $primaryKey = 'idCandidato';

    public $timestamps = false;

    protected $fillable = [
        'idCandidato',
        'idUsuario'
    ];

    public function votos()
    {
        return $this->hasMany(VotoCandidato::class, 'idCandidato');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'idUsuario');
    }

    public function propuestas()
    {
        return $this->hasMany(PropuestaCandidato::class, 'idCandidato');
    }

    public function elecciones()
    {
        return $this->belongsToMany(Elecciones::class, 'CandidatoEleccion', 'idCandidato', 'idElecciones')
            ->withPivot('idCargo', 'idPartido');
    }

    public function candidatoElecciones()
    {
        return $this->hasMany(CandidatoEleccion::class, 'idCandidato');
    }

    public function obtenerTipoVoto(User $votante): TipoVoto
    {
        return $this->usuario->perfil->area->getKey() == $votante->perfil->area->getKey()
            ? TipoVoto::mismaArea()
            : TipoVoto::otraArea();
    }
}
