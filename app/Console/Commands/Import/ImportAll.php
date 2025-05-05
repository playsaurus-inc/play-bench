<?php

namespace App\Console\Commands\Import;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'import',
    description: 'Import all benchmark data from the source database',
)]
class ImportAll extends Command
{
    /**
     * The name and signature of the command.
     *
     * @var string
     */
    protected $signature = 'import
        {--fresh : Clear existing data before importing}
        {--skip-elo : Skip ELO calculation after import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all benchmark data from the source database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->components->info('Starting full data import...');

        if ($this->option('fresh')) {
            if (! $this->components->confirm('This will delete all existing data. Are you sure?', true)) {
                $this->components->info('Import canceled.');

                return self::SUCCESS;
            }
        }

        $this->components->task('Importing Chess Matches', function () {
            return $this->callSilently('import:chess', ['--fresh' => $this->option('fresh')]) === 0;
        });

        $this->components->task('Importing Rock-Paper-Scissors Matches', function () {
            return $this->callSilently('import:rps', ['--fresh' => $this->option('fresh')]) === 0;
        });

        $this->components->task('Importing SVG Matches', function () {
            return $this->callSilently('import:svg', ['--fresh' => $this->option('fresh')]) === 0;
        });

        if (! $this->option('skip-elo')) {
            $this->components->task('Calculating ELO Ratings', function () {
                return $this->callSilently('calculate:elo') === 0;
            });
        }

        $this->components->info('All benchmark data imported successfully!');

        return self::SUCCESS;
    }
}
