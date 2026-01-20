<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoElecciones extends Model
{
    public const PROGRAMADO = 1;
    public const FINALIZADO = 2;
    public const ANULADO = 3;

    protected $table = 'EstadoElecciones';

    protected $primaryKey = 'idEstado';

    public $timestamps = false;

    protected $fillable = [
        'idEstado',
        'estado'
    ];

    public function elecciones()
    {
        return $this->hasMany(Elecciones::class, 'estado');
    }

    public function esProgramado()
    {
        return $this->idEstado === self::PROGRAMADO;
    }

    public function esFinalizado()
    {
        return $this->idEstado === self::FINALIZADO;
    }

    public function esAnulado()
    {
        return $this->idEstado === self::ANULADO;
    }

    // Una elección activa es la que no está finalizada ni anulada
    public function esActivo()
    {
        return ! $this->esFinalizado() && ! $this->esAnulado();
    }

    public static function programado()
    {
        return self::where('idEstado', '=', self::PROGRAMADO)->first();
    }

    public static function finalizado()
    {
        return self::where('idEstado', '=', self::FINALIZADO)->first();
    }

    public static function anulado()
    {
        return self::where('idEstado', '=', self::ANULADO)->first();
    }
}
