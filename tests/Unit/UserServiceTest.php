<?php

namespace Tests\Unit;

use App\Models\Area;
use App\Models\Carrera;
use App\Models\EstadoUsuario;
use App\Models\PerfilUsuario;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $seed = true;

    private UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserService();
    }

    private function crearArea(): Area
    {
        return Area::create(['area' => fake()->words(2, true)]);
    }

    private function crearCarrera(): Carrera
    {
        return Carrera::create(['carrera' => fake()->words(3, true)]);
    }

    private function crearDatosUsuario(array $override = []): array
    {
        return array_merge([
            'correo' => fake()->unique()->email(),
            'contraseña' => 'password123',
        ], $override);
    }

    private function crearDatosPerfil(array $override = []): array
    {
        $area = $this->crearArea();
        $carrera = $this->crearCarrera();

        return array_merge([
            'idArea' => $area->getKey(),
            'idCarrera' => $carrera->getKey(),
            'nombre' => fake()->firstName(),
            'apellidoPaterno' => fake()->lastName(),
            'apellidoMaterno' => fake()->lastName(),
            'dni' => fake()->numerify('########'),
        ], $override);
    }

    public function test_obtener_usuarios_retorna_coleccion(): void
    {
        $usuariosIniciales = $this->service->obtenerUsuarios()->count();

        $usuarios = $this->service->obtenerUsuarios();

        $this->assertCount($usuariosIniciales, $usuarios);
    }

    public function test_obtener_usuarios_retorna_todos_los_usuarios(): void
    {
        $usuariosIniciales = $this->service->obtenerUsuarios()->count();

        for ($i = 0; $i < 3; $i++) {
            $this->service->crearUsuario($this->crearDatosUsuario(), $this->crearDatosPerfil());
        }

        $usuarios = $this->service->obtenerUsuarios();

        $this->assertCount($usuariosIniciales + 3, $usuarios);
    }

    public function test_obtener_usuario_por_id_retorna_usuario_correcto(): void
    {
        $datosUsuario = $this->crearDatosUsuario(['correo' => 'test@ejemplo.com']);
        $usuario = $this->service->crearUsuario($datosUsuario, $this->crearDatosPerfil());

        $resultado = $this->service->obtenerUsuarioPorId($usuario->getKey());

        $this->assertEquals($usuario->getKey(), $resultado->getKey());
        $this->assertEquals('test@ejemplo.com', $resultado->correo);
    }

    public function test_obtener_usuario_por_id_lanza_excepcion_si_no_existe(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->service->obtenerUsuarioPorId(99999);
    }

    public function test_crear_usuario_crea_usuario_y_perfil(): void
    {
        $datosUsuario = $this->crearDatosUsuario(['correo' => 'nuevo@ejemplo.com']);
        $datosPerfil = $this->crearDatosPerfil(['nombre' => 'Juan', 'apellidoPaterno' => 'Pérez']);

        $usuario = $this->service->crearUsuario($datosUsuario, $datosPerfil);

        $this->assertDatabaseHas('User', ['correo' => 'nuevo@ejemplo.com']);
        $this->assertDatabaseHas('PerfilUsuario', [
            'idUser' => $usuario->getKey(),
            'nombre' => 'Juan',
            'apellidoPaterno' => 'Pérez',
        ]);
        $this->assertInstanceOf(User::class, $usuario);
    }

    public function test_crear_usuario_asigna_estado_activo(): void
    {
        $usuario = $this->service->crearUsuario($this->crearDatosUsuario(), $this->crearDatosPerfil());

        $this->assertEquals(EstadoUsuario::ACTIVO, $usuario->idEstadoUsuario);
    }

    public function test_crear_usuario_hashea_contraseña(): void
    {
        $datosUsuario = $this->crearDatosUsuario(['contraseña' => 'contraseñaSegura123']);

        $usuario = $this->service->crearUsuario($datosUsuario, $this->crearDatosPerfil());

        // La contraseña no debe ser el texto plano
        $this->assertNotEquals('contraseñaSegura123', $usuario->contraseña);
        // Verifica que es un hash válido de bcrypt
        $this->assertTrue(password_verify('contraseñaSegura123', $usuario->contraseña));
    }

    public function test_editar_usuario_actualiza_correo(): void
    {
        $usuario = $this->service->crearUsuario(
            $this->crearDatosUsuario(['correo' => 'original@ejemplo.com']),
            $this->crearDatosPerfil()
        );

        $resultado = $this->service->editarUsuario(['correo' => 'modificado@ejemplo.com'], $usuario);

        $this->assertEquals('modificado@ejemplo.com', $resultado->correo);
        $this->assertDatabaseHas('User', ['correo' => 'modificado@ejemplo.com']);
    }

    public function test_editar_perfil_usuario_actualiza_nombre(): void
    {
        $usuario = $this->service->crearUsuario(
            $this->crearDatosUsuario(),
            $this->crearDatosPerfil(['nombre' => 'NombreOriginal'])
        );

        $this->service->editarPerfilUsuario(['nombre' => 'NombreModificado'], $usuario);

        $this->assertDatabaseHas('PerfilUsuario', [
            'idUser' => $usuario->getKey(),
            'nombre' => 'NombreModificado',
        ]);
    }

    public function test_editar_perfil_usuario_actualiza_area(): void
    {
        $area1 = $this->crearArea();
        $area2 = $this->crearArea();

        $usuario = $this->service->crearUsuario(
            $this->crearDatosUsuario(),
            $this->crearDatosPerfil(['idArea' => $area1->getKey()])
        );

        $this->service->editarPerfilUsuario(['idArea' => $area2->getKey()], $usuario);

        $this->assertDatabaseHas('PerfilUsuario', [
            'idUser' => $usuario->getKey(),
            'idArea' => $area2->getKey(),
        ]);
    }

    public function test_eliminar_usuario_elimina_correctamente(): void
    {
        $usuario = $this->service->crearUsuario($this->crearDatosUsuario(), $this->crearDatosPerfil());
        $usuarioId = $usuario->getKey();

        $this->service->eliminarUsuario($usuario);

        $this->assertDatabaseMissing('User', ['idUser' => $usuarioId]);
    }

    public function test_crear_multiples_usuarios_con_diferentes_areas(): void
    {
        $area1 = $this->crearArea();
        $area2 = $this->crearArea();

        $usuario1 = $this->service->crearUsuario(
            $this->crearDatosUsuario(),
            $this->crearDatosPerfil(['idArea' => $area1->getKey()])
        );

        $usuario2 = $this->service->crearUsuario(
            $this->crearDatosUsuario(),
            $this->crearDatosPerfil(['idArea' => $area2->getKey()])
        );

        $perfil1 = PerfilUsuario::where('idUser', $usuario1->getKey())->first();
        $perfil2 = PerfilUsuario::where('idUser', $usuario2->getKey())->first();

        $this->assertEquals($area1->getKey(), $perfil1->idArea);
        $this->assertEquals($area2->getKey(), $perfil2->idArea);
    }

    public function test_crear_usuario_con_todos_los_campos_del_perfil(): void
    {
        $area = $this->crearArea();
        $carrera = $this->crearCarrera();

        $datosPerfil = [
            'idArea' => $area->getKey(),
            'idCarrera' => $carrera->getKey(),
            'nombre' => 'María',
            'apellidoPaterno' => 'García',
            'apellidoMaterno' => 'López',
            'otrosNombres' => 'Elena',
            'dni' => '12345678',
        ];

        $usuario = $this->service->crearUsuario($this->crearDatosUsuario(), $datosPerfil);

        $this->assertDatabaseHas('PerfilUsuario', [
            'idUser' => $usuario->getKey(),
            'nombre' => 'María',
            'apellidoPaterno' => 'García',
            'apellidoMaterno' => 'López',
            'dni' => '12345678',
        ]);
    }
}
