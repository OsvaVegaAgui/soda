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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->tinyInteger('rol'); // 1=Admin, 2=Usuario, etc.
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();

            // IMPORTANTE: tu tabla usa password en TEXTO PLANO en la screenshot,
            // pero Laravel usa HASH — te lo dejo como string normal.
            $table->string('password');

            // Tus columnas adicionales:
            $table->string('reset_token')->nullable();
            $table->timestamp('reset_token_date')->nullable();

            $table->boolean('activo')->default(1);

            $table->timestamps(); // created_at / updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
