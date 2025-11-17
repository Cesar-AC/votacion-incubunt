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
use App\Models\Voto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VotoControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const PERMISO_GLOBAL_CRUD = 'gestion.voto.*';
    private const RUTA_VER = 'crud.voto.ver';
    private const RUTA_VER_DATOS = 'crud.voto.ver_datos';
    private const RUTA_CREAR = 'crud.voto.crear';
    private const RUTA_EDITAR = 'crud.voto.editar';
    private const RUTA_ELIMINAR = 'crud.voto.eliminar';

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
            'biografia' => fake()->sentence(6),
            'experiencia' => fake()->sentence(6),
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

    private function crearCandidato(User $user): Candidato
    {
        $participante = $this->crearParticipante($user);
        $cargo = $this->crearCargo();
        $partido = $this->crearPartido();
        $c = new Candidato([
            'idParticipante' => $participante->getKey(),
            'idCargo' => $cargo->getKey(),
            'idPartido' => $partido->getKey(),
        ]);
        $c->save();
        return $c;
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

    public function test_cargar_vista_listado_voto()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_VER));
        $response->assertViewIs('crud.voto.ver');
        $response->assertOk();
    }

    public function test_cargar_vista_crear_voto()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_CREAR));
        $response->assertViewIs('crud.voto.crear');
        $response->assertOk();
    }

    public function test_crear_voto()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $candidato = $this->crearCandidato($user);
        $eleccion = $candidato->partido->elecciones; // misma elección del partido

        $datos = [
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'fechaVoto' => now(),
        ];

        $response = $this->post(route(self::RUTA_CREAR), $datos);
        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'idCandidato',
                'idElecciones',
                'fechaVoto',
            ],
        ]);
    }

    public function test_ver_datos_voto()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $candidato = $this->crearCandidato($user);
        $eleccion = $candidato->partido->elecciones;
        $voto = new Voto([
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'fechaVoto' => now(),
        ]);
        $voto->save();

        $response = $this->get(route(self::RUTA_VER_DATOS, [$voto->getKey()]));
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'idCandidato',
                'idElecciones',
                'fechaVoto',
            ],
        ]);
    }

    public function test_cargar_vista_editar_voto()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $candidato = $this->crearCandidato($user);
        $eleccion = $candidato->partido->elecciones;
        $voto = new Voto([
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'fechaVoto' => now(),
        ]);
        $voto->save();

        $response = $this->get(route(self::RUTA_EDITAR, [$voto->getKey()]));
        $response->assertViewIs('crud.voto.editar');
        $response->assertOk();
    }

    public function test_actualizar_voto()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $candidato = $this->crearCandidato($user);
        $eleccion = $candidato->partido->elecciones;
        $voto = new Voto([
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'fechaVoto' => now(),
        ]);
        $voto->save();

        $datos = [
            'fechaVoto' => now(),
        ];

        $response = $this->post(route(self::RUTA_EDITAR, [$voto->getKey()]), $datos);
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'idCandidato',
                'idElecciones',
                'fechaVoto',
            ],
        ]);
    }

    public function test_eliminar_voto()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $candidato = $this->crearCandidato($user);
        $eleccion = $candidato->partido->elecciones;
        $voto = new Voto([
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'fechaVoto' => now(),
        ]);
        $voto->save();

        $response = $this->delete(route(self::RUTA_ELIMINAR, [$voto->getKey()]));
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'idCandidato',
                'idElecciones',
                'fechaVoto',
            ],
        ]);
    }
}
