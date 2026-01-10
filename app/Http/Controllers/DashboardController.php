<?php

namespace App\Http\Controllers;

use App\Policies\Utils\ValidadorPermisos;
use Illuminate\Http\Request;

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
        return match (true) {
            $this->esAdministrador($request) => view('admin.dashboard'),
            $this->esVotante($request) => redirect()->route('votante.home'),
            default => abort(403, 'No tienes permiso para acceder al dashboard. Contacta a un administrador.'),
        };
    }
}
