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

    public function create()
    {
        $this->authorize('create', VotoCandidato::class);
        
        $elecciones = Elecciones::where('idEstado', EstadoElecciones::PROGRAMADO)->get();
        $candidatos = [];
        $partidos = [];
        $eleccionActiva = null;

        if ($elecciones->count() > 0) {
            $eleccionActiva = $elecciones->first();
            $candidatos = Candidato::whereHas('candidatoElecciones', function ($query) use ($eleccionActiva) {
                $query->where('CandidatoEleccion.idElecciones', $eleccionActiva->idElecciones);
            })->with('usuario.perfil')->get();

            $partidos = Partido::whereHas('elecciones', function ($query) use ($eleccionActiva) {
                $query->where('PartidoEleccion.idElecciones', $eleccionActiva->idElecciones);
            })->get();
        }

        return view('crud.voto.crear', compact('candidatos', 'partidos', 'elecciones', 'eleccionActiva'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', VotoCandidato::class);
        
        $data = $request->validate([
            'idElecciones' => 'required|integer|min:1',
            'tipo' => 'required|in:candidato,partido',
            'entidad_id' => 'required|integer|min:1',
            'idTipoVoto' => 'required|integer|min:1'
        ], [
            'idElecciones.required' => 'Debes seleccionar una elección',
            'idElecciones.integer' => 'La elección debe ser un número válido',
            'idElecciones.min' => 'Debes seleccionar una elección válida',
            'tipo.required' => 'Debes seleccionar un tipo de voto',
            'tipo.in' => 'El tipo de voto debe ser candidato o partido',
            'entidad_id.required' => 'Debes seleccionar una entidad para votar',
            'entidad_id.integer' => 'La entidad debe ser un número válido',
            'entidad_id.min' => 'Debes seleccionar una entidad válida',
            'idTipoVoto.required' => 'Debes seleccionar un tipo de ponderación',
            'idTipoVoto.integer' => 'El tipo de ponderación debe ser un número válido',
            'idTipoVoto.min' => 'Debes seleccionar un tipo de ponderación válido'
        ]);

        try {
            $eleccionActiva = Elecciones::findOrFail($data['idElecciones']);

            if ($data['tipo'] === 'candidato') {
                $entidad = Candidato::findOrFail($data['entidad_id']);
            } else {
                $entidad = Partido::findOrFail($data['entidad_id']);
            }

            DB::transaction(function () use ($entidad, $eleccionActiva, $data) {
                $votoData = [
                    $entidad->obtenerNombrePK() => $entidad->obtenerPK(),
                    'idElecciones' => $eleccionActiva->getKey(),
                    'idTipoVoto' => $data['idTipoVoto'],
                ];

                DB::table($entidad->obtenerTablaDeVoto())->insert($votoData);
            });

            return redirect()->route('crud.voto.ver')->with('success', 'Voto registrado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_general', 'Error al registrar el voto: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        // Intentar buscar en votos de candidatos
        $voto = VotoCandidato::find($id);
        $tipo = 'candidato';

        if (!$voto) {
            $voto = VotoPartido::find($id);
            $tipo = 'partido';
        }

        if (!$voto) {
            abort(404, 'Voto no encontrado');
        }

        $this->authorize('view', $voto);

        return view('crud.voto.ver_datos', compact('voto', 'tipo'));
    }

    public function edit($id)
    {
        // Intentar buscar en votos de candidatos
        $voto = VotoCandidato::find($id);
        $tipo = 'candidato';

        if (!$voto) {
            $voto = VotoPartido::find($id);
            $tipo = 'partido';
        }

        if (!$voto) {
            abort(404, 'Voto no encontrado');
        }

        $this->authorize('update', $voto);

        $eleccionActiva = Elecciones::where('idEstado', EstadoElecciones::PROGRAMADO)->first();
        $candidatos = [];
        $partidos = [];

        if ($eleccionActiva) {
            $candidatos = Candidato::whereHas('candidatoElecciones', function ($query) use ($eleccionActiva) {
                $query->where('CandidatoEleccion.idElecciones', $eleccionActiva->idElecciones);
            })->with('usuario.perfil')->get();

            $partidos = Partido::whereHas('elecciones', function ($query) use ($eleccionActiva) {
                $query->where('PartidoEleccion.idElecciones', $eleccionActiva->idElecciones);
            })->get();
        }

        return view('crud.voto.editar', compact('voto', 'tipo', 'candidatos', 'partidos', 'eleccionActiva'));
    }

    public function update(Request $request, $id)
    {
        // Intentar buscar en votos de candidatos
        $voto = VotoCandidato::find($id);
        $tipo = 'candidato';

        if (!$voto) {
            $voto = VotoPartido::find($id);
            $tipo = 'partido';
        }

        if (!$voto) {
            abort(404, 'Voto no encontrado');
        }

        $this->authorize('update', $voto);

        $data = $request->validate([
            'idTipoVoto' => 'required|integer'
        ]);

        try {
            $voto->update(['idTipoVoto' => $data['idTipoVoto']]);

            return redirect()->route('crud.voto.ver')->with('success', 'Voto actualizado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_general', 'Error al actualizar el voto: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        // Intentar buscar en votos de candidatos
        $voto = VotoCandidato::find($id);
        $tipo = 'candidato';

        if (!$voto) {
            $voto = VotoPartido::find($id);
            $tipo = 'partido';
        }

        if (!$voto) {
            abort(404, 'Voto no encontrado');
        }

        $this->authorize('delete', $voto);

        try {
            $voto->delete();

            return redirect()->route('crud.voto.ver')->with('success', 'Voto eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error_general', 'Error al eliminar el voto: ' . $e->getMessage());
        }
    }

    /**
     * Un alias para la funcionalidad de store
     */
    public function votar(Request $request)
    {
        return $this->store($request);
    }
}
