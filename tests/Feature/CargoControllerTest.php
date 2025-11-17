<?php

namespace Tests\Feature;

use App\Models\Permiso;
use App\Models\User;
use App\Models\Area;
use App\Models\Cargo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CargoControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const PERMISO_GLOBAL_CRUD = 'gestion.cargo.*';
    private const RUTA_VER = 'crud.cargo.ver';
    private const RUTA_VER_DATOS = 'crud.cargo.ver_datos';
    private const RUTA_CREAR = 'crud.cargo.crear';
    private const RUTA_EDITAR = 'crud.cargo.editar';
    private const RUTA_ELIMINAR = 'crud.cargo.eliminar';

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

    private function crearArea(): Area
    {
        $area = new Area([
            'area' => fake()->words(2, true),
        ]);
        $area->save();
        return $area;
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

    public function test_cargar_vista_listado_cargo()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_VER));
        $response->assertViewIs('crud.cargo.ver');
        $response->assertOk();
    }

    public function test_cargar_vista_crear_cargo()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_CREAR));
        $response->assertViewIs('crud.cargo.crear');
        $response->assertOk();
    }

    public function test_crear_cargo()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $area = $this->crearArea();

        $datos = [
            'idCargo' => fake()->numberBetween(1, 9999),
            'cargo' => fake()->words(2, true),
            'idArea' => $area->getKey(),
        ];

        $response = $this->post(route(self::RUTA_CREAR), $datos);
        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'cargo',
                'idArea',
            ],
        ]);
    }

    public function test_ver_datos_cargo()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $area = $this->crearArea();
        $cargo = new Cargo([
            'idCargo' => fake()->numberBetween(1, 9999),
            'cargo' => fake()->words(2, true),
            'idArea' => $area->getKey(),
        ]);
        $cargo->save();

        $response = $this->get(route(self::RUTA_VER_DATOS, [$cargo->getKey()]));
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'cargo',
                'idArea',
            ],
        ]);
    }

    public function test_cargar_vista_editar_cargo()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $area = $this->crearArea();
        $cargo = new Cargo([
            'idCargo' => fake()->numberBetween(1, 9999),
            'cargo' => fake()->words(2, true),
            'idArea' => $area->getKey(),
        ]);
        $cargo->save();

        $response = $this->get(route(self::RUTA_EDITAR, [$cargo->getKey()]));
        $response->assertViewIs('crud.cargo.editar');
        $response->assertOk();
    }

    public function test_actualizar_cargo()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $area = $this->crearArea();
        $cargo = new Cargo([
            'idCargo' => fake()->numberBetween(1, 9999),
            'cargo' => fake()->words(2, true),
            'idArea' => $area->getKey(),
        ]);
        $cargo->save();

        $datos = [
            'cargo' => fake()->words(2, true),
            'idArea' => $area->getKey(),
        ];

        $response = $this->post(route(self::RUTA_EDITAR, [$cargo->getKey()]), $datos);
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'cargo',
                'idArea',
            ],
        ]);
    }

    public function test_eliminar_cargo()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $area = $this->crearArea();
        $cargo = new Cargo([
            'idCargo' => fake()->numberBetween(1, 9999),
            'cargo' => fake()->words(2, true),
            'idArea' => $area->getKey(),
        ]);
        $cargo->save();

        $response = $this->delete(route(self::RUTA_ELIMINAR, [$cargo->getKey()]));
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'cargo',
                'idArea',
            ],
        ]);
    }
}
