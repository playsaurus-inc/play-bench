<?php

use App\Http\Controllers\RpsMatchController;
use App\Http\Controllers\AiModelController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// RPS Match routes
Route::get('/rps', [RpsMatchController::class, 'index'])->name('rps.index');
Route::get('/rps/{rpsMatch}', [RpsMatchController::class, 'show'])->name('rps.show');

// AI Model routes
Route::get('/models', [AiModelController::class, 'index'])->name('models.index');
Route::get('/models/compare/{modelOne}/{modelTwo}', [AiModelController::class, 'compare'])->name('models.compare');
Route::get('/models/{aiModel}', [AiModelController::class, 'show'])->name('models.show');
