<?php

namespace App\Http\Controllers;

use App\Interfaces\Services\IVotoService;
use App\Models\VotoCandidato;
use App\Models\VotoPartido;
use App\Models\Candidato;
use App\Models\Partido;
use App\Models\Elecciones;
use App\Models\EstadoElecciones;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VotoController extends Controller
{
    public function __construct(
        protected readonly IVotoService $votoService,
    ) {}

    public function index()
    {
        $this->authorize('viewAny', VotoCandidato::class);

        // Obtener votos de candidatos
        $votosCandidatos = VotoCandidato::with([
            'candidato.usuario.perfil',
            'eleccion',
            'tipoVoto'
        ])->get()->map(function ($voto) {
            return [
                'id' => $voto->idVotoCandidato,
                'tipo' => 'candidato',
                'entidad' => $voto->candidato->usuario->perfil->nombre . ' ' .
                    $voto->candidato->usuario->perfil->apellidoPaterno . ' ' .
                    $voto->candidato->usuario->perfil->apellidoMaterno,
                'entidadCompleta' => $voto,
                'eleccion' => $voto->eleccion->titulo ?? 'N/A',
                'tipoVoto' => $voto->tipoVoto->descripcion ?? 'N/A',
                'model' => $voto
            ];
        });

        // Obtener votos de partidos
        $votosPartidos = VotoPartido::with([
            'partido',
            'eleccion',
            'tipoVoto'
        ])->get()->map(function ($voto) {
            return [
                'id' => $voto->idVotoPartido,
                'tipo' => 'partido',
                'entidad' => $voto->partido->partido ?? 'N/A',
                'entidadCompleta' => $voto,
                'eleccion' => $voto->eleccion->titulo ?? 'N/A',
                'tipoVoto' => $voto->tipoVoto->id ?? 'N/A',
                'model' => $voto
            ];
        });

        // Combinar y ordenar
        $votos = collect($votosCandidatos)->concat($votosPartidos)->sortByDesc('id');

        return view('crud.voto.ver', compact('votos'));
    }
}
