<?php

namespace App\Http\Controllers;

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
            'contraseña' => $request->password,
        ];

        if (!Auth::attempt($credenciales)){
            return back()->withErrors(['email' => 'Las credenciales no coinciden con nuestros registros.'])->withInput();
        }

        $request->session()->regenerate();
        return redirect(route('dashboard'));
    }

    public function cerrarSesion(Request $request)
    {
        Auth::logout();
        return redirect(route('vistaLogin'))->withCookie(cookie()->forget('token'));
    }
}
