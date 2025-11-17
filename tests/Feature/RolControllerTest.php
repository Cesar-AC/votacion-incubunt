<?php

namespace Tests\Feature;

use App\Models\Permiso;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RolControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const PERMISO_GLOBAL_CRUD = 'gestion.rol.*';
    private const RUTA_VER = 'crud.rol.ver';
    private const RUTA_VER_DATOS = 'crud.rol.ver_datos';
    private const RUTA_CREAR = 'crud.rol.crear';
    private const RUTA_EDITAR = 'crud.rol.editar';
    private const RUTA_ELIMINAR = 'crud.rol.eliminar';
    private const RUTA_AGREGAR_PERMISO = 'crud.rol.agregar_permiso';

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

    public function test_cargar_vista_listado_rol()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_VER));
        $response->assertViewIs('crud.rol.ver');
        $response->assertOk();
    }

    public function test_cargar_vista_crear_rol()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_CREAR));
        $response->assertViewIs('crud.rol.crear');
        $response->assertOk();
    }

    public function test_crear_rol()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $datos = [
            'rol' => fake()->unique()->word(),
        ];

        $response = $this->post(route(self::RUTA_CREAR), $datos);
        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'rol',
            ],
        ]);
    }

    public function test_ver_datos_rol()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $rol = new Rol(['rol' => fake()->unique()->word()]);
        $rol->save();

        $response = $this->get(route(self::RUTA_VER_DATOS, [$rol->getKey()]));
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'rol',
            ],
        ]);
    }

    public function test_cargar_vista_editar_rol()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $rol = new Rol(['rol' => fake()->unique()->word()]);
        $rol->save();

        $response = $this->get(route(self::RUTA_EDITAR, [$rol->getKey()]));
        $response->assertViewIs('crud.rol.editar');
        $response->assertOk();
    }

    public function test_actualizar_rol()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $rol = new Rol(['rol' => fake()->unique()->word()]);
        $rol->save();

        $datos = [
            'rol' => fake()->unique()->word(),
        ];

        $response = $this->post(route(self::RUTA_EDITAR, [$rol->getKey()]), $datos);
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'rol',
            ],
        ]);
    }

    public function test_eliminar_rol()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $rol = new Rol(['rol' => fake()->unique()->word()]);
        $rol->save();

        $response = $this->delete(route(self::RUTA_ELIMINAR, [$rol->getKey()]));
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'rol',
            ],
        ]);
    }

    public function test_agregar_permiso_a_rol()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $rol = new Rol(['rol' => fake()->unique()->word()]);
        $rol->save();

        $permiso = new Permiso(['permiso' => 'gestion.' . fake()->word() . '.' . fake()->word()]);
        $permiso->save();

        $payload = [
            'idPermiso' => $permiso->getKey(),
        ];

        $response = $this->post(route(self::RUTA_AGREGAR_PERMISO, [$rol->getKey()]), $payload);
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'rol',
                'permiso_id',
            ],
        ]);
    }
}
