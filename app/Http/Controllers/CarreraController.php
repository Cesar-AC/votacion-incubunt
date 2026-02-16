<?php

namespace App\Http\Controllers;

use App\Interfaces\Services\ICarreraService;
use Illuminate\Http\Request;

class CarreraController extends Controller
{
    public function __construct(protected ICarreraService $carreraService) {}

    public function index()
    {
        $carreras = $this->carreraService->obtenerCarreras();

        return view('crud.carrera.ver', compact('carreras'));
    }

    public function create()
    {
        return view('crud.carrera.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'carrera' => 'required|string|max:100|unique:Carrera,carrera',
        ], [
            'carrera.required' => 'El nombre de la carrera es obligatorio.',
            'carrera.string' => 'El nombre de la carrera debe ser texto.',
            'carrera.max' => 'El nombre de la carrera no puede exceder 100 caracteres.',
            'carrera.unique' => 'El nombre de la carrera ya existe.',
        ]);

        $carrera = $this->carreraService->crearCarrera($request->all());

        return redirect()
            ->route('crud.carrera.ver')
            ->with('success', 'Carrera creada correctamente');
    }

    public function show($id)
    {
        $carrera = $this->carreraService->obtenerCarreraPorId($id);

        return response()->json([
            'success' => true,
            'message' => 'Carrera obtenida',
            'data' => [
                'carrera' => $carrera->carrera,
            ],
        ]);
    }

    public function edit($id)
    {
        $carrera = $this->carreraService->obtenerCarreraPorId($id);

        return view('crud.carrera.editar', compact('carrera'));
    }

    public function update(Request $request, $id)
    {
        $carrera = $this->carreraService->obtenerCarreraPorId($id);

        $request->validate([
            'carrera' => 'required|string|max:100|unique:Carrera,carrera',
        ], [
            'carrera.required' => 'El nombre de la carrera es obligatorio.',
            'carrera.string' => 'El nombre de la carrera debe ser texto.',
            'carrera.max' => 'El nombre de la carrera no puede exceder 100 caracteres.',
            'carrera.unique' => 'El nombre de la carrera ya existe.',
        ]);

        $this->carreraService->editarCarrera($request->all(), $carrera);

        return redirect()
            ->route('crud.carrera.ver')
            ->with('success', 'Carrera actualizada correctamente');
    }

    public function destroy($id)
    {
        try {
            $carrera = $this->carreraService->obtenerCarreraPorId($id);
            $this->carreraService->eliminarCarrera($carrera);

            return redirect()
                ->route('crud.carrera.ver')
                ->with('success', 'Carrera eliminada correctamente');
        } catch (\Exception $e) {
            return redirect()
                ->route('crud.carrera.ver')
                ->with('error', 'Error al eliminar la carrera: ' . $e->getMessage());
        }
    }
}
