<?php

namespace App\Http\Controllers;

use App\Models\Log as LogModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class AutenticacionController extends Controller
{
    public function verInicioSesion()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function iniciarSesion(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credenciales = [
            'correo' => $request->email,
            'password' => $request->password,
        ];

        if (!Auth::attempt($credenciales)){
            $log = LogModel::create([
                'idCategoriaLog' => 1,
                'idNivelLog' => 2,
                'idUsuario' => null,
                'fecha' => Carbon::now(),
                'descripcion' => 'Inicio de sesión fallido para el correo: ' . $request->email,
            ]);

            $log->save();

            return back()->withErrors(['email' => 'Las credenciales no coinciden con nuestros registros.'])->withInput();
        }

        $log = LogModel::create([
            'idCategoriaLog' => 1,
            'idNivelLog' => 1,
            'idUsuario' => null,
            'fecha' => Carbon::now(),
            'descripcion' => 'Inicio de sesión correcto para el correo: ' . $request->email,
        ]);

        $log->save();

        $request->session()->regenerate();
        return redirect(route('dashboard'));
    }

    public function cerrarSesion(Request $request)
    {
        Auth::logout();
        return redirect(route('vistaLogin'))->withCookie(cookie()->forget('token'));
    }
}
