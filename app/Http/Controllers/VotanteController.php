<?php
// app/Http/Controllers/VotanteController.php

namespace App\Http\Controllers;

use App\Models\Elecciones;
use App\Models\Candidato;
use App\Models\Cargo;
use App\Models\Voto;
use App\Models\PadronElectoral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VotanteController extends Controller
{

    // Página principal del votante
    public function home() 
    {
        $eleccionesActivas = \App\Models\Elecciones::with('estadoEleccion')->get()->filter(function($e) {
            return method_exists($e, 'estaActivo') ? $e->estaActivo() : false;
        })->values();

        return view('votante.home', compact('eleccionesActivas'));

    }

    /**
     * Lista de elecciones disponibles
     */
    public function listarElecciones()
    {
        // TEMPORAL: Datos de ejemplo sin BD
        $elecciones = collect([
            (object)[
                'id' => 1,
                'nombreEleccion' => 'Elecciones Centro de Estudiantes 2024',
                'descripcion' => 'Elección de representantes estudiantiles para el periodo académico 2024-2025',
                'fechaInicio' => '2024-12-15 08:00:00',
                'fechaFin' => '2024-12-20 18:00:00',
                'idEstadoEleccion' => 2,
                'estadoEleccion' => (object)['nombreEstado' => 'Activa']
            ],
            (object)[
                'id' => 2,
                'nombreEleccion' => 'Delegados de Aula 2024-I',
                'descripcion' => 'Elección de delegados de aula para el semestre académico 2024-I',
                'fechaInicio' => '2024-03-01 08:00:00',
                'fechaFin' => '2024-03-05 18:00:00',
                'idEstadoEleccion' => 3,
                'estadoEleccion' => (object)['nombreEstado' => 'Finalizada']
            ],
        ]);

        // Cuando conectes con BD, descomentar:
        // $elecciones = Elecciones::whereIn('idEstadoEleccion', [2, 3])
        //     ->with('estadoEleccion')
        //     ->orderBy('fechaInicio', 'desc')
        //     ->paginate(10);

        return view('votante.elecciones.index', compact('elecciones'));
    }

    /**
     * Ver detalle de una elección
     */
    public function verDetalleEleccion($id)
    {
        // TEMPORAL: Datos de ejemplo sin BD
        $eleccion = (object)[
            'id' => $id,
            'nombreEleccion' => 'Elecciones Centro de Estudiantes 2024',
            'descripcion' => 'Elección de representantes estudiantiles para el periodo académico 2024-2025',
            'fechaInicio' => '2024-12-15 08:00:00',
            'fechaFin' => '2024-12-20 18:00:00',
            'idEstadoEleccion' => 2,
            'estadoEleccion' => (object)['nombreEstado' => 'Activa']
        ];

        $yaVoto = false; // Cambiar según lógica

        // Cuando conectes con BD, descomentar:
        // $eleccion = Elecciones::with(['estadoEleccion', 'candidatos.usuario.perfil'])
        //     ->findOrFail($id);
        
        // $yaVoto = Voto::where('idPadronElectoral', function($query) use ($id) {
        //     $query->select('id')
        //           ->from('padron_electoral')
        //           ->where('idUser', Auth::id())
        //           ->where('idEleccion', $id)
        //           ->limit(1);
        // })->exists();

        return view('votante.votar.lista', compact('eleccion', 'yaVoto'));
    }

    /**
     * Iniciar proceso de votación
     */
    public function iniciarVotacion($eleccionId)
    {
        $eleccion = Elecciones::findOrFail($eleccionId);

        // Verificar que la elección esté activa
        if (! method_exists($eleccion, 'estaActivo') || ! $eleccion->estaActivo()) {
            return redirect()->route('votante.elecciones')
                ->with('error', 'Esta elección no está activa.');
        }

        // Verificar que el usuario esté en el padrón electoral
        $padron = PadronElectoral::where('idUser', Auth::id())
            ->where('idEleccion', $eleccionId)
            ->first();

        if (!$padron) {
            return redirect()->route('votante.elecciones')
                ->with('error', 'No estás registrado en el padrón electoral para esta elección.');
        }

        // Verificar si ya votó
        $yaVoto = Voto::where('idPadronElectoral', $padron->id)->exists();

        if ($yaVoto) {
            return redirect()->route('votante.elecciones.detalle', $eleccionId)
                ->with('info', 'Ya has emitido tu voto en esta elección.');
        }

        return view('votante.votar.lista', compact('eleccion'));
    }

    /**
     * Listar candidatos para votar
     */
    public function listarCandidatos($eleccionId)
    {
        $eleccion = Elecciones::findOrFail($eleccionId);

        // Verificar estado y padrón
        if (! method_exists($eleccion, 'estaActivo') || ! $eleccion->estaActivo()) {
            return redirect()->route('votante.elecciones')
                ->with('error', 'Esta elección no está activa.');
        }

        $padron = PadronElectoral::where('idUser', Auth::id())
            ->where('idEleccion', $eleccionId)
            ->first();

        if (!$padron) {
            return redirect()->route('votante.elecciones')
                ->with('error', 'No estás registrado en el padrón electoral para esta elección.');
        }

        // Verificar si ya votó
        if (Voto::where('idPadronElectoral', $padron->id)->exists()) {
            return redirect()->route('votante.elecciones.detalle', $eleccionId)
                ->with('info', 'Ya has emitido tu voto en esta elección.');
        }

        // Obtener cargos y candidatos
        $cargos = Cargo::where('idEleccion', $eleccionId)
            ->orderBy('orden')
            ->get();

        $candidatosPorCargo = [];
        foreach ($cargos as $cargo) {
            $candidatosPorCargo[$cargo->id] = Candidato::with([
                'usuario.perfil.carrera',
                'partido',
                'propuestas'
            ])
            ->where('idCargo', $cargo->id)
            ->where('idEstadoParticipante', 2) // 2 = Aprobado
            ->get();
        }

        return view('votante.votar.lista', compact('eleccion', 'cargos', 'candidatosPorCargo'));
    }

    /**
     * Ver detalle de un candidato
     */
    public function verDetalleCandidato($eleccionId, $candidatoId)
    {
        $eleccion = Elecciones::findOrFail($eleccionId);
        
        $candidato = Candidato::with([
            'usuario.perfil.carrera',
            'partido',
            'propuestas',
            'cargo'
        ])->findOrFail($candidatoId);

        return view('votante.votar.detalle_candidato', compact('eleccion', 'candidato'));
    }

    /**
     * Emitir voto
     */
    public function emitirVoto(Request $request, $eleccionId)
    {
        $request->validate([
            'candidatos' => 'required|array',
            'candidatos.*' => 'required|exists:candidato,id'
        ]);

        $eleccion = Elecciones::findOrFail($eleccionId);

        // Verificar estado
        if (! method_exists($eleccion, 'estaActivo') || ! $eleccion->estaActivo()) {
            return back()->with('error', 'Esta elección no está activa.');
        }

        // Obtener padrón
        $padron = PadronElectoral::where('idUser', Auth::id())
            ->where('idEleccion', $eleccionId)
            ->firstOrFail();

        // Verificar si ya votó
        if (Voto::where('idPadronElectoral', $padron->id)->exists()) {
            return redirect()->route('votante.elecciones.detalle', $eleccionId)
                ->with('error', 'Ya has votado en esta elección.');
        }

        try {
            DB::beginTransaction();

            // Registrar votos
            foreach ($request->candidatos as $cargoId => $candidatoId) {
                Voto::create([
                    'idCandidato' => $candidatoId,
                    'idPadronElectoral' => $padron->id,
                    'fechaVoto' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('votante.votar.exito', $eleccionId)
                ->with('success', '¡Tu voto ha sido registrado exitosamente!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Hubo un error al registrar tu voto. Por favor, intenta nuevamente.');
        }
    }

    /**
     * Pantalla de éxito después de votar
     */
    public function votoExitoso($eleccionId)
    {
        $eleccion = Elecciones::findOrFail($eleccionId);
        
        // Obtener el padrón y votos del usuario
        $padron = PadronElectoral::where('idUser', Auth::id())
            ->where('idEleccion', $eleccionId)
            ->firstOrFail();

        $votos = Voto::with(['candidato.usuario.perfil', 'candidato.partido', 'candidato.cargo'])
            ->where('idPadronElectoral', $padron->id)
            ->get();

        return view('votante.votar.exito', compact('eleccion', 'votos'));
    }

    /**
     * Ver resultados de una elección
     */
    public function verResultados($eleccionId)
    {
        $eleccion = Elecciones::with('estadoEleccion')->findOrFail($eleccionId);

        // Solo mostrar resultados si la elección ha finalizado
        if ($eleccion->idEstadoEleccion != 3) { // 3 = Finalizada
            return redirect()->route('votante.elecciones.detalle', $eleccionId)
                ->with('info', 'Los resultados estarán disponibles cuando finalice la elección.');
        }

        // Obtener resultados por cargo
        $cargos = Cargo::where('idEleccion', $eleccionId)->get();
        
        $resultadosPorCargo = [];
        foreach ($cargos as $cargo) {
            $resultadosPorCargo[$cargo->id] = Candidato::withCount('votos')
                ->with(['usuario.perfil', 'partido'])
                ->where('idCargo', $cargo->id)
                ->orderBy('votos_count', 'desc')
                ->get();
        }

        return view('votante.votar.resultados', compact('eleccion', 'cargos', 'resultadosPorCargo'));
    }
}