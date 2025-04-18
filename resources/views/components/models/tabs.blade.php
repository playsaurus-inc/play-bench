@props(['model', 'activeTab' => 'overview'])

<div class="mb-6">
    <!-- Mobile navigation (always visible on xs and sm screens) -->
    <div class="block md:hidden">
        <nav class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <a
                href="{{ route('models.show', $model) }}"
                class="flex items-center p-3 {{ $activeTab === 'overview' ? 'bg-amber-50 border-l-4 border-amber-500 text-amber-700' : 'border-l-4 border-transparent hover:bg-gray-50' }}"
            >
                <x-phosphor-star-fill class="w-5 h-5 mr-3 {{ $activeTab === 'overview' ? 'text-amber-500' : 'text-gray-400' }}" />
                <span class="font-medium">Overview</span>
            </a>

            <a
                href="{{ route('models.show.rps', $model) }}"
                class="flex items-center p-3 {{ $activeTab === 'rps' ? 'bg-amber-50 border-l-4 border-amber-500 text-amber-700' : 'border-l-4 border-transparent hover:bg-gray-50' }}"
            >
                <x-phosphor-hand-fill class="w-5 h-5 mr-3 {{ $activeTab === 'rps' ? 'text-amber-500' : 'text-gray-400' }}" />
                <span class="font-medium">Rock Paper Scissors</span>
            </a>

            <a
                href="{{ route('models.show.svg', $model) }}"
                class="flex items-center p-3 {{ $activeTab === 'svg' ? 'bg-amber-50 border-l-4 border-amber-500 text-amber-700' : 'border-l-4 border-transparent hover:bg-gray-50' }}"
            >
                <x-phosphor-paint-brush-fill class="w-5 h-5 mr-3 {{ $activeTab === 'svg' ? 'text-amber-500' : 'text-gray-400' }}" />
                <span class="font-medium">SVG Drawing</span>
                <span class="ml-2 text-xs px-2 py-0.5 bg-blue-100 text-blue-800 rounded">Coming soon</span>
            </a>

            <a
                href="{{ route('models.show.chess', $model) }}"
                class="flex items-center p-3 {{ $activeTab === 'chess' ? 'bg-amber-50 border-l-4 border-amber-500 text-amber-700' : 'border-l-4 border-transparent hover:bg-gray-50' }}"
            >
                <x-phosphor-crown-cross-fill class="w-5 h-5 mr-3 {{ $activeTab === 'chess' ? 'text-amber-500' : 'text-gray-400' }}" />
                <span class="font-medium">Chess</span>
                <span class="ml-2 text-xs px-2 py-0.5 bg-green-100 text-green-800 rounded">Coming soon</span>
            </a>
        </nav>
    </div>

    <!-- Tablet view compact layout (md screens only) -->
    <div class="hidden md:block lg:hidden border-b border-gray-200 mb-4">
        <div class="grid grid-cols-4 gap-1">
            <a
                href="{{ route('models.show', $model) }}"
                class="flex flex-col items-center py-3 px-1 {{ $activeTab === 'overview'
                    ? 'bg-amber-50 text-amber-600 border-b-2 border-amber-500'
                    : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} rounded-t-lg transition-colors"
            >
                <x-phosphor-star-fill class="w-5 h-5 mb-1 {{ $activeTab === 'overview' ? 'text-amber-500' : 'text-gray-400' }}" />
                <span class="text-xs font-medium">Overview</span>
            </a>

            <a
                href="{{ route('models.show.rps', $model) }}"
                class="flex flex-col items-center py-3 px-1 {{ $activeTab === 'rps'
                    ? 'bg-amber-50 text-amber-600 border-b-2 border-amber-500'
                    : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} rounded-t-lg transition-colors"
            >
                <x-phosphor-hand-fill class="w-5 h-5 mb-1 {{ $activeTab === 'rps' ? 'text-amber-500' : 'text-gray-400' }}" />
                <span class="text-xs font-medium">Rock Paper Scissors</span>
            </a>

            <a
                href="{{ route('models.show.svg', $model) }}"
                class="flex flex-col items-center py-3 px-1 {{ $activeTab === 'svg'
                    ? 'bg-amber-50 text-amber-600 border-b-2 border-amber-500'
                    : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} rounded-t-lg transition-colors"
            >
                <x-phosphor-paint-brush-fill class="w-5 h-5 mb-1 {{ $activeTab === 'svg' ? 'text-amber-500' : 'text-gray-400' }}" />
                <div class="text-xs font-medium mt-0.5 relative">
                    SVG Drawing
                    <span class="absolute -top-4 -right-6 text-[9px] px-1 bg-blue-100 text-blue-600 rounded-full">Soon</span>
                </div>
            </a>

            <a
                href="{{ route('models.show.chess', $model) }}"
                class="flex flex-col items-center py-3 px-1 {{ $activeTab === 'chess'
                    ? 'bg-amber-50 text-amber-600 border-b-2 border-amber-500'
                    : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700' }} rounded-t-lg transition-colors"
            >
                <x-phosphor-crown-cross-fill class="w-5 h-5 mb-1 {{ $activeTab === 'chess' ? 'text-amber-500' : 'text-gray-400' }}" />
                <div class="text-xs font-medium mt-0.5 relative">
                    Chess
                    <span class="absolute -top-4 -right-6 text-[9px] px-1 bg-green-100 text-green-600 rounded-full">Soon</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Desktop tab navigation (lg screens and up) -->
    <div class="hidden lg:block border-b border-gray-200">
        <nav class="flex flex-wrap justify-around md:justify-start md:space-x-6">
            <a
                href="{{ route('models.show', $model) }}"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'overview'
                    ? 'border-amber-500 text-amber-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <span class="flex items-center">
                    <x-phosphor-star-fill class="w-4 h-4 mr-1.5 {{ $activeTab === 'overview' ? 'text-amber-500' : 'text-gray-400' }}" />
                    Overview
                </span>
            </a>

            <a
                href="{{ route('models.show.rps', $model) }}"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'rps'
                    ? 'border-amber-500 text-amber-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <span class="flex items-center">
                    <x-phosphor-hand-fill class="w-4 h-4 mr-1.5 {{ $activeTab === 'rps' ? 'text-amber-500' : 'text-gray-400' }}" />
                    Rock Paper Scissors
                </span>
            </a>

            <a
                href="{{ route('models.show.svg', $model) }}"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'svg'
                    ? 'border-amber-500 text-amber-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <span class="flex items-center">
                    <x-phosphor-paint-brush-fill class="w-4 h-4 mr-1.5 {{ $activeTab === 'svg' ? 'text-amber-500' : 'text-gray-400' }}" />
                    SVG Drawing
                    <span class="ml-1.5 text-xs px-1.5 py-0.5 bg-blue-100 text-blue-600 rounded">Coming soon</span>
                </span>
            </a>

            <a
                href="{{ route('models.show.chess', $model) }}"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'chess'
                    ? 'border-amber-500 text-amber-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
            >
                <span class="flex items-center">
                    <x-phosphor-crown-cross-fill class="w-4 h-4 mr-1.5 {{ $activeTab === 'chess' ? 'text-amber-500' : 'text-gray-400' }}" />
                    Chess
                    <span class="ml-1.5 text-xs px-1.5 py-0.5 bg-green-100 text-green-600 rounded">Coming soon</span>
                </span>
            </a>
        </nav>
    </div>
</div>
