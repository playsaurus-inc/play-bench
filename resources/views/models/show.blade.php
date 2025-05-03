<x-layouts::app :title="$model->name">
    <!-- Header Component -->
    <x-models.header :model="$model" :activeTab="$activeTab" />

    <!-- Main content with stats cards - horizontal layout -->
    <div class="mb-10 space-y-8">
        <!-- Rock Paper Scissors Card -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
            <div class="bg-gradient-to-r from-red-50 to-white px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900 flex items-center mb-1">
                    <x-phosphor-hand-fill class="size-5 mr-2 text-red-500" />
                    Rock Paper Scissors
                </h2>
                <a href="{{ route('models.show.rps', $model) }}"
                    class="text-xs text-red-600 hover:text-red-800 flex items-center">
                    View details
                    <x-phosphor-arrow-right class="w-3 h-3 ml-1" />
                </a>
            </div>
            <div class="flex flex-col md:flex-row">
                <!-- Header Section -->

                <div class="flex-grow p-6">
                    <div class="md:flex md:space-x-6">
                        <!-- Key Stats -->
                        <div class="flex flex-col md:w-1/3 mb-6 md:mb-0">
                            <div class="flex justify-around">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900">{{ $totalRpsMatches }}</div>
                                    <div class="text-xs text-gray-500">Matches</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold {{ $rpsWinRate > 0.5 ? 'text-green-600' : 'text-gray-900' }}">
                                        {{ Number::percentage($rpsWinRate * 100, 1) }}
                                    </div>
                                    <div class="text-xs text-gray-500">Win Rate</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-red-600">{{ Number::format($model->rps_elo, 0) }}</div>
                                    <div class="text-xs text-gray-500">ELO Rating</div>
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 text-xs text-gray-700 mt-6">
                                {{ $rpsStrategyAnalysis }}
                            </div>
                        </div>

                        <!-- Move Distribution -->
                        <div class="md:w-2/3">
                            @php
                                $totalMoves = max(1, $moveBreakdown['rock'] + $moveBreakdown['paper'] + $moveBreakdown['scissors']);
                                $rockPercent = ($moveBreakdown['rock'] / $totalMoves) * 100;
                                $paperPercent = ($moveBreakdown['paper'] / $totalMoves) * 100;
                                $scissorsPercent = ($moveBreakdown['scissors'] / $totalMoves) * 100;
                            @endphp

                            <div>
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Move Distribution</h3>

                                <div class="space-y-2">
                                    <!-- Rock -->
                                    <div>
                                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                                            <span class="flex items-center">
                                                <x-fas-hand-rock class="w-3 h-3 mr-1 text-red-700" /> Rock
                                            </span>
                                            <span>{{ Number::percentage($rockPercent, 1) }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ min(100, $rockPercent) }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Paper -->
                                    <div>
                                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                                            <span class="flex items-center">
                                                <x-fas-hand-paper class="w-3 h-3 mr-1 text-blue-700" /> Paper
                                            </span>
                                            <span>{{ Number::percentage($paperPercent, 1) }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ min(100, $paperPercent) }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Scissors -->
                                    <div>
                                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                                            <span class="flex items-center">
                                                <x-fas-hand-scissors class="w-3 h-3 mr-1 text-green-700" /> Scissors
                                            </span>
                                            <span>{{ Number::percentage($scissorsPercent, 1) }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ min(100, $scissorsPercent) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SVG Drawing Performance Card -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
            <div class="bg-gradient-to-r from-blue-50 to-white px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900 flex items-center mb-1">
                    <x-phosphor-paint-brush-fill class="size-5 mr-2 text-blue-500" />
                    SVG Drawing
                </h2>
                <a href="{{ route('models.show.svg', $model) }}"
                    class="text-xs text-blue-600 hover:text-blue-800 flex items-center">
                    View details
                    <x-phosphor-arrow-right class="w-3 h-3 ml-1" />
                </a>
            </div>
            <div class="flex flex-col md:flex-row">
                <div class="flex-grow p-6">
                    <div class="md:flex md:space-x-6">
                        <div class="flex flex-col md:w-1/3 mb-6 md:mb-0">
                            <div class="flex justify-around">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900">{{ $totalSvgMatches }}</div>
                                    <div class="text-xs text-gray-500">Drawings</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold {{ $svgWinRate > 0.5 ? 'text-green-600' : 'text-gray-900' }}">
                                        {{ Number::percentage($svgWinRate * 100, 1) }}
                                    </div>
                                    <div class="text-xs text-gray-500">Win Rate</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600">{{ Number::format($model->svg_elo, 0) }}</div>
                                    <div class="text-xs text-gray-500">ELO Rating</div>
                                </div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 text-xs text-gray-700 mt-6">
                                {{ $svgStrategyAnalysis }}
                            </div>
                        </div>

                        <!-- Best Artworks Gallery -->
                        <div class="md:w-2/3">
                            @if($bestArtworks->isNotEmpty())
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Top Artwork</h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-2">
                                    @foreach($bestArtworks->take(6) as $artwork)
                                        <a href="{{ route('svg.matches.show', $artwork['match']) }}"
                                           class="aspect-square bg-gray-50 rounded-lg overflow-hidden border border-gray-100 hover:shadow-md transition-all relative group">
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                @if($artwork['svg_url'])
                                                    <img src="{{ $artwork['svg_url'] }}" alt="SVG Drawing" class="max-w-full max-h-full object-contain" />
                                                @else
                                                    <x-phosphor-image-square class="w-8 h-8 text-gray-300" />
                                                @endif
                                            </div>

                                            <!-- Hover overlay -->
                                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/70 via-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-2">
                                                <div class="text-white text-xs line-clamp-2 overflow-hidden">
                                                    "{{ Str::limit($artwork['prompt'], 60) }}"
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex items-center justify-center bg-gray-50 rounded-lg p-6 h-full">
                                    <div class="text-center">
                                        <x-phosphor-image-square class="w-10 h-10 mx-auto text-gray-300 mb-2" />
                                        <p class="text-sm text-gray-500">No artwork available yet</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chess Performance Card -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200 opacity-80">
            <div class="bg-gradient-to-r from-green-50 to-white px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900 flex items-center mb-1">
                    <x-phosphor-crown-fill class="size-5 mr-2 text-green-600" />
                    Chess
                </h2>
                <a href="{{ route('models.show.chess', $model) }}"
                    class="text-xs text-green-600 hover:text-green-800 flex items-center">
                    Coming soon
                    <x-phosphor-arrow-right class="w-3 h-3 ml-1" />
                </a>
            </div>
            <div class="flex flex-col md:flex-row">
                <div class="flex-grow p-6 flex items-center">
                    <div class="w-full">
                        <div class="flex items-center justify-center py-4 md:py-0">
                            <div class="text-left flex items-center gap-4">
                                <div class="bg-gray-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-3 md:mb-0">
                                    <x-phosphor-crown-fill class="w-8 h-8 text-gray-300" />
                                </div>
                                <div>
                                    <h3 class="text-base font-medium text-gray-600 mb-1">Coming Soon</h3>
                                    <p class="text-xs text-gray-500 max-w-xs">
                                        Chess benchmark will evaluate this model's strategic thinking and planning capabilities.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Rock Paper Scissors Matches -->
    @if($rpsMatches->count() > 0)
        <section class="mb-10">
            <h2 class="text-xl font-bold mb-6 text-gray-900 flex items-center">
                <x-phosphor-hand-fill class="w-5 h-5 mr-2 text-red-500" />
                Recent Rock Paper Scissors Matches
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($rpsMatches as $match)
                    <x-rps.model-match-card :match="$match" :model="$model" />
                @endforeach
            </div>

            <div class="mt-4 text-center">
                <x-ui.button :href="route('models.show.rps', $model)" variant="outline" size="sm">
                    View All RPS Matches
                    <x-phosphor-arrow-right class="w-4 h-4 ml-1" />
                </x-ui.button>
            </div>
        </section>
    @endif

    <!-- Recent SVG Drawings -->
    @if($svgMatches->count() > 0)
        <section class="mb-10">
            <h2 class="text-xl font-bold mb-6 text-gray-900 flex items-center">
                <x-phosphor-paint-brush-fill class="w-5 h-5 mr-2 text-blue-500" />
                Recent SVG Drawing Matches
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($svgMatches as $match)
                    <x-svg.model-match-card :match="$match" :model="$model" />
                @endforeach
            </div>

            <div class="mt-4 text-center">
                <x-ui.button :href="route('models.show.svg', $model)" variant="outline" size="sm">
                    View All SVG Drawings
                    <x-phosphor-arrow-right class="w-4 h-4 ml-1" />
                </x-ui.button>
            </div>
        </section>
    @endif

    <!-- Why Multiple Benchmarks Matter -->
    <section class="mt-12 mb-6">
        <h2 class="text-xl font-bold mb-6 text-gray-900 flex items-center">
            <x-phosphor-lightbulb-fill class="w-5 h-5 mr-2 text-amber-500" />
            Why Multiple Benchmarks Matter
        </h2>

        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
            <div class="prose prose-amber max-w-none">
                <p>
                    Different benchmarks test different aspects of AI capability. By evaluating models across multiple
                    tasks, we can build a more comprehensive understanding of their strengths and limitations.
                </p>

                <p>
                    Models that excel in strategic games like Rock Paper Scissors demonstrate pattern recognition and
                    adaptive learning, while strong performance in visual tasks like SVG drawing indicates
                    spatial understanding and creative capabilities.
                </p>

                <p>
                    Chess requires long-term planning and complex decision trees, testing an entirely different set of
                    reasoning skills.
                </p>

                <p>
                    A model that performs well across all benchmarks demonstrates a broader range of intelligence
                    capabilities that more closely resembles general intelligence.
                </p>
            </div>
        </div>
    </section>
</x-layouts::app>
