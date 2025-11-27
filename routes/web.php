<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\EleccionesController;
use App\Http\Controllers\CandidatoController;
use App\Http\Controllers\PartidoController;
use App\Http\Controllers\PadronElectoralController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VotoController;
use App\Http\Controllers\TipoVotoController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\EstadoEleccionesController;
use App\Http\Controllers\EstadoParticipanteController;
use App\Http\Controllers\ListaVotanteController;
use App\Http\Controllers\ParticipanteController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\PropuestaCandidatoController;
use App\Http\Controllers\PropuestaPartidoController;
use App\Http\Controllers\RolController;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard - Temporal sin autenticación para testing
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard'); // Esto carga el archivo 'resources/views/admin/dashboard.blade.php'
})->name('admin.dashboard');

// Rutas de autenticación
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    // Validar credenciales
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Intentar autenticar
    if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }

    // Si falla la autenticación
    return back()->withErrors([
        'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
    ])->onlyInput('email');
})->name('login');

// Area
Route::get('/areas', [AreaController::class, 'index'])->name('crud.area.ver');
Route::get('/areas/crear', [AreaController::class, 'create'])->name('crud.area.crear');
Route::post('/areas/crear', [AreaController::class, 'store'])->name('crud.area.crear');
Route::get('/areas/{id}/editar', [AreaController::class, 'edit'])->name('crud.area.editar');
Route::post('/areas/{id}/editar', [AreaController::class, 'update'])->name('crud.area.editar');
Route::delete('/areas/{id}', [AreaController::class, 'destroy'])->name('crud.area.eliminar');
Route::get('/areas/{id}', [AreaController::class, 'show'])->name('crud.area.ver_datos');

// Carrera
Route::get('/carreras', [CarreraController::class, 'index'])->name('crud.carrera.ver');
Route::get('/carreras/crear', [CarreraController::class, 'create'])->name('crud.carrera.crear');
Route::post('/carreras/crear', [CarreraController::class, 'store'])->name('crud.carrera.crear');
Route::get('/carreras/{id}/editar', [CarreraController::class, 'edit'])->name('crud.carrera.editar');
Route::post('/carreras/{id}/editar', [CarreraController::class, 'update'])->name('crud.carrera.editar');
Route::delete('/carreras/{id}', [CarreraController::class, 'destroy'])->name('crud.carrera.eliminar');
Route::get('/carreras/{id}', [CarreraController::class, 'show'])->name('crud.carrera.ver_datos');

// Elecciones
Route::get('/elecciones', [EleccionesController::class, 'index'])->name('crud.elecciones.ver');
Route::get('/elecciones/crear', [EleccionesController::class, 'create'])->name('crud.elecciones.crear');
Route::post('/elecciones/crear', [EleccionesController::class, 'store'])->name('crud.elecciones.crear');
Route::get('/elecciones/{id}/editar', [EleccionesController::class, 'edit'])->name('crud.elecciones.editar');
Route::post('/elecciones/{id}/editar', [EleccionesController::class, 'update'])->name('crud.elecciones.editar');
Route::delete('/elecciones/{id}', [EleccionesController::class, 'destroy'])->name('crud.elecciones.eliminar');
Route::get('/elecciones/{id}', [EleccionesController::class, 'show'])->name('crud.elecciones.ver_datos');

// Candidato
Route::get('/candidatos', [CandidatoController::class, 'index'])->name('crud.candidato.ver');
Route::get('/candidatos/crear', [CandidatoController::class, 'create'])->name('crud.candidato.crear');
Route::post('/candidatos/crear', [CandidatoController::class, 'store'])->name('crud.candidato.crear');
Route::get('/candidatos/{id}/editar', [CandidatoController::class, 'edit'])->name('crud.candidato.editar');
Route::post('/candidatos/{id}/editar', [CandidatoController::class, 'update'])->name('crud.candidato.editar');
Route::delete('/candidatos/{id}', [CandidatoController::class, 'destroy'])->name('crud.candidato.eliminar');
Route::get('/candidatos/{id}', [CandidatoController::class, 'show'])->name('crud.candidato.ver_datos');

