<?php

namespace App\Console\Commands;

use App\Models\AiModel;
use App\Services\AiClient\AiClientService;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class SyncAiModelsCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-ai-models
        {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the AI models for benchmarking';

    /**
     * Execute the console command.
     */
    public function handle(AiClientService $aiClientService): int
    {
        if (! $this->confirmToProceed()) {
            return Command::FAILURE;
        }

        // Get available models from the AiClientService
        $models = $aiClientService->models();

        $existingModels = AiModel::all();

        foreach ($models as $slug => $modelInfo) {
            $model = $existingModels->firstWhere('slug', $slug);

            if ($model) {
                $model->update(['name' => $modelInfo['name']]);

                $this->info("Updated existing model: {$slug}");
            } else {
                AiModel::create([
                    'slug' => $slug,
                    'name' => $modelInfo['name'],
                ]);

                $this->info("Created new model: {$slug}");
            }
        }

        return Command::SUCCESS;
    }
}
