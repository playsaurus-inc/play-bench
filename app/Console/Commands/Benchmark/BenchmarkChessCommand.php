<?php

namespace App\Console\Commands\Benchmark;

use App\Console\Concerns\RunsMatchups;
use App\Models\ChessMatch;
use App\Models\Contracts\RankedMatch;
use App\Services\Chess\ChessBenchmarkService;
use App\Services\Chess\ChessGame;
use App\Services\Chess\ChessMove;
use App\Services\Chess\ChessPlayer;
use App\Services\Matchup;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'benchmark:chess',
    description: 'Run chess benchmarks between AI models',
)]
class BenchmarkChessCommand extends Command
{
    use RunsMatchups;

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
    public function handle(): int
    {
        $this->runBenchmark('chess', function (Matchup $matchup): RankedMatch {
            $game = new ChessGame($matchup->player1, $matchup->player2);

            app(ChessBenchmarkService::class)->runGame(
                game: $game,
                onMoveMade: fn (ChessGame $game, ChessPlayer $player, ChessMove $move) => $this->reportMove($game, $player, $move),
                onIllegalMove: fn (ChessGame $game, ChessPlayer $player, ChessMove $move) => $this->reportIllegalMove($game, $player, $move)
            );

            return $this->createMatch($game);
        });

        return Command::SUCCESS;
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
     * Get extra information about the match.
     */
    protected function getExtraInfo(ChessMatch $match): array
    {
        return [
            '🔴 White player' => $match->whitePlayer->name,
            '🔵 Black player' => $match->blackPlayer->name,
            '♟️ Result' => $match->result,
            '♟️ Moves played' => $match->ply_count,
        ];
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
