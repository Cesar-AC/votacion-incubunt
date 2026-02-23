<?php

namespace App\Http\Controllers;

use App\Interfaces\Services\IAreaService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'siglas' => 'required|string|max:10|unique:Area,siglas',
        ], [
            'area.required' => 'El nombre del área es obligatorio.',
            'area.string' => 'El nombre del área debe ser texto.',
            'area.max' => 'El nombre del área no puede exceder 100 caracteres.',
            'area.unique' => 'El nombre del área ya existe.',
            'siglas.required' => 'Las siglas del área son obligatorias.',
            'siglas.string' => 'Las siglas del área deben ser texto.',
            'siglas.max' => 'Las siglas del área no pueden exceder 10 caracteres.',
            'siglas.unique' => 'Las siglas del área ya existen.',
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
            'area' => [
                'required',
                'string',
                'max:100',
                Rule::unique('Area', 'area')->ignore($area->getKey(), 'idArea')
            ],
            'siglas' => [
                'required',
                'string',
                'max:10',
                Rule::unique('Area', 'siglas')->ignore($area->getKey(), 'idArea')
            ],
        ], [
            'area.required' => 'El nombre del área es obligatorio.',
            'area.string' => 'El nombre del área debe ser texto.',
            'area.max' => 'El nombre del área no puede exceder 100 caracteres.',
            'area.unique' => 'El nombre del área ya existe.',
            'siglas.required' => 'Las siglas del área son obligatorias.',
            'siglas.string' => 'Las siglas del área deben ser texto.',
            'siglas.max' => 'Las siglas del área no pueden exceder 10 caracteres.',
            'siglas.unique' => 'Las siglas del área ya existen.',
        ]);

        $this->areaService->editarArea($request->all(), $area);

        return redirect()
            ->route('crud.area.ver')
            ->with('success', 'Área actualizada correctamente');
    }

    public function destroy(int $id)
    {
        try {
            $area = $this->areaService->obtenerAreaPorId($id);

            $this->areaService->eliminarArea($area);

            return redirect()
                ->route('crud.area.ver')
                ->with('success', 'Área eliminada correctamente');
        } catch (\Exception $e) {
            return redirect()
                ->route('crud.area.ver')
                ->with('error', 'Error al eliminar el área: ' . $e->getMessage());
        }
    }
}
