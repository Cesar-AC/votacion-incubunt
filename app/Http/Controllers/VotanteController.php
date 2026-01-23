<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Elecciones;
use App\Models\PadronElectoral;
use App\Models\Voto;
use App\Models\Partido;
use App\Models\Area;

class VotanteController extends Controller
{
    /**
     * Página principal del votante
     */
    public function home()
    {
        // Obtener elección activa (estado 1 o donde la fecha sea vigente)
        // Asumiendo estado 1 = Activa. Campo correcto: idEstado
        $eleccionActiva = Elecciones::where('idEstado', 1) 
            ->orWhere(function($q) {
                $q->where('fechaInicio', '<=', now())
                  ->where('fechaCierre', '>=', now());
            })
            ->first();
        
        $totalElecciones = Elecciones::count();
        
        return view('votante.home', compact('eleccionActiva', 'totalElecciones'));
    }

    /**
     * Ver propuestas (Candidatos y Partidos)
     */
    public function propuestas()
    {
        // Obtener la elección activa
        $eleccionActiva = Elecciones::where('idEstado', 1) 
            ->orWhere(function($q) {
                $q->where('fechaInicio', '<=', now())
                  ->where('fechaCierre', '>=', now());
            })
            ->first();

        if (!$eleccionActiva) {
            return view('votante.propuestas.index', [
                'partidos' => collect([]), 
                'areas' => collect([]),
                'eleccion' => null
            ]);
        }
        
        // Obtener Partidos que tienen candidatos en esta elección
        // Relación implícita por candidatos que están en la elección via tablas pivote o relaciones directas
        // Partido belongsToMany Elecciones en modelo Partido
        $partidos = Partido::whereHas('elecciones', function($q) use ($eleccionActiva) {
                $q->where('Elecciones.idElecciones', $eleccionActiva->idElecciones);
            })
            ->with(['candidatos' => function($q) use ($eleccionActiva) {
                 // Filtrar candidatos de este partido que están en esta elección
                 // Candidato belongsTo Partido, Candidato belongsToMany Elecciones
                 $q->whereHas('elecciones', function($sq) use ($eleccionActiva) {
                     $sq->where('Elecciones.idElecciones', $eleccionActiva->idElecciones);
                 })->with(['usuario.perfil.carrera', 'cargo', 'propuestas']);
            }, 'propuestas'])
            ->get();

        // Obtener Áreas
         $areas = Area::with(['cargos' => function($qCargo) use ($eleccionActiva) {
             // Cargas cargos
             $qCargo->whereHas('candidatos', function($qCand) use ($eleccionActiva) {
                 // Filtrar cargos que tienen candidatos en esta elección
                 $qCand->whereHas('elecciones', function($sq) use ($eleccionActiva) {
                     $sq->where('Elecciones.idElecciones', $eleccionActiva->idElecciones);
                 });
             })->with(['candidatos' => function($qCand) use ($eleccionActiva) {
                 // Traer los candidatos de ese cargo en esa elección
                 $qCand->whereHas('elecciones', function($sq) use ($eleccionActiva) {
                     $sq->where('Elecciones.idElecciones', $eleccionActiva->idElecciones);
                 })->with(['usuario.perfil.carrera', 'propuestas']);
             }]);
        }])->get();

        return view('votante.propuestas.index', compact('eleccionActiva', 'partidos', 'areas'));
    }

    /**
     * Lista todas las elecciones
     */
    public function listarElecciones()
    {
        $elecciones = Elecciones::with('estadoEleccion')
            ->orderBy('fechaInicio', 'desc')
            ->paginate(9);
            
        return view('votante.elecciones.index', compact('elecciones'));
    }

    /**
     * Ver detalle de una elección
     */
    public function verDetalleEleccion($id)
    {
        $eleccion = Elecciones::with(['estadoEleccion', 'candidatos.usuario.perfil'])
            ->findOrFail($id);
        
        // Verificar si el usuario ya votó en esta elección
        // Primero obtener el registro del padrón electoral del usuario para esta elección
        $padron = PadronElectoral::where('idUsuario', Auth::id())
            ->where('idElecciones', $id)
            ->first();
            
        $yaVoto = false;
        if ($padron) {
            $yaVoto = Voto::where('idPadronElectoral', $padron->idPadronElectoral)->exists();
        }
        
        $candidatos = $eleccion->candidatos;

        return view('votante.elecciones.detalle', compact('eleccion', 'candidatos', 'yaVoto'));
    }

    /**
     * Iniciar proceso de votación
     */
    public function iniciarVotacion($eleccionId)
    {
        $eleccion = Elecciones::findOrFail($eleccionId);

        // Verificar que la elección esté activa
        if (!$eleccion->estaActivo()) {
            return redirect()->route('votante.elecciones')
                ->with('error', 'Esta elección no está activa.');
        }

        // Verificar que el usuario esté en el padrón electoral
        $padron = PadronElectoral::where('idUsuario', Auth::id())
            ->where('idElecciones', $eleccionId)
            ->first();

        if (!$padron) {
            return redirect()->route('votante.elecciones')
                ->with('error', 'No estás registrado en el padrón electoral para esta elección.');
        }

        // Verificar si ya votó
        $yaVoto = Voto::where('idPadronElectoral', $padron->idPadronElectoral)->exists();

        if ($yaVoto) {
            return redirect()->route('votante.elecciones.detalle', $eleccionId)
                ->with('info', 'Ya has emitido tu voto en esta elección.');
        }

        return redirect()->route('votante.votar.lista', $eleccionId);
    }

