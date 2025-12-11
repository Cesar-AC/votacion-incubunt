<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\Carrera;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PerfilUsuario>
 */
class PerfilUsuarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'apellidoPaterno' => $this->faker->lastName(),
            'apellidoMaterno' => $this->faker->lastName(),
            'nombre' => $this->faker->firstName(),
            'otrosNombres' => $this->faker->firstName(),
            'telefono' => $this->faker->phoneNumber(),
            'dni' => $this->faker->unique()->numerify('##########'),
            'idCarrera' => Carrera::inRandomOrder()->first()->getKey(),
            'idArea' => Area::inRandomOrder()->first()->getKey(),
        ];
    }
}
