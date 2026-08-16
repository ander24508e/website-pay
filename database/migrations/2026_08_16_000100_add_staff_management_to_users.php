<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('active')->default(true)->after('foto_perfil');
            $table->foreignId('created_by')->nullable()->after('active')->constrained('users')->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->after('created_by')->constrained('users')->nullOnDelete();

            $table->index(['active', 'manager_id']);
        });

        Schema::create('user_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['target_user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_audits');

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['active', 'manager_id']);
            $table->dropConstrainedForeignId('manager_id');
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn('active');
        });
    }
};
