<?php

namespace App\Services;

use App\Interfaces\Services\IEleccionesService;
use App\Interfaces\Services\IPartidoService;
use App\Models\Elecciones;
use App\Models\Partido;
use App\Models\PartidoEleccion;
use App\Models\PropuestaPartido;
use Illuminate\Support\Collection;

class PartidoService implements IPartidoService
{
    public function __construct(
        protected IEleccionesService $eleccionesService
    ) {}

    public function obtenerPartidos(): Collection
    {
        return Partido::all();
    }

    public function obtenerPartidosInscritosEnEleccion(?Elecciones $elecciones): Collection
    {
        $elecciones = $elecciones ?? $this->eleccionesService->obtenerEleccionActiva();

        return Partido::whereHas('elecciones', function ($query) use ($elecciones) {
            $query->where('idElecciones', '=', $elecciones->getKey());
        })->get();
    }

    public function obtenerPartidoPorId(int $id): Partido
    {
        return Partido::findOrFail($id);
    }

    public function crearPartido(array $datos): Partido
    {
        return Partido::create($datos);
    }

    public function editarPartido(array $datos, Partido $partido): Partido
    {
        $partido->update($datos);

        return $partido;
    }

    public function eliminarPartido(Partido $partido): void
    {
        $partido->delete();
    }

    public function obtenerPropuestaDePartido(int $idPropuestaPartido): PropuestaPartido
    {
        return PropuestaPartido::findOrFail($idPropuestaPartido);
    }

    public function obtenerPropuestasDePartidoEnElecciones(Partido $partido, Elecciones $elecciones): Collection
    {
        return PropuestaPartido::where('idPartido', '=', $partido->getKey())
            ->where('idElecciones', '=', $elecciones->getKey())
            ->get();
    }

    public function añadirPropuestaDePartido(array $datos, Partido $partido, Elecciones $elecciones): void
    {
        PropuestaPartido::create([
            'idPartido' => $partido->getKey(),
            'idElecciones' => $elecciones->getKey(),
            'propuesta' => $datos['propuesta'],
            'descripcion' => $datos['descripcion'],
        ]);
    }

    public function actualizarPropuestaDePartido(array $datos, PropuestaPartido $propuestaPartido): void
    {
        $propuestaPartido->update($datos);
    }

    public function eliminarPropuestaDePartido(int $idPropuestaPartido): void
    {
        $propuesta = PropuestaPartido::findOrFail($idPropuestaPartido);
        $propuesta->delete();
    }

    public function inscribirPartidoEnElecciones(Partido $partido, ?Elecciones $elecciones = null): void
    {
        $elecciones = $elecciones ?? $this->eleccionesService->obtenerEleccionActiva();

        if (PartidoEleccion::where('idPartido', '=', $partido->getKey())
            ->where('idElecciones', '=', $elecciones->getKey())
            ->exists()
        ) {
            throw new \Exception('El partido ya se encuentra inscrito en la elección.');
        }

        PartidoEleccion::create([
            'idPartido' => $partido->getKey(),
            'idElecciones' => $elecciones->getKey(),
        ]);
    }

    public function removerPartidoDeElecciones(Partido $partido, ?Elecciones $elecciones = null): void
    {
        $elecciones = $elecciones ?? $this->eleccionesService->obtenerEleccionActiva();

        if (!PartidoEleccion::where('idPartido', '=', $partido->getKey())
            ->where('idElecciones', '=', $elecciones->getKey())
            ->exists()) {
            throw new \Exception('El partido no se encuentra inscrito en la elección.');
        }

        PartidoEleccion::where('idPartido', '=', $partido->getKey())
            ->where('idElecciones', '=', $elecciones->getKey())
            ->delete();
    }
}
