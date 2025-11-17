<?php

namespace Tests\Feature;

use App\Models\Permiso;
use App\Models\User;
use App\Models\EstadoElecciones;
use App\Models\Elecciones;
use App\Models\TipoVoto;
use App\Models\ListaVotante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListaVotanteControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private const PERMISO_GLOBAL_CRUD = 'gestion.lista_votante.*';
    private const RUTA_VER = 'crud.lista_votante.ver';
    private const RUTA_VER_DATOS = 'crud.lista_votante.ver_datos';
    private const RUTA_CREAR = 'crud.lista_votante.crear';
    private const RUTA_EDITAR = 'crud.lista_votante.editar';
    private const RUTA_ELIMINAR = 'crud.lista_votante.eliminar';

    private function usuarioConPermiso(string $permiso = self::PERMISO_GLOBAL_CRUD){
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

    private function crearEleccion(): Elecciones
    {
        $estado = new EstadoElecciones(['estado' => fake()->words(2, true)]);
        $estado->save();
        $inicio = now();
        $e = new Elecciones([
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'descripcion' => fake()->sentence(8),
            'fecha_inicio' => $inicio,
            'fecha_cierre' => (clone $inicio)->addMonth(),
            'estado' => $estado->getKey(),
        ]);
        $e->save();
        return $e;
    }

    private function crearTipoVoto(): TipoVoto
    {
        $tipo = new TipoVoto([
            'idTipoVoto' => fake()->numberBetween(1, 9999),
            'tipoVoto' => 'digital',
        ]);
        $tipo->save();
        return $tipo;
    }

    public function test_autorizacion_negativa_listado_devuelve_404()
    {
        $user = User::factory()->create([
            'usuario' => fake()->userName(),
            'email' => fake()->email(),
            'password' => bcrypt(fake()->password()),
        ]);

        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_VER));
        $response->assertNotFound();
    }

    public function test_cargar_vista_listado_lista_votante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_VER));
        $response->assertViewIs('crud.lista_votante.ver');
        $response->assertOk();
    }

    public function test_cargar_vista_crear_lista_votante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);
        $response = $this->get(route(self::RUTA_CREAR));
        $response->assertViewIs('crud.lista_votante.crear');
        $response->assertOk();
    }

    public function test_crear_lista_votante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $eleccion = $this->crearEleccion();
        $tipo = $this->crearTipoVoto();

        $datos = [
            'idUser' => $user->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'fechaVoto' => now(),
            'idTipoVoto' => $tipo->getKey(),
        ];

        $response = $this->post(route(self::RUTA_CREAR), $datos);
        $response->assertCreated();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'idUser',
                'idElecciones',
                'fechaVoto',
                'idTipoVoto',
            ],
        ]);
    }

    public function test_ver_datos_lista_votante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $eleccion = $this->crearEleccion();
        $tipo = $this->crearTipoVoto();
        $lv = new ListaVotante([
            'idUser' => $user->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'fechaVoto' => now(),
            'idTipoVoto' => $tipo->getKey(),
        ]);
        $lv->save();

        $response = $this->get(route(self::RUTA_VER_DATOS, [$lv->getKey()]));
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'idUser',
                'idElecciones',
                'fechaVoto',
                'idTipoVoto',
            ],
        ]);
    }

    public function test_cargar_vista_editar_lista_votante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $eleccion = $this->crearEleccion();
        $tipo = $this->crearTipoVoto();
        $lv = new ListaVotante([
            'idUser' => $user->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'fechaVoto' => now(),
            'idTipoVoto' => $tipo->getKey(),
        ]);
        $lv->save();

        $response = $this->get(route(self::RUTA_EDITAR, [$lv->getKey()]));
        $response->assertViewIs('crud.lista_votante.editar');
        $response->assertOk();
    }

    public function test_actualizar_lista_votante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $eleccion = $this->crearEleccion();
        $tipo = $this->crearTipoVoto();
        $lv = new ListaVotante([
            'idUser' => $user->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'fechaVoto' => now(),
            'idTipoVoto' => $tipo->getKey(),
        ]);
        $lv->save();

        $datos = [
            'fechaVoto' => now(),
            'idTipoVoto' => $tipo->getKey(),
        ];

        $response = $this->post(route(self::RUTA_EDITAR, [$lv->getKey()]), $datos);
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'idUser',
                'idElecciones',
                'fechaVoto',
                'idTipoVoto',
            ],
        ]);
    }

    public function test_eliminar_lista_votante()
    {
        $user = $this->usuarioConPermiso();
        $this->actingAs($user);

        $eleccion = $this->crearEleccion();
        $tipo = $this->crearTipoVoto();
        $lv = new ListaVotante([
            'idUser' => $user->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'fechaVoto' => now(),
            'idTipoVoto' => $tipo->getKey(),
        ]);
        $lv->save();

        $response = $this->delete(route(self::RUTA_ELIMINAR, [$lv->getKey()]));
        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'idUser',
                'idElecciones',
                'fechaVoto',
                'idTipoVoto',
            ],
        ]);
    }
}
