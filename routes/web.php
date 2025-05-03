<?php

use App\Http\Controllers\AiModels\AiModelController;
use App\Http\Controllers\AiModels\ChessController;
use App\Http\Controllers\AiModels\RpsController;
use App\Http\Controllers\AiModels\SvgController;
use App\Http\Controllers\RpsMatchController;
use App\Http\Controllers\SvgMatchController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/models');

Route::view('/about', 'about')->name('about');

// Cross-benchmark model routes
Route::get('/models', [AiModelController::class, 'index'])->name('models.index');
Route::get('/models/{aiModel}', [AiModelController::class, 'show'])->name('models.show');

// Benchmark-specific model subpages
Route::get('/models/{aiModel}/rock-paper-scissors', [RpsController::class, 'show'])
    ->name('models.show.rps');
Route::get('/models/{aiModel}/chess', [ChessController::class, 'show'])
    ->name('models.show.chess');
Route::get('/models/{aiModel}/svg-drawing', [SvgController::class, 'show'])
    ->name('models.show.svg');

// Benchmark index pages
Route::get('/rock-paper-scissors', [RpsMatchController::class, 'home'])->name('rps.index');
Route::get('/rock-paper-scissors/matches', [RpsMatchController::class, 'index'])->name('rps.matches.index');
Route::get('/rock-paper-scissors/matches/{rpsMatch}', [RpsMatchController::class, 'show'])->name('rps.matches.show');

// SVG Drawing benchmark pages
Route::get('/svg-drawing', [SvgMatchController::class, 'home'])->name('svg.index');
Route::get('/svg-drawing/matches', [SvgMatchController::class, 'index'])->name('svg.matches.index');
Route::get('/svg-drawing/matches/{svgMatch}', [SvgMatchController::class, 'show'])->name('svg.matches.show');

// Future benchmark index pages (commented out until implemented)
// Route::get('/chess', [ChessMatchController::class, 'index'])->name('chess.index');
// Route::get('/chess/matches/{chessMatch}', [ChessMatchController::class, 'show'])->name('chess.matches.show');
