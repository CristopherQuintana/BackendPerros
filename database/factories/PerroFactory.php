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
        try{
            $response = Http::get('https://dog.ceo/api/breeds/image/random');
            if($response->successful()){
                $data = $response->json();
            }
            if($response->failed()){
                return ["body"=>"fallo de informacion", "status"=> $response->status()];

            }
            if($response->clientError()){
                return ["body"=>" fallo de comunicacion", "status"=> $response->status()];
            }
        }
        catch (Exception $e) {
            return response()->json([
                "error" => $e->getMessage(),
                 "linea"=> $e->getLine(), 
                 "file"=> $e->getFile(),
                 "metodo"=> __METHOD__
            ], Response::HTTP_BAD_REQUEST);
        }

        return [
            'nombre'=> fake()->name(),
            'foto_url' => $data['message'],
            'descripcion' => fake()->text()
        ];
    }
}
