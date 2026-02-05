<?php

namespace App\Providers;

use App\DTO\Services\VotosCandidatoDTO;
use App\Enum\Config;
use App\Interfaces\DTO\Services\IVotosCandidatoDTO;
use App\Services\EstadisticasElectoralesService;
use App\Interfaces\IEstadisticasElectoralesService;
use App\Interfaces\Services\IArchivoService;
use App\Interfaces\Services\IAreaService;
use App\Interfaces\Services\ICandidatoService;
use App\Interfaces\Services\ICarreraService;
use App\Services\EleccionesService;
use App\Interfaces\Services\IEleccionesService;
use App\Interfaces\Services\IPadronElectoralService;
use App\Interfaces\Services\IPartidoService;
use App\Interfaces\Services\IPermisoService;
use App\Interfaces\Services\IUserService;
use App\Interfaces\Services\IVotoService;
use App\Interfaces\Services\PadronElectoral\IImportadorFactory;
use App\Interfaces\Services\PadronElectoral\IImportadorService;
use App\Models\Configuracion;
use App\Models\Elecciones;
use App\Services\ArchivoService;
use App\Services\PadronElectoral\ImportadorFactory;
use App\Services\PadronElectoral\ImportadorService;
use App\Services\AreaService;
use App\Services\CandidatoService;
use App\Services\CarreraService;
use App\Services\PadronElectoralService;
use App\Services\PartidoService;
use App\Services\PermisoService;
use App\Services\UserService;
use App\Services\VotoService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(IEstadisticasElectoralesService::class, EstadisticasElectoralesService::class);
        $this->app->singleton(IEleccionesService::class, function () {
            return new EleccionesService(
                Elecciones::find(Configuracion::obtenerValor(Config::ELECCION_ACTIVA))
            );
        });

        $this->app->singleton(IPermisoService::class, PermisoService::class);

        $this->app->singleton(IVotoService::class, function () {
            return new VotoService(
                $this->app->make(IEleccionesService::class),
                $this->app->make(IPermisoService::class)
            );
        });

        $this->app->singleton(IArchivoService::class, ArchivoService::class);
        $this->app->singleton(IImportadorFactory::class, ImportadorFactory::class);
        $this->app->singleton(IImportadorService::class, ImportadorService::class);
        $this->app->singleton(IAreaService::class, AreaService::class);
        $this->app->singleton(ICandidatoService::class, CandidatoService::class);
        $this->app->singleton(ICarreraService::class, CarreraService::class);
        $this->app->singleton(IPadronElectoralService::class, function () {
            return new PadronElectoralService(
                $this->app->make(IEleccionesService::class)
            );
        });

        $this->app->singleton(IPartidoService::class, function () {
            return new PartidoService(
                $this->app->make(IEleccionesService::class),
                $this->app->make(IArchivoService::class),
            );
        });

        $this->app->singleton(IUserService::class, function () {
            return new UserService(
                $this->app->make(IArchivoService::class),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip() . $request->get('email'))
                ->response(function () {
                    return back()->withErrors(['email' => 'Demasiados intentos de inicio de sesión. Por favor, inténtelo de nuevo en un minuto.'])->withInput();
                });
        });
    }
}
