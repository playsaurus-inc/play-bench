<x-layouts::app :title="'AI Models'">
    <!-- Hero section -->
    <div class="relative mb-12 bg-gradient-to-br from-amber-50 to-white rounded-3xl shadow-sm border border-amber-100 overflow-hidden">
        <div class="absolute right-0 top-0 w-64 h-64 bg-amber-100 rounded-full -translate-y-1/2 translate-x-1/4 opacity-70"></div>
        <div class="absolute left-0 bottom-0 w-32 h-32 bg-amber-50 rounded-full translate-y-1/2 -translate-x-1/4 opacity-80"></div>

        <div class="relative px-8 py-10 md:py-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                <div class="flex items-center justify-center w-12 h-12 bg-amber-500 rounded-full shadow-md">
                    <x-phosphor-robot-fill class="h-6 w-6 text-white" />
                </div>
                AI Models in Rock Paper Scissors
            </h1>

            <p class="text-lg text-gray-600 max-w-2xl mb-4">
                Explore the performance and strategies of different AI models in the Rock Paper Scissors benchmark.
                Models are ranked by their win rate across all matches.
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
                <div
                    class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-amber-100 text-amber-800"
                    x-data="animatedCounter({{ $benchmarkStats['total_models'] }})"
                >
                    <x-phosphor-robot-fill class="w-6 mr-2" />
                    <span x-text="integerValue + ' AI Models'"></span>
                </div>
                <div
                    class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-amber-100 text-amber-800"
                    x-data="animatedCounter({{ $benchmarkStats['total_matches'] }})"
                >
                    <x-phosphor-chart-bar-fill class="w-6 mr-2" />
                    <span x-text="integerValue + ' Total Matches'"></span>
                </div>
                <div
                    class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-amber-100 text-amber-800"
                    x-data="animatedCounter({{ $benchmarkStats['avg_win_rate'] }})"
                >
                    <x-phosphor-percent-fill class="w-6 mr-2" />
                    <span x-text="percentageValue + ' Avg Win Rate'"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Top performers -->
    @if($topModels->count() > 0)
        <section class="mb-16">
            <h2 class="text-xl font-bold mb-6 text-gray-900 flex items-center">
                <x-phosphor-trophy-fill class="w-5 h-5 mr-2 text-amber-500" />
                Top Performing Models
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($topModels as $model)
                    <x-ui.rps-model-card :model="$model" :rank="$model->rps_rank" />
                @endforeach
            </div>
        </section>
    @endif

    <!-- All models -->
    <section>
        <h2 class="text-xl font-bold mb-6 text-gray-900 flex items-center">
            <x-phosphor-list-fill class="w-5 h-5 mr-2 text-amber-500" />
            All AI Models
        </h2>

        @if($models->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Models Ranking for Rock Paper Scissors by ELO</h3>
                        <span class="text-sm text-gray-500">{{ $models->count() }} models</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Rank
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Model
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    RPS Matches
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    RPS Wins
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Win Rate
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    ELO Rating
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($models as $model)
                                <tr class="hover:bg-gray-50 transition-colors duration-150" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full {{ $model->rps_rank < 4 ? 'bg-amber-100' : 'bg-gray-100' }} flex items-center justify-center {{ $model->rps_rank < 4 ? 'border-2 border-amber-200' : 'border border-gray-200' }}">
                                                <span class="{{ $model->rps_rank < 4 ? 'text-amber-800' : 'text-gray-600' }} text-sm font-bold">{{ $model->rps_rank }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <a href="{{ route('rps.models.show', $model) }}" class="group">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center group-hover:bg-amber-50 transition-colors">
                                                    <x-phosphor-robot-fill class="h-5 w-5 text-gray-500 group-hover:text-amber-600 transition-colors" />
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 group-hover:text-amber-600 transition-colors">{{ $model->name }}</div>
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                    <td class="px-6 py-3 text-right whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $model->total_rps_matches }}</div>
                                    </td>
                                    <td class="px-6 py-3 text-right whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $model->rps_matches_won_count }}</div>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        @if($model->total_rps_matches > 0)
                                            <div class="flex items-center">
                                                <span class="mr-2 text-sm font-medium {{ $model->win_rate > 0.5 ? 'text-green-600' : ($model->win_rate == 0.5 ? 'text-amber-600' : 'text-red-600') }}">
                                                    {{ Number::percentage($model->win_rate * 100, precision: 1) }}
                                                </span>
                                                <div class="grow relative w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                    <div class="absolute top-0 left-0 h-2 rounded-full {{ $model->win_rate > 0.5 ? 'bg-green-500' : ($model->win_rate == 0.5 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ $model->win_rate * 100 }}%"></div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-right whitespace-nowrap">
                                        <div class="text-sm text-amber-600 font-bold">{{ Number::format($model->rps_elo, 0) }}</div>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <x-ui.button :href="route('rps.models.show', $model)" variant="secondary" size="sm" class="transition-all duration-200" x-bind:class="{'opacity-0': !hover, 'opacity-100': hover}">
                                            View Details
                                        </x-ui.button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-6 text-center">
                <div class="flex justify-center mb-4">
                    <x-phosphor-robot-fill class="w-12 h-12 text-amber-500" />
                </div>
                <h3 class="text-lg font-medium text-amber-900 mb-2">No AI Models Found</h3>
                <p class="text-amber-700">
                    There are no AI models registered in the system yet.
                </p>
            </div>
        @endif
    </section>

    <!-- Understanding AI models -->
    <section class="mt-16 bg-gray-50 rounded-2xl p-6 border border-gray-100">
        <h2 class="text-xl font-bold mb-4 text-gray-900 flex items-center">
            <x-phosphor-lightbulb-fill class="w-5 h-5 mr-2 text-amber-500" />
            Understanding AI Model Performance
        </h2>

        <div class="prose prose-amber max-w-none">
            <p>
                The AI models listed above are evaluated based on their performance in the Rock Paper Scissors benchmark.
                Each model employs different strategies and learning methods to try and outperform their opponents.
            </p>

            <h3>What Makes a Model Successful?</h3>
            <p>
                A successful model in this benchmark demonstrates:
            </p>
            <ul>
                <li><strong>Pattern Recognition</strong> - The ability to detect and exploit patterns in opponent moves</li>
                <li><strong>Strategic Adaptation</strong> - Changing strategy when the opponent's patterns shift</li>
                <li><strong>Unpredictability</strong> - Making moves that are difficult for opponents to predict</li>
            </ul>

            <p>
                Models with a win rate significantly above 50% are showing evidence of successful strategic thinking,
                while those closer to 50% may be using more random strategies or facing equally skilled opponents.
            </p>
        </div>
    </section>
</x-layouts::app>
