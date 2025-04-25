<?php

namespace App\Console\Commands;

use App\Models\RpsMatch;
use App\Services\EloRatingService;
use App\Services\Rps\RpsBenchmarkService;
use App\Services\Rps\RpsGame;
use App\Services\Rps\RpsRound;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BenchmarkRpsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'benchmark:rps {--matches=10 : Number of matches to run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Rock Paper Scissors benchmarks between AI models';

    /**
     * The number of completed matches.
     */
    protected int $completedMatches = 0;

    /**
     * Execute the console command.
     */
    public function handle(
        RpsBenchmarkService $benchmarkService,
        EloRatingService $eloService,
    ): int {
        $matchCount = (int) $this->option('matches');
        $matchCount = $matchCount > 0 ? $matchCount : PHP_INT_MAX;

        // Get all available AI models
        $aiModels = $benchmarkService->getAvailableModels();

        if ($aiModels->isEmpty()) {
            $this->error('No AI models found in the database. Please add some AI models first.');

            return Command::FAILURE;
        }

        $this->info('Starting Rock Paper Scissors benchmarks');

        $this->completedMatches = 0;

        while ($this->completedMatches < $matchCount) {
            // Randomly select two different models
            $player1 = $aiModels->random();
            $player2 = $aiModels->whereNotIn('id', [$player1->id])->random();

            $game = new RpsGame($player1, $player2);

            $this->reportGameStarted($game);

            try {
                $benchmarkService->runGame(
                    game: $game,
                    onRoundComplete: fn(RpsRound $round) => $this->reportRound($game, $round),
                );
            } catch (\Exception $e) {
                $this->reportError($e);
                continue;
            }

            $this->reportGameEnded($game);

            $this->createMatch($game);

            $eloService->updateRpsEloRatings();

            $this->completedMatches++;
        }

        $this->info('All matches completed');

        return Command::SUCCESS;
    }

    /**
     * Report the result of a round
     */
    protected function reportRound(RpsGame $game, RpsRound $round): void
    {
        $this->info(sprintf(
            'Round %d: %s vs %s (%s)',
            $game->getRoundCount(),
            $round->player1Move->name(),
            $round->player2Move->name(),
            $round->result->name(),
        ));
    }

    /**
     * Report an error while running the match
     */
    protected function reportError(Exception $exception): void
    {
        report($exception);

        $this->error('Error occurred while running the match:');
        $this->error($exception->getMessage());
        $this->error('Please check the logs for more details.');

        Log::error('RPS benchmark error', [
            'exception' => $exception->getMessage(),
        ]);
    }

    /**
     * Report the start of a game.
     */
    protected function reportGameStarted(RpsGame $game): void
    {
        $this->info(sprintf(
            'Game started between %s and %s',
            $game->getPlayer1()->name,
            $game->getPlayer2()->name,
        ));
    }

    /**
     * Report the end of a game.
     */
    protected function reportGameEnded(RpsGame $game): void
    {
        $this->info('Match completed');
        $this->table(
            ['Player', 'Score'],
            [
                [$game->getPlayer1()->name, $game->getPlayer1Score()],
                [$game->getPlayer2()->name, $game->getPlayer2Score()],
            ],
        );
    }

    /**
     * Creates a new RPS match instance from the game state
     */
    protected function createMatch(RpsGame $game): RpsMatch
    {
        // Remaining properties will be inferred automatically by the model's saving logic
        return RpsMatch::create([
            'player1_id' => $game->getPlayer1()->id,
            'player2_id' => $game->getPlayer2()->id,
            'started_at' => $game->getStartedAt(),
            'ended_at' => $game->getEndedAt(),
            'move_history' => $game->getRoundHistory(),
            'is_forced_completion' => !$game->isOver(),
        ]);
    }
}
