<?php

namespace App\Console\Commands;

use App\Models\AiModel;
use App\Models\ChessMatch;
use Illuminate\Support\Carbon;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'import:chess',
    description: 'Import chess matches from the source database',
)]
class ImportChess extends AbstractImport
{
    /**
     * The name and signature of the command.
     *
     * @var string
     */
    protected $signature = 'import:chess {--fresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import chess matches from the source database';

    /**
     * Clear existing chess matches.
     */
    protected function clearExistingData(): void
    {
        ChessMatch::truncate();
        $this->info('Cleared existing chess matches.');
    }

    /**
     * Import chess matches from the source database.
     *
     * @return int The number of imported records
     */
    protected function importData(): int
    {
        $this->info('Importing chess matches...');

        // Fetch all AI models to map the names
        $aiModels = AiModel::all()->keyBy('name');

        // Check if we have the required AI models
        if ($aiModels->isEmpty()) {
            $this->error('No AI models found. Please run the AiModelSeeder first.');

            return 0;
        }

        $sourceMatches = $this->getSourceQuery('results')->get();
        $importCount = 0;

        $this->withProgressBar($sourceMatches, function ($sourceMatch) use ($aiModels, &$importCount) {
            // Find the AI models
            $white = $aiModels->get($sourceMatch->white);
            $black = $aiModels->get($sourceMatch->black);
            $winner = null;

            // Skip if the models don't exist
            if (! $white || ! $black) {
                $this->newLine();
                $this->warn("Skipping match #{$sourceMatch->id}: models not found");

                return;
            }

            // Set the winner if not a draw
            if ($sourceMatch->winnerModel !== 'draw') {
                $winner = $aiModels->get($sourceMatch->winnerModel);
                if (! $winner) {
                    $this->newLine();
                    $this->warn("Winner model not found for match #{$sourceMatch->id}, treating as draw");
                }
            }

            // Determine the result
            $result = match ($sourceMatch->winnerColor) {
                'white' => 'white',
                'black' => 'black',
                default => 'draw',
            };

            // Calculate start and end times
            $timestamp = Carbon::createFromTimestamp($sourceMatch->time);
            $startedAt = $timestamp->copy()->subMinutes(rand(10, 60));
            $endedAt = $timestamp;

            // Create the chess match
            ChessMatch::create([
                'white_id' => $white->id,
                'black_id' => $black->id,
                'winner_id' => $winner?->id,
                'ply_count' => $sourceMatch->numMoves * 2, // Convert moves to ply count
                'result' => $result,
                'pgn' => $sourceMatch->PGN,
                'final_fen' => $sourceMatch->endFen,
                'illegal_moves_white' => $sourceMatch->illegalMovesWhite ?? 0,
                'illegal_moves_black' => $sourceMatch->illegalMovesBlack ?? 0,
                'is_forced_completion' => $sourceMatch->forced === 'Y',
                'started_at' => $startedAt,
                'ended_at' => $endedAt,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            $importCount++;
        });

        $this->newLine(2);

        return $importCount;
    }
}
