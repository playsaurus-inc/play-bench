@props(['model', 'activeTab' => 'overview'])

<div>

    <div class="mb-8">
        <div class="flex items-center justify-between">
            <x-ui.button :href="route('models.index')" variant="secondary" class="text-sm">
                <x-phosphor-arrow-left class="w-4 h-4 mr-4" />
                Back to All Models
            </x-ui.button>
        </div>
    </div>

    <!-- Model Overview -->
    <div class="relative bg-white rounded-3xl shadow-md border border-gray-100 mb-4 overflow-hidden">
        <!-- Background decorations -->
        <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-r from-amber-50 to-white opacity-50"></div>

        <!-- Content -->
        <div class="relative px-6 py-8 md:px-8">
            <div class="flex flex-col md:flex-row items-start gap-8">
                <!-- Model avatar and info -->
                <div class="md:w-1/3">
                    <div class="flex flex-col items-center md:items-start">
                        <div class="w-24 h-24 md:w-32 md:h-32 rounded-xl bg-amber-100 border-4 border-white shadow-lg flex items-center justify-center mb-4">
                            <x-phosphor-robot-fill class="w-12 h-12 md:w-16 md:h-16 text-amber-500" />
                        </div>
                        <h1 class="text-2xl md:text-4xl font-bold text-gray-900 text-center md:text-left">{{ $model->name }}</h1>
                        <p class="text-lg text-gray-600 mt-2 text-center md:text-left">{{ $model->description ?? 'AI Model' }}</p>
                    </div>
                </div>

                <!-- Performance overview -->
                <div class="md:w-2/3 grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <!-- RPS Performance -->
                    <div class="bg-gray-50 rounded-lg p-5 relative transition-all duration-300 hover:bg-white hover:shadow-md hover:-translate-y-1">
                        <div class="absolute top-0 right-0 w-16 h-16 border-l border-b border-gray-100 rounded-bl-3xl opacity-40"></div>
                        <div class="relative">
                            <h3 class="text-sm font-medium text-gray-500 mb-2 flex items-center">
                                <x-phosphor-hand-fill class="w-4 h-4 mr-1.5 text-red-500" />
                                Rock Paper Scissors
                            </h3>
                            @if($model->rps_rank > 0)
                                <div class="flex items-end">
                                    <span class="text-2xl font-bold text-amber-600">Rank #{{ $model->rps_rank }}</span>
                                </div>
                                <div class="mt-2 flex flex-col">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-500">ELO Rating:</span>
                                        <span class="font-medium">{{ Number::format($model->rps_elo, 0) }}</span>
                                    </div>
                                    <div class="mt-1">
                                        <a href="{{ route('models.show.rps', $model) }}" class="text-xs text-amber-600 hover:text-amber-700 flex items-center">
                                            View RPS details
                                            <x-phosphor-arrow-right class="w-3 h-3 ml-1" />
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center mt-2">
                                    <span class="text-sm text-gray-500">No matches yet</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- SVG Drawing Performance (coming soon) -->
                    <div class="bg-gray-50 rounded-lg p-5 relative opacity-70">
                        <div class="absolute top-0 right-0 w-16 h-16 border-l border-b border-gray-100 rounded-bl-3xl opacity-40"></div>
                        <div class="relative">
                            <div class="flex items-center mb-2">
                                <h3 class="text-sm font-medium text-gray-500 flex items-center">
                                    <x-phosphor-paint-brush-fill class="w-4 h-4 mr-1.5 text-blue-500" />
                                    SVG Drawing
                                </h3>
                                <span class="ml-2 text-xs px-1.5 py-0.5 bg-blue-100 text-blue-800 rounded-sm">Coming soon</span>
                            </div>
                            <div class="flex items-center mt-2">
                                <span class="text-sm text-gray-500">No matches yet</span>
                            </div>
                        </div>
                    </div>

                    <!-- Chess Performance (coming soon) -->
                    <div class="bg-gray-50 rounded-lg p-5 relative opacity-70">
                        <div class="absolute top-0 right-0 w-16 h-16 border-l border-b border-gray-100 rounded-bl-3xl opacity-40"></div>
                        <div class="relative">
                            <div class="flex items-center mb-2">
                                <h3 class="text-sm font-medium text-gray-500 flex items-center">
                                    <x-phosphor-crown-cross-fill class="w-4 h-4 mr-1.5 text-green-500" />
                                    Chess
                                </h3>
                                <span class="ml-2 text-xs px-1.5 py-0.5 bg-green-100 text-green-800 rounded-sm">Coming soon</span>
                            </div>
                            <div class="flex items-center mt-2">
                                <span class="text-sm text-gray-500">No matches yet</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <x-models.tabs :model="$model" :activeTab="$activeTab" />
</div>
