<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('vehicle_brands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('vehicle_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_brand_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['vehicle_brand_id', 'name']);
        });

        Schema::create('vehicle_specifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_brand_id')->constrained('vehicle_brands')->restrictOnDelete();
            $table->foreignId('vehicle_model_id')->constrained('vehicle_models')->restrictOnDelete();
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types')->restrictOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['vehicle_brand_id', 'vehicle_model_id', 'vehicle_type_id'], 'vehicle_specs_unique');
            $table->index(['active']);
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_specification_id')->constrained()->restrictOnDelete();
            $table->string('plate', 20)->unique();
            $table->string('color', 80)->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->text('observations')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('vehicle_specifications');
        Schema::dropIfExists('vehicle_models');
        Schema::dropIfExists('vehicle_brands');
        Schema::dropIfExists('vehicle_types');
    }
};
