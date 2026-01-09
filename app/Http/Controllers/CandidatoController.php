<?php

namespace App\Http\Controllers;

use App\Models\Candidato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Partido;
use App\Models\PartidoEleccion;
use App\Models\Cargo;

class CandidatoController extends Controller
{
    public function index()
{
    $elecciones = \App\Models\Elecciones::with([
        'partidos.candidatos.usuario.perfil',
        'partidos.candidatos.cargo.area'
    ])->get();

    return view('crud.candidato.ver', compact('elecciones'));
}

public function create()
{
    $partidos   = \App\Models\Partido::all();
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

            // 🔒 VALIDACIÓN: Evitar duplicados
            $existeCandidato = Candidato::where([
                'idUsuario' => $c['idUsuario'],
                'idCargo' => $c['idCargo'],
                'idPartido' => $c['idPartido']
            ])->exists();

            if ($existeCandidato) {
                throw new \Exception('Este candidato ya está registrado');
            }

            /**
             * ==========================
             *  CANDIDATO INDIVIDUAL
             * ==========================
             */
            if ($c['tipo'] === 'Individual') {

                // Crear partido individual
                $partido = Partido::create([
                    'partido' => 'Candidato ' . $c['idUsuario'],
                    'descripcion' => 'Candidato individual',
                    'tipo' => 'INDIVIDUAL',
                    'urlPartido' => 'hi',
                ]);

                Log::info('PARTIDO INDIVIDUAL CREADO', $partido->toArray());

                // Asociar partido a elección
                PartidoEleccion::create([
                    'idPartido' => $partido->idPartido,
                    'idElecciones' => $request->idEleccion
                ]);

                Log::info('PARTIDO INDIVIDUAL ASOCIADO A ELECCION', [
                    'idPartido' => $partido->idPartido,
                    'idElecciones' => $request->idEleccion
                ]);

                // Crear candidato
                Candidato::create([
                    'idUsuario' => $c['idUsuario'],
                    'idCargo' => $c['idCargo'],
                    'idPartido' => $partido->idPartido
                ]);

                Log::info('CANDIDATO INDIVIDUAL CREADO', $c);

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

                // Crear candidato
                Candidato::create([
                    'idUsuario' => $c['idUsuario'],
                    'idCargo' => $c['idCargo'],
                    'idPartido' => $c['idPartido']
                ]);

                Log::info('CANDIDATO GRUPAL CREADO', $c);
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
        return response()->json([
            'success' => true,
            'message' => 'Candidato obtenido',
            'data' => [
                'idPartido' => $c->idPartido,
                'idCargo' => $c->idCargo,
                'idUsuario' => $c->idUsuario,
            ],
        ]);
    }

    public function edit($id)
    {
        $candidato = Candidato::findOrFail($id);
        $partidos = \App\Models\Partido::all();
        $cargos = \App\Models\Cargo::all();
        $usuarios = \App\Models\User::all();
        $elecciones = \App\Models\Eleccion::all();
        return view('crud.candidato.editar', compact('candidato', 'partidos', 'cargos', 'usuarios', 'elecciones'));
    }

    public function update(Request $request, $id)
    {
        $c = Candidato::findOrFail($id);
        $data = $request->validate([
            'idPartido' => 'nullable|integer',
            'idCargo' => 'required|integer',
            'idUsuario' => 'required|integer',
            'idEleccion' => 'required|integer',
        ]);
        $c->update($data);

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
