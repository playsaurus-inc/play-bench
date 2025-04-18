<?php

namespace App\Http\Controllers\Models;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use Illuminate\View\View;

class ChessController extends Controller
{
    /**
     * Display the Chess-specific performance for this model.
     */
    public function show(AiModel $aiModel): View
    {
        return view('models.show-chess', [
            'model' => $aiModel,
            'activeTab' => 'chess',
        ]);
    }
}
