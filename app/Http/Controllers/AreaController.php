<?php

namespace App\Http\Controllers;

use App\Interfaces\Services\IAreaService;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function __construct(
        protected IAreaService $areaService
    ) {}

    public function index()
    {
        $areas = $this->areaService->obtenerAreas();

        return view('crud.area.ver', compact('areas'));
    }

    public function create()
    {
        return view('crud.area.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'area' => 'required|string|max:100|unique:Area,area',
        ], [
            'area.required' => 'El nombre del área es obligatorio.',
            'area.string' => 'El nombre del área debe ser texto.',
            'area.max' => 'El nombre del área no puede exceder 100 caracteres.',
            'area.unique' => 'El nombre del área ya existe.',
        ]);

        $this->areaService->crearArea($request->all());

        return redirect()
            ->route('crud.area.ver')
            ->with('success', 'Área creada correctamente');
    }

    public function show(int $id)
    {
        $area = $this->areaService->obtenerAreaPorId($id);

        return response()->json([
            'success' => true,
            'message' => 'Área obtenida',
            'data' => [
                'area' => $area->area,
            ],
        ]);
    }

    public function edit(int $id)
    {
        $area = $this->areaService->obtenerAreaPorId($id);
        return view('crud.area.editar', compact('area'));
    }

    public function update(Request $request, int $id)
    {
        $area = $this->areaService->obtenerAreaPorId($id);

        $request->validate([
            'area' => 'required|string|max:100|unique:Area,area',
        ], [
            'area.required' => 'El nombre del área es obligatorio.',
            'area.string' => 'El nombre del área debe ser texto.',
            'area.max' => 'El nombre del área no puede exceder 100 caracteres.',
            'area.unique' => 'El nombre del área ya existe.',
        ]);

        $this->areaService->editarArea($request->all(), $area);

        return redirect()
            ->route('crud.area.ver')
            ->with('success', 'Área actualizada correctamente');
    }

    public function destroy(int $id)
    {
        $area = $this->areaService->obtenerAreaPorId($id);

        $this->areaService->eliminarArea($area);

        return response()->json([
            'success' => true,
            'message' => 'Área eliminada correctamente',
            'data' => [
                'id' => $id,
                'area' => $area->area,
            ],
        ]);
    }
}
