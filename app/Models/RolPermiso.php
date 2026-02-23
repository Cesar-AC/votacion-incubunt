<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Thiagoprz\CompositeKey\HasCompositeKey;

class RolPermiso extends Model
{
    use HasCompositeKey;
    protected $table = 'RolPermiso';

    protected $primaryKey = ['idPermiso', 'idRol'];

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
