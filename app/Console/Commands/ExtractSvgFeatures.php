<?php

namespace App\Console\Commands;

use App\Models\SvgMatch;
use App\Services\Svg\SvgAnalysisService;
use Illuminate\Console\Command;

class ExtractSvgFeatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:extract-svg-features';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract SVG features from the SVG files';

    /**
     * Execute the console command.
     */
    public function handle(SvgAnalysisService $analysis)
    {
        $bar = $this->output->createProgressBar(
            SvgMatch::query()->count()
        );

        $bar->start();

        foreach (SvgMatch::query()->lazyById() as $match) {
            $match->player1_features = $analysis->extractFeatures(
                $match->getPlayer1SvgContent()
            );
            $match->player2_features = $analysis->extractFeatures(
                $match->getPlayer2SvgContent()
            );

            $match->save();

            $bar->advance();
        }

        $bar->finish();

        $this->info('SVG statistics extracted successfully.');
    }
}
