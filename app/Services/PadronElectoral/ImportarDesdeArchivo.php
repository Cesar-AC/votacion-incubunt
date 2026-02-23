<?php

namespace App\Services\PadronElectoral;

use App\Interfaces\Services\ArchivosTabla\ILectorArchivoTabla;
use App\Interfaces\Services\IAreaService;
use App\Interfaces\Services\PadronElectoral\IImportarDesdeArchivo;
use App\Models\Elecciones;
use App\Models\EstadoUsuario;
use App\Models\PadronElectoral;
use App\Models\PerfilUsuario;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ImportarDesdeArchivo implements IImportarDesdeArchivo
{
    public function __construct(
        protected ILectorArchivoTabla $lector
    ) {}

    protected function obtenerAreas(): array
    {
        $areaService = app(IAreaService::class);

        return $areaService->obtenerTodasLasAreas()->pluck('idArea', 'siglas')->toArray();
    }

    public function importar(string $ruta, Elecciones $eleccion): void
    {
        $areasMap = $this->obtenerAreas();
        $iterador = $this->lector->leer($ruta);

        // Collect all records first
        $registros = [];
        foreach ($iterador as $registro) {
            $registros[] = $registro;
        }

        // Process in chunks
        $chunks = array_chunk($registros, 500);

        foreach ($chunks as $chunk) {
            DB::transaction(function () use ($chunk, $eleccion, $areasMap) {
                $this->procesarChunk($chunk, $eleccion, $areasMap);
            });
        }
    }

    protected function procesarChunk(array $registros, Elecciones $eleccion, array $areasMap): void
    {
        $rowsByCorreo = [];

        foreach ($registros as $row) {
            $correo = trim((string) $row['correo']);
            if (!$correo || isset($rowsByCorreo[$correo])) continue;
            $rowsByCorreo[$correo] = $row;
        }

        if (empty($rowsByCorreo)) return;

        $correos = array_keys($rowsByCorreo);

        $existingUsers = User::whereIn('correo', $correos)->pluck('idUser', 'correo');

        $usersToInsert = [];
        foreach ($correos as $correo) {
            if ($existingUsers->has($correo)) continue;

            $row = $rowsByCorreo[$correo];
            $nombres = explode(' ', (string) $row['nombres']);
            $apellidos = explode(' ', (string) $row['apellidos']);
            $dni = (string) $row['dni'];

            $nombre = $nombres[0] ?? '';
            $apellidoPaterno = $apellidos[0] ?? '';

            $contraseñaPlana = substr($nombre, 0, 3) . substr($apellidoPaterno, 0, 3) . $dni;

            $usersToInsert[] = [
                'correo' => $correo,
                'contraseña' => bcrypt($contraseñaPlana),
                'idEstadoUsuario' => EstadoUsuario::ACTIVO,
            ];
        }

        if (!empty($usersToInsert)) {
            User::insert($usersToInsert);
        }

        $allUsers = User::whereIn('correo', $correos)->pluck('idUser', 'correo');

        $perfilesData = [];
        $rolesData = [];
        $padronData = [];

        $userIds = $allUsers->values()->toArray();
        $existingProfiles = PerfilUsuario::whereIn('idUser', $userIds)->pluck('idUser')->flip();
        $existingRoles = DB::table('RolUser')
            ->where('idRol', Rol::ID_VOTANTE)
            ->whereIn('idUser', $userIds)
            ->pluck('idUser')
            ->flip();
        $existingPadron = PadronElectoral::where('idElecciones', $eleccion->getKey())
            ->whereIn('idUsuario', $userIds)
            ->pluck('idUsuario')
            ->flip();

        foreach ($correos as $correo) {
            $userId = $allUsers->get($correo);
            if (!$userId) continue;

            $row = $rowsByCorreo[$correo];
            $areaKey = (string) $row['area'];

            if (!array_key_exists($areaKey, $areasMap)) {
                throw new \ErrorException("Undefined array key \"$areaKey\"");
            }

            $nombres = explode(' ', (string) $row['nombres']);
            $apellidos = explode(' ', (string) $row['apellidos']);

            if (!isset($existingProfiles[$userId])) {
                $perfilesData[] = [
                    'idUser' => $userId,
                    'apellidoPaterno' => $apellidos[0] ?? '',
                    'apellidoMaterno' => join(' ', array_slice($apellidos, 1)) ?? '',
                    'nombre' => $nombres[0] ?? '',
                    'otrosNombres' => join(' ', array_slice($nombres, 1)) ?? '',
                    'dni' => (string) $row['dni'],
                    'telefono' => (string) $row['telefono'],
                    'idCarrera' => 1,
                    'idArea' => $areasMap[$areaKey],
                ];
                $existingProfiles[$userId] = true;
            }

            if (!isset($existingRoles[$userId])) {
                $rolesData[] = [
                    'idUser' => $userId,
                    'idRol' => Rol::ID_VOTANTE
                ];
                $existingRoles[$userId] = true;
            }

            if (!isset($existingPadron[$userId])) {
                $padronData[] = [
                    'idElecciones' => $eleccion->getKey(),
                    'idUsuario' => $userId,
                ];
                $existingPadron[$userId] = true;
            }
        }

        if (!empty($perfilesData)) {
            PerfilUsuario::insert($perfilesData);
        }

        if (!empty($rolesData)) {
            DB::table('RolUser')->insert($rolesData);
        }

        if (!empty($padronData)) {
            PadronElectoral::insert($padronData);
        }
    }
}
