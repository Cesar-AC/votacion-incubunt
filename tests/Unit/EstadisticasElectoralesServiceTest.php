<?php

namespace Tests\Unit;

use App\Models\Area;
use App\Models\Elecciones;
use App\Models\EstadoElecciones;
use App\Models\PadronElectoral;
use App\Models\PerfilUsuario;
use App\Models\User;
use App\Services\EstadisticasElectoralesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EstadisticasElectoralesServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $seed = true;

    private EstadisticasElectoralesService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EstadisticasElectoralesService();
    }

    private function crearEleccion(int $estado = EstadoElecciones::PROGRAMADO): Elecciones
    {
        return Elecciones::create([
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'fechaInicio' => now(),
            'fechaCierre' => now()->addMonth(),
            'descripcion' => fake()->sentence(10),
            'idEstado' => $estado,
        ]);
    }

    private function crearUsuario(Area $area): User
    {
        $usuario = User::factory()->create([
            'correo' => fake()->unique()->email(),
            'contraseña' => bcrypt('password'),
            'idEstadoUsuario' => 1,
        ]);

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

    private function crearArea(): Area
    {
        return Area::create(['area' => fake()->words(2, true)]);
    }

    public function test_contar_elecciones_activas_sin_elecciones(): void
    {
        $conteo = $this->service->contarEleccionesActivas();

        $this->assertEquals(0, $conteo);
    }

    public function test_contar_elecciones_activas_con_elecciones_programadas(): void
    {
        $this->crearEleccion(EstadoElecciones::PROGRAMADO);
        $this->crearEleccion(EstadoElecciones::PROGRAMADO);
        $this->crearEleccion(EstadoElecciones::FINALIZADO);

        $conteo = $this->service->contarEleccionesActivas();

        $this->assertEquals(2, $conteo);
    }

    public function test_contar_electores_habilitados_por_eleccion_sin_electores(): void
    {
        $eleccion = $this->crearEleccion();

        $conteo = $this->service->contarElectoresHabilitadosPorEleccion($eleccion);

        $this->assertEquals(0, $conteo);
    }

    public function test_contar_electores_habilitados_por_eleccion(): void
    {
        $eleccion = $this->crearEleccion();
        $area = $this->crearArea();

        for ($i = 0; $i < 5; $i++) {
            $usuario = $this->crearUsuario($area);
            PadronElectoral::create([
                'idUsuario' => $usuario->getKey(),
                'idElecciones' => $eleccion->getKey(),
            ]);
        }

        $conteo = $this->service->contarElectoresHabilitadosPorEleccion($eleccion);

        $this->assertEquals(5, $conteo);
    }

    public function test_contar_electores_habilitados_por_eleccion_y_area(): void
    {
        $eleccion = $this->crearEleccion();
        $area1 = $this->crearArea();
        $area2 = $this->crearArea();

        for ($i = 0; $i < 3; $i++) {
            $usuario = $this->crearUsuario($area1);
            PadronElectoral::create([
                'idUsuario' => $usuario->getKey(),
                'idElecciones' => $eleccion->getKey(),
            ]);
        }

        for ($i = 0; $i < 2; $i++) {
            $usuario = $this->crearUsuario($area2);
            PadronElectoral::create([
                'idUsuario' => $usuario->getKey(),
                'idElecciones' => $eleccion->getKey(),
            ]);
        }

        $conteo = $this->service->contarElectoresHabilitadosPorEleccionYArea($eleccion, $area1);

        $this->assertEquals(3, $conteo);
    }

    public function test_contar_votos_por_eleccion(): void
    {
        $eleccion = $this->crearEleccion();
        $area = $this->crearArea();

        for ($i = 0; $i < 5; $i++) {
            $usuario = $this->crearUsuario($area);
            PadronElectoral::create([
                'idUsuario' => $usuario->getKey(),
                'idElecciones' => $eleccion->getKey(),
                'fechaVoto' => $i < 3 ? now() : null, // 3 votaron, 2 no
            ]);
        }

        $conteo = $this->service->contarCantidadVotosPorEleccion($eleccion);

        $this->assertEquals(3, $conteo);
    }

    public function test_calcular_porcentaje_participacion(): void
    {
        $eleccion = $this->crearEleccion();
        $area = $this->crearArea();

        for ($i = 0; $i < 10; $i++) {
            $usuario = $this->crearUsuario($area);
            PadronElectoral::create([
                'idUsuario' => $usuario->getKey(),
                'idElecciones' => $eleccion->getKey(),
                'fechaVoto' => $i < 4 ? now() : null, // 4 de 10 votaron = 40%
            ]);
        }

        $porcentaje = $this->service->calcularPorcentajeParticipacionPorEleccion($eleccion);

        $this->assertEquals(40.0, $porcentaje);
    }

    public function test_calcular_porcentaje_participacion_sin_electores(): void
    {
        $eleccion = $this->crearEleccion();

        $porcentaje = $this->service->calcularPorcentajeParticipacionPorEleccion($eleccion);

        $this->assertEquals(-1, $porcentaje);
    }
}
