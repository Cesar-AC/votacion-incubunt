<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    protected $table = 'Partido';

    protected $primaryKey = 'idPartido';

    public $timestamps = false;

    protected $fillable = [
        'idPartido',
        'partido',
        'urlPartido',
        'descripcion',
        'tipo'
    ];

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
