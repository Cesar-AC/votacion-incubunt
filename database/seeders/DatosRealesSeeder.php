<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Cargo;
use App\Models\Carrera;
use App\Models\CategoriaLog;
use App\Models\EstadoElecciones;
use App\Models\EstadoUsuario;
use App\Models\NivelLog;
use App\Models\TipoVoto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatosRealesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $areaPresidencia = Area::create([
            'area' => 'Presidencia',
        ]);

        $cargosPresidencia = [
            'Presidente',
            'Vicepresidente',
            'Coordinador general',
        ];

        foreach ($cargosPresidencia as $cargo) {
            Cargo::create([
                'cargo' => $cargo,
                'idArea' => $areaPresidencia->getKey(),
            ]);
        }

        $areas = [
            'Sin área asignada',
            'Marketing',
            'Project Management Office',
            'Tecnologías de la Información',
            'Logística y Finanzas',
            'Sistemas Integrados de Gestión y Emprendimiento',
            'Gestión del Talento Humano',
        ];

        $cargos = [
            'Director de Área',
        ];

        foreach ($areas as $area) {
            $areaModelo = Area::create([
                'area' => $area,
            ]);

            foreach ($cargos as $cargo) {
                Cargo::create([
                    'cargo' => $cargo,
                    'idArea' => $areaModelo->getKey(),
                ]);
            }
        }

        $carreras = [
            'Sin carrera asignada',
            'Agronomía',
            'Ingeniería Agrícola',
            'Ingeniería Agroindustrial',
            'Zootecnia',
            'Ciencias Biológicas',
            'Microbiológica y Parasitología',
            'Biología Pesquera',
            'Administración',
            'Contabilidad y Finanzas',
            'Economía',
            'Estomatología',
            'Estadística',
            'Física',
            'Informática',
            'Matemáticas',
            'Antropología',
            'Arqueología',
            'Historia',
            'Trabajo Social',
            'Turismo',
            'Derecho',
            'Ciencias Política y Gobernabilidad',
            'Enfermería',
            'Ciencias de la Comunicación',
            'Educación Inicial',
            'Educación Primaria',
            'Educación Secundaria Mención Idiomas',
            'Educación Secundaria Mención Matemáticas',
            'Educación Secundaria Mención Lengua y Literatura',
            'Educación Secundaria Mención Ciencias Naturales',
            'Educación Secundaria Mención Filosofía, Psicología y Ciencias Sociales',
            'Educación Secundaria Mención Historia y Geografía',
            'Farmacia y Bioquímica',
            'Arquitectura y Urbanismo',
            'Ingeniería Civil',
            'Ingeniería Industrial',
            'Ingeniería de Materiales',
            'Ingeniería Mecánica',
            'Ingeniería Mecatrónica',
            'Ingeniería Metalúrgica',
            'Ingeniería de Minas',
            'Ingeniería de Sistemas',
            'Ingeniería Química',
            'Ingeniería Ambiental',
            'Medicina',
        ];

        foreach ($carreras as $carrera) {
            Carrera::create([
                'carrera' => $carrera,
            ]);
        }

        $estadosElecciones = [
            'Programada',
            'Finalizada',
            'Anulada'
        ];

        foreach ($estadosElecciones as $estado) {
            EstadoElecciones::create([
                'estado' => $estado,
            ]);
        }

        $estadosUsuario = [
            'Activo',
            'Inactivo',
            'Suspendido',
            'Inhabilitado',
        ];

        foreach ($estadosUsuario as $estado) {
            EstadoUsuario::create([
                'nombre' => $estado,
            ]);
        }

        $nivelLog = [
            'Información',
            'Advertencia',
            'Error',
            'Error crítico',
        ];

        foreach ($nivelLog as $nivel) {
            NivelLog::create([
                'nombre' => $nivel,
            ]);
        }

        $categoriaLog = [
            'Autenticación',
        ];

        foreach ($categoriaLog as $categoria) {
            CategoriaLog::create([
                'nombre' => $categoria,
            ]);
        }

        $tiposVoto = [
            ['idTipoVoto' => TipoVoto::ID_NO_APLICABLE, 'descripcion' => 'Tipo de voto no aplicable', 'peso' => 1],
            ['idTipoVoto' => TipoVoto::ID_MISMA_AREA, 'descripcion' => 'Voto de la misma área', 'peso' => 2],
            ['idTipoVoto' => TipoVoto::ID_OTRA_AREA, 'descripcion' => 'Voto de área externa', 'peso' => 1],
        ];

        foreach ($tiposVoto as $tipoVoto) {
            TipoVoto::create($tipoVoto);
        }
    }
}
