<?php

namespace App\Http\Controllers;

use App\Models\Log as LogModel;
use App\Models\CategoriaLog;
use App\Models\EstadoUsuario;
use App\Models\NivelLog;
use App\Models\User;
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
        ], [
            'email.required' => 'El correo es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $credenciales = [
            'correo' => $request->email,
            'password' => $request->password,
        ];

        if (User::where('correo', '=', $credenciales['correo'])->first()->estaInhabilitado()) {
            return back()->withErrors(['email' => 'El usuario ha sido deshabilitado.'])->withInput();
        }

        if (!Auth::attempt($credenciales)) {
            $categoria = CategoriaLog::where('nombre', 'Autenticación')->first();
            $nivel = NivelLog::where('nombre', 'Error')->first() ?? NivelLog::first();

            if ($categoria && $nivel) {
                LogModel::create([
                    'idCategoriaLog' => $categoria->idCategoriaLog,
                    'idNivelLog' => $nivel->idNivelLog,
                    'idUsuario' => null,
                    'fecha' => Carbon::now(),
                    'descripcion' => 'Inicio de sesión fallido para el correo: ' . $request->email,
                ]);
            }

            return back()->withErrors(['email' => 'Las credenciales no coinciden con nuestros registros.'])->withInput();
        }

        // Fetch category and level by name instead of hardcoded IDs
        $categoria = CategoriaLog::where('nombre', 'Autenticación')->first();
        $nivel = NivelLog::where('nombre', 'Éxito')->first() ?? NivelLog::first();

        if ($categoria && $nivel) {
            LogModel::create([
                'idCategoriaLog' => $categoria->idCategoriaLog,
                'idNivelLog' => $nivel->idNivelLog,
                'idUsuario' => Auth::id(),
                'fecha' => Carbon::now(),
                'descripcion' => 'Inicio de sesión correcto para el correo: ' . $request->email,
            ]);
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
