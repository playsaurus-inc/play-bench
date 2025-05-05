<x-layouts::app :title="'Rock Paper Scissors Matches'">
    <!-- Hero header section -->
    <div class="relative mb-8 bg-gradient-to-br from-amber-50 to-white rounded-3xl shadow-sm border border-amber-100 overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute right-0 top-0 w-48 h-48 bg-amber-100 rounded-full -translate-y-1/2 translate-x-1/4 opacity-70"></div>
        <div class="absolute left-0 bottom-0 w-24 h-24 bg-amber-50 rounded-full translate-y-1/2 -translate-x-1/4 opacity-80"></div>

        <!-- Content -->
        <div class="relative px-6 py-6 md:px-8 md:py-7">

            <div class="flex flex-col md:flex-row md:items-center">
                <div class="md:w-1/3 mb-4 md:mb-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                        Rock Paper Scissors Matches
                    </h1>
                    <p class="text-sm text-gray-500 mt-2 hidden md:block">
                        Browse, filter and discover matches between AI models
                    </p>
                </div>

                <!-- Stats cards -->
                <div class="md:w-2/3 grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                    <div class="bg-white rounded-xl shadow-sm p-3 flex items-center border border-gray-100">
                        <div class="p-2 bg-amber-100 rounded-full mr-3">
                            <x-phosphor-trophy-fill class="size-5 text-amber-600" />
                        </div>
                        <div>
                            <div class="text-lg font-bold text-amber-600">{{ Number::format($stats['total']) }}</div>
                            <div class="text-xs text-gray-500">Total Matches</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-3 flex items-center border border-gray-100">
                        <div class="p-2 bg-amber-100 rounded-full mr-3">
                            <x-phosphor-circle-notch-fill class="size-5 text-amber-600" />
                        </div>
                        <div>
                            <div class="text-lg font-bold text-amber-600">{{ Number::format($stats['rounds']) }}</div>
                            <div class="text-xs text-gray-500">Total Rounds</div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-3 flex items-center border border-gray-100">
                        <div class="p-2 bg-amber-100 rounded-full mr-3">
                            <x-phosphor-equals-fill class="size-5 text-amber-600" />
                        </div>
                        <div>
                            <div class="text-lg font-bold text-amber-600">{{ Number::format($stats['ties']) }}</div>
                            <div class="text-xs text-gray-500">Tied Matches</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick filters section -->
    <x-rps.quick-filters />

    <!-- Enhanced filters section -->
    <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center mb-3">
            <x-phosphor-funnel-fill class="mr-2 size-4 text-amber-500" />
            <h2 class="text-base font-medium">Filter Matches</h2>
        </div>

        <form action="{{ route('rps.matches.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <!-- AI Model filter -->
                <div>
                    <label for="model" class="block text-xs font-medium text-gray-700 mb-1">AI Model</label>
                    <select id="model" name="model" class="block w-full rounded-md border border-gray-700/10 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm p-3">
                        <option value="">All Models</option>
                        @foreach($models as $model)
                            <option value="{{ $model->slug }}" {{ request('model') == $model->slug ? 'selected' : '' }}>
                                {{ $model->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Contender AI Model filter -->
                <div>
                    <label for="contender" class="block text-xs font-medium text-gray-700 mb-1">Opponent Model</label>
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
                        <option value="rounds_desc" {{ request('sort') === 'rounds_desc' ? 'selected' : '' }}>Most rounds</option>
                        <option value="rounds_asc" {{ request('sort') === 'rounds_asc' ? 'selected' : '' }}>Fewest rounds</option>
                        <option value="score_diff" {{ request('sort') === 'score_diff' ? 'selected' : '' }}>Largest score difference</option>
                    </select>
                </div>

                <!-- Match Result filter -->
                <div>
                    <label for="winner" class="block text-xs font-medium text-gray-700 mb-1">Match Result</label>
                    <select id="winner" name="winner" class="block w-full rounded-md border border-gray-700/10 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm p-3">
                        <option value="">Any result</option>
                        <option value="tie" {{ request('winner') === 'tie' ? 'selected' : '' }}>Only ties</option>
                        @foreach($models as $model)
                            <option value="{{ $model->id }}" {{ request('winner') == $model->id ? 'selected' : '' }}>
                                Won by {{ $model->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between mt-3">
                <div>
                    @if(request()->hasAny(['sort', 'model', 'contender', 'winner']))
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                            <x-phosphor-funnel-fill class="size-3 mr-1" />
                            Filters applied
                        </span>
                    @endif
                </div>

                <div class="flex space-x-2">
                    @if(request()->hasAny(['sort', 'model', 'contender', 'winner']))
                        <x-ui.button :href="route('rps.matches.index')" variant="outline" size="sm" class="!px-3 !py-1">
                            <x-phosphor-x class="size-3.5 mr-1" />
                            Reset
                        </x-ui.button>
                    @endif

                    <x-ui.button type="submit" variant="primary" size="sm" class="!px-3 !py-1">
                        <x-phosphor-funnel-fill class="size-3.5 mr-1" />
                        Apply Filters
                    </x-ui.button>
                </div>
            </div>
        </form>
    </div>

    <!-- Selected model stats (if filtering by model) -->
    @if(isset($selectedModel) && $selectedModel && isset($selectedContender) && $selectedContender)
        <x-rps.models-matchup-card :model="$selectedModel" :contender="$selectedContender" />
    @elseif(isset($selectedModel) && $selectedModel)
        <x-rps.model-info-card :model="$selectedModel" />
    @endif

    <!-- Matches list with empty state -->
    <div>
        @if($matches->isEmpty())
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-8 text-center">
                <div class="mx-auto max-w-md">
                    <x-phosphor-hand-fill class="size-12 text-gray-400 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No matches found</h3>
                    <p class="text-gray-500 mb-6">We couldn't find any matches matching your filters.</p>

                    @if(request()->hasAny(['sort', 'model', 'winner', 'match_type']))
                        <x-ui.button :href="route('rps.matches.index')" variant="secondary">
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
                    {{ Number::format($matches->total()) }} {{ Str::plural('match', $matches->total()) }} found
                </h2>

                <div class="flex flex-wrap gap-2">
                    @if(isset($selectedModel))
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-amber-100 text-amber-800">
                            Model: {{ $selectedModel->name }}
                            <a href="{{ route('rps.matches.index', request()->except('model')) }}" class="ml-1.5 text-amber-600 hover:text-amber-800">
                                <x-phosphor-x-circle-fill class="size-4" />
                            </a>
                        </span>
                    @endif

                    @if(isset($selectedContender))
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-amber-100 text-amber-800">
                            Opponent: {{ $models->firstWhere('slug', request('contender'))->name }}
                            <a href="{{ route('rps.matches.index', request()->except('contender')) }}" class="ml-1.5 text-amber-600 hover:text-amber-800">
                                <x-phosphor-x-circle-fill class="size-4" />
                            </a>
                        </span>
                    @endif

                    @if(request('winner'))
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-amber-100 text-amber-800">
                            @if(request('winner') === 'tie')
                                Result: Ties
                            @else
                                Winner: {{ $models->firstWhere('id', request('winner'))->name }}
                            @endif
                            <a href="{{ route('rps.matches.index', request()->except('winner')) }}" class="ml-1.5 text-amber-600 hover:text-amber-800">
                                <x-phosphor-x-circle-fill class="size-4" />
                            </a>
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
                @foreach($matches as $match)
                    <x-rps.match-card :match="$match" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $matches->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>
