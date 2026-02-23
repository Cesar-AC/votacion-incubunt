<?php

namespace Tests\Feature;

use App\Models\Permiso;
use App\Models\User;
use App\Models\EstadoParticipante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EstadoParticipanteControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const PERMISO_GLOBAL_CRUD = 'gestion.estado_participante.*';
    private const RUTA_VER = 'crud.estado_participante.ver';
    private const RUTA_VER_DATOS = 'crud.estado_participante.ver_datos';
    private const RUTA_CREAR = 'crud.estado_participante.crear';
    private const RUTA_EDITAR = 'crud.estado_participante.editar';
    private const RUTA_ELIMINAR = 'crud.estado_participante.eliminar';

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

    public function test_cargar_vista_listado_estado_participante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_VER));
        $response->assertViewIs('crud.estado_participante.ver');
        $response->assertOk();
    }

    public function test_cargar_vista_crear_estado_participante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_CREAR));
        $response->assertViewIs('crud.estado_participante.crear');
        $response->assertOk();
    }

    public function test_crear_estado_participante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $datos = [
            'idEstadoParticipante' => fake()->numberBetween(1, 9999),
            'estadoParticipante' => fake()->words(2, true),
        ];

        $response = $this->post(route(self::RUTA_CREAR), $datos);
        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'estadoParticipante',
            ],
        ]);
    }

    public function test_ver_datos_estado_participante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $estado = new EstadoParticipante([
            'idEstadoParticipante' => fake()->numberBetween(1, 9999),
            'estadoParticipante' => fake()->words(2, true),
        ]);
        $estado->save();

        $response = $this->get(route(self::RUTA_VER_DATOS, [$estado->getKey()]));
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'estadoParticipante',
            ],
        ]);
    }

    public function test_cargar_vista_editar_estado_participante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $estado = new EstadoParticipante([
            'idEstadoParticipante' => fake()->numberBetween(1, 9999),
            'estadoParticipante' => fake()->words(2, true),
        ]);
        $estado->save();

        $response = $this->get(route(self::RUTA_EDITAR, [$estado->getKey()]));
        $response->assertViewIs('crud.estado_participante.editar');
        $response->assertOk();
    }

    public function test_actualizar_estado_participante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $estado = new EstadoParticipante([
            'idEstadoParticipante' => fake()->numberBetween(1, 9999),
            'estadoParticipante' => fake()->words(2, true),
        ]);
        $estado->save();

        $datos = [
            'estadoParticipante' => fake()->words(2, true),
        ];

        $response = $this->post(route(self::RUTA_EDITAR, [$estado->getKey()]), $datos);
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'estadoParticipante',
            ],
        ]);
    }

    public function test_eliminar_estado_participante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $estado = new EstadoParticipante([
            'idEstadoParticipante' => fake()->numberBetween(1, 9999),
            'estadoParticipante' => fake()->words(2, true),
        ]);
        $estado->save();

        $response = $this->delete(route(self::RUTA_ELIMINAR, [$estado->getKey()]));
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'estadoParticipante',
            ],
        ]);
    }
}
