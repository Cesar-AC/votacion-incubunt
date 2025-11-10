<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Carrera;
use App\Models\EstadoElecciones;
use App\Models\TipoVoto;
use App\Models\Area;
use App\Models\Cargo;

class DatosPruebaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carreras = [
            ['idCarrera' => 1, 'carrera' => 'Ingeniería de Sistemas'],
            ['idCarrera' => 2, 'carrera' => 'Administración de Empresas'],
            ['idCarrera' => 3, 'carrera' => 'Derecho'],
            ['idCarrera' => 4, 'carrera' => 'Medicina'],
        ];

        foreach ($carreras as $carrera) {
            Carrera::create($carrera);
        }

        $estados = [
            ['idEstado' => 1, 'estado' => 'Pendiente'],
            ['idEstado' => 2, 'estado' => 'Activa'],
            ['idEstado' => 3, 'estado' => 'Finalizada'],
            ['idEstado' => 4, 'estado' => 'Cancelada'],
        ];

        foreach ($estados as $estado) {
            EstadoElecciones::create($estado);
        }

        $tiposVoto = [
            ['idTipoVoto' => 1, 'tipoVoto' => 'digital'],
            ['idTipoVoto' => 2, 'tipoVoto' => 'presencial'],
        ];

        foreach ($tiposVoto as $tipoVoto) {
            TipoVoto::create($tipoVoto);
        }

        $areas = [
            ['idArea' => 1, 'area' => 'Consejo Estudiantil'],
            ['idArea' => 2, 'area' => 'Representantes de Curso'],
            ['idArea' => 3, 'area' => 'Comités Especiales'],
        ];

        foreach ($areas as $area) {
            Area::create($area);
        }

        $cargos = [
            ['idCargo' => 1, 'cargo' => 'Presidente', 'idArea' => 1],
            ['idCargo' => 2, 'cargo' => 'Vicepresidente', 'idArea' => 1],
            ['idCargo' => 3, 'cargo' => 'Secretario', 'idArea' => 1],
            ['idCargo' => 4, 'cargo' => 'Representante', 'idArea' => 2],
        ];

        foreach ($cargos as $cargo) {
            Cargo::create($cargo);
        }
    }
}
