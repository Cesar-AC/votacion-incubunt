<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AreaController extends Controller
{
    public function index()
    {
       $areas = Area::orderBy('idArea', 'desc')->get();
    return view('crud.area.ver', compact('areas'));
    }

    public function create()
    {
        return view('crud.area.crear');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'area' => 'required|string|max:30',
        ]);

        $area = new Area($data);
        $area->save();

        return redirect()
            ->route('crud.area.ver')
            ->with('success', 'Área creada correctamente');
    }

    public function show($id)
    {
        $area = Area::findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Área obtenida',
            'data' => [
                'area' => $area->area,
            ],
        ]);
    }

    public function edit($id)
    {
        $area = Area::findOrFail($id);
        return view('crud.area.editar', compact('area'));
    }

    public function update(Request $request, $id)
    {
        $area = Area::findOrFail($id);

        $data = $request->validate([
            'area' => 'required|string|max:30',
        ]);

        $area->update($data);

        return redirect()
            ->route('crud.area.ver')
            ->with('success', 'Área actualizada correctamente');
    }

    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        $area->delete();

        return response()->json([
            'success' => true,
            'message' => 'Área eliminada correctamente',
            'data' => [
                'id' => (int) $id,
                'area' => $area->area,
            ],
        ]);
    }
}
