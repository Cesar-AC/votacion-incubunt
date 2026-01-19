<?php

namespace App\Providers;

use App\DTO\Services\VotosCandidatoDTO;
use App\Enum\Config;
use App\Interfaces\DTO\Services\IVotosCandidatoDTO;
use App\Services\EstadisticasElectoralesService;
use App\Interfaces\IEstadisticasElectoralesService;
use App\Services\EleccionesService;
use App\Interfaces\Services\IEleccionesService;
use App\Interfaces\Services\IPermisoService;
use App\Interfaces\Services\IVotoService;
use App\Models\Configuracion;
use App\Models\Elecciones;
use App\Services\PermisoService;
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

        $this->app->bind(IVotosCandidatoDTO::class, VotosCandidatoDTO::class);
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
