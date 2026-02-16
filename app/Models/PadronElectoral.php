<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Thiagoprz\CompositeKey\HasCompositeKey;

class PadronElectoral extends Model
{
    use HasCompositeKey;

    protected $table = 'PadronElectoral';

    protected $primaryKey = ['idElecciones', 'idUsuario'];

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'idElecciones',
        'idUsuario',
        'fechaVoto',
    ];

    public function eleccion()
    {
        return $this->belongsTo(Elecciones::class, 'idElecciones');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'idUsuario', 'idUser');
    }
}
