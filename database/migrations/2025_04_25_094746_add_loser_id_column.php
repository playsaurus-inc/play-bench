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
        Schema::table('rps_matches', function (Blueprint $table) {
            $table->foreignId('loser_id')->nullable()->constrained('ai_models'); // null = tie
        });

        Schema::table('svg_matches', function (Blueprint $table) {
            $table->foreignId('loser_id')->nullable()->constrained('ai_models'); // null should not happen if everything works ok
        });

        Schema::table('chess_matches', function (Blueprint $table) {
            $table->foreignId('loser_id')->nullable()->constrained('ai_models'); // null = draw
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rps_matches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('loser_id');
        });

        Schema::table('svg_matches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('loser_id');
        });

        Schema::table('chess_matches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('loser_id');
        });
    }
};
