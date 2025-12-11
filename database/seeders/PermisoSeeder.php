<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;

class PermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    const INDICATIVO_CRUD = 'crud';
    const INDICATIVO_COMODIN = '*';
    
    private function crearPermisosCRUD($entidades){
        $indicativo_crud = self::INDICATIVO_CRUD;
        $acciones = ['ver:' . self::INDICATIVO_COMODIN, 'agregar', 'editar', 'eliminar', self::INDICATIVO_COMODIN];

        foreach($entidades as $entidad){
            $permiso = join(':', [$entidad, $indicativo_crud]);
            
            foreach($acciones as $accion){
                $permisoCRUD = join(':', [$permiso, $accion]);
                Permiso::create([
                    'permiso' => $permisoCRUD,
                ]);
            }
        }
    }

    private function crearPermisosComodin($entidades){
        foreach ($entidades as $entidad){
            $permiso = join(':', [$entidad, self::INDICATIVO_COMODIN]);
            Permiso::create([
                'permiso' => $permiso,
            ]);
        }
    }

    private function crearPermisosDashboard(){
        $dashboards = ['administrador', 'votante'];

        foreach ($dashboards as $dashboard){
            $permiso = join(':', ['dashboard', $dashboard]);
            Permiso::create([
                'permiso' => $permiso,
            ]);
        }
    }

    private function obtenerPermisosVotante(){
        // Por implementar.
        return [];
    }

    private function crearPermisosVotante(){
        $permisos = $this->obtenerPermisosVotante();
        foreach ($permisos as $permiso){
            Permiso::create([
                'permiso' => $permiso,
            ]);
        }
    }

    private function obtenerEntidades(){
        $entidades = [
            'area',
            'candidato',
            'cargo',
            'carrera',
            'elecciones',
            'estado_elecciones',
            'estado_participante',
            'lista_votante',
            'padron_electoral',
            'participante',
            'partido',
            'permiso',
            'propuesta_candidato',
            'propuesta_partido',
            'rol',
            'tipo_voto',
            'user',
            'voto'
        ];

        return $entidades;
    }

    private function crearPermisos(){
        $entidades = $this->obtenerEntidades();

        $this->crearPermisosCRUD($entidades);
        $this->crearPermisosComodin($entidades);
        $this->crearPermisosVotante();
        $this->crearPermisosDashboard();
    }

    private function crearRoles(){
        $roles = ['administrador', 'votante'];

        foreach ($roles as $rol){
            Rol::create([
                'rol' => $rol,
            ]);
        }
    }

    private function asignarPermisosAdministrador(){
        $rolAdministrador = Rol::where('rol', 'administrador')->first();

        $entidades = $this->obtenerEntidades();

        $rolAdministrador->permisos()->attach(Permiso::where('permiso', '=', 'dashboard:administrador')->first());
        foreach ($entidades as $entidad){
            $permiso = join(':', [$entidad, self::INDICATIVO_COMODIN]);
            $rolAdministrador->permisos()->attach(Permiso::where('permiso', $permiso)->first());
        }
    }

    private function asignarPermisosVotante(){
        $rolVotante = Rol::where('rol', 'votante')->first();

        $rolVotante->permisos()->attach(Permiso::where('permiso', '=', 'dashboard:votante')->first());
        $permisos = $this->obtenerPermisosVotante();
        foreach ($permisos as $permiso){
            $rolVotante->permisos()->attach(Permiso::where('permiso', '=', $permiso)->first());
        }   
    }
    
    private function asignarPermisosARoles(){
        $this->asignarPermisosAdministrador();
        $this->asignarPermisosVotante();
    }

    private function asignarRolAUsuario($rol, $usuario){
        $rolAdministrador = Rol::where('rol', $rol)->first();
        $usuario->roles()->attach($rolAdministrador);
    }

    private function crearUsuarioAdministrador(){
        $usuario = new User([
            'correo' => 'administrador@incubunt.com',
            'contraseña' => bcrypt('password'),
            'idEstadoUsuario' => 1, // Activo
        ]);

        $usuario->save();

        $this->asignarRolAUsuario('administrador', $usuario);
    }

    private function crearUsuarioVotante(){
        $usuario = new User([
            'correo' => 'votante@incubunt.com',
            'contraseña' => bcrypt('password'),
            'idEstadoUsuario' => 1, // Activo
        ]);

        $usuario->save();

        $this->asignarRolAUsuario('votante', $usuario);
    }
    
    public function run(): void
    {
        $this->crearPermisos();
        $this->crearRoles();
        $this->asignarPermisosARoles();
        $this->crearUsuarioAdministrador();
        $this->crearUsuarioVotante();
    }
}
