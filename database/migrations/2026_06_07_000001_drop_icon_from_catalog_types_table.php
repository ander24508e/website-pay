<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('catalog_types', 'icon')) {
            return;
        }

        Schema::table('catalog_types', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('catalog_types', 'icon')) {
            return;
        }

        Schema::table('catalog_types', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('slug');
        });
    }
};
