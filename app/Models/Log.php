<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'Logs';

    protected $primaryKey = 'idLog';

    public $timestamps = false;

    protected $fillable = [
        'idCategoriaLog',
        'idNivelLog',
        'idUsuario',
        'fecha',
        'descripcion'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'idUsuario', 'idUser');
    }

    public function permiso()
    {
        return $this->belongsTo(Permiso::class);
    }
}
