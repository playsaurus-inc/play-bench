<?php

namespace App\Console\Commands\AiModels;

use App\Models\AiModel;
use App\Models\ChessMatch;
use App\Models\RpsMatch;
use App\Models\SvgMatch;
use App\Services\EloRatingService;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Collection;

use function Laravel\Prompts\select;

class DeleteModel extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai-models:delete
        {--force : Force the operation to run when in production}
        {model? : The slug of the model to delete}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete an AI model and all it\' matches from the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return Command::FAILURE;
        }

        $modelSlug = $this->argument('model') ?? $this->askModelSlug();

        // Check if the model exists
        $model = AiModel::where('slug', $modelSlug)->first();

        if (! $model) {
            $this->error("Model with slug '{$modelSlug}' not found.");

            return Command::FAILURE;
        }

        // Delete the model and its matches
        $count = [
            'rps' => $this->deleteAndCount($this->getRpsMatches($model)),
            'svg' => $this->deleteAndCount($this->getSvgMatches($model)),
            'chess' => $this->deleteAndCount($this->getChessMatches($model)),
        ];

        $model->delete();

        $this->table(
            ['Match Type', 'Deleted Matches'],
            [
                ['RPS', $count['rps']],
                ['SVG', $count['svg']],
                ['Chess', $count['chess']],
            ]
        );

        $this->info("Model '{$modelSlug}' and its matches have been deleted.");

        $this->info('Updating Elo ratings...');

        app(EloRatingService::class)->updateAll();

        $this->info('Elo ratings updated.');

        return Command::SUCCESS;
    }

    /**
     * Delete the given models and return the count of deleted matches.
     */
    protected function deleteAndCount(Collection $matches): int
    {
        $matches->each->delete();

        return $matches->count();
    }

    /**
     * Ask for the model slug if not provided.
     */
    protected function askModelSlug(): string
    {
        $models = AiModel::all();

        return select(
            label: 'Please select the model to delete',
            options: $models->pluck('name', 'slug')->toArray(),
            scroll: 30,
        );
    }

    /**
     * Get a collection of all RPS matches.
     */
    protected function getRpsMatches(AiModel $model): Collection
    {
        return RpsMatch::select('id')
            ->where('player1_id', $model->id)
            ->orWhere('player2_id', $model->id)
            ->orWhere('winner_id', $model->id)
            ->get();
    }

    /**
     * Get a collection of all SVG matches.
     */
    protected function getSvgMatches(AiModel $model): Collection
    {
        return SvgMatch::select('id')
            ->where('player1_id', $model->id)
            ->orWhere('player2_id', $model->id)
            ->orWhere('winner_id', $model->id)
            ->get();
    }

    /**
     * Get a collection of all Chess matches.
     */
    protected function getChessMatches(AiModel $model): Collection
    {
        return ChessMatch::select('id')
            ->where('white_id', $model->id)
            ->orWhere('black_id', $model->id)
            ->orWhere('winner_id', $model->id)
            ->get();
    }
}
