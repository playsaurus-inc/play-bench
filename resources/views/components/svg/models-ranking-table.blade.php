@props(['models'])

<div class="overflow-hidden bg-white shadow-sm border border-gray-100 sm:rounded-xl">
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
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Wins / Losses
                    </th>
                    <th scope="col" class="min-w-46 px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
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
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($models as $index => $model)
                    <tr
                        class="hover:bg-gray-50 transition-colors duration-150"
                        x-data="{ hover: false }"
                        x-on:mouseenter="hover = true"
                        x-on:mouseleave="hover = false"
                    >
                        <td class="px-6 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <div @class([
                                    'w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold',
                                    'bg-amber-100 border-2 border-amber-200 text-amber-800' => $model->svg_rank < 4,
                                    'bg-gray-100 border border-gray-200 text-gray-600' => $model->svg_rank >= 4,
                                ])>{{ $model->svg_rank }}</div>
                            </div>
                        </td>

                        <td class="px-6 py-3 whitespace-nowrap">
                            <a href="{{ route('models.show.svg', $model) }}" class="group">
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
                            <div class="text-sm text-gray-900">{{ $model->total_svg_matches }}</div>
                        </td>
                        <td class="px-6 py-3">
                            <div class="text-sm text-gray-900 flex flex-row gap-2 justify-center">
                                <span class="w-6 text-center font-bold text-green-600">{{ $model->svg_matches_won_count }}</span>
                                <span class="text-sm text-gray-400">/</span>
                                <span class="w-6 text-center font-bold text-red-600">{{ $model->svg_matches_lost_count }}</span>
                            </div>
                        </td>

                        <td class="min-w-46 px-6 py-3 whitespace-nowrap">
                            <div class="flex items-center w-full">
                                <span class="w-12 text-right mr-2 text-sm font-medium shrink-0 text-slate-600">
                                    {{ Number::percentage($model->win_rate * 100, precision: 1) }}
                                </span>
                                <div class="flex h-2 w-full overflow-hidden rounded-full bg-red-600">
                                    <div
                                        class="bg-green-500 border-r-2 border-white h-full"
                                        style="width: {{ Number::percentage($model->svg_matches_won_count / $model->total_svg_matches * 100) }}"
                                    ></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-3 text-right whitespace-nowrap">
                            <div class="text-sm text-amber-600 font-bold">{{ Number::format($model->svg_elo, 0) }}</div>
                        </td>

                        <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <x-ui.button :href="route('models.show.svg', $model)" variant="secondary" size="sm" class="transition-all duration-200" x-bind:class="{'opacity-0': !hover, 'opacity-100': hover}">
                                View Details
                            </x-ui.button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
