<?php

namespace Tests\Unit;

use App\Models\Carrera;
use App\Services\CarreraService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class CarreraServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $seed = true;

    private CarreraService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CarreraService();
    }

    public function test_obtener_carreras_retorna_coleccion_vacia_si_no_hay_carreras(): void
    {
        $carrerasIniciales = Carrera::count();
        $carreras = $this->service->obtenerCarreras();

        $this->assertCount($carrerasIniciales, $carreras);
    }

    public function test_obtener_carreras_retorna_todas_las_carreras(): void
    {
        $carrerasIniciales = Carrera::count();

        Carrera::create(['carrera' => 'Ingeniería de Sistemas']);
        Carrera::create(['carrera' => 'Medicina']);
        Carrera::create(['carrera' => 'Derecho']);

        $carreras = $this->service->obtenerCarreras();

        $this->assertCount($carrerasIniciales + 3, $carreras);
    }

    public function test_obtener_carrera_por_id_retorna_carrera_correcta(): void
    {
        $carrera = Carrera::create(['carrera' => 'Carrera de Prueba']);

        $resultado = $this->service->obtenerCarreraPorId($carrera->getKey());

        $this->assertEquals($carrera->getKey(), $resultado->getKey());
        $this->assertEquals('Carrera de Prueba', $resultado->carrera);
    }

    public function test_obtener_carrera_por_id_lanza_excepcion_si_no_existe(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->obtenerCarreraPorId(99999);
    }

    public function test_crear_carrera_crea_y_retorna_nueva_carrera(): void
    {
        $datos = ['carrera' => 'Nueva Carrera'];

        $carrera = $this->service->crearCarrera($datos);

        $this->assertDatabaseHas('Carrera', ['carrera' => 'Nueva Carrera']);
        $this->assertEquals('Nueva Carrera', $carrera->carrera);
        $this->assertInstanceOf(Carrera::class, $carrera);
    }

    public function test_editar_carrera_actualiza_datos_correctamente(): void
    {
        $carrera = Carrera::create(['carrera' => 'Carrera Original']);

        $resultado = $this->service->editarCarrera(['carrera' => 'Carrera Modificada'], $carrera);

        $this->assertDatabaseHas('Carrera', ['carrera' => 'Carrera Modificada']);
        $this->assertDatabaseMissing('Carrera', ['carrera' => 'Carrera Original']);
        $this->assertEquals('Carrera Modificada', $resultado->carrera);
    }

    public function test_eliminar_carrera_elimina_correctamente(): void
    {
        $carrera = Carrera::create(['carrera' => 'Carrera a Eliminar']);

        $this->service->eliminarCarrera($carrera);

        $this->assertDatabaseMissing('Carrera', ['carrera' => 'Carrera a Eliminar']);
    }

    public function test_crear_multiples_carreras_y_verificar_conteo(): void
    {
        $carrerasIniciales = Carrera::count();

        for ($i = 1; $i <= 5; $i++) {
            $this->service->crearCarrera(['carrera' => "Carrera {$i}"]);
        }

        $carreras = $this->service->obtenerCarreras();

        $this->assertCount($carrerasIniciales + 5, $carreras);
    }
}
