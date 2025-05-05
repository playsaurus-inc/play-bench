<div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-100 p-4">
    <div class="flex flex-col md:flex-row md:items-center">
        <!-- First model -->
        <div class="flex-1 flex items-center mb-4 md:mb-0">
            <div class="flex-shrink-0 mr-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 border-2 border-white shadow-md flex items-center justify-center">
                    <x-phosphor-robot-fill class="w-6 h-6 text-blue-500" />
                </div>
            </div>
            <div>
                <h3 class="text-base font-medium text-gray-900">{{ $model->name }}</h3>
                <p class="text-xs text-gray-500">Artist 1</p>
            </div>
        </div>

        <!-- VS indicator -->
        <div class="flex-shrink-0 flex justify-center mb-4 md:mb-0">
            <div class="p-2 rounded-full bg-amber-50 text-amber-600 font-bold">VS</div>
        </div>

        <!-- Second model -->
        <div class="flex-1 flex items-center justify-end">
            <div class="text-right mr-4">
                <h3 class="text-base font-medium text-gray-900">{{ $contender->name }}</h3>
                <p class="text-xs text-gray-500">Artist 2</p>
            </div>
            <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-full bg-indigo-100 border-2 border-white shadow-md flex items-center justify-center">
                    <x-phosphor-robot-fill class="w-6 h-6 text-indigo-500" />
                </div>
            </div>
        </div>
    </div>

    <!-- Matchup stats -->
    <div class="mt-4 pt-4 border-t border-gray-100">
        <div class="grid grid-cols-3 gap-2 text-center">
            @php
                // Get matches between these two models
                use App\Models\SvgMatch;

                $player1 = $model->id;
                $player2 = $contender->id;

                $matches = SvgMatch::where(function($q) use ($player1, $player2) {
                    $q->where(function($inner) use ($player1, $player2) {
                        $inner->where('player1_id', $player1)->where('player2_id', $player2);
                    })->orWhere(function($inner) use ($player1, $player2) {
                        $inner->where('player1_id', $player2)->where('player2_id', $player1);
                    });
                })->get();

                $total = $matches->count();
                $modelWins = $matches->where('winner_id', $model->id)->count();
                $contenderWins = $matches->where('winner_id', $contender->id)->count();
                $ties = $total - $modelWins - $contenderWins;
            @endphp

            <div>
                <div class="text-xs text-gray-500">{{ $model->name }} wins</div>
                <div class="text-lg font-bold text-blue-600">{{ $modelWins }}</div>
            </div>

            <div>
                <div class="text-xs text-gray-500">Tie/Undecided</div>
                <div class="text-lg font-bold text-gray-600">{{ $ties }}</div>
            </div>

            <div>
                <div class="text-xs text-gray-500">{{ $contender->name }} wins</div>
                <div class="text-lg font-bold text-indigo-600">{{ $contenderWins }}</div>
            </div>
        </div>
    </div>
</div>
