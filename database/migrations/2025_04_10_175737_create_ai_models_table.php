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
            // Unique name inside our system. Does not need to match the name used in the API.
            // For example the AI Model name (e.g. 'o3-mini') with different configuration parameters
            // can be mapped to different entries in the `ai_models` table.
            // E.g. 'o3-mini-low' an 'o3-mini-high' can both map to 'o3-mini' in the API one with
            // `reasoning_effort` set to `low` and the other to `high`.
            $table->string('name')->unique();

            $table->string('slug')->unique(); // Only used for URLs

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
