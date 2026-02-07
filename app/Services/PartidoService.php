<?php

namespace App\Services;

use App\Interfaces\Services\IArchivoService;
use App\Interfaces\Services\IEleccionesService;
use App\Interfaces\Services\IPartidoService;
use App\Models\Elecciones;
use App\Models\Partido;
use App\Models\PartidoEleccion;
use App\Models\PropuestaPartido;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class PartidoService implements IPartidoService
{
    public function __construct(
        protected IEleccionesService $eleccionesService,
        protected IArchivoService $archivoService,
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

        if (!$elecciones->estaProgramado()) {
            throw new \Exception('La elección no se encuentra programada.');
        }

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

        if (!$elecciones->estaProgramado()) {
            throw new \Exception('La elección no se encuentra programada.');
        }

        if (!PartidoEleccion::where('idPartido', '=', $partido->getKey())
            ->where('idElecciones', '=', $elecciones->getKey())
            ->exists()) {
            throw new \Exception('El partido no se encuentra inscrito en la elección.');
        }

        PartidoEleccion::where('idPartido', '=', $partido->getKey())
            ->where('idElecciones', '=', $elecciones->getKey())
            ->delete();
    }

    public function establecerEleccionesDePartido(Partido $partido, Collection $elecciones): void
    {
        $programadasNuevas = $elecciones->filter(function ($eleccion) {
            /** @var Elecciones $eleccion */
            return $eleccion->estaProgramado();
        });

        PartidoEleccion::insertOrIgnore($programadasNuevas->map(function ($eleccion) use ($partido) {
            return [
                'idPartido' => $partido->getKey(),
                'idElecciones' => $eleccion->getKey(),
            ];
        })->toArray());

        $programadasAntiguas = PartidoEleccion::where('idPartido', '=', $partido->getKey())
            ->whereNotIn('idElecciones', $programadasNuevas->pluck('idElecciones'))
            ->get();

        $programadasAntiguas->each(function ($programadaAntigua) {
            $programadaAntigua->delete();
        });
    }

    public function subirFoto(Partido $partido, UploadedFile $archivo): void
    {
        if ($partido->foto) {
            throw new \Exception('El partido ya tiene una foto.');
        }

        $archivo = $this->archivoService->subirArchivo('partidos/fotos', $archivo->hashName(), $archivo, 'public');

        $partido->foto()->associate($archivo);
        $partido->save();
    }

    public function removerFoto(Partido $partido): void
    {
        $foto = $partido->foto;

        $partido->foto()->disassociate();
        $partido->save();

        if ($foto == null) {
            throw new \Exception('El partido no tiene una foto.');
        }

        $this->archivoService->eliminarArchivo($foto->getKey());
    }

    public function cambiarFoto(Partido $partido, UploadedFile $archivo): void
    {
        try {
            $this->removerFoto($partido);
        } catch (\Exception $e) {
            // No se hace nada, porque puede que la foto no exista.
        }

        $this->subirFoto($partido, $archivo);
    }

    public function obtenerFotoURL(Partido $partido): ?string
    {
        return $partido->obtenerFotoURL();
    }
}
