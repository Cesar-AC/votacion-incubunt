<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerfilUsuario extends Model
{
    protected $table = 'PerfilUsuario';

    protected $primaryKey = 'idUser';

    public $timestamps = false;

    protected $fillable = [
        'apellidoPaterno',
        'apellidoMaterno',
        'nombre',
        'otrosNombres',
        'dni',
        'telefono'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }
}
