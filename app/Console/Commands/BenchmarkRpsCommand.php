<?php

namespace App\Console\Commands;

use App\Services\EloRatingService;
use App\Services\Rps\RpsBenchmarkService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Helper\TableStyle;

class BenchmarkRpsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'benchmark:rps {--rounds=50 : Number of rounds to play in each match} {--matches=10 : Number of matches to run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Rock Paper Scissors benchmarks between AI models';

    /**
     * Execute the console command.
     */
    public function handle(
        RpsBenchmarkService $benchmarkService,
        EloRatingService $eloService,
    ): int {
        $this->info('Starting Rock Paper Scissors benchmarks');

        $rounds = (int) $this->option('rounds');
        $matchCount = (int) $this->option('matches');

        // Get all available AI models
        $aiModels = $benchmarkService->getAvailableModels();

        if ($aiModels->isEmpty()) {
            $this->error('No AI models found in the database. Please add some AI models first.');

            return Command::FAILURE;
        }

        $this->info(sprintf('Found %d AI models. Planning to run %d matches with %d rounds each.',
            $aiModels->count(),
            $matchCount,
            $rounds
        ));

        $completedMatches = 0;

        for ($i = 0; $i < $matchCount; $i++) {
            // Randomly select two different models
            $player1 = $aiModels->random();
            $player2 = $aiModels->whereNotIn('id', [$player1->id])->random();

            $this->info(sprintf('Match %d/%d: %s vs %s', $i + 1, $matchCount, $player1->name, $player2->name));

            try {
                $match = $benchmarkService->runMatch($player1, $player2);

                $this->info('Match completed');
                $this->table(
                    headers: ['Player', 'Score'],
                    rows: [
                        [$player1->name, $match->player1_score],
                        [$player2->name, $match->player2_score],
                    ],
                );

                $completedMatches++;
            } catch (\Exception $e) {
                $this->error(sprintf('Error running match: %s', $e->getMessage()));
                Log::error('RPS benchmark error', [
                    'exception' => $e->getMessage(),
                    'player1' => $player1->name,
                    'player2' => $player2->name,
                ]);
                report($e);
            }
        }

        // Update ELO ratings
        $this->info('Updating ELO ratings...');
        $matchesUpdated = $eloService->updateRpsEloRatings();
        $this->info(sprintf('Updated ELO ratings for %d matches', $matchesUpdated));

        $this->info(sprintf('Successfully completed %d/%d matches', $completedMatches, $matchCount));

        return Command::SUCCESS;
    }
}
