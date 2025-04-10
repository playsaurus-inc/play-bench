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
            $table->foreignId('white_ai_model_id')->constrained('ai_models');
            $table->foreignId('black_ai_model_id')->constrained('ai_models');
            $table->integer('moves_count');
            $table->enum('winner_color', ['white', 'black', 'draw']);
            $table->foreignId('winner_ai_model_id')->nullable()->constrained('ai_models');
            $table->text('pgn'); // The PGN notation of the game
            $table->string('final_fen'); // Final board position
            $table->integer('illegal_moves_white')->default(0);
            $table->integer('illegal_moves_black')->default(0);
            $table->boolean('is_forced_completion')->default(false);
            $table->timestamps();
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
