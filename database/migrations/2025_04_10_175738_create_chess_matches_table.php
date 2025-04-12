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
        Schema::create('chess_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('white_id')->index()->constrained('ai_models');
            $table->foreignId('black_id')->index()->constrained('ai_models');
            $table->foreignId('winner_id')->nullable()->index()->constrained('ai_models'); // Null if draw or not finished

            $table->unsignedInteger('ply_count'); // Must match the number of half-moves (ply) in the PGN

            $table->enum('result', ['white', 'black', 'draw']);

            $table->text('pgn'); // The PGN notation of the game
            $table->text('final_fen'); // Final board position

            $table->integer('illegal_moves_white')->default(0); // Number of illegal moves made by white
            $table->integer('illegal_moves_black')->default(0); // Number of illegal moves made by black
            $table->boolean('is_forced_completion')->default(false); // Whether the match was forced due to technical issues

            $table->timestamp('started_at')->nullable(); // When the match started
            $table->timestamp('ended_at')->nullable(); // When the match ended
            $table->timestamps(); // When the record was stored + updated
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chess_matches');
    }
};
