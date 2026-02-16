<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoUsuario extends Model
{

    public const ACTIVO = 1;
    public const INACTIVO = 2;
    public const SUSPENDIDO = 3;
    public const INHABILITADO = 4;

    protected $table = 'EstadoUsuario';

    protected $primaryKey = 'idEstadoUsuario';

    public $timestamps = false;

    protected $fillable = [
        'idEstadoUsuario',
        'nombre',
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'idEstadoUsuario');
    }
}
