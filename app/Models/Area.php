<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    const PRESIDENCIA = 1;
    const SIN_AREA_ASIGNADA = 2;

    protected $table = 'Area';

    protected $primaryKey = 'idArea';

    public $timestamps = false;

    protected $fillable = [
        'idArea',
        'area',
        'siglas',
    ];

    public function cargos()
    {
        return $this->hasMany(Cargo::class, 'idArea');
    }

    public function tieneAreaAsignada(): bool
    {
        return $this->idArea != self::SIN_AREA_ASIGNADA;
    }

    public function esPresidencia(): bool
    {
        return $this->idArea == self::PRESIDENCIA;
    }

    public function presidencia(): self
    {
        return self::find(self::PRESIDENCIA);
    }

    public function sinAreaAsignada(): self
    {
        return self::find(self::SIN_AREA_ASIGNADA);
    }
}
