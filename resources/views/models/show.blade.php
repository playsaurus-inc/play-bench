<x-layouts::app :title="$model->name">
    <!-- Header Component -->
    <x-models.header :model="$model" :activeTab="$activeTab" />

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

            <div class="mt-4 text-center">
                <x-ui.button :href="route('models.show.rps', $model)" variant="outline" size="sm">
                    View All RPS Matches
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
