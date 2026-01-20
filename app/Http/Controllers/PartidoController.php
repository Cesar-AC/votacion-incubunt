<?php

namespace App\Http\Controllers;

use App\Models\Partido;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PartidoController extends Controller
{
    public function index()
    {
        $partidos = Partido::orderBy('idPartido', 'desc')->get();

        return view('crud.partido.ver', compact('partidos'));
    }

    public function create()
    {
        return view('crud.partido.crear');
    }

   public function store(Request $request)
{
    $data = $request->validate([
        'partido'     => 'required|string|max:255',
        'urlPartido'  => 'required|string',
        'descripcion' => 'required|string',
        'tipo'        => 'required|in:LISTA,INDIVIDUAL'
    ]);

    $p = Partido::create([
        'partido'     => $data['partido'],
        'urlPartido'  => $data['urlPartido'],
        'descripcion' => $data['descripcion'],
        'tipo'        => $data['tipo'],
    ]);
    return redirect()
    ->route('crud.partido.ver')
    ->with('success', 'Partido creado correctamente');

}


    public function show($id)
    {
        $p = Partido::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Partido obtenido',
            'data' => [
                'partido' => $p->partido,
                'urlPartido' => $p->urlPartido,
                'descripcion' => $p->descripcion,
                'tipo' => $p->tipo,
                'elecciones' => $p->elecciones()->pluck('idElecciones'),
            ],
        ]);
    }

    public function edit($id)
    {
        $partido = Partido::findOrFail($id);

        return view('crud.partido.editar', compact('partido'));
    }

    public function update(Request $request, $id)
    {
        $p = Partido::findOrFail($id);
        $data = $request->validate([
            'partido' => 'required|string|max:255',
            'urlPartido' => 'required|string',
            'descripcion' => 'required|string',
            'tipo' => 'required|in:LISTA,INDIVIDUAL',
            'elecciones' => 'required|array',
            'elecciones.*' => 'integer',
        ]);
        $p->update([
            'partido' => $data['partido'],
            'urlPartido' => $data['urlPartido'],
            'descripcion' => $data['descripcion'],
            'tipo' => $data['tipo'],
        ]);
        $p->elecciones()->sync($data['elecciones']);
        return response()->json([
            'success' => true,
            'message' => 'Partido actualizado',
            'data' => [
                'id' => $p->getKey(),
                'partido' => $p->partido,
                'urlPartido' => $p->urlPartido,
                'descripcion' => $p->descripcion,
                'tipo' => $p->tipo,
                'elecciones' => $p->elecciones()->pluck('idElecciones'),
            ],
        ]);
    }

    public function destroy($id)
    {
        $p = Partido::findOrFail($id);
        $p->elecciones()->detach();
        $p->delete();
        return response()->json([
            'success' => true,
            'message' => 'Partido eliminado',
            'data' => [
                'id' => (int) $id,
                'partido' => $p->partido,
                'urlPartido' => $p->urlPartido,
                'descripcion' => $p->descripcion,
                'tipo' => $p->tipo,
            ],
        ]);
    }
}
