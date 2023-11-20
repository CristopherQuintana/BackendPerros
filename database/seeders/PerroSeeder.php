<?php

namespace Database\Seeders;

use App\Models\Perro;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PerroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear 10 perros de prueba utilizando la fábrica
        Perro::factory()->count(10)->create();
    }
}
