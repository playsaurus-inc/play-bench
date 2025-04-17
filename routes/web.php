<?php

use App\Http\Controllers\RpsMatchController;
use App\Http\Controllers\ModelController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Cross-benchmark model routes
Route::get('/models', [ModelController::class, 'index'])->name('models.index');
Route::get('/models/{aiModel}', [ModelController::class, 'show'])->name('models.show');

// Benchmark-specific model subpages
Route::get('/models/{aiModel}/rock-paper-scissors', [ModelController::class, 'showRps'])
    ->name('models.show.rps');
Route::get('/models/{aiModel}/chess', [ModelController::class, 'showChess'])
    ->name('models.show.chess');
Route::get('/models/{aiModel}/svg-drawing', [ModelController::class, 'showSvg'])
    ->name('models.show.svg');

// Benchmark index pages
Route::get('/rock-paper-scissors', [RpsMatchController::class, 'index'])->name('rps.index');
Route::get('/rock-paper-scissors/matches/{rpsMatch}', [RpsMatchController::class, 'show'])->name('rps.matches.show');

// Future benchmark index pages (commented out until implemented)
// Route::get('/svg-drawing', [SvgMatchController::class, 'index'])->name('svg.index');
// Route::get('/svg-drawing/matches/{svgMatch}', [SvgMatchController::class, 'show'])->name('svg.matches.show');
// Route::get('/chess', [ChessMatchController::class, 'index'])->name('chess.index');
// Route::get('/chess/matches/{chessMatch}', [ChessMatchController::class, 'show'])->name('chess.matches.show');
