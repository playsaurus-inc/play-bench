<x-layouts::app :title="'Rock Paper Scissors Matches'">
    <!-- Hero section -->
    <div class="relative mb-10 bg-gradient-to-br from-amber-50 to-white rounded-3xl shadow-sm border border-amber-100 overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute right-0 top-0 w-64 h-64 bg-amber-100 rounded-full -translate-y-1/2 translate-x-1/4 opacity-70"></div>
        <div class="absolute left-0 bottom-0 w-32 h-32 bg-amber-50 rounded-full translate-y-1/2 -translate-x-1/4 opacity-80"></div>

        <!-- Content -->
        <div class="relative px-8 py-10 md:py-16 flex flex-col md:flex-row items-center">
            <div class="md:w-2/3 mb-8 md:mb-0">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 flex items-center gap-5">
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-full shadow-md">
                        <x-phosphor-hand-fill class="size-6 text-white" />
                    </div>
                    Rock Paper Scissors Benchmark
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl">
                    Watch AI models compete in the classic game of Rock Paper Scissors to reveal their
                    strategic thinking capabilities and pattern recognition skills.
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <div
                        class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-amber-100 text-amber-800"
                        x-data="animatedCounter({{ $totalMatchesCount }})"
                    >
                        <x-phosphor-trophy-fill class="size-6 mr-2" />
                        <span x-text="integerValue + ' matches'"></span>
                    </div>
                    <div
                        class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-amber-100 text-amber-800"
                        x-data="animatedCounter({{ $totalRoundsCount }})"
                    >
                        <x-phosphor-circle-notch-fill class="size-6 mr-2" />
                        <span x-text="integerValue + ' rounds'"></span>
                    </div>
                    <span
                        class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-amber-100 text-amber-800"
                        x-data="animatedCounter({{ $modelsCount }})"
                    >
                        <x-phosphor-robot-fill class="size-6 mr-2" />
                        <span x-text="integerValue + ' AI models competing'"></span>
                    </span>
                </div>

                <div class="mt-8">
                    <x-ui.button href="#featured-matches" variant="primary" size="lg">
                        Explore Matches
                    </x-ui.button>
                </div>
            </div>

            <!-- RPS Icon Display -->
            <div class="md:w-1/3 flex justify-center">
                <div class="relative" x-data="{ currentMove: 0 }" x-init="setInterval(() => currentMove = (currentMove + 1) % 3, 1500)">
                    <!-- Rock -->
                    <div
                        class="size-24 md:size-32 rounded-full bg-red-100 border-4 border-white shadow-lg flex items-center justify-center absolute -left-22 md:-left-28 top-0 md:top-0"
                        :class="{'scale-110 shadow-xl z-50': currentMove === 0, 'z-0': currentMove !== 0}"
                        style="transition: all 0.3s ease"
                    >
                        <x-fas-hand-rock class="size-8" x-bind:class="{'text-red-600': currentMove === 0}" />
                    </div>

                    <!-- Scissors -->
                    <div
                        class="size-24 md:size-32 rounded-full bg-green-100 border-4 border-white shadow-lg flex items-center justify-center absolute -right-22 md:-right-28 top-0 md:top-0"
                        :class="{'scale-110 shadow-xl z-50': currentMove === 2, 'z-10': currentMove !== 2}"
                        style="transition: all 0.3s ease"
                    >
                        <x-fas-hand-scissors class="size-8" x-bind:class="{'text-green-600': currentMove === 2}" />
                    </div>

                    <!-- Paper -->
                    <div
                        class="size-24 md:size-32 rounded-full bg-blue-100 border-4 border-white shadow-lg flex items-center justify-center relative"
                        :class="{'scale-110 shadow-xl z-50': currentMove === 1, 'z-20': currentMove !== 1}"
                        style="transition: all 0.3s ease"
                    >
                        <x-fas-hand-paper class="size-8" x-bind:class="{'text-blue-600': currentMove === 1}" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Matches -->
    <section id="featured-matches" class="mb-8 scroll-mt-20">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
            <x-phosphor-star-fill class="w-6 h-6 mr-2 text-amber-500" />
            Featured Matches
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @if($closeMatch)
                <div class="h-full">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-800 inline-flex items-center">
                            <x-phosphor-arrows-in-fill class="w-5 h-5 mr-2 text-amber-500" />
                            Closest Match
                        </h3>

                        <div class="text-sm text-gray-500">
                            Decided by a single point
                        </div>
                    </div>
                    <x-rps.match-card :match="$closeMatch" class="hover-scale" />
                </div>
            @endif

            @if($mostRoundsMatch)
                <div class="h-full">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-800 inline-flex items-center">
                            <x-phosphor-timer-fill class="w-5 h-5 mr-2 text-amber-500" />
                            Longest Match
                        </h3>

                        <div class="text-sm text-gray-500">
                            Most rounds played
                        </div>
                    </div>
                    <x-rps.match-card :match="$mostRoundsMatch" class="hover-scale"/>
                </div>
            @endif

            @if($latestMatch)
                <div class="h-full">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-800 inline-flex items-center">
                            <x-phosphor-clock-fill class="w-5 h-5 mr-2 text-amber-500" />
                            Latest Match
                        </h3>
                        <div class="text-sm text-gray-500">
                            Most recent played
                        </div>
                    </div>
                    <x-rps.match-card :match="$latestMatch" class="hover-scale"/>
                </div>
            @endif
        </div>
    </section>

    <!-- Model Rankings -->
    <section id="model-rankings" class="mb-10 scroll-mt-20">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
            <x-phosphor-trophy-fill class="w-6 h-6 mr-2 text-amber-500" />
            Current Model Rankings
        </h2>

        <!-- Using our new reusable component -->
        <x-rps.models-ranking-table :models="$models" />

        <div class="mt-4 text-center">
            <x-ui.button href="{{ route('rps.models.index') }}" variant="outline">
                <x-phosphor-chart-bar-fill class="w-4 h-4 mr-2" />
                View Detailed Model Analysis
            </x-ui.button>
        </div>
    </section>

    <!-- How it works -->
    <section class="mt-8 bg-gradient-to-br from-gray-50 to-amber-50/20 rounded-2xl p-8 border border-gray-100 shadow-sm">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
            <x-phosphor-question-fill class="w-6 h-6 mr-2 text-amber-500" />
            How the Rock Paper Scissors Benchmark Works
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-full mb-4">
                    <x-phosphor-strategy-fill class="w-6 h-6 text-red-600" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Strategic AI Competition</h3>
                <p class="text-gray-600">
                    AI models compete against each other in a series of Rock Paper Scissors rounds, making strategic choices based on game history.
                </p>
            </div>

            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mb-4">
                    <x-phosphor-brain-fill class="w-6 h-6 text-blue-600" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Pattern Recognition</h3>
                <p class="text-gray-600">
                    Models analyze previous moves to detect patterns and predict their opponent's next choice, demonstrating learning capabilities.
                </p>
            </div>

            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-full mb-4">
                    <x-phosphor-chart-bar-fill class="w-6 h-6 text-green-600" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Insightful Metrics</h3>
                <p class="text-gray-600">
                    Each match generates data on win rates, pattern predictability, and strategic adaptation, revealing AI capabilities.
                </p>
            </div>
        </div>

        <div class="prose prose-amber max-w-none">
            <h3>Game Rules</h3>
            <div class="flex flex-col md:flex-row md:justify-center gap-6 mb-4">
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

            <p>
                Each match typically consists of 150+ rounds, providing enough data to evaluate the model's strategic capabilities.
                A truly random strategy would result in a win rate close to 50%, but models that can successfully
                detect and exploit patterns in their opponent's moves can achieve higher win rates.
            </p>
        </div>

        <div class="mt-6 flex justify-center">
            <x-ui.button href="{{ route('rps.models.index') }}" variant="outline">
                <x-phosphor-robot-fill class="size-4 mr-3" />
                Browse AI Models
            </x-ui.button>
        </div>
    </section>
</x-layouts::app>
