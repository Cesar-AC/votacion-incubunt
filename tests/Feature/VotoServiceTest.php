<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Candidato;
use App\Models\Cargo;
use App\Models\Elecciones;
use App\Models\EstadoElecciones;
use App\Models\PadronElectoral;
use App\Models\Partido;
use App\Models\Permiso;
use App\Models\User;
use App\Models\Voto;
use App\Services\EleccionesService;
use App\Services\PermisoService;
use App\Services\VotoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VotoServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $seed = true;

    private function crearEstadoEleccion(): EstadoElecciones
    {
        $estadoElecciones = new EstadoElecciones([
            'estado' => fake()->words(2, true),
        ]);
        $estadoElecciones->save();

        return $estadoElecciones;
    }

    private function crearEleccion(?EstadoElecciones $estado = null): Elecciones
    {
        $estado = $estado ?? $this->crearEstadoEleccion();

        $eleccion = Elecciones::create([
            'titulo' => 'Elecciones ' . fake()->words(2, true),
            'fechaInicio' => now(),
            'fechaCierre' => now()->addMonth(),
            'descripcion' => fake()->sentence(10),
            'idEstado' => $estado->getKey(),
        ]);

        return $eleccion;
    }

    private function crearUsuario(): User
    {
        return User::factory()->create([
            'correo' => fake()->email(),
            'contraseña' => bcrypt(fake()->password()),
            'idEstadoUsuario' => 1,
        ]);
    }

    private function crearArea(): Area
    {
        $area = new Area([
            'area' => fake()->words(2, true),
        ]);
        $area->save();

        return $area;
    }

    private function crearCargo(?Area $area = null): Cargo
    {
        $area = $area ?? $this->crearArea();

        $cargo = new Cargo([
            'cargo' => fake()->words(2, true),
            'idArea' => $area->getKey(),
        ]);
        $cargo->save();

        return $cargo;
    }

    private function crearPartido(): Partido
    {
        $partido = new Partido([
            'partido' => fake()->words(2, true),
            'urlPartido' => fake()->url(),
            'descripcion' => fake()->sentence(10),
        ]);
        $partido->save();

        return $partido;
    }

    private function crearCandidato(Elecciones $eleccion, ?Cargo $cargo = null, ?Partido $partido = null, ?User $usuario = null): Candidato
    {
        $cargo = $cargo ?? $this->crearCargo();
        $partido = $partido ?? $this->crearPartido();
        $usuario = $usuario ?? $this->crearUsuario();

        $candidato = new Candidato([
            'idElecciones' => $eleccion->getKey(),
            'idCargo' => $cargo->getKey(),
            'idPartido' => $partido->getKey(),
            'idUsuario' => $usuario->getKey(),
        ]);
        $candidato->save();

        // Asociar candidato a la elección
        $eleccion->candidatos()->attach($candidato);

        return $candidato;
    }

    private function crearPermisoVotar(): Permiso
    {
        $permiso = Permiso::where('permiso', '=', \App\Enum\Permiso::VOTO_VOTAR->value)->first();

        if (!$permiso) {
            $permiso = new Permiso([
                'permiso' => \App\Enum\Permiso::VOTO_VOTAR->value,
            ]);
            $permiso->save();
        }

        return $permiso;
    }

    private function agregarUsuarioAPadron(User $usuario, Elecciones $eleccion): void
    {
        PadronElectoral::create([
            'idUsuario' => $usuario->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'fechaVoto' => null,
        ]);
    }

    private function crearVotoService(Elecciones $eleccion): VotoService
    {
        $eleccionesService = new EleccionesService($eleccion);
        $permisoService = new PermisoService();

        return new VotoService($eleccionesService, $permisoService);
    }

    public function test_votar_exitosamente(): void
    {
        $eleccion = $this->crearEleccion();
        $usuario = $this->crearUsuario();
        $candidato = $this->crearCandidato($eleccion);
        $permisoVotar = $this->crearPermisoVotar();

        // Agregar usuario al padrón y darle permiso
        $this->agregarUsuarioAPadron($usuario, $eleccion);
        $usuario->permisos()->attach($permisoVotar->getKey());

        $service = $this->crearVotoService($eleccion);
        $service->votar($usuario, $candidato);

        // Verificar que se registró el voto
        $this->assertDatabaseHas('Voto', [
            'idCandidato' => $candidato->getKey(),
            'idElecciones' => $eleccion->getKey(),
        ]);

        // Verificar que se actualizó la fecha de voto en el padrón
        $this->assertDatabaseMissing('PadronElectoral', [
            'idUsuario' => $usuario->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'fechaVoto' => null,
        ]);
    }

    public function test_rechazar_voto_sin_permiso(): void
    {
        $eleccion = $this->crearEleccion();
        $usuario = $this->crearUsuario();
        $candidato = $this->crearCandidato($eleccion);
        $this->crearPermisoVotar();

        // Agregar usuario al padrón pero NO darle permiso
        $this->agregarUsuarioAPadron($usuario, $eleccion);

        $service = $this->crearVotoService($eleccion);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No tienes permiso para votar.');

        $service->votar($usuario, $candidato);
    }

    public function test_rechazar_voto_usuario_no_en_padron(): void
    {
        $eleccion = $this->crearEleccion();
        $usuario = $this->crearUsuario();
        $candidato = $this->crearCandidato($eleccion);
        $permisoVotar = $this->crearPermisoVotar();

        // Dar permiso pero NO agregar al padrón
        $usuario->permisos()->attach($permisoVotar->getKey());

        $service = $this->crearVotoService($eleccion);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No estás registrado en el padrón electoral.');

        $service->votar($usuario, $candidato);
    }

    public function test_rechazar_voto_candidato_no_pertenece_a_eleccion(): void
    {
        $eleccion = $this->crearEleccion();
        $otraEleccion = $this->crearEleccion();
        $usuario = $this->crearUsuario();
        $permisoVotar = $this->crearPermisoVotar();

        // Crear candidato en otra elección
        $candidato = $this->crearCandidato($otraEleccion);

        // Agregar usuario al padrón de la primera elección y darle permiso
        $this->agregarUsuarioAPadron($usuario, $eleccion);
        $usuario->permisos()->attach($permisoVotar->getKey());

        $service = $this->crearVotoService($eleccion);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('El candidato a votar no pertenece a la elección.');

        $service->votar($usuario, $candidato);
    }

    public function test_rechazar_voto_duplicado(): void
    {
        $eleccion = $this->crearEleccion();
        $usuario = $this->crearUsuario();
        $candidato = $this->crearCandidato($eleccion);
        $permisoVotar = $this->crearPermisoVotar();

        // Agregar usuario al padrón con fecha de voto ya registrada
        PadronElectoral::create([
            'idUsuario' => $usuario->getKey(),
            'idElecciones' => $eleccion->getKey(),
            'fechaVoto' => now(), // Ya votó
        ]);

        $usuario->permisos()->attach($permisoVotar->getKey());

        $service = $this->crearVotoService($eleccion);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Ya has votado en esta elección.');

        $service->votar($usuario, $candidato);
    }

    public function test_usuario_puede_votar_en_diferentes_elecciones(): void
    {
        $eleccion1 = $this->crearEleccion();
        $eleccion2 = $this->crearEleccion();
        $usuario = $this->crearUsuario();
        $permisoVotar = $this->crearPermisoVotar();

        $candidato1 = $this->crearCandidato($eleccion1);
        $candidato2 = $this->crearCandidato($eleccion2);

        // Agregar usuario a ambos padrones y darle permiso
        $this->agregarUsuarioAPadron($usuario, $eleccion1);
        $this->agregarUsuarioAPadron($usuario, $eleccion2);
        $usuario->permisos()->attach($permisoVotar->getKey());

        // Votar en la primera elección
        $service1 = $this->crearVotoService($eleccion1);
        $service1->votar($usuario, $candidato1);

        // Votar en la segunda elección
        $service2 = $this->crearVotoService($eleccion2);
        $service2->votar($usuario, $candidato2);

        // Verificar que se registraron ambos votos
        $this->assertDatabaseHas('Voto', [
            'idCandidato' => $candidato1->getKey(),
            'idElecciones' => $eleccion1->getKey(),
        ]);

        $this->assertDatabaseHas('Voto', [
            'idCandidato' => $candidato2->getKey(),
            'idElecciones' => $eleccion2->getKey(),
        ]);
    }

    public function test_conteo_de_votos_despues_de_votar(): void
    {
        $eleccion = $this->crearEleccion();
        $candidato = $this->crearCandidato($eleccion);
        $permisoVotar = $this->crearPermisoVotar();

        // Crear varios usuarios y hacerlos votar
        for ($i = 0; $i < 5; $i++) {
            $usuario = $this->crearUsuario();
            $this->agregarUsuarioAPadron($usuario, $eleccion);
            $usuario->permisos()->attach($permisoVotar->getKey());

            $service = $this->crearVotoService($eleccion);
            $service->votar($usuario, $candidato);
        }

        // Verificar conteo de votos
        $votosCount = Voto::where('idCandidato', '=', $candidato->getKey())
            ->where('idElecciones', '=', $eleccion->getKey())
            ->count();

        $this->assertEquals(5, $votosCount);
    }
}
