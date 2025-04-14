@props(['match', 'compact' => false])

<a href="{{ route('rps.show', $match) }}"
   class="block bg-white overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 rounded-xl"
   x-data="{ hover: false }"
   @mouseenter="hover = true"
   @mouseleave="hover = false">
    <div class="p-6">
        @if(!$compact)
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <x-phosphor-hand-fill class="h-5 w-5 text-amber-500 mr-2" />
                    <span class="text-sm font-medium text-amber-700">Match #{{ $match->id }}</span>
                </div>
                <span class="text-xs text-gray-500">
                    {{ $match->created_at->format('M d, Y') }}
                </span>
            </div>
        @else
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-amber-700">#{{ $match->id }}</span>
                <span class="text-xs text-gray-500">
                    {{ $match->created_at->format('M d, Y') }}
                </span>
            </div>
        @endif

        <!-- Players -->
        <div class="flex items-center justify-between {{ $compact ? 'mb-3' : 'mb-6' }}">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <x-phosphor-robot-fill class="w-5 h-5 text-red-500" />
                    </div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-900">{{ $match->player1->name }}</div>
                    <div class="text-xs text-gray-500">Player 1</div>
                </div>
            </div>
            <div class="text-lg font-bold">VS</div>
            <div class="flex items-center space-x-4">
                <div>
                    <div class="text-sm font-medium text-gray-900 text-right">{{ $match->player2->name }}</div>
                    <div class="text-xs text-gray-500 text-right">Player 2</div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <x-phosphor-robot-fill class="w-5 h-5 text-blue-500" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Score and rounds -->
        <div class="flex items-center justify-between mb-4">
            <div class="w-full">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xl font-bold {{ $match->winner_id === $match->player1_id ? 'text-red-600' : 'text-gray-800' }}">
                        {{ $match->player1_score }}
                    </span>
                    <div class="text-sm text-gray-500">
                        {{ $match->rounds_played }} rounds
                    </div>
                    <span class="text-xl font-bold {{ $match->winner_id === $match->player2_id ? 'text-blue-600' : 'text-gray-800' }}">
                        {{ $match->player2_score }}
                    </span>
                </div>
                <div class="relative h-2 bg-gray-100 rounded-full overflow-hidden">
                    @php
                        $p1Percentage = ($match->player1_score / $match->rounds_played) * 100;
                        $p2Percentage = ($match->player2_score / $match->rounds_played) * 100;
                        $tiePercentage = 100 - $p1Percentage - $p2Percentage;

                        // Ensure we have some minimum width for visibility when percentages are very small
                        $p1Percentage = max(2, $p1Percentage);
                        $p2Percentage = max(2, $p2Percentage);

                        // Adjust if total exceeds 100%
                        if ($p1Percentage + $p2Percentage > 100) {
                            $factor = 100 / ($p1Percentage + $p2Percentage);
                            $p1Percentage *= $factor;
                            $p2Percentage *= $factor;
                        }
                    @endphp
                    <div class="absolute left-0 top-0 h-2 bg-red-400" style="width: {{ $p1Percentage }}%"></div>
                    <div class="absolute right-0 top-0 h-2 bg-blue-400" style="width: {{ $p2Percentage }}%"></div>
                </div>
            </div>
        </div>

        <!-- Winner -->
        <div class="flex items-center justify-between">
            <div>
                @if($match->isTie())
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        <x-phosphor-equals-fill class="w-3 h-3 mr-1" />
                        Tie
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $match->winner_id === $match->player1_id ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                        <x-phosphor-trophy-fill class="w-3 h-3 mr-1" />
                        {{ $match->winner->name }} wins
                    </span>
                @endif
            </div>

            <span class="text-xs font-medium text-amber-600 hover:text-amber-700 transition-colors inline-flex items-center" :class="{'translate-x-0.5': hover}">
                View details
                <x-phosphor-arrow-right class="h-3 w-3 ml-1 transition-transform" x-bind:class="{'transform translate-x-0.5': hover}" />
            </span>
        </div>
    </div>
</a>
