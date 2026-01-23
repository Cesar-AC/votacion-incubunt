<?php

namespace Database\Factories;

use App\Models\Candidato;
use Illuminate\Database\Eloquent\Factories\Factory;

class CandidatoFactory extends Factory
{
    protected $model = Candidato::class;

    public function definition(): array
    {
        return [
            'idPartido' => 1, // Se sobrescribirá en el seeder
            'idCargo' => 1,   // Se sobrescribirá en el seeder
            'idUsuario' => 1, // Se sobrescribirá en el seeder
        ];
    }
}
