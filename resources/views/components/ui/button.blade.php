@props([
    'type' => 'button',
    'href' => null,
    'variant' => 'primary', // primary, secondary, danger
])

@php
    $baseClasses = 'inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150';

    $variantClasses = match($variant) {
        'primary' => 'border-transparent bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
        'secondary' => 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-gray-500',
        'danger' => 'border-transparent bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        default => 'border-transparent bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
    };

    $classes = $baseClasses . ' ' . $variantClasses;
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
