<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolPermiso extends Model
{
    protected $table = 'RolPermiso';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'idPermiso',
        'idRol'
    ];

    public function permiso()
    {
        return $this->belongsTo(Permiso::class, 'idPermiso');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'idRol');
    }
}
