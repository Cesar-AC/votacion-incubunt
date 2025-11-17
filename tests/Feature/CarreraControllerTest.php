<?php

namespace Tests\Feature;

use App\Models\Permiso;
use App\Models\User;
use App\Models\Carrera;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CarreraControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const PERMISO_GLOBAL_CRUD = 'gestion.carrera.*';
    private const RUTA_VER = 'crud.carrera.ver';
    private const RUTA_VER_DATOS = 'crud.carrera.ver_datos';
    private const RUTA_CREAR = 'crud.carrera.crear';
    private const RUTA_EDITAR = 'crud.carrera.editar';
    private const RUTA_ELIMINAR = 'crud.carrera.eliminar';

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

    public function test_cargar_vista_listado_carrera()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_VER));
        $response->assertViewIs('crud.carrera.ver');
        $response->assertOk();
    }

    public function test_cargar_vista_crear_carrera()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_CREAR));
        $response->assertViewIs('crud.carrera.crear');
        $response->assertOk();
    }

    public function test_crear_carrera()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $datos = [
            'idCarrera' => fake()->numberBetween(1, 9999),
            'carrera' => fake()->words(2, true),
        ];

        $response = $this->post(route(self::RUTA_CREAR), $datos);
        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'carrera',
            ],
        ]);
    }

    public function test_ver_datos_carrera()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $carrera = new Carrera([
            'idCarrera' => fake()->numberBetween(1, 9999),
            'carrera' => fake()->words(2, true),
        ]);
        $carrera->save();

        $response = $this->get(route(self::RUTA_VER_DATOS, [$carrera->getKey()]));
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'carrera',
            ],
        ]);
    }

    public function test_cargar_vista_editar_carrera()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $carrera = new Carrera([
            'idCarrera' => fake()->numberBetween(1, 9999),
            'carrera' => fake()->words(2, true),
        ]);
        $carrera->save();

        $response = $this->get(route(self::RUTA_EDITAR, [$carrera->getKey()]));
        $response->assertViewIs('crud.carrera.editar');
        $response->assertOk();
    }

    public function test_actualizar_carrera()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $carrera = new Carrera([
            'idCarrera' => fake()->numberBetween(1, 9999),
            'carrera' => fake()->words(2, true),
        ]);
        $carrera->save();

        $datos = [
            'carrera' => fake()->words(2, true),
        ];

        $response = $this->post(route(self::RUTA_EDITAR, [$carrera->getKey()]), $datos);
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'carrera',
            ],
        ]);
    }

    public function test_eliminar_carrera()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $carrera = new Carrera([
            'idCarrera' => fake()->numberBetween(1, 9999),
            'carrera' => fake()->words(2, true),
        ]);
        $carrera->save();

        $response = $this->delete(route(self::RUTA_ELIMINAR, [$carrera->getKey()]));
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'carrera',
            ],
        ]);
    }
}
