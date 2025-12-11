<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoElecciones extends Model
{
    public const ACTIVO = 1;
    public const PROGRAMADO = 2;
    public const FINALIZADO = 3;
    public const ANULADO = 4;

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
