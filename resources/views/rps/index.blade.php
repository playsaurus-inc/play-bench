<x-layouts::app :title="'Rock Paper Scissors Matches'">
    <div class="pb-20"
         x-data="{}"
         x-init="$nextTick(() => { $el.classList.add('page-enter-active') })"
         class="page-enter">
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
                        Watch AI models compete in the classic game of Rock Paper Scissors to reveal their strategic thinking capabilities and pattern recognition skills.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <div
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800"
                            x-data="animatedCounter({{ $stats['total_matches'] }})"
                        >
                            <x-phosphor-robot-fill class="size-4 mr-2" />
                            <span x-text="current + ' matches'"></span>
                        </div>
                        <div
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800"
                            x-data="animatedCounter({{ $stats['total_rounds'] }})"
                        >
                            <x-phosphor-circle-notch-fill class="size-4 mr-2" />
                            <span x-text="current + ' rounds'"></span>
                        </div>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800"
                        >
                            <x-phosphor-chart-pie-fill class="size-4 mr-2" />
                            {{ $stats['tie_rate'] }}% Ties
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
                    <div class="relative" x-data="{ currentMove: 0 }" x-init="setInterval(() => currentMove = (currentMove + 1) % 3, 2000)">
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
        <section id="featured-matches" class="mb-16 scroll-mt-20">
            <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
                <x-phosphor-star-fill class="w-6 h-6 mr-2 text-amber-500" />
                Featured Matches
            </h2>

            @if($closeMatches->count() > 0)
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-800 inline-flex items-center">
                            <x-phosphor-arrows-in-fill class="w-5 h-5 mr-2 text-amber-500" />
                            Closest Matches
                        </h3>

                        <div class="text-sm text-gray-500">
                            Matches decided by a single point
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($closeMatches as $match)
                            <div class="hover-scale">
                                <x-ui.match-card :match="$match" />
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($mostRoundsMatches->count() > 0)
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-800 inline-flex items-center">
                            <x-phosphor-timer-fill class="w-5 h-5 mr-2 text-amber-500" />
                            Longest Matches
                        </h3>

                        <div class="text-sm text-gray-500">
                            Matches with the most rounds played
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($mostRoundsMatches as $match)
                            <div class="hover-scale">
                                <x-ui.match-card :match="$match" />
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>

        <!-- Recent Matches -->
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                    <x-phosphor-clock-clockwise-fill class="w-6 h-6 mr-2 text-amber-500" />
                    Recent Matches
                </h2>

                @if($matches->count() > 0)
                    <div class="text-sm text-gray-500">
                        Showing {{ $matches->firstItem() ?? 0 }} to {{ $matches->lastItem() ?? 0 }} of {{ $matches->total() }} matches
                    </div>
                @endif
            </div>

            @if($matches->count() > 0)
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8"
                    x-data="{
                        visibleMatches: 0,
                        totalMatches: {{ $matches->count() }},
                        showMore() {
                            let increment = 3;
                            this.visibleMatches = Math.min(this.visibleMatches + increment, this.totalMatches);
                        },
                        init() {
                            visibleMatches = Math.min(6, totalMatches);
                            $nextTick(() => {
                                const observer = new IntersectionObserver((entries) => {
                                    entries.forEach(entry => {
                                        if (entry.isIntersecting) {
                                            entry.target.classList.add('animate-fade-in');
                                            observer.unobserve(entry.target);
                                        }
                                    });
                                }, { threshold: 0.3 });

                                document.querySelectorAll('.match-card').forEach(card => {
                                    observer.observe(card);
                                });
                            })
                        },
                    }"
                >
                    @foreach($matches as $index => $match)
                        <div class="match-card hover-scale opacity-0" x-show="{{$index}} < visibleMatches" x-transition>
                            <x-ui.match-card :match="$match" />
                        </div>
                    @endforeach

                    <template x-if="visibleMatches < totalMatches">
                        <div class="col-span-full flex justify-center my-6">
                            <x-ui.button @click="showMore()" variant="secondary">
                                <x-phosphor-arrow-down class="size-4 mr-4" />
                                Load More
                            </x-ui.button>
                        </div>
                    </template>
                </div>

                <div class="mt-10 flex justify-center">
                    {{ $matches->links() }}
                </div>
            @else
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-10 text-center">
                    <div class="flex justify-center mb-6">
                        <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center">
                            <x-phosphor-warning-fill class="w-8 h-8 text-amber-500" />
                        </div>
                    </div>
                    <h3 class="text-xl font-medium text-amber-900 mb-3">No matches found</h3>
                    <p class="text-amber-700 max-w-lg mx-auto mb-6">
                        There are no matches that match your filter criteria. Try adjusting your filters or check back later.
                    </p>

                    @if(request()->anyFilled(['player1', 'player2', 'min_rounds']))
                        <a href="{{ route('rps.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 text-white rounded-md shadow-sm hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors">
                            <x-phosphor-x class="w-4 h-4 mr-2" />
                            Clear All Filters
                        </a>
                    @endif
                </div>
            @endif
        </section>

        <!-- How it works -->
        <section class="mt-20 bg-gradient-to-br from-gray-50 to-amber-50/20 rounded-2xl p-8 border border-gray-100 shadow-sm">
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
                <x-ui.button href="{{ route('models.index') }}" variant="outline">
                    <x-phosphor-robot-fill class="w-4 h-4" />
                    Browse AI Models
                </x-ui.button>
            </div>
        </section>
    </div>

    <!-- Alpine.js animation script -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('animatedCounter', (target) => ({
                current: 0,
                target: target,
                init() {
                    this.animate();
                },
                animate() {
                    const duration = 1500;
                    const startTime = Date.now();

                    const updateCounter = () => {
                        const currentTime = Date.now();
                        const progress = Math.min((currentTime - startTime) / duration, 1);

                        this.current = Math.floor(progress * this.target);

                        if (progress < 1) {
                            requestAnimationFrame(updateCounter);
                        } else {
                            this.current = this.target;
                        }
                    };

                    updateCounter();
                }
            }));
        });
    </script>
</x-layouts::app>
