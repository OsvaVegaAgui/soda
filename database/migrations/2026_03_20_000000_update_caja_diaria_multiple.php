<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('caja_diaria', function (Blueprint $table) {
            // Primero soltar la FK para poder eliminar el índice único
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id', 'fecha']);

            // Re-agregar la FK (ahora sin el índice único compuesto)
            $table->foreign('user_id')->references('id')->on('users')
                  ->cascadeOnUpdate()->restrictOnDelete();

            // Nuevas columnas
            $table->boolean('cerrada')->default(false)->after('observacion');
            $table->timestamp('hora_apertura')->nullable()->after('cerrada');
            $table->timestamp('hora_cierre')->nullable()->after('hora_apertura');
        });

        // Inicializar hora_apertura con created_at para registros existentes
        DB::table('caja_diaria')->update(['hora_apertura' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('caja_diaria', function (Blueprint $table) {
            $table->dropColumn(['cerrada', 'hora_apertura', 'hora_cierre']);
            $table->unique(['user_id', 'fecha']);
        });
    }
};
