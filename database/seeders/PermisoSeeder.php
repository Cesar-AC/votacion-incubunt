<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permiso;

class PermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisos = [
            ['idPermiso' => 1, 'permiso' => 'gestion.usuarios'],
            ['idPermiso' => 2, 'permiso' => 'gestion.roles'],
            ['idPermiso' => 3, 'permiso' => 'gestion.permisos'],
            ['idPermiso' => 4, 'permiso' => 'gestion.elecciones'],
            ['idPermiso' => 5, 'permiso' => 'gestion.candidatos'],
            ['idPermiso' => 6, 'permiso' => 'gestion.partidos'],
            ['idPermiso' => 7, 'permiso' => 'auditoria.votos'],
            ['idPermiso' => 8, 'permiso' => 'ver.reportes'],
            ['idPermiso' => 9, 'permiso' => 'votar'],
            ['idPermiso' => 10, 'permiso' => 'ver.resultados'],
        ];

        foreach ($permisos as $permiso) {
            Permiso::create($permiso);
        }
    }
}
