<?php

namespace App\Console\Concerns;

use App\Models\AiModel;
use App\Services\AiClient\AiClientService;
use App\Services\Matchup;
use App\Services\PlayerSelectionService;
use Illuminate\Support\Collection;

use function Laravel\Prompts\select;

/**
 * @mixin \Illuminate\Console\Command
 */
trait OrganizesMatchups
{
    /**
     * Get the AI models for the matchup.
     */
    protected function matchup(string $game): ?Matchup
    {
        $aiModels = $this->getAvailableModels($game);

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
}
