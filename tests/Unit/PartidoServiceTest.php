<?php

namespace Tests\Unit;

use App\Models\Elecciones;
use App\Models\EstadoElecciones;
use App\Models\Partido;
use App\Services\EleccionesService;
use App\Services\PartidoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class PartidoServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $seed = true;

    private PartidoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PartidoService(new EleccionesService($this->crearEleccion()));
    }

    private function crearEleccion(): Elecciones
    {
        return Elecciones::create([
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'fechaInicio' => now()->addMonth(),
            'fechaCierre' => now()->addMonths(2),
            'descripcion' => fake()->sentence(10),
            'idEstado' => EstadoElecciones::PROGRAMADO,
        ]);
    }

    private function crearDatosPartido(array $override = []): array
    {
        return array_merge([
            'partido' => fake()->words(3, true),
            'urlPartido' => fake()->url(),
            'descripcion' => fake()->sentence(10),
        ], $override);
    }

    public function test_obtener_partidos_retorna_coleccion_vacia_si_no_hay_partidos(): void
    {
        $partidos = $this->service->obtenerPartidos();

        $this->assertCount(0, $partidos);
    }

    public function test_obtener_partidos_retorna_todos_los_partidos(): void
    {
        Partido::create($this->crearDatosPartido(['partido' => 'Partido A']));
        Partido::create($this->crearDatosPartido(['partido' => 'Partido B']));
        Partido::create($this->crearDatosPartido(['partido' => 'Partido C']));

        $partidos = $this->service->obtenerPartidos();

        $this->assertCount(3, $partidos);
    }

    public function test_obtener_partido_por_id_retorna_partido_correcto(): void
    {
        $partido = Partido::create($this->crearDatosPartido(['partido' => 'Partido de Prueba']));

        $resultado = $this->service->obtenerPartidoPorId($partido->getKey());

        $this->assertEquals($partido->getKey(), $resultado->getKey());
        $this->assertEquals('Partido de Prueba', $resultado->partido);
    }

    public function test_obtener_partido_por_id_lanza_excepcion_si_no_existe(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->obtenerPartidoPorId(99999);
    }

    public function test_crear_partido_crea_y_retorna_nuevo_partido(): void
    {
        $datos = $this->crearDatosPartido(['partido' => 'Nuevo Partido']);

        $partido = $this->service->crearPartido($datos);

        $this->assertDatabaseHas('Partido', ['partido' => 'Nuevo Partido']);
        $this->assertEquals('Nuevo Partido', $partido->partido);
        $this->assertInstanceOf(Partido::class, $partido);
    }

    public function test_crear_partido_guarda_todos_los_campos(): void
    {
        $datos = [
            'partido' => 'Partido Completo',
            'urlPartido' => 'https://ejemplo.com',
            'descripcion' => 'Descripción del partido',
        ];

        $partido = $this->service->crearPartido($datos);

        $this->assertEquals('Partido Completo', $partido->partido);
        $this->assertEquals('https://ejemplo.com', $partido->urlPartido);
        $this->assertEquals('Descripción del partido', $partido->descripcion);
    }

    public function test_editar_partido_actualiza_datos_correctamente(): void
    {
        $partido = Partido::create($this->crearDatosPartido(['partido' => 'Partido Original']));

        $nuevosDatos = ['partido' => 'Partido Modificado'];
        $resultado = $this->service->editarPartido($nuevosDatos, $partido);

        $this->assertDatabaseHas('Partido', ['partido' => 'Partido Modificado']);
        $this->assertEquals('Partido Modificado', $resultado->partido);
    }

    public function test_editar_partido_actualiza_url(): void
    {
        $partido = Partido::create($this->crearDatosPartido(['urlPartido' => 'https://original.com']));

        $resultado = $this->service->editarPartido(['urlPartido' => 'https://nueva-url.com'], $partido);

        $this->assertEquals('https://nueva-url.com', $resultado->urlPartido);
    }

    public function test_eliminar_partido_elimina_correctamente(): void
    {
        $partido = Partido::create($this->crearDatosPartido(['partido' => 'Partido a Eliminar']));

        $this->service->eliminarPartido($partido);

        $this->assertDatabaseMissing('Partido', ['partido' => 'Partido a Eliminar']);
    }

    public function test_crear_multiples_partidos_y_verificar_conteo(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->service->crearPartido($this->crearDatosPartido(['partido' => "Partido {$i}"]));
        }

        $partidos = $this->service->obtenerPartidos();

        $this->assertCount(5, $partidos);
    }
}
