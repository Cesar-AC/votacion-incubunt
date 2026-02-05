<?php

namespace App\Models;

use App\Models\Enum\TablasVoto;
use App\Models\Interfaces\IElegibleAVoto;
use App\Models\Interfaces\ITieneFoto;
use App\Models\Traits\ElegibleAVoto;
use App\Models\Traits\TieneFoto;
use Illuminate\Database\Eloquent\Model;

class Partido extends Model implements IElegibleAVoto, ITieneFoto
{
    use ElegibleAVoto;
    use TieneFoto;

    protected $tablaVoto = TablasVoto::PARTIDO->value;

    protected $table = 'Partido';

    protected $primaryKey = 'idPartido';

    public $timestamps = false;

    protected $fillable = [
        'idPartido',
        'partido',
        'urlPartido',
        'descripcion',
        'tipo',
        'planTrabajo',
        'foto_idArchivo'
    ];

    public function votos()
    {
        return $this->hasMany(VotoPartido::class, 'idPartido');
    }

    public function elecciones()
    {
        return $this->belongsToMany(Elecciones::class, 'PartidoEleccion', 'idPartido', 'idElecciones');
    }

    public function candidatos()
    {
        return $this->hasManyThrough(Candidato::class, CandidatoEleccion::class, 'idPartido', 'idCandidato', 'idPartido', 'idCandidato');
    }

    public function propuestas()
    {
        return $this->hasMany(PropuestaPartido::class, 'idPartido');
    }

    public function foto()
    {
        return $this->belongsTo(Archivo::class, 'foto_idArchivo');
    }
}
