<?php

namespace App\Http\Controllers;

use App\Models\Candidato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Partido;
use App\Models\PartidoEleccion;
use App\Models\CandidatoEleccion;
use App\Models\Cargo;

class CandidatoController extends Controller
{
    public function index()
{
    $elecciones = \App\Models\Elecciones::with([
        'candidatos.usuario.perfil',
        'candidatos.cargo.area',
        'candidatos.partido',
        'partidos.candidatos.usuario.perfil',
        'partidos.candidatos.cargo.area',
        'partidos.candidatos.partido'
    ])->get();

    return view('crud.candidato.ver', compact('elecciones'));
}

public function create()
{
    $partidos   = \App\Models\Partido::where('tipo', 'LISTA')->get();
    $cargos     = \App\Models\Cargo::with('area')->get();
    $usuarios   = \App\Models\User::with('perfil')->get();
    $elecciones = \App\Models\Elecciones::all();

    return view('crud.candidato.crear', compact(
        'partidos',
        'cargos',
        'usuarios',
        'elecciones'
    ));
}

public function store(Request $request)
{
    // 🔍 DEBUG: request completo
    Log::info('STORE CANDIDATO - REQUEST COMPLETO', [
        'all' => $request->all()
    ]);

    // ✅ VALIDACIÓN GENERAL
    $request->validate([
        'idEleccion' => 'required|integer|exists:Elecciones,idElecciones',
        'candidatos' => 'required|array|min:1',

        'candidatos.*.tipo' => 'required|in:Individual,Grupal',
        'candidatos.*.idUsuario' => 'required|integer|exists:User,idUser',
        'candidatos.*.idCargo' => 'required|integer|exists:Cargo,idCargo',
        'candidatos.*.idPartido' => 'nullable|integer|exists:Partido,idPartido',
        'candidatos.*.planTrabajo' => 'nullable|string',
    ]);

    Log::info('STORE CANDIDATO - VALIDADO', [
        'idEleccion' => $request->idEleccion,
        'candidatos' => $request->candidatos
    ]);

    DB::transaction(function () use ($request) {

        foreach ($request->candidatos as $index => $c) {

            Log::info("PROCESANDO CANDIDATO #{$index}", $c);

            // 🔒 VALIDACIÓN: Grupal requiere partido
            if ($c['tipo'] === 'Grupal' && empty($c['idPartido'])) {
                throw new \Exception('El candidato grupal requiere partido');
            }

            /**
             * ==========================
             *  CANDIDATO INDIVIDUAL
             * ==========================
             */
            if ($c['tipo'] === 'Individual') {

                // 🔒 VALIDACIÓN: Evitar duplicados individuales
                $existeCandidato = Candidato::where([
                    'idUsuario' => $c['idUsuario'],
                    'idCargo' => $c['idCargo'],
                ])->whereNull('idPartido')->exists();

                if ($existeCandidato) {
                    throw new \Exception('Este candidato individual ya está registrado');
                }

                // Crear candidato individual SIN partido
                $candidato = Candidato::create([
                    'idUsuario' => $c['idUsuario'],
                    'idCargo' => $c['idCargo'],
                    'idPartido' => null,
                    'planTrabajo' => $c['planTrabajo'] ?? null
                ]);

                Log::info('CANDIDATO INDIVIDUAL CREADO', $candidato->toArray());

                // Asociar candidato a elección
                CandidatoEleccion::create([
                    'idCandidato' => $candidato->idCandidato,
                    'idElecciones' => $request->idEleccion
                ]);

                Log::info('CANDIDATO INDIVIDUAL ASOCIADO A ELECCION', [
                    'idCandidato' => $candidato->idCandidato,
                    'idElecciones' => $request->idEleccion
                ]);

            /**
             * ==========================
             *  CANDIDATO GRUPAL
             * ==========================
             */
            } else {

                $cargo = Cargo::with('area')->findOrFail($c['idCargo']);

                Log::info('CARGO GRUPAL', $cargo->toArray());

                // Validar que sea Junta Directiva
                if ($cargo->idArea != 1) {
                    throw new \Exception('Cargo inválido para Junta Directiva');
                }

                // 🔒 VALIDACIÓN: Evitar duplicados grupales
                $existeCandidato = Candidato::where([
                    'idUsuario' => $c['idUsuario'],
                    'idCargo' => $c['idCargo'],
                    'idPartido' => $c['idPartido']
                ])->exists();

                if ($existeCandidato) {
                    throw new \Exception('Este candidato grupal ya está registrado en este partido');
                }

                // Asegurar relación Partido - Elección
                $existeRelacion = PartidoEleccion::where('idPartido', $c['idPartido'])
                    ->where('idElecciones', $request->idEleccion)
                    ->exists();

                if (!$existeRelacion) {
                    PartidoEleccion::create([
                        'idPartido' => $c['idPartido'],
                        'idElecciones' => $request->idEleccion
                    ]);

                    Log::info('PARTIDO GRUPAL ASOCIADO A ELECCION', [
                        'idPartido' => $c['idPartido'],
                        'idElecciones' => $request->idEleccion
                    ]);
                }

                // Si viene plan de trabajo grupal, guardarlo en el Partido
                if (!empty($c['planTrabajo'])) {
                    $partido = Partido::findOrFail($c['idPartido']);
                    $partido->planTrabajo = $c['planTrabajo'];
                    $partido->save();

                    Log::info('PLAN TRABAJO GUARDADO EN PARTIDO (GRUPAL)', [
                        'idPartido' => $partido->idPartido,
                        'planTrabajo' => $partido->planTrabajo
                    ]);
                }

                // Crear candidato grupal CON partido (sin planTrabajo en candidato)
                $candidato = Candidato::create([
                    'idUsuario' => $c['idUsuario'],
                    'idCargo' => $c['idCargo'],
                    'idPartido' => $c['idPartido']
                ]);

                Log::info('CANDIDATO GRUPAL CREADO', $candidato->toArray());
            }
        }
    });

    return redirect()
        ->route('crud.candidato.ver')
        ->with('success', 'Candidatos creados correctamente.');
}


