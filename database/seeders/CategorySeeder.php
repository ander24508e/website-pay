<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Categorías de servicios
        Category::create(['name' => 'Lavado',      'type' => 'service']);
        Category::create(['name' => 'Lubricación', 'type' => 'service']);

        // Categorías de productos
        Category::create(['name' => 'Aceites de Motor', 'type' => 'product']);
        Category::create(['name' => 'Insumos',          'type' => 'product']);
    }
}
