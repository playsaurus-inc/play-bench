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
        Schema::create('svg_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player1_ai_model_id')->constrained('ai_models');
            $table->foreignId('player2_ai_model_id')->constrained('ai_models');
            $table->enum('winner', ['player1', 'player2', 'tie']);
            $table->text('prompt'); // The image prompt given
            $table->string('player1_svg_path'); // Path to the SVG file from player 1
            $table->string('player2_svg_path'); // Path to the SVG file from player 2
            $table->text('judge_reasoning'); // Explanation of the winner
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('svg_matches');
    }
};
