<?php

namespace App\Models;

use App\Models\Interfaces\ITieneFoto;
use App\Models\Traits\TieneFoto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerfilUsuario extends Model implements ITieneFoto
{
    use HasFactory;
    use TieneFoto;

    protected $table = 'PerfilUsuario';

    protected $primaryKey = 'idUser';

    public $timestamps = false;

    protected $fillable = [
        'idUser',
        'apellidoPaterno',
        'apellidoMaterno',
        'nombre',
        'otrosNombres',
        'dni',
        'telefono',
        'idCarrera',
        'idArea',
        'foto_idArchivo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'idCarrera');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'idArea');
    }

    public function foto()
    {
        return $this->belongsTo(Archivo::class, 'foto_idArchivo');
    }

    public function obtenerNombreApellido()
    {
        return trim("{$this->nombre} {$this->otrosNombres} {$this->apellidoPaterno} {$this->apellidoMaterno}");
    }

    public function obtenerApellidoNombre()
    {
        return trim("{$this->apellidoPaterno} {$this->apellidoMaterno} {$this->nombre} {$this->otrosNombres}");
    }

    public function obtenerNombreApellidoCorto()
    {
        return ucwords(
            strtolower(
                trim("{$this->nombre} " . substr($this->apellidoPaterno, 0, 1) . ".")
            )
        );
    }
}
