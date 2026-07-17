<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('correo')->nullable();
            $table->string('eslogan')->nullable();
            $table->text('descripcion_corta')->nullable();
            $table->text('descripcion_footer')->nullable();
            $table->string('horario')->nullable();
            $table->string('servicios_resumen')->nullable();
            $table->text('ubicacion_embed')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->string('whatsapp_url')->nullable();
            $table->string('logo')->nullable();
            $table->string('color_primario', 7)->nullable();
            $table->string('color_secundario', 7)->nullable();
            $table->string('color_terciario', 7)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
