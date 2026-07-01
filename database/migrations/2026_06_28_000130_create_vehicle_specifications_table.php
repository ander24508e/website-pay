<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vehicle_specifications')) {
            Schema::create('vehicle_specifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vehicle_brand_id')->constrained('vehicle_brands')->restrictOnDelete();
                $table->foreignId('vehicle_model_id')->constrained('vehicle_models')->restrictOnDelete();
                $table->foreignId('vehicle_type_id')->constrained('vehicle_types')->restrictOnDelete();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('active')->default(true);
                $table->timestamps();

                $table->unique(['vehicle_brand_id', 'vehicle_model_id', 'vehicle_type_id'], 'vehicle_specs_unique');
                $table->index(['active', 'sort_order']);
            });
        }

        if (Schema::hasTable('vehicles') && !Schema::hasColumn('vehicles', 'vehicle_specification_id')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->foreignId('vehicle_specification_id')
                    ->nullable()
                    ->after('vehicle_type_id')
                    ->constrained('vehicle_specifications')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasTable('vehicles') && Schema::hasTable('vehicle_specifications')) {
            DB::table('vehicles')
                ->whereNull('vehicle_specification_id')
                ->whereNotNull('vehicle_brand_id')
                ->whereNotNull('vehicle_model_id')
                ->whereNotNull('vehicle_type_id')
                ->orderBy('id')
                ->get(['id', 'vehicle_brand_id', 'vehicle_model_id', 'vehicle_type_id'])
                ->each(function ($vehicle) {
                    $specification = DB::table('vehicle_specifications')
                        ->where('vehicle_brand_id', $vehicle->vehicle_brand_id)
                        ->where('vehicle_model_id', $vehicle->vehicle_model_id)
                        ->where('vehicle_type_id', $vehicle->vehicle_type_id)
                        ->first(['id']);

                    if (!$specification) {
                        $specificationId = DB::table('vehicle_specifications')->insertGetId([
                            'vehicle_brand_id' => $vehicle->vehicle_brand_id,
                            'vehicle_model_id' => $vehicle->vehicle_model_id,
                            'vehicle_type_id' => $vehicle->vehicle_type_id,
                            'sort_order' => 0,
                            'active' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        $specificationId = $specification->id;
                    }

                    DB::table('vehicles')
                        ->where('id', $vehicle->id)
                        ->update(['vehicle_specification_id' => $specificationId]);
                });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vehicles') && Schema::hasColumn('vehicles', 'vehicle_specification_id')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropConstrainedForeignId('vehicle_specification_id');
            });
        }

        Schema::dropIfExists('vehicle_specifications');
    }
};
