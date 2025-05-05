<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Model
            </th>
            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <div class="flex items-center justify-center">
                    <x-phosphor-hand-fill class="w-4 h-4 mr-1 text-red-500" />
                    RPS Rank & ELO
                </div>
            </th>
            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider opacity-50">
                <div class="flex items-center justify-center">
                    <x-phosphor-paint-brush-fill class="w-4 h-4 mr-1 text-blue-500" />
                    SVG Rank & ELO
                </div>
            </th>
            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider opacity-50">
                <div class="flex items-center justify-center">
                    <x-phosphor-crown-cross-fill class="w-4 h-4 mr-1 text-green-500" />
                    Chess Rank & ELO
                </div>
            </th>
            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Overall (*)
            </th>
            <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Actions</span>
            </th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @php $rank = 1; @endphp
        @foreach($models as $model)
            <tr class="hover:bg-gray-50 transition-colors duration-150" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                <td class="px-6 py-4 whitespace-nowrap">
                    <a href="{{ route('models.show.rps', $model) }}" class="group">
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
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    @if($model->rps_rank > 0)
                        <span class="text-sm font-medium px-2.5 py-0.5 rounded-full {{ $model->rps_rank <= 3 ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $model->rps_rank }}
                        </span>
                        <span class="text-sm font-medium text-amber-600 ml-2">
                            {{ Number::format($model->rps_elo, 0) }}
                        </span>
                    @else
                        <span class="text-sm text-gray-500">N/A</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    @if($model->svg_rank > 0)
                        <span class="text-sm font-medium px-2.5 py-0.5 rounded-full {{ $model->svg_rank <= 3 ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $model->svg_rank }}
                        </span>
                        <span class="text-sm font-medium text-amber-600 ml-2">
                            {{ Number::format($model->svg_elo, 0) }}
                        </span>
                    @else
                        <span class="text-sm text-gray-500">N/A</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center opacity-50">
                    <span class="text-sm text-gray-500">Coming soon</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <span class="text-sm font-medium px-2.5 py-0.5 rounded-full {{ $model->rank <= 3 ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $model->rank }}
                    </span>
                    <span class="text-sm font-medium text-amber-600 ml-2">
                        {{ Number::format($model->elo, 0) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <x-ui.button :href="route('models.show', $model)" variant="secondary" size="sm" class="transition-all duration-200" x-bind:class="{'opacity-0': !hover, 'opacity-100': hover}">
                        View Details
                    </x-ui.button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
