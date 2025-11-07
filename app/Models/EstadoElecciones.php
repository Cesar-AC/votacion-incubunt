<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoElecciones extends Model
{
    protected $table = 'EstadoElecciones';

    protected $primaryKey = 'idEstado';

    public $timestamps = false;

    protected $fillable = [
        'idEstado',
        'estado'
    ];

    public function elecciones()
    {
        return $this->hasMany(Elecciones::class, 'estado');
    }
}
