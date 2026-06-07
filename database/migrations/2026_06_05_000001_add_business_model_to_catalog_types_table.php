<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalog_types', function (Blueprint $table) {
            $table->string('business_model', 20)->default('services')->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('catalog_types', function (Blueprint $table) {
            $table->dropColumn('business_model');
        });
    }
};
