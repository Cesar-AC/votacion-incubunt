<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'Logs';

    protected $primaryKey = 'idLog';

    public $timestamps = false;

    protected $fillable = [
        'idUser',
        'idPermiso',
        'fecha_log',
        'descripcion'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function permiso()
    {
        return $this->belongsTo(Permiso::class);
    }
}
