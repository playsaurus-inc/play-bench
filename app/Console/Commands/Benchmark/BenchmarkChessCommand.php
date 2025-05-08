<?php

namespace App\Console\Commands\Benchmark;

use App\Console\Concerns\OrganizesMatchups;
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
    use OrganizesMatchups;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'benchmark:chess {--matches=1 : Number of chess matches to run}';

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

        $this->info('Starting Chess benchmark tests');

        $this->completedMatches = 0;

        while ($this->completedMatches < $matchCount) {
            $matchup = $this->matchup('chess');

            if (! $matchup) {
                $this->error('No matchup found. Exiting.');

                return Command::FAILURE;
            }

            $game = new ChessGame($matchup->player1, $matchup->player2);

            $this->reportGameStarted($game, $matchup->matchesPlayed);

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

            $this->showMatch($this->createMatch($game));

            $eloService->updateChessEloRatings();

            $this->completedMatches++;
        }

        $this->info('All matches completed');

        return Command::SUCCESS;
    }

    /**
     * Report the start of a game.
     */
    protected function reportGameStarted(ChessGame $game, int $matchesPlayed): void
    {
        $this->info(sprintf(
            'Chess game started: %s (White) vs %s (Black). Matches played together: %d',
            $game->getWhitePlayer()->name,
            $game->getBlackPlayer()->name,
            $matchesPlayed,
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

    /**
     * Show the match URL.
     */
    protected function showMatch(ChessMatch $match): void
    {
        // TO BE ADDED LATER: Show the match URL
        // $this->info('Match URL: '.route('chess.matches.show', $match->id));
    }
}
