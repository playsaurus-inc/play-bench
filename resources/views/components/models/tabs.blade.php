@props(['model', 'activeTab' => 'overview'])

<div class="border-b border-gray-200 mb-6">
    <nav class="-mb-px flex space-x-8">
        <a href="{{ route('models.show', $model) }}"
           class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'overview'
                ? 'border-amber-500 text-amber-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            Overview
        </a>

        <a href="{{ route('models.show.rps', $model) }}"
           class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'rps'
                ? 'border-amber-500 text-amber-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            <span class="flex items-center">
                <x-phosphor-hand-fill class="w-4 h-4 mr-1.5 {{ $activeTab === 'rps' ? 'text-amber-500' : 'text-gray-400' }}" />
                Rock Paper Scissors
            </span>
        </a>

        <a href="{{ route('models.show.chess', $model) }}"
           class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm opacity-60 {{ $activeTab === 'chess'
                ? 'border-amber-500 text-amber-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            <span class="flex items-center">
                <x-phosphor-crown-cross-fill class="w-4 h-4 mr-1.5 {{ $activeTab === 'chess' ? 'text-amber-500' : 'text-gray-400' }}" />
                Chess
                <span class="ml-1.5 text-xs px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded">Coming soon</span>
            </span>
        </a>

        <a href="{{ route('models.show.svg', $model) }}"
           class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm opacity-60 {{ $activeTab === 'svg'
                ? 'border-amber-500 text-amber-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            <span class="flex items-center">
                <x-phosphor-paint-brush-fill class="w-4 h-4 mr-1.5 {{ $activeTab === 'svg' ? 'text-amber-500' : 'text-gray-400' }}" />
                SVG Drawing
                <span class="ml-1.5 text-xs px-1.5 py-0.5 bg-gray-100 text-gray-600 rounded">Coming soon</span>
            </span>
        </a>
    </nav>
</div>
