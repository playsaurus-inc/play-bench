@props([
    'title' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow-sm sm:rounded-lg']) }}>
    @if ($title)
        <div class="border-b border-gray-200 bg-white px-6 py-4">
            <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
        </div>
    @endif
    <div class="bg-white p-6">
        {{ $slot }}
    </div>
</div>
