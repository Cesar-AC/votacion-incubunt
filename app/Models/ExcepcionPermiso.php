<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExcepcionPermiso extends Model
{
    protected $table = 'ExcepcionPermiso';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'idUser',
        'idPermiso'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }

    public function permiso()
    {
        return $this->belongsTo(Permiso::class, 'idPermiso');
    }
}