    public function show($id)
    {
        $c = Candidato::findOrFail($id);
        $tipo = $c->idPartido === null ? 'Individual' : 'Grupal';
        
        return response()->json([
            'success' => true,
            'message' => 'Candidato obtenido',
            'data' => [
                'tipo' => $tipo,
                'idPartido' => $c->idPartido,
                'partido' => $c->partido->partido ?? null,
                'urlPartido' => $c->partido->urlPartido ?? null,
                'descripcion' => $c->partido->descripcion ?? null,
                'idCargo' => $c->idCargo,
                'idUsuario' => $c->idUsuario,
                'planTrabajo' => $tipo === 'Grupal' ? ($c->partido->planTrabajo ?? null) : $c->planTrabajo,
            ],
        ]);
    }

    public function edit($id)
    {
        $candidato = Candidato::findOrFail($id);
        $partidos = \App\Models\Partido::where('tipo', 'LISTA')->get();
        $cargos = \App\Models\Cargo::all();
        $usuarios = \App\Models\User::all();
        $elecciones = \App\Models\Elecciones::all();
        return view('crud.candidato.editar', compact('candidato', 'partidos', 'cargos', 'usuarios', 'elecciones'));
    }

    public function update(Request $request, $id)
    {
        $c = Candidato::findOrFail($id);
        
        // ✅ VALIDACIÓN GENERAL
        $data = $request->validate([
            'tipo' => 'required|in:Individual,Grupal',
            'idPartido' => 'nullable|integer|exists:Partido,idPartido',
            'idCargo' => 'required|integer|exists:Cargo,idCargo',
            'idUsuario' => 'required|integer|exists:User,idUser',
            'planTrabajo' => 'nullable|string',
        ]);

        // 🔒 VALIDACIÓN: Grupal requiere partido
        if ($data['tipo'] === 'Grupal' && empty($data['idPartido'])) {
            throw new \Exception('El candidato grupal requiere partido');
        }

        // 🔒 VALIDACIÓN: Individual no puede tener partido
        if ($data['tipo'] === 'Individual') {
            $data['idPartido'] = null;
        }

        // Validar que no exista duplicado
        $duplicado = Candidato::where('idUsuario', $data['idUsuario'])
            ->where('idCargo', $data['idCargo'])
            ->where('idCandidato', '!=', $id);
        
        if ($data['tipo'] === 'Grupal') {
            $duplicado->where('idPartido', $data['idPartido']);
        } else {
            $duplicado->whereNull('idPartido');
        }

        if ($duplicado->exists()) {
            throw new \Exception('Este candidato ya está registrado con esta configuración');
        }

        if ($data['tipo'] === 'Grupal') {
            // Guardar plan de trabajo en el partido
            if (!empty($data['idPartido'])) {
                $partido = Partido::findOrFail($data['idPartido']);
                $partido->planTrabajo = $data['planTrabajo'] ?? $partido->planTrabajo;
                $partido->save();
            }

            // Actualizar candidato SIN planTrabajo
            $c->update([
                'idPartido' => $data['idPartido'],
                'idCargo' => $data['idCargo'],
                'idUsuario' => $data['idUsuario']
            ]);
        } else {
            // Individual: guardar planTrabajo en candidato y limpiar partido
            $c->update([
                'idPartido' => null,
                'idCargo' => $data['idCargo'],
                'idUsuario' => $data['idUsuario'],
                'planTrabajo' => $data['planTrabajo']
            ]);
        }

        return redirect()->route('crud.candidato.ver')
                         ->with('success', 'Candidato actualizado correctamente.');
    }

    public function destroy($id)
    {
        $c = Candidato::findOrFail($id);
        $c->delete();

        return redirect()->route('crud.candidato.ver')
                         ->with('success', 'Candidato eliminado correctamente.');
    }
}
