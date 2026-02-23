<?php

namespace Tests\Feature;

use App\Models\Permiso;
use App\Models\User;
use App\Models\Area;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AreaControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const PERMISO_GLOBAL_CRUD = 'gestion.area.*';
    private const RUTA_VER = 'crud.area.ver';
    private const RUTA_VER_DATOS = 'crud.area.ver_datos';
    private const RUTA_CREAR = 'crud.area.crear';
    private const RUTA_EDITAR = 'crud.area.editar';
    private const RUTA_ELIMINAR = 'crud.area.eliminar';

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

    public function test_cargar_vista_listado_area()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_VER));
        $response->assertViewIs('crud.area.ver');
        $response->assertOk();
    }

    public function test_cargar_vista_crear_area()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_CREAR));
        $response->assertViewIs('crud.area.crear');
        $response->assertOk();
    }

    public function test_crear_area()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $datos = [
            'area' => fake()->words(2, true),
        ];

        $response = $this->post(route(self::RUTA_CREAR), $datos);
        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'area',
            ],
        ]);
    }

    public function test_ver_datos_area()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $area = new Area([
            'area' => fake()->words(2, true),
        ]);
        $area->save();

        $response = $this->get(route(self::RUTA_VER_DATOS, [$area->getKey()]));
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'area',
            ],
        ]);
    }

    public function test_cargar_vista_editar_area()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $area = new Area([
            'area' => fake()->words(2, true),
        ]);
        $area->save();

        $response = $this->get(route(self::RUTA_EDITAR, [$area->getKey()]));
        $response->assertViewIs('crud.area.editar');
        $response->assertOk();
    }

    public function test_actualizar_area()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $area = new Area([
            'area' => fake()->words(2, true),
        ]);
        $area->save();

        $datos = [
            'area' => fake()->words(2, true),
        ];

        $response = $this->post(route(self::RUTA_EDITAR, [$area->getKey()]), $datos);
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'area',
            ],
        ]);
    }

    public function test_eliminar_area()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $area = new Area([
            'area' => fake()->words(2, true),
        ]);
        $area->save();

        $response = $this->delete(route(self::RUTA_ELIMINAR, [$area->getKey()]));
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'area',
            ],
        ]);
    }
}
