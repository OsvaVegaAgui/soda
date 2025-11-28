<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket', function (Blueprint $table) {
            $table->integer('id_ticket')->autoIncrement();
            $table->string('nombre', 150);
            $table->string('codigo', 50)->unique();
            $table->integer('categoria_d');
            $table->integer('categoria_inst_id');
            $table->decimal('precio', 10, 2)->default(0);
            $table->integer('cantidad')->default(0);
            $table->timestamps();


            $table->index('categoria_id');
            $table->foreign('categoria_id')
                  ->references('id_categoria')->on('categoria_tiquetes')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->index('categoria_inst_id');
            $table->foreign('categoria_inst_id')
                  ->references('id_categoria_inst')->on('categoria_instituto')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
        });

        Schema::create('categoria_tiquetes', function (Blueprint $table) {
            $table->integer('id_categoria')->autoIncrement();
            $table->string('nombre', 100);
            $table->timestamps();
        });

        Schema::create('categoria_instituto', function (Blueprint $table) {
            $table->integer('id_categoria_inst')->autoIncrement();
            $table->string('nombre', 100);
            $table->timestamps();
        });

        DB::table('categoria_instituto')->insert([
            ['nombre' => 'Escuela', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Colegio', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Otros', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('categoria_instituto');

        Schema::dropIfExists('categoria_tiquetes');

        Schema::dropIfExists('ticket');
    }
};
