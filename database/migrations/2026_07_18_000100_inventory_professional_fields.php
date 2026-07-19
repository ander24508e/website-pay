<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalog_item_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('catalog_item_variants', 'cost_price')) {
                $table->decimal('cost_price', 10, 2)->nullable()->after('price');
            }

            if (!Schema::hasColumn('catalog_item_variants', 'min_stock')) {
                $table->unsignedInteger('min_stock')->default(0)->after('stock');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'catalog_item_variant_id')) {
                $table->foreignId('catalog_item_variant_id')
                    ->nullable()
                    ->after('itemable_id')
                    ->constrained('catalog_item_variants')
                    ->nullOnDelete();
            }
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_movements', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('sale_item_id')->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('inventory_movements', 'order_item_id')) {
                $table->foreignId('order_item_id')->nullable()->after('order_id')->constrained()->nullOnDelete();
            }

            if (!Schema::hasColumn('inventory_movements', 'reason')) {
                $table->string('reason')->nullable()->after('type');
            }

            if (!Schema::hasColumn('inventory_movements', 'reference')) {
                $table->string('reference')->nullable()->after('reason');
            }

            if (!Schema::hasColumn('inventory_movements', 'voided_at')) {
                $table->timestamp('voided_at')->nullable()->after('notes');
            }

            if (!Schema::hasColumn('inventory_movements', 'voided_by')) {
                $table->foreignId('voided_by')->nullable()->after('voided_at')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('inventory_movements', 'reversal_movement_id')) {
                $table->foreignId('reversal_movement_id')
                    ->nullable()
                    ->after('voided_by')
                    ->constrained('inventory_movements')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_movements', 'reversal_movement_id')) {
                $table->dropConstrainedForeignId('reversal_movement_id');
            }
            if (Schema::hasColumn('inventory_movements', 'voided_by')) {
                $table->dropConstrainedForeignId('voided_by');
            }
            if (Schema::hasColumn('inventory_movements', 'voided_at')) {
                $table->dropColumn('voided_at');
            }
            if (Schema::hasColumn('inventory_movements', 'reference')) {
                $table->dropColumn('reference');
            }
            if (Schema::hasColumn('inventory_movements', 'reason')) {
                $table->dropColumn('reason');
            }
            if (Schema::hasColumn('inventory_movements', 'order_item_id')) {
                $table->dropConstrainedForeignId('order_item_id');
            }
            if (Schema::hasColumn('inventory_movements', 'order_id')) {
                $table->dropConstrainedForeignId('order_id');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'catalog_item_variant_id')) {
                $table->dropConstrainedForeignId('catalog_item_variant_id');
            }
        });

        Schema::table('catalog_item_variants', function (Blueprint $table) {
            if (Schema::hasColumn('catalog_item_variants', 'min_stock')) {
                $table->dropColumn('min_stock');
            }
            if (Schema::hasColumn('catalog_item_variants', 'cost_price')) {
                $table->dropColumn('cost_price');
            }
        });
    }
};
