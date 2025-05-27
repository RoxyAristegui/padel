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
        Schema::create('partidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained();
            $table->integer('jornada');
            // $table->string('fecha');
            $table->timestamp('horario');
            $table->boolean('editado')->nullable()->default(false);
            $table->string('club');
            $table->string('rival');
            $table->timestamps();
            $table->boolean('completado')->default(false);
            $table->unique(['team_id', 'jornada']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {        
        Schema::dropIfExists('partidos');
    }
};
