<x-layouts::app :title="'SVG Match #' . $svgMatch->id . ' Details'">
    <!-- Header section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <x-ui.button :href="route('svg.index')" variant="secondary" class="text-xs sm:text-sm">
                <x-phosphor-arrow-left class="size-4 mr-1 sm:mr-2" />
                <span class="hidden xs:inline">Back to All Matches</span>
                <span class="xs:hidden">Back</span>
            </x-ui.button>

            <h1 class="text-xl sm:text-2xl font-bold text-center">
                SVG Drawing Match #{{ $svgMatch->id }}
            </h1>

            <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                <x-phosphor-clock-fill class="w-3 h-3 mr-1" />
                {{ $svgMatch->created_at->format('M d, Y') }}
            </span>
        </div>
    </div>

    <!-- Prompt section -->
    <div class="mb-8 text-center bg-gradient-to-r from-indigo-50 via-purple-50 to-indigo-50 rounded-xl p-5 border border-indigo-100 shadow-sm relative overflow-hidden">
        <div class="flex items-center justify-center mb-3">
            <x-phosphor-paint-brush-fill class="w-6 h-6 text-indigo-700 mr-2" />
            <h2 class="text-lg font-medium text-indigo-700">Creative Challenge</h2>
        </div>
        <p class="text-xl sm:text-2xl font-semibold text-gray-800 max-w-3xl mx-auto relative">
            "{{ $svgMatch->prompt }}"
        </p>
    </div>

    <!-- SVG Display Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Player 1's SVG -->
        <div x-data="{ showCode: false }">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden relative h-full flex flex-col">
                <div class="bg-red-50 px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                            <x-phosphor-robot-fill class="w-5 h-5 text-red-600" />
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $svgMatch->player1->name }}</div>
                            <div class="text-xs text-gray-500 flex items-center">
                                Player 1
                                @if($svgMatch->player1_elo_before && $svgMatch->player1_elo_after)
                                <span class="mx-3">•</span>
                                <span class="font-mono text-xs">
                                    ELO: {{ Number::format($svgMatch->player1_elo_before, 0) }}
                                    <x-phosphor-arrow-right class="w-3 h-3 mx-0.5 inline text-gray-400" />
                                    <span class="{{ $svgMatch->player1_elo_after > $svgMatch->player1_elo_before ? 'text-green-600' : ($svgMatch->player1_elo_after < $svgMatch->player1_elo_before ? 'text-red-600' : 'text-gray-600') }}">
                                        {{ Number::format($svgMatch->player1_elo_after, 0) }}
                                        @if($svgMatch->player1_elo_after != $svgMatch->player1_elo_before)
                                            ({{ $svgMatch->player1_elo_after > $svgMatch->player1_elo_before ? '+' : '' }}{{ Number::format($svgMatch->player1_elo_after - $svgMatch->player1_elo_before, 1) }})
                                        @endif
                                    </span>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <button
                        @click="showCode = !showCode"
                        class="px-2 py-1 text-xs font-medium rounded bg-white border border-red-200 text-red-700 hover:bg-red-50 transition-colors flex items-center"
                    >
                        <x-phosphor-code-fill x-show="!showCode" class="w-3 h-3 mr-1" />
                        <x-phosphor-image-fill x-show="showCode" x-cloak class="w-3 h-3 mr-1" />
                        <span x-text="showCode ? 'View Image' : 'View Code'">View Code</span>
                    </button>
                </div>

                <!-- SVG Image Display -->
                <div x-show="!showCode" class="p-6 flex-grow">
                    <div class="aspect-square w-full h-full border border-gray-200 rounded bg-gray-50 flex items-center justify-center p-4 overflow-hidden">
                        @if($svgMatch->getPlayer1SvgUrl())
                            <img src="{{ $svgMatch->getPlayer1SvgUrl() }}" alt="SVG created by {{ $svgMatch->player1->name }}"
                                class="max-w-full max-h-full object-contain {{ $svgMatch->isPlayer1Winner() ? 'ring-2 ring-red-300' : '' }}">
                        @else
                            <div class="text-center p-8 text-gray-400">
                                <x-phosphor-image-square-fill class="w-12 h-12 mx-auto mb-2" />
                                <p>SVG image not available</p>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-between items-center mt-3">
                        <a href="{{ $svgMatch->getPlayer1SvgUrl() }}" target="_blank" class="text-xs text-gray-500 hover:text-red-600 flex items-center">
                            <x-phosphor-arrow-square-out-fill class="w-3.5 h-3.5 mr-1" />
                            Open in new tab
                        </a>

                        @if($svgMatch->isPlayer1Winner())
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <x-phosphor-trophy-fill class="w-3 h-3 mr-1" />
                                Winner
                            </span>
                        @endif
                    </div>
                </div>

                <!-- SVG Code Display -->
                <div x-show="showCode" x-cloak class="p-4 flex-grow">
                    @if($svgMatch->getPlayer1SvgContent())
                        <div class="bg-gray-900 text-gray-100 rounded-lg p-4 overflow-x-auto text-xs font-mono h-full min-h-[400px] overflow-y-auto">
                            <pre>{{ htmlspecialchars($svgMatch->getPlayer1SvgContent()) }}</pre>
                        </div>
                    @else
                        <div class="text-center p-8 text-gray-400 h-full min-h-[400px] flex flex-col items-center justify-center">
                            <x-phosphor-code-fill class="w-12 h-12 mx-auto mb-2" />
                            <p>SVG code not available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Player 2's SVG -->
        <div x-data="{ showCode: false }">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden relative h-full flex flex-col">
                <div class="bg-blue-50 px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <x-phosphor-robot-fill class="w-5 h-5 text-blue-600" />
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $svgMatch->player2->name }}</div>
                            <div class="text-xs text-gray-500 flex items-center">
                                Player 2
                                @if($svgMatch->player2_elo_before && $svgMatch->player2_elo_after)
                                <span class="mx-3">•</span>
                                <span class="font-mono text-xs">
                                    ELO: {{ Number::format($svgMatch->player2_elo_before, 0) }}
                                    <x-phosphor-arrow-right class="w-3 h-3 mx-0.5 inline text-gray-400" />
                                    <span class="{{ $svgMatch->player2_elo_after > $svgMatch->player2_elo_before ? 'text-green-600' : ($svgMatch->player2_elo_after < $svgMatch->player2_elo_before ? 'text-red-600' : 'text-gray-600') }}">
                                        {{ Number::format($svgMatch->player2_elo_after, 0) }}
                                        @if($svgMatch->player2_elo_after != $svgMatch->player2_elo_before)
                                            ({{ $svgMatch->player2_elo_after > $svgMatch->player2_elo_before ? '+' : '' }}{{ Number::format($svgMatch->player2_elo_after - $svgMatch->player2_elo_before, 1) }})
                                        @endif
                                    </span>
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <button
                        @click="showCode = !showCode"
                        class="px-2 py-1 text-xs font-medium rounded bg-white border border-blue-200 text-blue-700 hover:bg-blue-50 transition-colors flex items-center"
                    >
                        <x-phosphor-code-fill x-show="!showCode" class="w-3 h-3 mr-1" />
                        <x-phosphor-image-fill x-show="showCode" x-cloak class="w-3 h-3 mr-1" />
                        <span x-text="showCode ? 'View Image' : 'View Code'">View Code</span>
                    </button>
                </div>

                <!-- SVG Image Display -->
                <div x-show="!showCode" class="p-6 flex-grow">
                    <div class="aspect-square w-full h-full border border-gray-200 rounded bg-gray-50 flex items-center justify-center p-4 overflow-hidden">
                        @if($svgMatch->getPlayer2SvgUrl())
                            <img src="{{ $svgMatch->getPlayer2SvgUrl() }}" alt="SVG created by {{ $svgMatch->player2->name }}"
                                class="max-w-full max-h-full object-contain {{ $svgMatch->isPlayer2Winner() ? 'ring-2 ring-blue-300' : '' }}">
                        @else
                            <div class="text-center p-8 text-gray-400">
                                <x-phosphor-image-square-fill class="w-12 h-12 mx-auto mb-2" />
                                <p>SVG image not available</p>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-between items-center mt-3">
                        <a href="{{ $svgMatch->getPlayer2SvgUrl() }}" target="_blank" class="text-xs text-gray-500 hover:text-blue-600 flex items-center">
                            <x-phosphor-arrow-square-out-fill class="w-3.5 h-3.5 mr-1" />
                            Open in new tab
                        </a>

                        @if($svgMatch->isPlayer2Winner())
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <x-phosphor-trophy-fill class="w-3 h-3 mr-1" />
                                Winner
                            </span>
                        @endif
                    </div>
                </div>

                <!-- SVG Code Display -->
                <div x-show="showCode" x-cloak class="p-4 flex-grow">
                    @if($svgMatch->getPlayer2SvgContent())
                        <div class="bg-gray-900 text-gray-100 rounded-lg p-4 overflow-x-auto text-xs font-mono h-full min-h-[400px] overflow-y-auto">
                            <pre>{{ htmlspecialchars($svgMatch->getPlayer2SvgContent()) }}</pre>
                        </div>
                    @else
                        <div class="text-center p-8 text-gray-400 h-full min-h-[400px] flex flex-col items-center justify-center">
                            <x-phosphor-code-fill class="w-12 h-12 mx-auto mb-2" />
                            <p>SVG code not available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Judge reasoning -->
    <div class="mb-8">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-purple-50 via-indigo-50 to-purple-50 px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                        <x-phosphor-scales-fill class="w-5 h-5 text-indigo-600" />
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900">GPT-4o</div>
                        <div class="text-xs text-gray-500">AI Judge</div>
                    </div>
                </div>
            </div>

            <!-- Judge Comments -->
            <div class="p-6">
                <div class="flex rounded-xl overflow-hidden">
                    <div class="border-l-4 border-indigo-500 pl-4 py-1">
                        <p class="text-gray-700 leading-relaxed">{{ $svgMatch->judge_reasoning }}</p>
                    </div>
                </div>

                <!-- Evaluation Criteria Compact Display -->
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                    <div class="bg-gray-50 p-3 rounded-lg flex items-center">
                        <div class="w-8 h-8 min-w-8 rounded-full bg-indigo-100 flex items-center justify-center mr-2">
                            <x-phosphor-palette-fill class="w-4 h-4 text-indigo-600" />
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">Creativity</div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg flex items-center">
                        <div class="w-8 h-8 min-w-8 rounded-full bg-indigo-100 flex items-center justify-center mr-2">
                            <x-phosphor-target-fill class="w-4 h-4 text-indigo-600" />
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">Prompt Adherence</div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg flex items-center">
                        <div class="w-8 h-8 min-w-8 rounded-full bg-indigo-100 flex items-center justify-center mr-2">
                            <x-phosphor-sparkle-fill class="w-4 h-4 text-indigo-600" />
                        </div>
                        <div>
                            <div class="font-medium text-gray-800">Visual Appeal</div>
                        </div>
                    </div>
                </div>

                <!-- Judging info minimized -->
                <details class="mt-6 text-sm text-gray-600 bg-gray-50 rounded-lg p-3">
                    <summary class="font-medium cursor-pointer">How judging works</summary>
                    <div class="pt-3">
                        <p>SVG drawings are converted to static PNG images for evaluation. The AI judge receives the original prompt and both images, then determines which drawing better fulfills the evaluation criteria without seeing animations, interactivity, or SVG code.</p>
                    </div>
                </details>
            </div>
        </div>
    </div>

    <!-- Similar matches -->
    @if($similarMatches->count() > 0)
        <section class="mt-8 sm:mt-10">
            <h2 class="text-lg sm:text-xl font-bold mb-4 sm:mb-6 text-gray-900 flex items-center">
                <x-phosphor-arrows-in-line-horizontal-fill class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-indigo-500" />
                Similar Matches
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($similarMatches as $match)
                    <a href="{{ route('svg.matches.show', $match) }}" class="block bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-medium text-gray-900">#{{ $match->id }}</span>
                                <span class="text-sm text-gray-500">{{ $match->created_at->format('M d') }}</span>
                            </div>

                            <div class="text-sm text-gray-600 mb-3 truncate">{{ $match->prompt }}</div>

                            <div class="grid grid-cols-2 gap-2 mb-4">
                                <div class="aspect-square bg-gray-50 rounded-md p-2 flex items-center justify-center overflow-hidden">
                                    @if($match->getPlayer1SvgUrl())
                                        <img src="{{ $match->getPlayer1SvgUrl() }}" alt="SVG by {{ $match->player1->name }}"
                                             class="max-w-full max-h-full object-contain">
                                    @else
                                        <x-phosphor-image-square class="w-6 h-6 text-gray-300" />
                                    @endif
                                </div>

                                <div class="aspect-square bg-gray-50 rounded-md p-2 flex items-center justify-center overflow-hidden">
                                    @if($match->getPlayer2SvgUrl())
                                        <img src="{{ $match->getPlayer2SvgUrl() }}" alt="SVG by {{ $match->player2->name }}"
                                             class="max-w-full max-h-full object-contain">
                                    @else
                                        <x-phosphor-image-square class="w-6 h-6 text-gray-300" />
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center">
                                    <x-phosphor-trophy-fill class="w-3 h-3 mr-1 text-indigo-500" />
                                    <span>{{ $match->winner ? $match->winner->name : 'No winner' }}</span>
                                </div>

                                <span class="text-indigo-600 flex items-center">
                                    View Details
                                    <x-phosphor-arrow-right class="w-3 h-3 ml-1" />
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts::app>
