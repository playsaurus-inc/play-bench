<x-layouts::app :title="'SVG Drawing Matches'">
    <!-- Hero header section -->
    <div class="relative mb-8 bg-gradient-to-br from-amber-50 to-white rounded-3xl shadow-sm border border-amber-100 overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute right-0 top-0 w-48 h-48 bg-amber-100 rounded-full -translate-y-1/2 translate-x-1/4 opacity-70"></div>
        <div class="absolute left-0 bottom-0 w-24 h-24 bg-amber-50 rounded-full translate-y-1/2 -translate-x-1/4 opacity-80"></div>

        <!-- SVG Design Elements (subtle background) -->
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
        <div class="relative px-6 py-6 md:px-8 md:py-7">
            <div class="flex flex-col md:flex-row md:items-center">
                <div class="md:w-1/3 mb-4 md:mb-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        SVG Drawing Gallery
                    </h1>
                    <p class="text-sm text-gray-500 mt-2 hidden md:block">
                        Browse, filter and discover artworks created by AI models
                    </p>
                </div>

                <!-- Stats cards -->
                <div class="md:w-2/3 grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                    <div class="bg-white rounded-xl shadow-sm p-3 flex items-center border border-gray-100">
                        <div class="p-2 bg-amber-100 rounded-full mr-3">
                            <x-phosphor-image-fill class="size-5 text-amber-600" />
                        </div>
                        <div>
                            <div class="text-lg font-bold text-amber-600">{{ Number::format($stats['total']) }}</div>
                            <div class="text-xs text-gray-500">Total Artworks</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-3 flex items-center border border-gray-100">
                        <div class="p-2 bg-amber-100 rounded-full mr-3">
                            <x-phosphor-robot-fill class="size-5 text-amber-600" />
                        </div>
                        <div>
                            <div class="text-lg font-bold text-amber-600">{{ Number::format($stats['models']) }}</div>
                            <div class="text-xs text-gray-500">AI Artists</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-3 flex items-center border border-gray-100">
                        <div class="p-2 bg-amber-100 rounded-full mr-3">
                            <x-phosphor-text-t-fill class="size-5 text-amber-600" />
                        </div>
                        <div>
                            <div class="text-lg font-bold text-amber-600">{{ Number::format($stats['unique_prompts']) }}</div>
                            <div class="text-xs text-gray-500">Unique Prompts</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick filters section -->
    <x-svg.quick-filters />

    <!-- Enhanced filters section -->
    <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center mb-3">
            <x-phosphor-funnel-fill class="mr-2 size-4 text-amber-500" />
            <h2 class="text-base font-medium">Filter Drawings</h2>
        </div>

        <form action="{{ route('svg.matches.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <!-- AI Model filter -->
                <div>
                    <label for="model" class="block text-xs font-medium text-gray-700 mb-1">AI Artist</label>
                    <select id="model" name="model" class="block w-full rounded-md border border-gray-700/10 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm p-3">
                        <option value="">All Artists</option>
                        @foreach($models as $model)
                            <option value="{{ $model->slug }}" {{ request('model') == $model->slug ? 'selected' : '' }}>
                                {{ $model->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Contender AI Model filter -->
                <div>
                    <label for="contender" class="block text-xs font-medium text-gray-700 mb-1">Opponent Artist</label>
                    <select id="contender" name="contender" class="block w-full rounded-md border border-gray-700/10 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm p-3">
                        <option value="">Any Opponent</option>
                        @foreach($models as $model)
                            <option value="{{ $model->slug }}" {{ request('contender') == $model->slug ? 'selected' : '' }}>
                                {{ $model->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort filter -->
                <div>
                    <label for="sort" class="block text-xs font-medium text-gray-700 mb-1">Sort by</label>
                    <select id="sort" name="sort" class="block w-full rounded-md border border-gray-700/10 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm p-3">
                        <option value="date_desc" {{ request('sort', 'date_desc') === 'date_desc' ? 'selected' : '' }}>Newest</option>
                        <option value="date_asc" {{ request('sort') === 'date_asc' ? 'selected' : '' }}>Oldest</option>
                        <option value="complexity" {{ request('sort') === 'complexity' ? 'selected' : '' }}>Most complex (path commands)</option>
                        <option value="animations" {{ request('sort') === 'animations' ? 'selected' : '' }}>Most animations</option>
                        <option value="text" {{ request('sort') === 'text' ? 'selected' : '' }}>Most text elements</option>
                        <option value="gradients" {{ request('sort') === 'gradients' ? 'selected' : '' }}>Most gradients</option>
                    </select>
                </div>

                <!-- Prompt keyword filter -->
                <div>
                    <label for="prompt" class="block text-xs font-medium text-gray-700 mb-1">Prompt contains</label>
                    <input
                        type="text"
                        id="prompt"
                        name="prompt"
                        value="{{ request('prompt') }}"
                        placeholder="e.g. landscape, giraffe, robot"
                        class="block w-full rounded-md border border-gray-700/10 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm p-3"
                    >
                </div>
            </div>

            <x-svg.quick-themes class="mt-4" />

            <div class="flex items-center justify-between mt-4">
                <div>
                    @if(request()->hasAny(['sort', 'model', 'contender', 'winner', 'prompt']))
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                            <x-phosphor-funnel-fill class="size-3 mr-1" />
                            Filters applied
                        </span>
                    @endif
                </div>

                <div class="flex space-x-2">
                    @if(request()->hasAny(['sort', 'model', 'contender', 'winner', 'prompt']))
                        <x-ui.button :href="route('svg.matches.index')" variant="outline" size="sm" class="!px-3 !py-1">
                            <x-phosphor-x class="size-3.5 mr-1" />
                            Reset
                        </x-ui.button>
                    @endif

                    <x-ui.button type="submit" variant="primary" size="sm" class="!px-3 !py-1 cursor-pointer">
                        <x-phosphor-funnel-fill class="size-3.5 mr-1" />
                        Apply Filters
                    </x-ui.button>
                </div>
            </div>
        </form>
    </div>


    <!-- Selected model stats (if filtering by model) -->
    @if(isset($selectedModel) && $selectedModel && isset($selectedContender) && $selectedContender)
        <x-svg.models-matchup-card :model="$selectedModel" :contender="$selectedContender" />
    @elseif(isset($selectedModel) && $selectedModel)
        <x-svg.model-info-card :model="$selectedModel" />
    @endif

    <!-- Matches list with empty state -->
    <div>
        @if($matches->isEmpty())
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-8 text-center">
                <div class="mx-auto max-w-md">
                    <x-phosphor-image-fill class="size-12 text-gray-400 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No drawings found</h3>
                    <p class="text-gray-500 mb-6">We couldn't find any SVG drawings matching your filters.</p>

                    @if(request()->hasAny(['sort', 'model', 'contender', 'prompt']))
                        <x-ui.button :href="route('svg.matches.index')" variant="secondary">
                            <x-phosphor-arrow-counter-clockwise class="size-4 mr-1" />
                            Reset filters
                        </x-ui.button>
                    @endif
                </div>
            </div>
        @else
            <!-- Matches count and active filters summary -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-900 mb-2 sm:mb-0">
                    {{ Number::format($matches->total()) }} {{ Str::plural('drawing', $matches->total()) }} found
                </h2>

                <div class="flex flex-wrap gap-2">
                    @if(isset($selectedModel))
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-amber-100 text-amber-800">
                            Artist: {{ $selectedModel->name }}
                            <a href="{{ route('svg.matches.index', request()->except('model')) }}" class="ml-1.5 text-amber-600 hover:text-amber-800">
                                <x-phosphor-x-circle-fill class="size-4" />
                            </a>
                        </span>
                    @endif

                    @if(isset($selectedContender))
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-amber-100 text-amber-800">
                            Opponent: {{ $selectedContender->name }}
                            <a href="{{ route('svg.matches.index', request()->except('contender')) }}" class="ml-1.5 text-amber-600 hover:text-amber-800">
                                <x-phosphor-x-circle-fill class="size-4" />
                            </a>
                        </span>
                    @endif

                    @if(request('prompt'))
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-amber-100 text-amber-800">
                            Prompt: "{{ request('prompt') }}"
                            <a href="{{ route('svg.matches.index', request()->except('prompt')) }}" class="ml-1.5 text-amber-600 hover:text-amber-800">
                                <x-phosphor-x-circle-fill class="size-4" />
                            </a>
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
                @foreach($matches as $match)
                    <x-svg.match-card :match="$match" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $matches->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
