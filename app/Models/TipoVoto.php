<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoVoto extends Model
{
    protected $table = 'TipoVoto';

    protected $primaryKey = 'idTipoVoto';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'idTipoVoto',
        'tipoVoto'
    ];

    public function listaVotantes()
    {
        return $this->hasMany(ListaVotante::class, 'idTipoVoto');
    }
}
