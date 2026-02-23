<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    public const ID_ADMIN = 1;
    public const ID_VOTANTE = 2;

    protected $table = 'Rol';

    protected $primaryKey = 'idRol';

    public $timestamps = false;

    protected $fillable = [
        'rol'
    ];

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'RolPermiso', 'idRol', 'idPermiso');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'RolUser', 'idRol', 'idUser');
    }

    public function esAdmin(): bool
    {
        return $this->getKey() === self::ID_ADMIN;
    }

    public function esVotante(): bool
    {
        return $this->getKey() === self::ID_VOTANTE;
    }

    public function admin(): static
    {
        return Rol::where('idRol', '=', self::ID_ADMIN)->first();
    }

    public function votante(): static
    {
        return Rol::where('idRol', '=', self::ID_VOTANTE)->first();
    }
}
