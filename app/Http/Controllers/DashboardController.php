<?php

namespace App\Http\Controllers;

use App\Policies\Utils\ValidadorPermisos;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Candidato;
use App\Models\Partido;
use App\Models\Elecciones;
use App\Models\PadronElectoral;
use App\Models\VotoCandidato;
use App\Models\VotoPartido;

class DashboardController extends Controller
{

    private function esAdministrador(Request $request)
    {
        $user = $request->user();
        return ValidadorPermisos::usuarioTienePermiso($user, 'dashboard:administrador');
    }

    private function esVotante(Request $request)
    {
        $user = $request->user();
        return ValidadorPermisos::usuarioTienePermiso($user, 'dashboard:votante');
    }

    public function index(Request $request)
    {
        if ($this->esAdministrador($request)) {
            $usuarios = User::count();
            $candidatos = Candidato::count();
            $partidos = Partido::count();
            $eleccionesTotal = Elecciones::count();
            $eleccionesActivas = Elecciones::all()->filter(function ($e) {
                return method_exists($e, 'estaActivo') ? $e->estaActivo() : false;
            })->count();
            $padrones = PadronElectoral::count();
            $votos = VotoCandidato::count() + VotoPartido::count();

            // Contar administradores mediante el validador de permisos
            $admins = collect(User::all())->filter(function ($u) {
                return ValidadorPermisos::usuarioTienePermiso($u, 'dashboard:administrador');
            })->count();

            $recentEleccion = Elecciones::latest('fechaInicio')->first();

            $stats = [
                'usuarios' => $usuarios,
                'candidatos' => $candidatos,
                'partidos' => $partidos,
                'elecciones_total' => $eleccionesTotal,
                'elecciones_activas' => $eleccionesActivas,
                'padrones' => $padrones,
                // 'votos' => $votos,
                'admins' => $admins,
            ];

            return view('admin.dashboard', compact('stats', 'recentEleccion'));
        }

        if ($this->esVotante($request)) {
            return redirect()->route('votante.home');
        }

        return abort(403, 'No tienes permiso para acceder al dashboard. Contacta a un administrador.');
    }
}
