<?php

namespace Database\Factories;

use App\Models\EstadoElecciones;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Elecciones>
 */
class EleccionesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titulo' => $this->faker->sentence(3),
            'descripcion' => $this->faker->paragraph(),
            'fechaInicio' => $this->faker->dateTimeBetween('+1 days', '+1 month'),
            'fechaCierre' => $this->faker->dateTimeBetween('+2 months', '+3 months'),
            'idEstado' => EstadoElecciones::PROGRAMADO,
        ];
    }
}
