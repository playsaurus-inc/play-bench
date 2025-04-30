@props(['match'])

<a
    href="{{ route('svg.matches.show', $match) }}"
    class="block bg-white overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 border border-gray-100 rounded-xl"
    x-data="{ hover: false }"
    @mouseenter="hover = true"
    @mouseleave="hover = false"
>
    <!-- Header with match ID, date and prompt (with padding) -->
    <div class="p-6 pb-3">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-indigo-700">Match #{{ $match->id }}</span>
            <span class="text-xs text-gray-500">{{ $match->created_at->format('M d, Y') }}</span>
        </div>

        <!-- Prompt (single line with truncation) -->
        <p class="text-sm font-medium text-gray-800 line-clamp-1">
            "{{ $match->prompt }}"
        </p>
    </div>

    <!-- SVG Preview (full width, no horizontal padding) -->
    <div class="aspect-[2/1] flex w-full">
        <!-- Player 1 SVG -->
        <div class="w-1/2 relative flex items-center justify-center bg-gray-50 border-t border-b border-r border-gray-100">
            <div class="absolute top-2 left-2 bg-red-100 text-red-800 text-xs font-medium px-1.5 py-0.5 rounded">P1</div>
            @if($match->getPlayer1SvgUrl())
                <img
                    src="{{ $match->getPlayer1SvgUrl() }}"
                    alt="SVG by {{ $match->player1->name }}"
                    class="max-w-full max-h-full object-contain"
                >
            @else
                <x-phosphor-image-square class="size-8 text-gray-300" />
            @endif

            @if($match->isPlayer1Winner())
                <div class="absolute bottom-1 right-1">
                    <x-phosphor-trophy-fill class="size-6 text-amber-500" />
                </div>
            @endif
        </div>

        <!-- Player 2 SVG -->
        <div class="w-1/2 relative flex items-center justify-center bg-gray-50 border-t border-b border-gray-100">
            <div class="absolute top-2 left-2 bg-blue-100 text-blue-800 text-xs font-medium px-1.5 py-0.5 rounded">P2</div>
            @if($match->getPlayer2SvgUrl())
                <img
                    src="{{ $match->getPlayer2SvgUrl() }}"
                    alt="SVG by {{ $match->player2->name }}"
                    class="max-w-full max-h-full object-contain"
                >
            @else
                <x-phosphor-image-square class="size-8 text-gray-300" />
            @endif

            @if($match->isPlayer2Winner())
                <div class="absolute bottom-1 right-1">
                    <x-phosphor-trophy-fill class="size-6 text-amber-500" />
                </div>
            @endif
        </div>
    </div>

    <!-- Footer with players and view details (with padding) -->
    <div class="p-6 pt-3 flex items-center justify-between">
        <!-- Player information in compact format -->
        <div class="flex items-center gap-3 text-xs">
            <div class="flex items-center">
                <div class="size-6 rounded-full bg-red-100 flex items-center justify-center mr-1.5">
                    <x-phosphor-robot-fill class="size-3 text-red-500" />
                </div>
                <span class="font-medium truncate max-w-24">{{ $match->player1->name }}</span>
            </div>

            <span class="text-gray-400">vs</span>

            <div class="flex items-center">
                <div class="size-6 rounded-full bg-blue-100 flex items-center justify-center mr-1.5">
                    <x-phosphor-robot-fill class="size-3 text-blue-500" />
                </div>
                <span class="font-medium truncate max-w-24">{{ $match->player2->name }}</span>
            </div>
        </div>

        <!-- View details button -->
        <span class="text-xs font-medium text-indigo-600 hover:text-indigo-700 transition-colors inline-flex items-center" :class="{'translate-x-0.5': hover}">
            View
            <x-phosphor-arrow-right class="size-3.5 ml-1 transition-transform" x-bind:class="{'transform translate-x-0.5': hover}" />
        </span>
    </div>
</a>
