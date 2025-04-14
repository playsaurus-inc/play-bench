<?php

namespace App\Console\Commands;

use App\Models\AiModel;
use App\Models\RpsMatch;
use Illuminate\Support\Carbon;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'import:rps',
    description: 'Import rock-paper-scissors matches from the source database',
)]
class ImportRps extends AbstractImport
{
    /**
     * The name and signature of the command.
     *
     * @var string
     */
    protected $signature = 'import:rps {--fresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import rock-paper-scissors matches from the source database';

    /**
     * Clear existing RPS matches.
     */
    protected function clearExistingData(): void
    {
        RpsMatch::truncate();
        $this->info('Cleared existing RPS matches.');
    }

    /**
     * Import RPS matches from the source database.
     *
     * @return int The number of imported records
     */
    protected function importData(): int
    {
        $this->info('Importing rock-paper-scissors matches...');

        $sourceMatches = $this->getSourceQuery('rps_results')->get();
        $importCount = 0;

        $this->withProgressBar($sourceMatches, function ($sourceMatch) use (&$importCount) {
            $player1 = $this->aiModel($sourceMatch->player1);
            $player2 = $this->aiModel($sourceMatch->player2);

            // Determine the winner
            $winner = null;
            if ($sourceMatch->winner === 'player1') {
                $winner = $player1;
            } elseif ($sourceMatch->winner === 'player2') {
                $winner = $player2;
            }

            // Calculate start and end times
            $timestamp = Carbon::createFromTimestamp($sourceMatch->time);
            $startedAt = $timestamp->copy()->subMinutes(rand(10, 60));
            $endedAt = $timestamp;

            // Parse move history from JSON
            $moveHistory = $this->parseMoveHistory($sourceMatch->moveHistory);

            // Create the RPS match
            RpsMatch::create([
                'player1_id' => $player1->id,
                'player2_id' => $player2->id,
                'winner_id' => $winner?->id,
                'rounds_played' => $sourceMatch->rounds,
                'player1_score' => $sourceMatch->score1,
                'player2_score' => $sourceMatch->score2,
                'move_history' => $moveHistory,
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

    /**
     * Parse move history from JSON representation to our compact string format
     *
     * @param  string  $jsonMoveHistory  The move history in JSON format
     * @return string The move history in compact string format
     */
    protected function parseMoveHistory(string $jsonMoveHistory): string
    {
        $moves = json_decode($jsonMoveHistory, true) ?? [];
        $result = [];

        foreach ($moves as $move) {
            if (is_array($move) && count($move) >= 3) {
                $p1Move = $this->abbreviateMove($move[1] ?? '');
                $p2Move = $this->abbreviateMove($move[2] ?? '');
                $outcome = $this->abbreviateOutcome($move[3] ?? '');

                if ($p1Move && $p2Move && $outcome) {
                    $result[] = $p1Move.$p2Move.$outcome;
                }
            }
        }

        return implode(' ', $result);
    }

    /**
     * Convert a move to its single-character abbreviation
     */
    protected function abbreviateMove(string $move): string
    {
        $move = strtolower(trim($move));

        return match ($move) {
            'rock', 'r' => 'r',
            'paper', 'p' => 'p',
            'scissors', 's' => 's',
            default => '',
        };
    }

    /**
     * Convert an outcome to its single-character abbreviation
     */
    protected function abbreviateOutcome(string $outcome): string
    {
        $outcome = strtolower(trim($outcome));

        if (in_array($outcome, ['player1', 'player1_win', '1'])) {
            return '1';
        } elseif (in_array($outcome, ['player2', 'player2_win', '2'])) {
            return '2';
        } elseif (in_array($outcome, ['tie', 't'])) {
            return 't';
        }

        return '';
    }
}
