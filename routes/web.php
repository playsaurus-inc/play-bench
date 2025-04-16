<?php

use App\Http\Controllers\RpsMatchController;
use App\Http\Controllers\RpsModelController;
use App\Http\Controllers\ModelController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Cross-benchmark model routes
Route::get('/models', [ModelController::class, 'index'])->name('models.index');
Route::get('/models/{aiModel}', [ModelController::class, 'show'])->name('models.show');

// RPS routes
Route::get('/rock-paper-scissors', [RpsMatchController::class, 'index'])->name('rps.index');
Route::get('/rock-paper-scissors/matches/{rpsMatch}', [RpsMatchController::class, 'show'])->name('rps.matches.show');

// Keep individual model detail pages for each benchmark
Route::get('/rock-paper-scissors/models/{aiModel}', [RpsModelController::class, 'show'])->name('rps.models.show');

// Future routes (commented out until implemented)
// Route::get('/svg-drawing', [SvgMatchController::class, 'index'])->name('svg.index');
// Route::get('/svg-drawing/matches/{svgMatch}', [SvgMatchController::class, 'show'])->name('svg.matches.show');
// Route::get('/svg-drawing/models/{aiModel}', [SvgModelController::class, 'show'])->name('svg.models.show');

// Route::get('/chess', [ChessMatchController::class, 'index'])->name('chess.index');
// Route::get('/chess/matches/{chessMatch}', [ChessMatchController::class, 'show'])->name('chess.matches.show');
// Route::get('/chess/models/{aiModel}', [ChessModelController::class, 'show'])->name('chess.models.show');
