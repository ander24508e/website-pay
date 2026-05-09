<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('presentation')->nullable();
            $table->string('specification')->nullable();
            $table->string('sku')->nullable()->unique();
            $table->decimal('price', 8, 2);
            $table->unsignedInteger('stock')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['product_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};

