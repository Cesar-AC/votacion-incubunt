<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolUser extends Model
{
    protected $table = 'RolUser';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'idRol',
        'idUser'
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'idRol');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }
}
