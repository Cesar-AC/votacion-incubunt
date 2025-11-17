<?php

namespace Tests\Feature;

use App\Models\Permiso;
use App\Models\User;
use App\Models\Carrera;
use App\Models\Participante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ParticipanteControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const PERMISO_GLOBAL_CRUD = 'gestion.participante.*';
    private const RUTA_VER = 'crud.participante.ver';
    private const RUTA_VER_DATOS = 'crud.participante.ver_datos';
    private const RUTA_CREAR = 'crud.participante.crear';
    private const RUTA_EDITAR = 'crud.participante.editar';
    private const RUTA_ELIMINAR = 'crud.participante.eliminar';

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

    private function crearCarrera(): Carrera
    {
        $carrera = new Carrera([
            'idCarrera' => fake()->numberBetween(1, 9999),
            'carrera' => fake()->words(2, true),
        ]);
        $carrera->save();
        return $carrera;
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

    public function test_cargar_vista_listado_participante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_VER));
        $response->assertViewIs('crud.participante.ver');
        $response->assertOk();
    }

    public function test_cargar_vista_crear_participante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_CREAR));
        $response->assertViewIs('crud.participante.crear');
        $response->assertOk();
    }

    public function test_crear_participante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $carrera = $this->crearCarrera();

        $datos = [
            'nombre' => fake()->firstName(),
            'apellidos' => fake()->lastName(),
            'idUser' => $user->getKey(),
            'idCarrera' => $carrera->getKey(),
            'biografia' => fake()->sentence(8),
            'experiencia' => fake()->sentence(8),
            'estado' => 1,
        ];

        $response = $this->post(route(self::RUTA_CREAR), $datos);
        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'nombre',
                'apellidos',
                'idUser',
                'idCarrera',
                'biografia',
                'experiencia',
                'estado',
            ],
        ]);
    }

    public function test_ver_datos_participante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $carrera = $this->crearCarrera();
        $p = new Participante([
            'nombre' => fake()->firstName(),
            'apellidos' => fake()->lastName(),
            'idUser' => $user->getKey(),
            'idCarrera' => $carrera->getKey(),
            'biografia' => fake()->sentence(8),
            'experiencia' => fake()->sentence(8),
            'estado' => 1,
        ]);
        $p->save();

        $response = $this->get(route(self::RUTA_VER_DATOS, [$p->getKey()]));
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'nombre',
                'apellidos',
                'idUser',
                'idCarrera',
                'biografia',
                'experiencia',
                'estado',
            ],
        ]);
    }

    public function test_cargar_vista_editar_participante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $carrera = $this->crearCarrera();
        $p = new Participante([
            'nombre' => fake()->firstName(),
            'apellidos' => fake()->lastName(),
            'idUser' => $user->getKey(),
            'idCarrera' => $carrera->getKey(),
            'biografia' => fake()->sentence(8),
            'experiencia' => fake()->sentence(8),
            'estado' => 1,
        ]);
        $p->save();

        $response = $this->get(route(self::RUTA_EDITAR, [$p->getKey()]));
        $response->assertViewIs('crud.participante.editar');
        $response->assertOk();
    }

    public function test_actualizar_participante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $carrera = $this->crearCarrera();
        $p = new Participante([
            'nombre' => fake()->firstName(),
            'apellidos' => fake()->lastName(),
            'idUser' => $user->getKey(),
            'idCarrera' => $carrera->getKey(),
            'biografia' => fake()->sentence(8),
            'experiencia' => fake()->sentence(8),
            'estado' => 1,
        ]);
        $p->save();

        $datos = [
            'nombre' => fake()->firstName(),
            'apellidos' => fake()->lastName(),
            'biografia' => fake()->sentence(8),
            'experiencia' => fake()->sentence(8),
            'estado' => 1,
        ];

        $response = $this->post(route(self::RUTA_EDITAR, [$p->getKey()]), $datos);
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'nombre',
                'apellidos',
                'idUser',
                'idCarrera',
                'biografia',
                'experiencia',
                'estado',
            ],
        ]);
    }

    public function test_eliminar_participante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $carrera = $this->crearCarrera();
        $p = new Participante([
            'nombre' => fake()->firstName(),
            'apellidos' => fake()->lastName(),
            'idUser' => $user->getKey(),
            'idCarrera' => $carrera->getKey(),
            'biografia' => fake()->sentence(8),
            'experiencia' => fake()->sentence(8),
            'estado' => 1,
        ]);
        $p->save();

        $response = $this->delete(route(self::RUTA_ELIMINAR, [$p->getKey()]));
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'nombre',
                'apellidos',
                'idUser',
                'idCarrera',
                'biografia',
                'experiencia',
                'estado',
            ],
        ]);
    }
}
