<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('catalog_types') && Schema::hasColumn('catalog_types', 'sort_order')) {
            Schema::table('catalog_types', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('catalog_types') && ! Schema::hasColumn('catalog_types', 'sort_order')) {
            Schema::table('catalog_types', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0)->after('business_model');
            });
        }
    }
};
