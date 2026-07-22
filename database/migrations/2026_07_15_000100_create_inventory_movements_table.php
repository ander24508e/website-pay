<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type', 30)->default('warehouse');
            $table->string('address')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['empresa_id', 'active']);
            $table->index(['empresa_id', 'is_default']);
        });

        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_location_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalog_item_variant_id')->constrained('catalog_item_variants')->cascadeOnDelete();
            $table->integer('quantity')->default(0);
            $table->unsignedInteger('min_stock')->default(0);
            $table->timestamps();

            $table->unique(['inventory_location_id', 'catalog_item_variant_id'], 'inventory_stock_location_variant_unique');
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('document')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index(['empresa_id', 'active']);
        });

        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('inventory_location_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('document_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('status', 30)->default('received');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['empresa_id', 'status']);
        });

        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalog_item_variant_id')->constrained('catalog_item_variants')->restrictOnDelete();
            $table->integer('quantity');
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('inventory_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_location_id')->constrained('inventory_locations')->restrictOnDelete();
            $table->foreignId('to_location_id')->constrained('inventory_locations')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference')->nullable();
            $table->string('status', 30)->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_transfer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalog_item_variant_id')->constrained('catalog_item_variants')->restrictOnDelete();
            $table->integer('quantity');
            $table->timestamps();
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

        Schema::create('inventory_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date_from');
            $table->date('date_to');
            $table->string('status')->default('closed');
            $table->unsignedInteger('variants_count')->default(0);
            $table->integer('total_units')->default(0);
            $table->decimal('total_value', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->unique(['empresa_id', 'date_from', 'date_to'], 'inventory_period_unique_range');
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_item_variant_id')->constrained('catalog_item_variants')->cascadeOnDelete();
            $table->foreignId('inventory_location_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('from_location_id')->nullable()->constrained('inventory_locations')->nullOnDelete();
            $table->foreignId('to_location_id')->nullable()->constrained('inventory_locations')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sale_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('purchase_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('purchase_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('inventory_transfer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('inventory_transfer_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('inventory_return_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('inventory_return_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('inventory_count_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('inventory_count_item_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['in', 'out', 'adjust']);
            $table->string('reason')->nullable();
            $table->string('reference')->nullable();
            $table->string('batch_number')->nullable();
            $table->date('expires_at')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->integer('stock_before')->nullable();
            $table->integer('stock_after')->nullable();
            $table->integer('balance_quantity')->nullable();
            $table->decimal('balance_unit_cost', 10, 2)->nullable();
            $table->decimal('balance_total_cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reversal_movement_id')->nullable()->constrained('inventory_movements')->nullOnDelete();
            $table->timestamps();

            $table->index(['catalog_item_variant_id', 'created_at']);
            $table->index(['inventory_location_id', 'created_at']);
            $table->index(['type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('inventory_periods');
        Schema::dropIfExists('inventory_count_items');
        Schema::dropIfExists('inventory_counts');
        Schema::dropIfExists('inventory_return_items');
        Schema::dropIfExists('inventory_returns');
        Schema::dropIfExists('inventory_transfer_items');
        Schema::dropIfExists('inventory_transfers');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('inventory_stocks');
        Schema::dropIfExists('inventory_locations');
    }
};
