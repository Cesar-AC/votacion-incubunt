<?php

namespace Tests\Utils;

use App\Models\Permiso;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Auth;

class LoginHelper
{
    private static function asignarPermisoAUsuario(User $user, string $permiso){
        $permisoModel = Permiso::firstOrCreate(['permiso' => $permiso]);
        $user->permisos()->attach($permisoModel->idPermiso);
    }

    private static function usuarioConPermisos(array|string $permisos){
        $user = User::factory()->create([
            'correo' => fake()->email(),
            'contraseña' => bcrypt(fake()->password()),
        ]);

        if (is_array($permisos)) {
            foreach ($permisos as $permiso) {
                self::asignarPermisoAUsuario($user, $permiso);
            }
        } else {
            self::asignarPermisoAUsuario($user, $permisos);
        }
        
        return $user;
    }

    public static function loguearseConPermiso(TestCase $test, array|string $permisos): User
    {
        $test->seed();
        $user = self::usuarioConPermisos($permisos);
        $test->actingAs($user);
        
        return $user;
    }

}