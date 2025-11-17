<?php

namespace Tests\Feature;

use App\Models\Permiso;
use App\Models\User;
use App\Models\TipoVoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TipoVotoControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const PERMISO_GLOBAL_CRUD = 'gestion.tipo_voto.*';
    private const RUTA_VER = 'crud.tipo_voto.ver';
    private const RUTA_VER_DATOS = 'crud.tipo_voto.ver_datos';
    private const RUTA_CREAR = 'crud.tipo_voto.crear';
    private const RUTA_EDITAR = 'crud.tipo_voto.editar';
    private const RUTA_ELIMINAR = 'crud.tipo_voto.eliminar';

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

    public function test_cargar_vista_listado_tipo_voto()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_VER));
        $response->assertViewIs('crud.tipo_voto.ver');
        $response->assertOk();
    }

    public function test_cargar_vista_crear_tipo_voto()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_CREAR));
        $response->assertViewIs('crud.tipo_voto.crear');
        $response->assertOk();
    }

    public function test_crear_tipo_voto()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $datos = [
            'idTipoVoto' => fake()->numberBetween(1, 9999),
            'tipoVoto' => fake()->word(),
        ];

        $response = $this->post(route(self::RUTA_CREAR), $datos);
        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'tipoVoto',
            ],
        ]);
    }

    public function test_ver_datos_tipo_voto()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $tipo = new TipoVoto([
            'idTipoVoto' => fake()->numberBetween(1, 9999),
            'tipoVoto' => fake()->word(),
        ]);
        $tipo->save();

        $response = $this->get(route(self::RUTA_VER_DATOS, [$tipo->getKey()]));
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'tipoVoto',
            ],
        ]);
    }

    public function test_cargar_vista_editar_tipo_voto()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $tipo = new TipoVoto([
            'idTipoVoto' => fake()->numberBetween(1, 9999),
            'tipoVoto' => fake()->word(),
        ]);
        $tipo->save();

        $response = $this->get(route(self::RUTA_EDITAR, [$tipo->getKey()]));
        $response->assertViewIs('crud.tipo_voto.editar');
        $response->assertOk();
    }

    public function test_actualizar_tipo_voto()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $tipo = new TipoVoto([
            'idTipoVoto' => fake()->numberBetween(1, 9999),
            'tipoVoto' => fake()->word(),
        ]);
        $tipo->save();

        $datos = [
            'tipoVoto' => fake()->word(),
        ];

        $response = $this->post(route(self::RUTA_EDITAR, [$tipo->getKey()]), $datos);
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'tipoVoto',
            ],
        ]);
    }

    public function test_eliminar_tipo_voto()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $tipo = new TipoVoto([
            'idTipoVoto' => fake()->numberBetween(1, 9999),
            'tipoVoto' => fake()->word(),
        ]);
        $tipo->save();

        $response = $this->delete(route(self::RUTA_ELIMINAR, [$tipo->getKey()]));
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'tipoVoto',
            ],
        ]);
    }
}
