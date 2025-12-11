<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelLog extends Model
{
    protected $table = 'NivelLog';
    protected $primaryKey = 'idNivelLog';
    public $timestamps = false;
    protected $fillable = [
        'idNivelLog',
        'nombre'
    ];

    public function logs()
    {
        return $this->hasMany(Log::class, 'idNivelLog');
    }
}
