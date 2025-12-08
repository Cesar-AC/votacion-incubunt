<?php

namespace Tests\Feature;

use App\Http\Controllers\PadronElectoralController;
use App\Models\Participante;
use App\Models\Permiso;
use App\Models\User;
use App\Models\EstadoElecciones;
use App\Models\Elecciones;
use App\Models\EstadoParticipante;
use App\Models\PadronElectoral;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use League\Csv\Reader;
use Aspera\Spreadsheet\XLSX\Reader as XLSXReader;
use Tests\TestCase;

class PadronElectoralControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const PERMISO_GLOBAL_CRUD = 'gestion.padron_electoral.*';
    private const RUTA_VER = 'crud.padron_electoral.ver';
    private const RUTA_VER_DATOS = 'crud.padron_electoral.ver_datos';
    private const RUTA_CREAR = 'crud.padron_electoral.crear';
    private const RUTA_EDITAR = 'crud.padron_electoral.editar';
    private const RUTA_ELIMINAR = 'crud.padron_electoral.eliminar';

    private function usuarioConPermiso(string $permiso = self::PERMISO_GLOBAL_CRUD){
        $user = User::factory()->create([
            'usuario' => fake()->userName(),
            'email' => fake()->email(),
            'password' => bcrypt(fake()->password()),
        ]);

        $perm = new Permiso([
            'permiso' => $permiso,
        ]);
        $perm->save();

        $user->permisos()->attach($perm);

        return $user;
    }

    private function crearEleccion(): Elecciones
    {
        $estado = new EstadoElecciones(['estado' => fake()->words(2, true)]);
        $estado->save();
        $inicio = now();
        $e = new Elecciones([
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'descripcion' => fake()->sentence(8),
            'fechaInicio' => $inicio,
            'fechaCierre' => (clone $inicio)->addMonth(),
            'idEstado' => $estado->getKey(),
        ]);
        $e->save();
        return $e;
    }

    private function crearEstadoParticipante(): EstadoParticipante
    {
        $ep = new EstadoParticipante([
            'idEstadoParticipante' => fake()->numberBetween(1, 9999),
            'estadoParticipante' => fake()->words(2, true),
        ]);
        $ep->save();
        return $ep;
    }

    public function test_autorizacion_negativa_listado_devuelve_404()
    {
        $user = User::factory()->create([
            'usuario' => fake()->userName(),
            'email' => fake()->email(),
            'password' => bcrypt(fake()->password()),
        ]);

        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_VER));
        $response->assertNotFound();
    }

    public function test_cargar_vista_listado_padron_electoral()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_VER));
        $response->assertViewIs('crud.padron_electoral.ver');
        $response->assertOk();
    }

    public function test_cargar_vista_crear_padron_electoral()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_CREAR));
        $response->assertViewIs('crud.padron_electoral.crear');
        $response->assertOk();
    }

    public function test_crear_padron_electoral()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $eleccion = $this->crearEleccion();
        $ep = $this->crearEstadoParticipante();

        $datos = [
            'idElecciones' => $eleccion->getKey(),
            'idUser' => $user->getKey(),
            'idEstadoParticipante' => $ep->getKey(),
        ];

        $response = $this->post(route(self::RUTA_CREAR), $datos);
        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'idElecciones',
                'idUser',
                'idEstadoParticipante',
            ],
        ]);
    }

    public function test_ver_datos_padron_electoral()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $eleccion = $this->crearEleccion();
        $ep = $this->crearEstadoParticipante();
        $padron = new PadronElectoral([
            'idElecciones' => $eleccion->getKey(),
            'idUser' => $user->getKey(),
            'idEstadoParticipante' => $ep->getKey(),
        ]);
        $padron->save();

        $response = $this->get(route(self::RUTA_VER_DATOS, [$padron->getKey()]));
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'idElecciones',
                'idUser',
                'idEstadoParticipante',
            ],
        ]);
    }

    public function test_cargar_vista_editar_padron_electoral()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $eleccion = $this->crearEleccion();
        $ep = $this->crearEstadoParticipante();
        $padron = new PadronElectoral([
            'idElecciones' => $eleccion->getKey(),
            'idUser' => $user->getKey(),
            'idEstadoParticipante' => $ep->getKey(),
        ]);
        $padron->save();

        $response = $this->get(route(self::RUTA_EDITAR, [$padron->getKey()]));
        $response->assertViewIs('crud.padron_electoral.editar');
        $response->assertOk();
    }

    public function test_actualizar_padron_electoral()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $eleccion = $this->crearEleccion();
        $ep = $this->crearEstadoParticipante();
        $padron = new PadronElectoral([
            'idElecciones' => $eleccion->getKey(),
            'idUser' => $user->getKey(),
            'idEstadoParticipante' => $ep->getKey(),
        ]);
        $padron->save();

        $datos = [
            'idEstadoParticipante' => $ep->getKey(),
        ];

        $response = $this->post(route(self::RUTA_EDITAR, [$padron->getKey()]), $datos);
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'idElecciones',
                'idUser',
                'idEstadoParticipante',
            ],
        ]);
    }

    public function test_eliminar_padron_electoral()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $eleccion = $this->crearEleccion();
        $ep = $this->crearEstadoParticipante();
        $padron = new PadronElectoral([
            'idElecciones' => $eleccion->getKey(),
            'idUser' => $user->getKey(),
            'idEstadoParticipante' => $ep->getKey(),
        ]);
        $padron->save();

        $response = $this->delete(route(self::RUTA_ELIMINAR, [$padron->getKey()]));
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'idElecciones',
                'idUser',
                'idEstadoParticipante',
            ],
        ]);
    }

    public function test_cargar_vista_importar_padron_electoral()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $response = $this->get(route('crud.padron_electoral.importar'));
        $response->assertViewIs('crud.padron_electoral.importar');
        $response->assertOk();
    }

    public function test_importar_padron_electoral_crea_y_omite_duplicados()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $eleccion = $this->crearEleccion();
        $ep = $this->crearEstadoParticipante();

        $u1 = \App\Models\User::factory()->create();
        $u2 = \App\Models\User::factory()->create();
        $u3 = \App\Models\User::factory()->create();

        // Pre-existente para forzar omisión
        $pre = new \App\Models\PadronElectoral([
            'idElecciones' => $eleccion->getKey(),
            'idUser' => $u1->getKey(),
            'idEstadoParticipante' => $ep->getKey(),
        ]);
        $pre->save();

        $payload = [
            'idElecciones' => $eleccion->getKey(),
            'idEstadoParticipante' => $ep->getKey(),
            'usuarios' => [
                $u1->getKey(), // duplicado
                $u2->getKey(), // nuevo
                $u3->getKey(), // nuevo
            ],
        ];

        $response = $this->post(route('crud.padron_electoral.importar'), $payload);
        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'idElecciones',
                'idEstadoParticipante',
                'creados',
                'omitidos',
            ],
        ]);
    }

    public function test_importar_desde_csv(){
        $this->seed();

        $ruta = base_path('tests/TestFiles/prueba_padron.csv');
        $eleccion = $this->crearEleccion();

        $respuesta = PadronElectoralController::importFromFile($eleccion, $ruta);

        $filas = Reader::createFromPath($ruta, 'r');
        $filas->setHeaderOffset(0);
        $registros = $filas->getRecords();

        foreach($registros as $registro){
            $correo = (string) $registro['correo'];
            /* Area por implementar */
            // $area = (int) $registro['area'];
            $nombres = explode(' ',(string) $registro['nombres']);
            $apellidos = explode(' ', (string) $registro['apellidos']);
            $dni = (string) $registro['dni'];
            $telefono = (string) $registro['telefono'];

            $this->assertDatabaseHas('User', [
                'correo' => $correo
            ]);

            $user = User::where('correo', $correo)->first();

            $this->assertDatabaseHas('PerfilUsuario', [
                'apellidoPaterno' => $apellidos[0] ?? '',
                'apellidoMaterno' => $apellidos[1] ?? '',
                'nombre' => $nombres[0] ?? '',
                'otrosNombres' => join(' ', array_slice($nombres, 1)) ?? '',
                'dni' => $dni,
                'telefono' => $telefono
            ]);

            $this->assertDatabaseHas('PadronElectoral', [
                'idElecciones' => $eleccion->getKey(),
                'idUsuario' => $user->getKey(),
            ]);
        }
    }

    public function test_importar_desde_xlsx(){
        $this->seed();

        $ruta = base_path('tests/TestFiles/prueba_padron.xlsx');
        $eleccion = $this->crearEleccion();

        $respuesta = PadronElectoralController::importFromFile($eleccion, $ruta);

        $lector = new XLSXReader();
        $lector->open($ruta);
        $lector->changeSheet(0);

        foreach ($lector as $indice => $registro) {
            if ($indice == 0) {
                continue;
            }

            $correo = (string) $registro[0];
            /* Area por implementar */
            // $area = (int) $row[1];
            $nombres = explode(' ',(string) $registro[2]);
            $apellidos = explode(' ', (string) $registro[3]);
            $dni = (string) $registro[4];
            $telefono = (string) $registro[5];

            $this->assertDatabaseHas('User', [
                'correo' => $correo
            ]);

            $user = User::where('correo', $correo)->first();

            $this->assertDatabaseHas('PerfilUsuario', [
                'apellidoPaterno' => $apellidos[0] ?? '',
                'apellidoMaterno' => $apellidos[1] ?? '',
                'nombre' => $nombres[0] ?? '',
                'otrosNombres' => join(' ', array_slice($nombres, 1)) ?? '',
                'dni' => $dni,
                'telefono' => $telefono
            ]);

            $this->assertDatabaseHas('PadronElectoral', [
                'idElecciones' => $eleccion->getKey(),
                'idUsuario' => $user->getKey(),
            ]);
        }
    }
}
