<?php

namespace Tests\Feature;

use App\Interfaces\DTO\Services\IVotosCandidatoDTO;
use App\Models\Area;
use App\Models\Candidato;
use App\Models\Cargo;
use App\Models\Elecciones;
use App\Models\EstadoElecciones;
use App\Models\Partido;
use App\Models\TipoVoto;
use App\Models\User;
use App\Models\VotoCandidato;
use App\Models\VotoPartido;
use App\Services\EleccionesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Tests\TestCase;

class EleccionesServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $seed = true;

    private function crearEleccion(?EstadoElecciones $estado = null): Elecciones
    {
        $estado = $estado ?? EstadoElecciones::programado();

        $eleccion = Elecciones::create([
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'fechaInicio' => now(),
            'fechaCierre' => now()->addMonth(),
            'descripcion' => fake()->sentence(10),
            'idEstado' => $estado->getKey(),
        ]);

        return $eleccion;
    }

    private function crearUsuario(): User
    {

        return User::factory()->create([
            'correo' => fake()->email(),
            'contraseña' => bcrypt(fake()->password()),
            'idEstadoUsuario' => 1,
        ]);
    }

    private function crearArea(): Area
    {
        $area = new Area([
            'area' => fake()->words(2, true),
        ]);
        $area->save();

        return $area;
    }

    private function crearCargo(?Area $area = null): Cargo
    {
        $area = $area ?? $this->crearArea();

        $cargo = new Cargo([
            'cargo' => fake()->words(2, true),
            'idArea' => $area->getKey(),
        ]);
        $cargo->save();

        return $cargo;
    }

    private function crearPartido(): Partido
    {
        $partido = new Partido([
            'partido' => fake()->words(2, true),
            'urlPartido' => fake()->url(),
            'descripcion' => fake()->sentence(10),
        ]);
        $partido->save();

        return $partido;
    }

    private function crearCandidato(Elecciones $eleccion, ?Cargo $cargo = null, ?Partido $partido = null, ?User $usuario = null): Candidato
    {
        $cargo = $cargo ?? $this->crearCargo();
        $partido = $partido ?? $this->crearPartido();
        $usuario = $usuario ?? $this->crearUsuario();

        $candidato = new Candidato([
            'idElecciones' => $eleccion->getKey(),
            'idCargo' => $cargo->getKey(),
            'idPartido' => $partido->getKey(),
            'idUsuario' => $usuario->getKey(),
        ]);
        $candidato->save();

        // Asociar candidato a la elección
        $eleccion->candidatos()->attach($candidato);

        return $candidato;
    }

    public function test_obtener_eleccion_activa(): void
    {
        $eleccion = $this->crearEleccion();
        $service = new EleccionesService($eleccion);

        $resultado = $service->obtenerEleccionActiva();

        $this->assertInstanceOf(Elecciones::class, $resultado);
        $this->assertEquals($eleccion->getKey(), $resultado->getKey());
    }

    public function test_usuario_esta_en_padron_electoral(): void
    {
        $eleccion = $this->crearEleccion();
        $usuario = $this->crearUsuario();

        // Agregar usuario al padrón electoral
        $eleccion->usuarios()->attach($usuario);

        $service = new EleccionesService($eleccion);
        $resultado = $service->estaEnPadronElectoral($usuario, null);

        $this->assertTrue($resultado);
    }

    public function test_usuario_no_esta_en_padron_electoral(): void
    {
        $eleccion = $this->crearEleccion();
        $usuario = $this->crearUsuario();

        // No agregar usuario al padrón

        $service = new EleccionesService($eleccion);
        $resultado = $service->estaEnPadronElectoral($usuario, null);

        $this->assertFalse($resultado);
    }

    public function test_usuario_esta_en_padron_electoral_de_eleccion_especifica(): void
    {
        $eleccionActiva = $this->crearEleccion();
        $otraEleccion = $this->crearEleccion();
        $usuario = $this->crearUsuario();

        // Agregar usuario solo al padrón de la otra elección
        $otraEleccion->usuarios()->attach($usuario);

        $service = new EleccionesService($eleccionActiva);

        // El usuario no está en la elección activa
        $this->assertFalse($service->estaEnPadronElectoral($usuario, null));

        // Pero sí está en la otra elección
        $this->assertTrue($service->estaEnPadronElectoral($usuario, $otraEleccion));
    }

    public function test_obtener_candidatos_por_cargo(): void
    {
        $eleccion = $this->crearEleccion();
        $cargo = $this->crearCargo();
        $otroCargo = $this->crearCargo();

        // Crear candidatos para el cargo específico
        $candidato1 = $this->crearCandidato($eleccion, $cargo);
        $candidato2 = $this->crearCandidato($eleccion, $cargo);

        // Crear un candidato para otro cargo
        $candidato3 = $this->crearCandidato($eleccion, $otroCargo);

        $service = new EleccionesService($eleccion);
        $resultado = $service->obtenerCandidatos($cargo, null);

        $this->assertInstanceOf(Collection::class, $resultado);
        $this->assertCount(2, $resultado);
        $this->assertTrue($resultado->contains('idCandidato', $candidato1->getKey()));
        $this->assertTrue($resultado->contains('idCandidato', $candidato2->getKey()));
        $this->assertFalse($resultado->contains('idCandidato', $candidato3->getKey()));
    }

    public function test_obtener_candidatos_de_eleccion_especifica(): void
    {
        $eleccionActiva = $this->crearEleccion();
        $otraEleccion = $this->crearEleccion();
        $cargo = $this->crearCargo();

        // Crear candidatos en elecciones diferentes
        $candidatoActiva = $this->crearCandidato($eleccionActiva, $cargo);
        $candidatoOtra = $this->crearCandidato($otraEleccion, $cargo);

        $service = new EleccionesService($eleccionActiva);

        // Obtener candidatos de la otra elección
        $resultado = $service->obtenerCandidatos($cargo, $otraEleccion);

        $this->assertCount(1, $resultado);
        $this->assertTrue($resultado->contains('idCandidato', $candidatoOtra->getKey()));
        $this->assertFalse($resultado->contains('idCandidato', $candidatoActiva->getKey()));
    }

    public function test_obtener_votos_de_candidato_especifico(): void
    {
        $eleccion = $this->crearEleccion();
        $cargo = $this->crearCargo();
        $candidato = $this->crearCandidato($eleccion, $cargo);

        // Crear votos para el candidato
        for ($i = 0; $i < 5; $i++) {
            VotoCandidato::create([
                'idCandidato' => $candidato->getKey(),
                'idElecciones' => $eleccion->getKey(),
                'idTipoVoto' => TipoVoto::ID_MISMA_AREA,
            ]);
        }

        $service = new EleccionesService($eleccion);
        $resultado = $service->obtenerVotos($candidato, null);

        // 5 votos de MISMA_AREA (peso 2) = 10 puntos
        $this->assertEquals(10, $resultado->getVotos());
    }

    public function test_obtener_votos_de_todos_los_candidatos(): void
    {
        $eleccion = $this->crearEleccion();
        $cargo = $this->crearCargo();

        $candidato1 = $this->crearCandidato($eleccion, $cargo);
        $candidato2 = $this->crearCandidato($eleccion, $cargo);

        // Crear votos
        for ($i = 0; $i < 3; $i++) {
            VotoCandidato::create([
                'idCandidato' => $candidato1->getKey(),
                'idElecciones' => $eleccion->getKey(),
                'idTipoVoto' => TipoVoto::ID_MISMA_AREA,
            ]);
        }

        for ($i = 0; $i < 7; $i++) {
            VotoCandidato::create([
                'idCandidato' => $candidato2->getKey(),
                'idElecciones' => $eleccion->getKey(),
                'idTipoVoto' => TipoVoto::ID_MISMA_AREA,
            ]);
        }

        $service = new EleccionesService($eleccion);
        $resultado = $service->obtenerVotos(null, null);

        $this->assertInstanceOf(Collection::class, $resultado);
        $this->assertCount(2, $resultado);

        // Verificar que cada elemento es un DTO con los votos correctos
        $resultado->each(function ($dto) use ($candidato1, $candidato2) {
            $this->assertInstanceOf(IVotosCandidatoDTO::class, $dto);

            if ($dto->getCandidato()->getKey() === $candidato1->getKey()) {
                // 3 votos * peso 2 = 6
                $this->assertEquals(6, $dto->getVotos());
            } elseif ($dto->getCandidato()->getKey() === $candidato2->getKey()) {
                // 7 votos * peso 2 = 14
                $this->assertEquals(14, $dto->getVotos());
            }
        });
    }

    public function test_obtener_votos_filtra_por_eleccion(): void
    {
        $eleccion1 = $this->crearEleccion();
        $eleccion2 = $this->crearEleccion();
        $cargo = $this->crearCargo();

        $candidato = $this->crearCandidato($eleccion1, $cargo);

        // Votos en elección 1
        for ($i = 0; $i < 3; $i++) {
            VotoCandidato::create([
                'idCandidato' => $candidato->getKey(),
                'idElecciones' => $eleccion1->getKey(),
                'idTipoVoto' => TipoVoto::ID_MISMA_AREA,
            ]);
        }

        // Votos en elección 2
        for ($i = 0; $i < 5; $i++) {
            VotoCandidato::create([
                'idCandidato' => $candidato->getKey(),
                'idElecciones' => $eleccion2->getKey(),
                'idTipoVoto' => TipoVoto::ID_MISMA_AREA,
            ]);
        }

        $service = new EleccionesService($eleccion1);

        // Votos en elección activa (elección 1)
        // 3 votos * peso 2 = 6
        $votosEleccion1 = $service->obtenerVotos($candidato, null);
        $this->assertEquals(6, $votosEleccion1->getVotos());

        // Votos en elección específica (elección 2)
        // 5 votos * peso 2 = 10
        $votosEleccion2 = $service->obtenerVotos($candidato, $eleccion2);
        $this->assertEquals(10, $votosEleccion2->getVotos());
    }

    public function test_cambiar_eleccion_activa(): void
    {
        $eleccionInicial = $this->crearEleccion();
        $nuevaEleccion = $this->crearEleccion();

        $service = new EleccionesService($eleccionInicial);

        // Verificar que la elección activa es la inicial
        $this->assertEquals($eleccionInicial->getKey(), $service->obtenerEleccionActiva()->getKey());

        // Cambiar la elección activa
        $service->cambiarEleccionActiva($nuevaEleccion);

        // Verificar que la elección activa cambió
        $this->assertEquals($nuevaEleccion->getKey(), $service->obtenerEleccionActiva()->getKey());
    }

    public function test_cambiar_eleccion_activa_actualiza_configuracion(): void
    {
        $eleccionInicial = $this->crearEleccion();
        $nuevaEleccion = $this->crearEleccion();

        $service = new EleccionesService($eleccionInicial);

        // Cambiar la elección activa
        $service->cambiarEleccionActiva($nuevaEleccion);

        // Verificar que se guardó en la configuración
        $valorGuardado = \App\Models\Configuracion::obtenerValor(\App\Enum\Config::ELECCION_ACTIVA);
        $this->assertEquals($nuevaEleccion->getKey(), $valorGuardado);
    }

    public function test_obtener_partidos_de_eleccion_activa(): void
    {
        $eleccion = $this->crearEleccion();

        // Crear partidos y asociarlos a la elección
        $partido1 = $this->crearPartido();
        $partido2 = $this->crearPartido();
        $partido3 = $this->crearPartido();

        $eleccion->partidos()->attach([$partido1->getKey(), $partido2->getKey(), $partido3->getKey()]);

        $service = new EleccionesService($eleccion);
        $resultado = $service->obtenerPartidos(null);

        $this->assertInstanceOf(Collection::class, $resultado);
        $this->assertCount(3, $resultado);
        $this->assertTrue($resultado->contains('idPartido', $partido1->getKey()));
        $this->assertTrue($resultado->contains('idPartido', $partido2->getKey()));
        $this->assertTrue($resultado->contains('idPartido', $partido3->getKey()));
    }

    public function test_obtener_partidos_de_eleccion_especifica(): void
    {
        $eleccionActiva = $this->crearEleccion();
        $otraEleccion = $this->crearEleccion();

        // Crear partidos
        $partidoActiva = $this->crearPartido();
        $partidoOtra = $this->crearPartido();

        // Asociar partidos a diferentes elecciones
        $eleccionActiva->partidos()->attach($partidoActiva->getKey());
        $otraEleccion->partidos()->attach($partidoOtra->getKey());

        $service = new EleccionesService($eleccionActiva);

        // Obtener partidos de la otra elección
        $resultado = $service->obtenerPartidos($otraEleccion);

        $this->assertCount(1, $resultado);
        $this->assertTrue($resultado->contains('idPartido', $partidoOtra->getKey()));
        $this->assertFalse($resultado->contains('idPartido', $partidoActiva->getKey()));
    }

    public function test_obtener_partidos_retorna_coleccion_vacia_si_no_hay_partidos(): void
    {
        $eleccion = $this->crearEleccion();

        // No asociar ningún partido

        $service = new EleccionesService($eleccion);
        $resultado = $service->obtenerPartidos(null);

        $this->assertInstanceOf(Collection::class, $resultado);
        $this->assertCount(0, $resultado);
    }

    public function test_obtener_votos_de_partido_especifico(): void
    {
        $eleccion = $this->crearEleccion();
        $partido = $this->crearPartido();

        // Asociar partido a la elección
        $eleccion->partidos()->attach($partido->getKey());

        // Crear votos para el partido
        for ($i = 0; $i < 5; $i++) {
            VotoPartido::create([
                'idPartido' => $partido->getKey(),
                'idElecciones' => $eleccion->getKey(),
                'idTipoVoto' => TipoVoto::ID_NO_APLICABLE,
            ]);
        }

        $this->assertEquals(5, $partido->votos()->where('idElecciones', '=', $eleccion->getKey())->count());
    }

    public function test_obtener_votos_de_todos_los_partidos(): void
    {
        $eleccion = $this->crearEleccion();

        $partido1 = $this->crearPartido();
        $partido2 = $this->crearPartido();

        // Asociar partidos a la elección
        $eleccion->partidos()->attach([$partido1->getKey(), $partido2->getKey()]);

        // Crear votos
        for ($i = 0; $i < 4; $i++) {
            VotoPartido::create([
                'idPartido' => $partido1->getKey(),
                'idElecciones' => $eleccion->getKey(),
                'idTipoVoto' => TipoVoto::ID_NO_APLICABLE,
            ]);
        }

        for ($i = 0; $i < 8; $i++) {
            VotoPartido::create([
                'idPartido' => $partido2->getKey(),
                'idElecciones' => $eleccion->getKey(),
                'idTipoVoto' => TipoVoto::ID_NO_APLICABLE,
            ]);
        }

        // Verificar votos de cada partido
        $votosPartido1 = $partido1->votos()->where('idElecciones', '=', $eleccion->getKey())->count();
        $votosPartido2 = $partido2->votos()->where('idElecciones', '=', $eleccion->getKey())->count();

        $this->assertEquals(4, $votosPartido1);
        $this->assertEquals(8, $votosPartido2);
    }

    public function test_obtener_votos_de_partido_filtra_por_eleccion(): void
    {
        $eleccion1 = $this->crearEleccion();
        $eleccion2 = $this->crearEleccion();

        $partido = $this->crearPartido();

        // Asociar partido a ambas elecciones
        $eleccion1->partidos()->attach($partido->getKey());
        $eleccion2->partidos()->attach($partido->getKey());

        // Votos en elección 1
        for ($i = 0; $i < 3; $i++) {
            VotoPartido::create([
                'idPartido' => $partido->getKey(),
                'idElecciones' => $eleccion1->getKey(),
                'idTipoVoto' => TipoVoto::ID_NO_APLICABLE,
            ]);
        }

        // Votos en elección 2
        for ($i = 0; $i < 6; $i++) {
            VotoPartido::create([
                'idPartido' => $partido->getKey(),
                'idElecciones' => $eleccion2->getKey(),
                'idTipoVoto' => TipoVoto::ID_NO_APLICABLE,
            ]);
        }

        // Votos en elección 1
        $votosEleccion1 = $partido->votos()->where('idElecciones', '=', $eleccion1->getKey())->count();
        $this->assertEquals(3, $votosEleccion1);

        // Votos en elección 2
        $votosEleccion2 = $partido->votos()->where('idElecciones', '=', $eleccion2->getKey())->count();
        $this->assertEquals(6, $votosEleccion2);
    }

    public function test_partido_sin_votos_retorna_cero(): void
    {
        $eleccion = $this->crearEleccion();
        $partido = $this->crearPartido();

        // Asociar partido a la elección pero sin votos
        $eleccion->partidos()->attach($partido->getKey());

        $votosPartido = $partido->votos()->where('idElecciones', '=', $eleccion->getKey())->count();

        $this->assertEquals(0, $votosPartido);
    }

    public function test_votos_de_partido_y_candidato_son_independientes(): void
    {
        $eleccion = $this->crearEleccion();
        $cargo = $this->crearCargo();
        $partido = $this->crearPartido();
        $candidato = $this->crearCandidato($eleccion, $cargo, $partido);

        // Asociar partido a la elección
        $eleccion->partidos()->attach($partido->getKey());

        // Crear votos para el partido
        for ($i = 0; $i < 3; $i++) {
            VotoPartido::create([
                'idPartido' => $partido->getKey(),
                'idElecciones' => $eleccion->getKey(),
                'idTipoVoto' => TipoVoto::ID_NO_APLICABLE,
            ]);
        }

        // Crear votos para el candidato
        for ($i = 0; $i < 5; $i++) {
            VotoCandidato::create([
                'idCandidato' => $candidato->getKey(),
                'idElecciones' => $eleccion->getKey(),
                'idTipoVoto' => TipoVoto::ID_MISMA_AREA,
            ]);
        }

        // Verificar que los votos son independientes
        $votosPartido = $partido->votos()->where('idElecciones', '=', $eleccion->getKey())->count();
        $votosCandidato = $candidato->votos()->where('idElecciones', '=', $eleccion->getKey())->count();

        $this->assertEquals(3, $votosPartido);
        $this->assertEquals(5, $votosCandidato);
    }

    public function test_conteo_votos_ponderado_mixto(): void
    {
        $eleccion = $this->crearEleccion();
        $candidato = $this->crearCandidato($eleccion);

        // 2 votos de MISMA_AREA (Peso 2) -> 4 puntos
        for ($i = 0; $i < 2; $i++) {
            VotoCandidato::create([
                'idCandidato' => $candidato->getKey(),
                'idElecciones' => $eleccion->getKey(),
                'idTipoVoto' => TipoVoto::ID_MISMA_AREA,
            ]);
        }

        // 3 votos de OTRA_AREA (Peso 1) -> 3 puntos
        for ($i = 0; $i < 3; $i++) {
            VotoCandidato::create([
                'idCandidato' => $candidato->getKey(),
                'idElecciones' => $eleccion->getKey(),
                'idTipoVoto' => TipoVoto::ID_OTRA_AREA,
            ]);
        }

        $service = new EleccionesService($eleccion);
        $resultado = $service->obtenerVotos($candidato, null);

        // Total esperado: 4 + 3 = 7
        $this->assertEquals(7, $resultado->getVotos());
    }
}
