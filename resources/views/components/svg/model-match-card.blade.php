@props(['match', 'model'])

@php
$isPlayer1 = $match->player1_id === $model->id;
$opponent = $isPlayer1 ? $match->player2 : $match->player1;
$result = $match->winner_id === $model->id ? 'win' : 'loss';
$resultColor = $result === 'win' ? 'green' : 'red';
$modelSvgUrl = $isPlayer1 ? $match->getPlayer1SvgUrl() : $match->getPlayer2SvgUrl();
@endphp
<a href="{{ route('svg.matches.show', $match) }}" class="block bg-white rounded-lg border border-gray-200 hover:shadow-md transition-all">
    <div class="flex overflow-hidden">
        <!-- SVG preview thumbnail -->
        <div class="w-24 h-24 flex-shrink-0 bg-gray-50 border-r border-gray-100 flex items-center justify-center">
            @if($modelSvgUrl)
                <img src="{{ $modelSvgUrl }}" alt="SVG Drawing" class="max-h-full max-w-full object-contain p-1" />
            @else
                <x-phosphor-image-square class="w-6 h-6 text-gray-300" />
            @endif
        </div>

        <!-- Match details -->
        <div class="p-3 flex-grow">
            <div class="flex items-center justify-between mb-1">
                <div class="flex items-center">
                    <span class="font-medium text-xs text-gray-500">#{{ $match->id }}</span>
                    <span class="mx-2 text-gray-400">•</span>
                    <span class="text-xs text-gray-500">{{ $match->created_at->format('M d') }}</span>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $resultColor }}-100 text-{{ $resultColor }}-800">
                    {{ ucfirst($result) }}
                </span>
            </div>

            <!-- Show prompt text -->
            <div class="text-xs text-gray-700 line-clamp-2 mb-1">
                "{{ Str::limit($match->prompt, 80) }}"
            </div>

            <div class="flex items-center justify-between mt-2">
                <div class="flex items-center">
                    <div class="bg-gray-100 rounded-full w-5 h-5 flex items-center justify-center mr-1.5">
                        <x-phosphor-robot-fill class="w-2.5 h-2.5 text-gray-500" />
                    </div>
                    <div class="truncate max-w-[120px]">
                        <div class="text-xs font-medium text-gray-900">vs {{ $opponent->name }}</div>
                    </div>
                </div>

                <span class="text-amber-600 text-xs flex items-center">
                    Details
                    <x-phosphor-arrow-right class="w-3 h-3 ml-1" />
                </span>
            </div>
        </div>
    </div>
</a>
