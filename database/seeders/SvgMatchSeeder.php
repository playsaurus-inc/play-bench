<?php

namespace Database\Seeders;

use App\Models\AiModel;
use App\Models\SvgMatch;
use Illuminate\Database\Seeder;

class SvgMatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $models = AiModel::all();

        for ($i = 0; $i < 10; $i++) {
            $player1 = $models->random();
            $player2 = $models->except($player1->id)->random();

            SvgMatch::factory()
                ->withPlayers($player1, $player2)
                ->withFakeSvgs()
                ->create();
        }
    }
}
