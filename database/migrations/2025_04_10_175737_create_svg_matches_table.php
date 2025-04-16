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
            $table->foreignId('player1_id')->index()->constrained('ai_models');
            $table->foreignId('player2_id')->index()->constrained('ai_models');
            $table->foreignId('winner_id')->index()->nullable()->constrained('ai_models'); // null should not happen if everything works ok
            $table->text('prompt'); // The image prompt given
            $table->string('player1_svg_path'); // Path to the SVG file from player 1
            $table->string('player2_svg_path'); // Path to the SVG file from player 2
            $table->text('judge_reasoning'); // Explanation of the winner, according to the judge AI

            // ELOs
            $table->float('player1_elo_before')->nullable(); // ELO of player 1 before the match
            $table->float('player2_elo_before')->nullable(); // ELO of player 2 before the match
            $table->float('player1_elo_after')->nullable(); // ELO of player 1 after the match
            $table->float('player2_elo_after')->nullable(); // ELO of player 2 after the match

            $table->integer('started_at')->nullable(); // When the match started
            $table->integer('ended_at')->nullable(); // When the match ended
            $table->timestamps(); // When the record was stored + updated
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
