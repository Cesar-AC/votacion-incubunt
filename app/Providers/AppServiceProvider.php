<?php

namespace App\Providers;

use App\Services\EstadisticasElectoralesService;
use App\Interfaces\IEstadisticasElectoralesService;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip().$request->get('email'))
                ->response(function(){
                    return back()->withErrors(['email' => 'Demasiados intentos de inicio de sesión. Por favor, inténtelo de nuevo en un minuto.'])->withInput();
                });
        });
    }
}
