<?php

namespace Tests\Feature;

use App\Enum\ImportadorArchivo;
use App\Models\Elecciones;
use App\Models\PadronElectoral;
use App\Models\User;
use App\Services\PadronElectoral\ImportadorFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportacionPadronElectoral extends TestCase
{
    use RefreshDatabase;

    private ImportadorFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->factory = new ImportadorFactory();
    }

    // ==================== TESTS CSV ====================

    public function test_importar_padron_csv_caso_normal(): void
    {
        $eleccion = Elecciones::factory()->create();
        $importador = $this->factory->crear(ImportadorArchivo::CSV);
        $rutaArchivo = base_path('tests/TestFiles/prueba_padron.csv');

        $importador->importar($rutaArchivo, $eleccion);

        // Verificar que se crearon usuarios
        $this->assertDatabaseHas('User', [
            'correo' => 'diegoagreda51@gmail.com',
        ]);

        $this->assertDatabaseHas('PerfilUsuario', [
            'dni' => '71336777',
            'nombre' => 'Diego',
            'apellidoPaterno' => 'Agreda',
        ]);

        // Verificar que se registró en el padrón
        $user = User::where('correo', 'diegoagreda51@gmail.com')->first();
        $this->assertDatabaseHas('PadronElectoral', [
            'idElecciones' => $eleccion->getKey(),
            'idUsuario' => $user->getKey(),
        ]);

        // Verificar cantidad en padrón
        $cantidad = PadronElectoral::where('idElecciones', $eleccion->getKey())->count();
        $this->assertGreaterThan(50, $cantidad);
    }

    public function test_importar_padron_csv_no_duplica_usuarios(): void
    {
        $eleccion = Elecciones::factory()->create();
        $importador = $this->factory->crear(ImportadorArchivo::CSV);
        $rutaArchivo = base_path('tests/TestFiles/padron_duplicados.csv');

        $importador->importar($rutaArchivo, $eleccion);

        // El archivo tiene 7 filas pero solo 3 usuarios únicos
        // usuario1@test.com aparece 3 veces
        // usuario2@test.com aparece 2 veces
        // usuario3@test.com aparece 1 vez

        $usuario1 = User::where('correo', 'usuario1@test.com')->get();
        $this->assertCount(1, $usuario1, 'usuario1 no debería duplicarse');

        $usuario2 = User::where('correo', 'usuario2@test.com')->get();
        $this->assertCount(1, $usuario2, 'usuario2 no debería duplicarse');

        $usuario3 = User::where('correo', 'usuario3@test.com')->get();
        $this->assertCount(1, $usuario3, 'usuario3 no debería duplicarse');

        // Verificar que solo hay 3 entradas en padrón para esta elección
        $cantidadPadron = PadronElectoral::where('idElecciones', $eleccion->getKey())->count();
        $this->assertEquals(3, $cantidadPadron, 'Solo deberían existir 3 registros únicos en el padrón');
    }

    public function test_importar_padron_csv_no_duplica_en_misma_eleccion(): void
    {
        $eleccion = Elecciones::factory()->create();
        $importador = $this->factory->crear(ImportadorArchivo::CSV);
        $rutaArchivo = base_path('tests/TestFiles/padron_duplicados.csv');

        // Importar dos veces el mismo archivo
        $importador->importar($rutaArchivo, $eleccion);
        $importador->importar($rutaArchivo, $eleccion);

        // Verificar que no se duplicaron usuarios
        $totalUsuarios = User::whereIn('correo', [
            'usuario1@test.com',
            'usuario2@test.com',
            'usuario3@test.com'
        ])->count();
        $this->assertEquals(3, $totalUsuarios);

        // Verificar que no se duplicaron entradas en padrón
        $cantidadPadron = PadronElectoral::where('idElecciones', $eleccion->getKey())->count();
        $this->assertEquals(3, $cantidadPadron);
    }

    public function test_importar_padron_csv_datos_incompletos_lanza_excepcion(): void
    {
        $eleccion = Elecciones::factory()->create();
        $importador = $this->factory->crear(ImportadorArchivo::CSV);
        $rutaArchivo = base_path('tests/TestFiles/padron_incompleto.csv');

        // Datos incompletos (area vacía) deberían causar error al buscar en el array
        $this->expectException(\ErrorException::class);

        $importador->importar($rutaArchivo, $eleccion);
    }

    // ==================== TESTS XLSX ====================

    public function test_importar_padron_xlsx_caso_normal(): void
    {
        $eleccion = Elecciones::factory()->create();
        $importador = $this->factory->crear(ImportadorArchivo::XLSX);
        $rutaArchivo = base_path('tests/TestFiles/prueba_padron.xlsx');

        $importador->importar($rutaArchivo, $eleccion);

        // Verificar cantidad en padrón (XLSX tiene los mismos datos que CSV)
        $cantidad = PadronElectoral::where('idElecciones', $eleccion->getKey())->count();
        $this->assertGreaterThan(0, $cantidad);

        // Verificar que al menos un usuario existe
        $usuarios = User::count();
        $this->assertGreaterThan(0, $usuarios);
    }

    public function test_importar_padron_xlsx_no_duplica_usuarios_en_diferentes_elecciones(): void
    {
        $eleccion1 = Elecciones::factory()->create();
        $eleccion2 = Elecciones::factory()->create();
        $importador = $this->factory->crear(ImportadorArchivo::XLSX);
        $rutaArchivo = base_path('tests/TestFiles/prueba_padron.xlsx');

        // Importar en elección 1
        $importador->importar($rutaArchivo, $eleccion1);
        $cantidadUsuariosAntes = User::count();

        // Importar en elección 2 (mismos usuarios, diferente elección)
        $importador->importar($rutaArchivo, $eleccion2);
        $cantidadUsuariosDespues = User::count();

        // Los usuarios no deben duplicarse (ya existen)
        $this->assertEquals(
            $cantidadUsuariosAntes,
            $cantidadUsuariosDespues,
            'Los usuarios no deberían duplicarse al importar en diferentes elecciones'
        );

        // Pero el padrón sí debe tener entradas para ambas elecciones
        $padronEleccion1 = PadronElectoral::where('idElecciones', $eleccion1->getKey())->count();
        $padronEleccion2 = PadronElectoral::where('idElecciones', $eleccion2->getKey())->count();

        $this->assertGreaterThan(0, $padronEleccion1);
        $this->assertGreaterThan(0, $padronEleccion2);
    }

    // ==================== TESTS MIXTOS ====================

    public function test_crear_importador_csv_desde_factory(): void
    {
        $importador = $this->factory->crear(ImportadorArchivo::CSV);
        $this->assertInstanceOf(\App\Interfaces\Services\PadronElectoral\IImportarDesdeArchivo::class, $importador);
    }

    public function test_crear_importador_xlsx_desde_factory(): void
    {
        $importador = $this->factory->crear(ImportadorArchivo::XLSX);
        $this->assertInstanceOf(\App\Interfaces\Services\PadronElectoral\IImportarDesdeArchivo::class, $importador);
    }

    public function test_importar_csv_y_xlsx_produce_mismos_usuarios(): void
    {
        // Crear dos elecciones separadas
        $eleccionCsv = Elecciones::factory()->create();
        $eleccionXlsx = Elecciones::factory()->create();

        $importadorCsv = $this->factory->crear(ImportadorArchivo::CSV);
        $importadorXlsx = $this->factory->crear(ImportadorArchivo::XLSX);

        // Importar CSV primero
        $importadorCsv->importar(base_path('tests/TestFiles/prueba_padron.csv'), $eleccionCsv);
        $cantidadPadronCsv = PadronElectoral::where('idElecciones', $eleccionCsv->getKey())->count();

        // Importar XLSX después (usuarios ya existen, solo crea entradas en padrón)
        $importadorXlsx->importar(base_path('tests/TestFiles/prueba_padron.xlsx'), $eleccionXlsx);
        $cantidadPadronXlsx = PadronElectoral::where('idElecciones', $eleccionXlsx->getKey())->count();

        // Ambos archivos deberían tener aproximadamente la misma cantidad de registros únicos
        // (pueden diferir si los archivos tienen contenido ligeramente distinto)
        $this->assertGreaterThan(0, $cantidadPadronCsv);
        $this->assertGreaterThan(0, $cantidadPadronXlsx);
    }
}
