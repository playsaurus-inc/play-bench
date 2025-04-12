<?php

namespace Database\Seeders;

use App\Models\AiModel;
use App\Models\RpsMatch;
use Illuminate\Database\Seeder;

class RpsMatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $models = AiModel::all();

        for ($i = 0; $i < 20; $i++) {
            $player1 = $models->random();
            $player2 = $models->except($player1->id)->random();

            RpsMatch::factory()
                ->withPlayers($player1, $player2)
                ->create();
        }
    }
}
