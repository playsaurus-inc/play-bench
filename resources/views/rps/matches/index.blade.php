<x-layouts::app :title="'Rock Paper Scissors Matches'">
    <!-- Header section -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <x-ui.button :href="route('rps.index')" variant="secondary" class="text-xs sm:text-sm">
                <x-phosphor-arrow-left class="size-4 mr-1 sm:mr-2" />
                <span class="hidden xs:inline">Back to RPS Home</span>
                <span class="xs:hidden">Back</span>
            </x-ui.button>
        </div>

        <h1 class="text-2xl sm:text-3xl font-bold mt-4 sm:mt-6 text-center">
            Rock Paper Scissors Matches
        </h1>
        
        <!-- Stats summary -->
        <div class="flex justify-center mt-4 space-x-4 sm:space-x-8">
            <div class="text-center">
                <div class="text-lg sm:text-xl font-bold text-amber-600">{{ Number::format($stats['total']) }}</div>
                <div class="text-xs sm:text-sm text-gray-500">Total Matches</div>
            </div>
            <div class="text-center">
                <div class="text-lg sm:text-xl font-bold text-amber-600">{{ Number::format($stats['rounds']) }}</div>
                <div class="text-xs sm:text-sm text-gray-500">Total Rounds</div>
            </div>
            <div class="text-center">
                <div class="text-lg sm:text-xl font-bold text-amber-600">{{ Number::format($stats['ties']) }}</div>
                <div class="text-xs sm:text-sm text-gray-500">Ties</div>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <form action="{{ route('rps.matches.index') }}" method="GET" class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
            <div class="flex-grow">
                <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort by</label>
                <select id="sort" name="sort" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm" onchange="this.form.submit()">
                    <option value="date_desc" {{ request('sort', 'date_desc') === 'date_desc' ? 'selected' : '' }}>Date (Newest first)</option>
                    <option value="date_asc" {{ request('sort') === 'date_asc' ? 'selected' : '' }}>Date (Oldest first)</option>
                    <option value="rounds" {{ request('sort') === 'rounds' ? 'selected' : '' }}>Most rounds</option>
                </select>
            </div>
            
            <div class="flex-grow-0">
                <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                @if(request()->hasAny(['sort', 'player']))
                    <x-ui.button :href="route('rps.matches.index')" variant="outline" class="w-full sm:w-auto">
                        <x-phosphor-x class="size-4 mr-1" />
                        Reset filters
                    </x-ui.button>
                @endif
            </div>
        </form>
    </div>

    <!-- Matches list -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
        @forelse($matches as $match)
            <x-rps.match-card :match="$match" />
        @empty
            <div class="col-span-full text-center py-12 bg-gray-50 rounded-lg border border-gray-200">
                <x-phosphor-hand-fill class="w-12 h-12 text-gray-400 mx-auto mb-3" />
                <p class="text-gray-600 mb-2">No matches found</p>
                @if(request()->hasAny(['sort', 'player']))
                    <x-ui.button :href="route('rps.matches.index')" variant="secondary" class="mt-3">
                        <x-phosphor-arrow-counter-clockwise class="size-4 mr-1" />
                        Reset filters
                    </x-ui.button>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $matches->links() }}
    </div>
</x-layouts::app>
