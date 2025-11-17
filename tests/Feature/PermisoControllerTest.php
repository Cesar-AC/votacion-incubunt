<?php

namespace Tests\Feature;

use App\Models\Permiso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PermisoControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const PERMISO_GLOBAL_CRUD = 'gestion.permiso.*';
    private const RUTA_VER = 'crud.permiso.ver';
    private const RUTA_VER_DATOS = 'crud.permiso.ver_datos';
    private const RUTA_CREAR = 'crud.permiso.crear';
    private const RUTA_EDITAR = 'crud.permiso.editar';
    private const RUTA_ELIMINAR = 'crud.permiso.eliminar';

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

    public function test_cargar_vista_listado_permiso()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_VER));
        $response->assertViewIs('crud.permiso.ver');
        $response->assertOk();
    }

    public function test_cargar_vista_crear_permiso()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_CREAR));
        $response->assertViewIs('crud.permiso.crear');
        $response->assertOk();
    }

    public function test_crear_permiso()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $datos = [
            'permiso' => 'gestion.' . fake()->word() . '.' . fake()->word(),
        ];

        $response = $this->post(route(self::RUTA_CREAR), $datos);
        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'permiso',
            ],
        ]);
    }

    public function test_ver_datos_permiso()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $permiso = new Permiso([
            'permiso' => 'gestion.' . fake()->word() . '.' . fake()->word(),
        ]);
        $permiso->save();

        $response = $this->get(route(self::RUTA_VER_DATOS, [$permiso->getKey()]));
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'permiso',
            ],
        ]);
    }

    public function test_cargar_vista_editar_permiso()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $permiso = new Permiso([
            'permiso' => 'gestion.' . fake()->word() . '.' . fake()->word(),
        ]);
        $permiso->save();

        $response = $this->get(route(self::RUTA_EDITAR, [$permiso->getKey()]));
        $response->assertViewIs('crud.permiso.editar');
        $response->assertOk();
    }

    public function test_actualizar_permiso()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $permiso = new Permiso([
            'permiso' => 'gestion.' . fake()->word() . '.' . fake()->word(),
        ]);
        $permiso->save();

        $datos = [
            'permiso' => 'gestion.' . fake()->word() . '.' . fake()->word(),
        ];

        $response = $this->post(route(self::RUTA_EDITAR, [$permiso->getKey()]), $datos);
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'permiso',
            ],
        ]);
    }

    public function test_eliminar_permiso()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $permiso = new Permiso([
            'permiso' => 'gestion.' . fake()->word() . '.' . fake()->word(),
        ]);
        $permiso->save();

        $response = $this->delete(route(self::RUTA_ELIMINAR, [$permiso->getKey()]));
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'permiso',
            ],
        ]);
    }
}
