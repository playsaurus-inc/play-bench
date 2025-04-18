<?php

namespace App\Console\Commands;

use App\Models\AiModel;
use App\Services\AiClient\AiClientService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateAiModelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-ai-models';

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
        // Get available models from the AiClientService
        $models = $aiClientService->getAvailableModels();

        $created = 0;
        $existing = 0;

        foreach ($models as $model) {
            // Check if the model already exists
            if (AiModel::where('name', $model)->exists()) {
                $existing++;
                $this->line(sprintf('AI model <comment>%s</comment> already exists, skipping', $model));

                continue;
            }

            // Create the model
            AiModel::create([
                'name' => $model,
                'slug' => Str::slug($model),
            ]);

            $created++;
            $this->info(sprintf('Created AI model <info>%s</info>', $model));
        }

        $this->newLine();
        $this->info(sprintf('Done! Created %d new AI models, %d already existed.', $created, $existing));

        return Command::SUCCESS;
    }
}
