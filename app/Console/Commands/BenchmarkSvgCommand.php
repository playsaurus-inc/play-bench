<?php

namespace App\Console\Commands;

use App\Services\EloRatingService;
use App\Services\Svg\SvgBenchmarkService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BenchmarkSvgCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'benchmark:svg {--matches=10 : Number of SVG matches to run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run SVG creation benchmarks between AI models';

    /**
     * Execute the console command.
     */
    public function handle(
        SvgBenchmarkService $benchmarkService,
        EloRatingService $eloService,
    ): int {
        $this->info('Starting SVG benchmark tests');

        $matchCount = (int) $this->option('matches');

        // Get all available AI models
        $aiModels = $benchmarkService->getAvailableModels();

        if ($aiModels->isEmpty()) {
            $this->error('No AI models found in the database. Please add some AI models first.');

            return Command::FAILURE;
        }

        $this->info(sprintf('Found %d AI models. Planning to run %d SVG matches.',
            $aiModels->count(),
            $matchCount
        ));

        // Ensure the SVG storage directory exists
        Storage::makeDirectory('svg');

        $completedMatches = 0;

        for ($i = 0; $i < $matchCount; $i++) {
            // Randomly select two different models
            $player1 = $aiModels->random();
            $player2 = $aiModels->whereNotIn('id', [$player1->id])->random();

            $this->info(sprintf('Match %d/%d: %s vs %s', $i + 1, $matchCount, $player1->name, $player2->name));

            try {
                $match = $benchmarkService->runMatch($player1, $player2);

                $winner = $match->winner;
                $winnerName = $winner ? $winner->name : 'Tie';

                $this->info(sprintf('Match completed: Winner: %s', $winnerName));
                $this->info(sprintf('Prompt: %s', $match->prompt));

                $completedMatches++;
            } catch (\Exception $e) {
                $this->error(sprintf('Error running match: %s', $e->getMessage()));
                Log::error('SVG benchmark error', [
                    'exception' => $e->getMessage(),
                    'player1' => $player1->name,
                    'player2' => $player2->name,
                ]);
                report($e);
            }
        }

        // Update ELO ratings
        $this->info('Updating ELO ratings...');
        $matchesUpdated = $eloService->updateSvgEloRatings();
        $this->info(sprintf('Updated ELO ratings for %d matches', $matchesUpdated));

        $this->info(sprintf('Successfully completed %d/%d matches', $completedMatches, $matchCount));

        return Command::SUCCESS;
    }
}
