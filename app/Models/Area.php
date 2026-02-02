<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    const SIN_AREA_ASIGNADA = 1;

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

    public function tieneAreaAsignada(): bool
    {
        return $this->idArea != self::SIN_AREA_ASIGNADA;
    }
}
