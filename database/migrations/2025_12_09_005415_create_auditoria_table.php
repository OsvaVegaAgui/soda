<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria', function (Blueprint $table) {
            $table->id('id_auditoria');
            $table->unsignedBigInteger('user_id');
            $table->string('tabla', 100);
            $table->unsignedBigInteger('registro_id');
            $table->string('accion', 20); // INSERT, UPDATE, DELETE
            $table->text('valores_antes')->nullable();  // null en INSERT
            $table->text('valores_despues')->nullable(); // null en DELETE
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria');
    }
};
