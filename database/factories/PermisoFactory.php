<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permiso>
 */
class PermisoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'permiso' => fake()->unique()->randomElement([
                'gestion.usuarios',
                'gestion.roles',
                'gestion.permisos',
                'gestion.elecciones',
                'gestion.candidatos',
                'gestion.partidos',
                'auditoria.votos',
                'ver.reportes',
                'votar',
                'ver.resultados'
            ]),
        ];
    }
}
