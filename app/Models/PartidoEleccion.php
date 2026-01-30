<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Thiagoprz\CompositeKey\HasCompositeKey;

class PartidoEleccion extends Model
{
    use HasCompositeKey;

    protected $primaryKey = ['idPartido', 'idElecciones'];

    public $incrementing = false;

    protected $table = 'PartidoEleccion';
    public $timestamps = false;
    protected $fillable = [
        'idPartido',
        'idElecciones'
    ];

    public function partido()
    {
        return $this->belongsTo(Partido::class, 'idPartido');
    }

    public function elecciones()
    {
        return $this->belongsTo(Elecciones::class, 'idElecciones');
    }
}
