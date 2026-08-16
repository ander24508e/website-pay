<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'assigned_to')) {
                $table->foreignId('assigned_to')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('orders', 'sale_id')) {
                $table->unsignedBigInteger('sale_id')->nullable()->after('assigned_to')->index();
            }

            if (! Schema::hasColumn('orders', 'work_status')) {
                $table->string('work_status')->default('pending')->after('status');
            }

            if (! Schema::hasColumn('orders', 'scheduled_at')) {
                $table->timestamp('scheduled_at')->nullable()->after('payphone_transaction_id');
            }

            if (! Schema::hasColumn('orders', 'arrived_at')) {
                $table->timestamp('arrived_at')->nullable()->after('scheduled_at');
            }

            if (! Schema::hasColumn('orders', 'started_at')) {
                $table->timestamp('started_at')->nullable()->after('arrived_at');
            }

            if (! Schema::hasColumn('orders', 'ready_at')) {
                $table->timestamp('ready_at')->nullable()->after('started_at');
            }

            if (! Schema::hasColumn('orders', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('ready_at');
            }

            if (! Schema::hasColumn('orders', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('completed_at');
            }

            if (! Schema::hasColumn('orders', 'work_notes')) {
                $table->text('work_notes')->nullable()->after('cancelled_at');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! $this->indexExists('orders', 'orders_work_status_scheduled_at_index')) {
                $table->index(['work_status', 'scheduled_at'], 'orders_work_status_scheduled_at_index');
            }

            if (! $this->indexExists('orders', 'orders_assigned_to_work_status_index')) {
                $table->index(['assigned_to', 'work_status'], 'orders_assigned_to_work_status_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if ($this->indexExists('orders', 'orders_assigned_to_work_status_index')) {
                $table->dropIndex('orders_assigned_to_work_status_index');
            }

            if ($this->indexExists('orders', 'orders_work_status_scheduled_at_index')) {
                $table->dropIndex('orders_work_status_scheduled_at_index');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'work_notes')) {
                $table->dropColumn('work_notes');
            }

            foreach (['cancelled_at', 'completed_at', 'ready_at', 'started_at', 'arrived_at', 'scheduled_at', 'work_status'] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('orders', 'sale_id')) {
                $table->dropColumn('sale_id');
            }

            if (Schema::hasColumn('orders', 'assigned_to')) {
                $table->dropConstrainedForeignId('assigned_to');
            }
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        return collect(Schema::getIndexes($table))
            ->contains(fn (array $definition) => ($definition['name'] ?? null) === $index);
    }
};
