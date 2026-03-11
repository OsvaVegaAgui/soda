<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_tiquetes', function (Blueprint $table) {
            $table->id('id_historial');
            $table->unsignedBigInteger('id_ticket');
            $table->unsignedBigInteger('user_id');
            $table->integer('cantidad_impresa');
            $table->timestamp('fecha_impresion')->useCurrent();

            $table->foreign('id_ticket')
                  ->references('id_ticket')->on('ticket')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_tiquetes');
    }
};
