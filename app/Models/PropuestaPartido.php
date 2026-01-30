<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropuestaPartido extends Model
{
    protected $table = 'PropuestaPartido';

    protected $primaryKey = 'idPropuesta';

    public $timestamps = false;

    protected $fillable = [
        'idPropuesta',
        'propuesta',
        'descripcion',
        'idPartido',
        'idElecciones'
    ];

    public function partido()
    {
        return $this->belongsTo(Partido::class, 'idPartido');
    }

    public function elecciones()
    {
        return $this->belongsTo(Elecciones::class, 'idElecciones');
    }
}
