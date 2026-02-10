<?php

namespace App\Http\Controllers;

use App\Interfaces\Services\PadronElectoral\IImportadorService;
use App\Interfaces\Services\IEleccionesService;
use App\Interfaces\Services\IPadronElectoralService;
use App\Models\Elecciones;
use App\Models\PadronElectoral;
use App\Models\User;
use App\Models\EstadoElecciones;
use Illuminate\Http\Request;

class PadronElectoralController extends Controller
{
	public function __construct(protected IEleccionesService $eleccionesService, protected IPadronElectoralService $padronElectoralService) {}

	public function index()
	{
		$elecciones = $this->eleccionesService->obtenerTodasLasEleccionesProgramables();

		return view('crud.padron_electoral.ver', compact('elecciones'));
	}

	public function create()
	{
		$elecciones = $this->eleccionesService->obtenerTodasLasEleccionesProgramables();

		$usuarios = User::with('perfil')->orderBy('idUser')->get();

		return view('crud.padron_electoral.crear', compact('elecciones', 'usuarios'));
	}

	public function store(Request $request)
	{
		$data = $request->validate(
			[
				'idElecciones' => 'required|exists:Elecciones,idElecciones',
				'usuarios' => 'required|array',
				'usuarios.*' => 'exists:User,idUser',
			],
			[
				'idElecciones.required' => 'Debe seleccionar una elección.',
				'idElecciones.exists' => 'La elección seleccionada no existe.',
				'usuarios.required' => 'Debe seleccionar al menos un usuario.',
				'usuarios.array' => 'Los usuarios deben ser un array.',
				'usuarios.*.exists' => 'El usuario seleccionado no existe.',
			]
		);

		foreach ($data['usuarios'] as $idUsuario) {
			PadronElectoral::firstOrCreate([
				'idElecciones' => $data['idElecciones'],
				'idUsuario' => $idUsuario,
			]);
		}

		return redirect()
			->route('crud.padron_electoral.ver')
			->with('success', 'Padrón creado correctamente');
	}

	public function show($id)
	{
		$eleccion = $this->eleccionesService->obtenerEleccionPorId($id);

		// Obtener todos los usuarios del padrón con sus perfiles
		$participantes = PadronElectoral::where('idElecciones', $id)
			->with(['usuario.perfil'])
			->get()
			->map(function ($padron) {
				return $padron->usuario;
			})
			->sortBy('idUser');

		return view('crud.padron_electoral.detalle', compact('eleccion', 'participantes'));
	}

	public function edit(int $id)
	{
		$eleccion = $this->eleccionesService->obtenerEleccionPorId($id);

		// Validar si se puede editar el padrón
		if ($this->eleccionesService->votacionHabilitada($eleccion)) {
			return redirect()
				->route('crud.padron_electoral.ver')
				->with('warning', 'No se puede editar el padrón: La elección ya se encuentra en periodo de votación.');
		}

		if ($eleccion->estaFinalizado()) {
			return redirect()
				->route('crud.padron_electoral.ver')
				->with('warning', 'No se puede editar el padrón: La elección ya ha finalizado.');
		}

		if ($eleccion->estaAnulado()) {
			return redirect()
				->route('crud.padron_electoral.ver')
				->with('warning', 'No se puede editar el padrón: La elección está anulada.');
		}

		$usuarios = User::with('perfil')->orderBy('idUser')->get();
		$padronUsuarios = PadronElectoral::where('idElecciones', '=', $id)->pluck('idUsuario')->toArray();
		return view('crud.padron_electoral.editar', compact('eleccion', 'usuarios', 'padronUsuarios'));
	}

	public function update(Request $request, int $id)
	{
		try {
			$eleccion = $this->eleccionesService->obtenerEleccionPorId($id);

			$data = $request->validate([
				'usuarios' => 'required|array',
				'usuarios.*' => 'exists:User,idUser',
			]);

			PadronElectoral::where('idElecciones', '=', $id)->delete();

			foreach ($data['usuarios'] as $idUsuario) {
				$this->padronElectoralService->agregarUsuarioAEleccion(User::find($idUsuario), $eleccion);
			}

			return redirect()
				->route('crud.padron_electoral.ver')
				->with('success', 'Padrón actualizado correctamente');
		} catch (\Exception $e) {
			return redirect()
				->route('crud.padron_electoral.ver')
				->with('error', $e->getMessage());
		}
	}

	public function destroy(int $id)
	{
		try {
			$eleccion = $this->eleccionesService->obtenerEleccionPorId($id);
			$this->padronElectoralService->restablecerPadronElectoral($eleccion);

			return redirect()
				->route('crud.padron_electoral.ver')
				->with('success', 'Padrón electoral eliminado correctamente');
		} catch (\Exception $e) {
			return redirect()
				->route('crud.padron_electoral.ver')
				->with('error', 'Error al eliminar el padrón electoral: ' . $e->getMessage());
		}
	}

	public function importForm()
	{
		$elecciones = Elecciones::where('idEstado', EstadoElecciones::PROGRAMADO)
			->orderBy('fechaInicio', 'desc')
			->get();

		return view('crud.padron_electoral.importar', compact('elecciones'));
	}

	public function importar(Request $request, IImportadorService $importadorService)
	{
		$data = $request->validate([
			'idElecciones' => 'required|integer',
			'archivo' => 'required|file|mimes:csv,xlsx',
		]);

		$eleccion = Elecciones::findOrFail($data['idElecciones']);
		$path = $request->file('archivo')->store('padron_electoral');

		$importadorService->importar(storage_path('app/private/' . $path), $eleccion);

		unlink(storage_path('app/private/' . $path));

		return redirect()->route('crud.padron_electoral.ver')
			->with('success', 'Padrón importado correctamente');
	}
}
