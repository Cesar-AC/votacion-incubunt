<?php

namespace Database\Factories;

use App\Models\PropuestaPartido;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropuestaPartidoFactory extends Factory
{
    protected $model = PropuestaPartido::class;

    public function definition(): array
    {
        return [
            'propuesta' => $this->faker->sentence(6),
            'descripcion' => $this->faker->paragraph(),
            'idPartido' => 1, // Se sobrescribirá en el seeder
        ];
    }
}
