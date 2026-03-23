<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
{
    $aceites = Category::where('name', 'Aceites de Motor')->first();
    $insumos = Category::where('name', 'Insumos')->first();

    $productos = [
        // Aceites
        ['name' => 'Aceite de Motor 20W50',  'category_id' => $aceites->id, 'provider' => 'Castrol',  'price' => 12.00, 'description' => 'Aceite mineral para motores de alta exigencia.'],
        ['name' => 'Aceite de Motor 40',     'category_id' => $aceites->id, 'provider' => 'Valvoline', 'price' => 10.00, 'description' => 'Aceite monogrado para motores de trabajo pesado.'],
        ['name' => 'Aceite de Motor 15W40',  'category_id' => $aceites->id, 'provider' => 'Mobil',     'price' => 14.00, 'description' => 'Aceite multigrado para motores diésel y gasolina.'],
        ['name' => 'Aceite de Caja',         'category_id' => $aceites->id, 'provider' => 'Shell',     'price' => 11.00, 'description' => 'Aceite para caja de cambios manual.'],
        ['name' => 'Aceite de Corona 90',    'category_id' => $aceites->id, 'provider' => 'Castrol',   'price' => 11.00, 'description' => 'Aceite para diferencial y corona trasera.'],

        // Insumos
        ['name' => 'Shampoo para Auto',      'category_id' => $insumos->id, 'provider' => 'Simoniz',   'price' => 5.00,  'description' => 'Shampoo concentrado para lavado de carrocería.'],
        ['name' => 'Cera de Pulido',         'category_id' => $insumos->id, 'provider' => 'Turtle Wax','price' => 8.00,  'description' => 'Cera protectora para pintura del vehículo.'],
        ['name' => 'Aromatizante',           'category_id' => $insumos->id, 'provider' => 'Proauto',   'price' => 3.00,  'description' => 'Aromatizante para interior del vehículo.'],
    ];

    foreach ($productos as $producto) {
        Product::create([
            'category_id' => $producto['category_id'],
            'name'        => $producto['name'],
            'provider'    => $producto['provider'],
            'description' => $producto['description'],
            'price'       => $producto['price'],
            'image'       => null,
            'active'      => true,
        ]);
    }
}
}
