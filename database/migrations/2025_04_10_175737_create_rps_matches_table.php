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
        Schema::create('rps_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player1_ai_model_id')->constrained('ai_models');
            $table->foreignId('player2_ai_model_id')->constrained('ai_models');
            $table->integer('rounds_played');
            $table->integer('player1_score');
            $table->integer('player2_score');
            $table->enum('winner', ['player1', 'player2', 'tie']);
            $table->json('move_history'); // Store the sequence of moves
            $table->boolean('is_forced_completion')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rps_matches');
    }
};
