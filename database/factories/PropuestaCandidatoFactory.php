<?php

namespace Database\Factories;

use App\Models\PropuestaCandidato;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropuestaCandidatoFactory extends Factory
{
    protected $model = PropuestaCandidato::class;

    public function definition(): array
    {
        return [
            'propuesta' => $this->faker->sentence(6),
            'descripcion' => $this->faker->paragraph(),
            'idCandidato' => 1, // Se sobrescribirá en el seeder
        ];
    }
}
