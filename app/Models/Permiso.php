<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table = 'Permiso';

    protected $primaryKey = 'idPermiso';

    public $timestamps = false;

    protected $fillable = [
        'permiso'
    ];

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'RolPermiso', 'idPermiso', 'idRol');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'UserPermiso', 'idPermiso', 'idUser');
    }
}
