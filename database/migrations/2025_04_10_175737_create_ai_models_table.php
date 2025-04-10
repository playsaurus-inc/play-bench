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
        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('provider')->nullable(); // OpenAI, Anthropic, etc.
            $table->string('family')->nullable(); // GPT, Claude, etc.
            $table->integer('chess_elo_rating')->default(1600);
            $table->integer('rps_elo_rating')->default(1600);
            $table->integer('svg_elo_rating')->default(1600);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_models');
    }
};
