@props([
    'models',
    'title' => 'Models Ranking for Rock Paper Scissors by ELO',
    'showCount' => true,
    'containerClasses' => 'bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100',
    'compact' => false
])

<div class="{{ $containerClasses }}">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-100">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
            @if($showCount)
                <span class="text-sm text-gray-500">{{ $models->count() }} models</span>
            @endif
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Rank
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Model
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        RPS Matches
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        RPS Wins
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Win Rate
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        ELO Rating
                    </th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($models as $model)
                    <tr
                        class="hover:bg-gray-50 transition-colors duration-150"
                        x-data="{ hover: false }"
                        x-on:mouseenter="hover = true"
                        x-on:mouseleave="hover = false"
                    >
                        <td class="px-6 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full {{ $model->rps_rank < 4 ? 'bg-amber-100' : 'bg-gray-100' }} flex items-center justify-center {{ $model->rps_rank < 4 ? 'border-2 border-amber-200' : 'border border-gray-200' }}">
                                    <span class="{{ $model->rps_rank < 4 ? 'text-amber-800' : 'text-gray-600' }} text-sm font-bold">{{ $model->rps_rank }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap">
                            <a href="{{ route('rps.models.show', $model) }}" class="group">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center group-hover:bg-amber-50 transition-colors">
                                        <x-phosphor-robot-fill class="h-5 w-5 text-gray-500 group-hover:text-amber-600 transition-colors" />
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 group-hover:text-amber-600 transition-colors">{{ $model->name }}</div>
                                    </div>
                                </div>
                            </a>
                        </td>
                        <td class="px-6 py-3 text-right whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $model->total_rps_matches }}</div>
                        </td>
                        <td class="px-6 py-3 text-right whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $model->rps_matches_won_count }}</div>
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap">
                            @if($model->total_rps_matches > 0)
                                <div class="flex items-center">
                                    <span class="mr-2 text-sm font-medium {{ $model->win_rate > 0.5 ? 'text-green-600' : ($model->win_rate == 0.5 ? 'text-amber-600' : 'text-red-600') }}">
                                        {{ Number::percentage($model->win_rate * 100, precision: 1) }}
                                    </span>
                                    <div class="grow relative w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="absolute top-0 left-0 h-2 rounded-full {{ $model->win_rate > 0.5 ? 'bg-green-500' : ($model->win_rate == 0.5 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ $model->win_rate * 100 }}%"></div>
                                    </div>
                                </div>
                            @else
                                <span class="text-sm text-gray-500">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right whitespace-nowrap">
                            <div class="text-sm text-amber-600 font-bold">{{ Number::format($model->rps_elo, 0) }}</div>
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <x-ui.button :href="route('rps.models.show', $model)" variant="secondary" size="sm" class="transition-all duration-200" x-bind:class="{'opacity-0': !hover, 'opacity-100': hover}">
                                View Details
                            </x-ui.button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
