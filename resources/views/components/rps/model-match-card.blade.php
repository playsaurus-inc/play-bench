@props(['match', 'model'])

@php
    $isPlayer1 = $match->player1_id === $model->id;
    $opponent = $isPlayer1 ? $match->player2 : $match->player1;
    $aiModelScore = $isPlayer1 ? $match->player1_score : $match->player2_score;
    $opponentScore = $isPlayer1 ? $match->player2_score : $match->player1_score;
    $result = $match->isTie() ? 'tie' : ($match->winner_id === $model->id ? 'win' : 'loss');
@endphp
<a href="{{ route('rps.matches.show', $match) }}" class="block bg-white rounded-lg border border-gray-200 hover:shadow-md transition-all">
    <div class="p-4">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center text-sm">
                <span class="font-medium  text-gray-900">#{{ $match->id }}</span>
                <span class="mx-2 text-gray-400">•</span>
                <span class="text-gray-500">{{ $match->created_at->format('M d') }}</span>
            </div>
            <span @class([
                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                'bg-green-100 text-green-800' => $result === 'win',
                'bg-red-100 text-red-800' => $result === 'loss',
                'bg-gray-100 text-gray-800' => $result === 'tie',
            ])>
                {{ $result === 'win' ? 'Victory' : ($result === 'loss' ? 'Defeat' : 'Draw') }}
            </span>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center mr-2">
                    <x-phosphor-robot-fill class="w-4 h-4 text-gray-500" />
                </div>
                <div class="truncate max-w-[120px]">
                    <div class="text-sm font-medium text-gray-900">{{ $opponent->name }}</div>
                </div>
            </div>

            <div class="flex items-center">
                <div class="text-lg font-bold {{ $result === 'win' ? 'text-green-600' : ($result === 'loss' ? 'text-red-600' : 'text-gray-600') }}">
                    {{ $aiModelScore }} - {{ $opponentScore }}
                </div>
            </div>
        </div>

        <div class="mt-2 text-xs text-gray-500 flex justify-between items-center">
            <span>{{ $match->rounds_played }} rounds</span>
            <span class="text-amber-600 flex items-center">
                Details
                <x-phosphor-arrow-right class="w-3 h-3 ml-1" />
            </span>
        </div>
    </div>
</a>
