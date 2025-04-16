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
            $table->foreignId('player1_id')->index()->constrained('ai_models');
            $table->foreignId('player2_id')->index()->constrained('ai_models');
            $table->foreignId('winner_id')->index()->nullable()->constrained('ai_models'); // null = tie

            // String separated by spaces
            // - Each round is represented by 3 characters
            // The first character is the move of player 1.
            // The second character is the move of player 2.
            // The third character is the result of the round.
            // - r=rock, p=paper, s=scissors
            // - 1=player1 win, 2=player2 win, t=tie
            // - Example: `rrt ps2 r1s`
            // This means:
            // - Round 1: player 1 played rock, player 2 played rock, result is tie
            // - Round 2: player 1 played paper, player 2 played scissors, result is player 2 win
            // - Round 3: player 1 played rock, player 2 played scissors, result is player 1 win
            // - The move history is stored as a string to save space
            $table->string('move_history');

            // Automatically calculated fields from the move history
            $table->integer('rounds_played'); // [Auto] Must be the number of spaces in move_history + 1.
            $table->integer('player1_score'); // [Auto] Number of rounds won by player 1
            $table->integer('player2_score'); // [Auto] Number of rounds won by player 2
            $table->integer('player1_win_streak'); // [Auto] Number of rounds won by player 1 in a row
            $table->integer('player2_win_streak'); // [Auto] Number of rounds won by player 2 in a row
            $table->jsonb('player1_move_distribution'); // [Auto] Number of times player 1 played rock, paper or scissors
            $table->jsonb('player2_move_distribution'); // [Auto] Number of times player 2 played rock, paper or scissors

            // ELO
            $table->float('player1_elo_before')->nullable(); // ELO of player 1 before the match
            $table->float('player2_elo_before')->nullable(); // ELO of player 2 before the match
            $table->float('player1_elo_after')->nullable(); // ELO of player 1 after the match
            $table->float('player2_elo_after')->nullable(); // ELO of player 2 after the match

            $table->timestamp('started_at')->nullable(); // When the match started
            $table->timestamp('ended_at')->nullable(); // When the match ended
            $table->boolean('is_forced_completion')->default(false); // Whether the match was forced due to technical issues
            $table->timestamps(); // When the record was stored + updated
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
