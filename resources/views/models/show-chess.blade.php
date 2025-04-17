<x-layouts::app :title="$model->name . ' - Chess'">
    <!-- Header section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <x-ui.button :href="route('models.index')" variant="secondary" class="text-sm">
                <x-phosphor-arrow-left class="w-4 h-4 mr-4" />
                Back to All Models
            </x-ui.button>
        </div>
    </div>

    <!-- Model Overview -->
    <div class="relative bg-white rounded-3xl shadow-md border border-gray-100 mb-10 overflow-hidden">
        <!-- Background decorations -->
        <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-r from-amber-50 to-white opacity-50"></div>

        <!-- Content -->
        <div class="relative px-6 py-8 md:px-8">
            <div class="flex flex-col md:flex-row items-start gap-8">
                <!-- Model avatar and info -->
                <div class="md:w-1/2">
                    <div class="flex flex-col items-center md:items-start">
                        <div class="w-24 h-24 md:w-32 md:h-32 rounded-xl bg-amber-100 border-4 border-white shadow-lg flex items-center justify-center mb-4">
                            <x-phosphor-robot-fill class="w-12 h-12 md:w-16 md:h-16 text-amber-500" />
                        </div>
                        <h1 class="text-2xl md:text-4xl font-bold text-gray-900 text-center md:text-left">{{ $model->name }}</h1>
                        <p class="text-lg text-gray-600 mt-2 text-center md:text-left">{{ $model->description ?? 'AI Model' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <x-models.tabs :model="$model" :activeTab="$activeTab" />

    <!-- Coming Soon -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="flex justify-center mb-6">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center">
                <x-phosphor-crown-cross-fill class="w-12 h-12 text-gray-400" />
            </div>
        </div>

        <h2 class="text-2xl font-bold text-gray-900 mb-3">Chess Benchmark Coming Soon</h2>
        <p class="text-gray-600 max-w-2xl mx-auto mb-6">
            The Chess benchmark is currently in development and will be available soon. This benchmark will test
            AI models on their ability to play chess, evaluating their strategic thinking, planning skills, and
            positional understanding.
        </p>

        <x-ui.button :href="route('models.show', $model)" variant="secondary">
            <x-phosphor-arrow-left class="w-4 h-4 mr-2" />
            Return to Overview
        </x-ui.button>
    </div>
</x-layouts::app>
