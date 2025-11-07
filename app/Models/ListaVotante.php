<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListaVotante extends Model
{
    protected $table = 'ListaVotante';

    protected $primaryKey = 'idListaVotante';

    public $timestamps = false;

    protected $fillable = [
        'idListaVotante',
        'idUser',
        'idElecciones',
        'fechaVoto',
        'idTipoVoto'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }

    public function elecciones()
    {
        return $this->belongsTo(Elecciones::class, 'idElecciones');
    }

    public function tipoVoto()
    {
        return $this->belongsTo(TipoVoto::class, 'idTipoVoto');
    }
}
