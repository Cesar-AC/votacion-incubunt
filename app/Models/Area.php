<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'Area';

    protected $primaryKey = 'idArea';

    public $timestamps = false;

    protected $fillable = [
        'idArea',
        'area'
    ];

    public function cargos()
    {
        return $this->hasMany(Cargo::class, 'idArea');
    }
}
