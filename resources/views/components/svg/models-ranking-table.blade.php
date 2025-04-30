@props(['models'])

<div class="overflow-hidden bg-white shadow-sm border border-gray-100 sm:rounded-xl">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                    <th scope="col" class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                    <th scope="col" class="px-3 sm:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Matches</th>
                    <th scope="col" class="px-3 sm:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Wins</th>
                    <th scope="col" class="px-3 sm:px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Win Rate</th>
                    <th scope="col" class="px-3 sm:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($models as $index => $model)
                    <tr class="{{ $loop->odd ? 'bg-white' : 'bg-gray-50' }} hover:bg-amber-50 transition-colors">
                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($index <= 2)
                                <div @class([
                                    'flex items-center justify-center size-6 sm:size-8 rounded-full',
                                    'bg-amber-100 text-amber-600' => $index === 0,
                                    'bg-gray-100 text-gray-600' => $index === 1,
                                    'bg-amber-100 text-amber-600' => $index === 2,
                                ])>
                                    <span class="font-bold">{{ $index + 1 }}</span>
                                </div>
                            @else
                                <span class="pl-3">{{ $index + 1 }}</span>
                            @endif
                        </td>

                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                            <a href="{{ route('models.show.svg', $model) }}" class="block hover:text-amber-600">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 size-8 sm:size-10 bg-amber-100 rounded-full flex items-center justify-center">
                                        <x-phosphor-robot-fill class="size-4 sm:size-5 text-amber-600" />
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-800 leading-5">{{ $model->name }}</p>
                                        <p class="text-xs text-gray-500 leading-4">{{ mb_strimwidth($model->description ?? 'AI Model', 0, 30, '...') }}</p>
                                    </div>
                                </div>
                            </a>
                        </td>

                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            {{ $model->total_svg_matches }}
                        </td>

                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-center">
                            <span class="text-sm font-medium text-gray-800">{{ $model->svg_matches_won_count }}</span>
                        </td>

                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-center">
                            <div class="flex flex-col items-center">
                                <span @class([
                                    'text-sm font-medium',
                                    'text-green-600' => $model->win_rate >= 0.6,
                                    'text-amber-600' => $model->win_rate >= 0.5,
                                    'text-gray-800' => $model->win_rate < 0.5,
                                ])>
                                    {{ Number::percentage($model->win_rate * 100, precision: 1) }}
                                </span>
                                <div class="mt-1 w-16 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                    <div
                                        @class([
                                            'h-full',
                                            'bg-green-500' => $model->win_rate >= 0.6,
                                            'bg-amber-500' => $model->win_rate >= 0.5,
                                            'bg-gray-400' => $model->win_rate < 0.5,
                                        ])
                                        style="width: {{ min(100, max(10, $model->win_rate * 100)) }}%"
                                    ></div>
                                </div>
                            </div>
                        </td>

                        <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('models.show.svg', $model) }}" class="text-amber-600 hover:text-amber-800 inline-flex items-center">
                                View
                                <x-phosphor-arrow-right class="ml-1 size-4" />
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
