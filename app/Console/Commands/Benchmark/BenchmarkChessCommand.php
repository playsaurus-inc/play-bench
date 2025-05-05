<?php

namespace App\Console\Commands\Benchmark;

use App\Models\ChessMatch;
use App\Services\Chess\ChessBenchmarkService;
use App\Services\Chess\ChessGame;
use App\Services\Chess\ChessMove;
use App\Services\Chess\ChessPlayer;
use App\Services\EloRatingService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'benchmark:chess',
    description: 'Run chess benchmarks between AI models',
)]
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
     * The number of completed matches.
     */
    protected int $completedMatches = 0;

    /**
     * Execute the console command.
     */
    public function handle(
        ChessBenchmarkService $benchmarkService,
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

        $this->info('Starting Chess benchmark tests');

        $this->completedMatches = 0;

        while ($this->completedMatches < $matchCount) {
            // Randomly select two different models
            $whitePlayer = $aiModels->random();
            $blackPlayer = $aiModels->whereNotIn('id', [$whitePlayer->id])->random();

            $game = new ChessGame($whitePlayer, $blackPlayer);

            $this->reportGameStarted($game);

            try {
                $benchmarkService->runGame(
                    game: $game,
                    onMoveMade: fn (ChessGame $game, ChessPlayer $player, ChessMove $move) => $this->reportMove($game, $player, $move),
                    onIllegalMove: fn (ChessGame $game, ChessPlayer $player, ChessMove $move) => $this->reportIllegalMove($game, $player, $move)
                );
            } catch (Exception $e) {
                $this->reportError($e);

                continue;
            }

            $this->reportGameEnded($game);

            $this->createMatch($game);

            $eloService->updateChessEloRatings();

            $this->completedMatches++;
        }

        $this->info('All matches completed');

        return Command::SUCCESS;
    }

    /**
     * Report the start of a game.
     */
    protected function reportGameStarted(ChessGame $game): void
    {
        $this->info(sprintf(
            'Chess game started: %s (White) vs %s (Black)',
            $game->getWhitePlayer()->name,
            $game->getBlackPlayer()->name
        ));
    }

    /**
     * Report a move.
     */
    protected function reportMove(ChessGame $game, ChessPlayer $player, ChessMove $move): void
    {
        $this->info(sprintf(
            'Move %d: %s (%s) played %s',
            $game->getMoveCount(),
            $game->getPlayer($player)->name,
            $player->name(),
            $move
        ));
    }

    /**
     * Report an illegal move.
     */
    protected function reportIllegalMove(ChessGame $game, ChessPlayer $player, ChessMove $move): void
    {
        $this->warn(sprintf(
            'Illegal move: %s (%s) attempted %s',
            $game->getPlayer($player)->name,
            $player->name(),
            $move
        ));
    }

    /**
     * Report the end of a game.
     */
    protected function reportGameEnded(ChessGame $game): void
    {
        $result = match ($game->getResult()) {
            'white' => "{$game->getWhitePlayer()->name} (White)",
            'black' => "{$game->getBlackPlayer()->name} (Black)",
            default => 'Draw'
        };

        $this->info('Game completed');
        $this->info(sprintf('Result: %s, Moves: %d', $result, $game->getMoveCount()));

        if ($game->isForced()) {
            $this->warn('Game was forced to completion');
        }
    }

    /**
     * Report an error.
     */
    protected function reportError(Exception $exception): void
    {
        report($exception);

        $this->error('Error occurred while running the match:');
        $this->error($exception->getMessage());
        $this->error('Please check the logs for more details.');

        Log::error('Chess benchmark error', [
            'exception' => $exception->getMessage(),
        ]);
    }

    /**
     * Create a chess match from the game state.
     */
    protected function createMatch(ChessGame $game): ChessMatch
    {
        return ChessMatch::create([
            'white_id' => $game->getWhitePlayer()->id,
            'black_id' => $game->getBlackPlayer()->id,
            'winner_id' => null, // Will be set automatically by the model's saving logic
            'ply_count' => $game->getPlyCount(),
            'result' => $game->getResult(),
            'pgn' => $game->generatePgn(),
            'final_fen' => $game->getFen(),
            'is_forced_completion' => $game->isForced(),
            'started_at' => $game->getStartedAt(),
            'ended_at' => $game->getEndedAt(),
        ]);
    }
}
