<?php

namespace App\Console\Commands\Import;

use App\Models\AiModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

abstract class AbstractImport extends Command
{
    /**
     * Indicates whether the data should be cleared before importing.
     */
    protected bool $shouldFresh = false;

    /**
     * Configure the command options.
     */
    protected function configure(): void
    {
        $this->addOption('fresh', null, null, 'Clear existing data before importing');
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->shouldFresh = $this->option('fresh');

        if ($this->shouldFresh) {
            $this->info('Clearing existing data...');
            $this->clearExistingData();
        }

        try {
            $count = $this->importData();
            $this->info("Imported {$count} records successfully.");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Import failed: {$e->getMessage()}");
            $this->error($e->getTraceAsString());

            return self::FAILURE;
        }
    }

    /**
     * Clear existing data for the model.
     */
    abstract protected function clearExistingData(): void;

    /**
     * Import data from source database.
     *
     * @return int The number of imported records
     */
    abstract protected function importData(): int;

    /**
     * Get a query builder for the source database.
     */
    protected function getSourceQuery(string $table): \Illuminate\Database\Query\Builder
    {
        return DB::connection('benchmark_source')->table($table);
    }

    /**
     * Find or create an AI model by name.
     */
    protected function aiModel(string $name): AiModel
    {
        return AiModel::query()->firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name],
        );
    }
}
