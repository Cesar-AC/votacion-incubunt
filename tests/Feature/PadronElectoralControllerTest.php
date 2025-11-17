<?php

namespace Tests\Feature;

use App\Models\Permiso;
use App\Models\User;
use App\Models\EstadoElecciones;
use App\Models\Elecciones;
use App\Models\EstadoParticipante;
use App\Models\PadronElectoral;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
            'fecha_inicio' => $inicio,
            'fecha_cierre' => (clone $inicio)->addMonth(),
            'estado' => $estado->getKey(),
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
}
