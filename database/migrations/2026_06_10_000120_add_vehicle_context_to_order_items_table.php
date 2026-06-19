<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('vehicle_id')->nullable()->after('itemable_id')->constrained()->nullOnDelete();
            $table->foreignId('vehicle_type_id')->nullable()->after('vehicle_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vehicle_type_id');
            $table->dropConstrainedForeignId('vehicle_id');
        });
    }
};
