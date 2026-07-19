<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
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

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->foreignId('inventory_location_id')->nullable()->after('catalog_item_variant_id')->constrained()->nullOnDelete();
            $table->foreignId('from_location_id')->nullable()->after('inventory_location_id')->constrained('inventory_locations')->nullOnDelete();
            $table->foreignId('to_location_id')->nullable()->after('from_location_id')->constrained('inventory_locations')->nullOnDelete();
            $table->foreignId('purchase_id')->nullable()->after('order_item_id')->constrained()->nullOnDelete();
            $table->foreignId('purchase_item_id')->nullable()->after('purchase_id')->constrained()->nullOnDelete();
            $table->foreignId('inventory_transfer_id')->nullable()->after('purchase_item_id')->constrained()->nullOnDelete();
            $table->foreignId('inventory_transfer_item_id')->nullable()->after('inventory_transfer_id')->constrained()->nullOnDelete();
        });

        DB::table('empresas')->orderBy('id')->get(['id'])->each(function ($empresa) {
            $locationId = DB::table('inventory_locations')->insertGetId([
                'empresa_id' => $empresa->id,
                'name' => 'Bodega principal',
                'type' => 'warehouse',
                'is_default' => true,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $variants = DB::table('catalog_item_variants')
                ->join('catalog_items', 'catalog_item_variants.catalog_item_id', '=', 'catalog_items.id')
                ->where('catalog_items.empresa_id', $empresa->id)
                ->select([
                    'catalog_item_variants.id',
                    'catalog_item_variants.stock',
                    'catalog_item_variants.min_stock',
                ])
                ->get();

            foreach ($variants as $variant) {
                DB::table('inventory_stocks')->insert([
                    'inventory_location_id' => $locationId,
                    'catalog_item_variant_id' => $variant->id,
                    'quantity' => (int) ($variant->stock ?? 0),
                    'min_stock' => (int) ($variant->min_stock ?? 0),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('inventory_transfer_item_id');
            $table->dropConstrainedForeignId('inventory_transfer_id');
            $table->dropConstrainedForeignId('purchase_item_id');
            $table->dropConstrainedForeignId('purchase_id');
            $table->dropConstrainedForeignId('to_location_id');
            $table->dropConstrainedForeignId('from_location_id');
            $table->dropConstrainedForeignId('inventory_location_id');
        });

        Schema::dropIfExists('inventory_transfer_items');
        Schema::dropIfExists('inventory_transfers');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('inventory_stocks');
        Schema::dropIfExists('inventory_locations');
    }
};
