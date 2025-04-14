<x-layouts::app :title="'Rock Paper Scissors Matches'">
    <h1 class="text-3xl font-bold mb-6">Rock Paper Scissors Benchmark</h1>

    <div class="mb-6">
        <p class="text-gray-600">
            This benchmark evaluates AI models' ability to understand game theory and strategic thinking by playing Rock Paper Scissors.
            Models compete against each other in multiple rounds to determine which model performs better at this classic game.
        </p>
    </div>

    <x-ui.card title="Recent Matches">
        @if($matches->count() > 0)
            <x-ui.table :headers="['ID', 'Player 1', 'Player 2', 'Score', 'Winner', 'Rounds', 'Date']">
                @foreach($matches as $match)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('rps.show', $match) }}" class="text-indigo-600 hover:text-indigo-900">
                                {{ $match->id }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('models.show', $match->player1) }}" class="text-gray-900 hover:text-indigo-600">
                                {{ $match->player1->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('models.show', $match->player2) }}" class="text-gray-900 hover:text-indigo-600">
                                {{ $match->player2->name }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $match->player1_score }} - {{ $match->player2_score }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($match->isTie())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Tie
                                </span>
                            @else
                                <a href="{{ route('models.show', $match->winner) }}" class="text-gray-900 hover:text-indigo-600">
                                    {{ $match->winner->name }}
                                </a>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $match->rounds_played }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $match->created_at->format('Y-m-d H:i') }}
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>

            <div class="mt-4">
                {{ $matches->links() }}
            </div>
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
                            No matches have been recorded yet.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </x-ui.card>
</x-layouts::app>