// Partido
Route::get('/partidos', [PartidoController::class, 'index'])->name('crud.partido.ver');
Route::get('/partidos/crear', [PartidoController::class, 'create'])->name('crud.partido.crear');
Route::post('/partidos/crear', [PartidoController::class, 'store'])->name('crud.partido.crear');
Route::get('/partidos/{id}/editar', [PartidoController::class, 'edit'])->name('crud.partido.editar');
Route::post('/partidos/{id}/editar', [PartidoController::class, 'update'])->name('crud.partido.editar');
Route::delete('/partidos/{id}', [PartidoController::class, 'destroy'])->name('crud.partido.eliminar');
Route::get('/partidos/{id}', [PartidoController::class, 'show'])->name('crud.partido.ver_datos');

// Padrón Electoral
Route::get('/padron-electoral', [PadronElectoralController::class, 'index'])->name('crud.padron_electoral.ver');
Route::get('/padron-electoral/crear', [PadronElectoralController::class, 'create'])->name('crud.padron_electoral.crear');
Route::post('/padron-electoral/crear', [PadronElectoralController::class, 'store'])->name('crud.padron_electoral.crear');
Route::get('/padron-electoral/{id}/editar', [PadronElectoralController::class, 'edit'])->name('crud.padron_electoral.editar');
Route::post('/padron-electoral/{id}/editar', [PadronElectoralController::class, 'update'])->name('crud.padron_electoral.editar');
Route::delete('/padron-electoral/{id}', [PadronElectoralController::class, 'destroy'])->name('crud.padron_electoral.eliminar');
Route::get('/padron-electoral/{id}', [PadronElectoralController::class, 'show'])->name('crud.padron_electoral.ver_datos');
// Importación de padrón
Route::get('/padron/import', [PadronElectoralController::class, 'importForm'])->name('crud.padron_electoral.importar');
Route::post('/padron/import', [PadronElectoralController::class, 'import'])->name('crud.padron_electoral.importar');

// User
Route::get('/users', [UserController::class, 'index'])->name('crud.user.ver');
Route::get('/users/crear', [UserController::class, 'create'])->name('crud.user.crear');
Route::post('/users/crear', [UserController::class, 'store'])->name('crud.user.crear');
Route::get('/users/{id}/editar', [UserController::class, 'edit'])->name('crud.user.editar');
Route::post('/users/{id}/editar', [UserController::class, 'update'])->name('crud.user.editar');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('crud.user.eliminar');
Route::get('/users/{id}', [UserController::class, 'show'])->name('crud.user.ver_datos');

// Voto
Route::get('/votos', [VotoController::class, 'index'])->name('crud.voto.ver');
Route::get('/votos/crear', [VotoController::class, 'create'])->name('crud.voto.crear');
Route::post('/votos/crear', [VotoController::class, 'store'])->name('crud.voto.crear');
Route::get('/votos/{id}/editar', [VotoController::class, 'edit'])->name('crud.voto.editar');
Route::post('/votos/{id}/editar', [VotoController::class, 'update'])->name('crud.voto.editar');
Route::delete('/votos/{id}', [VotoController::class, 'destroy'])->name('crud.voto.eliminar');
Route::get('/votos/{id}', [VotoController::class, 'show'])->name('crud.voto.ver_datos');

// Tipo Voto
Route::get('/tipo-voto', [TipoVotoController::class, 'index'])->name('crud.tipo_voto.ver');
Route::get('/tipo-voto/crear', [TipoVotoController::class, 'create'])->name('crud.tipo_voto.crear');
Route::post('/tipo-voto/crear', [TipoVotoController::class, 'store'])->name('crud.tipo_voto.crear');
Route::get('/tipo-voto/{id}/editar', [TipoVotoController::class, 'edit'])->name('crud.tipo_voto.editar');
Route::post('/tipo-voto/{id}/editar', [TipoVotoController::class, 'update'])->name('crud.tipo_voto.editar');
Route::delete('/tipo-voto/{id}', [TipoVotoController::class, 'destroy'])->name('crud.tipo_voto.eliminar');
Route::get('/tipo-voto/{id}', [TipoVotoController::class, 'show'])->name('crud.tipo_voto.ver_datos');

