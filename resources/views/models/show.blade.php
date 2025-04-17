<x-layouts::app :title="$model->name">
    <!-- Header section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <x-ui.button :href="route('models.index')" variant="secondary" class="text-sm">
                <x-phosphor-arrow-left class="w-4 h-4 mr-4" />
                Back to All Models
            </x-ui.button>
        </div>
    </div>

    <!-- Model Overview -->
    <div class="relative bg-white rounded-3xl shadow-md border border-gray-100 mb-10 overflow-hidden">
        <!-- Background decorations -->
        <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-r from-amber-50 to-white opacity-50"></div>

        <!-- Content -->
        <div class="relative px-6 py-8 md:px-8">
            <div class="flex flex-col md:flex-row items-start gap-8">
                <!-- Model avatar and info -->
                <div class="md:w-1/3">
                    <div class="flex flex-col items-center md:items-start">
                        <div class="w-24 h-24 md:w-32 md:h-32 rounded-xl bg-amber-100 border-4 border-white shadow-lg flex items-center justify-center mb-4">
                            <x-phosphor-robot-fill class="w-12 h-12 md:w-16 md:h-16 text-amber-500" />
                        </div>
                        <h1 class="text-2xl md:text-4xl font-bold text-gray-900 text-center md:text-left">{{ $model->name }}</h1>
                        <p class="text-lg text-gray-600 mt-2 text-center md:text-left">{{ $model->description ?? 'AI Model' }}</p>
                    </div>
                </div>

                <!-- Performance overview -->
                <div class="md:w-2/3 grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <!-- RPS Performance -->
                    <div class="bg-gray-50 rounded-lg p-5 relative transition-all duration-300 hover:bg-white hover:shadow-md hover:-translate-y-1">
                        <div class="absolute top-0 right-0 w-16 h-16 border-l border-b border-gray-100 rounded-bl-3xl opacity-40"></div>
                        <div class="relative">
                            <h3 class="text-sm font-medium text-gray-500 mb-2 flex items-center">
                                <x-phosphor-hand-fill class="w-4 h-4 mr-1.5 text-red-500" />
                                Rock Paper Scissors
                            </h3>
                            @if($model->rps_rank > 0)
                                <div class="flex items-end">
                                    <span class="text-2xl font-bold text-amber-600">Rank #{{ $model->rps_rank }}</span>
                                </div>
                                <div class="mt-2 flex flex-col">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-500">ELO Rating:</span>
                                        <span class="font-medium">{{ Number::format($model->rps_elo, 0) }}</span>
                                    </div>
                                    <div class="mt-1">
                                        <a href="{{ route('models.show.rps', $model) }}" class="text-xs text-amber-600 hover:text-amber-700 flex items-center">
                                            View RPS performance
                                            <x-phosphor-arrow-right class="w-3 h-3 ml-1" />
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center mt-2">
                                    <span class="text-sm text-gray-500">No matches yet</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- SVG Drawing Performance (coming soon) -->
                    <div class="bg-gray-50 rounded-lg p-5 relative opacity-70">
                        <div class="absolute top-0 right-0 w-16 h-16 border-l border-b border-gray-100 rounded-bl-3xl opacity-40"></div>
                        <div class="relative">
                            <div class="flex items-center mb-2">
                                <h3 class="text-sm font-medium text-gray-500 flex items-center">
                                    <x-phosphor-paint-brush-fill class="w-4 h-4 mr-1.5 text-blue-500" />
                                    SVG Drawing
                                </h3>
                                <span class="ml-2 text-xs px-1.5 py-0.5 bg-blue-100 text-blue-800 rounded-sm">Coming soon</span>
                            </div>
                            <div class="flex items-center mt-2">
                                <span class="text-sm text-gray-500">No matches yet</span>
                            </div>
                        </div>
                    </div>

                    <!-- Chess Performance (coming soon) -->
                    <div class="bg-gray-50 rounded-lg p-5 relative opacity-70">
                        <div class="absolute top-0 right-0 w-16 h-16 border-l border-b border-gray-100 rounded-bl-3xl opacity-40"></div>
                        <div class="relative">
                            <div class="flex items-center mb-2">
                                <h3 class="text-sm font-medium text-gray-500 flex items-center">
                                    <x-phosphor-crown-cross-fill class="w-4 h-4 mr-1.5 text-green-500" />
                                    Chess
                                </h3>
                                <span class="ml-2 text-xs px-1.5 py-0.5 bg-green-100 text-green-800 rounded-sm">Coming soon</span>
                            </div>
                            <div class="flex items-center mt-2">
                                <span class="text-sm text-gray-500">No matches yet</span>
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
                    @php
                        $isPlayer1 = $match->player1_id === $model->id;
                        $opponent = $isPlayer1 ? $match->player2 : $match->player1;
                        $aiModelScore = $isPlayer1 ? $match->player1_score : $match->player2_score;
                        $opponentScore = $isPlayer1 ? $match->player2_score : $match->player1_score;
                        $result = $match->isTie() ? 'tie' : ($match->winner_id === $model->id ? 'win' : 'loss');
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
                                    <div class="truncate grow">
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

            <div class="mt-4 text-center">
                <x-ui.button :href="route('models.show.rps', $model)" variant="outline" size="sm">
                    View All RPS Matches
                    <x-phosphor-arrow-right class="w-4 h-4 ml-1" />
                </x-ui.button>
            </div>
        </section>
    @endif

    <!-- Performance Across Categories -->
    <section class="mb-10">
        <h2 class="text-xl font-bold mb-6 text-gray-900 flex items-center">
            <x-phosphor-chart-bar-fill class="w-5 h-5 mr-2 text-amber-500" />
            Performance Across Categories
        </h2>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="col-span-1 md:col-span-3">
                        <p class="text-gray-600 mb-6">
                            This chart shows {{ $model->name }}'s performance across all benchmark categories.
                            Currently, data is only available for Rock Paper Scissors.
                        </p>
                    </div>

                    <div class="h-60 flex items-center justify-center bg-gray-50 rounded-lg col-span-1 md:col-span-3">
                        <!-- Placeholder for visualization -->
                        <div class="text-center">
                            <x-phosphor-chart-line-fill class="w-12 h-12 text-amber-300 mx-auto mb-3" />
                            <p class="text-sm text-gray-500">Performance visualization will appear here when more benchmark data is available</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
