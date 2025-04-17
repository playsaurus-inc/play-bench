<?php

namespace App\Console\Commands;

use App\Models\AiModel;
use App\Models\ChessMatch;
use App\Services\AiClientService;
use App\Services\ChessBenchmarkService;
use App\Services\EloRatingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BenchmarkChessCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'benchmark:chess {--matches=10 : Number of chess matches to run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run chess benchmarks between AI models';

    /**
     * Execute the console command.
     */
    public function handle(
        ChessBenchmarkService $benchmarkService,
        EloRatingService $eloService,
    ): int {
        $this->info('Starting Chess benchmark tests');

        $matchCount = (int) $this->option('matches');

        // Get all available AI models
        $aiModels = $benchmarkService->getAvailableModels();

        if ($aiModels->isEmpty()) {
            $this->error('No AI models found in the database. Please add some AI models first.');
            return Command::FAILURE;
        }

        $this->info(sprintf('Found %d AI models. Planning to run %d chess matches.',
            $aiModels->count(),
            $matchCount
        ));

        $completedMatches = 0;

        for ($i = 0; $i < $matchCount; $i++) {
            // Randomly select two different models
            $whitePlayer = $aiModels->random();
            $blackPlayer = $aiModels->whereNotIn('id', [$whitePlayer->id])->random();

            $this->info(sprintf('Match %d/%d: %s (White) vs %s (Black)',
                $i + 1,
                $matchCount,
                $whitePlayer->name,
                $blackPlayer->name
            ));

            try {
                $match = $benchmarkService->runMatch($whitePlayer, $blackPlayer);

                $result = match($match->result) {
                    'white' => "{$whitePlayer->name} (White)",
                    'black' => "{$blackPlayer->name} (Black)",
                    default => "Draw"
                };

                $this->info(sprintf('Match completed: Result: %s, Moves: %d',
                    $result,
                    $match->getMoveCount()
                ));

                if ($match->illegal_moves_white > 0 || $match->illegal_moves_black > 0) {
                    $this->info(sprintf('Illegal moves: White: %d, Black: %d',
                        $match->illegal_moves_white,
                        $match->illegal_moves_black
                    ));
                }

                $completedMatches++;
            } catch (\Exception $e) {
                $this->error(sprintf('Error running match: %s', $e->getMessage()));
                Log::error('Chess benchmark error', [
                    'exception' => $e->getMessage(),
                    'white' => $whitePlayer->name,
                    'black' => $blackPlayer->name,
                ]);
                report($e);
            }
        }

        // Update ELO ratings
        $this->info('Updating ELO ratings...');
        $matchesUpdated = $eloService->updateChessEloRatings();
        $this->info(sprintf('Updated ELO ratings for %d matches', $matchesUpdated));

        $this->info(sprintf('Successfully completed %d/%d matches', $completedMatches, $matchCount));
        return Command::SUCCESS;
    }
}
