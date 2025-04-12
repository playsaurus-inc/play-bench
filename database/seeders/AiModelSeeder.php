<?php

namespace Database\Seeders;

use App\Models\AiModel;
use Illuminate\Database\Seeder;

class AiModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $models = [
            'gpt-4o-mini-2024-07-18',
            'gpt-4o-2024-11-20',
            'o1-mini-2024-09-12',
            'o3-mini-2025-01-31-Low',
            'o3-mini-2025-01-31-High',
            'gpt-3.5-turbo-0125',
            'gemini-pro-1.5',
            'qwen-2.5-32b',
            'deepseek-r1-distill-llama-70b',
            'deepseek-r1-distill-qwen-32b',
            'llama3-70b-8192',
            'claude-3-7-sonnet-20250219-noThink',
            'claude-3-7-sonnet-20250219-Thinking',
            'claude-3-5-sonnet-20241022',
            'DeepSeek-R1',
            'llama-3.1-405b-instruct',
            'Deepseek-V3',
            'random', // Special case for random decisions
        ];

        foreach ($models as $modelName) {
            AiModel::factory()->withName($modelName)->create();
        }
    }
}
