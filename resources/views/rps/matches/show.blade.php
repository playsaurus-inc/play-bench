<x-layouts::app :title="'Match #' . $rpsMatch->id . ' Details'">
    <!-- Header section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <x-ui.button :href="route('rps.index')" variant="secondary" class="text-xs sm:text-sm">
                <x-phosphor-arrow-left class="size-4 mr-1 sm:mr-2" />
                <span class="hidden xs:inline">Back to All Matches</span>
                <span class="xs:hidden">Back</span>
            </x-ui.button>

            <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                <x-phosphor-clock-fill class="w-3 h-3 mr-1" />
                {{ $rpsMatch->created_at->format('M d, Y') }}
            </span>
        </div>

        <h1 class="text-2xl sm:text-3xl font-bold mt-4 sm:mt-6 text-center">
            Rock Paper Scissors Match #{{ $rpsMatch->id }}
        </h1>
    </div>

    <!-- Match overview card -->
    <div class="relative bg-white rounded-3xl shadow-md border border-gray-100 mb-8 sm:mb-10 overflow-hidden">
        <!-- Background decorations -->
        <div class="absolute top-0 left-0 w-full h-26 bg-gradient-to-r from-red-100 via-white to-blue-100 opacity-50"></div>

        <!-- Content -->
        <div class="relative p-4 sm:p-6 md:p-8">
            <!-- Players and score -->
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-6 md:gap-8">
                <!-- Player 1 -->
                <div class="flex flex-col items-center md:items-start">
                    <a href="{{ route('models.show.rps', $rpsMatch->player1) }}" class="group">
                        <div class="flex flex-col sm:flex-row items-center mb-3">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-red-100 border-4 border-white shadow-lg flex items-center justify-center mb-3 sm:mb-0 sm:mr-4 group-hover:ring-2 group-hover:ring-red-300 transition-all">
                                <x-phosphor-robot-fill class="w-6 h-6 sm:w-8 sm:h-8 text-red-500" />
                            </div>
                            <div class="text-center sm:text-left">
                                <div class="text-lg sm:text-xl font-bold text-gray-900 group-hover:text-red-600 transition-colors">{{ $rpsMatch->player1->name }}</div>
                                <div class="text-xs sm:text-sm text-gray-500">Player 1</div>

                                @if($rpsMatch->player1_elo_before && $rpsMatch->player1_elo_after)
                                <div class="text-xs mt-1 flex items-center justify-center sm:justify-start whitespace-nowrap">
                                    <span class="font-mono">ELO: {{ number_format($rpsMatch->player1_elo_before) }}</span>
                                    <x-phosphor-arrow-right class="w-3 h-3 mx-1 text-gray-400" />
                                    <span class="font-mono {{ $rpsMatch->player1_elo_after > $rpsMatch->player1_elo_before ? 'text-green-600' : ($rpsMatch->player1_elo_after < $rpsMatch->player1_elo_before ? 'text-red-600' : 'text-gray-600') }}">
                                        {{ number_format($rpsMatch->player1_elo_after) }}
                                        @if($rpsMatch->player1_elo_after != $rpsMatch->player1_elo_before)
                                            ({{ $rpsMatch->player1_elo_after > $rpsMatch->player1_elo_before ? '+' : '' }}{{ number_format($rpsMatch->player1_elo_after - $rpsMatch->player1_elo_before, 1) }})
                                        @endif
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Score -->
                <div class="relative flex flex-col items-center order-first md:order-none mb-4 md:mb-0">
                    <div class="flex items-center space-x-3">
                        <div class="text-3xl sm:text-4xl font-bold {{ !$rpsMatch->isTie() && $rpsMatch->winner_id === $rpsMatch->player1_id ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $rpsMatch->player1_score }}
                        </div>
                        <span class="text-xl text-gray-400">-</span>
                        <div class="text-3xl sm:text-4xl font-bold {{ !$rpsMatch->isTie() && $rpsMatch->winner_id === $rpsMatch->player2_id ? 'text-blue-600' : 'text-gray-800' }}">
                            {{ $rpsMatch->player2_score }}
                        </div>
                    </div>

                    <div class="mt-3 flex items-center justify-center">
                        @if($rpsMatch->isTie())
                            <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-semibold bg-gray-100 text-gray-800">
                                <x-phosphor-equals-fill class="w-3 h-3 sm:w-4 sm:h-4 mr-1" />
                                Tie
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-semibold {{ $rpsMatch->winner_id === $rpsMatch->player1_id ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                <x-phosphor-trophy-fill class="w-3 h-3 sm:w-4 sm:h-4 mr-1" />
                                {{ $rpsMatch->winner->name }} wins
                            </span>
                        @endif
                    </div>

                    <div class="mt-1 text-xs text-gray-500">
                        {{ $rpsMatch->rounds_played }} total rounds
                    </div>

                    <!-- Win rates -->
                    <div class="mt-3 sm:mt-4 w-full max-w-xs grid grid-cols-3 gap-2 sm:gap-3 text-center">
                        <div>
                            <div class="text-xs sm:text-sm text-gray-500">P1 Wins</div>
                            <div class="font-medium text-xs sm:text-sm {{ $rpsMatch->getPlayer1WinRate() > 0.5 ? 'text-red-600' : 'text-gray-800' }}">
                                {{ Number::percentage($rpsMatch->getPlayer1WinRate() * 100, precision: 1) }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs sm:text-sm text-gray-500">Ties</div>
                            <div class="font-medium text-xs sm:text-sm text-gray-800">
                                {{ Number::percentage($rpsMatch->getTieRate() * 100, precision: 1) }}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs sm:text-sm text-gray-500">P2 Wins</div>
                            <div class="font-medium text-xs sm:text-sm {{ $rpsMatch->getPlayer2WinRate() > 0.5 ? 'text-blue-600' : 'text-gray-800' }}">
                                {{ Number::percentage($rpsMatch->getPlayer2WinRate() * 100, precision: 1) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Player 2 -->
                <div class="flex flex-col items-center md:items-end">
                    <a href="{{ route('models.show.rps', $rpsMatch->player2) }}" class="group">
                        <div class="flex flex-col sm:flex-row items-center mb-3">
                            <div class="sm:order-2 w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-blue-100 border-4 border-white shadow-lg flex items-center justify-center mb-3 sm:mb-0 sm:ml-4 group-hover:ring-2 group-hover:ring-blue-300 transition-all">
                                <x-phosphor-robot-fill class="w-6 h-6 sm:w-8 sm:h-8 text-blue-500" />
                            </div>
                            <div class="sm:order-1 text-center sm:text-right">
                                <div class="text-lg sm:text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $rpsMatch->player2->name }}</div>
                                <div class="text-xs sm:text-sm text-gray-500">Player 2</div>

                                @if($rpsMatch->player2_elo_before && $rpsMatch->player2_elo_after)
                                <div class="text-xs mt-1 flex items-center justify-center sm:justify-end whitespace-nowrap">
                                    <span class="font-mono">ELO: {{ Number::format($rpsMatch->player2_elo_before, 0) }}</span>
                                    <x-phosphor-arrow-right class="w-3 h-3 mx-1 text-gray-400" />
                                    <span class="font-mono {{ $rpsMatch->player2_elo_after > $rpsMatch->player2_elo_before ? 'text-green-600' : ($rpsMatch->player2_elo_after < $rpsMatch->player2_elo_before ? 'text-red-600' : 'text-gray-600') }}">
                                        {{ Number::format($rpsMatch->player2_elo_after, 0) }}
                                        @if($rpsMatch->player2_elo_after != $rpsMatch->player2_elo_before)
                                            ({{ $rpsMatch->player2_elo_after > $rpsMatch->player2_elo_before ? '+' : '' }}{{ Number::format($rpsMatch->player2_elo_after - $rpsMatch->player2_elo_before, 1) }})
                                        @endif
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Move analysis -->
    @php
        $player1Moves = $rpsMatch->player1_move_distribution;
        $player2Moves = $rpsMatch->player2_move_distribution;
        $player1Percents = $rpsMatch->player1_move_distribution_percentages;
        $player2Percents = $rpsMatch->player2_move_distribution_percentages;
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 mb-8 sm:mb-10">
        <!-- Move distribution -->
        <x-ui.card title="Move Distribution" subtitle="Analysis of move choices by each player">
            <div class="space-y-6 sm:space-y-8">
                <!-- Player 1 moves -->
                <div>
                    <h3 class="text-base font-medium flex items-center mb-2 sm:mb-3">
                        <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                        {{ $rpsMatch->player1->name }}
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                        <!-- Rock -->
                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4 flex flex-row items-center">
                            <x-fas-hand-rock class="size-6 sm:size-8 text-gray-400 mr-2 sm:mr-4" />
                            <div class="flex flex-col grow">
                                <div class="flex items-center justify-between mb-1 sm:mb-2">
                                    <span class="text-xs sm:text-sm font-medium">Rock</span>
                                    <span class="text-xs sm:text-sm font-bold">{{ $player1Moves['rock'] }}</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-2 bg-red-500 rounded-full" style="width: {{ $player1Percents['rock'] * 100 }}%"></div>
                                </div>
                                <div class="mt-1 text-xs text-gray-500 text-right">{{ Number::percentage($player1Percents['rock'] * 100, 1) }}</div>
                            </div>
                        </div>

                        <!-- Paper -->
                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4 flex flex-row items-center">
                            <x-fas-hand-paper class="size-6 sm:size-8 text-gray-400 mr-2 sm:mr-4" />
                            <div class="flex flex-col grow">
                                <div class="flex items-center justify-between mb-1 sm:mb-2">
                                    <span class="text-xs sm:text-sm font-medium">Paper</span>
                                    <span class="text-xs sm:text-sm font-bold">{{ $player1Moves['paper'] }}</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-2 bg-red-500 rounded-full" style="width: {{ $player1Percents['paper'] * 100 }}%"></div>
                                </div>
                                <div class="mt-1 text-xs text-gray-500 text-right">{{ Number::percentage($player1Percents['paper'] * 100, 1) }}</div>
                            </div>
                        </div>

                        <!-- Scissors -->
                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4 flex flex-row items-center">
                            <x-fas-hand-scissors class="size-6 sm:size-8 text-gray-400 mr-2 sm:mr-4" />
                            <div class="flex flex-col grow">
                                <div class="flex items-center justify-between mb-1 sm:mb-2">
                                    <span class="text-xs sm:text-sm font-medium">Scissors</span>
                                    <span class="text-xs sm:text-sm font-bold">{{ $player1Moves['scissors'] }}</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-2 bg-red-500 rounded-full" style="width: {{ $player1Percents['scissors'] * 100 }}%"></div>
                                </div>
                                <div class="mt-1 text-xs text-gray-500 text-right">{{ Number::percentage($player1Percents['scissors'] * 100, 1) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Player 2 moves -->
                <div>
                    <h3 class="text-base font-medium flex items-center mb-2 sm:mb-3">
                        <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>
                        {{ $rpsMatch->player2->name }}
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                        <!-- Rock -->
                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4 flex flex-row items-center">
                            <x-fas-hand-rock class="size-6 sm:size-8 text-gray-400 mr-2 sm:mr-4" />
                            <div class="flex flex-col grow">
                                <div class="flex items-center justify-between mb-1 sm:mb-2">
                                    <span class="text-xs sm:text-sm font-medium">Rock</span>
                                    <span class="text-xs sm:text-sm font-bold">{{ $player2Moves['rock'] }}</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-2 bg-blue-500 rounded-full" style="width: {{ $player2Percents['rock'] * 100 }}%"></div>
                                </div>
                                <div class="mt-1 text-xs text-gray-500 text-right">{{ Number::percentage($player2Percents['rock'] * 100, 1) }}</div>
                            </div>
                        </div>

                        <!-- Paper -->
                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4 flex flex-row items-center">
                            <x-fas-hand-paper class="size-6 sm:size-8 text-gray-400 mr-2 sm:mr-4" />
                            <div class="flex flex-col grow">
                                <div class="flex items-center justify-between mb-1 sm:mb-2">
                                    <span class="text-xs sm:text-sm font-medium">Paper</span>
                                    <span class="text-xs sm:text-sm font-bold">{{ $player2Moves['paper'] }}</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-2 bg-blue-500 rounded-full" style="width: {{ $player2Percents['paper'] * 100 }}%"></div>
                                </div>
                                <div class="mt-1 text-xs text-gray-500 text-right">{{ Number::percentage($player2Percents['paper'] * 100, 1) }}</div>
                            </div>
                        </div>

                        <!-- Scissors -->
                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4 flex flex-row items-center">
                            <x-fas-hand-scissors class="size-6 sm:size-8 text-gray-400 mr-2 sm:mr-4" />
                            <div class="flex flex-col grow">
                                <div class="flex items-center justify-between mb-1 sm:mb-2">
                                    <span class="text-xs sm:text-sm font-medium">Scissors</span>
                                    <span class="text-xs sm:text-sm font-bold">{{ $player2Moves['scissors'] }}</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-2 bg-blue-500 rounded-full" style="width: {{ $player2Percents['scissors'] * 100 }}%"></div>
                                </div>
                                <div class="mt-1 text-xs text-gray-500 text-right">{{ Number::percentage($player2Percents['scissors'] * 100, 1) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.card>

        <!-- Strategy analysis -->
        <x-ui.card title="Strategy Analysis" subtitle="Performance insights from the match">
            <div class="space-y-4 sm:space-y-6">
                <!-- Win streaks -->
                <div class="mb-4 sm:mb-6">
                    <h3 class="text-base font-medium mb-2 sm:mb-3">Win Streaks</h3>

                    <div class="grid grid-cols-2 gap-3 sm:gap-6">
                        <div class="bg-red-50 rounded-lg p-3 sm:p-4">
                            <div class="text-xs sm:text-sm text-gray-600 mb-1">{{ $rpsMatch->player1->name }}</div>
                            <div class="flex items-baseline">
                                <div class="text-xl sm:text-2xl font-bold text-red-600">{{ $rpsMatch->player1_win_streak }}</div>
                                <div class="text-xs text-gray-500 ml-2">consecutive wins</div>
                            </div>
                        </div>

                        <div class="bg-blue-50 rounded-lg p-3 sm:p-4">
                            <div class="text-xs sm:text-sm text-gray-600 mb-1">{{ $rpsMatch->player2->name }}</div>
                            <div class="flex items-baseline">
                                <div class="text-xl sm:text-2xl font-bold text-blue-600">{{ $rpsMatch->player2_win_streak }}</div>
                                <div class="text-xs text-gray-500 ml-2">consecutive wins</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analysis summary -->
                <div>
                    <h3 class="text-base font-medium mb-2 sm:mb-3">Strategic Insights</h3>

                    <div class="max-w-none bg-gray-50 text-gray-600 rounded-lg p-3 sm:p-4 text-xs sm:text-sm font-semibold">
                        <p>{{ $rpsMatch->getStrategicInsights() }}</p>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>

    <!-- Match Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
        <x-rps.charts.cumulative-wins-chart :rpsMatch="$rpsMatch" />

        <x-rps.charts.win-percentage-chart :rpsMatch="$rpsMatch" />
    </div>

    <!-- Round-by-Round Results -->
    <div x-data="{ showAllRounds: false }">
        <x-ui.card>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 sm:mb-6 gap-2">
                <h2 class="text-base sm:text-lg font-semibold text-gray-900">Round-by-Round Results</h2>

                @if(count($rpsMatch->getRounds()) > 10)
                    <button @click="showAllRounds = !showAllRounds" class="text-xs sm:text-sm text-amber-600 hover:text-amber-700 flex items-center self-start sm:self-auto">
                        <span x-text="showAllRounds ? 'Show less rounds' : 'Show all rounds'">Show all rounds</span>
                        <x-phosphor-caret-down class="w-3 h-3 sm:w-4 sm:h-4 ml-1" x-show="!showAllRounds" />
                        <x-phosphor-caret-up class="w-3 h-3 sm:w-4 sm:h-4 ml-1" x-show="showAllRounds" x-cloak />
                    </button>
                @endif
            </div>

            @php $rounds = $rpsMatch->getRounds(); @endphp
            @if(count($rounds) > 0)
                <div class="-mx-4 sm:mx-0 sm:rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 border-y border-gray-200">
                                <tr>
                                    <th scope="col" class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        #
                                    </th>
                                    <th scope="col" class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        P1
                                    </th>
                                    <th scope="col" class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        P2
                                    </th>
                                    <th scope="col" class="px-2 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Result
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($rounds as $index => $round)
                                    <tr class="hover:bg-gray-50" x-show="showAllRounds || {{ $index < 10 ? 'true' : 'false' }}">
                                        <td class="px-2 sm:px-4 py-2 sm:py-3 whitespace-nowrap text-xs sm:text-sm text-gray-500">
                                            {{ $round['round_number'] }}
                                        </td>
                                        <td class="px-2 sm:px-4 py-2 sm:py-3 whitespace-nowrap">
                                            @php
                                                $win = $round['result'] === 'player1_win';
                                            @endphp
                                            <div class="flex items-center">
                                                @if ($round['player1_move'] === 'rock')
                                                    <x-fas-hand-rock @class(['size-5 sm:size-6 mr-1 sm:mr-2', 'text-green-500' => $win, 'text-gray-500' => !$win]) />
                                                @elseif ($round['player1_move'] === 'paper')
                                                    <x-fas-hand-paper @class(['size-5 sm:size-6 mr-1 sm:mr-2', 'text-green-500' => $win, 'text-gray-500' => !$win]) />
                                                @elseif ($round['player1_move'] === 'scissors')
                                                    <x-fas-hand-scissors @class(['size-5 sm:size-6 mr-1 sm:mr-2', 'text-green-500' => $win, 'text-gray-500' => !$win]) />
                                                @endif
                                                <span class="hidden sm:inline text-xs sm:text-sm capitalize {{ $win ? 'font-medium text-green-700' : '' }}">
                                                    {{ $round['player1_move'] }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-2 sm:px-4 py-2 sm:py-3 whitespace-nowrap">
                                            @php
                                                $win = $round['result'] === 'player2_win';
                                            @endphp
                                            <div class="flex items-center">
                                                @if ($round['player2_move'] === 'rock')
                                                    <x-fas-hand-rock @class(['size-5 sm:size-6 mr-1 sm:mr-2', 'text-green-500' => $win, 'text-gray-500' => !$win]) />
                                                @elseif ($round['player2_move'] === 'paper')
                                                    <x-fas-hand-paper @class(['size-5 sm:size-6 mr-1 sm:mr-2', 'text-green-500' => $win, 'text-gray-500' => !$win]) />
                                                @elseif ($round['player2_move'] === 'scissors')
                                                    <x-fas-hand-scissors @class(['size-5 sm:size-6 mr-1 sm:mr-2', 'text-green-500' => $win, 'text-gray-500' => !$win]) />
                                                @endif
                                                <span class="hidden sm:inline text-xs sm:text-sm capitalize {{ $win ? 'font-medium text-green-700' : '' }}">
                                                    {{ $round['player2_move'] }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-2 sm:px-4 py-2 sm:py-3 whitespace-nowrap">
                                            @if($round['result'] === 'player1_win')
                                                <span class="px-1.5 sm:px-2 py-0.5 inline-flex items-center rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1 hidden sm:inline-block"></span>
                                                    <span class="hidden sm:inline">{{ $rpsMatch->player1->name }} wins</span>
                                                    <span class="sm:hidden">P1</span>
                                                </span>
                                            @elseif($round['result'] === 'player2_win')
                                                <span class="px-1.5 sm:px-2 py-0.5 inline-flex items-center rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-1 hidden sm:inline-block"></span>
                                                    <span class="hidden sm:inline">{{ $rpsMatch->player2->name }} wins</span>
                                                    <span class="sm:hidden">P2</span>
                                                </span>
                                            @else
                                                <span class="px-1.5 sm:px-2 py-0.5 inline-flex items-center rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <span class="w-1.5 h-1.5 bg-gray-500 rounded-full mr-1 hidden sm:inline-block"></span>
                                                    Tie
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 flex justify-center" x-show="!showAllRounds && {{ count($rounds) > 10 ? 'true' : 'false' }}">
                    <button x-on:click="showAllRounds = true" class="inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 border border-gray-300 rounded-md shadow-sm text-xs sm:text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Show all {{ count($rounds) }} rounds
                        <x-phosphor-caret-down class="ml-1 sm:ml-2 -mr-0.5 sm:-mr-1 h-3 w-3 sm:h-4 sm:w-4 text-gray-400" />
                    </button>
                </div>
            @else
                <div class="bg-amber-50 border-l-4 border-amber-400 p-3 sm:p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-phosphor-warning-fill class="h-4 w-4 sm:h-5 sm:w-5 text-amber-400" />
                        </div>
                        <div class="ml-3">
                            <p class="text-xs sm:text-sm text-amber-700">
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
        <section class="mt-8 sm:mt-10">
            <h2 class="text-lg sm:text-xl font-bold mb-4 sm:mb-6 text-gray-900 flex items-center">
                <x-phosphor-arrows-in-line-horizontal-fill class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-amber-500" />
                Similar Matches
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
                @foreach($similarMatches as $match)
                    <x-rps.match-card :match="$match" />
                @endforeach
            </div>
        </section>
    @endif
</x-layouts::app>
