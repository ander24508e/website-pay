<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->decimal('unit_cost', 10, 2)->nullable()->after('quantity');
            $table->decimal('total_cost', 10, 2)->nullable()->after('unit_cost');
            $table->integer('balance_quantity')->nullable()->after('stock_after');
            $table->decimal('balance_unit_cost', 10, 2)->nullable()->after('balance_quantity');
            $table->decimal('balance_total_cost', 10, 2)->nullable()->after('balance_unit_cost');
        });

        Schema::create('inventory_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_location_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 30);
            $table->string('reference')->nullable();
            $table->string('status', 30)->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_return_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalog_item_variant_id')->constrained('catalog_item_variants')->restrictOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_counts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_location_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference')->nullable();
            $table->string('status', 30)->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_count_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_count_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalog_item_variant_id')->constrained('catalog_item_variants')->restrictOnDelete();
            $table->integer('expected_quantity');
            $table->integer('counted_quantity');
            $table->integer('difference_quantity');
            $table->timestamps();
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->foreignId('inventory_return_id')->nullable()->after('inventory_transfer_item_id')->constrained()->nullOnDelete();
            $table->foreignId('inventory_return_item_id')->nullable()->after('inventory_return_id')->constrained()->nullOnDelete();
            $table->foreignId('inventory_count_id')->nullable()->after('inventory_return_item_id')->constrained()->nullOnDelete();
            $table->foreignId('inventory_count_item_id')->nullable()->after('inventory_count_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('inventory_count_item_id');
            $table->dropConstrainedForeignId('inventory_count_id');
            $table->dropConstrainedForeignId('inventory_return_item_id');
            $table->dropConstrainedForeignId('inventory_return_id');
        });

        Schema::dropIfExists('inventory_count_items');
        Schema::dropIfExists('inventory_counts');
        Schema::dropIfExists('inventory_return_items');
        Schema::dropIfExists('inventory_returns');

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropColumn([
                'balance_total_cost',
                'balance_unit_cost',
                'balance_quantity',
                'total_cost',
                'unit_cost',
            ]);
        });
    }
};
