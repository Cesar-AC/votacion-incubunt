<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RolPermiso;

class RolPermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminPermisos = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        foreach ($adminPermisos as $idPermiso) {
            RolPermiso::create([
                'idRol' => 1,
                'idPermiso' => $idPermiso,
            ]);
        }

        $auditorPermisos = [7, 8, 10];
        foreach ($auditorPermisos as $idPermiso) {
            RolPermiso::create([
                'idRol' => 2,
                'idPermiso' => $idPermiso,
            ]);
        }

        $votantePermisos = [9, 10]; 
        foreach ($votantePermisos as $idPermiso) {
            RolPermiso::create([
                'idRol' => 3,
                'idPermiso' => $idPermiso,
            ]);
        }
    }
}
