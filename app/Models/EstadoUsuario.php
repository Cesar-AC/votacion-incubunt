<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoUsuario extends Model
{
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
