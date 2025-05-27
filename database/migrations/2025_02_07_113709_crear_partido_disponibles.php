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
        Schema::create('partido_disponibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partido_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->boolean('disponible')->default(false);
            $table->timestamps();
            $table->unique(['partido_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {        
        Schema::dropIfExists('partido_disponibles');
    }
};
