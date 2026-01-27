<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoVoto extends Model
{
    const ID_NO_APLICABLE = 1;
    const ID_MISMA_AREA = 2;
    const ID_OTRA_AREA = 3;

    protected $table = 'TipoVoto';

    protected $primaryKey = 'idTipoVoto';

    public $timestamps = false;

    protected $fillable = [
        'idTipoVoto',
        'descripcion',
        'peso'
    ];

    public function esNoAplicable(): bool
    {
        return $this->getKey() === self::ID_NO_APLICABLE;
    }

    public function esMismaArea(): bool
    {
        return $this->getKey() === self::ID_MISMA_AREA;
    }

    public function esOtraArea(): bool
    {
        return $this->getKey() === self::ID_OTRA_AREA;
    }

    public static function noAplicable(): TipoVoto
    {
        return TipoVoto::where('idTipoVoto', '=', self::ID_NO_APLICABLE)->first();
    }

    public static function mismaArea(): TipoVoto
    {
        return TipoVoto::where('idTipoVoto', '=', self::ID_MISMA_AREA)->first();
    }

    public static function otraArea(): TipoVoto
    {
        return TipoVoto::where('idTipoVoto', '=', self::ID_OTRA_AREA)->first();
    }
}
