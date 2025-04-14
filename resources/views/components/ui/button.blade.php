@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
])

@php
    $variantClasses = [
        'primary' => 'bg-amber-500 text-white hover:bg-amber-600 focus:ring-amber-500 border-transparent',
        'secondary' => 'bg-white text-gray-700 hover:bg-gray-50 focus:ring-amber-500 border-gray-300',
        'outline' => 'bg-transparent text-amber-600 hover:text-amber-700 hover:bg-amber-50 focus:ring-amber-500 border-amber-500',
        'danger' => 'bg-red-500 text-white hover:bg-red-600 focus:ring-red-500 border-transparent',
        'success' => 'bg-green-500 text-white hover:bg-green-600 focus:ring-green-500 border-transparent',
        'info' => 'bg-blue-500 text-white hover:bg-blue-600 focus:ring-blue-500 border-transparent',
        'warning' => 'bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-500 border-transparent',
        'subtle' => 'bg-transparent text-gray-600 hover:text-gray-900 focus:ring-amber-500 border-transparent',
    ];

    $sizeClasses = [
        'xs' => 'py-1 px-2.5 text-xs rounded',
        'sm' => 'py-1.5 px-3 text-sm rounded-md',
        'md' => 'py-2 px-4 text-sm rounded-md',
        'lg' => 'py-2.5 px-5 text-base rounded-md',
        'xl' => 'py-3 px-6 text-base rounded-md',
    ];

    $baseClasses = 'inline-flex items-center justify-center border font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200';
    $classes = $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
