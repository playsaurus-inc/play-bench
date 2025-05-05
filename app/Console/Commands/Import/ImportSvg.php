<?php

namespace App\Console\Commands\Import;

use App\Models\SvgMatch;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'import:svg',
    description: 'Import SVG matches from the source database',
)]
class ImportSvg extends AbstractImport
{
    /**
     * The name and signature of the command.
     *
     * @var string
     */
    protected $signature = 'import:svg {--fresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import SVG matches from the source database';

    /**
     * Clear existing SVG matches.
     */
    protected function clearExistingData(): void
    {
        // Remove SVG files
        $this->disk()->deleteDirectory('svg-matches');

        // Clear database records
        SvgMatch::truncate();

        $this->info('Cleared existing SVG matches and files.');
    }

    /**
     * Gets the disk where the SVG files are stored.
     */
    protected function disk(): Filesystem
    {
        return Storage::disk('svg');
    }

    /**
     * Import SVG matches from the source database.
     *
     * @return int The number of imported records
     */
    protected function importData(): int
    {
        $this->info('Importing SVG matches...');

        // Create storage directory if it doesn't exist
        $sourceMatches = $this->getSourceQuery('svg_results')->get();
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
            } else {
                $this->warn("Match {$sourceMatch->id} is a draw. Skipping...");

                return;
            }

            // Calculate start and end times
            $timestamp = Carbon::createFromTimestamp($sourceMatch->time);
            $startedAt = $timestamp->copy()->subMinutes(rand(10, 60));
            $endedAt = $timestamp;

            // Parse SVG content from move history
            $svgs = $this->extractSvgsFromMoveHistory($sourceMatch->moveHistory);

            // Save SVG files
            $svgPath1 = null;
            $svgPath2 = null;

            if ($svgs['player1'] ?? null) {
                $svgPath1 = "match-{$sourceMatch->id}-player1.svg";
                $this->disk()->put($svgPath1, $svgs['player1']);
            }

            if ($svgs['player2'] ?? null) {
                $svgPath2 = "match-{$sourceMatch->id}-player2.svg";
                $this->disk()->put($svgPath2, $svgs['player2']);
            }

            // Create the SVG match
            SvgMatch::create([
                'player1_id' => $player1->id,
                'player2_id' => $player2->id,
                'winner_id' => $winner?->id,
                'prompt' => $sourceMatch->lastPrompt,
                'player1_svg_path' => $svgPath1,
                'player2_svg_path' => $svgPath2,
                'judge_reasoning' => $svgs['reason'] ?? 'No reasoning provided',
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
     * Extract SVG content and judge reasoning from move history
     *
     * @param  string  $moveHistoryJson  JSON string containing move history
     * @return array<string, string> Array with SVG content and reasoning
     */
    protected function extractSvgsFromMoveHistory(string $moveHistoryJson): array
    {
        $result = [
            'player1' => null,
            'player2' => null,
            'reason' => null,
        ];

        $moveHistory = json_decode($moveHistoryJson, true);

        if (! is_array($moveHistory) || empty($moveHistory)) {
            return $result;
        }

        // Try to extract from the first move record
        $firstMove = $moveHistory[0] ?? null;

        if (is_array($firstMove)) {
            // SVG content is typically in index 4 and 5
            if (isset($firstMove[4]) && is_string($firstMove[4])) {
                $result['player1'] = $this->cleanupSvgContent($firstMove[4]);
            }

            if (isset($firstMove[5]) && is_string($firstMove[5])) {
                $result['player2'] = $this->cleanupSvgContent($firstMove[5]);
            }

            // Judge reasoning is typically in index 3
            if (isset($firstMove[3]) && is_string($firstMove[3])) {
                $result['reason'] = $firstMove[3];
            }
        }

        return $result;
    }

    /**
     * Clean up SVG content to ensure it's valid
     */
    protected function cleanupSvgContent(string $svgContent): string
    {
        // Remove markdown code block syntax
        $cleaned = preg_replace('/```svg\n?|```\n?/m', '', $svgContent);

        // Fix common issues that might break SVG rendering
        // Fix spaces in tag names (e.g., "< defs>" to "<defs>")
        $cleaned = preg_replace('/< ([a-zA-Z])/m', '<$1', $cleaned);

        // Fix self-closing tags that might be malformed
        $cleaned = preg_replace('/([a-zA-Z])\/>/m', '$1 />', $cleaned);

        // Ensure proper XML structure
        if (! str_contains($cleaned, 'xmlns="http://www.w3.org/2000/svg"') && str_contains($cleaned, '<svg')) {
            $cleaned = str_replace('<svg', '<svg xmlns="http://www.w3.org/2000/svg"', $cleaned);
        }

        // Make sure viewBox is properly defined
        if (! str_contains($cleaned, 'viewBox') && str_contains($cleaned, '<svg')) {
            $cleaned = str_replace('<svg', '<svg viewBox="0 0 300 300"', $cleaned);
        }

        return $cleaned;
    }
}
