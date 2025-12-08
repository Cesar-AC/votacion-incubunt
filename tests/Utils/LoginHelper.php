<?php

namespace Tests\Utils;

use App\Models\Permiso;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Auth;

class LoginHelper
{
    private static function usuarioConPermiso(string $permiso){
        $user = User::factory()->create([
            'usuario' => fake()->userName(),
            'email' => fake()->email(),
            'password' => bcrypt(fake()->password()),
        ]);

        $perm = new Permiso([
            'permiso' => $permiso,
        ]);
        $perm->save();

        $user->permisos()->attach($perm);

        return $user;
    }

    public static function loguearseConPermiso(TestCase $test, string $permiso): User
    {
        $user = self::usuarioConPermiso($permiso);
        $test->actingAs($user);
        
        return $user;
    }

}