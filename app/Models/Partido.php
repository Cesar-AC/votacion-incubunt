<?php

namespace App\Models;

use App\Models\Enum\TablasVoto;
use App\Models\Interfaces\IElegibleAVoto;
use App\Models\Traits\ElegibleAVoto;
use Illuminate\Database\Eloquent\Model;

class Partido extends Model implements IElegibleAVoto
{
    use ElegibleAVoto;

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
        'planTrabajo'
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
        return $this->hasMany(Candidato::class, 'idPartido');
    }

    public function propuestas()
    {
        return $this->hasMany(PropuestaPartido::class, 'idPartido');
    }
}
