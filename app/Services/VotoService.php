<?php

namespace App\Services;

use App\Interfaces\Services\IVotoService;
use App\Models\User;
use App\Interfaces\Services\IEleccionesService;
use App\Interfaces\Services\IPermisoService;
use App\Enum\Permiso;
use App\Models\Elecciones;
use App\Models\Interfaces\IElegibleAVoto;
use App\Models\PadronElectoral;
use Illuminate\Support\Facades\DB;

class VotoService implements IVotoService
{
    public function __construct(
        protected readonly IEleccionesService $eleccionesService,
        protected readonly IPermisoService $permisoService,
    ) {}

    protected function tienePermisoVotar(User $usuario): bool
    {
        return $this->permisoService->comprobarUsuario($usuario, $this->permisoService->permisoDesdeEnum(Permiso::VOTO_VOTAR));
    }

    protected function estaUsuarioEnPadron(User $usuario, Elecciones $eleccion): bool
    {
        return $this->eleccionesService->estaEnPadronElectoral($usuario, $eleccion);
    }

    protected function yaVoto(User $usuario, Elecciones $eleccion): bool
    {
        return PadronElectoral::where('idUsuario', '=', $usuario->getKey())
            ->where('idElecciones', '=', $eleccion->getKey())
            ->whereNotNull('fechaVoto')
            ->exists();
    }

    protected function perteneceEntidadAEleccion(IElegibleAVoto $entidad, Elecciones $eleccion): bool
    {
        return DB::table($entidad->obtenerTabla() . 'Eleccion')
            ->where('idElecciones', '=', $eleccion->getKey())
            ->where($entidad->obtenerNombrePK(), '=', $entidad->obtenerPK())
            ->exists();
    }

    public function votar(User $usuario, IElegibleAVoto $entidad): void
    {
        $eleccion = $this->eleccionesService->obtenerEleccionActiva();

        if (!$this->tienePermisoVotar($usuario)) {
            throw new \Exception('No tienes permiso para votar.');
        }

        if (!$this->estaUsuarioEnPadron($usuario, $eleccion)) {
            throw new \Exception('No estás registrado en el padrón electoral.');
        }

        if (!$this->perteneceEntidadAEleccion($entidad, $eleccion)) {
            throw new \Exception('La entidad no es elegible a voto en esta elección.');
        }

        if ($this->yaVoto($usuario, $eleccion)) {
            throw new \Exception('Ya has votado en esta elección.');
        }

        try {
            DB::transaction(function () use ($usuario, $eleccion, $entidad) {
                PadronElectoral::where('idUsuario', '=', $usuario->getKey())
                    ->where('idElecciones', '=', $eleccion->getKey())
                    ->update([
                        'fechaVoto' => now()
                    ]);

                $data = [
                    $entidad->obtenerNombrePK() => $entidad->obtenerPK(),
                    'idElecciones' => $eleccion->getKey(),
                    'idTipoVoto' => $entidad->obtenerTipoVoto($usuario)->getKey(),
                ];

                DB::table($entidad->obtenerTablaDeVoto())->insert($data);
            });
        } catch (\Exception $e) {
            throw $e;
            throw new \Exception('Un error ha ocurrido al votar. Por favor, intenta de nuevo.');
        }
    }
}
