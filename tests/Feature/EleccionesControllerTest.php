<?php

namespace Tests\Feature;

use App\Models\Elecciones;
use App\Models\EstadoElecciones;
use App\Models\Permiso;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EleccionesControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * A basic feature test example.
     */

    private const PERMISO_GLOBAL_CRUD_ELECCIONES = 'gestion.elecciones.*';
    private const RUTA_CRUD_ELECCIONES_VER = 'crud.elecciones.ver';
    private const RUTA_CRUD_ELECCIONES_VER_DATOS = 'crud.elecciones.ver_datos';
    private const RUTA_CRUD_ELECCIONES_CREAR = 'crud.elecciones.crear';
    private const RUTA_CRUD_ELECCIONES_EDITAR = 'crud.elecciones.editar';
    private const RUTA_CRUD_ELECCIONES_ELIMINAR = 'crud.elecciones.eliminar';

    private function usuarioConPermiso(string $permiso = self::PERMISO_GLOBAL_CRUD_ELECCIONES){
        $user = User::factory()->create([
            'usuario' => fake()->userName(),
            'email' => fake()->email(),
            'password' => bcrypt(fake()->password()),
        ]);

        $perm = new Permiso([
            'permiso' => $permiso,
        ]);
        $perm->save();

        $user->permisos()->attach($perm);

        return $user;
    }

    private function crearEstadoEleccion(): EstadoElecciones{
        $estadoElecciones = new EstadoElecciones([
            'estado' => fake()->words(2, true),
        ]);
        $estadoElecciones->save();

        return $estadoElecciones;
    }

    public function test_rechazar_cargar_crud_elecciones_sin_iniciar_sesion(): void
    {
        $this->actingAsGuest();
        $response = $this->get(route(self::RUTA_CRUD_ELECCIONES_VER));
        $response->assertViewIs('auth.login');
    }

    public function test_rechazar_cargar_crud_elecciones_con_sesion_iniciada_pero_sin_permiso(): void
    {
        $user = User::factory()->create([
            'usuario' => fake()->userName(),
            'email' => fake()->email(),
            'password' => bcrypt(fake()->password()),
        ]);

        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_CRUD_ELECCIONES_VER));
        $response->assertNotFound();
    }

    public function test_solicitar_datos_de_una_eleccion(): void
    {
        $user = $this->usuarioConPermiso();
        
        $estadoElecciones = $this->crearEstadoEleccion();
        $inicioEleccion = new Carbon(fake()->date());
        $eleccion = new Elecciones([
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'fecha_inicio' => $inicioEleccion,
            'fecha_cierre' => $inicioEleccion->addMonth(),
            'descripcion' => fake()->sentence(10),
            'estado' => $estadoElecciones->getKey(),
        ]);

        $eleccion->save();

        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_CRUD_ELECCIONES_VER_DATOS, [$eleccion->getKey()]));
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'titulo',
                'fecha_inicio',
                'fecha_cierre',
                'descripcion',
                'estado',
            ],
        ]);
    }

    public function test_cargar_vista_crear_una_eleccion(){
        $user = $this->usuarioConPermiso();

        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_CRUD_ELECCIONES_CREAR));
        $response->assertViewIs('crud.elecciones.crear');
        $response->assertOk();
    }

    public function test_crear_una_eleccion(){
        $user = $this->usuarioConPermiso();

        $this->actingAs($user);
        $fechaInicio = new Carbon(fake()->dateTimeThisDecade());
        $fechaFin = $fechaInicio->addMonth();
        $datos = [
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'fecha_inicio' => $fechaInicio,
            'fecha_cierre' => $fechaFin,
            'descripcion' => fake()->sentence(10)
        ];

        $response = $this->post(route(self::RUTA_CRUD_ELECCIONES_CREAR), $datos);
        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'titulo',
                'fecha_inicio',
                'fecha_cierre',
                'descripcion',
                'estado',
            ],
        ]);
    }

    public function test_rechazar_crear_una_eleccion_cuando_ya_hay_una_en_curso(){
        $user = $this->usuarioConPermiso();

        $this->actingAs($user);

        $estadoElecciones = $this->crearEstadoEleccion();
        $inicioEleccion = Carbon::now();
        $eleccion = new Elecciones([
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'fecha_inicio' => $inicioEleccion->clone()->subMonth(),
            'fecha_cierre' => $inicioEleccion->clone()->addMonths(3),
            'descripcion' => fake()->sentence(10),
            'estado' => $estadoElecciones->getKey(),
        ]);

        $eleccion->save();

        $datos = [
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'fecha_inicio' => $inicioEleccion->clone()->subMonth(),
            'fecha_cierre' => $inicioEleccion->clone()->addMonths(3),
            'descripcion' => fake()->sentence(10)
        ];

        $response = $this->post(route(self::RUTA_CRUD_ELECCIONES_CREAR), $datos);
        $response->assertConflict();
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
    }

    public function test_cargar_vista_modificar_datos_de_una_eleccion()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $estadoElecciones = $this->crearEstadoEleccion();
        $inicioEleccion = new Carbon(fake()->date());
        $eleccion = new Elecciones([
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'fecha_inicio' => $inicioEleccion,
            'fecha_cierre' => $inicioEleccion->addMonth(),
            'descripcion' => fake()->sentence(10),
            'estado' => $estadoElecciones->getKey(),
        ]);

        $eleccion->save();

        $response = $this->get(route(self::RUTA_CRUD_ELECCIONES_EDITAR, [$eleccion->getKey()]));
        $response->assertViewIs('crud.elecciones.editar');
        $response->assertOk();
    }

    public function test_modificar_datos_de_una_eleccion()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $estadoElecciones = $this->crearEstadoEleccion();
        $inicioEleccion = new Carbon(fake()->date());
        $eleccion = new Elecciones([
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'fecha_inicio' => $inicioEleccion,
            'fecha_cierre' => $inicioEleccion->addMonth(),
            'descripcion' => fake()->sentence(10),
            'estado' => $estadoElecciones->getKey(),
        ]);

        $eleccion->save();
        
        $datos = [
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'fecha_inicio' => $inicioEleccion,
            'fecha_cierre' => $inicioEleccion->addMonth(),
            'descripcion' => fake()->sentence(10),
            'estado' => $estadoElecciones->getKey(),
        ];

        $response = $this->post(route(self::RUTA_CRUD_ELECCIONES_EDITAR, [$eleccion->getKey()]), $datos);
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'titulo',
                'fecha_inicio',
                'fecha_cierre',
                'descripcion',
                'estado',
            ],
        ]);
    }

    public function test_rechazar_modificar_datos_de_una_eleccion_cuando_las_fechas_se_sobreponen_a_otra_eleccion_anterior_o_posterior(){
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $estadoElecciones = $this->crearEstadoEleccion();
        $inicioEleccion = Carbon::now();
        $eleccion = new Elecciones([
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'fecha_inicio' => $inicioEleccion->clone()->subMonth(),
            'fecha_cierre' => $inicioEleccion->clone()->addMonths(3),
            'descripcion' => fake()->sentence(10),
            'estado' => $estadoElecciones->getKey(),
        ]);
        $eleccion->save();


        $eleccion2 = new Elecciones([
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'fecha_inicio' => $inicioEleccion->clone()->addMonths(5),
            'fecha_cierre' => $inicioEleccion->clone()->addMonths(8),
            'descripcion' => fake()->sentence(10),
            'estado' => $estadoElecciones->getKey(),
        ]);
        $eleccion2->save();

        $datos = [
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'fecha_inicio' => $inicioEleccion,
            'fecha_cierre' => $inicioEleccion->clone()->addMonths(5),
            'descripcion' => fake()->sentence(10),
            'estado' => 1,
        ];

        $response = $this->post(route(self::RUTA_CRUD_ELECCIONES_EDITAR, [$eleccion->getKey()]), $datos);
        $response->assertConflict();
        $response->assertJsonStructure([
            'success',
            'message'
        ]);
    }

    public function test_eliminar_una_eleccion()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $estadoElecciones = $this->crearEstadoEleccion();
        $inicioEleccion = new Carbon(fake()->date());
        $eleccion = new Elecciones([
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'fecha_inicio' => $inicioEleccion,
            'fecha_cierre' => $inicioEleccion->addMonth(),
            'descripcion' => fake()->sentence(10),
            'estado' => $estadoElecciones->getKey(),
        ]);

        $eleccion->save();

        $response = $this->delete(route(self::RUTA_CRUD_ELECCIONES_ELIMINAR, [$eleccion->getKey()]));
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'titulo',
                'fecha_inicio',
                'fecha_cierre',
                'descripcion',
                'estado',
            ],
        ]);
    }
}
