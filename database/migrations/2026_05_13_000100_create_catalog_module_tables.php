<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->string('business_model', 20)->default('services');
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['empresa_id', 'slug']);
            $table->index(['empresa_id', 'active']);
        });

        Schema::create('catalog_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalog_type_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['catalog_type_id', 'slug']);
            $table->index(['empresa_id', 'active']);
        });

        Schema::create('catalog_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalog_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalog_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('legacy_source_type')->nullable();
            $table->unsignedBigInteger('legacy_source_id')->nullable();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2)->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->string('image')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('featured')->default(false);
            $table->boolean('purchasable')->default(true);
            $table->boolean('reservable')->default(false);
            $table->boolean('uses_inventory')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['empresa_id', 'slug']);
            $table->index(['empresa_id', 'active']);
            $table->index(['catalog_type_id', 'active']);
            $table->index(['legacy_source_type', 'legacy_source_id']);
        });

        Schema::create('catalog_item_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_item_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->integer('stock')->nullable();
            $table->unsignedInteger('min_stock')->default(0);
            $table->boolean('active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['catalog_item_id', 'active']);
            $table->index(['sku']);
        });

        Schema::create('service_vehicle_type_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_item_id')->constrained('catalog_items')->cascadeOnDelete();
            $table->foreignId('vehicle_type_id')->constrained()->restrictOnDelete();
            $table->decimal('price', 10, 2)->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->timestamps();

            $table->unique(['catalog_item_id', 'vehicle_type_id'], 'service_vehicle_type_unique');
        });

        Schema::create('catalog_item_supplies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_item_id')->constrained('catalog_items')->cascadeOnDelete();
            $table->foreignId('catalog_item_variant_id')->constrained('catalog_item_variants')->restrictOnDelete();
            $table->decimal('quantity', 10, 3);
            $table->string('unit', 30)->nullable();
            $table->timestamps();

            $table->unique(['catalog_item_id', 'catalog_item_variant_id'], 'catalog_item_supplies_unique');
        });

        Schema::create('catalog_item_bundles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['empresa_id', 'slug']);
            $table->index(['empresa_id', 'active']);
        });

        Schema::create('catalog_item_bundle_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_item_bundle_id')->constrained('catalog_item_bundles')->cascadeOnDelete();
            $table->foreignId('catalog_item_id')->constrained('catalog_items')->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->unique(['catalog_item_bundle_id', 'catalog_item_id'], 'catalog_bundle_items_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_item_bundle_items');
        Schema::dropIfExists('catalog_item_bundles');
        Schema::dropIfExists('catalog_item_supplies');
        Schema::dropIfExists('service_vehicle_type_prices');
        Schema::dropIfExists('catalog_item_variants');
        Schema::dropIfExists('catalog_items');
        Schema::dropIfExists('catalog_categories');
        Schema::dropIfExists('catalog_types');
    }
};
