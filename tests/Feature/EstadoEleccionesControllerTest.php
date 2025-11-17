<?php

namespace Tests\Feature;

use App\Models\Permiso;
use App\Models\User;
use App\Models\EstadoElecciones;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EstadoEleccionesControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const PERMISO_GLOBAL_CRUD = 'gestion.estado_elecciones.*';
    private const RUTA_VER = 'crud.estado_elecciones.ver';
    private const RUTA_VER_DATOS = 'crud.estado_elecciones.ver_datos';
    private const RUTA_CREAR = 'crud.estado_elecciones.crear';
    private const RUTA_EDITAR = 'crud.estado_elecciones.editar';
    private const RUTA_ELIMINAR = 'crud.estado_elecciones.eliminar';

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

    public function test_cargar_vista_listado_estado_elecciones()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_VER));
        $response->assertViewIs('crud.estado_elecciones.ver');
        $response->assertOk();
    }

    public function test_cargar_vista_crear_estado_elecciones()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_CREAR));
        $response->assertViewIs('crud.estado_elecciones.crear');
        $response->assertOk();
    }

    public function test_crear_estado_elecciones()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $datos = [
            'estado' => fake()->words(2, true),
        ];

        $response = $this->post(route(self::RUTA_CREAR), $datos);
        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'estado',
            ],
        ]);
    }

    public function test_ver_datos_estado_elecciones()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $estado = new EstadoElecciones([
            'estado' => fake()->words(2, true),
        ]);
        $estado->save();

        $response = $this->get(route(self::RUTA_VER_DATOS, [$estado->getKey()]));
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'estado',
            ],
        ]);
    }

    public function test_cargar_vista_editar_estado_elecciones()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $estado = new EstadoElecciones([
            'estado' => fake()->words(2, true),
        ]);
        $estado->save();

        $response = $this->get(route(self::RUTA_EDITAR, [$estado->getKey()]));
        $response->assertViewIs('crud.estado_elecciones.editar');
        $response->assertOk();
    }

    public function test_actualizar_estado_elecciones()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $estado = new EstadoElecciones([
            'estado' => fake()->words(2, true),
        ]);
        $estado->save();

        $datos = [
            'estado' => fake()->words(2, true),
        ];

        $response = $this->post(route(self::RUTA_EDITAR, [$estado->getKey()]), $datos);
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'estado',
            ],
        ]);
    }

    public function test_eliminar_estado_elecciones()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $estado = new EstadoElecciones([
            'estado' => fake()->words(2, true),
        ]);
        $estado->save();

        $response = $this->delete(route(self::RUTA_ELIMINAR, [$estado->getKey()]));
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'estado',
            ],
        ]);
    }
}
