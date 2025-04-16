<x-layouts::app :title="'Match #' . $rpsMatch->id . ' Details'">
    <!-- Header section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <x-ui.button :href="route('rps.index')" variant="secondary" class="text-sm">
                <x-phosphor-arrow-left class="size-4 mr-4" />
                Back to All Matches
            </x-ui.button>

            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                <x-phosphor-clock-fill class="w-3.5 h-3.5 mr-1" />
                {{ $rpsMatch->created_at->format('M d, Y') }}
            </span>
        </div>

        <h1 class="text-3xl font-bold mt-6 text-center">
            Rock Paper Scissors Match #{{ $rpsMatch->id }}
        </h1>
    </div>

    <!-- Match overview card -->
    <div class="relative bg-white rounded-3xl shadow-md border border-gray-100 mb-10 overflow-hidden">
        <!-- Background decorations -->
        <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-r from-red-100 via-white to-blue-100 opacity-50"></div>

        <!-- Content -->
        <div class="relative p-8">
            <!-- Players and score -->
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-8">
                <!-- Player 1 -->
                <div class="flex flex-col items-center md:items-start">
                    <a href="{{ route('rps.models.show', $rpsMatch->player1) }}" class="group">
                        <div class="flex flex-col sm:flex-row items-center mb-3">
                            <div class="w-20 h-20 rounded-full bg-red-100 border-4 border-white shadow-lg flex items-center justify-center mb-3 sm:mb-0 sm:mr-4 group-hover:ring-2 group-hover:ring-red-300 transition-all">
                                <x-phosphor-robot-fill class="w-8 h-8 text-red-500" />
                            </div>
                            <div class="text-center sm:text-left">
                                <div class="text-xl font-bold text-gray-900 group-hover:text-red-600 transition-colors">{{ $rpsMatch->player1->name }}</div>
                                <div class="text-sm text-gray-500">Player 1</div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Score -->
                <div class="relative flex flex-col items-center">
                    <div class="flex items-center space-x-3">
                        <div class="text-4xl font-bold {{ !$rpsMatch->isTie() && $rpsMatch->winner_id === $rpsMatch->player1_id ? 'text-red-600' : 'text-gray-800' }}">{{ $rpsMatch->player1_score }}</div>
                        <span class="text-xl text-gray-400">-</span>
                        <div class="text-4xl font-bold {{ !$rpsMatch->isTie() && $rpsMatch->winner_id === $rpsMatch->player2_id ? 'text-blue-600' : 'text-gray-800' }}">{{ $rpsMatch->player2_score }}</div>
                    </div>

                    <div class="mt-3 flex items-center justify-center">
                        @if($rpsMatch->isTie())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">
                                <x-phosphor-equals-fill class="w-4 h-4 mr-1" />
                                Tie
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $rpsMatch->winner_id === $rpsMatch->player1_id ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                <x-phosphor-trophy-fill class="w-4 h-4 mr-1" />
                                {{ $rpsMatch->winner->name }} wins
                            </span>
                        @endif
                    </div>

                    <div class="mt-1 text-xs text-gray-500">
                        {{ $rpsMatch->rounds_played }} total rounds
                    </div>

                    <!-- Win rates -->
                    <div class="mt-4 w-full max-w-xs grid grid-cols-3 gap-3 text-center">
                        <div>
                            <div class="text-sm text-gray-500">Player 1 Wins</div>
                            <div class="font-medium text-sm {{ $rpsMatch->getPlayer1WinRate() > 0.5 ? 'text-red-600' : 'text-gray-800' }}">
                                {{ Number::percentage($rpsMatch->getPlayer1WinRate() * 100, precision: 1) }}
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Ties</div>
                            <div class="font-medium text-sm text-gray-800">
                                {{ Number::percentage($rpsMatch->getTieRate() * 100, precision: 1) }}
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Player 2 Wins</div>
                            <div class="font-medium text-sm {{ $rpsMatch->getPlayer2WinRate() > 0.5 ? 'text-blue-600' : 'text-gray-800' }}">
                                {{ Number::percentage($rpsMatch->getPlayer2WinRate() * 100, precision: 1) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Player 2 -->
                <div class="flex flex-col items-center md:items-end">
                    <a href="{{ route('rps.models.show', $rpsMatch->player2) }}" class="group">
                        <div class="flex flex-col sm:flex-row items-center mb-3">
                            <div class="sm:order-2 w-20 h-20 rounded-full bg-blue-100 border-4 border-white shadow-lg flex items-center justify-center mb-3 sm:mb-0 sm:ml-4 group-hover:ring-2 group-hover:ring-blue-300 transition-all">
                                <x-phosphor-robot-fill class="w-8 h-8 text-blue-500" />
                            </div>
                            <div class="sm:order-1 text-center sm:text-right">
                                <div class="text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $rpsMatch->player2->name }}</div>
                                <div class="text-sm text-gray-500">Player 2</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Move analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
        <!-- Move distribution -->
        <x-ui.card title="Move Distribution" subtitle="Analysis of move choices by each player">
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

                // Calculate percentages
                $player1Total = array_sum($moveBreakdown['player1']);
                $player2Total = array_sum($moveBreakdown['player2']);

                $player1Percentages = [
                    'rock' => $player1Total > 0 ? ($moveBreakdown['player1']['rock'] / $player1Total) * 100 : 0,
                    'paper' => $player1Total > 0 ? ($moveBreakdown['player1']['paper'] / $player1Total) * 100 : 0,
                    'scissors' => $player1Total > 0 ? ($moveBreakdown['player1']['scissors'] / $player1Total) * 100 : 0,
                ];

                $player2Percentages = [
                    'rock' => $player2Total > 0 ? ($moveBreakdown['player2']['rock'] / $player2Total) * 100 : 0,
                    'paper' => $player2Total > 0 ? ($moveBreakdown['player2']['paper'] / $player2Total) * 100 : 0,
                    'scissors' => $player2Total > 0 ? ($moveBreakdown['player2']['scissors'] / $player2Total) * 100 : 0,
                ];
            @endphp

            <div class="space-y-8">
                <!-- Player 1 moves -->
                <div>
                    <h3 class="text-base font-medium flex items-center mb-3">
                        <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                        {{ $rpsMatch->player1->name }}
                    </h3>

                    <div class="grid grid-cols-3 gap-4">
                        <!-- Rock -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <x-fas-hand-rock class="mr-2" />
                                    <span class="text-sm font-medium">Rock</span>
                                </div>
                                <span class="text-sm font-bold">{{ $moveBreakdown['player1']['rock'] }}</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-2 bg-red-500 rounded-full" style="width: {{ $player1Percentages['rock'] }}%"></div>
                            </div>
                            <div class="mt-1 text-xs text-gray-500 text-right">{{ Number::percentage($player1Percentages['rock'], 1) }}</div>
                        </div>

                        <!-- Paper -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <x-fas-hand-paper class="mr-2" />
                                    <span class="text-sm font-medium">Paper</span>
                                </div>
                                <span class="text-sm font-bold">{{ $moveBreakdown['player1']['paper'] }}</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-2 bg-red-500 rounded-full" style="width: {{ $player1Percentages['paper'] }}%"></div>
                            </div>
                            <div class="mt-1 text-xs text-gray-500 text-right">{{ Number::percentage($player1Percentages['paper'], 1) }}</div>
                        </div>

                        <!-- Scissors -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <x-fas-hand-scissors class="mr-2" />
                                    <span class="text-sm font-medium">Scissors</span>
                                </div>
                                <span class="text-sm font-bold">{{ $moveBreakdown['player1']['scissors'] }}</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-2 bg-red-500 rounded-full" style="width: {{ $player1Percentages['scissors'] }}%"></div>
                            </div>
                            <div class="mt-1 text-xs text-gray-500 text-right">{{ number_format($player1Percentages['scissors'], 1) }}%</div>
                        </div>
                    </div>
                </div>

                <!-- Player 2 moves -->
                <div>
                    <h3 class="text-base font-medium flex items-center mb-3">
                        <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                        {{ $rpsMatch->player2->name }}
                    </h3>

                    <div class="grid grid-cols-3 gap-4">
                        <!-- Rock -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <x-fas-hand-rock class="mr-2" />
                                    <span class="text-sm font-medium">Rock</span>
                                </div>
                                <span class="text-sm font-bold">{{ $moveBreakdown['player2']['rock'] }}</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-2 bg-blue-500 rounded-full" style="width: {{ $player2Percentages['rock'] }}%"></div>
                            </div>
                            <div class="mt-1 text-xs text-gray-500 text-right">{{ number_format($player2Percentages['rock'], 1) }}%</div>
                        </div>

                        <!-- Paper -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <x-fas-hand-paper class="mr-2" />
                                    <span class="text-sm font-medium">Paper</span>
                                </div>
                                <span class="text-sm font-bold">{{ $moveBreakdown['player2']['paper'] }}</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-2 bg-blue-500 rounded-full" style="width: {{ $player2Percentages['paper'] }}%"></div>
                            </div>
                            <div class="mt-1 text-xs text-gray-500 text-right">{{ number_format($player2Percentages['paper'], 1) }}%</div>
                        </div>

                        <!-- Scissors -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <x-fas-hand-scissors class="mr-2" />
                                    <span class="text-sm font-medium">Scissors</span>
                                </div>
                                <span class="text-sm font-bold">{{ $moveBreakdown['player2']['scissors'] }}</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-2 bg-blue-500 rounded-full" style="width: {{ $player2Percentages['scissors'] }}%"></div>
                            </div>
                            <div class="mt-1 text-xs text-gray-500 text-right">{{ number_format($player2Percentages['scissors'], 1) }}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.card>

        <!-- Strategy analysis -->
        <x-ui.card title="Strategy Analysis" subtitle="Performance insights from the match">
            <div class="space-y-6">
                <!-- Win streaks -->
                <div class="mb-6">
                    <h3 class="text-base font-medium mb-3">Win Streaks</h3>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="bg-red-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">{{ $rpsMatch->player1->name }}</div>
                            <div class="flex items-baseline">
                                <div class="text-2xl font-bold text-red-600">{{ $rpsMatch->player1_win_streak }}</div>
                                <div class="text-xs text-gray-500 ml-2">consecutive wins</div>
                            </div>
                        </div>

                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">{{ $rpsMatch->player2->name }}</div>
                            <div class="flex items-baseline">
                                <div class="text-2xl font-bold text-blue-600">{{ $rpsMatch->player2_win_streak }}</div>
                                <div class="text-xs text-gray-500 ml-2">consecutive wins</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analysis summary -->
                <div>
                    <h3 class="text-base font-medium mb-3">Strategic Insights</h3>

                    <div class="prose prose-sm prose-amber max-w-none">
                        @if($rpsMatch->isTie())
                            <p>
                                This match ended in a tie, with both models demonstrating equally effective strategies. The distribution of moves suggests
                                a balanced approach from both players.
                            </p>
                        @elseif($rpsMatch->player1_score > $rpsMatch->player2_score)
                            <p>
                                {{ $rpsMatch->player1->name }} demonstrated a superior strategy in this match, winning {{ $rpsMatch->player1_score }} out of {{ $rpsMatch->rounds_played }} rounds
                                ({{ number_format($rpsMatch->getPlayer1WinRate() * 100, 1) }}% win rate).

                                @if($player1Percentages['rock'] > 40 || $player1Percentages['paper'] > 40 || $player1Percentages['scissors'] > 40)
                                    They showed a preference for {{ array_search(max($player1Percentages), $player1Percentages) }}, using it in {{ number_format(max($player1Percentages), 1) }}% of rounds.
                                @else
                                    They maintained a balanced distribution of moves, making their strategy harder to predict.
                                @endif
                            </p>
                        @else
                            <p>
                                {{ $rpsMatch->player2->name }} demonstrated a superior strategy in this match, winning {{ $rpsMatch->player2_score }} out of {{ $rpsMatch->rounds_played }} rounds
                                ({{ number_format($rpsMatch->getPlayer2WinRate() * 100, 1) }}% win rate).

                                @if($player2Percentages['rock'] > 40 || $player2Percentages['paper'] > 40 || $player2Percentages['scissors'] > 40)
                                    They showed a preference for {{ array_search(max($player2Percentages), $player2Percentages) }}, using it in {{ number_format(max($player2Percentages), 1) }}% of rounds.
                                @else
                                    They maintained a balanced distribution of moves, making their strategy harder to predict.
                                @endif
                            </p>
                        @endif

                        <p>
                            @php
                                $tiePct = $rpsMatch->getTieRate() * 100;
                            @endphp

                            @if($tiePct > 40)
                                The high tie rate ({{ number_format($tiePct, 1) }}%) suggests that both models may have been using similar strategies or were effectively countering each other's moves.
                            @elseif($tiePct < 20)
                                The low tie rate ({{ number_format($tiePct, 1) }}%) indicates that the models were using distinctly different strategies, rarely making the same move.
                            @else
                                The match had a moderate tie rate of {{ number_format($tiePct, 1) }}%, typical for Rock Paper Scissors games.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>

    <!-- Round-by-Round Results -->
    <div x-data="{ showAllRounds: false }">
        <x-ui.card>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900">Round-by-Round Results</h2>

                @if(count($rpsMatch->getRounds()) > 10)
                    <button @click="showAllRounds = !showAllRounds" class="text-sm text-amber-600 hover:text-amber-700 flex items-center">
                        <span x-text="showAllRounds ? 'Show less rounds' : 'Show all rounds'">Show all rounds</span>
                        <x-phosphor-caret-down class="w-4 h-4 ml-1" x-show="!showAllRounds" />
                        <x-phosphor-caret-up class="w-4 h-4 ml-1" x-show="showAllRounds" x-cloak />
                    </button>
                @endif
            </div>

            @php $rounds = $rpsMatch->getRounds(); @endphp
            @if(count($rounds) > 0)
                <div class="overflow-x-auto rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 border-y border-gray-200">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Round
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    {{ $rpsMatch->player1->name }}
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    {{ $rpsMatch->player2->name }}
                                </th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Result
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($rounds as $index => $round)
                                <tr class="hover:bg-gray-50" x-show="showAllRounds || {{ $index < 10 ? 'true' : 'false' }}">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $round['round_number'] }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @php
                                            $win = $round['result'] === 'player1_win';
                                        @endphp
                                        <div class="flex items-center">
                                            @if ($round['player1_move'] === 'rock')
                                                <x-fas-hand-rock @class(['size-6 mr-2', 'text-green-500' => $win, 'text-gray-500' => !$win]) />
                                            @elseif ($round['player1_move'] === 'paper')
                                                <x-fas-hand-paper @class(['size-6 mr-2', 'text-green-500' => $win, 'text-gray-500' => !$win]) />
                                            @elseif ($round['player1_move'] === 'scissors')
                                                <x-fas-hand-scissors @class(['size-6 mr-2', 'text-green-500' => $win, 'text-gray-500' => !$win]) />
                                            @endif
                                            <span @class(['text-sm capitalize', 'font-medium text-green-700' => $win])>
                                                {{ $round['player1_move'] }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @php
                                            $win = $round['result'] === 'player2_win';
                                        @endphp
                                        <div class="flex items-center">
                                            @if ($round['player2_move'] === 'rock')
                                                <x-fas-hand-rock @class(['size-6 mr-2', 'text-green-500' => $win, 'text-gray-500' => !$win]) />
                                            @elseif ($round['player2_move'] === 'paper')
                                                <x-fas-hand-paper @class(['size-6 mr-2', 'text-green-500' => $win, 'text-gray-500' => !$win]) />
                                            @elseif ($round['player2_move'] === 'scissors')
                                                <x-fas-hand-scissors @class(['size-6 mr-2', 'text-green-500' => $win, 'text-gray-500' => !$win]) />
                                            @endif
                                            <span @class(['text-sm capitalize', 'font-medium text-green-700' => $win])>
                                                {{ $round['player2_move'] }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($round['result'] === 'player1_win')
                                            <span class="px-2 py-0.5 inline-flex items-center rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1"></span>
                                                {{ $rpsMatch->player1->name }} wins
                                            </span>
                                        @elseif($round['result'] === 'player2_win')
                                            <span class="px-2 py-0.5 inline-flex items-center rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1"></span>
                                                {{ $rpsMatch->player2->name }} wins
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 inline-flex items-center rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <span class="w-1.5 h-1.5 bg-gray-500 rounded-full mr-1"></span>
                                                Tie
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-center" x-show="!showAllRounds && {{ count($rounds) > 10 ? 'true' : 'false' }}">
                    <button @click="showAllRounds = true" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Show all {{ count($rounds) }} rounds
                        <x-phosphor-caret-down class="ml-2 -mr-1 h-4 w-4 text-gray-400" />
                    </button>
                </div>
            @else
                <div class="bg-amber-50 border-l-4 border-amber-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-phosphor-warning-fill class="h-5 w-5 text-amber-400" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-amber-700">
                                No round data available for this match.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </x-ui.card>
    </div>

    <!-- Similar matches -->
    @if($similarMatches->count() > 0)
        <section class="mt-10">
            <h2 class="text-xl font-bold mb-6 text-gray-900 flex items-center">
                <x-phosphor-arrows-in-line-horizontal-fill class="w-5 h-5 mr-2 text-amber-500" />
                Similar Matches
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($similarMatches as $match)
                    <x-ui.rps-match-card :match="$match" :compact="true" />
                @endforeach
            </div>
        </section>
    @endif
</x-layouts::app>
