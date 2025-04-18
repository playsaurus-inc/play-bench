<?php

namespace App\Http\Controllers\AiModels;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use Illuminate\View\View;

class SvgController extends Controller
{
    /**
     * Display the SVG Drawing-specific performance for this model.
     */
    public function show(AiModel $aiModel): View
    {
        return view('models.show-svg', [
            'model' => $aiModel,
            'activeTab' => 'svg',
        ]);
    }
}