// Cargo
Route::get('/cargos', [CargoController::class, 'index'])->name('crud.cargo.ver');
Route::get('/cargos/crear', [CargoController::class, 'create'])->name('crud.cargo.crear');
Route::post('/cargos/crear', [CargoController::class, 'store'])->name('crud.cargo.crear');
Route::get('/cargos/{id}/editar', [CargoController::class, 'edit'])->name('crud.cargo.editar');
Route::post('/cargos/{id}/editar', [CargoController::class, 'update'])->name('crud.cargo.editar');
Route::delete('/cargos/{id}', [CargoController::class, 'destroy'])->name('crud.cargo.eliminar');
Route::get('/cargos/{id}', [CargoController::class, 'show'])->name('crud.cargo.ver_datos');

// Estado Elecciones
Route::get('/estado-elecciones', [EstadoEleccionesController::class, 'index'])->name('crud.estado_elecciones.ver');
Route::get('/estado-elecciones/crear', [EstadoEleccionesController::class, 'create'])->name('crud.estado_elecciones.crear');
Route::post('/estado-elecciones/crear', [EstadoEleccionesController::class, 'store'])->name('crud.estado_elecciones.crear');
Route::get('/estado-elecciones/{id}/editar', [EstadoEleccionesController::class, 'edit'])->name('crud.estado_elecciones.editar');
Route::post('/estado-elecciones/{id}/editar', [EstadoEleccionesController::class, 'update'])->name('crud.estado_elecciones.editar');
Route::delete('/estado-elecciones/{id}', [EstadoEleccionesController::class, 'destroy'])->name('crud.estado_elecciones.eliminar');
Route::get('/estado-elecciones/{id}', [EstadoEleccionesController::class, 'show'])->name('crud.estado_elecciones.ver_datos');

// Estado Participante
Route::get('/estado-participante', [EstadoParticipanteController::class, 'index'])->name('crud.estado_participante.ver');
Route::get('/estado-participante/crear', [EstadoParticipanteController::class, 'create'])->name('crud.estado_participante.crear');
Route::post('/estado-participante/crear', [EstadoParticipanteController::class, 'store'])->name('crud.estado_participante.crear');
Route::get('/estado-participante/{id}/editar', [EstadoParticipanteController::class, 'edit'])->name('crud.estado_participante.editar');
Route::post('/estado-participante/{id}/editar', [EstadoParticipanteController::class, 'update'])->name('crud.estado_participante.editar');
Route::delete('/estado-participante/{id}', [EstadoParticipanteController::class, 'destroy'])->name('crud.estado_participante.eliminar');
Route::get('/estado-participante/{id}', [EstadoParticipanteController::class, 'show'])->name('crud.estado_participante.ver_datos');

// Lista Votante
Route::get('/lista-votante', [ListaVotanteController::class, 'index'])->name('crud.lista_votante.ver');
Route::get('/lista-votante/crear', [ListaVotanteController::class, 'create'])->name('crud.lista_votante.crear');
Route::post('/lista-votante/crear', [ListaVotanteController::class, 'store'])->name('crud.lista_votante.crear');
Route::get('/lista-votante/{id}/editar', [ListaVotanteController::class, 'edit'])->name('crud.lista_votante.editar');
Route::post('/lista-votante/{id}/editar', [ListaVotanteController::class, 'update'])->name('crud.lista_votante.editar');
Route::delete('/lista-votante/{id}', [ListaVotanteController::class, 'destroy'])->name('crud.lista_votante.eliminar');
Route::get('/lista-votante/{id}', [ListaVotanteController::class, 'show'])->name('crud.lista_votante.ver_datos');

// Participante
Route::get('/participantes', [ParticipanteController::class, 'index'])->name('crud.participante.ver');
Route::get('/participantes/crear', [ParticipanteController::class, 'create'])->name('crud.participante.crear');
Route::post('/participantes/crear', [ParticipanteController::class, 'store'])->name('crud.participante.crear');
Route::get('/participantes/{id}/editar', [ParticipanteController::class, 'edit'])->name('crud.participante.editar');
Route::post('/participantes/{id}/editar', [ParticipanteController::class, 'update'])->name('crud.participante.editar');
Route::delete('/participantes/{id}', [ParticipanteController::class, 'destroy'])->name('crud.participante.eliminar');
Route::get('/participantes/{id}', [ParticipanteController::class, 'show'])->name('crud.participante.ver_datos');

