<?php

namespace App\Http\Controllers;

use App\Interfaces\Services\PadronElectoral\IImportadorFactory;
use App\Interfaces\Services\PadronElectoral\IImportadorService;
use App\Models\Elecciones;
use App\Models\PadronElectoral;
use App\Models\User;
use App\Models\EstadoElecciones;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Iterator;
use League\Csv\Reader;
use Aspera\Spreadsheet\XLSX\Reader as XLSXReader;

class PadronElectoralController extends Controller
{
    public function index()
    {
        $elecciones = Elecciones::withCount([
            'usuarios as participantes_count'
        ])
            ->having('participantes_count', '>', 0)
            ->orderBy('fechaInicio', 'desc')
            ->get();

        return view('crud.padron_electoral.ver', compact('elecciones'));
    }

    public function create()
    {
        $elecciones = Elecciones::where('idEstado', EstadoElecciones::PROGRAMADO)
            ->orderBy('fechaInicio', 'desc')
            ->get();

        $usuarios = User::with('perfil')->orderBy('idUser')->get();

        return view('crud.padron_electoral.crear', compact('elecciones', 'usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idElecciones' => 'required|exists:Elecciones,idElecciones',
            'usuarios' => 'required|array',
            'usuarios.*' => 'exists:User,idUser',
        ]);

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
        $p = PadronElectoral::findOrFail($id);
        return response()->json([
            'success' => true,
            'message' => 'Padrón obtenido',
            'data' => [
                'idElecciones' => $p->idElecciones,
                'idParticipante' => $p->idParticipante,
            ],
        ]);
    }

    public function edit($idElecciones)
    {
        $eleccion = Elecciones::findOrFail($idElecciones);
        $usuarios = User::with('perfil')->orderBy('idUser')->get();
        $padronUsuarios = PadronElectoral::where('idElecciones', $idElecciones)->pluck('idUsuario')->toArray();
        return view('crud.padron_electoral.editar', compact('eleccion', 'usuarios', 'padronUsuarios'));
    }

    public function update(Request $request, $idElecciones)
    {
        $data = $request->validate([
            'usuarios' => 'required|array',
            'usuarios.*' => 'exists:User,idUser',
        ]);

        // Remove existing
        PadronElectoral::where('idElecciones', $idElecciones)->delete();

        // Add selected users
        foreach ($data['usuarios'] as $idUsuario) {
            PadronElectoral::create([
                'idElecciones' => $idElecciones,
                'idUsuario' => $idUsuario,
            ]);
        }

        return redirect()
            ->route('crud.padron_electoral.ver')
            ->with('success', 'Padrón actualizado correctamente');
    }

    public function importForm()
    {
        $elecciones = Elecciones::where('idEstado', EstadoElecciones::PROGRAMADO)
            ->orderBy('fechaInicio', 'desc')
            ->get();

        return view('crud.padron_electoral.importar', compact('elecciones'));
    }

    public function import(Request $request)
    {
        $data = $request->validate([
            'idElecciones' => 'required|integer',
            'participantes' => 'required|array',
            'participantes.*' => 'integer',
        ]);

        $created = [];
        $skipped = [];
        foreach ($data['participantes'] as $idUser) {
            $exists = PadronElectoral::query()
                ->where('idElecciones', $data['idElecciones'])
                ->where('idParticipante', $idUser)
                ->exists();
            if ($exists) {
                $skipped[] = $idUser;
                continue;
            }
            $p = new PadronElectoral([
                'idElecciones' => $data['idElecciones'],
                'idParticipante' => $idUser
            ]);
            $p->save();
            $created[] = [
                'id' => $p->getKey(),
                'idParticipante' => $idUser,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Importación de padrón completada',
            'data' => [
                'idElecciones' => $data['idElecciones'],
                'creados' => $created,
                'omitidos' => $skipped,
            ],
        ], Response::HTTP_CREATED);
    }

    public function destroy($id)
    {
        $p = PadronElectoral::findOrFail($id);
        $p->delete();
        return response()->json([
            'success' => true,
            'message' => 'Padrón eliminado',
            'data' => [
                'id' => (int) $id,
                'idElecciones' => $p->idElecciones,
                'idParticipante' => $p->idUser
            ],
        ]);
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
