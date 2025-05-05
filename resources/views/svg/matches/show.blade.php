<x-layouts::app :title="'SVG Match #' . $svgMatch->id . ' Details'">
    <!-- Entire page with shared Alpine state -->
    <div x-data="{ showCode: false }">
        <!-- Header section with integrated view toggle -->
        <div class="mb-4 flex flex-col sm:items-start md:flex-row md:items-center justify-between gap-2">
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

            <!-- View toggle -->
            <div class="w-full md:w-auto mt-3 md:mt-0">
                <div class="flex rounded-lg border border-gray-200 p-1 bg-white shadow-sm">
                    <button
                        @click="showCode = false"
                        x-bind:class="{'bg-gray-100 text-gray-900': !showCode, 'text-gray-500 hover:text-gray-700 cursor-pointer': showCode}"
                        class="flex-1 md:flex-none px-3 py-1.5 md:py-1 text-sm font-medium rounded-md flex items-center justify-center md:justify-start transition-colors"
                    >
                        <x-phosphor-image-fill class="size-4 mr-1.5" />
                        <span class="inline md:hidden lg:inline">View Images</span>
                        <span class="hidden md:inline lg:hidden">Images</span>
                    </button>
                    <button
                        @click="showCode = true"
                        x-bind:class="{'bg-gray-100 text-gray-900': showCode, 'text-gray-500 hover:text-gray-700 cursor-pointer': !showCode}"
                        class="flex-1 md:flex-none px-3 py-1.5 md:py-1 text-sm font-medium rounded-md flex items-center justify-center md:justify-start transition-colors"
                    >
                        <x-phosphor-code-fill class="size-4 mr-1.5" />
                        <span class="inline md:hidden lg:inline">View Code</span>
                        <span class="hidden md:inline lg:hidden">Code</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 1. Prompt card (full width) -->
        <div class="bg-white rounded-lg shadow-sm border border-amber-100 mb-6 overflow-hidden">
            <div class="flex flex-col md:flex-row">
                <!-- Left: GPT-4o avatar -->
                <div class="p-4 md:w-64 bg-gradient-to-b from-amber-50 to-white flex flex-row items-center justify-center border-b md:border-b-0 md:border-r border-amber-100 gap-4">
                    <div class="size-16 rounded-full bg-amber-100 border-2 border-white shadow flex items-center justify-center">
                        <x-phosphor-robot-fill class="size-8 text-amber-600" />
                    </div>
                    <div class="text-left">
                        <div class="text-base font-bold text-amber-800">GPT-4o</div>
                        <div class="text-sm text-gray-500">Challenge Creator</div>
                    </div>
                </div>

                <!-- Right: Creative challenge prompt -->
                <div class="p-3 sm:p-6 flex flex-col gap-2">
                    <div class="flex text-base items-center align-middle">
                        <x-phosphor-paint-brush-fill class="size-5 mr-2 text-amber-500" />
                        <h2 class="text-gray-700 font-medium">Creative Challenge</h2>
                    </div>
                    <p class="text-base sm:text-xl text-amber-800 font-bold tracking-wide italic text-left">
                        "{{ $svgMatch->prompt }}"
                    </p>
                </div>
            </div>
        </div>

        <!-- 2. SVG displays (two equal columns) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
            <!-- Player 1's SVG -->
            <div class="bg-white rounded-lg shadow-sm border border-red-50 overflow-hidden">
                <div class="bg-gradient-to-r from-red-50 to-white p-3 border-b border-red-100 flex items-center">
                    <a href="{{ route('models.show.svg', $svgMatch->player1) }}" class="group flex items-center flex-1">
                        <div class="size-10 rounded-full bg-red-100 flex items-center justify-center mr-2.5 group-hover:ring-2 group-hover:ring-red-300 transition-all">
                            <x-phosphor-robot-fill class="size-5 text-red-500" />
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="text-base font-bold text-gray-900 group-hover:text-red-600 transition-colors truncate">{{ $svgMatch->player1->name }}</div>
                            <div class="text-xs text-gray-500 flex items-center flex-wrap">
                                <span>Player 1</span>
                                @if($svgMatch->player1_elo_before && $svgMatch->player1_elo_after)
                                    <span class="mx-1 hidden xs:inline">•</span>
                                    <div class="text-xs flex items-center sm:justify-start whitespace-nowrap w-full xs:w-auto mt-0.5 xs:mt-0">
                                        <span class="font-mono">ELO: {{ Number::format($svgMatch->player1_elo_before) }}</span>
                                        <x-phosphor-arrow-right class="w-3 h-3 mx-1 text-gray-400" />
                                        <span class="font-mono {{ $svgMatch->player1_elo_after > $svgMatch->player1_elo_before ? 'text-green-600' : ($svgMatch->player1_elo_after < $svgMatch->player1_elo_before ? 'text-red-600' : 'text-gray-600') }}">
                                            {{ Number::format($svgMatch->player1_elo_after) }}
                                            @if($svgMatch->player1_elo_after != $svgMatch->player1_elo_before)
                                                ({{ $svgMatch->player1_elo_after > $svgMatch->player1_elo_before ? '+' : '' }}{{ Number::format($svgMatch->player1_elo_after - $svgMatch->player1_elo_before, 1) }})
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>

                    @if($svgMatch->isPlayer1Winner())
                        <span class="inline-flex items-center px-2 py-1 sm:px-2.5 sm:py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-2 shrink-0 whitespace-nowrap">
                            <x-phosphor-trophy-fill class="size-4 mr-1" />
                            <span class="hidden sm:inline">Winner</span>
                        </span>
                    @endif
                </div>

                <!-- SVG image/code display -->
                <div x-show="!showCode">
                    <div class="w-full p-4 border border-gray-100 rounded bg-gray-50 flex items-center justify-center overflow-hidden">
                        @if($svgMatch->getPlayer1SvgUrl())
                            <img
                                src="{{ $svgMatch->getPlayer1SvgUrl() }}"
                                alt="SVG by {{ $svgMatch->player1->name }}"
                                class="size-full max-w-full max-h-[60vh] object-contain"
                            >
                        @else
                            <div class="text-center p-4 text-gray-400">
                                <x-phosphor-image-square-fill class="size-12 mx-auto mb-2" />
                                <p class="text-sm">SVG image not available</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div x-show="showCode" x-cloak class="overflow-auto w-full h-full max-h-[60vh]">
                    @if($svgMatch->getPlayer1SvgContent())
                        <pre class="bg-gray-900 text-gray-100 rounded text-xs font-mono size-full"><code class="language-xml min-w-full min-h-full w-max">{{ $svgMatch->getPlayer1SvgContent() }}</code></pre>
                    @else
                        <div class="text-center p-8 text-gray-400 size-full flex flex-col items-center justify-center">
                            <x-phosphor-code-fill class="size-12 mx-auto mb-2" />
                            <p class="text-sm">SVG code not available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Player 2's SVG -->
            <div class="bg-white rounded-lg shadow-sm border border-blue-50 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-white p-3 border-b border-blue-100 flex items-center">
                    <a href="{{ route('models.show.svg', $svgMatch->player2) }}" class="group flex items-center flex-1">
                        <div class="size-10 rounded-full bg-blue-100 flex items-center justify-center mr-2.5 group-hover:ring-2 group-hover:ring-blue-300 transition-all">
                            <x-phosphor-robot-fill class="size-5 text-blue-500" />
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="text-base font-bold text-gray-900 group-hover:text-blue-600 transition-colors truncate">{{ $svgMatch->player2->name }}</div>
                            <div class="text-xs text-gray-500 flex items-center flex-wrap">
                                <span>Player 2</span>
                                @if($svgMatch->player2_elo_before && $svgMatch->player2_elo_after)
                                    <span class="mx-1 hidden xs:inline">•</span>
                                    <div class="text-xs flex items-center sm:justify-start whitespace-nowrap w-full xs:w-auto mt-0.5 xs:mt-0">
                                        <span class="font-mono">ELO: {{ Number::format($svgMatch->player2_elo_before) }}</span>
                                        <x-phosphor-arrow-right class="w-3 h-3 mx-1 text-gray-400" />
                                        <span class="font-mono {{ $svgMatch->player2_elo_after > $svgMatch->player2_elo_before ? 'text-green-600' : ($svgMatch->player2_elo_after < $svgMatch->player2_elo_before ? 'text-red-600' : 'text-gray-600') }}">
                                            {{ Number::format($svgMatch->player2_elo_after) }}
                                            @if($svgMatch->player2_elo_after != $svgMatch->player2_elo_before)
                                                ({{ $svgMatch->player2_elo_after > $svgMatch->player2_elo_before ? '+' : '' }}{{ Number::format($svgMatch->player2_elo_after - $svgMatch->player2_elo_before, 1) }})
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>

                    @if($svgMatch->isPlayer2Winner())
                        <span class="inline-flex items-center px-2 py-1 sm:px-2.5 sm:py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-2 shrink-0 whitespace-nowrap">
                            <x-phosphor-trophy-fill class="size-4 mr-1" />
                            <span class="hidden sm:inline">Winner</span>
                        </span>
                    @endif
                </div>

                <!-- SVG image/code display -->
                <div x-show="!showCode">
                    <div class="w-full p-4 border border-gray-100 rounded bg-gray-50 flex items-center justify-center overflow-hidden">
                        @if($svgMatch->getPlayer2SvgUrl())
                            <img
                                src="{{ $svgMatch->getPlayer2SvgUrl() }}"
                                alt="SVG by {{ $svgMatch->player2->name }}"
                                class="size-full max-w-full max-h-[60vh] object-contain"
                            >
                        @else
                            <div class="text-center p-4 text-gray-400">
                                <x-phosphor-image-square-fill class="size-12 mx-auto mb-2" />
                                <p class="text-sm">SVG image not available</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div x-show="showCode" x-cloak class="max-h-[60vh] w-full h-full overflow-auto">
                    @if($svgMatch->getPlayer2SvgContent())
                        <pre class="bg-gray-900 text-gray-100 rounded text-xs font-mono size-full"><code class="language-xml min-w-full min-h-full w-max">{{ $svgMatch->getPlayer2SvgContent() }}</code></pre>
                    @else
                        <div class="text-center p-8 text-gray-400 size-full flex flex-col items-center justify-center">
                            <x-phosphor-code-fill class="size-12 mx-auto mb-2" />
                            <p class="text-sm">SVG code not available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 3. Judge analysis and winner (full width) -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6 overflow-hidden">
            <div class="flex flex-col lg:flex-row">
                <!-- Left: Judge avatar -->
                <div class="p-4 lg:w-64 bg-gradient-to-r lg:bg-gradient-to-b from-amber-50 to-white flex flex-row lg:flex-col gap-3 items-center justify-center border-b lg:border-b-0 lg:border-r border-gray-100">
                    <div class="size-16 rounded-full bg-amber-100 border-2 border-white shadow flex items-center justify-center">
                        <x-phosphor-robot-fill class="size-8 text-amber-600" />
                    </div>
                    <div class="text-left lg:text-center">
                        <div class="text-base font-bold text-amber-800">GPT-4o</div>
                        <div class="text-sm text-gray-500">Judge & Evaluator</div>
                    </div>
                </div>

                <!-- Right: Winner and analysis -->
                <div class="p-4 sm:p-6 lg:flex-1/3 border-b lg:border-b-0 lg:border-r border-gray-100">
                    <!-- Winner announcement -->
                    <div class="mb-5 text-center flex flex-col items-center justify-center h-full">
                        <x-phosphor-trophy-fill class="size-10 text-yellow-500 mb-4" />
                        <span class="text-base sm:text-lg font-bold text-gray-600 mb-4">
                            The winner of this SVG challenge is
                        </span>
                        @if($svgMatch->winner)
                            <span class="inline-flex items-center px-4 py-1 rounded-full text-base font-bold {{ $svgMatch->isPlayer1Winner() ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($svgMatch->winner->name) }}
                            </span>
                            <span class="mt-1 text-xs text-gray-500">
                                {{ $svgMatch->isPlayer1Winner() ? 'Player 1' : 'Player 2' }}
                            </span>
                        @else
                            {{-- This should never happen, but just in case --}}
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-base font-medium bg-gray-100 text-gray-800">
                                No winner
                            </span>
                            <span class="mt-1 text-xs text-gray-500">
                                No winner declared
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-4 sm:p-6 lg:flex-2/3">
                    <h3 class="text-base font-semibold text-gray-700 mb-3 flex items-center">
                        <x-phosphor-note-pencil-fill class="size-5 mr-2 text-amber-500" />
                        Judge's Analysis
                    </h3>
                    @php
                        $judgeAnalysis = Str::of($svgMatch->judge_reasoning)
                            // Detect end of sentence and split into paragraphs
                            ->replaceMatches('/(?<=[.!?])\s+/', '</p><p>')
                            // Replace "Player 1" and "Player 2" with a colored name in bold
                            ->replace('Player 1', '<span class="font-bold text-red-700 rounded bg-red-100 px-1.5">Player 1</span>')
                            ->replace('Player 2', '<span class="font-bold text-blue-700 rounded bg-blue-100 px-1.5">Player 2</span>');
                    @endphp
                    <ul class="bg-gray-50 rounded-lg p-3 sm:p-4 py-2 text-gray-700 leading-relaxed text-sm sm:text-base *:my-2">
                        <p>{!! $judgeAnalysis !!}</p>
                    </ul>
                </div>
            </div>
        </div>

        <!-- 3.5 SVG Features Analysis - Collapsible section -->
        <div x-data="{ open: false }" class="bg-white rounded-lg shadow-sm border border-amber-50 mb-6 overflow-hidden">
            <!-- Header with toggle button -->
            <button @click="open = !open" class="w-full text-left p-4 flex items-center justify-between bg-gradient-to-r from-amber-50 to-white cursor-pointer">
                <div class="flex items-center">
                    <div class="size-8 rounded-full bg-amber-100 flex items-center justify-center mr-3">
                        <x-phosphor-chart-bar-fill class="size-4 text-amber-600" />
                    </div>
                    <h3 class="text-base font-medium text-gray-800">SVG Stats & Insights</h3>
                    <p class="text-sm text-gray-500 ml-3">
                        Compare technical aspects of both SVGs
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <span class="ml-2 text-xs text-amber-800 bg-amber-50 rounded-full px-2 py-0.5">{{ count($svgFeatures->flatten(1)) }} features</span>
                    <x-phosphor-caret-down-fill
                        class="size-5 text-gray-500 transition-transform"
                        x-bind:class="{'rotate-180': open}"
                    />
                </div>
            </button>

            <!-- Expandable content -->
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-y-95"
                x-transition:enter-end="opacity-100 transform scale-y-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 transform scale-y-100"
                x-transition:leave-end="opacity-0 transform scale-y-95"
                x-cloak
                class="border-t border-amber-100 p-4"
            >
                <!-- Brief explanation -->
                <p class="text-sm text-gray-600 p-4 mb-2">
                    <x-phosphor-info-fill class="size-4 text-amber-600 inline-block mr-1" />
                    These metrics analyze technical aspects of both SVG drawings. Look for differences that might explain the judge's decision.
                    Use the "View Code" button above to see the SVG code for each drawing.
                </p>

                <!-- Features by category -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($svgFeatures as $category => $features)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <!-- Category header -->
                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <span class="size-6 rounded-full bg-amber-100 flex items-center justify-center mr-2">
                                    @switch(strtolower($category))
                                        @case('complexity')
                                            <x-phosphor-code-block-fill class="size-3 text-amber-600" />
                                            @break
                                        @case('color')
                                            <x-phosphor-paint-bucket-fill class="size-3 text-amber-600" />
                                            @break
                                        @case('structure')
                                            <x-phosphor-cube-fill class="size-3 text-amber-600" />
                                            @break
                                        @case('text')
                                            <x-phosphor-text-t-fill class="size-3 text-amber-600" />
                                            @break
                                        @default
                                            <x-phosphor-star-fill class="size-3 text-amber-600" />
                                    @endswitch
                                </span>
                                {{ ucfirst($category) }} Metrics
                            </h4>

                            <!-- Features table -->
                            <div class="space-y-2">
                                @foreach($features as $feature)
                                    <div class="flex flex-col sm:flex-row">
                                        <!-- Feature name and description -->
                                        <div class="flex-1 mb-2 sm:mb-0">
                                            <div class="font-medium text-sm text-gray-800">{{ $feature['name'] }}</div>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $feature['description'] }}</p>
                                        </div>

                                        <!-- Comparison values -->
                                        <div class="w-full sm:w-auto flex text-sm">
                                            <!-- Player 1 value -->
                                            <div class="flex-1 sm:w-24 text-center">
                                                <div class="text-xs text-gray-500 mb-1">Player 1</div>
                                                <div class="px-2.5 py-1 rounded bg-red-50 text-red-800 font-mono text-xs">
                                                    @if(is_numeric($feature['player1_value']))
                                                        {{ Number::format($feature['player1_value']) }}
                                                    @else
                                                        {{ $feature['player1_value'] ?? '—' }}
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Comparison indicator -->
                                            <div class="w-12 flex items-center justify-center">
                                                @if(isset($feature['delta']) && is_numeric($feature['delta']))
                                                    @if($feature['delta'] > 0)
                                                        <span class="size-6 rounded-full bg-red-100 flex items-center justify-center" title="Player 1 has higher value">
                                                            <x-phosphor-less-than-bold class="size-4 text-red-700" />
                                                        </span>
                                                    @elseif($feature['delta'] < 0)
                                                        <span class="size-6 rounded-full bg-blue-100 flex items-center justify-center" title="Player 2 has higher value">
                                                            <x-phosphor-greater-than-bold class="size-4 text-blue-700" />
                                                        </span>
                                                    @else
                                                        <span class="size-6 rounded-full bg-slate-200 flex items-center justify-center" title="Equal values">
                                                            <x-phosphor-equals-bold class="size-4 text-slate-700" />
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="size-6 rounded-full bg-gray-200 flex items-center justify-center" title="No comparison available">
                                                        <x-phosphor-question-mark-bold class="size-4 text-gray-700" />
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Player 2 value -->
                                            <div class="flex-1 sm:w-24 text-center">
                                                <div class="text-xs text-gray-500 mb-1">Player 2</div>
                                                <div class="px-2.5 py-1 rounded bg-blue-50 text-blue-800 font-mono text-xs">
                                                    @if(is_numeric($feature['player2_value']))
                                                        {{ Number::format($feature['player2_value']) }}
                                                    @else
                                                        {{ $feature['player2_value'] ?? '—' }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center p-4 text-gray-400 md:col-span-2">
                            <x-phosphor-chart-line-fill class="size-12 mx-auto mb-2" />
                            <p class="text-sm">No feature analysis available for these SVGs</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- 4. Judging info cards -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-6 overflow-hidden">
            <div class="p-3 sm:p-4">
                <div class="flex items-center mb-4">
                    <div class="size-7 rounded-full bg-amber-100 flex items-center justify-center mr-2">
                        <x-phosphor-brain-fill class="size-4 text-amber-600" />
                    </div>
                    <h3 class="text-base font-medium text-gray-800">AI Judging Process</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <!-- Creativity -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center mb-1">
                            <div class="size-8 rounded-full bg-amber-100 flex items-center justify-center mr-3">
                                <x-phosphor-palette-fill class="size-4 text-amber-600" />
                            </div>
                            <h4 class="text-base font-medium text-gray-800">Creativity</h4>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">
                            Originality, innovative use of shapes and unique approach to the prompt.
                        </p>
                    </div>

                    <!-- Prompt Adherence -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center mb-1">
                            <div class="size-8 rounded-full bg-amber-100 flex items-center justify-center mr-3">
                                <x-phosphor-target-fill class="size-4 text-amber-600" />
                            </div>
                            <h4 class="text-base font-medium text-gray-800">Prompt Adherence</h4>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">
                            How accurately the SVG captures the essence of the prompt.
                        </p>
                    </div>

                    <!-- Visual Appeal -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center mb-1">
                            <div class="size-8 rounded-full bg-amber-100 flex items-center justify-center mr-3">
                                <x-phosphor-sparkle-fill class="size-4 text-amber-600" />
                            </div>
                            <h4 class="text-base font-medium text-gray-800">Visual Appeal</h4>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">
                            Aesthetic quality including composition, color usage and overall visual impact.
                        </p>
                    </div>
                </div>

                <div class="mt-4 text-sm text-gray-600 bg-gray-50 rounded-lg p-3">
                    <div class="items-center align-middle inline-flex">
                        <div class="size-8 rounded-full bg-amber-100 flex items-center justify-center mr-2">
                            <x-phosphor-question-fill class="size-4 text-amber-600" />
                        </div>
                        <h4 class="text-base font-medium text-gray-800">How does judging work?</h4>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">
                        SVG drawings are converted to static PNG images for evaluation.
                        The AI judge receives the original prompt and both images,
                        then determines which drawing better fulfills the evaluation
                        criteria without seeing animations, interactivity, or SVG code.
                    </p>
                </div>
            </div>
        </div>

        <!-- 5. Similar matches -->
        @if($similarMatches->count() > 0)
            <section class="mb-6">
                <h2 class="text-lg font-bold mb-3 text-gray-900 flex items-center">
                    <x-phosphor-arrows-in-line-horizontal-fill class="size-5 mr-2 text-amber-500" />
                    Similar Challenges
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                    @foreach($similarMatches as $match)
                        <x-svg.match-card :match="$match" class="hover-scale" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layouts::app>
