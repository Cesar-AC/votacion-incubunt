<?php

namespace App\Models;

use App\Enum\Config;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'Configuracion';

    protected $primaryKey = 'clave';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'clave',
        'valor'
    ];

    /**
     * Define el valor de una configuración.
     * @param Config $clave
     *      Obligatorio.
     *      Clave de la configuración a definir.
     * @param string $valor
     *      Opcional.
     *      Valor de la configuración a definir.
     */
    public static function definirClave(Config $clave, ?string $valor = null): void
    {
        self::updateOrCreate(['clave' => $clave->value], ['valor' => $valor]);
    }

    /**
     * Obtiene el valor de una configuración.
     * @param Config $clave
     *      Obligatorio.
     *      Clave de la configuración buscada.
     * @return string|null
     *      Valor de la configuración buscada.
     */
    public static function obtenerValor(Config $clave): ?string
    {
        return self::where('clave', $clave->value)->first()?->valor;
    }
}
