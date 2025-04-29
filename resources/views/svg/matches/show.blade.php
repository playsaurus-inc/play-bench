<x-layouts::app :title="'SVG Match #' . $svgMatch->id . ' Details'">
    <!-- Entire page with shared Alpine state -->
    <div x-data="{ showCode: false }">
        <!-- Header section with integrated view toggle -->
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center">
                <x-ui.button :href="route('svg.index')" variant="secondary" class="text-xs sm:text-sm mr-3">
                    <x-phosphor-arrow-left class="size-4 mr-1 sm:mr-2" />
                    <span class="hidden xs:inline">Back to Matches</span>
                    <span class="xs:hidden">Back</span>
                </x-ui.button>

                <h1 class="text-lg sm:text-xl font-bold">
                    SVG Drawing Match #{{ $svgMatch->id }}
                </h1>

                <span class="ml-2 sm:ml-3 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                    <x-phosphor-clock-fill class="size-3.5 mr-1" />
                    {{ $svgMatch->created_at->format('M d, Y') }}
                </span>
            </div>

            <!-- View toggle moved to top right -->
            <div class="flex justify-end">
                <div class="inline-flex rounded-lg border border-gray-200 p-0.5 bg-white shadow-sm">
                    <button
                        @click="showCode = false"
                        x-bind:class="{'bg-gray-100 text-gray-900': !showCode, 'text-gray-500 hover:text-gray-700': showCode}"
                        class="px-3 py-1 text-xs font-medium rounded-md flex items-center transition-colors"
                    >
                        <x-phosphor-image-fill class="size-4 mr-1.5" />
                        View Images
                    </button>
                    <button
                        @click="showCode = true"
                        x-bind:class="{'bg-gray-100 text-gray-900': showCode, 'text-gray-500 hover:text-gray-700': !showCode}"
                        class="px-3 py-1 text-xs font-medium rounded-md flex items-center transition-colors"
                    >
                        <x-phosphor-code-fill class="size-4 mr-1.5" />
                        View Code
                    </button>
                </div>
            </div>
        </div>

        <!-- Main content with artwork focus -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-6">
            <!-- SVG Displays - Larger area (8/12 columns) -->
            <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Player 1's SVG -->
                <div class="bg-white rounded-lg shadow-sm border border-red-50 overflow-hidden">
                    <!-- Player 1 header with more breathing room -->
                    <div class="bg-gradient-to-r from-red-50 to-white p-3 border-b border-red-100 flex items-center">
                        <a href="{{ route('models.show.svg', $svgMatch->player1) }}" class="group flex items-center flex-1">
                            <div class="size-10 rounded-full bg-red-100 flex items-center justify-center mr-2.5 group-hover:ring-2 group-hover:ring-red-300 transition-all">
                                <x-phosphor-robot-fill class="size-5 text-red-500" />
                            </div>
                            <div>
                                <div class="text-base font-bold text-gray-900 group-hover:text-red-600 transition-colors">{{ $svgMatch->player1->name }}</div>
                                <div class="text-xs text-gray-500 flex items-center">
                                    Artist 1
                                    @if($svgMatch->player1_elo_before && $svgMatch->player1_elo_after)
                                    <span class="mx-1">•</span>
                                    <span class="font-mono text-xs">ELO:
                                        <span class="{{ $svgMatch->player1_elo_after > $svgMatch->player1_elo_before ? 'text-green-600' : ($svgMatch->player1_elo_after < $svgMatch->player1_elo_before ? 'text-red-600' : '') }}">
                                            {{ Number::format($svgMatch->player1_elo_after, 0) }}
                                            @if($svgMatch->player1_elo_after != $svgMatch->player1_elo_before)
                                                ({{ $svgMatch->player1_elo_after > $svgMatch->player1_elo_before ? '+' : '' }}{{ Number::format($svgMatch->player1_elo_after - $svgMatch->player1_elo_before, 1) }})
                                            @endif
                                        </span>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </a>

                        @if($svgMatch->isPlayer1Winner())
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <x-phosphor-trophy-fill class="size-4 mr-1" />
                                Winner
                            </span>
                        @endif
                    </div>

                    <!-- SVG image with minimal padding -->
                    <div x-show="!showCode" class="p-1">
                        <div class="aspect-square w-full border border-gray-100 rounded bg-gray-50 flex items-center justify-center overflow-hidden">
                            @if($svgMatch->getPlayer1SvgUrl())
                                <img src="{{ $svgMatch->getPlayer1SvgUrl() }}" alt="SVG by {{ $svgMatch->player1->name }}"
                                    class="max-w-full max-h-full object-contain {{ $svgMatch->isPlayer1Winner() ? 'ring-2 ring-red-300' : '' }}">
                            @else
                                <div class="text-center p-4 text-gray-400">
                                    <x-phosphor-image-square-fill class="size-12 mx-auto mb-2" />
                                    <p class="text-sm">SVG image not available</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- SVG code with minimal padding -->
                    <div x-show="showCode" x-cloak class="p-1">
                        @if($svgMatch->getPlayer1SvgContent())
                            <div class="bg-gray-900 text-gray-100 rounded p-3 overflow-x-auto text-xs font-mono h-80 overflow-y-auto">
                                <pre>{{ $svgMatch->getPlayer1SvgContent() }}</pre>
                            </div>
                        @else
                            <div class="text-center p-8 text-gray-400 h-80 flex flex-col items-center justify-center">
                                <x-phosphor-code-fill class="size-12 mx-auto mb-2" />
                                <p class="text-sm">SVG code not available</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Player 2's SVG -->
                <div class="bg-white rounded-lg shadow-sm border border-blue-50 overflow-hidden">
                    <!-- Player 2 header with more breathing room -->
                    <div class="bg-gradient-to-r from-blue-50 to-white p-3 border-b border-blue-100 flex items-center">
                        <a href="{{ route('models.show.svg', $svgMatch->player2) }}" class="group flex items-center flex-1">
                            <div class="size-10 rounded-full bg-blue-100 flex items-center justify-center mr-2.5 group-hover:ring-2 group-hover:ring-blue-300 transition-all">
                                <x-phosphor-robot-fill class="size-5 text-blue-500" />
                            </div>
                            <div>
                                <div class="text-base font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $svgMatch->player2->name }}</div>
                                <div class="text-xs text-gray-500 flex items-center">
                                    Artist 2
                                    @if($svgMatch->player2_elo_before && $svgMatch->player2_elo_after)
                                    <span class="mx-1">•</span>
                                    <span class="font-mono text-xs">ELO:
                                        <span class="{{ $svgMatch->player2_elo_after > $svgMatch->player2_elo_before ? 'text-green-600' : ($svgMatch->player2_elo_after < $svgMatch->player2_elo_before ? 'text-red-600' : '') }}">
                                            {{ Number::format($svgMatch->player2_elo_after, 0) }}
                                            @if($svgMatch->player2_elo_after != $svgMatch->player2_elo_before)
                                                ({{ $svgMatch->player2_elo_after > $svgMatch->player2_elo_before ? '+' : '' }}{{ Number::format($svgMatch->player2_elo_after - $svgMatch->player2_elo_before, 1) }})
                                            @endif
                                        </span>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </a>

                        @if($svgMatch->isPlayer2Winner())
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <x-phosphor-trophy-fill class="size-4 mr-1" />
                                Winner
                            </span>
                        @endif
                    </div>

                    <!-- SVG image with minimal padding -->
                    <div x-show="!showCode" class="p-1">
                        <div class="aspect-square w-full border border-gray-100 rounded bg-gray-50 flex items-center justify-center overflow-hidden">
                            @if($svgMatch->getPlayer2SvgUrl())
                                <img src="{{ $svgMatch->getPlayer2SvgUrl() }}" alt="SVG by {{ $svgMatch->player2->name }}"
                                    class="max-w-full max-h-full object-contain {{ $svgMatch->isPlayer2Winner() ? 'ring-2 ring-blue-300' : '' }}">
                            @else
                                <div class="text-center p-4 text-gray-400">
                                    <x-phosphor-image-square-fill class="size-12 mx-auto mb-2" />
                                    <p class="text-sm">SVG image not available</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- SVG code with minimal padding -->
                    <div x-show="showCode" x-cloak class="p-1">
                        @if($svgMatch->getPlayer2SvgContent())
                            <div class="bg-gray-900 text-gray-100 rounded p-3 overflow-x-auto text-xs font-mono h-80 overflow-y-auto">
                                <pre>{{ $svgMatch->getPlayer2SvgContent() }}</pre>
                            </div>
                        @else
                            <div class="text-center p-8 text-gray-400 h-80 flex flex-col items-center justify-center">
                                <x-phosphor-code-fill class="size-12 mx-auto mb-2" />
                                <p class="text-sm">SVG code not available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Challenge and Judge info - Sidebar (4/12 columns) -->
            <div class="lg:col-span-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden h-full flex flex-col">
                    <!-- Prompt section with personified GPT-4o -->
                    <div class="p-3 sm:p-4 border-b border-gray-100">
                        <!-- Personified GPT-4o as prompter -->
                        <div class="flex items-center mb-4">
                            <div class="size-10 rounded-full bg-purple-100 border-2 border-white shadow-sm flex items-center justify-center mr-3">
                                <x-phosphor-robot-fill class="size-5 text-purple-600" />
                            </div>
                            <div>
                                <div class="text-base font-bold text-purple-800">GPT-4o</div>
                                <div class="text-xs text-gray-500">Challenge Creator</div>
                            </div>
                        </div>

                        <!-- Creative Challenge -->
                        <div class="flex items-center mb-2 text-purple-600">
                            <x-phosphor-paint-brush-fill class="size-5 mr-2" />
                            <h2 class="text-sm font-medium">Creative Challenge</h2>
                        </div>

                        <div class="bg-purple-50 rounded-lg p-3.5 mb-3">
                            <p class="text-sm sm:text-base text-gray-800 italic">
                                "{{ $svgMatch->prompt }}"
                            </p>
                        </div>
                    </div>

                    <!-- Judge section with personified GPT-4o -->
                    <div class="p-3 sm:p-4 bg-gradient-to-b from-white to-purple-50 flex-grow">
                        <!-- Personified GPT-4o as judge -->
                        <div class="flex items-center mb-4">
                            <div class="size-10 rounded-full bg-purple-100 border-2 border-white shadow-sm flex items-center justify-center mr-3">
                                <x-phosphor-scales-fill class="size-5 text-purple-600" />
                            </div>
                            <div>
                                <div class="text-base font-bold text-purple-800">GPT-4o</div>
                                <div class="text-xs text-gray-500">Judge & Evaluator</div>
                            </div>
                        </div>

                        <!-- Winner highlight -->
                        @if($svgMatch->winner)
                            <div class="mb-4 text-center">
                                <div class="text-lg font-bold {{ $svgMatch->isPlayer1Winner() ? 'text-red-600' : 'text-blue-600' }} mb-1">
                                    {{ $svgMatch->winner->name }} Wins
                                </div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $svgMatch->isPlayer1Winner() ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                    <x-phosphor-trophy-fill class="size-4 mr-1.5" />
                                    Winner
                                </span>
                            </div>
                        @else
                            <div class="mb-4 text-center">
                                <div class="text-lg font-bold text-gray-600 mb-1">No Winner Determined</div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    <x-phosphor-equals-fill class="size-4 mr-1.5" />
                                    Tie
                                </span>
                            </div>
                        @endif

                        <!-- Judge's Analysis -->
                        <div>
                            <h3 class="text-base font-semibold text-gray-700 mb-3 flex items-center">
                                <x-phosphor-note-pencil-fill class="size-5 mr-2 text-purple-500" />
                                Judge's Analysis
                            </h3>
                            <div class="bg-white rounded-lg p-4 border border-purple-100 shadow-sm">
                                <p class="text-sm sm:text-base text-gray-700 leading-relaxed">{{ $svgMatch->judge_reasoning }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Judging info cards - single row with smaller height -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6 overflow-hidden">
            <div class="p-3 sm:p-4">
                <div class="flex items-center mb-3">
                    <div class="size-7 rounded-full bg-purple-100 flex items-center justify-center mr-2">
                        <x-phosphor-brain-fill class="size-4 text-purple-600" />
                    </div>
                    <h3 class="text-base font-medium text-gray-800">AI Judging Process</h3>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <!-- Creativity -->
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <div class="flex items-center mb-1">
                            <div class="size-6 rounded-full bg-purple-100 flex items-center justify-center mr-1">
                                <x-phosphor-palette-fill class="size-3.5 text-purple-600" />
                            </div>
                            <h4 class="text-sm font-medium text-gray-800">Creativity</h4>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">
                            Originality, innovative use of shapes and unique approach to the prompt.
                        </p>
                    </div>

                    <!-- Prompt Adherence -->
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <div class="flex items-center mb-1">
                            <div class="size-6 rounded-full bg-purple-100 flex items-center justify-center mr-1">
                                <x-phosphor-target-fill class="size-3.5 text-purple-600" />
                            </div>
                            <h4 class="text-sm font-medium text-gray-800">Prompt Adherence</h4>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">
                            How accurately the SVG captures the essence of the prompt.
                        </p>
                    </div>

                    <!-- Visual Appeal -->
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <div class="flex items-center mb-1">
                            <div class="size-6 rounded-full bg-purple-100 flex items-center justify-center mr-1">
                                <x-phosphor-sparkle-fill class="size-3.5 text-purple-600" />
                            </div>
                            <h4 class="text-sm font-medium text-gray-800">Visual Appeal</h4>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">
                            Aesthetic quality including composition, color usage and overall visual impact.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar matches - more compact grid -->
        @if($similarMatches->count() > 0)
            <section class="mb-6">
                <h2 class="text-lg font-bold mb-3 text-gray-900 flex items-center">
                    <x-phosphor-arrows-in-line-horizontal-fill class="size-5 mr-2 text-purple-500" />
                    Similar Challenges
                </h2>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($similarMatches as $match)
                        <a href="{{ route('svg.matches.show', $match) }}" class="block bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all overflow-hidden">
                            <div class="p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-medium text-xs text-gray-900">#{{ $match->id }}</span>
                                    <span class="text-xs text-gray-500">{{ $match->created_at->format('M d') }}</span>
                                </div>

                                <div class="text-xs text-gray-600 mb-3 line-clamp-2 h-8">{{ $match->prompt }}</div>

                                <div class="grid grid-cols-2 gap-1 mb-2">
                                    <div class="aspect-square bg-gray-50 rounded p-1 flex items-center justify-center overflow-hidden">
                                        @if($match->getPlayer1SvgUrl())
                                            <img src="{{ $match->getPlayer1SvgUrl() }}" alt="SVG by {{ $match->player1->name }}"
                                                 class="max-w-full max-h-full object-contain">
                                        @else
                                            <x-phosphor-image-square class="size-4 text-gray-300" />
                                        @endif
                                    </div>

                                    <div class="aspect-square bg-gray-50 rounded p-1 flex items-center justify-center overflow-hidden">
                                        @if($match->getPlayer2SvgUrl())
                                            <img src="{{ $match->getPlayer2SvgUrl() }}" alt="SVG by {{ $match->player2->name }}"
                                                 class="max-w-full max-h-full object-contain">
                                        @else
                                            <x-phosphor-image-square class="size-4 text-gray-300" />
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center">
                                        <x-phosphor-trophy-fill class="size-3.5 mr-1 text-purple-500" />
                                        <span class="truncate">{{ $match->winner ? $match->winner->name : 'No winner' }}</span>
                                    </div>

                                    <span class="text-purple-600 flex items-center">
                                        View
                                        <x-phosphor-arrow-right class="size-3.5 ml-1" />
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts::app>
