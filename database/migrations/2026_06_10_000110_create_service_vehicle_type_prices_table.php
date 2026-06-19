<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_vehicle_type_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_type_id')->constrained()->restrictOnDelete();
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->unique(['catalog_item_id', 'vehicle_type_id'], 'service_vehicle_type_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_vehicle_type_prices');
    }
};
