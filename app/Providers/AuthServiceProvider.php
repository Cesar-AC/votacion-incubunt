<?php

namespace App\Providers;

use App\Models\Area;
use App\Models\Candidato;
use App\Models\Cargo;
use App\Models\Carrera;
use App\Models\Elecciones;
use App\Models\EstadoElecciones;
use App\Models\EstadoParticipante;
use App\Models\ExcepcionPermiso;
use App\Models\ListaVotante;
use App\Models\Log;
use App\Models\PadronElectoral;
use App\Models\Participante;
use App\Models\Partido;
use App\Models\Permiso;
use App\Models\PropuestaCandidato;
use App\Models\PropuestaPartido;
use App\Models\Rol;
use App\Models\RolPermiso;
use App\Models\RolUser;
use App\Models\TipoVoto;
use App\Models\User;
use App\Models\UserPermiso;
use App\Models\Voto;
use App\Policies\AreaPolicy;
use App\Policies\CandidatoPolicy;
use App\Policies\CargoPolicy;
use App\Policies\CarreraPolicy;
use App\Policies\EleccionesPolicy;
use App\Policies\EstadoEleccionesPolicy;
use App\Policies\EstadoParticipantePolicy;
use App\Policies\ExcepcionPermisoPolicy;
use App\Policies\ListaVotantePolicy;
use App\Policies\LogPolicy;
use App\Policies\PadronElectoralPolicy;
use App\Policies\ParticipantePolicy;
use App\Policies\PartidoPolicy;
use App\Policies\PermisoPolicy;
use App\Policies\PropuestaCandidatoPolicy;
use App\Policies\PropuestaPartidoPolicy;
use App\Policies\RolPolicy;
use App\Policies\RolPermisoPolicy;
use App\Policies\RolUserPolicy;
use App\Policies\TipoVotoPolicy;
use App\Policies\UserPolicy;
use App\Policies\UserPermisoPolicy;
use App\Policies\VotoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Area::class => AreaPolicy::class,
        Candidato::class => CandidatoPolicy::class,
        Cargo::class => CargoPolicy::class,
        Carrera::class => CarreraPolicy::class,
        Elecciones::class => EleccionesPolicy::class,
        EstadoElecciones::class => EstadoEleccionesPolicy::class,
        EstadoParticipante::class => EstadoParticipantePolicy::class,
        ExcepcionPermiso::class => ExcepcionPermisoPolicy::class,
        ListaVotante::class => ListaVotantePolicy::class,
        Log::class => LogPolicy::class,
        PadronElectoral::class => PadronElectoralPolicy::class,
        Participante::class => ParticipantePolicy::class,
        Partido::class => PartidoPolicy::class,
        Permiso::class => PermisoPolicy::class,
        PropuestaCandidato::class => PropuestaCandidatoPolicy::class,
        PropuestaPartido::class => PropuestaPartidoPolicy::class,
        Rol::class => RolPolicy::class,
        RolPermiso::class => RolPermisoPolicy::class,
        RolUser::class => RolUserPolicy::class,
        TipoVoto::class => TipoVotoPolicy::class,
        User::class => UserPolicy::class,
        UserPermiso::class => UserPermisoPolicy::class,
        Voto::class => VotoPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
