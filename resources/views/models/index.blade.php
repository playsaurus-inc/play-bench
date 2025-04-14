<x-layouts::app :title="'AI Models'">
    <h1 class="text-3xl font-bold mb-6">AI Models Performance</h1>

    <div class="mb-6">
        <p class="text-gray-600">
            This page displays all AI models and their performance in the Rock Paper Scissors benchmark.
            Models are ranked by win rate across all matches.
        </p>
    </div>

    <x-ui.card title="Models Ranking">
        @if($models->count() > 0)
            <x-ui.table :headers="['Rank', 'Model', 'RPS Matches', 'RPS Wins', 'Win Rate', '']">
                @foreach($models as $index => $model)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium">
                            <a href="{{ route('models.show', $model) }}" class="text-gray-900 hover:text-indigo-600">
                                {{ $model->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $model->total_rps_matches }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $model->rps_matches_won_count }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($model->total_rps_matches > 0)
                                <div class="flex items-center">
                                    <span class="mr-2">{{ number_format($model->win_rate * 100, 1) }}%</span>
                                    <div class="relative w-24 h-2 bg-gray-200 rounded-full">
                                        <div class="absolute top-0 left-0 h-2 bg-indigo-500 rounded-full" style="width: {{ $model->win_rate * 100 }}%"></div>
                                    </div>
                                </div>
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <x-ui.button :href="route('models.show', $model)" variant="secondary" class="text-xs px-3 py-1">
                                Details
                            </x-ui.button>
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>
        @else
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            No AI models have been registered yet.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </x-ui.card>
</x-layouts::app>
