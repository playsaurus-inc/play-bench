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
                <div class="relative w-72 h-48" x-data="{ currentMove: 0 }" x-init="setInterval(() => currentMove = (currentMove + 1) % 3, 2000)">
                    <!-- Rock -->
                    <div
                        class="absolute left-0 top-1/2 -translate-y-1/2 transform transition-all duration-700 ease-in-out"
                        :class="{'scale-110 z-20 drop-shadow-xl': currentMove === 0, 'scale-90 opacity-60': currentMove !== 0}"
                    >
                        <div class="size-28 rounded-full bg-gradient-to-br from-red-100 to-white border-4 border-red-200 shadow-lg flex items-center justify-center">
                            <x-fas-hand-rock class="size-10" x-bind:class="{'text-red-600': currentMove === 0, 'text-red-400': currentMove !== 0}" />
                        </div>
                    </div>

                    <!-- Paper -->
                    <div
                        class="absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2 transform transition-all duration-700 ease-in-out"
                        :class="{'scale-110 z-20 drop-shadow-xl': currentMove === 1, 'scale-90 opacity-60': currentMove !== 1}"
                    >
                        <div class="size-28 rounded-full bg-gradient-to-br from-blue-100 to-white border-4 border-blue-200 shadow-lg flex items-center justify-center">
                            <x-fas-hand-paper class="size-10" x-bind:class="{'text-blue-600': currentMove === 1, 'text-blue-400': currentMove !== 1}" />
                        </div>
                    </div>

                    <!-- Scissors -->
                    <div
                        class="absolute right-0 top-1/2 -translate-y-1/2 transform transition-all duration-700 ease-in-out"
                        :class="{'scale-110 z-20 drop-shadow-xl': currentMove === 2, 'scale-90 opacity-60': currentMove !== 2}"
                    >
                        <div class="size-28 rounded-full bg-gradient-to-br from-green-100 to-white border-4 border-green-200 shadow-lg flex items-center justify-center">
                            <x-fas-hand-scissors class="size-10" x-bind:class="{'text-green-600': currentMove === 2, 'text-green-400': currentMove !== 2}" />
                        </div>
                    </div>

                    <!-- Decorative floating circles -->
                    <div class="absolute -top-4 -right-4 size-10 bg-red-200 rounded-full opacity-50 animate-pulse"></div>
                    <div class="absolute -bottom-6 -left-3 size-8 bg-blue-200 rounded-full opacity-60 animate-bounce"></div>
                    <div class="absolute bottom-4 -right-6 size-6 bg-green-200 rounded-full opacity-40"
                         style="animation: float 4s ease-in-out infinite alternate;"></div>
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
            <x-ui.button href="{{ route('models.index') }}" variant="outline">
                <x-phosphor-chart-bar-fill class="w-4 h-4 mr-2" />
                View Detailed Model Analysis
            </x-ui.button>
        </div>
    </section>

    <x-rps.game-rules />
</x-layouts::app>
