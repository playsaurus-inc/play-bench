<x-layouts::app :title="$aiModel->name">
    <div class="mb-4">
        <x-ui.button :href="route('models.index')" variant="secondary" class="text-sm">
            &larr; Back to All Models
        </x-ui.button>
    </div>

    <h1 class="text-3xl font-bold mb-6">{{ $aiModel->name }}</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <x-ui.card title="Rock Paper Scissors Performance">
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-500">Total Matches</div>
                        <div class="text-2xl font-bold">{{ $totalRpsMatches }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Matches Won</div>
                        <div class="text-2xl font-bold">{{ $totalRpsWins }}</div>
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500 mb-1">Win Rate</div>
                    <div class="flex items-center">
                        <span class="text-2xl font-bold mr-3">{{ number_format($winRate * 100, 1) }}%</span>
                        <div class="relative flex-grow h-3 bg-gray-200 rounded-full overflow-hidden">
                            <div class="absolute top-0 left-0 h-full bg-indigo-600" style="width: {{ $winRate * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Move Tendencies" class="col-span-2">
            @php
                $moveBreakdown = [
                    'rock' => 0,
                    'paper' => 0,
                    'scissors' => 0,
                ];
                $totalMoves = 0;

                foreach ($rpsMatches as $match) {
                    $rounds = $match->getRounds();
                    foreach ($rounds as $round) {
                        if ($match->player1_id === $aiModel->id) {
                            $moveBreakdown[$round['player1_move']]++;
                        } else {
                            $moveBreakdown[$round['player2_move']]++;
                        }
                        $totalMoves++;
                    }
                }
            @endphp

            @if($totalMoves > 0)
                <div class="grid grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1">{{ $moveBreakdown['rock'] }}</div>
                        <div class="text-sm text-gray-500 mb-2">Rock</div>
                        <div class="relative w-full h-3 bg-gray-200 rounded-full overflow-hidden">
                            <div class="absolute top-0 left-0 h-full bg-red-500" style="width: {{ ($moveBreakdown['rock'] / $totalMoves) * 100 }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ number_format(($moveBreakdown['rock'] / $totalMoves) * 100, 1) }}%
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1">{{ $moveBreakdown['paper'] }}</div>
                        <div class="text-sm text-gray-500 mb-2">Paper</div>
                        <div class="relative w-full h-3 bg-gray-200 rounded-full overflow-hidden">
                            <div class="absolute top-0 left-0 h-full bg-blue-500" style="width: {{ ($moveBreakdown['paper'] / $totalMoves) * 100 }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ number_format(($moveBreakdown['paper'] / $totalMoves) * 100, 1) }}%
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1">{{ $moveBreakdown['scissors'] }}</div>
                        <div class="text-sm text-gray-500 mb-2">Scissors</div>
                        <div class="relative w-full h-3 bg-gray-200 rounded-full overflow-hidden">
                            <div class="absolute top-0 left-0 h-full bg-green-500" style="width: {{ ($moveBreakdown['scissors'] / $totalMoves) * 100 }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ number_format(($moveBreakdown['scissors'] / $totalMoves) * 100, 1) }}%
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-sm text-gray-600">
                    <p>
                        @php
                            $highestMove = array_search(max($moveBreakdown), $moveBreakdown);
                            $perfectDistribution = abs(($moveBreakdown['rock'] - $totalMoves/3) / $totalMoves) < 0.1 &&
                                                  abs(($moveBreakdown['paper'] - $totalMoves/3) / $totalMoves) < 0.1 &&
                                                  abs(($moveBreakdown['scissors'] - $totalMoves/3) / $totalMoves) < 0.1;
                        @endphp

                        @if($perfectDistribution)
                            This model uses a balanced strategy, playing rock, paper, and scissors with nearly equal frequency.
                        @else
                            This model favors {{ $highestMove }} more frequently than other moves, which might make its strategy more predictable.
                        @endif
                    </p>
                </div>
            @else
                <div class="text-sm text-gray-600">
                    No RPS match data available for this model.
                </div>
            @endif
        </x-ui.card>
    </div>

    @if($opponents->count() > 0)
        <x-ui.card title="Performance Against Other Models" class="mb-6">
            <x-ui.table :headers="['Model', 'Matches', 'Win Rate', '']">
                @foreach($opponents->sortByDesc('win_rate_against') as $opponent)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap font-medium">
                            <a href="{{ route('models.show', $opponent) }}" class="text-gray-900 hover:text-indigo-600">
                                {{ $opponent->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $matchesCount = \App\Models\RpsMatch::where(function ($query) use ($aiModel, $opponent) {
                                    $query->where('player1_id', $aiModel->id)->where('player2_id', $opponent->id)
                                        ->orWhere('player1_id', $opponent->id)->where('player2_id', $aiModel->id);
                                })->count();
                            @endphp
                            {{ $matchesCount }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="mr-2">{{ number_format($opponent->win_rate_against * 100, 1) }}%</span>
                                <div class="relative w-24 h-2 bg-gray-200 rounded-full">
                                    <div class="absolute top-0 left-0 h-2 {{ $opponent->win_rate_against > 0.5 ? 'bg-green-500' : ($opponent->win_rate_against === 0.5 ? 'bg-yellow-500' : 'bg-red-500') }} rounded-full" style="width: {{ $opponent->win_rate_against * 100 }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @php
                                $url = route('rps.index') . '?player1=' . $aiModel->id . '&player2=' . $opponent->id;
                            @endphp
                            <x-ui.button :href="$url" variant="secondary" class="text-xs px-3 py-1">
                                View Matches
                            </x-ui.button>
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>
        </x-ui.card>
    @endif

    <x-ui.card title="Recent Rock Paper Scissors Matches">
        @if($rpsMatches->count() > 0)
            <x-ui.table :headers="['ID', 'Opponent', 'Score', 'Result', 'Rounds', 'Date']">
                @foreach($rpsMatches as $match)
                    @php
                        $isPlayer1 = $match->player1_id === $aiModel->id;
                        $opponent = $isPlayer1 ? $match->player2 : $match->player1;
                        $aiModelScore = $isPlayer1 ? $match->player1_score : $match->player2_score;
                        $opponentScore = $isPlayer1 ? $match->player2_score : $match->player1_score;
                        $result = $match->isTie() ? 'tie' : ($match->winner_id === $aiModel->id ? 'win' : 'loss');
                    @endphp
                    <tr class="hover:bg-gray-50 {{ $result === 'win' ? 'bg-green-50' : ($result === 'loss' ? 'bg-red-50' : '') }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('rps.show', $match) }}" class="text-indigo-600 hover:text-indigo-900">
                                {{ $match->id }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('models.show', $opponent) }}" class="text-gray-900 hover:text-indigo-600">
                                {{ $opponent->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $aiModelScore }} - {{ $opponentScore }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($result === 'win')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Win
                                </span>
                            @elseif($result === 'loss')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Loss
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Tie
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $match->rounds_played }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $match->created_at->format('Y-m-d H:i') }}
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>

            <div class="mt-4">
                {{ $rpsMatches->links() }}
            </div>
        @else
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            No RPS matches found for this model.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </x-ui.card>
</x-layouts::app>
