<?php

namespace App\Http\Controllers;

use App\Models\Participante;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ParticipanteController extends Controller
{
    public function index(){
        return view('crud.participante.ver');
    }
    public function create(){
        return view('crud.participante.crear');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'biografia' => 'string',
            'experiencia' => 'string',
            'idUser' => 'required|integer',
            'idCarrera' => 'required|integer',
            'idEstadoParticipante' => 'required|integer'
        ]);

        $m = new Participante($data); $m->save();

        return response()->json([
            'success' => true,
            'message' => 'Participante creado',
            'data' => [
                'id' => $m->getKey(),
                'biografia' => $m->biografia,
                'experiencia'=> $m->experiencia,
                'idUser' => $m->idUser,
                'idCarrera' => $m->idCarrera,
                'idEstadoParticipante' => $m->idEstadoParticipante
            ]
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $m = Participante::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Participante obtenido',
            'data' => [
                'id' => $m->getKey(),
                'biografia' => $m->biografia,
                'experiencia'=> $m->experiencia,
                'idUser' => $m->idUser,
                'idCarrera' => $m->idCarrera,
                'idEstadoParticipante' => $m->idEstadoParticipante
            ]
        ]);
    }

    public function edit($id){ 
        return view('crud.participante.editar');
    }

    public function update(Request $request, $id)
    {
        $m = Participante::findOrFail($id);
        $data = $request->validate([
            'biografia' => 'string',
            'experiencia' => 'string',
            'idUser' => 'required|integer',
            'idCarrera' => 'required|integer',
            'idEstadoParticipante' => 'required|integer'
        ]);

        $m->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Participante actualizado',
            'data' => [
                'id' => $m->getKey(),
                'biografia' => $m->biografia,
                'experiencia'=> $m->experiencia,
                'idUser' => $m->idUser,
                'idCarrera' => $m->idCarrera,
                'idEstadoParticipante' => $m->idEstadoParticipante
            ]
        ]);
    }

    public function destroy($id)
    {
        $m = Participante::findOrFail($id);
        $m->delete();
        return response()->json([
            'success' => true,
            'message' => 'Participante eliminado',
            'data' => [
                'id' => $m->getKey(),
                'biografia' => $m->biografia,
                'experiencia'=> $m->experiencia,
                'idUser' => $m->idUser,
                'idCarrera' => $m->idCarrera,
                'idEstadoParticipante' => $m->idEstadoParticipante
            ]
        ]);
    }
}
