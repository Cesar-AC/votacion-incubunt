<?php

namespace App\Http\Controllers;

use App\Models\Elecciones;
use App\Models\PadronElectoral;
use App\Models\User;
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
        return view('crud.padron_electoral.ver');
    }

    public function create()
    {
        return view('crud.padron_electoral.crear');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idElecciones' => 'required|integer',
            'idUser' => 'required|integer'
        ]);
        $p = new PadronElectoral($data);
        $p->save();
        return response()->json([
            'success' => true,
            'message' => 'Padrón creado',
            'data' => [
                'id' => $p->getKey(),
                'idElecciones' => $p->idElecciones,
                'idParticipante' => $p->idParticipante
            ],
        ], Response::HTTP_CREATED);
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
        return view('crud.padron_electoral.editar');
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

        $datosOmitidos = [];
        $correosRegistrados = [];
        $dniRegistrados = [];
        foreach ($registros as $indice => $registro) {
            $correo = (string) $registro[0];
            /* Area por implementar */
            // $area = (int) $registro[1];
            $nombres = explode(' ',(string) $registro[2]);
            $apellidos = explode(' ', (string) $registro[3]);
            $dni = (string) $registro[4];
            $telefono = (string) $registro[5];

            $correoDuplicado = isset($correosRegistrados[$correo]);
            $dniDuplicado = isset($dniRegistrados[$dni]);

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

            $correosRegistrados[$correo] = true;
            $dniRegistrados[$dni] = true;

            $user = User::create([
                'correo' => $correo,
                'contraseña' => bcrypt($dni),
            ]);

            $user->save();

            $perfil = $user->perfil()->create([
                'apellidoPaterno' => $apellidos[0] ?? '',
                'apellidoMaterno' => $apellidos[1] ?? '',
                'nombre' => $nombres[0] ?? '',
                'otrosNombres' => join(' ', array_slice($nombres, 1)) ?? '',
                'dni' => $dni,
                'telefono' => $telefono
            ]);

            $perfil->save();

            $participante = $user->participante()->create([
                'biografia' => '',
                'experiencia' => '',
                'idUser' => $user->getKey(),
                'idEstadoParticipante' => 1,
            ]);

            $participante->save();

            $padron = new PadronElectoral([
                'idElecciones' => $eleccion->getKey(),
                'idParticipante' => $participante->getKey()
            ]);

            $padron->save();
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