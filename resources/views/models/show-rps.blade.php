<x-layouts::app :title="$model->name . ' - Rock Paper Scissors'">
    <!-- Header Component -->
    <x-models.header :model="$model" :activeTab="$activeTab" />

    <!-- Main content -->
    <div class="grid grid-cols-1 xl:grid-cols-3 xl:gap-8">
        <!-- Left sidebar: Strategy and move patterns -->
        <div class="space-y-8">
            <!-- Move tendencies chart -->
            <x-ui.card title="Move Tendencies" subtitle="Frequency analysis of move choices">
                @php
                    $totalMoves = max(1, $moveBreakdown['rock'] + $moveBreakdown['paper'] + $moveBreakdown['scissors']);
                @endphp
                <div class="space-y-6">
                    <div x-data="{
                        moveData: [
                            {{ $moveBreakdown['rock'] }},
                            {{ $moveBreakdown['paper'] }},
                            {{ $moveBreakdown['scissors'] }}
                        ],
                        total: {{ $totalMoves }},
                        animate: false,
                        get percentages() {
                            return this.moveData.map(value => (value / this.total) * 100)
                        },
                        get rockStyle() {
                            return {clipPath: `inset(${this.animate ? 100 - this.percentages[0] : 100}% 0 0 0)`};
                        },
                        get paperStyle() {
                            return {clipPath: `inset(${this.animate ? 100 - this.percentages[1] : 100}% 0 0 0)`};
                        },
                        get scissorsStyle() {
                            return {clipPath: `inset(${this.animate ? 100 - this.percentages[2] : 100}% 0 0 0)`};
                        },
                        init() {
                            setTimeout(() => this.animate = true, 100);
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
                                        class="absolute inset-0 rounded-full bg-red-300"
                                        x-bind:style="rockStyle"
                                        style="transition: clip-path 1s ease-out;"
                                    ></div>
                                    <!-- Icon -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <x-fas-hand-rock class="text-red-800/70 size-8"  />
                                    </div>
                                </div>
                                <div class="text-xl font-bold">{{ Number::percentage(($moveBreakdown['rock'] / $totalMoves) * 100, 1) }}</div>
                                <div class="text-sm text-gray-500">Rock</div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ Number::abbreviate($moveBreakdown['rock'], maxPrecision: 2) }}
                                </div>
                            </div>

                            <!-- Paper -->
                            <div class="text-center" x-cloak>
                                <div class="relative aspect-square w-full max-w-[100px] mx-auto mb-3">
                                    <!-- Background circle -->
                                    <div class="absolute inset-0 rounded-full bg-gray-100"></div>
                                    <!-- Progress circle -->
                                    <div
                                        class="absolute inset-0 rounded-full bg-blue-300"
                                        x-bind:style="paperStyle"
                                        style="transition: clip-path 1s ease-out;"
                                    ></div>
                                    <!-- Icon -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <x-fas-hand-paper class="text-blue-800/70 size-8" />
                                    </div>
                                </div>
                                <div class="text-xl font-bold">{{ Number::percentage(($moveBreakdown['paper'] / $totalMoves) * 100, 1) }}</div>
                                <div class="text-sm text-gray-500">Paper</div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ Number::abbreviate($moveBreakdown['paper'], maxPrecision: 2) }}
                                </div>
                            </div>

                            <!-- Scissors -->
                            <div class="text-center" x-cloak>
                                <div class="relative aspect-square w-full max-w-[100px] mx-auto mb-3">
                                    <!-- Background circle -->
                                    <div class="absolute inset-0 rounded-full bg-gray-100"></div>
                                    <!-- Progress circle -->
                                    <div
                                        class="absolute inset-0 rounded-full bg-green-300"
                                        x-bind:style="scissorsStyle"
                                        style="transition: clip-path 1s ease-out;"
                                    ></div>
                                    <!-- Icon -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <x-fas-hand-scissors class="text-green-800/70 size-8" />
                                    </div>
                                </div>
                                <div class="text-xl font-bold">{{ Number::percentage(($moveBreakdown['scissors'] / $totalMoves) * 100, 1) }}</div>
                                <div class="text-sm text-gray-500">Scissors</div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ Number::abbreviate($moveBreakdown['scissors'], maxPrecision: 2) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Strategy Analysis</h3>
                        <div class="max-w-none bg-gray-50 text-gray-600 rounded-lg p-4 text-sm font-semibold">
                            <p>{{ $strategyAnalysis }}</p>
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
                                @if($mostImpressiveVictory->player1_id === $model->id)
                                    <div class="text-lg font-bold text-gray-900">{{ $model->name }}</div>
                                    <div class="text-xs text-gray-500">vs {{ $mostImpressiveVictory->player2->name }}</div>
                                @else
                                    <div class="text-lg font-bold text-gray-900">{{ $model->name }}</div>
                                    <div class="text-xs text-gray-500">vs {{ $mostImpressiveVictory->player1->name }}</div>
                                @endif
                            </div>

                            <div class="text-right">
                                @if($mostImpressiveVictory->player1_id === $model->id)
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
                                            <a href="{{ route('models.show', $opponent->model) }}" class="group">
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
                            <x-rps.model-match-card :match="$match" :model="$model" />
                        @endforeach
                    </div>
                </x-ui.card>
            @endif
        </div>
    </div>
</x-layouts::app>