    /**
     * Listar candidatos para votar
     */
    public function listarCandidatos($eleccionId)
    {
        $eleccion = Elecciones::findOrFail($eleccionId);

        // Verificar estado
        if (!$eleccion->estaActivo()) {
            return redirect()->route('votante.elecciones')
                ->with('error', 'Esta elección no está activa.');
        }

        // Verificar padrón
        $padron = PadronElectoral::where('idUsuario', Auth::id())
            ->where('idElecciones', $eleccionId)
            ->first();

        if (!$padron) {
            return redirect()->route('votante.elecciones')
                ->with('error', 'No estás registrado en el padrón electoral.');
        }

        // Obtener cargos que participan en esta elección (a través de los candidatos asignados)
        // Ojo: Esto depende de cómo modeles la relación Elección->Cargos. 
        // Si no hay tabla directa, lo inferimos de CandidatoEleccion->Candidato->Cargo
        
        /* 
           Lógica: Traer los cargos únicos de los candidatos que están en la tabla pivot CandidatoEleccion para esta elección.
        */
        $cargos = \App\Models\Cargo::whereHas('candidatos', function($q) use ($eleccionId) {
            $q->whereHas('elecciones', function($dq) use ($eleccionId) {
                $dq->where('Elecciones.idElecciones', $eleccionId);
            });
        })->get();

        $candidatosPorCargo = [];
        foreach ($cargos as $cargo) {
            // Obtener candidatos de este cargo y de esta elección
            $candidatosPorCargo[$cargo->idCargo] = \App\Models\Candidato::with([
                'usuario.perfil.carrera',
                'partido',
                'cargo',
                'propuestas'
            ])
            ->where('idCargo', $cargo->idCargo)
            ->whereHas('elecciones', function($q) use ($eleccionId) {
                 $q->where('Elecciones.idElecciones', $eleccionId);
            })
            ->get();
        }

        return view('votante.votar.lista', compact('eleccion', 'cargos', 'candidatosPorCargo'));
    }

    /**
     * Detalle de un candidato
     */
    public function verDetalleCandidato($eleccionId, $candidatoId)
    {
        $eleccion = Elecciones::findOrFail($eleccionId);
        
        $candidato = \App\Models\Candidato::with([
            'usuario.perfil.carrera',
            'partido.propuestas',
            'cargo',
            'propuestas'
        ])
        ->whereHas('elecciones', function($q) use ($eleccionId) {
                 $q->where('Elecciones.idElecciones', $eleccionId);
        })
        ->findOrFail($candidatoId);
        
        return view('votante.votar.detalle_candidato', compact('eleccion', 'candidato'));
    }

    /**
     * Procesa y emite el voto
     */
    public function emitirVoto(Request $request, $eleccionId)
    {
        $eleccion = Elecciones::findOrFail($eleccionId);

        // Verificar estado
        if (!$eleccion->estaActivo()) {
            return back()->with('error', 'Esta elección no está activa.');
        }

        // Obtener padrón
        $padron = PadronElectoral::where('idUsuario', Auth::id())
            ->where('idElecciones', $eleccionId)
            ->firstOrFail();

        // Verificar si ya votó
        if (Voto::where('idPadronElectoral', $padron->idPadronElectoral)->exists()) {
            return redirect()->route('votante.elecciones.detalle', $eleccionId)
                ->with('error', 'Ya has votado en esta elección.');
        }

        // Validar datos
        $request->validate([
            'candidatos' => 'required|array',
            'candidatos.*' => 'required|exists:Candidato,idCandidato'
        ]);

        try {
            DB::beginTransaction();

            // Registrar votos
            foreach ($request->candidatos as $cargoId => $candidatoId) {
                Voto::create([
                    'idCandidato' => $candidatoId,
                    'idPadronElectoral' => $padron->idPadronElectoral,
                    'fechaVoto' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('votante.votar.exito', $eleccionId)
                ->with('success', '¡Tu voto ha sido registrado exitosamente!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Hubo un error al registrar tu voto: ' . $e->getMessage());
        }
    }

    /**
     * Pantalla de éxito después de votar
     */
    public function votoExitoso($eleccionId)
    {
        $eleccion = Elecciones::findOrFail($eleccionId);
        
        // Obtener el padrón y votos del usuario
        $padron = PadronElectoral::where('idUsuario', Auth::id())
            ->where('idElecciones', $eleccionId)
            ->firstOrFail();

        $votos = Voto::with(['candidato.usuario.perfil', 'candidato.partido', 'candidato.cargo'])
            ->where('idPadronElectoral', $padron->idPadronElectoral)
            ->get();

        return view('votante.votar.exito', compact('eleccion', 'votos'));
    }
}