<x-layouts::app :title="'Match #' . $rpsMatch->id . ' Details'">
    <div class="mb-4">
        <x-ui.button :href="route('rps.index')" variant="secondary" class="text-sm">
            &larr; Back to All Matches
        </x-ui.button>
    </div>

    <h1 class="text-3xl font-bold mb-6">
        Match #{{ $rpsMatch->id }} Details
    </h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <x-ui.card title="Match Overview">
            <div class="flex flex-col space-y-4">
                <div class="grid grid-cols-3 gap-4 items-center">
                    <div class="text-center">
                        <a href="{{ route('models.show', $rpsMatch->player1) }}" class="block">
                            <div class="text-lg font-semibold text-gray-900 hover:text-indigo-600">
                                {{ $rpsMatch->player1->name }}
                            </div>
                            <div class="text-sm text-gray-500">Player 1</div>
                        </a>
                    </div>

                    <div class="text-center">
                        <div class="text-2xl font-bold py-2 px-4 rounded-lg bg-gray-100">
                            {{ $rpsMatch->player1_score }} - {{ $rpsMatch->player2_score }}
                        </div>
                        <div class="text-sm text-gray-500 mt-1">Score</div>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('models.show', $rpsMatch->player2) }}" class="block">
                            <div class="text-lg font-semibold text-gray-900 hover:text-indigo-600">
                                {{ $rpsMatch->player2->name }}
                            </div>
                            <div class="text-sm text-gray-500">Player 2</div>
                        </a>
                    </div>
                </div>

                <div class="py-3 border-t border-b border-gray-200">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-gray-500">Winner</div>
                            <div class="font-medium text-gray-900">
                                @if($rpsMatch->isTie())
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Tie
                                    </span>
                                @else
                                    {{ $rpsMatch->winner->name }}
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Rounds Played</div>
                            <div class="font-medium text-gray-900">{{ $rpsMatch->rounds_played }}</div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm text-gray-500">Date</div>
                        <div class="font-medium text-gray-900">
                            {{ $rpsMatch->created_at->format('Y-m-d H:i:s') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Duration</div>
                        <div class="font-medium text-gray-900">
                            @if($rpsMatch->getDuration())
                                {{ $rpsMatch->getDuration() }} seconds
                            @else
                                Unknown
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mt-4">
                    <div>
                        <div class="text-sm text-gray-500">Player 1 Win Rate</div>
                        <div class="font-medium text-gray-900">{{ number_format($rpsMatch->getPlayer1WinRate() * 100, 1) }}%</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Tie Rate</div>
                        <div class="font-medium text-gray-900">{{ number_format($rpsMatch->getTieRate() * 100, 1) }}%</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Player 2 Win Rate</div>
                        <div class="font-medium text-gray-900">{{ number_format($rpsMatch->getPlayer2WinRate() * 100, 1) }}%</div>
                    </div>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Performance Analysis" class="h-full">
            <div class="space-y-6">
                @php
                    $moveBreakdown = [
                        'player1' => [
                            'rock' => 0,
                            'paper' => 0,
                            'scissors' => 0,
                        ],
                        'player2' => [
                            'rock' => 0,
                            'paper' => 0,
                            'scissors' => 0,
                        ],
                    ];

                    foreach ($rpsMatch->getRounds() as $round) {
                        $moveBreakdown['player1'][$round['player1_move']]++;
                        $moveBreakdown['player2'][$round['player2_move']]++;
                    }
                @endphp

                <div>
                    <h4 class="text-base font-medium mb-2">Move Distribution</h4>

                    <div class="mb-4">
                        <h5 class="text-sm font-medium text-gray-500 mb-1">{{ $rpsMatch->player1->name }}</h5>
                        <div class="grid grid-cols-3 gap-2 mb-3">
                            <div class="text-center p-2 bg-gray-50 rounded">
                                <div class="text-lg font-medium">{{ $moveBreakdown['player1']['rock'] }}</div>
                                <div class="text-xs text-gray-500">Rock</div>
                            </div>
                            <div class="text-center p-2 bg-gray-50 rounded">
                                <div class="text-lg font-medium">{{ $moveBreakdown['player1']['paper'] }}</div>
                                <div class="text-xs text-gray-500">Paper</div>
                            </div>
                            <div class="text-center p-2 bg-gray-50 rounded">
                                <div class="text-lg font-medium">{{ $moveBreakdown['player1']['scissors'] }}</div>
                                <div class="text-xs text-gray-500">Scissors</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="text-sm font-medium text-gray-500 mb-1">{{ $rpsMatch->player2->name }}</h5>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="text-center p-2 bg-gray-50 rounded">
                                <div class="text-lg font-medium">{{ $moveBreakdown['player2']['rock'] }}</div>
                                <div class="text-xs text-gray-500">Rock</div>
                            </div>
                            <div class="text-center p-2 bg-gray-50 rounded">
                                <div class="text-lg font-medium">{{ $moveBreakdown['player2']['paper'] }}</div>
                                <div class="text-xs text-gray-500">Paper</div>
                            </div>
                            <div class="text-center p-2 bg-gray-50 rounded">
                                <div class="text-lg font-medium">{{ $moveBreakdown['player2']['scissors'] }}</div>
                                <div class="text-xs text-gray-500">Scissors</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <h4 class="text-base font-medium mb-2">Strategy Analysis</h4>
                    <p class="text-sm text-gray-600">
                        @if($rpsMatch->isTie())
                            Both models performed equally well, indicating a balanced match with no clear strategic advantage.
                        @elseif($rpsMatch->player1_score > $rpsMatch->player2_score)
                            {{ $rpsMatch->player1->name }} demonstrated a superior strategy by winning {{ $rpsMatch->player1_score }} of {{ $rpsMatch->rounds_played }} rounds.
                        @else
                            {{ $rpsMatch->player2->name }} demonstrated a superior strategy by winning {{ $rpsMatch->player2_score }} of {{ $rpsMatch->rounds_played }} rounds.
                        @endif
                    </p>
                </div>
            </div>
        </x-ui.card>
    </div>

    <x-ui.card title="Round-by-Round Results">
        @php $rounds = $rpsMatch->getRounds(); @endphp
        @if(count($rounds) > 0)
            <x-ui.table :headers="['Round', 'Player 1 Move', 'Player 2 Move', 'Result']">
                @foreach($rounds as $round)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $round['round_number'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap capitalize">
                            <span class="{{ $round['result'] === 'player1_win' ? 'font-medium text-green-700' : '' }}">
                                {{ $round['player1_move'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap capitalize">
                            <span class="{{ $round['result'] === 'player2_win' ? 'font-medium text-green-700' : '' }}">
                                {{ $round['player2_move'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($round['result'] === 'player1_win')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $rpsMatch->player1->name }} wins
                                </span>
                            @elseif($round['result'] === 'player2_win')
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $rpsMatch->player2->name }} wins
                                </span>
                            @else
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Tie
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>
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
                            No round data available for this match.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </x-ui.card>
</x-layouts::app>
