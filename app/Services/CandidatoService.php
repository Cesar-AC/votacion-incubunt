<?php

namespace App\Services;

use App\Interfaces\Services\ICandidatoService;
use App\Models\Candidato;
use App\Models\CandidatoEleccion;
use App\Models\Cargo;
use App\Models\Elecciones;
use App\Models\Partido;
use App\Models\PropuestaCandidato;
use Illuminate\Support\Collection;

class CandidatoService implements ICandidatoService
{
    public function obtenerCandidatos(): Collection
    {
        return Candidato::get();
    }

    public function obtenerCandidatoPorId(int $id): Candidato
    {
        return Candidato::findOrFail($id);
    }

    public function crearCandidato(array $datos): Candidato
    {
        $candidato = Candidato::create([
            'idUsuario' => $datos['idUsuario'],
        ]);

        return $candidato;
    }

    public function editarCandidato(array $datos, Candidato $candidato): Candidato
    {
        $candidato->update([
            'idUsuario' => $datos['idUsuario'],
        ]);

        return $candidato;
    }

    public function eliminarCandidato(Candidato $candidato): void
    {
        $candidato->delete();
    }

    public function obtenerCargoDeCandidatoEnElecciones(Candidato $candidato, Elecciones $elecciones): Cargo
    {
        return CandidatoEleccion::where('idCandidato', $candidato->getKey())
            ->where('idElecciones', $elecciones->getKey())
            ->first()
            ->cargo;
    }

    public function obtenerPartidoDeCandidatoEnElecciones(Candidato $candidato, Elecciones $elecciones): Partido
    {
        return CandidatoEleccion::where('idCandidato', $candidato->getKey())
            ->where('idElecciones', $elecciones->getKey())
            ->first()
            ->partido;
    }

    public function actualizarPartidoDeCandidatoEnElecciones(array $datos, Candidato $candidato, Elecciones $elecciones): void
    {
        CandidatoEleccion::where('idCandidato', $candidato->getKey())
            ->where('idElecciones', $elecciones->getKey())
            ->update([
                'idPartido' => $datos['idPartido'],
            ]);
    }

    public function actualizarCargoDeCandidatoEnElecciones(array $datos, Candidato $candidato, Elecciones $elecciones): void
    {
        CandidatoEleccion::where('idCandidato', $candidato->getKey())
            ->where('idElecciones', $elecciones->getKey())
            ->update([
                'idCargo' => $datos['idCargo'],
            ]);
    }

    public function vincularCandidatoAEleccion(array $datos, Candidato $candidato, Elecciones $elecciones): void
    {
        CandidatoEleccion::create([
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $elecciones->getKey(),
            'idPartido' => $datos['idPartido'] ?? null,
            'idCargo' => $datos['idCargo'],
        ]);
    }

    public function desvincularCandidatoDeEleccion(Candidato $candidato, Elecciones $elecciones): void
    {
        CandidatoEleccion::where('idCandidato', $candidato->getKey())
            ->where('idElecciones', $elecciones->getKey())
            ->delete();
    }

    public function removerPartidoDeCandidatoEnElecciones(Candidato $candidato, Elecciones $elecciones): void
    {
        CandidatoEleccion::where('idCandidato', $candidato->getKey())
            ->where('idElecciones', $elecciones->getKey())
            ->update([
                'idPartido' => null,
            ]);
    }

    public function actualizarDatosDeCandidatoEnElecciones(array $datos, Candidato $candidato, Elecciones $elecciones): void
    {
        CandidatoEleccion::where('idCandidato', $candidato->getKey())
            ->where('idElecciones', $elecciones->getKey())
            ->update([
                'idPartido' => $datos['idPartido'],
                'idCargo' => $datos['idCargo'],
            ]);
    }

    public function obtenerPropuestaDeCandidato(int $idPropuestaCandidato): PropuestaCandidato
    {
        return PropuestaCandidato::findOrFail($idPropuestaCandidato);
    }

    public function obtenerPropuestasDeCandidatoEnElecciones(Candidato $candidato, Elecciones $elecciones): Collection
    {
        return PropuestaCandidato::where('idCandidato', $candidato->getKey())
            ->where('idElecciones', $elecciones->getKey())
            ->get();
    }

    public function añadirPropuestaDeCandidato(array $datos, Candidato $candidato, Elecciones $elecciones): void
    {
        PropuestaCandidato::create([
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $elecciones->getKey(),
            'propuesta' => $datos['propuesta'],
            'descripcion' => $datos['descripcion'],
        ]);
    }

    public function actualizarPropuestaDeCandidato(array $datos, PropuestaCandidato $propuestaCandidato): void
    {
        $propuestaCandidato->update([
            'propuesta' => $datos['propuesta'],
            'descripcion' => $datos['descripcion'],
        ]);
    }

    public function eliminarPropuestaDeCandidato(int $idPropuestaCandidato): void
    {
        PropuestaCandidato::where('idPropuestaCandidato', $idPropuestaCandidato)
            ->delete();
    }
}
