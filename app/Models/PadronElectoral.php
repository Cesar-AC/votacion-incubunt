<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PadronElectoral extends Model
{
    protected $table = 'PadronElectoral';

    protected $primaryKey = 'idPadronElectoral';

    public $timestamps = false;

    protected $fillable = [
        'idPadronElectoral',
        'idElecciones',
        'idParticipante'
    ];

    public function elecciones()
    {
        return $this->belongsTo(Elecciones::class, 'idElecciones');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }
}
