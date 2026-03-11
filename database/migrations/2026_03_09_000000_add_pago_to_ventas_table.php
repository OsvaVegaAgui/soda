<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'mixto'])->nullable()->after('total');
            $table->decimal('monto_efectivo', 12, 2)->nullable()->after('metodo_pago');
            $table->decimal('vuelto', 12, 2)->nullable()->after('monto_efectivo');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['metodo_pago', 'monto_efectivo', 'vuelto']);
        });
    }
};
