<?php

namespace App\Console\Commands\Benchmark;

use App\Console\Concerns\RunsMatchups;
use App\Models\RpsMatch;
use App\Services\Matchup;
use App\Services\Rps\RpsBenchmarkService;
use App\Services\Rps\RpsGame;
use App\Services\Rps\RpsRound;
use App\Services\Rps\RpsRoundResult;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'benchmark:rps',
    description: 'Run Rock Paper Scissors benchmarks between AI models',
)]
class BenchmarkRpsCommand extends Command
{
    use RunsMatchups;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'benchmark:rps {--matches=1 : Number of matches to run}';

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
    public function handle()
    {
        $this->runBenchmark('rps', function (Matchup $matchup): RpsMatch {
            $game = new RpsGame($matchup->player1, $matchup->player2);

            app(RpsBenchmarkService::class)->runGame(
                game: $game,
                onRoundComplete: fn (RpsRound $round) => $this->reportRound($game, $round),
            );

            return $this->createMatch($game);
        });

        return Command::SUCCESS;
    }

    /**
     * Report the result of a round
     */
    protected function reportRound(RpsGame $game, RpsRound $round): void
    {
        $winner = match ($round->result) {
            RpsRoundResult::Player1Win => '🔴 '.$game->getPlayer1()->name.' Wins',
            RpsRoundResult::Player2Win => '🔵 '.$game->getPlayer2()->name.' Wins',
            RpsRoundResult::Tie => '⚪️ Tie',
        };

        $move1 = $round->player1Move->emoji() . ' ' . $round->player1Move->name();
        $move2 = $round->player2Move->emoji() . ' ' . $round->player2Move->name();

        $score1 = $game->getPlayer1Score();
        $score2 = $game->getPlayer2Score();

        $roundNumber = $game->getRoundCount();

        $this->newLine();
        $this->line("Round $roundNumber: $move1 vs $move2 ($winner)");
        $this->line("Score: 🔴 $score1 - 🔵 $score2");
    }

    /**
     * Get extra information about the match.
     */
    protected function getExtraInfo(RpsMatch $match): array
    {
        return [
            '🔴 Player 1 score' => $match->player1_score,
            '🔵 Player 2 score' => $match->player2_score,
        ];
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
            'is_forced_completion' => ! $game->isOver(),
        ]);
    }
}
