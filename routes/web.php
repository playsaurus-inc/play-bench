<?php

use App\Http\Controllers\RpsMatchController;
use App\Http\Controllers\AiModelController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// RPS Match routes
Route::get('/rock-paper-scissors', [RpsMatchController::class, 'index'])->name('rps.index');
Route::get('/rock-paper-scissors/matches/{rpsMatch}', [RpsMatchController::class, 'show'])->name('rps.matches.show');
Route::get('/rock-paper-scissors/models', [AiModelController::class, 'index'])->name('rps.models.index');
Route::get('/rock-paper-scissors/models/{aiModel}', [AiModelController::class, 'show'])->name('rps.models.show');
