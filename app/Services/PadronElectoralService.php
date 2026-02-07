<?php

namespace App\Services;

use App\Interfaces\Services\IEleccionesService;
use App\Interfaces\Services\IPadronElectoralService;
use App\Models\Elecciones;
use App\Models\PadronElectoral;
use App\Models\User;
use Illuminate\Support\Collection;

class PadronElectoralService implements IPadronElectoralService
{
    public function __construct(protected IEleccionesService $eleccionesService) {}

    protected function restringirModificacionesBajoCondiciones(Elecciones $eleccion): void
    {
        $accionNegada = "No se puede modificar el padrón electoral.";

        if ($this->eleccionesService->votacionHabilitada($eleccion)) {
            throw new \Exception('La elección ya se encuentra en periodo de votación: ' . $accionNegada);
        }

        if ($eleccion->estaFinalizado()) {
            throw new \Exception('La elección ya ha finalizado: ' . $accionNegada);
        }

        if ($eleccion->estaAnulado()) {
            throw new \Exception('La elección está anulada: ' . $accionNegada);
        }
    }

    protected function estaInscritoAEleccion(User $usuario, Elecciones $eleccion): bool
    {
        return PadronElectoral::where('idUsuario', '=', $usuario->getKey())
            ->where('idElecciones', '=', $eleccion->getKey())
            ->exists();
    }

    public function obtenerPadronElectoral(?Elecciones $eleccion = null): Collection
    {
        $eleccion = $eleccion ?? $this->eleccionesService->obtenerEleccionActiva();
        return $eleccion->padronElectoral;
    }

    public function agregarUsuarioAEleccion(User $usuario, ?Elecciones $eleccion = null): void
    {
        $eleccion = $eleccion ?? $this->eleccionesService->obtenerEleccionActiva();
        $this->restringirModificacionesBajoCondiciones($eleccion);

        if ($this->estaInscritoAEleccion($usuario, $eleccion)) {
            throw new \Exception('El usuario ya pertenece al padrón electoral.');
        }

        PadronElectoral::create([
            'idUsuario' => $usuario->getKey(),
            'idElecciones' => $eleccion->getKey(),
        ]);
    }

    public function eliminarUsuarioDeElecciones(User $usuario, ?Elecciones $eleccion = null): void
    {
        $eleccion = $eleccion ?? $this->eleccionesService->obtenerEleccionActiva();
        $this->restringirModificacionesBajoCondiciones($eleccion);

        if (!$this->estaInscritoAEleccion($usuario, $eleccion)) {
            throw new \Exception('El usuario no pertenece al padrón electoral.');
        }

        PadronElectoral::where('idUsuario', '=', $usuario->getKey())
            ->where('idElecciones', $eleccion->getKey())
            ->delete();
    }

    public function restablecerPadronElectoral(?Elecciones $eleccion = null): void
    {
        $eleccion = $eleccion ?? $this->eleccionesService->obtenerEleccionActiva();
        $this->restringirModificacionesBajoCondiciones($eleccion);

        PadronElectoral::where('idElecciones', $eleccion->getKey())->delete();
    }
}
