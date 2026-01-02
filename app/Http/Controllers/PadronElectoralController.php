<?php

namespace App\Http\Controllers;

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

    public function edit($id)
    {
        $padron = PadronElectoral::findOrFail($id);
        return view('crud.padron_electoral.editar', compact('padron'));
    }

    public function update(Request $request, $id)
    {
        $p = PadronElectoral::findOrFail($id);
        $data = $request->validate([
            'idEstadoParticipante' => 'required|integer',
        ]);
        $p->update($data);
        return response()->json([
            'success' => true,
            'message' => 'Padrón actualizado',
            'data' => [
                'id' => $p->getKey(),
                'idElecciones' => $p->idElecciones,
                'idParticipante' => $p->idParticipante,
            ],
        ]);
    }

    public function importForm()
    {
        return view('crud.padron_electoral.importar');
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

    private static function importFromCSV(string $path): Iterator{
        $csv = Reader::createFromPath($path, 'r');
        $csv->setHeaderOffset(0);
        $registros = $csv->getRecords();

        $mapa = [
            'correo' => 0,
            // 'area' => 1,
            'nombres' => 2,
            'apellidos' => 3,
            'dni' => 4,
            'telefono' => 5,
        ];

        return new CSVIteratorAdapter($registros, $mapa);
    }

    private static function importFromXLSX(string $path, bool $hasHeader = true): Iterator{
        $lector = new XLSXReader();
        $lector->open($path);
        $lector->changeSheet(0);

        if ($hasHeader) $lector->next();
 
        return $lector;
    }

    private static function readFile(string $path): Iterator {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        return match ($extension) {
            'csv' => self::importFromCSV($path),
            'xlsx' => self::importFromXLSX($path),
            default => throw new \InvalidArgumentException("Formato de archivo no soportado: $extension"),
        };
    }

    public static function importFromFile(Elecciones $eleccion, string $path) {
        $registros = self::readFile($path);

        $correosExistentes = PadronElectoral::query()
            ->where('idElecciones', '=', $eleccion->getKey())
            ->join('User', 'PadronElectoral.idUsuario', '=', 'User.idUser')
            ->get()
            ->pluck('correo');

        $dniExistentes = PadronElectoral::query()
            ->where('idElecciones', '=', $eleccion->getKey())
            ->join('User', 'PadronElectoral.idUsuario', '=', 'User.idUser')
            ->join('PerfilUsuario', 'User.idUser', '=', 'PerfilUsuario.idUser')
            ->get()
            ->pluck('dni');

        foreach ($registros as $indice => $registro) {
            $correo = (string) $registro[0];
            /* Area por implementar */
            // $area = (int) $registro[1];
            $nombres = explode(' ',(string) $registro[2]);
            $apellidos = explode(' ', (string) $registro[3]);
            $dni = (string) $registro[4];
            $telefono = (string) $registro[5];

            $correoDuplicado = $correosExistentes->contains($correo);
            $dniDuplicado = $dniExistentes->contains($dni);

            if ($correoDuplicado || $dniDuplicado) {
                $datosOmitidos[] = [
                    'indice' => $indice,
                    'correo' => $correo,
                    'nombres' => join(' ', $nombres),
                    'apellidos' => join(' ', $apellidos),
                    'dni' => $dni,
                    'telefono' => $telefono,
                    'razon' => $correoDuplicado ? 'Correo duplicado' : 'DNI duplicado',
                ];

                continue;
            }

            $correosExistentes->push($correo);
            $dniExistentes->push($dni);

            $user = User::createOrFirst([
                'correo' => $correo,
                'contraseña' => bcrypt($dni),
                'idEstadoUsuario' => 1, // Activo
            ]);

            $user->save();

            $perfil = $user->perfil()->createOrFirst([
                'apellidoPaterno' => $apellidos[0] ?? '',
                'apellidoMaterno' => $apellidos[1] ?? '',
                'nombre' => $nombres[0] ?? '',
                'otrosNombres' => join(' ', array_slice($nombres, 1)) ?? '',
                'dni' => $dni,
                'telefono' => $telefono,
                'idCarrera' => 1, // Temporal: sin carrera asignada
                'idArea' => 1, // Temporal: sin área asignada
            ]);

            $perfil->save();

            $registroEnPadron = PadronElectoral::createOrFirst([
                'idElecciones' => $eleccion->getKey(),
                'idUsuario' => $user->getKey(),
                'fechaVoto' => Carbon::now(),
            ]);

            $registroEnPadron->save();
        }

        $message = 'Importación completada';
        if (count($datosOmitidos) > 0) $message .= ' con ' . count($datosOmitidos) . ' registros omitidos';

        return [
            'success' => true,
            'message' => $message,
            'data' => [
                'registrosOmitidos' => $datosOmitidos,
            ],
        ];
    }
}

class CSVIteratorAdapter implements Iterator {
    private Iterator $iterator;
    private array $map;

    public function __construct(Iterator $reader, array $map) {
        $this->iterator = $reader;
        $this->map = $map;
    }

    public function rewind(): void { $this->iterator->rewind(); }

    public function valid(): bool { return $this->iterator->valid(); }

    public function next(): void { $this->iterator->next(); }

    public function key(): int { return $this->iterator->key(); }

    public function current(): array { 
        $row = $this->iterator->current();
        $mappedRow = [];
        foreach ($this->map as $key => $value) {
            $mappedRow[$value] = $row[$key] ?? null;
        }
        return $mappedRow;
    }
}