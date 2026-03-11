<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket', function (Blueprint $table) {
            $table->id('id_ticket');
            $table->string('nombre', 150);
            $table->string('codigo', 50)->unique();
            $table->unsignedBigInteger('categoria_d');
            $table->decimal('precio', 10, 2)->default(0);
            $table->integer('cantidad')->default(0);
            $table->date('fecha');
            $table->timestamps();

            $table->foreign('categoria_d')
                  ->references('id_categoria')->on('categoria_tiquetes')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket');
    }
};
