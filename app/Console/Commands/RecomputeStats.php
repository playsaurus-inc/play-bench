<?php

namespace App\Console\Commands;

use App\Models\RpsMatch;
use Illuminate\Console\Command;

class RecomputeStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:recompute-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recompute statistics for all matches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->components->task(
            'Recomputing Rock-Paper-Scissors Matches',
            fn () => RpsMatch::with('player1', 'player2')->get()->each->recompute(),
        );

        $this->components->task(
            'Recomputing Elo Ratings',
            fn () => $this->callSilently('calculate:elo'),
        );
    }
}
