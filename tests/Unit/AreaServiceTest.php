<?php

namespace Tests\Unit;

use App\Models\Area;
use App\Services\AreaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class AreaServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $seed = true;

    private AreaService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AreaService();
    }

    public function test_obtener_areas_retorna_coleccion_vacia_si_no_hay_areas(): void
    {
        $areasIniciales = Area::count();
        $areas = $this->service->obtenerAreas();

        $this->assertCount($areasIniciales, $areas);
    }

    public function test_obtener_areas_retorna_todas_las_areas(): void
    {
        $areasIniciales = Area::count();
        // Crear varias áreas
        Area::create(['area' => 'Área de Ingeniería']);
        Area::create(['area' => 'Área de Medicina']);
        Area::create(['area' => 'Área de Derecho']);

        $areas = $this->service->obtenerAreas();

        $this->assertCount($areasIniciales + 3, $areas);
    }

    public function test_obtener_area_por_id_retorna_area_correcta(): void
    {
        $area = Area::create(['area' => 'Área de Prueba']);

        $resultado = $this->service->obtenerAreaPorId($area->getKey());

        $this->assertEquals($area->getKey(), $resultado->getKey());
        $this->assertEquals('Área de Prueba', $resultado->area);
    }

    public function test_obtener_area_por_id_lanza_excepcion_si_no_existe(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->obtenerAreaPorId(99999);
    }

    public function test_crear_area_crea_y_retorna_nueva_area(): void
    {
        $datos = ['area' => 'Nueva Área'];

        $area = $this->service->crearArea($datos);

        $this->assertDatabaseHas('Area', ['area' => 'Nueva Área']);
        $this->assertEquals('Nueva Área', $area->area);
        $this->assertInstanceOf(Area::class, $area);
    }

    public function test_editar_area_actualiza_datos_correctamente(): void
    {
        $area = Area::create(['area' => 'Área Original']);

        $resultado = $this->service->editarArea(['area' => 'Área Modificada'], $area);

        $this->assertDatabaseHas('Area', ['area' => 'Área Modificada']);
        $this->assertDatabaseMissing('Area', ['area' => 'Área Original']);
        $this->assertEquals('Área Modificada', $resultado->area);
    }

    public function test_eliminar_area_elimina_correctamente(): void
    {
        $area = Area::create(['area' => 'Área a Eliminar']);

        $this->service->eliminarArea($area);

        $this->assertDatabaseMissing('Area', ['area' => 'Área a Eliminar']);
    }

    public function test_crear_multiples_areas_y_verificar_conteo(): void
    {
        $areasIniciales = Area::count();

        for ($i = 1; $i <= 5; $i++) {
            $this->service->crearArea(['area' => "Área {$i}"]);
        }

        $areas = $this->service->obtenerAreas();

        $this->assertCount(5 + $areasIniciales, $areas);
    }
}
