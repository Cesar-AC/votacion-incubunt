<?php

namespace Tests\Feature;

use App\Models\Permiso;
use App\Models\Rol;
use App\Models\User;
use App\Services\PermisoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PermisoServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $seed = true;

    private function crearUsuario(): User
    {
        return User::factory()->create([
            'correo' => fake()->email(),
            'contraseña' => bcrypt(fake()->password()),
            'idEstadoUsuario' => 1,
        ]);
    }

    private function crearPermiso(?string $nombre = null): Permiso
    {
        $permiso = new Permiso([
            'permiso' => $nombre ?? fake()->words(3, true),
        ]);
        $permiso->save();

        return $permiso;
    }

    private function crearRol(?string $nombre = null): Rol
    {
        $rol = new Rol([
            'rol' => $nombre ?? fake()->words(2, true),
        ]);
        $rol->save();

        return $rol;
    }

    public function test_comprobar_usuario_tiene_permiso_directo(): void
    {
        $usuario = $this->crearUsuario();
        $permiso = $this->crearPermiso();

        // Asignar permiso directamente al usuario
        $usuario->permisos()->attach($permiso->getKey());

        $service = new PermisoService();
        $resultado = $service->comprobarUsuario($usuario, $permiso);

        $this->assertTrue($resultado);
    }

    public function test_comprobar_usuario_no_tiene_permiso(): void
    {
        $usuario = $this->crearUsuario();
        $permiso = $this->crearPermiso();

        // No asignar permiso

        $service = new PermisoService();
        $resultado = $service->comprobarUsuario($usuario, $permiso);

        $this->assertFalse($resultado);
    }

    public function test_comprobar_usuario_tiene_permiso_a_traves_de_rol(): void
    {
        $usuario = $this->crearUsuario();
        $permiso = $this->crearPermiso();
        $rol = $this->crearRol();

        // Asignar permiso al rol y rol al usuario
        $rol->permisos()->attach($permiso->getKey());
        $usuario->roles()->attach($rol->getKey());

        $service = new PermisoService();
        $resultado = $service->comprobarUsuario($usuario, $permiso);

        $this->assertTrue($resultado);
    }

    public function test_comprobar_usuario_modo_estricto_solo_verifica_permiso_directo(): void
    {
        $usuario = $this->crearUsuario();
        $permiso = $this->crearPermiso();
        $rol = $this->crearRol();

        // Asignar permiso solo a través del rol
        $rol->permisos()->attach($permiso->getKey());
        $usuario->roles()->attach($rol->getKey());

        $service = new PermisoService();

        // Con modo estricto, no debe encontrar el permiso
        $resultadoEstricto = $service->comprobarUsuario($usuario, $permiso, true);
        $this->assertFalse($resultadoEstricto);

        // Sin modo estricto, sí debe encontrar el permiso
        $resultadoNormal = $service->comprobarUsuario($usuario, $permiso, false);
        $this->assertTrue($resultadoNormal);
    }

    public function test_comprobar_usuario_modo_estricto_con_permiso_directo(): void
    {
        $usuario = $this->crearUsuario();
        $permiso = $this->crearPermiso();

        // Asignar permiso directamente al usuario
        $usuario->permisos()->attach($permiso->getKey());

        $service = new PermisoService();
        $resultado = $service->comprobarUsuario($usuario, $permiso, true);

        $this->assertTrue($resultado);
    }

    public function test_comprobar_rol_tiene_permiso(): void
    {
        $rol = $this->crearRol();
        $permiso = $this->crearPermiso();

        // Asignar permiso al rol
        $rol->permisos()->attach($permiso->getKey());

        $service = new PermisoService();
        $resultado = $service->comprobarRol($rol, $permiso);

        $this->assertTrue($resultado);
    }

    public function test_comprobar_rol_no_tiene_permiso(): void
    {
        $rol = $this->crearRol();
        $permiso = $this->crearPermiso();

        // No asignar permiso

        $service = new PermisoService();
        $resultado = $service->comprobarRol($rol, $permiso);

        $this->assertFalse($resultado);
    }

    public function test_pertenece_usuario_a_rol(): void
    {
        $usuario = $this->crearUsuario();
        $rol = $this->crearRol();

        // Asignar usuario al rol
        $usuario->roles()->attach($rol->getKey());

        $service = new PermisoService();
        $resultado = $service->perteneceUsuarioARol($usuario, $rol);

        $this->assertTrue($resultado);
    }

    public function test_usuario_no_pertenece_a_rol(): void
    {
        $usuario = $this->crearUsuario();
        $rol = $this->crearRol();

        // No asignar usuario al rol

        $service = new PermisoService();
        $resultado = $service->perteneceUsuarioARol($usuario, $rol);

        $this->assertFalse($resultado);
    }

    public function test_agregar_permiso_a_usuario(): void
    {
        $usuario = $this->crearUsuario();
        $permiso = $this->crearPermiso();

        $service = new PermisoService();

        // Verificar que no tiene el permiso
        $this->assertFalse($service->comprobarUsuario($usuario, $permiso, true));

        // Agregar permiso
        $service->agregarPermisoAUsuario($usuario, $permiso);

        // Verificar que ahora tiene el permiso
        $this->assertTrue($service->comprobarUsuario($usuario, $permiso, true));
    }

    public function test_quitar_permiso_de_usuario(): void
    {
        $usuario = $this->crearUsuario();
        $permiso = $this->crearPermiso();

        // Asignar permiso
        $usuario->permisos()->attach($permiso->getKey());

        $service = new PermisoService();

        // Verificar que tiene el permiso
        $this->assertTrue($service->comprobarUsuario($usuario, $permiso, true));

        // Quitar permiso
        $service->quitarPermisoDeUsuario($usuario, $permiso);

        // Verificar que ya no tiene el permiso
        $this->assertFalse($service->comprobarUsuario($usuario, $permiso, true));
    }

    public function test_agregar_permiso_a_rol(): void
    {
        $rol = $this->crearRol();
        $permiso = $this->crearPermiso();

        $service = new PermisoService();

        // Verificar que no tiene el permiso
        $this->assertFalse($service->comprobarRol($rol, $permiso));

        // Agregar permiso
        $service->agregarPermisoARol($rol, $permiso);

        // Verificar que ahora tiene el permiso
        $this->assertTrue($service->comprobarRol($rol, $permiso));
    }

    public function test_quitar_permiso_de_rol(): void
    {
        $rol = $this->crearRol();
        $permiso = $this->crearPermiso();

        // Asignar permiso
        $rol->permisos()->attach($permiso->getKey());

        $service = new PermisoService();

        // Verificar que tiene el permiso
        $this->assertTrue($service->comprobarRol($rol, $permiso));

        // Quitar permiso
        $service->quitarPermisoDeRol($rol, $permiso);

        // Verificar que ya no tiene el permiso
        $this->assertFalse($service->comprobarRol($rol, $permiso));
    }

    public function test_agregar_usuario_a_rol(): void
    {
        $usuario = $this->crearUsuario();
        $rol = $this->crearRol();

        $service = new PermisoService();

        // Verificar que no pertenece al rol
        $this->assertFalse($service->perteneceUsuarioARol($usuario, $rol));

        // Agregar usuario al rol
        $service->agregarUsuarioARol($usuario, $rol);

        // Verificar que ahora pertenece al rol
        $this->assertTrue($service->perteneceUsuarioARol($usuario, $rol));
    }

    public function test_quitar_usuario_de_rol(): void
    {
        $usuario = $this->crearUsuario();
        $rol = $this->crearRol();

        // Asignar usuario al rol
        $usuario->roles()->attach($rol->getKey());

        $service = new PermisoService();

        // Verificar que pertenece al rol
        $this->assertTrue($service->perteneceUsuarioARol($usuario, $rol));

        // Quitar usuario del rol
        $service->quitarUsuarioDeRol($usuario, $rol);

        // Verificar que ya no pertenece al rol
        $this->assertFalse($service->perteneceUsuarioARol($usuario, $rol));
    }

    public function test_permiso_desde_enum(): void
    {
        $service = new PermisoService();
        $resultado = $service->permisoDesdeEnum(\App\Enum\Permiso::VOTO_VOTAR);

        $this->assertInstanceOf(Permiso::class, $resultado);
        $this->assertEquals(\App\Enum\Permiso::VOTO_VOTAR->value, $resultado->permiso);
    }
}
