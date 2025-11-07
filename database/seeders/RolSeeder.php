<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rol;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['idRol' => 1, 'rol' => 'Admin'],
            ['idRol' => 2, 'rol' => 'Auditor'],
            ['idRol' => 3, 'rol' => 'Votante'],
        ];

        foreach ($roles as $rol) {
            Rol::create($rol);
        }
    }
}
