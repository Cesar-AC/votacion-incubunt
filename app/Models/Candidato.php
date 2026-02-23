<?php

namespace App\Models;

use App\Enum\Config;
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
        'idUsuario',
        'planTrabajo'
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
        if (!$votante->relationLoaded('perfil')) {
            $votante->load('perfil.area');
        }

        if (!$votante->perfil) {
            return TipoVoto::otraArea();
        }

        if (!$votante->perfil->area) {
            return TipoVoto::otraArea();
        }

        $eleccionId = Configuracion::obtenerValor(Config::ELECCION_ACTIVA);
        if (!$eleccionId || $eleccionId === '-1') {
            return TipoVoto::otraArea();
        }

        $candidatoEleccion = $this->candidatoElecciones()
            ->where('idElecciones', '=', $eleccionId)
            ->with('cargo.area')
            ->first();

        if (!$candidatoEleccion || !$candidatoEleccion->cargo || !$candidatoEleccion->cargo->area) {
            return TipoVoto::otraArea();
        }

        // Comparar área del votante con el área de la postulación del candidato
        $mismaArea = $candidatoEleccion->cargo->area->getKey() == $votante->perfil->area->getKey();

        return $mismaArea ? TipoVoto::mismaArea() : TipoVoto::otraArea();
    }
}
