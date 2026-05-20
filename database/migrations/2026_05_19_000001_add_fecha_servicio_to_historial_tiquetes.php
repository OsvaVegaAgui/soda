<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('historial_tiquetes', function (Blueprint $table) {
            $table->date('fecha_servicio')->nullable()->after('cantidad_impresa');
        });
    }

    public function down(): void
    {
        Schema::table('historial_tiquetes', function (Blueprint $table) {
            $table->dropColumn('fecha_servicio');
        });
    }
};
