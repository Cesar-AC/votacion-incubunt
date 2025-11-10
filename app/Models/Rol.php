<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'Rol';

    protected $primaryKey = 'idRol';

    public $timestamps = false;
    
    protected $fillable = [
        'rol'
    ];

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class)->withPivot('RolPermiso');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('RolUser');
    }
}
