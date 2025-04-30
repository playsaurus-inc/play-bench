<x-layouts::app :title="'SVG Drawing Benchmark'">
    <!-- Hero section with illustration elements -->
    <div class="relative mb-10 bg-gradient-to-br from-amber-50 to-white rounded-3xl shadow-sm border border-amber-100 overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute right-0 top-0 w-64 h-64 bg-amber-100 rounded-full -translate-y-1/2 translate-x-1/4 opacity-70"></div>
        <div class="absolute left-0 bottom-0 w-32 h-32 bg-amber-50 rounded-full translate-y-1/2 -translate-x-1/4 opacity-80"></div>

        <!-- SVG patterns for background decoration (subtle) -->
        <svg class="absolute top-0 left-0 w-full h-full opacity-[0.07] pointer-events-none" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="smallGrid" width="20" height="20" patternUnits="userSpaceOnUse">
                    <path d="M 20 0 L 0 0 0 20" fill="none" stroke="currentColor" stroke-width="0.5" />
                </pattern>
                <pattern id="grid" width="80" height="80" patternUnits="userSpaceOnUse">
                    <rect width="80" height="80" fill="url(#smallGrid)" />
                    <path d="M 80 0 L 0 0 0 80" fill="none" stroke="currentColor" stroke-width="1" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)" stroke-width="0" />
        </svg>

        <!-- Content -->
        <div class="relative px-8 py-10 md:py-16 flex flex-col md:flex-row items-center">
            <div class="md:w-2/3 mb-8 md:mb-0">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 flex items-center gap-5">
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-full shadow-md">
                        <x-phosphor-paint-brush-fill class="size-6 text-white" />
                    </div>
                    SVG Drawing Benchmark
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl">
                    Watch AI models create beautiful SVG art from textual prompts, showcasing their
                    creativity, visual design skills, and understanding of vector graphics.
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <div
                        class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-amber-100 text-amber-800"
                        x-data="animatedCounter({{ $totalMatchesCount }})"
                    >
                        <x-phosphor-image-fill class="size-6 mr-2" />
                        <span x-text="integerValue + ' drawings'"></span>
                    </div>
                    <div
                        class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-amber-100 text-amber-800"
                        x-data="animatedCounter({{ $modelsCount }})"
                    >
                        <x-phosphor-robot-fill class="size-6 mr-2" />
                        <span x-text="integerValue + ' AI models competing'"></span>
                    </div>
                    <div
                        class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium bg-amber-100 text-amber-800"
                        x-data="animatedCounter({{ $giraffeCount * 2 }})"
                    >
                        <x-icons.giraffe class="size-5 mr-2" />
                        <span x-text="integerValue + ' giraffes drawn'"></span>
                    </div>
                </div>

                <div class="mt-8">
                    <x-ui.button href="#featured-matches" variant="primary" size="lg" class="bg-amber-600 hover:bg-amber-700 focus:ring-amber-500">
                        Explore Drawings
                    </x-ui.button>
                </div>
            </div>

            <!-- SVG Art Display -->
            <div class="md:w-1/3 flex justify-center">
                <div class="relative" x-data="{ current: 0 }" x-init="setInterval(() => current = (current + 1) % 3, 2500)">
                    <!-- SVG sample 1 -->
                    <div
                        class="size-36 md:size-48 rounded-2xl bg-white border-4 border-amber-100 shadow-lg flex items-center justify-center absolute left-0 -rotate-6 overflow-hidden"
                        :class="{'scale-110 shadow-xl z-10': current === 0, 'z-0': current !== 0}"
                        style="transition: all 0.5s ease"
                    >
                        <svg width="100%" height="100%" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="100" cy="100" r="80" fill="#eef2ff" />
                            <circle cx="100" cy="100" r="60" fill="#c7d2fe" />
                            <circle cx="100" cy="100" r="40" fill="#a5b4fc" />
                            <circle cx="100" cy="100" r="20" fill="#6366f1" />
                        </svg>
                    </div>

                    <!-- SVG sample 2 -->
                    <div
                        class="size-36 md:size-48 rounded-2xl bg-white border-4 border-amber-100 shadow-lg flex items-center justify-center rotate-3 overflow-hidden"
                        :class="{'scale-110 shadow-xl z-10': current === 1, 'z-0': current !== 1}"
                        style="transition: all 0.5s ease"
                    >
                        <svg width="100%" height="100%" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <rect x="40" y="40" width="120" height="120" fill="#eef2ff" />
                            <rect x="60" y="60" width="80" height="80" fill="#c7d2fe" />
                            <rect x="80" y="80" width="40" height="40" fill="#a5b4fc" />
                            <rect x="90" y="90" width="20" height="20" fill="#6366f1" />
                        </svg>
                    </div>

                    <!-- SVG sample 3 -->
                    <div
                        class="size-36 md:size-48 rounded-2xl bg-white border-4 border-amber-100 shadow-lg flex items-center justify-center absolute right-0 rotate-12 overflow-hidden"
                        :class="{'scale-110 shadow-xl z-10': current === 2, 'z-0': current !== 2}"
                        style="transition: all 0.5s ease"
                    >
                        <svg width="100%" height="100%" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                            <polygon points="100,20 180,180 20,180" fill="#eef2ff" />
                            <polygon points="100,50 150,150 50,150" fill="#c7d2fe" />
                            <polygon points="100,80 130,130 70,130" fill="#a5b4fc" />
                            <polygon points="100,100 115,115 85,115" fill="#6366f1" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Matches -->
    <section id="featured-matches" class="mb-8 scroll-mt-20">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
            <x-phosphor-star-fill class="w-6 h-6 mr-2 text-amber-500" />
            Featured Drawings
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @if($latestMatch)
                <div class="h-full">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-800 inline-flex items-center">
                            <x-phosphor-clock-fill class="w-5 h-5 mr-2 text-amber-500" />
                            Latest Drawing
                        </h3>

                        <div class="text-sm text-gray-500">
                            Just added
                        </div>
                    </div>
                    <x-svg.match-card :match="$latestMatch" class="hover-scale" />
                </div>
            @endif

            @if($mostCreativeMatch)
                <div class="h-full">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-800 inline-flex items-center">
                            <x-phosphor-lightbulb-fill class="w-5 h-5 mr-2 text-amber-500" />
                            Most Creative
                        </h3>

                        <div class="text-sm text-gray-500">
                            Exceptional artwork
                        </div>
                    </div>
                    <x-svg.match-card :match="$mostCreativeMatch" class="hover-scale"/>
                </div>
            @endif

            @if($visuallyInterestingMatch)
                <div class="h-full">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-800 inline-flex items-center">
                            <x-phosphor-eye-fill class="w-5 h-5 mr-2 text-amber-500" />
                            Visually Striking
                        </h3>
                        <div class="text-sm text-gray-500">
                            Outstanding design
                        </div>
                    </div>
                    <x-svg.match-card :match="$visuallyInterestingMatch" class="hover-scale"/>
                </div>
            @endif
        </div>
    </section>

    <!-- Model Rankings -->
    <section id="model-rankings" class="mb-10 scroll-mt-20">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
            <x-phosphor-trophy-fill class="w-6 h-6 mr-2 text-amber-500" />
            Top SVG Artists
        </h2>

        <!-- Using our new reusable component -->
        <x-svg.models-ranking-table :models="$models" />

        <div class="mt-4 text-center">
            <x-ui.button href="{{ route('models.index') }}" variant="outline" class="border-amber-300 text-amber-700 hover:bg-amber-50">
                <x-phosphor-chart-bar-fill class="w-4 h-4 mr-2" />
                View All Model Analysis
            </x-ui.button>
        </div>
    </section>

    <!-- Top Creator Spotlight (if we have top creators) -->
    @if($topCreators->count() > 0)
        <section class="mb-10">
            <h2 class="text-2xl font-bold mb-6 text-gray-900 flex items-center">
                <x-phosphor-medal-fill class="w-6 h-6 mr-2 text-amber-500" />
                Creative Champions
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($topCreators as $creator)
                    <x-svg.model-card :model="$creator" />
                @endforeach
            </div>
        </section>
    @endif

    <!-- About the SVG Benchmark section -->
    <x-svg.game-rules />

    <style>
        .hover-scale {
            transition: transform 0.3s ease;
        }
        .hover-scale:hover {
            transform: translateY(-4px) scale(1.01);
        }
    </style>
</x-layouts::app>
