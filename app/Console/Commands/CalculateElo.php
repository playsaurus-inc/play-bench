<?php

namespace App\Console\Commands;

use App\Services\EloRatingService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'calculate:elo',
    description: 'Calculate ELO ratings for all AI models based on match outcomes',
)]
class CalculateElo extends Command
{
    /**
     * The name and signature of the command.
     *
     * @var string
     */
    protected $signature = 'calculate:elo
                            {--game= : Specific game type to calculate (rps, svg, chess, or all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate ELO ratings for all AI models based on match outcomes';

    /**
     * Execute the console command.
     */
    public function handle(EloRatingService $eloService): int
    {
        $game = $this->option('game') ?? 'all';

        $this->info('Starting ELO rating calculations...');

        if ($game === 'all' || $game === 'rps') {
            $this->components->task(
                'Calculating Rock-Paper-Scissors ELO ratings',
                fn () => $eloService->updateRpsEloRatings(),
            );
        }

        if ($game === 'all' || $game === 'svg') {
            $this->components->task(
                'Calculating SVG drawing ELO ratings',
                fn () => $eloService->updateSvgEloRatings(),
            );
        }

        if ($game === 'all' || $game === 'chess') {
            $this->components->task(
                'Calculating Chess ELO ratings',
                fn () => $eloService->updateChessEloRatings(),
            );
        }

        $this->components->task(
            'Calculating overall ELO ratings',
            fn () => $eloService->updateOverallEloRatings(),
        );

        $this->info('ELO rating calculations completed!');

        return self::SUCCESS;
    }
}
