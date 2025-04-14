@props(['move', 'size' => 'md', 'winner' => false, 'class' => ''])

@php
    $sizeClasses = [
        'xs' => 'w-4 h-4',
        'sm' => 'w-5 h-5',
        'md' => 'w-6 h-6',
        'lg' => 'w-8 h-8',
        'xl' => 'w-10 h-10',
    ][$size] ?? 'w-6 h-6';

    $baseClasses = 'inline-block ' . $sizeClasses . ' ' . $class;

    $moveColor = [
        'rock' => $winner ? 'text-red-600' : 'text-gray-600',
        'paper' => $winner ? 'text-blue-600' : 'text-gray-600',
        'scissors' => $winner ? 'text-green-600' : 'text-gray-600',
    ][$move] ?? 'text-gray-600';

    $icon = match($move) {
        'rock' => '<x-phosphor-hand-fist-fill class="' . $baseClasses . ' ' . $moveColor . '" />',
        'paper' => '<x-phosphor-hand-fill class="' . $baseClasses . ' ' . $moveColor . '" />',
        'scissors' => '<x-phosphor-scissors-fill class="' . $baseClasses . ' ' . $moveColor . '" />',
        default => '<x-phosphor-question-fill class="' . $baseClasses . ' ' . $moveColor . '" />',
    };
@endphp

<div class="inline-block {{ $winner ? 'relative' : '' }}">
    {!! $icon !!}
    @if($winner)
        <div class="absolute -top-1 -right-1 rounded-full bg-white p-0.5 shadow-sm">
            <div class="w-2 h-2 bg-amber-500 rounded-full"></div>
        </div>
    @endif
</div>
