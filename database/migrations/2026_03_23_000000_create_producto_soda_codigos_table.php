<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_soda_codigos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('producto_soda_id');
            $table->string('codigo_barras', 50)->unique();

            $table->foreign('producto_soda_id')
                  ->references('id_producto_soda')
                  ->on('productos_soda')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_soda_codigos');
    }
};
