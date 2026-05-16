<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_banners', function (Blueprint $table) {
            $table->boolean('es_principal')->default(false)->after('activo');
            $table->index(['empresa_id', 'es_principal']);
        });
    }

    public function down(): void
    {
        Schema::table('landing_banners', function (Blueprint $table) {
            $table->dropIndex(['empresa_id', 'es_principal']);
            $table->dropColumn('es_principal');
        });
    }
};

