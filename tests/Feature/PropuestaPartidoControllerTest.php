<?php

namespace Tests\Feature;

use App\Models\Permiso;
use App\Models\User;
use App\Models\EstadoElecciones;
use App\Models\Elecciones;
use App\Models\Partido;
use App\Models\PropuestaPartido;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PropuestaPartidoControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const PERMISO_GLOBAL_CRUD = 'gestion.propuesta_partido.*';
    private const RUTA_VER = 'crud.propuesta_partido.ver';
    private const RUTA_VER_DATOS = 'crud.propuesta_partido.ver_datos';
    private const RUTA_CREAR = 'crud.propuesta_partido.crear';
    private const RUTA_EDITAR = 'crud.propuesta_partido.editar';
    private const RUTA_ELIMINAR = 'crud.propuesta_partido.eliminar';

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

    private function crearPartido(): Partido
    {
        $estado = new EstadoElecciones(['estado' => fake()->words(2, true)]);
        $estado->save();
        $inicio = now();
        $eleccion = new Elecciones([
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'descripcion' => fake()->sentence(8),
            'fecha_inicio' => $inicio,
            'fecha_cierre' => (clone $inicio)->addMonth(),
            'estado' => $estado->getKey(),
        ]);
        $eleccion->save();

        $partido = new Partido([
            'idElecciones' => $eleccion->getKey(),
            'partido' => 'Partido ' . fake()->words(2, true),
            'urlPartido' => fake()->url(),
            'descripcion' => fake()->sentence(8),
        ]);
        $partido->save();

        return $partido;
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

    public function test_cargar_vista_listado_propuesta_partido()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_VER));
        $response->assertViewIs('crud.propuesta_partido.ver');
        $response->assertOk();
    }

    public function test_cargar_vista_crear_propuesta_partido()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_CREAR));
        $response->assertViewIs('crud.propuesta_partido.crear');
        $response->assertOk();
    }

    public function test_crear_propuesta_partido()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $partido = $this->crearPartido();

        $datos = [
            'propuesta' => fake()->sentence(3),
            'descripcion' => fake()->sentence(10),
            'idPartido' => $partido->getKey(),
        ];

        $response = $this->post(route(self::RUTA_CREAR), $datos);
        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'propuesta',
                'descripcion',
                'idPartido',
            ],
        ]);
    }

    public function test_ver_datos_propuesta_partido()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $partido = $this->crearPartido();
        $prop = new PropuestaPartido([
            'propuesta' => fake()->sentence(3),
            'descripcion' => fake()->sentence(10),
            'idPartido' => $partido->getKey(),
        ]);
        $prop->save();

        $response = $this->get(route(self::RUTA_VER_DATOS, [$prop->getKey()]));
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'propuesta',
                'descripcion',
                'idPartido',
            ],
        ]);
    }

    public function test_cargar_vista_editar_propuesta_partido()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $partido = $this->crearPartido();
        $prop = new PropuestaPartido([
            'propuesta' => fake()->sentence(3),
            'descripcion' => fake()->sentence(10),
            'idPartido' => $partido->getKey(),
        ]);
        $prop->save();

        $response = $this->get(route(self::RUTA_EDITAR, [$prop->getKey()]));
        $response->assertViewIs('crud.propuesta_partido.editar');
        $response->assertOk();
    }

    public function test_actualizar_propuesta_partido()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $partido = $this->crearPartido();
        $prop = new PropuestaPartido([
            'propuesta' => fake()->sentence(3),
            'descripcion' => fake()->sentence(10),
            'idPartido' => $partido->getKey(),
        ]);
        $prop->save();

        $datos = [
            'propuesta' => fake()->sentence(3),
            'descripcion' => fake()->sentence(10),
        ];

        $response = $this->post(route(self::RUTA_EDITAR, [$prop->getKey()]), $datos);
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'propuesta',
                'descripcion',
                'idPartido',
            ],
        ]);
    }

    public function test_eliminar_propuesta_partido()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $partido = $this->crearPartido();
        $prop = new PropuestaPartido([
            'propuesta' => fake()->sentence(3),
            'descripcion' => fake()->sentence(10),
            'idPartido' => $partido->getKey(),
        ]);
        $prop->save();

        $response = $this->delete(route(self::RUTA_ELIMINAR, [$prop->getKey()]));
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'propuesta',
                'descripcion',
                'idPartido',
            ],
        ]);
    }
}
