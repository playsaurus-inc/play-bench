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
        Schema::table('svg_matches', function (Blueprint $table) {
            $table->json('player1_features')->nullable(); // Features of player 1 SVG
            $table->json('player2_features')->nullable(); // Features of player 2 SVG
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('svg_matches', function (Blueprint $table) {
            $table->dropColumn('player1_features');
            $table->dropColumn('player2_features');
        });
    }
};
