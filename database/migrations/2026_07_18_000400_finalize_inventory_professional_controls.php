<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->decimal('discount_total', 12, 2)->default(0)->after('subtotal');
            $table->decimal('tax_total', 12, 2)->default(0)->after('discount_total');
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->string('batch_number')->nullable()->after('reference');
            $table->date('expires_at')->nullable()->after('batch_number');
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

        $now = now();
        $permissions = [
            'inventory.view',
            'inventory.move',
            'inventory.void',
            'inventory.view_costs',
            'inventory.export',
            'inventory.close_periods',
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission, 'guard_name' => 'web'],
                ['created_at' => $now, 'updated_at' => $now]
            );
        }

        $adminRoleId = DB::table('roles')->where('name', 'admin')->where('guard_name', 'web')->value('id');

        if ($adminRoleId) {
            $permissionIds = DB::table('permissions')
                ->whereIn('name', $permissions)
                ->where('guard_name', 'web')
                ->pluck('id');

            foreach ($permissionIds as $permissionId) {
                DB::table('role_has_permissions')->updateOrInsert([
                    'role_id' => $adminRoleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_periods');

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropColumn(['batch_number', 'expires_at']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn(['discount_total', 'tax_total']);
        });

        DB::table('permissions')->whereIn('name', [
            'inventory.view',
            'inventory.move',
            'inventory.void',
            'inventory.view_costs',
            'inventory.export',
            'inventory.close_periods',
        ])->delete();
    }
};
