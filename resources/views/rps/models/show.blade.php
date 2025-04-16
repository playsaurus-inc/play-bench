<x-layouts::app :title="$aiModel->name">
    <!-- Header section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <x-ui.button :href="route('rps.models.index')" variant="secondary" class="text-sm">
                <x-phosphor-arrow-left class="w-4 h-4 mr-4" />
                Back to All Models
            </x-ui.button>

            <div class="flex items-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $rankPosition <= 3 ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800' }}">
                    <x-phosphor-trophy-fill class="w-3.5 h-3.5 mr-1 {{ $rankPosition <= 3 ? 'text-amber-600' : 'text-gray-500' }}" />
                    Rank #{{ $rankPosition }}
                </span>
            </div>
        </div>
    </div>

    <!-- Model overview -->
    <div class="relative bg-white rounded-3xl shadow-md border border-gray-100 mb-10 overflow-hidden">
        <!-- Background decorations -->
        <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-r from-amber-50 to-white opacity-50"></div>

        <!-- Content -->
        <div class="relative px-6 py-8 md:px-8">
            <div class="flex flex-col md:flex-row items-start gap-8">
                <!-- Model avatar and info -->
                <div class="md:w-1/2">
                    <div class="flex flex-col items-center md:items-start">
                        <div class="w-24 h-24 md:w-32 md:h-32 rounded-xl bg-amber-100 border-4 border-white shadow-lg flex items-center justify-center mb-4">
                            <x-phosphor-robot-fill class="w-12 h-12 md:w-16 md:h-16 text-amber-500" />
                        </div>
                        <h1 class="text-2xl md:text-4xl font-bold text-gray-900 text-center md:text-left">{{ $aiModel->name }}</h1>
                        <p class="text-lg text-gray-600 mt-2 text-center md:text-left">{{ $aiModel->description ?? 'AI Model' }}</p>
                    </div>
                </div>

                <!-- Performance stats -->
                <div class="md:w-1/2 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Win Rate -->
                    <div class="bg-gray-50 rounded-lg p-5 relative group transition-all duration-300 hover:bg-white hover:shadow-md hover:-translate-y-1">
                        <div class="absolute top-0 right-0 w-16 h-16 border-l border-b border-gray-100 rounded-bl-3xl opacity-40"></div>
                        <div class="relative">
                            <h3 class="text-sm font-medium text-gray-500 mb-2 flex items-center">
                                <x-phosphor-chart-line-up-fill class="w-4 h-4 mr-1.5 text-amber-500" />
                                Win Rate
                            </h3>
                            <div class="flex items-end">
                                <span class="text-3xl font-bold {{ $winRate > 0.5 ? 'text-green-600' : ($winRate == 0.5 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ number_format($winRate * 100, 1) }}%
                                </span>
                            </div>
                            <div class="mt-3 w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-2 rounded-full {{ $winRate > 0.5 ? 'bg-green-500' : ($winRate == 0.5 ? 'bg-amber-500' : 'bg-red-500') }}"
                                    style="width: {{ $winRate * 100 }}%"
                                    x-data="{width: 0}"
                                    x-init="setTimeout(() => width = {{ $winRate * 100 }}, 100)"
                                    :style="`width: ${width}%`"
                                    class="transition-all duration-1000 ease-out"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Matches -->
                    <div class="bg-gray-50 rounded-lg p-5 relative group transition-all duration-300 hover:bg-white hover:shadow-md hover:-translate-y-1">
                        <div class="absolute top-0 right-0 w-16 h-16 border-l border-b border-gray-100 rounded-bl-3xl opacity-40"></div>
                        <div class="relative">
                            <h3 class="text-sm font-medium text-gray-500 mb-2 flex items-center">
                                <x-phosphor-hash-fill class="w-4 h-4 mr-1.5 text-amber-500" />
                                Total Matches
                            </h3>
                            <div class="flex items-baseline">
                                <span class="text-3xl font-bold text-gray-900">{{ $totalRpsMatches }}</span>
                                <span class="ml-2 text-sm text-gray-500">matches</span>
                            </div>
                            <div class="mt-2 flex items-center text-xs text-gray-500">
                                <span>{{ $totalRpsWins }} wins ({{ $totalRpsMatches > 0 ? number_format(($totalRpsWins / $totalRpsMatches) * 100, 1) : '0' }}%)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Left sidebar: Strategy and move patterns -->
        <div class="space-y-8">
            <!-- Move tendencies chart -->
            <x-ui.card title="Move Tendencies" subtitle="Frequency analysis of move choices">
                @php
                    $totalMoves = $moveBreakdown['rock'] + $moveBreakdown['paper'] + $moveBreakdown['scissors'];
                @endphp
                <div class="space-y-6">
                    <div x-data="{
                        moveData: [
                            {{ $moveBreakdown['rock'] }},
                            {{ $moveBreakdown['paper'] }},
                            {{ $moveBreakdown['scissors'] }}
                        ],
                        get total() { return this.moveData.reduce((a, b) => a + b, 0) },
                        get percentages() {
                            return this.moveData.map(value => (value / this.total) * 100)
                        },
                        animate: false
                        init() {
                            this.setTimeout(() => animate = true, 100);
                        },
                    }">
                        <div class="grid grid-cols-3 gap-4">
                            <!-- Rock -->
                            <div class="text-center" x-cloak>
                                <div class="relative aspect-square w-full max-w-[100px] mx-auto mb-3">
                                    <!-- Background circle -->
                                    <div class="absolute inset-0 rounded-full bg-gray-100"></div>
                                    <!-- Progress circle -->
                                    <div
                                        class="absolute inset-0 rounded-full bg-red-100"
                                        x-bind:style="{ clipPath: `circle(${animate ? percentages[0]/2 : 0}% at 50% 50%)` }"
                                        style="transition: clip-path 1s ease-out;"
                                    ></div>
                                    <!-- Icon -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <x-fas-hand-rock class="size-8"  />
                                    </div>
                                </div>
                                <div class="text-xl font-bold">{{ Number::abbreviate($moveBreakdown['rock'], precision: 2) }}</div>
                                <div class="text-sm text-gray-500">Rock</div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $totalMoves > 0 ? Number::percentage(($moveBreakdown['rock'] / $totalMoves) * 100, 1) : '0' }}%
                                </div>
                            </div>

                            <!-- Paper -->
                            <div class="text-center" x-cloak>
                                <div class="relative aspect-square w-full max-w-[100px] mx-auto mb-3">
                                    <!-- Background circle -->
                                    <div class="absolute inset-0 rounded-full bg-gray-100"></div>
                                    <!-- Progress circle -->
                                    <div class="absolute inset-0 rounded-full bg-blue-100"
                                            :style="{ clipPath: `circle(${animate ? percentages[1]/2 : 0}% at 50% 50%)` }"
                                            style="transition: clip-path 1s ease-out;"></div>
                                    <!-- Icon -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <x-fas-hand-paper class="size-8" />
                                    </div>
                                </div>
                                <div class="text-xl font-bold">{{ Number::abbreviate($moveBreakdown['paper'], precision: 2) }}</div>
                                <div class="text-sm text-gray-500">Paper</div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $totalMoves > 0 ? Number::percentage(($moveBreakdown['paper'] / $totalMoves) * 100, 1) : '0' }}%
                                </div>
                            </div>

                            <!-- Scissors -->
                            <div class="text-center" x-cloak>
                                <div class="relative aspect-square w-full max-w-[100px] mx-auto mb-3">
                                    <!-- Background circle -->
                                    <div class="absolute inset-0 rounded-full bg-gray-100"></div>
                                    <!-- Progress circle -->
                                    <div class="absolute inset-0 rounded-full bg-green-100"
                                            :style="{ clipPath: `circle(${animate ? percentages[2]/2 : 0}% at 50% 50%)` }"
                                            style="transition: clip-path 1s ease-out;"></div>
                                    <!-- Icon -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <x-fas-hand-scissors class="size-8" />
                                    </div>
                                </div>
                                <div class="text-xl font-bold">{{ Number::abbreviate($moveBreakdown['scissors'], precision: 2) }}</div>
                                <div class="text-sm text-gray-500">Scissors</div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $totalMoves > 0 ? Number::percentage(($moveBreakdown['scissors'] / $totalMoves) * 100, 1) : '0' }}%
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Strategy Analysis</h3>
                        <div class="prose prose-sm prose-amber max-w-none text-gray-600">
                            @php
                                $highestMove = array_search(max($moveBreakdown), $moveBreakdown);
                                $perfectDistribution = abs(($moveBreakdown['rock'] - $totalMoves/3) / $totalMoves) < 0.1 &&
                                                    abs(($moveBreakdown['paper'] - $totalMoves/3) / $totalMoves) < 0.1 &&
                                                    abs(($moveBreakdown['scissors'] - $totalMoves/3) / $totalMoves) < 0.1;
                            @endphp

                            @if($perfectDistribution)
                                <p>
                                    {{ $aiModel->name }} uses a highly balanced strategy, playing rock, paper, and scissors with nearly equal frequency.
                                    This makes its moves very difficult to predict, as there is no clear pattern to exploit.
                                </p>
                            @else
                                <p>
                                    {{ $aiModel->name }} shows a preference for <strong>{{ $highestMove }}</strong>, using it more frequently than other moves.
                                    This tendency could potentially be exploited by opponents who can detect and adapt to this pattern.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <!-- Most impressive victory -->
            @if($mostImpressiveVictory)
                <x-ui.card title="Most Impressive Victory" subtitle="Highest point difference win">
                    <a href="{{ route('rps.matches.show', $mostImpressiveVictory) }}" class="block bg-gradient-to-r from-amber-50 to-white p-4 rounded-lg border border-amber-100 transition-all hover:shadow-md">
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center">
                                <x-phosphor-trophy-fill class="w-5 h-5 text-amber-500 mr-2" />
                                <span class="text-sm font-medium text-amber-700">Match #{{ $mostImpressiveVictory->id }}</span>
                            </div>
                            <span class="text-xs text-gray-500">{{ $mostImpressiveVictory->created_at->format('M d, Y') }}</span>
                        </div>

                        <div class="flex items-center justify-between mb-3">
                            <div class="text-sm font-medium">
                                @if($mostImpressiveVictory->player1_id === $aiModel->id)
                                    <div class="text-lg font-bold text-gray-900">{{ $aiModel->name }}</div>
                                    <div class="text-xs text-gray-500">vs {{ $mostImpressiveVictory->player2->name }}</div>
                                @else
                                    <div class="text-lg font-bold text-gray-900">{{ $aiModel->name }}</div>
                                    <div class="text-xs text-gray-500">vs {{ $mostImpressiveVictory->player1->name }}</div>
                                @endif
                            </div>

                            <div class="text-right">
                                @if($mostImpressiveVictory->player1_id === $aiModel->id)
                                    <div class="text-xl font-bold text-green-600">{{ $mostImpressiveVictory->player1_score }} - {{ $mostImpressiveVictory->player2_score }}</div>
                                @else
                                    <div class="text-xl font-bold text-green-600">{{ $mostImpressiveVictory->player2_score }} - {{ $mostImpressiveVictory->player1_score }}</div>
                                @endif
                                <div class="text-xs text-gray-500">{{ $mostImpressiveVictory->rounds_played }} rounds</div>
                            </div>
                        </div>

                        <div class="text-xs text-amber-600 flex items-center justify-end group">
                            View match details
                            <x-phosphor-arrow-right class="w-3.5 h-3.5 ml-1 transition-transform group-hover:translate-x-0.5" />
                        </div>
                    </a>
                </x-ui.card>
            @endif
        </div>

        <!-- Main content: Performance against other models and match history -->
        <div class="md:col-span-2 space-y-8">
            <!-- Performance against other models -->
            @if($opponents->count() > 0)
                <x-ui.card title="Performance Against Other Models" subtitle="Head-to-head statistics">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Model
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Matches
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Win Rate
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" x-data="{ hoverRow: null }">
                                @foreach($opponents->sortByDesc('win_rate') as $opponent)
                                    <tr
                                        x-on:mouseenter="hoverRow = {{ $opponent->model->id }}"
                                        x-on:mouseleave="hoverRow = null"
                                        class="hover:bg-gray-50 transition-colors"
                                    >
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <a href="{{ route('rps.models.show', $opponent->model) }}" class="group">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 w-8 h-8 rounded-md bg-gray-100 flex items-center justify-center overflow-hidden group-hover:bg-amber-50 transition-colors">
                                                        <x-phosphor-robot-fill class="w-4 h-4 text-gray-500 group-hover:text-amber-600" />
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-sm font-medium text-gray-900 group-hover:text-amber-600 transition-colors">{{ $opponent->model->name }}</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right">
                                            <div class="text-sm text-gray-900">{{ $opponent->total_matches }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right">
                                            <div class="text-sm font-medium {{ $opponent->win_rate > 0.5 ? 'text-green-600' : ($opponent->win_rate == 0.5 ? 'text-amber-600' : 'text-red-600') }}">
                                                {{ Number::percentage($opponent->win_rate * 100, 1) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($opponent->win_rate > 0.7)
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Dominates
                                                </span>
                                            @elseif($opponent->win_rate > 0.5)
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Counters
                                                </span>
                                            @elseif($opponent->win_rate == 0.5)
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Evenly matched
                                                </span>
                                            @elseif($opponent->win_rate < 0.3)
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Weak against
                                                </span>
                                            @else
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">
                                                    Struggles
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>
            @endif

            <!-- Recent matches -->
            @if($rpsMatches->count() > 0)
                <x-ui.card title="Recent Rock Paper Scissors Matches" subtitle="Latest performance data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($rpsMatches as $match)
                            @php
                                $isPlayer1 = $match->player1_id === $aiModel->id;
                                $opponent = $isPlayer1 ? $match->player2 : $match->player1;
                                $aiModelScore = $isPlayer1 ? $match->player1_score : $match->player2_score;
                                $opponentScore = $isPlayer1 ? $match->player2_score : $match->player1_score;
                                $result = $match->isTie() ? 'tie' : ($match->winner_id === $aiModel->id ? 'win' : 'loss');
                                $resultColor = $result === 'win' ? 'green' : ($result === 'loss' ? 'red' : 'gray');
                            @endphp
                            <a href="{{ route('rps.matches.show', $match) }}" class="block bg-white rounded-lg border border-gray-200 hover:shadow-md transition-all">
                                <div class="p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center">
                                            <span class="font-medium text-gray-900">#{{ $match->id }}</span>
                                            <span class="mx-2 text-gray-400">•</span>
                                            <span class="text-sm text-gray-500">{{ $match->created_at->format('M d') }}</span>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $resultColor }}-100 text-{{ $resultColor }}-800">
                                            {{ ucfirst($result) }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center mr-2">
                                                <x-phosphor-robot-fill class="w-4 h-4 text-gray-500" />
                                            </div>
                                            <div class="truncate max-w-[120px]">
                                                <div class="text-sm font-medium text-gray-900">{{ $opponent->name }}</div>
                                            </div>
                                        </div>

                                        <div class="flex items-center">
                                            <div class="text-lg font-bold {{ $result === 'win' ? 'text-green-600' : ($result === 'loss' ? 'text-red-600' : 'text-gray-600') }}">
                                                {{ $aiModelScore }} - {{ $opponentScore }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-2 text-xs text-gray-500 flex justify-between items-center">
                                        <span>{{ $match->rounds_played }} rounds</span>
                                        <span class="text-amber-600 flex items-center">
                                            Details
                                            <x-phosphor-arrow-right class="w-3 h-3 ml-1" />
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </x-ui.card>
            @endif
        </div>
    </div>
</x-layouts::app>
