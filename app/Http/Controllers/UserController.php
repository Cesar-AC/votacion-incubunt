<?php

namespace App\Http\Controllers;

use App\Interfaces\Services\IAreaService;
use App\Interfaces\Services\ICarreraService;
use App\Interfaces\Services\IPermisoService;
use App\Interfaces\Services\IUserService;
use App\Models\User;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct(protected IUserService $userService) {}

    public function index()
    {
        $usuarios = User::with(['perfil', 'roles'])->get();

        return view('crud.user.ver', compact('usuarios'));
    }

    public function create(ICarreraService $carreraService, IAreaService $areaService)
    {
        $carreras = $carreraService->obtenerCarreras();
        $areas = $areaService->obtenerAreas();

        return view('crud.user.crear', compact('carreras', 'areas'));
    }

    public function store(Request $request, IPermisoService $permisoService)
    {
        $datosUsuario = $request->validate([
            'correo' => 'required|email|unique:User,correo|max:255',
            'contraseña' => 'required|string|min:8',
        ], [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El correo debe ser válido.',
            'correo.unique' => 'El correo ya está registrado.',
            'correo.max' => 'El correo no puede exceder los 255 caracteres.',
            'contraseña.required' => 'La contraseña es obligatoria.',
            'contraseña.string' => 'La contraseña debe ser texto.',
            'contraseña.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $datosPerfil = $request->validate([
            'apellidoPaterno' => 'required|string|max:20',
            'apellidoMaterno' => 'required|string|max:20',
            'nombre' => 'required|string|max:20',
            'otrosNombres' => 'nullable|string|max:40',
            'dni' => 'required|string|max:8',
            'telefono' => 'required|string|max:20',
            'idCarrera' => 'required|integer|exists:Carrera,idCarrera',
            'idArea' => 'required|integer|exists:Area,idArea',
            'idRol' => 'required|integer|exists:Rol,idRol',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'apellidoPaterno.required' => 'El apellido paterno es obligatorio.',
            'apellidoPaterno.string' => 'El apellido paterno debe ser texto.',
            'apellidoPaterno.max' => 'El apellido paterno no puede exceder los 20 caracteres.',
            'apellidoMaterno.required' => 'El apellido materno es obligatorio.',
            'apellidoMaterno.string' => 'El apellido materno debe ser texto.',
            'apellidoMaterno.max' => 'El apellido materno no puede exceder los 20 caracteres.',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre debe ser texto.',
            'nombre.max' => 'El nombre no puede exceder los 20 caracteres.',
            'otrosNombres.string' => 'Los otros nombres deben ser texto.',
            'otrosNombres.max' => 'Los otros nombres no pueden exceder los 40 caracteres.',
            'dni.required' => 'El DNI es obligatorio.',
            'dni.string' => 'El DNI debe ser texto.',
            'dni.max' => 'El DNI no puede exceder los 8 caracteres.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.string' => 'El teléfono debe ser texto.',
            'telefono.max' => 'El teléfono no puede exceder los 20 caracteres.',
            'idCarrera.required' => 'La carrera es obligatoria.',
            'idCarrera.integer' => 'La carrera debe ser un número entero.',
            'idCarrera.exists' => 'La carrera no es válida.',
            'idArea.required' => 'El área es obligatoria.',
            'idArea.integer' => 'El área debe ser un número entero.',
            'idArea.exists' => 'El área no es válida.',
            'idRol.required' => 'El rol es obligatorio.',
            'idRol.integer' => 'El rol debe ser un número entero.',
            'idRol.exists' => 'El rol no es válido.',
            'foto.image' => 'La foto debe ser una imagen.',
            'foto.mimes' => 'La foto debe ser una imagen con extensión .jpeg, .png, .jpg o .gif.',
            'foto.max' => 'La foto no puede exceder los 5MB.',
        ]);

        DB::transaction(function () use ($datosUsuario, $datosPerfil, $permisoService, $request) {
            $usuario = $this->userService->crearUsuario($datosUsuario, $datosPerfil);
            $rol = $permisoService->obtenerRolPorId($datosPerfil['idRol']);
            $permisoService->agregarUsuarioARol($usuario, $rol);

            if ($request->hasFile('foto')) {
                $this->userService->subirFoto($usuario, $request->file('foto'));
            }
        });

        return redirect()
            ->route('crud.user.ver')
            ->with('success', 'Usuario creado correctamente');
    }


    public function show(int $id)
    {
        $usuario = $this->userService->obtenerUsuarioPorId($id);

        return response()->json([
            'success' => true,
            'message' => 'Usuario obtenido',
            'data' => [
                'correo' => $usuario->correo,
            ],
        ]);
    }

    public function edit(int $id, ICarreraService $carreraService, IAreaService $areaService)
    {
        $usuario = $this->userService->obtenerUsuarioPorId($id);
        $carreras = $carreraService->obtenerCarreras();
        $areas = $areaService->obtenerAreas();
        return view('crud.user.editar', compact('usuario', 'carreras', 'areas'));
    }

    public function update(Request $request, int $id)
    {
        $usuario = $this->userService->obtenerUsuarioPorId($id);

        $correoExistente = User::where('correo', $request->correo)
            ->where('idUser', '!=', $usuario->getKey())
            ->exists();

        if ($correoExistente) {
            return redirect()
                ->back()
                ->withErrors([
                    'correo' => 'El correo ya está registrado.',
                ])
                ->withInput();
        }

        $datosUsuario = $request->validate([
            'correo' => 'email|max:255',
            'contraseña' => 'string|min:8',
        ], [
            'correo.email' => 'El correo debe ser válido.',
            'correo.max' => 'El correo no puede exceder los 255 caracteres.',
            'contraseña.string' => 'La contraseña debe ser texto.',
            'contraseña.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $this->userService->editarUsuario($datosUsuario, $usuario);

        $datosPerfil = $request->validate([
            'apellidoPaterno' => 'string|max:20',
            'apellidoMaterno' => 'string|max:20',
            'nombre' => 'string|max:20',
            'otrosNombres' => 'nullable|string|max:40',
            'dni' => 'string|max:8',
            'telefono' => 'string|max:20',
            'idCarrera' => 'integer|exists:Carrera,idCarrera',
            'idArea' => 'integer|exists:Area,idArea',
        ], [
            'apellidoPaterno.string' => 'El apellido paterno debe ser texto.',
            'apellidoPaterno.max' => 'El apellido paterno no puede exceder los 20 caracteres.',
            'apellidoMaterno.string' => 'El apellido materno debe ser texto.',
            'apellidoMaterno.max' => 'El apellido materno no puede exceder los 20 caracteres.',
            'nombre.string' => 'El nombre debe ser texto.',
            'nombre.max' => 'El nombre no puede exceder los 20 caracteres.',
            'otrosNombres.string' => 'Los otros nombres deben ser texto.',
            'otrosNombres.max' => 'Los otros nombres no pueden exceder los 40 caracteres.',
            'dni.string' => 'El DNI debe ser texto.',
            'dni.max' => 'El DNI no puede exceder los 8 caracteres.',
            'telefono.string' => 'El teléfono debe ser texto.',
            'telefono.max' => 'El teléfono no puede exceder los 20 caracteres.',
            'idCarrera.integer' => 'La carrera debe ser un número entero.',
            'idCarrera.exists' => 'La carrera no es válida.',
            'idArea.integer' => 'El área debe ser un número entero.',
            'idArea.exists' => 'El área no es válida.',
        ]);

        $request->validate([
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'foto.image' => 'La foto debe ser una imagen.',
            'foto.mimes' => 'La foto debe ser una imagen con extensión .jpeg, .png, .jpg o .gif.',
            'foto.max' => 'La foto no puede exceder los 5MB.',
        ]);

        DB::transaction(function () use ($datosUsuario, $datosPerfil, $request, $usuario) {
            $this->userService->editarUsuario($datosUsuario, $usuario);
            $this->userService->editarPerfilUsuario($datosPerfil, $usuario);

            if ($request->hasFile('foto')) {
                $this->userService->cambiarFoto($usuario, $request->file('foto'));
            }
        });

        return redirect()
            ->route('crud.user.ver')
            ->with('success', 'Usuario actualizado correctamente');
    }

    public function permisos(int $id)
    {
        $usuario = $this->userService->obtenerUsuarioPorId($id);
        $permisos = Permiso::orderBy('permiso')->get();

        return view('crud.user.permisos', compact('usuario', 'permisos'));
    }

    public function asignarPermiso(Request $request, int $id, IPermisoService $permisoService)
    {
        $data = $request->validate([
            'permiso_id' => 'required|integer|exists:Permiso,idPermiso',
        ]);

        $usuario = $this->userService->obtenerUsuarioPorId($id);
        $permiso = $permisoService->obtenerPermisoPorId($data['permiso_id']);

        $permisoService->agregarPermisoAUsuario($usuario, $permiso);

        return redirect()
            ->route('crud.user.permisos', $usuario->getKey())
            ->with('success', 'Permiso asignado correctamente');
    }

    public function quitarPermiso(int $id, int $permisoId, IPermisoService $permisoService)
    {
        $usuario = $this->userService->obtenerUsuarioPorId($id);
        $permiso = $permisoService->obtenerPermisoPorId($permisoId);

        $permisoService->quitarPermisoDeUsuario($usuario, $permiso);

        return redirect()
            ->route('crud.user.permisos', $usuario->getKey())
            ->with('success', 'Permiso eliminado correctamente');
    }

    public function destroy(int $id)
    {
        $usuario = $this->userService->obtenerUsuarioPorId($id);
        $this->userService->eliminarUsuario($usuario);

        return redirect()
            ->route('crud.user.ver')
            ->with('success', 'Usuario eliminado correctamente');
    }

    public function subirFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'foto.required' => 'La foto es requerida.',
            'foto.image' => 'La foto debe ser una imagen.',
            'foto.mimes' => 'La foto debe ser un archivo de tipo: jpeg, png, jpg, gif.',
            'foto.max' => 'La foto no puede exceder los 5MB.'
        ]);

        $this->userService->cambiarFoto(Auth::user(), $request->file('foto'));

        return redirect()
            ->route('profile.show')
            ->with('success', 'Se ha actualizado la foto correctamente.');
    }
}
