<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lavado     = Category::where('name', 'Lavado')->first();
        $lubricacion = Category::where('name', 'Lubricación')->first();

        $servicios = [
            // Lavados
            ['name' => 'Lavada Completa',           'category_id' => $lavado->id,      'price' => 15.00, 'description' => 'Lavado exterior e interior completo del vehículo.'],
            ['name' => 'Lavada Express',             'category_id' => $lavado->id,      'price' => 8.00,  'description' => 'Lavado rápido exterior del vehículo.'],
            ['name' => 'Lavada Premium',             'category_id' => $lavado->id,      'price' => 25.00, 'description' => 'Lavado completo con encerado, aspirado y aromatizado.'],
            ['name' => 'Lavada de Motor',            'category_id' => $lavado->id,      'price' => 20.00, 'description' => 'Limpieza profunda del compartimento del motor.'],

            // Lubricaciones
            ['name' => 'Lubricación de Motor',          'category_id' => $lubricacion->id, 'price' => 30.00, 'description' => 'Cambio y lubricación completa del motor con aceite de calidad.'],
            ['name' => 'Lubricación de Caja de Cambios', 'category_id' => $lubricacion->id, 'price' => 35.00, 'description' => 'Cambio de aceite de caja de cambios manual o automática.'],
            ['name' => 'Lubricación de Corona',         'category_id' => $lubricacion->id, 'price' => 28.00, 'description' => 'Cambio de aceite de corona para transmisión trasera.'],
        ];

        foreach ($servicios as $servicio) {
            Service::create([
                'category_id' => $servicio['category_id'],
                'name'        => $servicio['name'],
                'description' => $servicio['description'],
                'price'       => $servicio['price'],
                'image'       => null,
                'active'      => true,
            ]);
        }
    }
}
