<?php

namespace Tests\Feature;

use App\Models\Permiso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const PERMISO_GLOBAL_CRUD = 'gestion.user.*';
    private const RUTA_VER = 'crud.user.ver';
    private const RUTA_VER_DATOS = 'crud.user.ver_datos';
    private const RUTA_CREAR = 'crud.user.crear';
    private const RUTA_EDITAR = 'crud.user.editar';
    private const RUTA_ELIMINAR = 'crud.user.eliminar';

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

    public function test_cargar_vista_listado_user()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_VER));
        $response->assertViewIs('crud.user.ver');
        $response->assertOk();
    }

    public function test_cargar_vista_crear_user()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_CREAR));
        $response->assertViewIs('crud.user.crear');
        $response->assertOk();
    }

    public function test_crear_user()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $datos = [
            'usuario' => fake()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password123'),
        ];

        $response = $this->post(route(self::RUTA_CREAR), $datos);
        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'usuario',
                'email',
            ],
        ]);
    }

    public function test_ver_datos_user()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $u = User::factory()->create();

        $response = $this->get(route(self::RUTA_VER_DATOS, [$u->getKey()]));
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'usuario',
                'email',
            ],
        ]);
    }

    public function test_cargar_vista_editar_user()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $u = User::factory()->create();

        $response = $this->get(route(self::RUTA_EDITAR, [$u->getKey()]));
        $response->assertViewIs('crud.user.editar');
        $response->assertOk();
    }

    public function test_actualizar_user()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $u = User::factory()->create();

        $datos = [
            'usuario' => fake()->userName(),
            'email' => fake()->unique()->safeEmail(),
        ];

        $response = $this->post(route(self::RUTA_EDITAR, [$u->getKey()]), $datos);
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'usuario',
                'email',
            ],
        ]);
    }

    public function test_eliminar_user()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $u = User::factory()->create();

        $response = $this->delete(route(self::RUTA_ELIMINAR, [$u->getKey()]));
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'usuario',
                'email',
            ],
        ]);
    }
}
