<?php

use App\Console\Commands\Benchmark\BenchmarkChessCommand;
use App\Console\Commands\Benchmark\BenchmarkRpsCommand;
use App\Console\Commands\Benchmark\BenchmarkSvgCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

$notifyTo = config('playbench.auto_run.notify_to');
$matches = config('playbench.auto_run.matches', 10);

$args = ['--no-interaction', '--matches' => $matches];

if (config('playbench.auto_run.svg')) {
    Schedule::command(BenchmarkSvgCommand::class, $args)
        ->daily()
        ->emailOutputTo($notifyTo);
}

if (config('playbench.auto_run.rps')) {
    Schedule::command(BenchmarkRpsCommand::class, $args)
        ->daily()
        ->emailOutputTo($notifyTo);
}

if (config('playbench.auto_run.chess')) {
    Schedule::command(BenchmarkChessCommand::class, $args)
        ->daily()
        ->emailOutputTo($notifyTo);
}
