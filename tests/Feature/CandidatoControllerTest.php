<?php

namespace Tests\Feature;

use App\Models\Permiso;
use App\Models\User;
use App\Models\Carrera;
use App\Models\Participante;
use App\Models\Area;
use App\Models\Cargo;
use App\Models\EstadoElecciones;
use App\Models\Elecciones;
use App\Models\Partido;
use App\Models\Candidato;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CandidatoControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const PERMISO_GLOBAL_CRUD = 'gestion.candidato.*';
    private const RUTA_VER = 'crud.candidato.ver';
    private const RUTA_VER_DATOS = 'crud.candidato.ver_datos';
    private const RUTA_CREAR = 'crud.candidato.crear';
    private const RUTA_EDITAR = 'crud.candidato.editar';
    private const RUTA_ELIMINAR = 'crud.candidato.eliminar';

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

    private function crearParticipante(User $user): Participante
    {
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
        return $p;
    }

    private function crearCargo(): Cargo
    {
        $area = new Area(['area' => fake()->words(2, true)]);
        $area->save();
        $cargo = new Cargo([
            'idCargo' => fake()->numberBetween(1, 9999),
            'cargo' => fake()->words(2, true),
            'idArea' => $area->getKey(),
        ]);
        $cargo->save();
        return $cargo;
    }

    private function crearEleccion(): Elecciones
    {
        $estado = new EstadoElecciones(['estado' => fake()->words(2, true)]);
        $estado->save();
        $inicio = now();
        $e = new Elecciones([
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'descripcion' => fake()->sentence(8),
            'fecha_inicio' => $inicio,
            'fecha_cierre' => (clone $inicio)->addMonth(),
            'estado' => $estado->getKey(),
        ]);
        $e->save();
        return $e;
    }

    private function crearPartido(): Partido
    {
        $eleccion = $this->crearEleccion();
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

    public function test_cargar_vista_listado_candidato()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_VER));
        $response->assertViewIs('crud.candidato.ver');
        $response->assertOk();
    }

    public function test_cargar_vista_crear_candidato()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_CREAR));
        $response->assertViewIs('crud.candidato.crear');
        $response->assertOk();
    }

    public function test_crear_candidato()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $participante = $this->crearParticipante($user);
        $cargo = $this->crearCargo();
        $partido = $this->crearPartido();

        $datos = [
            'idParticipante' => $participante->getKey(),
            'idCargo' => $cargo->getKey(),
            'idPartido' => $partido->getKey(),
        ];

        $response = $this->post(route(self::RUTA_CREAR), $datos);
        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'idParticipante',
                'idCargo',
                'idPartido',
            ],
        ]);
    }

    public function test_ver_datos_candidato()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $participante = $this->crearParticipante($user);
        $cargo = $this->crearCargo();
        $partido = $this->crearPartido();
        $candidato = new Candidato([
            'idParticipante' => $participante->getKey(),
            'idCargo' => $cargo->getKey(),
            'idPartido' => $partido->getKey(),
        ]);
        $candidato->save();

        $response = $this->get(route(self::RUTA_VER_DATOS, [$candidato->getKey()]));
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'idParticipante',
                'idCargo',
                'idPartido',
            ],
        ]);
    }

    public function test_cargar_vista_editar_candidato()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $participante = $this->crearParticipante($user);
        $cargo = $this->crearCargo();
        $partido = $this->crearPartido();
        $candidato = new Candidato([
            'idParticipante' => $participante->getKey(),
            'idCargo' => $cargo->getKey(),
            'idPartido' => $partido->getKey(),
        ]);
        $candidato->save();

        $response = $this->get(route(self::RUTA_EDITAR, [$candidato->getKey()]));
        $response->assertViewIs('crud.candidato.editar');
        $response->assertOk();
    }

    public function test_actualizar_candidato()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $participante = $this->crearParticipante($user);
        $cargo = $this->crearCargo();
        $partido = $this->crearPartido();
        $candidato = new Candidato([
            'idParticipante' => $participante->getKey(),
            'idCargo' => $cargo->getKey(),
            'idPartido' => $partido->getKey(),
        ]);
        $candidato->save();

        $datos = [
            'idParticipante' => $participante->getKey(),
            'idCargo' => $cargo->getKey(),
            'idPartido' => $partido->getKey(),
        ];

        $response = $this->post(route(self::RUTA_EDITAR, [$candidato->getKey()]), $datos);
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'idParticipante',
                'idCargo',
                'idPartido',
            ],
        ]);
    }

    public function test_eliminar_candidato()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $participante = $this->crearParticipante($user);
        $cargo = $this->crearCargo();
        $partido = $this->crearPartido();
        $candidato = new Candidato([
            'idParticipante' => $participante->getKey(),
            'idCargo' => $cargo->getKey(),
            'idPartido' => $partido->getKey(),
        ]);
        $candidato->save();

        $response = $this->delete(route(self::RUTA_ELIMINAR, [$candidato->getKey()]));
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'idParticipante',
                'idCargo',
                'idPartido',
            ],
        ]);
    }
}
