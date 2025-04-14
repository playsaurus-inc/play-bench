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
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 flex items-center gap-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-full shadow-md">
                            <x-phosphor-hand-fill class="h-6 w-6 text-white" />
                        </div>
                        Rock Paper Scissors Benchmark
                    </h1>
                    <p class="text-lg text-gray-600 max-w-2xl">
                        Watch AI models compete in the classic game of Rock Paper Scissors to reveal their strategic thinking capabilities and pattern recognition skills.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800"
                             x-data="animatedCounter({{ $stats['total_matches'] }})">
                            <x-phosphor-robot-fill class="w-3.5 h-3.5 mr-1" />
                            <span x-text="current"></span> Matches
                        </div>
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800"
                             x-data="animatedCounter({{ $stats['total_rounds'] }})">
                            <x-phosphor-circle-notch-fill class="w-3.5 h-3.5 mr-1" />
                            <span x-text="current"></span> Rounds
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                            <x-phosphor-chart-pie-fill class="w-3.5 h-3.5 mr-1" />
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
                        <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-red-100 border-4 border-white shadow-lg flex items-center justify-center absolute -left-4 md:-left-8 top-4 md:top-0 z-10"
                             :class="{'scale-110 shadow-xl': currentMove === 0}"
                             style="transition: all 0.3s ease">
                            <x-ui.rps-icon move="rock" size="lg" x-bind:class="{'text-red-600': currentMove === 0}" />
                        </div>

                        <!-- Paper -->
                        <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-blue-100 border-4 border-white shadow-lg flex items-center justify-center z-20"
                             :class="{'scale-110 shadow-xl': currentMove === 1}"
                             style="transition: all 0.3s ease">
                            <x-ui.rps-icon move="paper" size="lg" x-bind:class="{'text-blue-600': currentMove === 1}" />
                        </div>

                        <!-- Scissors -->
                        <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-green-100 border-4 border-white shadow-lg flex items-center justify-center absolute -right-4 md:-right-8 top-4 md:top-0 z-10"
                             :class="{'scale-110 shadow-xl': currentMove === 2}"
                             style="transition: all 0.3s ease">
                            <x-ui.rps-icon move="scissors" size="lg" x-bind:class="{'text-green-600': currentMove === 2}" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and search -->
        <div class="mb-10" x-data="{ openFilters: false }">
            <x-ui.card>
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                    <h2 class="text-lg font-semibold flex items-center">
                        <x-phosphor-funnel-fill class="w-5 h-5 mr-2 text-amber-500" />
                        Filter Matches
                    </h2>
                    <button @click="openFilters = !openFilters"
                            class="inline-flex items-center text-sm text-amber-600 hover:text-amber-700 focus-ring rounded-md py-1 px-2">
                        <span x-text="openFilters ? 'Hide filters' : 'Show filters'">Show filters</span>
                        <x-phosphor-caret-down
                            class="w-4 h-4 ml-1 transition-transform duration-300"
                            x-bind:class="{'transform rotate-180': openFilters}"
                        />
                    </button>
                </div>

                <form x-show="openFilters"
                      x-transition:enter="transition ease-out duration-200"
                      x-transition:enter-start="opacity-0 -translate-y-4"
                      x-transition:enter-end="opacity-100 translate-y-0"
                      x-transition:leave="transition ease-in duration-150"
                      x-transition:leave-start="opacity-100 translate-y-0"
                      x-transition:leave-end="opacity-0 -translate-y-4"
                      x-cloak
                      action="{{ route('rps.index') }}"
                      method="GET"
                      class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="player1" class="block text-sm font-medium text-gray-700 mb-1">Player 1</label>
                            <select id="player1" name="player1" class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-500 focus:ring-opacity-50">
                                <option value="">Any model</option>
                                @foreach($models as $model)
                                    <option value="{{ $model->id }}" {{ request('player1') == $model->id ? 'selected' : '' }}>
                                        {{ $model->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="player2" class="block text-sm font-medium text-gray-700 mb-1">Player 2</label>
                            <select id="player2" name="player2" class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-500 focus:ring-opacity-50">
                                <option value="">Any model</option>
                                @foreach($models as $model)
                                    <option value="{{ $model->id }}" {{ request('player2') == $model->id ? 'selected' : '' }}>
                                        {{ $model->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="min_rounds" class="block text-sm font-medium text-gray-700 mb-1">Minimum Rounds</label>
                            <select id="min_rounds" name="min_rounds" class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-500 focus:ring-opacity-50">
                                <option value="">Any number</option>
                                <option value="10" {{ request('min_rounds') == 10 ? 'selected' : '' }}>At least 10</option>
                                <option value="20" {{ request('min_rounds') == 20 ? 'selected' : '' }}>At least 20</option>
                                <option value="50" {{ request('min_rounds') == 50 ? 'selected' : '' }}>At least 50</option>
                                <option value="100" {{ request('min_rounds') == 100 ? 'selected' : '' }}>At least 100</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        @if(request()->anyFilled(['player1', 'player2', 'min_rounds']))
                            <a href="{{ route('rps.index') }}" class="text-sm text-gray-600 hover:text-gray-800">
                                Clear filters
                            </a>
                        @endif
                        <x-ui.button type="submit">
                            <x-phosphor-funnel-fill class="size-4 mr-4" />
                            Apply Filters
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>

        <!-- Featured Matches -->
        <section id="featured-matches" class="mb-16 scroll-mt-20">
            <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
                <x-phosphor-star-fill class="w-6 h-6 mr-2 text-amber-500" />
                Featured Matches
            </h2>

            @if($featuredMatches['close_matches']->count() > 0)
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
                        @foreach($featuredMatches['close_matches'] as $match)
                            <div class="hover-scale">
                                <x-ui.match-card :match="$match" />
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($featuredMatches['most_rounds_matches']->count() > 0)
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
                        @foreach($featuredMatches['most_rounds_matches'] as $match)
                            <div class="hover-scale">
                                <x-ui.match-card :match="$match" />
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($featuredMatches['close_matches']->count() === 0 && $featuredMatches['most_rounds_matches']->count() === 0)
                <div class="bg-amber-50 border border-amber-100 rounded-xl p-8 text-center">
                    <div class="flex justify-center mb-4">
                        <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center">
                            <x-phosphor-star-fill class="w-8 h-8 text-amber-500" />
                        </div>
                    </div>
                    <h3 class="text-lg font-medium text-amber-900 mb-2">No featured matches yet</h3>
                    <p class="text-amber-700 max-w-lg mx-auto">
                        We're still collecting exciting matches to feature. Check back soon to see the most interesting and competitive games.
                    </p>
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
                            <x-ui.rps-icon move="rock" size="md" />
                        </div>
                        <span class="text-gray-700">Rock crushes Scissors</span>
                    </div>

                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <x-ui.rps-icon move="scissors" size="md" />
                        </div>
                        <span class="text-gray-700">Scissors cuts Paper</span>
                    </div>

                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <x-ui.rps-icon move="paper" size="md" />
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
