<?php

namespace App\Services;

use App\Interfaces\Services\IVotoService;
use App\Models\Candidato;
use App\Models\User;
use App\Models\Voto;
use App\Interfaces\Services\IEleccionesService;
use App\Interfaces\Services\IPermisoService;
use App\Enum\Permiso;
use App\Models\Elecciones;
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

    protected function perteneceCandidatoAEleccion(Candidato $candidato, Elecciones $eleccion): bool
    {
        return $this->eleccionesService->perteneceCandidatoAEleccion($candidato, $eleccion);
    }

    public function votar(User $usuario, Candidato $candidato): void
    {
        $eleccion = $this->eleccionesService->obtenerEleccionActiva();

        if (!$this->tienePermisoVotar($usuario)) {
            throw new \Exception('No tienes permiso para votar.');
        }

        if (!$this->estaUsuarioEnPadron($usuario, $eleccion)) {
            throw new \Exception('No estás registrado en el padrón electoral.');
        }

        if (!$this->perteneceCandidatoAEleccion($candidato, $eleccion)) {
            throw new \Exception('El candidato a votar no pertenece a la elección.');
        }

        if ($this->yaVoto($usuario, $eleccion)) {
            throw new \Exception('Ya has votado en esta elección.');
        }

        try {
            DB::transaction(function () use ($usuario, $eleccion, $candidato) {
                PadronElectoral::where('idUsuario', '=', $usuario->getKey())
                    ->where('idElecciones', '=', $eleccion->getKey())
                    ->update([
                        'fechaVoto' => now()
                    ]);

                Voto::create([
                    'idCandidato' => $candidato->getKey(),
                    'idElecciones' => $eleccion->getKey(),
                    'idUser' => $usuario->getKey()
                ]);
            });
        } catch (\Exception $e) {
            throw new \Exception('Un error ha ocurrido al votar. Por favor, intenta de nuevo.');
        }
    }
}
