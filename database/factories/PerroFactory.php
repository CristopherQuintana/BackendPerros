<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Http;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Perro>
 */
class PerroFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Obtener URL de la API
        $response = Http::get('https://dog.ceo/api/breeds/image/random');
        $data = $response->json();

        return [
            'nombre'=> fake()->name(),
            'foto_url' => $data['message'],
            'descripcion' => fake()->text()
        ];
    }
}
