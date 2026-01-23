<?php

use App\Http\Controllers\AutenticacionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KPIController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\EleccionesController;
use App\Http\Controllers\CandidatoController;
use App\Http\Controllers\PartidoController;
use App\Http\Controllers\PadronElectoralController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VotoController;
use App\Http\Controllers\CargoController;
use App\Http\Controllers\EstadoEleccionesController;
use App\Http\Controllers\ListaVotanteController;
use App\Http\Controllers\PermisoController;

use App\Http\Controllers\PropuestaCandidatoController;
use App\Http\Controllers\PropuestaPartidoController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\VotanteController;

Route::get('/login', [AutenticacionController::class, 'verInicioSesion'])->name('vistaLogin');

Route::middleware(['throttle:login'])->group(function () {
    Route::post('/login', [AutenticacionController::class, 'iniciarSesion'])->name('login');
});

Route::middleware(['auth', 'throttle:login'])->group(function () {
    Route::post('/logout', [AutenticacionController::class, 'cerrarSesion'])->name('logout');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas del Votante

Route::middleware(['auth'])->group(function () {
    Route::prefix('votante')->name('votante.')->group(function () {
        Route::get('/home', [VotanteController::class, 'home'])->name('home');
        Route::get('/propuestas', [VotanteController::class, 'propuestas'])->name('propuestas');
        Route::get('/elecciones', [VotanteController::class, 'listarElecciones'])->name('elecciones');
        Route::get('/elecciones/{id}', [VotanteController::class, 'verDetalleEleccion'])->name('elecciones.detalle');

        Route::prefix('votar')->name('votar.')->group(function () {
            Route::get('/{eleccionId}/candidatos', [VotanteController::class, 'listarCandidatos'])->name('lista');
            Route::get('/{eleccionId}/candidato/{candidatoId}', [VotanteController::class, 'verDetalleCandidato'])->name('detalle_candidato');
            Route::post('/{eleccionId}/emitir', [VotanteController::class, 'emitirVoto'])->name('emitir');
        });
    });
});
// Area
Route::middleware(['auth'])->group(function () {
    Route::get('/areas', [AreaController::class, 'index'])->name('crud.area.ver')->middleware('can:viewAny,App\Models\Area');
    Route::get('/areas/crear', [AreaController::class, 'create'])->name('crud.area.crear')->middleware('can:create,App\Models\Area');
    Route::post('/areas/crear', [AreaController::class, 'store'])->name('crud.area.crear')->middleware('can:create,App\Models\Area');
    Route::get('/areas/{id}/editar', [AreaController::class, 'edit'])->name('crud.area.editar')->middleware('can:update,App\Models\Area');
    Route::post('/areas/{id}/editar', [AreaController::class, 'update'])->name('crud.area.editar')->middleware('can:update,App\Models\Area');
    Route::delete('/areas/{id}', [AreaController::class, 'destroy'])->name('crud.area.eliminar')->middleware('can:delete,App\Models\Area');
    Route::get('/areas/{id}', [AreaController::class, 'show'])->name('crud.area.ver_datos')->middleware('can:view,App\Models\Area');
});

// Carrera
Route::middleware(['auth'])->group(function () {
    Route::get('/carreras', [CarreraController::class, 'index'])->name('crud.carrera.ver')->middleware('can:viewAny,App\Models\Carrera');
    Route::get('/carreras/crear', [CarreraController::class, 'create'])->name('crud.carrera.crear')->middleware('can:create,App\Models\Carrera');
    Route::post('/carreras/crear', [CarreraController::class, 'store'])->name('crud.carrera.crear')->middleware('can:create,App\Models\Carrera');
    Route::get('/carreras/{id}/editar', [CarreraController::class, 'edit'])->name('crud.carrera.editar')->middleware('can:update,App\Models\Carrera');
    Route::post('/carreras/{id}/editar', [CarreraController::class, 'update'])->name('crud.carrera.editar')->middleware('can:update,App\Models\Carrera');
    Route::delete('/carreras/{id}', [CarreraController::class, 'destroy'])->name('crud.carrera.eliminar')->middleware('can:delete,App\Models\Carrera');
    Route::get('/carreras/{id}', [CarreraController::class, 'show'])->name('crud.carrera.ver_datos')->middleware('can:view,App\Models\Carrera');
});

// Elecciones
Route::middleware(['auth'])->group(function () {
    Route::get('/elecciones', [EleccionesController::class, 'index'])->name('crud.elecciones.ver')->middleware('can:viewAny,App\Models\Elecciones');
    Route::get('/elecciones/crear', [EleccionesController::class, 'create'])->name('crud.elecciones.crear')->middleware('can:create,App\Models\Elecciones');
    Route::post('/elecciones/crear', [EleccionesController::class, 'store'])->name('crud.elecciones.crear')->middleware('can:create,App\Models\Elecciones');
    Route::get('/elecciones/{id}/editar', [EleccionesController::class, 'edit'])->name('crud.elecciones.editar')->middleware('can:update,App\Models\Elecciones');
    Route::put('/elecciones/{id}/editar', [EleccionesController::class, 'update'])->name('crud.elecciones.editar')->middleware('can:update,App\Models\Elecciones');
    Route::delete('/elecciones/{id}', [EleccionesController::class, 'destroy'])->name('crud.elecciones.eliminar')->middleware('can:delete,App\Models\Elecciones');
    Route::get('/elecciones/{id}', [EleccionesController::class, 'show'])->name('crud.elecciones.ver_datos')->middleware('can:view,App\Models\Elecciones');
});

// Candidato
Route::middleware(['auth'])->group(function () {
    Route::get('/candidatos', [CandidatoController::class, 'index'])->name('crud.candidato.ver')->middleware('can:viewAny,App\Models\Candidato');
    Route::get('/candidatos/crear', [CandidatoController::class, 'create'])->name('crud.candidato.crear')->middleware('can:create,App\Models\Candidato');
    Route::post('/candidatos/crear', [CandidatoController::class, 'store'])->name('crud.candidato.crear')->middleware('can:create,App\Models\Candidato');
    Route::get('/candidatos/{id}/editar', [CandidatoController::class, 'edit'])->name('crud.candidato.editar')->middleware('can:update,App\Models\Candidato');
    Route::put('/candidatos/{id}/editar', [CandidatoController::class, 'update'])->name('crud.candidato.editar')->middleware('can:update,App\Models\Candidato');
    Route::delete('/candidatos/{id}', [CandidatoController::class, 'destroy'])->name('crud.candidato.eliminar')->middleware('can:delete,App\Models\Candidato');
    Route::get('/candidatos/{id}', [CandidatoController::class, 'show'])->name('crud.candidato.ver_datos')->middleware('can:view,App\Models\Candidato');
});

// Partido
Route::middleware(['auth'])->group(function () {
    Route::get('/partidos', [PartidoController::class, 'index'])->name('crud.partido.ver')->middleware('can:viewAny,App\Models\Partido');
    Route::get('/partidos/crear', [PartidoController::class, 'create'])->name('crud.partido.crear')->middleware('can:create,App\Models\Partido');
    Route::post('/partidos/crear', [PartidoController::class, 'store'])->name('crud.partido.crear')->middleware('can:create,App\Models\Partido');
    Route::get('/partidos/{id}/editar', [PartidoController::class, 'edit'])->name('crud.partido.editar')->middleware('can:update,App\Models\Partido');
    Route::put('/partidos/{id}/editar', [PartidoController::class, 'update'])->name('crud.partido.editar')->middleware('can:update,App\Models\Partido');
    Route::delete('/partidos/{id}', [PartidoController::class, 'destroy'])->name('crud.partido.eliminar')->middleware('can:delete,App\Models\Partido');
    Route::get('/partidos/{id}', [PartidoController::class, 'show'])->name('crud.partido.ver_datos')->middleware('can:view,App\Models\Partido');
});

// Padrón Electoral
Route::middleware(['auth'])->group(function () {
    Route::get('/padron-electoral', [PadronElectoralController::class, 'index'])->name('crud.padron_electoral.ver')->middleware('can:viewAny,App\Models\PadronElectoral');
    Route::get('/padron-electoral/crear', [PadronElectoralController::class, 'create'])->name('crud.padron_electoral.crear')->middleware('can:create,App\Models\PadronElectoral');
    Route::post('/padron-electoral/crear', [PadronElectoralController::class, 'store'])->name('crud.padron_electoral.crear')->middleware('can:create,App\Models\PadronElectoral');
    Route::get('/padron-electoral/{id}/editar', [PadronElectoralController::class, 'edit'])->name('crud.padron_electoral.editar')->middleware('can:update,App\Models\PadronElectoral');
    Route::put('/padron-electoral/{id}/editar', [PadronElectoralController::class, 'update'])->name('crud.padron_electoral.editar')->middleware('can:update,App\Models\PadronElectoral');
    Route::delete('/padron-electoral/{id}', [PadronElectoralController::class, 'destroy'])->name('crud.padron_electoral.eliminar')->middleware('can:delete,App\Models\PadronElectoral');
    Route::get('/padron-electoral/{id}', [PadronElectoralController::class, 'show'])->name('crud.padron_electoral.ver_datos')->middleware('can:view,App\Models\PadronElectoral');
    // Importación de padrón
    Route::get('/padron/import', [PadronElectoralController::class, 'importForm'])->name('crud.padron_electoral.importar')->middleware('can:create,App\Models\PadronElectoral');
    Route::post('/padron/import', [PadronElectoralController::class, 'import'])->name('crud.padron_electoral.importar')->middleware('can:create,App\Models\PadronElectoral');
    Route::post('/padron/import-file', [PadronElectoralController::class, 'importFile'])->name('crud.padron_electoral.importar_archivo')->middleware('can:create,App\Models\PadronElectoral');
});

// User
Route::middleware(['auth'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('crud.user.ver')->middleware('can:viewAny,App\Models\User');
    Route::get('/users/crear', [UserController::class, 'create'])->name('crud.user.crear')->middleware('can:create,App\Models\User');
    Route::post('/users/crear', [UserController::class, 'store'])->name('crud.user.crear')->middleware('can:create,App\Models\User');
    Route::get('/users/{id}/editar', [UserController::class, 'edit'])->name('crud.user.editar')->middleware('can:update,App\Models\User');
    Route::put('/users/{id}/editar', [UserController::class, 'update'])->name('crud.user.editar')->middleware('can:update,App\Models\User');
    Route::get('/users/{id}/permisos', [UserController::class, 'permisos'])->name('crud.user.permisos')->middleware('can:update,App\Models\User');
    Route::post('/users/{id}/permisos', [UserController::class, 'asignarPermiso'])->name('crud.user.permisos.asignar')->middleware('can:update,App\Models\User');
    Route::delete('/users/{id}/permisos/{permisoId}', [UserController::class, 'quitarPermiso'])->name('crud.user.permisos.quitar')->middleware('can:update,App\Models\User');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('crud.user.eliminar')->middleware('can:delete,App\Models\User');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('crud.user.ver_datos')->middleware('can:view,App\Models\User');
});

// Voto
Route::middleware(['auth'])->group(function () {
    Route::get('/votos', [VotoController::class, 'index'])->name('crud.voto.ver')->middleware('can:viewAny,App\Models\Voto');
    Route::get('/votos/crear', [VotoController::class, 'create'])->name('crud.voto.crear')->middleware('can:create,App\Models\Voto');
    Route::post('/votos/crear', [VotoController::class, 'store'])->name('crud.voto.crear')->middleware('can:create,App\Models\Voto');
    Route::get('/votos/{id}/editar', [VotoController::class, 'edit'])->name('crud.voto.editar')->middleware('can:update,App\Models\Voto');
    Route::put('/votos/{id}/editar', [VotoController::class, 'update'])->name('crud.voto.editar')->middleware('can:update,App\Models\Voto');
    Route::delete('/votos/{id}', [VotoController::class, 'destroy'])->name('crud.voto.eliminar')->middleware('can:delete,App\Models\Voto');
    Route::get('/votos/{id}', [VotoController::class, 'show'])->name('crud.voto.ver_datos')->middleware('can:view,App\Models\Voto');
});

// Cargo
Route::middleware(['auth'])->group(function () {
    Route::get('/cargos', [CargoController::class, 'index'])->name('crud.cargo.ver')->middleware('can:viewAny,App\Models\Cargo');
    Route::get('/cargos/crear', [CargoController::class, 'create'])->name('crud.cargo.crear')->middleware('can:create,App\Models\Cargo');
    Route::post('/cargos/crear', [CargoController::class, 'store'])->name('crud.cargo.crear')->middleware('can:create,App\Models\Cargo');
    Route::get('/cargos/{id}/editar', [CargoController::class, 'edit'])->name('crud.cargo.editar')->middleware('can:update,App\Models\Cargo');
    Route::post('/cargos/{id}/editar', [CargoController::class, 'update'])->name('crud.cargo.editar')->middleware('can:update,App\Models\Cargo');
    Route::delete('/cargos/{id}', [CargoController::class, 'destroy'])->name('crud.cargo.eliminar')->middleware('can:delete,App\Models\Cargo');
    Route::get('/cargos/{id}', [CargoController::class, 'show'])->name('crud.cargo.ver_datos')->middleware('can:view,App\Models\Cargo');
});

// Estado Elecciones
Route::middleware(['auth'])->group(function () {
    Route::get('/estado-elecciones', [EstadoEleccionesController::class, 'index'])->name('crud.estado_elecciones.ver')->middleware('can:viewAny,App\Models\EstadoElecciones');
    Route::get('/estado-elecciones/crear', [EstadoEleccionesController::class, 'create'])->name('crud.estado_elecciones.crear')->middleware('can:create,App\Models\EstadoElecciones');
    Route::post('/estado-elecciones/crear', [EstadoEleccionesController::class, 'store'])->name('crud.estado_elecciones.crear')->middleware('can:create,App\Models\EstadoElecciones');
    Route::get('/estado-elecciones/{id}/editar', [EstadoEleccionesController::class, 'edit'])->name('crud.estado_elecciones.editar')->middleware('can:update,App\Models\EstadoElecciones');
    Route::post('/estado-elecciones/{id}/editar', [EstadoEleccionesController::class, 'update'])->name('crud.estado_elecciones.editar')->middleware('can:update,App\Models\EstadoElecciones');
    Route::delete('/estado-elecciones/{id}', [EstadoEleccionesController::class, 'destroy'])->name('crud.estado_elecciones.eliminar')->middleware('can:delete,App\Models\EstadoElecciones');
    Route::get('/estado-elecciones/{id}', [EstadoEleccionesController::class, 'show'])->name('crud.estado_elecciones.ver_datos')->middleware('can:view,App\Models\EstadoElecciones');
});

// Lista Votante
Route::middleware(['auth'])->group(function () {
    Route::get('/lista-votante', [ListaVotanteController::class, 'index'])->name('crud.lista_votante.ver')->middleware('can:viewAny,App\Models\ListaVotante');
    Route::get('/lista-votante/crear', [ListaVotanteController::class, 'create'])->name('crud.lista_votante.crear')->middleware('can:create,App\Models\ListaVotante');
    Route::post('/lista-votante/crear', [ListaVotanteController::class, 'store'])->name('crud.lista_votante.crear')->middleware('can:create,App\Models\ListaVotante');
    Route::get('/lista-votante/{id}/editar', [ListaVotanteController::class, 'edit'])->name('crud.lista_votante.editar')->middleware('can:update,App\Models\ListaVotante');
    Route::post('/lista-votante/{id}/editar', [ListaVotanteController::class, 'update'])->name('crud.lista_votante.editar')->middleware('can:update,App\Models\ListaVotante');
    Route::delete('/lista-votante/{id}', [ListaVotanteController::class, 'destroy'])->name('crud.lista_votante.eliminar')->middleware('can:delete,App\Models\ListaVotante');
    Route::get('/lista-votante/{id}', [ListaVotanteController::class, 'show'])->name('crud.lista_votante.ver_datos')->middleware('can:view,App\Models\ListaVotante');
});

// Permiso
Route::middleware(['auth'])->group(function () {
    Route::get('/permisos', [PermisoController::class, 'index'])->name('crud.permiso.ver')->middleware('can:viewAny,App\Models\Permiso');
    Route::get('/permisos/crear', [PermisoController::class, 'create'])->name('crud.permiso.crear')->middleware('can:create,App\Models\Permiso');
    Route::post('/permisos/crear', [PermisoController::class, 'store'])->name('crud.permiso.crear')->middleware('can:create,App\Models\Permiso');
    Route::get('/permisos/{id}/editar', [PermisoController::class, 'edit'])->name('crud.permiso.editar')->middleware('can:update,App\Models\Permiso');
    Route::post('/permisos/{id}/editar', [PermisoController::class, 'update'])->name('crud.permiso.editar')->middleware('can:update,App\Models\Permiso');
    Route::delete('/permisos/{id}', [PermisoController::class, 'destroy'])->name('crud.permiso.eliminar')->middleware('can:delete,App\Models\Permiso');
    Route::get('/permisos/{id}', [PermisoController::class, 'show'])->name('crud.permiso.ver_datos')->middleware('can:view,App\Models\Permiso');
});

// Propuesta Candidato
Route::middleware(['auth'])->group(function () {
    Route::get('/propuesta-candidato', [PropuestaCandidatoController::class, 'index'])->name('crud.propuesta_candidato.ver')->middleware('can:viewAny,App\Models\PropuestaCandidato');
    Route::get('/propuesta-candidato/crear', [PropuestaCandidatoController::class, 'create'])->name('crud.propuesta_candidato.crear')->middleware('can:create,App\Models\PropuestaCandidato');
    Route::post('/propuesta-candidato/crear', [PropuestaCandidatoController::class, 'store'])->name('crud.propuesta_candidato.crear')->middleware('can:create,App\Models\PropuestaCandidato');
    Route::get('/propuesta-candidato/{id}/editar', [PropuestaCandidatoController::class, 'edit'])->name('crud.propuesta_candidato.editar')->middleware('can:update,App\Models\PropuestaCandidato');
    Route::post('/propuesta-candidato/{id}/editar', [PropuestaCandidatoController::class, 'update'])->name('crud.propuesta_candidato.editar')->middleware('can:update,App\Models\PropuestaCandidato');
    Route::delete('/propuesta-candidato/{id}', [PropuestaCandidatoController::class, 'destroy'])->name('crud.propuesta_candidato.eliminar')->middleware('can:delete,App\Models\PropuestaCandidato');
    Route::get('/propuesta-candidato/{id}', [PropuestaCandidatoController::class, 'show'])->name('crud.propuesta_candidato.ver_datos')->middleware('can:view,App\Models\PropuestaCandidato');
    Route::get('/api/elecciones/{id}/candidatos', [PropuestaCandidatoController::class, 'getCandidatosByEleccion'])->middleware('can:viewAny,App\Models\PropuestaCandidato');
});

// Propuesta Partido
Route::middleware(['auth'])->group(function () {
    Route::get('/propuesta-partido', [PropuestaPartidoController::class, 'index'])->name('crud.propuesta_partido.ver')->middleware('can:viewAny,App\Models\PropuestaPartido');
    Route::get('/propuesta-partido/crear', [PropuestaPartidoController::class, 'create'])->name('crud.propuesta_partido.crear')->middleware('can:create,App\Models\PropuestaPartido');
    Route::post('/propuesta-partido/crear', [PropuestaPartidoController::class, 'store'])->name('crud.propuesta_partido.crear')->middleware('can:create,App\Models\PropuestaPartido');
    Route::get('/propuesta-partido/{id}/editar', [PropuestaPartidoController::class, 'edit'])->name('crud.propuesta_partido.editar')->middleware('can:update,App\Models\PropuestaPartido');
    Route::post('/propuesta-partido/{id}/editar', [PropuestaPartidoController::class, 'update'])->name('crud.propuesta_partido.editar')->middleware('can:update,App\Models\PropuestaPartido');
    Route::delete('/propuesta-partido/{id}', [PropuestaPartidoController::class, 'destroy'])->name('crud.propuesta_partido.eliminar')->middleware('can:delete,App\Models\PropuestaPartido');
    Route::get('/propuesta-partido/{id}', [PropuestaPartidoController::class, 'show'])->name('crud.propuesta_partido.ver_datos')->middleware('can:view,App\Models\PropuestaPartido');
    Route::get('/api/elecciones/{id}/partidos', [PropuestaPartidoController::class, 'getPartidosByEleccion'])->middleware('can:viewAny,App\Models\PropuestaPartido');
});

// Rol
Route::middleware(['auth'])->group(function () {
    Route::get('/roles', [RolController::class, 'index'])->name('crud.rol.ver')->middleware('can:viewAny,App\Models\Rol');
    Route::get('/roles/crear', [RolController::class, 'create'])->name('crud.rol.crear')->middleware('can:create,App\Models\Rol');
    Route::post('/roles/crear', [RolController::class, 'store'])->name('crud.rol.crear')->middleware('can:create,App\Models\Rol');
    Route::get('/roles/{id}/editar', [RolController::class, 'edit'])->name('crud.rol.editar')->middleware('can:update,App\Models\Rol');
    Route::post('/roles/{id}/editar', [RolController::class, 'update'])->name('crud.rol.editar')->middleware('can:update,App\Models\Rol');
    Route::delete('/roles/{id}', [RolController::class, 'destroy'])->name('crud.rol.eliminar')->middleware('can:delete,App\Models\Rol');
    Route::get('/roles/{id}', [RolController::class, 'show'])->name('crud.rol.ver_datos')->middleware('can:view,App\Models\Rol');
    Route::post('/roles/{id}/agregar-permiso', [RolController::class, 'agregarPermiso'])->name('crud.rol.agregar_permiso')->middleware('can:update,App\Models\Rol');
});

Route::middleware(['auth'])->group(function () {
    Route::get('kpi/elecciones-activas', [KPIController::class, 'obtenerCantidadEleccionesActivas'])->name('kpi.elecciones_activas');
    Route::get('kpi/electores-habilitados/{eleccion}', [KPIController::class, 'obtenerCantidadElectoresHabilitados'])->name('kpi.electores_habilitados');
    Route::get('kpi/electores-habilitados/{eleccion}/area/{area}', [KPIController::class, 'obtenerCantidadElectoresHabilitadosPorArea'])->name('kpi.electores_habilitados_por_area');
    Route::get('kpi/porcentaje-participacion/{eleccion}', [KPIController::class, 'obtenerPorcentajeParticipacionPorEleccion'])->name('kpi.porcentaje_participacion_por_eleccion');
    Route::get('kpi/porcentaje-participacion/{eleccion}/area/{area}', [KPIController::class, 'obtenerPorcentajeParticipacionPorArea'])->name('kpi.porcentaje_participacion_por_area');
});


// ========================================
// RUTA NECESARIA PARA EVITAR ERROR DE votante.home
// ========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/votante', [VotanteController::class, 'home'])->name('votante.home');
});

// ========================================
// RUTAS PLACEHOLDER PARA EVITAR ERRORES EN EL MENÚ
// ========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/votante', [VotanteController::class, 'home'])->name('votante.home');
    Route::get('/votante/elecciones', function () {
        return redirect()->route('votante.home')->with('info', 'La sección de elecciones está temporalmente inactiva.');
    })->name('votante.elecciones');
});

