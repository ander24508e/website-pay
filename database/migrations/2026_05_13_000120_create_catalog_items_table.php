<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
            $table->string('image')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('featured')->default(false);
            $table->boolean('purchasable')->default(true);
            $table->boolean('reservable')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['empresa_id', 'slug']);
            $table->index(['empresa_id', 'active']);
            $table->index(['catalog_type_id', 'active']);
            $table->index(['legacy_source_type', 'legacy_source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_items');
    }
};
