<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auditoria', function (Blueprint $table) {
            $table->integer('idAuditoria')->autoIncrement();
            $table->integer('user_id');
            $table->string('tabla', 100);
            $table->integer('registro_id');
            $table->string('accion', 100);
            $table->string('valores_antes', 100);
            $table->string('valores_despues', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditoria');
    }
};
