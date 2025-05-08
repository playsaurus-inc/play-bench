<?php

namespace App\Console\Commands\Benchmark;

use App\Console\Concerns\RunsMatchups;
use App\Models\Contracts\RankedMatch;
use App\Models\SvgMatch;
use App\Services\Matchup;
use App\Services\Svg\SvgBenchmarkService;
use App\Services\Svg\SvgGame;
use App\Services\Svg\SvgPlayer;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'benchmark:svg',
    description: 'Run SVG creation benchmarks between AI models',
)]
class BenchmarkSvgCommand extends Command
{
    use RunsMatchups;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'benchmark:svg
        {--matches=1 : Number of SVG matches to run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run SVG creation benchmarks between AI models';

    /**
     * The number of completed matches.
     */
    protected int $completedMatches = 0;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->runBenchmark('svg', function (Matchup $matchup): RankedMatch {
            $game = new SvgGame($matchup->player1, $matchup->player2);

            app(SvgBenchmarkService::class)->runGame(
                game: $game,
                onPromptGenerated: fn ($game) => $this->reportPromptGenerated($game),
                onSvgSubmitted: fn ($game, $player) => $this->reportSvgSubmission($game, $player),
            );

            return $this->createMatch($game);
        });

        return Command::SUCCESS;
    }

    /**
     * Create a match record from game state
     */
    protected function createMatch(SvgGame $game): SvgMatch
    {
        // Store SVGs
        $matchId = (string) Str::uuid();
        $player1SvgPath = "{$matchId}-player1.svg";
        $player2SvgPath = "{$matchId}-player2.svg";

        try {
            Storage::disk('svg')->put($player1SvgPath, $game->getPlayer1Svg());
            Storage::disk('svg')->put($player2SvgPath, $game->getPlayer2Svg());

            return SvgMatch::create([
                'player1_id' => $game->getPlayer1()->id,
                'player2_id' => $game->getPlayer2()->id,
                'prompt' => $game->getPrompt(),
                'player1_svg_path' => $player1SvgPath,
                'player2_svg_path' => $player2SvgPath,
                'judge_reasoning' => $game->getJudgeReasoning(),
                'winner_id' => $game->getWinnerModel()?->id,
                'started_at' => $game->getStartedAt(),
                'ended_at' => $game->getEndedAt(),
            ]);
        } catch (Exception $e) {
            Storage::disk('svg')->delete($player1SvgPath);
            Storage::disk('svg')->delete($player2SvgPath);
            throw $e;
        }
    }

    /**
     * Report when a prompt is generated.
     */
    protected function reportPromptGenerated(SvgGame $game): void
    {
        $this->newLine();
        $this->info('Prompt generated');
        $this->line('📄 '.$game->getPrompt());
        $this->newLine();
    }

    /**
     * Report when an SVG is submitted.
     */
    protected function reportSvgSubmission(SvgGame $game, SvgPlayer $player): void
    {
        $emoji = $player === SvgPlayer::Player1 ? '🔴' : '🔵';
        $count = strlen($game->getPlayerSvg($player) ?? '');

        $this->line("$emoji SVG submitted by {$game->getPlayer($player)->name} ($count characters)");
    }

    /**
     * Get extra information about the match.
     */
    protected function getExtraInfo(SvgMatch $match): array
    {
        return [
            '📄 Judge Reasoning' => $match->judge_reasoning,
        ];
    }
}
