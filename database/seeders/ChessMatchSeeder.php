<?php

namespace Database\Seeders;

use App\Models\AiModel;
use App\Models\ChessMatch;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;

class ChessMatchSeeder extends Seeder
{
    use WithFaker;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $models = AiModel::all();

        for ($i = 0; $i < 15; $i++) {
            $white = $models->random();
            $black = $models->except($white->id)->random();

            ChessMatch::factory()->withPlayers($white, $black)->create();
        }
    }
}
