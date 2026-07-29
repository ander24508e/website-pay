<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_banners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->string('etiqueta')->nullable();
            $table->string('titulo')->nullable();
            $table->text('texto')->nullable();
            $table->string('imagen')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->boolean('es_principal')->default(false);
            $table->timestamps();

            $table->index(['empresa_id', 'es_principal', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_banners');
    }
};
