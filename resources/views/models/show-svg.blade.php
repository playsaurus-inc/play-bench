<x-layouts::app :title="$model->name . ' - SVG Drawing'">
    <!-- Header Component -->
    <x-models.header :model="$model" :activeTab="$activeTab" />

    <!-- Coming Soon -->
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
        <div class="flex justify-center mb-6">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center">
                <x-phosphor-paint-brush-fill class="w-12 h-12 text-gray-400" />
            </div>
        </div>

        <h2 class="text-2xl font-bold text-gray-900 mb-3">SVG Drawing Benchmark Coming Soon</h2>
        <p class="text-gray-600 max-w-2xl mx-auto mb-6">
            The SVG Drawing benchmark is currently in development and will be available soon. This benchmark will test
            AI models on their ability to create scalable vector graphics based on prompts, evaluating their visual
            creativity, spatial understanding, and technical precision.
        </p>

        <x-ui.button :href="route('models.show', $model)" variant="secondary">
            <x-phosphor-arrow-left class="w-4 h-4 mr-2" />
            Return to Overview
        </x-ui.button>
    </div>
</x-layouts::app>
