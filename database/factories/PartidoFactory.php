<?php

namespace Database\Factories;

use App\Models\Partido;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartidoFactory extends Factory
{
    protected $model = Partido::class;

    public function definition(): array
    {
        $nombres = [
            ['nombre' => 'Sinergia Estudiantil', 'descripcion' => 'Somos un equipo multidisciplinario comprometido con potenciar el ecosistema emprendedor de Incubunt. Creemos en la fuerza de la unión entre facultades para crear líderes integrales y proyectos innovadores que impacten en la sociedad.'],
            ['nombre' => 'Impulso Universitario', 'descripcion' => 'Buscamos transformar Incubunt en un referente nacional de emprendimiento universitario. Nuestra gestión se centrará en la visibilidad externa, fortalecer las habilidades blandas de cada asociado y crear redes de networking estratégicas.'],
            ['nombre' => 'Nexo Emprendedor', 'descripcion' => 'La conexión es clave en el emprendimiento. Proponemos una gestión transparente y horizontal, donde cada área tenga voz y voto en las decisiones estratégicas desde el primer día, fomentando la colaboración interdisciplinaria.'],
        ];

        static $index = 0;
        $partido = $nombres[$index % count($nombres)];
        $index++;

        return [
            'partido' => $partido['nombre'],
            'urlPartido' => $this->faker->url(),
            'descripcion' => $partido['descripcion'],
            'tipo' => $this->faker->randomElement(['Político', 'Independiente']),
        ];
    }
}
