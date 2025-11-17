<?php

namespace Tests\Feature;

use App\Models\Permiso;
use App\Models\User;
use App\Models\EstadoElecciones;
use App\Models\Elecciones;
use App\Models\Partido;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PartidoControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const PERMISO_GLOBAL_CRUD = 'gestion.partido.*';
    private const RUTA_VER = 'crud.partido.ver';
    private const RUTA_VER_DATOS = 'crud.partido.ver_datos';
    private const RUTA_CREAR = 'crud.partido.crear';
    private const RUTA_EDITAR = 'crud.partido.editar';
    private const RUTA_ELIMINAR = 'crud.partido.eliminar';

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

    private function crearEstadoEleccion(): EstadoElecciones{
        $estado = new EstadoElecciones([
            'estado' => fake()->words(2, true),
        ]);
        $estado->save();
        return $estado;
    }

    private function crearEleccion(): Elecciones
    {
        $estado = $this->crearEstadoEleccion();
        $inicio = now();
        $eleccion = new Elecciones([
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'descripcion' => fake()->sentence(8),
            'fecha_inicio' => $inicio,
            'fecha_cierre' => (clone $inicio)->addMonth(),
            'estado' => $estado->getKey(),
        ]);
        $eleccion->save();
        return $eleccion;
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

    public function test_cargar_vista_listado_partido()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_VER));
        $response->assertViewIs('crud.partido.ver');
        $response->assertOk();
    }

    public function test_cargar_vista_crear_partido()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_CREAR));
        $response->assertViewIs('crud.partido.crear');
        $response->assertOk();
    }

    public function test_crear_partido()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $eleccion = $this->crearEleccion();

        $datos = [
            'idElecciones' => $eleccion->getKey(),
            'partido' => 'Partido ' . fake()->words(2, true),
            'urlPartido' => fake()->url(),
            'descripcion' => fake()->sentence(10),
        ];

        $response = $this->post(route(self::RUTA_CREAR), $datos);
        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'idElecciones',
                'partido',
                'urlPartido',
                'descripcion',
            ],
        ]);
    }

    public function test_ver_datos_partido()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $eleccion = $this->crearEleccion();
        $partido = new Partido([
            'idElecciones' => $eleccion->getKey(),
            'partido' => 'Partido ' . fake()->words(2, true),
            'urlPartido' => fake()->url(),
            'descripcion' => fake()->sentence(10),
        ]);
        $partido->save();

        $response = $this->get(route(self::RUTA_VER_DATOS, [$partido->getKey()]));
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'idElecciones',
                'partido',
                'urlPartido',
                'descripcion',
            ],
        ]);
    }

    public function test_cargar_vista_editar_partido()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $eleccion = $this->crearEleccion();
        $partido = new Partido([
            'idElecciones' => $eleccion->getKey(),
            'partido' => 'Partido ' . fake()->words(2, true),
            'urlPartido' => fake()->url(),
            'descripcion' => fake()->sentence(10),
        ]);
        $partido->save();

        $response = $this->get(route(self::RUTA_EDITAR, [$partido->getKey()]));
        $response->assertViewIs('crud.partido.editar');
        $response->assertOk();
    }

    public function test_actualizar_partido()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $eleccion = $this->crearEleccion();
        $partido = new Partido([
            'idElecciones' => $eleccion->getKey(),
            'partido' => 'Partido ' . fake()->words(2, true),
            'urlPartido' => fake()->url(),
            'descripcion' => fake()->sentence(10),
        ]);
        $partido->save();

        $datos = [
            'idElecciones' => $eleccion->getKey(),
            'partido' => 'Partido ' . fake()->words(2, true),
            'urlPartido' => fake()->url(),
            'descripcion' => fake()->sentence(10),
        ];

        $response = $this->post(route(self::RUTA_EDITAR, [$partido->getKey()]), $datos);
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'idElecciones',
                'partido',
                'urlPartido',
                'descripcion',
            ],
        ]);
    }

    public function test_eliminar_partido()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $eleccion = $this->crearEleccion();
        $partido = new Partido([
            'idElecciones' => $eleccion->getKey(),
            'partido' => 'Partido ' . fake()->words(2, true),
            'urlPartido' => fake()->url(),
            'descripcion' => fake()->sentence(10),
        ]);
        $partido->save();

        $response = $this->delete(route(self::RUTA_ELIMINAR, [$partido->getKey()]));
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'idElecciones',
                'partido',
                'urlPartido',
                'descripcion',
            ],
        ]);
    }
}
