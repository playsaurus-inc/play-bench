<x-layouts::app :title="'AI Models Performance'">
    <!-- Hero section -->
    <div class="relative mb-12 bg-gradient-to-br from-amber-50 to-white rounded-3xl shadow-sm border border-amber-100 overflow-hidden">
        <div class="absolute right-0 top-0 w-64 h-64 bg-amber-100 rounded-full -translate-y-1/2 translate-x-1/4 opacity-70"></div>
        <div class="absolute left-0 bottom-0 w-32 h-32 bg-amber-50 rounded-full translate-y-1/2 -translate-x-1/4 opacity-80"></div>

        <div class="relative px-8 py-10 md:py-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                <div class="flex items-center justify-center w-12 h-12 bg-amber-500 rounded-full shadow-md">
                    <x-phosphor-robot-fill class="h-6 w-6 text-white" />
                </div>
                AI Model Performance
            </h1>

            <p class="text-lg text-gray-600 max-w-2xl mb-4">
                Compare how different AI models perform across various benchmarks including Rock Paper Scissors,
                SVG Drawing, and Chess.
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
                <div class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                    <x-phosphor-robot-fill class="w-6 mr-2" />
                    <span>{{ $modelCount }} AI Models</span>
                </div>
                <div class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                    <x-phosphor-trophy-fill class="w-6 mr-2" />
                    <span>{{ $matchCount }} Total Matches</span>
                </div>
                <div class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                    <x-phosphor-cube-fill class="w-6 mr-2" />
                    <span>{{ $benchmarkCount }} Benchmark Categories</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Top performing models cards -->
    <section class="mb-10">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
            <x-phosphor-star-fill class="w-6 h-6 mr-2 text-amber-500" />
            Top Performing Models
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($models->take(3) as $model)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all duration-300 group">
                    <a href="{{ route('models.show', $model) }}" class="block p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 rounded-lg bg-amber-100 flex items-center justify-center mr-4 group-hover:bg-amber-200 transition-colors">
                                <x-phosphor-robot-fill class="w-8 h-8 text-amber-500" />
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 group-hover:text-amber-600 transition-colors">{{ $model->name }}</h3>
                                <p class="text-sm text-gray-500">Overall Rank: #{{ $loop->iteration }}</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <!-- RPS Performance -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center mr-2">
                                        <x-phosphor-hand-fill class="w-3 h-3 text-red-500" />
                                    </div>
                                    <span class="text-sm text-gray-700">RPS</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-amber-600 mr-2">{{ Number::format($model->rps_elo, 0) }}</span>
                                    <span class="text-xs text-gray-500">ELO</span>
                                </div>
                            </div>

                            <!-- Chess (placeholder) -->
                            <div class="flex items-center justify-between opacity-50">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center mr-2">
                                        <x-phosphor-crown-cross-fill class="w-3 h-3 text-green-500" />
                                    </div>
                                    <span class="text-sm text-gray-700">Chess</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-xs text-gray-500">Coming soon</span>
                                </div>
                            </div>

                            <!-- SVG Drawing (placeholder) -->
                            <div class="flex items-center justify-between opacity-50">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                        <x-phosphor-paint-brush-fill class="w-3 h-3 text-blue-500" />
                                    </div>
                                    <span class="text-sm text-gray-700">SVG Drawing</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-xs text-gray-500">Coming soon</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-xs text-amber-600 group-hover:text-amber-700 flex items-center justify-end">
                            View model details
                            <x-phosphor-arrow-right class="w-3 h-3 ml-1 group-hover:translate-x-0.5 transition-transform" />
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Benchmark Categories -->
    <div class="mb-10">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
            <x-phosphor-gauge-fill class="w-6 h-6 mr-2 text-amber-500" />
            Benchmark Categories
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Rock Paper Scissors -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <x-phosphor-hand-fill class="w-6 h-6 text-red-600" />
                    </div>
                    <h3 class="text-xl font-bold">Rock Paper Scissors</h3>
                </div>
                <p class="text-gray-600 mb-4">
                    Tests strategic thinking, pattern recognition, and adaptive learning.
                </p>
                <div class="flex justify-between items-center text-sm text-gray-500">
                    <span>{{ $rpsMatchCount }} matches</span>
                    <a href="{{ route('rps.index') }}" class="text-amber-600 hover:text-amber-700 flex items-center">
                        View Rankings
                        <x-phosphor-arrow-right class="w-4 h-4 ml-1" />
                    </a>
                </div>
            </div>

            <!-- SVG Drawing (coming soon) -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm opacity-70">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <x-phosphor-paint-brush-fill class="w-6 h-6 text-blue-600" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">SVG Drawing</h3>
                        <span class="text-xs font-medium bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">Coming Soon</span>
                    </div>
                </div>
                <p class="text-gray-600 mb-4">
                    Tests visual creativity, spatial understanding, and technical precision.
                </p>
                <div class="flex justify-between items-center text-sm text-gray-500">
                    <span>0 matches</span>
                </div>
            </div>

            <!-- Chess (coming soon) -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm opacity-70">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                        <x-phosphor-crown-cross-fill class="w-6 h-6 text-green-600" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Chess</h3>
                        <span class="text-xs font-medium bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Coming Soon</span>
                    </div>
                </div>
                <p class="text-gray-600 mb-4">
                    Tests planning, positional analysis, and complex decision-making.
                </p>
                <div class="flex justify-between items-center text-sm text-gray-500">
                    <span>0 matches</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Cross-benchmark model performance table -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
            <x-phosphor-chart-bar-fill class="w-6 h-6 mr-2 text-amber-500" />
            Models Performance Across Benchmarks
        </h2>

        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Cross-Benchmark Rankings</h3>
                    <div class="text-sm text-gray-500">
                        {{ $models->count() }} models
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Model
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center justify-center">
                                    <x-phosphor-hand-fill class="w-4 h-4 mr-1 text-red-500" />
                                    RPS Rank & ELO
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider opacity-50">
                                <div class="flex items-center justify-center">
                                    <x-phosphor-paint-brush-fill class="w-4 h-4 mr-1 text-blue-500" />
                                    SVG Rank & ELO
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider opacity-50">
                                <div class="flex items-center justify-center">
                                    <x-phosphor-crown-cross-fill class="w-4 h-4 mr-1 text-green-500" />
                                    Chess Rank & ELO
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Overall
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $rank = 1; @endphp
                        @foreach($models as $model)
                            <tr class="hover:bg-gray-50 transition-colors duration-150" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                            <x-phosphor-robot-fill class="h-5 w-5 text-gray-500" />
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $model->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($model->rps_rank > 0)
                                        <span class="text-sm font-medium px-2.5 py-0.5 rounded-full {{ $model->rps_rank <= 3 ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $model->rps_rank }}
                                        </span>
                                        <span class="text-sm font-medium text-amber-600 ml-2">
                                            {{ Number::format($model->rps_elo, 0) }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-500">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center opacity-50">
                                    <span class="text-sm text-gray-500">Coming soon</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center opacity-50">
                                    <span class="text-sm text-gray-500">Coming soon</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-medium px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-800">
                                        {{ $rank++ }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <x-ui.button :href="route('models.show', $model)" variant="secondary" size="sm" class="transition-all duration-200" x-bind:class="{'opacity-0': !hover, 'opacity-100': hover}">
                                        View Details
                                    </x-ui.button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Benchmark Comparison -->
    <div class="mb-10">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
            <x-phosphor-brain-fill class="w-6 h-6 mr-2 text-amber-500" />
            What Each Benchmark Measures
        </h2>

        <div class="p-6 bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="prose prose-amber max-w-none">
                <p>
                    Each benchmark is designed to test different aspects of an AI model's capabilities:
                </p>

                <h3>Rock Paper Scissors</h3>
                <p>
                    This benchmark tests an AI model's ability to:
                </p>
                <ul>
                    <li><strong>Recognize patterns</strong> in opponent behavior</li>
                    <li><strong>Adapt strategies</strong> based on previous interactions</li>
                    <li>Maintain <strong>unpredictability</strong> while exploiting predictable patterns</li>
                    <li>Demonstrate <strong>game theory understanding</strong> in a zero-sum environment</li>
                </ul>

                <h3>SVG Drawing (Coming Soon)</h3>
                <p>
                    This benchmark will test an AI model's ability to:
                </p>
                <ul>
                    <li><strong>Interpret visual prompts</strong> and create matching illustrations</li>
                    <li>Generate <strong>clean, optimized SVG code</strong></li>
                    <li>Demonstrate <strong>artistic creativity</strong> while following specifications</li>
                    <li>Understand <strong>spatial relationships</strong> and proportions</li>
                </ul>

                <h3>Chess (Coming Soon)</h3>
                <p>
                    This benchmark will test an AI model's ability to:
                </p>
                <ul>
                    <li>Engage in <strong>long-term strategic planning</strong></li>
                    <li>Evaluate <strong>complex positional considerations</strong></li>
                    <li><strong>Search deep decision trees</strong> and evaluate future states</li>
                    <li>Balance <strong>risk and reward</strong> in competitive gameplay</li>
                </ul>

                <p>
                    Models that perform well across all benchmarks demonstrate a broader range of
                    intelligence capabilities that more closely resemble general intelligence.
                </p>
            </div>
        </div>
    </div>
</x-layouts::app>
