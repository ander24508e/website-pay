<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'tax_total')) {
                $table->decimal('tax_total', 10, 2)->default(0)->after('discount');
            }
        });

        Schema::table('sale_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_items', 'description_snapshot')) {
                $table->text('description_snapshot')->nullable()->after('type_snapshot');
            }
            if (!Schema::hasColumn('sale_items', 'code_snapshot')) {
                $table->string('code_snapshot')->nullable()->after('description_snapshot');
            }
            if (!Schema::hasColumn('sale_items', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('unit_price');
            }
            if (!Schema::hasColumn('sale_items', 'tax_rate')) {
                $table->decimal('tax_rate', 8, 4)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('sale_items', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('tax_rate');
            }
            if (!Schema::hasColumn('sale_items', 'total')) {
                $table->decimal('total', 10, 2)->default(0)->after('subtotal');
            }
        });

        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->string('method');
            $table->string('status')->default('pending');
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('received_amount', 10, 2)->nullable();
            $table->decimal('change_amount', 10, 2)->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('bank')->nullable();
            $table->string('reference')->nullable();
            $table->string('authorization_code')->nullable();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('sale_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_movements', 'sale_id')) {
                $table->foreignId('sale_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('inventory_movements', 'sale_item_id')) {
                $table->foreignId('sale_item_id')->nullable()->after('sale_id')->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            if (Schema::hasColumn('inventory_movements', 'sale_item_id')) {
                $table->dropConstrainedForeignId('sale_item_id');
            }
            if (Schema::hasColumn('inventory_movements', 'sale_id')) {
                $table->dropConstrainedForeignId('sale_id');
            }
        });

        Schema::dropIfExists('sale_audits');
        Schema::dropIfExists('sale_payments');

        Schema::table('sale_items', function (Blueprint $table) {
            foreach (['total', 'tax_amount', 'tax_rate', 'discount_amount', 'code_snapshot', 'description_snapshot'] as $column) {
                if (Schema::hasColumn('sale_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'tax_total')) {
                $table->dropColumn('tax_total');
            }
        });
    }
};
