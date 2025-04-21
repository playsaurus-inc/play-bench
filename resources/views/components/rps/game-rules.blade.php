<!-- "How it works" -->
<section class="mt-8 bg-gradient-to-br from-gray-50 to-amber-50/20 rounded-2xl p-8 border border-gray-100 shadow-sm">
    <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
        <x-phosphor-question-fill class="w-6 h-6 mr-2 text-amber-500" />
        How the Rock Paper Scissors Benchmark Works
    </h2>

    <!-- First row: Basic explanation boxes -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-full mb-4">
                <x-phosphor-strategy-fill class="w-6 h-6 text-red-600" />
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Strategic AI Competition</h3>
            <p class="text-gray-600">
                AI models compete against each other in a series of Rock Paper Scissors rounds, making strategic choices based on game history.
            </p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mb-4">
                <x-phosphor-brain-fill class="w-6 h-6 text-blue-600" />
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Pattern Recognition</h3>
            <p class="text-gray-600">
                Models analyze previous moves to detect patterns and predict their opponent's next choice, demonstrating learning capabilities.
            </p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-full mb-4">
                <x-phosphor-chart-bar-fill class="w-6 h-6 text-green-600" />
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Insightful Metrics</h3>
            <p class="text-gray-600">
                Each match generates data on win rates, pattern predictability, and strategic adaptation, revealing AI capabilities.
            </p>
        </div>
    </div>

    <!-- Game Rules Section -->
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm mb-8">
        <h3 class="text-xl font-semibold mb-4 text-gray-900 flex items-center">
            <x-phosphor-list-checks-fill class="w-5 h-5 mr-2 text-amber-500" />
            Game Rules
        </h3>

        <div class="flex flex-col md:flex-row md:justify-center gap-6 mb-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-3">
                    <x-fas-hand-rock class="size-6" />
                </div>
                <span class="text-gray-700">Rock crushes Scissors</span>
            </div>

            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    <x-fas-hand-scissors class="size-6" />
                </div>
                <span class="text-gray-700">Scissors cuts Paper</span>
            </div>

            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <x-fas-hand-paper class="size-6" />
                </div>
                <span class="text-gray-700">Paper covers Rock</span>
            </div>
        </div>

        <p class="text-gray-600">
            Each match typically consists of 50-150 rounds, providing enough data to evaluate the model's strategic capabilities.
            A truly random strategy would result in a win rate close to 33%, but models that can successfully
            detect and exploit patterns in their opponent's moves can achieve significantly higher win rates.
        </p>
    </div>

    <!-- Match Scoring Section -->
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm mb-8">
        <h3 class="text-xl font-semibold mb-4 text-gray-900 flex items-center">
            <x-phosphor-calculator-fill class="w-5 h-5 mr-2 text-amber-500" />
            Match Scoring & Ties
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h4 class="text-base font-medium text-gray-800 mb-2 flex items-center">
                    <x-phosphor-hand-pointing-fill class="w-4 h-4 mr-2 text-amber-600" />
                    How Winners Are Determined
                </h4>
                <p class="text-gray-600 mb-4">
                    In most cases, the AI with more winning rounds wins the match. However, when both AIs perform similarly,
                    statistical analysis is used to determine if the difference is meaningful or just random chance.
                </p>

                <div class="bg-blue-50 border-l-4 border-blue-300 px-4 py-3 rounded-r mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-phosphor-info-fill class="h-5 w-5 text-blue-500" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <span class="font-semibold">Statistical Ties:</span> When score differences are small enough
                                that they could be explained by random chance, matches are declared ties regardless of the
                                numerical score difference.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="text-base font-medium text-gray-800 mb-2 flex items-center">
                    <x-phosphor-sigma-fill class="w-4 h-4 mr-2 text-amber-600" />
                    Statistical Significance
                </h4>
                <p class="text-gray-600 mb-2">
                    The statistical significance threshold scales with the number of rounds played:
                </p>
                <ul class="space-y-1 list-inside text-gray-600 mb-4">
                    <li class="flex items-baseline">
                        <x-phosphor-dot class="size-4 text-amber-600 mr-1 flex-shrink-0" />
                        <span><strong>50 rounds:</strong> ~7 point difference needed</span>
                    </li>
                    <li class="flex items-baseline">
                        <x-phosphor-dot class="size-4 text-amber-600 mr-1 flex-shrink-0" />
                        <span><strong>100 rounds:</strong> ~10 point difference needed</span>
                    </li>
                    <li class="flex items-baseline">
                        <x-phosphor-dot class="size-4 text-amber-600 mr-1 flex-shrink-0" />
                        <span><strong>150 rounds:</strong> ~12 point difference needed</span>
                    </li>
                </ul>
                <p class="text-sm text-gray-500 italic">
                    This approach ensures that only meaningful skill differences affect rankings.
                </p>
            </div>
        </div>
    </div>

    <!-- ELO Ratings Explanation -->
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm mb-8">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="col-span-1">
                <h3 class="text-xl font-semibold mb-4 text-gray-900 flex items-center">
                    <x-phosphor-chart-line-up-fill class="w-5 h-5 mr-2 text-amber-500" />
                    Understanding ELO Ratings
                </h3>
                <p class="text-gray-600 mb-3">
                    ELO ratings provide a more sophisticated measure of performance than simple win rates:
                </p>
                <ul class="space-y-2 list-inside text-gray-600">
                    <li class="flex items-start">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-amber-100 rounded-full mr-2 mt-0.5 flex-shrink-0">
                            <span class="text-amber-800 text-xs font-medium">1</span>
                        </span>
                        <span>
                            <strong>Opponent strength matters</strong> - Beating strong models earns more points than beating weak ones
                        </span>
                    </li>
                    <li class="flex items-start">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-amber-100 rounded-full mr-2 mt-0.5 flex-shrink-0">
                            <span class="text-amber-800 text-xs font-medium">2</span>
                        </span>
                        <span>
                            <strong>Statistical ties are handled appropriately</strong> - Close matches don't add noise to the rankings
                        </span>
                    </li>
                    <li class="flex items-start">
                        <span class="inline-flex items-center justify-center w-5 h-5 bg-amber-100 rounded-full mr-2 mt-0.5 flex-shrink-0">
                            <span class="text-amber-800 text-xs font-medium">3</span>
                        </span>
                        <span>
                            <strong>Progressive adjustment</strong> - Rankings evolve as models play more matches
                        </span>
                    </li>
                </ul>
            </div>

            <div class="col-span-1 lg:col-span-2 flex items-center justify-center p-4 bg-gray-50 rounded-lg">
                <div class="w-full max-w-md">
                    <!-- Simple ELO calculation example -->
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center mr-2">
                                    <x-phosphor-robot-fill class="w-5 h-5 text-amber-500" />
                                </div>
                                <div>
                                    <div class="text-sm font-medium">Model A</div>
                                    <div class="text-xs text-gray-500">Higher ranked</div>
                                </div>
                            </div>

                            <div class="text-xl font-bold text-amber-600">
                                VS
                            </div>

                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-right">Model B</div>
                                    <div class="text-xs text-gray-500 text-right">Lower ranked</div>
                                </div>
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center ml-2">
                                    <x-phosphor-robot-fill class="w-5 h-5 text-blue-500" />
                                </div>
                            </div>
                        </div>

                        <!-- Before match -->
                        <div class="mb-4">
                            <div class="text-sm font-medium text-gray-700 mb-2">Before Match:</div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-2 rounded text-center">
                                    <div class="text-lg font-bold text-amber-600">1050</div>
                                    <div class="text-xs text-gray-500">Model A's ELO</div>
                                </div>
                                <div class="bg-gray-50 p-2 rounded text-center">
                                    <div class="text-lg font-bold text-blue-600">950</div>
                                    <div class="text-xs text-gray-500">Model B's ELO</div>
                                </div>
                            </div>
                        </div>

                        <!-- Match result -->
                        <div class="relative mb-6 mt-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center">
                                <span class="px-3 bg-white text-sm text-gray-500">Surprise! Model B wins</span>
                            </div>
                        </div>

                        <!-- After match with points change -->
                        <div>
                            <div class="text-sm font-medium text-gray-700 mb-2">After Match:</div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-2 rounded text-center relative">
                                    <div class="text-lg font-bold text-amber-600">1030</div>
                                    <div class="text-xs text-gray-500">Model A's ELO</div>
                                    <div class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-xs font-bold text-red-600 border-2 border-white">
                                        -20
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-2 rounded text-center relative">
                                    <div class="text-lg font-bold text-blue-600">970</div>
                                    <div class="text-xs text-gray-500">Model B's ELO</div>
                                    <div class="absolute -top-2 -right-2 w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-xs font-bold text-green-600 border-2 border-white">
                                        +20
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Explanation -->
                        <div class="mt-4 text-sm text-gray-600 bg-amber-50 p-2 rounded">
                            <p class="text-center">If a <strong>weaker model</strong> beats a <strong>stronger model</strong>, it gains <strong>more points</strong> than expected!</p>
                        </div>
                    </div>

                    <p class="text-xs text-center text-gray-500 mt-2">
                        ELO ratings increase more when you beat a stronger opponent and decrease more when you lose to a weaker one
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 flex justify-center">
        <x-ui.button href="{{ route('models.index') }}" variant="outline">
            <x-phosphor-robot-fill class="size-4 mr-3" />
            Browse AI Models
        </x-ui.button>
    </div>
</section>
