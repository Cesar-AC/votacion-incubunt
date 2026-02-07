<?php

namespace Tests\Unit;

use App\Models\Area;
use App\Models\Candidato;
use App\Models\CandidatoEleccion;
use App\Models\Cargo;
use App\Models\Elecciones;
use App\Models\EstadoElecciones;
use App\Models\Partido;
use App\Models\PerfilUsuario;
use App\Models\PropuestaCandidato;
use App\Models\User;
use App\Services\CandidatoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class CandidatoServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $seed = true;

    private CandidatoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CandidatoService();
    }

    private function crearUsuario(): User
    {
        $usuario = User::factory()->create([
            'correo' => fake()->unique()->email(),
            'contraseña' => bcrypt('password'),
            'idEstadoUsuario' => 1,
        ]);

        $area = Area::create(['area' => fake()->words(2, true)]);

        PerfilUsuario::create([
            'idUser' => $usuario->getKey(),
            'idArea' => $area->getKey(),
            'nombre' => fake()->firstName(),
            'apellidoPaterno' => fake()->lastName(),
            'apellidoMaterno' => fake()->lastName(),
            'dni' => fake()->numerify('########'),
        ]);

        return $usuario;
    }

    private function crearEleccion(): Elecciones
    {
        return Elecciones::create([
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'fechaInicio' => now(),
            'fechaCierre' => now()->addMonth(),
            'descripcion' => fake()->sentence(10),
            'idEstado' => EstadoElecciones::PROGRAMADO,
        ]);
    }

    private function crearCargo(): Cargo
    {
        $area = Area::create(['area' => fake()->words(2, true)]);
        return Cargo::create([
            'cargo' => fake()->words(2, true),
            'idArea' => $area->getKey(),
        ]);
    }

    private function crearPartido(): Partido
    {
        return Partido::create([
            'partido' => fake()->words(3, true),
            'urlPartido' => fake()->url(),
            'descripcion' => fake()->sentence(),
        ]);
    }

    public function test_obtener_candidatos_retorna_coleccion_vacia_si_no_hay_candidatos(): void
    {
        $candidatos = $this->service->obtenerCandidatos();

        $this->assertCount(0, $candidatos);
    }

    public function test_obtener_candidatos_retorna_todos_los_candidatos(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $usuario = $this->crearUsuario();
            Candidato::create(['idUsuario' => $usuario->getKey()]);
        }

        $candidatos = $this->service->obtenerCandidatos();

        $this->assertCount(3, $candidatos);
    }

    public function test_obtener_candidato_por_id_retorna_candidato_correcto(): void
    {
        $usuario = $this->crearUsuario();
        $candidato = Candidato::create(['idUsuario' => $usuario->getKey()]);

        $resultado = $this->service->obtenerCandidatoPorId($candidato->getKey());

        $this->assertEquals($candidato->getKey(), $resultado->getKey());
        $this->assertEquals($usuario->getKey(), $resultado->idUsuario);
    }

    public function test_obtener_candidato_por_id_lanza_excepcion_si_no_existe(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->obtenerCandidatoPorId(99999);
    }

    public function test_crear_candidato_crea_y_retorna_nuevo_candidato(): void
    {
        $usuario = $this->crearUsuario();

        $candidato = $this->service->crearCandidato(['idUsuario' => $usuario->getKey()]);

        $this->assertDatabaseHas('Candidato', ['idUsuario' => $usuario->getKey()]);
        $this->assertEquals($usuario->getKey(), $candidato->idUsuario);
        $this->assertInstanceOf(Candidato::class, $candidato);
    }

    public function test_editar_candidato_actualiza_usuario(): void
    {
        $usuario1 = $this->crearUsuario();
        $usuario2 = $this->crearUsuario();
        $candidato = Candidato::create(['idUsuario' => $usuario1->getKey()]);

        $resultado = $this->service->editarCandidato(['idUsuario' => $usuario2->getKey()], $candidato);

        $this->assertEquals($usuario2->getKey(), $resultado->idUsuario);
    }

    public function test_eliminar_candidato_elimina_correctamente(): void
    {
        $usuario = $this->crearUsuario();
        $candidato = Candidato::create(['idUsuario' => $usuario->getKey()]);
        $candidatoId = $candidato->getKey();

        $this->service->eliminarCandidato($candidato);

        $this->assertDatabaseMissing('Candidato', ['idCandidato' => $candidatoId]);
    }

    public function test_vincular_candidato_a_eleccion(): void
    {
        $usuario = $this->crearUsuario();
        $candidato = Candidato::create(['idUsuario' => $usuario->getKey()]);
        $eleccion = $this->crearEleccion();
        $cargo = $this->crearCargo();
        $partido = $this->crearPartido();

        $this->service->vincularCandidatoAEleccion([
            'idCargo' => $cargo->getKey(),
            'idPartido' => $partido->getKey(),
        ], $candidato, $eleccion);

        $this->assertDatabaseHas('CandidatoEleccion', [
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'idCargo' => $cargo->getKey(),
            'idPartido' => $partido->getKey(),
        ]);
    }

    public function test_desvincular_candidato_de_eleccion(): void
    {
        $usuario = $this->crearUsuario();
        $candidato = Candidato::create(['idUsuario' => $usuario->getKey()]);
        $eleccion = $this->crearEleccion();
        $cargo = $this->crearCargo();

        CandidatoEleccion::create([
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'idCargo' => $cargo->getKey(),
        ]);

        $this->service->desvincularCandidatoDeEleccion($candidato, $eleccion);

        $this->assertDatabaseMissing('CandidatoEleccion', [
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
        ]);
    }

    public function test_actualizar_partido_de_candidato_en_elecciones(): void
    {
        $usuario = $this->crearUsuario();
        $candidato = Candidato::create(['idUsuario' => $usuario->getKey()]);
        $eleccion = $this->crearEleccion();
        $cargo = $this->crearCargo();
        $partido1 = $this->crearPartido();
        $partido2 = $this->crearPartido();

        CandidatoEleccion::create([
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'idCargo' => $cargo->getKey(),
            'idPartido' => $partido1->getKey(),
        ]);

        $this->service->actualizarPartidoDeCandidatoEnElecciones(
            ['idPartido' => $partido2->getKey()],
            $candidato,
            $eleccion
        );

        $this->assertDatabaseHas('CandidatoEleccion', [
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'idPartido' => $partido2->getKey(),
        ]);
    }

    public function test_actualizar_cargo_de_candidato_en_elecciones(): void
    {
        $usuario = $this->crearUsuario();
        $candidato = Candidato::create(['idUsuario' => $usuario->getKey()]);
        $eleccion = $this->crearEleccion();
        $cargo1 = $this->crearCargo();
        $cargo2 = $this->crearCargo();

        CandidatoEleccion::create([
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'idCargo' => $cargo1->getKey(),
        ]);

        $this->service->actualizarCargoDeCandidatoEnElecciones(
            ['idCargo' => $cargo2->getKey()],
            $candidato,
            $eleccion
        );

        $this->assertDatabaseHas('CandidatoEleccion', [
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'idCargo' => $cargo2->getKey(),
        ]);
    }

    public function test_remover_partido_de_candidato_en_elecciones(): void
    {
        $usuario = $this->crearUsuario();
        $candidato = Candidato::create(['idUsuario' => $usuario->getKey()]);
        $eleccion = $this->crearEleccion();
        $cargo = $this->crearCargo();
        $partido = $this->crearPartido();

        CandidatoEleccion::create([
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'idCargo' => $cargo->getKey(),
            'idPartido' => $partido->getKey(),
        ]);

        $this->service->removerPartidoDeCandidatoEnElecciones($candidato, $eleccion);

        $this->assertDatabaseHas('CandidatoEleccion', [
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'idPartido' => null,
        ]);
    }

    public function test_añadir_propuesta_de_candidato(): void
    {
        $usuario = $this->crearUsuario();
        $candidato = Candidato::create(['idUsuario' => $usuario->getKey()]);
        $eleccion = $this->crearEleccion();

        $this->service->añadirPropuestaDeCandidato([
            'propuesta' => 'Propuesta de Prueba',
            'descripcion' => 'Descripción de la propuesta',
        ], $candidato, $eleccion);

        $this->assertDatabaseHas('PropuestaCandidato', [
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'propuesta' => 'Propuesta de Prueba',
            'descripcion' => 'Descripción de la propuesta',
        ]);
    }

    public function test_actualizar_propuesta_de_candidato(): void
    {
        $usuario = $this->crearUsuario();
        $candidato = Candidato::create(['idUsuario' => $usuario->getKey()]);
        $eleccion = $this->crearEleccion();

        $propuesta = PropuestaCandidato::create([
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'propuesta' => 'Propuesta Original',
            'descripcion' => 'Descripción original',
        ]);

        $this->service->actualizarPropuestaDeCandidato([
            'propuesta' => 'Propuesta Modificada',
            'descripcion' => 'Descripción modificada',
        ], $propuesta);

        $this->assertDatabaseHas('PropuestaCandidato', [
            'idPropuesta' => $propuesta->getKey(),
            'propuesta' => 'Propuesta Modificada',
            'descripcion' => 'Descripción modificada',
        ]);
    }

    public function test_eliminar_propuesta_de_candidato(): void
    {
        $usuario = $this->crearUsuario();
        $candidato = Candidato::create(['idUsuario' => $usuario->getKey()]);
        $eleccion = $this->crearEleccion();

        $propuesta = PropuestaCandidato::create([
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'propuesta' => 'Propuesta a Eliminar',
            'descripcion' => 'Descripción',
        ]);

        $this->service->eliminarPropuestaDeCandidato($propuesta->getKey());

        $this->assertDatabaseMissing('PropuestaCandidato', [
            'idPropuestaCandidato' => $propuesta->getKey(),
        ]);
    }

    public function test_obtener_propuestas_de_candidato_en_elecciones(): void
    {
        $usuario = $this->crearUsuario();
        $candidato = Candidato::create(['idUsuario' => $usuario->getKey()]);
        $eleccion = $this->crearEleccion();

        for ($i = 1; $i <= 3; $i++) {
            PropuestaCandidato::create([
                'idCandidato' => $candidato->getKey(),
                'idElecciones' => $eleccion->getKey(),
                'propuesta' => "Propuesta {$i}",
                'descripcion' => "Descripción {$i}",
            ]);
        }

        $propuestas = $this->service->obtenerPropuestasDeCandidatoEnElecciones($candidato, $eleccion);

        $this->assertCount(3, $propuestas);
    }

    public function test_obtener_propuesta_de_candidato(): void
    {
        $usuario = $this->crearUsuario();
        $candidato = Candidato::create(['idUsuario' => $usuario->getKey()]);
        $eleccion = $this->crearEleccion();

        $propuesta = PropuestaCandidato::create([
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'propuesta' => 'Propuesta Específica',
            'descripcion' => 'Descripción específica',
        ]);

        $resultado = $this->service->obtenerPropuestaDeCandidato($propuesta->getKey());

        $this->assertEquals($propuesta->getKey(), $resultado->getKey());
        $this->assertEquals('Propuesta Específica', $resultado->propuesta);
    }
}
