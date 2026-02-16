<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CargoController extends Controller
{
    public function index()
    {
        $cargos = Cargo::with('area')->get();
        return view('crud.cargo.ver', compact('cargos'));
    }

    public function create()
    {
        $areas = \App\Models\Area::all();
        return view('crud.cargo.crear', compact('areas'));
    }

    public function store(Request $request)
{
    $data = $request->validate([
        // 'idCargo' no es necesario si es auto-increment
        'cargo' => 'required|string|max:100',
        'idArea' => 'required|integer',
    ]);

    $c = new Cargo($data);
    $c->save();

    // Redirige a la lista de cargos con un mensaje de éxito
    return redirect()->route('crud.cargo.ver')
                     ->with('success', 'Cargo creado correctamente');
}


    public function show($id)
    {
        $c = Cargo::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Cargo obtenido',
            'data' => [
                'cargo' => $c->cargo,
                'idArea' => $c->idArea,
            ],
        ]);
    }

    public function edit($id)
    {
        $cargo = Cargo::findOrFail($id);
        $areas = \App\Models\Area::all();
        return view('crud.cargo.editar', compact('cargo', 'areas'));
    }

    public function update(Request $request, $id)
    {
        $c = Cargo::findOrFail($id);
        $data = $request->validate([
            'cargo' => 'required|string|max:100',
            'idArea' => 'required|integer',
        ]);
        $c->update($data);
        return redirect()
            ->route('crud.cargo.ver')
            ->with('success', 'Cargo actualizado correctamente');
    }

    public function destroy($id)
    {
        try {
            $c = Cargo::findOrFail($id);
            $c->delete();
            return redirect()
                ->route('crud.cargo.ver')
                ->with('success', 'Cargo eliminado correctamente');
        } catch (\Exception $e) {
            return redirect()
                ->route('crud.cargo.ver')
                ->with('error', 'Error al eliminar el cargo: ' . $e->getMessage());
        }
    }
}
