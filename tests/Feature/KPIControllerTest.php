<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Elecciones;
use App\Models\EstadoElecciones;
use App\Models\PadronElectoral;
use App\Models\PerfilUsuario;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Random\Randomizer;
use Tests\TestCase;
use Tests\Utils\LoginHelper;

class KPIControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_contar_elecciones_activas(): void
    {
        LoginHelper::loguearseConPermiso($this, [
            'kpi:estadisticas_electorales:ver',
            'elecciones:crud:agregar',
            'elecciones:crud:editar',
        ]);

        $random = new Randomizer();
        $cantidadEleccionesProgramadas = $random->getInt(1, 10);
        $cantidadEleccionesActivas = 0;

        for ($i = 0; $i < $cantidadEleccionesProgramadas; $i++) {
            $response = $this->post('/elecciones/crear', [
                'titulo' => 'Elección Activa ' . $i,
                'descripcion' => 'Descripción de la elección activa ' . $i,
                'fechaInicio' => now()->addDays(1)->toDateString(),
                'fechaCierre' => now()->addDays(10)->toDateString(),
            ]);

            $this->assertDatabaseHas('Elecciones', [
                'titulo' => 'Elección Activa ' . $i,
            ]);

            $eleccion = $response->json()['data'];

            if ($random->getInt(1, 2) == 1) {
                $response = $this->post('elecciones/' . $eleccion['id'] . '/editar', [
                    'idEstado' => EstadoElecciones::ACTIVO,
                ]);

                $this->assertDatabaseHas('Elecciones', [
                    'idElecciones' => $eleccion['id'],
                    'idEstado' => EstadoElecciones::ACTIVO,
                ]);

                $response->assertStatus(200);
                $cantidadEleccionesActivas++;
            }
        }

        $response = $this->get('/kpi/elecciones-activas');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);

        $responseData = $response->json();
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Cantidad de elecciones activas obtenida con éxito', $responseData['message']);
        $this->assertIsInt($responseData['data']);

        $this->assertEquals($cantidadEleccionesActivas, $responseData['data']);
    }

    public function test_contar_electores_habilitados(): void
    {
        $random = new Randomizer();

        LoginHelper::loguearseConPermiso($this, [
            'kpi:estadisticas_electorales:ver',
            'elecciones:crud:agregar',
        ]);

        $eleccionesCreadas = Elecciones::factory()->count(3)->create();

        /** @var Elecciones $eleccion */
        foreach ($eleccionesCreadas as $eleccion) {
            $cantidadInscritos = $random->getInt(1, 10);
            $usuariosInscritos = User::factory()->count($cantidadInscritos)->create();

            $eleccion->usuarios()->attach($usuariosInscritos->pluck('idUser')->toArray());

            $response = $this->get('/kpi/electores-habilitados/' . $eleccion->getKey());
            $response->assertStatus(200);

            $data = $response->json();
            $this->assertTrue($data['success']);
            $this->assertEquals('Cantidad de electores habilitados obtenida con éxito', $data['message']);
            $this->assertEquals($cantidadInscritos, $data['data']);
        }
    }

    public function test_contar_electores_habilitados_por_area(): void
    {
        $random = new Randomizer();

        LoginHelper::loguearseConPermiso($this, [
            'kpi:estadisticas_electorales:ver',
            'elecciones:crud:agregar',
        ]);

        $eleccionesCreadas = Elecciones::factory()->count(3)->create();

        /** @var Elecciones $eleccion */
        foreach ($eleccionesCreadas as $eleccion) {
            $mapaAreaCantidadUsuarios = [];
            $cantidadInscritos = $random->getInt(1, 10);
            $usuariosInscritos = User::factory()->count($cantidadInscritos)->create();

            foreach ($usuariosInscritos as $usuario) {
                $area = Area::inRandomOrder()->first();

                PerfilUsuario::factory()->create([
                    'idUser' => $usuario->getKey(),
                    'idArea' => $area->getKey(),
                ]);

                $mapaAreaCantidadUsuarios[$area->getKey()] = ($mapaAreaCantidadUsuarios[$area->getKey()] ?? 0) + 1;
            }

            $eleccion->usuarios()->attach($usuariosInscritos->pluck('idUser')->toArray());

            foreach ($mapaAreaCantidadUsuarios as $idArea => $cantidadUsuarios) {
                $response = $this->get('/kpi/electores-habilitados/' . $eleccion->getKey() . '/area/' . $idArea);
                $response->assertStatus(200);

                $data = $response->json();
                $this->assertTrue($data['success']);
                $this->assertEquals('Cantidad de electores habilitados por área obtenida con éxito', $data['message']);
                $this->assertEquals($cantidadUsuarios, $data['data']);
            }
        }
    }

    public function test_porcentaje_participacion_por_eleccion(): void
    {
        $random = new Randomizer();

        LoginHelper::loguearseConPermiso($this, [
            'kpi:estadisticas_electorales:ver',
            'elecciones:crud:agregar',
        ]);

        $eleccionesCreadas = Elecciones::factory()->count(3)->create();

        /** @var Elecciones $eleccion */
        foreach ($eleccionesCreadas as $eleccion) {
            $mapaAreaCantidadUsuarios = [];
            $cantidadInscritos = $random->getInt(1, 10);
            $usuariosInscritos = User::factory()->count($cantidadInscritos)->create();

            foreach ($usuariosInscritos as $usuario) {
                $area = Area::inRandomOrder()->first();

                PerfilUsuario::factory()->create([
                    'idUser' => $usuario->getKey(),
                    'idArea' => $area->getKey(),
                ]);

                $mapaAreaCantidadUsuarios[$area->getKey()] = ($mapaAreaCantidadUsuarios[$area->getKey()] ?? 0) + 1;
            }

            $eleccion->usuarios()->attach($usuariosInscritos->pluck('idUser')->toArray());

            $cantidadVotantes = $random->getInt(0, $cantidadInscritos);
            $usuariosVotantes = $usuariosInscritos->random($cantidadVotantes);
            foreach ($usuariosVotantes as $usuarioVotante) {
                PadronElectoral::where('idElecciones', '=', $eleccion->getKey())
                    ->where('idUsuario', '=', $usuarioVotante->getKey())
                    ->update(['fechaVoto' => now()]);
            }

            foreach ($mapaAreaCantidadUsuarios as $idArea => $cantidadUsuarios) {
                $response = $this->get('kpi/porcentaje-participacion/' . $eleccion->getKey());
                $response->assertStatus(200);

                $data = $response->json();
                $this->assertTrue($data['success']);
                $this->assertEquals('Porcentaje de participación por elección obtenido con éxito', $data['message']);
                $this->assertEquals($cantidadVotantes / $cantidadInscritos * 100, $data['data']);
            }
        }
    }

    public function test_porcentaje_participacion_por_area(): void
    {
        $random = new Randomizer();

        LoginHelper::loguearseConPermiso($this, [
            'kpi:estadisticas_electorales:ver',
            'elecciones:crud:agregar',
        ]);

        $eleccionesCreadas = Elecciones::factory()->count(3)->create();

        /** @var Elecciones $eleccion */
        foreach ($eleccionesCreadas as $eleccion) {
            // mapa usuarios por área y conteo por área
            $mapaAreaCantidadUsuarios = [];
            $usuarioAreaMap = [];

            $cantidadInscritos = $random->getInt(1, 10);
            $usuariosInscritos = User::factory()->count($cantidadInscritos)->create();

            foreach ($usuariosInscritos as $usuario) {
                $area = Area::inRandomOrder()->first();

                PerfilUsuario::factory()->create([
                    'idUser' => $usuario->getKey(),
                    'idArea' => $area->getKey(),
                ]);

                $mapaAreaCantidadUsuarios[$area->getKey()] = ($mapaAreaCantidadUsuarios[$area->getKey()] ?? 0) + 1;
                $usuarioAreaMap[$usuario->getKey()] = $area->getKey();
            }

            $eleccion->usuarios()->attach($usuariosInscritos->pluck('idUser')->toArray());

            // elegir votantes (puede ser 0)
            $cantidadVotantes = $random->getInt(0, $cantidadInscritos);
            if ($cantidadVotantes > 0) {
                $usuariosVotantes = $usuariosInscritos->random($cantidadVotantes);
            } else {
                $usuariosVotantes = collect();
            }

            // contar votantes por área y marcar fechaVoto en padrón
            $mapaAreaVotantes = [];
            foreach ($usuariosVotantes as $usuarioVotante) {
                $idUser = $usuarioVotante->getKey();
                $idArea = $usuarioAreaMap[$idUser] ?? null;
                if ($idArea !== null) {
                    $mapaAreaVotantes[$idArea] = ($mapaAreaVotantes[$idArea] ?? 0) + 1;
                }

                PadronElectoral::where('idElecciones', '=', $eleccion->getKey())
                    ->where('idUsuario', '=', $idUser)
                    ->update(['fechaVoto' => now()]);
            }

            // validar porcentaje por área
            foreach ($mapaAreaCantidadUsuarios as $idArea => $cantidadUsuarios) {
                $expectedVotantesEnArea = $mapaAreaVotantes[$idArea] ?? 0;
                $expected = $cantidadUsuarios > 0 ? ($expectedVotantesEnArea / $cantidadUsuarios * 100) : 0;

                $response = $this->get('kpi/porcentaje-participacion/' . $eleccion->getKey() . '/area/' . $idArea);
                $response->assertStatus(200);

                $data = $response->json();
                $this->assertTrue($data['success']);
                $this->assertEquals('Porcentaje de participación por área obtenido con éxito', $data['message']);
                $this->assertEquals(round($expected, 4), round($data['data'], 4));
            }
        }
    }
}
