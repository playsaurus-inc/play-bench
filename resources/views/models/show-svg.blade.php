<x-layouts::app :title="$model->name . ' - SVG Drawing'">
    <!-- Header Component -->
    <x-models.header :model="$model" :activeTab="$activeTab" />

    <!-- Main content -->
    <div class="grid grid-cols-1 xl:grid-cols-3 xl:gap-8">
        <!-- Left sidebar: Artist profile and best themes -->
        <div class="space-y-8">
            <!-- Artist Profile Card -->
            <x-ui.card title="AI Model Profile" subtitle="SVG Drawing performance stats">
                <div class="space-y-6">
                    <!-- Basic stats -->
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $totalSvgMatches }}</div>
                            <div class="text-xs text-gray-500">Total Matches</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold {{ $winRate > 0.5 ? 'text-green-600' : 'text-gray-900' }}">
                                {{ Number::percentage($winRate * 100, 1) }}
                            </div>
                            <div class="text-xs text-gray-500">Win Rate</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-amber-600">{{ $totalSvgWins }}</div>
                            <div class="text-xs text-gray-500">Wins</div>
                        </div>
                    </div>

                    <!-- ELO Rating -->
                    <div class="p-4 bg-gradient-to-r from-amber-50 to-white rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-sm font-medium text-gray-700">SVG ELO Rating</div>
                            <div class="text-xl font-bold text-amber-600">{{ Number::format($model->svg_elo, 0) }}</div>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-amber-500 h-2 rounded-full" style="width: {{ min(100, ($model->svg_elo / 1400) * 100) }}%"></div>
                        </div>
                    </div>

                </div>
            </x-ui.card>

        </div>

        <!-- Main content: Portfolio and match history -->
        <div class="md:col-span-2 space-y-8">
            <!-- Performance against other models -->
            @if($opponents->count() > 0)
                <x-ui.card title="Performance Against Other AI Models" subtitle="Head-to-head statistics">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        AI Model
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Matches
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Win Rate
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" x-data="{ hoverRow: null }">
                                @foreach($opponents->sortByDesc('win_rate') as $opponent)
                                    <tr
                                        x-on:mouseenter="hoverRow = {{ $opponent->model->id }}"
                                        x-on:mouseleave="hoverRow = null"
                                        class="hover:bg-gray-50 transition-colors"
                                    >
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <a href="{{ route('models.show.svg', $opponent->model) }}" class="group">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 w-8 h-8 rounded-md bg-gray-100 flex items-center justify-center overflow-hidden group-hover:bg-amber-50 transition-colors">
                                                        <x-phosphor-robot-fill class="w-4 h-4 text-gray-500 group-hover:text-amber-600" />
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-sm font-medium text-gray-900 group-hover:text-amber-600 transition-colors">{{ $opponent->model->name }}</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right">
                                            <div class="text-sm text-gray-900">{{ $opponent->total_matches }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right">
                                            <div class="text-sm font-medium {{ $opponent->win_rate > 0.5 ? 'text-green-600' : ($opponent->win_rate == 0.5 ? 'text-amber-600' : 'text-red-600') }}">
                                                {{ Number::percentage($opponent->win_rate * 100, 1) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($opponent->win_rate > 0.7)
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Outperforms
                                                </span>
                                            @elseif($opponent->win_rate > 0.5)
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Better than
                                                </span>
                                            @elseif($opponent->win_rate == 0.5)
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Equal match
                                                </span>
                                            @elseif($opponent->win_rate < 0.3)
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Struggles against
                                                </span>
                                            @else
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">
                                                    Slightly weaker
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-ui.card>
            @endif


            @if($winningArtworks->isNotEmpty())
                <x-ui.card title="Winning SVG Gallery" subtitle="Most successful drawings">
                    <div class="grid grid-cols-2 md:grid-cols-4 -mx-6 -my-5">
                        @foreach($winningArtworks as $artwork)
                            <a href="{{ route('svg.matches.show', $artwork['match']) }}" class="block aspect-square bg-gray-50 overflow-hidden border border-gray-100 hover:shadow-md transition-all group">
                                <div class="relative w-full h-full">
                                    <!-- Artwork -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        @if($artwork['svg_url'])
                                            <img src="{{ $artwork['svg_url'] }}" alt="SVG Drawing" class="max-w-full max-h-full object-contain" />
                                        @else
                                            <x-phosphor-image-square class="w-12 h-12 text-gray-300" />
                                        @endif
                                    </div>

                                    <!-- Overlay on hover  -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900/70 via-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-3">
                                        <div class="text-white text-xs font-medium line-clamp-4 overflow-y-auto max-h-24">
                                            "{{ $artwork['prompt'] }}"
                                        </div>
                                        <div class="text-white/80 text-xs flex items-center mt-1">
                                            <x-phosphor-calendar class="w-3 h-3 mr-1" />
                                            {{ $artwork['match']->created_at->format('M d, Y') }}
                                        </div>
                                    </div>

                                    <!-- Winner badge -->
                                    <div class="absolute top-1 left-1 px-1.5 py-0.5 bg-green-100 text-green-800 text-xs font-medium rounded flex items-center space-x-1">
                                        <x-phosphor-trophy-fill class="w-3 h-3" />
                                        <span>Winner</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </x-ui.card>
            @endif

            <!-- Failed Artwork Section -->
            @if($failedArtworks->isNotEmpty())
                <x-ui.card title="Failed Attempts" subtitle="SVGs that didn't win">
                    <div class="grid grid-cols-2 md:grid-cols-4 -mx-6 -my-5">
                        @foreach($failedArtworks as $artwork)
                            <a href="{{ route('svg.matches.show', $artwork['match']) }}" class="block aspect-square bg-gray-50 overflow-hidden border border-gray-100 hover:shadow-md transition-all group">
                                <div class="relative w-full h-full">
                                    <!-- Artwork -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        @if($artwork['svg_url'])
                                            <img src="{{ $artwork['svg_url'] }}" alt="SVG Drawing" class="max-w-full max-h-full object-contain" />
                                        @else
                                            <x-phosphor-image-square class="w-12 h-12 text-gray-300" />
                                        @endif
                                    </div>

                                    <!-- Overlay on hover  -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900/70 via-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-3">
                                        <div class="text-white text-xs font-medium line-clamp-4 overflow-y-auto max-h-24">
                                            "{{ $artwork['prompt'] }}"
                                        </div>
                                        <div class="text-white/80 text-xs flex items-center mt-1">
                                            <x-phosphor-calendar class="w-3 h-3 mr-1" />
                                            {{ $artwork['match']->created_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                    <!-- Loser badge -->
                                    <div class="absolute top-1 left-1 px-1.5 py-0.5 bg-red-100 text-red-800 text-xs font-medium rounded flex items-center space-x-1">
                                        <x-phosphor-x-circle-fill class="w-3 h-3" />
                                        <span>Loser</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </x-ui.card>
            @endif

            <!-- Recent matches -->
            @if($svgMatches->count() > 0)
                <x-ui.card title="Recent SVG Drawing Matches" subtitle="Latest drawing competitions">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($svgMatches as $match)
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

                                        <!-- Show more prompt text without tooltips -->
                                        <div class="text-xs text-gray-700 line-clamp-2 mb-1">
                                            "{{ $match->prompt }}"
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
                        @endforeach
                    </div>
                </x-ui.card>
            @endif
        </div>
    </div>
</x-layouts::app>
