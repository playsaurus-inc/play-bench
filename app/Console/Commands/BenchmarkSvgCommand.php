<?php

namespace App\Console\Commands;

use App\Models\AiModel;
use App\Models\SvgMatch;
use App\Services\EloRatingService;
use App\Services\Svg\SvgBenchmarkService;
use App\Services\Svg\SvgGame;
use App\Services\Svg\SvgPlayer;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
     * The number of completed matches.
     */
    protected int $completedMatches = 0;

    /**
     * Execute the console command.
     */
    public function handle(
        SvgBenchmarkService $benchmarkService,
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

        $this->info('Starting SVG benchmark tests');

        $this->completedMatches = 0;

        while ($this->completedMatches < $matchCount) {
            // Randomly select two different models
            $player1 = $aiModels->random();
            $player2 = $aiModels->whereNotIn('id', [$player1->id])->random();

            $game = new SvgGame($player1, $player2);

            $this->reportGameStarted($game);

            try {
                $benchmarkService->runGame(
                    game: $game,
                    onPromptGenerated: fn ($game) => $this->reportPromptGenerated($game),
                    onSvgSubmitted: fn ($game, $player) => $this->reportSvgSubmission($game, $player),
                );

                $this->reportGameEnded($game);

                $this->createMatch($game);

                $eloService->updateSvgEloRatings();

                $this->completedMatches++;
            } catch (\Exception $e) {
                $this->handleMatchError($e, $player1, $player2);
            }
        }

        $this->info('All matches completed');

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
     * Report when a game starts.
     */
    protected function reportGameStarted(SvgGame $game): void
    {
        $this->info(sprintf(
            'Starting SVG match: %s vs %s',
            $game->getPlayer1()->name,
            $game->getPlayer2()->name
        ));
    }

    /**
     * Report when a prompt is generated.
     */
    protected function reportPromptGenerated(SvgGame $game): void
    {
        $this->info(sprintf('Prompt: %s', $game->getPrompt()));
    }

    /**
     * Report when an SVG is submitted.
     */
    protected function reportSvgSubmission(SvgGame $game, SvgPlayer $player): void
    {
        $this->info(sprintf(
            '%s submitted their SVG drawing',
            $game->getPlayer($player)->name
        ));
    }

    /**
     * Report when judging is complete.
     */
    protected function reportGameEnded(SvgGame $game): void
    {
        $this->info(sprintf(
            'Judging complete: Winner: %s',
            $game->getWinnerModel()->name
        ));
        $this->info(sprintf('Reasoning: %s', $game->getJudgeReasoning()));
    }

    /**
     * Handle match error.
     */
    protected function handleMatchError(\Exception $e, AiModel $player1, AiModel $player2): void
    {
        $this->error(sprintf('Error running match: %s', $e->getMessage()));
        Log::error('SVG benchmark error', [
            'exception' => $e->getMessage(),
            'player1' => $player1->name,
            'player2' => $player2->name,
        ]);
        report($e);
    }
}
