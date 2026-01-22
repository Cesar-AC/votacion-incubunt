<?php

namespace App\Models;

use App\Models\Enum\TablasVoto;
use Illuminate\Database\Eloquent\Model;

class VotoPartido extends Model
{
    protected $table = TablasVoto::PARTIDO->value;

    protected $primaryKey = 'idVotoPartido';

    public $timestamps = false;

    protected $fillable = [
        'idVotoPartido',
        'idPartido',
        'idElecciones',
        'idTipoVoto'
    ];

    public function partido()
    {
        return $this->belongsTo(Partido::class, 'idPartido');
    }

    public function eleccion()
    {
        return $this->belongsTo(Elecciones::class, 'idElecciones');
    }

    public function tipoVoto()
    {
        return $this->belongsTo(TipoVoto::class, 'idTipoVoto');
    }
}