// Permiso
Route::get('/permisos', [PermisoController::class, 'index'])->name('crud.permiso.ver');
Route::get('/permisos/crear', [PermisoController::class, 'create'])->name('crud.permiso.crear');
Route::post('/permisos/crear', [PermisoController::class, 'store'])->name('crud.permiso.crear');
Route::get('/permisos/{id}/editar', [PermisoController::class, 'edit'])->name('crud.permiso.editar');
Route::post('/permisos/{id}/editar', [PermisoController::class, 'update'])->name('crud.permiso.editar');
Route::delete('/permisos/{id}', [PermisoController::class, 'destroy'])->name('crud.permiso.eliminar');
Route::get('/permisos/{id}', [PermisoController::class, 'show'])->name('crud.permiso.ver_datos');

// Propuesta Candidato
Route::get('/propuesta-candidato', [PropuestaCandidatoController::class, 'index'])->name('crud.propuesta_candidato.ver');
Route::get('/propuesta-candidato/crear', [PropuestaCandidatoController::class, 'create'])->name('crud.propuesta_candidato.crear');
Route::post('/propuesta-candidato/crear', [PropuestaCandidatoController::class, 'store'])->name('crud.propuesta_candidato.crear');
Route::get('/propuesta-candidato/{id}/editar', [PropuestaCandidatoController::class, 'edit'])->name('crud.propuesta_candidato.editar');
Route::post('/propuesta-candidato/{id}/editar', [PropuestaCandidatoController::class, 'update'])->name('crud.propuesta_candidato.editar');
Route::delete('/propuesta-candidato/{id}', [PropuestaCandidatoController::class, 'destroy'])->name('crud.propuesta_candidato.eliminar');
Route::get('/propuesta-candidato/{id}', [PropuestaCandidatoController::class, 'show'])->name('crud.propuesta_candidato.ver_datos');

// Propuesta Partido
Route::get('/propuesta-partido', [PropuestaPartidoController::class, 'index'])->name('crud.propuesta_partido.ver');
Route::get('/propuesta-partido/crear', [PropuestaPartidoController::class, 'create'])->name('crud.propuesta_partido.crear');
Route::post('/propuesta-partido/crear', [PropuestaPartidoController::class, 'store'])->name('crud.propuesta_partido.crear');
Route::get('/propuesta-partido/{id}/editar', [PropuestaPartidoController::class, 'edit'])->name('crud.propuesta_partido.editar');
Route::post('/propuesta-partido/{id}/editar', [PropuestaPartidoController::class, 'update'])->name('crud.propuesta_partido.editar');
Route::delete('/propuesta-partido/{id}', [PropuestaPartidoController::class, 'destroy'])->name('crud.propuesta_partido.eliminar');
Route::get('/propuesta-partido/{id}', [PropuestaPartidoController::class, 'show'])->name('crud.propuesta_partido.ver_datos');

// Rol
Route::get('/roles', [RolController::class, 'index'])->name('crud.rol.ver');
Route::get('/roles/crear', [RolController::class, 'create'])->name('crud.rol.crear');
Route::post('/roles/crear', [RolController::class, 'store'])->name('crud.rol.crear');
Route::get('/roles/{id}/editar', [RolController::class, 'edit'])->name('crud.rol.editar');
Route::post('/roles/{id}/editar', [RolController::class, 'update'])->name('crud.rol.editar');
Route::delete('/roles/{id}', [RolController::class, 'destroy'])->name('crud.rol.eliminar');
Route::get('/roles/{id}', [RolController::class, 'show'])->name('crud.rol.ver_datos');
Route::post('/roles/{id}/agregar-permiso', [RolController::class, 'agregarPermiso'])->name('crud.rol.agregar_permiso');
