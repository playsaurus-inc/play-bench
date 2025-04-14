<?php

namespace App\Http\Controllers;

use App\Models\RpsMatch;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RpsMatchController extends Controller
{
    /**
     * Display a listing of the RPS matches.
     */
    public function index(): View
    {
        $matches = RpsMatch::with(['player1', 'player2', 'winner'])
            ->latest()
            ->paginate(20);

        return view('rps.index', compact('matches'));
    }

    /**
     * Display the specified RPS match.
     */
    public function show(RpsMatch $rpsMatch): View
    {
        $rpsMatch->load(['player1', 'player2', 'winner']);

        return view('rps.show', compact('rpsMatch'));
    }
}
