<?php

namespace App\Services;

use App\Interfaces\Services\IVotoService;
use App\Models\User;
use App\Interfaces\Services\IEleccionesService;
use App\Interfaces\Services\IPermisoService;
use App\Enum\Permiso;
use App\Models\Candidato;
use App\Models\CandidatoEleccion;
use App\Models\Elecciones;
use App\Models\Interfaces\IElegibleAVoto;
use App\Models\PadronElectoral;
use App\Models\Partido;
use Illuminate\Support\Collection;
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

    public function haVotado(User $usuario, ?Elecciones $eleccion = null): bool
    {
        $eleccion = $eleccion ?? $this->eleccionesService->obtenerEleccionActiva();

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

    protected function pertenecenEntidadesAEleccion(Collection $entidades, Elecciones $eleccion): bool
    {
        return $entidades->filter(function (IElegibleAVoto $entidad) use ($eleccion) {
            return $this->perteneceEntidadAEleccion($entidad, $eleccion);
        })->isNotEmpty();
    }

    protected function haySoloUnPartido(Collection $entidades): bool
    {
        return $entidades->filter(function (IElegibleAVoto $entidad) {
            return $entidad instanceof Partido;
        })->count() == 1;
    }

    protected function hayUnCandidatoPorArea(Collection $entidades, Elecciones $eleccion): bool
    {
        $candidatos = $entidades->filter(function (IElegibleAVoto $entidad) {
            return $entidad instanceof Candidato;
        });

        $cargos = [];
        $candidatos->each(function (IElegibleAVoto $entidad) use (&$cargos, $eleccion) {
            $candidatoEleccion = CandidatoEleccion::where('idElecciones', '=', $eleccion->getKey())
                ->where('idCandidato', '=', $entidad->obtenerPK())
                ->first();

            if (!$candidatoEleccion) {
                return;
            }

            if (isset($cargos[$candidatoEleccion->idCargo])) {
                $cargos[$candidatoEleccion->idCargo] += 1;
            } else {
                $cargos[$candidatoEleccion->idCargo] = 1;
            }
        });

        return count($cargos) == $candidatos->count();
    }

    public function votar(User $votante, Collection $entidades): void
    {
        if ($entidades->isEmpty()) {
            throw new \Exception('No se ha seleccionado ninguna entidad.');
        }

        if (!$this->eleccionesService->votacionHabilitada()) {
            throw new \Exception('La votación no está habilitada.');
        }

        $eleccion = $this->eleccionesService->obtenerEleccionActiva();

        // Asegurar que el votante tiene sus relaciones cargadas
        if (!$votante->relationLoaded('perfil')) {
            $votante->load('perfil.area');
        }

        if (!$this->tienePermisoVotar($votante)) {
            throw new \Exception('No tienes permiso para votar.');
        }

        if (!$this->estaUsuarioEnPadron($votante, $eleccion)) {
            throw new \Exception('No estás registrado en el padrón electoral.');
        }

        if ($this->haVotado($votante)) {
            throw new \Exception('Ya has votado en esta elección.');
        }

        if (!$this->pertenecenEntidadesAEleccion($entidades, $eleccion)) {
            throw new \Exception('Alguna de las entidades a votar no es elegible en esta elección.');
        }

        if (!$this->haySoloUnPartido($entidades)) {
            throw new \Exception('Se debe votar por exactamente un partido.');
        }

        if (!$this->hayUnCandidatoPorArea($entidades, $eleccion)) {
            throw new \Exception('No está permitido votar por más de un candidato que postule a la misma área.');
        }

        try {
            DB::transaction(function () use ($votante, $eleccion, $entidades) {
                PadronElectoral::where('idUsuario', '=', $votante->getKey())
                    ->where('idElecciones', '=', $eleccion->getKey())
                    ->update([
                        'fechaVoto' => now()
                    ]);

                foreach ($entidades as $entidad) {
                    // Asegurar que los candidatos tienen relaciones cargadas
                    if ($entidad instanceof Candidato && !$entidad->relationLoaded('usuario')) {
                        $entidad->load('usuario.perfil.area');
                    }

                    $data = [
                        $entidad->obtenerNombrePK() => $entidad->obtenerPK(),
                        'idElecciones' => $eleccion->getKey(),
                        'idTipoVoto' => $entidad->obtenerTipoVoto($votante)->getKey(),
                    ];

                    DB::table($entidad->obtenerTablaDeVoto())->insert($data);
                }
            });
        } catch (\Exception $e) {
            throw new \Exception('Un error ha ocurrido al votar. Por favor, intenta de nuevo. Detalles: ' . $e->getMessage());
        }
    }

    public function contarVotos(IElegibleAVoto $entidad, ?Elecciones $eleccion = null): int
    {
        $eleccion = $eleccion ?? $this->eleccionesService->obtenerEleccionActiva();

        $votos = DB::table($entidad->obtenerTablaDeVoto())
            ->where('idElecciones', '=', $eleccion->getKey())
            ->where($entidad->obtenerNombrePK(), '=', $entidad->obtenerPK())
            ->get();

        $votosPonderados = $votos->reduce(function ($total, $voto) {
            return $total + $voto->tipoVoto->peso;
        }, 0);

        return $votosPonderados;
    }

    public function puedeVotar(User $votante, ?Elecciones $eleccion = null): bool
    {
        $eleccion = $eleccion ?? $this->eleccionesService->obtenerEleccionActiva();

        if (!$eleccion) {
            return false;
        }

        if (!$this->tienePermisoVotar($votante)) {
            return false;
        }

        if (!$this->estaUsuarioEnPadron($votante, $eleccion)) {
            return false;
        }

        if ($this->haVotado($votante)) {
            return false;
        }

        return true;
    }
}
