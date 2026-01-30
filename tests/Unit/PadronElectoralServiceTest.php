<?php

namespace Tests\Unit;

use App\Models\Area;
use App\Models\Elecciones;
use App\Models\EstadoElecciones;
use App\Models\PadronElectoral;
use App\Models\PerfilUsuario;
use App\Models\User;
use App\Services\EleccionesService;
use App\Services\PadronElectoralService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PadronElectoralServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $seed = true;

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

    private function crearServicio(Elecciones $eleccion): PadronElectoralService
    {
        $eleccionesService = new EleccionesService($eleccion);
        return new PadronElectoralService($eleccionesService);
    }

    public function test_obtener_padron_electoral_retorna_coleccion_vacia(): void
    {
        $eleccion = $this->crearEleccion();
        $service = $this->crearServicio($eleccion);

        $padron = $service->obtenerPadronElectoral();

        $this->assertCount(0, $padron);
    }

    public function test_agregar_usuario_a_eleccion(): void
    {
        $eleccion = $this->crearEleccion();
        $usuario = $this->crearUsuario();
        $service = $this->crearServicio($eleccion);

        $service->agregarUsuarioAEleccion($usuario, $eleccion);

        $this->assertDatabaseHas('PadronElectoral', [
            'idUsuario' => $usuario->getKey(),
            'idElecciones' => $eleccion->getKey(),
        ]);
    }

    public function test_agregar_usuario_duplicado_lanza_excepcion(): void
    {
        $eleccion = $this->crearEleccion();
        $usuario = $this->crearUsuario();
        $service = $this->crearServicio($eleccion);

        $service->agregarUsuarioAEleccion($usuario, $eleccion);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('El usuario ya pertenece al padrón electoral.');

        $service->agregarUsuarioAEleccion($usuario, $eleccion);
    }

    public function test_eliminar_usuario_de_elecciones(): void
    {
        $eleccion = $this->crearEleccion();
        $usuario = $this->crearUsuario();
        $service = $this->crearServicio($eleccion);

        $service->agregarUsuarioAEleccion($usuario, $eleccion);
        $service->eliminarUsuarioDeElecciones($usuario, $eleccion);

        $this->assertDatabaseMissing('PadronElectoral', [
            'idUsuario' => $usuario->getKey(),
            'idElecciones' => $eleccion->getKey(),
        ]);
    }

    public function test_eliminar_usuario_no_existente_lanza_excepcion(): void
    {
        $eleccion = $this->crearEleccion();
        $usuario = $this->crearUsuario();
        $service = $this->crearServicio($eleccion);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('El usuario no pertenece al padrón electoral.');

        $service->eliminarUsuarioDeElecciones($usuario, $eleccion);
    }

    public function test_restablecer_padron_electoral(): void
    {
        $eleccion = $this->crearEleccion();
        $service = $this->crearServicio($eleccion);

        for ($i = 0; $i < 5; $i++) {
            $usuario = $this->crearUsuario();
            $service->agregarUsuarioAEleccion($usuario, $eleccion);
        }

        $this->assertEquals(5, PadronElectoral::where('idElecciones', $eleccion->getKey())->count());

        $service->restablecerPadronElectoral($eleccion);

        $this->assertEquals(0, PadronElectoral::where('idElecciones', $eleccion->getKey())->count());
    }
}
