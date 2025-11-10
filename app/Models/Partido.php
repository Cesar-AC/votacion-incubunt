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
        'idElecciones',
        'partido',
        'urlPartido',
        'descripcion'
    ];

    public function elecciones()
    {
        return $this->belongsTo(Elecciones::class, 'idElecciones');
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
