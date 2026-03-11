<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caja_diaria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('fecha');
            $table->decimal('monto', 12, 2);
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                  ->cascadeOnUpdate()->restrictOnDelete();

            $table->unique(['user_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caja_diaria');
    }
};
