<?php

namespace App\Console\Concerns;

use App\Models\AiModel;
use App\Models\Contracts\RankedMatch;
use App\Services\AiClient\AiClientService;
use App\Services\EloRatingService;
use App\Services\Matchup;
use App\Services\PlayerSelectionService;
use Carbon\CarbonInterface;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;

use function Laravel\Prompts\select;

/**
 * @mixin \Illuminate\Console\Command
 */
trait RunsMatchups
{
    /**
     * Get the AI models for the matchup.
     *
     * @param  iterable<AiModel>  $excludePlayers  The players to exclude from the matchup.
     */
    protected function matchup(string $game, iterable $excludePlayers = []): ?Matchup
    {
        $excludePlayers = collect($excludePlayers)->pluck('slug');

        $aiModels = $this->getAvailableModels($game)->whereNotIn('slug', $excludePlayers);

        if ($aiModels->isEmpty()) {
            return null;
        }

        if ($this->option('no-interaction')) {
            return app(PlayerSelectionService::class)->first($game, $aiModels);
        }

        $aiModels = collect($aiModels);
        $player1 = $this->askAiModel('Player 1', $aiModels);

        $remainingPlayers = $aiModels->reject(fn (AiModel $model) => $model->id === $player1->id);
        $player2 = $this->askAiModel('Player 2', $remainingPlayers);

        return app(PlayerSelectionService::class)->matchup($game, $player1, $player2);
    }

    /**
     * Get all available AI models for the given game.
     *
     * @return Collection<AiModel>
     */
    protected function getAvailableModels(string $game): Collection
    {
        return AiModel::whereIn('slug', app(AiClientService::class)->getAvailableModels($game))->get();
    }

    /**
     * Ask the user to select an AI model from a collection.
     */
    protected function askAiModel(string $label, Collection $aiModels): AiModel
    {
        return $aiModels->firstWhere('slug', select(
            label: "Which AI model should be $label?",
            options: $aiModels->pluck('name', 'slug'),
            default: $aiModels->first()->id,
            scroll: 10,
        )) ?? throw new \RuntimeException('No AI model found');
    }

    /**
     * Runs a series of matches between two players.
     *
     * @param  callable(Matchup):RankedMatch  $callback  The callback to run for each matchup.
     */
    protected function runBenchmark(string $gameType, callable $callback): void
    {
        $matchCount = (int) $this->option('matches');
        $matchCount = $matchCount > 0 ? $matchCount : PHP_INT_MAX;

        $this->info(sprintf('Starting %s benchmark with %d matches...', ucfirst($gameType), $matchCount));

        /** @var Collection<AiModel> $problematicPlayers */
        $problematicPlayers = collect();

        /** @var Collection<RankedMatch> $completedMatchups */
        $completedMatchups = collect();

        while ($completedMatchups->count() < $matchCount) {
            $matchup = $this->matchup($gameType, $problematicPlayers);

            if (! $matchup) {
                throw new \RuntimeException('No matchup found');
            }

            $this->reportGameStarted($gameType, $matchup);

            try {
                $rankedMatch = $callback($matchup);
            } catch (Exception $e) {
                $this->reportError($matchup, $e);

                $problematicPlayers->push($matchup->player1, $matchup->player2);

                continue;
            }

            app(EloRatingService::class)->updateEloRatings($gameType);
            app(EloRatingService::class)->updateOverallEloRatings();

            $this->reportResult($rankedMatch);

            $completedMatchups->push($rankedMatch);
        }

        $this->notifyCompletion($completedMatchups, $problematicPlayers);
    }

    /**
     * Report an error that occurred during the match.
     */
    protected function reportError(Matchup $matchup, Exception $exception): void
    {
        report($exception);

        $this->error("Error occurred while running the matchup between {$matchup->player1->name} and {$matchup->player2->name}.");
        $this->error($exception->getMessage());
        $this->error('Please check the logs for more details.');
    }

    protected function getPlayerLabels(): array
    {
        return ['🔴 Player 1', '🔵 Player 2'];
    }

    /**
     * Report the start of a game.
     */
    protected function reportGameStarted(string $game, Matchup $matchup): void
    {
        $elo1 = $matchup->player1->{$game.'_elo'};
        $elo2 = $matchup->player2->{$game.'_elo'};

        $eloDifference = abs($elo1 - $elo2);
        $eloPlayer = $elo1 > $elo2 ? '🔴 Player 1' : '🔵 Player 2';

        $this->newLine();
        $this->info('Game started');
        $this->line("- 🎮 Game: $game");
        $this->line("- 🔴 Player 1: {$matchup->player1->name}. ELO: ".Number::format($elo1, 0));
        $this->line("- 🔵 Player 2: {$matchup->player2->name}. ELO: ".Number::format($elo2, 0));
        $this->line('- 📊 Games played: '.Number::format($matchup->matchesPlayed, 0));
        $this->line('- 📈 ELO difference: +'.Number::format($eloDifference, 0).' for '.$eloPlayer);
    }

    /**
     * Report the end of a game.
     */
    protected function reportResult(RankedMatch $match): void
    {
        $outcome = match ($match->getOutcome()) {
            '1' => "🔴 Player 1 wins {$match->getPlayer1()->name}",
            '2' => "🔵 Player 2 wins {$match->getPlayer2()->name}",
            't' => 'Tie',
        };

        $url = $match->getUrl();

        $info = array_merge([
            '🔴 Player 1' => $match->getPlayer1()->name,
            '🔵 Player 2' => $match->getPlayer2()->name,
            '🏆 Winner' => $outcome,
            '⏱️ Time taken' => $match->started_at->diffForHumans($match->ended_at, CarbonInterface::DIFF_ABSOLUTE),
            '🔗 Match URL' => "<href=$url>$url</>",
        ], $this->getExtraInfo($match));

        $this->newLine();
        $this->info('Game ended');
        foreach ($info as $key => $value) {
            $this->line("- $key: $value");
        }
        $this->newLine();
    }

    /**
     * Get extra information about the match.
     */
    protected function getExtraInfo(RankedMatch $match): array
    {
        return [];
    }

    /**
     * Notify the user of the completion of the matchups.
     *
     * @param  Collection<RankedMatch>  $completedMatchups
     * @param  Collection<AiModel>  $problematicPlayers
     */
    protected function notifyCompletion(Collection $completedMatchups, Collection $problematicPlayers): void
    {
        $this->newLine();

        $count = $completedMatchups->count();
        $this->info("Completed $count matches:");
        foreach ($completedMatchups as $match) {
            $url = $match->getUrl();
            $this->line("- 🔗 Match URL: <href=$url>$url</>");
        }

        if ($problematicPlayers->isNotEmpty()) {
            $this->warn('The following players encountered issues:');
            $this->line($problematicPlayers->pluck('name')->implode(', '));
        }
        $this->newLine();
    }
}
