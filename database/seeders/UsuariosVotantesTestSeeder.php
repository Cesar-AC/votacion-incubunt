<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Carrera;
use App\Models\Elecciones;
use App\Models\EstadoUsuario;
use App\Models\PadronElectoral;
use App\Models\PerfilUsuario;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariosVotantesTestSeeder extends Seeder
{
    /**
     * Crea usuarios de prueba para poder votar
     * Cada usuario tiene credenciales fáciles de recordar
     */
    public function run(): void
    {
        // Obtener la elección activa
        $eleccion = Elecciones::first();
        if (!$eleccion) {
            $this->command->error('No hay elección disponible.');
            return;
        }

        $estadoActivo = EstadoUsuario::where('nombre', 'Activo')->first();
        $rolVotante = Rol::where('rol', 'votante')->first();
        
        $areas = Area::where('idArea', '!=', Area::PRESIDENCIA)
            ->where('idArea', '!=', Area::SIN_AREA_ASIGNADA)
            ->get();
        
        $carreras = Carrera::all();

        if ($areas->isEmpty() || $carreras->isEmpty()) {
            $this->command->error('No hay áreas o carreras disponibles.');
            return;
        }

        // Lista de usuarios de prueba con nombres reales
        $usuariosPrueba = [
            [
                'nombre' => 'Juan',
                'apellidoPaterno' => 'Pérez',
                'apellidoMaterno' => 'García',
                'correo' => 'juan@test.com',
                'dni' => '70000001',
                'telefono' => '987654321',
            ],
            [
                'nombre' => 'María',
                'apellidoPaterno' => 'González',
                'apellidoMaterno' => 'López',
                'correo' => 'maria@test.com',
                'dni' => '70000002',
                'telefono' => '987654322',
            ],
            [
                'nombre' => 'Carlos',
                'apellidoPaterno' => 'Rodríguez',
                'apellidoMaterno' => 'Martínez',
                'correo' => 'carlos@test.com',
                'dni' => '70000003',
                'telefono' => '987654323',
            ],
            [
                'nombre' => 'Ana',
                'apellidoPaterno' => 'Fernández',
                'apellidoMaterno' => 'Sánchez',
                'correo' => 'ana@test.com',
                'dni' => '70000004',
                'telefono' => '987654324',
            ],
            [
                'nombre' => 'Luis',
                'apellidoPaterno' => 'Díaz',
                'apellidoMaterno' => 'Torres',
                'correo' => 'luis@test.com',
                'dni' => '70000005',
                'telefono' => '987654325',
            ],
            [
                'nombre' => 'Carmen',
                'apellidoPaterno' => 'Morales',
                'apellidoMaterno' => 'Ruiz',
                'correo' => 'carmen@test.com',
                'dni' => '70000006',
                'telefono' => '987654326',
            ],
            [
                'nombre' => 'Pedro',
                'apellidoPaterno' => 'Jiménez',
                'apellidoMaterno' => 'Vargas',
                'correo' => 'pedro@test.com',
                'dni' => '70000007',
                'telefono' => '987654327',
            ],
            [
                'nombre' => 'Laura',
                'apellidoPaterno' => 'Castro',
                'apellidoMaterno' => 'Mendoza',
                'correo' => 'laura@test.com',
                'dni' => '70000008',
                'telefono' => '987654328',
            ],
            [
                'nombre' => 'Roberto',
                'apellidoPaterno' => 'Herrera',
                'apellidoMaterno' => 'Ramírez',
                'correo' => 'roberto@test.com',
                'dni' => '70000009',
                'telefono' => '987654329',
            ],
            [
                'nombre' => 'Patricia',
                'apellidoPaterno' => 'Silva',
                'apellidoMaterno' => 'Cruz',
                'correo' => 'patricia@test.com',
                'dni' => '70000010',
                'telefono' => '987654330',
            ],
        ];

        $this->command->info("\n╔═══════════════════════════════════════════════════════╗");
        $this->command->info("║   CREANDO USUARIOS DE PRUEBA PARA VOTACIÓN          ║");
        $this->command->info("╚═══════════════════════════════════════════════════════╝\n");

        foreach ($usuariosPrueba as $index => $userData) {
            // Verificar si el usuario ya existe
            $existingUser = User::where('correo', $userData['correo'])->first();
            
            if ($existingUser) {
                $this->command->warn("⚠ Usuario {$userData['correo']} ya existe, omitiendo...");
                continue;
            }

            // Crear usuario
            $user = User::create([
                'correo' => $userData['correo'],
                'contraseña' => Hash::make('password'), // Todos tienen la misma contraseña: "password"
                'idEstadoUsuario' => $estadoActivo->idEstadoUsuario,
            ]);

            // Asignar rol de votante
            $user->roles()->attach($rolVotante->idRol);

            // Crear perfil
            $area = $areas->random();
            $carrera = $carreras->random();
            
            PerfilUsuario::create([
                'idUser' => $user->idUser,
                'nombre' => $userData['nombre'],
                'apellidoPaterno' => $userData['apellidoPaterno'],
                'apellidoMaterno' => $userData['apellidoMaterno'],
                'dni' => $userData['dni'],
                'telefono' => $userData['telefono'],
                'idArea' => $area->idArea,
                'idCarrera' => $carrera->idCarrera,
                'ciclo' => rand(1, 10),
            ]);

            // Agregar al padrón electoral
            PadronElectoral::create([
                'idElecciones' => $eleccion->idElecciones,
                'idUsuario' => $user->idUser,
                'fechaVoto' => null,
            ]);

            $this->command->info("✓ Usuario creado: {$userData['nombre']} {$userData['apellidoPaterno']}");
            $this->command->info("  Correo: {$userData['correo']}");
            $this->command->info("  Contraseña: password");
            $this->command->info("  Área: {$area->area}");
            $this->command->info("  Carrera: {$carrera->carrera}\n");
        }

        $this->command->info("\n╔═══════════════════════════════════════════════════════╗");
        $this->command->info("║              RESUMEN DE CREDENCIALES                 ║");
        $this->command->info("╠═══════════════════════════════════════════════════════╣");
        $this->command->info("║                                                      ║");
        
        foreach ($usuariosPrueba as $userData) {
            $this->command->info("║  • {$userData['correo']}");
        }
        
        $this->command->info("║                                                      ║");
        $this->command->info("║  Contraseña para todos: password                    ║");
        $this->command->info("║                                                      ║");
        $this->command->info("╚═══════════════════════════════════════════════════════╝\n");
    }
}